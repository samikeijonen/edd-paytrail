<?php

/**
 * Register Paytrail payment gateway
 *
 * @access      public
 * @since       1.0
 * @return      array
 */

function edd_paytrail_register_gateway( $gateways ) {
	
	// Format: ID => Name
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

	require_once( EDD_PAYTRAIL_INCLUDES . 'Verkkomaksut_Module_Rest.php' );

	/* Get errors. */
	$errors = edd_get_errors();

	if ( !$errors ) {

		/* An object is created to model all payment return urls. */
		$urlset = new Verkkomaksut_Module_Rest_Urlset(
			edd_get_success_page_uri(), // return url for successful payment
			edd_get_success_page_uri(), // return url for failed payment
			edd_get_success_page_uri(), // url for payment confirmation from SV server
			""  // pending url is not in use
		);

		/* An object is created to model all payment return addresses. */
		$urlset = new Verkkomaksut_Module_Rest_Urlset(
			edd_get_success_page_uri(), // return address for successful payment
			edd_get_success_page_uri(), // return address for failed payment
			edd_get_success_page_uri(),  // address for payment confirmation from SV server
			""  // pending url not in use
		);

		/* Payment creation. */
		$orderNumber = $purchase_data['purchase_key']; // Use distinguished order number
		$price = $purchase_data['price']; // Total (incl. VAT)
		$payment = new Verkkomaksut_Module_Rest_Payment_S1( $orderNumber, $urlset, $price );

		/* Changing payment default settings, changing payment method selection page language into English here. the default language is Finnish. See other options from PHP class. */
		//$payment->setLocale("en_US");

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

		/* Process payment data. */
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
	
		/* record the pending payment. */
		$payment_record = edd_insert_payment( $payment_data );
		
		$merchant_payment_confirmed = false;
		
		/* Check if everything was OK. */
		if( $payment_record ) {
			
			/* Update payment status. */
			edd_update_payment_status( $payment_record, 'publish' );

			/* Add transaction ID to payment notes. */
			edd_insert_payment_note( $payment_record, sprintf( __( 'Paytrail order number: %s', 'edd-paytrail' ), esc_attr( $_GET['ORDER_NUMBER'] ) ) );
			
		}
		else {
			/* Payment receipt was not valid, possible payment fraud attempt. */
			edd_set_error( 'authorize_error', __( 'Error: your payment could not be recorded. Please try again', 'edd-paytrail' ) );
			edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] );
		}
	
	} else {
		edd_send_back_to_checkout( '?payment-mode=' . $purchase_data['post_data']['edd-gateway'] );
	}
	
}
add_action( 'edd_gateway_paytrail', 'edd_paytrail_process_paytrail_payment' );

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

?>