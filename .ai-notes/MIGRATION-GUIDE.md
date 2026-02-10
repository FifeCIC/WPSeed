# Migration Guide: Switching to WPSeed

**For developers coming from other WordPress plugin boilerplates**

---

## 🎯 Why Migrate to WPSeed?

| Feature | WP Plugin Boilerplate | WP Plugin Skeleton | WPSeed |
|---------|----------------------|-------------------|--------|
| Last Updated | 2020 | 2019 | 2025 |
| Background Tasks | ❌ | ❌ | ✅ Action Scheduler |
| Modern Settings | ❌ | ❌ | ✅ Carbon Fields |
| Notifications | ❌ | ❌ | ✅ Full System |
| Performance Monitoring | ❌ | ❌ | ✅ Query Monitor Style |
| Development Dashboard | ❌ | ❌ | ✅ 14 Tabs |
| Library Management | ❌ | ❌ | ✅ Update Checker |
| Asset Registry | ❌ | ❌ | ✅ Centralized |
| WP-CLI Generators | ❌ | ⚠️ Basic | ✅ Advanced |
| Integration Examples | ❌ | ❌ | ✅ 12 Plugins |

---

## 📦 From WP Plugin Boilerplate

### File Structure Comparison

**WP Plugin Boilerplate:**
```
plugin-name/
├── admin/
│   ├── class-plugin-name-admin.php
│   ├── css/
│   └── js/
├── includes/
│   ├── class-plugin-name.php
│   ├── class-plugin-name-loader.php
│   └── class-plugin-name-i18n.php
├── public/
│   ├── class-plugin-name-public.php
│   ├── css/
│   └── js/
└── plugin-name.php
```

**WPSeed:**
```
wpseed/
├── admin/
│   ├── page/              # Admin pages (not classes)
│   └── notifications/     # Notification system
├── includes/
│   ├── classes/           # All classes here
│   ├── functions/         # Helper functions
│   ├── libraries/         # Bundled libraries
│   └── admin/             # Admin functionality
├── assets/
│   ├── css/
│   ├── js/
│   ├── css-registry.php   # NEW: Centralized registry
│   └── js-registry.php    # NEW: Centralized registry
└── loader.php             # Main loader
```

### Key Differences

#### 1. No Separate Public/Admin Classes
**Old Way (WPBP):**
```php
class Plugin_Name_Admin {
    public function enqueue_styles() {
        wp_enqueue_style($this->plugin_name, ...);
    }
}
```

**New Way (WPSeed):**
```php
// Register in assets/css-registry.php
'my-admin-style' => array(
    'path' => 'assets/css/admin.css',
    'pages' => array('toplevel_page_wpseed'),
),

// Enqueue directly
wp_enqueue_style('my-admin-style');
```

#### 2. No Loader Class
**Old Way (WPBP):**
```php
class Plugin_Name_Loader {
    protected $actions;
    protected $filters;
    
    public function add_action($hook, $component, $callback) {
        $this->actions = $this->add($this->actions, $hook, $component, $callback);
    }
}
```

**New Way (WPSeed):**
```php
// Direct WordPress hooks
add_action('init', 'my_function');
add_filter('the_content', 'my_filter');
```

#### 3. Simpler Initialization
**Old Way (WPBP):**
```php
function run_plugin_name() {
    $plugin = new Plugin_Name();
    $plugin->run();
}
run_plugin_name();
```

**New Way (WPSeed):**
```php
function WPSeed() {
    return WordPressPluginSeed::instance();
}
WPSeed();
```

### Migration Steps

1. **Copy Your Code**
   - Move admin classes to `includes/classes/`
   - Move public classes to `includes/classes/`
   - Move CSS to `assets/css/`
   - Move JS to `assets/js/`

2. **Update Class Names**
   ```bash
   # Find and replace
   Plugin_Name_Admin → WPSeed_Admin
   Plugin_Name_Public → WPSeed_Public
   ```

3. **Register Assets**
   - Add CSS to `assets/css-registry.php`
   - Add JS to `assets/js-registry.php`
   - Remove manual enqueue code

4. **Update Hooks**
   - Remove loader class usage
   - Use direct `add_action()` and `add_filter()`

5. **Update Includes**
   - Add your classes to `loader.php` includes section

---

## 🔧 From WP Plugin Skeleton

### Key Differences

