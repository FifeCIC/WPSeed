<?php
/**
 * WPSeed - Accordion Table Example
 *
 * Demonstrates table with accordion rows and dynamic sidebar.
 *
 * @package WPSeed/Examples
 * @version 2.0.0
 */

if (!defined('ABSPATH')) exit;

// Enqueue styles and scripts
wp_enqueue_style('wpseed-accordion-table');
wp_enqueue_script('wpseed-accordion-table');

/**
 * Return the nonce action used to sign and verify accordion configure URLs.
 *
 * Centralised so the action string is identical at link-generation time
 * (add_query_arg loop) and at verification time (get selected item block),
 * preventing any mismatch.
 *
 * @since  2.0.0
 * @return string Nonce action slug.
 */
function wpseed_accordion_nonce_action() {
    return 'wpseed_accordion_configure';
}

// Example data
$wpseed_items = array(
    'item_1' => array(
        'name' => 'Example Item 1',
        'status' => 'active',
        'priority' => 'high',
        'weight' => 85,
        'last_used' => '2024-01-15 10:30:00',
        'description' => 'This is an example item demonstrating the accordion table layout.',
        'details' => 'Additional details about this item that appear in the sidebar when selected.',
    ),
    'item_2' => array(
        'name' => 'Example Item 2',
        'status' => 'inactive',
        'priority' => 'medium',
        'weight' => 60,
        'last_used' => '2024-01-10 14:20:00',
        'description' => 'Another example item with different properties.',
        'details' => 'More information about this particular item.',
    ),
    'item_3' => array(
        'name' => 'Example Item 3',
        'status' => 'active',
        'priority' => 'low',
        'weight' => 40,
        'last_used' => '2024-01-12 09:15:00',
        'description' => 'Third example showing various states and configurations.',
        'details' => 'Detailed information that helps understand this item better.',
    ),
);

// Get selected item — verify the configure nonce before reading $_GET['configure'].
// wp_verify_nonce() returns false when the nonce is absent or stale, so the
// default item is shown safely on direct URL access without the nonce arg.
$wpseed_selected_item = 'item_1';
if ( current_user_can( 'manage_options' ) && isset( $_GET['configure'] ) ) {
    $wpseed_raw_nonce = isset( $_GET['_wpnonce'] ) ? sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ) : '';
    if ( wp_verify_nonce( $wpseed_raw_nonce, wpseed_accordion_nonce_action() ) ) {
        // sanitize_key() is correct for an array-key slug value.
        $wpseed_selected_item = sanitize_key( wp_unslash( $_GET['configure'] ) );
    }
}
?>

