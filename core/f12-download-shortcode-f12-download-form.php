<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Add action to check if the form has send to manipulate
 * the header
 */
add_action( "wp_loaded", "f12d_validate_form" );

/**
 * Speichern des Links und senden der Email
 */
function f12d_validate_form() {
	// Validate
	if ( ! isset( $_POST ) || ! isset( $_POST["action"] ) || $_POST["action"] != "f12d_download_form" ) {
		return;
	}

	$email           = $_POST["f12d_download_email"];
	$f12_download_id = $_POST["f12d_download_id"];

	if ( is_email( $email ) && is_numeric( $f12_download_id ) && ! empty( $f12_download_id ) && ! empty( $f12_download_id ) ) {

		// generate the generic download link
		$post_id = f12d_generic_create_post( $email, $f12_download_id );
		if ( $post_id != - 1 ) {
			$stored_meta = get_post_meta( $post_id );

			$link = f12d_get_download_link( $stored_meta["f12d_download_id"][0], $stored_meta["created"][0], $email );

			$header   = array();
			$header[] = "MIME-Version: 1.0";
			$header[] = "Content-type: text/html; charset=utf-8";
			$header[] = "From: " . get_option( "f12d" )["email"];
			$header[] = "X-Mailer: PHP/" . phpversion();
			$header[] = "Reply-To: " .  get_option( "f12d" )["email"];
			$header   = implode( "\r\n", $header );

			mail( $email, "Download Link", "Link zum Download: " . $link, $header );

			wp_redirect( get_permalink( get_option( "f12d" )["email_page_send"] ) );
			exit;
		}
	}
}

/**
 * Adding a shortcode for the form which will generate
 * the generic file for the download
 *
 * [f12-download-form id=518]
 */
function f12d_shortcode_download_form( $atts ) {
	$is_valid_nonce  = ( isset( $_POST['f12d_mail_nonce'] ) && wp_verify_nonce( $_POST['f12d_mail_nonce'], basename( __FILE__ ) ) ) ? true : false;
	$is_valid_email  = ( isset( $_POST['email'] ) && is_email( $_POST['email'] ) ) ? true : false;
	$f12_download_id = isset( $_POST["f12d_download_id"] ) ? $_POST["f12d_download_id"] : - 1;

	if ( ! $is_valid_nonce || ! $is_valid_email || ( $f12_download_id == - 1 || $f12_download_id != $atts["id"] ) ) {
		$type = ( isset( $atts['type'] ) ? $atts['type'] : "default" );

		if ( ! is_array( $atts ) ) {
			$atts = array();
		}

		if ( isset( $_GET["data-key"] ) ) {
			$atts["id"] = $_GET["data-key"];
		}

		switch ( $type ) {
			case 'popup':
				return f12d_show_form_popup( $atts["id"] );
			case 'link':
				return f12d_show_form_link( $atts["id"] );
			case 'default':
				if ( f12d_send_by_mail( $atts["id"] ) ) {
					return f12d_show_form( $atts["id"] );
				} else {
					header("LOCATION: ".f12d_get_download_link($atts["id"]));
				}
		}

	} else {
		return f12d_send_mail( $_POST['email'], $atts["id"] );
	}
}

/**
 * Check if the link should be send by mail
 */
function f12d_send_by_mail( $id ) {
	$download = get_post( $id );
	if ( ! $download ) {
		return false;
	}

	$metadata   = get_post_meta( $id );
	$sendbymail = isset( $metadata["sendbymail"] ) && ! empty( $metadata["sendbymail"] ) ? $metadata["sendbymail"][0] : 0;

	return $sendbymail;
}

/**
 * Show the form for the E-Mail Input
 * @return string
 */
function f12d_show_form_popup( $f12_download_id ) {
	//ob_start();
	return f12d_load_template( "f12_popup.php", array(
		"f12_download_id"   => $f12_download_id,
		"f12_download_form" => f12d_show_form( $f12_download_id ),
		"f12_download_name" => f12d_get_file_name_by_id( $f12_download_id )
	) );
}


/**
 * Show the form for the E-Mail Input
 * @return string
 */
function f12d_show_form_link( $f12_download_id ) {
	//ob_start();
	return f12d_load_template( "f12_link.php", array(
		"f12_download_id"   => $f12_download_id,
		"f12_download_name" => f12d_get_file_name_by_id( $f12_download_id )
	) );
}


/**
 * Show the form for the E-Mail Input
 * @return string
 */
function f12d_show_form( $f12_download_id ) {
	return f12d_load_template( "f12_download_form.php", array(
		"f12_mail_nonce"  => wp_nonce_field( basename( __FILE__ ), 'f12d_mail_nonce' ),
		"f12_download_id" => $f12_download_id
	) );
}

function f12d_generic_create_post( $email, $f12d_download_id ) {
	$time      = time();
	$author_id = 1; // Default first user - Admin
	$slug      = 'download-' . $time;
	$title     = 'download-' . $time;

	if ( null == get_page_by_title( $title ) ) {
		$post_id = wp_insert_post(
			array(
				'comment_status' => 'closed',
				'ping_status'    => 'closed',
				'post_author'    => $author_id,
				'post_name'      => $slug,
				'post_title'     => $title,
				'post_status'    => 'publish',
				'post_type'      => 'f12d_generic'
			)
		);

		// Add meta informations
		$meta_created              = $time;
		$meta_downloads            = 0;
		$meta_downloads_maximum    = 1; // TODO: Add to options to change by admin
		$meta_depleted             = 0;
		$meta_f12d_download_id     = $f12d_download_id;
		$meta_agb                  = 1; // TODO: Add to options to change by admin
		$meta_agb_accepted_by_user = 0;

		update_post_meta( $post_id, "created", $meta_created );
		update_post_meta( $post_id, "downloads", $meta_downloads );
		update_post_meta( $post_id, "downloads_maximum", $meta_downloads_maximum );
		update_post_meta( $post_id, "depleted", $meta_depleted );
		update_post_meta( $post_id, "f12d_download_id", $meta_f12d_download_id );
		update_post_meta( $post_id, "email", $email );
		update_post_meta( $post_id, "agb", $meta_agb );
		update_post_meta( $post_id, "agb_accepted_by_user", $meta_agb_accepted_by_user );

		$status = $post_id; // Data successfully added

	} else {
		$status = - 1; // Post exists already
	}

	return $status;
}

add_shortcode( "f12-download-form", "f12d_shortcode_download_form" );