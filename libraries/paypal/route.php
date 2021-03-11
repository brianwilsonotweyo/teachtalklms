<?php
global $wp_router;
$wp_router->post( array(
		'uri'  => '/payment/paypal/web-hook',
		'uses' => array(\stmLms\Libraries\Paypal\WebHook::class, 'web_hook')
	)
);
