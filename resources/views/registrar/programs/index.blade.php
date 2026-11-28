@extends('layouts.app')

@section('title', __('programs.title'))

@section('content')
<div class="space-y-6">

    <x-page-header
        :title="__('programs.title')"
        :subtitle="__('programs.subtitle')"
    >
        <x-slot:actions>
            <x-button href="{{ route('registrar.programs.create') }}">
                <x-icon name="plus" class="h-4 w-4" />
                {{ __('programs.add') }}
            </x-button>
        </x-slot:actions>
    </x-page-header>

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <x-stat-card
            :label="__('programs.all_programs')"
            :value="$programs->total()"
            icon-name="graduation-cap"
        />
        <x-stat-card
            :label="__('programs.active')"
            :value="$programs->getCollection()->where('is_active', true)->count()"
            icon-name="check-circle"
        />
    </div>

    <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="flex flex-col gap-2 border-b border-gray-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h3 class="font-bold text-gray-900">{{ __('programs.title') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('programs.program_count') }}: {{ $programs->total() }}</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[850px] text-sm">
                <thead class="bg-gray-50 text-xs font-semibold text-gray-600">
                    <tr>
                        <th class="px-4 py-4 text-start">#</th>
                        <th class="px-4 py-4 text-start">{{ __('programs.code') }}</th>
                        <th class="px-4 py-4 text-start">{{ __('programs.name') }}</th>
                        <th class="px-4 py-4 text-start">{{ __('programs.degree_level') }}</th>
                        <th class="px-4 py-4 text-center">{{ __('programs.duration') }}</th>
                        <th class="px-4 py-4 text-center">{{ __('programs.courses_count') }}</th>
                        <th class="px-4 py-4 text-center">{{ __('programs.students_count') }}</th>
                        <th class="px-4 py-4 text-center">{{ __('programs.status') }}</th>
                        <th class="px-4 py-4 text-center">{{ __('programs.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($programs as $prog)
                        @php
                            $degreeValue = $prog->degree_level instanceof \BackedEnum
                                ? $prog->degree_level->value
                                : $prog->degree_level;
                        @endphp
                        <tr class="transition hover:bg-gray-50">
                            <td class="px-4 py-4 text-gray-500">{{ $prog->id }}</td>
                            <td class="px-4 py-4">
                                <span class="rounded-lg bg-gray-100 px-2.5 py-1 font-mono text-xs font-bold text-gray-700">
                                    {{ $prog->code }}
                                </span>
                            </td>
                            <td class="px-4 py-4">
                                <div class="font-semibold text-gray-900">{{ $prog->name }}</div>
                                @if($prog->name_en)
                                    <div class="mt-1 text-xs text-gray-500">{{ $prog->name_en }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-4 text-gray-700">
                                {{ __('programs.' . ($degreeValue ?? 'not_specified')) }}
                            </td>
                            <td class="px-4 py-4 text-center tabular-nums">
                                {{ $prog->duration_years }} {{ __('programs.years') }}
                            </td>
                            <td class="px-4 py-4 text-center tabular-nums">{{ $prog->courses_count }}</td>
                            <td class="px-4 py-4 text-center tabular-nums">{{ $prog->students_count }}</td>
                            <td class="px-4 py-4 text-center">
                                @if($prog->is_active)
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700">
                                        {{ __('programs.active') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700">
                                        {{ __('programs.inactive') }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center justify-center gap-2">
                                    <a href="{{ route('registrar.programs.show', $prog) }}"
                                       class="rounded-lg p-2 text-gray-600 transition hover:bg-gray-100 hover:text-gray-900"
                                       title="{{ __('programs.view') }}">
                                        <x-icon name="eye" class="h-4 w-4" />
                                    </a>
                                    <a href="{{ route('registrar.programs.edit', $prog) }}"
                                       class="rounded-lg p-2 text-primary-700 transition hover:bg-primary-50"
                                       title="{{ __('programs.edit') }}">
                                        <x-icon name="pencil" class="h-4 w-4" />
                                    </a>
                                    <form action="{{ route('registrar.programs.destroy', $prog) }}" method="POST"
                                          onsubmit="return confirm(@js(__('programs.confirm_delete')))">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="rounded-lg p-2 text-red-600 transition hover:bg-red-50"
                                                title="{{ __('programs.delete') }}">
                                            <x-icon name="trash-2" class="h-4 w-4" />
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-14 text-center">
                                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-gray-100 text-gray-400">
                                    <x-icon name="graduation-cap" class="h-7 w-7" />
                                </div>
                                <p class="mt-4 font-semibold text-gray-700">{{ __('programs.no_programs') }}</p>
                                <a href="{{ route('registrar.programs.create') }}"
                                   class="mt-3 inline-flex items-center gap-2 text-sm font-semibold text-primary-700 hover:underline">
                                    <x-icon name="plus" class="h-4 w-4" />
                                    {{ __('programs.add') }}
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($programs->hasPages())
            <div class="border-t border-gray-100 px-5 py-4">
                {{ $programs->links() }}
            </div>
        @endif
    </div>
</div>
@endsection