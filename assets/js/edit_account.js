(function ($) {

    $(document).ready(function () {

        $('.stm_lms_settings_button').on('click', function () {
            $('.stm-lms-user_edit_profile_btn').click();
        });

        $('.stm-lms-user-avatar-edit .delete_avatar').on('click', function () {
            var $this = $(this);
            var $parent = $this.closest('.stm-lms-user-avatar-edit');
            $parent.addClass('loading-avatar');
            var formData = new FormData();
            formData.append('action', 'stm_lms_delete_avatar');
            formData.append('nonce', stm_lms_nonces['stm_lms_delete_avatar']);
            $this.remove();
            $.ajax({
                url: stm_lms_ajaxurl,
                type: 'POST',
                data: formData,
                processData: false,  // tell jQuery not to process the data
                contentType: false,   // tell jQuery not to set contentType
                success: function (data) {
                    $parent.removeClass('loading-avatar');
                    if (data.file) {
                        $parent.find('img').remove();
                        $parent.find('.stm-lms-user_avatar').append(data.file);
                    }
                }
            });
        });

        $('.stm-lms-user-avatar-edit input').on('change', function () {
            var $this = $(this);
            var files = $this[0].files;
            var $parent = $this.closest('.stm-lms-user-avatar-edit');
            $parent.addClass('loading-avatar');
            if (files.length) {
                var file = files[0];

                var formData = new FormData();
                formData.append('file', file);
                formData.append('action', 'stm_lms_change_avatar');
                formData.append('nonce', stm_lms_nonces['stm_lms_change_avatar']);

                $.ajax({
                    url: stm_lms_ajaxurl,
                    type: 'POST',
                    data: formData,
                    processData: false,  // tell jQuery not to process the data
                    contentType: false,   // tell jQuery not to set contentType
                    success: function (data) {
                        $parent.removeClass('loading-avatar');
                        if (data.file) {
                            $parent.find('img').attr('src', data.file);
                        }
                    }
                });

            }
        });

        $('[data-container]').on('click', function (e) {
            e.preventDefault();

            var $default_container = $('[data-container-open=".stm_lms_private_information"]');
            var $container = $('[data-container-open="' + $(this).attr('data-container') + '"]');

            var container_visible = $container.is(':visible');

            /*Close all*/
            $('[data-container]').removeClass('active');
            $('[data-container-open]').slideUp();

            /*Open Current*/
            if (!container_visible) {
                $(this).addClass('active');
                $container.slideDown();
            } else {
                $default_container.slideDown();
            }

        });

        // $('.stm-lms-user_edit_profile_btn').on('click', function(e){
        //     e.preventDefault();
        //     console.log($(this).is(':visible'));
        //     $(this).toggleClass('active');
        //
        //     $('.stm_lms_private_information, .stm_lms_edit_account').slideToggle();
        // });

        $('body').addClass('stm_lms_chat_page');

        new Vue({
            el: '#stm_lms_edit_account',
            data: function () {
                return {
                    data: stm_lms_edit_account_info,
                    loading: false,
                    message: '',
                    status: 'error',
                }
            },
            methods: {
                saveUserInfo() {
                    var vm = this;
                    var data = "&description=" + vm.data.meta['description'];
                    data += "&facebook=" + vm.data.meta['facebook'];
                    data += "&first_name=" + vm.data.meta['first_name'];
                    data += "&last_name=" + vm.data.meta['last_name'];
                    data += "&twitter=" + vm.data.meta['twitter'];
                    data += "&instagram=" + vm.data.meta['instagram'];
                    data += "&google-plus=" + vm.data.meta['google-plus'];
                    data += "&position=" + vm.data.meta['position'];
                    if (typeof vm.data.meta['new_pass'] !== 'undefined') {
                        data += "&new_pass=" + vm.data.meta['new_pass'];
                    }
                    if (typeof vm.data.meta['new_pass_re'] !== 'undefined') {
                        data += "&new_pass_re=" + vm.data.meta['new_pass_re'];
                    }
                    var url = stm_lms_ajaxurl + '?action=stm_lms_save_user_info' + data + '&nonce=' + stm_lms_nonces['stm_lms_save_user_info'];

                    vm.loading = true;
                    vm.message = vm.status = '';

                    this.$http.get(url).then(function (response) {
                        vm.loading = false;
                        vm.message = response.body['message'];
                        vm.status = response.body['status'];

                        if (response.body['relogin']) {
                            window.location.href = response.body['relogin'];
                        }

                        // update Data
                        var data_fields = {
                            'bio': '',
                            'facebook': 'href',
                            'twitter': 'href',
                            'google-plus': 'href',
                            'position': '',
                            'first_name': '',
                            'instagram': 'href'
                        };

                        for (var k in data_fields) {
                            if (data_fields.hasOwnProperty(k)) {
                                if (data_fields[k]) {
                                    $('.stm_lms_update_field__' + k).attr(data_fields[k], vm.data['meta'][k]);
                                } else {
                                    $('.stm_lms_update_field__' + k).text(vm.data['meta'][k]);
                                }
                            }
                        }


                    });
                }
            }
        });

    });

})(jQuery);