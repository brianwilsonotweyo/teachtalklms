<?php
/**
 * @var $lectures
 * @var $duration
 * @var $level
 */

$levels = array(
	'beginner'     => esc_html__('Beginner', 'masterstudy-lms-learning-management-system'),
	'intermediate' => esc_html__('Intermediate', 'masterstudy-lms-learning-management-system'),
	'advanced'     => esc_html__('Advanced', 'masterstudy-lms-learning-management-system'),
);

if(!empty($levels[$level])): ?>
	<div class="stm_lms_course__meta">
		<i class="stmlms-level"></i>
		<?php echo wp_kses_post($levels[$level]); ?>
	</div>
<?php endif; ?>

<?php if(!empty($lectures['lessons'])): ?>
	<div class="stm_lms_course__meta">
		<i class="stmlms-cats"></i>
		<?php printf(esc_html__('%s Lectures', 'masterstudy-lms-learning-management-system'), $lectures['lessons']); ?>
	</div>
<?php endif; ?>

<?php if(!empty($duration)): ?>
	<div class="stm_lms_course__meta">
		<i class="stmlms-lms-clocks"></i>
		<?php echo wp_kses_post($duration); ?>
	</div>
<?php endif; ?>
