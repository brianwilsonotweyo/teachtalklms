<?php
/**
 * @var string $type
 * @var array $stm_lms_vars
 * @var array $answers
 * @var string $question
 * @var string $question_explanation
 * @var string $question_hint
 * @var string $item_id
 */

$question_template = (STM_LMS_Quiz::show_answers($item_id)) ? 'questions/answers/' . $type : 'questions/' . $type;
?>
<div class="stm-lms-single_question stm-lms-single_question_<?php echo esc_attr($type); ?>">


    <div class="stm-lms-single_question_text">
        <h3><?php the_title(); ?></h3>
    </div>

	<?php if (!empty($question_explanation) and STM_LMS_Quiz::show_answers($item_id)): ?>
        <div class="stm-lms-single_question_explanation">
			<?php echo sanitize_text_field($question_explanation); ?>
        </div>
	<?php endif; ?>

    <div class="heading_font">
		<?php STM_LMS_Templates::show_lms_template($question_template, $stm_lms_vars); ?>
    </div>

</div>