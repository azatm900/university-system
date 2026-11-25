@extends('layouts.app')

@section('title', $invoice->invoice_number)

@section('content')
@php
    $total = (float) $invoice->total;
    $paid = (float) $invoice->paid_amount;
    $remaining = $total - $paid;
    $statusValue = $invoice->status?->value ?? 'unpaid';
@endphp

<x-page-header :title="$invoice->invoice_number" :subtitle="$invoice->description">
    @if($statusValue !== 'cancelled')
        <x-button variant="primary" :href="route('finance.payments.create', ['invoice_id' => $invoice->id])">
            <x-icon name="plus" :size="16" /> {{ __('invoices.actions.add_payment') }}
        </x-button>
        <x-button variant="secondary" :href="route('finance.invoices.edit', $invoice)">
            <x-icon name="pencil" :size="16" /> {{ __('invoices.actions.edit') }}
        </x-button>
    @endif
    <x-button variant="secondary" :href="route('finance.invoices.index')">
        {{ __('invoices.actions.back') }}
    </x-button>
</x-page-header>

<x-card :title="__('invoices.show.info')">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div>
            <p class="text-sm text-gray-500">{{ __('invoices.fields.student') }}</p>
            <p class="font-medium">{{ $invoice->student?->user?->name ?? '-' }}</p>
            <p class="text-xs text-gray-400 font-mono">{{ $invoice->student?->student_number }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('invoices.fields.semester') }}</p>
            <p class="font-medium">{{ $invoice->semester?->name ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('invoices.fields.status') }}</p>
            <x-badge :color="match($statusValue) { 'paid' => 'success', 'partial' => 'warning', 'cancelled' => 'danger', default => 'info' }">
                {{ __('invoices.statuses.' . $statusValue) }}
            </x-badge>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('invoices.fields.amount') }}</p>
            <p class="font-medium font-mono">{{ number_format((float) $invoice->amount, 2) }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('invoices.fields.discount') }}</p>
            <p class="font-medium font-mono">{{ number_format((float) $invoice->discount, 2) }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('invoices.fields.due_date') }}</p>
            <p class="font-medium">{{ $invoice->due_date?->format('Y-m-d') ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('invoices.fields.total') }}</p>
            <p class="font-bold font-mono text-lg">{{ number_format($total, 2) }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('invoices.fields.paid_amount') }}</p>
            <p class="font-medium font-mono text-green-600">{{ number_format($paid, 2) }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('invoices.fields.remaining') }}</p>
            <p class="font-bold font-mono text-lg {{ $remaining > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ number_format($remaining, 2) }}</p>
        </div>
    </div>
</x-card>

<x-card :title="__('invoices.show.payments')" class="mt-4">
    @if($invoice->payments->isEmpty())
        <x-empty-state icon="💳" :title="__('invoices.show.no_payments')" />
    @else
        <table class="table w-full">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('payments.fields.amount') }}</th>
                    <th>{{ __('payments.fields.payment_date') }}</th>
                    <th>{{ __('payments.fields.method') }}</th>
                    <th>{{ __('payments.fields.reference_number') }}</th>
                    <th>{{ __('payments.fields.receiver') }}</th>
                    <th>{{ __('payments.actions.view') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->payments as $payment)
                    @php $methodValue = $payment->method?->value ?? (string) $payment->method; @endphp
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td class="font-mono">{{ number_format((float) $payment->amount, 2) }}</td>
                        <td>{{ $payment->payment_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            <x-badge color="info">{{ __('payments.methods.' . $methodValue) }}</x-badge>
                        </td>
                        <td class="font-mono text-sm">{{ $payment->reference_number ?? '-' }}</td>
                        <td>{{ $payment->receiver?->name ?? '-' }}</td>
                        <td>
                            <x-button variant="secondary" size="sm" :href="route('finance.payments.show', $payment)">
                                <x-icon name="eye" :size="16" />
                            </x-button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</x-card>
@endsection