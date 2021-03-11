<?php stm_lms_register_script('courses'); ?>

<div class="text-center">
    <a href="#"
       class="btn btn-default stm_lms_load_more_courses"
       data-offset="1"
       data-template="courses/grid"
       data-args='<?php echo json_encode($args); ?>'>
        <span><?php esc_html_e('Load more', 'masterstudy-lms-learning-management-system') ?></span>
    </a>
</div>