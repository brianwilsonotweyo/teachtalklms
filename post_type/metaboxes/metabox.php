<?php

if (!defined('ABSPATH')) exit; //Exit if accessed directly


class STM_Metaboxes
{

    function __construct()
    {
        add_action('add_meta_boxes', array($this, 'stm_lms_register_meta_boxes'));

        add_action('admin_enqueue_scripts', array($this, 'stm_lms_scripts'));

        add_action('save_post', array($this, 'stm_lms_save'), 10, 3);

        add_action('wp_ajax_stm_curriculum', array($this, 'stm_search_posts'));

        add_action('wp_ajax_stm_manage_posts', array($this, 'manage_posts'));

        add_action('wp_ajax_stm_lms_change_post_status', array($this, 'change_status'));

        add_action('wp_ajax_stm_curriculum_create_item', array($this, 'stm_curriculum_create_item'));

        add_action('wp_ajax_stm_curriculum_get_item', array($this, 'stm_curriculum_get_item'));

        add_action('wp_ajax_stm_save_questions', array($this, 'stm_save_questions'));

        add_action('wp_ajax_stm_save_title', array($this, 'stm_save_title'));
    }

    function boxes()
    {
        return apply_filters('stm_lms_boxes', array(
            'stm_courses_curriculum' => array(
                'post_type' => array('stm-courses'),
                'label' => esc_html__('Course curriculum', 'masterstudy-lms-learning-management-system'),
            ),
            'stm_courses_settings' => array(
                'post_type' => array('stm-courses'),
                'label' => esc_html__('Course Settings', 'masterstudy-lms-learning-management-system'),
            ),
            'stm_lesson_settings' => array(
                'post_type' => array('stm-lessons'),
                'label' => esc_html__('Lesson Settings', 'masterstudy-lms-learning-management-system'),
            ),
            'stm_quiz_questions' => array(
                'post_type' => array('stm-quizzes'),
                'label' => esc_html__('Quiz Questions', 'masterstudy-lms-learning-management-system'),
            ),
            'stm_quiz_settings' => array(
                'post_type' => array('stm-quizzes'),
                'label' => esc_html__('Quiz Settings', 'masterstudy-lms-learning-management-system'),
            ),
            'stm_question_settings' => array(
                'post_type' => array('stm-questions'),
                'label' => esc_html__('Question Settings', 'masterstudy-lms-learning-management-system'),
            ),
            'stm_reviews' => array(
                'post_type' => array('stm-reviews'),
                'label' => esc_html__('Review info', 'masterstudy-lms-learning-management-system'),
            ),
            'stm_order_info' => array(
                'post_type' => array('stm-orders'),
                'label' => esc_html__('Order info', 'masterstudy-lms-learning-management-system'),
            ),
        ));
    }

    function get_users()
    {
        $users = array(
            '' => esc_html__('Choose User', 'masterstudy-lms-learning-management-system')
        );

        if (!is_admin()) return $users;

        $users_data = get_users();
        foreach ($users_data as $user) {
            $users[$user->ID] = $user->data->user_nicename;
        }

        return $users;
    }

