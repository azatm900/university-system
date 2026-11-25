@extends('layouts.app')

@section('title', 'تسجيلات ' . ($semester->code ?? ''))

@section('content')
<div class="container mx-auto p-6" dir="rtl">
    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('reports.semester-registrations.index') }}" class="text-maroon-700 hover:underline">← القائمة</a>
        <div class="flex gap-2">
            <a href="{{ route('reports.semester-registrations.excel', $semester) }}"
               class="bg-green-700 text-white px-4 py-2 rounded text-sm hover:bg-green-800">تصدير Excel</a>
            <a href="{{ route('reports.semester-registrations.pdf', $semester) }}"
               class="bg-red-700 text-white px-4 py-2 rounded text-sm hover:bg-red-800">تصدير PDF</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="text-xs text-gray-500">الفصل</div>
        <div class="text-xl font-bold text-maroon-700">{{ $semester->code }} — {{ $semester->name }}</div>
        <div class="text-sm text-gray-600">{{ $semester->start_date }} → {{ $semester->end_date }}</div>
        <div class="text-sm text-gray-600 mt-2">عدد التسجيلات: <strong>{{ $registrations->count() }}</strong></div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">الرقم الجامعي</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">الطالب</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">البرنامج</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">المقرر</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">الساعات</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">تاريخ التسجيل</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">الحالة</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($registrations as $r)
                    <tr>
                        <td class="px-3 py-2 text-sm font-mono">{{ $r->student?->student_number ?? '—' }}</td>
                        <td class="px-3 py-2 text-sm">{{ $r->student?->user?->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-sm">{{ $r->student?->program?->name ?? '—' }}</td>
                        <td class="px-3 py-2 text-sm">
                            <div class="font-mono text-xs">{{ $r->course?->code ?? '—' }}</div>
                            <div>{{ $r->course?->name ?? '—' }}</div>
                        </td>
                        <td class="px-3 py-2 text-sm">{{ $r->course?->credit_hours ?? '—' }}</td>
                        <td class="px-3 py-2 text-sm">{{ $r->registered_at }}</td>
                        <td class="px-3 py-2 text-sm">
                            @include('partials.enum-label', ['value' => $r->status])
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">لا توجد تسجيلات في هذا الفصل.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection