# Integration Examples

## WooCommerce

**File:** `examples/integrations/woocommerce-example.php`

**Features:**
- Custom product fields
- Order meta
- Admin columns
- Cart data

**Usage:**
```php
if (class_exists('WooCommerce')) {
    include_once WPSEED_PLUGIN_DIR . 'examples/integrations/woocommerce-example.php';
}
```

## ACF

**File:** `examples/integrations/acf-example.php`

**Features:**
- Field group registration
- Post meta fields
- Helper functions

**Usage:**
```php
if (function_exists('acf_add_local_field_group')) {
    include_once WPSEED_PLUGIN_DIR . 'examples/integrations/acf-example.php';
}
```

## Elementor

**File:** `examples/integrations/elementor-example.php`

**Features:**
- Custom widget
- Widget controls
- Custom category

**Usage:**
```php
if (did_action('elementor/loaded')) {
    include_once WPSEED_PLUGIN_DIR . 'examples/integrations/elementor-example.php';
}
```
