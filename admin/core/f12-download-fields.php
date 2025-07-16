<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Create & Add MetaBox
function f12d_hook_add_meta_box() {
	add_meta_box(
		"f12d_meta",
		__( 'Datei Informationen', "f12-download" ),
		"f12d_callback_meta_box",
		"f12d_download"
	);
}

add_action( 'add_meta_boxes', 'f12d_hook_add_meta_box' );

function f12d_enqueue_scripts() {
	wp_register_script( "f12-form-validate", plugins_url( "../assets/js/f12-form-validate.js", __FILE__ ), array( "jquery" ), false, true );
	wp_enqueue_script( "f12-form-validate" );

	wp_enqueue_style( "f12-form-validate", plugin_dir_url( __FILE__ ) . "../assets/css/f12-form-validate.css" );;
}

add_action( "admin_enqueue_scripts", "f12d_enqueue_scripts" );

/**
 * Rendering our metabox
 */
function f12d_callback_meta_box() {
	global $post;

	add_action( "admin_notices", "test" );
	// increase security
	wp_nonce_field( basename( __FILE__ ), "f12d_download_nonce" );
	$f12d_stored_meta = get_post_meta( $post->ID );
	?>
    <div class="f12-page-download">
        <table class="f12-table">
            <tr>
                <td class="label" style="width:300px;">
                    <label>
						<?php echo __( 'Beschreibung', "f12-download" ); ?>
                    </label>
                    <p>
						<?php echo __( 'Kurze Beschreibung für die Datei.', "f12-download" ); ?>
                    </p>
                </td>
                <td>
					<?php
					$content = "";
					if ( ! empty( $f12d_stored_meta['file_description'][0] ) ) {
						$content = $f12d_stored_meta['file_description'][0];
					}
					echo wp_editor( $content, "file_description" );
					?>
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label>
						<?php echo __( 'Datei:', "f12-download" ); ?>
                    </label>
                </td>
                <td>
					<?php if ( ! empty( $f12d_stored_meta['file'] ) ) {
						?>
                        <input type="hidden" name="f12-download-file-flag" value="1">
						<?php
						echo "<a href='" . plugin_dir_url( __FILE__ ) . "storage/" . esc_attr( $f12d_stored_meta['file'][0] ) . "' target='_blank''>" . esc_attr( $f12d_stored_meta['file'][0] ) . "</a>";
					} else {
						?>
                        <input type="hidden" name="f12-download-file-flag" value="">
						<?php
					} ?>
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label>
						<?php echo __( 'Link per E-Mail senden?', "f12-download" ); ?>
                    </label>
                </td>
                <td>
                    <input type="checkbox" name="sendbymail" value="1"
						<?php if ( ! empty( $f12d_stored_meta["sendbymail"] ) && $f12d_stored_meta["sendbymail"][0] == 1 ) : ?>
                            checked="checked"
						<?php endif; ?>
                    /> <?php echo __( 'Ja', "f12-download" ); ?>
                </td>
            </tr>
            <tr>
                <td class="label" style="width:300px;">
                    <label>
						<?php echo __( 'Widerrufsbelehrung anzeigen:', "f12-download" ); ?>
                    </label>
                    <p>
						<?php echo __( 'Wenn aktiviert muss der Besucher vor dem Herunterladen der Datei den Widerrufsbelehrungen zustimmen.', "f12-download" ); ?>
                    </p>
                </td>
                <td>
					<?php
					$agb = false;
					if ( ! empty( $f12d_stored_meta['agb'] ) ) {
						$agb = esc_attr( $f12d_stored_meta['agb'][0] );
					}
					?>
                    <input type="radio" name="agb" value="1" <?php if ( $agb == true ) {
						echo "checked";
					} ?>> <?php echo __( 'Ja', "f12-download" ); ?><br><br>
                    <input type="radio" name="agb" value="0" <?php if ( $agb == false ) {
						echo "checked";
					} ?>> <?php echo __( 'Nein', "f12-download" ); ?>
                </td>
            </tr>
            <tr>
                <td class="label">
                    <label>
						<?php echo __( 'Datei-Upload:', "f12-download" ); ?>
                    </label>
                </td>
                <td>
                    <input id="upload_file" class="f12-form-validate"
                           validation='{"condition":[{"type":"text","value":"","name":"f12-download-file-flag"}],"validation":{"required":true}}'
                           type="file" name="file" accept="application/pdf"/>
                </td>
            </tr>
        </table>
    </div>
	<?php
}

