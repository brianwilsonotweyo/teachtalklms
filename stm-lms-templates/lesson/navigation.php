<?php
/**
 * @var $post_id
 * @var $item_id
 */

$course_meta = STM_LMS_Helpers::parse_meta_field($post_id);
if (!empty($course_meta['curriculum'])):
    $curriculum_full = explode(',', $course_meta['curriculum']);
    $curriculum = STM_LMS_Helpers::only_array_numbers($curriculum_full);

    $type = get_post_type($item_id);

    if ($type === 'stm-quizzes') {
        if (STM_LMS_Quiz::quiz_passed($item_id)) {
            $completed = 'completed';
            $completed_label = esc_html__('Passed', 'masterstudy-lms-learning-management-system');
        } else {
            $completed_label = $completed = '';
        }
    } else {
        $completed = (STM_LMS_Lesson::is_lesson_completed('', $post_id, $item_id)) ? 'completed' : 'uncompleted';
        $completed_label = (!empty($completed)) ? esc_html__('Complete', 'masterstudy-lms-learning-management-system') : esc_html__('Completed', 'masterstudy-lms-learning-management-system');
    }

    if (in_array($item_id, $curriculum)) {
        $current_lesson_id = array_search($item_id, $curriculum);
        $section = STM_LMS_Lesson::get_lesson_info($curriculum_full, $item_id);
        $prev_lesson = (!empty($curriculum[$current_lesson_id - 1])) ? $curriculum[$current_lesson_id - 1] : '';
        $next_lesson = (!empty($curriculum[$current_lesson_id + 1])) ? $curriculum[$current_lesson_id + 1] : '';
    } ?>

    <div class="stm-lms-lesson_navigation heading_font <?php echo esc_attr($completed); ?>"
         data-completed="<?php esc_html_e('Completed', 'masterstudy-lms-learning-management-system'); ?>">

        <div class="stm-lms-lesson_navigation_side stm-lms-lesson_navigation_prev">
            <?php if (!empty($prev_lesson)):
                $prev_section = STM_LMS_Lesson::get_lesson_info($curriculum_full, $prev_lesson); ?>
                <a href="<?php echo esc_url(STM_LMS_Lesson::get_lesson_url($post_id, $prev_lesson)) ?>">
                    <i class="lnr lnr-chevron-left"></i>
                    <?php if (!empty($prev_section['text'])): ?>
                        <span class="stm_lms_section_text"><?php echo sanitize_text_field($prev_section['text']); ?></span>
                    <?php endif; ?>
                    <span><?php echo sanitize_text_field(get_the_title($prev_lesson)); ?></span>
                </a>
            <?php endif; ?>
        </div>

        <?php if (!empty($completed_label)): ?>
            <div
                    <?php echo apply_filters('stm_lms_navigation_complete_atts', '', $item_id); ?>
                    class="<?php echo apply_filters('stm_lms_navigation_complete_class', 'stm-lms-lesson_navigation_complete', $item_id); ?>">
                <a href="#" class="btn btn-default stm_lms_complete_lesson <?php echo esc_attr($completed); ?>"
                   data-course="<?php echo intval($post_id) ?>"
                   data-lesson="<?php echo intval($item_id); ?>">
                    <span><?php echo sanitize_text_field($completed_label); ?></span>
                </a>
            </div>
        <?php endif; ?>

        <div class="stm-lms-lesson_navigation_side stm-lms-lesson_navigation_next">
            <?php if (!empty($next_lesson)):
                $next_section = STM_LMS_Lesson::get_lesson_info($curriculum_full, $next_lesson); ?>
                <a href="<?php echo esc_url(STM_LMS_Lesson::get_lesson_url($post_id, $next_lesson)) ?>">
                    <?php if (!empty($next_section['text'])): ?>
                        <span class="stm_lms_section_text"><?php echo sanitize_text_field($next_section['text']); ?></span>
                    <?php endif; ?>
                    <span><?php echo sanitize_text_field(get_the_title($next_lesson)); ?></span>
                    <i class="lnr lnr-chevron-right"></i>
                </a>
            <?php endif; ?>
        </div>

    </div>

<?php endif;