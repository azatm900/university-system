$ErrorActionPreference = 'Stop'
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
$root = "C:\Users\maham\Herd\university-system"
Set-Location $root

function Write-File {
    param([string]$Rel, [string]$Content)
    $full = Join-Path $root $Rel
    $dir  = Split-Path $full -Parent
    if (-not (Test-Path $dir)) { New-Item -ItemType Directory -Force -Path $dir | Out-Null }
    if (Test-Path $full) { Copy-Item $full "$full.bak" -Force; Write-Host "  [BAK] $Rel.bak" -ForegroundColor DarkGray }
    [IO.File]::WriteAllText($full, $Content, (New-Object System.Text.UTF8Encoding($false)))
    Write-Host "  [WRT] $Rel" -ForegroundColor Green
}

function Lint-Php {
    param([string]$Rel)
    $out = & php -l (Join-Path $root $Rel) 2>&1 | Out-String
    if ($out -match 'No syntax errors') { Write-Host "  [OK ] php -l $Rel" -ForegroundColor Green }
    else { Write-Host "  [ERR] php -l $Rel" -ForegroundColor Red; Write-Host $out -ForegroundColor Red; throw "Syntax error in $Rel" }
}

Write-Host "═══ F1: InvoiceController refactor ═══" -ForegroundColor Cyan

$invoiceCtrl = @'
<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Semester;
use App\Models\Student;
use App\Services\AuditService;
use App\Services\FileNumberService;
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
'@

Write-File "app/Http/Controllers/Finance/InvoiceController.php" $invoiceCtrl
Lint-Php "app/Http/Controllers/Finance/InvoiceController.php"

Write-Host "`n═══ F1: PaymentController refactor (+ edit/update) ═══" -ForegroundColor Cyan

