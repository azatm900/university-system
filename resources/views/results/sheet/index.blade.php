@extends('layouts.app')

@section('title', __('results_ui.sheet_title'))

@section('content')
<div class="space-y-6">

    <x-page-header
        :title="__('results_ui.sheet_title')"
        :subtitle="__('results_ui.sheet_subtitle')"
    >
        <x-slot:actions>
            <x-button href="{{ route('results.results.index') }}" variant="secondary">
                <x-icon name="arrow-left" class="w-4 h-4" />
                {{ __('results_ui.back') }}
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
            <div class="mb-2 flex items-center gap-2 font-semibold">
                <x-icon name="alert-circle" class="h-5 w-5" />
                {{ __('validation.errors') }}
            </div>
            <ul class="list-inside list-disc space-y-1 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-gray-100 px-6 py-5">
            <div class="flex items-start gap-3">
                <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-primary-50 text-primary-700">
                    <x-icon name="file-spreadsheet" class="h-6 w-6" />
                </div>
                <div>
                    <h3 class="text-base font-bold text-gray-900">
                        {{ __('results_ui.sheet_title') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('results_ui.sheet_subtitle') }}
                    </p>
                </div>
            </div>
        </div>

        <form method="GET" action="{{ route('results.sheet.show') }}" class="p-6">
            <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 xl:grid-cols-4">

                <div class="space-y-2">
                    <label for="program_id" class="block text-sm font-semibold text-gray-700">
                        {{ __('results_ui.sheet_program') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="program_id"
                        name="program_id"
                        required
                        class="w-full rounded-xl border border-gray-300 bg-white px-3 py-3 text-sm text-gray-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100"
                    >
                        <option value="">{{ __('results_ui.sheet_select_program') }}</option>
                        @foreach($programs as $p)
                            <option value="{{ $p->id }}" {{ (string) request('program_id') === (string) $p->id ? 'selected' : '' }}>
                                {{ $p->code }} — {{ $p->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('program_id')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="level" class="block text-sm font-semibold text-gray-700">
                        {{ __('results_ui.sheet_level') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="level"
                        name="level"
                        required
                        class="w-full rounded-xl border border-gray-300 bg-white px-3 py-3 text-sm text-gray-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100"
                    >
                        <option value="">—</option>
                        @foreach($levels as $l)
                            <option value="{{ $l }}" {{ (string) request('level') === (string) $l ? 'selected' : '' }}>
                                {{ __('results_ui.sheet_level_label') }} {{ $l }}
                            </option>
                        @endforeach
                    </select>
                    @error('level')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="semester_id" class="block text-sm font-semibold text-gray-700">
                        {{ __('results_ui.sheet_semester') }}
                        <span class="text-red-500">*</span>
                    </label>
                    <select
                        id="semester_id"
                        name="semester_id"
                        required
                        class="w-full rounded-xl border border-gray-300 bg-white px-3 py-3 text-sm text-gray-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100"
                    >
                        <option value="">{{ __('results_ui.sheet_select_semester') }}</option>
                        @foreach($semesters as $s)
                            <option value="{{ $s->id }}" {{ (string) request('semester_id') === (string) $s->id ? 'selected' : '' }}>
                                {{ $s->code }} — {{ $s->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('semester_id')
                        <p class="text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="space-y-2">
                    <label for="course_id" class="block text-sm font-semibold text-gray-700">
                        {{ __('results_ui.sheet_course') }}
                        <span class="font-normal text-gray-400">({{ __('results_ui.sheet_optional') }})</span>
                    </label>
                    <select
                        id="course_id"
                        name="course_id"
                        class="w-full rounded-xl border border-gray-300 bg-white px-3 py-3 text-sm text-gray-800 shadow-sm transition focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100"
                    >
                        <option value="">{{ __('results_ui.sheet_all_courses') }}</option>
                        @if(request('program_id'))
                            @php
                                $courses = \App\Models\Course::where('program_id', request('program_id'))
                                    ->when(request('level'), fn($q) => $q->where('level', request('level')))
                                    ->orderBy('code')
                                    ->get(['id', 'code', 'name']);
                            @endphp
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}" {{ (string) request('course_id') === (string) $c->id ? 'selected' : '' }}>
                                    {{ $c->code }} — {{ $c->name }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>

            </div>

            <div class="mt-7 flex flex-col-reverse gap-3 border-t border-gray-100 pt-5 sm:flex-row sm:items-center sm:justify-between">
                <p class="text-xs text-gray-500">
                    <span class="text-red-500">*</span>
                    {{ __('validation.required') }}
                </p>

                <div class="flex flex-col gap-2 sm:flex-row">
                    <a
                        href="{{ route('results.sheet.index') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-xl border border-gray-300 bg-white px-5 py-3 text-sm font-semibold text-gray-700 transition hover:bg-gray-50"
                    >
                        <x-icon name="rotate-ccw" class="h-4 w-4" />
                        {{ __('common.reset') }}
                    </a>

                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-xl bg-primary-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                    >
                        <x-icon name="file-search" class="h-4 w-4" />
                        {{ __('results_ui.sheet_show') }}
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection