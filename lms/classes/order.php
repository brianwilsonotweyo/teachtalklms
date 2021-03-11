<?php

STM_LMS_Order::init();

class STM_LMS_Order
{

    public static function init()
    {

        /*Redirect if after Paypal method*/
        add_action('template_redirect', function () {
            $is_paypal = (!empty($_GET['paypal_order_id'])) ? true : false;

            if ($is_paypal) {
                wp_redirect(STM_LMS_User::user_page_url() . '#' . intval($_GET['paypal_order_id']));
            }
        });

        add_action('wp_ajax_stm_lms_get_order_info', 'STM_LMS_Order::get_order_info');

        add_action('wp_ajax_stm_lms_get_user_orders', 'STM_LMS_Order::user_orders');

        add_action('save_post', 'STM_LMS_Order::save_order');

        add_filter('manage_stm-orders_posts_columns', 'STM_LMS_Order::column_names');

        add_action('manage_stm-orders_posts_custom_column', 'STM_LMS_Order::column_fields', 10, 2);

    }

    public static function get_order_info($order_id = '', $return = false)
    {

        if (!$return) check_ajax_referer('get_order_info', 'nonce');

        $r = array();

        $order_id = (empty($_GET['order_id'])) ? $order_id : intval($_GET['order_id']);
        if (empty($order_id)) die;

        $date_format = get_option('date_format');
        $time_format = get_option('time_format');

        $order_meta = apply_filters('stm_lms_order_details', array(), $order_id);
        if (empty($order_meta)) $order_meta = STM_LMS_Helpers::parse_meta_field($order_id);

        $cart_items = array();
        $total = 0;

        foreach ($order_meta['items'] as $course) {
            $cart_items[$course['item_id']] = array(
                'title' => get_the_title($course['item_id']),
                'link' => get_the_permalink($course['item_id']),
                'image' => get_the_post_thumbnail($course['item_id'], 'img-300-225'),
                'price' => $course['price'],
                'terms' => stm_lms_get_terms_array($course['item_id'], 'stm_lms_course_taxonomy', 'name'),
                'price_formatted' => STM_LMS_Helpers::display_price($course['price']),
            );
            $total += $course['price'];
        }

        $i18n = STM_LMS_Order::translates();

        $timezone = get_option('gmt_offset');
        $diff = (!empty($timezone)) ? $timezone * 60 * 60 : 0;
        $diff = apply_filters('stm_lms_gmt_offset', $diff);

        $r['order'] = array_merge($order_meta, $i18n, array(
            'id' => $order_id,
            'date' => $order_meta['date'],
            'date_formatted' => date_i18n($date_format . ' ' . $time_format, $order_meta['date'] + $diff),
            'cart_items' => $cart_items,
            'total' => STM_LMS_Helpers::display_price($total),
            'user' => STM_LMS_User::get_current_user($order_meta['user_id'])
        ));

        if ($return) return $r['order'];

        wp_send_json($r);
    }

    /**
     * @param $data [user_id, cart_items, payment_code, _order_total, _order_currency]
     * @param bool $return
     *
     * @return int|WP_Error
     */
    public static function create_order($data, $return = false)
    {
        if (empty($data['user_id'])) die;

        $order_info = array(
            'user_id' => $data['user_id'],
            'items' => $data['cart_items'],
            'date' => time(),
            'status' => 'pending',
            'payment_code' => $data["payment_code"],
            'order_key' => uniqid($data['user_id'] . time()),
            '_order_total' => $data['_order_total'],
            '_order_currency' => $data['_order_currency'],
        );

        $order_post = array(
            'post_type' => 'stm-orders',
            'post_title' => wp_strip_all_tags($order_info['order_key']),
            'post_status' => 'publish',
        );

        $order_id = wp_insert_post($order_post);

        foreach ($order_info as $meta_key => $meta_value) {
            update_post_meta($order_id, $meta_key, $meta_value);
        }

        if ($return) return $order_id;

    }

    public static function translates()
    {
        return array(
            'i18n' => array(
                'order_key' => esc_html__('Order key', 'masterstudy-lms-learning-management-system'),
                'date' => esc_html__('Date', 'masterstudy-lms-learning-management-system'),
                'status' => esc_html__('Status', 'masterstudy-lms-learning-management-system'),
                'pending' => esc_html__('Pending', 'masterstudy-lms-learning-management-system'),
                'processing' => esc_html__('Processing', 'masterstudy-lms-learning-management-system'),
                'failed' => esc_html__('Failed', 'masterstudy-lms-learning-management-system'),
                'on-hold' => esc_html__('On hold', 'masterstudy-lms-learning-management-system'),
                'refunded' => esc_html__('Refunded', 'masterstudy-lms-learning-management-system'),
                'completed' => esc_html__('Completed', 'masterstudy-lms-learning-management-system'),
                'cancelled' => esc_html__('Cancelled', 'masterstudy-lms-learning-management-system'),
                'user' => esc_html__('User', 'masterstudy-lms-learning-management-system'),
                'order_items' => esc_html__('Order items', 'masterstudy-lms-learning-management-system'),
                'course_name' => esc_html__('Course name', 'masterstudy-lms-learning-management-system'),
                'course_price' => esc_html__('Course price', 'masterstudy-lms-learning-management-system'),
                'total' => esc_html__('Total', 'masterstudy-lms-learning-management-system'),
            )
        );
    }

