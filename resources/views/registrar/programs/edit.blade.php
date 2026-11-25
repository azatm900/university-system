@extends('layouts.app')
@section('title', 'تعديل برنامج')
@section('content')
<div class="card max-w-2xl mx-auto p-6">
    <form method="POST" action="{{ route('registrar.programs.update', $program) }}" class="space-y-4">
        @csrf @method('PUT')
        <div><label class="block text-sm font-semibold mb-1">الرمز</label><input name="code" value="{{ old('code', $program->code) }}" required class="input-field"></div>
        <div><label class="block text-sm font-semibold mb-1">الاسم</label><input name="name" value="{{ old('name', $program->name) }}" required class="input-field"></div>
        <div><label class="block text-sm font-semibold mb-1">الاسم بالإنجليزية</label><input name="name_en" value="{{ old('name_en', $program->name_en) }}" class="input-field"></div>
        <div><label class="block text-sm font-semibold mb-1">المستوى</label>
            <select name="degree_level" required class="input-field">
                @foreach (\App\Enums\DegreeLevel::cases() as $lvl)
                    @php $cur = $program->degree_level instanceof \BackedEnum ? $program->degree_level->value : $program->degree_level; @endphp
                    <option value="{{ $lvl->value }}" @selected(old('degree_level', $cur)===$lvl->value)>@include('partials.enum-label', ['value' => $lvl])</option>
                @endforeach
            </select></div>
        <div><label class="block text-sm font-semibold mb-1">المدة (سنوات)</label><input name="duration_years" type="number" min="1" max="10" value="{{ old('duration_years', $program->duration_years) }}" required class="input-field"></div>
        <div><label class="block text-sm font-semibold mb-1">الوصف</label><textarea name="description" rows="3" class="input-field">{{ old('description', $program->description) }}</textarea></div>
        <div class="flex items-center gap-2"><input type="checkbox" name="is_active" value="1" id="pae" @checked(old('is_active', $program->is_active))><label for="pae" class="text-sm">نشط</label></div>
        <div class="flex gap-2"><button class="btn-primary">حفظ</button><a href="{{ route('registrar.programs.index') }}" class="btn-secondary">إلغاء</a></div>
    </form>
</div>
@endsection