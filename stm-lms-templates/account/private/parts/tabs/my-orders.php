<?php

stm_lms_register_style('cart');
stm_lms_register_style('user-orders');

?>

<div class="stm-lms-user-orders" v-bind:class="{'loading' : loading}">

    <h3><?php esc_html_e('My orders', 'masterstudy-lms-learning-management-system'); ?></h3>

    <div class="stm-lms-user-order" v-for="(order, key) in orders" v-bind:class="'stm-lms-user-order-' + order.id">
        <h4 class="stm-lms-user-order__title" v-html="order.order_key"></h4>
        <div class="stm-lms-user-order__status">{{ order.date_formatted }}</div>
        <div class="stm-lms-user-order__status" v-bind:class="order.status">{{ order.i18n[order.status] }}</div>
        <div class="stm-lms-user-order__more" @click="openTab(key)">
            <i class="lnr" v-bind:class="{'lnr-chevron-down' : !order.isOpened, 'lnr-chevron-right' : order.isOpened}"></i>
        </div>

        <div class="stm-lms-user-order__advanced" v-if="order.isOpened">

            <table>
                <tbody>
                <tr v-for="item in order.cart_items">
                    <td class="image">
                        <div class="stm-lms-user-order__image" v-html="item.image"></div>
                    </td>
                    <td class="name">
                        <span v-html="item.terms.join(', ') + ' >'" v-if="item.terms.length"></span>
                        <a v-bind:href="item.link" v-html="item.title"></a>
                    </td>
                    <td class="price">
                        <?php esc_html_e('Price', 'masterstudy-lms-learning-management-system'); ?>
                        <strong>{{ item.price_formatted }}</strong>
                    </td>
                </tr>
                </tbody>
            </table>

        </div>
    </div>

    <h4 v-if="!orders.length"><?php esc_html_e('No orders.', 'masterstudy-lms-learning-management-system'); ?></h4>

</div>

<a @click="getOrders()" v-if="!total" class="btn btn-default" v-bind:class="{'loading' : loading}">
    <span><?php esc_html_e('Show more', 'masterstudy-lms-learning-management-system'); ?></span>
</a>