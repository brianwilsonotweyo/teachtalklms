<div class="stm_lms_edit_socials">
    <div class="row">

        <div class="col-md-12">
            <h3><?php esc_html_e('Change Password', 'masterstudy-lms-learning-management-system'); ?></h3>
        </div>

    </div>

    <div class="stm_lms_edit_socials_list">
        <div class="row">

            <div class="col-md-6">

                <div class="form-group">
                    <label class="heading_font"><?php esc_html_e('New Password', 'masterstudy-lms-learning-management-system'); ?></label>
                    <div class="form-group-social">
                        <input v-model="data.meta.new_pass"
                               class="form-control"
                               placeholder="<?php esc_html_e('Enter your New Password', 'masterstudy-lms-learning-management-system') ?>"/>
                        <i class="fa fa-key"></i>
                    </div>
                </div>

            </div>

            <div class="col-md-6">
                <div class="form-group">
                    <label class="heading_font"><?php esc_html_e('Re-type new password', 'masterstudy-lms-learning-management-system'); ?></label>
                    <div class="form-group-social">
                        <input v-model="data.meta.new_pass_re"
                               class="form-control"
                               placeholder="<?php esc_html_e('Enter your new password', 'masterstudy-lms-learning-management-system') ?>"/>
                        <i class="fa fa-key"></i>
                    </div>
                </div>
            </div>

        </div>
    </div>

</div>