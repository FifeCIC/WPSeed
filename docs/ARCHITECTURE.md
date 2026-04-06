# WPSeed Architecture Overview

## File Structure

```
wpseed/
├── admin/                      # Admin-specific functionality
│   ├── config/
│   │   └── admin-menus.php    # Menu registration
│   ├── notifications/
│   │   └── notifications.php  # Notification system
│   └── page/
│       ├── development/       # Development dashboard
│       ├── listener-monitor.php
│       ├── notification-center.php
│       └── security-audit.php
│
├── api/                        # API client architecture
│   ├── base-api.php           # Base API class
│   ├── api-directory.php      # API registry
│   └── api-factory.php        # API factory pattern
│
├── assets/                     # Frontend assets
│   ├── css/                   # Stylesheets
│   ├── js/                    # JavaScript
│   ├── images/                # Images
│   ├── manage-assets.php      # Asset manager
│   ├── queue-assets.php       # Asset queue system
│   ├── script-assets.php      # Script registration
│   └── style-assets.php       # Style registration
│
├── bin/                        # Command-line tools
│   └── generate-pot.bat       # Translation file generator
│
├── docs/                       # Documentation
│   ├── ARCHITECTURE.md        # This file
│   ├── API-REFERENCE.md       # API documentation
│   ├── LISTENER-PATTERNS.md   # Listener usage guide
│   ├── TRANSLATION-GUIDE.md   # Translation instructions
│   └── ...                    # Additional docs
│
├── examples/                   # Integration examples
│   ├── integrations/          # Plugin integrations
│   └── ...                    # Usage examples
│
├── includes/                   # Core functionality
│   ├── admin/                 # Admin components
│   ├── classes/               # Core classes
│   ├── functions/             # Helper functions
│   ├── libraries/             # Third-party libraries
│   ├── post-types/            # Custom post types
│   ├── shortcodes/            # Shortcode system
│   ├── toolbars/              # Admin toolbar
│   └── widgets/               # Widget system
│
├── languages/                  # Translation files
│   ├── wpseed.pot             # Translation template
│   ├── wpseed-*.po            # Language files
│   └── README.md              # Translation status
│
├── templates/                  # Template files
│
├── tests/                      # Unit tests
│   ├── bootstrap.php          # Test bootstrap
│   └── test-*.php             # Test files
│
├── loader.php                  # Main loader
├── wpseed.php                  # Plugin entry point
└── uninstall.php              # Uninstall handler
```

## Core Systems

### 1. Loader System
**File:** `loader.php`

Singleton pattern that:
- Defines constants
- Loads core files
- Initializes hooks
- Manages dependencies

### 2. Asset Management
**Files:** `assets/manage-assets.php`, `assets/queue-assets.php`

- Centralized asset registry
- Automatic page detection
- Missing file detection
- RTL stylesheet support

### 3. Listener System
**File:** `includes/classes/listener.php`

- Centralized form processing
- Automatic security checks
- Request tracking (dev mode)
- Decision logging

### 4. Notification System
**File:** `admin/notifications/notifications.php`

- Database-driven storage
- User-specific and global notifications
- Priority levels
- Email integration
- Admin bar bell icon

### 5. Settings Framework
**Files:** `includes/admin/settings/`

- Tabbed interface
- Multiple field types
- Repeater fields
- Import/export/reset

### 6. Background Processing
**Library:** Action Scheduler

- Reliable queue system
- Automatic retry
- Cron healthcheck
- Admin UI

## Design Patterns

### Singleton Pattern
Used for main plugin class:
```php
class WordPressPluginSeed {
    protected static $_instance = null;
    
    public static function instance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
}
```

### Factory Pattern
Used for API clients:
```php
$api = WPSeed_API_Factory::create('service_name');
```

### Registry Pattern
Used for object storage:
```php
WPSeed_Object_Registry::add('key', $object);
$obj = WPSeed_Object_Registry::get('key');
```

### Observer Pattern
Used throughout with WordPress hooks:
```php
do_action('wpseed_process_form_' . $action);
apply_filters('wpseed_data', $data);
```

## Data Flow

### Form Submission
1. User submits form with `wpseed_form_action`
2. Listener detects POST request
3. Security checks (nonce, user auth)
4. Fires action hook: `wpseed_process_form_{action}`
5. Handler processes data
6. Sets transient notice
7. Redirects user

