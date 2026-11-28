<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Registration;
use App\Services\SettingsService;
use Illuminate\Http\Request;

class StudentPortalController extends Controller
{
    private function studentFor(Request $request)
    {
        $student = $request->user()?->student;

        abort_unless($student, 404);

        return $student;
    }

    private function currentLevel($student): int
    {
        return max(1, min(8, (int) $student->level));
    }

    private function selectedLevel(Request $request, int $currentLevel): int
    {
        $validated = $request->validate([
            'level' => ['nullable', 'integer', 'min:1', 'max:' . $currentLevel],
        ]);

        return (int) ($validated['level'] ?? $currentLevel);
    }

    public function results(Request $request)
    {
        $student = $this->studentFor($request);
        $currentLevel = $this->currentLevel($student);
        $selectedLevel = $this->selectedLevel($request, $currentLevel);

        $registrations = Registration::query()
            ->where('student_id', $student->id)
            ->whereHas('course', fn ($query) => $query->where('level', $selectedLevel))
            ->whereHas('result', fn ($query) => $query->whereNotNull('published_at'))
            ->with(['course', 'semester', 'result'])
            ->get();

        $resultGroups = $registrations
            ->groupBy(fn ($registration) => $registration->semester_id
                ? 'semester_' . $registration->semester_id
                : 'unlinked'
            )
            ->map(function ($group) {
                $first = $group->first();
                $weightedPoints = 0;
                $totalHours = 0;

                foreach ($group as $registration) {
                    $hours = (float) ($registration->course?->credit_hours ?? 0);
                    $points = (float) ($registration->result?->grade_points ?? 0);

                    if ($hours > 0) {
                        $weightedPoints += $points * $hours;
                        $totalHours += $hours;
                    }
                }

                return [
                    'semester' => $first->semester,
                    'results' => $group->sortBy(
                        fn ($registration) => $registration->course?->code ?? ''
                    )->values(),
                    'gpa' => $totalHours > 0
                        ? round($weightedPoints / $totalHours, 2)
                        : null,
                ];
            })
            ->values();

        $allPublished = Registration::query()
            ->where('student_id', $student->id)
            ->whereHas('course', fn ($query) => $query->where('level', '<=', $selectedLevel))
            ->whereHas('result', fn ($query) => $query->whereNotNull('published_at'))
            ->with(['course', 'result'])
            ->get();

        $weightedPoints = 0;
        $totalHours = 0;

        foreach ($allPublished as $registration) {
            $hours = (float) ($registration->course?->credit_hours ?? 0);
            $points = (float) ($registration->result?->grade_points ?? 0);

            if ($hours > 0) {
                $weightedPoints += $points * $hours;
                $totalHours += $hours;
            }
        }

        $settings = app(SettingsService::class);

        return view('student.results.index', [
            'student' => $student,
            'currentLevel' => $currentLevel,
            'selectedLevel' => $selectedLevel,
            'levels' => range(1, $currentLevel),
            'resultGroups' => $resultGroups,
            'cumulativeGpa' => $totalHours > 0
                ? round($weightedPoints / $totalHours, 2)
                : null,
            'gpaMax' => (float) $settings->get('gpa.max', 4.0),
            'gpaScale' => $settings->get('gpa.scale', 'both'),
        ]);
    }

    public function finance(Request $request)
    {
        $student = $this->studentFor($request);
        $currentLevel = $this->currentLevel($student);
        $selectedLevel = $this->selectedLevel($request, $currentLevel);

        // Determine a semester level only when this student's registrations
        // identify exactly one distinct course level for that semester.
        $registrations = Registration::query()
            ->where('student_id', $student->id)
            ->with('course:id,level')
            ->get(['id', 'semester_id', 'course_id', 'student_id']);

        $semesterLevels = $registrations
            ->filter(fn ($registration) =>
                $registration->semester_id !== null
                && $registration->course?->level !== null
            )
            ->groupBy('semester_id')
            ->map(function ($group) {
                $levels = $group->pluck('course.level')->unique()->values();

                return $levels->count() === 1 ? (int) $levels->first() : null;
            });

        $invoices = Invoice::query()
            ->where('student_id', $student->id)
            ->where('status', '!=', 'cancelled')
            ->with([
                'semester',
                'payments' => fn ($query) => $query->orderBy('payment_date')->orderBy('id'),
            ])
            ->orderByDesc('created_at')
            ->get();

        $levelInvoices = collect();
        $unclassifiedInvoices = collect();

        foreach ($invoices as $invoice) {
            $invoiceLevel = $invoice->semester_id !== null
                ? ($semesterLevels->get($invoice->semester_id))
                : null;

            if ($invoiceLevel !== null && $invoiceLevel <= $currentLevel) {
                if ($invoiceLevel === $selectedLevel) {
                    $levelInvoices->push($invoice);
                }
            } else {
                $unclassifiedInvoices->push($invoice);
            }
        }

        $total = (float) $levelInvoices->sum('total');
        $paid = (float) $levelInvoices->sum('paid_amount');

        $unclassifiedTotal = (float) $unclassifiedInvoices->sum('total');
        $unclassifiedPaid = (float) $unclassifiedInvoices->sum('paid_amount');

        $settings = app(SettingsService::class);

        return view('student.finance.index', [
            'student' => $student,
            'currentLevel' => $currentLevel,
            'selectedLevel' => $selectedLevel,
            'levels' => range(1, $currentLevel),
            'invoices' => $levelInvoices,
            'total' => $total,
            'paid' => $paid,
            'remaining' => max(0, $total - $paid),
            'unclassifiedInvoices' => $unclassifiedInvoices,
            'unclassifiedTotal' => $unclassifiedTotal,
            'unclassifiedPaid' => $unclassifiedPaid,
            'unclassifiedRemaining' => max(0, $unclassifiedTotal - $unclassifiedPaid),
            'currency' => $settings->get('finance.currency', 'SDG'),
        ]);
    }
}