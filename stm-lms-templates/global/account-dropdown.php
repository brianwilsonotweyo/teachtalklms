<?php if(is_user_logged_in()):
    stm_lms_register_style('lesson');
    stm_lms_register_style('user');
    $user = STM_LMS_User::get_current_user();
	$new_messages = STM_LMS_Chat::user_new_messages($user['id']);
    ?>

	<div class="stm_lms_account_dropdown">
        <div class="dropdown">

            <?php if(!empty($new_messages)): ?>
                <div class="stm-lms-user_message_btn__counter">
                    <?php echo intval($new_messages); ?>
                </div>
            <?php endif; ?>

            <button id="dLabel" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="lnr lnr-user"></i>
                <span class="login_name"><?php echo stm_lms_minimize_word(sprintf(esc_html__('Hey, %s', 'masterstudy-lms-learning-management-system'), $user['login']), 15); ?></span>
                <span class="caret"></span>
            </button>

            <ul class="dropdown-menu" aria-labelledby="dLabel">
                <li>
                    <a href="<?php echo esc_url(STM_LMS_User::user_page_url()); ?>"><?php esc_html_e('Account', 'masterstudy-lms-learning-management-system'); ?></a>
                    <a href="#" class="stm_lms_logout"><?php esc_html_e('Logout', 'masterstudy-lms-learning-management-system'); ?></a>
                </li>
            </ul>

        </div>
	</div>

<?php endif;