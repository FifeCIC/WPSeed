# WPSeed API Reference

## Core Functions

### wpseed_get_option()
Get plugin option with default fallback.

```php
wpseed_get_option( string $key, mixed $default = false ): mixed
```

**Parameters:**
- `$key` (string) Option key
- `$default` (mixed) Default value if option doesn't exist

**Returns:** Option value or default

**Example:**
```php
$api_key = wpseed_get_option('api_key', '');
```

---

### wpseed_update_option()
Update plugin option.

```php
wpseed_update_option( string $key, mixed $value ): bool
```

---

## Listener System

### WPSeed_Listener

#### Process Form
Forms automatically processed when they include `wpseed_form_action` field.

**HTML:**
```php
<form method="post">
    <?php wp_nonce_field('my_action'); ?>
    <input type="hidden" name="wpseed_form_action" value="my_action">
    <input type="text" name="data">
    <button type="submit">Submit</button>
</form>
```

**Handler:**
```php
add_action('wpseed_process_form_my_action', 'handle_my_action');

function handle_my_action() {
    $data = sanitize_text_field($_POST['data']);
    // Process data
    
    set_transient('wpseed_admin_notice', array(
        'type' => 'success',
        'message' => 'Saved!'
    ), 30);
    
    wp_redirect(wp_get_referer());
    exit;
}
```

#### Get Recent Requests
```php
WPSeed_Listener::get_recent_requests( int $limit = 20 ): array
```

---

## Notification System

### WPSeed_Notifications

#### create_notification()
Create a new notification.

```php
WPSeed_Notifications::create_notification(
    string $type,
    string $message,
    array $args = array()
): int|false
```

**Parameters:**
- `$type` (string) Notification type (must be registered)
- `$message` (string) Notification message
- `$args` (array) Optional arguments:
  - `user_id` (int) User ID (0 for all admins)
  - `priority` (string) 'low', 'normal', 'high'
  - `send_email` (bool) Send email notification
  - `expiration` (int) Unix timestamp
  - `data` (array) Additional data

**Returns:** Notification ID or false

**Example:**
```php
WPSeed_Notifications::create_notification(
    'system_alert',
    'Database backup completed',
    array(
        'user_id' => 0,
        'priority' => 'high',
        'send_email' => true
    )
);
```

#### get_notifications()
```php
WPSeed_Notifications::get_notifications(
    int $user_id = 0,
    array $args = array()
): array
```

#### mark_as_read()
```php
WPSeed_Notifications::mark_as_read(
    int $notification_id,
    int $user_id = 0
): bool
```

#### get_unread_count()
```php
WPSeed_Notifications::get_unread_count( int $user_id ): int
```

---

## Settings Backup

### WPSeed_Settings_Backup

#### Export Settings
Triggered via admin form, downloads JSON file.

#### Import Settings
Triggered via admin form, uploads JSON file.

#### Reset Settings
Deletes all WPSeed options.

---

## Object Registry

### WPSeed_Object_Registry

#### add()
Store object globally.

```php
WPSeed_Object_Registry::add( string $key, object $object ): void
```

**Example:**
```php
$api = new My_API_Client();
WPSeed_Object_Registry::add('api_client', $api);
```

#### get()
Retrieve stored object.

```php
WPSeed_Object_Registry::get( string $key ): object|null
```

**Example:**
```php
$api = WPSeed_Object_Registry::get('api_client');
```

#### remove()
```php
WPSeed_Object_Registry::remove( string $key ): void
```

#### exists()
```php
WPSeed_Object_Registry::exists( string $key ): bool
```

---

## Data Freshness Manager

### WPSeed_Data_Freshness_Manager

#### ensure_freshness()
Get data with automatic cache refresh.

```php
WPSeed_Data_Freshness_Manager::ensure_freshness(
    string $key,
    string $interval,
    callable $callback
): mixed
```

**Parameters:**
- `$key` (string) Cache key
- `$interval` (string) 'hourly', 'daily', 'weekly'
- `$callback` (callable) Function to fetch fresh data

**Example:**
```php
$data = WPSeed_Data_Freshness_Manager::ensure_freshness(
    'api_data',
    'hourly',
    function() {
        return fetch_from_api();
    }
);
```

---

## Developer Flow Logger

### WPSeed_Developer_Flow_Logger

Only active in developer mode.

#### start_flow()
```php
WPSeed_Developer_Flow_Logger::start_flow( string $flow_name ): void
```

