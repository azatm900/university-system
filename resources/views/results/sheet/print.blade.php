@extends('layouts.app')

@section('title', 'كشف درجات')

@section('content')
<style>
    @media print {
        .no-print { display: none !important; }
        body { background: #fff !important; }
        .print-area { box-shadow: none !important; border: 1px solid #000 !important; }
        @page { size: A4; margin: 12mm; }
    }
    .print-area { background: #fff; padding: 18px; border-radius: 6px; }
    .uni-header { text-align: center; border-bottom: 2px solid #1e3a8a; padding-bottom: 10px; margin-bottom: 14px; }
    .uni-header h2 { margin: 0; color: #1e3a8a; font-weight: 700; }
    .uni-header .meta { color: #555; font-size: 13px; margin-top: 4px; }
    .sheet-table th, .sheet-table td { border: 1px solid #333 !important; vertical-align: middle !important; }
    .sheet-table thead th { background: #f1f5f9 !important; text-align: center; font-size: 13px; }
    .sheet-table tbody td { text-align: center; font-size: 13px; }
    .sheet-table tbody td.name-cell { text-align: right; font-weight: 600; }
    .sig-box { height: 60px; }
    .footer-sigs { display: flex; justify-content: space-between; margin-top: 40px; gap: 20px; }
    .footer-sigs > div { flex: 1; text-align: center; border-top: 1px solid #333; padding-top: 8px; font-weight: 600; }
    .student-group + .student-group { margin-top: 10px; }
    .student-header { background: #e2e8f0; font-weight: 700; padding: 4px 8px; font-size: 13px; }
</style>

<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <a href="{{ route('results.sheet.index') }}" class="btn btn-outline-secondary btn-sm">← فلاتر جديدة</a>
        <button onclick="window.print()" class="btn btn-primary btn-sm">🖨️ طباعة / PDF</button>
    </div>

    <div class="print-area shadow-sm">

        {{-- ترويسة الجامعة --}}
        <div class="uni-header">
            <h2>جامعة — كشف درجات</h2>
            <div class="meta">
                التخصص: <strong>{{ $program->name }}</strong>
                &nbsp;|&nbsp; المستوى: <strong>{{ $level }}</strong>
                &nbsp;|&nbsp; الفصل: <strong>{{ $semester->name }} ({{ $semester->code }})</strong>
                @if($course)
                    &nbsp;|&nbsp; المادة: <strong>{{ $course->code }} — {{ $course->name }}</strong>
                @endif
            </div>
        </div>

        {{-- جدول الدرجات --}}
        <table class="table table-bordered sheet-table mb-0">
            <thead>
                <tr>
                    <th style="width:45px">م</th>
                    <th>اسم الطالب الرباعي</th>
                    <th style="width:90px">التوقيع</th>
                    <th style="width:110px">أعمال السنة</th>
                    <th style="width:110px">درجة الامتحان</th>
                    <th style="width:110px">الدرجة النهائية</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $i => $group)
                    @php $st = $group['student']; @endphp
                    @foreach($group['rows'] as $j => $reg)
                        @php
                            $r = $reg->result;
                            $coursework = $r ? ((float)($r->coursework_score ?? 0) + (float)($r->midterm_score ?? 0)) : null;
                            $final      = $r?->final_score;
                            $total      = $r?->total_score;
                        @endphp
                        <tr>
                            @if($j === 0)
                                <td rowspan="{{ $group['rows']->count() }}">{{ $i + 1 }}</td>
                                <td rowspan="{{ $group['rows']->count() }}" class="name-cell">
                                    {{ $st->display_name }}
                                    <div class="text-muted" style="font-size:11px">{{ $st->student_number }}</div>
                                </td>
                                <td rowspan="{{ $group['rows']->count() }}" class="sig-box"></td>
                            @endif
                            <td>
                                <div class="text-muted" style="font-size:11px">{{ $reg->course?->code }}</div>
                                {{ $coursework !== null ? number_format($coursework, 2) : '—' }}
                            </td>
                            <td>{{ $final !== null ? number_format((float)$final, 2) : '—' }}</td>
                            <td><strong>{{ $total !== null ? number_format((float)$total, 2) : '—' }}</strong></td>
                        </tr>
                    @endforeach
                @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            لا يوجد طلاب مطابقون للفلاتر المحددة.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        {{-- التوقيعات السفلية --}}
        <div class="footer-sigs">
            <div>توقيع مسؤول النتائج</div>
            <div>قرار اللجنة</div>
            <div>القرار النهائي</div>
        </div>

    </div>
</div>
@endsection