#### 1. Better Asset Management
**Old Way (Skeleton):**
```php
wp_enqueue_style('my-style', plugins_url('css/style.css', __FILE__));
```

**New Way (WPSeed):**
```php
// Register once in css-registry.php
'my-style' => array(
    'path' => 'assets/css/style.css',
    'purpose' => 'Main styling',
),

// Enqueue anywhere
wp_enqueue_style('my-style');
```

#### 2. Background Processing
**Old Way (Skeleton):**
```php
// Use WP Cron (unreliable)
wp_schedule_event(time(), 'hourly', 'my_cron_hook');
```

**New Way (WPSeed):**
```php
// Use Action Scheduler (reliable)
WPSeed_Task_Scheduler::schedule_recurring(
    'my_task',
    array('data' => 'value'),
    time(),
    3600
);
```

#### 3. Settings Framework
**Old Way (Skeleton):**
```php
// Manual Settings API
add_settings_section('my_section', 'Settings', 'callback', 'my-page');
add_settings_field('my_field', 'Field', 'callback', 'my-page', 'my_section');
```

**New Way (WPSeed):**
```php
// Carbon Fields (modern)
use Carbon_Fields\Container;
use Carbon_Fields\Field;

Container::make('theme_options', 'Settings')
    ->add_fields(array(
        Field::make('text', 'api_key', 'API Key'),
        Field::make('checkbox', 'enable', 'Enable'),
    ));
```

### Migration Steps

1. **Replace WP Cron with Action Scheduler**
   ```php
   // Old
   wp_schedule_event(time(), 'hourly', 'my_hook');
   
   // New
   WPSeed_Task_Scheduler::schedule_recurring('my_hook', array(), time(), 3600);
   ```

2. **Upgrade Settings to Carbon Fields**
   - See `examples/carbon-fields-examples.php`
   - Much cleaner and more powerful

3. **Add Asset Registry**
   - Register all CSS/JS in registries
   - Remove scattered enqueue calls

---

## 🚀 From Custom/No Boilerplate

### What You Get

#### 1. Instant Development Dashboard
Access at: `wp-admin/admin.php?page=wpseed-development`

- **Assets Tab**: Track all CSS/JS files
- **Performance Tab**: Query monitoring
- **Debug Log Tab**: View WordPress debug log
- **Database Tab**: Inspect tables
- **Tasks Tab**: Monitor background tasks
- **Libraries Tab**: Check for updates

#### 2. Background Task System
```php
// Schedule a task
WPSeed_Task_Scheduler::schedule_single(
    'send_email',
    array('to' => 'user@example.com'),
    time() + 300  // 5 minutes
);

// Handle task
add_action('send_email', function($to) {
    wp_mail($to, 'Subject', 'Message');
});
```

#### 3. Notification System
```php
// Add notification
WPSeed_Notifications::add_notification(
    get_current_user_id(),
    'Task Complete',
    'Your export is ready',
    'success',
    'normal',
    admin_url('admin.php?page=exports'),
    'Download'
);
```

#### 4. Enhanced Logging
```php
// Log anything
WPSeed_Enhanced_Logger::log_query($sql, $time, $function);
WPSeed_Enhanced_Logger::log_hook($hook, $callback, $time);
WPSeed_Enhanced_Logger::log_error($message, $type, $source);

// View in Performance tab
```

#### 5. Modern Settings
```php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

Container::make('theme_options', 'My Settings')
    ->add_fields(array(
        Field::make('text', 'api_key', 'API Key'),
        Field::make('rich_text', 'content', 'Content'),
        Field::make('image', 'logo', 'Logo'),
        Field::make('complex', 'items', 'Items')
            ->add_fields(array(
                Field::make('text', 'title', 'Title'),
                Field::make('textarea', 'description', 'Description'),
            )),
    ));
```

### Migration Steps

1. **Copy Your Plugin Files**
   - Place in `includes/classes/` or `includes/functions/`

2. **Add to Loader**
   ```php
   // In loader.php includes() method
   include_once('includes/classes/my-class.php');
   ```

3. **Register Assets**
   - Add to `assets/css-registry.php`
   - Add to `assets/js-registry.php`

4. **Replace Custom Solutions**
   - Background tasks → Action Scheduler
   - Settings → Carbon Fields
   - Logging → Enhanced Logger
   - Notifications → Notification System

---

## 📋 Migration Checklist

