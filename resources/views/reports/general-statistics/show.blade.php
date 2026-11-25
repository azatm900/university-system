@extends('layouts.app')

@section('title', 'إحصاءات عامة')

@section('content')
<div class="container mx-auto p-6" dir="rtl">
    <div class="mb-4 flex justify-between items-center">
        <a href="{{ route('reports.index') }}" class="text-maroon-700 hover:underline">← التقارير</a>
        <div class="flex gap-2">
            <a href="{{ route('reports.general-statistics.excel') }}"
               class="bg-green-700 text-white px-4 py-2 rounded text-sm hover:bg-green-800">تصدير Excel</a>
            <a href="{{ route('reports.general-statistics.pdf') }}"
               class="bg-red-700 text-white px-4 py-2 rounded text-sm hover:bg-red-800">تصدير PDF</a>
        </div>
    </div>

    <h1 class="text-2xl font-bold mb-6 text-maroon-700">إحصاءات عامة</h1>

    @php
        $labels = [
            'students_total' => ['الطلاب (إجمالي)', 'students_total'],
            'students_active' => ['الطلاب (نشط)', 'students_active'],
            'programs_total' => ['البرامج (إجمالي)', 'programs_total'],
            'programs_active' => ['البرامج (نشط)', 'programs_active'],
            'courses_total' => ['المقررات (إجمالي)', 'courses_total'],
            'courses_active' => ['المقررات (نشط)', 'courses_active'],
            'semesters_total' => ['الفصول (إجمالي)', 'semesters_total'],
            'semesters_active' => ['الفصول (نشط)', 'semesters_active'],
            'registrations_total' => ['التسجيلات', 'registrations_total'],
            'results_total' => ['النتائج (إجمالي)', 'results_total'],
            'results_published' => ['النتائج (منشور)', 'results_published'],
            'invoices_total' => ['الفواتير (إجمالي)', 'invoices_total'],
            'invoices_unpaid' => ['الفواتير (غير مدفوعة)', 'invoices_unpaid'],
            'payments_total' => ['المدفوعات (عدد)', 'payments_total'],
            'payments_sum' => ['إجمالي المدفوعات (مبلغ)', 'cents'],
            'invoices_billed_sum' => ['إجمالي الفواتير (مبلغ)', 'cents'],
            'invoices_paid_sum' => ['إجمالي المسدّد (مبلغ)', 'cents'],
        ];
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($stats as $key => $val)
            @if(isset($labels[$key]))
                @php
                    list($label, $type) = $labels[$key];
                    $display = $type === 'cents' ? number_format($val / 100, 2) : number_format((float) $val);
                @endphp
                <div class="bg-white rounded-lg shadow p-4">
                    <div class="text-xs text-gray-500 mb-1">{{ $label }}</div>
                    <div class="text-2xl font-bold text-maroon-700">{{ $display }}</div>
                </div>
            @endif
        @endforeach
    </div>
</div>
@endsection