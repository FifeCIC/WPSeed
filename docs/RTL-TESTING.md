# RTL Language Support Testing

## Overview
WPSeed includes RTL (Right-to-Left) language support for Arabic, Hebrew, and other RTL languages.

---

## Testing RTL Support

### 1. Enable RTL Language
**Option A: Change WordPress Language**
1. Go to Settings → General
2. Change "Site Language" to Arabic (العربية) or Hebrew (עברית)
3. Save changes
4. WordPress will download language files

**Option B: Force RTL Mode (Testing)**
Add to `wp-config.php`:
```php
define('WP_LANG', 'ar');
define('WPLANG', 'ar');
```

### 2. Test Admin Interface
Visit WPSeed admin pages and verify:
- [ ] Text flows right-to-left
- [ ] Form labels align to the right
- [ ] Buttons positioned correctly
- [ ] Tables display properly
- [ ] Icons have correct spacing
- [ ] Notifications align right
- [ ] Footer debug area displays correctly
- [ ] Listener monitor table is readable

### 3. Test Specific Pages
- [ ] WPSeed → Development
- [ ] WPSeed → Listener Monitor
- [ ] WPSeed → Notifications
- [ ] Settings pages
- [ ] Footer debug area

### 4. Check for Issues
Common RTL problems:
- Text alignment incorrect
- Icons on wrong side
- Margins/padding reversed incorrectly
- Tables not readable
- Overlapping elements
- Broken layouts

---

## RTL CSS Classes

WPSeed automatically applies `.rtl` class to body when RTL language is active.

### Custom RTL Styles
File: `assets/css/rtl.css`

Add RTL-specific styles:
```css
.rtl .your-element {
    text-align: right;
    direction: rtl;
}
```

---

## WordPress RTL Functions

### Check if RTL
```php
if (is_rtl()) {
    // RTL-specific code
}
```

### Load RTL Stylesheet
WordPress automatically loads `style-rtl.css` if it exists.

---

## Testing Checklist

### Visual Testing
- [ ] Text direction correct
- [ ] Alignment proper
- [ ] No overlapping elements
- [ ] Icons positioned correctly
- [ ] Forms usable
- [ ] Tables readable
- [ ] Notifications display properly

### Functional Testing
- [ ] Forms submit correctly
- [ ] Buttons work
- [ ] Links clickable
- [ ] Dropdowns function
- [ ] Modals display properly
- [ ] AJAX requests work

### Browser Testing
Test in multiple browsers:
- [ ] Chrome
- [ ] Firefox
- [ ] Safari
- [ ] Edge

---

## Supported RTL Languages

### Fully Supported
- Arabic (ar)
- Hebrew (he)
- Persian/Farsi (fa)
- Urdu (ur)

### Partially Supported
Any RTL language WordPress supports will work with basic RTL styles.

---

## Reporting RTL Issues

When reporting RTL bugs, include:
1. Language and locale (e.g., ar, he_IL)
2. WordPress version
3. Browser and version
4. Screenshot showing issue
5. Page/section affected
6. Steps to reproduce

Submit to: https://github.com/ryanbayne/wpseed/issues
Tag: `rtl`, `i18n`

---

## Developer Notes

### Adding RTL Support to Custom Elements
```css
/* LTR (default) */
.my-element {
    margin-left: 10px;
}

/* RTL override */
.rtl .my-element {
    margin-left: 0;
    margin-right: 10px;
}
```

### Logical Properties (Modern Approach)
```css
/* Works for both LTR and RTL */
.my-element {
    margin-inline-start: 10px;
}
```

---

## Resources

- [WordPress RTL Handbook](https://make.wordpress.org/polyglots/handbook/translating/rtl-languages/)
- [RTL Styling Guide](https://rtlstyling.com/)
- [CSS Logical Properties](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_Logical_Properties)

---

Last Updated: 2025
