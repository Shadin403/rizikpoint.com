# Run this from an Administrator PowerShell in: D:\laragon\www\ecom3\backend
# It creates the public\storage symlink and the upload folders.
$ErrorActionPreference = "Stop"

Set-Location $PSScriptRoot

if (-not (Test-Path "storage\app\public\uploads")) {
    New-Item -ItemType Directory -Path "storage\app\public\uploads" | Out-Null
}
if (-not (Test-Path "storage\app\public\uploads\sliders")) {
    New-Item -ItemType Directory -Path "storage\app\public\uploads\sliders" | Out-Null
}

# Remove the placeholder file/dir if it exists so we can create a symlink
if (Test-Path "public\storage") {
    Remove-Item "public\storage" -Recurse -Force
}

New-Item -ItemType SymbolicLink -Path "public\storage" -Target (Resolve-Path "storage\app\public") | Out-Null

# Make sure Laravel can write to the storage folders
icacls "storage" /grant "IIS_IUSRS:(OI)(CI)M" /T | Out-Null
icacls "storage" /grant "Users:(OI)(CI)M" /T | Out-Null
icacls "bootstrap\cache" /grant "IIS_IUSRS:(OI)(CI)M" /T | Out-Null
icacls "bootstrap\cache" /grant "Users:(OI)(CI)M" /T | Out-Null

Write-Host "Storage symlink and upload folders are ready."
