<?php
/**
 * @var $name
 * @var $course_number
 * @var $used_quotas
 * @var $course_id
 * @var $quotas_left
 */
?>

<div class="modal fade stm-lms-use-subscription" tabindex="-1" role="dialog" aria-labelledby="stm-lms-use-subscription">
    <div class="modal-dialog" role="document">
        <div class="modal-content">

            <div class="modal-body">

				<?php STM_LMS_Templates::show_lms_template(
				        'account/use_membership',
                        compact('name', 'course_number', 'used_quotas', 'course_id', 'quotas_left')
                ); ?>

            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    (function ($) {
        $('[data-lms-usemembership]').on('click', function (e) {
            e.preventDefault();

            var course_id = $(this).attr('data-lms-course');

            if(typeof stm_lms_course_id !== 'undefined') course_id = stm_lms_course_id;

            $.ajax({
                url: stm_lms_ajaxurl,
                dataType: 'json',
                context: this,
                data: {
                    action: 'stm_lms_use_membership',
                    nonce: stm_lms_nonces['stm_lms_use_membership'],
                    course_id: course_id,
                },
                beforeSend: function () {
                    $(this).addClass('loading');
                },
                complete: function (data) {
                    var data = data['responseJSON'];
                    $(this).removeClass('loading');
                    window.location.href = data['url'];
                }
            });
        });
    })(jQuery)
</script>