    function fields()
    {
        $users = $this->get_users();

        $courses = (class_exists('STM_LMS_Settings')) ? STM_LMS_Settings::stm_get_post_type_array('stm-courses') : array();

        return apply_filters('stm_lms_fields', array(
            'stm_courses_curriculum' => array(
                'section_curriculum' => array(
                    'name' => esc_html__('Curriculum', 'masterstudy-lms-learning-management-system'),
                    'fields' => array(
                        'curriculum' => array(
                            'type' => 'post_type_repeat',
                            'post_type' => array('stm-lessons', 'stm-quizzes'),
                            'sanitize' => 'stm_lms_sanitize_curriculum'
                        ),
                    )
                )
            ),
            'stm_courses_settings' => array(
                'section_settings' => array(
                    'name' => esc_html__('Settings', 'masterstudy-lms-learning-management-system'),
                    'fields' => array(
                        'featured' => array(
                            'type' => 'checkbox',
                            'label' => esc_html__('Featured Course', 'masterstudy-lms-learning-management-system'),
                        ),
                        'views' => array(
                            'type' => 'number',
                            'label' => esc_html__('Course Views', 'masterstudy-lms-learning-management-system'),
                            'sanitize' => 'stm_lms_save_number'
                        ),
                        'level' => array(
                            'type' => 'select',
                            'label' => esc_html__('Course Level', 'masterstudy-lms-learning-management-system'),
                            'options' => array(
                                'beginner' => esc_html__('Beginner', 'masterstudy-lms-learning-management-system'),
                                'intermediate' => esc_html__('Intermediate', 'masterstudy-lms-learning-management-system'),
                                'advanced' => esc_html__('Advanced', 'masterstudy-lms-learning-management-system'),
                            )
                        ),
                        'current_students' => array(
                            'type' => 'number',
                            'label' => esc_html__('Current students', 'masterstudy-lms-learning-management-system'),
                            'sanitize' => 'stm_lms_save_number'
                        ),
//						'featured_course'   => array(
//							'type'  => 'checkbox',
//							'label' => esc_html__('Featured Course', 'masterstudy-lms-learning-management-system'),
//						),
//						'external_buy_link' => array(
//							'type'  => 'text',
//							'label' => esc_html__('External Buy link', 'masterstudy-lms-learning-management-system'),
//						),
                        'duration_info' => array(
                            'type' => 'text',
                            'label' => esc_html__('Duration info', 'masterstudy-lms-learning-management-system'),
                        ),
                        'video_duration' => array(
                            'type' => 'text',
                            'label' => esc_html__('Video Duration', 'masterstudy-lms-learning-management-system'),
                        ),
                        'skill_level' => array(
                            'type' => 'select',
                            'label' => esc_html__('Skill level', 'masterstudy-lms-learning-management-system'),
                            'options' => array(
                                '' => esc_html__('No skill required', 'masterstudy-lms-learning-management-system'),
                                'beginner' => esc_html__('Beginner', 'masterstudy-lms-learning-management-system'),
                                'medium' => esc_html__('Medium', 'masterstudy-lms-learning-management-system'),
                                'advanced' => esc_html__('Advanced', 'masterstudy-lms-learning-management-system'),
                            )
                        ),
                        'retake' => array(
                            'type' => 'checkbox',
                            'label' => esc_html__('Retake Course', 'masterstudy-lms-learning-management-system'),
                        ),
                        'status' => array(
                            'type' => 'select',
                            'label' => esc_html__('Status', 'masterstudy-lms-learning-management-system'),
                            'options' => array(
                                '' => esc_html__('No status', 'masterstudy-lms-learning-management-system'),
                                'hot' => esc_html__('Hot', 'masterstudy-lms-learning-management-system'),
                                'new' => esc_html__('New', 'masterstudy-lms-learning-management-system'),
                                'special' => esc_html__('Special', 'masterstudy-lms-learning-management-system'),
                            )
                        ),
                        'status_dates' => array(
                            'type' => 'dates',
                            'label' => esc_html__('Status Dates', 'masterstudy-lms-learning-management-system'),
                            'sanitize' => 'stm_lms_save_dates',
                            'dependency' => array(
                                'key' => 'status',
                                'value' => 'not_empty'
                            )
                        ),
                    )
                ),
                'section_accessibility' => array(
                    'name' => esc_html__('Accessibility', 'masterstudy-lms-learning-management-system'),
                    'fields' => array(
                        'price' => array(
                            'type' => 'number',
                            'label' => esc_html__('Course Price (leave blank to make the course free)', 'masterstudy-lms-learning-management-system'),
                            'sanitize' => 'stm_lms_save_number'
                        ),
                        'sale_price' => array(
                            'type' => 'number',
                            'label' => esc_html__('Sale Price', 'masterstudy-lms-learning-management-system'),
                            'sanitize' => 'stm_lms_save_number'
                        ),
                        'sale_price_dates' => array(
                            'type' => 'dates',
                            'label' => esc_html__('Sale Price Dates', 'masterstudy-lms-learning-management-system'),
                            'sanitize' => 'stm_lms_save_dates',
                            'dependency' => array(
                                'key' => 'sale_price',
                                'value' => 'not_empty'
                            ),
                            'pro' => true,
                        ),
                        'not_membership' => array(
                            'type' => 'checkbox',
                            'label' => esc_html__('Not included in membership', 'masterstudy-lms-learning-management-system'),
                        ),
                        'affiliate_course' => array(
                            'type' => 'checkbox',
                            'label' => esc_html__('Affiliate course', 'masterstudy-lms-learning-management-system'),
                            'pro' => true,
                        ),
                        'affiliate_course_text' => array(
                            'type' => 'text',
                            'label' => esc_html__('Button Text', 'masterstudy-lms-learning-management-system'),
                            'dependency' => array(
                                'key' => 'affiliate_course',
                                'value' => 'not_empty'
                            ),
                            'pro' => true,
                        ),
                        'affiliate_course_link' => array(
                            'type' => 'text',
                            'label' => esc_html__('Button Link', 'masterstudy-lms-learning-management-system'),
                            'dependency' => array(
                                'key' => 'affiliate_course',
                                'value' => 'not_empty'
                            ),
                            'pro' => true,
                        ),
                    )
                ),
                'section_announcement' => array(
                    'name' => esc_html__('Announcement', 'masterstudy-lms-learning-management-system'),
                    'fields' => array(
                        'announcement' => array(
                            'type' => 'editor',
                            'label' => esc_html__('Announcement', 'masterstudy-lms-learning-management-system'),
                        ),
                    )
                ),
                'section_faq' => array(
                    'name' => esc_html__('FAQ', 'masterstudy-lms-learning-management-system'),
                    'fields' => array(
                        'faq' => array(
                            'type' => 'faq',
                            'label' => esc_html__('FAQ', 'masterstudy-lms-learning-management-system'),
                        ),
                    )
                ),
            ),
            'stm_lesson_settings' => array(
                'section_lesson_settings' => array(
                    'name' => esc_html__('Lesson Settings', 'masterstudy-lms-learning-management-system'),
                    'fields' => array(
                        'type' => array(
                            'type' => 'select',
                            'label' => esc_html__('Lesson type', 'masterstudy-lms-learning-management-system'),
                            'options' => array(
                                'text' => esc_html__('Text', 'masterstudy-lms-learning-management-system'),
                                'video' => esc_html__('Video', 'masterstudy-lms-learning-management-system'),
                                'slide' => esc_html__('Slide', 'masterstudy-lms-learning-management-system'),
                            ),
                            'value' => 'text'
                        ),
                        'duration' => array(
                            'type' => 'text',
                            'label' => esc_html__('Lesson duration', 'masterstudy-lms-learning-management-system'),
                        ),
                        'preview' => array(
                            'type' => 'checkbox',
                            'label' => esc_html__('Lesson preview', 'masterstudy-lms-learning-management-system'),
                        ),
                        'lesson_excerpt' => array(
                            'type' => 'editor',
                            'label' => esc_html__('Lesson Frontend description', 'masterstudy-lms-learning-management-system'),
                        ),
                        'lesson_video_poster' => array(
                            'type' => 'image',
                            'label' => esc_html__('Lesson video poster', 'masterstudy-lms-learning-management-system'),
                        ),
                        'lesson_video_url' => array(
                            'type' => 'text',
                            'label' => esc_html__('Lesson video URL', 'masterstudy-lms-learning-management-system'),
                        ),
                    )
                )
            ),
            'stm_quiz_questions' => array(
                'section_questions' => array(
                    'name' => esc_html__('Questions', 'masterstudy-lms-learning-management-system'),
                    'fields' => array(
                        'questions' => array(
                            'type' => 'questions',
                            'label' => esc_html__('Questions', 'masterstudy-lms-learning-management-system'),
                            'post_type' => array('stm-questions')
                        ),
                    )
                )
            ),
            'stm_quiz_settings' => array(
                'section_quiz_settings' => array(
                    'name' => esc_html__('Quiz Settings', 'masterstudy-lms-learning-management-system'),
                    'fields' => array(
                        'lesson_excerpt' => array(
                            'type' => 'editor',
                            'label' => esc_html__('Quiz Frontend description', 'masterstudy-lms-learning-management-system'),
                        ),
                        'duration' => array(
                            'type' => 'duration',
                            'label' => esc_html__('Quiz duration', 'masterstudy-lms-learning-management-system'),
                        ),
                        'duration_measure' => array(
                            'type' => 'not_exist',
                        ),
                        'correct_answer' => array(
                            'type' => 'checkbox',
                            'label' => esc_html__('Show correct answer', 'masterstudy-lms-learning-management-system'),
                        ),
                        'passing_grade' => array(
                            'type' => 'number',
                            'label' => esc_html__('Passing grade (%)', 'masterstudy-lms-learning-management-system'),
                        ),
                        're_take_cut' => array(
                            'type' => 'number',
                            'label' => esc_html__('Points total cut after re-take (%)', 'masterstudy-lms-learning-management-system'),
                        ),
                    )
                )
            ),
            'stm_question_settings' => array(
                'section_question_settings' => array(
                    'name' => esc_html__('Question Settings', 'masterstudy-lms-learning-management-system'),
                    'fields' => array(
                        'type' => array(
                            'type' => 'select',
                            'label' => esc_html__('Question type', 'masterstudy-lms-learning-management-system'),
                            'options' => array(
                                'single_choice' => esc_html__('Single choice', 'masterstudy-lms-learning-management-system'),
                                'multi_choice' => esc_html__('Multi choice', 'masterstudy-lms-learning-management-system'),
                                'true_false' => esc_html__('True or False', 'masterstudy-lms-learning-management-system'),
                                'item_match' => esc_html__('Item Match', 'masterstudy-lms-learning-management-system'),
                                'keywords' => esc_html__('Keywords', 'masterstudy-lms-learning-management-system'),
                            ),
                            'value' => 'single_choice'
                        ),
                        'answers' => array(
                            'type' => 'answers',
                            'label' => esc_html__('Answers', 'masterstudy-lms-learning-management-system'),
                            'requirements' => 'type'
                        ),
                        'question_explanation' => array(
                            'type' => 'textarea',
                            'label' => esc_html__('Question result explanation', 'masterstudy-lms-learning-management-system'),
                        ),
                    )
                )
            ),
            'stm_reviews' => array(
                'section_data' => array(
                    'name' => esc_html__('Review info', 'masterstudy-lms-learning-management-system'),
                    'fields' => array(
                        'review_course' => array(
                            'type' => 'select',
                            'label' => esc_html__('Course Reviewed', 'masterstudy-lms-learning-management-system'),
                            'options' => $courses,
                        ),
                        'review_user' => array(
                            'type' => 'select',
                            'label' => esc_html__('User Reviewed', 'masterstudy-lms-learning-management-system'),
                            'options' => $users,
                        ),
                        'review_mark' => array(
                            'type' => 'select',
                            'label' => esc_html__('User Review mark', 'masterstudy-lms-learning-management-system'),
                            'options' => array(
                                '5' => '5',
                                '4' => '4',
                                '3' => '3',
                                '2' => '2',
                                '1' => '1'
                            )
                        ),
                    )
                )
            ),
            'stm_order_info' => array(
                'order_info' => array(
                    'name' => esc_html__('Order', 'masterstudy-lms-learning-management-system'),
                    'fields' => array(
                        'order' => array(
                            'type' => 'order',
                        ),
                    )
                )
            ),
        ));
    }

