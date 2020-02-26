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
 * @author     CN <contact@db-dzine.de>
 */
class Kong_Helpdesk_Deactivator {

	/**
	 * On Plugin deactivation remove roles
	 * @author CN
	 * @version 1.0.0
	 * @since   1.0.0
	 * 
	 * @return  [type]                       [description]
	 */
	public static function deactivate() {
        remove_role('agent');
        remove_role('subscriber');

		wp_clear_scheduled_hook('run_kong_helpdesk_inbox_fetching');
	}
}