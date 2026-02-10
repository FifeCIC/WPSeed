# WPSeed Quick Reference Guide

**Version**: 1.2.0  
**For**: Plugin Developers  
**Purpose**: Fast lookup for common tasks

---

## 🚀 Quick Start

### Rename Plugin (Find & Replace)
```
"wpseed" → "yourplugin"
"WPSeed" → "YourPlugin"
"WPSEED" → "YOURPLUGIN"
"Plugin Seed" → "Your Plugin Name"
```

---

## 📦 Background Tasks (Action Scheduler)

### Schedule Single Task
```php
WPSeed_Task_Scheduler::schedule_single(
    'my_task_hook',
    array('param1' => 'value'),
    time() + 3600  // Run in 1 hour
);
```

### Schedule Recurring Task
```php
WPSeed_Task_Scheduler::schedule_recurring(
    'my_recurring_task',
    array('data' => 'value'),
    time(),
    3600  // Every hour
);
```

### Schedule Cron Task
```php
WPSeed_Task_Scheduler::schedule_cron(
    'my_cron_task',
    array(),
    '0 0 * * *'  // Daily at midnight
);
```

### Handle Task
```php
add_action('my_task_hook', function($param1) {
    // Do work here
    error_log('Task executed: ' . $param1);
});
```

---

## ⚙️ Settings (Carbon Fields)

### Create Options Page
```php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

Container::make('theme_options', 'Plugin Settings')
    ->set_page_parent('options-general.php')
    ->add_fields(array(
        Field::make('text', 'api_key', 'API Key'),
        Field::make('textarea', 'description', 'Description'),
        Field::make('checkbox', 'enable_feature', 'Enable Feature'),
    ));
```

### Get Setting Value
```php
$api_key = carbon_get_theme_option('api_key');
```

### Create Post Meta
```php
Container::make('post_meta', 'Extra Fields')
    ->where('post_type', '=', 'post')
    ->add_fields(array(
        Field::make('text', 'subtitle', 'Subtitle'),
        Field::make('image', 'featured_image', 'Featured Image'),
    ));
```

---

## 🔔 Notifications

### Add Notification
```php
WPSeed_Notifications::add_notification(
    get_current_user_id(),
    'Update Available',
    'A new version is available',
    'update',
    'high',  // or 'normal'
    admin_url('plugins.php'),
    'Update Now'
);
```

### Get Unread Count
```php
$count = WPSeed_Notifications::get_unread_count(get_current_user_id());
```

### Mark as Read
```php
WPSeed_Notifications::mark_as_read($notification_id);
```

### Snooze Notification
```php
WPSeed_Notifications::snooze_notification($notification_id, 3600); // 1 hour
```

---

## 📝 Enhanced Logging

### Log Query
```php
WPSeed_Enhanced_Logger::log_query(
    'SELECT * FROM wp_posts',
    0.025,  // execution time
    'get_posts'
);
```

### Log Hook
```php
WPSeed_Enhanced_Logger::log_hook(
    'init',
    'my_function',
    0.001
);
```

### Log HTTP Request
```php
WPSeed_Enhanced_Logger::log_http_request(
    'https://api.example.com/data',
    'GET',
    200,
    0.5
);
```

### Log Error
```php
WPSeed_Enhanced_Logger::log_error(
    'Warning: Invalid data',
    'warning',  // notice, warning, error
    'my_function'
);
```

### Get Performance Metrics
```php
$metrics = WPSeed_Enhanced_Logger::get_performance_metrics();
// Returns: queries, hooks, http_requests, errors, execution_time, memory_usage
```

---

## 🎨 Asset Management

### Register CSS
Edit `assets/css-registry.php`:
```php
'my-custom-style' => array(
    'path' => 'assets/css/my-style.css',
    'purpose' => 'Custom styling for feature X',
    'pages' => array('toplevel_page_my-page'),
    'dependencies' => array(),
),
```

### Register JS
Edit `assets/js-registry.php`:
```php
'my-custom-script' => array(
    'path' => 'assets/js/my-script.js',
    'purpose' => 'Custom functionality for feature X',
    'pages' => array('toplevel_page_my-page'),
    'dependencies' => array('jquery'),
),
```

### Enqueue Assets
```php
wp_enqueue_style('my-custom-style');
wp_enqueue_script('my-custom-script');
```

---

## 🔌 REST API

