<?php
STM_LMS_Mails::init();

class STM_LMS_Mails
{

    public static function init()
    {
        add_action('order_created', 'STM_LMS_Mails::order_created', 10, 3);
        add_action('add_user_course', 'STM_LMS_Mails::add_user_course', 10, 2);
    }

    static function wp_mail_text_html() {
        add_filter( 'wp_mail_content_type', 'STM_LMS_Mails::wp_mail_html');
    }

    static function remove_wp_mail_text_html() {
        remove_filter( 'wp_mail_content_type', 'STM_LMS_Mails::wp_mail_html');
    }

    static function wp_mail_html() {
        return 'text/html';
    }

    static function order_created($user, $cart_items, $payment_code) {
        self::wp_mail_text_html();

        $user = STM_LMS_User::get_current_user($user);

        $message = sprintf(esc_html__('New Order from user %s.', 'masterstudy-lms-learning-management-system'), $user['login']);
        self::send_email('New Order', $message);

        $message = esc_html__('Your Order Accepted.', 'masterstudy-lms-learning-management-system');
        self::send_email('New Order', $message, $user['email']);

        self::remove_wp_mail_text_html();
    }

    static function add_user_course($user_id, $course_id) {
        self::wp_mail_text_html();

        $user = STM_LMS_User::get_current_user($user_id);

        $message = sprintf(esc_html__('Course %s was added to %s.', 'masterstudy-lms-learning-management-system'), get_the_title($course_id), $user['login']);
        self::send_email('Course added to User', $message);

        $message = sprintf(esc_html__('Course %s is now available to learn.', 'masterstudy-lms-learning-management-system'), get_the_title($course_id));
        self::send_email('Course added.', $message, $user['email']);

        self::remove_wp_mail_text_html();
    }

    static function send_email($subject, $message, $to = '', $additional_receivers = array()) {
        $to = (!empty($to)) ? $to : get_option('admin_email');
        $receivers = array_merge(array($to), $additional_receivers);

        wp_mail($receivers, $subject, $message);
    }
}