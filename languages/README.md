# WPSeed Translations

This directory contains translation files for the WPSeed plugin.

## File Structure

- `wpseed.pot` - Template file containing all translatable strings
- `wpseed-{locale}.po` - Translation files for specific languages (e.g., `wpseed-es_ES.po` for Spanish)
- `wpseed-{locale}.mo` - Compiled translation files (generated from .po files)

## Generating Translation Template

To generate/update the POT file with all translatable strings:

```bash
wp i18n make-pot . languages/wpseed.pot --domain=wpseed
```

Or using the makepot command:

```bash
php wp-content/plugins/wpseed/bin/makepot.php
```

## Creating a New Translation

1. Copy `wpseed.pot` to `wpseed-{locale}.po` (e.g., `wpseed-fr_FR.po` for French)
2. Edit the .po file with a translation tool like:
   - [Poedit](https://poedit.net/) (Desktop app)
   - [Loco Translate](https://wordpress.org/plugins/loco-translate/) (WordPress plugin)
   - Any text editor
3. Compile to .mo file (Poedit does this automatically)

## Translation Tools

### Using Poedit (Recommended)
1. Download from https://poedit.net/
2. Open `wpseed.pot`
3. Create new translation from template
4. Save as `wpseed-{locale}.po`
5. Poedit automatically generates the .mo file

### Using Loco Translate Plugin
1. Install Loco Translate plugin
2. Go to Loco Translate → Plugins → WPSeed
3. Click "New language"
4. Select language and translate

### Using WP-CLI
```bash
# Generate POT file
wp i18n make-pot . languages/wpseed.pot

# Create PO file for Spanish
wp i18n make-po languages/wpseed.pot languages/wpseed-es_ES.po

# Compile MO file
wp i18n make-mo languages/
```

## Available Locales

Common locale codes:
- `en_US` - English (US)
- `es_ES` - Spanish (Spain)
- `fr_FR` - French (France)
- `de_DE` - German (Germany)
- `it_IT` - Italian (Italy)
- `pt_BR` - Portuguese (Brazil)
- `ja` - Japanese
- `zh_CN` - Chinese (Simplified)

Full list: https://make.wordpress.org/polyglots/teams/

## Contributing Translations

To contribute a translation:
1. Create the translation files
2. Test in WordPress
3. Submit via GitHub pull request to `/languages/` directory

## Notes

- The plugin text domain is `wpseed`
- All translatable strings use `__()`, `_e()`, `_n()`, `_x()`, `esc_html__()`, etc.
- Translator comments are included for context on placeholders
- POT file should be regenerated before each release