### Phase 1: Setup (30 minutes)
- [ ] Download WPSeed
- [ ] Rename plugin (find/replace)
- [ ] Update plugin header in `wpseed.php`
- [ ] Update constants in `loader.php`
- [ ] Test activation

### Phase 2: Move Code (1-2 hours)
- [ ] Copy classes to `includes/classes/`
- [ ] Copy functions to `includes/functions/`
- [ ] Copy templates to `templates/`
- [ ] Copy assets to `assets/css/` and `assets/js/`
- [ ] Update namespaces/class names

### Phase 3: Register Assets (30 minutes)
- [ ] Add CSS to `assets/css-registry.php`
- [ ] Add JS to `assets/js-registry.php`
- [ ] Remove old enqueue code
- [ ] Test asset loading

### Phase 4: Update Features (2-4 hours)
- [ ] Replace WP Cron with Action Scheduler
- [ ] Upgrade settings to Carbon Fields
- [ ] Add Enhanced Logger calls
- [ ] Implement notifications
- [ ] Update REST API to use base controller

### Phase 5: Test (1 hour)
- [ ] Test all features
- [ ] Check Development Dashboard
- [ ] Verify background tasks
- [ ] Test settings save/load
- [ ] Check notifications
- [ ] Review debug log

### Phase 6: Cleanup (30 minutes)
- [ ] Remove unused files
- [ ] Update documentation
- [ ] Clean up comments
- [ ] Remove old boilerplate code

---

## 🎓 Learning Resources

### Essential Reading
1. **QUICK-REFERENCE.md** - Fast lookup for common tasks
2. **ACTION-SCHEDULER.md** - Background task system
3. **CARBON-FIELDS.md** - Modern settings framework
4. **ADVANCED-FEATURES.md** - Object Registry, Data Freshness, etc.

### Code Examples
- `/examples/` - Working code examples
- `/examples/integrations/` - Plugin integration examples
- `/docs/` - Full documentation

### Development Dashboard
- **Assets Tab** - See all registered assets
- **Performance Tab** - Monitor queries and hooks
- **Tasks Tab** - View scheduled tasks
- **Libraries Tab** - Check library versions

---

## 💡 Best Practices

### 1. Use Asset Registries
```php
// ❌ Don't do this
wp_enqueue_style('my-style', plugins_url('css/style.css', __FILE__));

// ✅ Do this
// Register in css-registry.php, then:
wp_enqueue_style('my-style');
```

### 2. Use Action Scheduler
```php
// ❌ Don't use WP Cron
wp_schedule_event(time(), 'hourly', 'my_hook');

// ✅ Use Action Scheduler
WPSeed_Task_Scheduler::schedule_recurring('my_hook', array(), time(), 3600);
```

### 3. Use Carbon Fields
```php
// ❌ Don't use Settings API manually
add_settings_section(...);
add_settings_field(...);

// ✅ Use Carbon Fields
Container::make('theme_options', 'Settings')
    ->add_fields(array(
        Field::make('text', 'api_key', 'API Key'),
    ));
```

### 4. Use Enhanced Logger
```php
// ❌ Don't use error_log everywhere
error_log('Query took ' . $time . ' seconds');

// ✅ Use Enhanced Logger
WPSeed_Enhanced_Logger::log_query($sql, $time, $function);
```

### 5. Use Notifications
```php
// ❌ Don't use admin notices for everything
add_action('admin_notices', function() {
    echo '<div class="notice">...</div>';
});

// ✅ Use Notification System
WPSeed_Notifications::add_notification(
    get_current_user_id(),
    'Title',
    'Message',
    'info'
);
```

---

## 🆘 Common Migration Issues

### Issue: Assets Not Loading
**Solution**: Register in asset registries, don't manually enqueue

### Issue: Background Tasks Not Running
**Solution**: Use Action Scheduler, not WP Cron

### Issue: Settings Not Saving
**Solution**: Use Carbon Fields, ensure `carbon_fields_register_fields` hook

### Issue: Class Not Found
**Solution**: Add to `loader.php` includes section

### Issue: Hooks Not Firing
**Solution**: Remove loader class, use direct WordPress hooks

---

## 📞 Need Help?

- **Documentation**: `/docs/` folder
- **Examples**: `/examples/` folder
- **Development Dashboard**: `wp-admin/admin.php?page=wpseed-development`
- **GitHub Issues**: Report bugs and ask questions

---

**Ready to migrate?** Start with Phase 1 of the checklist above!
