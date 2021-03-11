<?php

if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly

function stm_lms_add_user_quiz($user_quiz) {
	global $wpdb;
	$table_name = stm_lms_user_quizzes_name($wpdb);

	$wpdb->insert(
		$table_name,
		$user_quiz
	);
}

function stm_lms_get_user_quizzes($user_id, $quiz_id, $fields = array()) {
	global $wpdb;
	$table = stm_lms_user_quizzes_name($wpdb);

	$fields = (empty($fields)) ? '*' : implode(',', $fields);

	$request = "SELECT {$fields} FROM {$table}
			WHERE 
			user_id = {$user_id} AND
			quiz_id = {$quiz_id}";

	$r = $wpdb->get_results($request, ARRAY_A);
	return $r;
}

function stm_lms_get_user_course_quizzes($user_id, $course_id, $fields = array(), $status='passed') {
	global $wpdb;
	$table = stm_lms_user_quizzes_name($wpdb);

	$fields = (empty($fields)) ? '*' : implode(',', $fields);

	$request = "SELECT {$fields} FROM {$table}
			WHERE 
			user_id = {$user_id} AND
			status = '{$status}' AND
			course_id = {$course_id}";

	$r = $wpdb->get_results($request, ARRAY_A);
	return $r;
}

function stm_lms_get_user_last_quiz($user_id, $quiz_id, $fields = array()) {
	global $wpdb;
	$table = stm_lms_user_quizzes_name($wpdb);

	$fields = (empty($fields)) ? '*' : implode(',', $fields);

	$request = "SELECT {$fields} FROM {$table}
			WHERE 
			user_id = {$user_id} AND
			quiz_id = {$quiz_id}
		 	ORDER BY user_quiz_id DESC
		 	LIMIT 1";

	$r = $wpdb->get_results($request, ARRAY_A);
	return $r;
}

function stm_lms_get_user_all_quizzes($user_id, $limit = '', $offset = '', $fields = array(), $get_total = false) {
	global $wpdb;
	$table = stm_lms_user_quizzes_name($wpdb);

	$fields = (empty($fields)) ? '*' : implode(',', $fields);
	if($get_total) $fields = 'COUNT(*)';

	$request = "SELECT {$fields} FROM {$table}
			WHERE 
			user_id = {$user_id}
			ORDER BY user_quiz_id DESC";

	if(!empty($limit)) $request .= " LIMIT {$limit}";
	if(!empty($offset)) $request .= " OFFSET {$offset}";

	$r = $wpdb->get_results($request, ARRAY_A);
	return $r;
}

function stm_lms_get_course_passed_quizzes($course_id, $fields = array()) {
    global $wpdb;
    $table = stm_lms_user_quizzes_name($wpdb);

    $fields = (empty($fields)) ? '*' : implode(',', $fields);

    $request = "SELECT {$fields} FROM {$table}
			WHERE 
			status = 'passed' AND
			course_id = {$course_id}";

    $r = $wpdb->get_results($request, ARRAY_A);
    return $r;
}

function stm_lms_check_quiz($user_id, $quiz_id, $fields = array()) {
    global $wpdb;
    $table = stm_lms_user_quizzes_name($wpdb);

    $fields = (empty($fields)) ? '*' : implode(',', $fields);

    $request = "SELECT {$fields} FROM {$table}
			WHERE 
			status = 'passed' AND
			user_id = {$user_id} AND
			quiz_id = {$quiz_id}";

    $r = $wpdb->get_results($request, ARRAY_A);
    return $r;
}