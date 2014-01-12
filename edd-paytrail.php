<?php
/**
* Plugin Name: EDD Paytrail Gateway
* Plugin URI: https://foxnet-themes.fi/downloads/paytrail-payment-gateway/
* Description: Adds Paytrail payment gateway to Easy Digital Downloads plugin 
* Version: 1.0.1
* Author: Sami Keijonen
* Author URI: https://foxnet-themes.fi
* Text Domain: edd-paytrail
* Domain Path: /languages
*
* This program is free software; you can redistribute it and/or modify it under the terms of the GNU
* General Public License version 2, as published by the Free Software Foundation. You may NOT assume
* that you can use any other version of the GPL.
*
* This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without
* even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
*
* @package EDD Paytrail Gateway
* @version 1.0.1
* @author Sami Keijonen <sami.keijonen@foxnet.fi>
* @copyright Copyright (c) 2014, Sami Keijonen
* @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/* Exit if accessed directly. */
if ( ! defined( 'ABSPATH' ) ) exit;

final class EDD_PAYTRAIL {

	/**
	 * Holds the instances of this class.
	 *
	 * @since  1.0.0
	 * @access private
	 * @var    object
	 */
	private static $instance;

	/**
	* PHP5 constructor method.
	*
	* @since  1.0.0
	* @access public
	* @var    void
	*/
	public function __construct() {
		
		/*  Instantiate the licensing / updater. Must be placed in the main plugin file. */
		if( class_exists( 'EDD_License' ) ) {
			$license = new EDD_License( __FILE__, 'Paytrail Payment Gateway', '1.0.1', 'Sami Keijonen', null, 'http://foxnet-themes.fi/' );
		}
		
		/* Set the constants needed by the plugin. */
		add_action( 'plugins_loaded', array( $this, 'constants' ), 1 );

		/* Internationalize the text strings used. */
		add_action( 'plugins_loaded', array( $this, 'i18n' ), 2 );

		/* Load the functions files. */
		add_action( 'plugins_loaded', array( $this, 'includes' ), 3 );

	}

	/**
	* Defines constants used by the plugin.
	*
	* @since 1.0.0
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
			define( 'EDD_PAYTRAIL_VERSION', '1.0.1' );
		}

		/* For Licensing. */
		if ( ! defined( 'EDD_PAYTRAIL_STORE_URL' ) ) {
			define( 'EDD_PAYTRAIL_STORE_URL', 'http://foxnet-themes.fi' );
		}

		if ( ! defined( 'EDD_PAYTRAIL_STORE_NAME' ) ) {
			define( 'EDD_PAYTRAIL_STORE_NAME', 'Paytrail Payment Gateway' );
		}

	}

	/**
	* Load the translation of the plugin.
	*
	* @since 1.0.0
	*/
	public function i18n() {
	
		/* Load the translation of the plugin. */
		$domain = 'edd-paytrail';
		$locale = apply_filters( 'edd_paytrail_locale', get_locale(), $domain );
		
		/* You can put custom translation files in wp-content/languages/edd-paytrail folder. */
		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( 'edd-paytrail', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

	}

	/**
	* Loads the initial files needed by the plugin.
	*
	* @since 1.0.0
	*/
	public function includes() {

		/* Load necessary files. */
		require_once( EDD_PAYTRAIL_INCLUDES . 'settings.php' );
		require_once( EDD_PAYTRAIL_INCLUDES . 'address-info.php' );
		require_once( EDD_PAYTRAIL_INCLUDES . 'image-info.php' );
		require_once( EDD_PAYTRAIL_INCLUDES . 'functions.php' );
		
	}
	
	/**
	 * Returns the instance.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public static function get_instance() {

		if ( !self::$instance ) {
			self::$instance = new self;
		}
		
		return self::$instance;
	}

}

EDD_PAYTRAIL::get_instance();

?>