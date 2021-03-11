<?php

if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly

require_once STM_LMS_PATH . '/lms/enqueue.php';
require_once STM_LMS_PATH . '/lms/helpers.php';

require_once STM_LMS_PATH . '/lms/classes/Validation.php';
require_once STM_LMS_PATH . '/lms/classes/subscriptions.php';
require_once STM_LMS_PATH . '/lms/classes/helpers.php';
require_once STM_LMS_PATH . '/lms/classes/templates.php';
require_once STM_LMS_PATH . '/lms/classes/options.php';
require_once STM_LMS_PATH . '/lms/classes/reviews.php';
require_once STM_LMS_PATH . '/lms/classes/user.php';
require_once STM_LMS_PATH . '/lms/classes/instructors.php';
require_once STM_LMS_PATH . '/lms/classes/course.php';
require_once STM_LMS_PATH . '/lms/classes/quiz.php';
require_once STM_LMS_PATH . '/lms/classes/lesson.php';
require_once STM_LMS_PATH . '/lms/classes/cart.php';
require_once STM_LMS_PATH . '/lms/classes/order.php';
require_once STM_LMS_PATH . '/lms/classes/paypal.php';
require_once STM_LMS_PATH . '/lms/classes/comments.php';
require_once STM_LMS_PATH . '/lms/classes/chat.php';
require_once STM_LMS_PATH . '/lms/classes/mails.php';

if(class_exists('BuddyPress')) {
    require_once STM_LMS_PATH . '/lms/classes/buddypress.php';
}


/*Router*/
require_once STM_LMS_PATH . '/lms/classes/class-wp-request.php';
require_once STM_LMS_PATH . '/lms/classes/class-wp-middleware.php';
require_once STM_LMS_PATH . '/lms/classes/class-wp-response.php';
require_once STM_LMS_PATH . '/lms/classes/class-wp-router.php';
require_once STM_LMS_PATH . '/lms/classes/responses/class-wp-json-response.php';
require_once STM_LMS_PATH . '/lms/classes/responses/class-wp-template-response.php';
require_once STM_LMS_PATH . '/lms/classes/responses/class-wp-redirect-response.php';
require_once STM_LMS_PATH . '/lms/classes/routes.php';