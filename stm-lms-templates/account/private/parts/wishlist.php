<?php
/**
 * @var $current_user
 */

$wishlist = STM_LMS_User::get_wishlist($current_user['id']);
?>

<div class="row">

    <div class="col-md-4 col-sm-4">

        <?php STM_LMS_Templates::show_lms_template('account/private/parts/info', compact('current_user')); ?>

    </div>

    <div class="col-md-8 col-sm-8">

        <div class="stm_lms_private_information" data-container-open=".stm_lms_private_information">

            <h2><?php esc_html_e('My Wishlist', 'masterstudy-lms-learning-management-system'); ?></h2>
            <?php if (!empty($wishlist)): ?>
                <?php STM_LMS_Templates::show_lms_template('courses/grid', array(
                    'args' => array(
                        'post__in' => $wishlist,
                        'class' => 'archive_grid'
                    )
                )); ?>

            <?php else: ?>
                <h4><?php esc_html_e('Wishlist is empty', 'masterstudy-lms-learning-management-system'); ?></h4>
            <?php endif; ?>

        </div>

        <?php STM_LMS_Templates::show_lms_template('account/private/parts/edit_account', array('current_user' => $current_user)); ?>

    </div>

</div>