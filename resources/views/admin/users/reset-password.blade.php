@extends('layouts.app')

@section('title', __('users.reset_password.title'))

@section('content')
<div class="max-w-xl mx-auto space-y-6">
    <x-page-header :title="__('users.reset_password.title')" :subtitle="$user->name">
        <x-button variant="secondary" :href="route('admin.users.index')">{{ __('users.actions.back') }}</x-button>
    </x-page-header>

    <x-card :title="__('users.reset_password.title')" padding="p-6">
        <div class="mb-5 p-4 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800">
            <p class="text-sm text-amber-800 dark:text-amber-200">
                <x-icon name="alert-circle" :size="16" class="inline mb-0.5" />
                {{ __('users.reset_password.warning') }}
            </p>
        </div>

        <form method="POST" action="{{ route('admin.users.reset-password', $user) }}"
              class="space-y-5"
              onsubmit="return confirm('{{ __('users.reset_password.confirm') }}')">
            @csrf

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('users.fields.password') }}</label>
                <input id="password" name="password" type="password" required minlength="8"
                       class="input w-full @error('password') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">{{ __('users.hints.password') }}</p>
                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('users.fields.confirm_password') }}</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8"
                       class="input w-full">
            </div>

            <x-slot name="footer">
                <div class="flex gap-3">
                    <x-button type="submit" variant="primary">
                        <x-icon name="lock" :size="16" class="inline" />
                        {{ __('users.actions.reset_password') }}
                    </x-button>
                    <x-button variant="secondary" :href="route('admin.users.index')">{{ __('users.actions.cancel') }}</x-button>
                </div>
            </x-slot>
        </form>
    </x-card>
</div>
@endsection