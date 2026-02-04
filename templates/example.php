<div class="wpseed-example">
    <h3><?php echo esc_html($title); ?></h3>
    <ul>
        <?php foreach ($items as $item): ?>
            <li><?php echo esc_html($item['name']); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
