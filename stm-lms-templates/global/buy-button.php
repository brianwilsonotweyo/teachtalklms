<?php
/**
 * @var $course_id
 */
stm_lms_register_script('buy-button');

$has_course = STM_LMS_User::has_course_access($course_id);
$course_price = STM_LMS_Course::get_course_price($course_id);

?>

<div class="stm-lms-buy-buttons">
    <?php if ($has_course or empty($course_price)): ?>

        <?php $user = STM_LMS_User::get_current_user();
        if (empty($user['id'])) : ?>
            <?php
            stm_lms_register_style('login');
            stm_lms_register_style('register');
            enqueue_login_script();
            enqueue_register_script();
            ?>

            <a href="#"
               class="btn btn-default"
               data-target=".stm-lms-modal-login"
               id="stm_lms_buy_button"
               data-lms-modal="login">
                <span><?php esc_html_e('Enroll course', 'masterstudy-lms-learning-management-system'); ?></span>
            </a>
        <?php else:
            $user_id = $user['id'];
            $course = STM_LMS_Helpers::simplify_db_array(stm_lms_get_user_course($user_id, $course_id, array('current_lesson_id')));
            $current_lesson = (!empty($course['current_lesson_id'])) ? $course['current_lesson_id'] : '';
            $lesson_url = STM_LMS_Course::item_url($course_id, $current_lesson); ?>
            <a href="<?php echo esc_url($lesson_url); ?>" class="btn btn-default">
                <span><?php esc_html_e('Start course', 'masterstudy-lms-learning-management-system'); ?></span>
            </a>

        <?php endif; ?>

    <?php else:
        $price = get_post_meta($course_id, 'price', true);
        $sale_price = STM_LMS_Course::get_sale_price($course_id);
        $not_in_membership = get_post_meta($course_id, 'not_membership', true);
        $btn_class = array('btn btn-default');

        if (empty($price) and !empty($sale_price)) {
            $price = $sale_price;
            $sale_price = '';
        }

        if (!empty($price) and !empty($sale_price)) {
            $tmp_price = $sale_price;
            $sale_price = $price;
            $price = $tmp_price;
        }

        if (!empty($sale_price) or !empty($price)) $btn_class[] = 'btn_big heading_font';

        if (is_user_logged_in()) {
            $attributes = array(
                'data-buy-course="' . intval($course_id) . '"',
            );
        } else {
            wp_enqueue_script('vue-resource.js');
            stm_lms_register_style('login');
            stm_lms_register_style('register');
            enqueue_login_script();
            enqueue_register_script();
            $attributes = array(
                'data-target=".stm-lms-modal-login"',
                'data-lms-modal="login"'
            );
        } ?>

        <a href="#" id="stm_lms_buy_button" <?php echo implode(' ', $attributes); ?>
           class="<?php echo esc_attr(implode(' ', $btn_class)); ?>">
            <span>
                <?php esc_html_e('Get now', 'masterstudy-lms-learning-management-system'); ?>
            </span>

            <?php if (!empty($price) or !empty($sale_price)): ?>
                <div class="btn-prices">

                    <?php if (!empty($sale_price)): ?>
                        <label class="sale_price"><?php echo STM_LMS_Helpers::display_price($sale_price); ?></label>
                    <?php endif; ?>

                    <?php if (!empty($price)): ?>
                        <label class="price"><?php echo STM_LMS_Helpers::display_price($price); ?></label>
                    <?php endif; ?>

                </div>
            <?php endif; ?>
        </a>

        <?php if (empty($not_in_membership) and STM_LMS_Subscriptions::subscription_enabled()):
            stm_lms_register_style('membership');
            $sub = STM_LMS_Subscriptions::user_subscriptions();
            if (!empty($sub->course_number)) :
                $sub->course_id = get_the_ID(); ?>
                <span class="or heading_font"><?php esc_html_e('- Or -', 'masterstudy-lms-learning-management-system'); ?></span>
                <button type="button"
                        id="stm_lms_buy_button_subscription"
                        data-lms-params='<?php echo json_encode($sub); ?>'
                        class="btn btn-default btn-outline"
                        data-target=".stm-lms-use-subscription"
                        data-lms-modal="use_subscription">
                    <span><?php esc_html_e('Enroll with Membership', 'masterstudy-lms-learning-management-system'); ?></span>
                </button>
            <?php else: ?>
                <span class="or heading_font"><?php esc_html_e('- Or -', 'masterstudy-lms-learning-management-system'); ?></span>
                <a href="<?php echo esc_url(STM_LMS_Subscriptions::level_url()); ?>"
                   id="stm_lms_buy_button_subscription"
                   class="btn btn-default btn-subscription btn-outline">
                    <span><?php esc_html_e('Enroll with Membership', 'masterstudy-lms-learning-management-system'); ?></span>
                </a>
            <?php endif; ?>
        <?php endif; ?>

    <?php endif; ?>

</div>