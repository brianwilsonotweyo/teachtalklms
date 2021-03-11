<?php
/**
 * @var string $type
 * @var array $answers
 * @var string $question
 * @var string $question_explanation
 * @var string $question_hint
 */
$question_id = get_the_ID(); ?>
<?php foreach($answers as $answer): ?>
	<div class="stm-lms-single-answer">
		<label>
			<input required type="checkbox" name="<?php echo esc_attr($question_id); ?>[]" value="<?php echo sanitize_text_field($answer['text']); ?>" />
            <i class="fa fa-check"></i>
			<?php echo sanitize_text_field($answer['text']); ?>
		</label>
	</div>
<?php endforeach;