<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly
do_action('stm_lms_redirect_user');
?>



<?php get_header();

wp_enqueue_script('vue.js');
wp_enqueue_script('vue-resource.js');
do_action('stm_lms_template_main');

if(function_exists('vc_asset_url')) {
    wp_enqueue_style('stm_lms_wpb_front_css', vc_asset_url('css/js_composer.min.css'));
}
?>

    <div class="stm-lms-wrapper stm-lms-wrapper__login">

        <div class="container">
            <div class="row">
                <div class="col-md-6">
					<?php STM_LMS_Templates::show_lms_template('account/login'); ?>
                </div>
                <div class="col-md-6">
					<?php STM_LMS_Templates::show_lms_template('account/register'); ?>
                </div>
            </div>
        </div>
    </div>

<?php get_footer(); ?>