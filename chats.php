<?php

// Überprüfen, ob der Benutzer eingeloggt ist.
session_start();
if (! isset($_SESSION["user"])) {
    header("Location: login.php?not-authorized");
    exit();
}

include "internal/properties.inc.php";

// DB-Verbindung öffnen
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysqli_select_db($connection, DB_NAME);

/**
 * Diese Funktion selektiert alle Benutzer aus der Datenbank, mit denen Kontakt aufgenommen werden kann.
 *
 * @return Array mit allen Benutzern, die ausgewählt werden dürfen
 */
function select_users()
{
    global $connection;
    
    // Benutzer selektieren
    $sql = "SELECT name FROM benutzer WHERE NOT name = '" . $_SESSION["user"] . "'";
    $db_result = mysqli_query($connection, $sql);
    
    // Benutzer in Array speichern
    $users = array();
    while ($row = mysqli_fetch_assoc($db_result)) {
        $users[] = $row["name"];
    }
    
    return $users;
}

/**
 * Diese Funktion liest aus der Datenbank alle aktiven Chats des angemeldeten Benutzers.
 */
function fetch_chats()
{
    global $connection;
    
    // Hole Teilnehmer der aktiven Chats
    $sql = "SELECT DISTINCT chat FROM nimmtteil nt1 WHERE chat IN (SELECT chat FROM nimmtteil nt2 WHERE nt2.benutzer = '" . $_SESSION["user"] . "') AND NOT nt1.benutzer = '" . $_SESSION["user"] . "'";
    $db_result = mysqli_query($connection, $sql);
    
    $chats = array();
    
    while ($row = mysqli_fetch_assoc($db_result)) {
        $chats[] = $row["chat"];
    }
    
    return $chats;
}

function fetch_users_in_chat($chat)
{
    global $connection;
    
    $sql = "SELECT * FROM nimmtteil WHERE chat = '" . $chat . "'";
    $db_result = mysqli_query($connection, $sql);
    
    $users = array();
    
    while ($row = mysqli_fetch_assoc($db_result)) {
        $users[] = $row["benutzer"];
    }
    
    return $users;
}

?>

<html>

<head>
<title>Chat-&Uuml;bersicht</title>
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
</head>

<body>
	<div class="container">
		<div class="row mt-2">
			<div class="col">
				<h1>Chat-&Uuml;bersicht</h1>
			</div>
			<div class="col-sm">
				<p class="text-right">
					<a href="logout.php" class="btn btn-danger">Logout</a>
				</p>
			</div>
		</div>

		<div class="row">
			<div class="container-fluid col">
			<?php
$chats_found = false;
foreach (fetch_chats() as $chat) {
    $chats_found = true;
    $users = fetch_users_in_chat($chat);
    echo '<div class="row"><div class="col-lg"><a href="chat.php?id='.$chat.'">';
    echo $users[0];
    for ($i = 1; $i < count($users); $i++) {
        echo ', '.$users[$i];
    }
    echo '</a></div></div>';
}
?>
			</div>
		</div>

		<div class="row">
			<div class="col">
				<strong>Chat hinzufügen</strong>
				<form action="find-user.php" method="get">
					<div class="form-group">
						<label for="user">Bitte einzelnen Benutzer ausw&auml;hlen:</label>
						<input type="text" name="user" class="form-control" />
					</div>
					<input type="submit" class="btn btn-primary"
						value="Benutzer suchen" />
				</form>
			</div>
		</div>
	</div>

	<script type="text/javascript" src="js/bootstrap.min.js"></script>
</body>

</html>