<div class="wpseed-accordion-container">
    
    <div class="wpseed-layout">
        <!-- Left Column: Accordion Table -->
        <div class="wpseed-table-container">
            
            <div class="tablenav top">
                <div class="alignleft actions">
                    <input type="search" id="item-search" placeholder="<?php esc_attr_e('Search items...', 'wpseed'); ?>">
                    <button type="button" class="button"><?php esc_html_e('Search', 'wpseed'); ?></button>
                </div>
            </div>
            
            <div class="wp-list-table widefat fixed striped">
                <div class="table-header" style="display: flex; background: #f1f1f1; padding: 12px 15px; font-weight: 600; border-bottom: 1px solid #c3c4c7;">
                    <div style="flex: 2;"><?php esc_html_e('Name', 'wpseed'); ?></div>
                    <div style="flex: 1;"><?php esc_html_e('Status', 'wpseed'); ?></div>
                    <div style="flex: 1;"><?php esc_html_e('Priority', 'wpseed'); ?></div>
                    <div style="flex: 1;"><?php esc_html_e('Weight', 'wpseed'); ?></div>
                    <div style="flex: 1;"><?php esc_html_e('Last Used', 'wpseed'); ?></div>
                </div>
            </div>

            <div class="wpseed-accordion-table">
                <?php foreach ($wpseed_items as $wpseed_item_id => $wpseed_item): ?>
                    <div class="accordion-row">
                        <div class="accordion-header">
                            <div style="flex: 2;">
                                <strong><?php echo esc_html($wpseed_item['name']); ?></strong>
                            </div>
                            <div style="flex: 1;">
                                <span class="status-badge status-<?php echo esc_attr($wpseed_item['status']); ?>">
                                    <?php echo esc_html(ucfirst($wpseed_item['status'])); ?>
                                </span>
                            </div>
                            <div style="flex: 1;">
                                <span class="priority-badge priority-<?php echo esc_attr($wpseed_item['priority']); ?>">
                                    <?php echo esc_html(ucfirst($wpseed_item['priority'])); ?>
                                </span>
                            </div>
                            <div style="flex: 1;"><?php echo esc_html($wpseed_item['weight']); ?></div>
                            <div style="flex: 1;">
                                <?php echo esc_html(human_time_diff(strtotime($wpseed_item['last_used']), current_time('timestamp')) . ' ago'); ?>
                            </div>
                        </div>
                        
                        <div class="accordion-content">
                            <div class="item-meta">
                                <div>
                                    <strong><?php esc_html_e('Description:', 'wpseed'); ?></strong><br>
                                    <?php echo esc_html($wpseed_item['description']); ?>
                                </div>
                                <div>
                                    <strong><?php esc_html_e('Status:', 'wpseed'); ?></strong><br>
                                    <?php echo esc_html(ucfirst($wpseed_item['status'])); ?>
                                </div>
                                <div>
                                    <strong><?php esc_html_e('Priority:', 'wpseed'); ?></strong><br>
                                    <?php echo esc_html(ucfirst($wpseed_item['priority'])); ?>
                                </div>
                            </div>
                            
                            <div class="item-actions">
                                <?php
                                // Sign the configure URL with a nonce so the
                                // GET parameter can be verified before use.
                                $wpseed_configure_url = wp_nonce_url(
                                    add_query_arg( 'configure', $wpseed_item_id ),
                                    wpseed_accordion_nonce_action()
                                );
                                ?>
                                <a href="<?php echo esc_url( $wpseed_configure_url ); ?>" class="button button-primary">
                                    <?php esc_html_e('Configure', 'wpseed'); ?>
                                </a>
                                <button type="button" class="button"><?php esc_html_e('Edit', 'wpseed'); ?></button>
                                <button type="button" class="button"><?php esc_html_e('Delete', 'wpseed'); ?></button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Right Column: Details Sidebar -->
        <div class="wpseed-sidebar">
            <div class="wpseed-details-container">
                <?php if (isset($wpseed_items[$wpseed_selected_item])): 
                    $wpseed_item = $wpseed_items[$wpseed_selected_item];
                ?>
                    <div class="section-header">
                        <h3><?php echo esc_html($wpseed_item['name']); ?></h3>
                    </div>
                    
                    <div class="section-content">
                        <div class="detail-group">
                            <label><?php esc_html_e('Description:', 'wpseed'); ?></label>
                            <p><?php echo esc_html($wpseed_item['description']); ?></p>
                        </div>
                        
                        <div class="detail-group">
                            <label><?php esc_html_e('Details:', 'wpseed'); ?></label>
                            <p><?php echo esc_html($wpseed_item['details']); ?></p>
                        </div>
                        
                        <div class="detail-group">
                            <label><?php esc_html_e('Status:', 'wpseed'); ?></label>
                            <span class="status-badge status-<?php echo esc_attr($wpseed_item['status']); ?>">
                                <?php echo esc_html(ucfirst($wpseed_item['status'])); ?>
                            </span>
                        </div>
                        
                        <div class="detail-group">
                            <label><?php esc_html_e('Priority:', 'wpseed'); ?></label>
                            <span class="priority-badge priority-<?php echo esc_attr($wpseed_item['priority']); ?>">
                                <?php echo esc_html(ucfirst($wpseed_item['priority'])); ?>
                            </span>
                        </div>
                        
                        <div class="detail-group">
                            <label><?php esc_html_e('Weight:', 'wpseed'); ?></label>
                            <input type="number" value="<?php echo esc_attr($wpseed_item['weight']); ?>" min="0" max="100">
                        </div>
                        
                        <div class="detail-actions">
                            <button type="button" class="button button-primary"><?php esc_html_e('Save Changes', 'wpseed'); ?></button>
                            <button type="button" class="button"><?php esc_html_e('Reset', 'wpseed'); ?></button>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="details-placeholder">
                        <p><?php esc_html_e('Select an item to view details', 'wpseed'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
