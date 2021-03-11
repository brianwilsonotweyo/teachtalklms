<?php
/**
 * @var $current_user
 */

wp_enqueue_script('vue-resource.js');
stm_lms_register_script('instructor_courses');

$links = STM_LMS_Instructor::instructor_links();
stm_lms_register_style('instructor_courses');

?>

<div class="stm_lms_instructor_courses__top">
    <h3><?php esc_html_e('Courses', 'masterstudy-lms-learning-management-system'); ?></h3>
    <a href="<?php echo esc_url($links['add_new']); ?>" class="btn btn-default" target="_blank">
        <i class="fa fa-plus"></i>
		<?php esc_html_e('Add New course', 'masterstudy-lms-learning-management-system'); ?>
    </a>
</div>

<div class="stm_lms_instructor_courses vue_is_disabled" id="stm_lms_instructor_courses" v-if="courses.length" v-bind:class="{'is_vue_loaded' : vue_loaded}">

    <div class="stm_lms_instructor_quota heading_font" v-if="Object.keys(quota).length">
        <div class="stm_lms_instructor_quota__modal">
            <h5>
                <span class="quota_label"><?php esc_html_e('Available featured Quotes:', 'masterstudy-lms-learning-management-system-pro'); ?></span>
                <span class="used_quota">{{quota.used_quota}}</span> from <span class="total_quota">{{quota.total_quota}}</span>
            </h5>
            <div class="stm_lms_instructor_quota__buttons">
                <span class="btn btn-default" @click="quota = {}"><?php esc_html_e('Done', 'masterstudy-lms-learning-management-system-pro'); ?></span>
                <a href="<?php echo STM_LMS_Subscriptions::level_url(); ?>"
                   v-if="quota.available_quota === 0"
                   class="btn btn-default upgrade" >
                    <?php esc_html_e('Upgrade', 'masterstudy-lms-learning-management-system-pro'); ?>
                </a>
            </div>
        </div>
        <div class="stm_lms_instructor_quota__overlay" @click="quota = {}"></div>
    </div>

    <div class="stm_lms_instructor_courses__grid">

        <div class="stm_lms_instructor_courses__single" v-for="course in courses">
            <div class="stm_lms_instructor_courses__single__inner">
                <div class="stm_lms_instructor_courses__single--image">

                    <div class="stm_lms_post_status heading_font"
                         v-if="course.post_status"
                         v-bind:class="course.post_status.status">
                        {{ course.post_status.label }}
                    </div>

                    <div class="stm_lms_instructor_courses__single--actions heading_font">
                        <a v-bind:href="course.edit_link" target="_blank"><?php esc_html_e('Edit', 'masterstudy-lms-learning-management-system'); ?></a>
                        <a v-bind:href="course.link" target="_blank"><?php esc_html_e('View', 'masterstudy-lms-learning-management-system'); ?></a>
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

                    <div class="stm_lms_instructor_courses__single--meta">
                        <div class="average-rating-stars__top">
                            <div class="star-rating">
                                <span v-bind:style="{'width' : course.percent + '%'}">
                                    <strong class="rating">{{ course.average }}</strong>
                                </span>
                            </div>
                            <div class="average-rating-stars__av heading_font">
                                {{ course.average }} ({{course.total}})
                            </div>
                        </div>
                        <div class="views">
                            <i class="lnr lnr-eye"></i>
                            {{ course.views }}
                        </div>
                    </div>

                    <div class="stm_lms_instructor_courses__single--bottom">
                        <div class="stm_lms_instructor_courses__single--status" v-bind:class="course.status">
                            <i class="lnr lnr-checkmark-circle" v-if="course.status == 'publish'"></i>
                            {{ course.status_label }}
                        </div>
                        <div class="stm_lms_instructor_courses__single--price heading_font" v-if="course.sale_price && course.price">
                            <span>{{ course.price }}</span>
                            <strong>{{ course.sale_price }}</strong>
                        </div>
                        <div class="stm_lms_instructor_courses__single--price heading_font" v-else>
                            <strong>{{ course.price }}</strong>
                        </div>
                    </div>

                    <div class="stm_lms_instructor_courses__single--featured heading_font" v-bind:class="{'loading' : course.changingFeatured}">

                        <div class="feature_it add_to_featured"
                             @click="changeFeatured(course)"
                             v-if="course.status == 'publish' && course.is_featured != 'on'">
                            <?php esc_html_e('Make Featured', 'masterstudy-lms-learning-management-system'); ?>
                        </div>

                        <div class="feature_it remove_from_featured"
                             v-if="course.is_featured == 'on'"
                             @click="changeFeatured(course)">
                            <?php esc_html_e('Remove from Featured', 'masterstudy-lms-learning-management-system'); ?>
                        </div>

                    </div>

                </div>
            </div>
        </div>

    </div>

    <a href="#"
       class="btn btn-default"
       @click.prevent="loadCourses()"
       v-if="!total"
       v-bind:class="{'loading': loading}">
        <span><?php esc_html_e('Load more', 'masterstudy-lms-learning-management-system') ?></span>
    </a>


</div>
