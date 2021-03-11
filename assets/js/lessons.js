(function ($) {

    $(document).ready(function () {
        $('.stm_lms_complete_lesson').on('click', function(e){


            var disabled = $(this).closest('.stm-lms-lesson_navigation_complete').attr('data-disabled');

            if(disabled === 'true') return false;

            e.preventDefault();
            if($(this).hasClass('completed')) return false;
            var course = $(this).data('course');
            var lesson = $(this).data('lesson');
            $.ajax({
                url: stm_lms_ajaxurl,
                dataType: 'json',
                context: this,
                data: {
                    course: course,
                    lesson: lesson,
                    action: 'stm_lms_complete_lesson',
                    nonce: stm_lms_nonces['stm_lms_complete_lesson']
                },
                beforeSend: function () {
                    $(this).addClass('loading');
                },
                complete: function (data) {
                    var data = data['responseJSON'];
                    $(this).removeClass('loading');

                    var hasComplete = $(this).closest('[data-completed]').attr('data-completed');
                    var $button = $('.stm_lms_complete_lesson[data-course="' + data.course_id + '"][data-lesson="' + data.lesson_id + '"]');
                    if(typeof hasComplete !== 'undefined') {
                        $(this).closest('[data-completed]').removeClass('uncompleted').addClass('completed');
                        $button.find('span').text(hasComplete);
                    }

                    $button.addClass('completed');
                }
            });
        });

        $('.stm_lms_video').on('click', function(){
            $(this).addClass('visible');
        })
    });

})(jQuery);
