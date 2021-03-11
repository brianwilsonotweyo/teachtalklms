<?php

if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly

function stm_lms_add_user_answer($user_answer)
{
	global $wpdb;
	$table_name = stm_lms_user_answers_name($wpdb);

	$wpdb->insert(
		$table_name,
		$user_answer
	);
}

function stm_lms_get_user_answers($user_id, $quiz_id, $attempt = '1', $get_correct = false, $fields = array())
{
	global $wpdb;
	$table = stm_lms_user_answers_name($wpdb);

	$fields = (empty($fields)) ? '*' : implode(',', $fields);

	$request = "SELECT {$fields} FROM {$table}
			WHERE 
			user_ID = {$user_id} AND
			quiz_id = {$quiz_id} AND
			attempt_number = {$attempt}";
	if ($get_correct) $request .= " AND correct_answer = 1";

	$r = $wpdb->get_results($request, ARRAY_N);
	return $r;
}

function stm_lms_get_quiz_latest_answers($user_id, $quiz_id, $fields = array())
{
	global $wpdb;
	$table = stm_lms_user_answers_name($wpdb);

	$fields = (empty($fields)) ? '*' : implode(',', $fields);

	$request = "SELECT {$fields} FROM {$table}
			WHERE 
			user_ID = {$user_id} AND
			quiz_id = {$quiz_id} AND 
			attempt_number = (
				SELECT attempt_number FROM {$table}
				WHERE
				user_ID = {$user_id} AND
				quiz_id = {$quiz_id}
				ORDER BY attempt_number DESC
				LIMIT 1
			)";

	$r = $wpdb->get_results($request, ARRAY_A);
	return $r;
}