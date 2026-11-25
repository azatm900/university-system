@extends('layouts.app')
@section('title', 'عرض برنامج')
@section('content')
<div class="card max-w-2xl mx-auto p-6 space-y-3">
    <h3 class="font-semibold text-lg border-b pb-3">{{ $program->name }}</h3>
    <dl class="grid grid-cols-3 gap-y-2 text-sm">
        <dt class="text-gray-500">#</dt><dd class="col-span-2">{{ $program->id }}</dd>
        <dt class="text-gray-500">الرمز</dt><dd class="col-span-2">{{ $program->code }}</dd>
        <dt class="text-gray-500">الاسم EN</dt><dd class="col-span-2">{{ $program->name_en ?? '—' }}</dd>
        <dt class="text-gray-500">المستوى</dt><dd class="col-span-2">@include('partials.enum-label', ['value' => $program->degree_level])</dd>
        <dt class="text-gray-500">المدة</dt><dd class="col-span-2">{{ $program->duration_years }} سنة</dd>
        <dt class="text-gray-500">الوصف</dt><dd class="col-span-2">{{ $program->description ?? '—' }}</dd>
        <dt class="text-gray-500">الحالة</dt><dd class="col-span-2">@if($program->is_active) نشط @else معطل @endif</dd>
    </dl>
    <div class="flex gap-2 pt-3 border-t">
        <a href="{{ route('registrar.programs.edit', $program) }}" class="btn-primary">تعديل</a>
        <a href="{{ route('registrar.programs.index') }}" class="btn-secondary">رجوع</a>
    </div>
</div>
@endsection