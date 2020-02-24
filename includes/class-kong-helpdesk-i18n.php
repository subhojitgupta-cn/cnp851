<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       http://plugins.db-dzine.com
 * @since      1.0.0
 *
 * @package    Kong_Helpdesk
 * @subpackage Kong_Helpdesk/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Kong_Helpdesk
 * @subpackage Kong_Helpdesk/includes
 * @author     Daniel Barenkamp <contact@db-dzine.de>
 */
class Kong_Helpdesk_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$loaded = load_plugin_textdomain(
			'kong-helpdesk',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
