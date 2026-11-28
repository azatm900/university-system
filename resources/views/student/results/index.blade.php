@extends('layouts.app')

@section('title', __('student_portal.results_title'))

@section('content')
    <div class="mb-5 animate-fade-in-up">
        <h1 class="text-xl md:text-2xl font-bold" style="color: var(--text)">
            {{ __('student_portal.results_title') }}
        </h1>
        <p class="text-sm text-muted mt-1">
            {{ __('student_portal.results_subtitle') }}
        </p>
    </div>

    <div class="mb-6 animate-fade-in-up">
        <x-card>
            <form method="GET" action="{{ route('student.results.index') }}"
                  class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div class="flex-1">
                    <label for="level" class="block text-sm font-medium mb-2">
                        {{ __('student_portal.select_level') }}
                    </label>
                    <select id="level" name="level"
                            class="form-select w-full rounded-lg border p-2.5 text-sm"
                            style="border-color: var(--border); background: var(--surface); color: var(--text)">
                        @foreach($levels as $level)
                            <option value="{{ $level }}" @selected($selectedLevel === $level)>
                                {{ __('student_portal.level_number', ['number' => $level]) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    {{ __('student_portal.show_level') }}
                </button>
            </form>
        </x-card>
    </div>

    <div class="mb-6 grid grid-cols-1 sm:grid-cols-2 gap-4 animate-fade-in-up">
        <x-card>
            <p class="text-xs text-muted">{{ __('student_portal.cumulative_gpa') }}</p>
            <p class="text-xl font-bold mt-2" style="color: var(--text)">
                {{ $cumulativeGpa !== null
                    ? number_format($cumulativeGpa, 2) . ' / ' . number_format($gpaMax, 1)
                    : __('student_portal.not_available') }}
            </p>
        </x-card>

        <x-card>
            <p class="text-xs text-muted">{{ __('student_portal.level') }}</p>
            <p class="text-xl font-bold mt-2" style="color: var(--text)">
                {{ __('student_portal.level_number', ['number' => $selectedLevel]) }}
            </p>
        </x-card>
    </div>

    <div class="mb-6 animate-fade-in-up">
        <x-card :title="__('student_portal.results_title')">
            @if($resultGroups->isEmpty())
                <x-empty-state icon="book-open" :message="__('student_portal.no_results')" />
            @else
                <div class="space-y-5">
                    @foreach($resultGroups as $resultGroup)
                        <div class="rounded-xl border p-3 sm:p-4"
                             style="border-color: var(--border)">
                            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                                <div>
                                    <h3 class="font-semibold text-sm">
                                        {{ $resultGroup['semester']?->name ?? __('student_portal.no_semester') }}
                                    </h3>
                                    @if($resultGroup['semester']?->start_date || $resultGroup['semester']?->end_date)
                                        <p class="text-xs text-muted mt-1">
                                            {{ $resultGroup['semester']?->start_date?->format('Y-m-d') ?? '—' }}
                                            –
                                            {{ $resultGroup['semester']?->end_date?->format('Y-m-d') ?? '—' }}
                                        </p>
                                    @endif
                                </div>

                                <div class="text-sm font-semibold">
                                    {{ __('student_portal.semester_gpa') }}:
                                    {{ $resultGroup['gpa'] !== null
                                        ? number_format($resultGroup['gpa'], 2) . ' / ' . number_format($gpaMax, 1)
                                        : __('student_portal.not_available') }}
                                </div>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-sm min-w-[760px]">
                                    <thead>
                                        <tr class="text-xs text-muted border-b"
                                            style="border-color: var(--border)">
                                            <th class="text-start p-2">{{ __('student_portal.course') }}</th>
                                            <th class="text-start p-2">{{ __('student_portal.course_code') }}</th>
                                            <th class="text-center p-2">{{ __('student_portal.credit_hours') }}</th>
                                            <th class="text-center p-2">{{ __('student_portal.coursework_score') }}</th>
                                            <th class="text-center p-2">{{ __('student_portal.midterm_score') }}</th>
                                            <th class="text-center p-2">{{ __('student_portal.final_score') }}</th>
                                            <th class="text-center p-2">{{ __('student_portal.total_score') }}</th>
                                            <th class="text-center p-2">{{ __('student_portal.grade') }}</th>
                                            <th class="text-center p-2">{{ __('student_portal.result_status') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($resultGroup['results'] as $registration)
                                            @php
                                                $result = $registration->result;
                                                $course = $registration->course;
                                            @endphp
                                            <tr class="border-b last:border-0"
                                                style="border-color: var(--border)">
                                                <td class="p-2 font-medium">{{ $course?->name ?? '—' }}</td>
                                                <td class="p-2">{{ $course?->code ?? '—' }}</td>
                                                <td class="p-2 text-center">{{ $course?->credit_hours ?? '—' }}</td>
                                                <td class="p-2 text-center">{{ $result?->coursework_score ?? '—' }}</td>
                                                <td class="p-2 text-center">{{ $result?->midterm_score ?? '—' }}</td>
                                                <td class="p-2 text-center">{{ $result?->final_score ?? '—' }}</td>
                                                <td class="p-2 text-center font-semibold">{{ $result?->total_score ?? '—' }}</td>
                                                <td class="p-2 text-center font-semibold">{{ $result?->grade ?? '—' }}</td>
                                                <td class="p-2 text-center">
                                                    <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
                                                          style="background: var(--surface); color: var(--text)">
                                                        {{ __('student_portal.published') }}
                                                    </span>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>
@endsection