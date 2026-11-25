@extends('layouts.app')

@section('title', __('users.index.title'))

@section('content')
<div class="space-y-6">
    <x-page-header :title="__('users.index.title')" :subtitle="__('users.index.subtitle')">
        <x-button variant="primary" :href="route('admin.users.create')">
            <x-icon name="plus" :size="16" class="inline" />
            {{ __('users.actions.create') }}
        </x-button>
    </x-page-header>

    <x-table-wrapper :title="__('users.index.title')">
        {{-- Filters --}}
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/30">
            <form method="GET" class="flex gap-2 flex-wrap items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('users.filters.search') }}</label>
                    <input name="search" value="{{ request('search') }}"
                           placeholder="{{ __('users.filters.search_ph') }}"
                           class="input w-full">
                </div>
                <div class="w-44">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('users.filters.role') }}</label>
                    <select name="role" class="input w-full">
                        <option value="">{{ __('users.filters.all_roles') }}</option>
                        @foreach (\App\Enums\UserRole::cases() as $r)
                            <option value="{{ $r->value }}" @selected(request('role') === $r->value)>{{ $r->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="w-36">
                    <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 mb-1">{{ __('users.filters.status') }}</label>
                    <select name="is_active" class="input w-full">
                        <option value="">{{ __('users.filters.all_status') }}</option>
                        <option value="1" @selected(request('is_active') === '1')>{{ __('users.status.active') }}</option>
                        <option value="0" @selected(request('is_active') === '0')>{{ __('users.status.inactive') }}</option>
                    </select>
                </div>
                <x-button type="submit" variant="secondary">{{ __('users.filters.apply') }}</x-button>
                @if (request()->hasAny(['search','role','is_active']))
                    <x-button variant="secondary" :href="route('admin.users.index')">{{ __('users.filters.reset') }}</x-button>
                @endif
            </form>
        </div>

        {{-- Table --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 dark:bg-gray-800/60 text-gray-600 dark:text-gray-300">
                    <tr>
                        <th class="px-4 py-3 text-start w-12 font-semibold">#</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ __('users.fields.name') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ __('users.fields.email') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ __('users.fields.role') }}</th>
                        <th class="px-4 py-3 text-start font-semibold">{{ __('users.fields.status') }}</th>
                        <th class="px-4 py-3 text-end font-semibold">{{ __('users.fields.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse ($users as $u)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/40 transition">
                        <td class="px-4 py-3 text-gray-500">{{ $u->id }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800 dark:text-gray-100">{{ $u->name }}</td>
                        <td class="px-4 py-3 text-gray-600 dark:text-gray-400">{{ $u->email }}</td>
                        <td class="px-4 py-3">
                            @php
                                $roleColor = match($u->role?->value) {
                                    'admin'     => 'danger',
                                    'finance'   => 'warning',
                                    'registrar' => 'info',
                                    'results'   => 'success',
                                    'student'   => 'info',
                                    default     => 'info',
                                };
                            @endphp
                            <x-badge :color="$roleColor">{{ $u->role?->label() ?? '—' }}</x-badge>
                        </td>
                        <td class="px-4 py-3">
                            @if ($u->is_active)
                                <x-badge color="success">{{ __('users.status.active') }}</x-badge>
                            @else
                                <x-badge color="danger">{{ __('users.status.inactive') }}</x-badge>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-end">
                            <div class="flex items-center justify-end gap-1">
                                <a href="{{ route('admin.users.show', $u) }}" title="{{ __('users.actions.view') }}"
                                   class="p-2 text-gray-600 hover:text-brand-700 dark:text-gray-400">
                                    <x-icon name="eye" :size="16" />
                                </a>
                                <a href="{{ route('admin.users.edit', $u) }}" title="{{ __('users.actions.edit') }}"
                                   class="p-2 text-gray-600 hover:text-brand-700 dark:text-gray-400">
                                    <x-icon name="pencil" :size="16" />
                                </a>
                                <a href="{{ route('admin.users.reset-password-form', $u) }}" title="{{ __('users.actions.reset_password') }}"
                                   class="p-2 text-gray-600 hover:text-amber-600 dark:text-gray-400">
                                    <x-icon name="lock" :size="16" />
                                </a>
                                <form action="{{ route('admin.users.toggle', $u) }}" method="POST" class="inline">
                                    @csrf @method('PATCH')
                                    <button type="submit" title="{{ __('users.actions.toggle') }}"
                                            class="p-2 text-gray-600 hover:text-blue-600 dark:text-gray-400">
                                        <x-icon name="{{ $u->is_active ? 'x' : 'check-circle' }}" :size="16" />
                                    </button>
                                </form>
                                <form action="{{ route('admin.users.destroy', $u) }}" method="POST" class="inline"
                                      onsubmit="return confirm('{{ __('users.messages.confirm_delete') }}')">
                                    @csrf @method('DELETE')
                                    <button type="submit" title="{{ __('users.actions.delete') }}"
                                            class="p-2 text-red-600 hover:text-red-700">
                                        <x-icon name="trash" :size="16" />
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-0">
                            <x-empty-state icon="👥"
                                           :title="__('users.empty.title')"
                                           :description="__('users.empty.description')" />
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <x-slot name="footer">
                {{ $users->links() }}
            </x-slot>
        @endif
    </x-table-wrapper>
</div>
@endsection