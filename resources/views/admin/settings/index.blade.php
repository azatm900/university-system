@extends('layouts.app')
@section('title', 'الإعدادات')
@section('content')

@php
    $groupNames = [
        'academic' => 'الإعدادات الأكاديمية',
        'app' => 'إعدادات التطبيق والنظام',
        'finance' => 'الإعدادات المالية',
        'gpa' => 'إعدادات المعدل التراكمي (GPA)',
        'file_number' => 'تنسيق أرقام الملفات',
        'general' => 'إعدادات عامة',
    ];

    $keyNames = [
        'academic.current_year' => 'السنة الدراسية الحالية',
        'academic.current_semester' => 'الفصل الدراسي الحالي',
        'app.name' => 'اسم النظام (عربي)',
        'app.name_en' => 'اسم النظام (إنجليزي)',
        'app.logo' => 'شعار النظام (Logo)',
        'app.address' => 'عنوان الجامعة',
        'app.phone' => 'رقم هاتف الجامعة',
        'app.email' => 'البريد الإلكتروني الرسمي',
        'finance.currency' => 'رمز العملة (ISO)',
        'finance.currency_symbol' => 'رمز العملة المحلي',
        'gpa.scale' => 'نظام حساب المعدل',
        'gpa.max' => 'أقصى قيمة للمعدل',
        'gpa.passing_score' => 'درجة النجاح',
        'gpa.passing_gpa' => 'أدنى معدل للنجاح',
        'file_number.student_format' => 'تنسيق رقم الطالب',
        'file_number.invoice_format' => 'تنسيق رقم الفاتورة',
        'file_number.receipt_format' => 'تنسيق رقم الإيصال',
    ];

    $groupColors = [
        'app' => ['#3b82f6', '#dbeafe'],
        'academic' => ['#10b981', '#d1fae5'],
        'finance' => ['#f59e0b', '#fef3c7'],
        'gpa' => ['#8b5cf6', '#ede9fe'],
        'file_number' => ['#64748b', '#f1f5f9'],
        'general' => ['#0f172a', '#e2e8f0'],
    ];
@endphp

<div class="space-y-6 animate-fade-in max-w-6xl mx-auto">
    
    {{-- رأس الصفحة --}}
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white shadow-lg">
                <x-icon name="cog-6-tooth" :size="28" />
            </div>
            <div>
                <h1 class="text-2xl font-bold text-slate-800">إعدادات النظام</h1>
                <p class="text-sm text-slate-500 mt-1">تحكم في جميع إعدادات جامعة العلوم الصحية</p>
            </div>
        </div>
        <div class="hidden sm:block text-right">
            <div class="text-xs text-slate-400">مجموعات الإعدادات</div>
            <div class="text-2xl font-bold text-blue-600">{{ count($groups) }}</div>
        </div>
    </div>

    @forelse ($groups as $group => $items)
        @php
            $colors = $groupColors[$group] ?? ['#64748b', '#f1f5f9'];
            $groupName = $groupNames[$group] ?? ucfirst($group);
        @endphp
        
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden hover:shadow-md transition-shadow duration-300">
            {{-- رأس المجموعة --}}
            <div class="px-6 py-4 border-b border-slate-100 flex items-center gap-3" style="background: linear-gradient(to left, {{ $colors[1] }}, transparent);">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center text-white shadow-md" style="background-color: {{ $colors[0] }};">
                    <x-icon name="{{ $group === 'app' ? 'cog-6-tooth' : ($group === 'academic' ? 'academic-cap' : ($group === 'finance' ? 'banknotes' : ($group === 'gpa' ? 'chart-bar' : 'document-text'))) }}" :size="20" />
                </div>
                <h3 class="text-lg font-bold text-slate-800">{{ $groupName }}</h3>
                <span class="mr-auto text-xs font-semibold px-3 py-1 rounded-full" style="background-color: {{ $colors[1] }}; color: {{ $colors[0] }};">
                    {{ count($items) }} إعداد
                </span>
            </div>

            {{-- الجدول --}}
            <div class="overflow-x-auto">
                <table class="w-full text-sm" style="min-width: 600px;">
                    <thead class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-3 text-right w-5/12">اسم الإعداد</th>
                            <th class="px-6 py-3 text-center w-5/12">القيمة الحالية</th>
                            <th class="px-6 py-3 text-center w-2/12">إجراء</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach ($items as $key => $value)
                            <tr class="hover:bg-slate-50/50 transition-colors duration-150">
                                <td class="px-6 py-4">
                                    <div class="font-semibold text-slate-800">{{ $keyNames[$key] ?? $key }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-1 bg-slate-100 inline-block px-2 py-0.5 rounded">{{ $key }}</div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($key === 'app.logo' && $value)
                                        <img src="{{ asset('storage/' . $value) }}" alt="Logo" class="h-12 w-auto rounded-lg border border-slate-200 bg-white p-1 shadow-sm inline-block">
                                    @else
                                        <span class="inline-block bg-slate-100 text-slate-700 px-3 py-1.5 rounded-lg font-mono text-xs border border-slate-200">
                                            {{ $value ?: 'غير محدد' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('admin.settings.edit', $key) }}" class="inline-flex items-center gap-2 px-4 py-2 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow-md hover:-translate-y-0.5" style="background-color: {{ $colors[0] }};">
                                        <x-icon name="pencil" :size="14" /> تعديل
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
            <x-icon name="inbox" :size="48" class="mx-auto text-slate-300 mb-3" />
            <p class="text-slate-500">لا توجد إعدادات.</p>
        </div>
    @endforelse
</div>
@endsection