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
$error_inserting = true;
if(isset($_POST["start-chat"])) {
    $users = $_POST["users"];
    $chat_id = generate_chat_id();
    foreach ($users as $user) {
        $sql = "INSERT INTO nimmtteil (chat, benutzer) VALUES ('".$user."', ".$chat_id.")";
        $db_result = mysqli_query($connection, $sql);
        if($db_result == false) {
            $error_inserting = true;
            /*
             * Alle Einträge löschen, die bisher geschrieben wurden.
             * Das ist nur notwendig, da MySQL im Auto-Commit-Modus
             * läuft. Wäre dieser deaktiviert, würde ein einfaches
             * Rollback reichen.
             */
            
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

mysqli_close($connection);

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
	</div>

	<script type="text/javascript" src="js/bootstrap.min.js"></script>
</body>

</html>