<div class="meta-page f12-page-settings">
    <h1>
		<?php echo __( 'F12 Download Einstellungen', "f12-download" ); ?>
    </h1>

    <div class="f12-notice success" <?php if ( $args["update_status"] != 1 ) {
		echo "style=\"display:none;\"";
	} ?>>
		<?php echo __( 'Einstellungen gespeichert.', "f12-download" ); ?>
    </div>

    <form action="" method="post" name="f12d_settings_update" id="f12d_settings_update">
		<?php echo $args["nonce"] ?>
        <input type="hidden" name="action" value="f12d_settings_save"/>
        <div class="f12-panel">
            <div class="f12-panel__header">
                <h2>
					<?php echo __( 'Allgemeine Einstellungen', "f12-download" ); ?>
                </h2>
            </div>
            <div class="f12-panel__content">
                <table class="f12-table">
                    <tr>
                        <td class="label" style="width:300px;">
                            <label>
								<?php echo __( 'Maximale Größe der Dateien (in Bytes):', "f12-download" ); ?>
                            </label>
                            <p>
								<?php echo __( 'Gibt die maximale Größe für die Downloads an.', "f12-download" ); ?>
                            </p>
                        </td>
                        <td>
                            <input type="text" name="max_file_size" class="f12-form-validate"
                                   validation='{"validation":{"required":true}}'
                                   value="<?php echo $args["max_file_size"]; ?>"/>
                            <p>Standard: 5120000 (5MB)</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="label" style="width:300px;">
                            <label>
								<?php echo __( 'Erlaubte Dateiendungen:', "f12-download" ); ?>
                            </label>
                            <p>
								<?php echo __( 'Alle erlaubten Dateiformate durch "," getrennt.', "f12-download" ); ?>
                            </p>
                        </td>
                        <td>
                            <input type="text" name="allowed_extensions" class="f12-form-validate"
                                   validation='{"validation":{"required":true}}'
                                   value="<?php echo $args["allowed_extensions"]; ?>"/>
                            <p>Standard: pdf,rar</p>
                        </td>
                    </tr>
                    <tr>
                        <td class="label" style="width:300px;">
                            <label>
								<?php echo __( 'Alte Dateien beim verändern löschen?', "f12-download" ); ?>
                            </label>
                            <p>
								<?php echo __( 'Wenn aktiviert, werden mit dem Datensatz verknüpfte Dateien gelöscht.', "f12-download" ); ?>
                            </p>
                        </td>
                        <td>
                            <input type="radio" name="delete_files_onchange"
                                   value="1" <?php if ( $args["delete_files_onchange"] == 1 ) {
								echo "checked";
							} ?> /> <?php echo __( 'Ja', "f12-download" ); ?><br><br>
                            <input type="radio" name="delete_files_onchange"
                                   value="0" <?php if ( $args["delete_files_onchange"] == 0 ) {
								echo "checked";
							} ?>/> <?php echo __( 'Nein', "f12-download" ); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label" style="width:300px;">
                            <label>
								<?php echo __( 'Download Seite', "f12-download" ); ?>
                            </label>
                            <p>
								<?php echo __( 'Diese Seite wird an den Besucher (E-Mail) gesendet um den Download der Datei zu starten.', "f12-download" ); ?>
                            </p>
                        </td>
                        <td>
                            <select name="download_page" class="f12-form-validate"
                                    validation='{"validation":{"required":true}}'>
								<?php echo $args["download_page"]; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="label" style="width:300px;">
                            <label>
								<?php echo __( 'Widerrufsbelehrung', "f12-download" ); ?>
                            </label>
                            <p>
								<?php echo __( 'Wenn für den Download die Widerrufsbelehrung aktiviert wurde, wird diese Seite vor dem Download aufgerufen.', "f12-download" ); ?>
                            </p>
                            <p>
								<?php echo __( 'Auf dieser Seite muss der Shortcode [f12-download-agb] hinterlegt werden, um das Formular mit den Checkboxen einzubinden.', "f12-download" ); ?>
                            </p>
                        </td>
                        <td>
                            <select name="download_page_agb" class="f12-form-validate"
                                    validation='{"validation":{"required":true}}'>
								<?php echo $args["download_page_agb"]; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="label" style="width:300px;">
                            <label>
								<?php echo __( 'Widerrufsbelehrung Text', "f12-download" ); ?>
                            </label>
                            <p>
								<?php echo __( 'Geben Sie den Text für die Widerrufsbelehrung ein.', "f12-download" ); ?>
                            </p>
                            <p>
                                <strong><?php echo __( 'Platzhalter:', 'f12-download' ); ?></strong>
                            </p>
                            <p>
								{firstname}
                            </p>
                            <p>
                                {lastname}
                            </p>
                            <p>
                                {email}
                            </p>
                            <p>
                                {date}
                            </p>
                            <p>
                                {object-id}
                            </p>
                        </td>
                        <td>
							<?php echo wp_editor( $args["download_page_agb_text"], "download_page_agb_text" ); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label" style="width:300px;">
                            <label>
								<?php echo __( 'Widerrufsbelehrung Checkbox 1', "f12-download" ); ?>
                            </label>
                            <p>
								<?php echo __( 'Geben Sie den Text ein der vor der Checkbox 1 angezeigt werden soll.', "f12-download" ); ?>
                            </p>
                        </td>
                        <td>
							<?php echo wp_editor( $args["download_page_agb_checkbox_1"], "download_page_agb_checkbox_1" ); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label" style="width:300px;">
                            <label>
								<?php echo __( 'Widerrufsbelehrung Checkbox 2', "f12-download" ); ?>
                            </label>
                            <p>
								<?php echo __( 'Geben Sie den Text ein der vor der Checkbox 2 angezeigt werden soll.', "f12-download" ); ?>
                            </p>
                        </td>
                        <td>
							<?php echo wp_editor( $args["download_page_agb_checkbox_2"], "download_page_agb_checkbox_2" ); ?>
                        </td>
                    </tr>
                    <tr>
                        <td class="label" style="width:300px;">
                            <label>
								<?php echo __( 'Seite nach Link senden', "f12-download" ); ?>
                            </label>
                            <p>
								<?php echo __( 'Diese Seite wird aufgerufen, nachdem der Link zu der Datei versendet wurde.', "f12-download" ); ?>
                            </p>
                        </td>
                        <td>
                            <select name="email_page_send" class="f12-form-validate"
                                    validation='{"validation":{"required":true}}'>
								<?php echo $args["email_page_send"]; ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td class="label" style="width:300px;">
                            <label>
								<?php echo __( 'E-Mail', "f12-download" ); ?>
                            </label>
                            <p>
								<?php echo __( 'Diese E-Mail wird verwendet um die E-Mail mit den Links zu versenden.', "f12-download" ); ?>
                            </p>
                        </td>
                        <td>
                            <input type="text" name="email" class="f12-form-validate"
                                   validation='{"validation":{"required":true}}'
                                   value="<?php echo $args["email"]; ?>"/>
                        </td>
                    </tr>
                    <tr>
                        <td class="label" style="width:300px;">
                            <label>
								<?php echo __( 'Post Types', "f12-download" ); ?>
                            </label>
                            <p>
								<?php echo __( 'Auf diesen Seiten wird eine Meta-Box eingefügt die es vereinfacht, Downloads hinzuzufügen.', "f12-download" ); ?>
                            </p>
                        </td>
                        <td>
							<?php
							echo $args["post_types"];
							?>
                        </td>
                    </tr>
                </table>
            </div>
            <br>
            <input type="submit" name="update" value="<?php echo __( 'Speichern', "f12-download" ); ?>"/>
        </div>
    </form>

    <form action="" method="post" name="f12d_settings_update_storage">
		<?php echo wp_nonce_field( "f12d-storage-update" ); ?>
        <div class="f12-panel">
            <div class="f12-panel__header">
				<?php echo __( 'Speicherort', "f12-download" ); ?>
            </div>
            <div class="f12-panel__content">
                <table class="f12-table">
                    <tr>
                        <td class="label" style="width:300px;">
                            <label>
								<?php echo __( 'Speicherort für Dateien:', "f12-download" ); ?>
                            </label>
                            <p>
								<?php echo __( 'Hier werden die Dateien gespeichert.', "f12-download" ); ?>
                            </p>
                        </td>
                        <td>
                            <input type="text" name="storage" readonly
                                   value="<?php echo $args["storage"]; ?>"/>
                            <input type="submit" name="update-storage"
                                   value="<?php echo __( 'Überprüfen', "f12-download" ); ?>"/>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("form#f12d_settings_update").F12FormValidate();
    });
</script>