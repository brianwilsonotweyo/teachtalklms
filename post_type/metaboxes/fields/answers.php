<?php
/**
 * @var $field
 * @var $field_name
 * @var $section_name
 *
 */

$field_key = "data['{$section_name}']['fields']['{$field_name}']";
$requirements = "data['{$section_name}']['fields']['{$field['requirements']}']['value']";


include STM_LMS_PATH . '/post_type/metaboxes/components_js/answers.php';
?>

<label v-html="<?php echo esc_attr($field_key); ?>['label']"></label>

<stm-answers v-bind:stored_answers="<?php echo esc_attr($field_key); ?>['value']"
             v-on:get-answers="<?php echo esc_attr($field_key); ?>['value'] = $event"
             v-bind:choice="<?php echo sanitize_text_field($requirements); ?>"></stm-answers>

<div v-for="(answer, key) in <?php echo esc_attr($field_key); ?>['value']">
    <div v-for="(answer_data, property) in answer">
        <input type="hidden"
               v-bind:name="'<?php echo esc_attr($field_name); ?>' + '[' + key + '][' + property + ']'"
               v-model="<?php echo esc_attr($field_key); ?>['value'][key][property]" />
    </div>
</div>