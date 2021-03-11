<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>

<?php
/**
 * @var $post_id
 * @var $item_id
 */

stm_lms_register_style('quiz');
stm_lms_register_script('quiz');

$current_screen = get_queried_object();
$source = (!empty($current_screen)) ? $current_screen->ID : '';
if(empty($post_id)) $post_id = $source;

if (!empty($item_id)):

	/*Start Quiz*/

	$q = new WP_Query(array(
		'posts_per_page' => 1,
		'post_type'      => 'stm-quizzes',
		'post__in'       => array($item_id)
	));


	/*Quiz failed?*/
	STM_LMS_Quiz::is_quiz_failed($item_id, $post_id);

	if ($q->have_posts()):
		$quiz_meta = STM_LMS_Helpers::parse_meta_field($item_id);
		$user = apply_filters('user_answers__user_id', STM_LMS_User::get_current_user(), $source);
		$last_quiz = STM_LMS_Helpers::simplify_db_array(stm_lms_get_user_last_quiz($user['id'], $item_id, array('progress')));
		$progress = (!empty($last_quiz['progress'])) ? $last_quiz['progress'] : 0;
		$passing_grade = (!empty($quiz_meta['passing_grade'])) ? $quiz_meta['passing_grade'] : 0;
		$passed = $progress >= $passing_grade && !empty($progress);

		$classes = array();
		if ($passed) $classes[] = 'passed';
		if (!empty($last_quiz) and !$passed) $classes[] = 'not-passed';
		if (STM_LMS_Quiz::show_answers($item_id)) $classes[] = 'show_answers';
		if (!empty($last_quiz)) $classes[] = 'retaking';

		$duration = STM_LMS_Quiz::get_quiz_duration($item_id);
		$classes[] = (empty($duration)) ? 'no-timer' : 'has-timer';

		?>
        <div class="stm-lms-course__lesson-content <?php echo esc_attr(implode(' ', $classes)); ?>">
			<?php while ($q->have_posts()): $q->the_post();
				$meta_quiz = STM_LMS_Helpers::parse_meta_field(get_the_ID()); ?>


                <div class="stm-lms-course__lesson-html_content">
                    <?php echo wp_kses_post(get_the_content()); ?>
                </div>

                <?php if (!empty($meta_quiz['questions'])) {
                    STM_LMS_Templates::show_lms_template(
                        'quiz/quiz',
                        array_merge($meta_quiz, compact('post_id', 'item_id', 'last_quiz', 'source'))
                    );
                } ?>

				<?php STM_LMS_Templates::show_lms_template(
					'quiz/results',
					compact('quiz_meta', 'last_quiz', 'progress', 'passing_grade', 'passed', 'item_id')
				);
				?>

			<?php endwhile; ?>
        </div>

	<?php endif; ?>
	<?php wp_reset_postdata(); ?>
<?php endif;