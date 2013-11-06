<?php

/* Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register the paytrail.fi gateway settings
 *
 * @access      public
 * @since       1.0
 * @return      array
 */

function edd_paytrail_add_settings( $settings ) {

	$paytrail_settings = array(
		array(
			'id'    => 'paytrail_settings',
			'name'  => '<strong>' . __( 'Paytrail Settings', 'edd-paytrail' ) . '</strong>',
			'desc'  => __( 'Configure the Paytrail settings', 'edd-paytrail' ),
			'type'  => 'header'
		),
		array(
			'id'    => 'paytrail_merchant_id',
			'name'  => __( 'Merchant ID', 'edd-paytrail' ),
			'desc'  => __( 'Enter your Paytrail Merchant ID. This is needed in order to take payment.', 'edd-paytrail' ),
			'type'  => 'text',
			'size'  => 'regular'
		),
		array(
			'id'    => 'paytrail_merchant_secret',
			'name'  => __( 'Merchant Secret', 'edd-paytrail' ),
			'desc'  => __( 'Enter your Paytrail Merchant Secret. This is needed in order to take payment.', 'edd-paytrail' ),
			'type'  => 'text',
			'size'  => 'regular'
		),
		array(
			'id'    => 'paytrail_test_merchant_id',
			'name'  => __( 'Test Merchant ID', 'edd-paytrail' ),
			'desc'  => __( 'Enter your Paytrail test Merchant ID.', 'edd-paytrail' ),
			'type'  => 'text',
			'size'  => 'regular'
		),
		array(
			'id'    => 'paytrail_test_merchant_secret',
			'name'  => __( 'Test Merchant Secret', 'edd-paytrail' ),
			'desc'  => __( 'Enter your Paytrail test Merchant Secret.', 'edd-paytrail' ),
			'type'  => 'text',
			'size'  => 'regular'
		)
	);

	return array_merge( $settings, $paytrail_settings );

}
add_filter( 'edd_settings_gateways', 'edd_paytrail_add_settings' );

?>