$paymentCtrl = @'
<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Payment::with(['invoice.student.user', 'receiver']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                  ->orWhereHas('invoice', function ($iq) use ($search) {
                      $iq->where('invoice_number', 'like', "%{$search}%");
                  });
            });
        }
        if ($request->filled('method')) {
            $query->where('method', $request->input('method'));
        }
        if ($request->filled('invoice_id')) {
            $query->where('invoice_id', $request->input('invoice_id'));
        }

        $payments = $query->latest()->paginate(20)->withQueryString();
        $invoices = Invoice::whereIn('status', ['unpaid', 'partial'])->orderBy('invoice_number')->get();

        return view('finance.payments.index', compact('payments', 'invoices'));
    }

    public function create(): View
    {
        $invoices = Invoice::with('student.user')
            ->whereIn('status', ['unpaid', 'partial'])
            ->orderBy('invoice_number')
            ->get();
        return view('finance.payments.create', compact('invoices'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'invoice_id'       => ['required', 'integer', 'exists:invoices,id'],
            'amount'           => ['required', 'numeric', 'min:0.01'],
            'payment_date'     => ['required', 'date'],
            'method'           => ['required', 'in:cash,bank_transfer,card,check'],
            'reference_number' => ['nullable', 'string', 'max:50'],
            'received_by'      => ['nullable', 'integer', 'exists:users,id'],
            'notes'            => ['nullable', 'string'],
        ]);

        $invoice = Invoice::findOrFail($validated['invoice_id']);

        $invoiceStatus = $invoice->status instanceof \BackedEnum ? $invoice->status->value : $invoice->status;
        if ($invoiceStatus === 'cancelled') {
            throw ValidationException::withMessages([
                'invoice_id' => 'لا يمكن تسجيل دفعة على فاتورة ملغاة.',
            ]);
        }

        $alreadyPaid = (float) Payment::where('invoice_id', $invoice->id)->sum('amount');
        $remaining   = (float) $invoice->total - $alreadyPaid;
        if ((float) $validated['amount'] > $remaining) {
            throw ValidationException::withMessages([
                'amount' => 'المبلغ يتجاوز المتبقي (' . number_format($remaining, 2) . ').',
            ]);
        }

        $payment = Payment::create([
            'invoice_id'       => $invoice->id,
            'amount'           => (float) $validated['amount'],
            'payment_date'     => $validated['payment_date'],
            'method'           => $validated['method'],
            'reference_number' => $validated['reference_number'] ?? null,
            'received_by'      => $validated['received_by'] ?? null,
            'notes'            => $validated['notes'] ?? null,
        ]);

        app(AuditService::class)->log(
            'created',
            $payment,
            [],
            $payment->only(['invoice_id', 'amount', 'method', 'payment_date'])
        );

        $this->recalcInvoice($invoice);

        return redirect()->route('finance.payments.index')
            ->with('success', 'تم تسجيل الدفعة');
    }

    public function show(Payment $payment): View
    {
        $payment->load(['invoice.student.user', 'receiver']);
        return view('finance.payments.show', compact('payment'));
    }

    public function edit(Payment $payment): View
    {
        $payment->load(['invoice.student.user']);
        return view('finance.payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $validated = $request->validate([
            'reference_number' => ['nullable', 'string', 'max:50'],
            'notes'            => ['nullable', 'string'],
        ]);

        $old = $payment->only(['reference_number', 'notes']);
        $payment->update($validated);
        $new = $payment->only(['reference_number', 'notes']);

        app(AuditService::class)->log('updated', $payment, $old, $new);

        return redirect()->route('finance.payments.show', $payment)
            ->with('success', 'تم تحديث الدفعة');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $invoice = $this->resolveInvoice($payment);
        $old = $payment->only(['invoice_id', 'amount', 'method']);
        $payment->delete();
        app(AuditService::class)->log('deleted', $payment, $old, []);
        if ($invoice) { $this->recalcInvoice($invoice); }
        return redirect()->route('finance.payments.index')
            ->with('success', 'تم حذف الدفعة (Soft Delete)');
    }

    public function restore(int $id): RedirectResponse
    {
        $payment = Payment::withTrashed()->findOrFail($id);
        $payment->restore();
        app(AuditService::class)->log('restored', $payment, ['deleted_at' => 'set'], []);
        $invoice = $this->resolveInvoice($payment);
        if ($invoice) { $this->recalcInvoice($invoice); }
        return redirect()->route('finance.payments.index')
            ->with('success', 'تم استعادة الدفعة');
    }

    public function forceDestroy(Request $request, int $id): RedirectResponse
    {
        if ($request->input('confirm') !== 'force') {
            return redirect()->route('finance.payments.index')
                ->with('error', 'يجب تأكيد الحذف النهائي بـ ?confirm=force');
        }
        $payment = Payment::withTrashed()->findOrFail($id);
        $invoice = $this->resolveInvoice($payment);
        $old = $payment->only(['invoice_id', 'amount', 'method']);
        $payment->forceDelete();
        app(AuditService::class)->log('force_deleted', $payment, $old, []);
        if ($invoice) { $this->recalcInvoice($invoice); }
        return redirect()->route('finance.payments.index')
            ->with('success', 'تم الحذف النهائي للدفعة');
    }

    private function resolveInvoice(Payment $payment): ?Invoice
    {
        return Invoice::withTrashed()->find($payment->invoice_id);
    }

    private function recalcInvoice(Invoice $invoice): void
    {
        $invoice->refresh();
        $paidActive = (float) Payment::where('invoice_id', $invoice->id)->sum('amount');
        $paidCents  = (int) round($paidActive * 100);
        $totalCents = (int) round(((float) $invoice->total) * 100);

        if ($paidCents <= 0) {
            $newStatus = 'unpaid';
        } elseif ($paidCents >= $totalCents) {
            $newStatus = 'paid';
        } else {
            $newStatus = 'partial';
        }

        $oldStatus = $invoice->status instanceof \BackedEnum ? $invoice->status->value : $invoice->status;

        $invoice->update([
            'paid_amount' => $paidActive,
            'status'      => $newStatus,
        ]);

        if ($oldStatus !== $newStatus) {
            app(AuditService::class)->log(
                'updated',
                $invoice,
                ['status' => $oldStatus],
                ['status' => $newStatus, 'paid_amount' => $paidActive]
            );
        }
    }
}
'@

Write-File "app/Http/Controllers/Finance/PaymentController.php" $paymentCtrl
Lint-Php "app/Http/Controllers/Finance/PaymentController.php"

Write-Host "`n═══ F1: Lang files (6 new) ═══" -ForegroundColor Cyan

$arInvoices = @'
<?php

