# Third-Party Libraries in WPSeed

WPSeed bundles several third-party libraries to provide advanced functionality without requiring users to install dependencies. This document covers usage, licensing, and modification policies.

---

## 📦 Bundled Libraries

### Action Scheduler (v3.8.1)
**Purpose**: Background task processing  
**License**: GPL-3.0  
**Author**: Automattic (WooCommerce team)  
**Location**: `/includes/libraries/action-scheduler/`  
**GitHub**: https://github.com/woocommerce/action-scheduler

#### What It Does
Action Scheduler is a battle-tested job queue system used by WooCommerce to handle millions of background tasks daily. It provides:
- Reliable background processing
- Automatic retry on failure
- Task scheduling and recurring tasks
- Admin UI for monitoring

#### Usage Example

```php
// Schedule a one-time action
as_schedule_single_action( time() + 3600, 'wpseed_process_data', array( 'user_id' => 123 ) );

// Schedule a recurring action (every hour)
as_schedule_recurring_action( time(), HOUR_IN_SECONDS, 'wpseed_hourly_sync' );

// Hook your callback
add_action( 'wpseed_process_data', 'wpseed_handle_data_processing' );
function wpseed_handle_data_processing( $user_id ) {
    // Your processing logic here
    error_log( 'Processing data for user: ' . $user_id );
}

// Check if action is scheduled
if ( as_next_scheduled_action( 'wpseed_process_data' ) ) {
    // Action is already scheduled
}

// Cancel scheduled action
as_unschedule_action( 'wpseed_process_data', array( 'user_id' => 123 ) );
```

#### When to Use
- Processing large datasets in background
- Sending bulk emails
- API synchronization tasks
- Scheduled maintenance operations
- Any long-running task that shouldn't block page load

---

### Carbon Fields (v3.6.3)
**Purpose**: Modern custom fields framework  
**License**: GPL-2.0  
**Author**: htmlBurger (Miroslav Mitev, Atanas Angelov, Siyan Panayotov)  
**Location**: `/includes/libraries/carbon-fields/`  
**GitHub**: https://github.com/htmlburger/carbon-fields

#### What It Does
Carbon Fields provides a developer-friendly API for creating custom fields in WordPress:
- Theme options pages
- Post meta boxes
- Term meta
- User meta
- Widget fields

#### Usage Example

```php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

// Boot Carbon Fields (already done in WPSeed loader.php)
add_action( 'after_setup_theme', 'wpseed_load_carbon_fields' );
function wpseed_load_carbon_fields() {
    \Carbon_Fields\Carbon_Fields::boot();
}

// Register fields
add_action( 'carbon_fields_register_fields', 'wpseed_register_custom_fields' );
function wpseed_register_custom_fields() {
    
    // Theme Options Page
    Container::make( 'theme_options', __( 'Theme Settings' ) )
        ->add_fields( array(
            Field::make( 'text', 'wpseed_site_tagline', 'Site Tagline' ),
            Field::make( 'textarea', 'wpseed_footer_text', 'Footer Text' ),
            Field::make( 'image', 'wpseed_logo', 'Logo' ),
        ) );
    
    // Post Meta Box
    Container::make( 'post_meta', __( 'Custom Fields' ) )
        ->where( 'post_type', '=', 'post' )
        ->add_fields( array(
            Field::make( 'text', 'wpseed_subtitle', 'Subtitle' ),
            Field::make( 'checkbox', 'wpseed_featured', 'Featured Post' ),
            Field::make( 'complex', 'wpseed_gallery', 'Gallery' )
                ->add_fields( array(
                    Field::make( 'image', 'image', 'Image' ),
                    Field::make( 'text', 'caption', 'Caption' ),
                ) ),
        ) );
    
    // User Meta
    Container::make( 'user_meta', __( 'Social Profiles' ) )
        ->add_fields( array(
            Field::make( 'text', 'wpseed_twitter', 'Twitter URL' ),
            Field::make( 'text', 'wpseed_linkedin', 'LinkedIn URL' ),
        ) );
}

// Retrieve values
$tagline = carbon_get_theme_option( 'wpseed_site_tagline' );
$subtitle = carbon_get_post_meta( get_the_ID(), 'wpseed_subtitle' );
$twitter = carbon_get_user_meta( $user_id, 'wpseed_twitter' );
```

