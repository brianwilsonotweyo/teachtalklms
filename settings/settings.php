<?php

class STM_LMS_Settings
{

    function __construct()
    {
        add_action('init', array($this, 'stm_lms_settings_page'), 1000);
        add_action('wp_ajax_stm_save_settings', array($this, 'stm_save_settings'));
    }

    function stm_lms_settings_page()
    {

        if(current_user_can('manage_options')) {

            $post_types = array(
                'stm-courses',
                'stm-lessons',
                'stm-quizzes',
                'stm-questions',
                'stm-reviews',
                'stm-orders',
                'stm-ent-groups',
            );

            $taxonomies = array(
                'stm_lms_course_taxonomy'
            );

            add_menu_page(
                'STM LMS',
                'STM LMS',
                'manage_options',
                'stm-lms-settings',
                array($this, 'stm_lms_settings_page_view'),
                'dashicons-welcome-learn-more',
                5
            );

            foreach ($post_types as $post_type) {
                $post_type_data = get_post_type_object($post_type);

                if(empty($post_type_data)) continue;

                add_submenu_page(
                    'stm-lms-settings',
                    $post_type_data->label,
                    $post_type_data->label,
                    'manage_options',
                    '/edit.php?post_type=' . $post_type
                );
            }

            foreach ($taxonomies as $taxonomy) {
                $tax_data = get_taxonomy($taxonomy);

                add_submenu_page(
                    'stm-lms-settings',
                    $tax_data->label,
                    $tax_data->label,
                    'manage_options',
                    'edit-tags.php?taxonomy=' . $taxonomy
                );
            }
        }
    }

    public static function stm_get_post_type_array($post_type, $args = array())
    {
        $r = array(
            '' => esc_html__('Choose Page', 'masterstudy-lms-learning-management-system'),
        );

        $default_args = array(
            'post_type' => $post_type,
            'posts_per_page' => -1,
            'post_status' => 'publish'
        );

        $q = get_posts(wp_parse_args($args, $default_args));

        if (!empty($q)) {
            foreach ($q as $post_data) {
                $r[$post_data->ID] = $post_data->post_title;
            }
        }

        wp_reset_query();

        return $r;
    }

