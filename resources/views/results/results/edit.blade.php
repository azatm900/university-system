@extends('layouts.app')
@section('title', 'تعديل نتيجة')
@section('content')
<div class="card max-w-2xl mx-auto p-6">
    <form method="POST" action="{{ route('results.results.update', $result) }}" class="space-y-4">
        @csrf @method('PUT')
        <div><label class="block text-sm font-semibold mb-1">التسجيل</label>
            <select name="registration_id" required class="input-field">
                @foreach (($registrations ?? \App\Models\Registration::with(['student.user','course','semester'])->get()) as $reg)
                    <option value="{{ $reg->id }}" @selected(old('registration_id', $result->registration_id)===$reg->id)>{{ $reg->student?->student_number }} — {{ $reg->course?->name }}</option>
                @endforeach
            </select></div>
        <div class="grid grid-cols-3 gap-3">
            <div><label class="block text-sm font-semibold mb-1">أعمال السنة</label><input name="coursework_score" type="number" step="0.01" min="0" max="100" value="{{ old('coursework_score', $result->coursework_score) }}" class="input-field"></div>
            <div><label class="block text-sm font-semibold mb-1">النصفي</label><input name="midterm_score" type="number" step="0.01" min="0" max="100" value="{{ old('midterm_score', $result->midterm_score) }}" class="input-field"></div>
            <div><label class="block text-sm font-semibold mb-1">النهائي</label><input name="final_score" type="number" step="0.01" min="0" max="100" value="{{ old('final_score', $result->final_score) }}" class="input-field"></div>
        </div>
        <div class="p-3 bg-gray-50 rounded-lg text-sm">
            <p>المجموع: <span class="font-semibold">{{ $result->total_score }}</span></p>
            <p>التقدير: <span class="font-semibold">{{ $result->grade }}</span></p>
            <p>الحالة: <span class="font-semibold">@include('partials.enum-label', ['value' => $result->status])</span></p>
            <p>النشر: @if($result->published_at) منشور ({{ $result->published_at }}) @else غير منشور @endif</p>
        </div>
        <div class="flex gap-2"><button class="btn-primary">حفظ</button><a href="{{ route('results.results.index') }}" class="btn-secondary">إلغاء</a></div>
    </form>
</div>
@endsection