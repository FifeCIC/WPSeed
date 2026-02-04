jQuery(document).ready(function($) {
    var deactivateLink = '';
    
    // Intercept deactivate link click
    $('tr[data-slug="' + wpseedUninstall.plugin_slug.split('/')[0] + '"] .deactivate a').on('click', function(e) {
        e.preventDefault();
        deactivateLink = $(this).attr('href');
        $('#wpseed-uninstall-feedback-modal').fadeIn(200);
    });
    
    // Close modal
    $('.wpseed-modal-close, .wpseed-modal-overlay').on('click', function() {
        $('#wpseed-uninstall-feedback-modal').fadeOut(200);
    });
    
    // Show details textarea for certain reasons
    $('input[name="reason"]').on('change', function() {
        var value = $(this).val();
        if (value === 'missing_features' || value === 'not_working' || value === 'other') {
            $('.wpseed-details').slideDown(200);
        } else {
            $('.wpseed-details').slideUp(200);
        }
    });
    
    // Skip and deactivate
    $('.wpseed-skip').on('click', function() {
        window.location.href = deactivateLink;
    });
    
    // Submit feedback and deactivate
    $('.wpseed-submit').on('click', function() {
        var $btn = $(this);
        var reason = $('input[name="reason"]:checked').val();
        
        if (!reason) {
            alert('Please select a reason');
            return;
        }
        
        $btn.prop('disabled', true).text('Submitting...');
        
        $.ajax({
            url: wpseedUninstall.ajaxurl,
            type: 'POST',
            data: {
                action: 'wpseed_uninstall_feedback',
                nonce: wpseedUninstall.nonce,
                reason: reason,
                details: $('textarea[name="details"]').val(),
                email: $('input[name="email"]').val()
            },
            success: function() {
                window.location.href = deactivateLink;
            },
            error: function() {
                window.location.href = deactivateLink;
            }
        });
    });
});
