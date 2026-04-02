<?php
/**
 * Architecture View
 * 
 * @package WPSeed
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once WPSEED_PLUGIN_DIR . 'includes/classes/architecture-mapper.php';

?>

<div class="wpseed-architecture-view">
    <h2>Plugin Architecture</h2>
    <p>Visual guide to WPSeed structure for developers and AI assistants.</p>
    
    <?php WPSeed_Architecture_Mapper::render_tree(); ?>
</div>
