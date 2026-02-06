jQuery(document).ready(function($) {
    $('.install-plugin').on('click', function() {
        var $btn = $(this);
        var slug = $btn.data('slug');
        var name = $btn.data('name');
        
        $btn.prop('disabled', true).text('Installing...');
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: {
                action: 'wpseed_install_plugin',
                slug: slug,
                nonce: wpseedEcosystem.nonce
            },
            success: function(response) {
                if (response.success) {
                    $btn.text('Installed!').css('background', 'green');
                    setTimeout(function() { location.reload(); }, 1000);
                } else {
                    alert('Installation failed: ' + response.data);
                    $btn.prop('disabled', false).text('Install Now');
                }
            },
            error: function() {
                alert('Installation failed. Please try again.');
                $btn.prop('disabled', false).text('Install Now');
            }
        });
    });
});
