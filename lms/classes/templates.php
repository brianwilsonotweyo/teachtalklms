<?php

STM_LMS_Templates::load_templates();

class STM_LMS_Templates
{

	private static $instance;

	public static function load_templates()
	{
		add_filter('the_content', array(self::get_instance(), 'courses_archive_content'), 100);
		add_filter('the_content', array(self::get_instance(), 'instructors_archive_content'), 100);
		add_filter('single_template', array(self::get_instance(), 'lms_template'));
		add_action('stm-lms-content-stm-courses', array(self::get_instance(), 'single_course'), 100);

		add_filter('taxonomy_template', array(self::get_instance(), 'taxonomy_archive_content'), 100, 1);

	}

	public static function taxonomy_archive_content($template)
	{
		if (is_admin()) return $template;
		$taxonomy = get_query_var('taxonomy');
		if ($taxonomy === 'stm_lms_course_taxonomy') {
			$template = STM_LMS_Templates::locate_template('stm-lms-taxonomy-archive');

		}
		return $template;
	}

	public static function courses_archive_content($content)
	{


		$courses_page = STM_LMS_Options::courses_page();

		//Do nothing if no courses page
		if (empty($courses_page) or !is_page($courses_page)) {
			return $content;
		}

		if (is_page($courses_page)) {
			remove_filter('the_content', array(self::get_instance(), 'courses_archive_content'), 100);

			$args = array(
				'image_d'        => 'img-480-380',
				'per_row'        => STM_LMS_Options::get_option('courses_per_row', 4),
				'posts_per_page' => STM_LMS_Options::get_option('courses_per_page', get_option('posts_per_page')),
				'class'          => 'archive_grid'
			);

			$courses = '<div class="stm_lms_courses_wrapper">';
            $courses .= self::load_lms_template(
                'courses/filters',
                array('args' => $args)
            );

			$courses .= '<div class="stm_lms_courses stm_lms_courses__archive">';
			$courses .= self::load_lms_template(
				'courses/' . STM_LMS_Options::get_option('course_view', 'grid'),
				array('args' => $args)
			);
			$courses .= self::load_lms_template(
				'courses/load_more',
				array('args' => $args));
			$courses .= '</div>';

			$courses .= '</div>' /*Wrapper*/;

			add_filter('the_content', array(self::get_instance(), 'courses_archive_content'), 100);
			return $content . $courses;
		}

		return $content;
	}

	public static function instructors_archive_content($content)
	{


		$instructors_page = STM_LMS_Options::instructors_page();

		//Do nothing if no courses page
		if (empty($instructors_page) or !is_page($instructors_page)) {
			return $content;
		}

		if (is_page($instructors_page)) {
			remove_filter('the_content', array(self::get_instance(), 'instructors_archive_content'), 100);

			$instructors = '<div class="stm_lms_instructors_grid_wrapper">';
			$instructors .= '<h1 class="text-center">' . esc_html__('Instructors', 'masterstudy-lms-learning-management-system') . '</h1>';
			$instructors .= '<div class="stm_lms_courses stm_lms_courses__archive">';
			$instructors .= self::load_lms_template(
				'instructors/grid'
			);
			$instructors .= '</div>';
			$instructors .= '</div>';

			add_filter('the_content', array(self::get_instance(), 'instructors_archive_content'), 100);
			return $content . $instructors;
		}
	}

	public static function single_course()
	{
		echo self::load_lms_template('course/single');
	}

	public static function lms_template($template)
	{

		global $post;
		$post_types = array(
			'stm-courses'
		);
		if (in_array($post->post_type, $post_types)) return self::locate_template('masterstudy-lms-learning-management-system');

		return $template;
	}

	public static function locate_template($template_name, $stm_lms_vars = array())
	{
		$template_name = '/stm-lms-templates/' . $template_name . '.php';
		$template_name = apply_filters('stm_lms_template_name', $template_name, $stm_lms_vars);
		$lms_template = apply_filters('stm_lms_template_file', STM_LMS_PATH, $template_name) . $template_name;

		return (locate_template($template_name)) ? locate_template($template_name) : $lms_template;

	}

	public static function load_lms_template($template_name, $stm_lms_vars = array())
	{
		ob_start();
		extract($stm_lms_vars);
		include(self::locate_template($template_name, $stm_lms_vars));
		return apply_filters("stm_lms_{$template_name}", ob_get_clean(), $stm_lms_vars);
	}

	public static function show_lms_template($template_name, $stm_lms_vars = array())
	{
		echo self::load_lms_template($template_name, $stm_lms_vars);
	}


	public static function get_instance()
	{

		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}

}

;