<?php
/**
 * @var $current_user
 */

?>


<a href="<?php echo esc_url(STM_LMS_User::user_page_url()); ?>" class="back-to-account">
    <i class="lnricons-arrow-left"></i>
    <?php esc_html_e('Back to account', 'masterstudy-lms-learning-management-system-pro'); ?>
</a>

<div class="row">


	<div class="col-md-4 col-sm-4">

		<?php STM_LMS_Templates::show_lms_template('account/private/parts/info', compact('current_user')); ?>

	</div>

	<div class="col-md-8 col-sm-8">

		<div class="stm_lms_private_information" data-container-open=".stm_lms_private_information">

			<h2><?php esc_html_e('My Certificates', 'masterstudy-lms-learning-management-system'); ?></h2>

            <?php STM_LMS_Templates::show_lms_template('account/private/parts/certificate-list', array('current_user' => $current_user)); ?>

		</div>

		<?php STM_LMS_Templates::show_lms_template('account/private/parts/edit_account', array('current_user' => $current_user)); ?>

	</div>

</div>