<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Course;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Registration;
use App\Models\User;
use App\Models\Program;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $role = $user?->role?->value ?? 'guest';

        // ── إحصاءات موحدة (آمنة ضد غياب الجداول) ──
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

        // ── بيانات الرسوم البيانية (للمدير فقط) ──
        $chartData = null;
        if ($role === 'admin') {
            try {
                // آخر 6 أشهر — تسجيلات شهرية
                $registrations = Registration::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
                    ->where('created_at', '>=', now()->subMonths(6))
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('total', 'month')
                    ->toArray();

                // المدفوعات الشهرية
                $payments = Payment::selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, SUM(amount) as total")
                    ->where('created_at', '>=', now()->subMonths(6))
                    ->groupBy('month')
                    ->orderBy('month')
                    ->pluck('total', 'month')
                    ->toArray();

                // بناء مصفوفة 6 أشهر (مع أصفار للأشهر الفارغة)
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

        return view('dashboard.index', compact('stats', 'recentStudents', 'role', 'chartData'));
    }

    private function safeCount(string $model): int
    {
        if (!class_exists($model)) return 0;
        try { return (int) $model::count(); } catch (\Throwable $e) { return 0; }
    }

    private function safeWhereCount(string $model, string $col, $val): int
    {
        if (!class_exists($model)) return 0;
        try { return (int) $model::where($col, $val)->count(); } catch (\Throwable $e) { return 0; }
    }

    private function safeSum(string $model, string $col): float
    {
        if (!class_exists($model)) return 0.0;
        try { return (float) ($model::sum($col) ?? 0); } catch (\Throwable $e) { return 0.0; }
    }

    private function safeRecentWith(string $model, array $with, int $limit)
    {
        if (!class_exists($model)) return collect();
        try { return $model::with($with)->latest()->take($limit)->get(); } catch (\Throwable $e) { return collect(); }
    }
}