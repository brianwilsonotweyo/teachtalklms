<?php
/**
 * @var $current_user
 */

wp_enqueue_script('vue-resource.js');
stm_lms_register_script('user-tabs');

$tabs = array(
	'my-courses' => esc_html__('My Courses', 'masterstudy-lms-learning-management-system'),
	'my-quizzes' => esc_html__('My Quizzes', 'masterstudy-lms-learning-management-system'),
	'my-orders'  => esc_html__('My Orders', 'masterstudy-lms-learning-management-system'),
);

if (STM_LMS_Subscriptions::subscription_enabled()) $tabs['my-memberships'] = esc_html__('My Memberships', 'masterstudy-lms-learning-management-system');

$tabs = apply_filters("account-private-parts-tabs", $tabs);

$active = 'my-courses';
?>

<ul class="nav nav-tabs" role="tablist">
	<?php foreach ($tabs as $slug => $name): ?>
        <li role="presentation" class="<?php echo ($slug == $active) ? 'active' : '' ?>">
            <a href="#<?php echo esc_attr($slug); ?>" data-toggle="tab">
				<?php echo wp_kses_post($name); ?>
            </a>
        </li>
	<?php endforeach; ?>
</ul>

<div class="tab-content">
	<?php foreach ($tabs as $slug => $name): ?>
        <div role="tabpanel"
             class="tab-pane vue_is_disabled <?php echo ($slug == $active) ? 'active' : '' ?>"
             v-bind:class="{'is_vue_loaded' : vue_loaded}"
             id="<?php echo esc_attr($slug); ?>">
			<?php STM_LMS_Templates::show_lms_template('account/private/parts/tabs/' . $slug); ?>
        </div>
	<?php endforeach; ?>
</div>