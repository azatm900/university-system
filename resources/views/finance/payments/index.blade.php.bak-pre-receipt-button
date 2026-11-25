@extends('layouts.app')

@section('title', __('payments.title'))

@section('content')
<x-page-header :title="__('payments.title')" :subtitle="__('payments.subtitle')">
    <x-button variant="primary" :href="route('finance.payments.create')">
        <x-icon name="plus" :size="16" /> {{ __('payments.actions.create') }}
    </x-button>
</x-page-header>

<x-card class="mb-4">
    <form method="GET" action="{{ route('finance.payments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label class="block text-sm mb-1">{{ __('payments.filters.search') }}</label>
            <input type="text" name="search" value="{{ request('search') }}" class="input" placeholder="{{ __('payments.filters.search_placeholder') }}">
        </div>
        <div>
            <label class="block text-sm mb-1">رقم النظام</label>
            <input type="text" name="student_number" value="{{ request('student_number') }}" class="input font-mono" placeholder="2026140001" maxlength="20">
        </div>
        <div>
            <label class="block text-sm mb-1">{{ __('payments.fields.method') }}</label>
            <select name="method" class="input">
                <option value="">{{ __('payments.filters.all') }}</option>
                @foreach(\App\Enums\PaymentMethod::cases() as $case)
                    <option value="{{ $case->value }}" @selected(request('method') === $case->value)>{{ __('payments.methods.' . $case->value) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">{{ __('payments.fields.invoice') }}</label>
            <select name="invoice_id" class="input">
                <option value="">{{ __('payments.filters.all') }}</option>
                @foreach($invoices as $inv)
                    <option value="{{ $inv->id }}" @selected(request('invoice_id') == $inv->id)>{{ $inv->invoice_number }}</option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-4 flex gap-2">
            <x-button type="submit" variant="primary" size="sm">
                <x-icon name="search" :size="16" /> {{ __('payments.filters.search') }}
            </x-button>
            <x-button variant="secondary" size="sm" :href="route('finance.payments.index')">
                {{ __('payments.actions.cancel') }}
            </x-button>
        </div>
    </form>
</x-card>

<x-table-wrapper>
    @if($payments->isEmpty())
        <x-empty-state icon="💳" :title="__('payments.empty.title')" :description="__('payments.empty.description')" />
    @else
        <table class="table w-full">
            <thead>
                <tr>
                    <th>#</th>
                    <th>{{ __('payments.fields.invoice') }}</th>
                    <th>رقم النظام</th>
                    <th>{{ __('payments.fields.student') }}</th>
                    <th>{{ __('payments.fields.amount') }}</th>
                    <th>{{ __('payments.fields.payment_date') }}</th>
                    <th>{{ __('payments.fields.method') }}</th>
                    <th>{{ __('payments.fields.reference_number') }}</th>
                    <th>{{ __('payments.fields.receiver') }}</th>
                    <th>{{ __('payments.actions.view') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($payments as $payment)
                    @php $methodValue = $payment->method?->value ?? (string) $payment->method; @endphp
                    <tr>
                        <td>{{ $payment->id }}</td>
                        <td class="font-mono text-sm">
                            @if($payment->invoice)
                                <a href="{{ route('finance.invoices.show', $payment->invoice) }}" class="text-brand-700 hover:underline">
                                    {{ $payment->invoice->invoice_number }}
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="font-mono text-sm">{{ $payment->invoice?->student?->student_number ?? '-' }}</td>
                        <td>{{ $payment->invoice?->student?->user?->name ?? '-' }}</td>
                        <td class="font-mono">{{ number_format((float) $payment->amount, 2) }}</td>
                        <td>{{ $payment->payment_date?->format('Y-m-d') ?? '-' }}</td>
                        <td><x-badge color="info">{{ __('payments.methods.' . $methodValue) }}</x-badge></td>
                        <td class="font-mono text-sm">{{ $payment->reference_number ?? '-' }}</td>
                        <td>{{ $payment->receiver?->name ?? '-' }}</td>
                        <td>
                            <div class="flex items-center gap-1">
                                <x-button variant="success" size="sm" :href="route('finance.payments.receipt', $payment)" title="{{ __('payments.actions.receipt') ?? 'إيصال' }}">
                                    🧾
                                </x-button>
                                <x-button variant="secondary" size="sm" :href="route('finance.payments.show', $payment)">
                                    <x-icon name="eye" :size="16" />
                                </x-button>
                                <x-button variant="secondary" size="sm" :href="route('finance.payments.edit', $payment)">
                                    <x-icon name="pencil" :size="16" />
                                </x-button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <x-slot name="footer">{{ $payments->links() }}</x-slot>
    @endif
</x-table-wrapper>
@endsection