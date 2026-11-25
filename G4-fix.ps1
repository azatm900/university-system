$ErrorActionPreference = "Continue"
$projectRoot = "C:\Users\maham\Herd\university-system"
Set-Location $projectRoot

Write-Host "=== G4-fix v2 ===" -ForegroundColor Cyan

$arDashboard = Join-Path $projectRoot "lang\ar\dashboard.php"
$enDashboard = Join-Path $projectRoot "lang\en\dashboard.php"
$arCommon    = Join-Path $projectRoot "lang\ar\common.php"
$enCommon    = Join-Path $projectRoot "lang\en\common.php"
$dashView    = Join-Path $projectRoot "resources\views\dashboard\index.blade.php"

$missing = @()
if (-not (Test-Path $arDashboard)) { $missing += $arDashboard }
if (-not (Test-Path $enDashboard)) { $missing += $enDashboard }
if ($missing.Count -gt 0) {
    Write-Host "Missing files:" -ForegroundColor Red
    foreach ($m in $missing) { Write-Host "  - $m" -ForegroundColor Red }
    return
}
Write-Host "[OK] core files exist" -ForegroundColor Green

$arBak = "$arDashboard.bak-g4fix"
$enBak = "$enDashboard.bak-g4fix"
if (-not (Test-Path $arBak)) { Copy-Item $arDashboard $arBak -Force; Write-Host "[OK] Backup AR" -ForegroundColor Green } else { Write-Host "[i] Backup AR exists" -ForegroundColor Yellow }
if (-not (Test-Path $enBak)) { Copy-Item $enDashboard $enBak -Force; Write-Host "[OK] Backup EN" -ForegroundColor Green } else { Write-Host "[i] Backup EN exists" -ForegroundColor Yellow }

Write-Host "--- AR: merge stats ---" -ForegroundColor Cyan
$arContent = [IO.File]::ReadAllText($arDashboard, [Text.Encoding]::UTF8)
if ($arContent -match "'stats'\s*=>") {
    Write-Host "[i] 'stats' already in AR - skip" -ForegroundColor Yellow
} else {
    $arLines = @(
        "",
        "    // G4-fix: dashboard stats keys",
        "    'stats' => [",
        "        'users'           => 'المستخدمون',",
        "        'students'        => 'الطلاب',",
        "        'active_students' => 'طلاب نشطون',",
        "        'courses'         => 'المقررات',",
        "        'programs'        => 'البرامج',",
        "        'registrations'   => 'التسجيلات',",
        "        'unpaid_invoices' => 'فواتير غير مدفوعة',",
        "        'paid_invoices'   => 'فواتير مدفوعة',",
        "        'payments_total'  => 'إجمالي المدفوعات',",
        "        'invoices_total'  => 'إجمالي الفواتير',",
        "    ],"
    )
    $arStatsBlock = ($arLines -join "`r`n") + "`r`n"
    $arNewContent = $arContent -replace "(?s)(\r?\n)(\]\s*;\s*)$", "`$1$arStatsBlock`$1`$2"
    if ($arNewContent -eq $arContent) {
        Write-Host "[!] AR replace failed" -ForegroundColor Yellow
    } else {
        [IO.File]::WriteAllText($arDashboard, $arNewContent, (New-Object System.Text.UTF8Encoding($false)))
        Write-Host "[OK] AR merged" -ForegroundColor Green
    }
}

Write-Host "--- EN: merge stats ---" -ForegroundColor Cyan
$enContent = [IO.File]::ReadAllText($enDashboard, [Text.Encoding]::UTF8)
if ($enContent -match "'stats'\s*=>") {
    Write-Host "[i] 'stats' already in EN - skip" -ForegroundColor Yellow
} else {
    $enLines = @(
        "",
        "    // G4-fix: dashboard stats keys",
        "    'stats' => [",
        "        'users'           => 'Users',",
        "        'students'        => 'Students',",
        "        'active_students' => 'Active Students',",
        "        'courses'         => 'Courses',",
        "        'programs'        => 'Programs',",
        "        'registrations'   => 'Registrations',",
        "        'unpaid_invoices' => 'Unpaid Invoices',",
        "        'paid_invoices'   => 'Paid Invoices',",
        "        'payments_total'  => 'Payments Total',",
        "        'invoices_total'  => 'Invoices Total',",
        "    ],"
    )
    $enStatsBlock = ($enLines -join "`r`n") + "`r`n"
    $enNewContent = $enContent -replace "(?s)(\r?\n)(\]\s*;\s*)$", "`$1$enStatsBlock`$1`$2"
    if ($enNewContent -eq $enContent) {
        Write-Host "[!] EN replace failed" -ForegroundColor Yellow
    } else {
        [IO.File]::WriteAllText($enDashboard, $enNewContent, (New-Object System.Text.UTF8Encoding($false)))
        Write-Host "[OK] EN merged" -ForegroundColor Green
    }
}

