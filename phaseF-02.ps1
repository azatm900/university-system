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

Write-Host "═══ F2a: invoices/index.blade.php ═══" -ForegroundColor Cyan

$index = @'
@extends('layouts.app')

@section('title', __('invoices.title'))

@section('content')
<x-page-header :title="__('invoices.title')" :subtitle="__('invoices.subtitle')">
    <x-button variant="primary" :href="route('finance.invoices.create')">
        <x-icon name="plus" :size="16" /> {{ __('invoices.actions.create') }}
    </x-button>
</x-page-header>

<x-card class="mb-4">
    <form method="GET" action="{{ route('finance.invoices.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3">
        <div>
            <label class="block text-sm mb-1">{{ __('invoices.filters.search') }}</label>
            <input type="text" name="search" value="{{ request('search') }}" class="input" placeholder="{{ __('invoices.filters.search_placeholder') }}">
        </div>
        <div>
            <label class="block text-sm mb-1">{{ __('invoices.fields.status') }}</label>
            <select name="status" class="input">
                <option value="">{{ __('invoices.filters.all') }}</option>
                @foreach(\App\Enums\InvoiceStatus::cases() as $case)
                    <option value="{{ $case->value }}" @selected(request('status') === $case->value)>{{ __('invoices.statuses.' . $case->value) }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">{{ __('invoices.fields.semester') }}</label>
            <select name="semester_id" class="input">
                <option value="">{{ __('invoices.filters.all') }}</option>
                @foreach($semesters as $s)
                    <option value="{{ $s->id }}" @selected(request('semester_id') == $s->id)>{{ $s->name }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-sm mb-1">{{ __('invoices.fields.student') }}</label>
            <select name="student_id" class="input">
                <option value="">{{ __('invoices.filters.all') }}</option>
                @foreach($students as $st)
                    <option value="{{ $st->id }}" @selected(request('student_id') == $st->id)>
                        {{ $st->user?->name ?? ('#' . $st->id) }} — {{ $st->student_number }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="md:col-span-4 flex gap-2">
            <x-button type="submit" variant="primary" size="sm">
                <x-icon name="search" :size="16" /> {{ __('invoices.filters.search') }}
            </x-button>
            <x-button variant="secondary" size="sm" :href="route('finance.invoices.index')">
                {{ __('invoices.actions.cancel') }}
            </x-button>
        </div>
    </form>
</x-card>

<x-table-wrapper>
    @if($invoices->isEmpty())
        <x-empty-state icon="🧾" :title="__('invoices.empty.title')" :description="__('invoices.empty.description')" />
    @else
        <table class="table w-full">
            <thead>
                <tr>
                    <th>{{ __('invoices.fields.invoice_number') }}</th>
                    <th>{{ __('invoices.fields.student') }}</th>
                    <th>{{ __('invoices.fields.semester') }}</th>
                    <th>{{ __('invoices.fields.total') }}</th>
                    <th>{{ __('invoices.fields.paid_amount') }}</th>
                    <th>{{ __('invoices.fields.remaining') }}</th>
                    <th>{{ __('invoices.fields.status') }}</th>
                    <th>{{ __('invoices.fields.due_date') }}</th>
                    <th>{{ __('invoices.actions.view') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $invoice)
                    @php
                        $total = (float) $invoice->total;
                        $paid = (float) $invoice->paid_amount;
                        $remaining = $total - $paid;
                        $statusValue = $invoice->status?->value ?? 'unpaid';
                    @endphp
                    <tr>
                        <td class="font-mono text-sm">{{ $invoice->invoice_number }}</td>
                        <td>{{ $invoice->student?->user?->name ?? '-' }}</td>
                        <td>{{ $invoice->semester?->name ?? '-' }}</td>
                        <td class="font-mono">{{ number_format($total, 2) }}</td>
                        <td class="font-mono text-green-600">{{ number_format($paid, 2) }}</td>
                        <td class="font-mono {{ $remaining > 0 ? 'text-red-600' : 'text-gray-400' }}">{{ number_format($remaining, 2) }}</td>
                        <td>
                            <x-badge :color="match($statusValue) { 'paid' => 'success', 'partial' => 'warning', 'cancelled' => 'danger', default => 'info' }">
                                {{ __('invoices.statuses.' . $statusValue) }}
                            </x-badge>
                        </td>
                        <td>{{ $invoice->due_date?->format('Y-m-d') ?? '-' }}</td>
                        <td>
                            <div class="flex items-center gap-1">
                                <x-button variant="secondary" size="sm" :href="route('finance.invoices.show', $invoice)">
                                    <x-icon name="eye" :size="16" />
                                </x-button>
                                @if($statusValue !== 'cancelled')
                                    <x-button variant="secondary" size="sm" :href="route('finance.invoices.edit', $invoice)">
                                        <x-icon name="pencil" :size="16" />
                                    </x-button>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <x-slot name="footer">{{ $invoices->links() }}</x-slot>
    @endif
</x-table-wrapper>
@endsection
'@

Write-File "resources/views/finance/invoices/index.blade.php" $index

Write-Host "`n═══ F2b: invoices/create.blade.php ═══" -ForegroundColor Cyan

$create = @'
@extends('layouts.app')

@section('title', __('invoices.actions.create'))

@section('content')
<x-page-header :title="__('invoices.actions.create')" :subtitle="__('invoices.subtitle')" />

<form method="POST" action="{{ route('finance.invoices.store') }}">
    @csrf
    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm mb-1">{{ __('invoices.fields.student') }} <span class="text-red-500">*</span></label>
                <select name="student_id" class="input" required>
                    <option value="">— {{ __('invoices.filters.all') }} —</option>
                    @foreach($students as $st)
                        <option value="{{ $st->id }}" @selected(old('student_id') == $st->id)>
                            {{ $st->user?->name ?? ('#' . $st->id) }} — {{ $st->student_number }}
                        </option>
                    @endforeach
                </select>
                @error('student_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('invoices.fields.semester') }}</label>
                <select name="semester_id" class="input">
                    <option value="">— {{ __('invoices.filters.all') }} —</option>
                    @foreach($semesters as $s)
                        <option value="{{ $s->id }}" @selected(old('semester_id') == $s->id)>{{ $s->name }}</option>
                    @endforeach
                </select>
                @error('semester_id') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm mb-1">{{ __('invoices.fields.description') }} <span class="text-red-500">*</span></label>
                <input type="text" name="description" value="{{ old('description') }}" class="input" maxlength="255" required>
                @error('description') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('invoices.fields.amount') }} <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0.01" name="amount" value="{{ old('amount') }}" class="input" required>
                @error('amount') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('invoices.fields.discount') }}</label>
                <input type="number" step="0.01" min="0" name="discount" value="{{ old('discount', 0) }}" class="input">
                @error('discount') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm mb-1">{{ __('invoices.fields.due_date') }} <span class="text-red-500">*</span></label>
                <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(30)->toDateString()) }}" class="input" required>
                @error('due_date') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
        </div>
        <x-slot name="footer">
            <div class="flex gap-2">
                <x-button type="submit" variant="primary">{{ __('invoices.actions.save') }}</x-button>
                <x-button variant="secondary" :href="route('finance.invoices.index')">{{ __('invoices.actions.cancel') }}</x-button>
            </div>
        </x-slot>
    </x-card>
</form>
@endsection
'@

Write-File "resources/views/finance/invoices/create.blade.php" $create

Write-Host "`n═══ F2c: invoices/edit.blade.php ═══" -ForegroundColor Cyan

$edit = @'
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
'@

Write-File "resources/views/finance/invoices/edit.blade.php" $edit

Write-Host "`n═══ F2d: invoices/show.blade.php ═══" -ForegroundColor Cyan

$show = @'
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
'@

Write-File "resources/views/finance/invoices/show.blade.php" $show

Write-Host "`n═══ Clear blade cache ═══" -ForegroundColor Cyan
& php artisan view:clear

Write-Host "`n═══ F2 Complete ═══" -ForegroundColor Green
Write-Host "Next: phaseF-03-payments-views.ps1" -ForegroundColor Yellow