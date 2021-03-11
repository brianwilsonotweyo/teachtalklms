<?php
function stm_lms_enqueue_taxonomy_ss() {
	$v = time();
	$assets = STM_LMS_URL . 'assets';
	$base = STM_LMS_URL . '/post_type/taxonomy_meta/assets/';

	wp_enqueue_script('fonticonpicker', $base . 'js/jquery.fonticonpicker.min.js', array(), $v);
	wp_enqueue_style('fonticonpicker', $base . 'css/jquery.fonticonpicker.min.css', array(), $v);
	wp_enqueue_style('fonticonpicker-grey', $base . 'css/jquery.fonticonpicker.grey.min.css', array(), $v);
	wp_enqueue_style('linear', $assets . '/linearicons/linear.css', array(), $v);

}

add_action( 'admin_enqueue_scripts', 'stm_lms_enqueue_taxonomy_ss' );