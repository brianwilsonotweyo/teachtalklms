<?php
if (!empty($_GET['stm_lms_fix_reviews_all'])) {
    add_action('init', 'stm_lms_fix_reviews_all');
}

function stm_lms_fix_reviews_all()
{

    if (!current_user_can('manage_options')) die;

    $args = array(
        'post_type' => 'stm-reviews',
        'posts_per_page' => '-1',
    );

    $reviews = array();

    $q = new WP_Query($args);

    if ($q->have_posts()) {
        while ($q->have_posts()) {
            $q->the_post();
            $post_id = get_the_ID();

            $course = get_post_meta($post_id, 'review_course', true);
            $mark = get_post_meta($post_id, 'review_mark', true);
            $user = get_post_meta($post_id, 'review_user', true);

            $transient_name = STM_LMS_Instructor::transient_name(get_post_field('post_author', $course), 'rating');
            delete_transient($transient_name);

            if (!empty($mark) and !empty($course) and !empty($user)) {
                $marks = get_post_meta($course, 'course_marks', true);
                if (empty($marks)) $marks = array();
                $marks[$user] = $mark;

                $rates = STM_LMS_Course::course_average_rate($marks);

                update_post_meta($course, 'course_mark_average', $rates['average']);
                update_post_meta($course, 'course_marks', $marks);
            }

            $reviews[$course] = $marks;
        }
    }

    stm_lms_update_post_reviews($reviews);
}

if (!empty($_GET['stm_lms_fix_reviews'])) {
    add_action('init', 'stm_lms_update_post_reviews');
}

function stm_lms_update_post_reviews($reviews = '')
{

    if (!current_user_can('manage_options')) die;

    $args = array(
        'post_type' => 'stm-courses',
        'posts_per_page' => '-1',
    );

    $q = new WP_Query($args);

    if ($q->have_posts()) {
        while ($q->have_posts()) {
            $q->the_post();
            $post_id = get_the_ID();
            if (empty($reviews)) {
                $marks = get_post_meta($post_id, 'course_marks', true);
            } else {
                $marks = (!empty($reviews[$post_id])) ? $reviews[$post_id] : array();
            }

            $rates = STM_LMS_Course::course_average_rate($marks);
            update_post_meta($post_id, 'course_mark_average', $rates['average']);
            update_post_meta($post_id, 'course_marks', $marks);
        }
    }

    die('Rate Fixed');
}

if (!empty($_GET['set_random_rating'])) {
    add_action('init', function () {

        if (!current_user_can('manage_options')) die;

        $user_args = array(
            'role' => STM_LMS_Instructor::role(),
            'number' => 10
        );

        $user_query = new WP_User_Query($user_args);

        if (!empty($user_query->get_results())) {
            foreach ($user_query->get_results() as $user) {
                $user_id = $user->ID;

                update_user_meta($user_id, 'average_rating', rand(3, 5));
                update_user_meta($user_id, 'sum_rating', rand(30, 50));
                update_user_meta($user_id, 'total_reviews', 10);

            }
        }

        die;
    });
}