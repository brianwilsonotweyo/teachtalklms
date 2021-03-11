<?php
stm_lms_register_style('register');
enqueue_register_script();
$r_enabled = STM_LMS_Helpers::g_recaptcha_enabled();

if ($r_enabled):
    $recaptcha = STM_LMS_Helpers::g_recaptcha_keys();
endif;

$site_key = (!empty($recaptcha['public'])) ? $recaptcha['public'] : '';
?>

<div id="stm-lms-register"
     class="vue_is_disabled"
     v-init="site_key = '<?php echo stm_lms_filtered_output($site_key); ?>'"
     v-bind:class="{'is_vue_loaded' : vue_loaded}">
    <h3><?php esc_html_e('Sign Up', 'masterstudy-lms-learning-management-system'); ?></h3>

    <div class="stm_lms_register_wrapper">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="heading_font"><?php esc_html_e('Login', 'masterstudy-lms-learning-management-system'); ?></label>
                    <input class="form-control"
                           type="text"
                           name="login"
                           v-model="login"
                           placeholder="<?php esc_html_e('Enter login', 'masterstudy-lms-learning-management-system'); ?>"/>
                </div>

            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="heading_font"><?php esc_html_e('E-mail', 'masterstudy-lms-learning-management-system'); ?></label>
                    <input class="form-control"
                           type="email"
                           name="email"
                           v-model="email"
                           placeholder="<?php esc_html_e('Enter your E-mail', 'masterstudy-lms-learning-management-system'); ?>"/>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="heading_font"><?php esc_html_e('Password', 'masterstudy-lms-learning-management-system'); ?></label>
                    <input class="form-control"
                           type="password"
                           name="password"
                           v-model="password"
                           placeholder="<?php esc_html_e('Enter password', 'masterstudy-lms-learning-management-system'); ?>"/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="heading_font"><?php esc_html_e('Password again', 'masterstudy-lms-learning-management-system'); ?></label>
                    <input class="form-control"
                           type="password"
                           name="password_re"
                           v-model="password_re"
                           placeholder="<?php esc_html_e('Confirm password', 'masterstudy-lms-learning-management-system'); ?>"/>
                </div>
            </div>
        </div>

        <transition name="slide-fade">
            <div class="row" v-if="become_instructor">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="heading_font"><?php esc_html_e('Degree', 'masterstudy-lms-learning-management-system'); ?></label>
                        <input class="form-control"
                               type="text"
                               name="degree"
                               v-model="degree"
                               placeholder="<?php esc_html_e('Enter Your Degree', 'masterstudy-lms-learning-management-system'); ?>"/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="heading_font"><?php esc_html_e('Expertise', 'masterstudy-lms-learning-management-system'); ?></label>
                        <input class="form-control"
                               type="text"
                               name="expertize"
                               v-model="expertize"
                               placeholder="<?php esc_html_e('Enter your Expertize', 'masterstudy-lms-learning-management-system'); ?>"/>
                    </div>
                </div>
            </div>
        </transition>

        <div class="row">
            <div class="col-md-12">

                <?php STM_LMS_Templates::show_lms_template('gdpr/privacy_policy'); ?>

                <div class="stm_lms_register_wrapper__actions">

                    <?php ?>

                    <label class="stm_lms_styled_checkbox">
                        <span class="stm_lms_styled_checkbox__inner">
                            <input type="checkbox"
                                   name="become_instructor"
                                   v-model="become_instructor"/>
                            <span><i class="fa fa-check"></i> </span>
                        </span>
                        <span><?php esc_html_e('Register as Instructor', 'masterstudy-lms-learning-management-system'); ?></span>
                    </label>

                    <a href="#"
                       class="btn btn-default"
                       v-bind:class="{'loading': loading}"
                       @click.prevent="register()">
                        <span><?php esc_html_e('Register', 'masterstudy-lms-learning-management-system'); ?></span>
                    </a>

                </div>

            </div>
        </div>

    </div>

    <transition name="slide-fade">
        <div class="stm-lms-message" v-bind:class="status" v-if="message">
            {{ message }}
        </div>
    </transition>
</div>