/**
 * Hook called whenever the post is saved
 */
function f12d_hook_save_post() {
	global $post;

	if ( isset( $post ) ) {
		$post_id = $post->ID;

		// Check save status
		$is_autosave    = wp_is_post_autosave( $post_id );
		$is_revision    = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST['f12d_download_nonce'] ) && wp_verify_nonce( $_POST['f12d_download_nonce'], basename( __FILE__ ) ) ) ? true : false;

		// Exit script depending on status
		if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		// Update Description
		$description = $_POST['file_description'];
		update_post_meta( $post_id, "file_description", sanitize_text_field( $description ) );

		// agb
		$agb = isset( $_POST["agb"] ) ? $_POST["agb"] : 0;
		update_post_meta( $post_id, "agb", sanitize_text_field( $agb ) );

		$sendbymail = isset( $_POST["sendbymail"] ) ? $_POST["sendbymail"] : 0;
		update_post_meta( $post_id, "sendbymail", $sendbymail );

		if ( isset( $_FILES ) && ! empty( $_FILES ) ) {
			$status = f12d_meta_file_upload( $post_id, $_FILES['file'] );
			if ( $status != 1 ) {
				add_filter( 'redirect_post_location', 'f12d_add_notice_query_var_error_filesize', 99 );
			}
		}
	}
}

/**
 * Responsible for the uplaod of the file
 *
 * @param $post_id the id of the post
 * @param $file array from $_FILES
 *
 * @return bool
 */
function f12d_meta_file_upload( $post_id, $file ) {
	// Update File
	$filename  = pathinfo( $file['name'], PATHINFO_FILENAME );
	$extension = strtolower( pathinfo( $file['name'], PATHINFO_EXTENSION ) );

	// Upload Dir
	$folder = get_option( "f12d" )["storage"];

	// Allowed Extensions
	$allowed_extensions = explode( ",", get_option( "f12d" )["allowed_extensions"] );
	if ( ! in_array( $extension, $allowed_extensions ) ) {
		//echo "File not supported";

		return - 1;
	}

	// Maximum file size
	$max_size = intval(get_option( "f12d" )["max_file_size"]); // 5MB by default
	if ( $file['size'] > $max_size ) {
		//echo "File is to big";
		return - 2;
	}

	// Remove old file
	if ( get_option( "f12d" )["delete_files_onchange"] ) {
		$f12d_stored_meta = get_post_meta( $post_id );
		if ( ! empty( $f12d_stored_meta["file"] ) ) {
			$old_file = $f12d_stored_meta["file"][0];
			$old_path = $folder . $old_file;
			if ( is_file( $old_path ) ) {
				unlink( $old_path );
			}
		}
	}


	// Copy file
	$new_filename = $filename . "_" . $post_id . "." . $extension;
	$new_path     = $folder . $filename . "_" . $post_id . "." . $extension;

	move_uploaded_file( $file['tmp_name'], $new_path );
	//echo "File uploaded successfully to: " . $new_path;

	update_post_meta( $post_id, "file", $new_filename );

	return 1;
}

add_action( 'save_post', 'f12d_hook_save_post' );

/**
 * Extending the form to accept files
 */
function f12d_hook_update_edit_form() {
	echo ' enctype="multipart/form-data"';
}

add_action( 'post_edit_form_tag', 'f12d_hook_update_edit_form' );

/**
 * Creating custom error messages for the notice handler from wordpress
 */
function f12d_hook_admin_notice_error() {
	if ( ! isset( $_GET["error"] ) ) {
		return;
	} else {
		if ( $_GET["error"] == "filesize" ) {
			?>
            <div class="notice notice-error is-dismissible">
                <p><?php echo __( 'Die Datei ist zu groß.', "f12-download" ); ?></p>
            </div>
			<?php
		}
	}
}

add_action( 'admin_notices', 'f12d_hook_admin_notice_error' );

/**
 * Adding additional query arg which will trigger our error handler
 *
 * @param $location
 *
 * @return string
 */
function f12d_add_notice_query_var_error_filesize( $location ) {
	remove_filter( 'redirect_post_location', 'f12d_hook_admin_notice_error', 99 );

	return add_query_arg( array( 'error' => 'filesize' ), $location );
}
