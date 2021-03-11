(function ($) {

    $(document).ready(function () {

        new Vue({
            el: '#stm_lms_enterprise_groups',
            data: function () {
                return {
                    loading: false,
                    groups: [],
                    groupData: {
                        'group_id': '',
                        'title': '',
                        'emails': [],
                    },
                    newEmail: '',
                    status: '',
                    message: '',
                    external_data: window['stm_lms_groups'],
                    file: '',
                    file_message: ''
                }
            },
            mounted() {
                if (typeof this.group === 'undefined') this.$set(this, 'group', []);
                this.fetchGroups();
            },
            computed: {},
            methods: {
                fetchGroups: function () {
                    var vm = this;
                    var url = stm_lms_ajaxurl + '?action=stm_lms_get_enterprise_groups&nonce=' + stm_lms_nonces['stm_lms_get_enterprise_groups'];
                    vm.loading = true;

                    this.$http.get(url).then(function (response) {

                        vm.$set(vm, 'groups', response.body);
                        vm.loading = false;

                    });
                },
                deleteGroup(group) {
                    var vm = this;
                    if (confirm('Do you really want to delete group "' + group.title + '"?')) {

                        var url = stm_lms_ajaxurl + '?action=stm_lms_delete_enterprise_group&nonce=' + stm_lms_nonces['stm_lms_delete_enterprise_group'] + '&group_id=' + group.group_id;
                        vm.loading = true;

                        vm.$http.get(url).then(function (response) {

                            vm.fetchGroups();
                            vm.loading = false;

                        });
                    }
                },
                addNewEmail: function () {
                    var vm = this;
                    if (!vm.validEmail(vm.newEmail)) return false;
                    if (vm.groupData['emails'].includes(vm.newEmail)) return false;

                    if (typeof vm.groupData['emails'] === 'string') vm.groupData['emails'] = [];

                    vm.groupData['emails'].push(vm.newEmail);
                    vm.newEmail = '';

                },
                validEmail: function (email) {
                    var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                    return re.test(email);
                },
                addGroup() {
                    var vm = this;
                    if (!vm.groupData.title) return false;
                    if (!vm.groupData.emails.length) return false;

                    var url = stm_lms_ajaxurl + '?action=stm_lms_add_enterprise_group&nonce=' + stm_lms_nonces['stm_lms_add_enterprise_group'];
                    vm.loading = true;


                    console.log(vm.groupData);

                    this.$http.post(url, vm.groupData).then(function (response) {

                        var status = response.body.status;
                        var message = response.body.message;

                        vm.status = status;
                        vm.message = message;

                        if (status !== 'error') {
                            vm.fetchGroups();
                        }

                        vm.loading = false;
                        vm.resetGroupEditing();

                    });
                },
                resetGroupEditing() {
                    this.groupData = {
                        'group_id': '',
                        'title': '',
                        'emails': [],
                    };
                },
                handleFileUpload() {
                    this.$set(this, 'file', this.$refs['lms_group_csv'].files[0]);
                },
                submitFile() {
                    let vm = this;
                    let formData = new FormData();
                    formData.append('file', this.file);

                    var url = stm_lms_ajaxurl + '?action=stm_lms_import_groups&nonce=' + stm_lms_nonces['stm_lms_import_groups'];

                    vm.loading = true;

                    vm.$http.post(url, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }).then(function (r) {
                        var res = r.body;

                        vm.loading = false;

                        if(res.message) {
                            vm.file_message = res.message;

                            return false;
                        }

                        vm.fetchGroups();

                        vm.clearFile();

                    });
                },
                clearFile() {
                    this.$set(this, 'file', '');
                    this.$set(this, 'file_message', '');
                }
            }
        });

    });

})(jQuery);