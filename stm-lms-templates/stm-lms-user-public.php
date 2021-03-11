<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>

<?php
$user = parse_url($user_id);
$user_id = $user['path'];
$current_user = STM_LMS_User::get_current_user($user_id, false, true);

if(empty($current_user['id'])) {
	require_once(get_404_template());
	die;
};

$tpl = 'account/public/main';

get_header();
stm_lms_register_style('user');
do_action('stm_lms_template_main');

?>

	<div class="stm-lms-wrapper">
		<div class="container">
            <?php if(!empty($tpl)) STM_LMS_Templates::show_lms_template($tpl, array('current_user' => $current_user)); ?>
		</div>
	</div>

<?php get_footer(); ?>