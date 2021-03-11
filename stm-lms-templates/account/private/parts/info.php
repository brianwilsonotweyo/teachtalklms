<?php
/**
 * @var $current_user
 */

?>

<div class="stm_lms_user_side">

    <?php if (!empty($current_user['avatar'])): ?>
        <div class="stm-lms-user_avatar">
            <?php echo wp_kses_post($current_user['avatar']); ?>
        </div>
    <?php endif; ?>

    <h3 class="student_name stm_lms_update_field__first_name"><?php echo esc_attr($current_user['login']); ?></h3>

    <div class="stm_lms_profile_buttons_set">
        <div class="stm_lms_profile_buttons_set__inner">

            <?php STM_LMS_Templates::show_lms_template('account/private/parts/messages_btn', array('current_user' => $current_user)); ?>

            <?php STM_LMS_Templates::show_lms_template('account/private/parts/my_certificates_btn', array('current_user' => $current_user)); ?>

            <?php STM_LMS_Templates::show_lms_template('account/private/parts/edit_profile_btn', array('current_user' => $current_user)); ?>

            <?php do_action('stm_lms_after_profile_buttons_all', $current_user); ?>

        </div>
    </div>

</div>