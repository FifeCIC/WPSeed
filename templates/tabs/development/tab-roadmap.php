<?php
/**
 * Roadmap tab — development planning and task management.
 *
 * ROLE: template
 *
 * Displays the plugin's development roadmap with accordion phases, two-column
 * task/architecture layout, priority badges, and localStorage-persisted
 * checkboxes. Every plugin cloned from WPSeed gets this tab and populates
 * it with its own phases.
 *
 * DEPENDS ON:
 *   - assets/js/admin/roadmap.js (accordion + localStorage)
 *   - assets/css/components/roadmap.css (styling)
 *
 * @package  WPSeed
 * @since    3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wpseed-roadmap-intro">
	<p><?php esc_html_e( 'Development roadmap with task tracking. Checkbox states are saved in your browser.', 'wpseed' ); ?></p>
	<p><small><?php esc_html_e( 'Keyboard: Ctrl+Shift+E = expand all, Ctrl+Shift+C = collapse all, Ctrl+Shift+R = reset tasks.', 'wpseed' ); ?></small></p>
</div>

<!-- Phase Status Overview -->
<div class="wpseed-roadmap-status-grid">
	<div class="wpseed-roadmap-status-card wpseed-status-completed">
		<h3><?php esc_html_e( 'PHASE 0', 'wpseed' ); ?></h3>
		<div class="wpseed-status-title"><?php esc_html_e( 'Composer & Namespaces', 'wpseed' ); ?></div>
		<div class="wpseed-status-badge"><?php esc_html_e( '✅ COMPLETE', 'wpseed' ); ?></div>
	</div>
	<div class="wpseed-roadmap-status-card wpseed-status-completed">
		<h3><?php esc_html_e( 'PHASE 1', 'wpseed' ); ?></h3>
		<div class="wpseed-status-title"><?php esc_html_e( 'Structure — loader & includes', 'wpseed' ); ?></div>
		<div class="wpseed-status-badge"><?php esc_html_e( '✅ COMPLETE', 'wpseed' ); ?></div>
	</div>
	<div class="wpseed-roadmap-status-card wpseed-status-active">
		<h3><?php esc_html_e( 'PHASE 2', 'wpseed' ); ?></h3>
		<div class="wpseed-status-title"><?php esc_html_e( 'Structure — templates', 'wpseed' ); ?></div>
		<div class="wpseed-status-badge"><?php esc_html_e( '🔄 IN PROGRESS', 'wpseed' ); ?></div>
	</div>
	<div class="wpseed-roadmap-status-card wpseed-status-pending">
		<h3><?php esc_html_e( 'PHASE 3', 'wpseed' ); ?></h3>
		<div class="wpseed-status-title"><?php esc_html_e( 'Structure — assets', 'wpseed' ); ?></div>
		<div class="wpseed-status-badge"><?php esc_html_e( '📋 PLANNED', 'wpseed' ); ?></div>
	</div>
	<div class="wpseed-roadmap-status-card wpseed-status-pending">
		<h3><?php esc_html_e( 'PHASE 4', 'wpseed' ); ?></h3>
		<div class="wpseed-status-title"><?php esc_html_e( 'AI-Readable Standards', 'wpseed' ); ?></div>
		<div class="wpseed-status-badge"><?php esc_html_e( '📋 PLANNED', 'wpseed' ); ?></div>
	</div>
</div>

<!-- Main Roadmap Content -->
<div class="wpseed-roadmap-main">

	<!-- PHASE 0: Composer & Namespaces -->
	<div class="wpseed-roadmap-phase">
		<div class="wpseed-roadmap-phase-header" data-phase="phase0">
			<h2><?php esc_html_e( 'PHASE 0: Composer & Namespace Foundation ✅', 'wpseed' ); ?></h2>
			<div class="wpseed-roadmap-phase-toggle">▶</div>
		</div>
		<div class="wpseed-roadmap-phase-content" id="phase0-content" style="display:none;">
			<div class="wpseed-roadmap-objective">
				<strong><?php esc_html_e( 'Objective:', 'wpseed' ); ?></strong>
				<?php esc_html_e( 'Establish PSR-4 autoloading via Composer so every new file uses namespaces from the start.', 'wpseed' ); ?>
			</div>
			<div class="wpseed-roadmap-section">
				<div class="wpseed-roadmap-tasks-grid">
					<div class="wpseed-roadmap-tasks-column">
						<h4><?php esc_html_e( 'Tasks', 'wpseed' ); ?></h4>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t01" class="wpseed-task-checkbox" checked disabled>
							<label for="t01"><?php esc_html_e( 'Create composer.json with PSR-4 autoload map', 'wpseed' ); ?></label>
						</div>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t02" class="wpseed-task-checkbox" checked disabled>
							<label for="t02"><?php esc_html_e( 'Load Composer autoloader in wpseed.php', 'wpseed' ); ?></label>
						</div>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t03" class="wpseed-task-checkbox" checked disabled>
							<label for="t03"><?php esc_html_e( 'Create namespace directory structure', 'wpseed' ); ?></label>
						</div>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t04" class="wpseed-task-checkbox" checked disabled>
							<label for="t04"><?php esc_html_e( 'Migrate Registry as proof-of-concept', 'wpseed' ); ?></label>
						</div>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t05" class="wpseed-task-checkbox" checked disabled>
							<label for="t05"><?php esc_html_e( 'Delete legacy SPL autoloader', 'wpseed' ); ?></label>
						</div>
					</div>
					<div class="wpseed-roadmap-architecture-column">
						<h4><?php esc_html_e( 'Key Files', 'wpseed' ); ?></h4>
						<div class="wpseed-roadmap-arch-item">
							<code>composer.json</code><br>
							<?php esc_html_e( 'PSR-4 map: WPSeed\\ → includes/', 'wpseed' ); ?>
						</div>
						<div class="wpseed-roadmap-arch-item">
							<code>vendor/autoload.php</code><br>
							<?php esc_html_e( 'Composer autoloader entry point', 'wpseed' ); ?>
						</div>
						<div class="wpseed-roadmap-arch-item">
							<code>includes/Ecosystem/Registry.php</code><br>
							<?php esc_html_e( 'First namespaced class — proof of concept', 'wpseed' ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- PHASE 1: Structure -->
	<div class="wpseed-roadmap-phase">
		<div class="wpseed-roadmap-phase-header" data-phase="phase1">
			<h2><?php esc_html_e( 'PHASE 1: Structure — loader & includes ✅', 'wpseed' ); ?></h2>
			<div class="wpseed-roadmap-phase-toggle">▶</div>
		</div>
		<div class="wpseed-roadmap-phase-content" id="phase1-content" style="display:none;">
			<div class="wpseed-roadmap-objective">
				<strong><?php esc_html_e( 'Objective:', 'wpseed' ); ?></strong>
				<?php esc_html_e( 'Reorganise includes/ so the category of any file is obvious from its path. Namespace and migrate all Tier 1 classes.', 'wpseed' ); ?>
			</div>
			<div class="wpseed-roadmap-section">
				<div class="wpseed-roadmap-tasks-grid">
					<div class="wpseed-roadmap-tasks-column">
						<h4><?php esc_html_e( 'Migrated Classes (13 total)', 'wpseed' ); ?></h4>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t11" class="wpseed-task-checkbox" checked disabled>
							<label for="t11"><?php esc_html_e( 'Ecosystem: Registry, Menu_Manager, Installer', 'wpseed' ); ?></label>
						</div>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t12" class="wpseed-task-checkbox" checked disabled>
							<label for="t12"><?php esc_html_e( 'Core: Install, AJAX_Handler, Logger, Enhanced_Logger, Task_Scheduler', 'wpseed' ); ?></label>
						</div>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t13" class="wpseed-task-checkbox" checked disabled>
							<label for="t13"><?php esc_html_e( 'Admin: Dashboard_Widgets, Notification_Bell, Uninstall_Feedback', 'wpseed' ); ?></label>
						</div>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t14" class="wpseed-task-checkbox" checked disabled>
							<label for="t14"><?php esc_html_e( 'API: REST_Controller, Base_API', 'wpseed' ); ?></label>
						</div>
					</div>
					<div class="wpseed-roadmap-architecture-column">
						<h4><?php esc_html_e( 'Namespace Map', 'wpseed' ); ?></h4>
						<div class="wpseed-roadmap-arch-item">
							<code>WPSeed\Ecosystem\</code> → <code>includes/Ecosystem/</code>
						</div>
						<div class="wpseed-roadmap-arch-item">
							<code>WPSeed\Core\</code> → <code>includes/Core/</code>
						</div>
						<div class="wpseed-roadmap-arch-item">
							<code>WPSeed\Admin\</code> → <code>includes/Admin/</code>
						</div>
						<div class="wpseed-roadmap-arch-item">
							<code>WPSeed\API\</code> → <code>includes/API/</code>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- PHASE 2: Templates (active) -->
	<div class="wpseed-roadmap-phase wpseed-roadmap-phase-active">
		<div class="wpseed-roadmap-phase-header" data-phase="phase2">
			<h2><?php esc_html_e( 'PHASE 2: Structure — templates 🔄', 'wpseed' ); ?></h2>
			<div class="wpseed-roadmap-phase-toggle">▼</div>
		</div>
		<div class="wpseed-roadmap-phase-content" id="phase2-content">
			<div class="wpseed-roadmap-objective">
				<strong><?php esc_html_e( 'Objective:', 'wpseed' ); ?></strong>
				<?php esc_html_e( 'Consolidate all templates into templates/ with predictable naming: pages → tabs → partials.', 'wpseed' ); ?>
			</div>
			<div class="wpseed-roadmap-section">
				<div class="wpseed-roadmap-section-header">
					<h3><?php esc_html_e( 'Template Migration', 'wpseed' ); ?></h3>
					<span class="wpseed-priority-badge wpseed-priority-high"><?php esc_html_e( 'HIGH PRIORITY', 'wpseed' ); ?></span>
				</div>
				<div class="wpseed-roadmap-tasks-grid">
					<div class="wpseed-roadmap-tasks-column">
						<h4><?php esc_html_e( 'Tasks', 'wpseed' ); ?></h4>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t21" class="wpseed-task-checkbox" checked>
							<label for="t21"><?php esc_html_e( 'Inventory all template files in FILE-INVENTORY.md', 'wpseed' ); ?></label>
						</div>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t22" class="wpseed-task-checkbox" checked>
							<label for="t22"><?php esc_html_e( 'Move 15 development tab files to templates/tabs/development/', 'wpseed' ); ?></label>
						</div>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t23" class="wpseed-task-checkbox" checked>
							<label for="t23"><?php esc_html_e( 'Move 17 UI library partials to templates/partials/ui-library/', 'wpseed' ); ?></label>
						</div>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t24" class="wpseed-task-checkbox">
							<label for="t24"><?php esc_html_e( 'Add roadmap tab scaffold (this tab)', 'wpseed' ); ?></label>
						</div>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t25" class="wpseed-task-checkbox">
							<label for="t25"><?php esc_html_e( 'Add architecture tab scaffold', 'wpseed' ); ?></label>
						</div>
					</div>
					<div class="wpseed-roadmap-architecture-column">
						<h4><?php esc_html_e( 'Structure', 'wpseed' ); ?></h4>
						<div class="wpseed-roadmap-arch-item">
							<code>templates/pages/</code><br>
							<?php esc_html_e( 'Full admin pages (one per menu item)', 'wpseed' ); ?>
						</div>
						<div class="wpseed-roadmap-arch-item">
							<code>templates/tabs/{page}/</code><br>
							<?php esc_html_e( 'Tab content (one per tab)', 'wpseed' ); ?>
						</div>
						<div class="wpseed-roadmap-arch-item">
							<code>templates/partials/</code><br>
							<?php esc_html_e( 'Reusable HTML fragments', 'wpseed' ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- PHASE 3: Assets -->
	<div class="wpseed-roadmap-phase">
		<div class="wpseed-roadmap-phase-header" data-phase="phase3">
			<h2><?php esc_html_e( 'PHASE 3: Structure — assets 📋', 'wpseed' ); ?></h2>
			<div class="wpseed-roadmap-phase-toggle">▶</div>
		</div>
		<div class="wpseed-roadmap-phase-content" id="phase3-content" style="display:none;">
			<div class="wpseed-roadmap-objective">
				<strong><?php esc_html_e( 'Objective:', 'wpseed' ); ?></strong>
				<?php esc_html_e( 'Convert procedural asset files to Asset_Manager class. Add component CSS for roadmap and architecture tabs.', 'wpseed' ); ?>
			</div>
			<div class="wpseed-roadmap-section">
				<div class="wpseed-roadmap-tasks-grid">
					<div class="wpseed-roadmap-tasks-column">
						<h4><?php esc_html_e( 'Tasks', 'wpseed' ); ?></h4>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t31" class="wpseed-task-checkbox">
							<label for="t31"><?php esc_html_e( 'Create Asset_Manager class', 'wpseed' ); ?></label>
						</div>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t32" class="wpseed-task-checkbox">
							<label for="t32"><?php esc_html_e( 'Create assets/css/components/roadmap.css', 'wpseed' ); ?></label>
						</div>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t33" class="wpseed-task-checkbox">
							<label for="t33"><?php esc_html_e( 'Create assets/js/admin/roadmap.js', 'wpseed' ); ?></label>
						</div>
					</div>
					<div class="wpseed-roadmap-architecture-column">
						<h4><?php esc_html_e( 'Key Files', 'wpseed' ); ?></h4>
						<div class="wpseed-roadmap-arch-item">
							<code>assets/Asset_Manager.php</code><br>
							<?php esc_html_e( 'Replaces manage-assets.php + queue-assets.php', 'wpseed' ); ?>
						</div>
						<div class="wpseed-roadmap-arch-item">
							<code>assets/css/components/</code><br>
							<?php esc_html_e( 'roadmap.css, flow-diagram.css, action-docs.css', 'wpseed' ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- PHASE 4: AI Standards -->
	<div class="wpseed-roadmap-phase">
		<div class="wpseed-roadmap-phase-header" data-phase="phase4">
			<h2><?php esc_html_e( 'PHASE 4: AI-Readable Code Standards 📋', 'wpseed' ); ?></h2>
			<div class="wpseed-roadmap-phase-toggle">▶</div>
		</div>
		<div class="wpseed-roadmap-phase-content" id="phase4-content" style="display:none;">
			<div class="wpseed-roadmap-objective">
				<strong><?php esc_html_e( 'Objective:', 'wpseed' ); ?></strong>
				<?php esc_html_e( 'Every file has a standard header with ROLE, DEPENDS ON, CONSUMED BY, and DATA FLOW tags so AI can navigate the codebase without reading implementations.', 'wpseed' ); ?>
			</div>
			<div class="wpseed-roadmap-section">
				<div class="wpseed-roadmap-tasks-grid">
					<div class="wpseed-roadmap-tasks-column">
						<h4><?php esc_html_e( 'Tasks', 'wpseed' ); ?></h4>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t41" class="wpseed-task-checkbox" checked>
							<label for="t41"><?php esc_html_e( 'Create FILE-HEADER-TEMPLATE.md', 'wpseed' ); ?></label>
						</div>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t42" class="wpseed-task-checkbox">
							<label for="t42"><?php esc_html_e( 'Apply headers to all Core/ files', 'wpseed' ); ?></label>
						</div>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t43" class="wpseed-task-checkbox">
							<label for="t43"><?php esc_html_e( 'Apply headers to all Ecosystem/ files', 'wpseed' ); ?></label>
						</div>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t44" class="wpseed-task-checkbox">
							<label for="t44"><?php esc_html_e( 'Create Hook_Registry.php', 'wpseed' ); ?></label>
						</div>
						<div class="wpseed-roadmap-task">
							<input type="checkbox" id="t45" class="wpseed-task-checkbox" checked>
							<label for="t45"><?php esc_html_e( 'Create NAMING-CONVENTIONS.md', 'wpseed' ); ?></label>
						</div>
					</div>
					<div class="wpseed-roadmap-architecture-column">
						<h4><?php esc_html_e( 'Reference', 'wpseed' ); ?></h4>
						<div class="wpseed-roadmap-arch-item">
							<code>docs/FILE-HEADER-TEMPLATE.md</code><br>
							<?php esc_html_e( 'Copy-paste headers for all 10 role types', 'wpseed' ); ?>
						</div>
						<div class="wpseed-roadmap-arch-item">
							<code>docs/NAMING-CONVENTIONS.md</code><br>
							<?php esc_html_e( 'All naming patterns documented', 'wpseed' ); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>

<!-- Progress Summary -->
<div class="wpseed-roadmap-summary">
	<h3><?php esc_html_e( 'Development Progress', 'wpseed' ); ?></h3>
	<div class="wpseed-roadmap-progress-bars">
		<div class="wpseed-progress-item">
			<label><?php esc_html_e( 'Phase 0 — Composer & Namespaces', 'wpseed' ); ?></label>
			<div class="wpseed-progress-bar"><div class="wpseed-progress-fill" style="width:100%; background: linear-gradient(90deg, #46b450, #28a745);"></div></div>
			<span class="wpseed-progress-text"><?php esc_html_e( '5/5 tasks completed', 'wpseed' ); ?></span>
		</div>
		<div class="wpseed-progress-item">
			<label><?php esc_html_e( 'Phase 1 — Structure (loader & includes)', 'wpseed' ); ?></label>
			<div class="wpseed-progress-bar"><div class="wpseed-progress-fill" style="width:100%; background: linear-gradient(90deg, #46b450, #28a745);"></div></div>
			<span class="wpseed-progress-text"><?php esc_html_e( '13/13 classes migrated', 'wpseed' ); ?></span>
		</div>
		<div class="wpseed-progress-item">
			<label><?php esc_html_e( 'Phase 2 — Structure (templates)', 'wpseed' ); ?></label>
			<div class="wpseed-progress-bar"><div class="wpseed-progress-fill" style="width:60%;"></div></div>
			<span class="wpseed-progress-text"><?php esc_html_e( '3/5 tasks completed', 'wpseed' ); ?></span>
		</div>
	</div>
</div>
