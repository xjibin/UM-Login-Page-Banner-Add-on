<?php
/**
 * Removes plugin options on uninstall.
 *
 * @package UM_Login_Page_Banner_Addon
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'um_login_banner_options' );
