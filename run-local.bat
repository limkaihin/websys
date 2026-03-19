@echo off
set PHP_EXE=%~dp0php\php.exe
if not exist "%PHP_EXE%" (
  echo Bundled php.exe not found. Install PHP or restore the php folder.
  exit /b 1
)
echo Starting MeowMart on http://127.0.0.1:8000
"%PHP_EXE%" -S 127.0.0.1:8000
