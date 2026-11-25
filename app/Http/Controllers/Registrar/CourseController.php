<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Program;
use App\Models\Registration;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CourseController extends Controller
{
    public function index(Request $request): View
    {
        $query = Course::with('program');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }
        if ($request->filled('program_id')) {
            $query->where('program_id', $request->input('program_id'));
        }
        if ($request->filled('semester_number')) {
            $query->where('semester_number', (int) $request->input('semester_number'));
        }
        if ($request->has('is_active') && $request->input('is_active') !== '') {
            $query->where('is_active', (bool) $request->input('is_active'));
        }

        $courses  = $query->latest()->paginate(20)->withQueryString();
        $programs = Program::orderBy('name')->get();

        return view('registrar.courses.index', compact('courses', 'programs'));
    }

    public function create(): View
    {
        $programs = Program::orderBy('name')->get();
        return view('registrar.courses.create', compact('programs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code'            => ['required', 'string', 'max:20'],
            'name'            => ['required', 'string', 'max:200'],
            'program_id'      => ['required', 'integer', 'exists:programs,id'],
            'credit_hours'    => ['required', 'integer', 'min:1', 'max:12'],
            'level'           => ['required', 'integer', 'min:1', 'max:10'],
            'semester_number' => ['required', 'integer', 'min:1', 'max:4'],
            'course_type'     => ['required', 'in:compulsory,elective'],
            'description'     => ['nullable', 'string'],
            'is_active'       => ['nullable', 'boolean'],
        ]);
        $validated['is_active'] = $validated['is_active'] ?? true;

        $course = Course::create($validated);

        app(AuditService::class)->log(
            'created',
            $course,
            [],
            $course->only(['code','name','program_id','credit_hours','level','semester_number','course_type','is_active'])
        );

        return redirect()->route('registrar.courses.index')
            ->with('success', 'تم إنشاء المقرر بنجاح');
    }

    public function show(Course $course): View
    {
        $course->load([
            'program',
            'registrations.student.user',
            'registrations.semester',
        ]);
        return view('registrar.courses.show', compact('course'));
    }

    public function edit(Course $course): View
    {
        $programs = Program::orderBy('name')->get();
        return view('registrar.courses.edit', compact('course', 'programs'));
    }

    public function update(Request $request, Course $course): RedirectResponse
    {
        $validated = $request->validate([
            'code'            => ['required', 'string', 'max:20'],
            'name'            => ['required', 'string', 'max:200'],
            'program_id'      => ['required', 'integer', 'exists:programs,id'],
            'credit_hours'    => ['required', 'integer', 'min:1', 'max:12'],
            'level'           => ['required', 'integer', 'min:1', 'max:10'],
            'semester_number' => ['required', 'integer', 'min:1', 'max:4'],
            'course_type'     => ['required', 'in:compulsory,elective'],
            'description'     => ['nullable', 'string'],
            'is_active'       => ['nullable', 'boolean'],
        ]);

        $fields = ['code','name','program_id','credit_hours','level','semester_number','course_type','is_active'];
        $old = $course->only($fields);
        $course->update($validated);
        $new = $course->only($fields);

        app(AuditService::class)->log('updated', $course, $old, $new);

        return redirect()->route('registrar.courses.index')
            ->with('success', 'تم تحديث المقرر بنجاح');
    }

    public function destroy(Course $course): RedirectResponse
    {
        $regCount = Registration::where('course_id', $course->id)->count();
        if ($regCount > 0) {
            return redirect()->route('registrar.courses.index')
                ->with('error', 'لا يمكن حذف المقرر لوجود ' . $regCount . ' تسجيل مرتبط به.');
        }

        $old = $course->only(['code','name']);
        $course->delete();

        app(AuditService::class)->log('deleted', $course, $old, []);

        return redirect()->route('registrar.courses.index')
            ->with('success', 'تم حذف المقرر (Soft Delete)');
    }

    public function forceDestroy(Request $request, int $id): RedirectResponse
    {
        if ($request->input('confirm') !== 'force') {
            return redirect()->route('registrar.courses.index')
                ->with('error', 'يجب تأكيد الحذف النهائي بـ ?confirm=force');
        }

        $course = Course::withTrashed()->findOrFail($id);

        $regCount = Registration::where('course_id', $course->id)->count();
        if ($regCount > 0) {
            return redirect()->route('registrar.courses.index')
                ->with('error', 'لا يمكن الحذف النهائي لوجود ' . $regCount . ' تسجيل.');
        }

        $old = $course->only(['code','name']);
        $course->forceDelete();

        app(AuditService::class)->log('force_deleted', $course, $old, []);

        return redirect()->route('registrar.courses.index')
            ->with('success', 'تم الحذف النهائي للمقرر');
    }
}