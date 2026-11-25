@extends('layouts.app')

@section('title', __('payments.actions.create'))

@section('content')
<x-page-header :title="__('payments.actions.create')" :subtitle="__('payments.subtitle')" />

<form method="POST" action="{{ route('finance.payments.store') }}">
    @csrf
    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">رقم النظام الجامعي <span class="text-red-500">*</span></label>
                <div class="flex gap-2">
                    <input type="text" id="student_number_input" class="input font-mono"
                           placeholder="2026140001" maxlength="20">
                    <x-button type="button" variant="primary" id="student_lookup_btn">
                        <x-icon name="search" :size="16" /> بحث
                    </x-button>
                </div>
                <div id="student_lookup_error" class="hidden text-red-500 text-sm mt-1"></div>
            </div>

            <div id="student_card" class="md:col-span-2 hidden">
                <div class="border rounded-lg p-4 bg-gray-50 dark:bg-gray-800">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-sm">
                        <div><span class="text-gray-500">رقم النظام:</span> <span id="sc_number" class="font-mono font-bold"></span></div>
                        <div><span class="text-gray-500">الاسم:</span> <span id="sc_name"></span></div>
                        <div><span class="text-gray-500">البرنامج:</span> <span id="sc_program">-</span></div>
                        <div><span class="text-gray-500">الحالة المالية:</span> <span id="sc_finance"></span></div>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-sm mb-1">{{ __('payments.fields.invoice') }} <span class="text-red-500">*</span></label>
                <select name="invoice_id" id="invoice_select" class="input" required disabled>
                    <option value="">— اختر فاتورة —</option>
                </select>
                @error('invoice_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('payments.fields.amount') }} <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" class="input" required>
                @error('amount') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('payments.fields.payment_date') }} <span class="text-red-500">*</span></label>
                <input type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" class="input" required>
                @error('payment_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('payments.fields.method') }} <span class="text-red-500">*</span></label>
                <select name="method" class="input" required>
                    @foreach(\App\Enums\PaymentMethod::cases() as $case)
                        <option value="{{ $case->value }}" @selected(old('method', 'cash') === $case->value)>{{ __('payments.methods.' . $case->value) }}</option>
                    @endforeach
                </select>
                @error('method') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('payments.fields.reference_number') }}</label>
                <input type="text" name="reference_number" value="{{ old('reference_number') }}" class="input" maxlength="50">
                @error('reference_number') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">{{ __('payments.fields.notes') }}</label>
                <textarea name="notes" class="input" rows="3">{{ old('notes') }}</textarea>
                @error('notes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <x-slot name="footer">
            <div class="flex gap-2">
                <x-button type="submit" variant="primary">{{ __('payments.actions.save') }}</x-button>
                <x-button variant="secondary" :href="route('finance.payments.index')">{{ __('payments.actions.cancel') }}</x-button>
            </div>
        </x-slot>
    </x-card>
</form>

@push('scripts')
<script>
(function () {
    const lookupUrl = '{{ route('finance.students.lookup') }}';
    const dueBase   = '{{ url('finance/students') }}';
    const btn       = document.getElementById('student_lookup_btn');
    const input     = document.getElementById('student_number_input');
    const errBox    = document.getElementById('student_lookup_error');
    const card      = document.getElementById('student_card');
    const invoiceSel= document.getElementById('invoice_select');

    function showError(msg) {
        errBox.textContent = msg;
        errBox.classList.remove('hidden');
        card.classList.add('hidden');
        invoiceSel.disabled = true;
        invoiceSel.innerHTML = '<option value="">— اختر فاتورة —</option>';
    }

    async function loadDueInvoices(studentId) {
        const res  = await fetch(`${dueBase}/${studentId}/due-invoices`, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        });
        const data = await res.json();
        invoiceSel.innerHTML = '<option value="">— اختر فاتورة —</option>';
        if (data.invoices && data.invoices.length > 0) {
            data.invoices.forEach(inv => {
                const opt = document.createElement('option');
                opt.value = inv.id;
                opt.textContent = `${inv.invoice_number} — متبقي: ${inv.remaining.toFixed(2)} (استحقاق: ${inv.due_date || '-'})`;
                invoiceSel.appendChild(opt);
            });
            invoiceSel.disabled = false;
        } else {
            invoiceSel.innerHTML = '<option value="">— لا توجد فواتير مستحقة —</option>';
            invoiceSel.disabled = true;
        }
    }

    async function doLookup() {
        const sn = (input.value || '').trim();
        if (! sn) { showError('أدخل رقم النظام الجامعي'); return; }
        btn.disabled = true;
        try {
            const res  = await fetch(`${lookupUrl}?student_number=${encodeURIComponent(sn)}`, {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (! res.ok || ! data.found) {
                showError(data.message || 'لا يوجد طالب بهذا الرقم');
            } else {
                errBox.classList.add('hidden');
                card.classList.remove('hidden');
                document.getElementById('sc_number').textContent  = data.student.student_number;
                document.getElementById('sc_name').textContent    = data.student.full_name;
                document.getElementById('sc_program').textContent = data.student.program || '-';
                document.getElementById('sc_finance').textContent = data.student.finance_status_label || data.student.finance_status;
                await loadDueInvoices(data.student.id);
            }
        } catch (e) {
            showError('خطأ في الاتصال بالخادم');
        } finally {
            btn.disabled = false;
        }
    }

    btn.addEventListener('click', doLookup);
    input.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); doLookup(); } });
})();
</script>
@endpush
@endsection