    function get_fields($metaboxes)
    {

        $fields = array();
        foreach ($metaboxes as $metabox_name => $metabox) {
            foreach ($metabox as $section) {
                foreach ($section['fields'] as $field_name => $field) {
                    $sanitize = (!empty($field['sanitize'])) ? $field['sanitize'] : 'stm_lms_save_field';

                    $fields[$field_name] = !empty($_POST[$field_name]) ? call_user_func(array($this, $sanitize), $_POST[$field_name], $field_name) : '';

                }
            }
        }

        return $fields;
    }

    function stm_lms_save_field($value)
    {
        return $value;
    }

    function stm_lms_save_number($value)
    {
        return floatval($value);
    }

    function stm_lms_sanitize_curriculum($value)
    {
        $value = str_replace('stm_lms_amp', '&', $value);
        return sanitize_text_field($value);
    }

    function stm_lms_save_dates($value, $field_name)
    {
        global $post_id;

        $dates = explode(',', $value);
        if (!empty($dates) and count($dates) > 1) {
            update_post_meta($post_id, $field_name . '_start', $dates[0]);
            update_post_meta($post_id, $field_name . '_end', $dates[1]);
        }

        return $value;
    }

    function stm_lms_register_meta_boxes()
    {
        $boxes = $this->boxes();
        foreach ($boxes as $box_id => $box) {
            $box_name = $box['label'];
            add_meta_box($box_id, $box_name, array($this, 'stm_lms_display_callback'), $box['post_type'], 'normal', 'high', $this->fields());
        }
    }

