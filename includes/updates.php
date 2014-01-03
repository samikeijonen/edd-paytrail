<?php
/**
 * Loads the Updater
 *
 * Instantiates the Software Licensing Plugin Updater and passes the plugin
 * data to the class
 *
 * @since 0.1.0
 */
function edd_paytrail_updater() {
	
	if( ! class_exists( 'EDD_SL_Plugin_Updater' ) ) {
		// load our custom updater
		require_once( EDD_PAYTRAIL_INCLUDES . 'EDD_SL_Plugin_Updater.php' );
	}

	$get_license_key = get_option( 'edd_settings_gateways' ); // This is array
	$license_key = isset( $get_license_key['paytrail_license_key'] ) ? $get_license_key['paytrail_license_key'] : '';

	$edd_updater = new EDD_SL_Plugin_Updater( EDD_PAYTRAIL_STORE_URL, __FILE__, array(
			'version' 	=> EDD_PAYTRAIL_VERSION,
			'license' 	=> $license_key,
			'item_name' => EDD_PAYTRAIL_STORE_NAME,
			'author' 	=> 'Sami Keijonen'
		)
	);
}
add_action( 'admin_init', 'edd_paytrail_updater' );

/**
 * Activate the license
 *
 * @since 0.1.0
 */
function edd_paytrail_activate_license() {
	global $edd_options;
	
	if( ! isset( $_POST['edd_settings_gateways'] ) )
		return;
	if( ! isset( $_POST['edd_settings_gateways']['paytrail_license_key'] ) )
		return;

	if( get_option( 'paytrail_license_active' ) == 'valid' )
		return;

	$license = sanitize_text_field( $_POST['edd_settings_gateways']['paytrail_license_key'] );

	// data to send in our API request
	$api_params = array(
		'edd_action'=> 'activate_license',
		'license' 	=> $license,
		'item_name' => urlencode( EDD_PAYTRAIL_STORE_NAME ) // the name of our product in EDD
	);

	// Call the custom API.
	$response = wp_remote_get( add_query_arg( $api_params, EDD_PAYTRAIL_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

	// make sure the response came back okay
	if ( is_wp_error( $response ) )
		return false;

	// decode the license data
	$license_data = json_decode( wp_remote_retrieve_body( $response ) );

	update_option( 'paytrail_license_active', $license_data->license );

}
add_action( 'admin_init', 'edd_paytrail_activate_license' );

?>