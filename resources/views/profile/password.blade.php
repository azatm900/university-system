@extends('layouts.app')

@section('title', __('profile.password.title'))

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <x-page-header :title="__('profile.password.title')" :subtitle="__('profile.password.subtitle')" />

    <x-card :title="__('profile.password.form_title')" icon="lock">
        <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    {{ __('profile.fields.current_password') }}
                </label>
                <input id="current_password" name="current_password" type="password"
                       required autocomplete="current-password"
                       placeholder="{{ __('profile.placeholders.current_password') }}"
                       class="input @error('current_password') border-red-500 @enderror">
                @error('current_password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    {{ __('profile.fields.new_password') }}
                </label>
                <input id="password" name="password" type="password"
                       required autocomplete="new-password"
                       placeholder="{{ __('profile.placeholders.new_password') }}"
                       class="input @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    {{ __('profile.fields.confirm_password') }}
                </label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                       required autocomplete="new-password"
                       placeholder="{{ __('profile.placeholders.confirm_password') }}"
                       class="input">
            </div>

            <div class="flex items-center gap-3 pt-2">
                <x-button type="submit" variant="primary">
                    {{ __('profile.actions.update_password') }}
                </x-button>
                <x-button variant="secondary" :href="route('profile.edit')">
                    {{ __('profile.actions.cancel') }}
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection