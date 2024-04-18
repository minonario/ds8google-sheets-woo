<?php
/**
 * @package DS8 Google Sheet Woo
 */
/*
Plugin Name: DS8 Google Sheet Woo
Plugin URI: https://deseisaocho.com/
Description: DS8 <strong>Sync up google sheet products with Woocommerce</strong>
Version: 1.0
Author: JLMA
Author URI: https://deseisaocho.com/wordpress-plugins/
License: GPLv2 or later
Text Domain: ds8google-sheets-woo
*/


if ( !function_exists( 'add_action' ) ) {
	echo 'Hi there!  I\'m just a plugin, not much I can do when called directly.';
	exit;
}

define( 'DS8GOOGLE_SHEETS_WOO_VERSION', '3.4' );
define( 'DS8GOOGLE_SHEETS_WOO_MINIMUM_WP_VERSION', '5.0' );
define( 'DS8GOOGLE_SHEETS_WOO_ASSETS', plugins_url('/assets/', __FILE__));
define( 'DS8GOOGLE_SHEETS_WOO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

register_activation_hook( __FILE__, array( 'DS8GoogleSheetWOO', 'plugin_activation' ) );
register_deactivation_hook( __FILE__, array( 'DS8GoogleSheetWOO', 'plugin_deactivation' ) );

//require_once DS8GOOGLE_SHEETS_WOO_PLUGIN_DIR . '/includes/helpers.php';
require_once( DS8GOOGLE_SHEETS_WOO_PLUGIN_DIR . 'class.ds8googlesheetwoo.php' );

if ( is_admin() ) {
	require_once( DS8GOOGLE_SHEETS_WOO_PLUGIN_DIR . 'class.ds8googlesheetwoo-admin.php' );
	add_action( 'init', array( 'DS8GoogleSheetWOO_Admin', 'init' ) );
}

//add_action( 'init', array( 'DS8RelatedPosts', 'init' ) );

global $simple_ds8_googlesheets;
$simple_ds8_googlesheets = DS8GoogleSheetWOO::get_instance();
