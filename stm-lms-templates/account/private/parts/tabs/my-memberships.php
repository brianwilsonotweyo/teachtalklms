<?php stm_lms_register_style('pmpro_membership');
global $pmpro_pages;
$pmpro_pages = array(
	'account' => get_option( 'pmpro_account_page_id' ),
	'billing' => get_option( 'pmpro_billing_page_id' ),
	'cancel' =>get_option( 'pmpro_cancel_page_id' ),
	'checkout' => get_option( 'pmpro_checkout_page_id' ),
	'confirmation' => get_option( 'pmpro_confirmation_page_id' ),
	'invoice' => get_option( 'pmpro_invoice_page_id' ),
	'levels' => get_option( 'pmpro_levels_page_id' )
);
?>

<div class="stm-lms-user-memberships">
    <?php echo do_shortcode('[pmpro_account]'); ?>
</div>