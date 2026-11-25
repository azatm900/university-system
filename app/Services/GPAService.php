<?php

namespace App\Services;

use App\Models\Semester;
use App\Models\Student;
use Illuminate\Support\Collection;

class GPAService
{
    public function __construct(protected SettingsService $settings) {}

    public function calculate(Student $student, ?Semester $semester = null): array
    {
        // FIX (Step 10.4): ResultStatus::Published does not exist.
        // Treat every non-incomplete result as a final result (Option A).
        $query = $student->results()->where('results.status', '!=', 'incomplete');
        if ($semester) {
            $query->where('semester_id', $semester->id);
        }
        return $this->compute($query->with('registration.course')->get());
    }

    public function semesterGPA(Student $student, Semester $semester): array
    {
        return $this->calculate($student, $semester);
    }

    public function cumulativeGPA(Student $student): array
    {
        return $this->calculate($student, null);
    }

    public function compute(Collection $results): array
    {
        $totalCredits   = 0.0;
        $earnedCredits  = 0.0;
        $weightedPoints = 0.0;
        $weightedScore  = 0.0;
        $passed = 0;
        $failed = 0;
        $passScore = (float) $this->settings->get('gpa.passing_score', 50);

        foreach ($results as $r) {
            $credits = (float) ($r->course->credit_hours ?? 0);
            if ($credits <= 0) continue;

            $points = (float) ($r->grade_points ?? 0);
            $score  = (float) ($r->total_score  ?? 0);

            $totalCredits   += $credits;
            $weightedPoints += $points * $credits;
            $weightedScore  += $score  * $credits;

            // FIX (Step 10.4): ResultStatus has no Published case; also status may be
            // string (no enum cast on Result) or enum (if cast added later). Handle both.
            $statusVal = $r->status instanceof \BackedEnum ? $r->status->value : (string) $r->status;
            $isPassed  = $statusVal === 'passed';

            if ($isPassed) { $passed++; $earnedCredits += $credits; }
            else           { $failed++; }
        }

        $gpa        = $totalCredits > 0 ? round($weightedPoints / $totalCredits, 2) : 0.0;
        $percentage = $totalCredits > 0 ? round($weightedScore  / $totalCredits, 2) : 0.0;

        return [
            'gpa'            => $gpa,
            'percentage'     => $percentage,
            'grade_letter'   => $this->gradeLetter($percentage),
            'total_credits'  => $totalCredits,
            'earned_credits' => $earnedCredits,
            'courses_count'  => $results->count(),
            'passed_count'   => $passed,
            'failed_count'   => $failed,
            'is_passing'     => $gpa >= (float) $this->settings->get('gpa.passing_gpa', 1.0),
        ];
    }

    public function gradeLetter(float $percentage): string
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
}