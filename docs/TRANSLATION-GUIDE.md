# Translation Guidelines

## Overview
WPSeed is translation-ready and welcomes community translations. This guide helps translators contribute effectively.

---

## Getting Started

### Prerequisites
- Text editor or translation tool (Poedit recommended)
- Basic understanding of WordPress i18n
- Familiarity with target language

### Translation Tools
- **Poedit** (Recommended): https://poedit.net/
- **Loco Translate** (WordPress plugin): https://wordpress.org/plugins/loco-translate/
- **GlotPress** (Online): Coming soon

---

## Translation Workflow

### 1. Get the POT File
Download or locate: `languages/wpseed.pot`

### 2. Create Translation File
Using Poedit:
1. Open Poedit
2. File → New from POT/PO file
3. Select `wpseed.pot`
4. Choose your language (e.g., Spanish - es_ES)
5. Save as `wpseed-es_ES.po`

### 3. Translate Strings
- Translate each msgid to your language
- Keep placeholders intact (%s, %d, %1$s, etc.)
- Maintain HTML tags and formatting
- Consider context comments

### 4. Generate MO File
Poedit automatically generates `.mo` file on save.

### 5. Test Translation
1. Copy `.po` and `.mo` files to `wp-content/plugins/wpseed/languages/`
2. Change WordPress language in Settings → General
3. Verify translations appear correctly

### 6. Submit Translation
- Fork repository on GitHub
- Add translation files to `languages/` folder
- Create pull request with description

---

## Translation Best Practices

### Placeholders
Keep placeholders in correct order:
```
# English
msgid "Hello %s, you have %d messages"

# Spanish - Correct
msgstr "Hola %s, tienes %d mensajes"

# Wrong - Don't change placeholder order without reason
msgstr "Tienes %d mensajes, hola %s"
```

### HTML Tags
Preserve all HTML:
```
# English
msgid "Click <strong>here</strong> to continue"

# Correct
msgstr "Haz clic <strong>aquí</strong> para continuar"
```

### Context
Use context comments to understand usage:
```
#: admin/page/listener-monitor.php:120
#. Refers to timestamp column header
msgid "Time"
msgstr "Tiempo"
```

### Plurals
Handle plural forms correctly:
```
msgid "1 item"
msgid_plural "%d items"
msgstr[0] "1 elemento"
msgstr[1] "%d elementos"
```

### Consistency
- Use consistent terminology throughout
- Match WordPress core translations when possible
- Create glossary for technical terms

---

## Common Strings

### Admin Interface
- "Settings" → Use WordPress core translation
- "Save Changes" → Use WordPress core translation
- "Delete" → Use WordPress core translation

### Status Messages
- "Success" → Standard success message
- "Error" → Standard error message
- "Warning" → Standard warning message

### Actions
- "View" → Viewing action
- "Edit" → Editing action
- "Delete" → Deletion action

---

## Language-Specific Guidelines

### Spanish (es_ES)
- Use formal "usted" form for admin interface
- "tú" form acceptable for user-facing content
- Follow RAE spelling guidelines

### French (fr_FR)
- Use formal "vous" form
- Respect gender agreements
- Follow Académie française guidelines

### German (de_DE)
- Use formal "Sie" form
- Capitalize nouns
- Use ß where appropriate

### RTL Languages (Arabic, Hebrew)
- Text direction handled automatically
- Test layout carefully
- Report any RTL-specific issues

---

## File Naming Convention

Format: `wpseed-{locale}.po`

Examples:
- Spanish (Spain): `wpseed-es_ES.po`
- Spanish (Mexico): `wpseed-es_MX.po`
- French (France): `wpseed-fr_FR.po`
- German: `wpseed-de_DE.po`
- Portuguese (Brazil): `wpseed-pt_BR.po`

---

## Testing Checklist

- [ ] All strings translated (no empty msgstr)
- [ ] Placeholders preserved correctly
- [ ] HTML tags intact
- [ ] Plural forms handled
- [ ] No encoding issues (UTF-8)
- [ ] MO file generated
- [ ] Tested in WordPress admin
- [ ] Tested on frontend (if applicable)
- [ ] No layout breaking
- [ ] Special characters display correctly

---

## Translation Priority

### High Priority (Core Functionality)
1. Admin menu items
2. Settings page labels
3. Error messages
4. Success messages
5. Form labels and buttons

### Medium Priority (User Interface)
1. Help text
2. Tooltips
3. Dashboard widgets
4. Notification messages

### Low Priority (Documentation)
1. README content
2. Code comments (not translated)
3. Developer documentation

---

## Updating Translations

When plugin updates:
1. Download new POT file
2. Open your PO file in Poedit
3. Catalog → Update from POT file
4. Translate new strings
5. Review fuzzy translations
6. Save and test

---

## Translation Tools Setup

### Poedit Configuration
1. Edit → Preferences
2. Set your name and email
3. Enable automatic compilation
4. Configure translation memory

### Loco Translate (WordPress)
1. Install Loco Translate plugin
2. Go to Loco Translate → Plugins
3. Select WPSeed
4. Click "New language"
5. Choose language and location
6. Start translating

---

## Common Issues

### Issue: Translations Not Showing
**Solutions:**
1. Check file names match locale exactly
2. Verify MO file exists
3. Clear WordPress cache
4. Check WordPress language setting
5. Ensure files in correct directory

### Issue: Broken Layout
**Solutions:**
1. Check for missing HTML tags
2. Verify placeholder positions
3. Test with shorter/longer text
4. Report layout issues on GitHub

### Issue: Special Characters
**Solutions:**
1. Ensure UTF-8 encoding
2. Use proper character entities
3. Test in different browsers
4. Check font support

---

## Glossary Template

Create language-specific glossary:

| English | Translation | Notes |
|---------|-------------|-------|
| Settings | [Your translation] | Use WP core |
| Dashboard | [Your translation] | Admin area |
| Listener | [Your translation] | Technical term |
| Monitor | [Your translation] | Verb or noun? |
| Debug | [Your translation] | Keep technical? |

---

## Resources

### WordPress i18n
- [WordPress Translator Handbook](https://make.wordpress.org/polyglots/handbook/)
- [WordPress Language Packs](https://translate.wordpress.org/)
- [i18n for Developers](https://developer.wordpress.org/plugins/internationalization/)

### Translation Tools
- [Poedit](https://poedit.net/)
- [Loco Translate](https://localise.biz/wordpress/plugin)
- [GlotPress](https://glotpress.blog/)

### Language Resources
- [Locale Codes](https://wpastra.com/docs/complete-list-wordpress-locale-codes/)
- [Plural Forms](https://developer.wordpress.org/reference/functions/translate_plural/)

---

## Contributing

### Submit Translation
1. Fork: https://github.com/ryanbayne/wpseed
2. Add files to `languages/` folder
3. Create pull request
4. Include:
   - Language name and locale
   - Translator name (optional)
   - Translation percentage
   - Any notes or issues

### Translation Credits
Translators will be credited in:
- Plugin credits page
- README.md
- Release notes

---

## Support

### Questions?
- GitHub Issues: https://github.com/ryanbayne/wpseed/issues
- GitHub Discussions: https://github.com/ryanbayne/wpseed/discussions
- Tag: `translation`

### Report Issues
- Missing strings
- Context needed
- Technical term clarification
- Layout problems with translation

---

**Thank you for helping make WPSeed accessible to more users!**

Last Updated: 2025
