<?php
/**
 * @var $current_user
 */

?>

<div class="stm_lms_user_side">

	<?php if (!empty($current_user['avatar'])): ?>
        <div class="stm-lms-user_avatar">
			<?php echo wp_kses_post($current_user['avatar']); ?>
        </div>
	<?php endif; ?>

    <h3 class="student_name"><?php echo esc_attr($current_user['login']); ?></h3>

	<?php STM_LMS_Templates::show_lms_template('account/public/parts/messages_btn', array('current_user' => $current_user)); ?>

</div>