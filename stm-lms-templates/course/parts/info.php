<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>

<?php
/**
 * @var $course_id
 */

stm_lms_register_style('course_info');

$meta = STM_LMS_Helpers::parse_meta_field($course_id);
$meta_fields = array();

if(!empty($meta['current_students'])) {
	$meta_fields[esc_html__('Enrolled', 'masterstudy-lms-learning-management-system')] = array(
		'text' => sprintf(_n('%s student', '%s students', $meta['current_students'], 'masterstudy-lms-learning-management-system'), $meta['current_students']),
		'icon' => 'fa-icon-stm_icon_users'
	);
}

if(!empty($meta['duration_info'])) {
	$meta_fields[esc_html__('Duration', 'masterstudy-lms-learning-management-system')] = array(
		'text' => $meta['duration_info'],
		'icon' => 'fa-icon-stm_icon_clock'
	);
}

if(!empty($meta['curriculum'])) {
	$curriculum_info = STM_LMS_Course::curriculum_info($meta['curriculum']);
	$meta_fields[esc_html__('Lectures', 'masterstudy-lms-learning-management-system')] = array(
		'text' => $curriculum_info['lessons'],
		'icon' => 'fa-icon-stm_icon_bullhorn'
	);
}

if(!empty($meta['video_duration'])) {
	$meta_fields[esc_html__('Video', 'masterstudy-lms-learning-management-system')] = array(
		'text' => $meta['video_duration'],
		'icon' => 'fa-icon-stm_icon_film-play'
	);
}

if(!empty($meta['level'])) {
	$levels = array(
		'beginner' => esc_html__('Beginner', 'masterstudy-lms-learning-management-system'),
		'intermediate' => esc_html__('Intermediate', 'masterstudy-lms-learning-management-system'),
		'advanced' => esc_html__('Advanced', 'masterstudy-lms-learning-management-system'),
	);
	$meta_fields[esc_html__('Level', 'masterstudy-lms-learning-management-system')] = array(
		'text' => $levels[$meta['level']],
		'icon' => 'lnr lnr-sort-amount-asc'
	);
}

if(!empty($meta_fields)): ?>
	<div class="stm-lms-course-info heading_font">
		<?php foreach($meta_fields as $meta_field_key => $meta_field): ?>
			<div class="stm-lms-course-info__single">
                <div class="stm-lms-course-info__single_label">
                    <span><?php echo sanitize_text_field($meta_field_key) ?></span>:
                    <strong><?php echo sanitize_text_field($meta_field['text']); ?></strong>
                </div>
                <div class="stm-lms-course-info__single_icon">
                    <i class="<?php echo sanitize_text_field($meta_field['icon']); ?>"></i>
                </div>
			</div>
		<?php endforeach; ?>
	</div>
<?php else: ?>
    <div class="stm-lms-course-info"><div class="stm-lms-course-info__single"></div></div>
<?php endif;