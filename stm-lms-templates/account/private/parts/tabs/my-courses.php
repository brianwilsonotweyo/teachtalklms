<?php stm_lms_register_style('user-courses');
stm_lms_register_style('instructor_courses'); ?>

<div class="stm-lms-user-courses">

    <div class="stm_lms_instructor_courses__grid">

        <div class="stm_lms_instructor_courses__single" v-for="course in courses">
            <div class="stm_lms_instructor_courses__single__inner">
                <div class="stm_lms_instructor_courses__single--image">

                    <div class="stm_lms_post_status heading_font"
                         v-if="course.post_status"
                         v-bind:class="course.post_status.status">
                        {{ course.post_status.label }}
                    </div>
                    <div v-html="course.image"></div>
                </div>

                <div class="stm_lms_instructor_courses__single--inner">

                    <div class="stm_lms_instructor_courses__single--terms" v-if="course.terms">
                        <div class="stm_lms_instructor_courses__single--term"
                             v-for="(term, key) in course.terms"
                             v-html="term + ' >'" v-if="key === 0">
                        </div>
                    </div>

                    <div class="stm_lms_instructor_courses__single--title">
                        <a v-bind:href="course.link">
                            <h5 v-html="course.title"></h5>
                        </a>
                    </div>

                    <div class="stm_lms_instructor_courses__single--progress">
                        <div class="stm_lms_instructor_courses__single--progress_top">
                            <div class="stm_lms_instructor_courses__single--duration" v-if="course.duration">
                                <i class="far fa-clock"></i>
                                {{ course.duration }}
                            </div>
                            <div class="stm_lms_instructor_courses__single--completed">
                                {{ course.progress_label }}
                            </div>
                        </div>

                        <div class="stm_lms_instructor_courses__single--progress_bar">
                            <div class="stm_lms_instructor_courses__single--progress_filled"
                                 v-bind:style="{'width' : course.progress + '%'}"></div>
                        </div>

                    </div>

                    <div class="stm_lms_instructor_courses__single--enroll">
                        <a v-bind:href="course.current_lesson_id" class="btn btn-default"
                           v-bind:class="{'continue' : course.progress !== '0'}">
                            <span v-if="course.progress === '0'"><?php esc_html_e('Start Course', 'masterstudy-lms-learning-management-system'); ?></span>
                            <span v-else><?php esc_html_e('Continue', 'masterstudy-lms-learning-management-system'); ?></span>
                        </a>
                    </div>

                    <div class="stm_lms_instructor_courses__single--started">
                        {{ course.start_time }}
                    </div>

                </div>
            </div>

        </div>

    </div>


    <h4 v-if="!courses.length"><?php esc_html_e('No courses.', 'masterstudy-lms-learning-management-system'); ?></h4>

</div>

<div class="text-center load-my-courses">
    <a @click="getCourses()" v-if="!total" class="btn btn-default" v-bind:class="{'loading' : loading}">
        <span><?php esc_html_e('Show more', 'masterstudy-lms-learning-management-system'); ?></span>
    </a>
</div>