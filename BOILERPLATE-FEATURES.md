# WPSeed Boilerplate Features

## New Features Added

### 1. REST API Support
**Location**: `includes/classes/rest-controller.php`, `includes/classes/rest-example.php`

**Test endpoint** (requires admin login):
```
GET /wp-json/wpseed/v1/example
```

**Create custom endpoint**:
```php
class My_REST_Controller extends WPSeed_REST_Controller {
    protected $rest_base = 'myendpoint';
    
    public function register_routes() {
        register_rest_route($this->namespace, '/' . $this->rest_base, array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => array($this, 'get_items'),
            'permission_callback' => array($this, 'get_items_permissions_check'),
        ));
    }
    
    public function get_items($request) {
        return rest_ensure_response(array('data' => 'your data'));
    }
}
```

**Security Options**:
```php
// Admin only (default - secure)
public function get_items_permissions_check($request) {
    return current_user_can('manage_options');
}

// Public access (use with caution)
public function get_items_permissions_check($request) {
    return true;
}

// Logged-in users only
public function get_items_permissions_check($request) {
    return is_user_logged_in();
}

// Custom capability
public function get_items_permissions_check($request) {
    return current_user_can('edit_posts');
}
```

### 2. WP-CLI Commands
**Location**: `includes/classes/cli-commands.php`

**Usage**:
```bash
wp wpseed info
wp wpseed cache clear
```

### 3. Internationalization (i18n)
**Location**: `includes/classes/i18n.php`

Automatically loads translation files from `/languages/` directory.

### 4. Enhanced Uninstall
**Location**: `uninstall.php`

Automatically cleans:
- Plugin options
- Transients
- User meta
- Scheduled hooks

### 5. Dependency Checker
**Location**: `includes/classes/dependencies.php`

**Usage**:
```php
$deps = new WPSeed_Dependencies();
$deps->add_dependency('plugin-folder/plugin.php', 'Plugin Name', '1.0.0');
```

### 6. Multisite Support
**Location**: `includes/classes/multisite.php`

**Check if network activated**:
```php
WPSeed_Multisite::is_network_activated();
```

### 7. Unit Testing
**Location**: `tests/`, `phpunit.xml`

**Run tests**:
```bash
phpunit
```

### 8. GitHub Actions CI/CD
**Location**: `.github/workflows/ci.yml`

Automatically runs tests on push/PR to main/develop branches.

## Testing New Features

1. **REST API**: 
   - Requires authentication by default (secure)
   - Test as admin: Visit `/wp-json/wpseed/v1/example` while logged in
   - For public endpoints: Override `get_items_permissions_check()` to return `true`
2. **WP-CLI**: Run `wp wpseed info` in terminal
3. **i18n**: Create `.po` files in `/languages/`
4. **Uninstall**: Delete plugin to test cleanup
5. **Tests**: Run `phpunit` command

## Next Steps

- Add custom REST endpoints
- Create WP-CLI commands for your plugin
- Add translation files
- Write unit tests
- Configure GitHub Actions
