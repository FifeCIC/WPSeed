# Listener Implementation Patterns

## Overview
WPSeed's listener system provides centralized, secure form processing with automatic tracking and decision logging in developer mode.

---

## Basic Usage

### 1. Create a Form with Listener Action

```php
<form method="post">
    <?php wp_nonce_field('my_custom_action'); ?>
    <input type="hidden" name="wpseed_form_action" value="my_custom_action">
    
    <input type="text" name="user_input" required>
    <button type="submit">Submit</button>
</form>
```

### 2. Hook Your Processing Function

```php
add_action('wpseed_process_form_my_custom_action', 'handle_my_custom_action');

function handle_my_custom_action() {
    $user_input = sanitize_text_field($_POST['user_input']);
    
    // Process data
    update_option('my_custom_option', $user_input);
    
    // Set success notice
    set_transient('wpseed_admin_notice', array(
        'type' => 'success',
        'message' => 'Settings saved successfully!'
    ), 30);
    
    // Redirect to prevent resubmission
    wp_redirect(wp_get_referer());
    exit;
}
```

---

## Security Features

### Automatic Security Checks
The listener automatically handles:
- ✅ Nonce verification
- ✅ User authentication check
- ✅ Action sanitization
- ✅ Request method validation

### What You Still Need to Do
- Sanitize/validate all input data
- Check user capabilities for specific actions
- Escape output data

```php
add_action('wpseed_process_form_delete_item', 'handle_delete_item');

function handle_delete_item() {
    // Check capability
    if (!current_user_can('delete_posts')) {
        wp_die(__('Unauthorized', 'wpseed'));
    }
    
    // Validate input
    $item_id = absint($_POST['item_id']);
    if (!$item_id) {
        wp_die(__('Invalid item ID', 'wpseed'));
    }
    
    // Process
    wp_delete_post($item_id, true);
    
    wp_redirect(admin_url('admin.php?page=my-page'));
    exit;
}
```

---

## Developer Mode Features

### Request Tracking
When developer mode is enabled, the listener automatically logs:
- Request type (POST/GET/AJAX)
- URL and timestamp
- POST/GET data (JSON encoded)
- User ID and IP address
- Processing status and decision reason

### Decision Logging
The listener logs why each request was:
- **Processed**: Action executed successfully
- **Rejected**: Failed security checks
- **Skipped**: No wpseed_form_action field

### View Logs
Enable footer debug to see recent requests:
1. Click "WPSeed Dev" in admin toolbar
2. Toggle "Footer Debug: ON"
3. View "Recent Requests" table at page bottom

---

## Advanced Patterns

### Pattern 1: AJAX Form Processing

```php
// Form with AJAX
<form id="ajax-form">
    <?php wp_nonce_field('ajax_action'); ?>
    <input type="hidden" name="action" value="wpseed_ajax_handler">
    <input type="hidden" name="wpseed_form_action" value="ajax_action">
    <input type="text" name="data">
    <button type="submit">Submit</button>
</form>

<script>
jQuery('#ajax-form').on('submit', function(e) {
    e.preventDefault();
    jQuery.post(ajaxurl, jQuery(this).serialize(), function(response) {
        alert('Success!');
    });
});
</script>
```

```php
// Handler
add_action('wp_ajax_wpseed_ajax_handler', 'handle_ajax_request');

function handle_ajax_request() {
    // Listener will verify nonce and user
    do_action('wpseed_process_form_ajax_action');
    wp_send_json_success();
}

add_action('wpseed_process_form_ajax_action', 'process_ajax_data');

function process_ajax_data() {
    $data = sanitize_text_field($_POST['data']);
    update_option('ajax_data', $data);
}
```

### Pattern 2: Multi-Step Forms

```php
add_action('wpseed_process_form_step_1', 'handle_step_1');
add_action('wpseed_process_form_step_2', 'handle_step_2');

function handle_step_1() {
    // Save step 1 data to transient
    set_transient('form_step_1_' . get_current_user_id(), $_POST, 600);
    
    wp_redirect(admin_url('admin.php?page=form-step-2'));
    exit;
}

function handle_step_2() {
    // Retrieve step 1 data
    $step_1 = get_transient('form_step_1_' . get_current_user_id());
    
    // Combine and process
    $final_data = array_merge($step_1, $_POST);
    
    // Save and cleanup
    update_option('final_data', $final_data);
    delete_transient('form_step_1_' . get_current_user_id());
    
    wp_redirect(admin_url('admin.php?page=success'));
    exit;
}
```

