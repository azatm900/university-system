<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Program;
use App\Models\Student;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function index(): View
    {
        $programs = Program::withCount(['courses', 'students'])->latest()->paginate(15);
        return view('registrar.programs.index', compact('programs'));
    }

    public function create(): View
    {
        return view('registrar.programs.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'code'           => ['required', 'string', 'max:20', 'unique:programs,code'],
            'name'           => ['required', 'string', 'max:200'],
            'name_en'        => ['nullable', 'string', 'max:200'],
            'degree_level'   => ['required', 'in:diploma,bachelor,master,phd'],
            'duration_years' => ['required', 'integer', 'min:1', 'max:10'],
            'description'    => ['nullable', 'string'],
            'is_active'      => ['nullable', 'boolean'],
        ]);
        $validated['is_active'] = $validated['is_active'] ?? true;

        $program = Program::create($validated);

        app(AuditService::class)->log(
            'created',
            $program,
            [],
            $program->only(['code','name','degree_level','duration_years','is_active'])
        );

        return redirect()->route('registrar.programs.index')
            ->with('success', 'تم إنشاء البرنامج بنجاح');
    }

    public function edit(Program $program): View
    {
        return view('registrar.programs.edit', compact('program'));
    }

    public function update(Request $request, Program $program): RedirectResponse
    {
        $validated = $request->validate([
            'code'           => ['required', 'string', 'max:20', 'unique:programs,code,' . $program->id],
            'name'           => ['required', 'string', 'max:200'],
            'name_en'        => ['nullable', 'string', 'max:200'],
            'degree_level'   => ['required', 'in:diploma,bachelor,master,phd'],
            'duration_years' => ['required', 'integer', 'min:1', 'max:10'],
            'description'    => ['nullable', 'string'],
            'is_active'      => ['nullable', 'boolean'],
        ]);

        $fields = ['code','name','degree_level','duration_years','is_active'];
        $old = $program->only($fields);
        $program->update($validated);
        $new = $program->only($fields);

        app(AuditService::class)->log('updated', $program, $old, $new);

        return redirect()->route('registrar.programs.index')
            ->with('success', 'تم تحديث البرنامج بنجاح');
    }

    public function destroy(Program $program): RedirectResponse
    {
        // pre-check: only active (non-soft-deleted) students block soft-delete
        $studentsCount = Student::where('program_id', $program->id)->count();
        if ($studentsCount > 0) {
            return redirect()->route('registrar.programs.index')
                ->with('error', 'لا يمكن حذف البرنامج لوجود ' . $studentsCount . ' طالب نشط.');
        }

        $old = $program->only(['code','name']);
        $program->delete();

        app(AuditService::class)->log('deleted', $program, $old, []);

        return redirect()->route('registrar.programs.index')
            ->with('success', 'تم حذف البرنامج (Soft Delete)');
    }

    public function forceDestroy(Request $request, int $id): RedirectResponse
    {
        if ($request->input('confirm') !== 'force') {
            return redirect()->route('registrar.programs.index')
                ->with('error', 'يجب تأكيد الحذف النهائي بـ ?confirm=force');
        }

        $program = Program::withTrashed()->findOrFail($id);

        // FK RESTRICT: any student (active or soft-deleted) blocks force delete
        $studentsCount = Student::withTrashed()->where('program_id', $program->id)->count();
        if ($studentsCount > 0) {
            return redirect()->route('registrar.programs.index')
                ->with('error', 'لا يمكن الحذف النهائي لوجود ' . $studentsCount . ' طالب (حتى المحذوفين).');
        }

        $coursesCount = Course::withTrashed()->where('program_id', $program->id)->count();
        $old = $program->only(['code','name']);
        $program->forceDelete();

        app(AuditService::class)->log(
            'force_deleted',
            $program,
            $old,
            ['cascaded_courses' => $coursesCount]
        );

        return redirect()->route('registrar.programs.index')
            ->with('success', 'تم الحذف النهائي للبرنامج (' . $coursesCount . ' مقرر حُذف تلقائياً)');
    }
}