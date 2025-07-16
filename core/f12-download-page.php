<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * The Download Page Process
 *
 * @param int {$f12_download_id} the id of the file to download
 * @param int {$f12_generic_created} the timestamp the generic link was created
 */
function f12d_download_page_process() {
	// ensure that it only works on the defined download page.
	$download_page_id = get_option( "f12d" )["download_page"];;

	if ( ! is_page( $download_page_id ) ) {
		return;
	}

	$hash = F12DownloadUtils::get_hash();

	$f12_download_id = $hash[0]; // ID of the Download

	// Check if download exists
	if($f12_download_id == -1){
		//wp_die( "Datei exisitert nicht" );
		global $wp_query;
		status_header(404);
		$wp_query->set_404();
		wp_redirect( get_home_url()."/404" );
		exit;
	}


	// Check if email and name is neccessary for this file
	if ( ! f12d_send_by_mail( $f12_download_id ) ) {
		// if not we can start the download
		$time = time();
		f12d_move_file_to_tmp_folder( $f12_download_id, $time, "no-reply@forge12.com" );
		f12d_start_download( $f12_download_id, $time, "no-reply@forge12.com" );
	} else {

		$f12_generic_created = $hash[1]; // Timestamp
		$email               = $hash[2]; // E-Mail

		// check if the hash is valid
		if ( ! is_numeric( $f12_download_id ) || ! is_numeric( $f12_generic_created ) || empty( $email ) ) {
			wp_die( "Hash unknown" );
		}

		$file_status = f12d_file_status( $f12_download_id, $f12_generic_created, $email );

		if ( $file_status == 0 ) {
			//wp_die( "Datei exisitert nicht" );
			global $wp_query;
			status_header(404);
			$wp_query->set_404();
			wp_redirect( get_home_url()."/404" );
			exit;
		}

		if ( $file_status == - 1 ) {
			//wp_die( "Der Link zur Datei ist nicht mehr gültig." );
			global $wp_query;
			status_header(404);
			$wp_query->set_404();
			wp_redirect( get_home_url()."/404" );
			exit;
		}

		if ( $file_status == - 2 ) {
			//wp_die( "Das Downloadlimit der Datei wurde erreicht" );
			global $wp_query;
			status_header(404);
			$wp_query->set_404();
			wp_redirect( get_home_url()."/404" );
			exit;
		}

		// AGB Status
		if ( ! f12d_file_agb( $f12_download_id, $f12_generic_created, $email ) ) {
			// create a temporary copy of the file
			f12d_move_file_to_tmp_folder( $f12_download_id, $f12_generic_created, $email );
			// start the download progress
			f12d_start_download( $f12_download_id, $f12_generic_created, $email );
			// update file counter
			f12d_update_file_meta( $f12_download_id, $f12_generic_created, $email );
		} else {
			// redirect to the agb page
			$link = add_query_arg( "hash", $f12_download_id . "_" . $f12_generic_created . "_" . $email, get_page_link( get_option( "f12d" )["download_page_agb"] ) );
			header( "LOCATION: " . $link );
		}
	}
}

/**
 * Check if the user has to aggree to the agb before download
 * and if yes it checks if the user already agreed to the agb
 *
 * @returns bool
 */
function f12d_file_agb( $f12_download_id, $f12_generic_created, $email ) {
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

	$stored_meta_data = get_post_meta( $item->ID );
	$stored_meta_download = get_post_meta($f12_download_id);

	// check if agb is activated
	if ( ! empty( $stored_meta_download["agb"] ) ) {
		if ( $stored_meta_download["agb"][0] == true ) {
			if ( ! empty( $stored_meta_data["agb_accepted_by_user"] ) && !empty($stored_meta_data["agb_2_accepted_by_user"]) ) {
				if ( $stored_meta_data["agb_accepted_by_user"][0] == 1  && $stored_meta_data["agb_2_accepted_by_user"][0] == 1) {
					return false;
				}
			}

			return true;
		}
	}

	return false;
}

/**
 * Update the post entry for the downloaded file
 *
 * @param $f12_download_id
 * @param $f12_generic_created
 */
function f12d_update_file_meta( $f12_download_id, $f12_generic_created, $email ) {
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
	$loop->the_post(); // loading only the first hit.
	$stored_meta_data = get_post_meta( get_the_ID() );

	$downloads = 0;

	if ( ! empty( $stored_meta_data["downloads"] ) ) {
		$downloads = $stored_meta_data["downloads"][0];
	}

	$downloads += 1; // increase downloads by 1

	update_post_meta( get_the_ID(), "downloads", $downloads );
	update_post_meta( get_the_ID(), "agb_accepted_by_user_timestamp", time() );
	update_post_meta( get_the_ID(), "agb_2_accepted_by_user_timestamp", time() );
}