#### When to Use
- Creating settings pages
- Adding custom fields to posts/pages
- Building theme options
- User profile extensions
- Custom widget fields

---

### Parsedown (Latest)
**Purpose**: Markdown parser  
**License**: MIT  
**Author**: Emanuil Rusev  
**Location**: `/includes/libraries/parsedown/` (if bundled)  
**GitHub**: https://github.com/erusev/parsedown

#### What It Does
Fast and extensible Markdown parser for PHP. Used in WPSeed for rendering documentation.

#### Usage Example

```php
require_once WPSEED_PLUGIN_DIR_PATH . 'includes/libraries/parsedown/Parsedown.php';

$parsedown = new Parsedown();

// Parse Markdown to HTML
$markdown = '# Hello World

This is **bold** and this is *italic*.

- List item 1
- List item 2';

$html = $parsedown->text( $markdown );
echo $html;

// Parse inline Markdown
$inline = $parsedown->line( 'This is **bold** text' );
```

#### When to Use
- Rendering README files
- Documentation viewers
- User-generated content with Markdown support
- Admin help text

---

## 🔒 Licensing & Attribution

### GPL Compliance
WPSeed is GPL-3.0 licensed. All bundled libraries are GPL-compatible:
- **Action Scheduler**: GPL-3.0 (compatible)
- **Carbon Fields**: GPL-2.0 (compatible)
- **Parsedown**: MIT (compatible)

### Attribution Requirements
- Library credits displayed in **Development → Credits** tab
- Original licenses preserved in library directories
- GitHub links provided for all libraries
- Creator names and companies acknowledged

---

## ⚠️ Modification Policy

### DO NOT Edit Libraries Directly

**Never modify bundled library files directly.** Changes will be lost when libraries are updated.

### Instead, Use These Approaches:

#### 1. WordPress Hooks
```php
// Extend Action Scheduler with hooks
add_filter( 'action_scheduler_queue_runner_batch_size', function( $batch_size ) {
    return 50; // Increase batch size
} );
```

#### 2. Wrapper Classes
```php
// Create a wrapper for Carbon Fields
class WPSeed_Settings {
    public static function register_fields() {
        // Your custom logic
        \Carbon_Fields\Container::make( 'theme_options', 'Settings' )
            ->add_fields( self::get_fields() );
    }
    
    private static function get_fields() {
        // Centralized field definitions
        return array(
            \Carbon_Fields\Field::make( 'text', 'api_key', 'API Key' ),
        );
    }
}
```

#### 3. Extend Classes
```php
// Extend library classes (if supported)
class WPSeed_Custom_Field extends \Carbon_Fields\Field\Field {
    // Your custom field type
}
```

### Reporting Bugs
If you find a bug in a bundled library:
1. **DO NOT** fix it in the bundled code
2. Report it to the library's GitHub repository
3. Wait for official fix or apply temporary workaround in your code
4. Update library when fix is released

---

## 🔄 Updating Libraries

### Manual Update Process

1. **Backup Current Version**
   ```bash
   cp -r includes/libraries/action-scheduler includes/libraries/action-scheduler.backup
   ```

2. **Download Latest Release**
   - Visit library's GitHub releases page
   - Download latest stable version
   - Extract to temporary location

3. **Replace Files**
   ```bash
   rm -rf includes/libraries/action-scheduler
   cp -r /path/to/new/action-scheduler includes/libraries/
   ```

4. **Update Version in Monitor**
   Edit `includes/classes/library-update-monitor.php`:
   ```php
   'action-scheduler' => array(
       'version' => '3.9.0', // Update version
       'bundled_date' => '2026-03-15', // Update date
   ),
   ```

