<?php
/**
 * @var $price
 * @var $sale_price
 */
?>

<div class="stm_lms_courses__single--price heading_font">
    <?php if (empty($price) and empty($sale_price)): ?>
        <strong><?php esc_html_e('Free', 'masterstudy-lms-learning-management-system'); ?></strong>
    <?php elseif (!empty($price) and !empty($sale_price)): ?>
        <span><?php echo STM_LMS_Helpers::display_price($price); ?></span>
        <strong><?php echo STM_LMS_Helpers::display_price($sale_price); ?></strong>
    <?php else: ?>
        <strong><?php echo STM_LMS_Helpers::display_price($price); ?></strong>
    <?php endif; ?>
</div>