<?php
/**
 * Plugin Name: Forge12 Download Encrypter
 * Plugin URI: https://www.forge12.com
 * Description: Create hidden links for Downloads
 * Version: 0.01
 * Author: Forge12 Interactive GmbH
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once( plugin_dir_path( __FILE__ ) . "core/f12-download-settings.php" );
require_once( plugin_dir_path( __FILE__ ) . "core/f12-download-cpt.php" );
require_once( plugin_dir_path( __FILE__ ) . "core/f12-download-fields.php" );
require_once( plugin_dir_path( __FILE__ ) . "core/f12-generic-fields.php" );
require_once( plugin_dir_path( __FILE__ ) . "core/f12-generic-fields-file-picker.php" );
require_once( plugin_dir_path( __FILE__ ) . "core/f12-download-edit-page.php" );
require_once( plugin_dir_path( __FILE__ ) . "core/f12-download-overview.php" );