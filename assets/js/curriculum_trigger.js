(function ($) {
    $(document).ready(function(){

        var $trigger = $('.stm-lms-curriculum-trigger');

        $trigger.on('click', function(){
            $('body').toggleClass('curriculum-opened');
        });

        $('.stm-curriculum__close, .stm-lms-course__overlay').on('click', function(){
            $('body').removeClass('curriculum-opened');
        });

        if(location.hash === '#curriculum_trigger' && $trigger.length) {
            $trigger.click();
            $('.stm-curriculum-section:not(.opened)').each(function() {
                $(this).closest('.stm-curriculum-section').find('.stm-curriculum-section__lessons').slideToggle();
                $(this).find('.stm-curriculum-item__section').addClass('opened');
            });
        }

    });

})(jQuery);