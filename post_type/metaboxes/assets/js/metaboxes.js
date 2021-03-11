(function ($) {
    $(document).ready(function () {

        $('[data-vue]').each(function () {

            let $this = $(this);

            let data_var = $this.attr('data-vue');

            let data = window[data_var];

            new Vue({
                el: $(this)[0],
                data: function () {
                    return {
                        loading: false,
                        data: data,
                    }
                },
                mounted: function () {
                    if (typeof this.data['section_curriculum'] !== 'undefined') {
                        console.log(this.data['section_curriculum']['fields']['curriculum']['value']);
                    }
                },
                methods: {
                    changeTab: function (tab) {
                        let $tab = $('#' + tab);
                        $tab.closest('.stm_metaboxes_grid__inner').find('.stm-lms-tab').removeClass('active');
                        $tab.addClass('active');

                        let $section = $('div[data-section="' + tab + '"]');
                        $tab.closest('.stm_metaboxes_grid__inner').find('.stm-lms-nav').removeClass('active');
                        $section.addClass('active');

                    },
                    saveSettings: function (id) {
                        var vm = this;
                        vm.loading = true;
                        this.$http.post(stm_lms_ajaxurl + '?action=stm_save_settings&nonce=' + stm_lms_nonces['stm_save_settings'] + '&name=' + id, JSON.stringify(vm.data)).then(function (response) {
                            vm.loading = false;
                        });
                    }
                }
            });

        });

    });
})(jQuery);