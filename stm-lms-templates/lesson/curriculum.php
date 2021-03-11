<?php

stm_lms_register_style('curriculum');
stm_lms_register_script('curriculum');
$post_id = (!empty($post_id)) ? $post_id : get_the_ID();
$curriculum = get_post_meta($post_id, 'curriculum', true); ?>


<div class="stm-curriculum__close">
    <i class="lnr lnr-cross"></i>
</div>

<div class="stm-curriculum">
    <h3 class="stm-curriculum__title"><?php esc_html_e('Course sections', 'masterstudy-lms-learning-management-system'); ?></h3>
	<?php if (!empty($curriculum)):
		$curriculum_full = $curriculum = explode(',', $curriculum);
		$has_access = STM_LMS_User::has_course_access($post_id);
		$lesson_number = 1;

		$sections = STM_LMS_Lesson::create_sections($curriculum);

		$item_index = 0;
		?>


		<?php foreach ($sections as $index => $section_info): ?>
		    <?php

                $curriculum = (!empty($section_info['items'])) ? $section_info['items'] : array();
                $opened = (is_array($section_info['items']) and in_array($item_id, $section_info['items'])) ? 'opened' : '';
                $lesson_number = 1;

		    ?>
            <div class="stm-curriculum-section <?php echo esc_attr($opened); ?>">
                <div class="stm-curriculum-item stm-curriculum-item__section <?php echo esc_attr($opened); ?>">
                    <div class="stm-curriculum-section__info">
                        <span><?php echo wp_kses_post($section_info['number']); ?></span>
                        <?php if (!empty($section_info['title'])): ?>
                            <h5><?php echo wp_kses_post($section_info['title']); ?></h5>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="stm-curriculum-section__lessons">
                    <?php foreach ($curriculum as $curriculum_item): ?>

                        <?php $content_type = get_post_type($curriculum_item);
                        $meta = '';
						$icon = 'stmlms-text';
                        $type = 'lesson';
                        $quiz_info = array();

                        $previous_completed = (isset($completed)) ? $completed : 'first';

                        if ($content_type === 'stm-quizzes') {
                            $type = 'quiz';
                            $user = STM_LMS_User::get_current_user();
                            $user_id = $user['id'];
                            $quiz_info = STM_LMS_Helpers::simplify_db_array(stm_lms_get_user_quizzes($user_id, $curriculum_item, array('progress')));
                            $completed = (STM_LMS_Quiz::quiz_passed($curriculum_item)) ? 'completed' : '';
							$icon = 'stmlms-quiz';
                        } else {
                            $meta = get_post_meta($curriculum_item, 'duration', true);
                            $completed = (STM_LMS_Lesson::is_lesson_completed('', $post_id, $curriculum_item)) ? 'completed' : '';
							$type = get_post_meta($curriculum_item, 'type', true);

							if($type == 'slide' or $type == 'video') $icon = 'stmlms-slides';
                        }

                        $item_classes = array('stm-curriculum-item');
                        $item_classes[] = $type;
                        $item_classes[] = "is-{$completed}";
                        $item_classes[] = apply_filters("stm_lms_prev_status", "{$previous_completed}", $post_id, $curriculum_item);
                        if($curriculum_item === $item_id) $item_classes[] = 'active';

                        ?>
                        <a href="<?php echo esc_url(STM_LMS_Lesson::get_lesson_url($post_id, $curriculum_item)) ?>"
                           class="<?php echo implode(' ', $item_classes); ?>">

                            <div class="stm-curriculum-item__icon">
                                <i class="<?php echo esc_attr($icon) ?>"></i>
                            </div>

                            <div class="stm-curriculum-item__num">
                                <?php echo intval($lesson_number); ?>
                            </div>

                            <div class="stm-curriculum-item__title">
                                <div class="heading_font">
                                    <?php echo esc_attr(get_the_title($curriculum_item)); ?>
                                </div>
                            </div>

                            <div class="stm-curriculum-item__meta">
                                <?php if (!empty($quiz_info['progress'])): ?>
                                    <?php echo intval($quiz_info['progress']) ?>%
                                <?php endif; ?>
                            </div>

                            <?php echo apply_filters('stm_lms_curriculum_item_status', '
                                <div class="stm-curriculum-item__completed '. esc_attr($completed) . '">
                                    <i class="fa fa-check"></i>
                                </div>
                            ', $previous_completed, $post_id, $curriculum_item); ?>

                        </a>

                        <?php $lesson_number++; ?>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
	<?php endif; ?>
</div>