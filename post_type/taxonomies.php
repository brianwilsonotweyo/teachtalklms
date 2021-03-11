<?php

if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly


class STM_Lms_Taxonomies
{
	function __construct()
	{
		add_action('init', array($this, 'taxonomies_init'), -1);
	}

	function taxonomies_init()
	{
		$taxonomies = $this->taxonomies();
		foreach ($taxonomies as $taxonomy => $taxonomy_args) {
			register_taxonomy($taxonomy, $taxonomy_args['post_type'], $taxonomy_args['args']);
		}
	}

	function taxonomies()
	{
		$rewrite_slug = STM_LMS_Options::get_option('courses_categories_slug', 'stm_lms_course_category');
		return apply_filters('stm_lms_taxonomies', array(
			'stm_lms_course_taxonomy' => array(
				'post_type' => 'stm-courses',
				'args' => array(
					'hierarchical'      => true,
					'labels'            => array(
						'name'              => _x('Courses category', 'taxonomy general name', 'masterstudy-lms-learning-management-system'),
						'singular_name'     => _x('Course category', 'taxonomy singular name', 'masterstudy-lms-learning-management-system'),
						'search_items'      => __('Search Courses category', 'masterstudy-lms-learning-management-system'),
						'all_items'         => __('All Courses category', 'masterstudy-lms-learning-management-system'),
						'parent_item'       => __('Parent Course category', 'masterstudy-lms-learning-management-system'),
						'parent_item_colon' => __('Parent Course category:', 'masterstudy-lms-learning-management-system'),
						'edit_item'         => __('Edit Course category', 'masterstudy-lms-learning-management-system'),
						'update_item'       => __('Update Course category', 'masterstudy-lms-learning-management-system'),
						'add_new_item'      => __('Add New Course category', 'masterstudy-lms-learning-management-system'),
						'new_item_name'     => __('New Course category Name', 'masterstudy-lms-learning-management-system'),
						'menu_name'         => __('Course category', 'masterstudy-lms-learning-management-system'),
					),
					'show_ui'           => true,
					'show_admin_column' => true,
					'query_var'         => true,
					'rewrite'           => array('slug' => $rewrite_slug),
				)
			)
		));
	}

}

new STM_Lms_Taxonomies();