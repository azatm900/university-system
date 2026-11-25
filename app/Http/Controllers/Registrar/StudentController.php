<?php

namespace App\Http\Controllers\Registrar;

use App\Http\Controllers\Controller;
use App\Models\Program;
use App\Models\Student;
use App\Services\AuditService;
use App\Services\FileNumberService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::query()->with(['program', 'user']);

        $search = trim((string) $request->query('search', ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                $q->where('student_number', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhereHas('user', fn ($uq) => $uq->where('name', 'like', "%{$search}%"));
            });
        }

        $programId = (int) $request->query('program_id', 0);
        if ($programId > 0) {
            $query->where('program_id', $programId);
        }

        $status = trim((string) $request->query('status', ''));
        if ($status !== '' && in_array($status, \App\Enums\StudentStatus::values(), true)) {
            $query->where('status', $status);
        }

        $students = $query->latest()->paginate(20)->withQueryString();
        $programs = Program::orderBy('name')->get();

        return view('registrar.students.index', compact('students', 'programs'));
    }

    public function create(): View
    {
        $programs = Program::where('is_active', true)->orderBy('name')->get();
        return view('registrar.students.create', compact('programs'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'program_id'      => ['required', 'integer', 'exists:programs,id'],
            'full_name'       => ['required', 'string', 'max:255'],
            'national_id'     => ['nullable', 'string', 'max:30', 'unique:students,national_id'],
            'date_of_birth'   => ['required', 'date', 'before:today'],
            'gender'          => ['required', 'in:male,female'],
            'phone'           => ['required', 'string', 'max:20'],

            // Contacts (Phase G4-ext)
            'next_of_kin_name'  => ['nullable', 'string', 'max:255'],
            'next_of_kin_phone' => ['nullable', 'string', 'max:20'],
            'mother_name'       => ['nullable', 'string', 'max:255'],
            'mother_phone'      => ['nullable', 'string', 'max:20'],

            // Residence
            'address'  => ['nullable', 'string', 'max:500'],
            'state'    => ['nullable', 'string', 'max:100'],
            'district' => ['nullable', 'string', 'max:100'],
            'block'    => ['nullable', 'string', 'max:100'],

            // Certificate
            'certificate_score' => ['nullable', 'string', 'max:50'],
        ]);

        $program = Program::findOrFail($validated['program_id']);

        $validated['student_number']  = app(FileNumberService::class)->nextStudentNumber($program);
        $validated['enrollment_date'] = now()->toDateString();
        $validated['status']          = 'active';
        $validated['finance_status']  = 'pending';

        $student = Student::create($validated);

        app(AuditService::class)->log(
            'created',
            $student,
            [],
            $student->only([
                'student_number', 'program_id', 'status',
                'finance_status', 'full_name', 'phone',
            ])
        );

        return redirect()->route('registrar.students.index')
            ->with('success', 'ØªÙ… Ø¥Ù†Ø´Ø§Ø¡ Ø§Ù„Ø·Ø§Ù„Ø¨ Ø¨Ù†Ø¬Ø§Ø­. Ø§Ù„Ø±Ù‚Ù… Ø§Ù„Ø¬Ø§Ù…Ø¹ÙŠ: ' . $student->student_number);
    }

    public function show(Student $student)
    {
        $student->load(['program', 'user', 'registrations.course', 'invoices']);
        return view('registrar.students.show', compact('student'));
    }

    public function edit(Student $student): View
    {
        $programs = Program::where('is_active', true)->orderBy('name')->get();
        return view('registrar.students.edit', compact('student', 'programs'));
    }

    public function update(Request $request, Student $student): RedirectResponse
    {
        $validated = $request->validate([
            'program_id'      => ['required', 'integer', 'exists:programs,id'],
            'full_name'       => ['required', 'string', 'max:255'],
            'national_id'     => ['nullable', 'string', 'max:30', 'unique:students,national_id,' . $student->id],
            'date_of_birth'   => ['required', 'date', 'before:today'],
            'gender'          => ['required', 'in:male,female'],
            'phone'           => ['required', 'string', 'max:20'],
            'address'         => ['nullable', 'string', 'max:500'],
            'enrollment_date' => ['required', 'date'],
            'status'          => ['required', 'in:active,graduated,suspended,withdrawn'],

            // Contacts (Phase G4-ext)
            'next_of_kin_name'  => ['nullable', 'string', 'max:255'],
            'next_of_kin_phone' => ['nullable', 'string', 'max:20'],
            'mother_name'       => ['nullable', 'string', 'max:255'],
            'mother_phone'      => ['nullable', 'string', 'max:20'],

            // Form 111
            'occupation'              => ['nullable', 'string', 'max:100'],
            'appointment_date'        => ['nullable', 'date'],
            'state'                   => ['nullable', 'string', 'max:100'],
            'district'                => ['nullable', 'string', 'max:100'],
            'block'                   => ['nullable', 'string', 'max:100'],
            'certificate_type'        => ['nullable', 'string', 'max:50'],
            'certificate_date'        => ['nullable', 'date'],
            'certificate_score'       => ['nullable', 'string', 'max:50'],
            'academic_notes'          => ['nullable', 'string', 'max:1000'],
            'registration_fee'        => ['nullable', 'numeric', 'min:0'],
            'registration_fee_words'  => ['nullable', 'string', 'max:255'],
            'tuition_fee'             => ['nullable', 'numeric', 'min:0'],
            'tuition_fee_words'       => ['nullable', 'string', 'max:255'],
            'registration_receipt_no' => ['nullable', 'string', 'max:100'],
            'tuition_receipt_no'      => ['nullable', 'string', 'max:100'],
            'accountant_name'         => ['nullable', 'string', 'max:255'],
            'registrar_name'          => ['nullable', 'string', 'max:255'],
            'photo'                   => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'finance_status'          => ['nullable', 'in:pending,paid,partial'],
        ]);

        if ($request->hasFile('photo')) {
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $validated['photo'] = $request->file('photo')->store('students/photos', 'public');
        }

        $fields = [
            'program_id', 'national_id', 'phone', 'status', 'full_name',
            'state', 'district', 'finance_status',
        ];
        $old = $student->only($fields);

        $student->update($validated);
        $new = $student->only($fields);

        app(AuditService::class)->log(
            'updated',
            $student,
            $old,
            $new
        );

        return redirect()->route('registrar.students.show', $student)
            ->with('success', 'ØªÙ… ØªØ­Ø¯ÙŠØ« Ø¨ÙŠØ§Ù†Ø§Øª Ø§Ù„Ø·Ø§Ù„Ø¨ Ø¨Ù†Ø¬Ø§Ø­');
    }

    public function destroy(Student $student): RedirectResponse
    {
        $old = $student->only(['student_number', 'program_id', 'user_id']);
        $student->delete();

        app(AuditService::class)->log(
            'deleted',
            $student,
            $old,
            []
        );

        return redirect()->route('registrar.students.index')
            ->with('success', 'ØªÙ… Ø­Ø°Ù Ø§Ù„Ø·Ø§Ù„Ø¨ (Ù†Ù‚Ù„ Ø¥Ù„Ù‰ Ø§Ù„Ø£Ø±Ø´ÙŠÙ)');
    }

    public function restore(int $id): RedirectResponse
    {
        $student = Student::withTrashed()->findOrFail($id);
        $student->restore();
        $student->update(['status' => 'active']);

        app(AuditService::class)->log(
            'restored',
            $student,
            ['deleted_at' => 'set'],
            ['status' => 'active']
        );

        return redirect()->route('registrar.students.index')
            ->with('success', 'ØªÙ… Ø§Ø³ØªØ¹Ø§Ø¯Ø© Ø§Ù„Ø·Ø§Ù„Ø¨ Ø¨Ù†Ø¬Ø§Ø­');
    }

    public function forceDestroy(Request $request, int $id): RedirectResponse
    {
        if ($request->input('confirm') !== 'force') {
            return redirect()->route('registrar.students.index')
                ->with('error', 'ÙŠØ¬Ø¨ ØªØ£ÙƒÙŠØ¯ Ø§Ù„Ø­Ø°Ù Ø§Ù„Ù†Ù‡Ø§Ø¦ÙŠ Ø¹Ø¨Ø± Ø¥Ø¶Ø§ÙØ© ?confirm=force');
        }

        $student = Student::withTrashed()->findOrFail($id);
        $old = $student->only(['student_number', 'program_id', 'user_id']);

        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }

        $student->forceDelete();

        app(AuditService::class)->log(
            'force_deleted',
            $student,
            $old,
            []
        );

        return redirect()->route('registrar.students.index')
            ->with('success', 'ØªÙ… Ø§Ù„Ø­Ø°Ù Ø§Ù„Ù†Ù‡Ø§Ø¦ÙŠ Ù„Ù„Ø·Ø§Ù„Ø¨ Ø¨Ù†Ø¬Ø§Ø­');
    }
}