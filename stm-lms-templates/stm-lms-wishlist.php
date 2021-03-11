<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>

<?php get_header();
    stm_lms_register_style('user');
    do_action('stm_lms_template_main');

    if(function_exists('vc_asset_url')) {
        wp_enqueue_style('stm_lms_wpb_front_css', vc_asset_url('css/js_composer.min.css'));
    }

    $tpl = (is_user_logged_in() ? 'account/private/parts/wishlist' : 'account/private/not_logged/wishlist');
    $current_user = STM_LMS_User::get_current_user('', false, true);
    ?>

	<div class="stm-lms-wrapper stm-lms-wrapper-wishlist">
		<div class="container">
            <?php STM_LMS_Templates::show_lms_template($tpl, array('current_user' => $current_user)); ?>
		</div>
	</div>

<?php get_footer(); ?>