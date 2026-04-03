<?php
/**
 * Architecture tab — plugin structure, data flow, and key functions reference.
 *
 * ROLE: template
 *
 * Displays the plugin's internal architecture using three reusable UI patterns:
 * 1. Data storage display (JSON/option structure with annotations)
 * 2. Button behaviour table (action → handler → data effect)
 * 3. Data flow diagram (numbered steps with split branches)
 *
 * Every plugin cloned from WPSeed gets this tab and replaces the content
 * with its own architecture.
 *
 * @package  WPSeed
 * @since    3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wpseed-arch-intro">
	<p><?php esc_html_e( 'This tab documents the internal architecture of the plugin — how data flows, where files live, and what each key class does. Useful for developers and AI assistants navigating the codebase.', 'wpseed' ); ?></p>
</div>

<!-- Two Column Layout -->
<div class="wpseed-arch-grid">

	<!-- Left: Namespace Map -->
	<div class="wpseed-arch-panel">
		<h3><?php esc_html_e( 'Namespace Map', 'wpseed' ); ?></h3>
		<div class="wpseed-arch-flow">
			<div class="wpseed-arch-step">
				<strong>WPSeed\Ecosystem\</strong><br>
				→ <code>includes/Ecosystem/</code><br>
				<?php esc_html_e( 'Registry, Menu_Manager, Installer', 'wpseed' ); ?>
			</div>
			<div class="wpseed-arch-step">
				<strong>WPSeed\Core\</strong><br>
				→ <code>includes/Core/</code><br>
				<?php esc_html_e( 'Install, AJAX_Handler, Logger, Enhanced_Logger, Task_Scheduler', 'wpseed' ); ?>
			</div>
			<div class="wpseed-arch-step">
				<strong>WPSeed\Admin\</strong><br>
				→ <code>includes/Admin/</code><br>
				<?php esc_html_e( 'Dashboard_Widgets, Notification_Bell, Uninstall_Feedback', 'wpseed' ); ?>
			</div>
			<div class="wpseed-arch-step">
				<strong>WPSeed\API\</strong><br>
				→ <code>includes/API/</code><br>
				<?php esc_html_e( 'REST_Controller, Base_API', 'wpseed' ); ?>
			</div>
		</div>
	</div>

	<!-- Right: Template Map -->
	<div class="wpseed-arch-panel">
		<h3><?php esc_html_e( 'Template Structure', 'wpseed' ); ?></h3>
		<div class="wpseed-arch-flow">
			<div class="wpseed-arch-step">
				<strong>templates/pages/</strong><br>
				<?php esc_html_e( 'Full admin pages — one file per menu item', 'wpseed' ); ?>
			</div>
			<div class="wpseed-arch-step">
				<strong>templates/tabs/{page}/</strong><br>
				<?php esc_html_e( 'Tab content — one file per tab within a page', 'wpseed' ); ?><br>
				<?php esc_html_e( 'Example: tabs/development/tab-roadmap.php', 'wpseed' ); ?>
			</div>
			<div class="wpseed-arch-step">
				<strong>templates/partials/</strong><br>
				<?php esc_html_e( 'Reusable HTML fragments and UI components', 'wpseed' ); ?>
			</div>
		</div>
	</div>
</div>

<!-- Pattern 1: Data Storage Display -->
<div class="wpseed-arch-json-panel">
	<h3><?php esc_html_e( 'Data Storage & Options', 'wpseed' ); ?></h3>
	<div class="wpseed-arch-json-files">

		<div class="wpseed-arch-json-file">
			<h5><code>wpseed_version</code> — <?php esc_html_e( 'Plugin Version', 'wpseed' ); ?></h5>
			<div class="wpseed-arch-json-content">
				<strong><?php esc_html_e( 'Type:', 'wpseed' ); ?></strong> <?php esc_html_e( 'WordPress option (string)', 'wpseed' ); ?><br>
				<strong><?php esc_html_e( 'Written by:', 'wpseed' ); ?></strong> <code>WPSeed\Core\Install::update_package_version()</code><br>
				<strong><?php esc_html_e( 'Read by:', 'wpseed' ); ?></strong> <code>WPSeed\Core\Install::check_version()</code><br>
				<strong><?php esc_html_e( 'Purpose:', 'wpseed' ); ?></strong> <?php esc_html_e( 'Triggers install routine when version changes.', 'wpseed' ); ?>
			</div>
		</div>

		<div class="wpseed-arch-json-file">
			<h5><code>wpseed_ecosystem_mode</code> — <?php esc_html_e( 'Ecosystem Status', 'wpseed' ); ?></h5>
			<div class="wpseed-arch-json-content">
				<strong><?php esc_html_e( 'Type:', 'wpseed' ); ?></strong> <?php esc_html_e( 'WordPress option (boolean)', 'wpseed' ); ?><br>
				<strong><?php esc_html_e( 'Written by:', 'wpseed' ); ?></strong> <code>WPSeed\Ecosystem\Registry::detect_ecosystem()</code><br>
				<strong><?php esc_html_e( 'Read by:', 'wpseed' ); ?></strong> <?php esc_html_e( 'Admin UI for conditional menu placement', 'wpseed' ); ?><br>
				<strong><?php esc_html_e( 'Purpose:', 'wpseed' ); ?></strong> <?php esc_html_e( 'True when 2+ EvolveWP plugins are active.', 'wpseed' ); ?>
			</div>
		</div>

		<div class="wpseed-arch-json-file">
			<h5><code>wpseed_ecosystem_plugins</code> — <?php esc_html_e( 'Registered Plugins', 'wpseed' ); ?></h5>
			<div class="wpseed-arch-json-content">
				<strong><?php esc_html_e( 'Type:', 'wpseed' ); ?></strong> <?php esc_html_e( 'WordPress option (serialized array)', 'wpseed' ); ?><br>
				<strong><?php esc_html_e( 'Written by:', 'wpseed' ); ?></strong> <code>WPSeed\Ecosystem\Registry::detect_ecosystem()</code><br>
				<strong><?php esc_html_e( 'Structure:', 'wpseed' ); ?></strong>
				<pre>{
  "wpseed": {
    "name": "WPSeed",
    "version": "3.0.0",
    "has_logging": true,
    "has_cron": true,
    "has_background_tasks": true
  }
}</pre>
			</div>
		</div>

	</div>
</div>

<!-- Pattern 2: Key Functions Table -->
<div class="wpseed-arch-json-panel">
	<h3><?php esc_html_e( 'Key Functions & Classes', 'wpseed' ); ?></h3>
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th style="width:30%;"><?php esc_html_e( 'Class / Function', 'wpseed' ); ?></th>
				<th style="width:25%;"><?php esc_html_e( 'File', 'wpseed' ); ?></th>
				<th style="width:45%;"><?php esc_html_e( 'Purpose', 'wpseed' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><code>WPSeed\Ecosystem\Registry</code></td>
				<td><code>includes/Ecosystem/Registry.php</code></td>
				<td><?php esc_html_e( 'Cross-plugin registration, feature detection, shared resource management.', 'wpseed' ); ?></td>
			</tr>
			<tr>
				<td><code>WPSeed\Core\Install</code></td>
				<td><code>includes/Core/Install.php</code></td>
				<td><?php esc_html_e( 'Activation, DB tables, roles, version checking, transient cleanup.', 'wpseed' ); ?></td>
			</tr>
			<tr>
				<td><code>WPSeed\Core\Logger</code></td>
				<td><code>includes/Core/Logger.php</code></td>
				<td><?php esc_html_e( 'Structured trace logging with loop detection and data-loss tracking.', 'wpseed' ); ?></td>
			</tr>
			<tr>
				<td><code>WPSeed\Core\Enhanced_Logger</code></td>
				<td><code>includes/Core/Enhanced_Logger.php</code></td>
				<td><?php esc_html_e( 'Query Monitor-style per-request logging — queries, hooks, HTTP, errors.', 'wpseed' ); ?></td>
			</tr>
			<tr>
				<td><code>WPSeed\Core\Task_Scheduler</code></td>
				<td><code>includes/Core/Task_Scheduler.php</code></td>
				<td><?php esc_html_e( 'Action Scheduler wrapper — schedule, cancel, query background jobs.', 'wpseed' ); ?></td>
			</tr>
			<tr>
				<td><code>WPSeed\API\REST_Controller</code></td>
				<td><code>includes/API/REST_Controller.php</code></td>
				<td><?php esc_html_e( 'Abstract base for REST endpoints with secure-by-default permissions.', 'wpseed' ); ?></td>
			</tr>
			<tr>
				<td><code>WPSeed\API\Base_API</code></td>
				<td><code>includes/API/Base_API.php</code></td>
				<td><?php esc_html_e( 'Abstract base for external API integrations with logging.', 'wpseed' ); ?></td>
			</tr>
			<tr>
				<td><code>wpseed_ecosystem()</code></td>
				<td><code>functions.php</code></td>
				<td><?php esc_html_e( 'Global accessor → Registry singleton.', 'wpseed' ); ?></td>
			</tr>
			<tr>
				<td><code>wpseed_log()</code></td>
				<td><code>functions.php</code></td>
				<td><?php esc_html_e( 'Global accessor → Logger singleton.', 'wpseed' ); ?></td>
			</tr>
		</tbody>
	</table>
</div>

<!-- Pattern 3: Data Flow Diagram -->
<div class="wpseed-arch-json-panel">
	<h3><?php esc_html_e( 'Plugin Boot Sequence', 'wpseed' ); ?></h3>

	<div class="wpseed-arch-data-flow">
		<div class="wpseed-arch-flow-step">
			<div class="wpseed-arch-flow-number">1</div>
			<div class="wpseed-arch-flow-content">
				<strong><?php esc_html_e( 'WordPress loads wpseed.php', 'wpseed' ); ?></strong><br>
				<?php esc_html_e( 'Constants defined → Composer autoloader loaded → functions.php loaded → loader.php loaded', 'wpseed' ); ?>
			</div>
		</div>

		<div class="wpseed-arch-flow-arrow">↓</div>

		<div class="wpseed-arch-flow-step">
			<div class="wpseed-arch-flow-number">2</div>
			<div class="wpseed-arch-flow-content">
				<strong><?php esc_html_e( 'WordPressPluginSeed::__construct()', 'wpseed' ); ?></strong><br>
				<?php esc_html_e( 'define_constants() → includes() → init_hooks() → fires wpseed_loaded action', 'wpseed' ); ?>
			</div>
		</div>

		<div class="wpseed-arch-flow-arrow">↓</div>

		<div class="wpseed-arch-flow-step">
			<div class="wpseed-arch-flow-number">3</div>
			<div class="wpseed-arch-flow-content">
				<strong><?php esc_html_e( 'includes() — grouped file loading', 'wpseed' ); ?></strong><br>
				<?php esc_html_e( 'Core functions → Core classes → Libraries → Ecosystem → Features → API', 'wpseed' ); ?>
			</div>
		</div>

		<div class="wpseed-arch-flow-arrow">↓</div>

		<div class="wpseed-arch-flow-step wpseed-arch-flow-decision">
			<div class="wpseed-arch-flow-number">4</div>
			<div class="wpseed-arch-flow-content">
				<strong><?php esc_html_e( 'Request type detection', 'wpseed' ); ?></strong><br>
				<?php esc_html_e( 'is_request("admin") → load admin files on init priority 1', 'wpseed' ); ?><br>
				<?php esc_html_e( 'is_request("frontend") → load frontend scripts', 'wpseed' ); ?>
			</div>
		</div>

		<div class="wpseed-arch-flow-arrow">↓</div>

		<div class="wpseed-arch-flow-step">
			<div class="wpseed-arch-flow-number">5</div>
			<div class="wpseed-arch-flow-content">
				<strong><?php esc_html_e( 'Admin files loaded (admin requests only)', 'wpseed' ); ?></strong><br>
				<?php esc_html_e( 'admin.php → admin-menus.php → notifications → toolbars', 'wpseed' ); ?><br>
				<?php esc_html_e( 'Menus registered on admin_menu hook → pages render via callbacks', 'wpseed' ); ?>
			</div>
		</div>
	</div>
</div>

<!-- Database Tables -->
<div class="wpseed-arch-json-panel">
	<h3><?php esc_html_e( 'Database Tables', 'wpseed' ); ?></h3>
	<table class="wp-list-table widefat fixed striped">
		<thead>
			<tr>
				<th style="width:30%;"><?php esc_html_e( 'Table', 'wpseed' ); ?></th>
				<th style="width:20%;"><?php esc_html_e( 'Created by', 'wpseed' ); ?></th>
				<th style="width:50%;"><?php esc_html_e( 'Purpose', 'wpseed' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td><code>{prefix}wpseed_api_calls</code></td>
				<td><code>Install::create_tables()</code></td>
				<td><?php esc_html_e( 'Logs every external API call with status and outcome.', 'wpseed' ); ?></td>
			</tr>
			<tr>
				<td><code>{prefix}wpseed_api_endpoints</code></td>
				<td><code>Install::create_tables()</code></td>
				<td><?php esc_html_e( 'Tracks unique API endpoints with usage counters.', 'wpseed' ); ?></td>
			</tr>
			<tr>
				<td><code>{prefix}wpseed_api_errors</code></td>
				<td><code>Install::create_tables()</code></td>
				<td><?php esc_html_e( 'Records API errors with code, message, and source location.', 'wpseed' ); ?></td>
			</tr>
			<tr>
				<td><code>{prefix}wpseed_debug_logs</code></td>
				<td><code>Enhanced_Logger::create_table()</code></td>
				<td><?php esc_html_e( 'Per-request performance data — queries, hooks, memory, errors.', 'wpseed' ); ?></td>
			</tr>
			<tr>
				<td><code>{prefix}wpseed_notifications</code></td>
				<td><code>Install::create_tables()</code></td>
				<td><?php esc_html_e( 'Admin notification queue with read/snooze/expiry tracking.', 'wpseed' ); ?></td>
			</tr>
			<tr>
				<td><code>{prefix}wpseed_ai_usage</code></td>
				<td><code>Install::create_tables()</code></td>
				<td><?php esc_html_e( 'AI provider usage tracking — tokens consumed per task type.', 'wpseed' ); ?></td>
			</tr>
		</tbody>
	</table>
</div>