5. **Test Thoroughly**
   - Check for breaking changes in changelog
   - Test all features using the library
   - Check debug.log for errors

### Automated Update (Future)
WPSeed will support one-click library updates in future versions via the **Development → Libraries** tab.

---

## 📊 Library Comparison

| Feature | Action Scheduler | Carbon Fields | Parsedown |
|---------|-----------------|---------------|-----------|
| **Use Case** | Background tasks | Custom fields | Markdown parsing |
| **Complexity** | Medium | Medium | Low |
| **Performance** | High | Medium | High |
| **Learning Curve** | Moderate | Easy | Easy |
| **Documentation** | Excellent | Good | Good |
| **Active Development** | Yes | Yes | Yes |

---

## 🚀 Migration Guides

### From WPSeed_Background_Process to Action Scheduler

**Old Code:**
```php
class My_Background_Process extends WPSeed_Background_Process {
    protected $action = 'my_process';
    
    protected function task( $item ) {
        // Process item
        return false;
    }
}

$process = new My_Background_Process();
$process->push_to_queue( array( 'id' => 1 ) );
$process->save()->dispatch();
```

**New Code:**
```php
// Schedule action
as_enqueue_async_action( 'wpseed_my_process', array( 'id' => 1 ) );

// Hook callback
add_action( 'wpseed_my_process', 'wpseed_process_item' );
function wpseed_process_item( $id ) {
    // Process item
}
```

### From Custom Settings to Carbon Fields

**Old Code:**
```php
add_settings_section( 'wpseed_section', 'Settings', null, 'wpseed' );
add_settings_field( 'wpseed_api_key', 'API Key', 'wpseed_api_key_callback', 'wpseed', 'wpseed_section' );
register_setting( 'wpseed', 'wpseed_api_key' );
```

**New Code:**
```php
use Carbon_Fields\Container;
use Carbon_Fields\Field;

Container::make( 'theme_options', 'Settings' )
    ->add_fields( array(
        Field::make( 'text', 'wpseed_api_key', 'API Key' ),
    ) );
```

---

## 🔗 Additional Resources

### Action Scheduler
- [Official Documentation](https://actionscheduler.org/usage/)
- [WooCommerce Implementation](https://github.com/woocommerce/woocommerce/wiki/Action-Scheduler)
- [Performance Best Practices](https://actionscheduler.org/perf/)

### Carbon Fields
- [Official Documentation](https://carbonfields.net/docs/)
- [Field Types Reference](https://carbonfields.net/docs/fields-usage/)
- [Container Types](https://carbonfields.net/docs/containers-usage/)

### Parsedown
- [Official Documentation](https://parsedown.org/)
- [GitHub Repository](https://github.com/erusev/parsedown)
- [Markdown Syntax](https://www.markdownguide.org/basic-syntax/)

---

## 💡 Best Practices

1. **Always use library APIs** - Don't bypass or hack around library functionality
2. **Check version compatibility** - Test after updating libraries
3. **Monitor performance** - Use WPSeed's performance monitoring for library impact
4. **Keep libraries updated** - Check **Development → Libraries** tab regularly
5. **Read changelogs** - Review breaking changes before updating
6. **Use wrappers** - Create abstraction layers for easier library replacement
7. **Document usage** - Comment why you're using specific library features

---

## ❓ FAQ

**Q: Can I remove a bundled library?**  
A: Yes, but ensure no WPSeed features depend on it. Check for usage before removing.

**Q: Can I use a different version?**  
A: Not recommended. WPSeed is tested with specific versions. Use at your own risk.

**Q: How do I add a new library?**  
A: Bundle in `/includes/libraries/`, register in `library-update-monitor.php`, add to Credits tab.

**Q: Are libraries loaded on every page?**  
A: No. Libraries are loaded only when needed to minimize performance impact.

**Q: Can I use Composer instead?**  
A: WPSeed bundles libraries for simplicity. Composer support is optional for advanced users.

---

**Last Updated**: February 2026  
**WPSeed Version**: 1.0.0
