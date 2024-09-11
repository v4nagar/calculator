<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://amitmittal.tech
 * @since             1.0.0
 * @package           Bitss_Squiggles_Customizations
 *
 * @wordpress-plugin
 * Plugin Name:       Bitss Squiggles Customizations
 * Plugin URI:        https://bitss.tech
 * Description:       This is a description of the plugin.
 * Version:           1.0.1
 * Author:            Amit Mittal
 * Author URI:        https://amitmittal.tech
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bitss-squiggles-customizations
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'BITSS_SQUIGGLES_CUSTOMIZATIONS_VERSION', '1.0.1' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-bitss-squiggles-customizations-activator.php
 */
function activate_bitss_squiggles_customizations() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bitss-squiggles-customizations-activator.php';
	Bitss_Squiggles_Customizations_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-bitss-squiggles-customizations-deactivator.php
 */
function deactivate_bitss_squiggles_customizations() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-bitss-squiggles-customizations-deactivator.php';
	Bitss_Squiggles_Customizations_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_bitss_squiggles_customizations' );
register_deactivation_hook( __FILE__, 'deactivate_bitss_squiggles_customizations' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-bitss-squiggles-customizations.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_bitss_squiggles_customizations() {

	$plugin = new Bitss_Squiggles_Customizations();
	$plugin->run();

}
run_bitss_squiggles_customizations();
