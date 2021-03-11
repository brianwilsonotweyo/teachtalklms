<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>

<?php
/**
 * @var $current_user
 */
?>

<div class="row">

	<div class="col-md-3 col-sm-12">

		<?php STM_LMS_Templates::show_lms_template('account/public/parts/info', compact('current_user')); ?>

	</div>

	<div class="col-md-9 col-sm-12">

		<?php STM_LMS_Templates::show_lms_template('account/private/parts/name_and_socials', compact('current_user')); ?>

		<?php STM_LMS_Templates::show_lms_template('account/private/parts/bio', compact('current_user')); ?>

	</div>

</div>