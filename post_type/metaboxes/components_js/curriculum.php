<script>
	<?php
	ob_start();
	include STM_LMS_PATH . '/post_type/metaboxes/components/stm-curriculum.php';
	$template = preg_replace( "/\r|\n/", "", ob_get_clean() );
	?>

    Vue.component('v-select', VueSelect.VueSelect);
    Vue.component('stm-curriculum', {
        props: ['posts', 'stored_ids'],
        data: function () {
            return {
                items: [],
                search: '',
                add_new_lesson: '',
                add_new_section: '',
                add_new_quiz: '',
                options: [],
                loading: true,
                ids: [],
                list:[
                    {name:"John"},
                    {name:"Joao"},
                    {name:"Jean"}
                ],
                sectionEmpty: false,
                lessonEmpty: false,
                quizEmpty: false,
                timeout : ''
            }
        },
        template: '<?php echo stm_lms_filtered_output($template); ?>',
        created: function() {
            if(this.stored_ids) {
                this.getPosts(stm_lms_ajaxurl + '?action=stm_curriculum&nonce='+ stm_lms_nonces['stm_curriculum'] + '&posts_per_page=-1&orderby=post__in&ids=' + this.stored_ids + '&post_types=' + this.posts.join(','), 'items');
            } else {
                this.isLoading(false);
            }

            this.updateIds();

            console.log(this.items);
        },
        methods: {
            emitMethod (item) {
                STM_LMS_EventBus.$emit('STM_LMS_Curriculum_item', item);
            },
            isLoading(isLoading) {
                this.loading = isLoading;
            },
            onSearch(search) {
                var exclude = this.ids.join(',');
                this.getPosts(stm_lms_ajaxurl + '?action=stm_curriculum&nonce='+ stm_lms_nonces['stm_curriculum'] + '&exclude_ids=' + exclude + '&s=' + search + '&post_types=' + this.posts.join(','), 'options');
            },
            getPosts(url, variable) {
                var vm = this;
                vm.isLoading(true);
                this.$http.get(url).then(function (response) {
                    vm[variable] = response.body;
                    vm.isLoading(false);
                });
            },
            containsObject(obj, list) {
                var i;
                for (i = 0; i < list.length; i++) {
                    if (list[i]['id'] === obj['id']) {
                        return true;
                    }
                }

                return false;
            },
            updateIds() {
                var vm = this;
                vm.ids = [];
                this.items.forEach(function(value, key){
                    vm.$set(value, 'title', value.title.replace(/stm_lms_amp/g, "&"));
                    vm.ids.push(value.id);
                });
                vm.$emit('get-ids', vm.ids);
            },
            confirmDelete(item_key, message) {
                var r = confirm(message);
                if(!r) return;
                this.items.splice(item_key, 1);
            },
            callFunction(functionName, item, model) {
                functionName(item, model);
            },
            changeSection(item) {
                console.log(item);
            },
            createItem(item, model) {
                var vm = this;

                vm.quizEmpty = vm.lessonEmpty = false;
                clearTimeout(vm.timeout);
                if(this[model] === '') {

                    var itemName = (model === 'add_new_quiz') ? 'quizEmpty' : 'lessonEmpty';
                    vm[itemName] = true;
                    vm.timeout = setTimeout(function(){
                        vm[itemName] = false;
                    }, 800);

                    return false;
                }

                if(this[model] === '') return false;
                vm.isLoading(true);

                var url = stm_lms_ajaxurl + '?action=stm_curriculum_create_item&nonce='+ stm_lms_nonces['stm_curriculum_create_item'] + '&post_type=' + item + '&title=' + this[model];
                this.$http.get(url).then(function(response){
                    if(typeof response.body.status === 'undefined') this.items.push(response.body);
                    vm[model]= '';
                    vm.isLoading(false);
                });
            },
            createSection() {
                var vm = this;

                vm.sectionEmpty = false;
                clearTimeout(vm.timeout);
                if(this.add_new_section === '') {

                    vm.sectionEmpty = true;
                    vm.timeout = setTimeout(function(){
                        vm.sectionEmpty = false;
                    }, 800);

                    return false;
                }

                this.items.push({
                    id: vm.add_new_section,
                    title: vm.add_new_section
                });

            },
            changeTitle(post_id, title, itemKey) {
                if(isNaN(post_id)) {
                    this.items[itemKey]['id'] = title;
                    this.updateIds();
                } else {
                    this.$http.get(stm_lms_ajaxurl + '?action=stm_save_title&nonce=' + stm_lms_nonces['action=stm_save_title'] + '&title=' + title + '&id=' + post_id);
                }
            }
        },
        watch: {
            search: function(value) {
                if(typeof value === 'object' && value !== null && !this.containsObject(value, this.items)) {
                    this.items.push(value);
                }
            },
            items: function() {
                this.updateIds();
            }
        }
    })
</script>