(function ($) {

    $(document).ready(function () {

        new Vue({
            el: '#my-courses',
            data: function () {
                return {
                    vue_loaded: true,
                    loading: false,
                    courses: [],
                    offset: 0,
                    total: false
                }
            },
            mounted: function () {
                this.getCourses();
            },
            methods: {
                getCourses() {
                    var vm = this;
                    var url = stm_lms_ajaxurl + '?action=stm_lms_get_user_courses&offset=' + vm.offset + '&nonce=' + stm_lms_nonces['stm_lms_get_user_courses'];
                    vm.loading = true;

                    this.$http.get(url).then(function (response) {
                        if(response.body['posts']) {
                            response.body['posts'].forEach(function (course) {
                                vm.courses.push(course);
                            });
                        }
                        vm.total = response.body['total'];
                        vm.loading = false;
                        vm.offset++;
                    });
                }
            }
        });

        new Vue({
            el: '#my-quizzes',
            data: function () {
                return {
                    vue_loaded: true,
                    loading: false,
                    quizzes: [],
                    offset: 0,
                    total: false
                }
            },
            mounted: function () {
                this.getQuizzes();
            },
            methods: {
                getQuizzes() {
                    var vm = this;
                    var url = stm_lms_ajaxurl + '?action=stm_lms_get_user_quizzes&nonce='+ stm_lms_nonces['stm_lms_get_user_quizzes'] + '&offset=' + vm.offset;
                    vm.loading = true;

                    this.$http.get(url).then(function (response) {
                        if(response.body['posts']) {
                            response.body['posts'].forEach(function (course) {
                                vm.quizzes.push(course);
                            });
                        }
                        vm.total = response.body['total'];
                        vm.loading = false;
                        vm.offset++;
                    });
                }
            }
        });

        new Vue({
            el: '#my-orders',
            data: function () {
                return {
                    vue_loaded: true,
                    hash: parseFloat(window.location.hash.replace(/[^0-9]/, '')),
                    loading: false,
                    orders: [],
                    offset: 0,
                    total: false
                }
            },
            mounted: function () {
                this.getOrders();
            },
            methods: {
                getOrders() {
                    var vm = this;
                    var url = stm_lms_ajaxurl + '?action=stm_lms_get_user_orders&nonce=' + stm_lms_nonces['user_orders'] + '&offset=' + vm.offset;
                    vm.loading = true;

                    this.$http.get(url).then(function (response) {
                        if(response.body['posts']) {
                            response.body['posts'].forEach(function (order) {

                                if(order.id === vm.hash) {
                                    order['isOpened'] = true;
                                    $('a[href="#my-orders"]').click();

                                    Vue.nextTick()
                                        .then(function () {

                                            $('html, body').animate({
                                                scrollTop: $('.stm-lms-user-order-' + order.id).offset().top - 200
                                            }, 300);

                                        })
                                }

                                vm.orders.push(order);
                            });
                        }

                        vm.total = response.body['total'];
                        vm.loading = false;
                        vm.offset++;
                    });
                },
                openTab(key) {
                    var opened = (typeof this.orders[key]['isOpened'] === 'undefined') ? true : !this.orders[key]['isOpened'];
                    this.$set(this.orders[key], 'isOpened', opened);
                }
            }
        });

    });

    $(window).load(function() {
        var hash = window.location.hash;
        if(hash === '#settings') {
            $('.stm-lms-user_edit_profile_btn').click();
        }
    })

})(jQuery);
