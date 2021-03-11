<div class="modal fade stm-lms-modal-login" tabindex="-1" role="dialog" aria-labelledby="stm-lms-modal-login">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active">
                        <a href="#stm-lms-login" data-toggle="tab"><?php esc_html_e('Login', 'masterstudy'); ?></a>
                    </li>
                    <li role="presentation" class="">
                        <a href="#stm-lms-register" data-toggle="tab"><?php esc_html_e('Register', 'masterstudy'); ?></a>
                    </li>
                </ul>
				<?php STM_LMS_Templates::show_lms_template('account/login'); ?>
				<?php STM_LMS_Templates::show_lms_template('account/register'); ?>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    stm_lms_login(false);
    stm_lms_register(false);
</script>