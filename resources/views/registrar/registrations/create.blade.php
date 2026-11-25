@extends('layouts.app')

@section('title', __('registrations.actions.create'))

@section('content')
<x-page-header :title="__('registrations.actions.create')" :subtitle="__('registrations.subtitle')" />

<form method="POST" action="{{ route('registrar.registrations.store') }}">
    @csrf
    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">{{ __('registrations.fields.student') }} <span class="text-red-500">*</span></label>
                <select name="student_id" class="input" required>
                    <option value="">— {{ __('common.select') ?? 'Select' }} —</option>
                    @foreach($students as $st)
                        <option value="{{ $st->id }}" @selected(old('student_id') == $st->id)>
                            {{ $st->user?->name ?? ('#' . $st->id) }}
                        </option>
                    @endforeach
                </select>
                @error('student_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('registrations.fields.course') }} <span class="text-red-500">*</span></label>
                <select name="course_id" class="input" required>
                    <option value="">— {{ __('common.select') ?? 'Select' }} —</option>
                    @foreach($courses as $c)
                        <option value="{{ $c->id }}" @selected(old('course_id') == $c->id)>{{ $c->code }} — {{ $c->name }}</option>
                    @endforeach
                </select>
                @error('course_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('registrations.fields.semester') }} <span class="text-red-500">*</span></label>
                <select name="semester_id" class="input" required>
                    <option value="">— {{ __('common.select') ?? 'Select' }} —</option>
                    @foreach($semesters as $s)
                        <option value="{{ $s->id }}" @selected(old('semester_id') == $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
                @error('semester_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('registrations.fields.registered_at') }}</label>
                <input type="date" name="registered_at" value="{{ old('registered_at', now()->toDateString()) }}" class="input">
                @error('registered_at') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">{{ __('registrations.fields.status') }}</label>
                <select name="status" class="input">
                    @foreach(\App\Enums\RegistrationStatus::cases() as $case)
                        <option value="{{ $case->value }}" @selected(old('status', 'registered') === $case->value)>{{ __('registrations.statuses.' . $case->value) }}</option>
                    @endforeach
                </select>
                @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <x-slot name="footer">
            <div class="flex gap-2">
                <x-button type="submit" variant="primary">{{ __('registrations.actions.save') }}</x-button>
                <x-button variant="secondary" :href="route('registrar.registrations.index')">{{ __('registrations.actions.cancel') }}</x-button>
            </div>
        </x-slot>
    </x-card>
</form>
@endsection