@extends('layouts.app')

@section('title', __('payments.singular') . ' #' . $payment->id)

@section('content')
@php $methodValue = $payment->method?->value ?? (string) $payment->method; @endphp

<x-page-header :title="__('payments.singular') . ' #' . $payment->id" :subtitle="$payment->invoice?->invoice_number">
    <x-button variant="secondary" :href="route('finance.payments.edit', $payment)">
        <x-icon name="pencil" :size="16" /> {{ __('payments.actions.edit') }}
    </x-button>
    <x-button variant="secondary" :href="route('finance.payments.index')">
        {{ __('payments.actions.back') }}
    </x-button>
</x-page-header>

<x-card :title="__('payments.singular')">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <p class="text-sm text-gray-500">{{ __('payments.fields.invoice') }}</p>
            <p class="font-medium font-mono">
                @if($payment->invoice)
                    <a href="{{ route('finance.invoices.show', $payment->invoice) }}" class="text-brand-700 hover:underline">
                        {{ $payment->invoice->invoice_number }}
                    </a>
                @else
                    -
                @endif
            </p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('payments.fields.student') }}</p>
            <p class="font-medium">{{ $payment->invoice?->student?->user?->name ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('payments.fields.amount') }}</p>
            <p class="font-bold text-lg font-mono text-green-600">{{ number_format((float) $payment->amount, 2) }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('payments.fields.payment_date') }}</p>
            <p class="font-medium">{{ $payment->payment_date?->format('Y-m-d') ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('payments.fields.method') }}</p>
            <x-badge color="info">{{ __('payments.methods.' . $methodValue) }}</x-badge>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('payments.fields.reference_number') }}</p>
            <p class="font-medium font-mono">{{ $payment->reference_number ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('payments.fields.receiver') }}</p>
            <p class="font-medium">{{ $payment->receiver?->name ?? '-' }}</p>
        </div>
        <div class="md:col-span-2">
            <p class="text-sm text-gray-500">{{ __('payments.fields.notes') }}</p>
            <p class="font-medium whitespace-pre-line">{{ $payment->notes ?? '-' }}</p>
        </div>
    </div>
</x-card>
@endsection