<script>
	<?php
	ob_start();
	include STM_LMS_PATH . '/post_type/metaboxes/components/answers.php';
	$template = preg_replace( "/\r|\n/", "", addslashes(ob_get_clean()));
	?>
    Vue.component('stm-answers', {
        props: ['stored_answers', 'choice', 'origin'],
        data: function () {
            return {
                questions: [],
                new_answer: '',
                correctAnswer: '',
                correctAnswers: {},
                previousAnswers: [],
                previousCorrectAnswer: '',
                isEmpty: false,
                timeout : ''
            }
        },
        mounted: function() {
            var vm = this;
            this.questions = this.stored_answers;


            if(this.questions !== '' && typeof this.questions !== 'undefined') {
                this.questions.forEach(function (answer) {
                    if (answer.isTrue === '1' || answer.isTrue === 1) {
                        vm.correctAnswer = answer.text;

                        vm.$set(vm.correctAnswers, answer.text, answer.isTrue);
                    }
                })
            } else {
                this.questions = [];
            }
        },
        template: '<?php echo stm_lms_filtered_output($template); ?>',
        methods: {
            addAnswer: function () {
                var vm = this;
                var exists = false;
                vm.isEmpty = false;
                clearTimeout(vm.timeout);
                /*Check if answer is typed*/
                if(this.new_answer.length < 1) {
                    vm.isEmpty = true;
                    vm.timeout = setTimeout(function(){
                        vm.isEmpty = false;
                    }, 800);

                    return false;
                }
                /*Check if answer exists*/

                if(typeof vm.questions !== 'undefined') {
                    vm.questions.forEach(function (v, k) {
                        if (v['text'] == vm.new_answer) exists = true;
                    });
                } else {
                    exists = false;
                }

                if(exists) return;

                this.questions.push({
                    text : vm.new_answer,
                    isTrue : 0
                });

                this.new_answer = '';
            },
            isAnswer() {
                var vm = this;
                this.questions.forEach(function(value, key){
                    vm.questions[key]['isTrue'] = (vm.correctAnswer === value.text) ? 1 : 0;
                });
            },
            isAnswers() {
                var vm = this;
                this.questions.forEach(function(value, key){
                    var answer = value.text;
                    vm.questions[key]['isTrue'] = (typeof vm.correctAnswers[answer] !== 'undefined' && vm.correctAnswers[answer]) ? 1 : 0;
                });
            },
            deleteAnswer(k) {
                this.questions.splice(k, 1);
            }
        },
        watch: {
            questions: function(value) {
                this.$emit('get-answers', value);
            },
            choice: function(value) {
                var vm = this;
                if(value === 'true_false') {
                    vm.previousAnswers = vm.questions.slice(0);
                    vm.previousCorrectAnswer = vm.correctAnswer;
                    vm.questions = [
                        {
                            'text' : '<?php esc_html_e('True', 'masterstudy-lms-learning-management-system'); ?>',
                            'isTrue' : 1,
                        },
                        {
                            'text' : '<?php esc_html_e('False', 'masterstudy-lms-learning-management-system'); ?>',
                            'isTrue' : 0,
                        },
                    ];

                    vm.correctAnswer = "<?php esc_html_e('True', 'masterstudy-lms-learning-management-system'); ?>";

                } else {
                    if(vm.previousAnswers.length) {
                        vm.correctAnswer = vm.previousCorrectAnswer;
                        vm.questions = vm.previousAnswers;
                    }
                }
            }
        }
    })
</script>