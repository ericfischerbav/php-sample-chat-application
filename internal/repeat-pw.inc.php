<div class="form-group">
	<label for="repeat-pw">Passwort wiederholen:</label>
	<?php 
	if ($repeat_pw_missing) {
	    $text = "Bitte Passwort wiederholen.";
	    create_error_message($text);
	}
	?>
	<input type="password" class="form-control" name="repeat-pw" id="repeat-pw" <?php if(isset($_POST["repeat-pw"])) {echo 'value="'.$_POST["repeat-pw"].'"';} ?>/>
</div>