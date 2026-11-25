@extends('layouts.app')
@section('title', __('students.title'))

@section('content')
<div class="space-y-6">
    <x-page-header :title="__('students.title')" :subtitle="__('students.subtitle')">
        <x-button variant="primary" :href="route('registrar.students.create')">
            <x-icon name="plus" :size="16" />
            <span class="ms-1">{{ __('students.actions.create') }}</span>
        </x-button>
    </x-page-header>

    <x-card>
        <form method="GET" action="{{ route('registrar.students.index') }}"
              class="grid grid-cols-1 md:grid-cols-4 gap-3">
            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('students.filters.search') }}</label>
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="{{ __('students.filters.search_placeholder') }}"
                       class="input w-full" />
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('students.fields.program') }}</label>
                <select name="program_id" class="input w-full">
                    <option value="">{{ __('students.filters.all_programs') }}</option>
                    @foreach ($programs as $program)
                        <option value="{{ $program->id }}" @selected((int) request('program_id') === $program->id)>
                            {{ $program->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1">{{ __('students.fields.status') }}</label>
                <select name="status" class="input w-full">
                    <option value="">{{ __('students.filters.all_statuses') }}</option>
                    @foreach (\App\Enums\StudentStatus::cases() as $st_)
                        <option value="{{ $st_->value }}" @selected(request('status') === $st_->value)>
                            {{ __('students.statuses.' . $st_->value) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <x-button variant="primary" type="submit">
                    <x-icon name="search" :size="16" />
                    <span class="ms-1">{{ __('students.filters.apply') }}</span>
                </x-button>
                <x-button variant="secondary" :href="route('registrar.students.index')">
                    {{ __('students.filters.reset') }}
                </x-button>
            </div>
        </form>
    </x-card>

    <x-table-wrapper>
        <table class="table w-full text-sm">
            <thead>
                <tr>
                    <th>{{ __('students.fields.student_number') }}</th>
                    <th>{{ __('students.fields.name') }}</th>
                    <th>{{ __('students.fields.program') }}</th>
                    <th>{{ __('students.fields.status') }}</th>
                    <th>{{ __('students.fields.enrollment_date') }}</th>
                    <th class="text-end">{{ __('students.fields.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($students as $st)
                    @php $sv = $st->status instanceof \BackedEnum ? $st->status->value : $st->status; @endphp
                    <tr>
                        <td class="font-mono">{{ $st->student_number }}</td>
                        <td>{{ $st->user?->name ?? '—' }}</td>
                        <td>{{ $st->program?->name ?? '—' }}</td>
                        <td>
                            <x-badge :color="match($sv) {
                                'active'    => 'success',
                                'suspended' => 'warning',
                                'graduated' => 'info',
                                'withdrawn' => 'danger',
                                default     => 'primary',
                            }">{{ __('students.statuses.' . $sv) }}</x-badge>
                        </td>
                        <td>{{ $st->enrollment_date }}</td>
                        <td class="text-end">
                            <div class="inline-flex gap-1">
                                <x-button variant="secondary" size="sm" :href="route('registrar.students.show', $st)" :title="__('students.actions.view')">
                                    <x-icon name="eye" :size="16" />
                                </x-button>
                                <x-button variant="secondary" size="sm" :href="route('registrar.students.edit', $st)" :title="__('students.actions.edit')">
                                    <x-icon name="pencil" :size="16" />
                                </x-button>
                                <form method="POST" action="{{ route('registrar.students.destroy', $st) }}"
                                      onsubmit="return confirm({{ json_encode(__('students.actions.confirm_delete')) }});" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-secondary btn-sm text-red-600" title="{{ __('students.actions.delete') }}">
                                        <x-icon name="trash" :size="16" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <x-empty-state icon="👥"
                                :title="__('students.empty.title')"
                                :description="__('students.empty.description')" />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <x-slot name="footer">{{ $students->links() }}</x-slot>
    </x-table-wrapper>
</div>
@endsection