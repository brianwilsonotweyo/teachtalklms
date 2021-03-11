(function ($) {
    $(document).ready(function () {

        var timeout;

        $('.enter_keyword_to_fill').on('keyup', function() {

            clearTimeout(timeout);

            var $this = $(this);
            var $parent = $this.closest('.stm_lms_question_item_keywords');
            var answers = window[$parent.attr('data-quiz')];
            var user_answer = $this.val().toLowerCase();

            timeout = setTimeout(function(){
                /*Check if user has answer in input*/
                answers.forEach(function(answer, answer_index) {

                    if(user_answer.includes(answer)) {
                        $this.val('').focus();

                        $insertTo = $parent.find('.stm_lms_question_item_keywords__answer_' + answer_index + ' .value');

                        $flying = $parent.find('.flying_word').text(answer);
                        var childPos = $insertTo.offset();
                        var parentPos = $insertTo.closest('.stm_lms_question_item_keywords').offset();
                        $flying.addClass('visible').css({
                            top: childPos.top - parentPos.top + 8,
                            left: childPos.left - parentPos.left + 16
                        });

                        setTimeout(function(){
                            $insertTo.text(answer);
                            answers[answer_index] = '(^-^*)/';
                            $flying.text('').css({
                                top: '5px',
                                left: '14px'
                            }).removeClass('visible');

                            addAnswer();
                        }, 500);
                    }
                });
            }, 300);

        });

        function addAnswer() {
            $('.stm_lms_question_item_keywords').each(function() {
                var answers = [];

                $(this).find('.stm_lms_question_item_keywords__answer').each(function() {
                    answers.push($(this).find('.value').text());
                });

                var $input = $(this).find('.stm_lms_question_item_keywords__input');

                var keywords_val = "[stm_lms_keywords]" + answers.join('[stm_lms_sep]');

                $input.val(keywords_val);

            });
        }


    });
})(jQuery);