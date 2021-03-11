<?php
/**
 * @var $current_user
 */

$bio = get_user_meta($current_user['id'], 'description', true);
if(!empty($bio)):

	stm_lms_register_style('user_bio');
	?>

	<div class="stm_lms_user_bio">
		<h3><?php esc_html_e('Bio', 'masterstudy-lms-learning-management-system'); ?></h3>
        <div class="stm_lms_update_field__description"><?php echo wp_kses_post($bio); ?></div>
	</div>

<?php endif; ?>