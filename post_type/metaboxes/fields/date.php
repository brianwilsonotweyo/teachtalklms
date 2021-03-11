<?php
/**
 * @var $field_name
 * @var $section_name
 *
 */

$field_key = "data['{$section_name}']['fields']['{$field_name}']";

?>

<script type="text/javascript">
    <?php
    ob_start();
    include STM_LMS_PATH . '/post_type/metaboxes/components/date.php';
    $template = preg_replace("/\r|\n/", "", ob_get_clean());
    ?>

    Vue.component('date-picker', DatePicker.default);
    Vue.component('stm-date', {
        props: ['current_date'],
        data: function () {
            return {
                date: []
            }
        },
        mounted: function () {
            if (typeof this.current_date[0] !== 'undefined') {
                this.date.push(new Date(parseInt(this.current_date)));
            }
        },
        template: '<?php echo stm_lms_filtered_output($template); ?>',
        methods: {
            dateChanged(newDate) {
                this.$emit('date-changed', new Date(newDate).getTime());
            }
        },
    });
</script>

<label v-html="<?php echo esc_attr($field_key); ?>['label']"></label>

<stm-date v-bind:current_date="<?php echo esc_attr($field_key) ?>['value']"
          placeholder=""
          v-on:date-changed="<?php echo esc_attr($field_key) ?>['value'] = $event"></stm-date>

<input type="hidden" name="<?php echo esc_attr($field_name); ?>" v-model="<?php echo esc_attr($field_key) ?>['value']"/>