    function stm_lms_display_callback($post, $metabox)
    {
        $meta = $this->convert_meta($post->ID);
        foreach ($metabox['args'] as $metabox_name => $metabox_data) {
            foreach ($metabox_data as $section_name => $section) {
                foreach ($section['fields'] as $field_name => $field) {
                    $default_value = (!empty($field['value'])) ? $field['value'] : '';
                    $value = (isset($meta[$field_name])) ? $meta[$field_name] : $default_value;
                    if (!empty($value)) {
                        switch ($field['type']) {
                            case 'dates' :
                                $value = explode(',', $value);
                                break;
                            case 'answers' :
                                $value = unserialize($value);
                                break;
                        }
                    }
                    $metabox['args'][$metabox_name][$section_name]['fields'][$field_name]['value'] = $value;
                }
            }
        }
        include STM_LMS_PATH . '/post_type/metaboxes/metabox-display.php';
    }

    function convert_meta($post_id)
    {
        $meta = get_post_meta($post_id);
        $metas = array();
        foreach ($meta as $meta_name => $meta_value) {
            $metas[$meta_name] = $meta_value[0];
        }

        return $metas;
    }

    function stm_lms_scripts($hook)
    {
        $v = time();
        $base = STM_LMS_URL . '/post_type/metaboxes/assets/';
        $assets = STM_LMS_URL . 'assets';

        wp_enqueue_media();
        wp_enqueue_script('vue.js', $base . 'js/vue.min.js', array('jquery'), $v);
        wp_enqueue_script('vue-resource.js', $base . 'js/vue-resource.min.js', array('vue.js'), $v);
        wp_enqueue_script('vue2-datepicker.js', $base . 'js/vue2-datepicker.min.js', array('vue.js'), $v);
        wp_enqueue_script('vue-select.js', $base . 'js/vue-select.js', array('vue.js'), $v);
        wp_enqueue_script('vue2-editor.js', $base . 'js/vue2-editor.min.js', array('vue.js'), $v);
        wp_enqueue_script('vue2-color.js', $base . 'js/vue-color.min.js', array('vue.js'), $v);
        wp_enqueue_script('sortable.js', $base . 'js/sortable.min.js', array('vue.js'), $v);
        wp_enqueue_script('vue-draggable.js', $base . 'js/vue-draggable.min.js', array('sortable.js'), $v);
        wp_enqueue_script('stm_lms_mixins.js', $base . 'js/mixins.js', array('vue.js'), $v);
        wp_enqueue_script('stm_lms_metaboxes.js', $base . 'js/metaboxes.js', array('vue.js'), $v);
        wp_enqueue_script('stm-user-search', $base . 'js/stm-user-search.js', array('vue.js'), $v);

        wp_enqueue_style('stm-lms-metaboxes.css', $base . 'css/main.css', array(), $v);
        wp_enqueue_style('stm-lms-icons', STM_LMS_URL . 'assets/icons/style.css', array(), $v);
        wp_enqueue_style('linear-icons', $base . 'css/linear-icons.css', array('stm-lms-metaboxes.css'), $v);
        wp_enqueue_style('font-awesome-min', $assets . '/vendors/font-awesome.min.css', NULL, $v, 'all');
    }

