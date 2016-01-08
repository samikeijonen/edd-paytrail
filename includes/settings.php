<?php

/* Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Check if site owner have checked to show address fields.
 *
 * @access      public
 * @since       1.0.0
 * @return      boolean
 */
function edd_paytrail_show_extra_address_fields() {
	
	$edd_paytrail_show_address_fields = edd_get_option( 'edd_paytrail_show_address_fields' );
	
	if( $edd_paytrail_show_address_fields ) {
		return true;
	} else {
		return false;
	}
	
}

/**
 * Add section for gateways
 *
 * @since       1.1.5
 * @param       array $sections The existing EDD sections array
 * @return      array The modified EDD sections array
 */
function edd_paytrail_gateways_sections( $sections ) {
				
	$sections['edd-paytrail-gateway']  = esc_html__( 'Paytrail', 'edd-paytrail' );
	return $sections;
				
}
add_filter( 'edd_settings_sections_gateways', 'edd_paytrail_gateways_sections' );

/**
 * Add section for settings
 *
 * @since       1.1.5
 * @param       array $sections The existing EDD sections array
 * @return      array The modified EDD sections array
 */
function edd_paytrail_settings_sections( $sections ) {
				
	$sections['edd-paytrail-settings'] = esc_html__( 'Paytrail', 'edd-paytrail' );
	return $sections;
				
}
add_filter( 'edd_settings_sections_extensions', 'edd_paytrail_settings_sections' );

/**
 * Register the paytrail.fi gateway settings
 *
 * @access      public
 * @since       1.0.0
 * @param 		$settings array the existing plugin settings
 * @return      array
 */
function edd_paytrail_gateways_settings( $settings ) {

	$paytrail_settings = array(
		array(
			'id'   => 'edd_paytrail_settings',
			'name' => '<strong>' . esc_html_x( 'Paytrail Settings', 'Paytrail settings in Gateways page', 'edd-paytrail' ) . '</strong>',
			'desc' => esc_html__( 'Configure the Paytrail settings', 'edd-paytrail' ),
			'type' => 'header'
		),
		array(
			'id'   => 'edd_paytrail_merchant_id',
			'name' => esc_html__( 'Merchant ID', 'edd-paytrail' ),
			'desc' => esc_html__( 'Enter your Paytrail Merchant ID. This is needed in order to take payment.', 'edd-paytrail' ),
			'type' => 'text',
			'size' => 'regular'
		),
		array(
			'id'   => 'edd_paytrail_merchant_secret',
			'name' => esc_html__( 'Merchant Secret', 'edd-paytrail' ),
			'desc' => esc_html__( 'Enter your Paytrail Merchant Secret. This is needed in order to take payment.', 'edd-paytrail' ),
			'type' => 'text',
			'size' => 'regular'
		),
		array(
			'id'   => 'edd_paytrail_test_merchant_id',
			'name' => esc_html__( 'Test Merchant ID', 'edd-paytrail' ),
			'desc' => esc_html__( 'Enter your Paytrail test Merchant ID.', 'edd-paytrail' ),
			'type' => 'text',
			'size' => 'regular'
		),
		array(
			'id'   => 'edd_paytrail_test_merchant_secret',
			'name' => esc_html__( 'Test Merchant Secret', 'edd-paytrail' ),
			'desc' => esc_html__( 'Enter your Paytrail test Merchant Secret.', 'edd-paytrail' ),
			'type' => 'text',
			'size' => 'regular'
		)
	);
	
	// If EDD is at version 2.5 or later use section for settings.
	if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
		// Use the previously noted array key as an array key again and next your settings
		$paytrail_settings = array( 'edd-paytrail-gateway' => $paytrail_settings );
	}

	return array_merge( $settings, $paytrail_settings );

}
add_filter( 'edd_settings_gateways', 'edd_paytrail_gateways_settings' );

/**
 * Registers the new options in Extensions.
 *
 * @access      public
 * @since       1.0.0
 * @param 		$settings array the existing plugin settings
 * @return      array
*/
function edd_paytrail_extensions_settings( $settings ) {

	$extensions_settings = array(
		array(
			'id'   => 'edd_paytrail_header',
			'name' => '<strong>' . esc_html_x( 'Paytrail Settings', 'Paytrail settings in Extensions page', 'edd-paytrail' ) . '</strong>',
			'desc' => '',
			'type' => 'header',
			'size' => 'regular'
		),
		array(
			'id'   => 'edd_paytrail_show_image',
			'name' => esc_html__( 'Show Paytrail image', 'edd-paytrail' ),
			'desc' => esc_html__( 'Check this if you want to show Paytrail image on checkout page.', 'edd-paytrail' ),
			'type' => 'checkbox'
		),
		array(
			'id'   => 'edd_paytrail_show_address_fields',
			'name' => esc_html__( 'Finnish address fields', 'edd-paytrail' ),
			'desc' => esc_html__( 'Check this if you want to show address fields like in Finland on checkout page. Address and product info is also send to Paytrail account in this case.', 'edd-paytrail' ),
			'type' => 'checkbox'
		)
	);
	
	// If EDD is at version 2.5 or later use section for settings.
	if ( version_compare( EDD_VERSION, 2.5, '>=' ) ) {
		// Use the previously noted array key as an array key again and next your settings
		$extensions_settings = array( 'edd-paytrail-settings' => $extensions_settings );
	}

	return array_merge( $settings, $extensions_settings );

}
add_filter( 'edd_settings_extensions', 'edd_paytrail_extensions_settings' );