<?php
/**
 * @var string $type
 * @var array $answers
 * @var string $question
 * @var string $question_explanation
 * @var string $question_hint
 */
$question_id = get_the_ID();

stm_lms_register_style('item_match_question');
stm_lms_register_script('item_match_question', array('jquery-ui-sortable'));
?>

<div class="stm_lms_question_item_match">

    <div class="row">

        <div class="col-md-6">
            <div class="stm_lms_question_item_match__questions">
                <?php foreach ($answers as $answer): ?>
                    <div class="stm_lms_question_item_match__single">
                        <?php echo wp_kses_post($answer['question']); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <input required type="text" class="stm_lms_question_item_match__input" name="<?php echo esc_attr($question_id); ?>"/>
        </div>

        <div class="col-md-6">
            <div class="stm_lms_question_item_match__answers">
                <?php foreach ($answers as $answer): ?>
                    <div class="stm_lms_question_item_match__answer"></div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="col-md-12">
            <div class="stm_lms_question_item_match__matches">
                <?php shuffle($answers); foreach ($answers as $answer): ?>
                    <div class="stm_lms_question_item_match__match" data-answer="<?php echo esc_attr($answer['text']); ?>"><?php echo wp_kses_post($answer['text']); ?></div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>


</div>