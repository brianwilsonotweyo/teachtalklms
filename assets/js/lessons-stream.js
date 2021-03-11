(function ($) {


    $(document).ready(function () {
        $('.stm_lms_stream_lesson__content, .stm_lms_stream_lesson .left').resizable({
            start: function () {
                $('.stm_lms_stream_lesson').addClass('is-resizing');
            },
            stop: function () {
                $('.stm_lms_stream_lesson').removeClass('is-resizing');
            },
            minHeight: 100,
            minWidth: 200,
        });

        timer();

        var $stream_not_ended = $('.stream-is-not-ended');

        if ($stream_not_ended.length) {

            setTimeout(function () {

                $('.stream-cannot-be-completed').removeClass('stream-cannot-be-completed stream-is-not-ended').attr('data-disabled', false);

            }, $stream_not_ended.data('timer') * 1000);
        }


    });


    function timer() {
        var $timer = $('.stm_countdown');

        if (!$timer.length) return false;

        var flash = false;
        var ts = $timer.data('timer');
        $timer.countdown({
            timestamp: ts,
            callback: function (days, hours, minutes, seconds) {
                var summaryTime = days + hours + minutes + seconds;
                if (summaryTime === 0) {
                    location.reload();
                }
            }
        });
    }

})(jQuery);