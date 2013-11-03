<?php
/**
* Plugin Name: EDD Paytrail Gateway
* Plugin URI: https://foxnet-themes.fi/downloads/edd-paytrail/
* Description: Adds a payment gateway for Paytrail Payment Gateaway
* Version: 1.0
* Author: Sami Keijonen
* Author URI: https://foxnet-themes.fi
*
* This program is free software; you can redistribute it and/or modify it under the terms of the GNU
* General Public License version 2, as published by the Free Software Foundation. You may NOT assume
* that you can use any other version of the GPL.
*
* This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
* even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*
* @package EDD Paytrail Gateway
* @version 1.0
* @author Sami Keijonen <sami.keijonen@foxnet.fi>
* @copyright Copyright (c) 2013, Sami Keijonen
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

class EDD_PAYTRAIL {

	/**
	* PHP5 constructor method.
	*
	* @since 1.0
	*/
	public function __construct() {

		/* Set the constants needed by the plugin. */
		add_action( 'plugins_loaded', array( &$this, 'constants' ), 1 );

		/* Internationalize the text strings used. */
		add_action( 'plugins_loaded', array( &$this, 'i18n' ), 2 );

		/* Load the functions files. */
		add_action( 'plugins_loaded', array( &$this, 'includes' ), 3 );

	}

	/**
	* Defines constants used by the plugin.
	*
	* @since 1.0
	*/
	public function constants() {

		/* Set constant path to the plugin directory. */
		define( 'EDD_PAYTRAIL_DIR', trailingslashit( plugin_dir_path( __FILE__ ) ) );
		
		/* Set constant path to the plugin directory. */
		define( 'EDD_PAYTRAIL_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );

		/* Set the constant path to the includes directory. */
		define( 'EDD_PAYTRAIL_INCLUDES', EDD_PAYTRAIL_DIR . trailingslashit( 'includes' ) );
		
		/* Define Plugin Version. */
		if ( ! defined( 'EDD_PAYTRAIL_VERSION' ) ) {
			define( 'EDD_PAYTRAIL_VERSION', '1.0' );
		}

		/* For Licensing. */
		if ( ! defined( 'EDD_PAYTRAIL_STORE_URL' ) ) {
			define( 'EDD_PAYTRAIL_STORE_URL', 'http://localhost/foxnet-themes-shop' );
		}

		if ( ! defined( 'EDD_PAYTRAIL_STORE_NAME' ) ) {
			define( 'EDD_PAYTRAIL_STORE_NAME', 'EDD Paytrail Gateway' );
		}

	}

	/**
	* Load the translation of the plugin.
	*
	* @since 1.0
	*/
	public function i18n() {

		/* Load the translation of the plugin. */
		load_plugin_textdomain( 'edd-paytrail', false, 'edd-paytrail/languages' );

	}

	/**
	* Loads the initial files needed by the plugin.
	*
	* @since 1.0
	*/
	public function includes() {
		
		/* Load necessary files. */
		require_once( EDD_PAYTRAIL_INCLUDES . 'functions.php' );
		require_once( EDD_PAYTRAIL_INCLUDES . 'settings.php' );
		require_once( EDD_PAYTRAIL_INCLUDES . 'form-template.php' );
		
		/* Load the EDD license handler only if not already loaded. Must be placed in the main plugin file */
		if( ! class_exists( 'EDD_License' ) )
			include( dirname( __FILE__ ) . '/includes/EDD_License_Handler.php' );

		/* Instantiate the licensing / updater. Must be placed in the main plugin file */
		$license = new EDD_License( __FILE__, 'Paytrail Payment Gateway', '1.0', 'Sami Keijonen' );
		
	}

}

new EDD_PAYTRAIL();

?>