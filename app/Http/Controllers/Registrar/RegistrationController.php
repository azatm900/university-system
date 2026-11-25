<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Registration;
use App\Models\Semester;
use App\Models\Student;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegistrationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Registration::with(['student.user', 'course', 'semester']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->input('course_id'));
        }
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->input('semester_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $registrations = $query->latest()->paginate(20)->withQueryString();

        $students  = Student::with('user')->orderBy('id')->get();
        $courses   = Course::orderBy('code')->get();
        $semesters = Semester::orderByDesc('start_date')->get();

        return view('registrar.registrations.index',
            compact('registrations', 'students', 'courses', 'semesters'));
    }

    public function create(): View
    {
        $students  = Student::with('user')->orderBy('id')->get();
        $courses   = Course::orderBy('code')->get();
        $semesters = Semester::orderByDesc('start_date')->get();
        return view('registrar.registrations.create',
            compact('students', 'courses', 'semesters'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id'    => ['required', 'integer', 'exists:students,id'],
            'course_id'     => ['required', 'integer', 'exists:courses,id'],
            'semester_id'   => ['required', 'integer', 'exists:semesters,id'],
            'registered_at' => ['nullable', 'date'],
            'status'        => ['nullable', 'in:registered,dropped,completed'],
        ]);

        $exists = Registration::where('student_id', $validated['student_id'])
            ->where('course_id', $validated['course_id'])
            ->where('semester_id', $validated['semester_id'])
            ->exists();

        if ($exists) {
            return redirect()->route('registrar.registrations.index')
                ->with('error', 'الطالب مسجل بالفعل في هذا المقرر لهذا الفصل.');
        }

        $validated['registered_at'] = $validated['registered_at'] ?? now()->toDateString();
        $validated['status'] = $validated['status'] ?? 'registered';

        $registration = Registration::create($validated);

        app(AuditService::class)->log(
            'created',
            $registration,
            [],
            $registration->only(['student_id','course_id','semester_id','registered_at','status'])
        );

        return redirect()->route('registrar.registrations.index')
            ->with('success', 'تم إنشاء التسجيل بنجاح');
    }

    public function show(Registration $registration): View
    {
        $registration->load(['student.user', 'course.program', 'semester', 'result']);
        return view('registrar.registrations.show', compact('registration'));
    }

    public function edit(Registration $registration): View
    {
        $students  = Student::with('user')->orderBy('id')->get();
        $courses   = Course::orderBy('code')->get();
        $semesters = Semester::orderByDesc('start_date')->get();
        return view('registrar.registrations.edit',
            compact('registration', 'students', 'courses', 'semesters'));
    }

    public function update(Request $request, Registration $registration): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'in:registered,dropped,completed'],
        ]);

        $currentStatus = $registration->status instanceof \App\Enums\RegistrationStatus
            ? $registration->status->value
            : $registration->status;

        if ($currentStatus !== 'registered') {
            return redirect()->route('registrar.registrations.index')
                ->with('error', 'لا يمكن تعديل تسجيل غير "مسجل". التسجيلات المكتملة أو المسحوبة غير قابلة للتعديل.');
        }

        if ($validated['status'] === 'registered') {
            return redirect()->route('registrar.registrations.index')
                ->with('error', 'الحالة الحالية هي "مسجل" بالفعل.');
        }

        $old = ['status' => $currentStatus];
        $registration->update(['status' => $validated['status']]);
        $new = ['status' => $validated['status']];

        app(AuditService::class)->log('updated', $registration, $old, $new);

        return redirect()->route('registrar.registrations.index')
            ->with('success', 'تم تحديث حالة التسجيل');
    }

    public function destroy(Registration $registration): RedirectResponse
    {
        $old = $registration->only(['student_id','course_id','semester_id','status']);
        $registration->delete();

        app(AuditService::class)->log('deleted', $registration, $old, []);

        return redirect()->route('registrar.registrations.index')
            ->with('success', 'تم حذف التسجيل نهائياً');
    }
}