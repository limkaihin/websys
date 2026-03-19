$php = Join-Path $PSScriptRoot 'php\php.exe'
if (!(Test-Path $php)) {
  Write-Host 'Bundled php.exe not found. Install PHP or restore the php folder.' -ForegroundColor Red
  exit 1
}
Write-Host 'Starting MeowMart on http://127.0.0.1:8000' -ForegroundColor Green
& $php -S 127.0.0.1:8000
