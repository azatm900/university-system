@extends('layouts.app')
@section('title', __('dashboard.title'))

@section('content')
    @php
        $role = $role ?? (auth()->user()?->role?->value ?? 'guest');
        $sd = $studentDashboard ?? null;
        $student = $sd['student'] ?? null;

        $degreeLevel = $student?->program?->degree_level;
        $degreeLevel = $degreeLevel instanceof \BackedEnum ? $degreeLevel->value : (string) $degreeLevel;

        $degreeLabel = match ($degreeLevel) {
            'diploma' => app()->getLocale() === 'ar' ? 'دبلوم' : 'Diploma',
            'bachelor' => app()->getLocale() === 'ar' ? 'بكالوريوس' : 'Bachelor',
            'master' => app()->getLocale() === 'ar' ? 'ماجستير' : 'Master',
            'phd' => app()->getLocale() === 'ar' ? 'دكتوراه' : 'PhD',
            default => __('student_dashboard.not_available'),
        };

        $studentStatus = $student?->status;
        $studentStatus = $studentStatus instanceof \BackedEnum ? $studentStatus->value : (string) $studentStatus;
    @endphp

    {{-- Welcome --}}
    <div class="mb-5">
        <h1 class="text-xl md:text-2xl font-bold" style="color: var(--text)">
            {{ __('dashboard.welcome') }}, {{ auth()->user()?->name ?? '' }}
        </h1>
        <p class="text-sm text-muted mt-1">{{ __('dashboard.subtitle') }}</p>
    </div>

    {{-- Student dashboard --}}
    @if($role === 'student')
        @if($student && $sd)
            {{-- Student profile --}}
            <div class="mb-6 animate-fade-in-up">
                <x-card :title="__('student_dashboard.profile')">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-muted">{{ __('student_dashboard.full_name') }}</p>
                            <p class="text-sm font-semibold mt-1">{{ $student->full_name ?: $student->user?->name ?? auth()->user()?->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted">{{ __('student_dashboard.system_number') }}</p>
                            <p class="text-sm font-semibold mt-1">{{ $student->student_number ?: __('student_dashboard.not_available') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted">{{ __('student_dashboard.email') }}</p>
                            <p class="text-sm font-semibold mt-1 break-all">{{ auth()->user()?->email ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted">{{ __('student_dashboard.program') }}</p>
                            <p class="text-sm font-semibold mt-1">{{ $student->program?->name ?? __('student_dashboard.not_available') }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted">{{ __('student_dashboard.degree_level') }}</p>
                            <p class="text-sm font-semibold mt-1">{{ $degreeLabel }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-muted">{{ __('student_dashboard.level') }}</p>
                            <p class="text-sm font-semibold mt-1">
                                {{ $student->level ? __('student_dashboard.level') . ' ' . $student->level : __('student_dashboard.not_available') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-muted">{{ __('student_dashboard.enrollment_date') }}</p>
                            <p class="text-sm font-semibold mt-1">
                                {{ $student->enrollment_date?->translatedFormat('d M Y') ?? __('student_dashboard.not_recorded') }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-muted">{{ __('student_dashboard.registration_status') }}</p>
                            <p class="text-sm font-semibold mt-1">
                                {{ __('student_dashboard.student_statuses.' . $studentStatus) }}
                            </p>
                        </div>
                    </div>
                </x-card>
            </div>

            {{-- Registration details --}}
            <div class="mb-6 animate-fade-in-up">
                <x-card :title="__('student_dashboard.registrations_title')">
                    @if($sd['registration_groups']->isEmpty())
                        <x-empty-state icon="clipboard" :message="__('student_dashboard.no_registrations')" />
                    @else
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            @foreach($sd['registration_groups'] as $group)
                                @php
                                    $semester = $group['semester'];
                                    $registrationStatus = $group['status'];
                                @endphp
                                <div class="rounded-xl border p-4" style="border-color: var(--border)">
                                    <div class="flex items-start justify-between gap-3 mb-4">
                                        <div>
                                            <h3 class="font-semibold text-sm">
                                                {{ $semester?->name ?? __('student_dashboard.semester') }}
                                            </h3>
                                            <p class="text-xs text-muted mt-1">
                                                @if($semester?->start_date || $semester?->end_date)
                                                    {{ $semester?->start_date?->format('Y-m-d') ?? '—' }}
                                                    –
                                                    {{ $semester?->end_date?->format('Y-m-d') ?? '—' }}
                                                @else
                                                    {{ __('student_dashboard.not_recorded') }}
                                                @endif
                                            </p>
                                        </div>
                                        <span class="text-xs font-semibold rounded-full px-3 py-1"
                                              style="background: var(--surface); color: var(--text)">
                                            {{ __('student_dashboard.registration_statuses.' . $registrationStatus) }}
                                        </span>
                                    </div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        <div>
                                            <p class="text-xs text-muted">{{ __('student_dashboard.registration_date') }}</p>
                                            <p class="text-sm font-medium mt-1">
                                                {{ $group['registration_date']?->format('Y-m-d') ?? __('student_dashboard.not_recorded') }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-muted">{{ __('student_dashboard.registration_status') }}</p>
                                            <p class="text-sm font-medium mt-1">
                                                {{ __('student_dashboard.registration_statuses.' . $registrationStatus) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-card>
            </div>

            {{-- Financial details --}}
            <div class="mb-6 animate-fade-in-up">
                <x-card :title="__('student_dashboard.finance_title')">
                    @if($sd['finance_groups']->isEmpty() && !$sd['has_unlinked_invoices'])
                        <x-empty-state icon="wallet" :message="__('student_dashboard.no_invoices')" />
                    @else
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            @foreach($sd['finance_groups'] as $finance)
                                <div class="rounded-xl border p-4" style="border-color: var(--border)">
                                    <h3 class="font-semibold text-sm mb-4">
                                        {{ $finance['semester']?->name ?? __('student_dashboard.semester') }}
                                    </h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <p class="text-xs text-muted">{{ __('student_dashboard.total_fees') }}</p>
                                            <p class="text-sm font-semibold mt-1">
                                                {{ number_format($finance['total'], 2) }} {{ $sd['currency'] }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-muted">{{ __('student_dashboard.paid_amount') }}</p>
                                            <p class="text-sm font-semibold mt-1">
                                                {{ number_format($finance['paid'], 2) }} {{ $sd['currency'] }}
                                            </p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-muted">{{ __('student_dashboard.remaining_amount') }}</p>
                                            <p class="text-sm font-semibold mt-1">
                                                {{ number_format($finance['remaining'], 2) }} {{ $sd['currency'] }}
                                            </p>
                                        </div>
                                    </div>
                                    <p class="text-xs mt-3 font-medium" style="color: var(--text)">
                                        {{ $finance['remaining'] > 0
                                            ? (app()->getLocale() === 'ar' ? 'توجد مبالغ مستحقة' : 'Outstanding balance')
                                            : (app()->getLocale() === 'ar' ? 'لا توجد مبالغ مستحقة' : 'No outstanding balance') }}
                                    </p>
                                </div>
                            @endforeach

                            @if($sd['has_unlinked_invoices'])
                                <div class="rounded-xl border p-4" style="border-color: var(--border)">
                                    <h3 class="font-semibold text-sm mb-4">{{ __('student_dashboard.unlinked_fees') }}</h3>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                        <div>
                                            <p class="text-xs text-muted">{{ __('student_dashboard.total_fees') }}</p>
                                            <p class="text-sm font-semibold mt-1">{{ number_format($sd['unlinked_finance']['total'], 2) }} {{ $sd['currency'] }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-muted">{{ __('student_dashboard.paid_amount') }}</p>
                                            <p class="text-sm font-semibold mt-1">{{ number_format($sd['unlinked_finance']['paid'], 2) }} {{ $sd['currency'] }}</p>
                                        </div>
                                        <div>
                                            <p class="text-xs text-muted">{{ __('student_dashboard.remaining_amount') }}</p>
                                            <p class="text-sm font-semibold mt-1">{{ number_format($sd['unlinked_finance']['remaining'], 2) }} {{ $sd['currency'] }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </x-card>
            </div>

            {{-- Published academic results --}}
            <div class="mb-6 animate-fade-in-up">
                <x-card :title="__('student_dashboard.results_title')">
                    @if($sd['result_groups']->isEmpty())
                        <x-empty-state icon="book-open" :message="__('student_dashboard.no_published_results')" />
                    @else
                        <div class="mb-4 grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div class="rounded-xl border p-4" style="border-color: var(--border)">
                                <p class="text-xs text-muted">{{ __('student_dashboard.cumulative_gpa') }}</p>
                                <p class="text-xl font-bold mt-1" style="color: var(--text)">
                                    {{ $sd['cumulative_gpa'] !== null ? number_format($sd['cumulative_gpa'], 2) . ' / ' . number_format($sd['gpa_max'], 1) : __('student_dashboard.not_available') }}
                                </p>
                            </div>
                            <div class="rounded-xl border p-4" style="border-color: var(--border)">
                                <p class="text-xs text-muted">{{ __('student_dashboard.overall_grade') }}</p>
                                <p class="text-sm font-semibold mt-2">{{ __('student_dashboard.not_available') }}</p>
                            </div>
                        </div>

                        <div class="space-y-5">
                            @foreach($sd['result_groups'] as $resultGroup)
                                <div class="rounded-xl border p-3 sm:p-4" style="border-color: var(--border)">
                                    <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
                                        <h3 class="font-semibold text-sm">
                                            {{ $resultGroup['semester']?->name ?? __('student_dashboard.semester') }}
                                        </h3>
                                        <p class="text-sm font-semibold">
                                            {{ __('student_dashboard.semester_gpa') }}:
                                            {{ $resultGroup['gpa'] !== null ? number_format($resultGroup['gpa'], 2) . ' / ' . number_format($sd['gpa_max'], 1) : __('student_dashboard.not_available') }}
                                        </p>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm min-w-[760px]">
                                            <thead>
                                                <tr class="text-xs text-muted border-b" style="border-color: var(--border)">
                                                    <th class="text-start p-2">{{ __('student_dashboard.course') }}</th>
                                                    <th class="text-start p-2">{{ __('student_dashboard.course_code') }}</th>
                                                    <th class="text-center p-2">{{ __('student_dashboard.credit_hours') }}</th>
                                                    <th class="text-center p-2">{{ __('student_dashboard.coursework_score') }}</th>
                                                    <th class="text-center p-2">{{ __('student_dashboard.midterm_score') }}</th>
                                                    <th class="text-center p-2">{{ __('student_dashboard.final_score') }}</th>
                                                    <th class="text-center p-2">{{ __('student_dashboard.total_score') }}</th>
                                                    <th class="text-center p-2">{{ __('student_dashboard.grade') }}</th>
                                                    <th class="text-center p-2">{{ __('student_dashboard.result_status') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($resultGroup['results'] as $item)
                                                    @php
                                                        $result = $item['result'];
                                                        $course = $item['course'];
                                                        $resultStatus = $result->status;
                                                        $resultStatus = $resultStatus instanceof \BackedEnum ? $resultStatus->value : (string) $resultStatus;
                                                    @endphp
                                                    <tr class="border-b last:border-0" style="border-color: var(--border)">
                                                        <td class="p-2 font-medium">{{ $course?->name ?? '—' }}</td>
                                                        <td class="p-2">{{ $course?->code ?? '—' }}</td>
                                                        <td class="p-2 text-center">{{ $course?->credit_hours ?? '—' }}</td>
                                                        <td class="p-2 text-center">{{ $result->coursework_score ?? '—' }}</td>
                                                        <td class="p-2 text-center">{{ $result->midterm_score ?? '—' }}</td>
                                                        <td class="p-2 text-center">{{ $result->final_score ?? '—' }}</td>
                                                        <td class="p-2 text-center font-semibold">{{ $result->total_score ?? '—' }}</td>
                                                        <td class="p-2 text-center font-semibold">{{ $result->grade ?? '—' }}</td>
                                                        <td class="p-2 text-center">{{ __('student_dashboard.result_statuses.' . $resultStatus) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </x-card>
            </div>
        @else
            <x-card>
                <x-empty-state icon="user" :message="__('student_dashboard.not_available')" />
            </x-card>
        @endif
    @endif

    {{-- Statistics for staff roles --}}
    @if($role !== 'student')
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
    @endif

    {{-- Charts (admin only) --}}
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

    {{-- Quick actions for staff roles --}}
    @if($role !== 'student')
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
    @endif

    {{-- Recent activity (admin/registrar) --}}
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