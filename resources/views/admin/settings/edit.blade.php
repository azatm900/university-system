@extends('layouts.app')
@section('title', 'تعديل إعداد')
@section('content')

@php
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
    $arabicName = $keyNames[$key] ?? $key;
    $group = explode('.', $key)[0] ?? 'general';
@endphp

<div class="max-w-3xl mx-auto animate-fade-in">
    
    {{-- رأس الفورم --}}
    <div class="bg-gradient-to-l from-blue-600 via-indigo-600 to-purple-600 rounded-2xl shadow-lg p-8 text-white mb-6 relative overflow-hidden">
        {{-- زخرفة خلفية --}}
        <div class="absolute top-0 left-0 w-full h-full opacity-10">
            <svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
                <path fill="#FFFFFF" d="M44.7,-76.3C58.9,-68.7,71.8,-58.2,79.4,-45.2C87,-32.2,89.3,-16.8,87.5,-2.3C85.7,12.2,79.8,26.3,71.1,38.5C62.4,50.7,50.9,61,37.7,68.4C24.5,75.8,9.5,80.3,-6.3,82.5C-22.1,84.7,-38.7,84.6,-51.9,76.8C-65.1,69,-74.9,53.5,-81.1,37.4C-87.3,21.3,-89.9,4.6,-86.3,-10.3C-82.7,-25.2,-72.9,-38.6,-60.6,-46.9C-48.3,-55.2,-33.5,-58.4,-19.4,-64.3C-5.3,-70.2,8.1,-78.8,23.4,-82.1C38.7,-85.4,55.9,-83.5,44.7,-76.3Z" transform="translate(100 100)" />
            </svg>
        </div>
        
        <div class="relative flex items-center gap-4">
            <div class="w-16 h-16 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center">
                <x-icon name="pencil-square" :size="32" />
            </div>
            <div class="flex-1">
                <p class="text-blue-100 text-sm mb-1">تعديل الإعداد</p>
                <h1 class="text-2xl font-bold">{{ $arabicName }}</h1>
                <div class="mt-2 inline-block bg-white/20 backdrop-blur-sm px-3 py-1 rounded-lg text-xs font-mono">
                    {{ $key }}
                </div>
            </div>
        </div>
    </div>

    {{-- جسم الفورم --}}
    <form method="POST" action="{{ route('admin.settings.update', $key) }}" enctype="multipart/form-data" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-8 space-y-6">
        @csrf 
        @method('PUT')

        {{-- حقل الشعار --}}
        @if ($key === 'app.logo')
            <div class="bg-slate-50 rounded-xl p-6 border-2 border-dashed border-slate-200 hover:border-blue-400 transition-colors">
                <label class="flex items-center gap-2 text-sm font-bold text-slate-700 mb-4">
                    <x-icon name="photo" :size="18" class="text-blue-600" />
                    رفع شعار جديد
                </label>
                
                <div class="flex items-center gap-6">
                    <div class="flex-1">
                        @if ($value)
                            <div class="mb-3">
                                <p class="text-xs text-slate-500 mb-2">الشعار الحالي:</p>
                                <img src="{{ asset('storage/' . $value) }}" class="h-20 w-auto rounded-lg border border-slate-200 bg-white p-2 shadow-sm">
                            </div>
                        @endif
                        <input type="file" name="value" id="logo-input" accept="image/*" 
                            class="block w-full text-sm text-slate-500 file:mr-4 file:py-3 file:px-6 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 cursor-pointer">
                        <p class="text-xs text-slate-400 mt-2">PNG, JPG, SVG, WEBP - الحد الأقصى 2MB</p>
                    </div>
                    <div id="preview-container" class="hidden w-32 h-32 rounded-xl border-2 border-blue-400 bg-white p-2 shadow-lg">
                        <img id="preview-logo" class="w-full h-full object-contain">
                    </div>
                </div>
            </div>
        @else
            {{-- حقول عادية --}}
            <div>
                <label class="flex items-center gap-2 text-sm font-bold text-slate-700 mb-3">
                    <x-icon name="value" :size="16" class="text-blue-600" />
                    القيمة الحالية
                </label>
                
                @if (in_array($type, ['integer', 'float']))
                    <input type="number" step="any" name="value" value="{{ old('value', $value) }}" 
                        class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all text-slate-800 font-mono" 
                        placeholder="أدخل القيمة الرقمية...">
                @elseif ($type === 'boolean')
                    <select name="value" class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all text-slate-800 bg-white">
                        <option value="1" @selected(old('value', $value) == '1' || old('value', $value) === true)>✓ نعم (مفعل)</option>
                        <option value="0" @selected(old('value', $value) == '0' || old('value', $value) === false)>✗ لا (معطل)</option>
                    </select>
                @else
                    <textarea name="value" rows="4" 
                        class="w-full px-4 py-3 rounded-xl border-2 border-slate-200 focus:border-blue-500 focus:ring-4 focus:ring-blue-100 outline-none transition-all text-slate-800 font-mono resize-y"
                        placeholder="أدخل القيمة...">{{ old('value', is_array($value) ? json_encode($value, JSON_UNESCAPED_UNICODE) : $value) }}</textarea>
                @endif
                
                @if ($value && !in_array($key, ['app.logo']))
                    <p class="text-xs text-slate-500 mt-2 flex items-center gap-1">
                        <x-icon name="information-circle" :size="14" />
                        القيمة السابقة: <span class="font-mono bg-slate-100 px-2 py-0.5 rounded">{{ $value }}</span>
                    </p>
                @endif
            </div>
        @endif

        {{-- معلومات إضافية --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-slate-100">
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs text-slate-500 mb-1">النوع</p>
                <p class="font-semibold text-slate-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    {{ $type }}
                </p>
            </div>
            <div class="bg-slate-50 rounded-xl p-4">
                <p class="text-xs text-slate-500 mb-1">المجموعة</p>
                <p class="font-semibold text-slate-800 flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    {{ $group }}
                </p>
            </div>
        </div>

        {{-- أزرار الحفظ --}}
        <div class="flex items-center gap-3 pt-6 border-t border-slate-100">
            <button type="submit" class="flex-1 sm:flex-none inline-flex items-center justify-center gap-2 px-8 py-3 bg-gradient-to-l from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 hover:-translate-y-0.5">
                <x-icon name="check" :size="20" />
                حفظ التغييرات
            </button>
            <a href="{{ route('admin.settings.index') }}" class="inline-flex items-center justify-center gap-2 px-6 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold rounded-xl transition-all duration-200">
                <x-icon name="x-mark" :size="18" />
                إلغاء
            </a>
        </div>
    </form>

    {{-- معاينة الشعار --}}
    @if ($key === 'app.logo')
        <script>
            document.getElementById('logo-input').addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        document.getElementById('preview-logo').src = ev.target.result;
                        document.getElementById('preview-container').classList.remove('hidden');
                    }
                    reader.readAsDataURL(file);
                }
            });
        </script>
    @endif
</div>
@endsection