### Create Endpoint
```php
class My_REST_Controller extends WPSeed_REST_Controller {
    protected $rest_base = 'myendpoint';
    
    public function register_routes() {
        register_rest_route($this->namespace, '/' . $this->rest_base, array(
            'methods' => 'GET',
            'callback' => array($this, 'get_items'),
            'permission_callback' => array($this, 'get_items_permissions_check'),
        ));
    }
    
    public function get_items($request) {
        return rest_ensure_response(array(
            'success' => true,
            'data' => array('message' => 'Hello World')
        ));
    }
    
    public function get_items_permissions_check($request) {
        return current_user_can('read');
    }
}
```

### Register in loader.php
```php
add_action('rest_api_init', function() {
    $controller = new My_REST_Controller();
    $controller->register_routes();
});
```

### Access Endpoint
```
GET /wp-json/wpseed/v1/myendpoint
```

---

## 💻 WP-CLI Commands

### Get Plugin Info
```bash
wp wpseed info
```

### Clear Cache
```bash
wp wpseed cache clear
```

### Generate Custom Post Type
```bash
wp wpseed generate cpt Book --plural=Books --icon=book
```

### Generate Taxonomy
```bash
wp wpseed generate taxonomy Genre --post-type=book
```

### Generate REST Endpoint
```bash
wp wpseed generate rest books --methods=GET,POST
```

### Generate Settings Page
```bash
wp wpseed generate settings "API Settings"
```

---

## 📊 Object Registry

### Store Object
```php
WPSeed_Object_Registry::add('my_object', $object);
```

### Retrieve Object
```php
$obj = WPSeed_Object_Registry::get('my_object');
```

### Check if Exists
```php
if (WPSeed_Object_Registry::exists('my_object')) {
    // Object exists
}
```

---

## 🔄 Data Freshness Manager

### Ensure Fresh Data
```php
$data = WPSeed_Data_Freshness_Manager::ensure_freshness(
    'my_cache_key',
    'hourly',  // hourly, daily, weekly
    function() {
        // Fetch fresh data
        return fetch_data_from_api();
    }
);
```

### Check if Fresh
```php
$is_fresh = WPSeed_Data_Freshness_Manager::is_fresh('my_cache_key', 'hourly');
```

### Invalidate Cache
```php
WPSeed_Data_Freshness_Manager::invalidate('my_cache_key');
```

---

## 🐛 Developer Flow Logger

### Start Flow
```php
WPSeed_Developer_Flow_Logger::start_flow('data_processing');
```

### Log Decision
```php
WPSeed_Developer_Flow_Logger::log_decision('Check cache', 'HIT');
```

### End Flow
```php
WPSeed_Developer_Flow_Logger::end_flow('Success');
```

### Get Flow Log
```php
$log = WPSeed_Developer_Flow_Logger::get_flow_log('data_processing');
```

---

## 📚 Custom Post Types

### Register CPT
```php
function my_register_cpt() {
    register_post_type('book', array(
        'labels' => array(
            'name' => __('Books', 'wpseed'),
            'singular_name' => __('Book', 'wpseed'),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-book',
        'supports' => array('title', 'editor', 'thumbnail'),
        'show_in_rest' => true,
    ));
}
add_action('init', 'my_register_cpt');
```

---

## 🏷️ Taxonomies

### Register Taxonomy
```php
function my_register_taxonomy() {
    register_taxonomy('genre', 'book', array(
        'labels' => array(
            'name' => __('Genres', 'wpseed'),
            'singular_name' => __('Genre', 'wpseed'),
        ),
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
    ));
}
add_action('init', 'my_register_taxonomy');
```

---

## 🎯 Shortcodes

### Create Shortcode
```php
function my_shortcode($atts) {
    $atts = shortcode_atts(array(
        'title' => 'Default Title',
        'count' => 5,
    ), $atts);
    
    ob_start();
    ?>
    <div class="my-shortcode">
        <h3><?php echo esc_html($atts['title']); ?></h3>
        <!-- Content here -->
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('my_shortcode', 'my_shortcode');
```

### Use Shortcode
```
[my_shortcode title="Hello" count="10"]
```

---

## 🔐 Security

### Nonce Verification
```php
// Create nonce
wp_nonce_field('my_action', 'my_nonce');

// Verify nonce
if (!wp_verify_nonce($_POST['my_nonce'], 'my_action')) {
    wp_die('Security check failed');
}
```

