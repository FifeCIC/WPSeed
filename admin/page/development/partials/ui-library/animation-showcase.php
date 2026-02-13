<?php
/**
 * UI Library Animation Showcase Partial
 *
 * @package wpseed/Admin/Views/Partials
 * @version 1.0.9
 */

defined('ABSPATH') || exit;
?>
<div class="wpseed-ui-section">
    <h3><?php esc_html_e('Animation Showcase', 'wpseed'); ?></h3>
    <p><?php esc_html_e('CSS animations and transitions for enhancing user experience and providing visual feedback.', 'wpseed'); ?></p>
    
    <div class="wpseed-component-group">
        <!-- Fade Animations -->
        <div class="component-demo">
            <h4><?php esc_html_e('Fade Animations', 'wpseed'); ?></h4>
            <div class="wpseed-component-showcase">
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Fade In', 'wpseed'); ?></div>
                    <div class="wpseed-card fade-in-demo" data-animation="wpseed-fade-in"><?php esc_html_e('Hover Me', 'wpseed'); ?></div>
                </div>
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Fade Out', 'wpseed'); ?></div>
                    <div class="wpseed-card fade-out-demo" data-animation="wpseed-fade-out"><?php esc_html_e('Hover Me', 'wpseed'); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Slide Animations -->
        <div class="component-demo">
            <h4><?php esc_html_e('Slide Animations', 'wpseed'); ?></h4>
            <div class="wpseed-component-showcase">
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Slide Down', 'wpseed'); ?></div>
                    <div class="wpseed-card slide-down-demo" data-animation="wpseed-slide-in-down"><?php esc_html_e('Hover Me', 'wpseed'); ?></div>
                </div>
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Slide Up', 'wpseed'); ?></div>
                    <div class="wpseed-card slide-up-demo" data-animation="wpseed-slide-in-up"><?php esc_html_e('Hover Me', 'wpseed'); ?></div>
                </div>
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Slide Left', 'wpseed'); ?></div>
                    <div class="wpseed-card slide-left-demo" data-animation="wpseed-slide-in-left"><?php esc_html_e('Hover Me', 'wpseed'); ?></div>
                </div>
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Slide Right', 'wpseed'); ?></div>
                    <div class="wpseed-card slide-right-demo" data-animation="wpseed-slide-in-right"><?php esc_html_e('Hover Me', 'wpseed'); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Continuous Animations -->
        <div class="component-demo">
            <h4><?php esc_html_e('Continuous Animations', 'wpseed'); ?></h4>
            <div class="wpseed-component-showcase">
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Pulse', 'wpseed'); ?></div>
                    <div class="wpseed-card wpseed-pulse"><?php esc_html_e('Pulse', 'wpseed'); ?></div>
                </div>
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Heartbeat', 'wpseed'); ?></div>
                    <div class="wpseed-card wpseed-heartbeat"><?php esc_html_e('Heartbeat', 'wpseed'); ?></div>
                </div>
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Spin', 'wpseed'); ?></div>
                    <div class="wpseed-card">
                        <span class="dashicons dashicons-update wpseed-spin"></span>
                    </div>
                </div>
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Bounce', 'wpseed'); ?></div>
                    <div class="wpseed-card wpseed-bounce"><?php esc_html_e('Bounce', 'wpseed'); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Attention Animations -->
        <div class="component-demo">
            <h4><?php esc_html_e('Attention Animations', 'wpseed'); ?></h4>
            <div class="wpseed-component-showcase">
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Shake', 'wpseed'); ?></div>
                    <div class="wpseed-card shake-demo" data-animation="wpseed-shake"><?php esc_html_e('Click Me', 'wpseed'); ?></div>
                </div>
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Flash', 'wpseed'); ?></div>
                    <div class="wpseed-card wpseed-flash"><?php esc_html_e('Flash', 'wpseed'); ?></div>
                </div>
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Highlight', 'wpseed'); ?></div>
                    <div class="wpseed-card highlight-demo" data-animation="wpseed-highlight"><?php esc_html_e('Click Me', 'wpseed'); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Scale Animations -->
        <div class="component-demo">
            <h4><?php esc_html_e('Scale Animations', 'wpseed'); ?></h4>
            <div class="wpseed-component-showcase">
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Scale In', 'wpseed'); ?></div>
                    <div class="wpseed-card scale-in-demo" data-animation="wpseed-scale-in"><?php esc_html_e('Hover Me', 'wpseed'); ?></div>
                </div>
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Scale Out', 'wpseed'); ?></div>
                    <div class="wpseed-card scale-out-demo" data-animation="wpseed-scale-out"><?php esc_html_e('Hover Me', 'wpseed'); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Transitions -->
        <div class="component-demo">
            <h4><?php esc_html_e('Transitions', 'wpseed'); ?></h4>
            <div class="wpseed-component-showcase">
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Color Transition', 'wpseed'); ?></div>
                    <div class="wpseed-card wpseed-transition-colors transition-demo"><?php esc_html_e('Hover Me', 'wpseed'); ?></div>
                </div>
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Transform Transition', 'wpseed'); ?></div>
                    <div class="wpseed-card wpseed-transition-transform transform-demo"><?php esc_html_e('Hover Me', 'wpseed'); ?></div>
                </div>
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Fast Transition', 'wpseed'); ?></div>
                    <div class="wpseed-card wpseed-transition-fast transition-demo"><?php esc_html_e('Hover Me', 'wpseed'); ?></div>
                </div>
                <div class="animation-item">
                    <div class="animation-label"><?php esc_html_e('Slow Transition', 'wpseed'); ?></div>
                    <div class="wpseed-card wpseed-transition-slow transition-demo"><?php esc_html_e('Hover Me', 'wpseed'); ?></div>
                </div>
            </div>
        </div>
        
        <!-- Sequenced Animations -->
        <div class="component-demo">
            <h4><?php esc_html_e('Sequenced Animations', 'wpseed'); ?></h4>
            <div class="animation-sequence">
                <button id="sequence-trigger" class="button button-primary"><?php esc_html_e('Start Sequence', 'wpseed'); ?></button>
                <div class="sequence-container">
                    <div class="sequence-item wpseed-delay-100"><?php esc_html_e('First', 'wpseed'); ?></div>
                    <div class="sequence-item wpseed-delay-300"><?php esc_html_e('Second', 'wpseed'); ?></div>
                    <div class="sequence-item wpseed-delay-500"><?php esc_html_e('Third', 'wpseed'); ?></div>
                    <div class="sequence-item wpseed-delay-700"><?php esc_html_e('Fourth', 'wpseed'); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
