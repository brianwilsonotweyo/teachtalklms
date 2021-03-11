<?php
/**
 * @var $current_user
 */

wp_enqueue_script('vue-resource.js');
stm_lms_register_style('chat');
stm_lms_register_script('chat');
?>

<div id="stm_lms_chat">

    <a href="<?php echo esc_url(STM_LMS_User::user_page_url()); ?>" class="back-to-account">
        <i class="lnricons-arrow-left"></i>
        <?php esc_html_e('Back to account', 'masterstudy-lms-learning-management-system-pro'); ?>
    </a>

    <h1><?php esc_html_e('My messages', 'masterstudy-lms-learning-management-system'); ?></h1>
    <div class="row">

        <div class="col-md-4">
			<?php STM_LMS_Templates::show_lms_template('account/private/chat/contacts', array('current_user' => $current_user)); ?>
        </div>

        <div class="col-md-8">
			<?php STM_LMS_Templates::show_lms_template('account/private/chat/chat', array('current_user' => $current_user)); ?>
        </div>

    </div>
</div>