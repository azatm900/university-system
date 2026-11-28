@extends('layouts.app')

@section('title', __('student_portal.finance_title'))

@section('content')
    <div class="mb-5 animate-fade-in-up">
        <h1 class="text-xl md:text-2xl font-bold" style="color: var(--text)">
            {{ __('student_portal.finance_title') }}
        </h1>
        <p class="text-sm text-muted mt-1">
            {{ __('student_portal.finance_subtitle') }}
        </p>
    </div>

    <div class="mb-6 animate-fade-in-up">
        <x-card>
            <form method="GET" action="{{ route('student.finance.index') }}"
                  class="flex flex-col sm:flex-row sm:items-end gap-3">
                <div class="flex-1">
                    <label for="level" class="block text-sm font-medium mb-2">
                        {{ __('student_portal.select_level') }}
                    </label>
                    <select id="level" name="level"
                            class="form-select w-full rounded-lg border p-2.5 text-sm"
                            style="border-color: var(--border); background: var(--surface); color: var(--text)">
                        @foreach($levels as $level)
                            <option value="{{ $level }}" @selected($selectedLevel === $level)>
                                {{ __('student_portal.level_number', ['number' => $level]) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    {{ __('student_portal.show_level') }}
                </button>
            </form>
        </x-card>
    </div>

    <div class="mb-6 grid grid-cols-1 sm:grid-cols-3 gap-4 animate-fade-in-up">
        <x-card>
            <p class="text-xs text-muted">{{ __('student_portal.total_fees') }}</p>
            <p class="text-xl font-bold mt-2" style="color: var(--text)">
                {{ number_format($total, 2) }} <span class="text-sm">{{ $currency }}</span>
            </p>
        </x-card>

        <x-card>
            <p class="text-xs text-muted">{{ __('student_portal.total_paid') }}</p>
            <p class="text-xl font-bold mt-2" style="color: var(--text)">
                {{ number_format($paid, 2) }} <span class="text-sm">{{ $currency }}</span>
            </p>
        </x-card>

        <x-card>
            <p class="text-xs text-muted">{{ __('student_portal.total_remaining') }}</p>
            <p class="text-xl font-bold mt-2" style="color: var(--text)">
                {{ number_format($remaining, 2) }} <span class="text-sm">{{ $currency }}</span>
            </p>
        </x-card>
    </div>

    <div class="mb-6 animate-fade-in-up">
        <x-card :title="__('student_portal.invoices')">
            @if($invoices->isEmpty())
                <x-empty-state icon="wallet" :message="__('student_portal.no_invoices')" />
            @else
                <div class="space-y-5">
                    @foreach($invoices as $invoice)
                        @php
                            $statusValue = $invoice->status?->value ?? 'unpaid';
                            $invoiceRemaining = max(0, (float) $invoice->total - (float) $invoice->paid_amount);
                        @endphp

                        <div class="rounded-xl border p-3 sm:p-4"
                             style="border-color: var(--border)">
                            <div class="flex flex-wrap items-start justify-between gap-3 mb-4">
                                <div>
                                    <h3 class="font-semibold" style="color: var(--text)">
                                        {{ $invoice->invoice_number }}
                                    </h3>
                                    <p class="text-sm text-muted mt-1">
                                        {{ $invoice->description ?: '—' }}
                                    </p>
                                    <p class="text-xs text-muted mt-1">
                                        {{ $invoice->semester?->name ?? __('student_portal.no_semester') }}
                                    </p>
                                </div>

                                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold"
                                      style="background: var(--surface); color: var(--text)">
                                    {{ __('student_portal.status_' . $statusValue) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 text-sm">
                                <div>
                                    <p class="text-xs text-muted">{{ __('student_portal.invoice_date') }}</p>
                                    <p class="font-medium mt-1">
                                        {{ $invoice->created_at?->format('Y-m-d') ?? '—' }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-muted">{{ __('student_portal.amount') }}</p>
                                    <p class="font-medium mt-1">
                                        {{ number_format((float) $invoice->amount, 2) }} {{ $currency }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-muted">{{ __('student_portal.discount') }}</p>
                                    <p class="font-medium mt-1">
                                        {{ number_format((float) $invoice->discount, 2) }} {{ $currency }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-muted">{{ __('student_portal.total') }}</p>
                                    <p class="font-semibold mt-1">
                                        {{ number_format((float) $invoice->total, 2) }} {{ $currency }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-muted">{{ __('student_portal.paid_amount') }}</p>
                                    <p class="font-medium mt-1">
                                        {{ number_format((float) $invoice->paid_amount, 2) }} {{ $currency }}
                                    </p>
                                </div>
                                <div>
                                    <p class="text-xs text-muted">{{ __('student_portal.remaining_amount') }}</p>
                                    <p class="font-semibold mt-1">
                                        {{ number_format($invoiceRemaining, 2) }} {{ $currency }}
                                    </p>
                                </div>
                            </div>

                            <div class="mt-4 pt-4 border-t" style="border-color: var(--border)">
                                <h4 class="text-sm font-semibold mb-3">
                                    {{ __('student_portal.payments') }}
                                </h4>

                                @if($invoice->payments->isEmpty())
                                    <p class="text-sm text-muted">
                                        {{ __('student_portal.no_payments') }}
                                    </p>
                                @else
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-sm min-w-[560px]">
                                            <thead>
                                                <tr class="text-xs text-muted border-b"
                                                    style="border-color: var(--border)">
                                                    <th class="text-start p-2">{{ __('student_portal.payment_date') }}</th>
                                                    <th class="text-start p-2">{{ __('student_portal.payment_method') }}</th>
                                                    <th class="text-start p-2">{{ __('student_portal.reference_number') }}</th>
                                                    <th class="text-end p-2">{{ __('student_portal.payment_amount') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($invoice->payments as $payment)
                                                    @php
                                                        $methodValue = $payment->method?->value ?? '';
                                                        $methodLabel = match ($methodValue) {
                                                            'cash' => app()->getLocale() === 'ar' ? 'نقدي' : 'Cash',
                                                            'bank_transfer' => app()->getLocale() === 'ar' ? 'حوالة بنكية' : 'Bank transfer',
                                                            'card' => app()->getLocale() === 'ar' ? 'بطاقة' : 'Card',
                                                            'check' => app()->getLocale() === 'ar' ? 'شيك' : 'Check',
                                                            default => __('student_portal.unknown'),
                                                        };
                                                    @endphp
                                                    <tr class="border-b last:border-0"
                                                        style="border-color: var(--border)">
                                                        <td class="p-2">{{ $payment->payment_date?->format('Y-m-d') ?? '—' }}</td>
                                                        <td class="p-2">{{ $methodLabel }}</td>
                                                        <td class="p-2">{{ $payment->reference_number ?: '—' }}</td>
                                                        <td class="p-2 text-end font-semibold">
                                                            {{ number_format((float) $payment->amount, 2) }} {{ $currency }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </x-card>
    </div>

    @if($unclassifiedInvoices->isNotEmpty())
        <div class="mb-6 animate-fade-in-up">
            <x-card :title="__('student_portal.unclassified')">
                <p class="text-sm text-muted mb-4">
                    {{ __('student_portal.unclassified_note') }}
                </p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-5">
                    <div>
                        <p class="text-xs text-muted">{{ __('student_portal.total_fees') }}</p>
                        <p class="font-semibold mt-1">
                            {{ number_format($unclassifiedTotal, 2) }} {{ $currency }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-muted">{{ __('student_portal.total_paid') }}</p>
                        <p class="font-semibold mt-1">
                            {{ number_format($unclassifiedPaid, 2) }} {{ $currency }}
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-muted">{{ __('student_portal.total_remaining') }}</p>
                        <p class="font-semibold mt-1">
                            {{ number_format($unclassifiedRemaining, 2) }} {{ $currency }}
                        </p>
                    </div>
                </div>

                <div class="space-y-3">
                    @foreach($unclassifiedInvoices as $invoice)
                        @php
                            $statusValue = $invoice->status?->value ?? 'unpaid';
                            $invoiceRemaining = max(0, (float) $invoice->total - (float) $invoice->paid_amount);
                        @endphp
                        <div class="rounded-xl border p-3 sm:p-4"
                             style="border-color: var(--border)">
                            <div class="flex flex-wrap justify-between gap-2">
                                <div>
                                    <p class="font-semibold">{{ $invoice->invoice_number }}</p>
                                    <p class="text-sm text-muted mt-1">
                                        {{ $invoice->description ?: '—' }}
                                    </p>
                                    <p class="text-xs text-muted mt-1">
                                        {{ $invoice->semester?->name ?? __('student_portal.no_semester') }}
                                    </p>
                                </div>
                                <span class="text-xs rounded-full px-3 py-1 h-fit"
                                      style="background: var(--surface); color: var(--text)">
                                    {{ __('student_portal.status_' . $statusValue) }}
                                </span>
                            </div>

                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3 mt-4 text-sm">
                                <div>
                                    <p class="text-xs text-muted">{{ __('student_portal.total') }}</p>
                                    <p class="font-semibold mt-1">{{ number_format((float) $invoice->total, 2) }} {{ $currency }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-muted">{{ __('student_portal.paid_amount') }}</p>
                                    <p class="font-medium mt-1">{{ number_format((float) $invoice->paid_amount, 2) }} {{ $currency }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-muted">{{ __('student_portal.remaining_amount') }}</p>
                                    <p class="font-semibold mt-1">{{ number_format($invoiceRemaining, 2) }} {{ $currency }}</p>
                                </div>
                            </div>

                            @if($invoice->payments->isNotEmpty())
                                <div class="mt-4 pt-3 border-t" style="border-color: var(--border)">
                                    <p class="text-sm font-semibold mb-2">{{ __('student_portal.payments') }}</p>
                                    @foreach($invoice->payments as $payment)
                                        @php
                                            $methodValue = $payment->method?->value ?? '';
                                            $methodLabel = match ($methodValue) {
                                                'cash' => app()->getLocale() === 'ar' ? 'نقدي' : 'Cash',
                                                'bank_transfer' => app()->getLocale() === 'ar' ? 'حوالة بنكية' : 'Bank transfer',
                                                'card' => app()->getLocale() === 'ar' ? 'بطاقة' : 'Card',
                                                'check' => app()->getLocale() === 'ar' ? 'شيك' : 'Check',
                                                default => __('student_portal.unknown'),
                                            };
                                        @endphp
                                        <div class="flex flex-wrap justify-between gap-2 text-sm py-1">
                                            <span class="text-muted">
                                                {{ $payment->payment_date?->format('Y-m-d') ?? '—' }}
                                                · {{ $methodLabel }}
                                                · {{ $payment->reference_number ?: '—' }}
                                            </span>
                                            <span class="font-semibold">
                                                {{ number_format((float) $payment->amount, 2) }} {{ $currency }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </x-card>
        </div>
    @endif
@endsection