<script>
	<?php
	ob_start();
	include STM_LMS_PATH . '/post_type/metaboxes/components/autocomplete.php';
	$template = preg_replace( "/\r|\n/", "", ob_get_clean() );
	?>

    Vue.component('v-select', VueSelect.VueSelect);
    Vue.component('stm-autocomplete', {
        props: ['posts', 'stored_ids'],
        data: function () {
            return {
                ids: [],
                items: [],
                search: '',
                options: [],
                loading: true,
            }
        },
        template: '<?php echo stm_lms_filtered_output($template); ?>',
        created: function() {
            if(this.stored_ids) {
                this.getPosts(stm_lms_ajaxurl + '?action=stm_curriculum&nonce='+ stm_lms_nonces['stm_curriculum'] + '&posts_per_page=-1&orderby=post__in&ids=' + this.stored_ids + '&post_types=' + this.posts.join(','), 'items');
            } else {
                this.isLoading(false);
            }
        },
        methods: {
            isLoading(isLoading) {
                this.loading = isLoading;
            },
            onSearch(search) {
                var _this = this;
                var exclude = _this.ids.join(',');
                var post_types = _this.posts.join(',');
                _this.getPosts(stm_lms_ajaxurl + '?action=stm_curriculum&nonce='+ stm_lms_nonces['stm_curriculum'] + '&exclude_ids=' + exclude + '&s=' + search + '&post_types=' + post_types, 'options');
            },
            getPosts(url, variable) {
                var vm = this;
                vm.isLoading(true);
                this.$http.get(url).then(function (response) {
                    vm[variable] = response.body;
                    vm.isLoading(false);
                });
            },
            updateIds() {
                var vm = this;
                vm.ids = [];
                this.items.forEach(function(value, key){
                    vm.ids.push(value.id);
                });
                vm.$emit('autocomplete-ids', vm.ids);
            },
            callFunction(functionName, item, model) {
                functionName(item, model);
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
            removeItem(index) {
                this.items.splice(index, 1);
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