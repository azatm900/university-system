@extends('layouts.app')
@section('title', 'نتيجة جديدة')
@section('content')
<div class="card max-w-2xl mx-auto p-6">
    <form method="POST" action="{{ route('results.results.store') }}" class="space-y-4">
        @csrf
        <div><label class="block text-sm font-semibold mb-1">التسجيل (طالب + مقرر + فصل)</label>
            <select name="registration_id" required class="input-field">
                @foreach (($registrations ?? \App\Models\Registration::doesntHave('result')->with(['student.user','course','semester'])->get()) as $reg)
                    <option value="{{ $reg->id }}" @selected(old('registration_id')===$reg->id)>{{ $reg->student?->student_number }} — {{ $reg->student?->user?->name }} / {{ $reg->course?->name }} / {{ $reg->semester?->name }}</option>
                @endforeach
            </select></div>
        <div class="grid grid-cols-3 gap-3">
            <div><label class="block text-sm font-semibold mb-1">أعمال السنة</label><input name="coursework_score" type="number" step="0.01" min="0" max="100" value="{{ old('coursework_score') }}" class="input-field"></div>
            <div><label class="block text-sm font-semibold mb-1">النصفي</label><input name="midterm_score" type="number" step="0.01" min="0" max="100" value="{{ old('midterm_score') }}" class="input-field"></div>
            <div><label class="block text-sm font-semibold mb-1">النهائي</label><input name="final_score" type="number" step="0.01" min="0" max="100" value="{{ old('final_score') }}" class="input-field"></div>
        </div>
        <p class="text-xs text-gray-500">مجموع الدرجات يجب ألا يتجاوز 100. التقدير والحالة يُحسبان تلقائياً.</p>
        <div class="flex gap-2"><button class="btn-primary">حفظ</button><a href="{{ route('results.results.index') }}" class="btn-secondary">إلغاء</a></div>
    </form>
</div>
@endsection