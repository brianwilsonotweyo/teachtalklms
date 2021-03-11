<?php if ( ! defined( 'ABSPATH' ) ) exit; //Exit if accessed directly ?>

<?php wp_enqueue_script('vue-resource.js'); ?>
<?php wp_enqueue_script('vue2-editor.js'); ?>

<?php wp_add_inline_script('vue2-editor.js',
    'Vue.component("vue-editor", Vue2Editor.default.VueEditor);
    var stm_lms_post_id = ' . $post_id); ?>

<div id="stm_lms_add_review">
	<?php if (is_user_logged_in()): ?>
        <transition name="slide-fade">
            <div class="" v-if="openReview">
                <div class="form-group">
                    <vue-editor v-model="review_text"></vue-editor>
                </div>
                <div class="form-group">
                    <div class="star-rating" v-on:mouseover="ratingHover($event)">
                        <span v-bind:style="{'width' : this.rating * 20 + '%'}"></span>
                    </div>
                </div>
            </div>
        </transition>

        <a href="#" class="btn btn-default" @click.prevent="addReview()" v-bind:class="{'loading' : loading}">
            <span><?php esc_html_e('Add review', 'masterstudy-lms-learning-management-system'); ?></span>
        </a>

        <transition name="slide-fade">
            <div class="stm-lms-message" v-bind:class="status" v-if="message">
                {{ message }}
            </div>
        </transition>
	<?php else: ?>
		<?php printf(__('Please, <a href="%s">login</a> to leave a review', 'masterstudy-lms-learning-management-system'), STM_LMS_User::login_page_url()); ?>
	<?php endif; ?>
</div>