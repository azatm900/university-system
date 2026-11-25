@extends('layouts.app')

@section('title', 'تسجيل دفعة مالية')

@section('content')
<div class="max-w-5xl mx-auto py-6 px-4" dir="rtl">
    <h1 class="text-2xl font-bold mb-6 text-gray-800">💵 تسجيل دفعة مالية</h1>

    @if ($errors->any())
        <div class="bg-red-100 border-r-4 border-red-500 text-red-700 p-4 mb-4 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- البحث --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <label class="block text-sm font-semibold text-gray-700 mb-2">🔍 البحث برقم النظام الجامعي</label>
        <div class="flex gap-2">
            <input type="text" id="student_number" placeholder="مثال: 2026140001"
                   class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500">
            <button type="button" id="btn-lookup"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-semibold">
                بحث
            </button>
        </div>
        <p id="lookup-msg" class="mt-3 text-sm"></p>
    </div>

    {{-- بطاقة الطالب --}}
    <div id="student-card" class="hidden bg-gradient-to-l from-blue-50 to-indigo-50 rounded-lg shadow p-6 mb-6 border border-blue-200">
        <h2 class="text-lg font-bold text-indigo-800 mb-4">📋 بيانات الطالب</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
            <div><span class="text-gray-500">الاسم:</span> <strong id="s-name" class="text-gray-900"></strong></div>
            <div><span class="text-gray-500">رقم النظام:</span> <strong id="s-number"></strong></div>
            <div><span class="text-gray-500">البرنامج:</span> <strong id="s-program"></strong></div>
            <div><span class="text-gray-500">الرسوم الدراسية:</span> <strong id="s-tuition" class="text-blue-700"></strong></div>
            <div><span class="text-gray-500">رسوم التسجيل:</span> <strong id="s-registration" class="text-blue-700"></strong></div>
            <div><span class="text-gray-500">إجمالي الرسوم:</span> <strong id="s-total" class="text-green-700 text-base"></strong></div>
        </div>
    </div>

    {{-- النموذج --}}
    <form id="payment-form" method="POST" action="{{ route('finance.payments.store') }}"
          enctype="multipart/form-data" class="hidden bg-white rounded-lg shadow p-6">
        @csrf
        <input type="hidden" name="invoice_id" id="invoice_id">

        <div class="mb-4">
            <label class="block text-sm font-semibold mb-2">🧾 اختر الفاتورة المستحقة</label>
            <select id="invoice-select" class="w-full border border-gray-300 rounded-lg px-4 py-2">
                <option value="">— اختر فاتورة —</option>
            </select>
        </div>

        <div id="invoice-details" class="hidden bg-gray-50 rounded-lg p-4 mb-4 text-sm">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div><span class="text-gray-500">رقم الفاتورة:</span> <strong id="inv-number"></strong></div>
                <div><span class="text-gray-500">الإجمالي:</span> <strong id="inv-total"></strong></div>
                <div><span class="text-gray-500">المدفوع سابقًا:</span> <strong id="inv-paid" class="text-green-700"></strong></div>
                <div><span class="text-gray-500">المتبقي:</span> <strong id="inv-remaining" class="text-red-700"></strong></div>
            </div>
            <div class="mt-2 text-xs text-indigo-700">
                💡 هذه ستكون الدفعة رقم <strong id="inv-installment">1</strong>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-semibold mb-1">💰 المبلغ المدفوع <span class="text-red-500">*</span></label>
                <input type="number" name="amount" id="amount" step="0.01" min="0.01" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2">
                <p class="text-xs text-gray-500 mt-1">المتبقي بعد هذه الدفعة: <strong id="remaining-after" class="text-red-600">—</strong></p>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">📅 تاريخ الدفع <span class="text-red-500">*</span></label>
                <input type="date" name="payment_date" value="{{ date('Y-m-d') }}" required
                       class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">💳 طريقة الدفع <span class="text-red-500">*</span></label>
                <select name="method" id="method" required
                        class="w-full border border-gray-300 rounded-lg px-4 py-2">
                    <option value="cash">💵 نقدي</option>
                    <option value="bank">🏦 بنكي</option>
                    <option value="cheque">📝 شيك</option>
                </select>
            </div>
            <div id="ref-field" class="hidden">
                <label class="block text-sm font-semibold mb-1">🔢 رقم العملية / الشيك</label>
                <input type="text" name="reference_number" id="reference_number"
                       class="w-full border border-gray-300 rounded-lg px-4 py-2">
            </div>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-semibold mb-1">📷 صورة الإشعار (اختياري)</label>
            <input type="file" name="receipt_image" accept="image/*"
                   class="w-full border border-gray-300 rounded-lg px-4 py-2 text-sm">
            <p class="text-xs text-gray-500 mt-1">JPG / PNG — حتى 4 ميجا</p>
        </div>

        <div class="mt-4">
            <label class="block text-sm font-semibold mb-1">📝 ملاحظات (تظهر على الإيصال)</label>
            <textarea name="notes" rows="2" maxlength="500"
                      class="w-full border border-gray-300 rounded-lg px-4 py-2"
                      placeholder="مثال: الدفعة الأولى من أصل ثلاث دفعات"></textarea>
        </div>

        <div class="mt-6 flex gap-3">
            <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-lg font-bold">
                ✅ تسجيل الدفعة وإصدار الإيصال
            </button>
            <a href="{{ route('finance.payments.index') }}"
               class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-3 rounded-lg font-semibold">
                إلغاء
            </a>
        </div>
    </form>
