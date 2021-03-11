<?php if (!defined('ABSPATH')) exit; //Exit if accessed directly ?>

<?php get_header();
do_action('stm_lms_template_main');
?>

    <div class="stm-lms-wrapper">
        <div class="container">
            <?php STM_LMS_Templates::show_lms_template('checkout/cart'); ?>
        </div>
    </div>

<?php get_footer(); ?>