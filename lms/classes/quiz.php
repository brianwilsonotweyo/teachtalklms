<?php

STM_LMS_Quiz::init();

class STM_LMS_Quiz
{

    public static function init()
    {
        add_action('wp_ajax_stm_lms_start_quiz', 'STM_LMS_Quiz::start_quiz');

        add_action('wp_ajax_stm_lms_user_answers', 'STM_LMS_Quiz::user_answers');
        add_action('wp_ajax_nopriv_stm_lms_user_answers', 'STM_LMS_Quiz::user_answers');
    }

    public static function get_quiz_end_time($quiz_id)
    {
        $user = STM_LMS_User::get_current_user();
        if (empty($user['id'])) die;

        $already_started = STM_LMS_Helpers::simplify_db_array(stm_lms_get_user_quizzes_time($user['id'], $quiz_id, array('end_time')));

        return $already_started;
    }

    public static function is_quiz_failed($item_id, $course_id)
    {
        $duration = STM_LMS_Quiz::get_quiz_duration($item_id);

        if (!empty($duration)) {
            $already_started = STM_LMS_Quiz::get_quiz_end_time($item_id);
            if (!empty($already_started['end_time'])) {
                $end_time = $already_started['end_time'];
                /*Quiz failed*/
                if (time() > $end_time) {
                    STM_LMS_Quiz::quiz_failed($item_id, $course_id);
                }
            }
        }
    }

    public static function quiz_failed($quiz_id, $course_id)
    {
        $user = STM_LMS_User::get_current_user();
        if (empty($user['id'])) die;
        $user_id = $user['id'];

        $progress = 0;
        $status = 'failed';

        $user_quiz = compact('user_id', 'course_id', 'quiz_id', 'progress', 'status');
        stm_lms_add_user_quiz($user_quiz);

        /*REMOVE TIMER*/
        stm_lms_get_delete_user_quiz_time($user_id, $quiz_id);
    }

    public static function start_quiz()
    {

        check_ajax_referer('start_quiz', 'nonce');

        if (empty($_GET['quiz_id'])) die;
        $quiz_id = intval($_GET['quiz_id']);
        $source = (!empty($_GET['source'])) ? intval($_GET['source']) : '';

        $user = apply_filters('user_answers__user_id', STM_LMS_User::get_current_user(), $source);
        if (empty($user['id'])) die;
        $user_id = $user['id'];

        $duration = STM_LMS_Quiz::get_quiz_duration($quiz_id);
        $already_started = STM_LMS_Helpers::simplify_db_array(stm_lms_get_user_quizzes_time($user['id'], $quiz_id, array('end_time')));

        $count_to = (!empty($duration)) ? time() + $duration : 0;

        if (empty($already_started)) {
            stm_lms_add_user_quiz_time(
                array(
                    'user_id' => $user_id,
                    'quiz_id' => $quiz_id,
                    'start_time' => time(),
                    'end_time' => $count_to
                )
            );
        } else {
            $count_to = $already_started['end_time'];
        }

        if (time() - $count_to > 0) {
            /*REMOVE TIMER*/
            stm_lms_get_delete_user_quiz_time($user_id, $quiz_id);
            /*Set NEW*/
            $count_to = time() + $duration;
            stm_lms_add_user_quiz_time(
                array(
                    'user_id' => $user_id,
                    'quiz_id' => $quiz_id,
                    'start_time' => time(),
                    'end_time' => $count_to
                )
            );
        }


        wp_send_json($count_to);


    }

    public static function user_answers()
    {

        check_ajax_referer('user_answers', 'nonce');

        $source = (!empty($_GET['source'])) ? intval($_GET['source']) : '';
        $user = apply_filters('user_answers__user_id', STM_LMS_User::get_current_user(), $source);
        /*Checking Current User*/
        if (!$user['id']) die;
        $user_id = $user['id'];
        $course_id = (!empty($_GET['course_id'])) ? intval($_GET['course_id']) : '';
        $course_id = apply_filters('user_answers__course_id', $course_id, $source);

        if (empty($course_id) or empty($_GET['quiz_id'])) die;
        $quiz_id = intval($_GET['quiz_id']);


        $progress = 0;
        $quiz_info = STM_LMS_Helpers::parse_meta_field($quiz_id);
        $total_questions = count(explode(',', $quiz_info['questions']));
        $single_question_score_percent = 100 / $total_questions;
        $cutting_rate = (!empty($quiz_info['re_take_cut'])) ? (100 - $quiz_info['re_take_cut']) / 100 : 1;
        $passing_grade = (!empty($quiz_info['passing_grade'])) ? $quiz_info['passing_grade'] : 0;

        $user_quizzes = stm_lms_get_user_quizzes($user_id, $quiz_id, array('user_quiz_id', 'progress'));
        $attempt_number = count($user_quizzes) + 1;
        $prev_answers = ($attempt_number !== 1) ? stm_lms_get_user_answers($user_id, $quiz_id, $attempt_number - 1, true, array('question_id')) : array();

        foreach ($_GET as $question_id => $value) {
            if (is_numeric($question_id)) {
                $question_id = intval($question_id);
                $answer = is_array($value) ? array_map('sanitize_text_field', $value) : sanitize_text_field($value);
                $user_answer = (is_array($answer)) ? implode(',', $answer) : $answer;
                $correct_answer = STM_LMS_Quiz::check_answer($question_id, $answer);

                if ($correct_answer) {
                    if ($attempt_number === 1 or STM_LMS_Helpers::in_array_r($question_id, $prev_answers)) {
                        $single_question_score = $single_question_score_percent;
                    } else {
                        $single_question_score = $single_question_score_percent * $cutting_rate;
                    }

                    $progress += $single_question_score;
                }

                $add_answer = compact('user_id', 'course_id', 'quiz_id', 'question_id', 'attempt_number', 'user_answer', 'correct_answer');
                stm_lms_add_user_answer($add_answer);
            }
        }

        /*Add user quiz*/
        $status = ($progress < $passing_grade) ? 'failed' : 'passed';
        $user_quiz = compact('user_id', 'course_id', 'quiz_id', 'progress', 'status');
        stm_lms_add_user_quiz($user_quiz);

        /*REMOVE TIMER*/
        stm_lms_get_delete_user_quiz_time($user_id, $quiz_id);

        if ($status == 'passed') STM_LMS_Course::update_course_progress($user_id, $course_id);
        $user_quiz['passed'] = $progress >= $passing_grade;
        $user_quiz['progress'] = round($user_quiz['progress'], 1);
        $user_quiz['url'] = '<a class="btn btn-default" href="' . STM_LMS_Course::item_url($course_id, $quiz_id) . '">' . esc_html__('Close', 'masterstudy-lms-learning-management-system') . '</a>';
        $user_quiz['url'] = apply_filters('user_answers__course_url', $user_quiz['url'], $source);


        wp_send_json($user_quiz);
    }

