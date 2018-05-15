<form action="chat.php" method="get">
<div class="form-group form-check">
	<input type="hidden" name="start-chat" />
	<?php 
	$user_found = false;
	foreach($users as $user) {
	    $user_found = true;
	    echo '<input type="checkbox" name="users" value="'.$user.'" id="'.$user.'" class="form-check-input" />';
	    echo '<label for="'.$user.'" class="form-check-label">'.$user.'</label>';
	    echo '<br/>';
	}
	if (!$user_found) {
	   echo '<div class="alert alert-danger">Keine Benutzer zu dieser Suche gefunden.</div>';
	}
	?>
</div>
<input type="submit" value="Chat starten" class="btn btn-primary" />
</form>