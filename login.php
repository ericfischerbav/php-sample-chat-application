<?php
	$title = "";
	// Abhängig davon, ob die Seite zur Anmeldung oder Registierung
	// dienen soll, wird der entsprechende Titel gesetzt.
	if(!isset($_GET["register"])) {
		$title = "Login";
	} else {
		$title = "Registrieren";
	}
	
	// Bedingung dafür, dass sich ein Benutzer einloggen will.
	// In diesem Fall ist keine Passwort-Wiederholung gesetzt.
	if(isset($_POST["username"]) and isset($_POST["password"]) and !isset($_POST["repeat-pw"])) {
		
	// Registrierung
	} elseif(isset($_POST["username"]) and isset($_POST["password"]) and isset($_POST["repeat-pw"])) {
		
	}
?>

<html>
	
	<head>	
		<title><?php echo $title; ?></title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
	</head>
	
	<body>
		<div class="container">
			<h1><?php echo $title; ?></h1>
			<form action="login.php" method="post">
				<div class="form-group">
					<label for="username">Benutzername:</label>
					<input type="text" class="form-control" id="username" name="username" />
				</div>
				<div class="form-group">
					<label for="password">Passwort:</label>
					<input type="password" class="form-control" id="username" name="username" />
				</div>
				<?php
					// Nur wenn sich der Benutzer registrieren will, wird ein Feld zur
					// Passwort-Wiederholung angezeigt.
					if(isset($_GET["register"])) {
						include "internal/repeat-pw.inc.php";
					}
				?>
				<input type="submit" <?php echo 'value="'.$title.'"'; ?> class="btn btn-primary" />
			</form>
			<?php
				// Falls das Login-Form angezeigt wird, so wird ein Link
				// zur Registrierung angezeigt.
				if(!isset($_GET["register"])) {
					echo '<p>Noch kein Account? <a href="login.php?register">Jetzt registrieren</a>.';
				}
			?>
		</div>
		
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
	</body>
	
</html>