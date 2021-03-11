<?php
STM_LMS_Course::init();

class STM_LMS_Course
{
	public static function init()
	{
		add_action('rest_api_init', 'STM_LMS_Course::courses_search_endpoint');

		add_action('stm_lms_archive_card_price', 'STM_LMS_Course::archive_card_price');

		add_action('stm_lms_lesson_started', 'STM_LMS_Course::lesson_started', 10, 2);

	}

	public static function archive_card_price($args) {
        STM_LMS_Templates::show_lms_template('global/price', $args);
    }

	public static function courses_in_search($query) {
		if ( $query->is_search && $query->is_main_query() ) {
			$query->set( 'post_type', array( 'post', 'page', 'stm-courses' ) );
		}

		return $query;
	}

	public static function courses_search_endpoint()
	{
		register_rest_route('stm-lms/v1', '/courses', array(
			'callback' => 'STM_LMS_Course::courses_search',
		));
	}

	public static function courses_search()
	{
		$results = array();

		$search = (!empty($_GET['search'])) ? sanitize_text_field($_GET['search']) : '';

		$args = array(
			'post_type' => apply_filters('stm_lms_courses_search_endpoint_post_types', 'stm-courses'),
			'post_status' => 'publish',
			'posts_per_page' => 5,
			's' => $search
		);

		$q = new WP_Query($args);
		if($q->have_posts()) {
			while($q->have_posts()) {
				$q->the_post();
				$results[] = array(
					'value' => html_entity_decode(get_the_title()),
					'url' => get_the_permalink(),
				);
			}
		}

		return $results;
	}

	public static function add_user_course($course_id, $user_id, $current_lesson_id, $progress = 0, $is_translate = false, $enterprise = '')
	{
		if (empty($user_id)) {
			$current_user = STM_LMS_User::get_current_user();
			if (empty($current_user['id'])) die;
			$user_id = $current_user['id'];
		}

		if (empty($user_id)) die;

		$user_course = stm_lms_get_user_course($user_id, $course_id, array(), $enterprise);

		if (empty($user_course)) {
			$course = compact('user_id', 'course_id', 'current_lesson_id');
			$course['status'] = 'enrolled';
			$course['progress_percent'] = $progress;
			$course['start_time'] = time();

			if(function_exists('wpml_get_language_information')) {
                $post_language_information = wpml_get_language_information(null, $course_id);
                $course['lng_code'] = $post_language_information['locale'];
            } else {
			    $course['lng_code'] = get_locale();
            }

			$course['enterprise_id'] = $enterprise;

            stm_lms_add_user_course($course);

            if(!$is_translate) self::add_wpmls_binded_courses($course_id, $user_id, $current_lesson_id, $progress);

            /*User was Added course*/
            do_action('add_user_course', $user_id, $course_id);

		}
	}

	public static function add_wpmls_binded_courses($course_id, $user_id, $current_lesson_id, $progress) {
	    if(defined('ICL_SITEPRESS_VERSION')) {
            global $sitepress;
            $trid = $sitepress->get_element_trid($course_id);
            $translations = $sitepress->get_element_translations($trid);
            if(!empty($translations)) {
                foreach ($translations as $translation) {
                    STM_LMS_Course::add_user_course($translation->element_id, $user_id, $current_lesson_id, $progress, true);
                }
            }
        }
    }

	public static function update_course_progress($user_id, $course_id)
	{
		$curriculum = explode(',', get_post_meta($course_id, 'curriculum', true));
		$total_items = count(STM_LMS_Helpers::only_array_numbers($curriculum));
		$passed_items = 0;

		$passed_quizzes = stm_lms_get_user_course_quizzes($user_id, $course_id, array('user_quiz_id'));
		$passed_items += count($passed_quizzes);

		$passed_lessons = stm_lms_get_user_course_lessons($user_id, $course_id, array('user_lesson_id'));
		$passed_items += count($passed_lessons);

		$progress = (100 * $passed_items) / $total_items;

		$user_course = stm_lms_get_user_course($user_id, $course_id, array('user_course_id'));

		/*Add course if not exist*/
		if (empty($user_course)) {
			STM_LMS_Course::add_user_course($course_id, $user_id, 0, $progress);
			$user_course = stm_lms_get_user_course($user_id, $course_id, array('user_course_id'));
		}

		$user_course = STM_LMS_Helpers::simplify_db_array($user_course);
		$user_course_id = $user_course['user_course_id'];

		stm_lms_update_user_course_progress($user_course_id, $progress);

	}

	public static function courses_page_url()
	{
		return home_url('/') . STM_LMS_WP_Router::route_urls('courses');
	}

	public static function certificates_page_url($course_id = '')
	{
		if(!empty($course_id)) return home_url('/') . STM_LMS_WP_Router::route_urls('certificates') . '/' . $course_id;
		return home_url('/') . STM_LMS_WP_Router::route_urls('certificates');
	}

	public static function add_student($course_id)
	{
		$current_students = get_post_meta($course_id, 'current_students', true);
		if (empty($current_students)) $current_students = 0;
		$current_students++;
		update_post_meta($course_id, 'current_students', $current_students);
	}

