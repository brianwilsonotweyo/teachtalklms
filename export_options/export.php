<?php

if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly

if(!empty($_GET['stm_lms_export_options'])) {
	add_action('init', 'stm_lms_export_options');

	function stm_lms_export_options() {

	    if(!current_user_can('manage_options')) die;

		$settings = get_option('stm_lms_settings', array());
		stm_pa(json_encode($settings));

		die;
	}
}