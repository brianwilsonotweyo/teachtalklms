(function ($) {

    $(document).ready(function () {

        new Vue({
            el: '#stm_lms_gradebook',
            data: function () {
                return {
                    courses: [],
                    offset: 0,
                    loading: true,
                    course_curriculum : {},
                    search: '',
                    by_views : false,
                }
            },
            mounted() {
                this.getCourses();
            },
            computed: {
                filteredList() {
                    return this.courses.filter(course => {
                        return course.title.toLowerCase().includes(this.search.toLowerCase())
                    })
                },
            },
            methods: {
                compare(a, b) {
                    var vm = this;
                    var key = (vm.by_views) ? 'views' : 'time';

                    if (a[key] < b[key])
                        return 1;
                    if (a[key] > b[key])
                        return -1;
                    return 0;
                },
                sortBy() {
                    this.courses = this.courses.sort(this.compare);
                },
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
                openCourse(course) {
                    var vm = this;

                    if(typeof course.data === 'undefined') {
                        vm.$set(course, 'loading', true);
                        vm.$set(course, 'opened', true);

                        var url = stm_lms_ajaxurl + '?action=stm_lms_get_course_info&nonce=' + stm_lms_pro_nonces['stm_lms_get_course_info'] + '&course_id=' + course.id;

                        this.$http.get(url).then(function (response) {
                            vm.$set(course, 'data', response.data);
                            vm.$set(course, 'loading', false);
                        });

                    } else {
                        vm.$set(course, 'opened', !course.opened);
                    }
                },
                loadStudents(course) {
                    var vm = this;

                    vm.$set(course, 'students_loading', true);

                    var url = stm_lms_ajaxurl + '?action=stm_lms_get_course_students&nonce=' + stm_lms_pro_nonces['stm_lms_get_course_students'] + '&course_id=' + course.id;

                    this.$http.get(url).then(function (response) {
                        vm.$set(course, 'students', response.body.course_students);
                        vm.$set(course, 'students_loading', false);
                        vm.$set(vm, 'course_curriculum', response.body.course_curriculum);
                    });

                }
            }
        });

    });

})(jQuery);