<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://plugins.db-dzine.com
 * @since      1.0.0
 *
 * @package    Kong_Helpdesk
 * @subpackage Kong_Helpdesk/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Kong_Helpdesk
 * @subpackage Kong_Helpdesk/includes
 * @author     Daniel Barenkamp <contact@db-dzine.de>
 */
class Kong_Helpdesk_Deactivator {

	/**
	 * On Plugin deactivation remove roles
	 * @author Daniel Barenkamp
	 * @version 1.0.0
	 * @since   1.0.0
	 * @link    https://plugins.db-dzine.com
	 * @return  [type]                       [description]
	 */
	public static function deactivate() {
        remove_role('agent');
        remove_role('subscriber');

		wp_clear_scheduled_hook('run_kong_helpdesk_inbox_fetching');
	}
}