    function stm_lms_settings()
    {
        $pages = $this->stm_get_post_type_array('page');
        return apply_filters('stm_lms_settings', array(
            'id' => 'stm_lms_settings',
            'args' => array(
                'stm_lms_settings' => array(
                    'section_1' => array(
                        'name' => esc_html__('General', 'masterstudy-lms-learning-management-system'),
                        'fields' => array(
                            'main_color' => array(
                                'type' => 'color',
                                'label' => esc_html__('Main color', 'masterstudy-lms-learning-management-system'),
                                'columns' => '50'
                            ),
                            'secondary_color' => array(
                                'type' => 'color',
                                'label' => esc_html__('Secondary color', 'masterstudy-lms-learning-management-system'),
                                'columns' => '50'
                            ),
                            'currency_symbol' => array(
                                'type' => 'text',
                                'label' => esc_html__('Currency symbol', 'masterstudy-lms-learning-management-system'),
                                'columns' => '50'
                            ),
                            'currency_position' => array(
                                'type' => 'select',
                                'label' => esc_html__('Currency Position', 'masterstudy-lms-learning-management-system'),
                                'value' => 'left',
                                'options' => array(
                                    'left' => esc_html__('Left', 'masterstudy-lms-learning-management-system'),
                                    'right' => esc_html__('Right', 'masterstudy-lms-learning-management-system'),
                                ),
                                'columns' => '50'
                            ),
                            'currency_thousands' => array(
                                'type' => 'text',
                                'label' => esc_html__('Thousands Separator', 'masterstudy-lms-learning-management-system'),
                                'value' => ','
                            ),
                            'wocommerce_checkout' => array(
                                'type' => 'checkbox',
                                'label' => esc_html__('Enable WooCommerce Checkout', 'masterstudy-lms-learning-management-system'),
                                'description' => esc_html__('Note, you need to install WooCommerce, and set Cart and Checkout Pages'),
                                'pro' => true
                            ),

                            'wocommerce_checkout' => array(
                                'type' => 'checkbox',
                                'label' => esc_html__('Enable WooCommerce Checkout', 'masterstudy-lms-learning-management-system'),
                                'description' => esc_html__('Note, you need to install WooCommerce, and set Cart and Checkout Pages'),
                                'pro' => true
                            ),
                            'author_fee' => array(
                                'type' => 'number',
                                'label' => esc_html__('Author Fee (%)', 'masterstudy-lms-learning-management-system'),
                                'columns' => '50',
                                'value' => '10',
                                'pro' => true
                            ),
                            'courses_featured_num' => array(
                                'type' => 'number',
                                'label' => esc_html__('Number of free featured', 'masterstudy-lms-learning-management-system'),
                                'columns' => '50',
                                'value' => 1,
                                'pro' => true
                            ),
                        )
                    ),
                    'section_2' => array(
                        'name' => esc_html__('Courses', 'masterstudy-lms-learning-management-system'),
                        'fields' => array(
                            'courses_page' => array(
                                'type' => 'select',
                                'label' => esc_html__('Courses Page', 'masterstudy-lms-learning-management-system'),
                                'options' => $pages
                            ),
                            'courses_view' => array(
                                'type' => 'select',
                                'label' => esc_html__('Courses Page Layout', 'masterstudy-lms-learning-management-system'),
                                'options' => array(
                                    'grid' => esc_html__('Grid', 'masterstudy-lms-learning-management-system'),
                                    //'list' => esc_html__('List', 'masterstudy-lms-learning-management-system'),
                                ),
                                'value' => 'grid'
                            ),
                            'courses_per_page' => array(
                                'type' => 'number',
                                'label' => esc_html__('Courses per page', 'masterstudy-lms-learning-management-system'),
                                'value' => '9'
                            ),
                            'courses_per_row' => array(
                                'type' => 'select',
                                'label' => esc_html__('Courses per row', 'masterstudy-lms-learning-management-system'),
                                'options' => array(
                                    '2' => 2,
                                    '3' => 3,
                                    '4' => 4,
                                    '6' => 6,
                                ),
                                'value' => '4'
                            ),
                            'course_card_view' => array(
                                'type' => 'select',
                                'label' => esc_html__('Course Card Info', 'masterstudy-lms-learning-management-system'),
                                'options' => array(
                                    'center' => esc_html__('Center', 'masterstudy-lms-learning-management-system'),
                                    'right' => esc_html__('Right', 'masterstudy-lms-learning-management-system'),
                                ),
                                'value' => 'center'
                            ),
                            'course_card_style' => array(
                                'type' => 'select',
                                'label' => esc_html__('Course Card Style', 'masterstudy-lms-learning-management-system'),
                                'options' => array(
                                    'style_1' => esc_html__('Style 1', 'masterstudy-lms-learning-management-system'),
                                    'style_2' => esc_html__('Style 2', 'masterstudy-lms-learning-management-system'),
                                ),
                                'value' => 'style_1'
                            ),
                            'courses_categories_slug' => array(
                                'type' => 'text',
                                'label' => esc_html__('Courses category parent slug', 'masterstudy-lms-learning-management-system'),
                                'value' => 'stm_lms_course_category'
                            ),
                            'load_more_type' => array(
                                'type' => 'select',
                                'label' => esc_html__('Load More Type', 'masterstudy-lms-learning-management-system'),
                                'options' => array(
                                    'button' => esc_html__('Button', 'masterstudy-lms-learning-management-system'),
                                    'infinite' => esc_html__('Infinite Scrolling', 'masterstudy-lms-learning-management-system'),
                                ),
                                'value' => 'button'
                            ),
                            'disable_lazyload' => array(
                                'type' => 'checkbox',
                                'label' => esc_html__('Disable Lazyload', 'masterstudy-lms-learning-management-system'),
                            ),
                        )
                    ),
                    'section_course' => array(
                        'name' => esc_html__('Course', 'masterstudy-lms-learning-management-system'),
                        'fields' => array(
                            'course_style' => array(
                                'type' => 'select',
                                'label' => esc_html__('Courses Page Style', 'masterstudy-lms-learning-management-system'),
                                'options' => array(
                                    'default' => esc_html__('Default', 'masterstudy'),
                                    'classic' => esc_html__('Classic', 'masterstudy'),
                                    'udemy' => esc_html__('Udemy (Udemy Addon required)', 'masterstudy'),
                                ),
                                'value' => 'default',
                                'pro' => true,
                            ),
                            'redirect_after_purchase' => array(
                                'type' => 'checkbox',
                                'label' => esc_html__('Redirect to Checkout after adding to Cart', 'masterstudy-lms-learning-management-system'),
                            ),
                        )
                    ),
                    'section_routes' => array(
                        'name' => esc_html__('Routes', 'masterstudy-lms-learning-management-system'),
                        'fields' => array(
                            'user_url' => array(
                                'type' => 'text',
                                'label' => esc_html__('User Private Base Url (Default /lms-user)', 'masterstudy-lms-learning-management-system'),
                                'columns' => '50'
                            ),
                            'user_url_profile' => array(
                                'type' => 'text',
                                'label' => esc_html__('User Public Base Url (Default /lms-user_profile)', 'masterstudy-lms-learning-management-system'),
                                'columns' => '50'
                            ),
                            'certificate_url' => array(
                                'type' => 'text',
                                'label' => esc_html__('Certificates Base Url (Default /lms-certificates)', 'masterstudy-lms-learning-management-system'),
                                'columns' => '50'
                            ),
                            'login_url' => array(
                                'type' => 'text',
                                'label' => esc_html__('Login Url (Default /lms-login)', 'masterstudy-lms-learning-management-system'),
                                'columns' => '50'
                            ),
                            'chat_url' => array(
                                'type' => 'text',
                                'label' => esc_html__('Chat Url (Default /lms-chats)', 'masterstudy-lms-learning-management-system'),
                                'columns' => '50'
                            ),
                            'wishlist_url' => array(
                                'type' => 'text',
                                'label' => esc_html__('Wishlist Url (Default /lms-wishlist)', 'masterstudy-lms-learning-management-system'),
                                'columns' => '50'
                            ),
                            'checkout_url' => array(
                                'type' => 'text',
                                'label' => esc_html__('Checkout Url (Default /lms-checkout)', 'masterstudy-lms-learning-management-system'),
                                'columns' => '50'
                            ),
                        )
                    ),
                    'section_3' => array(
                        'name' => esc_html__('Payment Methods', 'masterstudy-lms-learning-management-system'),
                        'fields' => array(
                            'payment_methods' => array(
                                'type' => 'payments',
                                'label' => esc_html__('Payment Methods', 'masterstudy-lms-learning-management-system'),
                            ),
                        )
                    ),
                    'section_5' => array(
                        'name' => esc_html__('Google API', 'masterstudy-lms-learning-management-system'),
                        'fields' => array(
                            'recaptcha_site_key' => array(
                                'type' => 'text',
                                'label' => esc_html__('Recaptcha Site Key', 'masterstudy-lms-learning-management-system'),
                            ),
                            'recaptcha_private_key' => array(
                                'type' => 'text',
                                'label' => esc_html__('Recaptcha Private Key', 'masterstudy-lms-learning-management-system'),
                            ),
                        )
                    ),
                    'section_4' => array(
                        'name' => esc_html__('Profiles', 'masterstudy-lms-learning-management-system'),
                        'fields' => array(
                            'instructors_page' => array(
                                'type' => 'select',
                                'label' => esc_html__('Instructors Archive Page', 'masterstudy-lms-learning-management-system'),
                                'options' => $pages,
                                'columns' => 50
                            ),
                            'profile_style' => array(
                                'type' => 'select',
                                'label' => esc_html__('Profile Page Style', 'masterstudy-lms-learning-management-system'),
                                'options' => array(
                                    'default' => esc_html__('Default', 'masterstudy-lms-learning-management-system'),
                                    'classic' => esc_html__('Classic', 'masterstudy-lms-learning-management-system'),
                                ),
                                'value' => 'default',
                                'columns' => 50
                            ),
                            'course_premoderation' => array(
                                'type' => 'checkbox',
                                'label' => esc_html__('Enable Course Pre-moderation', 'masterstudy-lms-learning-management-system'),
                                'description' => esc_html__('Course will have Pending status, until you approve it', 'masterstudy-lms-learning-management-system'),
                                'pro' => true,
                                'columns' => 50
                            ),
                        )
                    ),
                    'section_6' => array(
                        'name' => esc_html__('Certificates', 'masterstudy-lms-learning-management-system'),
                        'fields' => array(
                            'certificate_image' => array(
                                'pro' => true,
                                'type' => 'image',
                                'label' => esc_html__('Certificate Image', 'masterstudy-lms-learning-management-system'),
                            ),
                            /*TITLE*/
                            'certificate_title' => array(
                                'pro' => true,
                                'type' => 'text',
                                'label' => esc_html__('Certificate Title', 'masterstudy-lms-learning-management-system'),
                            ),
                            'certificate_title_color' => array(
                                'type' => 'color',
                                'pro' => true,
                                'label' => esc_html__('Certificate title color', 'masterstudy-lms-learning-management-system'),
                                'columns' => '50'
                            ),
                            'certificate_title_fsz' => array(
                                'type' => 'number',
                                'pro' => true,
                                'label' => esc_html__('Certificate title font size (px)', 'masterstudy-lms-learning-management-system'),
                                'columns' => '50',
                                'value' => 60
                            ),
                            /*SUBTITLE*/
                            'certificate_subtitle' => array(
                                'pro' => true,
                                'type' => 'text',
                                'label' => esc_html__('Certificate subtitle', 'masterstudy-lms-learning-management-system'),
                            ),
                            'certificate_subtitle_color' => array(
                                'type' => 'color',
                                'pro' => true,
                                'label' => esc_html__('Certificate subtitle color', 'masterstudy-lms-learning-management-system'),
                                'columns' => '50'
                            ),
                            'certificate_subtitle_fsz' => array(
                                'type' => 'number',
                                'pro' => true,
                                'label' => esc_html__('Certificate subtitle font size (px)', 'masterstudy-lms-learning-management-system'),
                                'columns' => '50',
                                'value' => 40
                            ),
                            /*TEXT*/
                            'certificate_text' => array(
                                'pro' => true,
                                'type' => 'textarea',
                                'label' => esc_html__('Certificate Text', 'masterstudy-lms-learning-management-system'),
                                'description' => esc_html__(
                                    'Available shortcodes: Username - {username}; Course name - {course}; User First name - {user_first_name}; User Last name - {user_last_name}',
                                    'masterstudy-lms-learning-management-system'),
                            ),
                            'certificate_text_color' => array(
                                'type' => 'color',
                                'pro' => true,
                                'label' => esc_html__('Certificate text color', 'masterstudy-lms-learning-management-system'),
                                'columns' => '50'
                            ),
                            'certificate_text_fsz' => array(
                                'type' => 'number',
                                'pro' => true,
                                'label' => esc_html__('Certificate text font size (px)', 'masterstudy-lms-learning-management-system'),
                                'columns' => '50',
                                'value' => 17
                            ),
                        )
                    ),
                    'addons' => array(
                        'name' => esc_html__('Addons', 'masterstudy-lms-learning-management-system'),
                        'fields' => array(
                            'addons' => array(
                                'pro' => true,
                                'type' => 'addons',
                                'label' => esc_html__('Masterstudy LMS PRO Addons', 'masterstudy-lms-learning-management-system'),
                            ),
                        )
                    ),
                    'payout' => array(
                        'name' => esc_html__('Payout', 'masterstudy-lms-learning-management-system'),
                        'fields' => array(
                            'payout' => array(
                                'pro' => true,
                                'type' => 'payout',
                                'label' => esc_html__('Masterstudy LMS PRO Payout', 'masterstudy-lms-learning-management-system'),
                            ),
                        )
                    ),
                    'gdpr' => array(
                        'name' => esc_html__('GDPR', 'masterstudy-lms-learning-management-system'),
                        'fields' => array(
                            'gdpr_warning' => array(
                                'type' => 'text',
                                'label' => esc_html__('GDPR Label', 'masterstudy-lms-learning-management-system'),
                                'value' => 'I agree with storage and handling of my data by this website.',
                                'columns' => 50
                            ),
                            'gdpr_page' => array(
                                'type' => 'select',
                                'label' => esc_html__('GDPR Privacy Policy Page', 'masterstudy-lms-learning-management-system'),
                                'options' => $pages,
                                'columns' => 50
                            ),
                        )
                    ),
                )
            )
        ));
    }

