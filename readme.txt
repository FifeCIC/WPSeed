=== WPSeed - AI-Powered WordPress Plugin Boilerplate ===
Contributors: Ryan Bayne
Donate link: https://www.patreon.com/ryanbayne
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html
Tags: boilerplate, plugin starter, AI assistant, REST API, developer tools, WP-CLI, modern architecture
Requires at least: 5.0
Tested up to: 6.4
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv3

The most advanced WordPress plugin boilerplate with built-in AI assistance, professional developer tools, and modern architecture.
                       
== Description ==

WPSeed is a production-ready WordPress plugin boilerplate that includes everything you need to build professional plugins faster. Transform your plugin development workflow with built-in AI assistance, comprehensive developer tools, and modern architecture patterns.

= Professional Developer Tools =

* 10-Tab Development Dashboard (Assets, Theme, Debug Log, Database, PHP Info, AI Assistant, Dev Checklist, Tasks, Layouts, Diagrams)
* Advanced logging system (file-based and database-driven)
* Asset management with automatic tracking and missing file detection
* GitHub integration for documentation sync
* Task management with GitHub issues integration
* Interactive system diagrams with Mermaid.js

= Modern Architecture =

* REST API framework with secure base controller
* WP-CLI commands for plugin management
* Dependency injection ready structure
* PSR-compatible code organization
* PHPUnit testing framework included
* GitHub Actions CI/CD workflow

= Production-Ready Features =

* Custom post types and taxonomies with examples
* Tabbed settings framework with multiple field types
* Database-driven notification system
* Multisite support with network activation detection
* i18n ready with automatic translation loading
* Enhanced uninstall with complete cleanup
* Security-first approach (nonces, capability checks, sanitization)
* Uninstall feedback system for user insights

= UI/UX Components =

* Tooltip system for contextual help
* Admin notices (progress boxes, intro boxes, dismissible notices)
* WP_List_Table examples (basic and advanced)
* Template-based shortcode architecture
* Layout examples and CSS reference

= Project and Author Links =

This project belongs to the community and the WordPress community is there to help. 

