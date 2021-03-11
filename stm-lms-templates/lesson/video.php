<?php
/**
 *
 * @var $id
 */

$lesson_video_poster = get_post_meta($id,'lesson_video_poster', true);
$lesson_video_url = get_post_meta($id,'lesson_video_url', true);

if(!empty($lesson_video_poster) and !empty($lesson_video_url)): ?>
    <div class="stm_lms_video" style="background: url('<?php echo esc_url(stm_get_image_url($lesson_video_poster, 'full')); ?>');">
        <i class="stm_lms_play"></i>
        <iframe src="<?php echo esc_url($lesson_video_url); ?>"></iframe>
    </div>
<?php endif;