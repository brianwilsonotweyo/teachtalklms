<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>

<?php get_header();

$current_user = STM_LMS_User::get_current_user('', false, true);

$tpl = 'account/private/chat/main';

stm_lms_register_style('user');
do_action('stm_lms_template_main');

if(!is_user_logged_in()) STM_LMS_User::js_redirect(STM_LMS_User::login_page_url());

?>
	<div class="stm-lms-wrapper">
		<div class="container">
            <?php if(!empty($tpl)) STM_LMS_Templates::show_lms_template($tpl, array('current_user' => $current_user)); ?>
		</div>
	</div>

<?php get_footer(); ?>