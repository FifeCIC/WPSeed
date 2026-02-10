# Carbon Fields Fix Plan

**Status**: CRITICAL - Intended Feature Not Working  
**Priority**: HIGH  
**Issue**: Missing Pimple dependency container

---

## 🎯 Background

Carbon Fields was intentionally integrated into WPSeed as a core feature for modern settings management. It's NOT optional - it's a key selling point of the boilerplate.

**What We Built:**
- ✅ Carbon Fields integration wrapper class
- ✅ Documentation (CARBON-FIELDS.md)
- ✅ Example implementations
- ✅ Registered in asset system
- ❌ **Missing**: Complete Carbon Fields library with dependencies

---

## 🐛 The Problem

**Current State:**
- Downloaded Carbon Fields core files only
- Missing Pimple dependency (required by Carbon Fields)
- Fatal error on plugin load

**Error:**
```
Class "Carbon_Fields\Pimple\Container" not found
```

**Location:**
```
includes/libraries/carbon-fields/core/Carbon_Fields.php:313
```

---

## ✅ Solution Options

### Option 1: Download Complete Package (RECOMMENDED)
**Download from official source with all dependencies included**

1. Go to: https://github.com/htmlburger/carbon-fields/releases
2. Download latest release (v3.6+)
3. Extract to: `includes/libraries/carbon-fields/`
4. Ensure `vendor/` folder is included (contains Pimple)

**Pros:**
- Complete package
- All dependencies included
- No Composer required
- Works immediately

**Cons:**
- Larger file size
- Manual download

---

### Option 2: Use Composer (Alternative)
**Install via Composer with dependencies**

```bash
cd includes/libraries/
composer require htmlburger/carbon-fields
```

**Pros:**
- Automatic dependency resolution
- Easy updates
- Standard approach

**Cons:**
- Requires Composer on server
- Users need to run composer install
- Against WPSeed philosophy (no external dependencies)

---

### Option 3: Manual Pimple Installation
**Download Pimple separately and add to Carbon Fields**

1. Download Pimple: https://github.com/silexphp/Pimple
2. Place in: `includes/libraries/carbon-fields/vendor/pimple/`
3. Update autoloader

**Pros:**
- Minimal addition
- Keeps Carbon Fields structure

**Cons:**
- Manual work
- May miss other dependencies
- Fragile

---

## 🎯 Recommended Action: Option 1

### Step-by-Step Fix

1. **Backup Current Files**
   ```
   Rename: includes/libraries/carbon-fields/
   To: includes/libraries/carbon-fields-incomplete/
   ```

2. **Download Complete Package**
   - Visit: https://github.com/htmlburger/carbon-fields/releases/latest
   - Download: `carbon-fields-vX.X.X.zip`
   - Extract to: `includes/libraries/carbon-fields/`

3. **Verify Structure**
   ```
   includes/libraries/carbon-fields/
   ├── core/
   │   └── Carbon_Fields.php
   ├── vendor/
   │   ├── pimple/
   │   └── autoload.php
   └── carbon-fields-plugin.php
   ```

4. **Update Loader**
   ```php
   // In loader.php, change from:
   require_once plugin_dir_path(__FILE__) . 'includes/libraries/carbon-fields/core/Carbon_Fields.php';
   
   // To:
   require_once plugin_dir_path(__FILE__) . 'includes/libraries/carbon-fields/vendor/autoload.php';
   \Carbon_Fields\Carbon_Fields::boot();
   ```

5. **Re-enable in loader.php**
   - Remove the comment block
   - Uncomment Carbon Fields loading code

6. **Test**
   - Deactivate/reactivate plugin
   - Check for errors
   - Test Carbon Fields examples

---

## 📋 Testing Checklist

After fixing:
- [ ] Plugin activates without errors
- [ ] No fatal errors in debug.log
- [ ] Carbon Fields boots successfully
- [ ] Example settings page works
- [ ] Can create theme options
- [ ] Can save/retrieve values
- [ ] Documentation examples work

---

## 🔄 Alternative: Temporary Workaround

**If immediate fix not possible:**

Keep Carbon Fields disabled but update documentation:

1. Update README.md:
   ```markdown
   ### Carbon Fields Setup Required
   Carbon Fields requires manual installation:
   1. Download from: https://github.com/htmlburger/carbon-fields/releases
   2. Extract to: includes/libraries/carbon-fields/
   3. Reactivate plugin
   ```

2. Add admin notice:
   ```php
   if (!class_exists('Carbon_Fields\Carbon_Fields')) {
       add_action('admin_notices', function() {
           echo '<div class="notice notice-warning"><p>';
           echo 'Carbon Fields is not installed. Download from ';
           echo '<a href="https://github.com/htmlburger/carbon-fields/releases">GitHub</a>';
           echo '</p></div>';
       });
   }
   ```

---

## 📝 Documentation Updates Needed

After fix:
1. Update CARBON-FIELDS.md with installation steps
2. Update README.md to mention Carbon Fields is included
3. Update QUICK-REFERENCE.md with working examples
4. Add to DEVELOPMENT-STATUS.md as completed feature

---

## 🎯 Priority Action

**IMMEDIATE**: Download complete Carbon Fields package and replace incomplete version

**WHY**: Carbon Fields is a core feature of WPSeed, not an optional extra. It's mentioned in:
- README.md (as a key feature)
- DEVELOPMENT-STATUS.md (as completed)
- CARBON-FIELDS.md (full documentation)
- examples/carbon-fields-examples.php

**Without it working, WPSeed is incomplete.**

---

## 💡 Prevention for Future

Add to development checklist:
- [ ] Verify all bundled libraries are complete
- [ ] Test library loading before committing
- [ ] Check for dependency requirements
- [ ] Document installation steps for libraries

---

**Next Step**: Download complete Carbon Fields package from GitHub releases and replace current incomplete version.
