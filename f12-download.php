<?php
/**
 * Plugin Name: Forge12 Download Encrypter
 * Plugin URI: https://www.forge12.com
 * Description: Create hidden links for Downloads
 * Version: v1.0
 * Author: Forge12 Interactive GmbH
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( is_admin() ) {
	require_once( plugin_dir_path( __FILE__ ) . "admin/f12-admin-init.php" );
}

require_once( plugin_dir_path( __FILE__ ) . "core/f12-download-utils.php" );
require_once( plugin_dir_path( __FILE__ ) . "core/f12-generic-cqv.php" );
require_once( plugin_dir_path( __FILE__ ) . "core/f12-download-page.php" );
require_once( plugin_dir_path( __FILE__ ) . "core/f12-download-shortcode.php" );

add_action("plugins_loaded","f12_download_load_textdomain");

function f12_download_load_textdomain(){
	load_plugin_textdomain("f12-download",false, basename(dirname(__FILE__)));
}