    public static function check_answer($question_id, $answer, $answers = array())
    {
        $correct = false;
        $answers = !empty($answers) ? $answers : get_post_meta($question_id, 'answers', true);

        if (empty($answers)) return false;

        $type = get_post_meta($question_id, 'type', true);

        foreach ($answers as $stored_answer) {

            switch ($type) {

                case 'multi_choice':
                    if (in_array($stored_answer['text'], $answer) and $stored_answer['isTrue']) {
                        $correct = true;
                    } else if (!in_array($stored_answer['text'], $answer) and $stored_answer['isTrue']) {
                        $correct = false;
                    } else if (in_array($stored_answer['text'], $answer) and !$stored_answer['isTrue']) {
                        $correct = false;
                    }
                    break;

                case 'item_match':
                    $answer = explode('[stm_lms_sep]', str_replace('[stm_lms_item_match]', '', $answer));

                    foreach ($answers as $i => $correct_answer) {
                        $correct = true;
                        if (strtolower($correct_answer['text']) !== strtolower($answer[$i])) {
                            $correct = false;
                            break;
                        };
                    }

                    return $correct;

                case 'keywords':

                    $answer = explode('[stm_lms_sep]', str_replace('[stm_lms_keywords]', '', $answer));

                    foreach ($answers as $i => $correct_answer) {
                        $correct = true;
                        if (strtolower($correct_answer['text']) !== strtolower($answer[$i])) {
                            $correct = false;
                            break;
                        };
                    }

                    return $correct;

                case 'fill_the_gap' :

                    if(!empty($answers[0]) and !empty($answers[0]['text'])) {
                        $text = $answers[0]['text'];
                        $matches = stm_lms_get_string_between($text, '|', '|');

                        foreach($matches as $i => $correct_answer) {
                            $correct = true;
                            if(empty($answer[$i])) return false;
                            if (strtolower($correct_answer) !== strtolower($answer[$i])) {
                                $correct = false;
                                break;
                            };
                        }

                        return $correct;
                    }

                    break;

                default:

                    if ($answer == $stored_answer['text'] and $stored_answer['isTrue']) {
                        $correct = true;
                    }
            }

        }

        return $correct;
    }

    public static function passing_grade($meta)
    {
        return (!empty($meta['passing_grade'])) ? $meta['passing_grade'] : 0;
    }

    public static function quiz_passed($quiz_id)
    {
        $source = STM_LMS_Helpers::current_screen();
        $user = apply_filters('user_answers__user_id', STM_LMS_User::get_current_user(), $source);
        if (empty($user['id'])) return false;
        $user_id = $user['id'];

        $last_quiz = stm_lms_get_user_last_quiz($user_id, $quiz_id, array('progress'));
        if (empty($last_quiz)) return false;
        $last_quiz = STM_LMS_Helpers::simplify_db_array($last_quiz);
        $passing_grade = STM_LMS_Quiz::passing_grade(STM_LMS_Helpers::parse_meta_field($quiz_id));

        return $last_quiz['progress'] >= $passing_grade;
    }

    public static function can_watch_answers($quiz_id)
    {
        $show_answers = get_post_meta($quiz_id, 'correct_answer', true);
        if (!empty($show_answers) and $show_answers === 'on') return true;
        return STM_LMS_Quiz::quiz_passed($quiz_id);
    }

    public static function answers_url()
    {
        $current_url = add_query_arg('show_answers', '1', STM_LMS_Helpers::get_current_url());
        return $current_url;
    }

    public static function show_answers($quiz_id)
    {
        if (!STM_LMS_Quiz::can_watch_answers($quiz_id)) return false;
        if (STM_LMS_Quiz::quiz_passed($quiz_id)) return true;
        return (!empty($_GET['show_answers']) and $_GET['show_answers']) ? true : false;
    }

    public static function get_quiz_duration($quiz_id)
    {
        $duration = get_post_meta($quiz_id, 'duration', true);
        if (empty($duration)) return 0;
        $duration_measure = get_post_meta($quiz_id, 'duration_measure', true);
        switch ($duration_measure) {
            case 'hours':
                $multiple = 60 * 60;
                break;
            case 'days':
                $multiple = 24 * 60 * 60;
                break;
            default:
                $multiple = 60;
        }

        return $duration * $multiple;
    }
}