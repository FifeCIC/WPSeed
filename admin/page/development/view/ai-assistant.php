<?php
/**
 * WPSeed AI Assistant View
 *
 * @package WPSeed/Admin/Views
 * @version 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class WPSeed_Admin_Development_AI_Assistant {
    
    public static function output() {
        wp_enqueue_script('jquery');
        ?>
        <div class="wpseed-ai-assistant">
            <h2><?php _e('AI Assistant', 'wpseed'); ?></h2>
            
            <div class="wpseed-ai-prompt-section">
                <h3><?php _e('Ask AI', 'wpseed'); ?></h3>
                <textarea id="wpseed-ai-prompt" rows="4" style="width:100%;" placeholder="<?php esc_attr_e('Enter your question or request...', 'wpseed'); ?>"></textarea>
                <button id="wpseed-ai-submit" class="button button-primary" style="margin-top:10px;"><?php _e('Submit', 'wpseed'); ?></button>
                <div id="wpseed-ai-response" style="margin-top:20px; padding:15px; background:#f9f9f9; border-left:4px solid #0073aa; display:none;"></div>
            </div>
            
            <div class="wpseed-ai-suggestions" style="margin-top:30px;">
                <h3><?php _e('Proactive Suggestions', 'wpseed'); ?></h3>
                <div id="wpseed-ai-suggestions-list"></div>
            </div>
        </div>
        
        <script>
        jQuery(document).ready(function($) {
            $('#wpseed-ai-submit').on('click', function() {
                var prompt = $('#wpseed-ai-prompt').val();
                if (!prompt) return;
                
                $('#wpseed-ai-response').html('Processing...').show();
                
                $.post(ajaxurl, {
                    action: 'wpseed_ai_request',
                    nonce: '<?php echo wp_create_nonce('wpseed_ai_nonce'); ?>',
                    prompt: prompt
                }, function(response) {
                    if (response.success) {
                        $('#wpseed-ai-response').html('<strong>Response:</strong><br>' + response.response);
                    } else {
                        $('#wpseed-ai-response').html('<strong>Error:</strong> ' + response.message);
                    }
                });
            });
            
            // Load suggestions
            $.post(ajaxurl, {
                action: 'wpseed_ai_suggestions',
                nonce: '<?php echo wp_create_nonce('wpseed_ai_nonce'); ?>'
            }, function(response) {
                if (response.success && response.suggestions) {
                    var html = '<ul>';
                    response.suggestions.forEach(function(s) {
                        html += '<li><strong>' + s.title + ':</strong> ' + s.description + '</li>';
                    });
                    html += '</ul>';
                    $('#wpseed-ai-suggestions-list').html(html);
                }
            });
        });
        </script>
        <?php
    }
}