### Capability Check
```php
if (!current_user_can('manage_options')) {
    wp_die('Unauthorized');
}
```

### Sanitize Input
```php
$text = sanitize_text_field($_POST['text']);
$email = sanitize_email($_POST['email']);
$url = esc_url_raw($_POST['url']);
$html = wp_kses_post($_POST['html']);
```

### Escape Output
```php
echo esc_html($text);
echo esc_attr($attribute);
echo esc_url($url);
echo wp_kses_post($html);
```

---

## 🌐 Internationalization

### Mark Strings for Translation
```php
__('Text', 'wpseed');
_e('Text', 'wpseed');
_n('Singular', 'Plural', $count, 'wpseed');
esc_html__('Text', 'wpseed');
esc_html_e('Text', 'wpseed');
```

### Generate POT File
```bash
wp i18n make-pot . languages/wpseed.pot
```

---

## 📦 Database

### Create Table
```php
global $wpdb;
$table_name = $wpdb->prefix . 'my_table';
$charset_collate = $wpdb->get_charset_collate();

$sql = "CREATE TABLE $table_name (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    name varchar(255) NOT NULL,
    created_at datetime DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) $charset_collate;";

require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
dbDelta($sql);
```

### Insert Data
```php
$wpdb->insert(
    $table_name,
    array('name' => 'John Doe'),
    array('%s')
);
```

### Query Data
```php
$results = $wpdb->get_results("SELECT * FROM $table_name WHERE name = %s", 'John Doe');
```

---

## 🧪 Testing

### Run Tests
```bash
phpunit
```

### Run Specific Test
```bash
phpunit tests/test-example.php
```

### Write Test
```php
class Test_Example extends WP_UnitTestCase {
    public function test_something() {
        $this->assertTrue(true);
    }
}
```

---

## 📁 File Structure

```
wpseed/
├── admin/                  # Admin pages
├── api/                    # API clients
├── assets/                 # CSS, JS, images
│   ├── css/
│   ├── js/
│   ├── css-registry.php   # Register CSS here
│   └── js-registry.php    # Register JS here
├── includes/
│   ├── classes/           # Core classes
│   ├── functions/         # Helper functions
│   ├── libraries/         # Bundled libraries
│   │   ├── action-scheduler/
│   │   └── carbon-fields/
│   └── admin/             # Admin functionality
├── docs/                  # Documentation
├── examples/              # Code examples
├── tests/                 # PHPUnit tests
├── loader.php             # Main loader
└── wpseed.php            # Plugin entry point
```

---

## 🔗 Useful Links

- **Development Dashboard**: `wp-admin/admin.php?page=wpseed-development`
- **Notification Center**: `wp-admin/admin.php?page=wpseed-notifications`
- **Settings**: `wp-admin/admin.php?page=wpseed-settings`
- **Documentation**: `/docs/` folder
- **Examples**: `/examples/` folder

---

## 💡 Pro Tips

1. **Always use asset registries** - Never manually enqueue CSS/JS
2. **Use Action Scheduler** - For background tasks instead of WP Cron
3. **Log everything** - Use Enhanced Logger for debugging
4. **Check notifications** - Important updates appear in admin bar
5. **Use WP-CLI generators** - Save time with code generation
6. **Follow WordPress standards** - Use WordPress functions over PHP
7. **Test with WP_DEBUG** - Enable in wp-config.php during development
8. **Use Object Registry** - For global object access without globals
9. **Cache expensive operations** - Use Data Freshness Manager
10. **Document your code** - Future you will thank you

---

## 🆘 Common Issues

### Assets Not Loading
- Check asset is registered in `css-registry.php` or `js-registry.php`
- Verify file path is correct
- Check page slug matches in registry

### Tasks Not Running
- Verify Action Scheduler is loaded
- Check task is scheduled: `wp-admin/admin.php?page=wpseed-development&tab=tasks`
- Ensure hook is registered with `add_action()`

### Notifications Not Showing
- Check database table exists: `wp_wpseed_notifications`
- Verify user ID is correct
- Check notification bell is loaded in admin bar

### Settings Not Saving
- Verify Carbon Fields is loaded
- Check container is registered on `carbon_fields_register_fields` hook
- Ensure user has `manage_options` capability

---

**Need more help?** Check the full documentation in `/docs/` or visit the Development Dashboard!
