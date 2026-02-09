# Carbon Fields Integration

WPSeed includes **Carbon Fields** - a modern, developer-friendly WordPress custom fields library used by thousands of themes and plugins.

## Why Carbon Fields?

- ✅ **Modern API**: Clean, intuitive syntax
- ✅ **Lightweight**: Smaller than ACF or Redux
- ✅ **GPL Licensed**: Free for commercial use
- ✅ **Well Maintained**: Active development
- ✅ **Flexible**: Theme options, post meta, term meta, user meta
- ✅ **No UI Builder**: Code-based (better for version control)

## Quick Start

### Create Theme Options Page

```php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

add_action('wpseed_carbon_fields_register', 'my_theme_options');
function my_theme_options() {
    Container::make('theme_options', __('My Settings'))
        ->set_page_parent('wpseed-settings')
        ->add_fields(array(
            Field::make('text', 'site_phone', 'Phone Number'),
            Field::make('textarea', 'site_address', 'Address'),
        ));
}
```

### Create Post Meta Box

```php
add_action('wpseed_carbon_fields_register', 'my_post_meta');
function my_post_meta() {
    Container::make('post_meta', __('Product Info'))
        ->where('post_type', '=', 'product')
        ->add_fields(array(
            Field::make('text', 'price', 'Price'),
            Field::make('text', 'sku', 'SKU'),
            Field::make('select', 'stock_status', 'Stock Status')
                ->add_options(array(
                    'in_stock' => 'In Stock',
                    'out_of_stock' => 'Out of Stock',
                )),
        ));
}
```

### Get Field Values

```php
// Theme option
$phone = carbon_get_theme_option('site_phone');

// Post meta
$price = carbon_get_post_meta($post_id, 'price');

// Term meta
$color = carbon_get_term_meta($term_id, 'category_color');

// User meta
$role = carbon_get_user_meta($user_id, 'custom_role');
```

## Field Types

### Basic Fields

```php
Field::make('text', 'field_name', 'Label')
Field::make('textarea', 'field_name', 'Label')
Field::make('rich_text', 'field_name', 'Label')
Field::make('checkbox', 'field_name', 'Label')
```

### Choice Fields

```php
Field::make('select', 'field_name', 'Label')
    ->add_options(array(
        'value1' => 'Label 1',
        'value2' => 'Label 2',
    ))

Field::make('radio', 'field_name', 'Label')
    ->add_options(array(
        'option1' => 'Option 1',
        'option2' => 'Option 2',
    ))

Field::make('multiselect', 'field_name', 'Label')
    ->add_options(array(
        'val1' => 'Label 1',
        'val2' => 'Label 2',
    ))
```

### Media Fields

```php
Field::make('image', 'field_name', 'Label')
Field::make('file', 'field_name', 'Label')
Field::make('gallery', 'field_name', 'Label')
```

### Date/Time Fields

```php
Field::make('date', 'field_name', 'Label')
Field::make('time', 'field_name', 'Label')
Field::make('date_time', 'field_name', 'Label')
```

### Advanced Fields

```php
Field::make('color', 'field_name', 'Label')
Field::make('map', 'field_name', 'Label')
Field::make('html', 'field_name')->set_html('<p>Custom HTML</p>')
```

## Complex Fields (Repeaters)

```php
Field::make('complex', 'team_members', 'Team Members')
    ->add_fields(array(
        Field::make('text', 'name', 'Name'),
        Field::make('text', 'position', 'Position'),
        Field::make('image', 'photo', 'Photo'),
        Field::make('textarea', 'bio', 'Bio'),
    ))
    ->set_layout('tabbed-vertical')
```

### Get Complex Field Values

```php
$team_members = carbon_get_theme_option('team_members');

foreach ($team_members as $member) {
    echo $member['name'];
    echo $member['position'];
    echo wp_get_attachment_image($member['photo'], 'thumbnail');
    echo $member['bio'];
}
```

## Conditional Logic

```php
Field::make('checkbox', 'enable_api', 'Enable API')

Field::make('text', 'api_key', 'API Key')
    ->set_conditional_logic(array(
        array(
            'field' => 'enable_api',
            'value' => true,
        )
    ))
```

### Multiple Conditions

```php
Field::make('text', 'field_name', 'Label')
    ->set_conditional_logic(array(
        'relation' => 'AND', // or 'OR'
        array(
            'field' => 'enable_feature',
            'value' => true,
        ),
        array(
            'field' => 'user_type',
            'value' => 'premium',
        ),
    ))
```

## Tabs

