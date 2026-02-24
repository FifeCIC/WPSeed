# Translation Priority Guide

## Overview
This document lists strings in priority order for translators. Focus on Tier 1 first for maximum impact.

---

## Tier 1: Critical (Translate First)
**Impact:** First user experience, activation, critical errors  
**Estimated:** 40 strings | ~200 words

### Setup Wizard
```
msgid "Welcome to WPSeed"
msgid "Let's get started with a quick setup"
msgid "Skip Setup"
msgid "Next Step"
msgid "Previous Step"
msgid "Complete Setup"
msgid "Step 1 of 5"
msgid "Basic Configuration"
msgid "Configure your plugin settings"
msgid "API Settings"
msgid "Enter your API key"
msgid "Test Connection"
msgid "Connection successful"
msgid "Connection failed"
msgid "Developer Mode"
msgid "Enable developer tools and debugging"
msgid "Setup Complete!"
msgid "You're all set. Start using WPSeed now."
msgid "Go to Dashboard"
```

### Activation & Errors
```
msgid "WPSeed activated successfully"
msgid "Plugin requires WordPress %s or higher"
msgid "Plugin requires PHP %s or higher"
msgid "Security check failed"
msgid "Unauthorized"
msgid "Permission denied"
msgid "Invalid request"
msgid "Error occurred"
msgid "Please try again"
msgid "Contact support if problem persists"
```

### Critical Actions
```
msgid "Save Changes"
msgid "Cancel"
msgid "Delete"
msgid "Confirm"
msgid "Are you sure?"
msgid "This action cannot be undone"
msgid "Yes, delete"
msgid "No, keep it"
```

---

## Tier 2: High Priority
**Impact:** Main navigation, common actions, settings  
**Estimated:** 60 strings | ~300 words

### Main Menu
```
msgid "WPSeed"
msgid "Dashboard"
msgid "Settings"
msgid "Development"
msgid "Notifications"
msgid "Listener Monitor"
msgid "Security Audit"
msgid "Scheduled Actions"
```

### Common Actions
```
msgid "View"
msgid "Edit"
msgid "Close"
msgid "Filter"
msgid "Search"
msgid "Export"
msgid "Import"
msgid "Reset"
msgid "Refresh"
msgid "Clear"
msgid "Apply"
msgid "Back"
```

### Settings Labels
```
msgid "General Settings"
msgid "API Configuration"
msgid "Developer Options"
msgid "Enable/Disable"
msgid "API Key"
msgid "Secret Key"
msgid "Endpoint URL"
msgid "Timeout (seconds)"
msgid "Debug Mode"
msgid "Log Level"
msgid "Cache Duration"
```

### Status Messages
```
msgid "Settings saved successfully"
msgid "Changes discarded"
msgid "Item deleted"
msgid "Action completed"
msgid "Processing..."
msgid "Loading..."
msgid "No items found"
msgid "All items"
```

---

## Tier 3: Medium Priority
**Impact:** Admin pages, help text, notifications  
**Estimated:** 80 strings | ~500 words

### Listener Monitor
```
msgid "Listener Monitor"
msgid "Total Requests"
msgid "Processed"
msgid "Rejected"
msgid "Skipped"
msgid "Request Type"
msgid "Status"
msgid "Time"
msgid "User"
msgid "IP Address"
msgid "Actions"
msgid "View Details"
msgid "Clear All Logs"
msgid "Filter by Type"
msgid "Filter by Status"
msgid "No requests logged yet"
msgid "Showing last 100 requests"
```

### Notifications
```
msgid "You have %d new notifications"
msgid "Mark as read"
msgid "Mark all as read"
msgid "Dismiss"
msgid "Notification Center"
msgid "System Alerts"
msgid "Update Notices"
msgid "No notifications"
```

### Help Text
```
msgid "Enter your API credentials here"
msgid "Enable this to see debug information"
msgid "Click to view full details"
msgid "This setting controls..."
msgid "Learn more"
msgid "Documentation"
msgid "Need help?"
```

---

## Tier 4: Low Priority
**Impact:** Developer tools, advanced features  
**Estimated:** 100+ strings

### Developer Tools
```
msgid "Footer Debug Area"
msgid "Developer Toolbar"
msgid "Demo Mode"
msgid "Reset Pointers"
msgid "Architecture Mapper"
msgid "Flow Logger"
```

### Advanced Features
```
msgid "Background Processing"
msgid "Queue Management"
msgid "Cron Jobs"
msgid "Database Optimization"
msgid "Cache Management"
```

---

## Translation Workflow

### Step 1: Tier 1 (Week 1)
1. Open your language `.po` file in Poedit
2. Search for strings listed in Tier 1
3. Translate all 40 strings
4. Save and test Setup Wizard

### Step 2: Tier 2 (Week 2)
1. Translate main menu and common actions
2. Test navigation in your language
3. Verify settings page displays correctly

### Step 3: Tier 3 (Week 3)
1. Translate admin pages
2. Test Listener Monitor and Notifications
3. Check help text clarity

### Step 4: Tier 4 (Optional)
1. Translate developer tools
2. Complete remaining strings
3. Final review and testing

---

## Quick Start for Translators

### Focus on These First (Top 20)
1. "Welcome to WPSeed"
2. "Settings"
3. "Save Changes"
4. "Cancel"
5. "Delete"
6. "View"
7. "Edit"
8. "Close"
9. "Security check failed"
10. "Unauthorized"
11. "Settings saved successfully"
12. "Error occurred"
13. "Are you sure?"
14. "Yes, delete"
15. "No, keep it"
16. "Loading..."
17. "Processing..."
18. "No items found"
19. "Dashboard"
20. "Documentation"

**These 20 strings appear most frequently and cover critical user interactions.**

---

## Testing Priority Translations

### Test Tier 1
1. Activate plugin in your language
2. Run through Setup Wizard
3. Trigger an error message
4. Verify all text displays correctly

### Test Tier 2
1. Navigate all menu items
2. Open Settings page
3. Click common action buttons
4. Check status messages

### Test Tier 3
1. Visit Listener Monitor
2. Check Notifications
3. Read help text
4. Verify tooltips

---

## Completion Tracking

Mark your progress:

- [ ] Tier 1: Critical (40 strings)
- [ ] Tier 2: High Priority (60 strings)
- [ ] Tier 3: Medium Priority (80 strings)
- [ ] Tier 4: Low Priority (100+ strings)

**Target:** Complete Tier 1 + Tier 2 = 100 strings for functional translation

---

## Notes for Translators

### Context Matters
Some strings appear in multiple places:
- "View" - button label, menu item, action
- "Settings" - page title, menu item, section header
- "Delete" - button, confirmation, action

Translate consistently across all uses.

### Placeholders
Keep these intact:
- `%s` - string placeholder
- `%d` - number placeholder
- `%1$s`, `%2$s` - ordered placeholders

Example:
```
msgid "You have %d notifications"
msgstr "Tienes %d notificaciones"
```

### HTML Tags
Preserve all HTML:
```
msgid "Click <strong>here</strong>"
msgstr "Haz clic <strong>aquí</strong>"
```

---

## Support

Questions about priority strings?
- GitHub Issues: https://github.com/ryanbayne/wpseed/issues
- Tag: `translation`, `priority`

---

**Last Updated:** 2024  
**Total Strings:** ~280 across all tiers  
**Recommended Focus:** Tier 1 + Tier 2 = 100 strings
