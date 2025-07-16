<?php echo $args["error"]; ?>
<form action="" method="post" name="f12d-agb-form" style="text-align:left;">
	<?php echo $args["nonce"]; ?>

	<?php echo $args["content"]; ?>

	<?php echo $args["checkbox_1_text"]; ?>
    <p>
        <input type="checkbox" name="agb_accepted_by_user"
               value="1"/> <?php echo __( "AGB akzeptiert", "f12-download" ); ?>
    </p>

	<?php echo $args["checkbox_2_text"]; ?>

    <p>
        <input type="checkbox" name="agb_2_accepted_by_user"
               value="1"/> <?php echo __( "AGB 2 akzeptiert", "f12-download" ); ?>
    </p>

    <p>
        <input type="submit" name="send" value="<?php echo __( 'Herunterladen', "f12-download" ); ?>"
               style="margin:0 auto; margin-top:20px;"/>
    </p>
</form>