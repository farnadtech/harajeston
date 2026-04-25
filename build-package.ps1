# build-package.ps1
# ساخت پکیج نصبی کامل حراجینو
# Usage: .\build-package.ps1 -Version "1.0.0"

param(
    [Parameter(Mandatory=$true)]
    [string]$Version
)

$PackageName = "harajino-v$Version"
$OutputDir   = ".\dist"
$TempDir     = "$OutputDir\$PackageName"

# پوشه‌ها و فایل‌هایی که نباید در پکیج باشن
$Exclude = @(
    '.git',
    '.kiro',
    '.qoder',
    '.vscode',
    'node_modules',
    'storage\logs',
    'storage\framework\cache',
    'storage\framework\sessions',
    'storage\framework\views',
    'storage\backups',
    'storage\updates',
    'dist',
    'tests',
    '.env',
    'installed.lock',
    'build-update.ps1',
    'build-package.ps1',
    'UPDATE_GUIDE.md',
    'convert-to-blade.py'
)

Write-Host "Building installation package v$Version ..." -ForegroundColor Cyan

# پاک کردن temp قبلی
if (Test-Path $TempDir) { Remove-Item -Recurse -Force $TempDir }
New-Item -ItemType Directory -Path $TempDir | Out-Null

# کپی فایل‌ها
Write-Host "Copying files..." -ForegroundColor Gray

Get-ChildItem -Path "." -Recurse | ForEach-Object {
    $relativePath = $_.FullName.Substring((Get-Location).Path.Length + 1)

    # چک exclude
    $skip = $false
    foreach ($ex in $Exclude) {
        if ($relativePath.StartsWith($ex) -or $relativePath -eq $ex) {
            $skip = $true
            break
        }
    }
    if ($skip) { return }

    # فایل‌های storage رو فقط ساختار پوشه کپی کن
    if ($relativePath.StartsWith('storage\') -and $_.PSIsContainer) {
        $dest = Join-Path $TempDir $relativePath
        if (-not (Test-Path $dest)) {
            New-Item -ItemType Directory -Path $dest -Force | Out-Null
        }
        return
    }
    if ($relativePath.StartsWith('storage\') -and -not $_.PSIsContainer) {
        # فقط .gitignore های داخل storage رو کپی کن
        if ($_.Name -ne '.gitignore') { return }
    }

    if ($_.PSIsContainer) {
        $dest = Join-Path $TempDir $relativePath
        if (-not (Test-Path $dest)) {
            New-Item -ItemType Directory -Path $dest -Force | Out-Null
        }
    } else {
        $dest    = Join-Path $TempDir $relativePath
        $destDir = Split-Path $dest -Parent
        if (-not (Test-Path $destDir)) {
            New-Item -ItemType Directory -Path $destDir -Force | Out-Null
        }
        Copy-Item $_.FullName $dest -Force
    }
}

# اطمینان از وجود پوشه‌های ضروری storage
$storageDirs = @(
    'storage\app\public',
    'storage\framework\cache\data',
    'storage\framework\sessions',
    'storage\framework\views',
    'storage\logs',
    'storage\backups',
    'storage\updates',
    'bootstrap\cache'
)
foreach ($dir in $storageDirs) {
    $dest = Join-Path $TempDir $dir
    if (-not (Test-Path $dest)) {
        New-Item -ItemType Directory -Path $dest -Force | Out-Null
    }
    # .gitignore برای نگه داشتن پوشه خالی
    $gi = Join-Path $dest '.gitignore'
    if (-not (Test-Path $gi)) {
        Set-Content $gi "*`n!.gitignore"
    }
}

# کپی install.php به root پکیج (کنار script.zip)
Copy-Item "install.php" "$OutputDir\install.php" -Force

Write-Host "Files copied." -ForegroundColor Green

# ساخت zip با .NET
Write-Host "Creating zip..." -ForegroundColor Gray
if (-not (Test-Path $OutputDir)) { New-Item -ItemType Directory -Path $OutputDir | Out-Null }
$ZipPath = "$OutputDir\$PackageName.zip"
if (Test-Path $ZipPath) { Remove-Item $ZipPath }

Add-Type -AssemblyName System.IO.Compression.FileSystem
$zipStream = [System.IO.Compression.ZipFile]::Open($ZipPath, [System.IO.Compression.ZipArchiveMode]::Create)
$TempDirFull = (Resolve-Path $TempDir).Path.TrimEnd('\') + '\'

foreach ($file in (Get-ChildItem -Path $TempDir -Recurse -File)) {
    $entryName = $file.FullName.Substring($TempDirFull.Length) -replace '\\', '/'
    [System.IO.Compression.ZipFileExtensions]::CreateEntryFromFile($zipStream, $file.FullName, $entryName) | Out-Null
}
$zipStream.Dispose()

# پاک کردن temp
Remove-Item -Recurse -Force $TempDir

Write-Host ""
Write-Host "Package ready!" -ForegroundColor Green
Write-Host ""
Write-Host "Files in dist/:" -ForegroundColor Yellow
Write-Host "  install.php        <- installer (upload to server root)"
Write-Host "  $PackageName.zip   <- script files (upload to server root)"
Write-Host ""
Write-Host "Instructions for customer:" -ForegroundColor Cyan
Write-Host "  1. Upload install.php to server root"
Write-Host "  2. Upload $PackageName.zip to server root"
Write-Host "  3. Open https://yourdomain.com/install.php"
Write-Host "  4. Follow the wizard"
Write-Host "  5. Delete install.php after installation"
