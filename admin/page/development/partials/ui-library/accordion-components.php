<?php
/**
 * UI Library Accordion Components Partial
 *
 * @package WPSeed/Admin/Views/Partials
 * @version 1.0.0
 */

defined('ABSPATH') || exit;
?>
<div class="wpseed-ui-section">
    <h3><?php esc_html_e('Accordion Components', 'wpseed'); ?></h3>
    <p><?php esc_html_e('Collapsible content panels, expandable sections, tree-view components, and FAQ-style accordions for organizing information.', 'wpseed'); ?></p>
    
    <div class="wpseed-component-group">
        <!-- Basic Accordion -->
        <div class="component-demo">
            <h4><?php esc_html_e('Basic Accordion', 'wpseed'); ?></h4>
            <div class="wpseed-accordion">
                <div class="wpseed-accordion-item">
                    <div class="wpseed-accordion-header">
                        <h4><?php esc_html_e('Feature Overview', 'wpseed'); ?></h4>
                        <span class="wpseed-accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="wpseed-accordion-content">
                        <p><?php esc_html_e('This section contains detailed information about plugin features, including configuration options, usage examples, and best practices.', 'wpseed'); ?></p>
                        <div class="wpseed-grid wpseed-grid-2">
                            <div class="wpseed-card">
                                <h5><?php esc_html_e('Core Features', 'wpseed'); ?></h5>
                                <ul>
                                    <li><?php esc_html_e('Custom post types', 'wpseed'); ?></li>
                                    <li><?php esc_html_e('REST API endpoints', 'wpseed'); ?></li>
                                    <li><?php esc_html_e('Settings framework', 'wpseed'); ?></li>
                                </ul>
                            </div>
                            <div class="wpseed-card">
                                <h5><?php esc_html_e('Advanced Features', 'wpseed'); ?></h5>
                                <ul>
                                    <li><?php esc_html_e('Background processing', 'wpseed'); ?></li>
                                    <li><?php esc_html_e('Logging system', 'wpseed'); ?></li>
                                    <li><?php esc_html_e('Asset management', 'wpseed'); ?></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="wpseed-accordion-item">
                    <div class="wpseed-accordion-header">
                        <h4><?php esc_html_e('Configuration', 'wpseed'); ?></h4>
                        <span class="wpseed-accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="wpseed-accordion-content">
                        <p><?php esc_html_e('Configuration options and settings for customizing plugin behavior.', 'wpseed'); ?></p>
                        <div class="wpseed-table-container">
                            <table class="wpseed-table">
                                <thead>
                                    <tr>
                                        <th><?php esc_html_e('Setting', 'wpseed'); ?></th>
                                        <th><?php esc_html_e('Value', 'wpseed'); ?></th>
                                        <th><?php esc_html_e('Status', 'wpseed'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?php esc_html_e('Debug Mode', 'wpseed'); ?></td>
                                        <td>Enabled</td>
                                        <td><span class="wpseed-badge wpseed-badge-success"><?php esc_html_e('Active', 'wpseed'); ?></span></td>
                                    </tr>
                                    <tr>
                                        <td><?php esc_html_e('Logging', 'wpseed'); ?></td>
                                        <td>File + Database</td>
                                        <td><span class="wpseed-badge wpseed-badge-success"><?php esc_html_e('Active', 'wpseed'); ?></span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div class="wpseed-accordion-item">
                    <div class="wpseed-accordion-header">
                        <h4><?php esc_html_e('Performance', 'wpseed'); ?></h4>
                        <span class="wpseed-accordion-icon dashicons dashicons-arrow-down-alt2"></span>
                    </div>
                    <div class="wpseed-accordion-content">
                        <p><?php esc_html_e('Performance metrics and optimization settings.', 'wpseed'); ?></p>
                        <div class="media-progress-bar">
                            <div style="width: 85%;"><?php esc_html_e('85% Optimized', 'wpseed'); ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php
    // Add interactive functionality
    $accordion_script = "
        jQuery(document).ready(function($) {
            $('.wpseed-accordion-header').on('click', function() {
                var \$item = $(this).closest('.wpseed-accordion-item');
                var \$content = \$item.find('.wpseed-accordion-content').first();
                var \$icon = $(this).find('.wpseed-accordion-icon');
                
                \$content.slideToggle(300);
                \$item.toggleClass('wpseed-accordion-expanded');
                \$icon.toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-up-alt2');
                
                \$item.siblings('.wpseed-accordion-item').each(function() {
                    var \$siblingContent = $(this).find('.wpseed-accordion-content').first();
                    var \$siblingIcon = $(this).find('.wpseed-accordion-icon').first();
                    
                    if (\$siblingContent.is(':visible')) {
                        \$siblingContent.slideUp(300);
                        $(this).removeClass('wpseed-accordion-expanded');
                        \$siblingIcon.removeClass('dashicons-arrow-up-alt2').addClass('dashicons-arrow-down-alt2');
                    }
                });
            });
        });
    ";
    
    wp_add_inline_script('jquery', $accordion_script);
    ?>
</div>
