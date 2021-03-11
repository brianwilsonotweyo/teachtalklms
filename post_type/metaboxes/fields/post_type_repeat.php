<?php
/**
 * @var $field_name
 * @var $section_name
 *
 */

$field_key = "data['{$section_name}']['fields']['{$field_name}']";
$sections = "data['{$section_name}']['fields']['curriculum_sections']['value']";

include STM_LMS_PATH . '/post_type/metaboxes/components_js/curriculum.php';
?>

<stm-curriculum
        v-bind:posts="<?php echo esc_attr($field_key) ?>['post_type']"
        v-bind:stored_ids="<?php echo esc_attr($field_key) ?>['value']"
        v-on:get-ids="<?php echo esc_attr($field_key) ?>['value'] = $event;"></stm-curriculum>

<input type="hidden"
       name="<?php echo esc_attr($field_name); ?>"
       v-model="<?php echo esc_attr($field_key); ?>['value']" />