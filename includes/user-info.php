<?php

/* Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add phone number and company name to personal info.
 *
 * @access      public
 * @since       1.0
 */
function edd_paytrail_custom_checkout_fields() { 
	
	/* Return if paytrail is not chosen payment gateway and site owner don't want to show additional user info. */
	if ( 'paytrail' !== edd_get_chosen_gateway() || ! edd_paytrail_show_extra_user_info() ) {
		return;
	}
	?>
	
	<p id="edd-paytrail-phone-wrap">
		<label class="edd-label" for="edd-paytrail-phone"><?php echo apply_filters( 'edd_paytrail_phone_label', __( 'Phone Number', 'edd-paytrail' ) ); ?></label>
		<span class="edd-description"><?php echo apply_filters( 'edd_paytrail_phone_description', __( 'Enter your phone number.', 'edd-paytrail' ) ); ?></span>
		<input class="edd-input" type="text" name="edd_paytrail_phone" id="edd-paytrail-phone" placeholder="<?php _e( 'Phone Number', 'edd-paytrail' ); ?>" value="" />
	</p>
	<p id="edd-paytrail-company-wrap">
		<label class="edd-label" for="edd-paytrail-company"><?php echo apply_filters( 'edd_paytrail_company_label', __( 'Company Name', 'edd-paytrail' ) ); ?></label>
		<span class="edd-description"><?php echo apply_filters( 'edd_paytrail_company_description', __( 'Enter the name of your company.', 'edd-paytrail' ) ); ?></span>
		<input class="edd-input" type="text" name="edd_paytrail_company" id="edd-paytrail-company" placeholder="<?php _e('Company Name', 'edd-paytrail'); ?>" value="" />
	</p>
	
	<?php
	
}
add_action( 'edd_purchase_form_user_info', 'edd_paytrail_custom_checkout_fields' );

/**
 * Store the custom field data (phone and company) in the payment meta.
 *
 * @access      public
 * @since       1.0
 */
function edd_paytrail_store_custom_fields( $payment_meta ) {

	/* Return if site owner don't want to show additional user info. */
	if ( edd_paytrail_show_extra_user_info() ) {
		$payment_meta['phone']   = isset( $_POST['edd_paytrail_phone'] ) ? sanitize_text_field( $_POST['edd_paytrail_phone'] ) : '';
		$payment_meta['company'] = isset( $_POST['edd_paytrail_company'] ) ? sanitize_text_field( $_POST['edd_paytrail_company'] ) : '';
	}
	
	/* Payment meta. */
	return $payment_meta;
	
}
add_filter( 'edd_payment_meta', 'edd_paytrail_store_custom_fields' );

/**
 * Show the custom fields phone and company in the "View Order Details" popup.
 *
 * @access      public
 * @since       1.0
 */
function edd_paytrail_purchase_details( $payment_meta, $user_info ) {

	/* Return if site owner don't want to show additional user info. */
	if ( ! edd_paytrail_show_extra_user_info() ) {
		return;
	}
	
	/* Show phone number and company. */
	$phone   = isset( $payment_meta['phone'] ) ? esc_attr( $payment_meta['phone'] ) : '';
	$company = isset( $payment_meta['company'] ) ? esc_attr( $payment_meta['company'] ) : '';
	?>
	<li class="data"><span><?php echo __( 'Phone:', 'edd-paytrail' ) . '</span> ' . $phone; ?></li>
	<li class="data"><span><?php echo __( 'Company:', 'edd-paytrail' ) . '</span> ' . $company; ?></li>
 
	<?php

}
add_action( 'edd_payment_personal_details_list', 'edd_paytrail_purchase_details', 10, 2 );

?>