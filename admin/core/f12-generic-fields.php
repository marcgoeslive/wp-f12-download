<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// Create & Add MetaBox
function f12d_generic_hook_add_meta_box() {
	add_meta_box(
		"f12d_generic_meta",
		__( 'Link Informationen', "f12-download" ),
		"f12d_generic_callback_meta_box",
		"f12d_generic"
	);
}

add_action( 'add_meta_boxes', 'f12d_generic_hook_add_meta_box' );

/**
 * Rendering our metabox
 */
function f12d_generic_callback_meta_box() {
	global $post;

	// increase security
	wp_nonce_field( basename( __FILE__ ), "f12d_generic_nonce" );
	$f12d_stored_meta = get_post_meta( $post->ID );
	?>
    <table class="f12-table">
        <tr>
            <td class="label" style="width:300px;">
                <label>
					<?php echo __( 'E-Mail:', "f12-download" ); ?>
                </label>
                <p>
					<?php echo __( 'An diese E-Mail wurde der Link versendet.', "f12-download" ); ?>
                </p>
            </td>
            <td>
                <input type="text" name="email" class="f12-form-validate" validation='{"validation":{"required":true}}'
                       value="<?php if ( ! empty( $f12d_stored_meta['email'] ) ) {
					       echo esc_attr( $f12d_stored_meta['email'][0] );
				       } ?>"/>
            </td>
        </tr>
        <tr>
            <td class="label" style="width:300px;">
                <label>
					<?php echo __( 'Vorname:', "f12-download" ); ?>
                </label>
                <p>
					<?php echo __( 'Der Vorname.', "f12-download" ); ?>
                </p>
            </td>
            <td>
                <input type="text" name="firstname" class="f12-form-validate"
                       validation='{"validation":{"required":true}}'
                       value="<?php if ( ! empty( $f12d_stored_meta['firstname'] ) ) {
					       echo esc_attr( $f12d_stored_meta['firstname'][0] );
				       } ?>"/>
            </td>
        </tr>
        <tr>
            <td class="label" style="width:300px;">
                <label>
					<?php echo __( 'Nachname:', "f12-download" ); ?>
                </label>
                <p>
					<?php echo __( 'Der Nachname.', "f12-download" ); ?>
                </p>
            </td>
            <td>
                <input type="text" name="lastname" class="f12-form-validate"
                       validation='{"validation":{"required":true}}'
                       value="<?php if ( ! empty( $f12d_stored_meta['lastname'] ) ) {
					       echo esc_attr( $f12d_stored_meta['lastname'][0] );
				       } ?>"/>
            </td>
        </tr>
        <tr>
            <td class="label" style="width:300px;">
                <label>
					<?php echo __( 'Erstellt:', "f12-download" ); ?>
                </label>
                <p>
					<?php echo __( 'Der Timestamp wann der Link angefordert wurde.', "f12-download" ); ?>
                </p>
            </td>
            <td>
                <input type="text" name="created_timestamp" readonly
                       value="<?php if ( ! empty( $f12d_stored_meta['created'] ) ) {
					       echo esc_attr( $f12d_stored_meta['created'][0] );
				       } ?>"/>
				<?php if ( ! empty( $f12d_stored_meta['created'] ) ) : ?>
					<?php echo __( 'Leserlich:', "f12-download" ); ?>
					<?php
					echo date( "d.m.Y", esc_attr( $f12d_stored_meta['created'][0] ) );
					echo " um " . date( "H:i:s", esc_attr( $f12d_stored_meta['created'][0] ) );
				endif;
				?>
            </td>
        </tr>
        <tr>
            <td class="label" style="width:300px;">
                <label><?php echo __( 'Downloads:', "f12-download" ); ?></label>
                <p>
					<?php echo __( 'Wie häufig wurde die Datei bereits heruntergeladen.', "f12-download" ); ?>
                </p>
            </td>
            <td>
                <input type="number" name="downloads" class="f12-form-validate"
                       validation='{"validation":{"required":true}}'
                       value="<?php if ( ! empty( $f12d_stored_meta['downloads'] ) ) {
					       echo esc_attr( $f12d_stored_meta['downloads'][0] );
				       } else {
					       echo "0";
				       } ?>"/>
            </td>
        </tr>
        <tr>
            <td class="label" style="width:300px;">
                <label>
					<?php echo __( 'Downloadlimit:', "f12-download" ); ?>
                </label>
                <p>
					<?php echo __( 'Wie oft darf diese Datei heruntergeladen werden.', "f12-download" ); ?>
                </p>
            </td>
            <td>
                <input type="number" name="downloads_maximum" class="f12-form-validate"
                       validation='{"validation":{"required":true}}'
                       value="<?php if ( ! empty( $f12d_stored_meta['downloads_maximum'] ) ) {
					       echo esc_attr( $f12d_stored_meta['downloads_maximum'][0] );
				       } else {
					       echo "1";
				       } ?>"/>
            </td>
        </tr>
        <tr>
            <td class="label" style="width:300px;">
                <label>
					<?php echo __( 'Deaktivieren:', "f12-download" ); ?>
                </label>
                <p>
					<?php echo __( 'Ermöglicht das manuelle Deaktivieren des Links durch einen Administrator.', "f12-download" ); ?>
                </p>
            </td>
            <td>
				<?php
				$depleted = false;
				if ( ! empty( $f12d_stored_meta['depleted'] ) ) {
					$depleted = esc_attr( $f12d_stored_meta['depleted'][0] );
				}
				?>
                <input type="radio" name="depleted" value="1" <?php if ( $depleted == true ) {
					echo "checked";
				} ?>> <?php echo __( 'Ja', "f12-download" ); ?><br><br>
                <input type="radio" name="depleted" value="0" <?php if ( $depleted == false ) {
					echo "checked";
				} ?>> <?php echo __( 'Nein', "f12-download" ); ?>
            </td>
        </tr>
        <tr>
            <td class="label" style="width:300px;">
                <label>
					<?php echo __( 'Widerrufsbelehrung akzeptiert:', "f12-download" ); ?>
                </label>
                <p>
					<?php echo __( 'Wenn der Besucher die Widerrufsbelehrung durch den Besucher akzeptiert wurde, wird dies hier gespeichert.', "f12-download" ); ?>
                </p>
            </td>
            <td>
				<?php
				$agb_accepted = false;
				if ( ! empty( $f12d_stored_meta['agb_accepted_by_user'] ) ) {
					$agb_accepted = esc_attr( $f12d_stored_meta['agb_accepted_by_user'][0] );
				}
				?>
                <input type="radio" name="agb_accepted_by_user" value="1" <?php if ( $agb_accepted == true ) {
					echo "checked";
				} ?>> <?php echo __( 'Ja', "f12-download" ); ?><br><br>
                <input type="radio" name="agb_accepted_by_user" value="0" <?php if ( $agb_accepted == false ) {
					echo "checked";
				} ?>> <?php echo __( 'Nein', "f12-download" ); ?>
                <br><br>
				<?php if ( ! empty( $f12d_stored_meta['agb_accepted_by_user_timestamp'] ) && ! empty( $f12d_stored_meta["agb_accepted_by_user_timestamp"][0] ) ) : ?>Akzeptiert am: <?php
					echo date( "d.m.Y", esc_attr( $f12d_stored_meta['agb_accepted_by_user_timestamp'][0] ) );
					echo " um " . date( "H:i:s", esc_attr( $f12d_stored_meta['agb_accepted_by_user_timestamp'][0] ) );
				endif;
				?>
                <input type="hidden" name="created" readonly
                       value="<?php if ( ! empty( $f12d_stored_meta['agb_accepted_by_user_timestamp'] ) ) {
					       echo esc_attr( $f12d_stored_meta['agb_accepted_by_user_timestamp'][0] );
				       } ?>"/>
            </td>
        </tr>
        <tr>
            <td class="label" style="width:300px;">
                <label>
					<?php echo __( 'Widerrufsbelehrung 2 akzeptiert:', "f12-download" ); ?>
                </label>
                <p>
					<?php echo __( 'Wenn der Besucher die Widerrufsbelehrung durch den Besucher akzeptiert wurde, wird dies hier gespeichert.', "f12-download" ); ?>
                </p>
            </td>
            <td>
				<?php
				$agb_2_accepted = false;
				if ( ! empty( $f12d_stored_meta['agb_2_accepted_by_user'] ) ) {
					$agb_2_accepted = esc_attr( $f12d_stored_meta['agb_2_accepted_by_user'][0] );
				}
				?>
                <input type="radio" name="agb_2_accepted_by_user" value="1" <?php if ( $agb_2_accepted == true ) {
					echo "checked";
				} ?>> <?php echo __( 'Ja', "f12-download" ); ?><br><br>
                <input type="radio" name="agb_2_accepted_by_user" value="0" <?php if ( $agb_2_accepted == false ) {
					echo "checked";
				} ?>> <?php echo __( 'Nein', "f12-download" ); ?>
                <br><br>
				<?php if ( ! empty( $f12d_stored_meta['agb_2_accepted_by_user_timestamp'] ) && ! empty( $f12d_stored_meta["agb_2_accepted_by_user_timestamp"][0] ) ) : ?>Akzeptiert am: <?php
					echo date( "d.m.Y", esc_attr( $f12d_stored_meta['agb_2_accepted_by_user_timestamp'][0] ) );
					echo " um " . date( "H:i:s", esc_attr( $f12d_stored_meta['agb_2_accepted_by_user_timestamp'][0] ) );
				endif;
				?>
                <input type="hidden" name="created" readonly
                       value="<?php if ( ! empty( $f12d_stored_meta['agb_2_accepted_by_user_timestamp'] ) ) {
					       echo esc_attr( $f12d_stored_meta['agb_2_accepted_by_user_timestamp'][0] );
				       } ?>"/>
            </td>
        </tr>
        <tr>
            <td class="label" style="width:300px;">
                <label>
					<?php echo __( 'IP-Adresse:', "f12-download" ); ?>
                </label>
                <p>
					<?php echo __( 'Die IP Adresse des Besuchers.', "f12-download" ); ?>
                </p>
            </td>
            <td>
                <input type="text" name="ip"
                       value="<?php if ( ! empty( $f12d_stored_meta['ip'] ) ) {
					       echo esc_attr( $f12d_stored_meta['ip'][0] );
				       } ?>"/>
            </td>
        </tr>
        <tr>
            <td class="label">
                <label>
					<?php echo __( 'Objekt-ID:', "f12-download" ); ?>
                </label>
                <p>
					<?php echo __( 'Eine Objekt-ID um den Download zu identifizieren.', "f12-download" ); ?>
                </p>
            </td>
            <td>
                <input type="text" name="f12d_object_id" class="f12-form-validate"
                       validation='{"validation":{"required":true}}' id="f12d_object_id"
                       value="<?php if ( ! empty( $f12d_stored_meta['f12d_object_id'] ) ) {
					       echo esc_attr( $f12d_stored_meta['f12d_object_id'][0] );
				       } ?>"/>
            </td>
        </tr>
        <tr>
            <td class="label">
                <label>
					<?php echo __( 'Verlinkte Datei:', "f12-download" ); ?>
                </label>
                <p>
					<?php echo __( 'Die verlinkte Datei zu diesem generischen Link.', "f12-download" ); ?>
                    <br>
                    <br>
                    <a href="#TB_inline?width=800&height=550&inlineId=f12-file-picker" class="thickbox button">
						<?php echo __( 'Datei hinzufügen', "f12-download" ); ?>
                    </a>
                </p>
            </td>
            <td>
                <input type="text" name="f12d_download_id" class="f12-form-validate"
                       validation='{"validation":{"required":true}}' id="f12d_download_id"
                       value="<?php if ( ! empty( $f12d_stored_meta['f12d_download_id'] ) ) {
					       echo esc_attr( $f12d_stored_meta['f12d_download_id'][0] );
				       } ?>"/>
                <a href="#" id="f12d_download_file"><?php if ( ! empty( $f12d_stored_meta['f12d_download_id'] ) ) {
						echo f12d_get_file_name_by_id( $f12d_stored_meta['f12d_download_id'][0] );
					} ?></a>
            </td>
        </tr>
        <tr>
            <td class="label">
                <label>
					<?php echo __( 'Download Link', "f12-download" ); ?>
                </label>
                <p>
					<?php echo __( 'Der Download Link.', "f12-download" ); ?>
                </p>
            </td>
            <td>
				<?php if ( isset( $f12d_stored_meta['f12d_download_id'][0] ) ): ?>
                    <a href="<?php
					echo f12d_get_download_link( $f12d_stored_meta['f12d_download_id'][0], $f12d_stored_meta['created'][0], $f12d_stored_meta['email'][0] ); ?>">
						<?php echo f12d_get_download_link( $f12d_stored_meta['f12d_download_id'][0], $f12d_stored_meta['created'][0], $f12d_stored_meta['email'][0] ); ?>
                    </a>
                    <p>
                        <strong>
							<?php echo __( 'Achtung:', "f12-download" ); ?>
                        </strong>
						<?php echo __( 'Wenn Sie den Link öffnen müssen Sie den Download-Counter zurücksetzen oder das Limit erhöhen um sicherzustellen das die Datei nicht als ungültig makiert wird.', "f12-download" ); ?>
                    </p>
				<?php else: ?>
                    <p>
                        Der Link wird erstellt sobald alle benötigten Felder ausgefüllt wurden.
                    </p>
				<?php endif; ?>
            </td>
        </tr>
    </table>
	<?php

	f12_modal_file_picker();
}

