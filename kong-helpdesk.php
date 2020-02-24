<?php

/**
 * The plugin bootstrap file
 *
 *
 * @link              http://capitalnumbers.com/
 * @since             1.0.0
 * @package           Kong_Helpdesk
 *
 * @wordpress-plugin
 * Plugin Name:       Kong Helpdesk
 * Plugin URI:        http://capitalnumbers.com/
 * Description:       The All in One Kong Helpdesk solution
 * Version:           1.0
 * Author:            Capital Numbers
 * Author URI:        http://capitalnumbers.com/
 * Text Domain:       kong-helpdesk
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-kong-helpdesk-activator.php
 */
function activate_Kong_Helpdesk() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kong-helpdesk-activator.php';
	$activator = new Kong_Helpdesk_Activator();
	$activator->activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-kong-helpdesk-deactivator.php
 */
function deactivate_Kong_Helpdesk() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-kong-helpdesk-deactivator.php';
	Kong_Helpdesk_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_Kong_Helpdesk' );
register_deactivation_hook( __FILE__, 'deactivate_Kong_Helpdesk' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-kong-helpdesk.php';

/**
 * Run the Plugin
 * @author CN
 * @version 1.0.0
 * @since   1.0.0
 * @link    http://plugins.db-dzine.com
 */
function run_Kong_Helpdesk() {

	$plugin_data = get_plugin_data( __FILE__ );
	$version = $plugin_data['Version'];

	$plugin = new Kong_Helpdesk($version);
	$plugin->run();

}
function run_inbox_fetching($args = array()) {
	$plugin_data = get_plugin_data( __FILE__ );
	$version = $plugin_data['Version'];

	$plugin = new Kong_Helpdesk($version);
	$inbox = $plugin->inbox;
	$inbox->init();
	$inbox->run_cronjob();

}
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
include_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );

require_once plugin_dir_path( __FILE__ ) . 'includes/redux-framework/class.redux-plugin.php';
ReduxFrameworkPlugin::instance();

if (class_exists('ReduxFrameworkPlugin') ){
	run_Kong_Helpdesk();
	add_action ('run_kong_helpdesk_inbox_fetching', 'run_inbox_fetching'); 
} else {
	add_action( 'admin_notices', 'run_Kong_Helpdesk_Not_Installed' );
}

function run_Kong_Helpdesk_Not_Installed()
{
	?>
    <div class="error">
      <p><?php _e( 'Kong Helpdesk requires the Redux Framework Please install or activate it before!', 'kong-helpdesk'); ?></p>
    </div>
    <?php
}

