<?php
/**
 * @var $name
 * @var $course_number
 * @var $used_quotas
 * @var $course_id
 * @var $quotas_left
 */

?>

<div class="stm_lms_use_membership_popup">
    <h2><?php printf(esc_html__('Your current membership is "%s"', 'masterstudy-lms-learning-management-system'), $name); ?></h2>
    <p><?php printf(__('Membership quotas left: <strong>%s</strong>', 'masterstudy-lms-learning-management-system'), $quotas_left); ?></p>

	<?php if ($quotas_left): ?>
        <a href="#"
           class="btn btn-default"
           data-lms-usemembership=""
           data-lms-course="<?php echo intval($course_id); ?>">
            <span>
                <?php esc_html_e('Enroll with membership', 'masterstudy-lms-learning-management-system'); ?>
            </span>
        </a>
    <?php else: ?>
        <a href="<?php echo esc_url(STM_LMS_Subscriptions::level_url()); ?>"
           class="btn btn-default">
            <span>
                <?php esc_html_e('Buy Membership', 'masterstudy-lms-learning-management-system'); ?>
            </span>
        </a>
	<?php endif; ?>
</div>