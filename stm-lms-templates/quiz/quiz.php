<?php
/***
 * @var $questions
 * @var $last_quiz
 * @var $passed
 * @var $post_id
 * @var $item_id
 * @var $last_quiz
 * @var $source
 */

if (!empty($questions)):

	$args = array(
		'post_type'      => 'stm-questions',
		'posts_per_page' => -1,
		'post__in'       => explode(',', $questions),
		'orderby'        => 'post__in'
	);

	$q = new WP_Query($args);
	if ($q->have_posts()):
		$user = apply_filters('user_answers__user_id', STM_LMS_User::get_current_user(), $source);
		$last_answers = stm_lms_get_quiz_latest_answers($user['id'], $item_id, array('question_id', 'user_answer', 'correct_answer'));
		$last_answers = STM_LMS_Helpers::set_value_as_key($last_answers, 'question_id');
		$question_index = 0;


		?>

        <?php if(STM_LMS_Quiz::show_answers($item_id)): ?>
		    <?php STM_LMS_Templates::show_lms_template('quiz/circle_result', compact('last_quiz', 'passing_grade')); ?>
        <?php endif; ?>

        <?php STM_LMS_Templates::show_lms_template('quiz/timer', compact('q', 'item_id')); ?>

		<?php if (!STM_LMS_Quiz::show_answers($item_id) and empty($last_quiz)): ?>
            <a href="#"
               class="btn btn-default stm_lms_start_quiz">
                <?php esc_html_e('Start Quiz', 'masterstudy-lms-learning-management-system'); ?>
            </a>
        <?php endif; ?>

        <form class="stm-lms-single_quiz">

            <?php
            $current_screen = get_queried_object();
            $source = (!empty($current_screen)) ? $current_screen->ID : '';
            ?>

            <input type="hidden" name="source" value="<?php echo intval($source); ?>">

			<?php while ($q->have_posts()): $q->the_post();
				$question_index++; ?>
                <span class="stm-lms-single_quiz__label"><?php printf(esc_html__('Question %s', 'masterstudy-lms-learning-management-system'), $question_index); ?></span>
				<?php STM_LMS_Templates::show_lms_template('quiz/question', compact('item_id', 'last_answers')); ?>
			<?php endwhile; ?>

			<?php if (!STM_LMS_Quiz::show_answers($item_id)): ?>
                <input type="hidden" name="action" value="stm_lms_user_answers"/>
                <input type="hidden" name="quiz_id" value="<?php echo intval($item_id); ?>"/>
                <input type="hidden" name="course_id" value="<?php echo intval($post_id); ?>"/>
                <button type="submit" class="btn btn-default stm_lms_complete_lesson">
                    <span><?php esc_html_e('Submit Quiz', 'masterstudy-lms-learning-management-system'); ?></span>
                </button>
			<?php endif; ?>

        </form>

		<?php wp_reset_postdata(); ?>
	<?php endif; ?>
<?php endif;