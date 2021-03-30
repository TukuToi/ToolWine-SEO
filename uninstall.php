<?php

/**
 * Fired when the plugin is uninstalled.
 *
 * When populating this file, consider the following flow
 * of control:
 *
 * - This method should be static
 * - Check if the $_REQUEST content actually is the plugin name
 * - Run an admin referrer check to make sure it goes through authentication
 * - Verify the output of $_GET makes sense
 * - Repeat with other user roles. Best directly by using the links/query string parameters.
 * - Repeat things for multisite. Once for a single site in the network, once sitewide.
 *
 * This file may be updated more in future version of the Boilerplate; however, this is the
 * general skeleton and outline for how the file should work.
 *
 * For more information, see the following discussion:
 * https://github.com/tommcfarlin/WordPress-Plugin-Boilerplate/pull/123#issuecomment-28541913
 *
 * @link       https://www.tukutoi.com/
 * @since      1.0.0
 *
 * @package    Tw_Seo
 */


$plugin             = 'tw-seo/tw-seo.php';
$ref_action         = 'delete-plugin';
$action             = 'updates';
$nonce              = '_ajax_nonce';
$cap                = 'delete_plugins';
$options            = get_option( 'tkt_common' );
$options_to_delete  = array('tw_seo','tkt_common');
$maybe_del_options  = 'tkt_common_delete_options';

if ( 
    ! defined( 'WP_UNINSTALL_PLUGIN' ) 
    || $_REQUEST['plugin'] != $plugin
    || $_REQUEST['action'] != $ref_action 
    || !check_ajax_referer( $action, $nonce )
    || !current_user_can($cap)
    || !empty($_GET)
) {
    exit;
}

if ( is_array($options) && array_key_exists($maybe_del_options, $options) ){
    foreach ($options_to_delete as $option_to_delete) {
        delete_option($option_to_delete);
    } 
}
