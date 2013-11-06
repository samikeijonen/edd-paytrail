<?php

/* Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Remove Credit Card Form
 *
 * @access private
 * @since 1.0
 */
function edd_paytrail_add_banks() {
 
	global $edd_options;
	
	/* Use test credentials if test mode is on. */
	if( edd_is_test_mode() ) {
		$paytrail_merchant_id = $edd_options['paytrail_test_merchant_id'];
		$paytrail_merchant_secret = $edd_options['paytrail_test_merchant_secret'];
	} else {
		$paytrail_merchant_id = $edd_options['paytrail_merchant_id'];
		$paytrail_merchant_secret = $edd_options['paytrail_merchant_secret'];
	}
	
	ob_start(); ?>
		<input type="hidden" name="MERCHANT_ID" value="<?php echo $paytrail_merchant_id; ?>" />
	<?php
	echo ob_get_clean();
	
}
add_action( 'edd_paytrail_cc_form', '__return_false' );

/**
 * Register Paytrail payment gateway
 *
 * @access      public
 * @since       1.0
 * @return      array
 */
function edd_paytrail_register_gateway( $gateways ) {
	
	/* Format: ID => Name. */
	$gateways['paytrail'] = array( 'admin_label' => __( 'Paytrail', 'edd-paytrail' ), 'checkout_label' => __( 'Paytrail', 'edd-paytrail' ) );
	
	return $gateways;
	
}
add_filter( 'edd_payment_gateways', 'edd_paytrail_register_gateway' );

/**
 * Process Paytrail submission.
 *
 * @access      public
 * @since       1.0
 * @return      void
 */
function edd_paytrail_process_paytrail_payment( $purchase_data ) {

	global $edd_options;
	
	/* Use test credentials if test mode is on. */
	if( edd_is_test_mode() ) {
		$paytrail_merchant_id = $edd_options['paytrail_test_merchant_id'];
		$paytrail_merchant_secret = $edd_options['paytrail_test_merchant_secret'];
	} else {
		$paytrail_merchant_id = $edd_options['paytrail_merchant_id'];
		$paytrail_merchant_secret = $edd_options['paytrail_merchant_secret'];
	}
	
	/* Load the paytrail module payment file. */
	require_once( EDD_PAYTRAIL_INCLUDES . 'Verkkomaksut_Module_Rest.php' );

	/* Get errors. */
	$errors = edd_get_errors();

	if ( !$errors ) {
	
		/* Set payment data. */
		$payment_data = array(
			'price' 		=> $purchase_data['price'],
			'date' 			=> $purchase_data['date'],
			'user_email' 	=> $purchase_data['user_email'],
			'purchase_key' 	=> $purchase_data['purchase_key'],
			'currency' 		=> $edd_options['currency'],
			'downloads' 	=> $purchase_data['downloads'],
			'cart_details' 	=> $purchase_data['cart_details'],
			'user_info' 	=> $purchase_data['user_info'],
			'status' 		=> 'pending'
		);
	
		/* Record the pending payment. This returns payment ID if everything is ok. */
		$payment_record = edd_insert_payment( $payment_data );
		
		/* Process payment data. */
		if( $payment_record && 'EUR' == $edd_options['currency'] ) {

			/* Add note to payment notes. */
			edd_insert_payment_note( $payment_record, sprintf( __( 'Status changed to Pending', 'edd-paytrail' ) ) );
			
		}
		else {
			
			/* Payment could not be recorded. */
			if( $payment_record )
				edd_set_error( 'authorize_error', __( 'Error: your payment could not be recorded. Please try again', 'edd-paytrail' ) );
			
			/* Use EUR with Paytrail. */
			if( 'EUR' !== $edd_options['currency'] )
				edd_set_error( 'currency_error', __( 'Error: Paytrail only accepts EUR as currency. Contact the site admin.', 'edd-paytrail' ) );
			
			/* Send back to checkout. */
			edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] );			
			
		}	

		/* An object is created to model all payment return urls. */
		$urlset = new Verkkomaksut_Module_Rest_Urlset(
			add_query_arg( 'confirm_payment_id', $payment_record, edd_get_success_page_uri() ), // return url for successful payment
			edd_get_failed_transaction_uri(),                                                   // return url for failed payment
			edd_get_success_page_uri(),                                                         // url for payment confirmation from SV server
			""                                                                                  // pending url is not in use
		);

		/* Payment creation. */
		$orderNumber = $purchase_data['purchase_key']; // Use distinguished order number
		$price = $purchase_data['price'];              // Total (incl. VAT)
		$payment = new Verkkomaksut_Module_Rest_Payment_S1( $orderNumber, $urlset, $price );

		/* Set locale. The default language is en_US. See other options from PHP class. */
		$payment->setLocale( edd_paytrail_locale() );

		/* Sending payment to Paytrail service and handling possible errors. */
		$module = new Verkkomaksut_Module_Rest( $paytrail_merchant_id, $paytrail_merchant_secret );
		try {
			$result = $module->processPayment( $payment );	
		}
		catch( Verkkomaksut_Exception $e ) {
			// processing the error
			// Error description available $e->getMessage()
			edd_set_error( 'authorize_error', __( 'Error: we could not create your payment to Paytrail. Please try again', 'edd-paytrail' ) );
			edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] );
		}
		
		/* Using url address Paytrail returned for the desired payment method. User is immediately directed to the received address here. */
		header("Location: {$result->getUrl()}");
	
	} // end ! $errors 
	else {
		edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] );
	}
	
}
add_action( 'edd_gateway_paytrail', 'edd_paytrail_process_paytrail_payment' );

