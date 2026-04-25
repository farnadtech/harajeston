# ============================================================
# build-update.ps1 — ساخت پکیج آپدیت از روی git diff
# استفاده: .\build-update.ps1 -Version "1.2.0" -FromTag "v1.1.0"
# ============================================================
param(
    [Parameter(Mandatory=$true)]
    [string]$Version,

    [Parameter(Mandatory=$true)]
    [string]$FromTag
)

$OutputDir  = ".\dist"
$PackageName = "update-v$Version"
$TempDir    = "$OutputDir\$PackageName"

# فایل‌هایی که هیچ‌وقت توی آپدیت نباید باشن
$Exclude = @('.env', 'storage/', 'vendor/', 'node_modules/', 'public/storage/')

Write-Host "📦 ساخت پکیج آپدیت $Version از $FromTag ..." -ForegroundColor Cyan

# پاک کردن temp قبلی
if (Test-Path $TempDir) { Remove-Item -Recurse -Force $TempDir }
New-Item -ItemType Directory -Path $TempDir | Out-Null

# گرفتن لیست فایل‌های تغییر کرده
$ChangedFiles = git diff --name-only $FromTag HEAD 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ خطا در git diff. مطمئن شو tag '$FromTag' وجود داره." -ForegroundColor Red
    exit 1
}

$FilesToInclude = @()
$MigrationFiles = @()
$DeletedFiles   = @()

foreach ($file in $ChangedFiles) {
    $file = $file.Trim()
    if (-not $file) { continue }

    # چک حذف شده‌ها
    $status = git diff --name-status $FromTag HEAD -- $file | Select-String "^D"
    if ($status) {
        $DeletedFiles += $file
        continue
    }

    # چک exclude
    $skip = $false
    foreach ($ex in $Exclude) {
        if ($file.StartsWith($ex) -or $file -eq $ex) { $skip = $true; break }
    }
    if ($skip) { continue }

    if (-not (Test-Path $file)) { continue }

    $FilesToInclude += $file

    if ($file -like "database/migrations/*") {
        $MigrationFiles += $file
    }
}

if ($FilesToInclude.Count -eq 0) {
    Write-Host "⚠️  هیچ فایلی برای آپدیت پیدا نشد." -ForegroundColor Yellow
    exit 0
}

Write-Host "✅ $($FilesToInclude.Count) فایل تغییر کرده پیدا شد" -ForegroundColor Green

# کپی فایل‌ها با حفظ ساختار پوشه
foreach ($file in $FilesToInclude) {
    $dest = Join-Path $TempDir $file
    $destDir = Split-Path $dest -Parent
    if (-not (Test-Path $destDir)) {
        New-Item -ItemType Directory -Path $destDir -Force | Out-Null
    }
    Copy-Item $file $dest -Force
    Write-Host "  + $file" -ForegroundColor Gray
}

# ساخت manifest.json
$manifest = @{
    version      = $Version
    from_version = $FromTag -replace '^v', ''
    released_at  = (Get-Date -Format "yyyy-MM-dd")
    changelog    = "آپدیت نسخه $Version"
    files        = $FilesToInclude
    migrations   = $MigrationFiles
    delete       = $DeletedFiles
} | ConvertTo-Json -Depth 5

$manifest | Set-Content "$TempDir\manifest.json" -Encoding UTF8
Write-Host "  + manifest.json" -ForegroundColor Gray

# ساخت zip
if (-not (Test-Path $OutputDir)) { New-Item -ItemType Directory -Path $OutputDir | Out-Null }
$ZipPath = "$OutputDir\$PackageName.zip"
if (Test-Path $ZipPath) { Remove-Item $ZipPath }
Compress-Archive -Path "$TempDir\*" -DestinationPath $ZipPath
Remove-Item -Recurse -Force $TempDir

Write-Host ""
Write-Host "🎉 پکیج آماده شد: $ZipPath" -ForegroundColor Green
Write-Host ""
Write-Host "📋 مراحل بعدی:" -ForegroundColor Yellow
Write-Host "  1. $ZipPath رو روی سرور آپدیتت آپلود کن"
Write-Host "  2. version.json رو آپدیت کن:"
Write-Host "     { `"version`": `"$Version`", `"download_url`": `"https://yoursite.com/updates/$PackageName.zip`" }"
Write-Host "  3. تگ جدید بزن: git tag v$Version && git push --tags"
