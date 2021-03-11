<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>

<?php $term = get_queried_object();
stm_lms_register_style('taxonomy_archive');

$args = array(
	'per_row'        => STM_LMS_Options::get_option('courses_per_row', 4),
	'posts_per_page' => STM_LMS_Options::get_option('courses_per_page', get_option('posts_per_page')),
	'tax_query'      => array(
		array(
			'taxonomy' => 'stm_lms_course_taxonomy',
			'field'    => 'term_id',
			'terms'    => $term->term_id,
		)
	),
	'class'          => 'archive_grid'
);
?>

<h2><?php echo sanitize_text_field($term->name); ?></h2>

<div class="stm_lms_courses">
	<?php STM_LMS_Templates::show_lms_template('courses/' . STM_LMS_Options::get_option('course_view', 'grid'),
		array(
			'args' => $args
		)
	); ?>

	<?php STM_LMS_Templates::show_lms_template('courses/load_more',
		array(
			'args' => $args
		)
	); ?>

</div>
