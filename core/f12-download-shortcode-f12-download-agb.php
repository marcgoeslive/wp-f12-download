<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class F12DownloadShortcode
 */
class F12DownloadShortcode {
	/**
	 * Form Error
	 */
	private $_form_error = false;

	/**
	 * F12DownloadShortcode constructor.
	 */
	public function __construct() {
		add_action( "wp", array( $this, "update" ) );
		add_shortcode( "f12-download-agb", array( $this, "add_shortcode" ) );
	}

	/**
	 * Adding before any output
	 */
	public function update() {
		if ( is_page( get_option( "f12d" )["download_page_agb"] ) ) {
			$is_valid_nonce = ( isset( $_POST['f12d_agb_nonce'] ) && wp_verify_nonce( $_POST['f12d_agb_nonce'], basename( __FILE__ ) ) ) ? true : false;
			$is_valid_agb   = ( isset( $_POST['agb_accepted_by_user'] ) && $_POST['agb_accepted_by_user'] == 1 ) ? true : false;
			$is_valid_agb_2 = ( isset( $_POST['agb_2_accepted_by_user'] ) && $_POST['agb_2_accepted_by_user'] == 1 ) ? true : false;

			if ( $is_valid_agb_2 && $is_valid_agb && $is_valid_nonce ) {
				$hash = get_query_var( "hash" );
				$hash = explode( "_", $hash );

				$f12_download_id     = $hash[0]; // ID of the Download
				$f12_generic_created = $hash[1]; // Timestamp
				$email               = $hash[2]; // E-Mail

				$this->update_file( $f12_download_id, $f12_generic_created, $email );

				header( "LOCATION: " . f12d_get_download_link( $f12_download_id, $f12_generic_created, $email ) );
			} else {
				$this->_form_error = true;
			}
		}
	}

	/**
	 * Update the AGB Field from the File
	 */
	private function update_file( $f12_download_id, $f12_generic_created, $email ) {
		$args = array(
			"post_type"     => "f12d_generic",
			"post_per_page" => - 1,
			"meta_query"    => array(
				"relation" => "AND",
				array(
					"key"   => "created",
					"value" => $f12_generic_created
				),
				array(
					"key"   => "f12d_download_id",
					"value" => $f12_download_id
				),
				array(
					"key"   => "email",
					"value" => $email
				)

			)
		);
		$loop = new WP_Query( $args );
		$item = $loop->get_posts()[0]; // loading only the first hit.

		update_post_meta( $item->ID, "agb_accepted_by_user", 1 );
		update_post_meta( $item->ID, "agb_accepted_by_user_timestamp", time() );
		update_post_meta( $item->ID, "agb_2_accepted_by_user", 1 );
		update_post_meta( $item->ID, "agb_2_accepted_by_user_timestamp", time() );
		update_post_meta( $item->ID, "ip", $_SERVER['REMOTE_ADDR'] );
	}

	/**
	 * Replace placeholders
	 *
	 * @param string $content
	 *
	 * @return string
	 */
	private function add_placeholder( $content ) {
		$args = array(
			"firstname" => "",
			"lastname"  => "",
			"email"     => "",
			"object-id" => "",
			"date"      => "",
		);

		$hash = F12DownloadUtils::get_hash();

		if ( ! empty( $hash ) && is_numeric( $hash[0] ) ) {
			$generic_object = F12DownloadUtils::get_generic_object( $hash[0], $hash[1], $hash[2] );

			if ( $generic_object != null ) {

				$metadata          = get_post_meta( $generic_object->ID );
				$args["firstname"] = F12DownloadUtils::get_field( $metadata, "firstname" );
				$args["lastname"]  = F12DownloadUtils::get_field( $metadata, "lastname" );
				$args["email"]     = F12DownloadUtils::get_field( $metadata, "email" );
				$args["object-id"] = F12DownloadUtils::get_field( $metadata, "f12d_object_id" );
				$args["date"]      = date( "d.m.Y", time() ) . " " . __( "um", "f12d_download" ) . " " . date( " H:i:s", time() ) . " " . __( "Uhr", "f12d_download" );
			}

		}

		foreach ( $args as $key => $value ) {
			$content = preg_replace( "!{" . $key . "}!", $value, $content );
		}

		return $content;
	}

	/**
	 * Shortcode laden
	 */
	public function add_shortcode() {
		$content            = wpautop( $this->add_placeholder( get_option( "f12d" )["download_page_agb_text"] ) );
		$content_checkbox_1 = wpautop( get_option( "f12d" )["download_page_agb_checkbox_1"] );
		$content_checkbox_2 = wpautop( get_option( "f12d" )["download_page_agb_checkbox_2"] );
		$error              = "";

		if ( $this->_form_error ) {
			$error = F12DownloadUtils::loadTemplate("shortcode-download-agb-error.php",array());
		}

		$args = array(
			"nonce"           => wp_nonce_field( basename( __FILE__ ), 'f12d_agb_nonce' ),
			"checkbox_1_text" => $content_checkbox_1,
			"checkbox_2_text" => $content_checkbox_2,
			"content"         => $content,
			"error"           => $error
	);

		return F12DownloadUtils::loadTemplate( "shortcode-download-agb.php", $args );
	}
}

new F12DownloadShortcode();