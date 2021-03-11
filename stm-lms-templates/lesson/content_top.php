<?php
/**
 * @var $post_id
 * @var $item_id
 */

$section = STM_LMS_Lesson::get_lesson_info(get_post_meta($post_id, 'curriculum', true), $item_id);
?>

<div class="stm-lms-course__lesson-content__top">
	<?php if (!empty($section['text'])): ?>
		<h3><?php echo sanitize_text_field($section['text']); ?></h3>
	<?php endif; ?>
	<h1><?php echo sanitize_text_field(get_the_title($item_id)); ?></h1>
</div>