/**
 * Hook called whenever the post is saved
 */
function f12d_generic_hook_save_post() {
	global $post;

	if ( isset( $post ) ) {
		$post_id = $post->ID;

		// Check save status
		$is_autosave    = wp_is_post_autosave( $post_id );
		$is_revision    = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST['f12d_generic_nonce'] ) && wp_verify_nonce( $_POST['f12d_generic_nonce'], basename( __FILE__ ) ) ) ? true : false;

		// Exit script depending on status
		if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
			return;
		}

		// Update Create Time - Only once automatically with the current timestamp
		$created = isset( $_POST['created_timestamp'] ) && ! empty( $_POST["created_timestamp"] ) ? $_POST['created_timestamp'] : time();
		update_post_meta( $post_id, "created", sanitize_text_field( $created ) );

		// Vorname + Nachname
		$firstname = $_POST["firstname"];
		update_post_meta( $post_id, "firstname", sanitize_text_field( $firstname ) );
		$lastname = $_POST["lastname"];
		update_post_meta( $post_id, "lastname", sanitize_text_field( $lastname ) );


		// Update Downloads
		$downloads = $_POST["downloads"];
		update_post_meta( $post_id, "downloads", sanitize_text_field( $downloads ) );

		// Update Max Downloads
		$downloads_maximum = $_POST["downloads_maximum"];
		update_post_meta( $post_id, "downloads_maximum", sanitize_text_field( $downloads_maximum ) );

		// Update Depleted
		$depleted = $_POST["depleted"];
		update_post_meta( $post_id, "depleted", sanitize_text_field( $depleted ) );

		// Update Linked File
		$f12d_download_id = $_POST["f12d_download_id"];
		update_post_meta( $post_id, "f12d_download_id", sanitize_text_field( $f12d_download_id ) );

		// E-Mail
		$email = $_POST["email"];
		update_post_meta( $post_id, "email", sanitize_text_field( $email ) );

		// agb accepted by user
		$agb_accepted_by_user = $_POST["agb_accepted_by_user"];
		update_post_meta( $post_id, "agb_accepted_by_user", sanitize_text_field( $agb_accepted_by_user ) );

		// AGB akzeptiert timestamp
		$agb_accepted_by_user_timestamp = isset( $_POST['agb_accepted_by_user_timestamp'] ) ? $_POST['agb_accepted_by_user_timestamp'] : "";
		update_post_meta( $post_id, "agb_accepted_by_user_timestamp", sanitize_text_field( $agb_accepted_by_user_timestamp ) );

		// agb
		$agb_2 = $_POST["agb_2"];
		update_post_meta( $post_id, "agb_2", sanitize_text_field( $agb_2 ) );

		// agb accepted by user
		$agb_2_accepted_by_user = $_POST["agb_2_accepted_by_user"];
		update_post_meta( $post_id, "agb_2_accepted_by_user", sanitize_text_field( $agb_2_accepted_by_user ) );

		// AGB 2 akzeptiert timestamp
		$agb_2_accepted_by_user_timestamp = isset( $_POST['agb_2_accepted_by_user_timestamp'] ) ? $_POST['agb_2_accepted_by_user_timestamp'] : "";
		update_post_meta( $post_id, "agb_2_accepted_by_user_timestamp", sanitize_text_field( $agb_2_accepted_by_user_timestamp ) );

		// Objekt ID
		$object_id = isset( $_POST['f12d_object_id'] ) ? $_POST['f12d_object_id'] : "";
		update_post_meta( $post_id, "f12d_object_id", sanitize_text_field( $object_id ) );

		// ip address used for download
		$ip = $_POST["ip"];
		update_post_meta( $post_id, "ip", sanitize_text_field( $ip ) );

	}
}

add_action( 'save_post', 'f12d_generic_hook_save_post' );