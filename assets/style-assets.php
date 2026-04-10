<?php
/**
 * WPSeed Style Assets Registry
 *
 * Central catalogue of all CSS files with page-based loading rules.
 * The Asset Queue reads this to enqueue only what's needed per page.
 *
 * Classifications:
 *   - 'all'                → loads on every admin page
 *   - 'wpseed_development' → loads only on the Development page
 *   - specific page slug   → loads only on that page
 *
 * @package WPSeed/Assets
 * @version 3.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

return array(

	/*
	 * ─────────────────────────────────────────────────────────────────────
	 * Base — design tokens, reset, typography. Loaded on all admin pages.
	 * ─────────────────────────────────────────────────────────────────────
	 */
	'base' => array(
		'variables' => array(
			'path'         => 'css/base/variables.css',
			'purpose'      => 'CSS custom properties (design tokens)',
			'pages'        => array( 'all' ),
			'dependencies' => array(),
		),
		'reset' => array(
			'path'         => 'css/base/reset.css',
			'purpose'      => 'Browser normalisation for admin pages',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'typography' => array(
			'path'         => 'css/base/typography.css',
			'purpose'      => 'Headings, text, links, code blocks',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'main' => array(
			'path'         => 'css/admin.css',
			'purpose'      => 'Main admin stylesheet',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
	),

	/*
	 * ─────────────────────────────────────────────────────────────────────
	 * Layouts — page structure, grids, tabs, responsive.
	 * ─────────────────────────────────────────────────────────────────────
	 */
	'layouts' => array(
		'admin-layout' => array(
			'path'         => 'css/layouts/admin.css',
			'purpose'      => 'Admin page layout structure',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'tabs' => array(
			'path'         => 'css/layouts/tabs.css',
			'purpose'      => 'Tab navigation',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'grids' => array(
			'path'         => 'css/layouts/grids.css',
			'purpose'      => 'CSS grid utilities',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'layouts' => array(
			'path'         => 'css/layouts/layouts.css',
			'purpose'      => 'General layout utilities',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'responsive' => array(
			'path'         => 'css/layouts/responsive.css',
			'purpose'      => 'Responsive breakpoints',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'api-layout' => array(
			'path'         => 'css/layouts/api.css',
			'purpose'      => 'API management page layout',
			'pages'        => array( 'wpseed_api' ),
			'dependencies' => array( 'variables' ),
		),
		'database-layout' => array(
			'path'         => 'css/layouts/database.css',
			'purpose'      => 'Database admin page layout',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'features-layout' => array(
			'path'         => 'css/layouts/features.css',
			'purpose'      => 'Feature toggle layout',
			'pages'        => array( 'wpseed_settings' ),
			'dependencies' => array( 'variables' ),
		),
		'shortcodes-layout' => array(
			'path'         => 'css/layouts/shortcodes.css',
			'purpose'      => 'Shortcode output layout',
			'pages'        => array( 'wpseed_settings' ),
			'dependencies' => array( 'variables' ),
		),
	),

	/*
	 * ─────────────────────────────────────────────────────────────────────
	 * Core components — used across multiple admin pages.
	 * ─────────────────────────────────────────────────────────────────────
	 */
	'components' => array(
		'accordion' => array(
			'path'         => 'css/components/accordion.css',
			'purpose'      => 'Accordion expand/collapse',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'alerts' => array(
			'path'         => 'css/components/alerts.css',
			'purpose'      => 'Alert/notice boxes',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'badges' => array(
			'path'         => 'css/components/badges.css',
			'purpose'      => 'Status badges',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'buttons' => array(
			'path'         => 'css/components/buttons.css',
			'purpose'      => 'Button styles and variants',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'cards' => array(
			'path'         => 'css/components/cards.css',
			'purpose'      => 'Card UI elements',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'code-blocks' => array(
			'path'         => 'css/components/code-blocks.css',
			'purpose'      => 'Code display blocks',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'content-sections' => array(
			'path'         => 'css/components/content-sections.css',
			'purpose'      => 'Content section layouts',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'controls' => array(
			'path'         => 'css/components/controls.css',
			'purpose'      => 'Control components',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'filters' => array(
			'path'         => 'css/components/filters.css',
			'purpose'      => 'Filter components',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'form-controls' => array(
			'path'         => 'css/components/form-controls.css',
			'purpose'      => 'Form control elements',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'forms' => array(
			'path'         => 'css/components/forms.css',
			'purpose'      => 'Form elements and layouts',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'forms-wizard' => array(
			'path'         => 'css/components/forms-wizard.css',
			'purpose'      => 'Multi-step form wizard',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables', 'forms' ),
		),
		'lists' => array(
			'path'         => 'css/components/lists.css',
			'purpose'      => 'List components',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'meta-data' => array(
			'path'         => 'css/components/meta-data.css',
			'purpose'      => 'Metadata display',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'metrics' => array(
			'path'         => 'css/components/metrics.css',
			'purpose'      => 'Metric cards and displays',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'modals' => array(
			'path'         => 'css/components/modals.css',
			'purpose'      => 'Modal dialogs',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'notices' => array(
			'path'         => 'css/components/notices.css',
			'purpose'      => 'Admin notices',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'pagination' => array(
			'path'         => 'css/components/pagination.css',
			'purpose'      => 'Pagination controls',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'progress' => array(
			'path'         => 'css/components/progress.css',
			'purpose'      => 'Progress bars and indicators',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'status' => array(
			'path'         => 'css/components/status.css',
			'purpose'      => 'General status styles',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'status-indicators' => array(
			'path'         => 'css/components/status-indicators.css',
			'purpose'      => 'Status dot/badge indicators',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'status-messages' => array(
			'path'         => 'css/components/status-messages.css',
			'purpose'      => 'Status message boxes',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'steps' => array(
			'path'         => 'css/components/steps.css',
			'purpose'      => 'Step indicators',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'switches' => array(
			'path'         => 'css/components/switches.css',
			'purpose'      => 'Toggle switches',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'tables' => array(
			'path'         => 'css/components/tables.css',
			'purpose'      => 'Data tables',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'tooltips' => array(
			'path'         => 'css/components/tooltips.css',
			'purpose'      => 'Tooltip system',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
	),

	/*
	 * ─────────────────────────────────────────────────────────────────────
	 * Development page — only loaded on the Development admin page.
	 * ─────────────────────────────────────────────────────────────────────
	 */
	'development' => array(
		'architecture' => array(
			'path'         => 'css/components/architecture.css',
			'purpose'      => 'Architecture tab styles',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'roadmap' => array(
			'path'         => 'css/components/roadmap.css',
			'purpose'      => 'Roadmap tab styles',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'animations' => array(
			'path'         => 'css/components/animations.css',
			'purpose'      => 'CSS animation showcase',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'charts' => array(
			'path'         => 'css/components/charts.css',
			'purpose'      => 'Chart visualisation',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'data-analysis' => array(
			'path'         => 'css/components/data-analysis.css',
			'purpose'      => 'Data analysis components',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'data-explorer' => array(
			'path'         => 'css/components/data-explorer.css',
			'purpose'      => 'Data explorer UI',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'data-filters' => array(
			'path'         => 'css/components/data-filters.css',
			'purpose'      => 'Data filter components',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'diagnostics' => array(
			'path'         => 'css/components/diagnostics.css',
			'purpose'      => 'Diagnostic panels',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'log-viewer' => array(
			'path'         => 'css/components/log-viewer.css',
			'purpose'      => 'Log viewer UI',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'pointers' => array(
			'path'         => 'css/components/pointers.css',
			'purpose'      => 'WordPress pointer styles',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'help-tooltips' => array(
			'path'         => 'css/components/help-tooltips.css',
			'purpose'      => 'Contextual help tooltips for development pages',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'task-details' => array(
			'path'         => 'css/components/task-details.css',
			'purpose'      => 'Task detail panels',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'task-items' => array(
			'path'         => 'css/components/task-items.css',
			'purpose'      => 'Task list items',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'task-selection' => array(
			'path'         => 'css/components/task-selection.css',
			'purpose'      => 'Task selection UI',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'working-notes' => array(
			'path'         => 'css/components/working-notes.css',
			'purpose'      => 'Working notes display',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'development-page' => array(
			'path'         => 'css/pages/development.css',
			'purpose'      => 'Development page base styles',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'development-assets' => array(
			'path'         => 'css/pages/development-assets.css',
			'purpose'      => 'Development assets tab',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'development-tasks' => array(
			'path'         => 'css/pages/development-tasks.css',
			'purpose'      => 'Development tasks tab',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'development-current-task' => array(
			'path'         => 'css/pages/development-current-task.css',
			'purpose'      => 'Current task display',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'ui-library' => array(
			'path'         => 'css/pages/ui-library.css',
			'purpose'      => 'UI library showcase',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
	),

	/*
	 * ─────────────────────────────────────────────────────────────────────
	 * Page-specific — loaded only on their respective admin pages.
	 * ─────────────────────────────────────────────────────────────────────
	 */
	'pages' => array(
		'dashboard' => array(
			'path'         => 'css/pages/dashboard.css',
			'purpose'      => 'Main dashboard page',
			'pages'        => array( 'wpseed_dashboard', 'toplevel_page_wpseed' ),
			'dependencies' => array( 'variables' ),
		),
		'api-management' => array(
			'path'         => 'css/pages/api-management.css',
			'purpose'      => 'API management page',
			'pages'        => array( 'wpseed_api' ),
			'dependencies' => array( 'variables' ),
		),
		'settings-general' => array(
			'path'         => 'css/pages/settings-general.css',
			'purpose'      => 'General settings page',
			'pages'        => array( 'wpseed_settings', 'wpseed-settings' ),
			'dependencies' => array( 'variables' ),
		),
		'settings-database' => array(
			'path'         => 'css/pages/settings-database.css',
			'purpose'      => 'Database settings',
			'pages'        => array( 'wpseed_settings' ),
			'dependencies' => array( 'variables' ),
		),
		'settings-shortcodes' => array(
			'path'         => 'css/pages/settings-shortcodes.css',
			'purpose'      => 'Shortcode settings',
			'pages'        => array( 'wpseed_settings' ),
			'dependencies' => array( 'variables' ),
		),
		'settings-features' => array(
			'path'         => 'css/pages/settings-tab-features.css',
			'purpose'      => 'Feature tab settings',
			'pages'        => array( 'wpseed_settings' ),
			'dependencies' => array( 'variables' ),
		),
		'setup' => array(
			'path'         => 'css/pages/setup.css',
			'purpose'      => 'Setup wizard page',
			'pages'        => array( 'wpseed_setup', 'wpseed-setup' ),
			'dependencies' => array( 'variables' ),
		),
		'assets-page' => array(
			'path'         => 'css/pages/assets.css',
			'purpose'      => 'Assets tracker page',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'tasks-page' => array(
			'path'         => 'css/pages/tasks.css',
			'purpose'      => 'Tasks page',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'jquery-ui' => array(
			'path'         => 'external',
			'purpose'      => 'jQuery UI styles',
			'pages'        => array( 'wpseed_settings' ),
			'dependencies' => array(),
			'external'     => array( 'wp-jquery-ui-dialog' ),
		),
	),

	/*
	 * ─────────────────────────────────────────────────────────────────────
	 * Standalone — root-level CSS loaded on specific pages.
	 * ─────────────────────────────────────────────────────────────────────
	 */
	'standalone' => array(
		'accordion-table' => array(
			'path'         => 'css/accordion-table.css',
			'purpose'      => 'Accordion table hybrid',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'activation' => array(
			'path'         => 'css/activation.css',
			'purpose'      => 'Activation page styles',
			'pages'        => array( 'plugins' ),
			'dependencies' => array(),
		),
		'credits' => array(
			'path'         => 'css/credits.css',
			'purpose'      => 'Credits tab',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'developer-checklist' => array(
			'path'         => 'css/developer-checklist.css',
			'purpose'      => 'Developer checklist',
			'pages'        => array( 'wpseed_development' ),
			'dependencies' => array( 'variables' ),
		),
		'ecosystem-installer' => array(
			'path'         => 'css/ecosystem-installer.css',
			'purpose'      => 'Ecosystem installer page',
			'pages'        => array( 'wpseed_ecosystem_installer' ),
			'dependencies' => array( 'variables' ),
		),
		'license-manager' => array(
			'path'         => 'css/license-manager.css',
			'purpose'      => 'Licence management page',
			'pages'        => array( 'wpseed_license' ),
			'dependencies' => array( 'variables' ),
		),
		'notification-center' => array(
			'path'         => 'css/notification-center.css',
			'purpose'      => 'Notification centre',
			'pages'        => array( 'wpseed_notifications' ),
			'dependencies' => array( 'variables' ),
		),
		'settings-import-export' => array(
			'path'         => 'css/settings-import-export.css',
			'purpose'      => 'Settings import/export',
			'pages'        => array( 'wpseed_settings' ),
			'dependencies' => array( 'variables' ),
		),
		'settings-repeater' => array(
			'path'         => 'css/settings-repeater.css',
			'purpose'      => 'Repeater field styles',
			'pages'        => array( 'wpseed_settings' ),
			'dependencies' => array( 'variables' ),
		),
		'tooltips-root' => array(
			'path'         => 'css/tooltips.css',
			'purpose'      => 'Root-level tooltip styles',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'uninstall-feedback' => array(
			'path'         => 'css/uninstall-feedback.css',
			'purpose'      => 'Uninstall feedback modal',
			'pages'        => array( 'plugins' ),
			'dependencies' => array(),
		),
		'wpseed-setup' => array(
			'path'         => 'css/wpseed-setup.css',
			'purpose'      => 'Setup wizard',
			'pages'        => array( 'wpseed_setup', 'wpseed-setup' ),
			'dependencies' => array( 'variables' ),
		),
	),

	/*
	 * ─────────────────────────────────────────────────────────────────────
	 * Dark mode — loaded when dark mode is active.
	 * ─────────────────────────────────────────────────────────────────────
	 */
	'dark' => array(
		'dark-variables' => array(
			'path'         => 'css/dark/variables.css',
			'purpose'      => 'Dark mode variable overrides',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'variables' ),
		),
		'dark-admin' => array(
			'path'         => 'css/dark/admin.css',
			'purpose'      => 'Dark mode admin overrides',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'dark-variables' ),
		),
		'dark-tabs' => array(
			'path'         => 'css/dark/tabs.css',
			'purpose'      => 'Dark mode tab overrides',
			'pages'        => array( 'all' ),
			'dependencies' => array( 'dark-variables' ),
		),
	),
);
