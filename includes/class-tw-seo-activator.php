<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tw_Seo
 * @subpackage Tw_Seo/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tw_Seo
 * @subpackage Tw_Seo/includes
 * @author     TukuToi <hello@tukutoi.com>
 */
class Tw_Seo_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {

		if ( ! is_plugin_active( 'types/wpcf.php' ) and current_user_can( 'activate_plugins' ) ) {
        		// Stop activation redirect and show error
        		wp_die('Sorry, but this plugin requires the Toolset Types Plugin to be installed and active. <br><a href="' . admin_url( 'plugins.php' ) . '">&laquo; Return to Plugins</a>');
    		}

	}

}
