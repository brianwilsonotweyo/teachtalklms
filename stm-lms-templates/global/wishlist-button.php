<?php
$icon = (empty($icon)) ? 'lnr lnr-bookmark' : esc_attr($icon);
$class = (empty($class)) ? '' : esc_attr($class);
?>

<div class="stm_lms_wishlist_button">
    <a href="<?php echo esc_url(STM_LMS_User::wishlist_url()); ?>" data-text="<?php esc_html_e('Favorites', 'masterstudy-lms-learning-management-system'); ?>">
        <i class="<?php echo esc_attr($icon); ?> <?php echo esc_attr($class); ?>"></i>
    </a>
</div>