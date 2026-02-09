# Repeater Fields Documentation

## Overview

The WPSeed repeater field allows you to create dynamic, repeatable field groups in your settings pages. Users can add, remove, and reorder multiple instances of a field group.

## Features

- **Add/Remove Items** - Dynamically add or remove field groups
- **Drag & Drop Sorting** - Reorder items with drag and drop
- **Collapsible Items** - Collapse/expand items for better organization
- **Multiple Field Types** - Support for text, textarea, select, checkbox, email, url, number
- **Clean UI** - Professional interface matching WordPress admin design

## Basic Usage

### Simple Example

```php
array(
    'title'           => __( 'Social Media Links', 'wpseed' ),
    'desc'            => __( 'Add your social media profiles.', 'wpseed' ),
    'id'              => 'wpseed_social_links',
    'type'            => 'repeater',
    'item_title'      => __( 'Social Link', 'wpseed' ),
    'add_button_text' => __( 'Add Social Link', 'wpseed' ),
    'fields'          => array(
        array(
            'id'          => 'platform',
            'label'       => __( 'Platform', 'wpseed' ),
            'type'        => 'text',
            'placeholder' => __( 'e.g., Facebook', 'wpseed' ),
        ),
        array(
            'id'          => 'url',
            'label'       => __( 'Profile URL', 'wpseed' ),
            'type'        => 'url',
            'placeholder' => __( 'https://...', 'wpseed' ),
        ),
    ),
)
```

### Advanced Example with Multiple Field Types

```php
array(
    'title'           => __( 'API Credentials', 'wpseed' ),
    'desc'            => __( 'Add multiple API credentials.', 'wpseed' ),
    'id'              => 'wpseed_api_credentials',
    'type'            => 'repeater',
    'item_title'      => __( 'API Credential', 'wpseed' ),
    'add_button_text' => __( 'Add API Credential', 'wpseed' ),
    'fields'          => array(
        array(
            'id'          => 'service_name',
            'label'       => __( 'Service Name', 'wpseed' ),
            'type'        => 'text',
            'placeholder' => __( 'e.g., Stripe', 'wpseed' ),
        ),
        array(
            'id'          => 'api_key',
            'label'       => __( 'API Key', 'wpseed' ),
            'type'        => 'text',
        ),
        array(
            'id'      => 'environment',
            'label'   => __( 'Environment', 'wpseed' ),
            'type'    => 'select',
            'options' => array(
                'sandbox'    => __( 'Sandbox', 'wpseed' ),
                'production' => __( 'Production', 'wpseed' ),
            ),
            'default' => 'sandbox',
        ),
        array(
            'id'          => 'enabled',
            'type'        => 'checkbox',
            'description' => __( 'Enable this credential', 'wpseed' ),
            'default'     => 1,
        ),
    ),
)
```

## Field Configuration

### Main Repeater Options

| Option | Type | Required | Description |
|--------|------|----------|-------------|
| `id` | string | Yes | Unique identifier for the field |
| `title` | string | Yes | Field title displayed in settings |
| `type` | string | Yes | Must be 'repeater' |
| `desc` | string | No | Description text below title |
| `fields` | array | Yes | Array of field definitions |
| `item_title` | string | No | Title for each item (default: "Item") |
| `add_button_text` | string | No | Text for add button (default: "Add Item") |

### Sub-Field Options

| Option | Type | Required | Description |
|--------|------|----------|-------------|
| `id` | string | Yes | Field identifier (unique within repeater) |
| `label` | string | No | Field label |
| `type` | string | Yes | Field type (text, textarea, select, checkbox, email, url, number) |
| `placeholder` | string | No | Placeholder text |
| `class` | string | No | Additional CSS classes |
| `default` | mixed | No | Default value |
| `options` | array | No | Options for select fields |
| `description` | string | No | Description for checkbox fields |

## Supported Field Types

### Text Input Types
- `text` - Standard text input
- `email` - Email input with validation
- `url` - URL input with validation
- `number` - Numeric input

### Other Types
- `textarea` - Multi-line text input
- `select` - Dropdown selection
- `checkbox` - Single checkbox

## Retrieving Data

### Get All Items

```php
$items = get_option( 'wpseed_social_links', array() );

foreach ( $items as $item ) {
    echo $item['platform'] . ': ' . $item['url'];
}
```

### Using WPSeed Settings API

```php
$items = WPSeed_Admin_Settings::get_option( 'wpseed_social_links', array() );
```

### Check if Items Exist

```php
$items = get_option( 'wpseed_social_links', array() );

if ( ! empty( $items ) ) {
    // Process items
}
```

## Data Structure

Data is stored as a serialized array in the WordPress options table:

```php
array(
    0 => array(
        'platform' => 'Facebook',
        'url'      => 'https://facebook.com/example',
    ),
    1 => array(
        'platform' => 'Twitter',
        'url'      => 'https://twitter.com/example',
    ),
)
```

## Use Cases

### 1. API Credentials Management
Store multiple API keys for different services or environments.

### 2. Custom Rules Engine
Define multiple rules with conditions and actions.

### 3. Social Media Links
Manage multiple social media profile links.

### 4. Team Members
Add team member information with name, role, and bio.

### 5. Pricing Tables
Create multiple pricing tiers with features.

### 6. Testimonials
Manage customer testimonials with name, company, and quote.

### 7. FAQ Items
Build FAQ sections with questions and answers.

### 8. Custom Redirects
Define URL redirect rules.

## Styling

The repeater field comes with default styling that matches WordPress admin design. You can customize the appearance by overriding CSS:

```css
.wpseed-repeater-item {
    /* Custom item styling */
}

.wpseed-repeater-item-header {
    /* Custom header styling */
}
```

## JavaScript Events

The repeater field triggers custom events you can hook into:

```javascript
jQuery(document).on('wpseed_repeater_item_added', function(e, $item) {
    // Item was added
});

jQuery(document).on('wpseed_repeater_item_removed', function(e, $item) {
    // Item was removed
});
```

## Best Practices

1. **Limit Field Count** - Don't add too many fields per item (5-7 max recommended)
2. **Use Clear Labels** - Make field purposes obvious
3. **Provide Defaults** - Set sensible default values
4. **Validate Data** - Always validate and sanitize on save
5. **Consider Performance** - Large repeater datasets may impact page load

## Troubleshooting

### Items Not Saving

Check that:
- Field ID is unique
- Nonce verification is passing
- No JavaScript errors in console

### Styling Issues

Ensure:
- CSS file is enqueued properly
- No theme conflicts
- Browser cache is cleared

### Sorting Not Working

Verify:
- jQuery UI Sortable is loaded
- No JavaScript conflicts
- Items have proper structure

## Migration from Other Plugins

If migrating from ACF or CMB2 repeater fields, you'll need to transform the data structure to match WPSeed's format.

## Version History

- **1.1.0** - Initial release of repeater field functionality

## Support

For issues or questions:
- GitHub Issues: https://github.com/ryanbayne/wpseed/issues
- Documentation: https://github.com/ryanbayne/wpseed/docs
