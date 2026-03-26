<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<div class="wpseed-example">
    <h3><?php echo esc_html($title); ?></h3>
    <ul>
        <?php foreach ($items as $wpseed_item): ?>
            <li><?php echo esc_html($wpseed_item['name']); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
