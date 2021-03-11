<?php

if (!defined('ABSPATH')) exit; //Exit if accessed directly


/***
 * @var $post
 * @var $metabox
 * @var $args_id
 *
 */

$id = $metabox['id'];
$sections = $metabox['args'][$id];

$active = '';

$data_vue = (!empty($data_vue)) ? '' : "data-vue='" . str_replace('\'', '`', json_encode($sections)) . "'";

$random_vue = uniqid('stm_lms_settings_');

$data_random_vue = json_encode($sections);

if($id === 'stm_courses_curriculum') {
    $data_random_vue = str_replace('&', 'stm_lms_amp', $data_random_vue);
}

?>

    <script>
        var <?php echo stm_lms_filtered_output($random_vue); ?> = <?php echo stm_lms_filtered_output($data_random_vue); ?>;
    </script>

<?php if (!empty($sections)): ?>
    <div class="stm_metaboxes_grid" data-vue="<?php echo esc_attr($random_vue); ?>">
        <div class="stm_metaboxes_grid__inner">

            <div class="container">

                <div class="stm-lms-tab-nav">
                    <?php
                    $i = 0;
                    foreach ($sections as $section_name => $section):
                        if ($i == 0) $active = $section_name;
                        ?>
                        <div class="stm-lms-nav <?php if ($active == $section_name) echo 'active'; ?>"
                             data-section="<?php echo esc_attr($section_name); ?>"
                             @click="changeTab('<?php echo esc_attr($section_name); ?>')">
                            <?php echo sanitize_text_field($section['name']); ?>
                        </div>
                        <?php $i++; endforeach; ?>
                </div>

                <?php foreach ($sections as $section_name => $section): ?>
                    <div id="<?php echo esc_attr($section_name); ?>"
                         class="stm-lms-tab <?php if ($section_name == $active) echo 'active'; ?>">
                        <div class="container container-constructed">
                            <div class="row">

                                <div class="column">
                                    <?php foreach ($section['fields'] as $field_name => $field):
                                        $dependency = stm_lms_metaboxes_deps($field, $section_name);
                                        $width = (empty($field['columns'])) ? 'column-1' : "column-{$field['columns']}";
                                        $is_pro = (!empty($field['pro'])) ? 'is_pro' : 'not_pro';
                                        $description = (!empty($field['description'])) ? $field['description'] : '';
                                        if (STM_LMS_Helpers::is_pro()) $is_pro = '';
                                        ?>
                                        <transition name="slide-fade">
                                            <div class="<?php echo esc_attr($width . ' ' . $is_pro); ?>" <?php echo sanitize_text_field($dependency); ?>>

                                                <?php if ($is_pro === 'is_pro'): ?>
                                                    <span><?php _e('Available in <a href="https://stylemixthemes.com/plugins/masterstudy-pro/" target="_blank">Pro Version</a>', 'masterstudy-lms-learning-management-system'); ?></span>
                                                <?php endif; ?>

                                                <?php include STM_LMS_PATH . '/post_type/metaboxes/fields/' . $field['type'] . '.php'; ?>
                                                <?php if (!empty($description)): ?>
                                                    <p class="description"><?php echo html_entity_decode($description); ?></p>
                                                <?php endif; ?>
                                            </div>
                                        </transition>
                                    <?php endforeach; ?>
                                </div>

                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>


        </div>
    </div>
<?php endif; ?>