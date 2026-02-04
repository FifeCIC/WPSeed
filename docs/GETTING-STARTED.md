# Getting Started with WPSeed

Welcome to WPSeed! This guide will help you create your first WordPress plugin in just 10 minutes.

---

## Table of Contents

1. [Installation](#installation)
2. [Rename Your Plugin](#rename-your-plugin)
3. [Activate & Configure](#activate--configure)
4. [Your First Feature](#your-first-feature)
5. [Common Tasks](#common-tasks)
6. [Troubleshooting](#troubleshooting)

---

## Installation

### Method 1: Clone from GitHub
```bash
cd wp-content/plugins/
git clone https://github.com/ryanbayne/wpseed.git
```

### Method 2: Download ZIP
1. Download the latest release from [GitHub](https://github.com/ryanbayne/wpseed/releases)
2. Extract to `wp-content/plugins/`
3. Rename folder from `wpseed-main` to `wpseed`

### Method 3: WP-CLI
```bash
wp plugin install https://github.com/ryanbayne/wpseed/archive/main.zip --activate
```

---

## Rename Your Plugin

### Step 1: Rename Folder
```bash
# From wp-content/plugins/
mv wpseed my-awesome-plugin
```

### Step 2: Find & Replace

**Search for** → **Replace with**:
- `wpseed` → `myawesomeplugin`
- `WPSeed` → `MyAwesomePlugin`
- `WPSEED` → `MYAWESOMEPLUGIN`
- `Plugin Seed` → `My Awesome Plugin`

**Files to update**:
- `my-awesome-plugin.php` (main plugin file)
- `loader.php`
- All files in `includes/`
- All files in `admin/`
- `readme.txt`

**Tools**:
- **VS Code**: Ctrl+Shift+H (Find & Replace in Files)
- **PHPStorm**: Ctrl+Shift+R
- **Command Line**: 
  ```bash
  # Linux/Mac
  find . -type f -name "*.php" -exec sed -i 's/wpseed/myawesomeplugin/g' {} +
  
  # Windows (PowerShell)
  Get-ChildItem -Recurse -Filter *.php | ForEach-Object { (Get-Content $_.FullName) -replace 'wpseed', 'myawesomeplugin' | Set-Content $_.FullName }
  ```

### Step 3: Update Plugin Header

Edit `my-awesome-plugin.php`:
```php
/**
 * Plugin Name: My Awesome Plugin
 * Plugin URI: https://yoursite.com/my-awesome-plugin
 * Description: Your plugin description here
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://yoursite.com
 * License: GPL3
 * Text Domain: myawesomeplugin
 */
```

---

## Activate & Configure

### 1. Activate Plugin
- Go to **WordPress Admin → Plugins**
- Find "My Awesome Plugin"
- Click **Activate**

### 2. Visit Settings
- Go to **Settings → My Awesome Plugin Settings**
- Configure basic settings
- Save changes

### 3. Explore Developer Tools
- Go to **Plugins → My Awesome Plugin**
- Explore the 9-tab Development Dashboard:
  - **Assets** - View CSS/JS files
  - **Theme** - Theme information
  - **Debug Log** - View WordPress debug log
  - **Database** - Database tools
  - **PHP Info** - Server information
  - **AI Assistant** - AI-powered help
  - **Dev Checklist** - Pre-release checklist
  - **Tasks** - GitHub issues
  - **Layouts** - Layout examples

---

## Your First Feature

Let's create a simple "Book" custom post type in 5 minutes.

### Step 1: Edit Install Class

Open `includes/classes/install.php` and find the `register_post_types()` method:

```php
public static function register_post_types() {
    // Book Custom Post Type
    register_post_type('book', array(
        'labels' => array(
            'name' => __('Books', 'myawesomeplugin'),
            'singular_name' => __('Book', 'myawesomeplugin'),
            'add_new' => __('Add New Book', 'myawesomeplugin'),
            'add_new_item' => __('Add New Book', 'myawesomeplugin'),
            'edit_item' => __('Edit Book', 'myawesomeplugin'),
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-book',
        'supports' => array('title', 'editor', 'thumbnail', 'excerpt'),
        'show_in_rest' => true, // Gutenberg support
    ));
}
```

### Step 2: Flush Rewrite Rules

Deactivate and reactivate your plugin, or visit **Settings → Permalinks** and click Save.

### Step 3: Test It

- Go to **WordPress Admin**
- You'll see "Books" in the sidebar
- Click **Add New Book**
- Create your first book!

**That's it!** You've created your first feature.

---

## Common Tasks

### Add a Custom Taxonomy

In `includes/classes/install.php`, add to `register_taxonomies()`:

```php
public static function register_taxonomies() {
    register_taxonomy('genre', 'book', array(
        'labels' => array(
            'name' => __('Genres', 'myawesomeplugin'),
            'singular_name' => __('Genre', 'myawesomeplugin'),
        ),
        'hierarchical' => true,
        'show_in_rest' => true,
    ));
}
```

### Create a REST API Endpoint

Create `includes/classes/rest-books.php`:

```php
<?php
class MyAwesomePlugin_REST_Books extends WPSeed_REST_Controller {
    protected $rest_base = 'books';
    
    public function register_routes() {
        register_rest_route($this->namespace, '/' . $this->rest_base, array(
            'methods' => 'GET',
            'callback' => array($this, 'get_items'),
            'permission_callback' => '__return_true', // Public access
        ));
    }
    
    public function get_items($request) {
        $books = get_posts(array('post_type' => 'book', 'posts_per_page' => 10));
        return rest_ensure_response($books);
    }
}
```

Register in `loader.php`:
```php
add_action('rest_api_init', function() {
    $controller = new MyAwesomePlugin_REST_Books();
    $controller->register_routes();
});
```

Test: Visit `/wp-json/myawesomeplugin/v1/books`

### Add a Settings Page

Create `includes/admin/settings/settings-books.php`:

```php
<?php
if (!defined('ABSPATH')) exit;

class MyAwesomePlugin_Settings_Books extends WPSeed_Settings_Page {
    
    public function __construct() {
        $this->id = 'books';
        $this->label = __('Books', 'myawesomeplugin');
        parent::__construct();
    }
    
    public function get_settings() {
        return array(
            array(
                'title' => __('Book Settings', 'myawesomeplugin'),
                'type' => 'title',
                'id' => 'book_settings'
            ),
            array(
                'title' => __('Books Per Page', 'myawesomeplugin'),
                'id' => 'myawesomeplugin_books_per_page',
                'type' => 'number',
                'default' => '10',
                'desc' => __('Number of books to display per page', 'myawesomeplugin'),
            ),
            array(
                'type' => 'sectionend',
                'id' => 'book_settings'
            ),
        );
    }
}

return new MyAwesomePlugin_Settings_Books();
```

Register in `includes/admin/admin-settings.php`:
```php
$settings[] = include('settings/settings-books.php');
```

### Add a Shortcode

In `shortcodes/shortcodes.php`:

```php
function myawesomeplugin_books_shortcode($atts) {
    $atts = shortcode_atts(array(
        'limit' => 5,
    ), $atts);
    
    $books = get_posts(array(
        'post_type' => 'book',
        'posts_per_page' => $atts['limit'],
    ));
    
    ob_start();
    include plugin_dir_path(__FILE__) . '../templates/books-list.php';
    return ob_get_clean();
}
add_shortcode('books', 'myawesomeplugin_books_shortcode');
```

Create `templates/books-list.php`:
```php
<div class="myawesomeplugin-books">
    <?php foreach ($books as $book): ?>
        <div class="book-item">
            <h3><?php echo esc_html($book->post_title); ?></h3>
            <div><?php echo wp_kses_post($book->post_excerpt); ?></div>
        </div>
    <?php endforeach; ?>
</div>
```

Use: `[books limit="10"]`

### Add WP-CLI Command

In `includes/classes/cli-commands.php`:

```php
WP_CLI::add_command('myawesomeplugin books', function($args, $assoc_args) {
    $books = get_posts(array('post_type' => 'book', 'posts_per_page' => -1));
    WP_CLI::success(sprintf('Found %d books', count($books)));
    
    foreach ($books as $book) {
        WP_CLI::line('- ' . $book->post_title);
    }
});
```

Use: `wp myawesomeplugin books`

---

## Troubleshooting

### Plugin Won't Activate

**Error**: "Plugin could not be activated because it triggered a fatal error"

**Solution**:
1. Check PHP version (requires 7.4+)
2. Enable debug mode in `wp-config.php`:
   ```php
   define('WP_DEBUG', true);
   define('WP_DEBUG_LOG', true);
   ```
3. Check `wp-content/debug.log` for errors

### Custom Post Type Not Showing

**Solution**:
1. Flush rewrite rules: Visit **Settings → Permalinks** and click Save
2. Or deactivate/reactivate plugin

### REST API Returns 404

**Solution**:
1. Check permalink structure (must not be "Plain")
2. Flush rewrite rules
3. Verify namespace in REST controller matches

### Assets Not Loading

**Solution**:
1. Check file paths in `assets/script-assets.php` and `assets/style-assets.php`
2. Clear browser cache
3. Check Development → Assets tab for missing files

### Database Tables Not Created

**Solution**:
1. Deactivate and reactivate plugin
2. Check `wp_options` for `wpseed_db_version`
3. Manually run: `WPSeed_Install::install()`

---

## Next Steps

### Learn More
- [Architecture Overview](ARCHITECTURE.md) - Understand the file structure
- [API Reference](API-REFERENCE.md) - Available functions and classes
- [Integration Examples](INTEGRATIONS.md) - WooCommerce, ACF, Elementor

### Build Something
- Create a SaaS plugin
- Extend WooCommerce
- Build an API integration
- Create admin tools

### Get Help
- [GitHub Issues](https://github.com/ryanbayne/wpseed/issues)
- [GitHub Discussions](https://github.com/ryanbayne/wpseed/discussions)
- [Documentation](https://github.com/ryanbayne/wpseed/wiki)

---

## Quick Reference

### File Structure
```
my-awesome-plugin/
├── includes/
│   ├── classes/          # Core classes
│   ├── functions/        # Helper functions
│   └── admin/            # Admin interface
├── admin/
│   ├── page/             # Admin pages
│   └── settings/         # Settings pages
├── assets/               # CSS, JS, images
├── templates/            # Template files
├── api/                  # API clients
└── tests/                # Unit tests
```

### Key Classes
- `WPSeed_Install` - Installation and setup
- `WPSeed_REST_Controller` - REST API base
- `WPSeed_Logger` - Logging system
- `WPSeed_AI_Assistant` - AI integration
- `WPSeed_Settings_Page` - Settings framework

### Key Functions
- `wpseed_log()` - Log messages
- `wpseed_get_option()` - Get plugin option
- `wpseed_help_tip()` - Add tooltip
- `wpseed_clean()` - Sanitize data

### Hooks
- `wpseed_loaded` - Plugin loaded
- `wpseed_init` - Plugin initialized
- `wpseed_settings_save_{tab}` - Settings saved
- `wpseed_admin_mainviews` - Add admin views

---

**Congratulations!** You're ready to build amazing WordPress plugins with WPSeed. 🚀
