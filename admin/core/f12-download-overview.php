<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Change the view of the overview table
 */
class F12DownloadOverview {
	public function __construct() {
		//add_filter( 'manage_f12_contact_columns', array( &$this, "set_columns" ) );
		add_filter( 'manage_f12d_generic_posts_columns', array( &$this, "posts_columns" ) );
		add_action( 'manage_f12d_generic_posts_custom_column', array( &$this, "custom_columns" ), 10, 2);
	}

	public function custom_columns( $column, $post_id ) {
		$stored_meta_data = get_post_meta( $post_id );
		switch ( $column ) {
			case 'Shortcode':
				echo "[f12-download-form id=".$post_id." type=link]";
				break;
		}
	}

	public function posts_columns( $columns ) {
		$args = array(
			"Shortcode"   => "Shortcode",
			"date"   => $columns["date"]
		);
		;
		unset($columns["date"]);

		$columns = array_merge($columns,$args);

		return $columns;
	}
}

new F12DownloadOverview();