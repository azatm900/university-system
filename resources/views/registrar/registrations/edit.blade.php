@extends('layouts.app')

@section('title', __('registrations.actions.edit'))

@section('content')
<x-page-header :title="__('registrations.actions.edit')" :subtitle="__('registrations.singular') . ' #' . $registration->id">
    <x-button variant="secondary" :href="route('registrar.registrations.index')">{{ __('registrations.actions.back') }}</x-button>
</x-page-header>

<form method="POST" action="{{ route('registrar.registrations.update', $registration) }}">
    @csrf
    @method('PUT')
    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm mb-1 text-gray-500">{{ __('registrations.fields.student') }}</label>
                <p class="font-medium">{{ $registration->student?->user?->name ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm mb-1 text-gray-500">{{ __('registrations.fields.course') }}</label>
                <p class="font-medium">{{ $registration->course?->name ?? '-' }}</p>
            </div>
            <div>
                <label class="block text-sm mb-1 text-gray-500">{{ __('registrations.fields.semester') }}</label>
                <p class="font-medium">{{ $registration->semester?->name ?? '-' }}</p>
            </div>
        </div>

        <div>
            <label class="block text-sm mb-1">{{ __('registrations.fields.status') }} <span class="text-red-500">*</span></label>
            <select name="status" class="input" required>
                @foreach(\App\Enums\RegistrationStatus::cases() as $case)
                    <option value="{{ $case->value }}" @selected(old('status', $registration->status?->value ?? 'registered') === $case->value)>
                        {{ __('registrations.statuses.' . $case->value) }}
                    </option>
                @endforeach
            </select>
            @error('status') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
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