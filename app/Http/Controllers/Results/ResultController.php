<?php

namespace App\Http\Controllers\Results;

use App\Http\Controllers\Controller;
use App\Models\Registration;
use App\Models\Result;
use App\Services\AuditService;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ResultController extends Controller
{
    public function index(): View
    {
        $results = Result::with(['registration.student', 'registration.course', 'registration.semester'])
            ->latest()
            ->paginate(20);
        return view('results.results.index', compact('results'));
    }

    public function create(): View
    {
        $registrations = Registration::where('status', 'completed')
            ->whereDoesntHave('result')
            ->with(['student', 'course', 'semester'])
            ->orderBy('id', 'desc')
            ->get();
        return view('results.results.create', compact('registrations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'registration_id'  => ['required', 'integer', 'exists:registrations,id', 'unique:results,registration_id'],
            'coursework_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'midterm_score'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'final_score'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'status'           => ['nullable', 'in:passed,failed,incomplete'],
        ]);

        $coursework = (float) ($validated['coursework_score'] ?? 0);
        $midterm    = (float) ($validated['midterm_score'] ?? 0);
        $final      = (float) ($validated['final_score'] ?? 0);
        $total      = round($coursework + $midterm + $final, 2);

        if ($total > 100.0) {
            throw ValidationException::withMessages([
                'total_score' => 'مجموع الدرجات يتجاوز 100 (' . number_format($total, 2) . ').',
            ]);
        }

        [$grade, $gradePoints, $status] = $this->deriveGrade($total, $validated['status'] ?? null);

        $result = Result::create([
            'registration_id'  => $validated['registration_id'],
            'coursework_score' => $coursework > 0 ? $coursework : null,
            'midterm_score'    => $midterm    > 0 ? $midterm    : null,
            'final_score'      => $final      > 0 ? $final      : null,
            'total_score'      => $total,
            'grade'            => $grade,
            'grade_points'     => $gradePoints,
            'status'           => $status,
            'published_at'     => null,
        ]);

        app(AuditService::class)->log(
            'created',
            $result,
            [],
            $result->only(['registration_id', 'total_score', 'grade', 'grade_points', 'status'])
        );

        return redirect()->route('results.results.index')
            ->with('success', 'تم إدخال النتيجة');
    }

    public function show(Result $result): View
    {
        $result->load(['registration.student', 'registration.course', 'registration.semester']);
        return view('results.results.show', compact('result'));
    }

    public function edit(Result $result): View
    {
        if ($result->published_at !== null) {
            // caller should not reach; guard at controller level via update()
        }
        $result->load(['registration.student', 'registration.course']);
        return view('results.results.edit', compact('result'));
    }

    public function update(Request $request, Result $result): RedirectResponse
    {
        if ($result->published_at !== null) {
            return redirect()->route('results.results.index')
                ->with('error', 'لا يمكن تعديل نتيجة منشورة. ألغِ النشر أولاً.');
        }

        $validated = $request->validate([
            'coursework_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'midterm_score'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'final_score'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'status'           => ['nullable', 'in:passed,failed,incomplete'],
        ]);

        $coursework = (float) ($validated['coursework_score'] ?? 0);
        $midterm    = (float) ($validated['midterm_score'] ?? 0);
        $final      = (float) ($validated['final_score'] ?? 0);
        $total      = round($coursework + $midterm + $final, 2);

        if ($total > 100.0) {
            throw ValidationException::withMessages([
                'total_score' => 'مجموع الدرجات يتجاوز 100 (' . number_format($total, 2) . ').',
            ]);
        }

        [$grade, $gradePoints, $status] = $this->deriveGrade($total, $validated['status'] ?? null);

        $old = $result->only(['total_score', 'grade', 'grade_points', 'status']);
        $result->update([
            'coursework_score' => $coursework > 0 ? $coursework : null,
            'midterm_score'    => $midterm    > 0 ? $midterm    : null,
            'final_score'      => $final      > 0 ? $final      : null,
            'total_score'      => $total,
            'grade'            => $grade,
            'grade_points'     => $gradePoints,
            'status'           => $status,
        ]);
        $new = $result->only(['total_score', 'grade', 'grade_points', 'status']);

        app(AuditService::class)->log('updated', $result, $old, $new);

        return redirect()->route('results.results.show', $result)
            ->with('success', 'تم تحديث النتيجة');
    }

    public function publish(Result $result): RedirectResponse
    {
        if ($result->published_at !== null) {
            return redirect()->route('results.results.index')
                ->with('error', 'النتيجة منشورة بالفعل.');
        }

        $statusVal = $result->status instanceof \BackedEnum ? $result->status->value : (string) $result->status;
        if ($statusVal === 'incomplete') {
            return redirect()->route('results.results.index')
                ->with('error', 'لا يمكن نشر نتيجة غير مكتملة.');
        }

        $result->update(['published_at' => now()]);
        app(AuditService::class)->log(
            'updated',
            $result,
            ['published_at' => null],
            ['published_at' => $result->published_at->toDateTimeString()]
        );

        return redirect()->route('results.results.index')
            ->with('success', 'تم نشر النتيجة');
    }

    public function unpublish(Result $result): RedirectResponse
    {
        if ($result->published_at === null) {
            return redirect()->route('results.results.index')
                ->with('error', 'النتيجة غير منشورة.');
        }

        $oldVal = $result->published_at->toDateTimeString();
        $result->update(['published_at' => null]);
        app(AuditService::class)->log(
            'updated',
            $result,
            ['published_at' => $oldVal],
            ['published_at' => null]
        );

        return redirect()->route('results.results.index')
            ->with('success', 'تم إلغاء نشر النتيجة');
    }

    public function destroy(Result $result): RedirectResponse
    {
        if ($result->published_at !== null) {
            return redirect()->route('results.results.index')
                ->with('error', 'لا يمكن حذف نتيجة منشورة. ألغِ النشر أولاً.');
        }

        $old = $result->only(['registration_id', 'total_score', 'status']);
        $result->delete();
        app(AuditService::class)->log('deleted', $result, $old, []);

        return redirect()->route('results.results.index')
            ->with('success', 'تم حذف النتيجة نهائياً');
    }

    /**
     * Derive grade letter, grade points, and status from total score.
     * If $explicitStatus === 'incomplete', force INC regardless of score.
     */
    private function deriveGrade(float $total, ?string $explicitStatus): array
    {
        if ($explicitStatus === 'incomplete') {
            return ['INC', 0.0, 'incomplete'];
        }
        $passScore = (float) app(SettingsService::class)->get('gpa.passing_score', 50);
        $grade = $this->gradeLetter($total);
        $points = $this->gradePoints($grade);
        $status = $total >= $passScore ? 'passed' : 'failed';
        return [$grade, $points, $status];
    }

    private function gradeLetter(float $percentage): string
    {
        return match (true) {
            $percentage >= 95 => 'A+',
            $percentage >= 90 => 'A',
            $percentage >= 85 => 'B+',
            $percentage >= 80 => 'B',
            $percentage >= 75 => 'C+',
            $percentage >= 70 => 'C',
            $percentage >= 65 => 'D+',
            $percentage >= 60 => 'D',
            default           => 'F',
        };
    }

    private function gradePoints(string $grade): float
    {
        return match ($grade) {
            'A+', 'A' => 4.0,
            'B+'      => 3.5,
            'B'       => 3.0,
            'C+'      => 2.5,
            'C'       => 2.0,
            'D+'      => 1.5,
            'D'       => 1.0,
            default   => 0.0,
        };
    }
}