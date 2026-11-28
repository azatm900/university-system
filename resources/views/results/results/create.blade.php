@extends('layouts.app')

@section('title', __('results_ui.create_title'))

@section('content')
<div class="space-y-6">

    <x-page-header
        :title="__('results_ui.create_title')"
        :subtitle="__('results_ui.create_subtitle')"
    >
        <x-button variant="secondary" :href="route('results.results.index')">
            <x-icon name="arrow-right" :size="16" />
            <span class="ms-1">{{ __('results_ui.back') }}</span>
        </x-button>
    </x-page-header>

    <div class="max-w-4xl mx-auto">
        <x-card :title="__('results_ui.registration')" icon="user-round">
            <form method="POST" action="{{ route('results.results.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label for="registration_id" class="block text-sm font-semibold mb-2">
                        {{ __('results_ui.registration') }}
                    </label>

                    <select
                        id="registration_id"
                        name="registration_id"
                        required
                        class="input-field w-full"
                    >
                        <option value="">{{ __('results_ui.select_registration') }}</option>

                        @foreach (($registrations ?? \App\Models\Registration::doesntHave('result')->with(['student.user','course','semester'])->get()) as $reg)
                            <option
                                value="{{ $reg->id }}"
                                @selected((string) old('registration_id') === (string) $reg->id)
                            >
                                {{ $reg->student?->student_number }}
                                — {{ $reg->student?->full_name ?? $reg->student?->user?->name }}
                                / {{ $reg->course?->name }}
                                / {{ $reg->semester?->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('registration_id')
                        <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t border-default pt-5">
                    <h3 class="font-semibold text-base mb-1">{{ __('results_ui.total_score') }}</h3>
                    <p class="text-sm text-muted mb-4">{{ __('results_ui.score_hint') }}</p>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                        <div>
                            <label for="coursework_score" class="block text-sm font-semibold mb-2">
                                {{ __('results_ui.coursework') }}
                            </label>
                            <input
                                id="coursework_score"
                                name="coursework_score"
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                value="{{ old('coursework_score') }}"
                                class="input-field w-full"
                            >
                            @error('coursework_score')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="midterm_score" class="block text-sm font-semibold mb-2">
                                {{ __('results_ui.midterm') }}
                            </label>
                            <input
                                id="midterm_score"
                                name="midterm_score"
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                value="{{ old('midterm_score') }}"
                                class="input-field w-full"
                            >
                            @error('midterm_score')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="final_score" class="block text-sm font-semibold mb-2">
                                {{ __('results_ui.final') }}
                            </label>
                            <input
                                id="final_score"
                                name="final_score"
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                value="{{ old('final_score') }}"
                                class="input-field w-full"
                            >
                            @error('final_score')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>
                </div>

                @if ($errors->any())
                    <div class="rounded-lg border border-red-200 p-4 text-sm text-red-700">
                        <p class="font-semibold mb-2">{{ __('validation.attributes.score') ?? 'يرجى مراجعة البيانات المدخلة' }}</p>
                        <ul class="list-disc ps-5 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="flex flex-wrap gap-2 pt-4 border-t border-default">
                    <x-button type="submit" variant="primary">
                        <x-icon name="save" :size="16" />
                        <span class="ms-1">{{ __('results_ui.save') }}</span>
                    </x-button>

                    <x-button variant="secondary" :href="route('results.results.index')">
                        {{ __('results_ui.cancel') }}
                    </x-button>
                </div>
            </form>
        </x-card>
    </div>
</div>
@endsection