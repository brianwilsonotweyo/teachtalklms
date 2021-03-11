<?php
/**
 * @var $current_user
 */

stm_lms_register_style('edit_account');
stm_lms_register_script('edit_account', array('vue.js', 'vue-resource.js'));
$data = json_encode($current_user);
wp_add_inline_script('stm-lms-edit_account',
	"var stm_lms_edit_account_info = {$data}");

?>

<div data-container-open=".stm_lms_edit_account">
    <div class="stm_lms_edit_account" id="stm_lms_edit_account">

        <div class="stm_lms_user_info_top">
            <h1><?php esc_html_e('Edit Profile', 'masterstudy-lms-learning-management-system'); ?></h1>
        </div>

		<?php STM_LMS_Templates::show_lms_template('account/private/edit_account/name'); ?>

		<?php STM_LMS_Templates::show_lms_template('account/private/edit_account/position'); ?>

		<?php STM_LMS_Templates::show_lms_template('account/private/edit_account/bio'); ?>

		<?php STM_LMS_Templates::show_lms_template('account/private/edit_account/socials'); ?>

		<?php STM_LMS_Templates::show_lms_template('account/private/edit_account/change_password'); ?>

        <div class="row">

            <div class="col-md-12">
                <button @click="saveUserInfo()"
                        v-bind:class="{'loading' : loading}"
                        class="btn btn-default">
                    <span><?php esc_html_e('Save changes', 'masterstudy-lms-learning-management-system'); ?></span>
                </button>
            </div>

            <div class="col-md-12">
                <transition name="slide-fade">
                    <div class="stm-lms-message" v-bind:class="status" v-if="message">
                        {{ message }}
                    </div>
                </transition>
            </div>

        </div>
    </div>
</div>