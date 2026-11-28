@extends('layouts.app')

@section('title', __('results_ui.show_title'))

@section('content')
<div class="space-y-6">

    <x-page-header
        :title="__('results_ui.show_title') . ' #' . $result->id"
        :subtitle="__('results_ui.subtitle')"
    >
        <x-button variant="secondary" :href="route('results.results.index')">
            <x-icon name="arrow-right" :size="16" />
            <span class="ms-1">{{ __('results_ui.back') }}</span>
        </x-button>
    </x-page-header>

    <div class="max-w-4xl mx-auto space-y-5">

        <x-card :title="__('results_ui.publication')" icon="globe">
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div>
                    <p class="text-sm text-muted">{{ __('results_ui.publication') }}</p>
                    <div class="mt-2">
                        @if ($result->published_at)
                            <x-badge color="success">{{ __('results_ui.published') }}</x-badge>
                        @else
                            <x-badge color="warning">{{ __('results_ui.unpublished') }}</x-badge>
                        @endif
                    </div>
                </div>

                @if ($result->published_at)
                    <div class="text-sm">
                        <p class="text-muted">{{ __('results_ui.publication_date') }}</p>
                        <p class="font-semibold mt-1">{{ $result->published_at }}</p>
                    </div>
                @endif
            </div>
        </x-card>

        <x-card :title="__('results_ui.registration')" icon="user-round">
            <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">

                <div>
                    <dt class="text-sm text-muted">{{ __('results_ui.student') }}</dt>
                    <dd class="font-semibold mt-1">
                        {{ $result->registration?->student?->full_name
                            ?? $result->registration?->student?->user?->name
                            ?? __('results_ui.not_available') }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-muted">{{ __('results_ui.student_number') }}</dt>
                    <dd class="font-semibold mt-1">
                        {{ $result->registration?->student?->student_number ?? '—' }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-muted">{{ __('results_ui.course') }}</dt>
                    <dd class="font-semibold mt-1">
                        {{ $result->registration?->course?->name ?? __('results_ui.not_available') }}
                    </dd>
                </div>

                <div>
                    <dt class="text-sm text-muted">{{ __('results_ui.semester') }}</dt>
                    <dd class="font-semibold mt-1">
                        {{ $result->registration?->semester?->name ?? __('results_ui.not_available') }}
                    </dd>
                </div>

            </dl>
        </x-card>

        <x-card :title="__('results_ui.current_summary')" icon="clipboard-list">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                <div class="rounded-lg border border-default p-4">
                    <p class="text-sm text-muted">{{ __('results_ui.coursework') }}</p>
                    <p class="text-xl font-bold mt-2">{{ $result->coursework_score ?? '—' }}</p>
                </div>

                <div class="rounded-lg border border-default p-4">
                    <p class="text-sm text-muted">{{ __('results_ui.midterm') }}</p>
                    <p class="text-xl font-bold mt-2">{{ $result->midterm_score ?? '—' }}</p>
                </div>

                <div class="rounded-lg border border-default p-4">
                    <p class="text-sm text-muted">{{ __('results_ui.final') }}</p>
                    <p class="text-xl font-bold mt-2">{{ $result->final_score ?? '—' }}</p>
                </div>

                <div class="rounded-lg border border-default p-4">
                    <p class="text-sm text-muted">{{ __('results_ui.total_score') }}</p>
                    <p class="text-xl font-bold mt-2">{{ $result->total_score ?? '—' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 border-t border-default pt-5">
                <div>
                    <p class="text-sm text-muted">{{ __('results_ui.grade') }}</p>
                    <p class="text-lg font-bold mt-1">{{ $result->grade ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-sm text-muted">{{ __('results_ui.grade_points') }}</p>
                    <p class="text-lg font-bold mt-1">{{ $result->grade_points ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-sm text-muted">{{ __('results_ui.status') }}</p>
                    <div class="mt-2">
                        @include('partials.enum-label', ['value' => $result->status])
                    </div>
                </div>
            </div>
        </x-card>

        <div class="flex flex-wrap gap-2">
            @if (!$result->published_at)
                <x-button variant="primary" :href="route('results.results.edit', $result)">
                    <x-icon name="pencil" :size="16" />
                    <span class="ms-1">{{ __('results_ui.edit') }}</span>
                </x-button>

                @if ($result->status !== 'incomplete')
                    <form
                        action="{{ route('results.results.publish', $result) }}"
                        method="POST"
                        onsubmit="return confirm(@js(__('results_ui.confirm_publish')))"
                    >
                        @csrf
                        <x-button type="submit" variant="secondary">
                            {{ __('results_ui.publish') }}
                        </x-button>
                    </form>
                @endif
            @else
                <form
                    action="{{ route('results.results.unpublish', $result) }}"
                    method="POST"
                    onsubmit="return confirm(@js(__('results_ui.confirm_unpublish')))"
                >
                    @csrf
                    <x-button type="submit" variant="secondary">
                        {{ __('results_ui.unpublish') }}
                    </x-button>
                </form>
            @endif

            <x-button variant="secondary" :href="route('results.results.index')">
                {{ __('results_ui.back') }}
            </x-button>
        </div>

    </div>
</div>
@endsection