@echo off
REM Generate WPSeed translation template (POT file)
REM Requires WP-CLI to be installed

echo Generating WPSeed translation template...
echo.

cd /d "%~dp0.."

wp i18n make-pot . languages/wpseed.pot --domain=wpseed --exclude=node_modules,vendor,tests,bin

if %ERRORLEVEL% EQU 0 (
    echo.
    echo Success! POT file generated at languages/wpseed.pot
    echo.
    echo Next steps:
    echo 1. Use Poedit to create translations from the POT file
    echo 2. Or use: wp i18n make-po languages/wpseed.pot languages/wpseed-LOCALE.po
    echo.
) else (
    echo.
    echo Error: Failed to generate POT file
    echo Make sure WP-CLI is installed: https://wp-cli.org/
    echo.
)

pause