### Pattern 3: Bulk Actions

```php
add_action('wpseed_process_form_bulk_delete', 'handle_bulk_delete');

function handle_bulk_delete() {
    if (!current_user_can('delete_posts')) {
        wp_die(__('Unauthorized', 'wpseed'));
    }
    
    $items = isset($_POST['items']) ? (array) $_POST['items'] : array();
    $deleted = 0;
    
    foreach ($items as $item_id) {
        $item_id = absint($item_id);
        if ($item_id && wp_delete_post($item_id, true)) {
            $deleted++;
        }
    }
    
    set_transient('wpseed_admin_notice', array(
        'type' => 'success',
        'message' => sprintf(__('%d items deleted', 'wpseed'), $deleted)
    ), 30);
    
    wp_redirect(wp_get_referer());
    exit;
}
```

---

## Best Practices

### 1. Always Redirect After POST
Prevents form resubmission on page refresh:
```php
wp_redirect(wp_get_referer());
exit;
```

### 2. Use Specific Action Names
Be descriptive to avoid conflicts:
```php
// Good
wpseed_form_action="save_api_settings"

// Bad
wpseed_form_action="save"
```

### 3. Validate Early, Fail Fast
```php
function handle_form() {
    // Validate first
    if (empty($_POST['required_field'])) {
        set_transient('wpseed_admin_notice', array(
            'type' => 'error',
            'message' => 'Required field missing'
        ), 30);
        wp_redirect(wp_get_referer());
        exit;
    }
    
    // Then process
    // ...
}
```

### 4. Use Transients for Notices
Single-use notices that disappear after display:
```php
set_transient('wpseed_admin_notice', array(
    'type' => 'success', // success, error, warning, info
    'message' => 'Your message here'
), 30); // 30 seconds expiration
```

### 5. Check Capabilities
```php
if (!current_user_can('manage_options')) {
    wp_die(__('Unauthorized', 'wpseed'));
}
```

---

## Debugging

### Enable Developer Mode
Developer mode is auto-enabled on:
- localhost
- 127.0.0.1
- *.local, *.test, *.dev domains
- When `WPSEED_DEV_MODE` constant is true

### View Request Logs
```php
// Get recent requests programmatically
$requests = WPSeed_Listener::get_recent_requests(20);

foreach ($requests as $req) {
    echo $req->request_type . ': ' . $req->url . ' - ' . $req->status;
}
```

### Check Decision Reasons
Look for these in the footer debug table:
- "No wpseed_form_action field" - Form missing required field
- "User not logged in" - Authentication failed
- "Nonce verification failed" - Security check failed
- "Action: {action_name}" - Successfully processed

---

## Migration from Other Systems

### From WordPress Settings API
```php
// Old way
add_settings_section(...);
add_settings_field(...);
register_setting(...);

// New way
<form method="post">
    <?php wp_nonce_field('save_settings'); ?>
    <input type="hidden" name="wpseed_form_action" value="save_settings">
    <!-- fields -->
</form>

add_action('wpseed_process_form_save_settings', 'handle_save');
```

### From Direct POST Handling
```php
// Old way
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    check_admin_referer('my_action');
    // process
}

// New way
add_action('wpseed_process_form_my_action', 'handle_my_action');
```

---

## Common Issues

### Issue: Form Not Processing
**Check:**
1. Form has `wpseed_form_action` field
2. Nonce field matches action name
3. User is logged in
4. Action hook is registered

### Issue: Notice Not Displaying
**Check:**
1. Transient name is `wpseed_admin_notice`
2. Redirect happens after setting transient
3. You're on an admin page

### Issue: Request Not Logged
**Check:**
1. Developer mode is enabled
2. Request has POST or GET data
3. Database table exists (auto-created)

---

## Examples in WPSeed

See these files for working examples:
- `includes/admin/settings/settings-page.php` - Settings forms
- `examples/` - Various integration examples
- `includes/classes/listener.php` - Core implementation

---

**Last Updated**: 2025
**WPSeed Version**: 1.0.0
