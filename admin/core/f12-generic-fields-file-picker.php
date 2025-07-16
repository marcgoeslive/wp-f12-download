<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Creates the popup for the Files to link
 * This will display the modal box with all f12d-downloads inside.
 * On click they will be added to generic link
 */
function f12_modal_file_picker( $js_callback_function = "", $js_output_class = "", $js_output_type = "", $ignored_files = array() ) {
	add_thickbox();
	?>
    <div id="f12-file-picker" style="display:none;">
        <div class="f12-file-picker__content">
            <div class="f12-option-bar">
                <div class="f12-pageinator">
					<?php echo f12_get_downloads_pageinator(); ?>
                </div>
                <div class="f12-search">
                    <input type="text" name="search" value="" placeholder="Dateiname">
                </div>
            </div>
            <table class="f12-file-picker" cellpadding="0" cellspacing="0"
				<?php if ( ! empty( $js_callback_function ) ) {
					echo "data-callback=\"" . $js_callback_function . "\"";
				} ?>
				<?php if ( ! empty( $js_output_class ) ) {
					echo "data-output=\"" . $js_output_class . "\"";
				} ?>
				<?php if ( ! empty( $js_output_type ) ) {
					echo "data-output-type=\"" . $js_output_type . "\"";
				} ?>
            >
				<?php echo f12_get_downloads_by_page( 1 ); ?>
            </table>
        </div>
    </div>
	<?php
}

/**
 * Returns the Page Bar
 */
function f12_get_downloads_pageinator() {
	ob_start();

	$args = array(
		"post_type"      => "f12d_download",
		"posts_per_page" => 10,
	);

	$loop = new WP_Query( $args );

	$pages = $loop->max_num_pages;

	for ( $i = 1; $i <= $pages; $i ++ ):
		?>
        <a href="javascript:void(0);" class="f12-button<?php if($i == 1) echo " active";?>" data-value="<?php echo $i; ?>"><?php echo $i; ?></a>
	<?php
	endfor;
	$output_string = ob_get_contents();
	ob_end_clean();

	return $output_string;
}

/**
 * Read all entries by Page
 */
function f12_get_downloads_by_page( $page, $keyword = "" ) {
	ob_start();
	?>
    <tr>
        <th>
	        <?php echo __('ID', "f12-download");?>
        </th>
        <th>
	        <?php echo __('Titel', "f12-download");?>
        </th>
        <th>
	        <?php echo __('Datei', "f12-download");?>
        </th>
    </tr>
	<?php

	if ( empty( $keyword ) ) {
		$args = array(
			"post_type"      => "f12d_download",
			"posts_per_page" => 10,
			"paged"          => $page
		);
	} else {
		$args = array(
			"post_type"      => "f12d_download",
			"posts_per_page" => 10,
			"paged"          => $page,
			"meta_query"     => array(
				array(
					"key"     => "file",
					"value"   => $keyword,
					"compare" => "LIKE"
				)
			)
		);
	}

	$loop = new WP_Query( $args );
	$items = $loop->get_posts();
	foreach($items as $item):
        /* @var $item WP_Post */
		// Loading Meta-Data
		$f12d_stored_meta = get_post_meta( $item->ID );
		?>
        <tr>
            <td>
				<?php echo $item->ID; ?>
            </td>
            <td>
				<?php echo $item->post_title; ?>
            </td>
            <td>
				<?php
				if ( ! empty( $f12d_stored_meta["file"] ) ) {
					echo $f12d_stored_meta["file"][0];
				}
				?>
            </td>
        </tr>
	<?php
	endforeach;
	$output_string = ob_get_contents();
	ob_end_clean();

	return $output_string;
}


/**
 * Ajax response to get the Next page for the file picker
 */
function f12d_filepicker_get_page() {
	$page = isset( $_POST["page"] ) ? $_POST["page"] : 1;

	echo json_encode( f12_get_downloads_by_page( $page ) );
	wp_die();
}

add_action( 'wp_ajax_f12d_filepicker_get_page', 'f12d_filepicker_get_page' );

/**
 * Ajax response to the the file searched by the search field
 */
function f12d_filepicker_search() {
	$value = isset( $_POST["value"] ) ? $_POST["value"] : "";
	$page  = isset( $_POST["page"] ) ? $_POST["page"] : 1;

	echo json_encode( f12_get_downloads_by_page( $page, $value ) );
	wp_die();
}

add_action( 'wp_ajax_f12d_filepicker_search', 'f12d_filepicker_search' );