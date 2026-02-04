/**
 * WPSeed Tooltips
 */
(function($) {
    'use strict';

    $(document).ready(function() {
        // Initialize tooltips using WordPress native tooltip
        $('.wpseed-help-tip').each(function() {
            var $tip = $(this);
            var tipText = $tip.attr('data-tip');
            
            if (tipText) {
                $tip.attr('title', tipText);
            }
        });
    });

})(jQuery);
