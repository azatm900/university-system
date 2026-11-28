<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use App\Models\Program;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $role = $user?->role?->value ?? 'guest';

        $stats = [
            'students_count'      => $this->safeCount(Student::class),
            'active_students'     => $this->safeWhereCount(Student::class, 'status', 'active'),
            'courses_count'       => $this->safeCount(Course::class),
            'programs_count'      => $this->safeCount(Program::class),
            'users_count'         => $this->safeCount(User::class),
            'registrations_count' => $this->safeCount(Registration::class),
            'unpaid_invoices'     => $this->safeWhereCount(Invoice::class, 'status', 'unpaid'),
            'paid_invoices'       => $this->safeWhereCount(Invoice::class, 'status', 'paid'),
            'payments_count'      => $this->safeCount(Payment::class),
            'payments_total'      => $this->safeSum(Payment::class, 'amount'),
            'invoices_total'      => $this->safeSum(Invoice::class, 'total'),
            'paid_amount_total'   => $this->safeSum(Invoice::class, 'paid_amount'),
        ];

        $recentStudents = in_array($role, ['admin', 'registrar'], true)
            ? $this->safeRecentWith(Student::class, ['user'], 5)
            : collect();

        $chartData = null;

        if ($role === 'admin') {
            try {
                $registrations = Registration::selectRaw(
                    "DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total"
                )
                    ->where('created_at', '>=', now()->subMonths(6))
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('total', 'month')
                    ->toArray();

                $payments = Payment::selectRaw(
                    "DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total"
                )
                    ->where('created_at', '>=', now()->subMonths(6))
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('total', 'month')
                    ->toArray();

                $months = [];
                $regSeries = [];
                $paySeries = [];

                for ($i = 5; $i >= 0; $i--) {
                    $m = now()->subMonths($i)->format('Y-m');
                    $label = now()->subMonths($i)->translatedFormat('M');

                    $months[] = $label;
                    $regSeries[] = (int) ($registrations[$m] ?? 0);
                    $paySeries[] = (float) ($payments[$m] ?? 0);
                }

                $chartData = [
                    'months' => $months,
                    'registrations' => $regSeries,
                    'payments' => $paySeries,
                ];
            } catch (\Throwable $e) {
                $chartData = null;
            }
        }

        /*
         * Student dashboard data.
         * This section runs only for the student role.
         */
        $studentDashboard = null;

        if ($role === 'student' && $user) {
            $student = $user->student;

            if ($student) {
                $student->load([
                    'program',
                    'registrations.semester',
                    'registrations.course',
                    'registrations.result',
                    'invoices.semester',
                ]);

                // Registration records are grouped by semester.
                $registrationGroups = $student->registrations
                    ->sortBy('registered_at')
                    ->groupBy(fn ($registration) =>
                        $registration->semester_id
                            ? 'semester_' . $registration->semester_id
                            : 'unlinked'
                    )
                    ->map(function ($group) {
                        $first = $group->first();

                        $statuses = $group->map(function ($registration) {
                            $status = $registration->status;

                            return $status instanceof \BackedEnum
                                ? $status->value
                                : (string) $status;
                        })->unique()->values();

                        if ($statuses->contains('dropped')) {
                            $status = 'dropped';
                        } elseif ($statuses->contains('registered')) {
                            $status = 'registered';
                        } elseif ($statuses->contains('completed')) {
                            $status = 'completed';
                        } else {
                            $status = $statuses->first() ?? 'not_recorded';
                        }

                        $dates = $group
                            ->pluck('registered_at')
                            ->filter()
                            ->sort();

                        return [
                            'semester' => $first->semester,
                            'registration_date' => $dates->first(),
                            'status' => $status,
                            'count' => $group->count(),
                        ];
                    })
                    ->values();

                // Ignore cancelled invoices and group the remaining invoices by semester.
                $validInvoices = $student->invoices->filter(function ($invoice) {
                    $status = $invoice->status;

                    $status = $status instanceof \BackedEnum
                        ? $status->value
                        : (string) $status;

                    return $status !== 'cancelled';
                });

                $financeGroups = $validInvoices
                    ->filter(fn ($invoice) => $invoice->semester_id !== null)
                    ->groupBy('semester_id')
                    ->map(function ($group) {
                        $first = $group->first();

                        $total = (float) $group->sum('total');
                        $paid = (float) $group->sum('paid_amount');

                        return [
                            'semester' => $first->semester,
                            'total' => $total,
                            'paid' => $paid,
                            'remaining' => max(0, $total - $paid),
                        ];
                    })
                    ->values();

                // Invoices not linked to a semester are shown separately.
                $unlinkedInvoices = $validInvoices
                    ->filter(fn ($invoice) => $invoice->semester_id === null);

                $unlinkedFinance = [
                    'total' => (float) $unlinkedInvoices->sum('total'),
                    'paid' => (float) $unlinkedInvoices->sum('paid_amount'),
                    'remaining' => max(
                        0,
                        (float) $unlinkedInvoices->sum('total')
                        - (float) $unlinkedInvoices->sum('paid_amount')
                    ),
                ];

                // Only published results are included in the student transcript.
                $publishedResults = $student->registrations
                    ->filter(fn ($registration) =>
                        $registration->result
                        && $registration->result->published_at !== null
                    )
                    ->map(fn ($registration) => [
                        'result' => $registration->result,
                        'course' => $registration->course,
                        'semester' => $registration->semester,
                        'semester_id' => $registration->semester_id,
                    ]);

                // Calculate weighted GPA using course credit hours.
                $calculateGpa = function ($items) {
                    $weightedPoints = 0;
                    $totalHours = 0;

                    foreach ($items as $item) {
                        $hours = (float) ($item['course']?->credit_hours ?? 0);
                        $points = (float) ($item['result']->grade_points ?? 0);

                        if ($hours <= 0) {
                            continue;
                        }

                        $weightedPoints += $points * $hours;
                        $totalHours += $hours;
                    }

                    return $totalHours > 0
                        ? round($weightedPoints / $totalHours, 2)
                        : null;
                };

                $resultGroups = $publishedResults
                    ->groupBy(fn ($item) =>
                        $item['semester_id']
                            ? 'semester_' . $item['semester_id']
                            : 'unlinked'
                    )
                    ->map(function ($group) use ($calculateGpa) {
                        $first = $group->first();

                        return [
                            'semester' => $first['semester'],
                            'results' => $group->sortBy(
                                fn ($item) => $item['course']?->code ?? ''
                            )->values(),
                            'gpa' => $calculateGpa($group),
                        ];
                    })
                    ->values();

                $settings = app(SettingsService::class);

                $studentDashboard = [
                    'student' => $student,
                    'registration_groups' => $registrationGroups,
                    'finance_groups' => $financeGroups,
                    'unlinked_finance' => $unlinkedFinance,
                    'has_unlinked_invoices' => $unlinkedInvoices->isNotEmpty(),
                    'result_groups' => $resultGroups,
                    'cumulative_gpa' => $calculateGpa($publishedResults),
                    'currency' => $settings->get('finance.currency', 'SDG'),
                    'currency_symbol' => $settings->get('finance.currency_symbol', ''),
                    'gpa_max' => (float) $settings->get('gpa.max', 4.0),
                    'gpa_scale' => $settings->get('gpa.scale', 'both'),
                ];
            }
        }

        return view('dashboard.index', compact(
            'stats',
            'recentStudents',
            'role',
            'chartData',
            'studentDashboard'
        ));
    }

    private function safeCount(string $model): int
    {
        if (!class_exists($model)) {
            return 0;
        }

        try {
            return (int) $model::count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function safeWhereCount(string $model, string $col, $val): int
    {
        if (!class_exists($model)) {
            return 0;
        }

        try {
            return (int) $model::where($col, $val)->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function safeSum(string $model, string $col): float
    {
        if (!class_exists($model)) {
            return 0.0;
        }

        try {
            return (float) ($model::sum($col) ?? 0);
        } catch (\Throwable $e) {
            return 0.0;
        }
    }

    private function safeRecentWith(string $model, array $with, int $limit)
    {
        if (!class_exists($model)) {
            return collect();
        }

        try {
            return $model::with($with)->latest()->take($limit)->get();
        } catch (\Throwable $e) {
            return collect();
        }
    }
}