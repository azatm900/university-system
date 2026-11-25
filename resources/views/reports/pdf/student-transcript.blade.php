<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>كشف درجات طالب</title>
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
    <div class="sub">كشف درجات طالب</div>
</div>
<div class="meta">
    <div><strong>الطالب:</strong> {{ $student->user?->name ?? '—' }} ({{ $student->student_number }})</div>
    <div><strong>البرنامج:</strong> {{ $student->program?->name ?? '—' }}</div>
    @php
        $gpaVal = $cumulative['gpa'] ?? $cumulative['cumulative_gpa'] ?? $cumulative['cumulativeGPA'] ?? null;
    @endphp
    <div><strong>المعدل التراكمي:</strong> {{ $gpaVal !== null ? number_format((float) $gpaVal, 2) : '—' }}</div>
</div>

<table>
    <thead>
        <tr>
            <th>الفصل</th>
            <th>المقرر</th>
            <th>الساعات</th>
            <th>المجموع</th>
            <th>التقدير</th>
            <th>النقاط</th>
            <th>الحالة</th>
        </tr>
    </thead>
    <tbody>
        @forelse($results as $r)
            <tr>
                <td>{{ $r->registration?->semester?->code ?? '—' }}</td>
                <td>{{ $r->registration?->course?->code ?? '—' }} — {{ $r->registration?->course?->name ?? '—' }}</td>
                <td>{{ $r->registration?->course?->credit_hours ?? '—' }}</td>
                <td><strong>{{ number_format((float) $r->total_score, 2) }}</strong></td>
                <td>{{ $r->grade }}</td>
                <td>{{ number_format((float) $r->grade_points, 2) }}</td>
                <td>{{ $r->status instanceof \BackedEnum ? $r->status->value : $r->status }}</td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:#999;">لا توجد نتائج منشورة.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="footer">تم الإنشاء: {{ now()->format('Y-m-d H:i') }} — نظام الجامعة</div>
</body>
</html>