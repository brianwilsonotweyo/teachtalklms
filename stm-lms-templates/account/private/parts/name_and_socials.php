<?php
/**
 * @var $current_user
 */

stm_lms_register_style('user_info_top');

?>

<div class="stm_lms_user_info_top">
	<h1><?php echo esc_attr($current_user['login']); ?></h1>

	<?php STM_LMS_Templates::show_lms_template('account/private/parts/socials', array('current_user' => $current_user)); ?>

</div>