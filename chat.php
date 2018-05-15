<?php
session_start();
if (! isset($_SESSION["user"])) {
    header("Location: login.php?not-authorized");
    exit();
}

/*
 * Falls ein Chat weder gestartet noch angezeigt werden soll,
 * dann soll der Benutzer zur Übersicht zurück geleitet werden. 
 */
if(!isset($_POST["start-chat"]) or !isset($_POST["users"])) {
    header("Location: chats.php");
    exit();
}

include "internal/properties.inc.php";

// DB-Verbindung öffnen
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysqli_select_db($connection, DB_NAME);

/*
 * Erstelle den Chat an der Datenbank.
 * Das soll allerdings nur gemacht werden, wenn auch
 * das "start-chat"-Flag gesetzt ist.
 */
$error_inserting = false;
if(isset($_POST["start-chat"])) {
    $chat_id = generate_chat_id();
    var_dump($_POST["users"]);
    foreach ($_POST["users"] as $user) {
        $sql = "INSERT INTO nimmtteil (benutzer, chat) VALUES ('".$user."', ".$chat_id.")";
        echo $sql;
        $db_result = mysqli_query($connection, $sql);
        var_dump($db_result);
        if($db_result == false) {
            $error_inserting = true;
            /*
             * Alle Einträge löschen, die bisher geschrieben wurden.
             * Das ist nur notwendig, da MySQL im Auto-Commit-Modus
             * läuft. Wäre dieser deaktiviert, würde ein einfaches
             * Rollback reichen.
             */
            $delete_chat_sql = "DELETE FROM nimmtteil WHERE chat = ".$chat_id;
            echo $delete_chat_sql;
            mysqli_query($connection, $delete_chat_sql);
            break;
        }
    }
}

/**
 * Creates the title that fits to the state of the page.
 */
function create_title()
{
    if (isset($_POST["start-chat"]))
        return "Chat starten";
    else 
        return "Chat";
}

function generate_chat_id() {
    global $connection;
    
    $id = rand(0, 2147483646);
    
    $check_sql = "SELECT * FROM nimmtteil WHERE chat = ".$id;
    $db_result = mysqli_query($connection, $check_sql);
    if (mysqli_fetch_assoc($db_result)) {
        return generate_chat_id();
    } else {
        return $id;
    }
}

?>

<html>

<head>
<title><?php echo create_title(); ?></title>
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
</head>

<body>
	<div class="container">
		<div class="row mt-2">
			<div class="col">
				<h1><?php echo create_title(); ?></h1>
			</div>
			<div class="col-sm">
				<p class="text-right">
					<a href="chats.php" class="btn btn-primary">Zur &Uuml;bersicht</a>
					<a href="logout.php" class="btn btn-danger">Logout</a>
				</p>
			</div>
		</div>
		<div class="row">
			<?php 
			if ($error_inserting) {
			    $text = "Es gab einen Fehler beim erstellen des Chats. Bitte gehe zur&uuml;ck zur &Uuml;bersicht.";
			    $type = "danger";
			    include "internal/message.inc.php";
			} else {
			    /*
			     * Suche alle Nachrichten zum entsprechenden Chat in der Datenbank.
			     */
			    $sql = "SELECT id FROM nachricht WHERE chat = ".$chat_id;
			    $db_result = mysqli_query($connection, $sql);
			    $messages_ids = array();
			    while ($row = mysqli_fetch_assoc($db_result)) {
			        $messages_ids[] = $row["id"];
			    }
			    include "internal/chat-container.inc.php";
			}
			?>
		</div>
	</div>

	<script type="text/javascript" src="js/bootstrap.min.js"></script>
</body>

</html>

<?php mysqli_close($connection); ?>