    function stm_lms_get_settings()
    {
        return get_option('stm_lms_settings', array());
    }

    function stm_lms_settings_page_view()
    {
        $metabox = $this->stm_lms_settings();
        $settings = $this->stm_lms_get_settings();

        foreach ($metabox['args']['stm_lms_settings'] as $section_name => $section) {
            foreach ($section['fields'] as $field_name => $field) {
                $default_value = (!empty($field['value'])) ? $field['value'] : '';
                $metabox['args']['stm_lms_settings'][$section_name]['fields'][$field_name]['value'] = (!empty($settings[$field_name])) ? $settings[$field_name] : $default_value;
            }
        }
        require_once(STM_LMS_PATH . '/settings/view/main.php');
    }

    function stm_save_settings()
    {

        check_ajax_referer('stm_save_settings', 'nonce');

        if(!current_user_can('manage_options')) die;

        if (empty($_REQUEST['name'])) die;
        $id = sanitize_text_field($_REQUEST['name']);
        $settings = array();
        $request_body = file_get_contents('php://input');
        if (!empty($request_body)) {
            $request_body = json_decode($request_body, true);
            foreach ($request_body as $section_name => $section) {
                foreach ($section['fields'] as $field_name => $field) {
                    $settings[$field_name] = $field['value'];
                }
            }
        }

        wp_send_json(update_option($id, $settings));
    }
}

new STM_LMS_Settings;