    public static function save_order($post_id)
    {
        if (get_post_type($post_id) == 'stm-orders') {
            /*is_completed?*/
            $status = (!empty($_POST['order_status'])) ? sanitize_text_field($_POST['order_status']) : '';
            $user_id = get_post_meta($post_id, 'user_id', true);
            $previous_status = get_post_meta($post_id, 'status', true);

            //echo $previous_status;
            /*If status changed*/
            if ($previous_status !== $status) {
                if ($status === 'completed' and !empty($user_id)) {
                    update_post_meta($post_id, 'status', $status);
                    STM_LMS_Order::accept_order($user_id, $post_id);
                } elseif ($status === 'cancelled' and !empty($user_id)) {
                    update_post_meta($post_id, 'status', $status);
                    STM_LMS_Order::remove_order($user_id, $post_id);
                }
            }
        }
    }

    public static function accept_order($user_id, $order_id = '')
    {

        $accept_order = apply_filters('stm_lms_accept_order', true);
        $cart_items = stm_lms_get_cart_items($user_id, apply_filters('stm_lms_cart_items_fields', array('item_id', 'price')));

        if(!empty($order_id)) {
            $cart_items = get_post_meta($order_id, 'items', true);
        }

        if ($accept_order) {

            foreach ($cart_items as $cart_item) {
                STM_LMS_Course::add_user_course($cart_item['item_id'], $user_id, 0, 0);
                STM_LMS_Course::add_student($cart_item['item_id']);
            }

            /*Delete Cart*/
            stm_lms_get_delete_cart_items($user_id);
        }

        do_action('stm_lms_order_accepted', $user_id, $cart_items);
    }

    public static function remove_order($user_id, $order_id)
    {
        $cart_items = get_post_meta($order_id, 'items', true);

        foreach ($cart_items as $cart_item) {
            stm_lms_get_delete_user_course($user_id, $cart_item['item_id']);
            STM_LMS_Course::remove_student($cart_item['item_id']);
            do_action('stm_lms_order_remove', $cart_item['item_id'], $cart_item);
        }

        /*Delete Cart*/
        stm_lms_get_delete_cart_items($user_id);
    }

    public static function user_orders()
    {

        check_ajax_referer('user_orders', 'nonce');

        $user = STM_LMS_User::get_current_user();
        if (empty($user['id'])) die;
        $user_id = $user['id'];

        $r = array(
            'posts' => array(),
            'total' => 0
        );

        $posts = array();

        $pp = get_option('posts_per_page');
        $offset = (!empty($_GET['offset'])) ? intval($_GET['offset']) : 0;

        $offset = $offset * $pp;

        $user_orders = apply_filters('stm_lms_user_orders', array(), $user_id, $pp, $offset);

        if (empty($user_orders)) {

            $args = array(
                'post_type' => 'stm-orders',
                'posts_per_page' => $pp,
                'post_status' => 'publish',
                'offset' => $offset,
                'meta_query' => array(
                    array(
                        'key' => 'user_id',
                        'compare' => '=',
                        'value' => $user_id
                    )
                )
            );


            $q = new WP_Query($args);
            $total = $q->found_posts;


            if ($q->have_posts()) {
                while ($q->have_posts()) {
                    $q->the_post();
                    $id = get_the_ID();
                    $posts[] = STM_LMS_Order::get_order_info($id, true);
                }

                wp_reset_postdata();
            }

        } else {

            extract($user_orders);
        }

        $r['total'] = $total <= $offset + $pp;
        $r['posts'] = $posts;

        wp_send_json($r);

    }

    public static function column_names($columns)
    {

        unset($columns['cb']);
        unset($columns['title']);

        $lms_id = array(
            'cb' => '<input type="checkbox" />',
            'lms_id' => esc_html__('Order', 'masterstudy-lms-learning-management-system'),
            'order_key' => esc_html__('Order Key', 'masterstudy-lms-learning-management-system'),
            'order_status' => esc_html__('Order Status', 'masterstudy-lms-learning-management-system'),
        );

        $columns = array_merge($lms_id, $columns);

        return $columns;
    }

    public static function column_fields($column, $post_id)
    {
        $edit_link = get_edit_post_link($post_id);
        $title = get_the_title($post_id);

        $order_meta = STM_LMS_Helpers::parse_meta_field($post_id);
        $user = STM_LMS_User::get_current_user($order_meta['user_id']);


        switch ($column) {
            case 'lms_id' :
                $user_login = $user['login'];
                echo wp_kses_post("<a class='row-title' href='{$edit_link}'>#{$post_id} {$user_login}</a>");
                break;
            case 'order_key' :
                echo wp_kses_post($title);
                break;
            case 'order_status' :
                echo wp_kses_post("<span class='stm_lms_status stm_lms_status_{$order_meta['status']}'>{$order_meta['status']}</span>");
                break;
        }
    }
}