</div>

<script>
const lookupBtn = document.getElementById('btn-lookup');
const studentNumberInput = document.getElementById('student_number');
const msgEl = document.getElementById('lookup-msg');
const cardEl = document.getElementById('student-card');
const formEl = document.getElementById('payment-form');
const invoiceSelect = document.getElementById('invoice-select');
const invoiceDetails = document.getElementById('invoice-details');
let currentInvoices = [];
let currentRemaining = 0;

function fmt(n) { return (Number(n) || 0).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2}); }

async function doLookup() {
    const num = studentNumberInput.value.trim();
    if (!num) return;
    msgEl.textContent = 'جارٍ البحث...';
    msgEl.className = 'mt-3 text-sm text-blue-600';
    cardEl.classList.add('hidden');
    formEl.classList.add('hidden');

    try {
        const url = `{{ route('finance.students.lookup') }}?student_number=${encodeURIComponent(num)}`;
        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
        const data = await res.json();

        if (!data.found) {
            msgEl.textContent = data.message || 'لا يوجد طالب بهذا الرقم';
            msgEl.className = 'mt-3 text-sm text-red-600';
            return;
        }

        msgEl.textContent = '✅ تم العثور على الطالب';
        msgEl.className = 'mt-3 text-sm text-green-600';

        document.getElementById('s-name').textContent = data.student.full_name;
        document.getElementById('s-number').textContent = data.student.student_number;
        document.getElementById('s-program').textContent = data.student.program || '—';
        document.getElementById('s-tuition').textContent = fmt(data.student.tuition_fee);
        document.getElementById('s-registration').textContent = fmt(data.student.registration_fee);
        document.getElementById('s-total').textContent = fmt(data.student.total_fees);
        cardEl.classList.remove('hidden');

        currentInvoices = data.due_invoices || [];
        invoiceSelect.innerHTML = '<option value="">— اختر فاتورة —</option>';
        currentInvoices.forEach(inv => {
            const opt = document.createElement('option');
            opt.value = inv.id;
            opt.textContent = `${inv.invoice_number} — متبقي: ${fmt(inv.remaining)}`;
            opt.dataset.inv = JSON.stringify(inv);
            invoiceSelect.appendChild(opt);
        });
        if (currentInvoices.length === 0) {
            invoiceSelect.innerHTML = '<option value="">لا توجد فواتير مستحقة</option>';
        }
        formEl.classList.remove('hidden');
    } catch (e) {
        msgEl.textContent = 'حدث خطأ في الاتصال';
        msgEl.className = 'mt-3 text-sm text-red-600';
    }
}

lookupBtn.addEventListener('click', doLookup);
studentNumberInput.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); doLookup(); } });

invoiceSelect.addEventListener('change', () => {
    const opt = invoiceSelect.selectedOptions[0];
    if (!opt || !opt.dataset.inv) { invoiceDetails.classList.add('hidden'); currentRemaining = 0; return; }
    const inv = JSON.parse(opt.dataset.inv);
    currentRemaining = inv.remaining;
    document.getElementById('invoice_id').value = inv.id;
    document.getElementById('inv-number').textContent = inv.invoice_number;
    document.getElementById('inv-total').textContent = fmt(inv.total);
    document.getElementById('inv-paid').textContent = fmt(inv.paid_amount);
    document.getElementById('inv-remaining').textContent = fmt(inv.remaining);
    document.getElementById('inv-installment').textContent = inv.next_installment_no || 1;
    invoiceDetails.classList.remove('hidden');
    updateRemainingAfter();
});

document.getElementById('amount').addEventListener('input', updateRemainingAfter);

function updateRemainingAfter() {
    const amount = parseFloat(document.getElementById('amount').value) || 0;
    const after = Math.max(0, currentRemaining - amount);
    document.getElementById('remaining-after').textContent = fmt(after);
}

document.getElementById('method').addEventListener('change', e => {
    const refField = document.getElementById('ref-field');
    if (e.target.value === 'bank' || e.target.value === 'cheque') {
        refField.classList.remove('hidden');
    } else {
        refField.classList.add('hidden');
        document.getElementById('reference_number').value = '';
    }
});
</script>
@endsection