@extends('layouts.app')

@section('title', __('courses.title'))

@section('content')
<x-page-header :title="__('courses.title')" :subtitle="__('courses.subtitle')">
    <x-button variant="primary" :href="route('registrar.courses.create')">
        <x-icon name="plus" :size="16" /> {{ __('courses.actions.create') }}
    </x-button>
</x-page-header>

<x-card class="mb-4">
    <form method="GET" action="{{ route('registrar.courses.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label class="block text-sm mb-1">{{ __('courses.filters.search') }}</label>
            <input type="text" name="search" value="{{ request('search') }}" class="input" placeholder="{{ __('courses.filters.search_placeholder') }}">
        </div>
        <div>
            <label class="block text-sm mb-1">{{ __('courses.fields.program') }}</label>
            <select name="program_id" class="input">
                <option value="">{{ __('courses.filters.all') }}</option>
                @foreach($programs as $p)
                    <option value="{{ $p->id }}" @selected(request('program_id') == $p->id)>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">{{ __('courses.fields.semester_number') }}</label>
            <select name="semester_number" class="input">
                <option value="">{{ __('courses.filters.all') }}</option>
                @for($i = 1; $i <= 4; $i++)
                    <option value="{{ $i }}" @selected(request('semester_number') == $i)>{{ $i }}</option>
                @endfor
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">{{ __('courses.fields.is_active') }}</label>
            <select name="is_active" class="input">
                <option value="">{{ __('courses.filters.all') }}</option>
                <option value="1" @selected(request('is_active') === '1')>{{ __('courses.statuses.active') }}</option>
                <option value="0" @selected(request('is_active') === '0')>{{ __('courses.statuses.inactive') }}</option>
            </select>
        </div>
        <div class="md:col-span-4 flex gap-2">
            <x-button type="submit" variant="primary" size="sm">
                <x-icon name="search" :size="16" /> {{ __('common.search') ?? 'Search' }}
            </x-button>
            <x-button variant="secondary" size="sm" :href="route('registrar.courses.index')">
                {{ __('common.reset') ?? 'Reset' }}
            </x-button>
        </div>
    </form>
</x-card>

<x-table-wrapper>
    @if($courses->isEmpty())
        <x-empty-state icon="📚" :title="__('courses.empty.title')" :description="__('courses.empty.description')" />
    @else
        <table class="table w-full">
            <thead>
                <tr>
                    <th>{{ __('courses.fields.code') }}</th>
                    <th>{{ __('courses.fields.name') }}</th>
                    <th>{{ __('courses.fields.program') }}</th>
                    <th>{{ __('courses.fields.credit_hours') }}</th>
                    <th>{{ __('courses.fields.level') }}</th>
                    <th>{{ __('courses.fields.semester_number') }}</th>
                    <th>{{ __('courses.fields.course_type') }}</th>
                    <th>{{ __('courses.fields.is_active') }}</th>
                    <th>{{ __('common.actions') ?? 'Actions' }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($courses as $course)
                    <tr>
                        <td class="font-mono">{{ $course->code }}</td>
                        <td>{{ $course->name }}</td>
                        <td>{{ $course->program?->name ?? '-' }}</td>
                        <td>{{ $course->credit_hours }}</td>
                        <td>{{ $course->level }}</td>
                        <td>{{ $course->semester_number }}</td>
                        <td>
                            <x-badge :color="$course->course_type?->value === 'compulsory' ? 'primary' : 'info'">
                                {{ __('courses.types.' . ($course->course_type?->value ?? 'compulsory')) }}
                            </x-badge>
                        </td>
                        <td>
                            <x-badge :color="$course->is_active ? 'success' : 'danger'">
                                {{ $course->is_active ? __('courses.statuses.active') : __('courses.statuses.inactive') }}
                            </x-badge>
                        </td>
                        <td>
                            <div class="flex items-center gap-1">
                                <x-button variant="secondary" size="sm" :href="route('registrar.courses.show', $course)">
                                    <x-icon name="eye" :size="16" />
                                </x-button>
                                <x-button variant="secondary" size="sm" :href="route('registrar.courses.edit', $course)">
                                    <x-icon name="pencil" :size="16" />
                                </x-button>
                                <form method="POST" action="{{ route('registrar.courses.destroy', $course) }}" onsubmit="return confirm('{{ __('courses.messages.confirm_delete') }}')">
                                    @csrf
                                    @method('DELETE')
                                    <x-button type="submit" variant="secondary" size="sm">
                                        <x-icon name="trash" :size="16" />
                                    </x-button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <x-slot name="footer">{{ $courses->links() }}</x-slot>
    @endif
</x-table-wrapper>
@endsection