# Changelog

All notable changes to WPSeed will be documented in this file.

## [Unreleased]

### Removed
- Premium Licensing System (moved to EvolveWP.Core)
- Skype, Slack, Trello placeholder links
- MailChimp newsletter form from help tabs
- phpcs inline suppression comments

### Fixed
- PHP parse errors in admin-help.php and admin-settings.php
- Footer debug class instantiated before WordPress user functions loaded
- Multiple WordPress coding standard violations (escaping, sanitization, deprecated functions)
- SQL injection vulnerabilities in database helper functions and notifications

### Changed
- Help & Support tab now uses GitHub Issues and Discussions only
- FAQ depersonalized for boilerplate reuse

## [1.2.0] - 2025

### Added
- Action Scheduler integration for reliable background processing
- AI Assistant tab with Gemini integration (50 free requests/day)
- Architecture mapper tab with interactive plugin structure visualization
- Object registry for global access without globals
- Data freshness manager for cache validation
- Developer flow logger for decision tracking
- Library Update Monitor foundation
- Credits & Contributors Gallery foundation

### Changed
- Development Dashboard now 11 tabs
- Enhanced logging with developer mode support
- Setup wizard with Features configuration step
- Asset management with centralized registry

### Fixed
- Missing database tables on activation
- Carbon Fields loading with Pimple dependencies

## [1.1.0] - 2025

### Added
- Repeater fields for settings framework
- Built-in documentation viewer in Development Dashboard
- 12 integration examples (WooCommerce, Elementor, Contact Form 7, Yoast SEO, Gravity Forms, BuddyPress, Easy Digital Downloads, bbPress, LearnDash, MemberPress, WPForms, ACF)

### Changed
- Documentation tab in Development Dashboard
- Settings framework with repeater field support

## [1.0.0] - 2024

### Added
- Built-in AI Assistant with Amazon Q and Gemini integration
- 10-Tab Development Dashboard
- REST API framework with secure base controller
- WP-CLI commands for plugin management
- Advanced logging system (file-based and database-driven)
- Asset management with automatic tracking
- GitHub integration for documentation sync
- Task management with GitHub issues
- Interactive system diagrams with Mermaid.js
- Uninstall feedback system
- Multisite support
- i18n framework with automatic loading (31 languages)
- Enhanced uninstall with complete cleanup
- PHPUnit testing framework
- GitHub Actions CI/CD workflow
- Tooltip system for contextual help
- Database-driven notification system
- Custom post types and taxonomies examples
- Tabbed settings framework
- Template-based shortcode architecture
