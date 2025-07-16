<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class F12downloadSettings
 */
class F12downloadSettings {
	/**
	 * F12downloadSettings constructor.
	 */
	public function __construct() {
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( "admin_menu", array( $this, "add_admin_menu" ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	public function enqueue_scripts() {
		if ( ! wp_script_is( "f12_modal_file_picker", "enqueued" ) ) {
			wp_register_script( "f12_modal_file_picker", plugins_url( '../assets/js/f12-file-picker.js', __FILE__ ), array( "jquery" ), false, true );
			wp_enqueue_script( "f12_modal_file_picker" );
		}

		if ( ! wp_script_is( "f12_download_remove", "enqueued" ) ) {
			wp_register_script( "f12_download_remove", plugins_url( "../assets/js/f12-download-remove.js", __FILE__ ), array( "jquery" ), false, true );
			wp_enqueue_script( "f12_download_remove" );
		}

		if ( ! wp_style_is( "f12_download_css", "enqueued" ) ) {
			wp_enqueue_style( "f12_download_css", plugins_url( "../assets/css/style.css", __FILE__ ) );
		}
	}

	public function register_settings() {
		add_option( 'f12d', array(
			'max_file_size'                => 5120000,
			'allowed_extensions'           => 'pdf,rar',
			'storage'                      => dirname( plugin_dir_path( __FILE__ ), 2 ) . '/storage/',
			'delete_files_onchange'        => true,
			'download_page'                => - 1,
			'download_page_agb'            => - 1,
			'download_page_agb_text'       => '',
			'download_page_agb_checkbox_1' => '',
			'download_page_agb_checkbox_2' => '',
			'email'                        => "no-reply@domain.tld",
			'email_page_send'              => - 1,
			'post_types'                   => "page"
		) );
	}

	public function add_admin_menu() {
		add_submenu_page( "edit.php?post_type=f12d_download", __( 'Einstellungen', "f12-download" ), __( 'Einstellungen', "f12-download" ), "manage_options", "f12-download/admin/core/f12d.php" );
	}
}

new F12DownloadSettings();