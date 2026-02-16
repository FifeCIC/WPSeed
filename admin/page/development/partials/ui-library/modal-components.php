<?php
/**
 * UI Library Modal Components Partial
 *
 * @package wpseed/Admin/Views/Partials
 * @version 1.0.6
 */

defined('ABSPATH') || exit;
?>
<div class="wpseed-ui-section">
    <h3><?php esc_html_e('Modal Components', 'wpseed'); ?></h3>
    <p><?php esc_html_e('Dialog boxes or pop-up windows that are displayed on top of the current page.', 'wpseed'); ?></p>

    <div class="wpseed-component-group">
        <!-- Basic Modal Demo -->
        <div class="component-demo">
            <h4><?php esc_html_e('Basic Modal', 'wpseed'); ?></h4>
            <button class="tp-button tp-button-primary" id="open-demo-modal"><?php esc_html_e('Open Modal', 'wpseed'); ?></button>

            <!-- Modal Structure (hidden by default) -->
            <div id="ui-library-demo-modal" class="wpseed-modal" style="display:none;">
                <div class="wpseed-modal-content">
                    <div class="wpseed-modal-header">
                        <h2><?php esc_html_e('Sample Modal Title', 'wpseed'); ?></h2>
                        <button class="wpseed-modal-close" aria-label="<?php esc_attr_e('Close modal', 'wpseed'); ?>">&times;</button>
                    </div>
                    <div class="wpseed-modal-body">
                        <p><?php esc_html_e('This is the content of the modal. You can put any HTML here, including forms, text, or other components.', 'wpseed'); ?></p>
                        <p><?php esc_html_e('Modal dialogs are useful for displaying additional information, forms, or confirmation messages without navigating away from the current page.', 'wpseed'); ?></p>
                    </div>
                    <div class="wpseed-modal-footer">
                        <button class="tp-button tp-button-secondary close-demo-modal"><?php esc_html_e('Cancel', 'wpseed'); ?></button>
                        <button class="tp-button tp-button-primary"><?php esc_html_e('Save Changes', 'wpseed'); ?></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task Detail Modal Demo -->
        <div class="component-demo">
            <h4><?php esc_html_e('Task Detail Modal', 'wpseed'); ?></h4>
            <button class="tp-button tp-button-secondary" id="open-task-modal"><?php esc_html_e('View Task Details', 'wpseed'); ?></button>

            <!-- Task Detail Modal Structure -->
            <div id="ui-library-task-modal" class="wpseed-modal" style="display:none;">
                <div class="wpseed-modal-content">
                    <div class="wpseed-modal-header">
                        <h2><?php esc_html_e('Task Details', 'wpseed'); ?></h2>
                        <button class="wpseed-modal-close" aria-label="<?php esc_attr_e('Close modal', 'wpseed'); ?>">&times;</button>
                    </div>
                    <div class="wpseed-modal-body">
                        <div class="wpseed-task-detail-header">
                            <h3 class="wpseed-task-detail-title"><?php esc_html_e('Analyze AAPL Stock Performance', 'wpseed'); ?></h3>
                        </div>
                        
                        <div class="wpseed-task-detail-meta">
                            <div class="wpseed-task-detail-meta-item">
                                <span class="wpseed-task-detail-meta-label"><?php esc_html_e('Status:', 'wpseed'); ?></span>
                                <span class="status-active"><?php esc_html_e('Active', 'wpseed'); ?></span>
                            </div>
                            <div class="wpseed-task-detail-meta-item">
                                <span class="wpseed-task-detail-meta-label"><?php esc_html_e('Priority:', 'wpseed'); ?></span>
                                <span class="priority-high"><?php esc_html_e('High', 'wpseed'); ?></span>
                            </div>
                            <div class="wpseed-task-detail-meta-item">
                                <span class="wpseed-task-detail-meta-label"><?php esc_html_e('Created:', 'wpseed'); ?></span>
                                <span><?php echo esc_html( gmdate( 'Y-m-d H:i' ) ); ?></span>
                            </div>
                        </div>

                        <div class="wpseed-task-description">
                            <h4><?php esc_html_e('Description', 'wpseed'); ?></h4>
                            <p><?php esc_html_e('Complete technical analysis of Apple Inc. (AAPL) stock performance over the last quarter. Include price movements, volume analysis, and comparison with sector averages.', 'wpseed'); ?></p>
                        </div>

                        <div class="wpseed-task-attachments">
                            <h4><?php esc_html_e('Attachments', 'wpseed'); ?></h4>
                            <ul>
                                <li><span class="dashicons dashicons-media-spreadsheet"></span> AAPL_Q3_Data.xlsx</li>
                                <li><span class="dashicons dashicons-chart-line"></span> Technical_Indicators.pdf</li>
                            </ul>
                        </div>
                    </div>
                    <div class="wpseed-modal-footer">
                        <button class="tp-button tp-button-secondary close-task-modal"><?php esc_html_e('Close', 'wpseed'); ?></button>
                        <button class="tp-button tp-button-primary"><?php esc_html_e('Edit Task', 'wpseed'); ?></button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Loading Modal Demo -->
        <div class="component-demo">
            <h4><?php esc_html_e('Loading Modal', 'wpseed'); ?></h4>
            <button class="tp-button tp-button-secondary" id="open-loading-modal"><?php esc_html_e('Show Loading', 'wpseed'); ?></button>

            <!-- Loading Modal Structure -->
            <div id="ui-library-loading-modal" class="wpseed-modal" style="display:none;">
                <div class="wpseed-modal-content">
                    <div class="wpseed-modal-header">
                        <h2><?php esc_html_e('Processing Request', 'wpseed'); ?></h2>
                    </div>
                    <div class="wpseed-modal-body">
                        <div class="wpseed-loading-spinner">
                            <span class="spinner is-active"></span>
                            <p><?php esc_html_e('Please wait while we process your request...', 'wpseed'); ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Confirmation Modal Demo -->
        <div class="component-demo">
            <h4><?php esc_html_e('Confirmation Modal', 'wpseed'); ?></h4>
            <button class="tp-button tp-button-danger" id="open-confirm-modal"><?php esc_html_e('Delete Item', 'wpseed'); ?></button>

            <!-- Confirmation Modal Structure -->
            <div id="ui-library-confirm-modal" class="wpseed-modal" style="display:none;">
                <div class="wpseed-modal-content">
                    <div class="wpseed-modal-header">
                        <h2><?php esc_html_e('Confirm Deletion', 'wpseed'); ?></h2>
                        <button class="wpseed-modal-close" aria-label="<?php esc_attr_e('Close modal', 'wpseed'); ?>">&times;</button>
                    </div>
                    <div class="wpseed-modal-body">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <span class="dashicons dashicons-warning" style="color: #d63638; font-size: 32px; width: 32px; height: 32px;"></span>
                            <div>
                                <p style="margin: 0; font-weight: 600;"><?php esc_html_e('Are you sure you want to delete this item?', 'wpseed'); ?></p>
                                <p style="margin: 5px 0 0 0; color: #646970;"><?php esc_html_e('This action cannot be undone.', 'wpseed'); ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="wpseed-modal-footer">
                        <button class="tp-button tp-button-secondary close-confirm-modal"><?php esc_html_e('Cancel', 'wpseed'); ?></button>
                        <button class="tp-button tp-button-danger"><?php esc_html_e('Delete', 'wpseed'); ?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Add inline script for modal functionality using existing patterns
    $modal_script = "
        jQuery(document).ready(function($) {
            // Basic modal functionality
            $('#open-demo-modal').on('click', function() {
                $('#ui-library-demo-modal').show().addClass('open');
            });

            $('#open-task-modal').on('click', function() {
                $('#ui-library-task-modal').show().addClass('open');
            });

            $('#open-loading-modal').on('click', function() {
                var modal = $('#ui-library-loading-modal');
                modal.show().addClass('open');
                
                // Auto close loading modal after 3 seconds
                setTimeout(function() {
                    modal.hide().removeClass('open');
                }, 3000);
            });

            $('#open-confirm-modal').on('click', function() {
                $('#ui-library-confirm-modal').show().addClass('open');
            });

            // Close modal functionality
            $('.wpseed-modal-close, .close-demo-modal, .close-task-modal, .close-confirm-modal').on('click', function() {
                $(this).closest('.wpseed-modal').hide().removeClass('open');
            });

            // Close modal by clicking outside
            $('.wpseed-modal').on('click', function(event) {
                if ($(event.target).is('.wpseed-modal')) {
                    $(this).hide().removeClass('open');
                }
            });

            // Escape key to close modal
            $(document).on('keydown', function(event) {
                if (event.keyCode === 27) { // ESC key
                    $('.wpseed-modal:visible').hide().removeClass('open');
                }
            });
        });
    ";

    wp_add_inline_script('jquery', $modal_script);
    ?>
</div>
