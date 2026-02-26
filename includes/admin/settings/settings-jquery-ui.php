<?php
/**
 * jQuery UI Settings Gallery
 * 
 * Examples of all jQuery UI components supported by WordPress core
 * 
 * @package WPSeed
 */

defined( 'ABSPATH' ) || die;

function wpseed_render_jquery_ui_gallery() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e( 'jQuery UI Components Gallery', 'wpseed' ); ?></h1>
        
        <form method="post" action="">
            <?php wp_nonce_field( 'jquery_ui_demo' ); ?>
            <input type="hidden" name="wpseed_form_action" value="jquery_ui_demo">
            
            <table class="form-table">
                
                <!-- Datepicker -->
                <tr>
                    <th><?php esc_html_e( 'Datepicker', 'wpseed' ); ?></th>
                    <td>
                        <input type="text" id="wpseed_datepicker" name="datepicker" class="regular-text" value="<?php echo esc_attr( get_option( 'wpseed_datepicker', '' ) ); ?>">
                        <p class="description"><?php esc_html_e( 'Click to select a date', 'wpseed' ); ?></p>
                    </td>
                </tr>
                
                <!-- Slider -->
                <tr>
                    <th><?php esc_html_e( 'Slider', 'wpseed' ); ?></th>
                    <td>
                        <div id="wpseed_slider"></div>
                        <input type="hidden" id="wpseed_slider_value" name="slider" value="<?php echo esc_attr( get_option( 'wpseed_slider', 50 ) ); ?>">
                        <p class="description"><?php esc_html_e( 'Value: ', 'wpseed' ); ?><span id="slider_display">50</span></p>
                    </td>
                </tr>
                
                <!-- Progressbar -->
                <tr>
                    <th><?php esc_html_e( 'Progressbar', 'wpseed' ); ?></th>
                    <td>
                        <div id="wpseed_progressbar"></div>
                        <button type="button" id="progress_btn" class="button"><?php esc_html_e( 'Simulate Progress', 'wpseed' ); ?></button>
                    </td>
                </tr>
                
                <!-- Autocomplete -->
                <tr>
                    <th><?php esc_html_e( 'Autocomplete', 'wpseed' ); ?></th>
                    <td>
                        <input type="text" id="wpseed_autocomplete" name="autocomplete" class="regular-text" value="<?php echo esc_attr( get_option( 'wpseed_autocomplete', '' ) ); ?>">
                        <p class="description"><?php esc_html_e( 'Type: PHP, JavaScript, WordPress, MySQL', 'wpseed' ); ?></p>
                    </td>
                </tr>
                
                <!-- Accordion -->
                <tr>
                    <th><?php esc_html_e( 'Accordion', 'wpseed' ); ?></th>
                    <td>
                        <div id="wpseed_accordion">
                            <h3><?php esc_html_e( 'Section 1', 'wpseed' ); ?></h3>
                            <div><p><?php esc_html_e( 'Content for section 1', 'wpseed' ); ?></p></div>
                            <h3><?php esc_html_e( 'Section 2', 'wpseed' ); ?></h3>
                            <div><p><?php esc_html_e( 'Content for section 2', 'wpseed' ); ?></p></div>
                            <h3><?php esc_html_e( 'Section 3', 'wpseed' ); ?></h3>
                            <div><p><?php esc_html_e( 'Content for section 3', 'wpseed' ); ?></p></div>
                        </div>
                    </td>
                </tr>
                
                <!-- Tabs -->
                <tr>
                    <th><?php esc_html_e( 'Tabs', 'wpseed' ); ?></th>
                    <td>
                        <div id="wpseed_tabs">
                            <ul>
                                <li><a href="#tab-1"><?php esc_html_e( 'Tab 1', 'wpseed' ); ?></a></li>
                                <li><a href="#tab-2"><?php esc_html_e( 'Tab 2', 'wpseed' ); ?></a></li>
                                <li><a href="#tab-3"><?php esc_html_e( 'Tab 3', 'wpseed' ); ?></a></li>
                            </ul>
                            <div id="tab-1"><p><?php esc_html_e( 'Content for tab 1', 'wpseed' ); ?></p></div>
                            <div id="tab-2"><p><?php esc_html_e( 'Content for tab 2', 'wpseed' ); ?></p></div>
                            <div id="tab-3"><p><?php esc_html_e( 'Content for tab 3', 'wpseed' ); ?></p></div>
                        </div>
                    </td>
                </tr>
                
                <!-- Dialog (Button to trigger) -->
                <tr>
                    <th><?php esc_html_e( 'Dialog', 'wpseed' ); ?></th>
                    <td>
                        <button type="button" id="open_dialog" class="button"><?php esc_html_e( 'Open Dialog', 'wpseed' ); ?></button>
                        <div id="wpseed_dialog" title="<?php esc_attr_e( 'Example Dialog', 'wpseed' ); ?>" style="display:none;">
                            <p><?php esc_html_e( 'This is a jQuery UI dialog example.', 'wpseed' ); ?></p>
                        </div>
                    </td>
                </tr>
                
                <!-- Sortable -->
                <tr>
                    <th><?php esc_html_e( 'Sortable', 'wpseed' ); ?></th>
                    <td>
                        <ul id="wpseed_sortable" style="list-style:none; padding:0;">
                            <li class="ui-state-default" style="padding:10px; margin:5px; background:#f0f0f0; cursor:move;">Item 1</li>
                            <li class="ui-state-default" style="padding:10px; margin:5px; background:#f0f0f0; cursor:move;">Item 2</li>
                            <li class="ui-state-default" style="padding:10px; margin:5px; background:#f0f0f0; cursor:move;">Item 3</li>
                        </ul>
                        <p class="description"><?php esc_html_e( 'Drag to reorder', 'wpseed' ); ?></p>
                    </td>
                </tr>
                
                <!-- Spinner -->
                <tr>
                    <th><?php esc_html_e( 'Spinner', 'wpseed' ); ?></th>
                    <td>
                        <input type="text" id="wpseed_spinner" name="spinner" value="<?php echo esc_attr( get_option( 'wpseed_spinner', 0 ) ); ?>">
                        <p class="description"><?php esc_html_e( 'Use arrows or type a number', 'wpseed' ); ?></p>
                    </td>
                </tr>
                
            </table>
            
            <?php submit_button(); ?>
        </form>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        // Datepicker
        $('#wpseed_datepicker').datepicker({ dateFormat: 'yy-mm-dd' });
        
        // Slider
        $('#wpseed_slider').slider({
            min: 0,
            max: 100,
            value: <?php echo (int) get_option( 'wpseed_slider', 50 ); ?>,
            slide: function(event, ui) {
                $('#slider_display').text(ui.value);
                $('#wpseed_slider_value').val(ui.value);
            }
        });
        
        // Progressbar
        $('#wpseed_progressbar').progressbar({ value: 0 });
        $('#progress_btn').click(function() {
            var val = 0;
            var interval = setInterval(function() {
                val += 10;
                $('#wpseed_progressbar').progressbar('value', val);
                if (val >= 100) clearInterval(interval);
            }, 200);
        });
        
        // Autocomplete
        $('#wpseed_autocomplete').autocomplete({
            source: ['PHP', 'JavaScript', 'WordPress', 'MySQL', 'Python', 'Ruby']
        });
        
        // Accordion
        $('#wpseed_accordion').accordion({ collapsible: true });
        
        // Tabs
        $('#wpseed_tabs').tabs();
        
        // Dialog
        $('#wpseed_dialog').dialog({ autoOpen: false, modal: true });
        $('#open_dialog').click(function() {
            $('#wpseed_dialog').dialog('open');
        });
        
        // Sortable
        $('#wpseed_sortable').sortable();
        
        // Spinner
        $('#wpseed_spinner').spinner({ min: 0, max: 100 });
    });
    </script>
    <?php
}
