<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Hook edit page to add the download elements as meta box to the page
 */
function f12d_hook_custom_metabox() {
    // Get all post types where to add the download meta box
    $post_types = explode(",",get_option("f12d")["post_types"]);

    foreach($post_types as $post_type){
	    add_meta_box(
		    "f12d_post_metabox",
		    __('Downloads', "f12-download"),
		    "f12d_callback_custom_meta_box",
		    $post_type,
		    "side"
	    );
    }
}

add_action( 'add_meta_boxes', 'f12d_hook_custom_metabox' );

/**
 * Create the output for the meta box which will be displayed on each page.
 */
function f12d_callback_custom_meta_box() {
	global $post;
	wp_nonce_field( basename( __FILE__ ), "f12d_metabox_nonce" );
	$stored_meta_data = get_post_meta( $post->ID );

	$f12_download = array();

	if ( ! empty( $stored_meta_data["f12_download"] ) ) {
		$f12_download = explode( ",", $stored_meta_data["f12_download"][0] );
	}

	?>
    <a href="#TB_inline?width=600&height=550&inlineId=f12-file-picker" data-output="f12_metabox_output"
       data-output-type="li" class="thickbox">
        <?php echo __('Datei hinzufügen', "f12-download");?></a>

    <ul class="f12_metabox_output">
		<?php
		foreach ( $f12_download as $key => $value ):
			?>
            <li>
                <input type="hidden" name="f12_download[]" value="<?php echo $value; ?>"/>
                <?php echo f12d_get_file_name_by_id($value);?> (<a href="javascript:void(0);" class="f12-download-remove"><?php echo __('Entfernen', "f12-download");?></a>)
            </li>
		<?php
		endforeach;
		?>
    </ul>
	<?php
	f12_modal_file_picker( "f12_metabox_add", "f12_metabox_output", "li", $f12_download);
}


/**
 * Hook the save from posts to save the downloads
 */
function f12d_save_downloads() {
	global $post;
	if ( isset( $post ) ) {
		$post_id = $post->ID;
		// Check save status
		$is_autosave    = wp_is_post_autosave( $post_id );
		$is_revision    = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST['f12d_metabox_nonce'] ) && wp_verify_nonce( $_POST['f12d_metabox_nonce'], basename( __FILE__ ) ) ) ? true : false;

		// Exit script depending on status
		if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		if ( isset( $_POST["f12_download"] ) ) {
			$f12_download = implode( ",", $_POST["f12_download"] );
			update_post_meta( $post_id, "f12_download", $f12_download );
		} else {
		    delete_post_meta($post_id, "f12_download");
		}
	}
}

add_action( "save_post", "f12d_save_downloads" );