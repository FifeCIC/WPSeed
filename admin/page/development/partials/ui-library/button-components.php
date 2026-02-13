<?php
/**
 * UI Library Button Components Partial
 *
 * @package wpseed/Admin/Views/Partials
 * @version 1.0.7
 */

defined('ABSPATH') || exit;
?>
<div class="wpseed-ui-section">
    <h3><?php esc_html_e('Button Components', 'wpseed'); ?></h3>
    <p><?php esc_html_e('Standard button variations for consistent UI interactions.', 'wpseed'); ?></p>

    <!-- Primary Buttons -->
    <div class="wpseed-component-group">
        <h4><?php esc_html_e('Primary Buttons', 'wpseed'); ?></h4>
        <div class="wpseed-component-showcase">
            <button class="button button-primary"><?php esc_html_e('Primary Button', 'wpseed'); ?></button>
            <button class="button button-primary" disabled><?php esc_html_e('Disabled Primary', 'wpseed'); ?></button>
            <button class="button button-primary button-large"><?php esc_html_e('Large Primary', 'wpseed'); ?></button>
            <button class="button button-primary button-small"><?php esc_html_e('Small Primary', 'wpseed'); ?></button>
        </div>
    </div>

    <!-- Secondary Buttons -->
    <div class="wpseed-component-group">
        <h4><?php esc_html_e('Secondary Buttons', 'wpseed'); ?></h4>
        <div class="wpseed-component-showcase">
            <button class="button button-secondary"><?php esc_html_e('Secondary Button', 'wpseed'); ?></button>
            <button class="button button-secondary" disabled><?php esc_html_e('Disabled Secondary', 'wpseed'); ?></button>
            <button class="button button-secondary button-large"><?php esc_html_e('Large Secondary', 'wpseed'); ?></button>
            <button class="button button-secondary button-small"><?php esc_html_e('Small Secondary', 'wpseed'); ?></button>
        </div>
    </div>

    <!-- Icon Buttons -->
    <div class="wpseed-component-group">
        <h4><?php esc_html_e('Icon Buttons', 'wpseed'); ?></h4>
        <div class="wpseed-component-showcase">
            <button class="button button-primary">
                <span class="dashicons dashicons-plus-alt"></span>
                <?php esc_html_e('Add New', 'wpseed'); ?>
            </button>
            <button class="button button-secondary">
                <span class="dashicons dashicons-edit"></span>
                <?php esc_html_e('Edit', 'wpseed'); ?>
            </button>
            <button class="button button-secondary">
                <span class="dashicons dashicons-trash"></span>
                <?php esc_html_e('Delete', 'wpseed'); ?>
            </button>
            <button class="button button-secondary">
                <span class="dashicons dashicons-download"></span>
                <?php esc_html_e('Download', 'wpseed'); ?>
            </button>
        </div>
    </div>

    <!-- Link Buttons -->
    <div class="wpseed-component-group">
        <h4><?php esc_html_e('Link Buttons', 'wpseed'); ?></h4>
        <div class="wpseed-component-showcase">
            <button class="button-link"><?php esc_html_e('Link Button', 'wpseed'); ?></button>
            <button class="button-link-delete"><?php esc_html_e('Delete Link', 'wpseed'); ?></button>
            <button class="button-link" disabled><?php esc_html_e('Disabled Link', 'wpseed'); ?></button>
        </div>
    </div>

    <!-- Button Groups -->
    <div class="wpseed-component-group">
        <h4><?php esc_html_e('Button Groups', 'wpseed'); ?></h4>
        <div class="wpseed-component-showcase">
            <div class="button-group">
                <button class="button button-secondary"><?php esc_html_e('Left', 'wpseed'); ?></button>
                <button class="button button-secondary"><?php esc_html_e('Center', 'wpseed'); ?></button>
                <button class="button button-secondary"><?php esc_html_e('Right', 'wpseed'); ?></button>
            </div>
        </div>
    </div>

    <!-- API Status Buttons -->
    <div class="wpseed-component-group">
        <h4><?php esc_html_e('API Status Buttons', 'wpseed'); ?></h4>
        <div class="wpseed-component-showcase">
            <button class="button"><?php esc_html_e('Call Test', 'wpseed'); ?></button>
            <button class="button"><?php esc_html_e('Query Test', 'wpseed'); ?></button>
            <button class="button"><?php esc_html_e('Status Details', 'wpseed'); ?></button>
            <button class="button"><?php esc_html_e('Switch to Paper', 'wpseed'); ?></button>
            <button class="button"><?php esc_html_e('Switch to Live', 'wpseed'); ?></button>
            <button class="button"><?php esc_html_e('Enable', 'wpseed'); ?></button>
            <button class="button"><?php esc_html_e('Disable', 'wpseed'); ?></button>
        </div>
    </div>

    <!-- Status Badge Buttons -->
    <div class="wpseed-component-group">
        <h4><?php esc_html_e('Status Badge Buttons', 'wpseed'); ?></h4>
        <div class="wpseed-component-showcase">
            <span class="status-badge status-active"><?php esc_html_e('Operational', 'wpseed'); ?></span>
            <span class="status-badge status-inactive"><?php esc_html_e('Disabled', 'wpseed'); ?></span>
            <span class="type-badge type-data"><?php esc_html_e('Data Only', 'wpseed'); ?></span>
            <span class="type-badge type-trading"><?php esc_html_e('Trading', 'wpseed'); ?></span>
            <span class="mode-badge mode-live"><?php esc_html_e('Live', 'wpseed'); ?></span>
            <span class="mode-badge mode-paper"><?php esc_html_e('Paper', 'wpseed'); ?></span>
            <span class="rate-limit-badge rate-normal"><?php esc_html_e('Normal', 'wpseed'); ?></span>
        </div>
    </div>
</div>
