<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://amitmittal.tech
 * @since      1.0.0
 *
 * @package    Bitss_Squiggles_Customizations
 * @subpackage Bitss_Squiggles_Customizations/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Bitss_Squiggles_Customizations
 * @subpackage Bitss_Squiggles_Customizations/includes
 * @author     Amit Mittal <amitmittal@bitsstech.com>
 */
class Bitss_Squiggles_Customizations_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'bitss-squiggles-customizations',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
