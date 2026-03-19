# WPSeed - The AI-Powered WordPress Plugin Boilerplate

[![License: GPL v3](https://img.shields.io/badge/License-GPLv3-blue.svg)](https://www.gnu.org/licenses/gpl-3.0)
[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-blue.svg)](https://wordpress.org/)
[![PHP Version](https://img.shields.io/badge/PHP-7.4%2B-purple.svg)](https://php.net)
[![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg)](https://github.com/ryanbayne/wpseed/pulls)

> **The most advanced WordPress plugin boilerplate with built-in AI assistance, professional developer tools, and modern architecture.**

Transform your plugin development workflow with WPSeed - a production-ready boilerplate that includes everything you need to build professional WordPress plugins faster.

---

## 🚀 Features at a Glance

### 🤖 AI-Powered Development
- **Built-in AI Assistant** - Amazon Q and Gemini integration for code generation and debugging
- **Intelligent Context Awareness** - AI understands your plugin structure and suggests improvements
- **Usage Tracking & Rate Limiting** - Monitor AI usage with built-in analytics

### 🛠️ Professional Developer Tools
- **10-Tab Development Dashboard** - Assets, Theme Info, Debug Log, Database, PHP Info, Documentation, Dev Checklist, Tasks, Layouts, Diagrams, Architecture
- **Advanced Logging System** - File-based and database-driven logging with filtering
- **Asset Management** - Automatic asset tracking with missing file detection
- **GitHub Integration** - Sync documentation directly to your repository
- **Task Management** - View and manage GitHub issues from WordPress admin
- **Built-in Documentation Viewer** - Access all docs from within WordPress admin

### 🔌 Modern Architecture
- **REST API Framework** - Base controller with secure authentication
- **WP-CLI Commands** - Built-in commands for plugin management
- **Background Processing** - Queue system for long-running tasks
- **Object Registry** - Global object access without globals
- **Data Freshness Manager** - Cache validation and auto-refresh
- **Developer Flow Logger** - Detailed decision tracking for debugging
- **Dependency Injection Ready** - Clean, testable code structure
- **PSR-Compatible** - Modern PHP standards (optional)
- **Unit Testing** - PHPUnit configuration included
- **CI/CD Ready** - GitHub Actions workflow included

### 📦 Production-Ready Features
- **Custom Post Types & Taxonomies** - Example implementations included
- **Settings Framework** - Tabbed settings with multiple field types
- **Notification System** - Database-driven persistent notifications
- **Multisite Support** - Network activation detection and helpers
- **i18n Ready** - Translation-ready with automatic loading
- **Enhanced Uninstall** - Complete cleanup of options, transients, and user meta
- **Security First** - Nonce verification, capability checks, input sanitization

### 🎨 UI/UX Components
- **Tooltip System** - Contextual help throughout admin interface
- **Admin Notices** - Progress boxes, intro boxes, and dismissible notices
- **List Tables** - Basic and advanced WP_List_Table examples
- **Shortcode System** - Template-based shortcode architecture

---

## 📸 Screenshots

### Development Dashboard
![Development Dashboard](assets/screenshots/development-dashboard.png)
*10-tab developer interface with comprehensive tools and built-in documentation*

### AI Assistant
![AI Assistant](assets/screenshots/ai-assistant.png)
*Built-in AI chat for code generation and debugging*

### Settings Interface
![Settings Page](assets/screenshots/settings-page.png)
*Professional tabbed settings with multiple field types*

### Asset Tracker
![Assets Tracker](assets/screenshots/assets-tracker.png)
*Automatic asset management and missing file detection*

---

## ⚡ Quick Start (5 Minutes)

### 1. Download & Install
```bash
# Clone the repository
git clone https://github.com/ryanbayne/wpseed.git

# Or download ZIP and extract to wp-content/plugins/
```

### 2. Rename Your Plugin
```bash
# Rename the folder
mv wpseed my-awesome-plugin

# Find & Replace in all files:
# "wpseed" → "myawesomeplugin"
# "WPSeed" → "MyAwesomePlugin"
# "WPSEED" → "MYAWESOMEPLUGIN"
# "Plugin Seed" → "My Awesome Plugin"
```

### 3. Activate & Configure
1. Go to **WordPress Admin → Plugins**
2. Find "My Awesome Plugin" and click **Activate**
3. Visit **Settings → My Awesome Plugin Settings**
4. Configure your plugin settings

### 4. Start Building
```php
// Add your custom post type
// See: includes/classes/install.php

// Create REST endpoints
// See: includes/classes/rest-example.php

// Add settings pages
// See: includes/admin/settings/settings-example.php
```

**That's it!** You now have a professional plugin foundation ready for development.

---

## 📚 Documentation

- **[Getting Started Guide](docs/GETTING-STARTED.md)** - Detailed setup and customization
- **[Architecture Overview](docs/ARCHITECTURE.md)** - File structure and design patterns
- **[API Reference](docs/API-REFERENCE.md)** - Functions, classes, and hooks
- **[Repeater Fields Guide](docs/REPEATER-FIELDS.md)** - Dynamic repeatable field groups
- **[Integration Examples](docs/INTEGRATIONS.md)** - WooCommerce, ACF, Elementor, and more
- **[Video Walkthrough](https://youtube.com/watch?v=...)** - 15-minute video tutorial

---

## 🆚 Why Choose WPSeed?

| Feature | WPSeed | WP Plugin Boilerplate | WP Plugin Skeleton | Others |
|---------|--------|----------------------|-------------------|--------|
| **AI Assistant** | ✅ Built-in | ❌ | ❌ | ❌ |
| **Developer Dashboard** | ✅ 10 Tabs | ❌ | ❌ | ❌ |
| **REST API Framework** | ✅ | ❌ | ✅ | ⚠️ |
| **WP-CLI Commands** | ✅ | ❌ | ✅ | ⚠️ |
| **GitHub Integration** | ✅ | ❌ | ❌ | ❌ |
| **Asset Management** | ✅ Advanced | ⚠️ Basic | ⚠️ Basic | ⚠️ |
| **Logging System** | ✅ Dual (File + DB) | ❌ | ❌ | ⚠️ |
| **Unit Testing** | ✅ | ✅ | ✅ | ⚠️ |
| **CI/CD Ready** | ✅ GitHub Actions | ❌ | ⚠️ | ⚠️ |
| **Integration Examples** | ✅ 12+ | ❌ | ❌ | ❌ |
| **Active Development** | ✅ 2024 | ⚠️ 2020 | ⚠️ 2019 | ⚠️ |

**Legend**: ✅ Full Support | ⚠️ Partial/Basic | ❌ Not Available

---

## 🎯 Perfect For

### SaaS Plugins
Build subscription-based plugins with REST API integration, user management, and billing systems.

### E-commerce Extensions
Extend WooCommerce, Easy Digital Downloads, or other e-commerce platforms with custom functionality.

### API Integrations
Connect WordPress to external services with built-in API client architecture and logging.

### Admin Tools
Create powerful admin dashboards with the included UI components and developer tools.

### Custom Dashboards
Build client-facing dashboards with custom post types, taxonomies, and data visualization.

---

## 🔧 What's Included

### Core Systems
```
wpseed/
├── includes/
│   ├── classes/          # Core classes (Install, Logger, API, etc.)
│   ├── functions/        # Helper functions
│   ├── ai-system/        # AI integration (Amazon Q, Gemini)
│   └── admin/            # Admin interface components
├── admin/
│   ├── page/             # Admin pages (Development, Settings)
│   ├── notices/          # Notice system
│   └── notifications/    # Notification system
├── api/                  # API client architecture
├── assets/               # CSS, JS, images
├── templates/            # Template files
├── tests/                # PHPUnit tests
├── docs/                 # Documentation
└── examples/             # Integration examples
```

### Developer Tools
- **Assets Tab** - Track CSS/JS files, detect missing assets
- **Theme Tab** - View active theme info and template hierarchy
- **Debug Log Tab** - View and filter WordPress debug log
- **Database Tab** - Inspect tables, run queries, optimize database
- **PHP Info Tab** - Server configuration and PHP settings
- **Documentation Tab** - Browse all documentation from within WordPress
- **Dev Checklist Tab** - Pre-release checklist with industry tools
- **Tasks Tab** - GitHub issues integration
- **Layouts Tab** - Visual layout examples and CSS reference

---

## 🚀 Advanced Features

### Background Processing
```php
// Process large tasks in background
class My_Process extends WPSeed_Background_Process {
    protected $action = 'my_process';
    
    protected function task( $item ) {
        // Process item
        return false; // Remove from queue
    }
}

$process = new My_Process();
$process->push_to_queue( array( 'id' => 1 ) );
$process->save()->dispatch();
```

### Object Registry
```php
// Store and access objects globally
WPSeed_Object_Registry::add( 'my_object', $object );
$obj = WPSeed_Object_Registry::get( 'my_object' );
```

### Data Freshness Manager
```php
// Ensure cache freshness
$data = WPSeed_Data_Freshness_Manager::ensure_freshness(
    'cache_key',
    'hourly',
    function() {
        return fetch_fresh_data();
    }
);
```

### Developer Flow Logger
```php
// Track decision flows (developer mode only)
WPSeed_Developer_Flow_Logger::start_flow( 'data_processing' );
WPSeed_Developer_Flow_Logger::log_decision( 'Check cache', 'HIT' );
WPSeed_Developer_Flow_Logger::end_flow( 'Success' );
```

See [docs/ADVANCED-FEATURES.md](docs/ADVANCED-FEATURES.md) for complete guide.

### Code Generator (WP-CLI)
```bash
# Generate Custom Post Type
wp wpseed generate cpt "Book" --plural="Books" --icon="book"

# Generate Taxonomy
wp wpseed generate taxonomy "Genre" --post-type="book"

# Generate REST Endpoint
wp wpseed generate rest "books" --methods="GET,POST"

# Generate Settings Page
wp wpseed generate settings "API Settings"
```

### REST API Example
```php
// Create custom endpoint
class My_REST_Controller extends WPSeed_REST_Controller {
    protected $rest_base = 'myendpoint';
    
    public function register_routes() {
        register_rest_route($this->namespace, '/' . $this->rest_base, array(
            'methods'  => 'GET',
            'callback' => array($this, 'get_items'),
            'permission_callback' => array($this, 'get_items_permissions_check'),
        ));
    }
    
    public function get_items($request) {
        return rest_ensure_response(array('data' => 'Hello World'));
    }
}
```

### AI Integration Example
```php
// Use AI assistant in your code
$ai = new WPSeed_AI_Assistant();
$response = $ai->process_request('Generate a custom post type for books');
echo $response['message'];
```

---

## 🔌 Integration Examples

WPSeed includes ready-to-use integration examples for popular plugins:

### Tier 1 (E-commerce & Page Builders)
- **WooCommerce** - Custom product fields, order management, admin columns
- **Easy Digital Downloads** - Download meta, purchase hooks, custom receipts
- **Advanced Custom Fields** - Field group registration, custom field types
- **Elementor** - Custom widgets, dynamic tags, theme builder integration

### Tier 2 (Forms & SEO)
- **Contact Form 7** - Form handlers, database logging, custom validation
- **Gravity Forms** - Custom fields, form handlers, conditional logic
- **WPForms** - Submission processing, validation, confirmation messages
- **Yoast SEO** - Meta box integration, schema markup, sitemap customization

### Tier 3 (Community & Learning)
- **BuddyPress** - Profile tabs, custom fields, activity streams
- **bbPress** - Forum hooks, topic/reply management, custom fields
- **LearnDash** - Course completion, quiz tracking, points system
- **MemberPress** - Subscription management, transaction hooks, validation

### More Examples
See [docs/INTEGRATIONS.md](docs/INTEGRATIONS.md) for complete list and usage instructions.

---

## 🧪 Testing

### Run Unit Tests
```bash
# Install PHPUnit
composer install

# Run tests
phpunit

# Run specific test
phpunit tests/test-sample.php
```

### CI/CD
GitHub Actions workflow included - automatically runs tests on push/PR.

---

## 🌍 Internationalization

WPSeed is translation-ready:

```bash
# Generate POT file
wp i18n make-pot . languages/wpseed.pot

# Create translation
# Use Poedit or Loco Translate to create .po/.mo files
```

Place translation files in `/languages/` directory.

---

## 🤝 Contributing

We welcome contributions! Here's how:

1. **Fork the repository**
2. **Create a feature branch** (`git checkout -b feature/amazing-feature`)
3. **Commit your changes** (`git commit -m 'Add amazing feature'`)
4. **Push to the branch** (`git push origin feature/amazing-feature`)
5. **Open a Pull Request**

### Development Guidelines
- Follow [WordPress Coding Standards](https://developer.wordpress.org/coding-standards/)
- Write tests for new features
- Update documentation
- Keep commits atomic and descriptive

---

## 📋 Requirements

- **WordPress**: 5.0 or higher
- **PHP**: 7.4 or higher
- **MySQL**: 5.7+ or MariaDB 10.2+

### Recommended
- **PHP**: 8.0+
- **Composer**: For dependency management
- **WP-CLI**: For command-line tools
- **Node.js**: For asset compilation (optional)

---

## 📄 License

WPSeed is licensed under the [GNU General Public License v3.0](LICENSE).

You are free to:
- ✅ Use commercially
- ✅ Modify
- ✅ Distribute
- ✅ Use privately

---

## 🙏 Credits & Acknowledgments

### Built With Inspiration From
- **Automattic** - WooCommerce architecture patterns
- **wpseed** - Advanced developer tools and AI integration
- **WordPress Plugin Boilerplate** - Community standards
- **WP-CLI** - Command-line interface patterns

### Contributors
- **Ryan Bayne** - Original creator and maintainer
- **Community Contributors** - See [CONTRIBUTORS.md](CONTRIBUTORS.md)

### Special Thanks
- WordPress community for excellent documentation
- All plugin developers who share their knowledge
- Beta testers and early adopters

---

## About This Project

This project was created by **Fife CIC** (Community Interest Company) — a volunteer-driven organisation based in Fife, Scotland. All development work on this project has been carried out by unpaid volunteers. Fife CIC has received no external funding for this project.

Fife CIC's mission is to build technology and community services that create real opportunities for people — from accessible pathways into employment, to skills training, to supporting single parents and young people in their technology-related aspirations. Every line of code here contributes to that mission.

### 💛 Support This Project

If you find this project useful, please consider supporting us financially, it will encourage development. Your contributions go directly toward:

- **Rewarding our volunteers** through our Fair Incentive Policy (revenue-sharing credits, service discounts, and recognition)
- **Sustaining development** so we can continue maintaining and improving this project
- **Genuine Need** — profits from our work fund our personal workstations, software and services used to achieve our high standards

**Every contribution, no matter how small, makes a difference.**

[💖 Sponsor us on GitHub](https://github.com/sponsors/FifeCIC) · [🌐 Learn more about Fife CIC](https://fifecic.co.uk)

---

## 📞 Support & Community

### Get Help
- **Documentation**: [docs/](docs/)
- **GitHub Issues**: [Report bugs or request features](https://github.com/ryanbayne/wpseed/issues)
- **GitHub Discussions**: [Ask questions and share ideas](https://github.com/ryanbayne/wpseed/discussions)

### Stay Connected
- **Blog**: [ryanbayne.wordpress.com](https://ryanbayne.wordpress.com)
- **Twitter**: [@ryanrbayne](https://twitter.com/ryanrbayne)
- **LinkedIn**: [Ryan Bayne](https://www.linkedin.com/in/ryanrbayne/)

### Support Development
If you find WPSeed valuable, consider supporting its development:

- ⭐ **Star this repository** on GitHub
- 💰 **[Sponsor on Patreon](https://www.patreon.com/ryanbayne)**
- 🐛 **Report bugs** and suggest features
- 📝 **Contribute code** or documentation
- 💬 **Share your experience** and help others

---

## 🗺️ Roadmap

### Future Enhancements
- [ ] Plugin update server integration
- [ ] Video tutorial series

---

## 📊 Stats

- **Lines of Code**: 15,000+
- **Classes**: 50+
- **Functions**: 200+
- **Admin Pages**: 10+
- **Integration Examples**: 12
- **Documentation Pages**: 20+

---

## ⚡ Quick Links

- [Download Latest Release](https://github.com/ryanbayne/wpseed/releases/latest)
- [View Changelog](CHANGELOG.md)
- [Read Documentation](docs/)
- [Watch Video Tutorial](https://youtube.com/watch?v=...)
- [Report an Issue](https://github.com/ryanbayne/wpseed/issues/new)
- [Request a Feature](https://github.com/ryanbayne/wpseed/issues/new?labels=enhancement)

---

## 💡 Pro Tips

### Speed Up Development
```bash
# Use WP-CLI for faster setup
wp plugin install wpseed --activate

# Generate boilerplate code
wp wpseed generate cpt "Product"

# Clear cache quickly
wp wpseed cache clear
```

### Best Practices
1. **Always use nonces** for form submissions
2. **Sanitize input**, escape output
3. **Use transients** for expensive operations
4. **Log errors** with the built-in logger
5. **Write tests** for critical functionality

### Common Customizations
- Change plugin name: Find & replace "wpseed" throughout
- Add custom post type: Edit `includes/classes/install.php`
- Create REST endpoint: Extend `WPSeed_REST_Controller`
- Add settings tab: Create file in `includes/admin/settings/`

---

<div align="center">

**Made with ❤️ by the WordPress Community**

[⭐ Star on GitHub](https://github.com/ryanbayne/wpseed) • [📖 Read Docs](docs/) • [🐛 Report Bug](https://github.com/ryanbayne/wpseed/issues) • [💡 Request Feature](https://github.com/ryanbayne/wpseed/issues)

</div>
