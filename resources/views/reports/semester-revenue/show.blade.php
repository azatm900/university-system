@extends('layouts.app')

@section('title', 'إيرادات ' . ($semester->code ?? ''))

@section('content')
<div class="container mx-auto p-6" dir="rtl">
    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('reports.semester-revenue.index') }}" class="text-maroon-700 hover:underline">← القائمة</a>
        <div class="flex gap-2">
            <a href="{{ route('reports.semester-revenue.excel', $semester) }}"
               class="bg-green-700 text-white px-4 py-2 rounded text-sm hover:bg-green-800">تصدير Excel</a>
            <a href="{{ route('reports.semester-revenue.pdf', $semester) }}"
               class="bg-red-700 text-white px-4 py-2 rounded text-sm hover:bg-red-800">تصدير PDF</a>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <div class="text-xs text-gray-500">الفصل</div>
        <div class="text-xl font-bold text-maroon-700">{{ $semester->code }} — {{ $semester->name }}</div>
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-gray-50 rounded p-3">
                <div class="text-xs text-gray-500">الإيراد الكلي</div>
                <div class="text-2xl font-bold text-green-700">{{ number_format((float) $total, 2) }}</div>
            </div>
            <div class="bg-gray-50 rounded p-3">
                <div class="text-xs text-gray-500 mb-1">حسب طريقة الدفع</div>
                @forelse($totalByMethod as $m => $cents)
                    <div class="flex justify-between text-sm">
                        <span>@include('partials.enum-label', ['value' => $m])</span>
                        <span class="font-mono">{{ number_format($cents / 100, 2) }}</span>
                    </div>
                @empty
                    <span class="text-gray-500 text-sm">—</span>
                @endforelse
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">التاريخ</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">الفاتورة</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">الطالب</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">المبلغ</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">الطريقة</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">المرجع</th>
                    <th class="px-3 py-2 text-right text-xs font-medium text-gray-500">استلمها</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($payments as $pay)
                    <tr>
                        <td class="px-3 py-2 text-sm">{{ $pay->payment_date }}</td>
                        <td class="px-3 py-2 text-sm font-mono">{{ $pay->invoice?->invoice_number ?? '—' }}</td>
                        <td class="px-3 py-2 text-sm">
                            <div class="font-mono text-xs">{{ $pay->invoice?->student?->student_number ?? '—' }}</div>
                            <div>{{ $pay->invoice?->student?->user?->name ?? '—' }}</div>
                        </td>
                        <td class="px-3 py-2 text-sm font-bold text-green-700">{{ number_format((float) $pay->amount, 2) }}</td>
                        <td class="px-3 py-2 text-sm">@include('partials.enum-label', ['value' => $pay->method])</td>
                        <td class="px-3 py-2 text-sm">{{ $pay->reference_number ?? '—' }}</td>
                        <td class="px-3 py-2 text-sm">{{ $pay->receiver?->name ?? '—' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="px-4 py-6 text-center text-gray-500">لا توجد مدفوعات لهذا الفصل.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection