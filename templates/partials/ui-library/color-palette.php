<?php
/**
 * UI Library Color Palette Partial
 *
 * @package wpseed/Admin/Views/Partials
 * @version 1.0.0
 */

defined('ABSPATH') || exit;
?>
<div class="wpseed-ui-section">
    <h3><?php esc_html_e('Color Palette', 'wpseed'); ?></h3>
    <p><?php esc_html_e('The wpseed color system uses CSS custom properties for consistent theming.', 'wpseed'); ?></p>

    <!-- Primary Colors -->
    <div class="wpseed-color-group">
        <h4><?php esc_html_e('Primary Colors', 'wpseed'); ?></h4>
        <div class="wpseed-color-grid">
            <div class="wpseed-color-item" onclick="wpseedUILibrary.showColorInfo(this, 'Primary', '#2271b1', '--wpseed-color-primary')">
                <div class="wpseed-color-swatch wpseed-color-primary"></div>
                <div class="wpseed-color-info">
                    <span class="wpseed-color-name">Primary</span>
                    <span class="wpseed-color-value">#2271b1</span>
                </div>
            </div>
            <div class="wpseed-color-item" onclick="wpseedUILibrary.showColorInfo(this, 'Primary Dark', '#135e96', '--wpseed-color-primary-dark')">
                <div class="wpseed-color-swatch wpseed-color-primary-dark"></div>
                <div class="wpseed-color-info">
                    <span class="wpseed-color-name">Primary Dark</span>
                    <span class="wpseed-color-value">#135e96</span>
                </div>
            </div>
            <div class="wpseed-color-item" onclick="wpseedUILibrary.showColorInfo(this, 'Primary Light', '#72aee6', '--wpseed-color-primary-light')">
                <div class="wpseed-color-swatch wpseed-color-primary-light"></div>
                <div class="wpseed-color-info">
                    <span class="wpseed-color-name">Primary Light</span>
                    <span class="wpseed-color-value">#72aee6</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Colors -->
    <div class="wpseed-color-group">
        <h4><?php esc_html_e('Status Colors', 'wpseed'); ?></h4>
        <div class="wpseed-color-grid">
            <div class="wpseed-color-item" onclick="wpseedUILibrary.showColorInfo(this, 'Success', '#00a32a', '--wpseed-color-success')">
                <div class="wpseed-color-swatch wpseed-color-success"></div>
                <div class="wpseed-color-info">
                    <span class="wpseed-color-name">Success</span>
                    <span class="wpseed-color-value">#00a32a</span>
                </div>
            </div>
            <div class="wpseed-color-item" onclick="wpseedUILibrary.showColorInfo(this, 'Warning', '#dba617', '--wpseed-color-warning')">
                <div class="wpseed-color-swatch wpseed-color-warning"></div>
                <div class="wpseed-color-info">
                    <span class="wpseed-color-name">Warning</span>
                    <span class="wpseed-color-value">#dba617</span>
                </div>
            </div>
            <div class="wpseed-color-item" onclick="wpseedUILibrary.showColorInfo(this, 'Error', '#d63638', '--wpseed-color-error')">
                <div class="wpseed-color-swatch wpseed-color-error"></div>
                <div class="wpseed-color-info">
                    <span class="wpseed-color-name">Error</span>
                    <span class="wpseed-color-value">#d63638</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Neutral Colors -->
    <div class="wpseed-color-group">
        <h4><?php esc_html_e('Neutral Colors', 'wpseed'); ?></h4>
        <div class="wpseed-color-grid">
            <div class="wpseed-color-item" onclick="wpseedUILibrary.showColorInfo(this, 'White', '#ffffff', '--wpseed-color-white')">
                <div class="wpseed-color-swatch wpseed-color-white"></div>
                <div class="wpseed-color-info">
                    <span class="wpseed-color-name">White</span>
                    <span class="wpseed-color-value">#ffffff</span>
                </div>
            </div>
            <div class="wpseed-color-item" onclick="wpseedUILibrary.showColorInfo(this, 'Gray 100', '#f0f0f1', '--wpseed-color-gray-100')">
                <div class="wpseed-color-swatch wpseed-color-gray-100"></div>
                <div class="wpseed-color-info">
                    <span class="wpseed-color-name">Gray 100</span>
                    <span class="wpseed-color-value">#f0f0f1</span>
                </div>
            </div>
            <div class="wpseed-color-item" onclick="wpseedUILibrary.showColorInfo(this, 'Gray 300', '#dcdcde', '--wpseed-color-gray-300')">
                <div class="wpseed-color-swatch wpseed-color-gray-300"></div>
                <div class="wpseed-color-info">
                    <span class="wpseed-color-name">Gray 300</span>
                    <span class="wpseed-color-value">#dcdcde</span>
                </div>
            </div>
            <div class="wpseed-color-item" onclick="wpseedUILibrary.showColorInfo(this, 'Gray 500', '#a7aaad', '--wpseed-color-gray-500')">
                <div class="wpseed-color-swatch wpseed-color-gray-500"></div>
                <div class="wpseed-color-info">
                    <span class="wpseed-color-name">Gray 500</span>
                    <span class="wpseed-color-value">#a7aaad</span>
                </div>
            </div>
            <div class="wpseed-color-item" onclick="wpseedUILibrary.showColorInfo(this, 'Gray 700', '#646970', '--wpseed-color-gray-700')">
                <div class="wpseed-color-swatch wpseed-color-gray-700"></div>
                <div class="wpseed-color-info">
                    <span class="wpseed-color-name">Gray 700</span>
                    <span class="wpseed-color-value">#646970</span>
                </div>
            </div>
            <div class="wpseed-color-item" onclick="wpseedUILibrary.showColorInfo(this, 'Gray 900', '#1d2327', '--wpseed-color-gray-900')">
                <div class="wpseed-color-swatch wpseed-color-gray-900"></div>
                <div class="wpseed-color-info">
                    <span class="wpseed-color-name">Gray 900</span>
                    <span class="wpseed-color-value">#1d2327</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Color Information Display -->
    <div id="wpseed-color-info-display" class="wpseed-color-info-panel" style="display: none;">
        <h4><?php esc_html_e('Color Information', 'wpseed'); ?></h4>
        <div id="wpseed-color-details"></div>
    </div>
</div>
