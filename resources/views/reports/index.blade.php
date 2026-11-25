@extends('layouts.app')

@section('title', __('reports.title'))

@section('content')
<x-page-header :title="__('reports.title')" :subtitle="__('reports.subtitle')" />

@if(empty($visibleReports))
    {{-- لا توجد تقارير متاحة لهذا الدور --}}
    <x-card :padding="'p-8'" class="text-center">
        <div class="flex flex-col items-center gap-3">
            <div class="w-16 h-16 rounded-full bg-gray-100 text-gray-400 flex items-center justify-center">
                <x-icon name="chart-bar" :size="32" />
            </div>
            <h3 class="text-lg font-bold text-gray-700">{{ __('reports.no_reports_available') ?? 'لا توجد تقارير متاحة لدورك' }}</h3>
            <p class="text-sm text-gray-500">{{ __('reports.contact_admin') ?? 'يرجى التواصل مع مدير النظام' }}</p>
        </div>
    </x-card>
@else
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($visibleReports as $key => $report)
            <a href="{{ route($report['route']) }}" class="block group">
                <x-card :padding="'p-5'" class="h-full transition hover:shadow-lg hover:-translate-y-0.5">
                    <div class="flex items-start gap-4">
                        <div class="flex-shrink-0 w-12 h-12 rounded-full bg-{{ $report['color'] }}-100 text-{{ $report['color'] }}-700 flex items-center justify-center">
                            <x-icon :name="$report['icon']" :size="24" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="font-bold text-lg mb-1 group-hover:text-brand-700">{{ __($report['title']) }}</h3>
                            <p class="text-sm text-gray-500">{{ __($report['desc']) }}</p>
                        </div>
                    </div>
                </x-card>
            </a>
        @endforeach
    </div>
@endif
@endsection