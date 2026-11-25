<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>كشف مالي طالب</title>
<style>
    @font-face {
        font-family: 'Cairo';
        font-style: normal;
        font-weight: 400;
        src: url('https://fonts.gstatic.com/s/cairo/v28/SLXgc1nY6HkvangtZmpQdkhzfH5lkSs2SgRjCAGMQ1z0hOA-W1ToLQ-HmkA.woff2') format('woff2');
    }
    * { font-family: 'Cairo', 'DejaVu Sans', sans-serif; }
    body { direction: rtl; text-align: right; font-size: 10pt; color: #333; padding: 12px; }
    h1 { color: #7a1e2e; font-size: 16pt; margin: 0 0 4px 0; }
    .header { border-bottom: 2px solid #7a1e2e; padding-bottom: 6px; margin-bottom: 12px; }
    .header .sub { font-size: 11pt; color: #666; }
    .meta { font-size: 9pt; color: #666; margin-bottom: 8px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    th { background: #7a1e2e; color: #fff; padding: 5px; font-size: 9pt; }
    td { padding: 4px; border-bottom: 1px solid #eee; font-size: 9pt; }
    .totals { background: #f5f5f5; padding: 8px; margin-top: 6px; border-radius: 4px; font-size: 10pt; }
    .totals strong { color: #7a1e2e; }
    .footer { position: fixed; bottom: -10px; left: 0; right: 0; text-align: center; font-size: 7pt; color: #999; }
    .badge-green { color: #15803d; }
    .badge-red { color: #b91c1c; }
    .badge-gray { color: #6b7280; }
</style>
</head>
<body>
<div class="header">
    <h1>جامعة العلوم الصحية</h1>
    <div class="sub">كشف مالي طالب</div>
</div>
<div class="meta">
    <div><strong>الطالب:</strong> {{ $student->user?->name ?? '—' }} ({{ $student->student_number }})</div>
    <div><strong>البرنامج:</strong> {{ $student->program?->name ?? '—' }}</div>
</div>

<table>
    <thead>
        <tr>
            <th>الفاتورة</th>
            <th>الوصف</th>
            <th>الفصل</th>
            <th>الإجمالي</th>
            <th>المدفوع</th>
            <th>المتبقي</th>
            <th>الحالة</th>
        </tr>
    </thead>
    <tbody>
        @forelse($invoices as $inv)
            @php
                $tc = (int) round(((float) ($inv->total ?? 0)) * 100);
                $pc = (int) round(((float) ($inv->paid_amount ?? 0)) * 100);
                $bal = ($tc - $pc) / 100;
            @endphp
            <tr>
                <td style="font-family:monospace;">{{ $inv->invoice_number }}</td>
                <td>{{ $inv->description }}</td>
                <td>{{ $inv->semester?->code ?? '—' }}</td>
                <td>{{ number_format((float) $inv->total, 2) }}</td>
                <td class="badge-green">{{ number_format((float) $inv->paid_amount, 2) }}</td>
                <td class="{{ $bal > 0 ? 'badge-red' : 'badge-green' }}">{{ number_format((float) $bal, 2) }}</td>
                <td>{{ $inv->status instanceof \BackedEnum ? $inv->status->value : $inv->status }}</td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:#999;">لا توجد فواتير.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="totals">
    <div><strong>إجمالي الفواتير:</strong> {{ number_format((float) $totalBilled, 2) }}</div>
    <div><strong>إجمالي المدفوع:</strong> {{ number_format((float) $totalPaid, 2) }}</div>
    <div><strong>الرصيد المتبقي:</strong> {{ number_format((float) $balance, 2) }}</div>
</div>
<div class="footer">تم الإنشاء: {{ now()->format('Y-m-d H:i') }} — نظام الجامعة</div>
</body>
</html>