### Background Task
1. Schedule action: `as_enqueue_async_action()`
2. Action Scheduler queues task
3. Cron processes queue
4. Callback executes
5. Task removed from queue

### Notification Flow
1. Create notification: `WPSeed_Notifications::create_notification()`
2. Store in database
3. Display in admin bar bell
4. User views notification center
5. Mark as read

## Database Schema

### wp_wpseed_notifications
```sql
CREATE TABLE wp_wpseed_notifications (
    id mediumint(9) NOT NULL AUTO_INCREMENT,
    type varchar(50) NOT NULL,
    message text NOT NULL,
    user_id bigint(20) NOT NULL DEFAULT 0,
    priority varchar(20) NOT NULL DEFAULT 'normal',
    is_read tinyint(1) NOT NULL DEFAULT 0,
    created_at datetime NOT NULL,
    expires_at datetime DEFAULT NULL,
    data longtext DEFAULT NULL,
    PRIMARY KEY (id),
    KEY user_read (user_id, is_read)
);
```

### wp_wpseed_request_log
```sql
CREATE TABLE wp_wpseed_request_log (
    id bigint(20) NOT NULL AUTO_INCREMENT,
    request_type varchar(10) NOT NULL,
    url text NOT NULL,
    post_data longtext,
    get_data longtext,
    user_id bigint(20) NOT NULL DEFAULT 0,
    ip_address varchar(45),
    status varchar(20),
    decision_reason text,
    created_at datetime NOT NULL,
    PRIMARY KEY (id),
    KEY user_id (user_id),
    KEY created_at (created_at)
);
```

## Hook System

### Actions
```php
// Initialization
do_action('wpseed_loaded');
do_action('wpseed_init');

// Form processing
do_action('wpseed_process_form_{action}');

// Notifications
do_action('wpseed_notification_created', $id, $type, $message);
```

### Filters
```php
// Data modification
apply_filters('wpseed_data', $data);
apply_filters('wpseed_settings', $settings);

// Asset URLs
apply_filters('wpseed_asset_url', $url, $type, $name);
```

## Security Layers

### 1. Nonce Verification
All forms include nonce fields:
```php
wp_nonce_field('action_name');
check_admin_referer('action_name', '_wpnonce');
```

### 2. Capability Checks
```php
if (!current_user_can('manage_options')) {
    wp_die(__('Unauthorized', 'wpseed'));
}
```

### 3. Input Sanitization
```php
$input = sanitize_text_field($_POST['field']);
$email = sanitize_email($_POST['email']);
```

### 4. Output Escaping
```php
echo esc_html($data);
echo esc_url($url);
echo esc_attr($attribute);
```

## Performance Optimization

### Caching
- Transients for temporary data
- Object cache for database queries
- Asset caching with version numbers

### Lazy Loading
- Admin files loaded only in admin
- Frontend files loaded only on frontend
- Libraries loaded on demand

### Database Optimization
- Indexed columns
- Efficient queries
- Batch operations

## Extensibility

### Adding Custom Features
1. Create class in `includes/classes/`
2. Load in `loader.php`
3. Hook into WordPress actions
4. Register admin pages if needed

### Creating Integrations
1. Add file to `examples/integrations/`
2. Follow existing patterns
3. Document usage
4. Test thoroughly

### Adding Settings
1. Create file in `includes/admin/settings/`
2. Register settings
3. Add to settings page
4. Handle form submission

## Development Workflow

### Local Development
1. Enable `WPSEED_DEV_MODE` constant
2. Enable `WP_DEBUG` and `WP_DEBUG_LOG`
3. Use footer debug area
4. Monitor listener activity

### Testing
1. Write PHPUnit tests in `tests/`
2. Run: `phpunit`
3. Check coverage
4. Test in multiple environments

### Deployment
1. Update version numbers
2. Generate POT file
3. Run tests
4. Create release tag
5. Deploy via GitHub

## Best Practices

### Code Organization
- One class per file
- Descriptive file names
- Logical directory structure
- Consistent naming conventions

### Documentation
- PHPDoc blocks for all functions
- Inline comments for complex logic
- README files in directories
- Keep docs updated

### Security
- Always verify nonces
- Check capabilities
- Sanitize input
- Escape output
- Use prepared statements

### Performance
- Minimize database queries
- Use transients wisely
- Lazy load when possible
- Optimize assets

---

Last Updated: 2025