	public static function remove_student($course_id)
	{
		$current_students = get_post_meta($course_id, 'current_students', true);
		if (empty($current_students)) $current_students = 0;
		if ($current_students > 0) $current_students--;
		update_post_meta($course_id, 'current_students', $current_students);
	}

	public static function item_url($course_id, $item_id)
	{
		if (empty($item_id)) {
			$curriculum = STM_LMS_Helpers::only_array_numbers(explode(',', get_post_meta($course_id, 'curriculum', true)));
			if(!empty($curriculum)) {
				$item_id = $curriculum[0];
			}
		}
		return esc_url(get_the_permalink($course_id) . $course_id . '-' . $item_id);
	}

	public static function curriculum_info($meta)
	{
		$r = array(
			'sections' => 0,
			'lessons'  => 0,
			'quizzes'  => 0
		);
		$curriculum = explode(',', $meta);
		if (!empty($curriculum)) {
			foreach ($curriculum as $item) {
				if (is_numeric($item)) {
					if (get_post_type($item) == 'stm-lessons') {
						$r['lessons']++;
					} else {
						$r['quizzes']++;
					}
				} else {
					$r['sections']++;
				}
			}
		}

		return $r;
	}

	public static function course_average_rate($reviews)
	{
		$r = array(
			'average' => 0,
			'percent' => 0,
		);
		if(empty($reviews)) return $r;
		$r['average'] = round(array_sum($reviews) / count($reviews), 1);
		$r['percent'] = $r['average'] * 100 / 5;
		return $r;
	}

	public static function course_views($post_id)
	{
		if (get_post_type() === 'stm-courses') {
			$views = STM_LMS_Course::get_course_views($post_id);

			$cookie_name = 'stm_lms_courses_watched';

			if (empty($_COOKIE[$cookie_name])) {
				$views++;
				$posts = array($post_id);
			} else {
				$posts = explode(',', $_COOKIE[$cookie_name]);
				if (!in_array($post_id, $posts)) {
					$views++;
					$posts[] = $post_id;
				}
			}

			setcookie($cookie_name, implode(',', $posts), time() + (86400 * 30 * 30), "/");
			update_post_meta($post_id, 'views', $views);
		}
	}

	public static function get_course_views($course_id)
	{
		$views = get_post_meta($course_id, 'views', true);

		return (empty($views)) ? 0 : intval($views);
	}

	public static function status_label($status)
	{
		$labels = array(
			'hot'     => esc_html__('Hot', 'masterstudy-lms-learning-management-system'),
			'new'     => esc_html__('New', 'masterstudy-lms-learning-management-system'),
			'special' => esc_html__('Special', 'masterstudy-lms-learning-management-system'),
		);


		return (!empty($labels[$status])) ? $labels[$status] : '';
	}

	public static function get_post_status($course_id)
	{
		$post_status = get_post_meta($course_id, 'status', true);

		if (empty($post_status)) return '';

		$post_status_label = STM_LMS_Course::status_label($post_status);
		$status_dates_start = get_post_meta($course_id, 'status_dates_start', true);
		$status_dates_end = get_post_meta($course_id, 'status_dates_end', true);

		if(!empty($status_dates_end)) $status_dates_start = intval($status_dates_start);
		if(!empty($status_dates_end)) $status_dates_end = intval($status_dates_end);

		if (empty($status_dates_start) and empty($status_dates_end)) {
			return array(
				'status' => $post_status,
				'label'  => $post_status_label,
			);
		}

		if (!empty(intval($status_dates_start)) and !empty(intval($status_dates_end))) {
			$current_time = time() * 1000;
			if ($current_time > $status_dates_start and $current_time < $status_dates_end) {
				return array(
					'status' => $post_status,
					'label'  => $post_status_label,
				);
			}
		}

		return '';
	}

	public static function get_course_price($course_id)
	{
		$price = get_post_meta($course_id, 'price', true);
		$sale_price = STM_LMS_Course::get_sale_price($course_id);

		/*If we have both prices*/
		if (!empty($price) and !empty($sale_price)) $price = $sale_price;

		/*If we have only sale price*/
		if (empty($price) and !empty($sale_price)) $price = $sale_price;

		/*If no prices*/
		if (empty($price) and empty($sale_price)) $price = 0;

		return apply_filters('stm_lms_course_price', $price);
	}

	public static function get_sale_price($post_id) {
		return apply_filters('stm_lms_get_sale_price', get_post_meta($post_id, 'sale_price', true), $post_id);
	}

	public static function lesson_started($post_id, $course_id) {

	    $user = STM_LMS_User::get_current_user();
	    if(empty($user['id'])) die;

	    $user_id = $user['id'];

	    $option_name = "stm_lms_course_started_{$post_id}_{$course_id}";

	    $started = get_user_meta($user_id, $option_name, true);

	    if(empty($started)) {
	        update_user_meta($user_id, $option_name, time());
        }

    }

}