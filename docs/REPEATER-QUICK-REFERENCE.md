# Repeater Fields - Quick Reference

## Basic Syntax

```php
array(
    'id'              => 'field_id',
    'title'           => 'Field Title',
    'type'            => 'repeater',
    'item_title'      => 'Item',
    'add_button_text' => 'Add Item',
    'fields'          => array(
        // Sub-fields here
    ),
)
```

## Field Types

```php
// Text
array('id' => 'name', 'label' => 'Name', 'type' => 'text')

// Email
array('id' => 'email', 'label' => 'Email', 'type' => 'email')

// URL
array('id' => 'website', 'label' => 'Website', 'type' => 'url')

// Number
array('id' => 'age', 'label' => 'Age', 'type' => 'number')

// Textarea
array('id' => 'bio', 'label' => 'Bio', 'type' => 'textarea')

// Select
array(
    'id'      => 'status',
    'label'   => 'Status',
    'type'    => 'select',
    'options' => array(
        'active'   => 'Active',
        'inactive' => 'Inactive',
    ),
)

// Checkbox
array(
    'id'          => 'featured',
    'type'        => 'checkbox',
    'description' => 'Mark as featured',
)
```

## Retrieve Data

```php
// Get all items
$items = get_option('field_id', array());

// Loop through items
foreach ($items as $item) {
    echo $item['name'];
    echo $item['email'];
}

// Using WPSeed API
$items = WPSeed_Admin_Settings::get_option('field_id', array());
```

## Common Patterns

### API Credentials
```php
'fields' => array(
    array('id' => 'service', 'label' => 'Service', 'type' => 'text'),
    array('id' => 'api_key', 'label' => 'API Key', 'type' => 'text'),
    array('id' => 'api_secret', 'label' => 'Secret', 'type' => 'text'),
)
```

### Social Links
```php
'fields' => array(
    array('id' => 'platform', 'label' => 'Platform', 'type' => 'text'),
    array('id' => 'url', 'label' => 'URL', 'type' => 'url'),
)
```

### Team Members
```php
'fields' => array(
    array('id' => 'name', 'label' => 'Name', 'type' => 'text'),
    array('id' => 'role', 'label' => 'Role', 'type' => 'text'),
    array('id' => 'bio', 'label' => 'Bio', 'type' => 'textarea'),
    array('id' => 'email', 'label' => 'Email', 'type' => 'email'),
)
```

## Tips

- Keep 5-7 fields max per repeater
- Always provide default values
- Use clear, descriptive labels
- Validate data on retrieval
- Consider performance with large datasets
