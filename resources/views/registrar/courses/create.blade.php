@extends('layouts.app')

@section('title', __('courses.actions.create'))

@section('content')
<x-page-header :title="__('courses.actions.create')" :subtitle="__('courses.subtitle')" />

<form method="POST" action="{{ route('registrar.courses.store') }}">
    @csrf
    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">{{ __('courses.fields.code') }} <span class="text-red-500">*</span></label>
                <input type="text" name="code" value="{{ old('code') }}" class="input" maxlength="20" required>
                @error('code') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('courses.fields.name') }} <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" class="input" maxlength="200" required>
                @error('name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('courses.fields.program') }} <span class="text-red-500">*</span></label>
                <select name="program_id" class="input" required>
                    <option value="">— {{ __('common.select') ?? 'Select' }} —</option>
                    @foreach($programs as $p)
                        <option value="{{ $p->id }}" @selected(old('program_id') == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
                @error('program_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('courses.fields.credit_hours') }} <span class="text-red-500">*</span></label>
                <input type="number" name="credit_hours" value="{{ old('credit_hours', 3) }}" class="input" min="1" max="12" required>
                @error('credit_hours') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('courses.fields.level') }} <span class="text-red-500">*</span></label>
                <input type="number" name="level" value="{{ old('level', 1) }}" class="input" min="1" max="10" required>
                @error('level') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('courses.fields.semester_number') }} <span class="text-red-500">*</span></label>
                <select name="semester_number" class="input" required>
                    @for($i = 1; $i <= 4; $i++)
                        <option value="{{ $i }}" @selected(old('semester_number', 1) == $i)>{{ $i }}</option>
                    @endfor
                </select>
                @error('semester_number') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('courses.fields.course_type') }} <span class="text-red-500">*</span></label>
                <select name="course_type" class="input" required>
                    @foreach(\App\Enums\CourseType::cases() as $case)
                        <option value="{{ $case->value }}" @selected(old('course_type', 'compulsory') === $case->value)>{{ __('courses.types.' . $case->value) }}</option>
                    @endforeach
                </select>
                @error('course_type') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('courses.fields.is_active') }}</label>
                <label class="flex items-center gap-2 mt-2">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
                    <span>{{ __('courses.statuses.active') }}</span>
                </label>
                @error('is_active') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">{{ __('courses.fields.description') }}</label>
                <textarea name="description" class="input" rows="4">{{ old('description') }}</textarea>
                @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <x-slot name="footer">
            <div class="flex gap-2">
                <x-button type="submit" variant="primary">{{ __('courses.actions.save') }}</x-button>
                <x-button variant="secondary" :href="route('registrar.courses.index')">{{ __('courses.actions.cancel') }}</x-button>
            </div>
        </x-slot>
    </x-card>
</form>
@endsection