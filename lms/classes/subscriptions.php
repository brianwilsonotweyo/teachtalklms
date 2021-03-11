<?php

STM_LMS_Subscriptions::init();

class STM_LMS_Subscriptions
{

    public static function init()
    {
        add_action('pmpro_membership_level_after_other_settings', 'STM_LMS_Subscriptions::stm_lms_pmpro_settings');
        add_action('pmpro_save_membership_level', 'STM_LMS_Subscriptions::stm_lms_pmpro_save_settings');

        add_action('wp_ajax_stm_lms_load_modal', 'STM_LMS_Helpers::load_modal');
        add_action('wp_ajax_nopriv_stm_lms_load_modal', 'STM_LMS_Helpers::load_modal');

        add_action('wp_ajax_stm_lms_use_membership', 'STM_LMS_Subscriptions::use_membership');
        add_action('wp_ajax_nopriv_stm_lms_use_membership', 'STM_LMS_Subscriptions::use_membership');

        add_action('pmpro_after_change_membership_level', 'STM_LMS_Subscriptions::subscription_changed', 10, 3);

        add_action('wp_ajax_stm_lms_change_featured', 'STM_LMS_Subscriptions::featured_status');

        add_action('wp_ajax_stm_lms_delete_course_subscription', 'STM_LMS_Subscriptions::remove_subscription_course');
    }

    public static function use_membership()
    {

        check_ajax_referer('stm_lms_use_membership', 'nonce');

        /*Check if has course id*/
        if (empty($_GET['course_id'])) die;
        $course_id = intval($_GET['course_id']);

        /*Check if logged in*/
        $current_user = STM_LMS_User::get_current_user();

        if (empty($current_user['id'])) die;
        $user_id = $current_user['id'];

        /*Check if user already has course*/
        $courses = stm_lms_get_user_course($user_id, $course_id, array('user_course_id'));
        if (!empty($courses)) die;

        $r = array();
        $subs = STM_LMS_Subscriptions::user_subscriptions();
        if (!empty($subs->quotas_left)) {
            $progress_percent = 0;
            $current_lesson_id = 0;
            $status = 'enrolled';
            $subscription_id = $subs->subscription_id;
            $user_course = compact('user_id', 'course_id', 'current_lesson_id', 'status', 'progress_percent', 'subscription_id');
            $user_course['start_time'] = time();
            stm_lms_add_user_course($user_course);
            STM_LMS_Course::add_student($course_id);
            $r['url'] = get_the_permalink($course_id);
        }

        wp_send_json($r);

    }

    public static function subscription_enabled()
    {
        return (defined('PMPRO_VERSION'));
    }

    public static function level_url()
    {
        if (!STM_LMS_Subscriptions::subscription_enabled()) return false;

        $membership_levels = pmpro_getOption("levels_page_id");
        return (get_the_permalink($membership_levels));
    }

    public static function user_subscriptions($all = false, $user_id = '')
    {

        if (!STM_LMS_Subscriptions::subscription_enabled()) return false;

        $subs = object;

        if (is_user_logged_in() && function_exists('pmpro_hasMembershipLevel') && pmpro_hasMembershipLevel()) {
            if(empty($user_id)) {
                $user = STM_LMS_User::get_current_user();
                if (empty($user['id'])) return $subs;
                $user_id = $user['id'];
            }
            $subs = pmpro_getMembershipLevelForUser($user_id);

            $subscription_id = ($all) ? '*' : $subs->subscription_id;
            $subscriptions = (!empty($subs->ID)) ? count(stm_lms_get_user_courses_by_subscription($user_id, '*', array('user_course_id'), 0)) : 0;

            $subs->course_number = (!empty($subs->ID)) ? STM_LMS_Subscriptions::get_course_number($subs->ID) : 0;
            $subs->used_quotas = $subscriptions;
            $subs->quotas_left = $subs->course_number - $subs->used_quotas;
        }

        return $subs;
    }

