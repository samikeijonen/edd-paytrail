<?php

/* Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Check if site owner have checked to show extra user info.
 *
 * @access      public
 * @since       1.0
 */
function edd_paytrail_show_extra_user_info() {

	global $edd_options;
	
	return isset( $edd_options['edd_paytrail_show_user_info'] );
	
}

/**
 * Check if site owner have checked to show address fields.
 *
 * @access      public
 * @since       1.0
 */
function edd_paytrail_show_extra_address_fields() {

	global $edd_options;
	
	return isset( $edd_options['edd_paytrail_show_address_fields'] );
	
}

/**
 * Register the paytrail.fi gateway settings
 *
 * @access      public
 * @since       1.0
 * @param 		$settings array the existing plugin settings
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

/**
 * Registers the new options in Extensions.
 *
 * @access      public
 * @since       1.0
 * @param 		$settings array the existing plugin settings
 * @return      array
*/
function edd_paytrail_settings( $settings ) {

	$extensions_settings = array(
		array(
			'id' => 'edd_paytrail_header',
			'name' => '<strong>' . __( 'Paytrail', 'edd-paytrail' ) . '</strong>',
			'desc' => '',
			'type' => 'header',
			'size' => 'regular'
		),
		array(
			'id' => 'edd_paytrail_show_image',
			'name' => __( 'Show Paytrail image', 'edd-paytrail' ),
			'desc' => __( 'Check this if you want to show Paytrail image on checkout page.', 'edd-paytrail' ),
			'type' => 'checkbox'
		),
		array(
			'id' => 'edd_paytrail_show_user_info',
			'name' => __( 'Additional user info', 'edd-paytrail' ),
			'desc' => __( 'Check this if you want to show phone number and company name fields on checkout page. This info is also send to Paytrail account.', 'edd-paytrail' ),
			'type' => 'checkbox'
		),
		array(
			'id' => 'edd_paytrail_show_address_fields',
			'name' => __( 'Show address fields', 'edd-paytrail' ),
			'desc' => __( 'Check this if you want to show address fields on checkout page. Address and product info is also send to Paytrail account.', 'edd-paytrail' ),
			'type' => 'checkbox'
		)
	);

	return array_merge( $settings, $extensions_settings );

}
add_filter( 'edd_settings_extensions', 'edd_paytrail_settings' );

?>