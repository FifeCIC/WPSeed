/**
 * WPSeed Notification Center
 * 
 * @package WPSeed/JS
 * @version 1.2.0
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Snooze button click
        $('.snooze-btn').on('click', function() {
            var id = $(this).data('id');
            $('#snooze-' + id).slideToggle();
        });
        
        // Cancel snooze
        $('.cancel-snooze').on('click', function() {
            $(this).closest('.snooze-options').slideUp();
        });
    });
    
})(jQuery);
