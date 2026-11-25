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

Write-Host "═══ F4a: reports/index.blade.php (Hub) ═══" -ForegroundColor Cyan

$reportsIndex = @'
@extends('layouts.app')

@section('title', __('reports.title'))

@section('content')
<x-page-header :title="__('reports.title')" :subtitle="__('reports.subtitle')" />

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    {{-- 1. Student Transcript --}}
    <a href="{{ route('reports.student-transcript.index') }}" class="block group">
        <x-card :padding="'p-5'" class="h-full transition hover:shadow-lg">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center">
                    <x-icon name="graduation-cap" :size="24" />
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-lg mb-1 group-hover:text-brand-700">{{ __('reports.student_transcript') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('reports.student_transcript_desc') }}</p>
                </div>
            </div>
        </x-card>
    </a>

    {{-- 2. Semester Registrations --}}
    <a href="{{ route('reports.semester-registrations.index') }}" class="block group">
        <x-card :padding="'p-5'" class="h-full transition hover:shadow-lg">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-blue-100 text-blue-700 flex items-center justify-center">
                    <x-icon name="clipboard" :size="24" />
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-lg mb-1 group-hover:text-brand-700">{{ __('reports.semester_registrations') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('reports.semester_registrations_desc') }}</p>
                </div>
            </div>
        </x-card>
    </a>

    {{-- 3. Student Financial --}}
    <a href="{{ route('reports.student-financial.index') }}" class="block group">
        <x-card :padding="'p-5'" class="h-full transition hover:shadow-lg">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-green-100 text-green-700 flex items-center justify-center">
                    <x-icon name="wallet" :size="24" />
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-lg mb-1 group-hover:text-brand-700">{{ __('reports.student_financial') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('reports.student_financial_desc') }}</p>
                </div>
            </div>
        </x-card>
    </a>

    {{-- 4. Semester Revenue --}}
    <a href="{{ route('reports.semester-revenue.index') }}" class="block group">
        <x-card :padding="'p-5'" class="h-full transition hover:shadow-lg">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-purple-100 text-purple-700 flex items-center justify-center">
                    <x-icon name="trending-up" :size="24" />
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-lg mb-1 group-hover:text-brand-700">{{ __('reports.semester_revenue') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('reports.semester_revenue_desc') }}</p>
                </div>
            </div>
        </x-card>
    </a>

    {{-- 5. General Statistics --}}
    <a href="{{ route('reports.general-statistics.show') }}" class="block group">
        <x-card :padding="'p-5'" class="h-full transition hover:shadow-lg">
            <div class="flex items-start gap-4">
                <div class="flex-shrink-0 w-12 h-12 rounded-full bg-orange-100 text-orange-700 flex items-center justify-center">
                    <x-icon name="chart-bar" :size="24" />
                </div>
                <div class="flex-1">
                    <h3 class="font-bold text-lg mb-1 group-hover:text-brand-700">{{ __('reports.general_statistics') }}</h3>
                    <p class="text-sm text-gray-500">{{ __('reports.general_statistics_desc') }}</p>
                </div>
            </div>
        </x-card>
    </a>
</div>
@endsection
'@

Write-File "resources/views/reports/index.blade.php" $reportsIndex

Write-Host "`n═══ Clear blade cache ═══" -ForegroundColor Cyan
& php artisan view:clear

Write-Host "`n═══ F4b: npm run build ═══" -ForegroundColor Cyan
& npm run build
if ($LASTEXITCODE -ne 0) { throw "npm run build failed" }

Write-Host "`n═══ F4c: Read manifest (lesson D+3) ═══" -ForegroundColor Cyan
$manifestPath = Join-Path $root "public/build/manifest.json"
if (Test-Path $manifestPath) {
    $manifest = Get-Content $manifestPath -Raw | ConvertFrom-Json
    $cssEntry = $manifest.'resources/css/app.css'
    $jsEntry  = $manifest.'resources/js/app.js'
    if ($cssEntry) { Write-Host "  CSS -> $($cssEntry.file)" -ForegroundColor Green }
    if ($jsEntry)  { Write-Host "  JS  -> $($jsEntry.file)" -ForegroundColor Green }
}

