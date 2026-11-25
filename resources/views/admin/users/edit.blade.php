@extends('layouts.app')

@section('title', __('users.edit.title'))

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <x-page-header :title="__('users.edit.title')" :subtitle="$user->name">
        <x-button variant="secondary" :href="route('admin.users.index')">{{ __('users.actions.back') }}</x-button>
        <x-button variant="secondary" :href="route('admin.users.show', $user)">{{ __('users.actions.view') }}</x-button>
    </x-page-header>

    <x-card :title="__('users.edit.title')" padding="p-6">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-5">
            @csrf @method('PATCH')

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('users.fields.name') }}</label>
                <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required
                       class="input w-full @error('name') border-red-500 @enderror">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('users.fields.email') }}</label>
                <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required
                       class="input w-full @error('email') border-red-500 @enderror">
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('users.fields.password_optional') }}</label>
                <input id="password" name="password" type="password" minlength="8"
                       class="input w-full @error('password') border-red-500 @enderror">
                <p class="mt-1 text-xs text-gray-500">{{ __('users.hints.password_optional') }}</p>
                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="role" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">{{ __('users.fields.role') }}</label>
                <select id="role" name="role" required class="input w-full @error('role') border-red-500 @enderror">
                    @foreach (\App\Enums\UserRole::cases() as $r)
                        <option value="{{ $r->value }}" @selected(old('role', $user->role?->value) === $r->value)>{{ $r->label() }}</option>
                    @endforeach
                </select>
                @error('role')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user->is_active)) class="w-4 h-4 rounded">
                    <span class="text-sm text-gray-700 dark:text-gray-300">{{ __('users.fields.is_active') }}</span>
                </label>
            </div>

            <x-slot name="footer">
                <div class="flex gap-3">
                    <x-button type="submit" variant="primary">{{ __('users.actions.save') }}</x-button>
                    <x-button variant="secondary" :href="route('admin.users.index')">{{ __('users.actions.cancel') }}</x-button>
                </div>
            </x-slot>
        </form>
    </x-card>
</div>
@endsection