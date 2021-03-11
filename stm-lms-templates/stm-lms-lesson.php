<?php if (!defined('ABSPATH')) exit; //Exit if accessed directly ?>

<?php
/**
 * @var $lesson
 * @var $course
 */

$lesson = parse_url($lesson);
$lesson = $lesson['path'];

$post_ids = explode('-', $lesson);
$post_id = $post_ids[0];
$item_id = $post_ids[1];

$is_previewed = (!empty($is_previewed)) ? $is_previewed : false;

$content_type = (get_post_type($item_id) == 'stm-lessons') ? 'lesson' : 'quiz';

$lesson_type = '';
if ($content_type === 'lesson') {
    $lesson_type = get_post_meta($item_id, 'type', true);
    stm_lms_register_style('lesson_' . $lesson_type);
}

STM_LMS_Templates::show_lms_template(
    'lesson/header',
    compact('post_id', 'item_id', 'is_previewed', 'content_type', 'lesson_type')
);

$custom_css = get_post_meta($item_id, '_wpb_shortcodes_custom_css', true);

stm_lms_register_style('lesson', array(), $custom_css);
do_action('stm_lms_template_main');

$has_access = STM_LMS_User::has_course_access($post_id, $item_id);
$has_preview = STM_LMS_Lesson::lesson_has_preview($item_id);
$is_previewed = STM_LMS_Lesson::is_previewed($post_id, $item_id);

if ($has_access or $has_preview):

    if(!$is_previewed) do_action('stm_lms_lesson_started', $post_id, $item_id);
    stm_lms_update_user_current_lesson($post_id, $item_id);

    ?>

    <div class="stm-lms-course__overlay"></div>

    <div class="stm-lms-wrapper <?php echo esc_attr(get_post_type($item_id)); ?>">

        <div class="stm-lms-course__curriculum">
            <?php STM_LMS_Templates::show_lms_template('lesson/curriculum', array('post_id' => $post_id, 'item_id' => $item_id)); ?>
        </div>

        <?php if (!$is_previewed): ?>
            <div class="stm-lms-course__sidebar_toggle">
                <i class="fa fa-question"></i>
            </div>
        <?php endif; ?>

        <div class="stm-lms-course__sidebar">
            <div class="stm-lesson_sidebar__close">
                <i class="lnr lnr-cross"></i>
            </div>
            <?php if (!$is_previewed): ?>
                <?php STM_LMS_Templates::show_lms_template('lesson/sidebar', compact('post_id', 'item_id', 'is_previewed')); ?>
            <?php endif; ?>
        </div>

        <?php $item_content = apply_filters('stm_lms_show_item_content', true, $post_id, $item_id); ?>

        <?php if ($item_content) : ?>
            <div id="stm-lms-lessons">
                <div class="stm-lms-course__content">

                    <?php STM_LMS_Templates::show_lms_template('lesson/content_top_wrapper_start', compact('lesson_type')); ?>
                    <?php STM_LMS_Templates::show_lms_template('lesson/content_top', compact('post_id', 'item_id')); ?>
                    <?php STM_LMS_Templates::show_lms_template('lesson/content_top_wrapper_end', compact('lesson_type')); ?>

                    <div class="stm-lms-course__content_wrapper">
                        <?php STM_LMS_Templates::show_lms_template('lesson/content_wrapper_start', compact('lesson_type')); ?>

                        <?php echo apply_filters('stm_lms_lesson_content', STM_LMS_Templates::load_lms_template(
                            'course/parts/' . $content_type,
                            compact('post_id', 'item_id', 'is_previewed')
                        ), $post_id, $item_id); ?>

                        <?php STM_LMS_Templates::show_lms_template('lesson/content_wrapper_end', compact('lesson_type')); ?>
                    </div>
                </div>
            </div>

        <?php endif; ?>

        <?php do_action('stm_lms_course_item_content', $post_id, $item_id); ?>

    </div>
<?php else:

    stm_lms_register_style('lesson_locked');
    stm_lms_register_script('lesson_locked', array(), false, "stm_lms_course_id = {$post_id};");

    ?>


    <div class="stm_lms_locked_lesson__overlay"></div>
    <div class="stm_lms_locked_lesson__popup">
        <div class="stm_lms_locked_lesson__popup_inner">
            <h3><?php esc_html_e('Hey there, great course, right? Do you like this course?', 'masterstudy-lms-learning-management-system'); ?></h3>
            <p>
                <?php printf(esc_html__('All of the most interesting lessons further. In order to continue you just need to purchase it for only %s', 'masterstudy-lms-learning-management-system'), STM_LMS_Helpers::display_price(STM_LMS_Course::get_course_price($post_id))); ?>
            </p>
            <?php STM_LMS_Templates::show_lms_template('global/buy-button', array('course_id' => $post_id, 'item_id' => $item_id)); ?>
            <a class="stm_lms_locked_lesson__popup_close" href="<?php echo esc_url(get_permalink($post_id)); ?>">
                <i class="lnricons-cross"></i>
            </a>
        </div>
    </div>


    <div class="stm-lms-course__overlay"></div>

    <div class="stm-lms-wrapper <?php echo esc_attr(get_post_type($item_id)); ?>">

        <div class="stm-lms-course__curriculum">
            <?php STM_LMS_Templates::show_lms_template('lesson/curriculum', array('post_id' => $post_id, 'item_id' => $item_id)); ?>
        </div>

        <?php if (!$is_previewed): ?>
            <div class="stm-lms-course__sidebar_toggle">
                <i class="fa fa-question"></i>
            </div>
        <?php endif; ?>

        <div class="stm-lms-course__sidebar">
            <div class="stm-lesson_sidebar__close">
                <i class="lnr lnr-cross"></i>
            </div>
            <?php if (!$is_previewed): ?>
                <?php STM_LMS_Templates::show_lms_template('lesson/sidebar', compact('post_id', 'item_id', 'is_previewed')); ?>
            <?php endif; ?>
        </div>

        <div id="stm-lms-lessons">
            <div class="stm-lms-course__content">

                <?php STM_LMS_Templates::show_lms_template('lesson/content_top_wrapper_start', compact('lesson_type')); ?>
                <?php STM_LMS_Templates::show_lms_template('lesson/content_top', compact('post_id', 'item_id')); ?>
                <?php STM_LMS_Templates::show_lms_template('lesson/content_top_wrapper_end', compact('lesson_type')); ?>

                <div class="stm-lms-course__content_wrapper">
                    <?php STM_LMS_Templates::show_lms_template('lesson/content_wrapper_start', compact('lesson_type')); ?>

                    <h4 class="text-center">
                        <?php esc_html_e('Lesson is locked. Please Buy course to proceed.', 'masterstudy-lms-learning-management-system'); ?>
                    </h4>

                    <?php STM_LMS_Templates::show_lms_template('lesson/content_wrapper_end', compact('lesson_type')); ?>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (!$is_previewed) STM_LMS_Templates::show_lms_template('lesson/navigation', compact('post_id', 'item_id')); ?>

<?php
STM_LMS_Templates::show_lms_template(
    'lesson/footer',
    compact('post_id', 'item_id', 'is_previewed')
); ?>