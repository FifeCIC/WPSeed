<?php
/**
 * UI Library Form Components Partial
 *
 * @package wpseed/Admin/Views/Partials
 * @version 1.0.0
 */

defined('ABSPATH') || exit;
?>
<div class="wpseed-ui-section">
    <h3><?php esc_html_e('Form Components', 'wpseed'); ?></h3>
    <p><?php esc_html_e('Standard form elements and input controls for consistent user input handling.', 'wpseed'); ?></p>

    <!-- Text Inputs -->
    <div class="wpseed-component-group">
        <h4><?php esc_html_e('Text Inputs', 'wpseed'); ?></h4>
        <div class="wpseed-form-showcase">
            <div class="wpseed-form-row">
                <label class="wpseed-form-label" for="demo-text-input"><?php esc_html_e('Text Input', 'wpseed'); ?></label>
                <input type="text" id="demo-text-input" class="wpseed-form-input" placeholder="<?php esc_attr_e('Enter text...', 'wpseed'); ?>">
            </div>
            <div class="wpseed-form-row">
                <label class="wpseed-form-label" for="demo-email-input"><?php esc_html_e('Email Input', 'wpseed'); ?></label>
                <input type="email" id="demo-email-input" class="wpseed-form-input" placeholder="<?php esc_attr_e('user@example.com', 'wpseed'); ?>">
            </div>
            <div class="wpseed-form-row">
                <label class="wpseed-form-label" for="demo-password-input"><?php esc_html_e('Password Input', 'wpseed'); ?></label>
                <input type="password" id="demo-password-input" class="wpseed-form-input" placeholder="<?php esc_attr_e('Enter password...', 'wpseed'); ?>">
            </div>
            <div class="wpseed-form-row">
                <label class="wpseed-form-label" for="demo-number-input"><?php esc_html_e('Number Input', 'wpseed'); ?></label>
                <input type="number" id="demo-number-input" class="wpseed-form-input wpseed-form-input-number" min="0" max="100" value="50">
            </div>
        </div>
    </div>

    <!-- Textarea -->
    <div class="wpseed-component-group">
        <h4><?php esc_html_e('Textarea', 'wpseed'); ?></h4>
        <div class="wpseed-form-showcase">
            <div class="wpseed-form-row">
                <label class="wpseed-form-label" for="demo-textarea"><?php esc_html_e('Description', 'wpseed'); ?></label>
                <textarea id="demo-textarea" class="wpseed-form-textarea" rows="4" placeholder="<?php esc_attr_e('Enter detailed description...', 'wpseed'); ?>"></textarea>
            </div>
        </div>
    </div>

    <!-- Select Dropdowns -->
    <div class="wpseed-component-group">
        <h4><?php esc_html_e('Select Dropdowns', 'wpseed'); ?></h4>
        <div class="wpseed-form-showcase">
            <div class="wpseed-form-row">
                <label class="wpseed-form-label" for="demo-select"><?php esc_html_e('Single Select', 'wpseed'); ?></label>
                <select id="demo-select" class="wpseed-form-select">
                    <option value=""><?php esc_html_e('Choose option...', 'wpseed'); ?></option>
                    <option value="option1"><?php esc_html_e('Option 1', 'wpseed'); ?></option>
                    <option value="option2"><?php esc_html_e('Option 2', 'wpseed'); ?></option>
                    <option value="option3"><?php esc_html_e('Option 3', 'wpseed'); ?></option>
                </select>
            </div>
            <div class="wpseed-form-row">
                <label class="wpseed-form-label" for="demo-multiselect"><?php esc_html_e('Multi Select', 'wpseed'); ?></label>
                <select id="demo-multiselect" class="wpseed-form-select wpseed-form-select-multiple" multiple size="4">
                    <option value="apple"><?php esc_html_e('Apple', 'wpseed'); ?></option>
                    <option value="banana" selected><?php esc_html_e('Banana', 'wpseed'); ?></option>
                    <option value="cherry"><?php esc_html_e('Cherry', 'wpseed'); ?></option>
                    <option value="date" selected><?php esc_html_e('Date', 'wpseed'); ?></option>
                </select>
            </div>
        </div>
    </div>

    <!-- Checkbox and Radio Groups -->
    <div class="wpseed-component-group">
        <h4><?php esc_html_e('Checkbox and Radio Groups', 'wpseed'); ?></h4>
        <div class="wpseed-form-showcase">
            <div class="wpseed-form-row">
                <fieldset class="wpseed-form-fieldset">
                    <legend class="wpseed-form-legend"><?php esc_html_e('Checkbox Group', 'wpseed'); ?></legend>
                    <div class="wpseed-form-checkbox-group">
                        <label class="wpseed-form-checkbox-label">
                            <input type="checkbox" name="demo-checkbox[]" value="option1" checked class="wpseed-form-checkbox">
                            <span class="wpseed-form-checkbox-text"><?php esc_html_e('Option 1', 'wpseed'); ?></span>
                        </label>
                        <label class="wpseed-form-checkbox-label">
                            <input type="checkbox" name="demo-checkbox[]" value="option2" class="wpseed-form-checkbox">
                            <span class="wpseed-form-checkbox-text"><?php esc_html_e('Option 2', 'wpseed'); ?></span>
                        </label>
                        <label class="wpseed-form-checkbox-label">
                            <input type="checkbox" name="demo-checkbox[]" value="option3" checked class="wpseed-form-checkbox">
                            <span class="wpseed-form-checkbox-text"><?php esc_html_e('Option 3', 'wpseed'); ?></span>
                        </label>
                    </div>
                </fieldset>
            </div>
            <div class="wpseed-form-row">
                <fieldset class="wpseed-form-fieldset">
                    <legend class="wpseed-form-legend"><?php esc_html_e('Radio Group', 'wpseed'); ?></legend>
                    <div class="wpseed-form-radio-group">
                        <label class="wpseed-form-radio-label">
                            <input type="radio" name="demo-radio" value="small" checked class="wpseed-form-radio">
                            <span class="wpseed-form-radio-text"><?php esc_html_e('Small', 'wpseed'); ?></span>
                        </label>
                        <label class="wpseed-form-radio-label">
                            <input type="radio" name="demo-radio" value="medium" class="wpseed-form-radio">
                            <span class="wpseed-form-radio-text"><?php esc_html_e('Medium', 'wpseed'); ?></span>
                        </label>
                        <label class="wpseed-form-radio-label">
                            <input type="radio" name="demo-radio" value="large" class="wpseed-form-radio">
                            <span class="wpseed-form-radio-text"><?php esc_html_e('Large', 'wpseed'); ?></span>
                        </label>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>

    <!-- Form Validation States -->
    <div class="wpseed-component-group">
        <h4><?php esc_html_e('Validation States', 'wpseed'); ?></h4>
        <div class="wpseed-form-showcase">
            <div class="wpseed-form-row">
                <label class="wpseed-form-label" for="demo-success-input"><?php esc_html_e('Success State', 'wpseed'); ?></label>
                <input type="text" id="demo-success-input" class="wpseed-form-input wpseed-form-input-success" value="<?php esc_attr_e('Valid input', 'wpseed'); ?>">
                <div class="wpseed-form-feedback wpseed-form-feedback-success">
                    <span class="dashicons dashicons-yes-alt"></span>
                    <?php esc_html_e('This field is valid', 'wpseed'); ?>
                </div>
            </div>
            <div class="wpseed-form-row">
                <label class="wpseed-form-label" for="demo-error-input"><?php esc_html_e('Error State', 'wpseed'); ?></label>
                <input type="text" id="demo-error-input" class="wpseed-form-input wpseed-form-input-error" value="<?php esc_attr_e('Invalid input', 'wpseed'); ?>">
                <div class="wpseed-form-feedback wpseed-form-feedback-error">
                    <span class="dashicons dashicons-dismiss"></span>
                    <?php esc_html_e('This field has an error', 'wpseed'); ?>
                </div>
            </div>
            <div class="wpseed-form-row">
                <label class="wpseed-form-label" for="demo-warning-input"><?php esc_html_e('Warning State', 'wpseed'); ?></label>
                <input type="text" id="demo-warning-input" class="wpseed-form-input wpseed-form-input-warning" value="<?php esc_attr_e('Warning input', 'wpseed'); ?>">
                <div class="wpseed-form-feedback wpseed-form-feedback-warning">
                    <span class="dashicons dashicons-warning"></span>
                    <?php esc_html_e('This field has a warning', 'wpseed'); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Input -->
    <div class="wpseed-component-group">
        <h4><?php esc_html_e('Search Input', 'wpseed'); ?></h4>
        <div class="wpseed-form-showcase">
            <div class="wpseed-form-row">
                <label class="wpseed-form-label" for="demo-search-input"><?php esc_html_e('Search', 'wpseed'); ?></label>
                <div class="wpseed-search-wrapper">
                    <input type="search" id="demo-search-input" class="wpseed-form-input wpseed-search-input" placeholder="<?php esc_attr_e('Search...', 'wpseed'); ?>">
                    <span class="wpseed-search-icon dashicons dashicons-search"></span>
                </div>
            </div>
        </div>
    </div>

    <!-- File Upload -->
    <div class="wpseed-component-group">
        <h4><?php esc_html_e('File Upload', 'wpseed'); ?></h4>
        <div class="wpseed-form-showcase">
            <div class="wpseed-form-row">
                <label class="wpseed-form-label" for="demo-file-input"><?php esc_html_e('File Upload', 'wpseed'); ?></label>
                <input type="file" id="demo-file-input" class="wpseed-form-file">
                <p class="wpseed-form-description"><?php esc_html_e('Choose a file to upload (max 2MB)', 'wpseed'); ?></p>
            </div>
        </div>
    </div>

    <!-- Form Layouts -->
    <div class="wpseed-component-group">
        <h4><?php esc_html_e('Form Layouts', 'wpseed'); ?></h4>
        <div class="wpseed-form-showcase">
            <!-- Horizontal Layout -->
            <div class="wpseed-form-layout wpseed-form-layout-horizontal">
                <h5><?php esc_html_e('Horizontal Layout', 'wpseed'); ?></h5>
                <div class="wpseed-form-row wpseed-form-row-horizontal">
                    <label class="wpseed-form-label wpseed-form-label-horizontal" for="demo-horizontal-1"><?php esc_html_e('First Name:', 'wpseed'); ?></label>
                    <input type="text" id="demo-horizontal-1" class="wpseed-form-input">
                </div>
                <div class="wpseed-form-row wpseed-form-row-horizontal">
                    <label class="wpseed-form-label wpseed-form-label-horizontal" for="demo-horizontal-2"><?php esc_html_e('Last Name:', 'wpseed'); ?></label>
                    <input type="text" id="demo-horizontal-2" class="wpseed-form-input">
                </div>
            </div>

            <!-- Inline Layout -->
            <div class="wpseed-form-layout wpseed-form-layout-inline">
                <h5><?php esc_html_e('Inline Layout', 'wpseed'); ?></h5>
                <div class="wpseed-form-row wpseed-form-row-inline">
                    <label class="wpseed-form-label wpseed-form-label-inline" for="demo-inline-1"><?php esc_html_e('City:', 'wpseed'); ?></label>
                    <input type="text" id="demo-inline-1" class="wpseed-form-input wpseed-form-input-inline">
                    <label class="wpseed-form-label wpseed-form-label-inline" for="demo-inline-2"><?php esc_html_e('State:', 'wpseed'); ?></label>
                    <select id="demo-inline-2" class="wpseed-form-select wpseed-form-select-inline">
                        <option value=""><?php esc_html_e('Select...', 'wpseed'); ?></option>
                        <option value="ca"><?php esc_html_e('California', 'wpseed'); ?></option>
                        <option value="ny"><?php esc_html_e('New York', 'wpseed'); ?></option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Simple Contact Form -->
    <div class="wpseed-component-group">
        <h4><?php esc_html_e('Simple Contact Form', 'wpseed'); ?></h4>
        <div class="wpseed-form-showcase">
            <form method="post" action="" class="wpseed-demo-form">
                <?php wp_nonce_field('wpseed_ui_contact_form'); ?>
                <input type="hidden" name="wpseed_form_action" value="contact_form">
                
                <div class="wpseed-form-row">
                    <label class="wpseed-form-label" for="contact-name"><?php esc_html_e('Name *', 'wpseed'); ?></label>
                    <input type="text" id="contact-name" name="contact_name" class="wpseed-form-input" required>
                </div>
                <div class="wpseed-form-row">
                    <label class="wpseed-form-label" for="contact-email"><?php esc_html_e('Email *', 'wpseed'); ?></label>
                    <input type="email" id="contact-email" name="contact_email" class="wpseed-form-input" required>
                </div>
                <div class="wpseed-form-row">
                    <label class="wpseed-form-label" for="contact-message"><?php esc_html_e('Message *', 'wpseed'); ?></label>
                    <textarea id="contact-message" name="contact_message" class="wpseed-form-textarea" rows="4" required></textarea>
                </div>
                <div class="wpseed-form-actions">
                    <button type="submit" class="tp-button tp-button-primary"><?php esc_html_e('Send Message', 'wpseed'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Trading Settings Form -->
    <div class="wpseed-component-group">
        <h4><?php esc_html_e('Trading Settings Form', 'wpseed'); ?></h4>
        <div class="wpseed-form-showcase">
            <form method="post" action="" class="wpseed-demo-form">
                <?php wp_nonce_field('wpseed_ui_trading_settings'); ?>
                <input type="hidden" name="wpseed_form_action" value="trading_settings">
                
                <div class="wpseed-form-row">
                    <label class="wpseed-form-label" for="risk-level"><?php esc_html_e('Risk Level', 'wpseed'); ?></label>
                    <select id="risk-level" name="risk_level" class="wpseed-form-select">
                        <option value="low"><?php esc_html_e('Low Risk', 'wpseed'); ?></option>
                        <option value="medium" selected><?php esc_html_e('Medium Risk', 'wpseed'); ?></option>
                        <option value="high"><?php esc_html_e('High Risk', 'wpseed'); ?></option>
                    </select>
                </div>
                <div class="wpseed-form-row">
                    <label class="wpseed-form-label" for="max-investment"><?php esc_html_e('Max Investment ($)', 'wpseed'); ?></label>
                    <input type="number" id="max-investment" name="max_investment" class="wpseed-form-input" min="100" max="100000" value="5000">
                </div>
                <div class="wpseed-form-row">
                    <fieldset class="wpseed-form-fieldset">
                        <legend class="wpseed-form-legend"><?php esc_html_e('Trading Preferences', 'wpseed'); ?></legend>
                        <div class="wpseed-form-checkbox-group">
                            <label class="wpseed-form-checkbox-label">
                                <input type="checkbox" name="preferences[]" value="day_trading" class="wpseed-form-checkbox">
                                <span class="wpseed-form-checkbox-text"><?php esc_html_e('Day Trading', 'wpseed'); ?></span>
                            </label>
                            <label class="wpseed-form-checkbox-label">
                                <input type="checkbox" name="preferences[]" value="swing_trading" class="wpseed-form-checkbox" checked>
                                <span class="wpseed-form-checkbox-text"><?php esc_html_e('Swing Trading', 'wpseed'); ?></span>
                            </label>
                            <label class="wpseed-form-checkbox-label">
                                <input type="checkbox" name="preferences[]" value="long_term" class="wpseed-form-checkbox">
                                <span class="wpseed-form-checkbox-text"><?php esc_html_e('Long-term Investment', 'wpseed'); ?></span>
                            </label>
                        </div>
                    </fieldset>
                </div>
                <div class="wpseed-form-actions">
                    <button type="submit" class="tp-button tp-button-primary"><?php esc_html_e('Save Settings', 'wpseed'); ?></button>
                    <button type="reset" class="tp-button tp-button-secondary"><?php esc_html_e('Reset', 'wpseed'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Ajax Validation Form -->
    <div class="wpseed-component-group">
        <h4><?php esc_html_e('Ajax Validation Form', 'wpseed'); ?></h4>
        <div class="wpseed-form-showcase">
            <form id="ajax-validation-form" class="wpseed-demo-form">
                <?php wp_nonce_field('wpseed_ui_ajax_validation', 'ajax_nonce'); ?>
                
                <div class="wpseed-form-row">
                    <label class="wpseed-form-label" for="username"><?php esc_html_e('Username *', 'wpseed'); ?></label>
                    <input type="text" id="username" name="username" class="wpseed-form-input" required>
                    <div id="username-feedback" class="wpseed-form-feedback" style="display:none;"></div>
                </div>
                <div class="wpseed-form-row">
                    <label class="wpseed-form-label" for="symbol-check"><?php esc_html_e('Stock Symbol *', 'wpseed'); ?></label>
                    <input type="text" id="symbol-check" name="symbol" class="wpseed-form-input" placeholder="AAPL" required>
                    <div id="symbol-feedback" class="wpseed-form-feedback" style="display:none;"></div>
                </div>
                <div class="wpseed-form-actions">
                    <button type="submit" class="tp-button tp-button-primary" id="ajax-submit-btn"><?php esc_html_e('Validate & Submit', 'wpseed'); ?></button>
                </div>
            </form>
        </div>
    </div>

    <script>
    jQuery(document).ready(function($) {
        // Username validation
        $('#username').on('blur', function() {
            var username = $(this).val();
            if (username.length < 3) return;
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'wpseed_validate_username',
                    username: username,
                    nonce: $('#ajax_nonce').val()
                },
                success: function(response) {
                    var feedback = $('#username-feedback');
                    feedback.show();
                    
                    if (response.success) {
                        feedback.removeClass('wpseed-form-feedback-error')
                               .addClass('wpseed-form-feedback-success')
                               .html('<span class="dashicons dashicons-yes-alt"></span>' + response.data.message);
                        $('#username').removeClass('wpseed-form-input-error')
                                     .addClass('wpseed-form-input-success');
                    } else {
                        feedback.removeClass('wpseed-form-feedback-success')
                               .addClass('wpseed-form-feedback-error')
                               .html('<span class="dashicons dashicons-dismiss"></span>' + response.data.message);
                        $('#username').removeClass('wpseed-form-input-success')
                                     .addClass('wpseed-form-input-error');
                    }
                }
            });
        });
        
        // Symbol validation
        $('#symbol-check').on('blur', function() {
            var symbol = $(this).val().toUpperCase();
            if (symbol.length < 1) return;
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'wpseed_validate_symbol',
                    symbol: symbol,
                    nonce: $('#ajax_nonce').val()
                },
                success: function(response) {
                    var feedback = $('#symbol-feedback');
                    feedback.show();
                    
                    if (response.success) {
                        feedback.removeClass('wpseed-form-feedback-error')
                               .addClass('wpseed-form-feedback-success')
                               .html('<span class="dashicons dashicons-yes-alt"></span>' + response.data.message);
                        $('#symbol-check').removeClass('wpseed-form-input-error')
                                         .addClass('wpseed-form-input-success');
                    } else {
                        feedback.removeClass('wpseed-form-feedback-success')
                               .addClass('wpseed-form-feedback-error')
                               .html('<span class="dashicons dashicons-dismiss"></span>' + response.data.message);
                        $('#symbol-check').removeClass('wpseed-form-input-success')
                                         .addClass('wpseed-form-input-error');
                    }
                }
            });
        });
        
        // Form submission
        $('#ajax-validation-form').on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'wpseed_submit_ajax_form',
                    username: $('#username').val(),
                    symbol: $('#symbol-check').val(),
                    nonce: $('#ajax_nonce').val()
                },
                success: function(response) {
                    if (response.success) {
                        alert('Form submitted successfully: ' + response.data.message);
                    } else {
                        alert('Error: ' + response.data.message);
                    }
                }
            });
        });
    });
    </script>
</div>
