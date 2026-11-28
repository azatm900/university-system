@extends('layouts.app')

@section('title', __('programs.show_title'))

@section('content')
@php
    $degreeValue = $program->degree_level instanceof \BackedEnum
        ? $program->degree_level->value
        : $program->degree_level;
@endphp

<div class="space-y-6">
    <x-page-header
        :title="$program->name"
        :subtitle="__('programs.show_title')"
    >
        <x-slot:actions>
            <x-button href="{{ route('registrar.programs.edit', $program) }}">
                <x-icon name="pencil" class="h-4 w-4" />
                {{ __('programs.edit') }}
            </x-button>
            <x-button href="{{ route('registrar.programs.index') }}" variant="secondary">
                <x-icon name="arrow-left" class="h-4 w-4" />
                {{ __('programs.back') }}
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <x-stat-card
            :label="__('programs.courses_count')"
            :value="$program->courses_count ?? $program->courses()->count()"
            icon-name="book-open"
        />
        <x-stat-card
            :label="__('programs.students_count')"
            :value="$program->students_count ?? $program->students()->count()"
            icon-name="users"
        />
        <x-stat-card
            :label="__('programs.duration')"
            :value="$program->duration_years . ' ' . __('programs.years')"
            icon-name="calendar-days"
        />
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-3 border-b border-gray-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="font-bold text-gray-900">{{ __('programs.overview') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('programs.program_information') }}</p>
            </div>

            @if($program->is_active)
                <span class="inline-flex w-fit items-center rounded-full bg-green-50 px-3 py-1.5 text-xs font-semibold text-green-700">
                    {{ __('programs.active') }}
                </span>
            @else
                <span class="inline-flex w-fit items-center rounded-full bg-red-50 px-3 py-1.5 text-xs font-semibold text-red-700">
                    {{ __('programs.inactive') }}
                </span>
            @endif
        </div>

        <dl class="grid grid-cols-1 gap-x-8 divide-y divide-gray-100 px-6 sm:grid-cols-2 sm:divide-y-0">
            <div class="flex items-start justify-between gap-4 border-b border-gray-100 py-4">
                <dt class="text-sm text-gray-500">{{ __('programs.program_id') }}</dt>
                <dd class="text-sm font-semibold text-gray-900">{{ $program->id }}</dd>
            </div>

            <div class="flex items-start justify-between gap-4 border-b border-gray-100 py-4">
                <dt class="text-sm text-gray-500">{{ __('programs.code') }}</dt>
                <dd class="font-mono text-sm font-semibold text-gray-900">{{ $program->code }}</dd>
            </div>

            <div class="flex items-start justify-between gap-4 border-b border-gray-100 py-4">
                <dt class="text-sm text-gray-500">{{ __('programs.name') }}</dt>
                <dd class="text-sm font-semibold text-gray-900">{{ $program->name }}</dd>
            </div>

            <div class="flex items-start justify-between gap-4 border-b border-gray-100 py-4">
                <dt class="text-sm text-gray-500">{{ __('programs.name_en') }}</dt>
                <dd dir="ltr" class="text-end text-sm font-semibold text-gray-900">{{ $program->name_en ?: '—' }}</dd>
            </div>

            <div class="flex items-start justify-between gap-4 border-b border-gray-100 py-4">
                <dt class="text-sm text-gray-500">{{ __('programs.degree_level') }}</dt>
                <dd class="text-sm font-semibold text-gray-900">{{ __('programs.' . ($degreeValue ?? 'not_specified')) }}</dd>
            </div>

            <div class="flex items-start justify-between gap-4 border-b border-gray-100 py-4">
                <dt class="text-sm text-gray-500">{{ __('programs.duration') }}</dt>
                <dd class="text-sm font-semibold text-gray-900">{{ $program->duration_years }} {{ __('programs.years') }}</dd>
            </div>

            <div class="flex items-start justify-between gap-4 border-b border-gray-100 py-4 sm:col-span-2">
                <dt class="text-sm text-gray-500">{{ __('programs.description') }}</dt>
                <dd class="max-w-2xl whitespace-pre-line text-end text-sm leading-6 text-gray-800">{{ $program->description ?: '—' }}</dd>
            </div>
        </dl>
    </div>
</div>
@endsection