return [
    'title'    => 'الفواتير',
    'subtitle' => 'إدارة فواتير الطلاب',
    'singular' => 'فاتورة',

    'fields' => [
        'invoice_number' => 'رقم الفاتورة',
        'student'        => 'الطالب',
        'semester'       => 'الفصل الدراسي',
        'description'    => 'الوصف',
        'amount'         => 'المبلغ',
        'discount'       => 'الخصم',
        'total'          => 'الإجمالي',
        'paid_amount'    => 'المدفوع',
        'remaining'      => 'المتبقي',
        'status'         => 'الحالة',
        'due_date'       => 'تاريخ الاستحقاق',
    ],

    'statuses' => [
        'unpaid'    => 'غير مدفوعة',
        'partial'   => 'مدفوعة جزئياً',
        'paid'      => 'مدفوعة',
        'cancelled' => 'ملغاة',
    ],

    'filters' => [
        'search'             => 'بحث',
        'search_placeholder' => 'رقم الفاتورة أو اسم الطالب...',
        'all'                => 'الكل',
    ],

    'actions' => [
        'create'      => 'إضافة فاتورة',
        'edit'        => 'تعديل الفاتورة',
        'view'        => 'عرض الفاتورة',
        'save'        => 'حفظ',
        'cancel'      => 'إلغاء',
        'back'        => 'رجوع',
        'add_payment' => 'تسجيل دفعة',
    ],

    'empty' => [
        'title'       => 'لا توجد فواتير',
        'description' => 'لم يتم إضافة أي فاتورة بعد',
    ],

    'messages' => [
        'confirm_delete' => 'هل تريد حذف هذه الفاتورة؟',
    ],

    'show' => [
        'info'        => 'بيانات الفاتورة',
        'payments'    => 'الدفعات',
        'no_payments' => 'لا توجد دفعات على هذه الفاتورة',
    ],
];
'@

$enInvoices = @'
<?php

return [
    'title'    => 'Invoices',
    'subtitle' => 'Manage student invoices',
    'singular' => 'Invoice',

    'fields' => [
        'invoice_number' => 'Invoice #',
        'student'        => 'Student',
        'semester'       => 'Semester',
        'description'    => 'Description',
        'amount'         => 'Amount',
        'discount'       => 'Discount',
        'total'          => 'Total',
        'paid_amount'    => 'Paid',
        'remaining'      => 'Remaining',
        'status'         => 'Status',
        'due_date'       => 'Due Date',
    ],

    'statuses' => [
        'unpaid'    => 'Unpaid',
        'partial'   => 'Partial',
        'paid'      => 'Paid',
        'cancelled' => 'Cancelled',
    ],

    'filters' => [
        'search'             => 'Search',
        'search_placeholder' => 'Invoice # or student name...',
        'all'                => 'All',
    ],

    'actions' => [
        'create'      => 'Add Invoice',
        'edit'        => 'Edit Invoice',
        'view'        => 'View Invoice',
        'save'        => 'Save',
        'cancel'      => 'Cancel',
        'back'        => 'Back',
        'add_payment' => 'Add Payment',
    ],

    'empty' => [
        'title'       => 'No Invoices',
        'description' => 'No invoices have been added yet',
    ],

    'messages' => [
        'confirm_delete' => 'Are you sure you want to delete this invoice?',
    ],

    'show' => [
        'info'        => 'Invoice Information',
        'payments'    => 'Payments',
        'no_payments' => 'No payments for this invoice',
    ],
];
'@

$arPayments = @'
<?php

return [
    'title'    => 'الدفعات',
    'subtitle' => 'إدارة دفعات الفواتير',
    'singular' => 'دفعة',

    'fields' => [
        'invoice'          => 'الفاتورة',
        'student'          => 'الطالب',
        'amount'           => 'المبلغ',
        'payment_date'     => 'تاريخ الدفع',
        'method'           => 'طريقة الدفع',
        'reference_number' => 'رقم المرجع',
        'receiver'         => 'المستلم',
        'notes'            => 'ملاحظات',
    ],

    'methods' => [
        'cash'          => 'نقدي',
        'bank_transfer' => 'حوالة بنكية',
        'card'          => 'بطاقة',
        'check'         => 'شيك',
    ],

    'filters' => [
        'search'             => 'بحث',
        'search_placeholder' => 'رقم الفاتورة أو المرجع...',
        'all'                => 'الكل',
    ],

    'actions' => [
        'create' => 'تسجيل دفعة',
        'edit'   => 'تعديل الدفعة',
        'view'   => 'عرض الدفعة',
        'save'   => 'حفظ',
        'cancel' => 'إلغاء',
        'back'   => 'رجوع',
    ],

    'empty' => [
        'title'       => 'لا توجد دفعات',
        'description' => 'لم يتم تسجيل أي دفعة بعد',
    ],

    'messages' => [
        'confirm_delete' => 'هل تريد حذف هذه الدفعة؟',
        'edit_note'      => 'لا يمكن تعديل المبلغ أو الطريقة. احذف الدفعة وأعد تسجيلها إن لزم.',
    ],
];
'@

