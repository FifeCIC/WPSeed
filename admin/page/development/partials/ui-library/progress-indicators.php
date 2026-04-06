<?php
/**
 * UI Library Progress Indicators Partial
 *
 * @package wpseed/Admin/Views/Partials
 * @version 1.2.0
 */

defined('ABSPATH') || exit;
?>
<div class="wpseed-ui-section">
    <h3><?php esc_html_e('Progress Indicators', 'wpseed'); ?></h3>
    <p><?php esc_html_e('Visual indicators to show progress, loading states, and multi-step processes.', 'wpseed'); ?></p>
    
    <div class="wpseed-component-group">
        <!-- Basic Progress Bars -->
        <div class="component-demo">
            <h4><?php esc_html_e('Basic Progress Bars', 'wpseed'); ?></h4>
            
            <div class="progress-example">
                <div class="progress-label"><?php esc_html_e('Default Progress (40%)', 'wpseed'); ?></div>
                <div class="media-progress-bar">
                    <div style="width: 40%;"></div>
                </div>
            </div>
            
            <div class="progress-example">
                <div class="progress-label"><?php esc_html_e('Success Progress (75%)', 'wpseed'); ?></div>
                <div class="media-progress-bar progress-success">
                    <div style="width: 75%;"></div>
                </div>
            </div>
            
            <div class="progress-example">
                <div class="progress-label"><?php esc_html_e('Warning Progress (60%)', 'wpseed'); ?></div>
                <div class="media-progress-bar progress-warning">
                    <div style="width: 60%;"></div>
                </div>
            </div>
            
            <div class="progress-example">
                <div class="progress-label"><?php esc_html_e('Error Progress (25%)', 'wpseed'); ?></div>
                <div class="media-progress-bar progress-error">
                    <div style="width: 25%;"></div>
                </div>
            </div>
        </div>
        
        <!-- WordPress Admin Loading Spinners -->
        <div class="component-demo">
            <h4><?php esc_html_e('Loading Spinners (WordPress Native)', 'wpseed'); ?></h4>
            
            <div class="wpseed-component-showcase">
                <div class="spinner-example">
                    <div class="spinner-label"><?php esc_html_e('WordPress Default', 'wpseed'); ?></div>
                    <span class="spinner is-active"></span>
                </div>
                
                <div class="spinner-example">
                    <div class="spinner-label"><?php esc_html_e('With Text', 'wpseed'); ?></div>
                    <div class="loading-with-text">
                        <span class="spinner is-active"></span>
                        <span class="loading-text"><?php esc_html_e('Loading data...', 'wpseed'); ?></span>
                    </div>
                </div>
                
                <div class="spinner-example">
                    <div class="spinner-label"><?php esc_html_e('Custom Color', 'wpseed'); ?></div>
                    <span class="spinner is-active" style="filter: hue-rotate(120deg);"></span>
                </div>
            </div>
        </div>
        
        <!-- Step Indicators - Using existing patterns -->
        <div class="component-demo">
            <h4><?php esc_html_e('Step Indicators', 'wpseed'); ?></h4>
            
            <div class="step-progress-container">
                <div class="step-progress-item completed">
                    <div class="step-progress-marker">
                        <span class="dashicons dashicons-yes"></span>
                    </div>
                    <div class="step-progress-label"><?php esc_html_e('Account Setup', 'wpseed'); ?></div>
                </div>
                <div class="step-progress-item active">
                    <div class="step-progress-marker">2</div>
                    <div class="step-progress-label"><?php esc_html_e('API Connection', 'wpseed'); ?></div>
                </div>
                <div class="step-progress-item">
                    <div class="step-progress-marker">3</div>
                    <div class="step-progress-label"><?php esc_html_e('Preferences', 'wpseed'); ?></div>
                </div>
                <div class="step-progress-item">
                    <div class="step-progress-marker">4</div>
                    <div class="step-progress-label"><?php esc_html_e('Confirmation', 'wpseed'); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Progress with Text - Using inline styles for positioning -->
        <div class="component-demo">
            <h4><?php esc_html_e('Progress with Text', 'wpseed'); ?></h4>
            
            <div class="progress-example">
                <div class="progress-with-text-container">
                    <div class="media-progress-bar">
                        <div style="width: 65%;"></div>
                    </div>
                    <div class="progress-text-overlay">65%</div>
                </div>
            </div>
            
            <div class="progress-example">
                <div class="progress-container-labeled">
                    <div class="progress-header">
                        <div class="progress-label"><?php esc_html_e('Data Processing', 'wpseed'); ?></div>
                        <div class="progress-value">85%</div>
                    </div>
                    <div class="media-progress-bar progress-success">
                        <div style="width: 85%;"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Infinite Scroll Loader - Using WordPress spinner -->
        <div class="component-demo">
            <h4><?php esc_html_e('Content Loading States', 'wpseed'); ?></h4>
            
            <div class="infinite-scroll-container">
                <div class="infinite-scroll-items">
                    <div class="infinite-scroll-item">
                        <h5><?php esc_html_e('Item 1', 'wpseed'); ?></h5>
                        <p><?php esc_html_e('Example content for the first item in the infinite scroll list.', 'wpseed'); ?></p>
                    </div>
                    <div class="infinite-scroll-item">
                        <h5><?php esc_html_e('Item 2', 'wpseed'); ?></h5>
                        <p><?php esc_html_e('Example content for the second item in the infinite scroll list.', 'wpseed'); ?></p>
                    </div>
                    <div class="infinite-scroll-item">
                        <h5><?php esc_html_e('Item 3', 'wpseed'); ?></h5>
                        <p><?php esc_html_e('Example content for the third item in the infinite scroll list.', 'wpseed'); ?></p>
                    </div>
                </div>
                <div class="infinite-scroll-loader" style="display: none;">
                    <span class="spinner is-active"></span>
                    <p><?php esc_html_e('Loading more items...', 'wpseed'); ?></p>
                </div>
                <button class="tp-button tp-button-secondary infinite-scroll-button">
                    <?php esc_html_e('Load More', 'wpseed'); ?>
                </button>
            </div>
        </div>
    </div>

    <?php
    // Add interactive demo script — variable prefixed with wpseed_ to satisfy global variable naming standards.
    $wpseed_progress_script = "
        jQuery(document).ready(function($) {
            // Simulate progress animation for media-progress-bar
            function animateProgress() {
                $('.media-progress-bar div').each(function() {
                    var targetWidth = $(this).css('width');
                    $(this).css('width', '0%').animate({
                        width: targetWidth
                    }, 1500);
                });
                
                // Reset after 3 seconds to demo again
                setTimeout(function() {
                    animateProgress();
                }, 3000);
            }
            
            // Load more button functionality
            $('.infinite-scroll-button').on('click', function() {
                var button = $(this);
                var loader = $('.infinite-scroll-loader');
                
                button.hide();
                loader.show();
                
                setTimeout(function() {
                    loader.hide();
                    
                    var newItems = '';
                    for (var i = 4; i <= 6; i++) {
                        newItems += '<div class=\"infinite-scroll-item\">' +
                                    '<h5>Item ' + i + '</h5>' +
                                    '<p>Example content for item ' + i + ' in the infinite scroll list.</p>' +
                                    '</div>';
                    }
                    
                    $('.infinite-scroll-items').append(newItems);
                    button.show();
                }, 1500);
            });
            
            // Initial animation
            animateProgress();
        });
    ";
    
    wp_add_inline_script('jquery', $wpseed_progress_script);
    ?>
</div>
