<?php
/**
 * Education/Learning Center View
 *
 * @package WPSeed/Admin/Views
 */

if (!defined('ABSPATH')) exit;
?>

<div class="wrap wpseed-learning-center">
    <h1><?php esc_html_e('WPSeed Learning Center', 'wpseed'); ?></h1>
    
    <div class="wpseed-learning-grid">
        <?php if (!empty($lessons)): ?>
            <?php foreach ($lessons as $lesson): ?>
                <div class="lesson-card">
                    <div class="lesson-header">
                        <h3><?php echo esc_html($lesson->title); ?></h3>
                        <?php if ($lesson->duration): ?>
                            <span class="lesson-duration"><?php echo esc_html($lesson->duration); ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="lesson-content">
                        <?php echo wp_kses_post(wpautop($lesson->content)); ?>
                    </div>
                    
                    <?php if ($lesson->video_url): ?>
                        <div class="lesson-video">
                            <a href="<?php echo esc_url($lesson->video_url); ?>" target="_blank" class="button">
                                <?php esc_html_e('Watch Video', 'wpseed'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    
                    <div class="lesson-footer">
                        <span class="lesson-type"><?php echo esc_html(ucfirst($lesson->lesson_type)); ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p><?php esc_html_e('No lessons available yet.', 'wpseed'); ?></p>
        <?php endif; ?>
    </div>
</div>