/**
 * Confirm and check that the Paytrail payment was valid.
 *
 * @since  1.0
 * @return void
 */
function edd_paytrail_confirm_payment() {

	global $edd_options;
	
	/* Use test credentials if test mode is on. */
	if( edd_is_test_mode() ) {
		$paytrail_merchant_id = $edd_options['paytrail_test_merchant_id'];
		$paytrail_merchant_secret = $edd_options['paytrail_test_merchant_secret'];
	} else {
		$paytrail_merchant_id = $edd_options['paytrail_merchant_id'];
		$paytrail_merchant_secret = $edd_options['paytrail_merchant_secret'];
	}
	
	/* Load the paytrail module payment file. */
	require_once( EDD_PAYTRAIL_INCLUDES . 'Verkkomaksut_Module_Rest.php' );
	
	/* Check id from payment. */
	$module = new Verkkomaksut_Module_Rest( $paytrail_merchant_id, $paytrail_merchant_secret );
	
	/* Check that we are on success page and payment id is set. After that check for valid payment. */
	if ( isset( $_GET['confirm_payment_id'] ) && is_page( $edd_options['success_page'] ) ) {
	
		if( $module->confirmPayment( $_GET['ORDER_NUMBER'], $_GET['TIMESTAMP'], $_GET['PAID'], $_GET['METHOD'], $_GET['RETURN_AUTHCODE'] ) ) {
			
			/* Update payment status. */
			edd_update_payment_status( esc_attr( $_GET['confirm_payment_id'] ), 'publish' );
			
			/* Add transaction ID to payment notes. */
			edd_insert_payment_note( esc_attr( $_GET['confirm_payment_id'] ), sprintf( __( 'Paytrail order number: %s', 'edd-paytrail' ), esc_attr( $_GET['ORDER_NUMBER'] ) ) );
			
		}
		else {
			/* Payment receipt was not valid, possible payment fraud attempt. */
			edd_set_error( 'authorize_error', __( 'Error: your payment could not be processed. Please try again', 'edd-paytrail' ) );
			edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] );
		}
	
	}
	
}
add_action( 'template_redirect', 'edd_paytrail_confirm_payment' );

/**
 * Admin Messages
 *
 * @access      private
 * @since       1.0
 * @return      void
*/

function edd_paytrail_admin_messages() {

	global $typenow, $edd_options;
	
	if ( 'download' != $typenow )
		return;

	if ( current_user_can( 'manage_shop_settings' ) && 'EUR' !== $edd_options['currency'] && edd_is_gateway_active( 'paytrail' ) ) {
		add_settings_error( 'edd-paytrail-notices', 'edd-payment-sent', sprintf( __( 'Note: You need to use EUR currency for Paytrail to work. Go to %ssettings%s.', 'edd' ), '<a href="' . admin_url( 'edit.php?post_type=download&page=edd-settings' ) . '">', '</a>' ), 'updated' );
	}

	settings_errors( 'edd-paytrail-notices' );
	   
}
add_action( 'admin_notices', 'edd_paytrail_admin_messages' );

/**
 * Enqueue scripts.
 *
 * @access      public
 * @since       1.0
 * @return      void
 */

function edd_paytrail_scripts() {
	
	$js_dir = EDD_PAYTRAIL_URL . 'js/';
	
	/* Load js only on checkout page and when paytrail is active. */
	if ( edd_is_checkout() && edd_is_gateway_active( 'paytrail' ) ) {
 
		wp_enqueue_script( 'edd-paytrail-payment-widget',  $js_dir . 'payment-widget.js', array( 'jquery' ), EDD_PAYTRAIL_VERSION, true );
		wp_enqueue_script( 'edd-paytrail-payment-widget-settings', $js_dir . 'settings-payment-widget.js', array( 'edd-paytrail-payment-widget' ), EDD_PAYTRAIL_VERSION, true );
	
	}
	
 }
//add_action( 'wp_enqueue_scripts', 'edd_paytrail_scripts' );

/**
 * Get locale for payment page.
 *
 * @access      public
 * @since       1.0
 * @return      void
 */

function edd_paytrail_locale() {

	/* Valid locales are fi_FI, en_US and sv_SE. */
	$edd_paytrail_valid_locales = array(
		'fi_FI', 'en_US', 'sv_SE'
	);

	$locale = get_locale();

	/* convert locales like "fi" to "fi_FI", in case that works for the given locale (sometimes it does). */
	if ( strlen( $locale ) == 2 ) {
		$locale = strtolower( $locale ) . '_' . strtoupper( $locale );
	}

	/* convert things like en-US to en_US. */
	$locale = str_replace( '-', '_', $locale );

	/* check to see if the locale is a valid one, if not, use en_US as a fallback */
	if ( !in_array( $locale, $edd_paytrail_valid_locales ) ) {
		$locale = 'en_US';
	}
	
	return $locale;

}

?>