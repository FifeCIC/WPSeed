# Integration Examples

WPSeed includes 12 ready-to-use integration examples for popular WordPress plugins. Each example demonstrates best practices for extending and integrating with third-party plugins.

## Table of Contents

1. [WooCommerce](#woocommerce)
2. [Advanced Custom Fields (ACF)](#advanced-custom-fields-acf)
3. [Elementor](#elementor)
4. [Contact Form 7](#contact-form-7)
5. [Yoast SEO](#yoast-seo)
6. [Gravity Forms](#gravity-forms)
7. [BuddyPress](#buddypress)
8. [Easy Digital Downloads](#easy-digital-downloads)
9. [bbPress](#bbpress)
10. [LearnDash](#learndash)
11. [MemberPress](#memberpress)
12. [WPForms](#wpforms)

---

## WooCommerce

**File:** `examples/integrations/woocommerce-example.php`

**Features:**
- Custom product fields in admin
- Order meta data storage
- Custom admin columns
- Product and order hooks

**Usage:**
```php
if (class_exists('WooCommerce')) {
    include_once WPSEED_PLUGIN_DIR . 'examples/integrations/woocommerce-example.php';
}
```

**Key Hooks:**
- `woocommerce_product_options_general_product_data`
- `woocommerce_process_product_meta`
- `woocommerce_checkout_create_order`

---

## Advanced Custom Fields (ACF)

**File:** `examples/integrations/acf-example.php`

**Features:**
- Field group registration
- Custom field types
- Post meta integration

**Usage:**
```php
if (function_exists('acf_add_local_field_group')) {
    include_once WPSEED_PLUGIN_DIR . 'examples/integrations/acf-example.php';
}
```

---

## Elementor

**File:** `examples/integrations/elementor-example.php`

**Features:**
- Custom widget creation
- Widget controls and settings
- Custom widget category

**Usage:**
```php
if (did_action('elementor/loaded')) {
    include_once WPSEED_PLUGIN_DIR . 'examples/integrations/elementor-example.php';
}
```

**Key Actions:**
- `elementor/widgets/register`
- `elementor/elements/categories_registered`

---

## Contact Form 7

**File:** `examples/integrations/contact-form-7-example.php`

**Features:**
- Form submission logging
- Custom validation
- Before/after send hooks
- Database storage

**Usage:**
```php
if (function_exists('wpcf7')) {
    include_once WPSEED_PLUGIN_DIR . 'examples/integrations/contact-form-7-example.php';
}
```

**Key Hooks:**
- `wpcf7_before_send_mail`
- `wpcf7_mail_sent`
- `wpcf7_validate_text*`

---

## Yoast SEO

**File:** `examples/integrations/yoast-seo-example.php`

**Features:**
- Custom meta descriptions
- Title customization
- Open Graph integration
- Custom OG images

**Usage:**
```php
if (defined('WPSEO_VERSION')) {
    include_once WPSEED_PLUGIN_DIR . 'examples/integrations/yoast-seo-example.php';
}
```

**Key Filters:**
- `wpseo_metadesc`
- `wpseo_title`
- `wpseo_opengraph_desc`

---

## Gravity Forms

**File:** `examples/integrations/gravity-forms-example.php`

**Features:**
- Form submission processing
- Custom validation rules
- Dynamic field population
- Custom confirmation messages
- Entry logging

**Usage:**
```php
if (class_exists('GFForms')) {
    include_once WPSEED_PLUGIN_DIR . 'examples/integrations/gravity-forms-example.php';
}
```

**Key Hooks:**
- `gform_after_submission`
- `gform_validation`
- `gform_pre_render`

---

## BuddyPress

**File:** `examples/integrations/buddypress-example.php`

**Features:**
- Custom profile tabs
- Profile field extensions
- Activity stream customization
- User meta integration

**Usage:**
```php
if (function_exists('buddypress')) {
    include_once WPSEED_PLUGIN_DIR . 'examples/integrations/buddypress-example.php';
}
```

**Key Actions:**
- `bp_setup_nav`
- `bp_after_profile_field_content`
- `xprofile_updated_profile`

---

## Easy Digital Downloads

**File:** `examples/integrations/easy-digital-downloads-example.php`

**Features:**
- Custom download meta fields
- Purchase completion hooks
- Custom receipt content
- Admin columns
- Purchase logging

**Usage:**
```php
if (class_exists('Easy_Digital_Downloads')) {
    include_once WPSEED_PLUGIN_DIR . 'examples/integrations/easy-digital-downloads-example.php';
}
```

**Key Hooks:**
- `edd_complete_purchase`
- `edd_meta_box_fields`
- `edd_purchase_receipt`

---

## bbPress

**File:** `examples/integrations/bbpress-example.php`

**Features:**
- Topic and reply hooks
- Custom topic fields
- Content modification
- Activity logging

**Usage:**
```php
if (class_exists('bbPress')) {
    include_once WPSEED_PLUGIN_DIR . 'examples/integrations/bbpress-example.php';
}
```

**Key Actions:**
- `bbp_new_topic`
- `bbp_new_reply`
- `bbp_theme_before_topic_form_submit_wrapper`

---

## LearnDash

**File:** `examples/integrations/learndash-example.php`

**Features:**
- Course completion tracking
- Lesson progress monitoring
- Quiz result logging
- Points/rewards system
- Custom course fields

**Usage:**
```php
if (defined('LEARNDASH_VERSION')) {
    include_once WPSEED_PLUGIN_DIR . 'examples/integrations/learndash-example.php';
}
```

**Key Actions:**
- `learndash_course_completed`
- `learndash_lesson_completed`
- `learndash_quiz_completed`

---

## MemberPress

**File:** `examples/integrations/memberpress-example.php`

**Features:**
- Transaction completion hooks
- Subscription lifecycle management
- Custom account validation
- Welcome email automation
- Activity logging

**Usage:**
```php
if (defined('MEPR_VERSION')) {
    include_once WPSEED_PLUGIN_DIR . 'examples/integrations/memberpress-example.php';
}
```

**Key Actions:**
- `mepr-event-transaction-completed`
- `mepr-event-subscription-created`
- `mepr-event-subscription-stopped`

---

## WPForms

**File:** `examples/integrations/wpforms-example.php`

**Features:**
- Form submission processing
- Custom validation rules
- Confirmation message customization
- Submission logging

**Usage:**
```php
if (function_exists('wpforms')) {
    include_once WPSEED_PLUGIN_DIR . 'examples/integrations/wpforms-example.php';
}
```

**Key Actions:**
- `wpforms_process_complete`
- `wpforms_process_before_form_data`
- `wpforms_frontend_confirmation_message`

---

## General Integration Tips

### 1. Always Check if Plugin is Active

```php
if (class_exists('PluginClass') || function_exists('plugin_function')) {
    // Your integration code
}
```

### 2. Use Proper Hook Priorities

```php
add_action('hook_name', 'callback', 10, 2); // Priority 10, 2 arguments
```

### 3. Sanitize and Validate Data

```php
$value = sanitize_text_field($_POST['field']);
$email = sanitize_email($_POST['email']);
```

### 4. Log Important Events

```php
do_action('wpseed_integration_event', $data);
```

### 5. Use Transients for Caching

```php
$data = get_transient('wpseed_cache_key');
if (false === $data) {
    $data = expensive_operation();
    set_transient('wpseed_cache_key', $data, HOUR_IN_SECONDS);
}
```

---

## Creating Your Own Integration

### Step 1: Create Integration File

Create a new file in `examples/integrations/your-plugin-example.php`

### Step 2: Check Plugin Availability

```php
if (!class_exists('YourPlugin')) {
    return;
}
```

### Step 3: Create Integration Class

```php
class WPSeed_YourPlugin_Integration {
    public function __construct() {
        add_action('plugin_hook', array($this, 'method'));
    }
    
    public function method() {
        // Your code
    }
}

new WPSeed_YourPlugin_Integration();
```

### Step 4: Include in Main Plugin

```php
if (class_exists('YourPlugin')) {
    include_once WPSEED_PLUGIN_DIR . 'examples/integrations/your-plugin-example.php';
}
```

---

## Support

For integration questions:
- Check plugin documentation
- Review example code
- Visit GitHub Issues
- Join community discussions
