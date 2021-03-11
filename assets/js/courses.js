(function ($) {
    $(document).ready(function () {

        $more = $('.stm_lms_load_more_courses');

        if($('body').hasClass('stm_lms_infinite')) {
            $(window).on('scroll', function(){
                $more.each(function(){
                    if(!$(this).is(":hidden")) {
                        var $this = $(this);
                        var position = $this.position().top;
                        var top = $(document).scrollTop();
                        if(position - 100 < top) {
                            $more.click();
                        }
                    }

                });
            });
        }

        $more.on('click', function(e){

            if($(this).is(":hidden")) return false;
            if($(this).hasClass('loading')) return false;

            e.preventDefault();

            var offset = $(this).attr('data-offset');
            var template = $(this).attr('data-template');
            var args = $(this).attr('data-args');

            var $grid = $(this).closest('.stm_lms_courses').find('[data-pages]');
            var total = $grid.attr('data-pages');

            if(total == offset) return false;

            $.ajax({
                url: stm_lms_ajaxurl,
                dataType: 'json',
                context: this,
                data: {
                    offset: offset,
                    template: template,
                    args: args,
                    action: 'stm_lms_load_content',
                },
                beforeSend: function () {
                    $(this).addClass('loading');
                },
                complete: function (data) {
                    data = data['responseJSON'];
                    $(this).removeClass('loading');

                    $grid.append(data['content']);
                    $(this).attr('data-offset', data['page']);
                    hide_button($(this), data['page']);

                }
            });
        });

        $more.each(function(){
            hide_button($(this), 1);
        })
    });
})(jQuery);

function hide_button($btn, page) {
    var $container = $btn.closest('.stm_lms_courses').find('[data-pages]');
    var pages = $container.attr('data-pages');

    if(parseInt(pages) === page || parseInt(pages) < page || !$container.length) {
        $btn.slideUp();
        $btn.closest('.stm_lms_courses').addClass('all_loaded');
    } else {
        $btn.slideDown();
        $btn.closest('.stm_lms_courses').removeClass('all_loaded');
    }
}