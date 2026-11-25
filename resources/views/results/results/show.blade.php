@extends('layouts.app')
@section('title', 'عرض نتيجة')
@section('content')
<div class="card max-w-2xl mx-auto p-6 space-y-3">
    <div class="flex justify-between items-center border-b pb-3">
        <h3 class="font-semibold text-lg">نتيجة #{{ $result->id }}</h3>
        @if ($result->published_at)
            <span class="text-green-700 text-sm font-semibold">منشور</span>
        @else
            <span class="text-gray-500 text-sm">غير منشور</span>
        @endif
    </div>
    <dl class="grid grid-cols-3 gap-y-2 text-sm">
        <dt class="text-gray-500">الطالب</dt><dd class="col-span-2">{{ $result->registration?->student?->user?->name ?? '—' }}</dd>
        <dt class="text-gray-500">الرقم الجامعي</dt><dd class="col-span-2">{{ $result->registration?->student?->student_number ?? '—' }}</dd>
        <dt class="text-gray-500">المقرر</dt><dd class="col-span-2">{{ $result->registration?->course?->name ?? '—' }}</dd>
        <dt class="text-gray-500">الفصل</dt><dd class="col-span-2">{{ $result->registration?->semester?->name ?? '—' }}</dd>
        <dt class="text-gray-500">أعمال السنة</dt><dd class="col-span-2">{{ $result->coursework_score ?? '—' }}</dd>
        <dt class="text-gray-500">النصفي</dt><dd class="col-span-2">{{ $result->midterm_score ?? '—' }}</dd>
        <dt class="text-gray-500">النهائي</dt><dd class="col-span-2">{{ $result->final_score ?? '—' }}</dd>
        <dt class="text-gray-500">المجموع</dt><dd class="col-span-2 font-semibold">{{ $result->total_score }}</dd>
        <dt class="text-gray-500">التقدير</dt><dd class="col-span-2 font-semibold">{{ $result->grade }}</dd>
        <dt class="text-gray-500">نقاط التقدير</dt><dd class="col-span-2">{{ $result->grade_points }}</dd>
        <dt class="text-gray-500">الحالة</dt><dd class="col-span-2">@include('partials.enum-label', ['value' => $result->status])</dd>
        @if ($result->published_at)
            <dt class="text-gray-500">تاريخ النشر</dt><dd class="col-span-2">{{ $result->published_at }}</dd>
        @endif
    </dl>
    <div class="flex gap-2 pt-3 border-t">
        <a href="{{ route('results.results.edit', $result) }}" class="btn-primary">تعديل</a>
        @if (!$result->published_at && $result->status !== 'incomplete')
            <form action="{{ route('results.results.publish', $result) }}" method="POST" class="inline">
                @csrf
                <button class="btn-secondary">نشر</button>
            </form>
        @endif
        @if ($result->published_at)
            <form action="{{ route('results.results.unpublish', $result) }}" method="POST" class="inline">
                @csrf
                <button class="btn-secondary">إلغاء النشر</button>
            </form>
        @endif
        <a href="{{ route('results.results.index') }}" class="btn-secondary">رجوع</a>
    </div>
</div>
@endsection