- [Blog](http://ryanbayne.wordpress.com) 
- [Project GitHub](https://github.com/ryanbayne/wordpresspluginseed)
- [Project Slack](https://wpseed.slack.com/)
- [Report Issues](https://github.com/ryanbayne/wordpresspluginseed/issues)
- [Patreon Donations](https://www.patreon.com/ryanbayne)                                                                                                   
- [Ryan's LinkedIn](https://www.linkedin.com/in/ryanrbayne/)
== Installation ==

= Quick Start (5 Minutes) =

1. Download and extract to wp-content/plugins/
2. Rename folder from "wpseed" to "your-plugin-name"
3. Find & Replace: "wpseed" → "yourplugin", "WPSeed" → "YourPlugin", "WPSEED" → "YOURPLUGIN"
4. Activate plugin in WordPress admin
5. Visit Settings → WPSeed Settings to configure

= Detailed Instructions =

See the comprehensive Getting Started guide in docs/GETTING-STARTED.md for:
* Step-by-step setup
* Creating your first feature
* Common customization tasks
* Troubleshooting tips

== Frequently Asked Questions ==

= What makes WPSeed different from other boilerplates? =

WPSeed is the only WordPress plugin boilerplate with built-in AI assistance, a comprehensive 10-tab developer dashboard, and production-ready features like REST API framework, WP-CLI commands, and advanced logging.

= Do I need AI API keys to use WPSeed? =

No, AI features are optional. WPSeed works perfectly without AI integration. If you want to use the AI assistant, you'll need API keys for Amazon Q or Gemini.

= Is WPSeed suitable for beginners? =

Yes! WPSeed includes extensive documentation, code examples, and a Getting Started guide. The developer tools help you learn WordPress plugin development best practices.

= Can I use WPSeed for commercial projects? =

Absolutely! WPSeed is licensed under GPLv3, which allows commercial use, modification, and distribution.

= Does WPSeed work with multisite? =

Yes, WPSeed includes multisite support with network activation detection and site-specific helpers.

= How do I get support? =

Visit our GitHub repository for documentation, issues, and discussions. See the Support section for links. 

== Screenshots ==

1. Development Dashboard - 11-tab developer interface with comprehensive tools including Assets, Theme Info, Debug Log, Database, PHP Info, AI Assistant, Dev Checklist, Tasks, Layouts, Diagrams, and Architecture mapper
2. AI Assistant Tab - Built-in AI chat interface with Gemini integration for code generation and debugging. Free tier with 50 requests/day, no credit card required
3. Settings Interface - Professional tabbed settings page with multiple field types, validation, and organized sections for easy configuration
4. Asset Tracker - Comprehensive asset management showing all CSS/JS files with found/missing status, file paths, purposes, and page assignments
5. Architecture Mapper - Interactive visual plugin structure showing all systems, classes, files, and relationships. Perfect for developers and AI assistants to understand the codebase
6. GitHub Integration - Documentation sync system to push plugin docs directly to GitHub repository. Developer mode feature for maintaining documentation
7. Task Management - GitHub issues integration displaying tasks, bugs, and feature requests directly in WordPress admin with filtering and status tracking

== Languages ==

Translator needed to localize the plugin.

== Upgrade Notice ==

No special upgrade instructions this time.

== Changelog ==

= 1.0.0 - 2024 =

**Major Release - Complete Rewrite**

* NEW: Built-in AI Assistant with Amazon Q and Gemini integration
* NEW: 10-Tab Development Dashboard (Assets, Theme, Debug Log, Database, PHP Info, AI Assistant, Dev Checklist, Tasks, Layouts, Diagrams)
* NEW: REST API framework with secure base controller
* NEW: WP-CLI commands for plugin management
* NEW: Advanced logging system (file-based and database-driven)
* NEW: Asset management with automatic tracking
* NEW: GitHub integration for documentation sync
* NEW: Task management with GitHub issues
* NEW: Interactive system diagrams with Mermaid.js
* NEW: Uninstall feedback system
* NEW: Multisite support
* NEW: i18n framework with automatic loading
* NEW: Enhanced uninstall with complete cleanup
* NEW: PHPUnit testing framework
* NEW: GitHub Actions CI/CD workflow
* NEW: Tooltip system for contextual help
* NEW: Database-driven notification system
* NEW: Custom post types and taxonomies examples
* NEW: Tabbed settings framework
* NEW: Template-based shortcode architecture
* IMPROVED: Modern file structure (includes/classes/, includes/functions/)
* IMPROVED: Security-first approach throughout
* IMPROVED: Comprehensive documentation
* UPDATED: Minimum requirements - WordPress 5.0+, PHP 7.4+ 

== Contributors ==
Donators, GitHub contributors and developers who support me when working on WP Seed will be listed here. 

* Automattic - Half of the plugin uses their perfect approaches to WordPress plugin development. 
* Brian at WPMUDEV - After a long Skype, showed me the importance of using the WordPress core a lot more. 
* Ignacio Cruz at WPMUDEV
* Ashley Rich (A5shleyRich)
* Igor Vaynberg
* M. Alsup
* Amir-Hossein Sobhi

== Version Numbers Explained ==

Explanation of versioning used by myself Ryan Bayne. The versioning scheme I use is called "Semantic Versioning 2.0.0" and more
information about it can be found at http://semver.org/ 

These are the rules followed to increase the TwitchPress plugin version number. Given a version number MAJOR.MINOR.PATCH, increment the:

MAJOR version when you make incompatible API changes,
MINOR version when you add functionality in a backwards-compatible manner, and
PATCH version when you make backwards-compatible bug fixes.
Additional labels for pre-release and build metadata are available as extensions to the MAJOR.MINOR.PATCH format.
