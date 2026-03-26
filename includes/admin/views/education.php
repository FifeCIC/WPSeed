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

<style>
.wpseed-learning-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.lesson-card {
    background: #fff;
    border: 1px solid #ccd0d4;
    border-radius: 4px;
    padding: 20px;
}

.lesson-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    border-bottom: 1px solid #eee;
    padding-bottom: 10px;
}

.lesson-header h3 {
    margin: 0;
    font-size: 16px;
}

.lesson-duration {
    background: #f0f0f1;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 12px;
}

.lesson-content {
    margin-bottom: 15px;
    color: #50575e;
}

.lesson-footer {
    margin-top: 15px;
    padding-top: 10px;
    border-top: 1px solid #eee;
}

.lesson-type {
    background: #2271b1;
    color: #fff;
    padding: 4px 8px;
    border-radius: 3px;
    font-size: 11px;
    text-transform: uppercase;
}
</style>
