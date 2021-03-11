<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>

<?php stm_lms_register_style('course'); ?>

<div class="row">

    <div class="col-md-9">

        <h1 class="stm_lms_course__title"><?php the_title(); ?></h1>

		<?php STM_LMS_Templates::show_lms_template('course/parts/panel_info'); ?>

		<?php STM_LMS_Templates::show_lms_template('course/parts/tabs'); ?>

    </div>

    <div class="col-md-3">

		<?php STM_LMS_Templates::show_lms_template('course/sidebar'); ?>

    </div>

</div>