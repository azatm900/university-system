@extends('layouts.app')

@section('title', $course->name)

@section('content')
<x-page-header :title="$course->name" :subtitle="$course->code">
    <x-button variant="secondary" :href="route('registrar.courses.edit', $course)">
        <x-icon name="pencil" :size="16" /> {{ __('courses.actions.edit') }}
    </x-button>
    <x-button variant="secondary" :href="route('registrar.courses.index')">
        {{ __('courses.actions.back') }}
    </x-button>
</x-page-header>

<x-card :title="__('courses.show.info')">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <p class="text-sm text-gray-500">{{ __('courses.fields.code') }}</p>
            <p class="font-mono font-medium">{{ $course->code }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('courses.fields.name') }}</p>
            <p class="font-medium">{{ $course->name }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('courses.fields.program') }}</p>
            <p class="font-medium">{{ $course->program?->name ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('courses.fields.credit_hours') }}</p>
            <p class="font-medium">{{ $course->credit_hours }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('courses.fields.level') }}</p>
            <p class="font-medium">{{ $course->level }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('courses.fields.semester_number') }}</p>
            <p class="font-medium">{{ $course->semester_number }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('courses.fields.course_type') }}</p>
            <x-badge :color="$course->course_type?->value === 'compulsory' ? 'primary' : 'info'">
                {{ __('courses.types.' . ($course->course_type?->value ?? 'compulsory')) }}
            </x-badge>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('courses.fields.is_active') }}</p>
            <x-badge :color="$course->is_active ? 'success' : 'danger'">
                {{ $course->is_active ? __('courses.statuses.active') : __('courses.statuses.inactive') }}
            </x-badge>
        </div>
        @if($course->description)
        <div class="md:col-span-3">
            <p class="text-sm text-gray-500">{{ __('courses.fields.description') }}</p>
            <p class="font-medium whitespace-pre-line">{{ $course->description }}</p>
        </div>
        @endif
    </div>
</x-card>

<x-card :title="__('courses.show.registrations')" class="mt-4">
    @if($course->registrations->isEmpty())
        <x-empty-state icon="📋" :title="__('courses.show.no_registrations')" />
    @else
        <table class="table w-full">
            <thead>
                <tr>
                    <th>{{ __('registrations.fields.student') }}</th>
                    <th>{{ __('registrations.fields.semester') }}</th>
                    <th>{{ __('registrations.fields.registered_at') }}</th>
                    <th>{{ __('registrations.fields.status') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($course->registrations as $reg)
                    <tr>
                        <td>{{ $reg->student?->user?->name ?? '-' }}</td>
                        <td>{{ $reg->semester?->name ?? '-' }}</td>
                        <td>{{ $reg->registered_at?->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            <x-badge :color="match($reg->status?->value) { 'completed' => 'success', 'dropped' => 'warning', default => 'primary' }">
                                {{ __('registrations.statuses.' . ($reg->status?->value ?? 'registered')) }}
                            </x-badge>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</x-card>
@endsection