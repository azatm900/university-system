<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<title>تسجيلات فصل</title>
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
    <div class="sub">تسجيلات فصل</div>
</div>
<div class="meta">
    <div><strong>الفصل:</strong> {{ $semester->code }} — {{ $semester->name }}</div>
    <div><strong>الفترة:</strong> {{ $semester->start_date }} → {{ $semester->end_date }}</div>
    <div><strong>عدد التسجيلات:</strong> {{ $registrations->count() }}</div>
</div>

<table>
    <thead>
        <tr>
            <th>الرقم</th>
            <th>الطالب</th>
            <th>البرنامج</th>
            <th>المقرر</th>
            <th>الساعات</th>
            <th>التاريخ</th>
            <th>الحالة</th>
        </tr>
    </thead>
    <tbody>
        @forelse($registrations as $r)
            <tr>
                <td style="font-family:monospace;">{{ $r->student?->student_number ?? '—' }}</td>
                <td>{{ $r->student?->user?->name ?? '—' }}</td>
                <td>{{ $r->student?->program?->name ?? '—' }}</td>
                <td>{{ $r->course?->code ?? '—' }} — {{ $r->course?->name ?? '—' }}</td>
                <td>{{ $r->course?->credit_hours ?? '—' }}</td>
                <td>{{ $r->registered_at }}</td>
                <td>{{ $r->status instanceof \BackedEnum ? $r->status->value : $r->status }}</td>
            </tr>
        @empty
            <tr><td colspan="7" style="text-align:center;color:#999;">لا توجد تسجيلات.</td></tr>
        @endforelse
    </tbody>
</table>
<div class="footer">تم الإنشاء: {{ now()->format('Y-m-d H:i') }} — نظام الجامعة</div>
</body>
</html>