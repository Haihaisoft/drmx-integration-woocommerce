<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.haihaisoft.com
 * @since             1.0.0
 * @package           Drmx_Integration_Woocommerce
 *
 * @wordpress-plugin
 * Plugin Name:       DRM-X 4.0 Integration With WooCommerce
 * Plugin URI:        https://www.drm-x.com
 * Description:       DRM-X 4.0 integrates with WooCommerce, verifies student course permissions when obtain licenses, and automatically obtain licenses. And it supports embedding encrypted videos into Xvast Player using shortcodes, For example [xvast-player] Video URL [/xvast-player].
 * Version:           1.0.0
 * Author:            Haihaisoft
 * Author URI:        https://www.haihaisoft.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       drmx-integration-woocommerce
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
define( 'DRMX_INTEGRATION_WOOCOMMERCE_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-drmx-integration-woocommerce-activator.php
 */
function activate_drmx_integration_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-drmx-integration-woocommerce-activator.php';
	Drmx_Integration_Woocommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-drmx-integration-woocommerce-deactivator.php
 */
function deactivate_drmx_integration_woocommerce() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-drmx-integration-woocommerce-deactivator.php';
	Drmx_Integration_Woocommerce_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_drmx_integration_woocommerce' );
register_deactivation_hook( __FILE__, 'deactivate_drmx_integration_woocommerce' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-drmx-integration-woocommerce.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_drmx_integration_woocommerce() {

	$plugin = new Drmx_Integration_Woocommerce();
	$plugin->run();

}
run_drmx_integration_woocommerce();

function embed_xvast_player($atts = [], $content){
	$embed_html = '<div id="Xvast_Video_URL" style="display: none;">'.$content.'</div>
	<script type="text/javascript" src="https://www.xvast.com/dist/js/embedPlayer.js"></script>
	<script type="text/javascript" src="https://www.xvast.com/dist/js/video.js"></script>
	<script type="text/javascript" src="https://www.xvast.com/dist/wordpress/XvastVideoJSPlayer.js"></script>';
	
	return $embed_html;
}
add_shortcode('xvast-player','embed_xvast_player');

add_action('admin_menu', 'register_drmx_integration_menu');
function register_drmx_integration_menu(){
	add_menu_page('DRM-X 4.0 integration with WooCommerce','DRM-X 4.0 with WooCommerce','administrator','DRMX-integration-WooCommerce-Settings','drmx_integration_settings');
}

function drmx_integration_settings(){
	include 'drmx-integration-woocommerce-settings.php';
}
