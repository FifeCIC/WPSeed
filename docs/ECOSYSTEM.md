# Plugin Ecosystem Framework

## Overview

WPSeed includes a **Plugin Ecosystem Framework** that enables seamless communication and resource sharing between multiple Ryan Bayne plugins. When 2+ ecosystem plugins are installed, shared views automatically move to WordPress Tools and Settings menus for a unified experience.

---

## Key Features

### 1. **Automatic Detection**
- Detects when multiple ecosystem plugins are installed
- Automatically switches to "ecosystem mode"
- No configuration required

### 2. **Dynamic Menu Placement**
- **Single Plugin**: Views stay in plugin menu
- **2+ Plugins**: Shared views move to Tools & Settings
- User can toggle this behavior in settings

### 3. **Shared Resources**
- **Unified Logging**: All plugin logs in one place (Tools → Ecosystem Logs)
- **CRON Monitor**: View all scheduled tasks (Tools → Background Tasks)
- **Background Processes**: Monitor async operations
- **Shared Settings**: Ecosystem-wide configuration (Settings → Ecosystem)

### 4. **One-Click Installation**
- Install related plugins with one click
- Automatic dependency detection
- Integration suggestions

---

## How It Works

### Plugin Registration

Each plugin registers itself with the ecosystem:

```php
add_action('wpseed_ecosystem_register', function() {
    wpseed_ecosystem()->register_plugin('myplugin', array(
        'name' => 'My Plugin',
        'version' => '1.0.0',
        'path' => plugin_dir_path(__FILE__),
        'url' => plugins_url('/', __FILE__),
        'has_logging' => true,
        'has_cron' => true,
        'has_background_tasks' => true,
        'shared_settings' => array('logging', 'cron'),
    ));
});
```

### Shared Resource Registration

Plugins can register shared resources:

```php
// Register logging view
wpseed_ecosystem()->register_shared_resource('logging', function($plugin_slug) {
    // Render logging interface for this plugin
    echo '<h3>Logs for ' . $plugin_slug . '</h3>';
    // ... your logging code
}, 10);

// Register background task view
wpseed_ecosystem()->register_shared_resource('background_tasks', function() {
    // Render background tasks for this plugin
    echo '<tr><td>My Task</td><td>Running</td></tr>';
}, 10);
```

### Check Ecosystem Mode

```php
if (wpseed_ecosystem()->is_ecosystem_mode()) {
    // 2+ plugins installed
    // Use shared menus
} else {
    // Single plugin
    // Use plugin-specific menus
}
```

---

## Menu Behavior

### Single Plugin Mode
```
WordPress Admin
├── Plugins
│   └── My Plugin
│       ├── Dashboard
│       ├── Settings
│       ├── Logging
│       └── Background Tasks
```

### Ecosystem Mode (2+ Plugins)
```
WordPress Admin
├── Tools
│   ├── Ecosystem Logs (all plugins)
│   └── Background Tasks (all plugins)
├── Settings
│   └── Ecosystem (shared settings)
├── Plugins
│   ├── Plugin 1 (plugin-specific features)
│   └── Plugin 2 (plugin-specific features)
```

---

## Shared Views

### 1. Ecosystem Logs (Tools Menu)

**Location**: Tools → Ecosystem Logs

**Features**:
- Tabbed interface (one tab per plugin)
- Unified log viewing
- Filter by plugin, level, date
- Export logs
- Clear logs

**Usage**:
```php
// In your plugin
wpseed_ecosystem()->register_shared_resource('logging', 'myplugin_render_logs', 10);

function myplugin_render_logs($plugin_slug) {
    // Render your plugin's logs
    $logs = get_option('myplugin_logs', array());
    foreach ($logs as $log) {
        echo '<div class="log-entry">' . esc_html($log['message']) . '</div>';
    }
}
```

### 2. Background Tasks (Tools Menu)

**Location**: Tools → Background Tasks

**Features**:
- View all CRON jobs across plugins
- Monitor background processes
- Track async tasks
- Run CRON jobs manually
- See next execution time

**Sections**:
- **WordPress CRON Jobs**: All scheduled tasks
- **Background Processes**: Long-running operations
- **Async Tasks**: Queued operations

### 3. Ecosystem Settings (Settings Menu)

**Location**: Settings → Ecosystem

**Features**:
- Toggle menu location (shared vs plugin-specific)
- View installed ecosystem plugins
- Configure shared resources
- Manage integrations

---

## One-Click Plugin Installation

**Location**: Settings → WPSeed Settings → Install Plugins

**Features**:
- Grid view of available ecosystem plugins
- One-click installation
- Automatic activation
- Integration detection
- Dependency management

**Usage**:
```php
// Define available plugins
add_filter('wpseed_ecosystem_available_plugins', function($plugins) {
    $plugins['myplugin'] = array(
        'name' => 'My Plugin',
        'description' => 'Does amazing things',
        'download_url' => 'https://github.com/user/myplugin/archive/main.zip',
        'required_by' => array(),
        'integrates_with' => array('wpseed', 'tradepress'),
    );
    return $plugins;
});
```

---

## Integration Detection

The ecosystem automatically detects integrations:

```php
// Check if specific plugin is installed
if (wpseed_ecosystem()->is_registered('tradepress')) {
    // TradePress is installed
    // Enable TradePress-specific features
}

// Get all installed plugins
$plugins = wpseed_ecosystem()->get_plugins();
foreach ($plugins as $slug => $plugin) {
    echo $plugin['name'] . ' v' . $plugin['version'];
}
```

---

## Feature Activation

Enable features based on installed plugins:

