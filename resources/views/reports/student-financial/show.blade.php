@extends('layouts.app')

@section('title', 'كشف مالي — ' . ($student->user?->name ?? ''))

@section('content')
<div class="container mx-auto p-6" dir="rtl">
    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('reports.student-financial.index') }}" class="text-maroon-700 hover:underline">← القائمة</a>
        <div class="flex gap-2">
            <a href="{{ route('reports.student-financial.excel', $student) }}"
               class="bg-green-700 text-white px-4 py-2 rounded text-sm hover:bg-green-800">تصدير Excel</a>
            <a href="{{ route('reports.student-financial.pdf', $student) }}"
               class="bg-red-700 text-white px-4 py-2 rounded text-sm hover:bg-red-800">تصدير PDF</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <div class="text-xs text-gray-500">الطالب</div>
                <div class="text-lg font-bold text-maroon-700">{{ $student->user?->name ?? '—' }}</div>
                <div class="text-sm font-mono">{{ $student->student_number }}</div>
            </div>
            <div>
                <div class="text-xs text-gray-500">البرنامج</div>
                <div class="text-sm">{{ $student->program?->name ?? '—' }}</div>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-gray-50 rounded p-3">
                <div class="text-xs text-gray-500">إجمالي الفواتير</div>
                <div class="text-xl font-bold">{{ number_format((float) $totalBilled, 2) }}</div>
            </div>
            <div class="bg-gray-50 rounded p-3">
                <div class="text-xs text-gray-500">إجمالي المدفوع</div>
                <div class="text-xl font-bold text-green-700">{{ number_format((float) $totalPaid, 2) }}</div>
            </div>
            <div class="bg-gray-50 rounded p-3">
                <div class="text-xs text-gray-500">الرصيد المتبقي</div>
                <div class="text-xl font-bold {{ $balance > 0 ? 'text-red-700' : 'text-green-700' }}">
                    {{ number_format((float) $balance, 2) }}
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">رقم الفاتورة</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">الوصف</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">الفصل</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">المبلغ</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">الخصم</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">الإجمالي</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">المدفوع</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">المتبقي</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">الحالة</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">الاستحقاق</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($invoices as $inv)
                    @php
                        $tc = (int) round(((float) ($inv->total ?? 0)) * 100);
                        $pc = (int) round(((float) ($inv->paid_amount ?? 0)) * 100);
                        $bal = ($tc - $pc) / 100;
                    @endphp
                    <tr>
                        <td class="px-3 py-2 text-sm font-mono">{{ $inv->invoice_number }}</td>
                        <td class="px-3 py-2 text-sm">{{ $inv->description }}</td>
                        <td class="px-3 py-2 text-sm">{{ $inv->semester?->code ?? '—' }}</td>
                        <td class="px-3 py-2 text-sm">{{ number_format((float) $inv->amount, 2) }}</td>
                        <td class="px-3 py-2 text-sm">{{ number_format((float) $inv->discount, 2) }}</td>
                        <td class="px-3 py-2 text-sm font-bold">{{ number_format((float) $inv->total, 2) }}</td>
                        <td class="px-3 py-2 text-sm text-green-700">{{ number_format((float) $inv->paid_amount, 2) }}</td>
                        <td class="px-3 py-2 text-sm {{ $bal > 0 ? 'text-red-700' : 'text-green-700' }}">{{ number_format((float) $bal, 2) }}</td>
                        <td class="px-3 py-2 text-sm">
                            @include('partials.enum-label', ['value' => $inv->status])
                        </td>
                        <td class="px-3 py-2 text-sm">{{ $inv->due_date }}</td>
                    </tr>
                @empty
                    <tr><td colspan="10" class="px-4 py-6 text-center text-gray-500">لا توجد فواتير لهذا الطالب.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection