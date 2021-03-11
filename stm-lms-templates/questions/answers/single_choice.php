<?php
/**
 * @var string $type
 * @var array $answers
 * @var string $question
 * @var string $question_explanation
 * @var string $question_hint
 */
$question_id = get_the_ID();

$is_correct = (!empty($user_answer['correct_answer'])) ? true : false;
$user_answer = (!empty($user_answer['user_answer'])) ? $user_answer['user_answer'] : '';

if($is_correct) $user_answer = array();

foreach ($answers as $answer):
	$answer_class = array();

	/*Get Right Answers*/
	if($is_correct) {
		if($answer['isTrue']) $user_answer = $answer['text'];
	}

	if ($answer['text'] == $user_answer and $answer['isTrue']) $answer_class[] = 'correctly_answered';
	if ($answer['text'] == $user_answer and !$answer['isTrue']) $answer_class[] = 'wrongly_answered';
	if ($answer['text'] != $user_answer and $answer['isTrue']) $answer_class[] = 'correct_answer';
	$answered = !empty(array_intersect(array('correctly_answered', 'wrongly_answered'), $answer_class)) ? true : false;
	?>
    <div class="stm-lms-single-answer <?php echo implode(' ', $answer_class); ?>">
        <label>
            <input <?php if ($answered) echo esc_attr('checked'); ?>
                    type="radio"
                    disabled
                    name="<?php echo esc_attr($question_id); ?>"
                    value="<?php echo sanitize_text_field($answer['text']); ?>"/>
            <i class="fa fa-check"></i>
			<?php echo sanitize_text_field($answer['text']); ?>
			<?php if (!empty($answer['explain'])): ?>
                <div class="stm-lms-single-answer__hint">
                    <i class="fa fa-info"></i>
                    <div class="stm-lms-single-answer__hint_text">
                        <div class="inner">
							<?php echo wp_kses_post($answer['explain']); ?>
                        </div>
                    </div>
                </div>
			<?php endif; ?>
        </label>
    </div>
<?php endforeach; ?>