    function stm_lms_post_types()
    {
        return apply_filters('stm_lms_post_types', array(
            'stm-courses',
            'stm-lessons',
            'stm-quizzes',
            'stm-questions',
            'stm-reviews',
        ));
    }

    function stm_lms_save($post_id, $post)
    {

        $post_type = get_post_type($post_id);

        if (!in_array($post_type, $this->stm_lms_post_types())) return;

        if (!empty($_POST) and !empty($_POST['action']) and $_POST['action'] === 'editpost') {

            $fields = $this->get_fields($this->fields());

            foreach ($fields as $field_name => $field_value) {
                update_post_meta($post_id, $field_name, $field_value);
            }
        }


    }

    function stm_search_posts()
    {

        check_ajax_referer('stm_curriculum', 'nonce');

        $r = array();

        $args = array(
            'posts_per_page' => 10,
        );

        if (!empty($_GET['post_types'])) {
            $args['post_type'] = explode(',', sanitize_text_field($_GET['post_types']));
        }

        if (!empty($_GET['s'])) {
            $args['s'] = sanitize_text_field($_GET['s']);
        }

        if (!empty($_GET['ids'])) {
            $args['post__in'] = explode(',', sanitize_text_field($_GET['ids']));
        }

        if (!empty($_GET['exclude_ids'])) {
            $args['post__not_in'] = explode(',', sanitize_text_field($_GET['exclude_ids']));
        }

        if (!empty($_GET['orderby'])) {
            $args['orderby'] = sanitize_text_field($_GET['orderby']);
        }

        if (!empty($_GET['posts_per_page'])) {
            $args['posts_per_page'] = sanitize_text_field($_GET['posts_per_page']);
        }

        $author = STM_LMS_User::get_current_user('', true);

        if (!in_array('administrator', $author['roles'])) {
            $args['author'] = $author['id'];
        }

        $q = new WP_Query($args);
        if ($q->have_posts()) {
            while ($q->have_posts()) {
                $q->the_post();

                $response = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'post_type' => get_post_type(get_the_ID())
                );

                if (in_array('stm-questions', $args['post_type'])) {
                    $response = array_merge($response, $this->question_fields($response['id']));
                }

                $r[] = $response;
            }

            wp_reset_postdata();
        }

