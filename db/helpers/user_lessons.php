<?php

if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly

function stm_lms_add_user_lesson($user_lesson)
{
	global $wpdb;
	$table_name = stm_lms_user_lessons_name($wpdb);

	$wpdb->insert(
		$table_name,
		$user_lesson
	);
}

function stm_lms_get_user_lesson($user_id, $course_id, $lesson_id, $fields = array())
{
	global $wpdb;
	$table = stm_lms_user_lessons_name($wpdb);

	$fields = (empty($fields)) ? '*' : implode(',', $fields);

	$request = "SELECT {$fields} FROM {$table}
			WHERE 
			user_ID = {$user_id} AND
			course_id = {$course_id} AND
			lesson_id = {$lesson_id}";

	$r = $wpdb->get_results($request, ARRAY_N);
	return $r;
}

function stm_lms_get_user_course_lessons($user_id, $course_id, $fields = array())
{
	global $wpdb;
	$table = stm_lms_user_lessons_name($wpdb);

	$fields = (empty($fields)) ? '*' : implode(',', $fields);

	$request = "SELECT {$fields} FROM {$table}
			WHERE 
			user_ID = {$user_id} AND
			course_id = {$course_id}";

	$r = $wpdb->get_results($request, ARRAY_N);
	return $r;
}

function stm_lms_get_user_lessons($course_id, $fields = array())
{
    global $wpdb;
    $table = stm_lms_user_lessons_name($wpdb);

    $fields = (empty($fields)) ? '*' : implode(',', $fields);

    $request = "SELECT {$fields} FROM {$table}
			WHERE 
			course_id = {$course_id}";

    $r = $wpdb->get_results($request, ARRAY_N);
    return $r;
}