@extends('layouts.app')

@section('title', __('password.request.title'))

@section('content')
<div class="max-w-md mx-auto mt-8">
    <x-card :title="__('password.request.title')" icon="lock">
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-5">
            {{ __('password.request.instructions') }}
        </p>

        @if (session('status'))
            <div class="mb-4 px-4 py-3 rounded-md bg-green-50 dark:bg-green-900/30 text-green-800 dark:text-green-200 text-sm">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
                    {{ __('password.fields.email') }}
                </label>
                <input id="email" name="email" type="email"
                       value="{{ old('email') }}"
                       required autocomplete="email" autofocus
                       class="input @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <div class="flex items-center gap-3">
                <x-button type="submit" variant="primary">
                    {{ __('password.request.submit') }}
                </x-button>
                <x-button variant="secondary" :href="route('login')">
                    {{ __('password.request.back') }}
                </x-button>
            </div>
        </form>
    </x-card>
</div>
@endsection