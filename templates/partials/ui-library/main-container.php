<?php
/**
 * WPSeed UI Library Main Container
 *
 * @package WPSeed/Admin/Views/Partials
 */

defined('ABSPATH') || exit;

$wpseed_ui_sections = array(
    'color-palette' => __('Color Palette', 'wpseed'),
    'button-components' => __('Button Components', 'wpseed'),
    'form-components' => __('Form Components', 'wpseed'),
    'notice-components' => __('Notice Components', 'wpseed'),
    'controls-actions' => __('Controls & Actions', 'wpseed'),
    'filters-search' => __('Filters & Search', 'wpseed'),
    'pagination-controls' => __('Pagination Controls', 'wpseed'),
    'progress-indicators' => __('Progress Indicators', 'wpseed'),
    'animation-showcase' => __('Animation Showcase', 'wpseed'),
    'accordion-components' => __('Accordion Components', 'wpseed'),
    'status-indicators' => __('Status Indicators', 'wpseed'),
    'data-analysis-components' => __('Data Analysis Components', 'wpseed'),
    'chart-visualization' => __('Chart Visualization', 'wpseed'),
    'modal-components' => __('Modal Components', 'wpseed'),
    'tooltips' => __('Tooltips', 'wpseed'),
    'pointers' => __('Pointers', 'wpseed')
);
?>

<div class="wrap wpseed-ui-library">
    <h1><?php esc_html_e('WPSeed UI Library', 'wpseed'); ?></h1>
    <p class="description"><?php esc_html_e('Comprehensive showcase of WPSeed UI components, styles, and interactive elements.', 'wpseed'); ?></p>
    
    <!-- Section Visibility Controls -->
    <div class="wpseed-ui-section-controls">
        <div class="wpseed-card">
            <div class="wpseed-card-header">
                <h3><?php esc_html_e('Section Visibility Controls', 'wpseed'); ?></h3>
                <div class="control-actions">
                    <button type="button" class="button button-secondary" id="show-all-sections">
                        <?php esc_html_e('Show All', 'wpseed'); ?>
                    </button>
                    <button type="button" class="button button-secondary" id="hide-all-sections">
                        <?php esc_html_e('Hide All', 'wpseed'); ?>
                    </button>
                </div>
            </div>
            <div class="wpseed-card-body">
                <p class="description">
                    <?php esc_html_e('Use these controls to show/hide specific sections while working on styles.', 'wpseed'); ?>
                </p>
                <div class="section-toggles">
                    <?php foreach ($wpseed_ui_sections as $wpseed_section_id => $wpseed_section_name) : ?>
                        <label class="section-toggle">
                            <input type="checkbox" 
                                   id="toggle-<?php echo esc_attr($wpseed_section_id); ?>" 
                                   class="section-toggle-checkbox" 
                                   data-section="<?php echo esc_attr($wpseed_section_id); ?>" 
                                   checked>
                            <span class="section-toggle-label"><?php echo esc_html($wpseed_section_name); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php
    $wpseed_sections = array(
        'color-palette.php',
        'button-components.php',
        'form-components.php',
        'notice-components.php',
        'controls-actions.php',
        'filters-search.php',
        'pagination-controls.php',
        'progress-indicators.php',
        'animation-showcase.php',
        'accordion-components.php',
        'status-indicators.php',
        'data-analysis-components.php',
        'chart-visualization.php',
        'modal-components.php',
        'tooltips.php',
        'pointers.php'
    );
    
    $wpseed_partials_dir = WPSEED_PLUGIN_DIR_PATH . 'templates/partials/ui-library/';
    
    foreach ($wpseed_sections as $wpseed_section) {
        $wpseed_section_id = str_replace('.php', '', $wpseed_section);
        $wpseed_section_path = $wpseed_partials_dir . $wpseed_section;
        
        echo '<div class="ui-library-section" data-section-id="' . esc_attr($wpseed_section_id) . '" id="section-' . esc_attr($wpseed_section_id) . '">';
        
        if (file_exists($wpseed_section_path)) {
            require_once $wpseed_section_path;
        } else {
            $wpseed_section_name = str_replace(array('-', '.php'), array(' ', ''), $wpseed_section);
            $wpseed_section_name = ucwords($wpseed_section_name);
            echo '<div class="wpseed-ui-section">';
            echo '<h3>' . esc_html($wpseed_section_name) . '</h3>';
            /* translators: %s: Section name */
            echo '<p>' . sprintf(esc_html__('Section "%s" is not yet available.', 'wpseed'), esc_html($wpseed_section_name)) . '</p>';
            echo '</div>';
        }
        
        echo '</div>';
    }
    ?>
</div>

<style>
.wpseed-ui-section-controls { margin: 20px 0; }
.wpseed-card { background: #fff; border: 1px solid #ccd0d4; box-shadow: 0 1px 1px rgba(0,0,0,.04); }
.wpseed-card-header { padding: 15px 20px; border-bottom: 1px solid #ddd; display: flex; justify-content: space-between; align-items: center; }
.wpseed-card-header h3 { margin: 0; }
.wpseed-card-body { padding: 20px; }
.section-toggles { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 10px; }
.section-toggle { display: flex; align-items: center; gap: 8px; }
.ui-library-section { margin-bottom: 30px; }
.wpseed-ui-section { padding: 20px; background: #fff; border: 1px solid #ccd0d4; }
.wpseed-ui-section h3 { margin-top: 0; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
</style>

<script>
jQuery(document).ready(function($) {
    $('#show-all-sections').on('click', function() {
        $('.section-toggle-checkbox').prop('checked', true).trigger('change');
    });
    
    $('#hide-all-sections').on('click', function() {
        $('.section-toggle-checkbox').prop('checked', false).trigger('change');
    });
    
    $('.section-toggle-checkbox').on('change', function() {
        var sectionId = $(this).data('section');
        var $section = $('#section-' + sectionId);
        
        if ($(this).is(':checked')) {
            $section.show();
        } else {
            $section.hide();
        }
    });
});
</script>
