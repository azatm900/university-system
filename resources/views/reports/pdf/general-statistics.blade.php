<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>إحصاءات عامة</title>
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
    <div class="sub">إحصاءات عامة</div>
</div>
@php
    $labels = [
        'students_total' => ['الطلاب (إجمالي)', 'count'],
        'students_active' => ['الطلاب (نشط)', 'count'],
        'programs_total' => ['البرامج (إجمالي)', 'count'],
        'programs_active' => ['البرامج (نشط)', 'count'],
        'courses_total' => ['المقررات (إجمالي)', 'count'],
        'courses_active' => ['المقررات (نشط)', 'count'],
        'semesters_total' => ['الفصول (إجمالي)', 'count'],
        'semesters_active' => ['الفصول (نشط)', 'count'],
        'registrations_total' => ['التسجيلات', 'count'],
        'results_total' => ['النتائج (إجمالي)', 'count'],
        'results_published' => ['النتائج (منشور)', 'count'],
        'invoices_total' => ['الفواتير (إجمالي)', 'count'],
        'invoices_unpaid' => ['الفواتير (غير مدفوعة)', 'count'],
        'payments_total' => ['المدفوعات (عدد)', 'count'],
        'payments_sum' => ['إجمالي المدفوعات', 'cents'],
        'invoices_billed_sum' => ['إجمالي الفواتير', 'cents'],
        'invoices_paid_sum' => ['إجمالي المسدّد', 'cents'],
    ];
@endphp

<table>
    <thead>
        <tr>
            <th>المؤشر</th>
            <th>القيمة</th>
        </tr>
    </thead>
    <tbody>
        @foreach($stats as $k => $v)
            @if(isset($labels[$k]))
                @php list($lbl, $typ) = $labels[$k]; @endphp
                <tr>
                    <td>{{ $lbl }}</td>
                    <td><strong>{{ $typ === 'cents' ? number_format($v / 100, 2) : number_format((float) $v) }}</strong></td>
                </tr>
            @endif
        @endforeach
    </tbody>
</table>
<div class="footer">تم الإنشاء: {{ now()->format('Y-m-d H:i') }} — نظام الجامعة</div>
</body>
</html>