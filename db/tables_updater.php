<?php

register_activation_hook( STM_LMS_FILE, 'stm_lms_set_table_v' );
 
function stm_lms_set_table_v() {
    update_option('stm_lms_db_version', STM_LMS_DB_VERSION);
}

function stm_lms_admin_notice__success()
{

    $current_v = get_option('stm_lms_db_version', 1);

    $has_new_version = version_compare(STM_LMS_DB_VERSION, $current_v);

    if ($has_new_version): ?>
        <div class="notice notice-lms notice-lms-update-db" data-ajax="<?php echo esc_url( admin_url('admin-ajax.php')); ?>">
            <p>
                <strong>
                    <?php esc_html_e('MasterStudy LMS database update required', 'masterstudy-lms-learning-management-system'); ?>
                </strong>
            </p>
            <p>
                <?php esc_html_e('MasterStudy LMS has been updated! We added new features, and need to update your database to latest version. Click "Update" and do not reload the page until finish.', 'masterstudy-lms-learning-management-system'); ?>
            </p>
            <p>
                <a href="#" class="button-primary">
                    <?php esc_html_e('Update', 'masterstudy-lms-learning-management-system'); ?>
                </a>
            </p>

            <span>
                <?php esc_html_e('Updating database...', 'masterstudy-lms-learning-management-system'); ?>
            </span>
        </div>
    <?php endif;
}

add_action('admin_notices', 'stm_lms_admin_notice__success');

add_action('admin_enqueue_scripts', 'stm_lms_table_update_scripts');

function stm_lms_table_update_scripts() {
    wp_enqueue_script('stm_lms_table_update_scripts', STM_LMS_URL . '/assets/js/table_update.js', array('jquery'), time(), true);
    stm_lms_register_style('table_update');
}

add_action('wp_ajax_stm_lms_tables_update', 'stm_lms_tables_update');

function stm_lms_tables_update() {

    check_ajax_referer('stm_lms_tables_update', 'nonce');

    if(!current_user_can('manage_options')) die;

    stm_lms_user_answers();
    stm_lms_user_cart();
    stm_lms_user_chat();
    stm_lms_user_conversation();
    stm_lms_user_courses();
    stm_lms_user_lessons();
    stm_lms_user_quizzes();
    stm_lms_user_quizzes_times();
    if(function_exists('stm_lms_user_subscriptions')) {
        stm_lms_user_subscriptions();
    }

    update_option('stm_lms_db_version', STM_LMS_DB_VERSION);

    wp_send_json('updated');

}