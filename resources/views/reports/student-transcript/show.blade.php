@extends('layouts.app')

@section('title', 'كشف درجات — ' . ($student->user?->name ?? ''))

@section('content')
<div class="container mx-auto p-6" dir="rtl">
    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('reports.student-transcript.index') }}" class="text-maroon-700 hover:underline">← القائمة</a>
        <div class="flex gap-2">
            <a href="{{ route('reports.student-transcript.excel', $student) }}"
               class="bg-green-700 text-white px-4 py-2 rounded text-sm hover:bg-green-800">تصدير Excel</a>
            <a href="{{ route('reports.student-transcript.pdf', $student) }}"
               class="bg-red-700 text-white px-4 py-2 rounded text-sm hover:bg-red-800">تصدير PDF</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <div class="text-xs text-gray-500">الطالب</div>
                <div class="text-lg font-bold text-maroon-700">{{ $student->user?->name ?? '—' }}</div>
                <div class="text-sm font-mono text-gray-600">{{ $student->student_number }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">البرنامج</div>
                <div class="text-sm">{{ $student->program?->name ?? '—' }}</div>
            </div>
            <div>
                @php
                    $gpaVal = $cumulative['gpa'] ?? $cumulative['cumulative_gpa'] ?? $cumulative['cumulativeGPA'] ?? null;
                    $creditsVal = $cumulative['total_credits'] ?? $cumulative['credits'] ?? null;
                    $pointsVal = $cumulative['total_points'] ?? null;
                @endphp
                <div class="text-xs text-gray-500">المعدل التراكمي</div>
                <div class="text-2xl font-bold text-maroon-700">
                    {{ $gpaVal !== null ? number_format((float) $gpaVal, 2) : '—' }}
                </div>
                @if($creditsVal !== null)
                    <div class="text-xs text-gray-600">الساعات: {{ $creditsVal }}</div>
                @endif
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">الفصل</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">المقرر</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">الساعات</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">أعمال</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">نصفي</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">نهائي</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">المجموع</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">التقدير</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">النقاط</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">الحالة</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">النشر</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($results as $r)
                    <tr>
                        <td class="px-3 py-2 text-sm">{{ $r->registration?->semester?->code ?? '—' }}</td>
                        <td class="px-3 py-2 text-sm">
                            <div class="font-mono text-xs">{{ $r->registration?->course?->code ?? '—' }}</div>
                            <div>{{ $r->registration?->course?->name ?? '—' }}</div>
                        </td>
                        <td class="px-3 py-2 text-sm">{{ $r->registration?->course?->credit_hours ?? '—' }}</td>
                        <td class="px-3 py-2 text-sm">{{ $r->coursework_score !== null ? number_format((float) $r->coursework_score, 2) : '—' }}</td>
                        <td class="px-3 py-2 text-sm">{{ $r->midterm_score !== null ? number_format((float) $r->midterm_score, 2) : '—' }}</td>
                        <td class="px-3 py-2 text-sm">{{ $r->final_score !== null ? number_format((float) $r->final_score, 2) : '—' }}</td>
                        <td class="px-3 py-2 text-sm font-bold">{{ number_format((float) $r->total_score, 2) }}</td>
                        <td class="px-3 py-2 text-sm font-mono">{{ $r->grade }}</td>
                        <td class="px-3 py-2 text-sm">{{ number_format((float) $r->grade_points, 2) }}</td>
                        <td class="px-3 py-2 text-sm">
                            @include('partials.enum-label', ['value' => $r->status])
                        </td>
                        <td class="px-3 py-2 text-sm">
                            @if($r->published_at)
                                <span class="text-green-700 text-xs">منشور</span>
                            @else
                                <span class="text-gray-500 text-xs">مسودة</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="11" class="px-4 py-6 text-center text-gray-500">لا توجد نتائج لهذا الطالب.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection