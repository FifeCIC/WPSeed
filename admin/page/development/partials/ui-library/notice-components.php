<?php
/**
 * wpseed UI Library - Notice Components
 *
 * @package wpseed/Admin/Views/Partials
 * @version 1.1.0
 */

defined('ABSPATH') || exit;
?>

<div class="wpseed-ui-section" data-section="notice-components">
    <div class="wpseed-card">
        <div class="wpseed-card-header">
            <h3><?php esc_html_e('Notice Components', 'wpseed'); ?></h3>
        </div>
        
        <div class="wpseed-card-body">
            
            <!-- WordPress Standard Notices -->
            <section class="notice-demo-group">
                <h5><?php esc_html_e('WordPress Standard Notices', 'wpseed'); ?></h5>
            
                <div class="notice notice-warning">
                    <p><strong>API Limit:</strong> You have used 80% of your monthly API quota.</p>
                </div>
                
                <div class="notice notice-error">
                    <p><strong>Connection Error:</strong> Unable to connect to trading platform API.</p>
                </div>
                
                <div class="notice notice-success">
                    <p><strong>Portfolio Updated:</strong> Added 50 shares of AAPL to your portfolio.</p>
                </div>
            </section>

            <!-- Demo Mode Indicators -->
            <section class="notice-demo-group">
                <h5><?php esc_html_e('Demo Mode Indicators', 'wpseed'); ?></h5>
                
                <div class="notice notice-info">
                    <p><strong>Demo Mode:</strong> This is a demonstration environment.</p>
                </div>
            </section>

            <!-- Custom Notice Boxes -->
            <section class="notice-demo-group">
                <h5><?php esc_html_e('Custom Notice Boxes', 'wpseed'); ?></h5>
                
                <div class="notice notice-info">
                    <p><strong>Trading Tip:</strong> Diversification reduces risk. Consider spreading investments across different sectors.</p>
                </div>
            </section>

            <!-- Notice Files from /notices folder -->
            <section class="notice-demo-group">
                <h5><?php esc_html_e('System Notices', 'wpseed'); ?></h5>
                <p><?php __('You will find this notice at the top of the page.', 'wpseed'); ?></p>
                
                <!-- Custom Notice -->
                <div id="message" class="updated wpseed-message">
                    <p><strong>API Connected:</strong> Successfully connected to Alpha Vantage API. Real-time data is now available.</p>
                </div>
                
            </section>

            <!-- Inline Alert Patterns -->
            <section class="notice-demo-group">
                <h5><?php esc_html_e('Inline Alert Patterns', 'wpseed'); ?></h5>
                <p><?php __('You will find this notice at the top of the page.', 'wpseed'); ?></p>
                
                <div class="notice notice-info">
                    <p><strong>Market Alert:</strong> High volatility detected in <code>TSLA</code>, <code>NVDA</code>. 
                    <a href="#" class="button button-small">View Details</a></p>
                </div>
                
                <div class="notice notice-warning">
                    <p><strong>Stop Loss Triggered:</strong> Position in MSFT closed at $330.25. 
                    <a href="#">View Transaction</a></p>
                </div>
                
            </section>

            <!-- Status Messages -->
            <section class="notice-demo-group">
                <h5><?php esc_html_e('Status Messages', 'wpseed'); ?></h5>
                
                <div class="status-message connecting">
                    <span class="dashicons dashicons-update spin"></span>
                    Connecting to trading platform...
                </div>
                
                <div class="status-message success">
                    <span class="dashicons dashicons-yes-alt"></span>
                    Connected to Alpaca Trading
                </div>
                
                <div class="status-message info">
                    <span class="dashicons dashicons-info"></span>
                    Market data delayed by 15 minutes
                </div>
            </section>

            <!-- Compact Guidelines -->
            <section class="notice-demo-group">
                <h5><?php esc_html_e('Usage Guidelines', 'wpseed'); ?></h5>
                <div class="notice notice-info">
                    <p><strong>Best Practices:</strong> Use specific titles • Provide clear actions • Make dismissible when appropriate • Test accessibility</p>
                </div>
            </section>
        </div>
    </div>
</div>
