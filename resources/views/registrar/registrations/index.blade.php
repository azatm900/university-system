@extends('layouts.app')

@section('title', __('registrations.title'))

@section('content')
<x-page-header :title="__('registrations.title')" :subtitle="__('registrations.subtitle')">
    <x-button variant="primary" :href="route('registrar.registrations.create')">
        <x-icon name="plus" :size="16" /> {{ __('registrations.actions.create') }}
    </x-button>
</x-page-header>

<x-card class="mb-4">
    <form method="GET" action="{{ route('registrar.registrations.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label class="block text-sm mb-1">{{ __('registrations.fields.student') }}</label>
            <select name="student_id" class="input">
                <option value="">{{ __('registrations.filters.all') }}</option>
                @foreach($students as $st)
                    <option value="{{ $st->id }}" @selected(request('student_id') == $st->id)>
                        {{ $st->user?->name ?? ('#' . $st->id) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">{{ __('registrations.fields.course') }}</label>
            <select name="course_id" class="input">
                <option value="">{{ __('registrations.filters.all') }}</option>
                @foreach($courses as $c)
                    <option value="{{ $c->id }}" @selected(request('course_id') == $c->id)>{{ $c->code }} — {{ $c->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">{{ __('registrations.fields.semester') }}</label>
            <select name="semester_id" class="input">
                <option value="">{{ __('registrations.filters.all') }}</option>
                @foreach($semesters as $s)
                    <option value="{{ $s->id }}" @selected(request('semester_id') == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">{{ __('registrations.fields.status') }}</label>
            <select name="status" class="input">
                <option value="">{{ __('registrations.filters.all') }}</option>
                @foreach(\App\Enums\RegistrationStatus::cases() as $case)
                    <option value="{{ $case->value }}" @selected(request('status') === $case->value)>{{ __('registrations.statuses.' . $case->value) }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-4 flex gap-2">
            <x-button type="submit" variant="primary" size="sm">
                <x-icon name="search" :size="16" /> {{ __('common.search') ?? 'Search' }}
            </x-button>
            <x-button variant="secondary" size="sm" :href="route('registrar.registrations.index')">
                {{ __('common.reset') ?? 'Reset' }}
            </x-button>
        </div>
    </form>
</x-card>

<x-table-wrapper>
    @if($registrations->isEmpty())
        <x-empty-state icon="📋" :title="__('registrations.empty.title')" :description="__('registrations.empty.description')" />
    @else
        <table class="table w-full">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('registrations.fields.student') }}</th>
                    <th>{{ __('registrations.fields.course') }}</th>
                    <th>{{ __('registrations.fields.semester') }}</th>
                    <th>{{ __('registrations.fields.registered_at') }}</th>
                    <th>{{ __('registrations.fields.status') }}</th>
                    <th>{{ __('common.actions') ?? 'Actions' }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($registrations as $reg)
                    <tr>
                        <td>{{ $reg->id }}</td>
                        <td>{{ $reg->student?->user?->name ?? '-' }}</td>
                        <td>{{ $reg->course?->code }} — {{ $reg->course?->name }}</td>
                        <td>{{ $reg->semester?->name ?? '-' }}</td>
                        <td>{{ $reg->registered_at?->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            <x-badge :color="match($reg->status?->value) { 'completed' => 'success', 'dropped' => 'warning', default => 'primary' }">
                                {{ __('registrations.statuses.' . ($reg->status?->value ?? 'registered')) }}
                            </x-badge>
                        </td>
                        <td>
                            <div class="flex items-center gap-1">
                                <x-button variant="secondary" size="sm" :href="route('registrar.registrations.show', $reg)">
                                    <x-icon name="eye" :size="16" />
                                </x-button>
                                <x-button variant="secondary" size="sm" :href="route('registrar.registrations.edit', $reg)">
                                    <x-icon name="pencil" :size="16" />
                                </x-button>
                                <form method="POST" action="{{ route('registrar.registrations.destroy', $reg) }}" onsubmit="return confirm('{{ __('registrations.messages.confirm_delete') }}')">
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
        <x-slot name="footer">{{ $registrations->links() }}</x-slot>
    @endif
</x-table-wrapper>
@endsection