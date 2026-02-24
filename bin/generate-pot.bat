@echo off
REM Generate POT file for WPSeed plugin
REM Requires WP-CLI to be installed

cd /d "%~dp0.."

wp i18n make-pot . languages/wpseed.pot ^
  --domain=wpseed ^
  --exclude=node_modules,vendor,includes/libraries,tests ^
  --headers="{\"Report-Msgid-Bugs-To\":\"https://github.com/ryanbayne/wpseed/issues\"}" ^
  --file-comment="Translation file for WPSeed plugin"

if %ERRORLEVEL% EQU 0 (
    echo POT file generated successfully at languages/wpseed.pot
) else (
    echo Error generating POT file. Make sure WP-CLI is installed.
)

pause
