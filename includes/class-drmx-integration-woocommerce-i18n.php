<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.haihaisoft.com
 * @since      1.0.0
 *
 * @package    Drmx_Integration_Woocommerce
 * @subpackage Drmx_Integration_Woocommerce/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Drmx_Integration_Woocommerce
 * @subpackage Drmx_Integration_Woocommerce/includes
 * @author     Haihaisoft <service@haihaisoft.com>
 */
class Drmx_Integration_Woocommerce_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'drmx-integration-woocommerce',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
