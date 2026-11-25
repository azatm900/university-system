@extends('layouts.app')

@section('title', 'تسجيل طالب جديد')
@section('page-title', 'تسجيل طالب جديد')
@section('page-subtitle', 'أكمل الخطوات الثلاث لتسجيل الطالب في النظام')

@section('content')
<div class="max-w-5xl mx-auto">

    {{-- Header --}}
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 flex items-center gap-2">
                <x-icon name="user-plus" class="w-6 h-6 text-indigo-600" />
                تسجيل طالب جديد
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                أكمل البيانات عبر الخطوات الثلاث ثم اضغط حفظ
            </p>
        </div>
        <a href="{{ route('registrar.students.index') }}"
           class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium transition">
            <x-icon name="arrow-right" class="w-4 h-4" />
            رجوع للقائمة
        </a>
    </div>

    {{-- Stepper --}}
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between" id="wizard-stepper">
            @php
                $steps = [
                    ['n' => 1, 'label' => 'البيانات الأساسية', 'icon' => 'user'],
                    ['n' => 2, 'label' => 'جهات الاتصال',       'icon' => 'phone'],
                    ['n' => 3, 'label' => 'العنوان والدرجات',   'icon' => 'map-pin'],
                ];
            @endphp
            @foreach($steps as $i => $s)
                <div class="flex items-center {{ $i < count($steps) - 1 ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center">
                        <div data-stepper-circle="{{ $s['n'] }}"
                             class="w-11 h-11 rounded-full flex items-center justify-center font-bold text-sm transition
                                    {{ $s['n'] === 1 ? 'bg-indigo-600 text-white' : 'bg-slate-100 text-slate-400' }}">
                            {{ $s['n'] }}
                        </div>
                        <span data-stepper-label="{{ $s['n'] }}"
                              class="mt-2 text-xs font-medium {{ $s['n'] === 1 ? 'text-indigo-600' : 'text-slate-400' }}">
                            {{ $s['label'] }}
                        </span>
                    </div>
                    @if($i < count($steps) - 1)
                        <div data-stepper-line="{{ $s['n'] }}"
                             class="flex-1 h-0.5 mx-3 bg-slate-200 transition"></div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Form --}}
    <form id="student-wizard-form"
          method="POST"
          action="{{ route('registrar.students.store') }}"
          class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        @csrf

        {{-- ===================== STEP 1 ===================== --}}
        <div data-panel="1" class="p-6 md:p-8">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <x-icon name="user" class="w-5 h-5 text-indigo-600" />
                    البيانات الأساسية
                </h2>
                <p class="text-sm text-slate-500 mt-1">الحقول المعلّمة بـ <span class="text-rose-600">*</span> إلزامية</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- full_name --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        الاسم الكامل <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" name="full_name" value="{{ old('full_name') }}" required
                           placeholder="مثال: محمد عبدالله الأحمد"
                           class="w-full px-4 py-2.5 rounded-xl border @error('full_name') border-rose-400 @else border-slate-300 @enderror
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    @error('full_name')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- program_id --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        البرنامج <span class="text-rose-600">*</span>
                    </label>
                    <select name="program_id" required
                            class="w-full px-4 py-2.5 rounded-xl border @error('program_id') border-rose-400 @else border-slate-300 @enderror
                                   focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white">
                        <option value="">— اختر البرنامج —</option>
                        @foreach($programs as $program)
                            <option value="{{ $program->id }}" @selected(old('program_id') == $program->id)>
                                {{ $program->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('program_id')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- gender --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        الجنس <span class="text-rose-600">*</span>
                    </label>
                    <select name="gender" required
                            class="w-full px-4 py-2.5 rounded-xl border @error('gender') border-rose-400 @else border-slate-300 @enderror
                                   focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm bg-white">
                        <option value="">— اختر —</option>
                        <option value="male"   @selected(old('gender') === 'male')>ذكر</option>
                        <option value="female" @selected(old('gender') === 'female')>أنثى</option>
                    </select>
                    @error('gender')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- date_of_birth --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        تاريخ الميلاد <span class="text-rose-600">*</span>
                    </label>
                    <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required
                           class="w-full px-4 py-2.5 rounded-xl border @error('date_of_birth') border-rose-400 @else border-slate-300 @enderror
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    @error('date_of_birth')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- national_id --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        الرقم الوطني
                    </label>
                    <input type="text" name="national_id" value="{{ old('national_id') }}"
                           placeholder="اختياري"
                           class="w-full px-4 py-2.5 rounded-xl border @error('national_id') border-rose-400 @else border-slate-300 @enderror
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    @error('national_id')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- phone --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        رقم الهاتف <span class="text-rose-600">*</span>
                    </label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                           placeholder="مثال: 0912345678"
                           class="w-full px-4 py-2.5 rounded-xl border @error('phone') border-rose-400 @else border-slate-300 @enderror
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    @error('phone')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ===================== STEP 2 ===================== --}}
        <div data-panel="2" class="p-6 md:p-8 hidden">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <x-icon name="phone" class="w-5 h-5 text-indigo-600" />
                    جهات الاتصال
                </h2>
                <p class="text-sm text-slate-500 mt-1">بيانات ولي الأمر والأم (اختيارية لكن مُوصى بها)</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- next_of_kin_name --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        اسم ولي الأمر / أقرب الأقارب
                    </label>
                    <input type="text" name="next_of_kin_name" value="{{ old('next_of_kin_name') }}"
                           placeholder="مثال: عبدالله الأحمد"
                           class="w-full px-4 py-2.5 rounded-xl border @error('next_of_kin_name') border-rose-400 @else border-slate-300 @enderror
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    @error('next_of_kin_name')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- next_of_kin_phone --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        هاتف ولي الأمر
                    </label>
                    <input type="text" name="next_of_kin_phone" value="{{ old('next_of_kin_phone') }}"
                           placeholder="مثال: 0923456789"
                           class="w-full px-4 py-2.5 rounded-xl border @error('next_of_kin_phone') border-rose-400 @else border-slate-300 @enderror
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    @error('next_of_kin_phone')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- mother_name --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        اسم الأم
                    </label>
                    <input type="text" name="mother_name" value="{{ old('mother_name') }}"
                           placeholder="مثال: فاطمة علي"
                           class="w-full px-4 py-2.5 rounded-xl border @error('mother_name') border-rose-400 @else border-slate-300 @enderror
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    @error('mother_name')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- mother_phone --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        هاتف الأم
                    </label>
                    <input type="text" name="mother_phone" value="{{ old('mother_phone') }}"
                           placeholder="مثال: 0934567890"
                           class="w-full px-4 py-2.5 rounded-xl border @error('mother_phone') border-rose-400 @else border-slate-300 @enderror
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    @error('mother_phone')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- ===================== STEP 3 ===================== --}}
        <div data-panel="3" class="p-6 md:p-8 hidden">
            <div class="mb-6">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2">
                    <x-icon name="map-pin" class="w-5 h-5 text-indigo-600" />
                    العنوان والدرجات
                </h2>
                <p class="text-sm text-slate-500 mt-1">بيانات السكن والدرجة العلمية</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                {{-- state --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الولاية</label>
                    <input type="text" name="state" value="{{ old('state') }}"
                           placeholder="مثال: الخرطوم"
                           class="w-full px-4 py-2.5 rounded-xl border @error('state') border-rose-400 @else border-slate-300 @enderror
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    @error('state')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- district --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">المحلية / القسم</label>
                    <input type="text" name="district" value="{{ old('district') }}"
                           placeholder="مثال: بحري"
                           class="w-full px-4 py-2.5 rounded-xl border @error('district') border-rose-400 @else border-slate-300 @enderror
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    @error('district')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- block --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">الحي / المربع</label>
                    <input type="text" name="block" value="{{ old('block') }}"
                           placeholder="مثال: مربع 12"
                           class="w-full px-4 py-2.5 rounded-xl border @error('block') border-rose-400 @else border-slate-300 @enderror
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    @error('block')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- certificate_score --}}
                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">درجة الشهادة</label>
                    <input type="number" step="0.01" min="0" max="100" name="certificate_score"
                           value="{{ old('certificate_score') }}"
                           placeholder="مثال: 85.5"
                           class="w-full px-4 py-2.5 rounded-xl border @error('certificate_score') border-rose-400 @else border-slate-300 @enderror
                                  focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">
                    @error('certificate_score')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- address --}}
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-slate-700 mb-2">العنوان التفصيلي</label>
                    <textarea name="address" rows="3"
                              placeholder="العنوان الكامل..."
                              class="w-full px-4 py-2.5 rounded-xl border @error('address') border-rose-400 @else border-slate-300 @enderror
                                     focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 text-sm">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="text-xs text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Finance Status Notice --}}
            <div class="mt-6 p-4 rounded-xl bg-amber-50 border border-amber-200 flex items-start gap-3">
                <x-icon name="alert-circle" class="w-5 h-5 text-amber-600 flex-shrink-0 mt-0.5" />
                <div>
                    <p class="text-sm font-semibold text-amber-800">الحالة المالية الافتراضية: قيد الانتظار</p>
                    <p class="text-xs text-amber-700 mt-1">
                        سيتم إنشاء الطالب بحالة مالية <strong>pending</strong>. يمكن تعديلها لاحقًا من شاشة المالية بعد سداد الرسوم.
                    </p>
                </div>
            </div>
        </div>

        {{-- ===================== Footer Buttons ===================== --}}
        <div class="px-6 md:px-8 py-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
            <button type="button" id="btn-prev"
                    class="hidden inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium transition">
                <x-icon name="arrow-right" class="w-4 h-4" />
                السابق
            </button>

            <div class="flex-1"></div>

            <button type="button" id="btn-next"
                    class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition shadow-sm">
                التالي
                <x-icon name="arrow-left" class="w-4 h-4" />
            </button>

            <button type="submit" id="btn-save"
                    class="hidden inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold transition shadow-sm">
                <x-icon name="check" class="w-4 h-4" />
                حفظ الطالب
            </button>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const form     = document.getElementById('student-wizard-form');
    const panels   = Array.from(form.querySelectorAll('[data-panel]'));
    const btnPrev  = document.getElementById('btn-prev');
    const btnNext  = document.getElementById('btn-next');
    const btnSave  = document.getElementById('btn-save');
    const total    = panels.length;
    let current    = 1;

    function updateStepper(step) {
        // Circles
        document.querySelectorAll('[data-stepper-circle]').forEach(el => {
            const n = parseInt(el.getAttribute('data-stepper-circle'), 10);
            el.classList.remove('bg-indigo-600','text-white','bg-slate-100','text-slate-400',
                                'bg-emerald-500','bg-emerald-100','text-emerald-700');
            if (n < step) {
                el.classList.add('bg-emerald-500','text-white');
            } else if (n === step) {
                el.classList.add('bg-indigo-600','text-white');
            } else {
                el.classList.add('bg-slate-100','text-slate-400');
            }
        });

        // Labels
        document.querySelectorAll('[data-stepper-label]').forEach(el => {
            const n = parseInt(el.getAttribute('data-stepper-label'), 10);
            el.classList.remove('text-indigo-600','text-emerald-700','text-slate-400');
            if (n < step)        el.classList.add('text-emerald-700');
            else if (n === step) el.classList.add('text-indigo-600');
            else                 el.classList.add('text-slate-400');
        });

        // Lines
        document.querySelectorAll('[data-stepper-line]').forEach(el => {
            const n = parseInt(el.getAttribute('data-stepper-line'), 10);
            el.classList.remove('bg-slate-200','bg-emerald-400');
            el.classList.add(n < step ? 'bg-emerald-400' : 'bg-slate-200');
        });
    }

    function showStep(step) {
        current = step;
        panels.forEach(p => {
            const n = parseInt(p.getAttribute('data-panel'), 10);
            p.classList.toggle('hidden', n !== step);
        });

        btnPrev.classList.toggle('hidden', step === 1);

        if (step === total) {
            btnNext.classList.add('hidden');
            btnSave.classList.remove('hidden');
        } else {
            btnNext.classList.remove('hidden');
            btnSave.classList.add('hidden');
        }

        updateStepper(step);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    function validateCurrentPanel() {
        const panel = form.querySelector(`[data-panel="${current}"]`);
        if (!panel) return true;
        const fields = panel.querySelectorAll('input, select, textarea');
        for (const f of fields) {
            if (!f.checkValidity()) {
                f.reportValidity();
                return false;
            }
        }
        return true;
    }

    btnNext.addEventListener('click', () => {
        if (!validateCurrentPanel()) return;
        if (current < total) showStep(current + 1);
    });

    btnPrev.addEventListener('click', () => {
        if (current > 1) showStep(current - 1);
    });

    // Initialize: if validation errors exist on a later panel, jump to it.
    @if($errors->any())
        @php
            $step1Fields = ['full_name','program_id','gender','date_of_birth','national_id','phone'];
            $step2Fields = ['next_of_kin_name','next_of_kin_phone','mother_name','mother_phone'];
            $step3Fields = ['state','district','block','address','certificate_score'];
            $target = 3;
            if ($errors->hasAny($step1Fields))      $target = 1;
            else if ($errors->hasAny($step2Fields)) $target = 2;
            else                                    $target = 3;
        @endphp
        showStep({{ $target }});
    @else
        showStep(1);
    @endif
})();
</script>
@endpush