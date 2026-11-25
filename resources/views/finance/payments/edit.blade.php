@extends('layouts.app')

@section('title', __('payments.actions.edit'))

@section('content')
<x-page-header :title="__('payments.actions.edit')" :subtitle="__('payments.singular') . ' #' . $payment->id">
    <x-button variant="secondary" :href="route('finance.payments.show', $payment)">{{ __('payments.actions.back') }}</x-button>
</x-page-header>

<x-card>
    <div class="mb-4 p-3 rounded bg-yellow-50 border border-yellow-200 text-sm text-yellow-800">
        {{ __('payments.messages.edit_note') }}
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
            <p class="text-sm text-gray-500">{{ __('payments.fields.invoice') }}</p>
            <p class="font-medium font-mono">{{ $payment->invoice?->invoice_number ?? '-' }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('payments.fields.amount') }}</p>
            <p class="font-medium font-mono">{{ number_format((float) $payment->amount, 2) }}</p>
        </div>
        <div>
            <p class="text-sm text-gray-500">{{ __('payments.fields.payment_date') }}</p>
            <p class="font-medium">{{ $payment->payment_date?->format('Y-m-d') ?? '-' }}</p>
        </div>
    </div>

    <form method="POST" action="{{ route('finance.payments.update', $payment) }}">
        @csrf
        @method('PUT')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">{{ __('payments.fields.reference_number') }}</label>
                <input type="text" name="reference_number" value="{{ old('reference_number', $payment->reference_number) }}" class="input" maxlength="50">
                @error('reference_number') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">{{ __('payments.fields.notes') }}</label>
                <textarea name="notes" class="input" rows="3">{{ old('notes', $payment->notes) }}</textarea>
                @error('notes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <div class="flex gap-2 mt-4">
            <x-button type="submit" variant="primary">{{ __('payments.actions.save') }}</x-button>
            <x-button variant="secondary" :href="route('finance.payments.show', $payment)">{{ __('payments.actions.cancel') }}</x-button>
        </div>
    </form>
</x-card>
@endsection