Write-Host "`n═══ F4d: Clear all caches ═══" -ForegroundColor Cyan
& php artisan view:clear
& php artisan route:clear
& php artisan config:clear
& php artisan optimize:clear

Write-Host "`n═══ F4e: Route verification (finance + reports) ═══" -ForegroundColor Cyan
$financeRoutes = & php artisan route:list --path=finance 2>&1 | Out-String
$reportsRoutes = & php artisan route:list --path=reports 2>&1 | Out-String

$required = @(
    'finance.invoices.index','finance.invoices.create','finance.invoices.store',
    'finance.invoices.show','finance.invoices.edit','finance.invoices.update','finance.invoices.destroy',
    'finance.payments.index','finance.payments.create','finance.payments.store',
    'finance.payments.show','finance.payments.edit','finance.payments.update','finance.payments.destroy',
    'reports.index',
    'reports.student-transcript.index',
    'reports.semester-registrations.index',
    'reports.student-financial.index',
    'reports.semester-revenue.index',
    'reports.general-statistics.show'
)
foreach ($r in $required) {
    if (($financeRoutes -match [regex]::Escape($r)) -or ($reportsRoutes -match [regex]::Escape($r))) {
        Write-Host "  [OK] $r" -ForegroundColor Green
    } else {
        Write-Host "  [MISS] $r" -ForegroundColor Red
    }
}

Write-Host "`n═══ F4f: File presence check ═══" -ForegroundColor Cyan
$files = @(
    "app/Http/Controllers/Finance/InvoiceController.php",
    "app/Http/Controllers/Finance/PaymentController.php",
    "resources/views/finance/invoices/index.blade.php",
    "resources/views/finance/invoices/create.blade.php",
    "resources/views/finance/invoices/edit.blade.php",
    "resources/views/finance/invoices/show.blade.php",
    "resources/views/finance/payments/index.blade.php",
    "resources/views/finance/payments/create.blade.php",
    "resources/views/finance/payments/edit.blade.php",
    "resources/views/finance/payments/show.blade.php",
    "resources/views/reports/index.blade.php",
    "lang/ar/invoices.php","lang/en/invoices.php",
    "lang/ar/payments.php","lang/en/payments.php",
    "lang/ar/reports.php","lang/en/reports.php"
)
foreach ($f in $files) {
    $full = Join-Path $root $f
    if (Test-Path $full) {
        $sz = [math]::Round((Get-Item $full).Length / 1024, 2)
        Write-Host "  [OK] $f ($sz KB)" -ForegroundColor Green
    } else {
        Write-Host "  [MISS] $f" -ForegroundColor Red
    }
}

Write-Host "`n═══ F4g: Final PHP lint ═══" -ForegroundColor Cyan
$phpFiles = @(
    "app/Http/Controllers/Finance/InvoiceController.php",
    "app/Http/Controllers/Finance/PaymentController.php",
    "lang/ar/invoices.php","lang/en/invoices.php",
    "lang/ar/payments.php","lang/en/payments.php",
    "lang/ar/reports.php","lang/en/reports.php"
)
foreach ($f in $phpFiles) {
    $out = & php -l (Join-Path $root $f) 2>&1 | Out-String
    if ($out -match 'No syntax errors') {
        Write-Host "  [OK] $f" -ForegroundColor Green
    } else {
        Write-Host "  [FAIL] $f" -ForegroundColor Red
        Write-Host $out -ForegroundColor Red
    }
}

Write-Host "`n══════════════════════════════════════════════" -ForegroundColor Cyan
Write-Host "Phase F deployed!" -ForegroundColor Cyan
Write-Host "Run 12 manual tests below:" -ForegroundColor Cyan
Write-Host "══════════════════════════════════════════════" -ForegroundColor Cyan