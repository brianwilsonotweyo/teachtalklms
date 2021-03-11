(function($) {
    $(document).ready(function(){
        new Vue({
            el: '#stm_lms_instructor_courses',
            data: {
                vue_loaded: true,
                courses: [],
                loading: true,
                offset: 0,
                total: false,
                quota: {}
            },
            created: function() {
                this.getCourses();
            },
            methods: {
                getCourses() {
                    var vm = this;
                    var url = stm_lms_ajaxurl + '?action=stm_lms_get_instructor_courses&nonce=' + stm_lms_nonces['stm_lms_get_instructor_courses'] + '&offset=' + vm.offset;
                    vm.loading = true;

                    this.$http.get(url).then(function (response) {
                        response.body['posts'].forEach(function(course){
                            vm.courses.push(course);
                        });
                        vm.total = response.body['total'];
                        vm.loading = false;
                        vm.offset++;
                    });
                },
                loadCourses() {
                    this.getCourses();
                },
                changeFeatured(course) {
                    var vm = this;
                    var url = stm_lms_ajaxurl + '?action=stm_lms_change_featured&nonce=' + stm_lms_nonces['stm_lms_change_featured'] + '&post_id=' + course.id;
                    this.$set(course, 'changingFeatured', true);
                    this.$http.get(url).then(function (response) {
                        vm.$set(vm, 'quota', response.body);
                        vm.$set(course, 'changingFeatured', false);
                        vm.$set(course, 'is_featured', response.body['featured']);
                    });
                }
            }
        });
    });
})(jQuery);