```php
// In your plugin
add_action('wpseed_ecosystem_plugin_registered', function($slug, $plugin) {
    if ($slug === 'tradepress') {
        // TradePress was just registered
        // Enable TradePress integration features
        update_option('myplugin_tradepress_integration', true);
    }
}, 10, 2);
```

---

## Best Practices

### 1. **Always Register Your Plugin**
```php
add_action('wpseed_ecosystem_register', 'myplugin_register_ecosystem');
```

### 2. **Use Shared Resources**
Register logging, CRON, and background task views so they appear in shared interfaces.

### 3. **Check Ecosystem Mode**
Adjust your menu structure based on ecosystem mode.

### 4. **Prefix Everything**
Use your plugin slug as prefix for hooks, CRON jobs, etc.

### 5. **Respect User Preferences**
Honor the menu location setting from Ecosystem Settings.

---

## Example: Full Integration

```php
<?php
/**
 * My Plugin - Ecosystem Integration
 */

// 1. Register with ecosystem
add_action('wpseed_ecosystem_register', function() {
    wpseed_ecosystem()->register_plugin('myplugin', array(
        'name' => 'My Plugin',
        'version' => '1.0.0',
        'path' => plugin_dir_path(__FILE__),
        'url' => plugins_url('/', __FILE__),
        'has_logging' => true,
        'has_cron' => true,
        'has_background_tasks' => true,
    ));
});

// 2. Register shared logging
wpseed_ecosystem()->register_shared_resource('logging', function($plugin_slug) {
    if ($plugin_slug !== 'myplugin') return;
    
    $logs = get_option('myplugin_logs', array());
    foreach ($logs as $log) {
        echo '<div class="log-entry">';
        echo '<strong>' . esc_html($log['level']) . '</strong>: ';
        echo esc_html($log['message']);
        echo '</div>';
    }
}, 10);

// 3. Register background tasks
wpseed_ecosystem()->register_shared_resource('background_tasks', function() {
    $tasks = get_option('myplugin_background_tasks', array());
    foreach ($tasks as $task) {
        echo '<tr>';
        echo '<td>' . esc_html($task['name']) . '</td>';
        echo '<td>My Plugin</td>';
        echo '<td>' . esc_html($task['status']) . '</td>';
        echo '<td>' . esc_html($task['progress']) . '%</td>';
        echo '</tr>';
    }
}, 10);

// 4. Adjust menus based on ecosystem mode
add_action('admin_menu', function() {
    if (wpseed_ecosystem()->use_shared_menu()) {
        // Don't add logging/CRON menus - they're in shared location
    } else {
        // Add plugin-specific menus
        add_menu_page('My Plugin', 'My Plugin', 'manage_options', 'myplugin');
        add_submenu_page('myplugin', 'Logs', 'Logs', 'manage_options', 'myplugin-logs', 'myplugin_render_logs');
    }
}, 999);

// 5. Detect other plugins
add_action('wpseed_ecosystem_plugin_registered', function($slug, $plugin) {
    if ($slug === 'tradepress') {
        // Enable TradePress integration
        update_option('myplugin_tradepress_enabled', true);
    }
}, 10, 2);
```

---

## API Reference

### Functions

**`wpseed_ecosystem()`**
- Returns: `WPSeed_Ecosystem_Registry` instance
- Global accessor for ecosystem registry

### Registry Methods

**`register_plugin($slug, $args)`**
- Register a plugin with the ecosystem
- Parameters:
  - `$slug` (string): Plugin slug
  - `$args` (array): Plugin configuration

**`get_plugins()`**
- Returns: Array of registered plugins

**`is_registered($slug)`**
- Check if plugin is registered
- Returns: boolean

**`get_plugin_count()`**
- Returns: Number of registered plugins

**`is_ecosystem_mode()`**
- Check if 2+ plugins are installed
- Returns: boolean

**`register_shared_resource($type, $callback, $priority)`**
- Register a shared resource
- Types: 'logging', 'background_tasks', 'async_tasks'

**`get_shared_resources($type)`**
- Get all registered resources of a type
- Returns: Array of callbacks

**`use_shared_menu()`**
- Check if shared menu location should be used
- Returns: boolean

### Hooks

**`wpseed_ecosystem_register`**
- Action: Register your plugin with ecosystem
- When: `plugins_loaded` priority 5

**`wpseed_ecosystem_plugin_registered`**
- Action: Fired when a plugin registers
- Parameters: `$slug`, `$plugin_data`

**`wpseed_ecosystem_available_plugins`**
- Filter: Add plugins to installer
- Returns: Array of available plugins

---

## Troubleshooting

### Menus Not Moving to Shared Location

**Check**:
1. Is ecosystem mode active? (2+ plugins installed)
2. Is menu location set to "shared" in Settings → Ecosystem?
3. Are plugins registered correctly?

**Solution**:
```php
// Debug
var_dump(wpseed_ecosystem()->is_ecosystem_mode());
var_dump(wpseed_ecosystem()->get_plugins());
var_dump(get_option('wpseed_ecosystem_menu_location'));
```

### Shared Resources Not Appearing

**Check**:
1. Is resource registered with correct type?
2. Is callback valid?
3. Is plugin registered?

**Solution**:
```php
// Debug
var_dump(wpseed_ecosystem()->get_shared_resources('logging'));
```

---

## Future Enhancements

- Shared user roles/capabilities
- Cross-plugin data sharing
- Unified notification system
- Shared cache management
- Cross-plugin search
- Ecosystem analytics dashboard

---

## Support

For ecosystem-related issues:
- GitHub: [Report Issue](https://github.com/ryanbayne/wpseed/issues)
- Documentation: [Wiki](https://github.com/ryanbayne/wpseed/wiki)
