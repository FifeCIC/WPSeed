<?php
/**
 * WPSeed Development - Credits & Contributors
 *
 * @package WPSeed/Admin/Development
 * @version 1.2.0
 */

if (!defined('ABSPATH')) exit;

class WPSeed_Admin_Development_Credits {
    
    public static function output() {
        $contributors = self::get_contributors();
        $selected = isset($_GET['contributor']) ? sanitize_text_field($_GET['contributor']) : 'action_scheduler';
        
        wp_enqueue_style('wpseed-accordion-table');
        wp_enqueue_script('wpseed-accordion-table');
        ?>
        
        <div class="wpseed-credits-container">
            <div class="wpseed-layout">
                
                <!-- Left: Contributors Table -->
                <div class="wpseed-table-container">
                    
                    <div class="tablenav top">
                        <div class="alignleft actions">
                            <select id="category-filter">
                                <option value=""><?php _e('All Categories', 'wpseed'); ?></option>
                                <option value="library"><?php _e('Libraries', 'wpseed'); ?></option>
                                <option value="inspiration"><?php _e('Inspiration', 'wpseed'); ?></option>
                                <option value="service"><?php _e('Services', 'wpseed'); ?></option>
                            </select>
                            <input type="search" id="contributor-search" placeholder="<?php esc_attr_e('Search...', 'wpseed'); ?>">
                        </div>
                    </div>
                    
                    <div class="wp-list-table widefat fixed striped">
                        <div class="table-header" style="display: flex; background: #f1f1f1; padding: 12px 15px; font-weight: 600; border-bottom: 1px solid #c3c4c7;">
                            <div style="flex: 2;"><?php _e('Name', 'wpseed'); ?></div>
                            <div style="flex: 1;"><?php _e('Type', 'wpseed'); ?></div>
                            <div style="flex: 1;"><?php _e('License', 'wpseed'); ?></div>
                            <div style="flex: 1;"><?php _e('Status', 'wpseed'); ?></div>
                        </div>
                    </div>

                    <div class="wpseed-accordion-table">
                        <?php foreach ($contributors as $id => $contributor): ?>
                            <div class="accordion-row" data-category="<?php echo esc_attr($contributor['category']); ?>">
                                <div class="accordion-header">
                                    <div style="flex: 2;">
                                        <strong><?php echo esc_html($contributor['name']); ?></strong>
                                        <?php if ($contributor['featured']): ?>
                                            <span class="featured-badge">⭐ Featured</span>
                                        <?php endif; ?>
                                    </div>
                                    <div style="flex: 1;">
                                        <span class="type-badge type-<?php echo esc_attr($contributor['category']); ?>">
                                            <?php echo esc_html(ucfirst($contributor['category'])); ?>
                                        </span>
                                    </div>
                                    <div style="flex: 1;">
                                        <span class="license-badge"><?php echo esc_html($contributor['license']); ?></span>
                                    </div>
                                    <div style="flex: 1;">
                                        <span class="status-badge status-<?php echo esc_attr($contributor['status']); ?>">
                                            <?php echo esc_html(ucfirst($contributor['status'])); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div class="accordion-content">
                                    <div class="contributor-meta">
                                        <div>
                                            <strong><?php _e('Description:', 'wpseed'); ?></strong><br>
                                            <?php echo esc_html($contributor['description']); ?>
                                        </div>
                                        <div>
                                            <strong><?php _e('Used In:', 'wpseed'); ?></strong><br>
                                            <?php echo esc_html($contributor['used_in']); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="contributor-actions">
                                        <a href="<?php echo esc_url(add_query_arg('contributor', $id)); ?>" class="button button-primary">
                                            <?php _e('View Details', 'wpseed'); ?>
                                        </a>
                                        <?php if ($contributor['website']): ?>
                                            <a href="<?php echo esc_url($contributor['website']); ?>" class="button" target="_blank">
                                                <?php _e('Visit Website', 'wpseed'); ?>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($contributor['github']): ?>
                                            <a href="<?php echo esc_url($contributor['github']); ?>" class="button" target="_blank">
                                                <?php _e('GitHub', 'wpseed'); ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- Right: Details Sidebar -->
                <div class="wpseed-sidebar">
                    <div class="wpseed-details-container">
                        <?php if (isset($contributors[$selected])): 
                            $contributor = $contributors[$selected];
                        ?>
                            <div class="section-header">
                                <h3><?php echo esc_html($contributor['name']); ?></h3>
                            </div>
                            
                            <div class="section-content">
                                <?php if ($contributor['logo']): ?>
                                    <div class="contributor-logo">
                                        <img src="<?php echo esc_url($contributor['logo']); ?>" alt="<?php echo esc_attr($contributor['name']); ?>">
                                    </div>
                                <?php endif; ?>
                                
                                <div class="detail-group">
                                    <label><?php _e('Description:', 'wpseed'); ?></label>
                                    <p><?php echo esc_html($contributor['description']); ?></p>
                                </div>
                                
                                <div class="detail-group">
                                    <label><?php _e('Full Details:', 'wpseed'); ?></label>
                                    <p><?php echo esc_html($contributor['details']); ?></p>
                                </div>
                                
                                <div class="detail-group">
                                    <label><?php _e('Category:', 'wpseed'); ?></label>
                                    <span class="type-badge type-<?php echo esc_attr($contributor['category']); ?>">
                                        <?php echo esc_html(ucfirst($contributor['category'])); ?>
                                    </span>
                                </div>
                                
                                <div class="detail-group">
                                    <label><?php _e('License:', 'wpseed'); ?></label>
                                    <span class="license-badge"><?php echo esc_html($contributor['license']); ?></span>
                                </div>
                                
                                <?php if ($contributor['stats']): ?>
                                    <div class="detail-group">
                                        <label><?php _e('Statistics:', 'wpseed'); ?></label>
                                        <p><?php echo esc_html($contributor['stats']); ?></p>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="detail-actions">
                                    <?php if ($contributor['website']): ?>
                                        <a href="<?php echo esc_url($contributor['website']); ?>" class="button button-primary" target="_blank">
                                            <?php _e('Visit Website', 'wpseed'); ?>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($contributor['github']): ?>
                                        <a href="<?php echo esc_url($contributor['github']); ?>" class="button" target="_blank">
                                            <?php _e('View on GitHub', 'wpseed'); ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="details-placeholder">
                                <p><?php _e('Select a contributor to view details', 'wpseed'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#category-filter').on('change', function() {
                var category = $(this).val();
                if (category) {
                    $('.accordion-row').hide();
                    $('.accordion-row[data-category="' + category + '"]').show();
                } else {
                    $('.accordion-row').show();
                }
            });
        });
        </script>
        <?php
    }
    
    private static function get_contributors() {
        return array(
            'action_scheduler' => array(
                'name' => 'Action Scheduler',
                'category' => 'library',
                'license' => 'GPL v3',
                'status' => 'bundled',
                'featured' => true,
                'description' => 'Battle-tested background processing library',
                'details' => 'Used by WooCommerce to handle millions of background tasks daily. Provides reliable job queue with automatic retry and monitoring.',
                'used_in' => 'Background task processing',
                'website' => 'https://actionscheduler.org/',
                'github' => 'https://github.com/woocommerce/action-scheduler',
                'logo' => '',
                'stats' => '5M+ active installations',
            ),
            'carbon_fields' => array(
                'name' => 'Carbon Fields',
                'category' => 'library',
                'license' => 'GPL v2',
                'status' => 'bundled',
                'featured' => true,
                'description' => 'Modern WordPress custom fields library',
                'details' => 'Developer-friendly custom fields library with clean API. Supports theme options, post meta, term meta, and user meta.',
                'used_in' => 'Settings framework, custom fields',
                'website' => 'https://carbonfields.net/',
                'github' => 'https://github.com/htmlburger/carbon-fields',
                'logo' => '',
                'stats' => '100K+ downloads',
            ),
            'parsedown' => array(
                'name' => 'Parsedown',
                'category' => 'library',
                'license' => 'MIT',
                'status' => 'bundled',
                'featured' => false,
                'description' => 'Markdown parser in PHP',
                'details' => 'Fast and extensible Markdown parser. Used for rendering documentation within WordPress admin.',
                'used_in' => 'Documentation viewer',
                'website' => 'https://parsedown.org/',
                'github' => 'https://github.com/erusev/parsedown',
                'logo' => '',
                'stats' => '10M+ downloads',
            ),
            'woocommerce' => array(
                'name' => 'WooCommerce',
                'category' => 'inspiration',
                'license' => 'GPL v3',
                'status' => 'reference',
                'featured' => true,
                'description' => 'Architecture patterns and best practices',
                'details' => 'WPSeed draws inspiration from WooCommerce\'s plugin architecture, including REST API patterns, background processing, and asset management.',
                'used_in' => 'Overall architecture, coding standards',
                'website' => 'https://woocommerce.com/',
                'github' => 'https://github.com/woocommerce/woocommerce',
                'logo' => '',
                'stats' => '5M+ active installations',
            ),
            'github' => array(
                'name' => 'GitHub',
                'category' => 'service',
                'license' => 'Commercial',
                'status' => 'integrated',
                'featured' => true,
                'description' => 'Version control and collaboration',
                'details' => 'GitHub provides version control, issue tracking, and CI/CD integration for WPSeed development.',
                'used_in' => 'Version control, documentation sync, CI/CD',
                'website' => 'https://github.com/',
                'github' => 'https://github.com/ryanbayne/wpseed',
                'logo' => '',
                'stats' => '100M+ developers',
            ),
        );
    }
}
