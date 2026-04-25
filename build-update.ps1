# build-update.ps1
# Usage: .\build-update.ps1 -Version "1.1.0" -FromTag "v1.0.0"
param(
    [Parameter(Mandatory=$true)] [string]$Version,
    [Parameter(Mandatory=$true)] [string]$FromTag
)

$OutputDir   = ".\dist"
$PackageName = "update-v$Version"
$TempDir     = "$OutputDir\$PackageName"

$Exclude = @('.env', 'storage/', 'vendor/', 'node_modules/', 'public/storage/', 'dist/')

Write-Host "Building update package $Version from $FromTag ..." -ForegroundColor Cyan

if (Test-Path $TempDir) { Remove-Item -Recurse -Force $TempDir }
New-Item -ItemType Directory -Path $TempDir | Out-Null

# Get changed files
$ChangedRaw = git diff --name-status "$FromTag" HEAD 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "ERROR: git diff failed. Make sure tag '$FromTag' exists." -ForegroundColor Red
    exit 1
}

$FilesToInclude = @()
$MigrationFiles = @()
$DeletedFiles   = @()

foreach ($line in $ChangedRaw) {
    $line = $line.Trim()
    if (-not $line) { continue }

    $parts  = $line -split '\s+', 2
    $status = $parts[0]
    $file   = $parts[1]

    if ($status -eq 'D') {
        $DeletedFiles += $file
        continue
    }

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
    Write-Host "WARNING: No changed files found." -ForegroundColor Yellow
    exit 0
}

Write-Host "$($FilesToInclude.Count) changed files found" -ForegroundColor Green

# Copy files preserving directory structure
foreach ($file in $FilesToInclude) {
    $dest    = Join-Path $TempDir $file
    $destDir = Split-Path $dest -Parent
    if (-not (Test-Path $destDir)) {
        New-Item -ItemType Directory -Path $destDir -Force | Out-Null
    }
    Copy-Item $file $dest -Force
    Write-Host "  + $file" -ForegroundColor Gray
}

# Build manifest.json
$manifest = [ordered]@{
    version      = $Version
    from_version = ($FromTag -replace '^v', '')
    released_at  = (Get-Date -Format "yyyy-MM-dd")
    changelog    = "Update to version $Version"
    files        = $FilesToInclude
    migrations   = $MigrationFiles
    delete       = $DeletedFiles
}

$manifestJson = $manifest | ConvertTo-Json -Depth 5
[System.IO.File]::WriteAllText("$TempDir\manifest.json", $manifestJson, [System.Text.Encoding]::UTF8)
Write-Host "  + manifest.json" -ForegroundColor Gray

# Create zip
if (-not (Test-Path $OutputDir)) { New-Item -ItemType Directory -Path $OutputDir | Out-Null }
$ZipPath = "$OutputDir\$PackageName.zip"
if (Test-Path $ZipPath) { Remove-Item $ZipPath }
Compress-Archive -Path "$TempDir\*" -DestinationPath $ZipPath
Remove-Item -Recurse -Force $TempDir

Write-Host ""
Write-Host "Package ready: $ZipPath" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "  1. Upload $ZipPath to your update server"
Write-Host "  2. Update version.json on server:"
Write-Host "     { `"version`": `"$Version`", `"download_url`": `"https://yoursite.com/updates/$PackageName.zip`" }"
Write-Host "  3. Tag and push:"
Write-Host "     git tag v$Version"
Write-Host "     git push origin main --tags"

# ساخت version.json برای سرور
$serverVersion = [ordered]@{
    version      = $Version
    changelog    = "Update to version $Version"
    released_at  = (Get-Date -Format "yyyy-MM-dd")
    download_url = "https://iranbooklet.ir/harajino/$PackageName.zip"
}
$serverVersionJson = $serverVersion | ConvertTo-Json
[System.IO.File]::WriteAllText("$OutputDir\version.json", $serverVersionJson, [System.Text.Encoding]::UTF8)
Write-Host ""
Write-Host "Also upload $OutputDir\version.json to https://iranbooklet.ir/harajino/version.json" -ForegroundColor Cyan