#### log_decision()
```php
WPSeed_Developer_Flow_Logger::log_decision(
    string $decision,
    string $result
): void
```

#### end_flow()
```php
WPSeed_Developer_Flow_Logger::end_flow( string $result ): void
```

**Example:**
```php
WPSeed_Developer_Flow_Logger::start_flow('data_processing');
WPSeed_Developer_Flow_Logger::log_decision('Check cache', 'HIT');
WPSeed_Developer_Flow_Logger::log_decision('Validate data', 'PASS');
WPSeed_Developer_Flow_Logger::end_flow('Success');
```

---

## Database Functions

### wpseed_db_selectrow()
```php
wpseed_db_selectrow(
    string $tablename,
    string $condition,
    string $select = '*'
): object|null
```

### wpseed_db_selectwhere()
```php
wpseed_db_selectwhere(
    string $tablename,
    string $condition = null,
    string $orderby = null,
    string $select = '*',
    string $object = 'ARRAY_A'
): array
```

### wpseed_db_count_rows()
```php
wpseed_db_count_rows(
    string $tablename,
    string $where = ''
): int
```

### wpseed_db_table_exists()
```php
wpseed_db_table_exists( string $table_name ): bool
```

---

## Action Scheduler Integration

### Schedule Single Action
```php
as_schedule_single_action(
    int $timestamp,
    string $hook,
    array $args = array()
): int
```

**Example:**
```php
as_schedule_single_action(
    time() + 3600,
    'wpseed_process_data',
    array('item_id' => 123)
);
```

### Schedule Recurring Action
```php
as_schedule_recurring_action(
    int $timestamp,
    int $interval_in_seconds,
    string $hook,
    array $args = array()
): int
```

### Enqueue Async Action
```php
as_enqueue_async_action(
    string $hook,
    array $args = array()
): int
```

### Unschedule Action
```php
as_unschedule_action(
    string $hook,
    array $args = array()
): void
```

### Check if Scheduled
```php
as_next_scheduled_action(
    string $hook,
    array $args = array()
): int|false
```

---

## REST API

### Base Controller
Extend `WPSeed_REST_Controller` for custom endpoints.

```php
class My_REST_Controller extends WPSeed_REST_Controller {
    protected $rest_base = 'myendpoint';
    
    public function register_routes() {
        register_rest_route($this->namespace, '/' . $this->rest_base, array(
            'methods'  => 'GET',
            'callback' => array($this, 'get_items'),
            'permission_callback' => array($this, 'get_items_permissions_check'),
        ));
    }
    
    public function get_items($request) {
        return rest_ensure_response(array('data' => 'Hello'));
    }
    
    public function get_items_permissions_check($request) {
        return current_user_can('manage_options');
    }
}
```

---

## Hooks Reference

### Actions

#### wpseed_loaded
Fires after plugin is loaded.
```php
add_action('wpseed_loaded', 'my_function');
```

#### wpseed_init
Fires during WordPress init.
```php
add_action('wpseed_init', 'my_function');
```

#### wpseed_process_form_{action}
Fires when form is processed.
```php
add_action('wpseed_process_form_save_settings', 'handle_save');
```

#### wpseed_notification_created
Fires when notification is created.
```php
add_action('wpseed_notification_created', 'my_function', 10, 4);
function my_function($id, $type, $message, $args) {
    // Handle notification
}
```

### Filters

#### wpseed_settings
Filter plugin settings.
```php
add_filter('wpseed_settings', 'modify_settings');
```

#### wpseed_asset_url
Filter asset URL.
```php
add_filter('wpseed_asset_url', 'modify_url', 10, 3);
function modify_url($url, $type, $name) {
    return $url;
}
```

---

## Constants

### WPSEED_VERSION
Plugin version number.

### WPSEED_PLUGIN_DIR_PATH
Absolute path to plugin directory.

### WPSEED_PLUGIN_DIR_URL
URL to plugin directory.

### WPSEED_DEV_MODE
Enable developer mode (boolean).

### WPSEED_LOG_DIR
Path to log directory.

---

## Helper Functions

### is_rtl()
Check if current language is RTL.
```php
if (is_rtl()) {
    // RTL-specific code
}
```

### current_user_can()
Check user capability.
```php
if (current_user_can('manage_options')) {
    // Admin code
}
```

---

Last Updated: 2025
