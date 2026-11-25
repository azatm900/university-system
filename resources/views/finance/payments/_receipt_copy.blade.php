{{-- _receipt_copy.blade.php — نسخة إيصال قابلة لإعادة الاستخدام --}}
{{-- تُستخدم داخل receipt.blade.php لعرض نسختين (طالب / مالية) --}}

@php
    $copyLabel = $copyLabel ?? 'نسخة';
    $payment   = $payment ?? null;
    $student   = $payment?->student;
    $invoice   = $payment?->invoice;
@endphp

@if($payment)
<div class="receipt-copy" dir="rtl" style="
    border: 1px dashed #999;
    padding: 12px;
    margin-bottom: 12px;
    font-family: 'Cairo', sans-serif;
    font-size: 12px;
    color: #111;
    background: #fff;
">
    {{-- الترويسة --}}
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #1e3a8a; padding-bottom: 6px; margin-bottom: 8px;">
        <div style="display: flex; align-items: center; gap: 8px;">
            <img src="{{ asset('images/logo.png') }}"
                 alt="شعار الجامعة"
                 style="height: 42px; width: 42px; object-fit: contain;"
                 onerror="this.style.display='none'">
            <div>
                <div style="font-weight: 700; font-size: 13px; color: #1e3a8a;">جامعة النظام</div>
                <div style="font-size: 10px; color: #555;">الإدارة المالية</div>
            </div>
        </div>
        <div style="text-align: left;">
            <div style="font-weight: 700; font-size: 13px; color: #1e3a8a;">إيصال دفع</div>
            <div style="font-size: 10px; color: #555;">{{ $copyLabel }}</div>
        </div>
    </div>

    {{-- بيانات الإيصال --}}
    <table style="width: 100%; border-collapse: collapse; font-size: 11px;">
        <tr>
            <td style="padding: 3px 6px; font-weight: 600; width: 22%;">رقم الإيصال:</td>
            <td style="padding: 3px 6px;">{{ $payment->receipt_number ?? $payment->id }}</td>
            <td style="padding: 3px 6px; font-weight: 600; width: 22%;">التاريخ:</td>
            <td style="padding: 3px 6px;">{{ optional($payment->paid_at ?? $payment->created_at)->format('Y-m-d H:i') }}</td>
        </tr>
        <tr>
            <td style="padding: 3px 6px; font-weight: 600;">اسم الطالب:</td>
            <td style="padding: 3px 6px;">{{ $student->name ?? '—' }}</td>
            <td style="padding: 3px 6px; font-weight: 600;">الرقم الجامعي:</td>
            <td style="padding: 3px 6px;">{{ $student->student_number ?? '—' }}</td>
        </tr>
        <tr>
            <td style="padding: 3px 6px; font-weight: 600;">رقم الفاتورة:</td>
            <td style="padding: 3px 6px;">{{ $invoice->invoice_number ?? ($payment->invoice_id ?? '—') }}</td>
            <td style="padding: 3px 6px; font-weight: 600;">طريقة الدفع:</td>
            <td style="padding: 3px 6px;">{{ $payment->method?->label() ?? '—' }}</td>
        </tr>
        <tr>
            <td style="padding: 3px 6px; font-weight: 600;">المبلغ:</td>
            <td style="padding: 3px 6px; color: #047857; font-weight: 700;">
                {{ number_format((float) ($payment->amount ?? 0), 2) }} ج.س
            </td>
            <td style="padding: 3px 6px; font-weight: 600;">القسط:</td>
            <td style="padding: 3px 6px;">{{ $payment->installment_no ?? '—' }}</td>
        </tr>
        @if(!empty($payment->notes))
        <tr>
            <td style="padding: 3px 6px; font-weight: 600;">ملاحظات:</td>
            <td colspan="3" style="padding: 3px 6px;">{{ $payment->notes }}</td>
        </tr>
        @endif
    </table>

    {{-- التذييل --}}
    <div style="margin-top: 10px; display: flex; justify-content: space-between; font-size: 10px; color: #555; border-top: 1px solid #ccc; padding-top: 6px;">
        <div>توقيع أمين الصندوق: ____________________</div>
        <div>توقيع المستلم: ____________________</div>
    </div>
</div>
@else
    <div style="padding: 8px; color: #b91c1c;">لا توجد بيانات دفعة لعرض الإيصال.</div>
@endif