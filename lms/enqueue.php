<?php

if (!defined('ABSPATH')) exit; //Exit if accessed directly


function stm_lms_wp_head()
{
    ?>
    <script type="text/javascript">
        var stm_lms_ajaxurl = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
    </script>

    <style>
        .vue_is_disabled {
            display: none;
        }
    </style>
    <?php
}

add_action('wp_head', 'stm_lms_wp_head');
add_action('admin_head', 'stm_lms_wp_head');


function stm_lms_enqueue_ss()
{
    $v = time();
    $assets = STM_LMS_URL . 'assets';
    $base = STM_LMS_URL . '/post_type/metaboxes/assets/';

    wp_enqueue_style('linear', $assets . '/linearicons/linear.css', array(), $v);
    wp_enqueue_style('linear-icons', $base . 'css/linear-icons.css', NULL, $v);
    wp_enqueue_style('stm_lms_icons', $assets . '/icons/style.css', NULL, $v);
    wp_enqueue_style('font-awesome-min', $assets . '/vendors/font-awesome.min.css', NULL, $v, 'all');
    wp_enqueue_style('font-icomoon', $assets . '/vendors/icomoon.fonts.css', NULL, $v, 'all');
    if (apply_filters('stm_lms_enqueue_bootstrap', true)) {
        wp_enqueue_style('boostrap', $assets . '/vendors/bootstrap.min.css', NULL, $v, 'all');
    }

    wp_enqueue_script('jquery');

    wp_enqueue_script('stripe.js', 'https://js.stripe.com/v3/', array(), false, false);
    wp_register_script('vue.js', $base . 'js/vue.min.js', array('jquery'), $v);
    wp_register_script('vue-resource.js', $base . 'js/vue-resource.min.js', array('vue.js'), $v);
    wp_register_script('vue2-editor.js', $base . 'js/vue2-editor.min.js', array('vue.js'), $v);

    $r_enabled = STM_LMS_Helpers::g_recaptcha_enabled();
    if ($r_enabled):
        $recaptcha = STM_LMS_Helpers::g_recaptcha_keys();

        wp_register_script(
            'stm_grecaptcha',
            'https://www.google.com/recaptcha/api.js?render=' . $recaptcha['public'], array('jquery'),
            $v,
            true
        );
    endif;

    wp_register_script('jquery.cookie', $assets . '/vendors/jquery.cookie.js', array('jquery'), $v, TRUE);
    wp_register_script('sticky-sidebar', $assets . '/vendors/sticky-sidebar.min.js', array('jquery'), $v, TRUE);
    if (apply_filters('stm_lms_enqueue_bootstrap', true)) {
        wp_enqueue_script('bootstrap', $assets . '/vendors/bootstrap.min.js', array('jquery'), $v, TRUE);
    }
    wp_enqueue_script('vue2-datepicker', $base . 'js/vue2-datepicker.min.js', array('vue.js'), $v);

    if (stm_lms_has_custom_colors()) {
        wp_enqueue_style('masterstudy-lms-learning-management-system', stm_lms_custom_styles_url(true) . '/stm_lms.css', array(), stm_lms_custom_styles_v());
    } else {
        wp_enqueue_style('masterstudy-lms-learning-management-system', $assets . '/css/stm_lms.css', array(), time());
    }

    if (current_user_can('edit_posts')) {
        wp_enqueue_style('stm_lms_logged_in', $assets . '/css/stm_lms_logged_in.css', NULL, $v, 'all');
    }


    stm_lms_register_script('lms');
    wp_localize_script('stm-lms-lms', 'stm_lms_vars', array(
        'symbol' => STM_LMS_Options::get_option('currency_symbol', '$'),
        'position' => STM_LMS_Options::get_option('currency_position', 'left'),
        'currency_thousands' => STM_LMS_Options::get_option('currency_thousands', ',')
    ));

    if (STM_LMS_Subscriptions::subscription_enabled()) stm_lms_register_style('pmpro');
}

add_action('wp_enqueue_scripts', 'stm_lms_enqueue_ss');

add_action('admin_head', 'stm_lms_nonces');
add_action('wp_head', 'stm_lms_nonces');
function stm_lms_nonces()
{

    $nonces = array(
        'load_modal',
        'load_content',
        'get_image_url',
        'start_quiz',
        'user_answers',
        'get_order_info',
        'user_orders',
        'stm_lms_get_instructor_courses',
        'stm_lms_add_comment',
        'stm_lms_get_comments',
        'stm_lms_login',
        'stm_lms_register',
        'stm_lms_become_instructor',
        'stm_lms_enterprise',
        'stm_lms_get_user_courses',
        'stm_lms_get_user_quizzes',
        'stm_lms_wishlist',
        'stm_lms_save_user_info',
        'stm_lms_lost_password',
        'stm_lms_change_avatar',
        'stm_lms_delete_avatar',
        'stm_lms_complete_lesson',
        'stm_lms_use_membership',
        'stm_lms_change_featured',
        'stm_lms_delete_course_subscription',
        'stm_lms_get_reviews',
        'stm_lms_add_review',
        'stm_lms_add_to_cart',
        'stm_lms_delete_from_cart',
        'stm_lms_purchase',
        'stm_lms_send_message',
        'stm_lms_get_user_conversations',
        'stm_lms_get_user_messages',
        'stm_curriculum',
        'stm_manage_posts',
        'stm_lms_change_post_status',
        'stm_curriculum_create_item',
        'stm_curriculum_get_item',
        'stm_save_questions',
        'stm_save_title',
        'stm_save_settings',
        'stm_lms_tables_update',
        'stm_lms_get_enterprise_groups',
        'stm_lms_get_enterprise_group',
        'stm_lms_add_enterprise_group',
        'stm_lms_delete_enterprise_group',
        'stm_lms_add_to_cart_enterprise',
        'stm_lms_get_user_ent_courses',
        'stm_lms_delete_user_ent_courses',
        'stm_lms_add_user_ent_courses',
        'stm_lms_change_ent_group_admin',
        'stm_lms_delete_user_from_group',
        'stm_lms_import_groups',
    );

    $nonces_list = array();

    foreach ($nonces as $nonce_name) {
        $nonces_list[$nonce_name] = wp_create_nonce($nonce_name);
    }

    ?>
    <script>
        var stm_lms_nonces = <?php echo json_encode($nonces_list); ?>;
    </script>
    <?php
}