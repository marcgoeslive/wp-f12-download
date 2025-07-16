<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class F12DownloadUtils {
	/**
	 * Returns a Option list with all pages and the given $selected as selected.
	 *
	 * @param $selected_id int
	 *
	 * @return string
	 */
	public static function get_option_list_pages( $selected_id ) {

		$option = "<option value='-1'>Bitte wählen</option>";
		$pages  = get_pages();
		foreach ( $pages as $page ) {
			if ( $selected_id == $page->ID ) {
				$option .= "<option value=\"" . $page->ID . "\" selected='selected'>" . $page->post_title . "</option>";
			} else {
				$option .= "<option value=\"" . $page->ID . "\">" . $page->post_title . "</option>";
			}
		}

		return $option;
	}

	/**
	 * Get field from Stored Meta Data and return value if it exists,
	 * otherwise return the default value
	 */
	public static function get_field( $stored_meta_data = array(), $key, $default = "" ) {
		if ( isset( $stored_meta_data[ $key ] ) && ! empty( $stored_meta_data[ $key ] ) ) {
			return $stored_meta_data[ $key ][0];
		}

		return $default;
	}

	/**
	 * Return the generic object with the given file id, timestamp and email
	 *
	 * @param $file_id
	 * @param $timestamp
	 * @param $email
	 *
	 * @return null | WP_Post
	 */
	public static function get_generic_object( $file_id, $timestamp, $email ) {
		$args = array(
			"post_type"     => "f12d_generic",
			"post_per_page" => 1,
			"meta_query"    => array(
				"relation" => "AND",
				array(
					"key"   => "created",
					"value" => $timestamp
				),
				array(
					"key"   => "f12d_download_id",
					"value" => $file_id
				),
				array(
					"key"   => "email",
					"value" => $email
				)

			)
		);

		$query = new WP_Query( $args );

		if ( $query->post_count > 0 ) {
			return $query->get_posts()[0];
		} else {
			return null;
		}
	}

	/**
	 * Read the hash from the url and return it
	 * @return array|bool|mixed
	 */
	public static function get_hash() {
		// read the hash for the download
		$hash = get_query_var( "hash" );

		if ( strlen( $hash ) == 0 || empty( $hash ) ) {
			return array(
				0 => - 1, // ID
				1 => 0, // Timestamp
				2 => "", // E-Mail
			);
		}

		$hash = explode( "_", $hash );

		return $hash;
	}

	/**
	 * Loads and returns the content of the frontend template
	 *
	 * @param $template - The Template that should be loaded
	 * @param array $args - parameter that should be loaded
	 *
	 * @return string the output of the template
	 */
	public static function loadTemplate( $template, $args = array() ) {
		if ( file_exists( get_stylesheet_directory() . "/f12d-download/" . $template ) ) {
			ob_start();
			include( get_stylesheet_directory() . "/f12d-download/" . $template );
			$output = ob_get_contents();
		} else {
			ob_start();
			include( plugin_dir_path( __FILE__ ) . "../templates/" . $template );
			$output = ob_get_clean();
		}

		return $output;
	}

	/**
	 * The same as loadAdminTemplate but returning the template instead of executing it
	 *
	 * @param $template
	 * @param array $args
	 *
	 * @return string
	 */
	public static function get_admin_template( $template, $args = array() ) {
		ob_start();
		include( plugin_dir_path( __FILE__ ) . "../admin/templates/" . $template );
		$output = ob_get_clean();

		return $output;
	}
}

/**
 * Transform filesize to readable format
 *
 * @param $bytes
 *
 * @return string
 */
function f12d_format_size_unit( $bytes ) {
	if ( $bytes >= 1073741824 ) {
		$bytes = number_format( $bytes / 1073741824, 2 ) . ' GB';
	} elseif ( $bytes >= 1048576 ) {
		$bytes = number_format( $bytes / 1048576, 2 ) . ' MB';
	} elseif ( $bytes >= 1024 ) {
		$bytes = number_format( $bytes / 1024, 2 ) . ' KB';
	} elseif ( $bytes > 1 ) {
		$bytes = $bytes . ' bytes';
	} elseif ( $bytes == 1 ) {
		$bytes = $bytes . ' byte';
	} else {
		$bytes = '0 bytes';
	}

	return $bytes;
}

/**
 * return the file size
 */
function f12d_get_file_size_by_id( $f12_download_id ) {
	$folder = get_option( "f12d" )["storage"];
	$file   = $folder . "/" . f12d_get_file_name_by_id( $f12_download_id );


	if ( file_exists( $file ) ) {
		return f12d_format_size_unit( filesize( $file ) );
	}

	return - 1;
}

/**
 * Generates the download link for the temporary file
 *
 * @param $f12d_download_id
 * @param $created
 *
 * @return string
 */
function f12d_get_download_link( $f12d_download_id, $created = "", $email = "" ) {
	$download_page_id = get_option( "f12d" )["download_page"];
	if ( $download_page_id == - 1 ) {
		return "Bitte legen Sie eine Download Seite in den Einstellungen fest.";
	}

	if ( $created != "" && $email != "" ) {
		return add_query_arg( "hash", $f12d_download_id . "_" . $created . "_" . $email, get_page_link( $download_page_id ) );
	} else {
		// Only for unsafe files
		return add_query_arg( "hash", $f12d_download_id, get_page_link( $download_page_id ) );
	}
}

/**
 * Returns the File Name of an given f12d_download post type ID
 * If none file available it will return an empty string.
 *
 * @param $id
 *
 * @return string|void
 */
function f12d_get_file_name_by_id( $id ) {
	$f12d_stored_meta = get_post_meta( $id );
	if ( ! empty( $f12d_stored_meta["file"] ) ) {
		return esc_attr( $f12d_stored_meta["file"][0] );
	}

	return "";
}

// Adding scripts
function f12d_hook_wp_enqueue_scripts() {
	wp_register_script( "f12_download_popup", plugins_url( '../assets/js/f12-download-popup.js', __FILE__ ), array( "jquery" ), false, true );
	wp_enqueue_script( "f12_download_popup" );

	wp_enqueue_style( "f12_download_popup", plugins_url( '../assets/css/f12-download-popup.css', __FILE__ ) );
}

add_action( 'wp_enqueue_scripts', 'f12d_hook_wp_enqueue_scripts' );

/**
 * Allows to load templates by file and to override them by the users
 * to assign them to there theme.
 *
 * @param $template
 * @param array $args
 *
 * @return string
 */
function f12d_load_template( $template, $args = array() ) {
	if ( file_exists( get_stylesheet_directory() . "/f12-download/" . $template ) ) {
		include( get_stylesheet_directory() . "/f12-download/" . $template );
	} else {
		include( plugin_dir_path( __FILE__ ) . "../templates/" . $template );
	}
}