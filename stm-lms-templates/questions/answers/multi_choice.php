<?php
/**
 * @var string $type
 * @var array $answers
 * @var array $user_answer
 * @var string $question
 * @var string $question_explanation
 * @var string $question_hint
 */

$question_id = get_the_ID();

$is_correct = (!empty($user_answer['correct_answer'])) ? true : false;
$user_answer['user_answer'] = (!empty($user_answer['user_answer'])) ? explode(',', $user_answer['user_answer']) : array();
$user_answer = $user_answer['user_answer'];

if($is_correct) $user_answer = array();

foreach($answers as $answer):
	$answer_class = array();

    $answer['text'] = trim($answer['text']);

    /*Get Right Answers*/
    if($is_correct) {
        if($answer['isTrue']) $user_answer[] = $answer['text'];
    }

	if(in_array($answer['text'], $user_answer) and $answer['isTrue']) $answer_class[] = 'correctly_answered';
	if(in_array($answer['text'], $user_answer) and !$answer['isTrue']) $answer_class[] = 'wrongly_answered';
	if(!in_array($answer['text'], $user_answer) and $answer['isTrue']) $answer_class[] = 'correct_answer';

	$answered = !empty(array_intersect(array('correctly_answered', 'wrongly_answered'), $answer_class)) ? true : false;

    ?>
	<div class="stm-lms-single-answer <?php echo implode(' ', $answer_class); ?>">
		<label>
			<input <?php if ($answered) echo esc_attr('checked'); ?>
                    disabled
                    type="checkbox"
                    name="<?php echo esc_attr($question_id); ?>[]"
                    value="<?php echo sanitize_text_field($answer['text']); ?>" />
            <i class="fa fa-check"></i>
			<?php echo sanitize_text_field($answer['text']); ?>
		</label>
	</div>
<?php endforeach;