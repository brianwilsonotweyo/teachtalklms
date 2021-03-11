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

stm_lms_register_style('fill_the_gap');

if (!empty($answers[0]) and !empty($answers[0]['text'])):

    $user_answer = (!empty($user_answer['user_answer'])) ? explode(',', $user_answer['user_answer']) : array();

    $text = $answers[0]['text'];
    $matches = stm_lms_get_string_between($text, '|', '|');
    $inputs = array();
    if(!empty($matches)) {
        foreach($matches as $match_index => $match) {
            $width = 'width: ' . (strlen($match) * 8 + 16) . 'px';
            $name = "{$question_id}[{$match_index}]";

            $correct = (!empty($user_answer[$match_index]) && strtolower($match) === strtolower($user_answer[$match_index])) ? 'correct' : 'incorrect';

            $inputs[$match_index] = "<div class='fill_the_gap_check {$correct}'>{$match}</div>";
        }
    }

    $text = str_replace(array_unique($matches), array_unique($inputs), $text);

    ?>

    <div class="stm_lms_question_item_fill_the_gap">
        <?php echo str_replace('|', '', $text); ?>
    </div>

<?php endif; ?>