```php
Container::make('theme_options', __('Settings'))
    ->add_tab(__('General'), array(
        Field::make('text', 'site_name', 'Site Name'),
    ))
    ->add_tab(__('Social'), array(
        Field::make('text', 'facebook', 'Facebook'),
        Field::make('text', 'twitter', 'Twitter'),
    ))
```

## Field Methods

### Required Fields

```php
Field::make('text', 'field_name', 'Label')
    ->set_required(true)
```

### Default Values

```php
Field::make('text', 'field_name', 'Label')
    ->set_default_value('Default text')
```

### Help Text

```php
Field::make('text', 'field_name', 'Label')
    ->set_help_text('This is a helpful description')
```

### Width

```php
Field::make('text', 'first_name', 'First Name')
    ->set_width(50)

Field::make('text', 'last_name', 'Last Name')
    ->set_width(50)
```

## Container Types

### Theme Options

```php
Container::make('theme_options', __('Settings'))
    ->set_page_parent('wpseed-settings')
```

### Post Meta

```php
Container::make('post_meta', __('Meta Box'))
    ->where('post_type', '=', 'post')
    ->where('post_template', '=', 'template-custom.php')
```

### Term Meta

```php
Container::make('term_meta', __('Category Fields'))
    ->where('term_taxonomy', 'IN', array('category', 'post_tag'))
```

### User Meta

```php
Container::make('user_meta', __('User Fields'))
    ->where('user_role', 'IN', array('administrator', 'editor'))
```

## WPSeed Helper Methods

```php
// Create options page
WPSeed_Carbon_Fields::create_options_page('My Settings', 'my-settings', 'wpseed-settings');

// Create post meta
WPSeed_Carbon_Fields::create_post_meta('Product Info', array('product'));

// Create term meta
WPSeed_Carbon_Fields::create_term_meta('Category Fields', array('category'));

// Create user meta
WPSeed_Carbon_Fields::create_user_meta('User Profile');
```

## Common Use Cases

### E-commerce Product Fields

```php
Container::make('post_meta', __('Product Details'))
    ->where('post_type', '=', 'product')
    ->add_fields(array(
        Field::make('text', 'price', 'Price'),
        Field::make('text', 'sale_price', 'Sale Price'),
        Field::make('text', 'sku', 'SKU'),
        Field::make('select', 'stock_status', 'Stock')
            ->add_options(array(
                'in_stock' => 'In Stock',
                'out_of_stock' => 'Out of Stock',
                'backorder' => 'On Backorder',
            )),
        Field::make('gallery', 'product_gallery', 'Gallery'),
    ));
```

### Team Members

```php
Container::make('theme_options', __('Team'))
    ->add_fields(array(
        Field::make('complex', 'team_members', 'Team Members')
            ->add_fields(array(
                Field::make('text', 'name', 'Name'),
                Field::make('text', 'position', 'Position'),
                Field::make('text', 'email', 'Email'),
                Field::make('image', 'photo', 'Photo'),
                Field::make('rich_text', 'bio', 'Bio'),
            ))
    ));
```

### Social Media Links

```php
Container::make('theme_options', __('Social Media'))
    ->add_fields(array(
        Field::make('text', 'facebook_url', 'Facebook'),
        Field::make('text', 'twitter_url', 'Twitter'),
        Field::make('text', 'instagram_url', 'Instagram'),
        Field::make('text', 'linkedin_url', 'LinkedIn'),
    ));
```

## Best Practices

1. **Use Prefixes**: Always prefix field names (`wpseed_field_name`)
2. **Organize with Tabs**: Group related fields
3. **Add Help Text**: Guide users with descriptions
4. **Use Conditional Logic**: Show/hide fields based on conditions
5. **Validate Input**: Use `set_required()` for mandatory fields
6. **Set Defaults**: Provide sensible default values

## Resources

- **Carbon Fields Docs**: https://carbonfields.net/docs/
- **GitHub**: https://github.com/htmlburger/carbon-fields
- **WPSeed Examples**: `/examples/carbon-fields-examples.php`

## Migration from Other Systems

### From ACF

```php
// ACF
get_field('field_name');

// Carbon Fields
carbon_get_theme_option('field_name');
carbon_get_post_meta($post_id, 'field_name');
```

### From Redux Framework

```php
// Redux
global $redux_options;
$value = $redux_options['field_name'];

// Carbon Fields
$value = carbon_get_theme_option('field_name');
```

## Support

For Carbon Fields specific issues:
- Documentation: https://carbonfields.net/docs/
- GitHub Issues: https://github.com/htmlburger/carbon-fields/issues

For WPSeed integration issues:
- GitHub: https://github.com/ryanbayne/wpseed/issues
