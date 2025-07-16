<form name='f12-download-form' action="" method="POST">
    <div class="bfi-ce-form">
	<?php echo $args["f12_mail_nonce"]; ?>
        <input type="hidden" name="action" value="f12d_download_form" />
	<input type='text' name='f12d_download_email' value='' placeholder='E-Mail'>
	<input type='submit' name='send' value='<?php echo __('Download anfordern', "f12-download");?>'/>
	<input type='hidden' name='f12d_download_id' value="<?php echo $args["f12_download_id"]; ?>"/>
    </div>
</form>