        $insert_sections = array();
        foreach ($args['post__in'] as $key => $post) {
            if (!is_numeric($post)) {
                $insert_sections[$key] = array('id' => $post, 'title' => $post);
            }
        }

        foreach ($insert_sections as $position => $inserted) {
            array_splice($r, $position, 0, array($inserted));
        }

        wp_send_json($r);
    }

    function get_question_fields()
    {
        return array(
            'type' => array(
                'default' => 'single_choice',
            ),
            'answers' => array(
                'default' => array(),
            ),
            'question' => array(),
            'question_explanation' => array(),
            'question_hint' => array(),
        );
    }

    function question_fields($post_id)
    {
        $fields = $this->get_question_fields();
        $meta = array();

        foreach ($fields as $field_key => $field) {
            $meta[$field_key] = get_post_meta($post_id, $field_key, true);
            $default = (isset($field['default'])) ? $field['default'] : '';
            $meta[$field_key] = (!empty($meta[$field_key])) ? $meta[$field_key] : $default;
        }

        return $meta;
    }

    function stm_curriculum_create_item()
    {

        check_ajax_referer('stm_curriculum_create_item', 'nonce');

        $r = array();
        $available_post_types = array('stm-lessons', 'stm-quizzes', 'stm-questions');

        if (!empty($_GET['post_type'])) $post_type = sanitize_text_field($_GET['post_type']);
        if (!empty($_GET['title'])) $title = sanitize_text_field($_GET['title']);

        /*Check if data passed*/
        if (empty($post_type) and empty($title)) return;

        /*Check if available post type*/
        if (!in_array($post_type, $available_post_types)) return;

        do_action('stm_lms_before_adding_item');

        $item = array(
            'post_type' => $post_type,
            'post_title' => wp_strip_all_tags($title),
            'post_status' => 'publish',
        );

        $r['id'] = wp_insert_post($item);
        $r['title'] = get_the_title($r['id']);
        $r['post_type'] = $post_type;

        if ($post_type == 'stm-questions') {
            $r = array_merge($r, $this->question_fields($r['id']));
        }

        wp_send_json($r);

    }

    function stm_curriculum_get_item()
    {

        check_ajax_referer('stm_curriculum_get_item', 'nonce');

        $post_id = intval($_GET['id']);
        $r = array();

        $r['meta'] = STM_LMS_Helpers::simplify_meta_array(get_post_meta($post_id));
        if (!empty($r['meta']['lesson_video_poster'])) {
            $image = wp_get_attachment_image_src($r['meta']['lesson_video_poster'], 'img-870-440');
            if (!empty($image[0])) $r['meta']['lesson_video_poster_url'] = $image[0];
        }
        $r['content'] = get_post_field('post_content', $post_id);

        wp_send_json($r);
    }

    function stm_save_questions()
    {

        check_ajax_referer('stm_save_questions', 'nonce');

        $r = array();
        $request_body = file_get_contents('php://input');

        if (!empty($request_body)) {

            $fields = $this->get_question_fields();


            $data = json_decode($request_body, true);

            foreach ($data as $question) {

                if (empty($question['id'])) continue;
                $post_id = $question['id'];

                foreach ($fields as $field_key => $field) {
                    if (!empty($question[$field_key])) {
                        foreach ($question[$field_key] as $index => $value) {
                            $question[$field_key][$index]['text'] = sanitize_text_field($value['text']);
                        }

                        $r[$field_key] = update_post_meta($post_id, $field_key, $question[$field_key]);
                    }
                }
            }
        }
        wp_send_json($r);
    }

    function stm_save_title()
    {

        check_ajax_referer('stm_save_title', 'nonce');

        if (empty($_GET['id']) and !empty($_GET['title'])) return false;

        $post = array(
            'ID' => intval($_GET['id']),
            'post_title' => sanitize_text_field($_GET['title']),
        );

        wp_update_post($post);

        wp_send_json($post);
    }

    function manage_posts()
    {

        check_ajax_referer('stm_manage_posts', 'nonce');

        $r = array(
            'posts' => array()
        );

        $args = array(
            'posts_per_page' => 10,
        );

        if (!empty($_GET['post_types'])) {
            $args['post_type'] = explode(',', sanitize_text_field($_GET['post_types']));
        }

        $args['post_status'] = (!empty($_GET['post_status'])) ? sanitize_text_field($_GET['post_status']) : 'all';
        $offset = (!empty($_GET['page'])) ? intval($_GET['page'] - 1) : 0;
        if (!empty($offset)) $args['offset'] = $offset * $args['posts_per_page'];

        if (!empty($_GET['meta'])) {
            $args['meta_query'] = array(
                array(
                    'key' => sanitize_text_field($_GET['meta']),
                    'compare' => 'EXISTS'
                )
            );
        }


        $r['args'] = $args;

        $q = new WP_Query($args);
        $r['total'] = intval($q->found_posts);
        $r['per_page'] = $args['posts_per_page'];
        $r['offset'] = $args['offset'];

        if ($q->have_posts()) {
            while ($q->have_posts()) {
                $q->the_post();

                $response = array(
                    'id' => get_the_ID(),
                    'title' => get_the_title(),
                    'url' => get_the_permalink(),
                    'status' => get_post_status(),
                    'edit_link' => get_edit_post_link(get_the_ID(), 'value'),
                    'loading' => false,
                    'loading_text' => ''
                );

                $r['posts'][] = $response;
            }

            wp_reset_postdata();
        }

        wp_send_json($r);
    }

    function change_status()
    {

        check_ajax_referer('stm_lms_change_post_status', 'nonce');

        if (!empty($_GET['post_id']) and !empty($_GET['status'])) {

            remove_action('save_post', array($this, 'stm_lms_save'), 10);
            $post_id = intval($_GET['post_id']);
            $status = sanitize_text_field($_GET['status']);

            $post = array(
                'post_type' => 'stm-courses',
                'ID' => $post_id,
                'post_status' => $status,
            );
            wp_update_post($post);

            add_action('save_post', array($this, 'stm_lms_save'), 10);
            wp_send_json($status);
        }
        die;
    }
}

new STM_Metaboxes();

function stm_lms_metaboxes_deps($field, $section_name)
{
    $dependency = '';
    if (empty($field['dependency'])) return $dependency;

    $key = $field['dependency']['key'];
    $compare = $field['dependency']['value'];
    if ($compare === 'not_empty') {
        $dependency = "v-if=data['{$section_name}']['fields']['{$key}']['value']";
    } else {
        $dependency = "v-if=data['{$section_name}']['fields']['{$key}']['value'] == '{$compare}'";
    }

    return $dependency;
}