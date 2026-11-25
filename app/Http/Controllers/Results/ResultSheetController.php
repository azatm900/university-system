<?php

namespace App\Http\Controllers\Results;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Program;
use App\Models\Registration;
use App\Models\Semester;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ResultSheetController extends Controller
{
    /**
     * صفحة الفلاتر (تخصص + مستوى + فصل + مادة اختيارياً).
     */
    public function index(): View
    {
        $programs  = Program::orderBy('name')->get(['id', 'code', 'name']);
        $semesters = Semester::orderByDesc('start_date')->get(['id', 'code', 'name']);
        $levels    = range(1, 8);

        return view('results.sheet.index', compact('programs', 'semesters', 'levels'));
    }

    /**
     * كشف الدرجات (يُعرض بعد اختيار الفلاتر، ويُطبع).
     */
    public function sheet(Request $request): View
    {
        $validated = $request->validate([
            'program_id'  => ['required', 'integer', 'exists:programs,id'],
            'level'       => ['required', 'integer', 'between:1,8'],
            'semester_id' => ['required', 'integer', 'exists:semesters,id'],
            'course_id'   => ['nullable', 'integer', 'exists:courses,id'],
        ]);

       

        $program  = Program::findOrFail($validated['program_id']);
        $semester = Semester::findOrFail($validated['semester_id']);
        $level    = (int) $validated['level'];
        $courseId = $validated['course_id'] ?? null;

        // الطلاب: لهم registrations في هذا الفصل + تخصصهم + مستواهم
        $registrations = Registration::query()
            ->where('semester_id', $semester->id)
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
            ->whereHas('student', function ($q) use ($program, $level) {
                $q->where('program_id', $program->id)
                  ->where('level', $level);
            })
            ->with([
                'student:id,user_id,full_name,student_number,program_id,level',
                'student.user:id,name',
                'course:id,code,name,program_id,level,semester_number,credit_hours',
                'result',
            ])
            ->orderBy('student_id')
            ->orderBy('course_id')
            ->get();

        // تجميع حسب الطالب
        $students = $registrations->groupBy('student_id')->map(function ($rows) {
            $student = $rows->first()->student;
            return [
                'student' => $student,
                'rows'    => $rows,
            ];
        })->values();

        $course = $courseId ? Course::find($courseId) : null;

        return view('results.sheet.print', compact(
            'program', 'semester', 'level', 'course',
            'students'
        ));
    }
}