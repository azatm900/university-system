@extends('layouts.app')

@section('title', __('registrations.singular') . ' #' . $registration->id)

@section('content')
<x-page-header :title="__('registrations.show.info')" :subtitle="__('registrations.singular') . ' #' . $registration->id">
    <x-button variant="secondary" :href="route('registrar.registrations.edit', $registration)">
        <x-icon name="pencil" :size="16" /> {{ __('registrations.actions.edit') }}
    </x-button>
    <x-button variant="secondary" :href="route('registrar.registrations.index')">
        {{ __('registrations.actions.back') }}
    </x-button>
</x-page-header>

<x-card :title="__('registrations.show.info')">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p class="text-sm text-gray-500">{{ __('registrations.fields.student') }}</p>
            <p class="font-medium">{{ $registration->student?->user?->name ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('registrations.fields.course') }}</p>
            <p class="font-medium">
                {{ $registration->course?->code }} — {{ $registration->course?->name }}
                @if($registration->course?->program)
                    <span class="text-sm text-gray-500">({{ $registration->course->program->name }})</span>
                @endif
            </p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('registrations.fields.semester') }}</p>
            <p class="font-medium">{{ $registration->semester?->name ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('registrations.fields.registered_at') }}</p>
            <p class="font-medium">{{ $registration->registered_at?->format('Y-m-d') ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('registrations.fields.status') }}</p>
            <x-badge :color="match($registration->status?->value) { 'completed' => 'success', 'dropped' => 'warning', default => 'primary' }">
                {{ __('registrations.statuses.' . ($registration->status?->value ?? 'registered')) }}
            </x-badge>
        </div>
    </div>
</x-card>

<x-card :title="__('registrations.show.result')" class="mt-4">
    @if($registration->result)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-500">{{ __('registrations.fields.result') }}</p>
                <p class="font-medium">{{ $registration->result->score ?? '—' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Grade</p>
                <p class="font-medium">{{ $registration->result->grade ?? '—' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Published</p>
                <x-badge :color="($registration->result->is_published ?? false) ? 'success' : 'warning'">
                    {{ ($registration->result->is_published ?? false) ? 'Yes' : 'No' }}
                </x-badge>
            </div>
        </div>
    @else
        <x-empty-state icon="📊" :title="__('registrations.show.no_result')" />
    @endif
</x-card>
@endsection