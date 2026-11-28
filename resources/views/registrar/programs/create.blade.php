@extends('layouts.app')

@section('title', __('programs.create_title'))

@section('content')
<div class="space-y-6">
    <x-page-header
        :title="__('programs.create_title')"
        :subtitle="__('programs.create_subtitle')"
    >
        <x-slot:actions>
            <x-button href="{{ route('registrar.programs.index') }}" variant="secondary">
                <x-icon name="arrow-left" class="h-4 w-4" />
                {{ __('programs.back') }}
            </x-button>
        </x-slot:actions>
    </x-page-header>

    @if($errors->any())
        <div class="rounded-xl border border-red-200 bg-red-50 p-4 text-red-700">
            <ul class="list-inside list-disc space-y-1 text-sm">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('registrar.programs.store') }}" class="space-y-6">
        @csrf

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h3 class="font-bold text-gray-900">{{ __('programs.program_information') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('programs.required_fields') }}</p>
            </div>

            <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
                <div class="space-y-2">
                    <label for="code" class="block text-sm font-semibold text-gray-700">
                        {{ __('programs.code') }} <span class="text-red-500">*</span>
                    </label>
                    <input id="code" name="code" value="{{ old('code') }}" required
                           class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                    <p class="text-xs text-gray-500">{{ __('programs.program_code_hint') }}</p>
                    @error('code') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="name" class="block text-sm font-semibold text-gray-700">
                        {{ __('programs.name') }} <span class="text-red-500">*</span>
                    </label>
                    <input id="name" name="name" value="{{ old('name') }}" required
                           class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                    @error('name') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="name_en" class="block text-sm font-semibold text-gray-700">{{ __('programs.name_en') }}</label>
                    <input id="name_en" name="name_en" value="{{ old('name_en') }}" dir="ltr"
                           class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                    @error('name_en') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h3 class="font-bold text-gray-900">{{ __('programs.academic_information') }}</h3>
            </div>

            <div class="grid grid-cols-1 gap-5 p-6 md:grid-cols-2">
                <div class="space-y-2">
                    <label for="degree_level" class="block text-sm font-semibold text-gray-700">
                        {{ __('programs.degree_level') }} <span class="text-red-500">*</span>
                    </label>
                    <select id="degree_level" name="degree_level" required
                            class="w-full rounded-xl border border-gray-300 bg-white px-4 py-3 text-sm shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                        <option value="">{{ __('programs.select_degree') }}</option>
                        @foreach(\App\Enums\DegreeLevel::cases() as $lvl)
                            <option value="{{ $lvl->value }}" @selected(old('degree_level') === $lvl->value)>
                                {{ __('programs.' . $lvl->value) }}
                            </option>
                        @endforeach
                    </select>
                    @error('degree_level') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <div class="space-y-2">
                    <label for="duration_years" class="block text-sm font-semibold text-gray-700">
                        {{ __('programs.duration') }} <span class="text-red-500">*</span>
                    </label>
                    <div class="flex items-center gap-2">
                        <input id="duration_years" name="duration_years" type="number" min="1" max="10"
                               value="{{ old('duration_years', 4) }}" required
                               class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">
                        <span class="whitespace-nowrap text-sm text-gray-500">{{ __('programs.years') }}</span>
                    </div>
                    <p class="text-xs text-gray-500">{{ __('programs.duration_hint') }}</p>
                    @error('duration_years') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
            <div class="border-b border-gray-100 px-6 py-4">
                <h3 class="font-bold text-gray-900">{{ __('programs.additional_information') }}</h3>
            </div>
            <div class="space-y-5 p-6">
                <div class="space-y-2">
                    <label for="description" class="block text-sm font-semibold text-gray-700">{{ __('programs.description') }}</label>
                    <textarea id="description" name="description" rows="4"
                              placeholder="{{ __('programs.description_placeholder') }}"
                              class="w-full rounded-xl border border-gray-300 px-4 py-3 text-sm shadow-sm focus:border-primary-500 focus:outline-none focus:ring-2 focus:ring-primary-100">{{ old('description') }}</textarea>
                    @error('description') <p class="text-xs text-red-600">{{ $message }}</p> @enderror
                </div>

                <label for="is_active" class="flex cursor-pointer items-center gap-3 rounded-xl border border-gray-200 p-4 transition hover:bg-gray-50">
                    <input type="checkbox" name="is_active" value="1" id="is_active"
                           @checked(old('is_active', true))
                           class="h-4 w-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    <span>
                        <span class="block text-sm font-semibold text-gray-800">{{ __('programs.active') }}</span>
                        <span class="mt-1 block text-xs text-gray-500">{{ __('programs.status') }}</span>
                    </span>
                </label>
            </div>
        </div>

        <div class="flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
            <x-button href="{{ route('registrar.programs.index') }}" variant="secondary">
                {{ __('programs.cancel') }}
            </x-button>
            <x-button type="submit">
                <x-icon name="save" class="h-4 w-4" />
                {{ __('programs.save') }}
            </x-button>
        </div>
    </form>
</div>
@endsection