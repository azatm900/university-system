<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Semester;
use App\Models\Student;
use App\Services\AuditService;
use App\Services\FileNumberService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Invoice::with(['student.user', 'semester']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhereHas('student.user', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        if ($studentNumber = $request->input('student_number')) {
            $query->whereHas('student', function ($sq) use ($studentNumber) {
                $sq->where('student_number', 'like', "%{$studentNumber}%")
                   ->orWhereHas('user', function ($uq) use ($studentNumber) {
                       $uq->where('name', 'like', "%{$studentNumber}%");
                   });
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('semester_id')) {
            $query->where('semester_id', $request->input('semester_id'));
        }
        if ($request->filled('student_id')) {
            $query->where('student_id', $request->input('student_id'));
        }

        $invoices  = $query->latest()->paginate(20)->withQueryString();
        $students  = Student::with('user')->orderBy('student_number')->get();
        $semesters = Semester::orderByDesc('start_date')->get();

        return view('finance.invoices.index', compact('invoices', 'students', 'semesters'));
    }

    public function create(): View
    {
        $students  = Student::with('user')->orderBy('student_number')->get();
        $semesters = Semester::orderByDesc('start_date')->get();
        return view('finance.invoices.create', compact('students', 'semesters'));
    }

    public function lookupStudent(Request $request): JsonResponse
    {
        $request->validate([
            'student_number' => ['required', 'string', 'max:20'],
        ]);

        $student = Student::with('user', 'program')
            ->where('student_number', $request->input('student_number'))
            ->first();

        if (! $student) {
            return response()->json([
                'found'   => false,
                'message' => 'لا يوجد طالب بهذا الرقم الجامعي',
            ], 404);
        }

        $financeStatus = $student->finance_status instanceof \BackedEnum
            ? $student->finance_status->value
            : $student->finance_status;

        $dueInvoices = Invoice::where('student_id', $student->id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('due_date')
            ->get()
            ->map(fn ($inv) => [
                'id'             => $inv->id,
                'invoice_number' => $inv->invoice_number,
                'description'    => $inv->description,
                'total'          => (float) $inv->total,
                'paid_amount'    => (float) $inv->paid_amount,
                'remaining'      => (float) ($inv->total - $inv->paid_amount),
                'status'         => $inv->status instanceof \BackedEnum ? $inv->status->value : $inv->status,
                'due_date'       => $inv->due_date?->format('Y-m-d'),
            ]);

        return response()->json([
            'found'   => true,
            'student' => [
                'id'                    => $student->id,
                'student_number'        => $student->student_number,
                'full_name'             => $student->user?->name ?? ('#' . $student->id),
                'program'               => $student->program?->name,
                'finance_status'        => $financeStatus,
                'finance_status_label'  => match ($financeStatus) {
                    'paid'    => 'مدفوع',
                    'partial' => 'مدفوع جزئيًا',
                    'waived'  => 'معفى',
                    default   => 'معلق',
                },
            ],
            'due_invoices' => $dueInvoices,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'student_id'  => ['required', 'integer', 'exists:students,id'],
            'semester_id' => ['nullable', 'integer', 'exists:semesters,id'],
            'description' => ['required', 'string', 'max:255'],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'discount'    => ['nullable', 'numeric', 'min:0'],
            'due_date'    => ['required', 'date'],
        ]);

        $amount   = (float) $validated['amount'];
        $discount = (float) ($validated['discount'] ?? 0);
        if ($discount > $amount) {
            throw ValidationException::withMessages([
                'discount' => 'الخصم لا يمكن أن يتجاوز المبلغ.',
            ]);
        }

        $semesterId    = $validated['semester_id'] ?? null;
        $semester      = $semesterId ? Semester::find($semesterId) : null;
        $invoiceNumber = $semester
            ? app(FileNumberService::class)->nextInvoiceNumber($semester)
            : 'INV-' . now()->format('Y') . '-' . str_pad((string) (((int) Invoice::max('id')) + 1), 4, '0', STR_PAD_LEFT);

        $invoice = Invoice::create([
            'student_id'     => $validated['student_id'],
            'semester_id'    => $validated['semester_id'] ?? null,
            'invoice_number' => $invoiceNumber,
            'description'    => $validated['description'],
            'amount'         => $amount,
            'discount'       => $discount,
            'total'          => $amount - $discount,
            'paid_amount'    => 0,
            'status'         => 'unpaid',
            'due_date'       => $validated['due_date'],
        ]);

        app(AuditService::class)->log(
            'created',
            $invoice,
            [],
            $invoice->only(['invoice_number', 'student_id', 'semester_id', 'total', 'status'])
        );

        return redirect()->route('finance.invoices.index')
            ->with('success', 'تم إنشاء الفاتورة رقم ' . $invoice->invoice_number);
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['student.user', 'semester', 'payments.receiver']);
        return view('finance.invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice): View
    {
        $students  = Student::with('user')->orderBy('student_number')->get();
        $semesters = Semester::orderByDesc('start_date')->get();
        return view('finance.invoices.edit', compact('invoice', 'students', 'semesters'));
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'description' => ['required', 'string', 'max:255'],
            'amount'      => ['required', 'numeric', 'min:0.01'],
            'discount'    => ['nullable', 'numeric', 'min:0'],
            'due_date'    => ['required', 'date'],
        ]);

        $amount   = (float) $validated['amount'];
        $discount = (float) ($validated['discount'] ?? 0);
        if ($discount > $amount) {
            throw ValidationException::withMessages([
                'discount' => 'الخصم لا يمكن أن يتجاوز المبلغ.',
            ]);
        }
        $newTotal    = $amount - $discount;
        $alreadyPaid = (float) Payment::where('invoice_id', $invoice->id)->sum('amount');
        if ($newTotal < $alreadyPaid) {
            throw ValidationException::withMessages([
                'amount' => 'المبلغ الإجمالي الجديد أقل من المبلغ المدفوع (' . number_format($alreadyPaid, 2) . ').',
            ]);
        }

        $old = $invoice->only(['amount', 'discount', 'total', 'description', 'due_date']);
        $invoice->update([
            'description' => $validated['description'],
            'amount'      => $amount,
            'discount'    => $discount,
            'total'       => $newTotal,
            'due_date'    => $validated['due_date'],
        ]);
        $new = $invoice->only(['amount', 'discount', 'total', 'description', 'due_date']);

        app(AuditService::class)->log('updated', $invoice, $old, $new);

        return redirect()->route('finance.invoices.show', $invoice)
            ->with('success', 'تم تحديث الفاتورة');
    }

    public function cancel(Invoice $invoice): RedirectResponse
    {
        $currentStatus = $invoice->status instanceof \BackedEnum ? $invoice->status->value : $invoice->status;
        if ($currentStatus === 'cancelled') {
            return redirect()->route('finance.invoices.index')
                ->with('error', 'الفاتورة ملغاة بالفعل.');
        }
        $payCount = Payment::where('invoice_id', $invoice->id)->count();
        if ($payCount > 0) {
            return redirect()->route('finance.invoices.index')
                ->with('error', 'لا يمكن إلغاء فاتورة عليها ' . $payCount . ' دفعة.');
        }
        $invoice->update(['status' => 'cancelled']);
        app(AuditService::class)->log(
            'updated',
            $invoice,
            ['status' => $currentStatus],
            ['status' => 'cancelled']
        );
        return redirect()->route('finance.invoices.index')
            ->with('success', 'تم إلغاء الفاتورة');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $payCount = Payment::where('invoice_id', $invoice->id)->count();
        if ($payCount > 0) {
            return redirect()->route('finance.invoices.index')
                ->with('error', 'لا يمكن حذف فاتورة عليها ' . $payCount . ' دفعة.');
        }
        $old = $invoice->only(['invoice_number', 'total', 'status']);
        $invoice->delete();
        app(AuditService::class)->log('deleted', $invoice, $old, []);
        return redirect()->route('finance.invoices.index')
            ->with('success', 'تم حذف الفاتورة (Soft Delete)');
    }

    public function forceDestroy(Request $request, int $id): RedirectResponse
    {
        if ($request->input('confirm') !== 'force') {
            return redirect()->route('finance.invoices.index')
                ->with('error', 'يجب تأكيد الحذف النهائي بـ ?confirm=force');
        }
        $invoice = Invoice::withTrashed()->findOrFail($id);
        $payCount = Payment::withTrashed()->where('invoice_id', $invoice->id)->count();
        if ($payCount > 0) {
            return redirect()->route('finance.invoices.index')
                ->with('error', 'لا يمكن الحذف النهائي لوجود ' . $payCount . ' دفعة (حتى المحذوفة). احذف الدفعات أولاً.');
        }
        $old = $invoice->only(['invoice_number', 'total', 'status']);
        $invoice->forceDelete();
        app(AuditService::class)->log('force_deleted', $invoice, $old, []);
        return redirect()->route('finance.invoices.index')
            ->with('success', 'تم الحذف النهائي للفاتورة');
    }
}