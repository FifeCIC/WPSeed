jQuery(document).ready(function($) {
    'use strict';

    // Initialize repeater fields
    $('.wpseed-repeater-container').each(function() {
        var $container = $(this);
        var $items = $container.find('.wpseed-repeater-items');
        var $template = $container.find('.wpseed-repeater-template');
        var itemIndex = $items.find('.wpseed-repeater-item').length;

        // Make items sortable
        $items.sortable({
            handle: '.wpseed-repeater-handle',
            placeholder: 'wpseed-repeater-placeholder',
            update: function() {
                updateItemNumbers($items);
            }
        });

        // Add new item
        $container.on('click', '.wpseed-repeater-add', function(e) {
            e.preventDefault();
            var template = $template.html();
            var newItem = template.replace(/\{\{INDEX\}\}/g, itemIndex);
            $items.append(newItem);
            itemIndex++;
            updateItemNumbers($items);
        });

        // Remove item
        $container.on('click', '.wpseed-repeater-remove', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to remove this item?')) {
                $(this).closest('.wpseed-repeater-item').remove();
                updateItemNumbers($items);
            }
        });

        // Toggle item content
        $container.on('click', '.wpseed-repeater-toggle', function(e) {
            e.preventDefault();
            var $item = $(this).closest('.wpseed-repeater-item');
            $item.toggleClass('collapsed');
            $(this).find('.dashicons').toggleClass('dashicons-arrow-down-alt2 dashicons-arrow-up-alt2');
        });
    });

    // Update item numbers
    function updateItemNumbers($items) {
        $items.find('.wpseed-repeater-item').each(function(index) {
            $(this).find('.wpseed-repeater-number').text(index + 1);
        });
    }
});
