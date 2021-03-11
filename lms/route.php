<?php
global $wp_router;

/**
 * stm lms order statistics
 */
$wp_router->get(array(
        'uri' => STM_LMS_BASE_API_URL . '/stm-lms/order/items',
        'uses' => function () {
            wp_send_json(\stmLms\Classes\Models\StmStatistics::get_user_orders_api());
            die;
        }
    )
);

/**
 * User
 */
$wp_router->get(array(
        'uri' => STM_LMS_BASE_API_URL . '/stm-lms-user/search',
        'uses' => function () {
            if (isset($_GET['search']))
                wp_send_json(\stmLms\Classes\Models\StmUser::search($_GET['search']));
            wp_send_json([]);
            die;
        }
    )
);

$wp_router->get(array(
        'uri' => STM_LMS_BASE_API_URL . '/stm-lms-user/course-list',
        'uses' => function () {
            if (isset($_GET['author_id']) AND $user = new \stmLms\Classes\Models\StmUser($_GET['author_id'])) {
                $course_list = [];
                $courses = $user->get_courses();
                foreach ($courses as $course) {
                    $course_list[] = [
                        "id" => $course->ID,
                        "title" => $course->post_title,
                    ];
                }
                wp_send_json($course_list);
            }
            wp_send_json([]);
            die;
        }
    )
);

$wp_router->post(array(
        'uri' => STM_LMS_BASE_API_URL . '/stm-lms-user/save-paypal-email',
        'uses' => function () {
            wp_send_json(\stmLms\Classes\Models\StmUser::save_paypal_email());
            die;
        }
    )
);

/**
 * stm lms payout
 */
$wp_router->post(array(
        'uri' => STM_LMS_BASE_API_URL . '/stm-lms-pauout/settings',
        'uses' => function () {
            wp_send_json(\stmLms\Classes\Models\StmLmsPayout::settings_payment_method());
            die;
        }
    )
);

$wp_router->post(array(
        'uri' => STM_LMS_BASE_API_URL . '/stm-lms-pauout/payment/set_default',
        'uses' => function () {
            wp_send_json(\stmLms\Classes\Models\StmLmsPayout::payment_set_default());
            die;
        }
    )
);

$wp_router->get(array(
        'uri' => STM_LMS_BASE_API_URL . '/stm-lms-pauout/pay-now',
        'uses' => function () {
            wp_send_json(\stmLms\Classes\Models\StmLmsPayout::pay_now());
            die;
        }
    )
);

$wp_router->get(array(
        'uri' => STM_LMS_BASE_API_URL . '/stm-lms-pauout/pay-now/{id}',
        'uses' => function ($id) {
            wp_send_json(\stmLms\Classes\Models\StmLmsPayout::pay_now_by_payout_id($id));
            die;
        }
    )
);

$wp_router->get(array(
        'uri' => STM_LMS_BASE_API_URL . '/stm-lms-pauout/payed/{id}',
        'uses' => function ($id) {
            wp_send_json(\stmLms\Classes\Models\StmLmsPayout::payed($id));
            die;
        }
    )
);
