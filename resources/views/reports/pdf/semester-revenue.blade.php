<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>إيرادات فصل</title>
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
    <div class="sub">إيرادات فصل</div>
</div>
<div class="meta">
    <div><strong>الفصل:</strong> {{ $semester->code }} — {{ $semester->name }}</div>
    <div><strong>الإيراد الكلي:</strong> {{ number_format((float) $total, 2) }}</div>
</div>

<table>
    <thead>
        <tr>
            <th>التاريخ</th>
            <th>الفاتورة</th>
            <th>الطالب</th>
            <th>المبلغ</th>
            <th>الطريقة</th>
            <th>المرجع</th>
        </tr>
    </thead>
    <tbody>
        @forelse($payments as $pay)
            <tr>
                <td>{{ $pay->payment_date }}</td>
                <td style="font-family:monospace;">{{ $pay->invoice?->invoice_number ?? '—' }}</td>
                <td>{{ $pay->invoice?->student?->user?->name ?? '—' }}</td>
                <td class="badge-green"><strong>{{ number_format((float) $pay->amount, 2) }}</strong></td>
                <td>{{ $pay->method instanceof \BackedEnum ? $pay->method->value : $pay->method }}</td>
                <td>{{ $pay->reference_number ?? '—' }}</td>
            </tr>
        @empty
            <tr><td colspan="6" style="text-align:center;color:#999;">لا توجد مدفوعات.</td></tr>
        @endforelse
    </tbody>
</table>

<div class="totals">
    <div><strong>حسب طريقة الدفع:</strong></div>
    @forelse($totalByMethod as $m => $cents)
        <div>{{ $m }}: {{ number_format($cents / 100, 2) }}</div>
    @empty
        <div>—</div>
    @endforelse
</div>
<div class="footer">تم الإنشاء: {{ now()->format('Y-m-d H:i') }} — نظام الجامعة</div>
</body>
</html>