    public static function save_course_number($level_id)
    {
        if (isset($_REQUEST['stm_lms_course_number'])) {
            update_option('stm_lms_course_number_' . $level_id, intval($_REQUEST['stm_lms_course_number']));
        }
        if (isset($_REQUEST['stm_lms_featured_courses_number'])) {
            update_option('stm_lms_featured_courses_number_' . $level_id, intval($_REQUEST['stm_lms_featured_courses_number']));
        }
        if (isset($_REQUEST['stm_lms_plan_group'])) {
            update_option('stm_lms_plan_group_' . $level_id, sanitize_text_field($_REQUEST['stm_lms_plan_group']));
        }

    }

    public static function get_course_number($level_id)
    {
        return get_option('stm_lms_course_number_' . $level_id, 0);
    }

    public static function get_featured_courses_number($level_id)
    {
        return get_option('stm_lms_featured_courses_number_' . $level_id, 0);
    }

    public static function get_plan_group($level_id)
    {
        return get_option('stm_lms_plan_group_' . $level_id, 0);
    }

    public static function stm_lms_pmpro_settings()
    {
        $level_id = (!empty($_GET['edit'])) ? intval($_GET['edit']) : 0;
        $course_number = STM_LMS_Subscriptions::get_course_number($level_id);
        $course_featured = STM_LMS_Subscriptions::get_featured_courses_number($level_id);
        $plan_group = STM_LMS_Subscriptions::get_plan_group($level_id);
        ?>
        <h3 class="topborder"><?php esc_html_e('STM LMS Settings', 'masterstudy-lms-learning-management-system'); ?></h3>
        <table class="form-table">
            <tbody>

            <tr class="membership_categories">
                <th scope="row" valign="top">
                    <label>
                        <?php esc_html_e('Number of available courses in subscription', 'masterstudy-lms-learning-management-system'); ?>
                        :
                    </label>
                </th>
                <td>
                    <input name="stm_lms_course_number" type="text" size="10"
                           value="<?php echo esc_attr($course_number); ?>"/>
                    <small><?php esc_html_e('User can enroll several courses after subscription', 'masterstudy-lms-learning-management-system'); ?></small>
                </td>
            </tr>

            <tr class="membership_categories">
                <th scope="row" valign="top">
                    <label>
                        <?php esc_html_e('Number of featured courses quote in subscription', 'masterstudy-lms-learning-management-system'); ?>
                        :
                    </label>
                </th>
                <td>
                    <input name="stm_lms_featured_courses_number" type="text" size="10"
                           value="<?php echo esc_attr($course_featured); ?>"/>
                    <small><?php esc_html_e('Instructors can mark their courses as featured', 'masterstudy-lms-learning-management-system'); ?></small>
                </td>
            </tr>

            <tr class="membership_categories">
                <th scope="row" valign="top">
                    <label><?php esc_html_e('Group Plan', 'masterstudy-lms-learning-management-system'); ?>:</label>
                </th>
                <td>
                    <input name="stm_lms_plan_group" type="text" size="10"
                           value="<?php echo esc_attr($plan_group); ?>"/>
                    <small><?php esc_html_e('Show plan group in separate tab', 'masterstudy-lms-learning-management-system'); ?></small>
                </td>
            </tr>
            </tbody>
        </table>
    <?php }

    public static function stm_lms_pmpro_save_settings($level_id)
    {
        STM_LMS_Subscriptions::save_course_number($level_id);
        return $level_id;
    }

    public static function check_user_subscription_courses($user_id, $is_cancelled)
    {

        /*Delete All if is cancelled*/
        if(!empty($is_cancelled)) {
            $courses = stm_lms_get_user_courses_by_subscription(
                $user_id,
                '*',
                array('course_id'),
                0
            );

            if (!empty($courses)) {
                foreach ($courses as $course) {
                    stm_lms_get_delete_user_course($user_id, $course['course_id']);
                }
            }

        } else {

            /*Delete overquoted courses only*/
            $sub_info = self::user_subscriptions(true, $user_id);

            if (!empty($sub_info->quotas_left) and $sub_info->quotas_left < 0) {

                $limit = $sub_info->used_quotas - $sub_info->course_number;

                $courses = stm_lms_get_user_courses_by_subscription(
                    $user_id,
                    '*',
                    array('course_id', 'start_time'),
                    $limit,
                    'start_time ASC'
                );

                if (!empty($courses)) {
                    foreach ($courses as $course) {
                        stm_lms_get_delete_user_course($user_id, $course['course_id']);
                    }
                }

            }
        }
    }

