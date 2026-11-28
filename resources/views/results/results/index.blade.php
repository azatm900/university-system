@extends('layouts.app')

@section('title', __('results_ui.title'))

@section('content')
<div class="space-y-6">

    <x-page-header
        :title="__('results_ui.title')"
        :subtitle="__('results_ui.subtitle')"
    >
        <x-button variant="primary" :href="route('results.results.create')">
            <x-icon name="plus" :size="16" />
            <span class="ms-1">{{ __('results_ui.create') }}</span>
        </x-button>
    </x-page-header>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <x-stat-card
            :label="__('results_ui.results_count')"
            :value="$results->total()"
            icon-name="clipboard-list"
        />
        <x-stat-card
            :label="__('results_ui.showing')"
            :value="$results->count()"
            icon-name="list-checks"
        />
    </div>

    <x-table-wrapper>
        <table class="table w-full text-sm">
            <thead>
                <tr>
                    <th class="whitespace-nowrap">#</th>
                    <th class="whitespace-nowrap">{{ __('results_ui.student') }}</th>
                    <th class="whitespace-nowrap">{{ __('results_ui.course') }}</th>
                    <th class="whitespace-nowrap">{{ __('results_ui.semester') }}</th>
                    <th class="whitespace-nowrap">{{ __('results_ui.total_score') }}</th>
                    <th class="whitespace-nowrap">{{ __('results_ui.grade') }}</th>
                    <th class="whitespace-nowrap">{{ __('results_ui.status') }}</th>
                    <th class="whitespace-nowrap">{{ __('results_ui.publication') }}</th>
                    <th class="whitespace-nowrap">{{ __('results_ui.actions') }}</th>
                </tr>
            </thead>

            <tbody>
                @forelse ($results as $r)
                    <tr>
                        <td class="whitespace-nowrap">{{ $r->id }}</td>

                        <td class="font-medium">
                            {{ $r->registration?->student?->full_name
                                ?? $r->registration?->student?->user?->name
                                ?? __('results_ui.not_available') }}
                        </td>

                        <td>
                            {{ $r->registration?->course?->name
                                ?? __('results_ui.not_available') }}
                        </td>

                        <td>
                            {{ $r->registration?->semester?->name
                                ?? __('results_ui.not_available') }}
                        </td>

                        <td class="font-semibold whitespace-nowrap">
                            {{ $r->total_score ?? '—' }}
                        </td>

                        <td class="font-semibold whitespace-nowrap">
                            {{ $r->grade ?? '—' }}
                        </td>

                        <td class="whitespace-nowrap">
                            @include('partials.enum-label', ['value' => $r->status])
                        </td>

                        <td class="whitespace-nowrap">
                            @if ($r->published_at)
                                <x-badge color="success">
                                    {{ __('results_ui.published') }}
                                </x-badge>
                            @else
                                <x-badge color="warning">
                                    {{ __('results_ui.unpublished') }}
                                </x-badge>
                            @endif
                        </td>

                        <td>
                            <div class="flex items-center gap-2 flex-wrap">

                                <x-button
                                    variant="secondary"
                                    size="sm"
                                    :href="route('results.results.show', $r)"
                                >
                                    {{ __('results_ui.view') }}
                                </x-button>

                                @if (!$r->published_at)
                                    <x-button
                                        variant="secondary"
                                        size="sm"
                                        :href="route('results.results.edit', $r)"
                                    >
                                        {{ __('results_ui.edit') }}
                                    </x-button>
                                @endif

                                @if (!$r->published_at && $r->status !== 'incomplete')
                                    <form
                                        action="{{ route('results.results.publish', $r) }}"
                                        method="POST"
                                        class="inline"
                                    >
                                        @csrf
                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-primary"
                                        >
                                            {{ __('results_ui.publish') }}
                                        </button>
                                    </form>
                                @endif

                                @if ($r->published_at)
                                    <form
                                        action="{{ route('results.results.unpublish', $r) }}"
                                        method="POST"
                                        class="inline"
                                    >
                                        @csrf
                                        <button
                                            type="submit"
                                            class="btn btn-sm btn-secondary"
                                        >
                                            {{ __('results_ui.unpublish') }}
                                        </button>
                                    </form>
                                @endif

                                @if (!$r->published_at)
                                    <form
                                        action="{{ route('results.results.destroy', $r) }}"
                                        method="POST"
                                        class="inline"
                                        onsubmit="return confirm(@js(__('results_ui.confirm_delete')))"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="text-red-600 hover:underline text-sm font-medium"
                                        >
                                            {{ __('results_ui.delete') }}
                                        </button>
                                    </form>
                                @endif

                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="py-10">
                            <x-empty-state
                                icon-name="clipboard-list"
                                :title="__('results_ui.title')"
                                :message="__('results_ui.empty')"
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <x-slot name="footer">
            {{ $results->links() }}
        </x-slot>
    </x-table-wrapper>

</div>
@endsection