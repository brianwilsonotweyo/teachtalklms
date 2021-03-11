<?php

$paypal_files[] = STM_LMS_PATH."/libraries/paypal/includes/classes/vender/autoload.php";
$paypal_files[] = STM_LMS_PATH."/libraries/paypal/route.php";

foreach (scandir(STM_LMS_PATH."/libraries/paypal/includes/classes/") as $key => $value) {
	if ( strpos($value, ".php") )
		$paypal_files[] = STM_LMS_PATH."/libraries/paypal/includes/classes/".$value;
}
$paypal_files[] = STM_LMS_PATH."/libraries/paypal/init.php";
foreach ( $paypal_files as $file ) {
	if(file_exists($file))
		require_once $file;
}