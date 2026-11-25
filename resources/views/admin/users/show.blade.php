@extends('layouts.app')

@section('title', __('users.show.title'))

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <x-page-header :title="__('users.show.title')" :subtitle="$user->name">
        <x-button variant="secondary" :href="route('admin.users.index')">{{ __('users.actions.back') }}</x-button>
        <x-button variant="primary" :href="route('admin.users.edit', $user)">{{ __('users.actions.edit') }}</x-button>
    </x-page-header>

    <x-card :title="__('users.show.title')" padding="p-6">
        <dl class="grid grid-cols-1 sm:grid-cols-2 gap-5">
            <div>
                <dt class="text-xs text-gray-500 mb-1">{{ __('users.fields.name') }}</dt>
                <dd class="font-medium">{{ $user->name }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 mb-1">{{ __('users.fields.email') }}</dt>
                <dd class="text-gray-700 dark:text-gray-300">{{ $user->email }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 mb-1">{{ __('users.fields.role') }}</dt>
                <dd>
                    @php
                        $roleColor = match($user->role?->value) {
                            'admin'     => 'danger',
                            'finance'   => 'warning',
                            'registrar' => 'info',
                            'results'   => 'success',
                            'student'   => 'info',
                            default     => 'info',
                        };
                    @endphp
                    <x-badge :color="$roleColor">{{ $user->role?->label() ?? '—' }}</x-badge>
                </dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 mb-1">{{ __('users.fields.status') }}</dt>
                <dd>
                    @if ($user->is_active)
                        <x-badge color="success">{{ __('users.status.active') }}</x-badge>
                    @else
                        <x-badge color="danger">{{ __('users.status.inactive') }}</x-badge>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 mb-1">{{ __('users.fields.created_at') }}</dt>
                <dd class="text-sm">{{ $user->created_at?->format('Y-m-d H:i') }}</dd>
            </div>
            <div>
                <dt class="text-xs text-gray-500 mb-1">{{ __('users.fields.updated_at') }}</dt>
                <dd class="text-sm">{{ $user->updated_at?->format('Y-m-d H:i') }}</dd>
            </div>
        </dl>

        <x-slot name="footer">
            <div class="flex flex-wrap gap-2">
                <x-button variant="secondary" :href="route('admin.users.reset-password-form', $user)">
                    <x-icon name="lock" :size="16" class="inline" />
                    {{ __('users.actions.reset_password') }}
                </x-button>
                <form action="{{ route('admin.users.toggle', $user) }}" method="POST" class="inline">
                    @csrf @method('PATCH')
                    <x-button type="submit" variant="secondary">
                        {{ $user->is_active ? __('users.actions.deactivate') : __('users.actions.activate') }}
                    </x-button>
                </form>
            </div>
        </x-slot>
    </x-card>
</div>
@endsection