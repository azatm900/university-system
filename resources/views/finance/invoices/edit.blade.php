@extends('layouts.app')

@section('title', __('invoices.actions.edit'))

@section('content')
<x-page-header :title="__('invoices.actions.edit')" :subtitle="$invoice->invoice_number">
    <x-button variant="secondary" :href="route('finance.invoices.index')">{{ __('invoices.actions.back') }}</x-button>
</x-page-header>

<form method="POST" action="{{ route('finance.invoices.update', $invoice) }}">
    @csrf
    @method('PUT')
    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <p class="text-sm text-gray-500">{{ __('invoices.fields.student') }}</p>
                <p class="font-medium">{{ $invoice->student?->user?->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">{{ __('invoices.fields.semester') }}</p>
                <p class="font-medium">{{ $invoice->semester?->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">{{ __('invoices.fields.paid_amount') }}</p>
                <p class="font-medium font-mono">{{ number_format((float) $invoice->paid_amount, 2) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">{{ __('invoices.fields.description') }} <span class="text-red-500">*</span></label>
                <input type="text" name="description" value="{{ old('description', $invoice->description) }}" class="input" maxlength="255" required>
                @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('invoices.fields.amount') }} <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount', $invoice->amount) }}" class="input" required>
                @error('amount') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('invoices.fields.discount') }}</label>
                <input type="number" step="0.01" min="0" name="discount" value="{{ old('discount', $invoice->discount) }}" class="input">
                @error('discount') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('invoices.fields.due_date') }} <span class="text-red-500">*</span></label>
                <input type="date" name="due_date" value="{{ old('due_date', $invoice->due_date?->format('Y-m-d')) }}" class="input" required>
                @error('due_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <x-slot name="footer">
            <div class="flex gap-2">
                <x-button type="submit" variant="primary">{{ __('invoices.actions.save') }}</x-button>
                <x-button variant="secondary" :href="route('finance.invoices.show', $invoice)">{{ __('invoices.actions.cancel') }}</x-button>
            </div>
        </x-slot>
    </x-card>
</form>
@endsection