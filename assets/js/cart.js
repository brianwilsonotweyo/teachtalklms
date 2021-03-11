(function ($) {

    $(document).ready(function () {

        new Vue({
            el: '#stm_lms_checkout',
            data: function () {
                return {
                    loading: false,
                    message: '',
                    status: 'error',
                    payment_code: '',
                    stripe : '',
                    stripe_card : '',
                    stripe_complete: false,
                }
            },
            mounted: function () {

            },
            methods: {
                purchase_courses: function() {
                    if(this.loading) return false;
                    var vm = this;
                    vm.loading = true;


                    if(vm.payment_code === 'stripe') {
                        vm.stripe.createToken(vm.stripe_card).then(function(result) {
                            if(result.error) {
                                vm.message = result.error.message;
                                vm.status = result.error;
                                vm.loading = false;
                            } else {
                                console.log(result);
                                vm.$http.get(stm_lms_ajaxurl + '?action=stm_lms_purchase&nonce=' + stm_lms_nonces['stm_lms_purchase'] + '&payment_code=' + vm.payment_code + '&token_id=' + result.token.id).then(function(response){
                                    vm.loading = false;
                                    vm.status = response.body.status;
                                    vm.message = response.body.message;

                                    if(response.body.url) window.location = response.body.url;
                                });
                            }
                        });
                    } else {

                        vm.$http.get(stm_lms_ajaxurl + '?action=stm_lms_purchase&nonce=' + stm_lms_nonces['stm_lms_purchase'] + '&payment_code=' + vm.payment_code).then(function(response){
                            vm.loading = false;
                            vm.status = response.body.status;
                            vm.message = response.body.message;

                            if(response.body.url) window.location = response.body.url;
                        });
                    }
                },
                generateStripe() {
                    var vm = this;

                    Vue.nextTick(function(){
                        vm.stripe = Stripe(stripe_id);
                        var elements = vm.stripe.elements();
                        vm.stripe_card = elements.create('card');
                        vm.stripe_card.mount('#stm-lms-stripe');

                        vm.stripe_card.addEventListener('change', function(event) {
                            vm.stripe_complete = event.complete;
                        });


                    });
                }
            },
            watch: {
                payment_code : function(value) {
                    if(value === 'stripe') {
                        this.generateStripe();
                    }
                }
            }
        });
    });

})(jQuery);
