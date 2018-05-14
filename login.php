<?php
include "internal/properties.inc.php";

$title = "";
$action = "";
$username_missing = false;
$password_missing = false;
$repeat_pw_missing = false;
$pw_not_equal = false;
$unknown_error_occurred = false;

// Abhängig davon, ob die Seite zur Anmeldung oder Registierung
// dienen soll, wird der entsprechende Titel gesetzt.
if (! isset($_GET["register"])) {
    $title = "Login";
    $action = "login.php";
} else {
    $title = "Registrieren";
    $action = "login.php?register";
}

// Bedingung dafür, dass sich ein Benutzer einloggen will.
// In diesem Fall ist keine Passwort-Wiederholung gesetzt.
if (isset($_POST["username"]) and isset($_POST["password"]) and ! isset($_POST["repeat-pw"])) {
    
    // Registrierung
} elseif (isset($_POST["username"]) and isset($_POST["password"]) and isset($_POST["repeat-pw"])) {
    /*
     * Prüfe, ob alle Felder gesetzt sind.
     */
    if ($_POST["username"] == "") {
        $username_missing = true;
    }
    
    if ($_POST["password"] == "") {
        $password_missing = true;
    }
    
    if ($_POST["repeat-pw"] == "") {
        $repeat_pw_missing = true;
    }
    
    if (! $username_missing and ! $password_missing and ! $repeat_pw_missing) {
        if ($_POST["password"] != $_POST["repeat-pw"]) {
            $pw_not_equal = true;
        } else {
            /*
             * Wenn alle Prüfungen erfolgreich sind,
             * dann bauen wir eine Datenbankverbindung auf
             * und legen den neuen Nutzer ab.
             */
            $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
            mysqli_select_db($connection, DB_NAME);
            $sql = "INSERT INTO benutzer (name, password) VALUES ('" . $_POST["username"] . "', '" . md5($_POST["password"]) . "')";
            $success = mysqli_query($connection, $sql);
            /*
             * Gehe zum login zurück, damit sich der neu registrierte
             * Benutzer das erste mal einloggen kann.
             */
            if ($success != false) {
                header("Location: login.php?register-success");
                exit();
            } else {
                $unknown_error_occurred = true;
            }
        }
    }
}

function create_error_message($text)
{
    $type = "danger";
    include "internal/message.inc.php";
}

function create_success_message($text) {
    $type = "success";
    include "internal/message.inc.php";
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
		<?php
		if ($unknown_error_occurred) {
		    create_error_message("Es ist ein unbekannter Fehler aufgetreten. Der Prozess konnte nicht abgeschlossen werden.");
		}
		if (isset($_GET["register-success"])) {
		    create_success_message("Registrierung erfolgreich. Bitte logge dich ein.");
		}
		?>
		<form <?php echo 'action="'.$action.'"'; ?> method="post">
			<div class="form-group">
				<label for="username">Benutzername:</label>
				<?php
    if ($username_missing) {
        $text = "Bitte gib deinen Benutzernamen ein.";
        create_error_message($text);
    }
    ?> 
    <input type="text" class="form-control" id="username"
					name="username"
					<?php if(isset($_POST["username"])) {echo 'value="'.$_POST["username"].'"';} ?> />
			</div>
			<div class="form-group">
				<label for="password">Passwort:</label>
				<?php
    if ($password_missing) {
        $text = "Bitte gib ein Passwort ein.";
        create_error_message($text);
    }
    ?>
     <input type="password" class="form-control" id="password"
					name="password" <?php if(isset($_POST["password"])) {echo 'value="'.$_POST["password"].'"';} ?> />
			</div>
				<?php
    // Nur wenn sich der Benutzer registrieren will, wird ein Feld zur
    // Passwort-Wiederholung angezeigt.
    if (isset($_GET["register"])) {
        include "internal/repeat-pw.inc.php";
    }
    ?>
				<input type="submit"
				<?php echo 'value="'.$title.'" '; echo 'name="'.$title.'" '; ?>
				class="btn btn-primary" />
		</form>
			<?php
// Falls das Login-Form angezeigt wird, so wird ein Link
// zur Registrierung angezeigt.
if (! isset($_GET["register"])) {
    echo '<p>Noch kein Account? <a href="login.php?register">Jetzt registrieren</a>.';
}
?>
		</div>

	<script type="text/javascript" src="js/bootstrap.min.js"></script>
</body>

</html>