@extends('layouts.app')
@section('title', __('dashboard.title'))

@section('content')
    @php $role = $role ?? (auth()->user()?->role?->value ?? 'guest'); @endphp

    {{-- Welcome --}}
    <div class="mb-5">
        <h1 class="text-xl md:text-2xl font-bold" style="color: var(--text)">
            {{ __('dashboard.welcome') }}, {{ auth()->user()?->name ?? '' }}
        </h1>
        <p class="text-sm text-muted mt-1">{{ __('dashboard.subtitle') }}</p>
    </div>

    {{-- ── Stat Cards (role-aware) ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 animate-fade-in-up">

        @if($role === 'admin')
            <x-stat-card :label="__('dashboard.stats.users')" :value="number_format($stats['users_count'])"
                         icon-name="users" color="brand" :href="route('admin.users.index')" />
            <x-stat-card :label="__('dashboard.stats.students')" :value="number_format($stats['students_count'])"
                         icon-name="graduation-cap" color="info" :href="route('registrar.students.index')" />
            <x-stat-card :label="__('dashboard.stats.unpaid_invoices')" :value="number_format($stats['unpaid_invoices'])"
                         icon-name="receipt" color="warning" :href="route('finance.invoices.index')" />
            <x-stat-card :label="__('dashboard.stats.payments_total')" :value="number_format($stats['payments_total'], 2)"
                         icon-name="wallet" color="success" :href="route('finance.payments.index')" />
        @elseif($role === 'registrar')
            <x-stat-card :label="__('dashboard.stats.students')" :value="number_format($stats['students_count'])"
                         icon-name="graduation-cap" color="info" :href="route('registrar.students.index')" />
            <x-stat-card :label="__('dashboard.stats.active_students')" :value="number_format($stats['active_students'])"
                         icon-name="graduation-cap" color="success" :href="route('registrar.students.index')" />
            <x-stat-card :label="__('dashboard.stats.programs')" :value="number_format($stats['programs_count'])"
                         icon-name="library" color="brand" :href="route('registrar.programs.index')" />
            <x-stat-card :label="__('dashboard.stats.registrations')" :value="number_format($stats['registrations_count'])"
                         icon-name="clipboard" color="warning" :href="route('registrar.registrations.index')" />
        @elseif($role === 'finance')
            <x-stat-card :label="__('dashboard.stats.unpaid_invoices')" :value="number_format($stats['unpaid_invoices'])"
                         icon-name="receipt" color="warning" :href="route('finance.invoices.index')" />
            <x-stat-card :label="__('dashboard.stats.paid_invoices')" :value="number_format($stats['paid_invoices'])"
                         icon-name="receipt" color="success" :href="route('finance.invoices.index')" />
            <x-stat-card :label="__('dashboard.stats.payments_total')" :value="number_format($stats['payments_total'], 2)"
                         icon-name="wallet" color="brand" :href="route('finance.payments.index')" />
            <x-stat-card :label="__('dashboard.stats.invoices_total')" :value="number_format($stats['invoices_total'], 2)"
                         icon-name="trending-up" color="info" :href="route('finance.invoices.index')" />
        @elseif($role === 'results')
            <x-stat-card :label="__('dashboard.stats.students')" :value="number_format($stats['students_count'])"
                         icon-name="graduation-cap" color="info" :href="route('results.results.index')" />
            <x-stat-card :label="__('dashboard.stats.courses')" :value="number_format($stats['courses_count'])"
                         icon-name="book" color="brand" :href="route('results.results.index')" />
            <x-stat-card :label="__('dashboard.stats.registrations')" :value="number_format($stats['registrations_count'])"
                         icon-name="clipboard" color="success" :href="route('results.results.index')" />
            <x-stat-card :label="__('dashboard.stats.programs')" :value="number_format($stats['programs_count'])"
                         icon-name="library" color="warning" :href="route('results.results.index')" />
        @endif
    </div>

    {{-- ── Charts (admin only) ── --}}
    @if($role === 'admin' && !empty($chartData))
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-6 animate-fade-in-up">
            <x-card :title="__('dashboard.charts.registrations')">
                <div class="p-2" style="height: 260px;">
                    <canvas id="chartRegistrations"
                            data-months="{{ json_encode($chartData['months'] ?? []) }}"
                            data-values="{{ json_encode($chartData['registrations'] ?? []) }}"
                            data-label="{{ __('dashboard.charts.registrations') }}"></canvas>
                </div>
            </x-card>
            <x-card :title="__('dashboard.charts.payments')">
                <div class="p-2" style="height: 260px;">
                    <canvas id="chartPayments"
                            data-months="{{ json_encode($chartData['months'] ?? []) }}"
                            data-values="{{ json_encode($chartData['payments'] ?? []) }}"
                            data-label="{{ __('dashboard.charts.payments') }}"></canvas>
                </div>
            </x-card>
        </div>
    @endif

    {{-- ── Quick actions (all roles, role-aware) ── --}}
    <div class="mt-6">
        <x-card :title="__('dashboard.quick_actions')">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                @if(in_array($role, ['admin','registrar']))
                    <a href="{{ route('registrar.students.create') }}" class="btn btn-primary">
                        <x-icon name="plus" :size="16" /> {{ __('navigation.students') }}
                    </a>
                    <a href="{{ route('registrar.registrations.create') }}" class="btn btn-secondary">
                        <x-icon name="clipboard" :size="16" /> {{ __('navigation.registrations') }}
                    </a>
                @endif
                @if(in_array($role, ['admin','finance']))
                    <a href="{{ route('finance.invoices.create') }}" class="btn btn-secondary">
                        <x-icon name="receipt" :size="16" /> {{ __('navigation.invoices') }}
                    </a>
                @endif
                @if(in_array($role, ['admin','registrar','finance','results']))
                    <a href="{{ route('reports.index') }}" class="btn btn-secondary">
                        <x-icon name="chart-bar" :size="16" /> {{ __('navigation.reports') }}
                    </a>
                @endif
            </div>
        </x-card>
    </div>

    {{-- ── Recent activity (admin/registrar) ── --}}
    @if(in_array($role, ['admin','registrar']))
        <div class="mt-4">
            <x-card :title="__('dashboard.recent_activity')">
                @if($recentStudents->isEmpty())
                    <x-empty-state icon="users" :message="__('dashboard.no_activity')" />
                @else
                    <ul class="divide-y divide-[var(--border)] -mx-1">
                        @foreach($recentStudents as $s)
                            <li class="py-2.5 px-1 flex items-center gap-3">
                                <div class="avatar-circle avatar-sm">
                                    {{ mb_strtoupper(mb_substr($s->user->name ?? 'S', 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold truncate">{{ $s->user->name ?? ('#' . $s->id) }}</p>
                                    <p class="text-xs text-muted flex items-center gap-1">
                                        <x-icon name="clock" :size="11" />
                                        {{ $s->created_at?->diffForHumans() ?? '' }}
                                    </p>
                                </div>
                                <a href="{{ route('registrar.students.show', $s) }}" class="icon-btn" title="{{ __('common.edit') }}">
                                    <x-icon name="eye" :size="16" />
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </x-card>
        </div>
    @endif
@endsection

