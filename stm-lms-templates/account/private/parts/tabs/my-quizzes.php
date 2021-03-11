<?php stm_lms_register_style('user-quizzes'); ?>

<div class="stm-lms-user-quizzes" v-bind:class="{'loading' : loading}">

    <h3><?php esc_html_e('My quizzes', 'masterstudy-lms-learning-management-system'); ?></h3>

    <div class="stm-lms-user-quiz__head heading_font">

        <div class="stm-lms-user-quiz__head_title">
            <?php esc_html_e('Course', 'masterstudy-lms-learning-management-system'); ?>
        </div>

        <div class="stm-lms-user-quiz__head_quiz">
			<?php esc_html_e('Quiz', 'masterstudy-lms-learning-management-system'); ?>
        </div>

        <div class="stm-lms-user-quiz__head_status">
			<?php esc_html_e('Status', 'masterstudy-lms-learning-management-system'); ?>
        </div>

    </div>

    <div class="stm-lms-user-quiz" v-for="(quiz, key) in quizzes">

		<div class="stm-lms-user-quiz__title">
            <a v-bind:href="quiz.course_url" v-html="quiz.course_title"></a>
		</div>

        <a v-bind:href="quiz.url" v-html="quiz.title" class="stm-lms-user-quiz__name"></a>

		<div class="stm-lms-user-quiz__progress">
            <div class="stm-lms-user-quiz__progress_bar">
                <div class="filled" v-bind:class="quiz.status" v-bind:style="{'width' : quiz.progress + '%'}"></div>
            </div>
			<div class="progress-status">{{quiz.progress + '%'}}</div>
		</div>

		<div class="stm-lms-user-quiz__status">
            <i class="lnr lnr-checkmark-circle" v-if="quiz.status == 'passed'"></i>
            <i class="lnr lnr-cross" v-else></i>
			<div class="status" v-bind:class="quiz.status">{{quiz.status_label}}</div>
		</div>

	</div>

    <h4 v-if="!quizzes.length"><?php esc_html_e('No quizzes.', 'masterstudy-lms-learning-management-system'); ?></h4>

</div>

<a @click="getQuizzes()" v-if="!total" class="btn btn-default" v-bind:class="{'loading' : loading}">
	<span><?php esc_html_e('Show more', 'masterstudy-lms-learning-management-system'); ?></span>
</a>