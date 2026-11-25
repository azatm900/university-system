$ErrorActionPreference = 'Stop'
[Console]::OutputEncoding = [System.Text.Encoding]::UTF8
$root = "C:\Users\maham\Herd\university-system"
Set-Location $root

function Write-File {
    param([string]$Rel, [string]$Content)
    $full = Join-Path $root $Rel
    if (Test-Path $full) { Copy-Item $full "$full.bak" -Force; Write-Host "  [BAK] $Rel.bak" -ForegroundColor DarkGray }
    [IO.File]::WriteAllText($full, $Content, (New-Object System.Text.UTF8Encoding($false)))
    Write-Host "  [WRT] $Rel" -ForegroundColor Green
}

Write-Host "═══ F3a: payments/index.blade.php ═══" -ForegroundColor Cyan

$index = @'
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
                        <td>{{ $payment->invoice?->student?->user?->name ?? '-' }}</td>
                        <td class="font-mono">{{ number_format((float) $payment->amount, 2) }}</td>
                        <td>{{ $payment->payment_date?->format('Y-m-d') ?? '-' }}</td>
                        <td><x-badge color="info">{{ __('payments.methods.' . $methodValue) }}</x-badge></td>
                        <td class="font-mono text-sm">{{ $payment->reference_number ?? '-' }}</td>
                        <td>{{ $payment->receiver?->name ?? '-' }}</td>
                        <td>
                            <div class="flex items-center gap-1">
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
'@

Write-File "resources/views/finance/payments/index.blade.php" $index

Write-Host "`n═══ F3b: payments/create.blade.php (يدعم ?invoice_id=X) ═══" -ForegroundColor Cyan

$create = @'
@extends('layouts.app')

@section('title', __('payments.actions.create'))

@section('content')
<x-page-header :title="__('payments.actions.create')" :subtitle="__('payments.subtitle')" />

<form method="POST" action="{{ route('finance.payments.store') }}">
    @csrf
    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">{{ __('payments.fields.invoice') }} <span class="text-red-500">*</span></label>
                @php $selectedInvoice = old('invoice_id', request('invoice_id')); @endphp
                <select name="invoice_id" class="input" required>
                    <option value="">— {{ __('payments.filters.all') }} —</option>
                    @foreach($invoices as $inv)
                        <option value="{{ $inv->id }}" @selected($selectedInvoice == $inv->id)>
                            {{ $inv->invoice_number }} — {{ $inv->student?->user?->name ?? '-' }} — {{ number_format((float) $inv->total, 2) }}
                        </option>
                    @endforeach
                </select>
                @error('invoice_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('payments.fields.amount') }} <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" class="input" required>
                @error('amount') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('payments.fields.payment_date') }} <span class="text-red-500">*</span></label>
                <input type="date" name="payment_date" value="{{ old('payment_date', now()->toDateString()) }}" class="input" required>
                @error('payment_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('payments.fields.method') }} <span class="text-red-500">*</span></label>
                <select name="method" class="input" required>
                    @foreach(\App\Enums\PaymentMethod::cases() as $case)
                        <option value="{{ $case->value }}" @selected(old('method', 'cash') === $case->value)>{{ __('payments.methods.' . $case->value) }}</option>
                    @endforeach
                </select>
                @error('method') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('payments.fields.reference_number') }}</label>
                <input type="text" name="reference_number" value="{{ old('reference_number') }}" class="input" maxlength="50">
                @error('reference_number') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">{{ __('payments.fields.notes') }}</label>
                <textarea name="notes" class="input" rows="3">{{ old('notes') }}</textarea>
                @error('notes') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <x-slot name="footer">
            <div class="flex gap-2">
                <x-button type="submit" variant="primary">{{ __('payments.actions.save') }}</x-button>
                <x-button variant="secondary" :href="route('finance.payments.index')">{{ __('payments.actions.cancel') }}</x-button>
            </div>
        </x-slot>
    </x-card>
</form>
@endsection
'@

Write-File "resources/views/finance/payments/create.blade.php" $create

Write-Host "`n═══ F3c: payments/edit.blade.php (reference + notes فقط) ═══" -ForegroundColor Cyan

$edit = @'
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
'@

Write-File "resources/views/finance/payments/edit.blade.php" $edit

Write-Host "`n═══ F3d: payments/show.blade.php ═══" -ForegroundColor Cyan

$show = @'
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
'@

Write-File "resources/views/finance/payments/show.blade.php" $show

Write-Host "`n═══ Clear blade cache ═══" -ForegroundColor Cyan
& php artisan view:clear

Write-Host "`n═══ F3 Complete ═══" -ForegroundColor Green
Write-Host "Next: phaseF-04-reports-verify.ps1" -ForegroundColor Yellow