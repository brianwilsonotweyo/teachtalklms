<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>

<?php
do_action('stm_lms_before_user_header');
get_header();
do_action('stm_lms_template_main');

$user = parse_url($user_id);
$user_id = $user['path'];
$current_user = STM_LMS_User::get_current_user('', false, true);
$tpl = '';

if(!empty($current_user) and $current_user['id'] == $user_id) {
    $tpl = 'account/private/main';
}

if(empty($tpl)) STM_LMS_User::js_redirect(STM_LMS_User::login_page_url());

stm_lms_register_style('user');

if(function_exists('vc_asset_url')) {
    wp_enqueue_style('stm_lms_wpb_front_css', vc_asset_url('css/js_composer.min.css'));
}

?>

	<div class="stm-lms-wrapper">
		<div class="container">
            <?php if(!empty($tpl)) STM_LMS_Templates::show_lms_template($tpl, compact('current_user')); ?>
		</div>
	</div>

<?php get_footer(); ?>