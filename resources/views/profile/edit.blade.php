@extends('layouts.app')

@section('title', __('profile.edit.title'))

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <x-page-header :title="__('profile.edit.title')" :subtitle="__('profile.edit.subtitle')" />

    <x-card :title="__('profile.edit.account_info')" icon="user">
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-5">
            @csrf
            @method('PATCH')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    {{ __('profile.fields.name') }}
                </label>
                <input id="name" name="name" type="text"
                       value="{{ old('name', $user->name) }}"
                       required autocomplete="name"
                       class="input @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    {{ __('profile.fields.email') }}
                </label>
                <input id="email" name="email" type="email"
                       value="{{ old('email', $user->email) }}"
                       required autocomplete="email"
                       class="input @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3 pt-2">
                <x-button type="submit" variant="primary">
                    {{ __('profile.actions.save') }}
                </x-button>
                <x-button variant="secondary" :href="route('profile.password')">
                    {{ __('profile.edit.change_password_cta') }}
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection