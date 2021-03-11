var stm_lms_get_image_mixin = {
    data: function () {
        return {
            image_url: '',
        }
    },
    created() {
        if(this.$options.propsData.stored_image) {
            this.get_image_url(this.$options.propsData.stored_image);
        }
    },
    methods: {
        get_image_url(image_id) {
            this.$http.get(stm_lms_ajaxurl + '?action=stm_lms_get_image_url&nonce=' + stm_lms_nonces['get_image_url'] + '&image_id=' + image_id).then(function (response) {
                this.image_url = response.body;
            });
        }
    }
}