Write-Host "--- syntax check ---" -ForegroundColor Cyan
$arLint = (& php -l $arDashboard 2>&1) -join " "
$enLint = (& php -l $enDashboard 2>&1) -join " "
Write-Host "AR: $arLint" -ForegroundColor Gray
Write-Host "EN: $enLint" -ForegroundColor Gray
$arFail = $arLint -match "Parse error"
$enFail = $enLint -match "Parse error"
if ($arFail -or $enFail) {
    Write-Host "[X] syntax failed - rollback" -ForegroundColor Red
    if (Test-Path $arBak) { Copy-Item $arBak $arDashboard -Force }
    if (Test-Path $enBak) { Copy-Item $enBak $enDashboard -Force }
    return
}
Write-Host "[OK] syntax ok" -ForegroundColor Green

Write-Host "--- common.view check ---" -ForegroundColor Cyan
if (-not (Test-Path $dashView)) {
    Write-Host "[!] dashboard/index missing" -ForegroundColor Yellow
} else {
    $viewContent = [IO.File]::ReadAllText($dashView, [Text.Encoding]::UTF8)
    $usesCommonView = $viewContent -match "__\(\s*'common\.view'\s*\)"
    if (-not $usesCommonView) {
        Write-Host "[i] common.view not used" -ForegroundColor Gray
    } else {
        $arHasView = $false
        $enHasView = $false
        if (Test-Path $arCommon) {
            $c = [IO.File]::ReadAllText($arCommon, [Text.Encoding]::UTF8)
            if ($c -match "'view'\s*=>") { $arHasView = $true }
        }
        if (Test-Path $enCommon) {
            $c = [IO.File]::ReadAllText($enCommon, [Text.Encoding]::UTF8)
            if ($c -match "'view'\s*=>") { $enHasView = $true }
        }
        if ($arHasView -and $enHasView) {
            Write-Host "[OK] 'view' exists in common.php" -ForegroundColor Green
        } else {
            Write-Host "[!] 'view' missing - replace with common.edit" -ForegroundColor Yellow
            $viewBak = "$dashView.bak-g4fix"
            if (-not (Test-Path $viewBak)) { Copy-Item $dashView $viewBak -Force }
            $newView = $viewContent -replace "__\(\s*'common\.view'\s*\)", "__('common.edit')"
            [IO.File]::WriteAllText($dashView, $newView, (New-Object System.Text.UTF8Encoding($false)))
            Write-Host "[OK] replaced" -ForegroundColor Green
        }
    }
}

Write-Host "--- cache clear ---" -ForegroundColor Cyan
$vc = (& php artisan view:clear 2>&1) -join " "
$cc = (& php artisan config:clear 2>&1) -join " "
Write-Host "view:clear   -> $vc" -ForegroundColor Gray
Write-Host "config:clear -> $cc" -ForegroundColor Gray

Write-Host "--- final checks ---" -ForegroundColor Cyan
$arFinal = [IO.File]::ReadAllText($arDashboard, [Text.Encoding]::UTF8)
$enFinal = [IO.File]::ReadAllText($enDashboard, [Text.Encoding]::UTF8)
$checks = @(
    @{ N = "AR stats";           Ok = ($arFinal -match "'stats'\s*=>") },
    @{ N = "AR users";           Ok = ($arFinal -match "'users'") },
    @{ N = "AR payments_total";  Ok = ($arFinal -match "'payments_total'") },
    @{ N = "EN stats";           Ok = ($enFinal -match "'stats'\s*=>") },
    @{ N = "EN users";           Ok = ($enFinal -match "'users'") },
    @{ N = "EN invoices_total";  Ok = ($enFinal -match "'invoices_total'") }
)
$allOk = $true
foreach ($c in $checks) {
    if ($c.Ok) { Write-Host "[OK] $($c.N)" -ForegroundColor Green }
    else { Write-Host "[X] $($c.N)" -ForegroundColor Red; $allOk = $false }
}

Write-Host ""
Write-Host "=== Verify Checklist ===" -ForegroundColor Cyan
Write-Host "php artisan serve -> http://127.0.0.1:8000"
Write-Host "[1] admin@demo.local    -> 4 cards: Users / Students / Unpaid / Payments Total"
Write-Host "                           sidebar: Home / My Info / Admin / Reports"
Write-Host "[2] registrar@demo.local -> Students / Active / Programs / Registrations"
Write-Host "[3] finance@demo.local   -> Unpaid / Paid / Payments Total / Invoices Total"
Write-Host "[4] results@demo.local   -> Students / Courses / Registrations / Programs"
Write-Host "[5] footer: name + email + role (3 lines)"
Write-Host "[6] My Info -> /profile (G1)"

if ($allOk) {
    Write-Host "=== G4-fix DONE ===" -ForegroundColor Green
} else {
    Write-Host "=== [!] some checks failed ===" -ForegroundColor Yellow
}