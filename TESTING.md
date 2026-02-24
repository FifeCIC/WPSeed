# WPSeed Testing Checklist

## Recent Changes to Test

### 1. Duplicate Classes Removal
**Changed:** Removed duplicate background processing classes
**Files Deleted:**
- `includes/classes/async-request.php`
- `includes/classes/background-process.php`
- `includes/libraries/library.async-request.php`
- `includes/libraries/library.background-process.php`

**Test:**
1. Activate plugin - should work without errors
2. Check debug.log for any missing class errors
3. Verify Action Scheduler is loaded (WP Admin → Tools → Scheduled Actions)

---

### 2. Footer Debug Area
**Changed:** Added footer debug area with toolbar toggle and request tracking
**Files Created:**
- `includes/classes/footer-debug.php`

**Files Modified:**
- `toolbars/toolbar-developers.php`
- `loader.php`
- `includes/classes/listener.php` (upgraded with tracking)

**Test:**
1. Go to any admin page
2. Click "WPSeed Dev" in admin toolbar
3. Click "Footer Debug: OFF" - should toggle to ON
4. Refresh page - footer debug area should appear at bottom
5. Submit a form with POST data - verify $_POST displays
6. Visit page with URL parameters (?test=123) - verify $_GET displays
7. Check "Recent Requests" table shows last 10 requests with:
   - Time, Type (POST/GET/AJAX), URL, Status, Decision Reason
8. Toggle OFF - debug area should disappear

**Database:**
- New table: `wp_wpseed_request_log` (auto-created on first request)
- Stores: request type, URL, POST/GET data, user, IP, status, decision reason

---

## Testing Status
- [ ] Duplicate classes removal tested
- [ ] Footer debug area tested
- [ ] Request tracking and logging tested
- [ ] No PHP errors in debug.log
- [ ] Plugin activates successfully

---

### 3. Listener Documentation
**Changed:** Created comprehensive listener implementation guide
**Files Created:**
- `docs/LISTENER-PATTERNS.md`

**Test:**
1. Review documentation for completeness
2. Try basic form example from docs
3. Test AJAX pattern example
4. Verify security features work as documented

---

### 4. Listener Monitor Tool
**Changed:** Created admin page for monitoring and debugging listener activity
**Files Created:**
- `admin/page/listener-monitor.php`

**Files Modified:**
- `admin/config/admin-menus.php`

**Test:**
1. Go to WPSeed → Listener Monitor
2. View statistics dashboard (Total, Processed, Rejected, Skipped)
3. Filter requests by Type (POST/GET/AJAX)
4. Filter requests by Status (processed/rejected/skipped)
5. Click "View" on any request to see full details (POST/GET data)
6. Test "Clear All Logs" button
7. Submit a form and verify it appears in the monitor
8. Check that last 100 requests are displayed

---

### 5. POT File Generation
**Changed:** Created translation template file
**Files Created:**
- `languages/wpseed.pot`
- `bin/generate-pot.bat`

**Test:**
1. Check `languages/wpseed.pot` exists
2. Verify file contains translatable strings from plugin
3. Check file references and line numbers are included
4. Run `bin/generate-pot.bat` if WP-CLI available (optional)

---

### 6. Translation Guidelines
**Changed:** Created comprehensive translation documentation
**Files Created:**
- `docs/TRANSLATION-GUIDE.md`

**Test:**
1. Review documentation for completeness
2. Follow workflow to create test translation
3. Verify guidelines are clear and actionable

---

### 7. RTL Language Support
**Changed:** Added RTL stylesheet and testing documentation
**Files Created:**
- `assets/css/rtl.css`
- `docs/RTL-TESTING.md`

**Files Modified:**
- `assets/queue-assets.php`

**Test:**
1. Change WordPress language to Arabic or Hebrew (Settings → General)
2. Visit WPSeed admin pages
3. Verify text flows right-to-left
4. Check form alignment and button positioning
5. Test Listener Monitor table display
6. Verify footer debug area displays correctly

---

### 8. Translation Files
**Changed:** Created translation files for 10 languages
**Files Created:**
- `languages/wpseed-es_ES.po` (Spanish - partial)
- `languages/wpseed-fr_FR.po` (French - partial)
- `languages/wpseed-de_DE.po` (German - template)
- `languages/wpseed-it_IT.po` (Italian - template)
- `languages/wpseed-pt_BR.po` (Portuguese - template)
- `languages/wpseed-nl_NL.po` (Dutch - template)
- `languages/wpseed-ja.po` (Japanese - template)
- `languages/wpseed-zh_CN.po` (Chinese - template)
- `languages/wpseed-ar.po` (Arabic - template)
- `languages/wpseed-ru_RU.po` (Russian - template)
- `languages/README.md`

**Test:**
1. Check all `.po` files exist in languages folder
2. Open Spanish or French file in Poedit to verify format
3. Templates ready for community translation

---

### 9. Settings Backup & Restore
**Changed:** Added settings export/import/reset functionality
**Files Created:**
- `includes/classes/settings-backup.php`

**Files Modified:**
- `loader.php`
- `admin/config/admin-menus.php`

**Test:**
1. Go to WPSeed → Backup & Restore
2. Click "Export Settings" - downloads JSON file
3. Change some settings
4. Click "Import Settings" - upload JSON file
5. Verify settings restored
6. Click "Reset All Settings" - confirm deletion
7. Check all WPSeed settings removed
