<?php
/**
 * @var $args
 */

if (empty($args)) $args = array();
if (!empty($_GET['search'])) {
	$args['s'] = sanitize_text_field($_GET['search']);
}

$default_args = array(
	'post_type'      => 'stm-courses',
	'posts_per_page' => STM_LMS_Options::get_option('courses_per_page', get_option('posts_per_page')),
);

$args['image_d'] = !empty($args['image_d']) ? $args['image_d'] : 'img-300-225';

$args = wp_parse_args($args, $default_args);
$q = new WP_Query($args);

$per_row = (!empty($args['per_row'])) ? $args['per_row'] : STM_LMS_Options::get_option('courses_per_row', 3);
$class = (!empty($args['class'])) ? $args['class'] : '';

if ($q->have_posts()):
	$course_view = 'stm_lms_courses__grid_' . STM_LMS_Options::get_option('course_card_view', 'center');
	$course_style = STM_LMS_Options::get_option('course_card_style', 'style_1');

	stm_lms_register_style('courses');
	stm_lms_register_style("courses/{$course_style}");

	if (empty($args['isAjax'])): ?>
        <div class="stm_lms_courses__grid stm_lms_courses__grid_<?php echo esc_attr($per_row . ' ' . $course_view); ?> <?php echo esc_attr($class); ?>"
        data-pages="<?php echo ceil($q->found_posts / $args['posts_per_page']); ?>">
	<?php endif; ?>

	<?php while ($q->have_posts()):
	    global $post;
        $vars = array();
        $q->the_post();
        $vars['id'] = $id = get_the_ID();
        $vars['price'] = $price = get_post_meta($id, 'price', true);
        $vars['sale_price'] = $sale_price = STM_LMS_Course::get_sale_price($id);

        if (empty($price) and !empty($sale_price)) {
			$vars['price'] = $sale_price;
			$vars['sale_price'] = '';
        }

        $vars['has_sale_price'] = !empty($sale_price) ? 'has-sale' : 'no-sale';
	    $vars['author_id'] = $post->post_author;
	    $vars['style'] = $course_style;
	    $vars['featured'] = get_post_meta($id, 'featured', true);

	    STM_LMS_Templates::show_lms_template("courses/styles/{$course_style}", $vars);

    endwhile; ?>
        <?php if (empty($args['isAjax'])): ?>
        </div>
    <?php endif; ?>

<?php else: ?>
    <h4><?php esc_html_e('No courses found.', 'masterstudy-lms-learning-management-system'); ?></h4>
<?php endif;
wp_reset_postdata();
?>