/**
 * Start the download with header send informations
 *
 * @param $f12_download_id
 * @param $f12_generic_created
 * @param $email
 */
function f12d_start_download( $f12_download_id, $f12_generic_created = "", $email = "" ) {
	if ( ! isset( wp_upload_dir()["basedir"] ) || empty( wp_upload_dir()["basedir"] ) ) {
		wp_die( "Upload Verzeichnis nicht gefunden" );
	}

	$baseurl = wp_upload_dir()["baseurl"] . "/f12d";
	$basedir = wp_upload_dir()["basedir"] . "/f12d";

	$file      = f12d_get_file_name_by_id( $f12_download_id );
	$extension = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );

	// create the url to the file
	$file_rel = $baseurl . "/" . $f12_download_id . "_" . $f12_generic_created . "_" . $email . "." . $extension;
	$file_abs = $basedir . "/" . $f12_download_id . "_" . $f12_generic_created . "_" . $email . "." . $extension;

	// get the file mime type using the file extension
	switch ( strtolower( substr( strrchr( $file_rel, '.' ), 1 ) ) ) {
		case 'pdf':
			$mime = 'application/pdf';
			break;
		case 'zip':
			$mime = 'application/zip';
			break;
		case 'jpeg':
		case 'jpg':
			$mime = 'image/jpg';
			break;
		default:
			$mime = 'application/force-download';
	}

	header( 'Pragma: public' );    // required
	header( 'Expires: 0' );        // no cache
	header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
	header( 'Cache-Control: private', false );
	header( 'Content-Type: ' . $mime );
	header( 'Content-Disposition: attachment; filename="' . basename( $file_abs ) . '"' );
	header( 'Content-Transfer-Encoding: binary' );
	header( 'Content-Length: ' . filesize( $file_abs ) );    // provide file size
	header( 'Connection: close' );
	readfile( $file_abs );        // push it out
	ignore_user_abort( true ); // ignores the cancel of the download
	register_shutdown_function( 'unlink', $file_abs ); // unlink the file after shutdown
}

add_action( "wp", "f12d_download_page_process" );

/**
 * Moves a file temporary to the uploads folder for download
 *
 * @param $f12_download_id
 * @param $f12_generic_created
 * @param $email
 */
function f12d_move_file_to_tmp_folder( $f12_download_id, $f12_generic_created, $email ) {
	$folder    = get_option( "f12d" )["storage"];
	$file      = $folder . "/" . f12d_get_file_name_by_id( $f12_download_id );
	$extension = strtolower( pathinfo( $file, PATHINFO_EXTENSION ) );

	if ( ! isset( wp_upload_dir()["basedir"] ) || empty( wp_upload_dir()["basedir"] ) ) {
		wp_die( "Upload Verzeichnis nicht gefunden" );
	}

	$basedir = wp_upload_dir()["basedir"] . "/f12d";

	if ( ! is_dir( $basedir ) ) {
		wp_mkdir_p( $basedir );
	}

	copy( $file, $basedir . "/" . $f12_download_id . "_" . $f12_generic_created . "_" . $email . "." . $extension );
}

/**
 * Check if the file with the given timestamp and the given id exists.
 *
 * @param $f12_download_id - The ID of the download
 * @param $f12_generic_created - The timestamp
 * @param $email - The E-mail from the requesting user
 *
 * @return numeric | 1 = ok, 0 = not found, -1 depleted, -2 download limit
 */
function f12d_file_status( $f12_download_id, $f12_generic_created, $email ) {
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
	if ( $loop->post_count > 0 ) {
		$loop->the_post();

		$stored_meta_data = get_post_meta( get_the_ID() );

		$downloads_maximum = 0;
		$downloads         = 0;
		$depleted          = false;

		if ( ! empty( $stored_meta_data["downloads_maximum"] ) ) {
			$downloads_maximum = $stored_meta_data["downloads_maximum"][0];
		}

		if ( ! empty( $stored_meta_data["downloads"] ) ) {
			$downloads = $stored_meta_data["downloads"][0];
		}

		if ( ! empty( $stored_meta_data["depleted"] ) ) {
			$depleted = $stored_meta_data["depleted"][0];
		}

		if ( $downloads >= $downloads_maximum ) {
			return - 2;
		}

		if ( $depleted == true ) {
			return - 1;
		}

		return 1;
	}

	return 0;
}