$enPayments = @'
<?php

return [
    'title'    => 'Payments',
    'subtitle' => 'Manage invoice payments',
    'singular' => 'Payment',

    'fields' => [
        'invoice'          => 'Invoice',
        'student'          => 'Student',
        'amount'           => 'Amount',
        'payment_date'     => 'Payment Date',
        'method'           => 'Method',
        'reference_number' => 'Reference #',
        'receiver'         => 'Received By',
        'notes'            => 'Notes',
    ],

    'methods' => [
        'cash'          => 'Cash',
        'bank_transfer' => 'Bank Transfer',
        'card'          => 'Card',
        'check'         => 'Check',
    ],

    'filters' => [
        'search'             => 'Search',
        'search_placeholder' => 'Invoice # or reference...',
        'all'                => 'All',
    ],

    'actions' => [
        'create' => 'Add Payment',
        'edit'   => 'Edit Payment',
        'view'   => 'View Payment',
        'save'   => 'Save',
        'cancel' => 'Cancel',
        'back'   => 'Back',
    ],

    'empty' => [
        'title'       => 'No Payments',
        'description' => 'No payments have been recorded yet',
    ],

    'messages' => [
        'confirm_delete' => 'Are you sure you want to delete this payment?',
        'edit_note'      => 'Amount and method cannot be changed. Delete and re-record if needed.',
    ],
];
'@

$arReports = @'
<?php

return [
    'title'    => 'التقارير',
    'subtitle' => 'التقارير والإحصائيات',

    'student_transcript'          => 'كشف درجات طالب',
    'student_transcript_desc'     => 'سجل درجات طالب مع GPA التراكمي',
    'semester_registrations'      => 'تسجيلات الفصل',
    'semester_registrations_desc' => 'قائمة بكل تسجيلات فصل دراسي',
    'student_financial'           => 'الحالة المالية للطالب',
    'student_financial_desc'      => 'الفواتير والمدفوعات والرصيد',
    'semester_revenue'            => 'إيرادات الفصل',
    'semester_revenue_desc'       => 'إجمالي المدفوعات حسب طريقة الدفع',
    'general_statistics'          => 'إحصائيات عامة',
    'general_statistics_desc'     => 'نظرة شاملة على النظام',

    'actions' => [
        'view'  => 'عرض',
        'excel' => 'Excel',
        'pdf'   => 'PDF',
    ],
];
'@

$enReports = @'
<?php

return [
    'title'    => 'Reports',
    'subtitle' => 'Reports and statistics',

    'student_transcript'          => 'Student Transcript',
    'student_transcript_desc'     => 'Student grade record with cumulative GPA',
    'semester_registrations'      => 'Semester Registrations',
    'semester_registrations_desc' => 'All registrations for a semester',
    'student_financial'           => 'Student Financial',
    'student_financial_desc'      => 'Invoices, payments and balance',
    'semester_revenue'            => 'Semester Revenue',
    'semester_revenue_desc'       => 'Total payments by method',
    'general_statistics'          => 'General Statistics',
    'general_statistics_desc'     => 'System-wide overview',

    'actions' => [
        'view'  => 'View',
        'excel' => 'Excel',
        'pdf'   => 'PDF',
    ],
];
'@

Write-File "lang/ar/invoices.php" $arInvoices
Write-File "lang/en/invoices.php" $enInvoices
Write-File "lang/ar/payments.php" $arPayments
Write-File "lang/en/payments.php" $enPayments
Write-File "lang/ar/reports.php" $arReports
Write-File "lang/en/reports.php" $enReports

Lint-Php "lang/ar/invoices.php"
Lint-Php "lang/en/invoices.php"
Lint-Php "lang/ar/payments.php"
Lint-Php "lang/en/payments.php"
Lint-Php "lang/ar/reports.php"
Lint-Php "lang/en/reports.php"

Write-Host "`n═══ Clear blade cache ═══" -ForegroundColor Cyan
& php artisan view:clear

Write-Host "`n═══ F1 Complete ═══" -ForegroundColor Green
Write-Host "Next: phaseF-02-invoices-views.ps1" -ForegroundColor Yellow