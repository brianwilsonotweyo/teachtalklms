<?php
/**
 * @var $current_user
 */

$new_messages = STM_LMS_Chat::user_new_messages($current_user['id']);
?>

<div class="stm-lms-user_message_btn">
    <?php if(!empty($new_messages)): ?>
	    <i class="stm-lms-user_message_btn__counter"><?php echo intval($new_messages); ?></i>
    <?php endif; ?>
	<a href="<?php echo esc_url(STM_LMS_Chat::chat_url()); ?>" class="btn btn-default"><?php esc_html_e('My messages', 'masterstudy-lms-learning-management-system'); ?></a>
</div>