    public static function remove_subscription_course()
    {

        check_ajax_referer('stm_lms_delete_course_subscription', 'nonce');

        if (empty($_GET['course_id'])) die;

        $user_id = get_current_user_id();
        if (empty($user_id)) die;

        $course_id = intval($_GET['course_id']);

        stm_lms_get_delete_user_course($user_id, $course_id);

        wp_send_json(array('success'));
    }

    /*FEATURED*/
    public static function featured_status()
    {

        check_ajax_referer('stm_lms_change_featured', 'nonce');

        $user = STM_LMS_User::get_current_user();

        if (empty($user['id'])) die;
        $user_id = $user['id'];


        if (empty($_GET['post_id'])) die;
        $post_id = intval($_GET['post_id']);

        $featured = get_post_meta($post_id, 'featured', true);
        $featured = (empty($featured)) ? 'on' : '';

        $quota = self::get_featured_quota();
        if (!$quota) $featured = '';

        update_post_meta($post_id, 'featured', $featured);

        if (self::get_featured_quota() < 0) {
            self::check_user_featured_courses();
        }

        wp_send_json(array(
            'featured' => $featured,
            'total_quota' => self::default_featured_quota() + self::pmpro_plan_quota(),
            'available_quota' => self::get_featured_quota(),
            'used_quota' => self::get_user_featured_count(),
        ));

    }

    public static function subscription_changed($level_id, $user_id, $cancelled_level_id)
    {
        self::check_user_featured_courses();

        self::check_user_subscription_courses($user_id, $cancelled_level_id);
    }

    public static function check_user_featured_courses()
    {
        $my_quota = self::get_featured_quota();
        $available_quota = self::default_featured_quota() + self::pmpro_plan_quota();

        if ($my_quota < 0) {
            $args = array(
                'post_type' => 'stm-courses',
                'post_status' => 'publish',
                'orderby' => 'date',
                'order' => 'ASC',
                'suppress_filters' => true,
                'offset' => $available_quota,
                'posts_per_page' => 999,
                'meta_query' => array(
                    array(
                        'key' => 'featured',
                        'value' => 'on',
                        'compare' => '='
                    )
                )
            );

            $q = new WP_Query($args);

            if ($q->have_posts()) {
                while ($q->have_posts()) {
                    $q->the_post();

                    update_post_meta(get_the_ID(), 'featured', '');

                }
            }
        }
    }

    public static function default_featured_quota()
    {

        $options = get_option('stm_lms_settings', array());
        $quota = isset($options['courses_featured_num']) ? $options['courses_featured_num'] : 1;

        return $quota;
    }

    public static function pmpro_plan_quota($user_id = '')
    {
        if (!STM_LMS_Subscriptions::subscription_enabled()) return 0;

        $subs = 0;

        if (is_user_logged_in() && function_exists('pmpro_hasMembershipLevel') && pmpro_hasMembershipLevel()) {
            if (empty($user_id)) {
                $user = STM_LMS_User::get_current_user();
                if (empty($user['id'])) return $subs;
                $user_id = $user['id'];
            }
            $subs = pmpro_getMembershipLevelForUser($user_id);

            $subs = self::get_featured_courses_number($subs->id);

        }

        return intval($subs);

    }

    public static function get_user_featured_count($user_id = '')
    {

        if (empty($user_id)) {
            $user = STM_LMS_User::get_current_user();
            if (empty($user['id'])) return 0;
            $user_id = $user['id'];
        }

        $args = array(
            'post_type' => 'stm-courses',
            'post_status' => array('publish'),
            'posts_per_page' => -1,
            'author' => $user_id,
            'meta_query' => array(
                array(
                    'key' => 'featured',
                    'value' => 'on',
                    'compare' => '='
                )
            )
        );

        $q = new WP_Query($args);

        return $q->found_posts;
    }

    public static function get_featured_quota()
    {
        return self::default_featured_quota() + self::pmpro_plan_quota() - self::get_user_featured_count();
    }


}