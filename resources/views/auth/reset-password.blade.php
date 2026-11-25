@extends('layouts.app')

@section('title', __('password.reset.title'))

@section('content')
<div class="max-w-md mx-auto mt-8">
    <x-card :title="__('password.reset.title')" icon="lock">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-5">
            {{ __('password.reset.instructions') }}
        </p>

        <form method="POST" action="{{ route('password.update') }}" class="space-y-5">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    {{ __('password.fields.email') }}
                </label>
                <input id="email" name="email" type="email"
                       value="{{ old('email', $email) }}"
                       required autocomplete="email"
                       class="input @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    {{ __('password.fields.new_password') }}
                </label>
                <input id="password" name="password" type="password"
                       required autocomplete="new-password"
                       class="input @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    {{ __('password.fields.confirm_password') }}
                </label>
                <input id="password_confirmation" name="password_confirmation" type="password"
                       required autocomplete="new-password"
                       class="input">
            </div>

            <div class="flex items-center gap-3">
                <x-button type="submit" variant="primary">
                    {{ __('password.reset.submit') }}
                </x-button>
                <x-button variant="secondary" :href="route('login')">
                    {{ __('password.reset.back') }}
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection