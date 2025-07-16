<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class F12D {
	public function __construct() {
		$status = $this->update();
		$this->update_storage();
		$this->show( $status );
	}

	/**
	 * Display html
	 */
	private function show( $status ) {
		$args = array(
			"update_status"                => $status,
			"nonce"                        => wp_nonce_field( "f12d-settings-update" ),
			"max_file_size"                => get_option( "f12d" )["max_file_size"],
			"allowed_extensions"           => get_option( "f12d" )["allowed_extensions"],
			"delete_files_onchange"        => get_option( "f12d" )["delete_files_onchange"],
			"download_page"                => F12DownloadUtils::get_option_list_pages( get_option( "f12d" )["download_page"] ),
			"download_page_agb"            => F12DownloadUtils::get_option_list_pages( get_option( "f12d" )["download_page_agb"] ),
			"download_page_agb_text"       => get_option( "f12d" )["download_page_agb_text"],
			"download_page_agb_checkbox_1" => get_option( "f12d" )["download_page_agb_checkbox_1"],
			"download_page_agb_checkbox_2" => get_option( "f12d" )["download_page_agb_checkbox_2"],
			"email_page_send"              => F12DownloadUtils::get_option_list_pages( get_option( "f12d" )["email_page_send"] ),
			"email"                        => get_option( "f12d" )["email"],
			"post_types"                   => $this->get_checkboxes_posttype( explode( ",", get_option( "f12d" )["post_types"] ) ),
			"storage"                      => get_option( "f12d" )["storage"]
		);
		echo F12DownloadUtils::get_admin_template( "admin.php", $args );
	}

	/**
	 * Update settings
	 * @return int|void
	 */
	public function update() {
		if ( isset( $_POST["update"] ) && $_POST["action"] == "f12d_settings_save" ) {
			$is_nonce_verified = isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST["_wpnonce"], "f12d-settings-update" ) ? true : false;

			if ( ! $is_nonce_verified ) {
				return;
			}

			$max_file_size                = sanitize_text_field( $_POST["max_file_size"] );
			$allowed_extension            = sanitize_text_field( $_POST["allowed_extensions"] );
			$delete_files_onchange        = $_POST["delete_files_onchange"];
			$download_page                = $_POST["download_page"];
			$download_page_agb            = $_POST["download_page_agb"];
			$download_page_agb_text       = stripslashes($_POST["download_page_agb_text"]);
			$download_page_agb_checkbox_1 = stripslashes($_POST["download_page_agb_checkbox_1"]);
			$download_page_agb_checkbox_2 = stripslashes($_POST["download_page_agb_checkbox_2"]);
			$email                        = sanitize_email( $_POST["email"] );
			$post_types                   = "";
			$email_page_send              = $_POST["email_page_send"];


			if ( isset( $_POST["post_types"] ) ) {
				$post_types = implode( ",", $_POST["post_types"] );
			}

			if ( ! is_numeric( $max_file_size ) ) {
				$max_file_size = 5120000;
			}

			update_option( "f12d", array(
				"max_file_size"                => $max_file_size,
				"allowed_extensions"           => $allowed_extension,
				"delete_files_onchange"        => $delete_files_onchange,
				"storage"                      => get_option( "f12d" )["storage"],
				"download_page"                => $download_page,
				"download_page_agb"            => $download_page_agb,
				"download_page_agb_text"       => $download_page_agb_text,
				"download_page_agb_checkbox_1" => $download_page_agb_checkbox_1,
				"download_page_agb_checkbox_2" => $download_page_agb_checkbox_2,
				"email"                        => $email,
				"post_types"                   => $post_types,
				"email_page_send"              => $email_page_send,
			) );

			return 1;
		}
	}

	/**
	 * Return a string with all checkboxes
	 *
	 * @param array $checked
	 *
	 * @return string
	 */
	function get_checkboxes_posttype( array $checked = array() ) {
		$post_types = get_post_types();
		ob_start();
		foreach ( $post_types as $post_type ) {
			?>
            <input type="checkbox" name="post_types[]"
                   value="<?php echo $post_type; ?>" <?php if ( in_array( $post_type, $checked ) ) {
				echo "checked=\"checked\"";
			} ?>> <?php echo $post_type; ?><br>
			<?php
		}
		$output_string = ob_get_contents();
		ob_end_clean();

		return $output_string;
	}

	/**
	 * Update Storage
	 */
	public function update_storage() {
		if ( isset( $_POST["update-storage"] ) ) {
			$is_nonce_verified = isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST["_wpnonce"], "f12d-storage-update" ) ? true : false;

			if ( ! $is_nonce_verified ) {
				return;
			}

			$options            = get_option( "f12d" );
			$options["storage"] = dirname( __FILE__, 3 ) . "/storage";

			update_option( "f12d", $options );
		}
	}
}

new F12D();

//$update_status = 0;

//$update_status = f12d_update_settings();
//f12d_update_storage();
?>