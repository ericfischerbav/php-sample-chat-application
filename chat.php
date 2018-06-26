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
if (! (isset($_POST["start-chat"]) and isset($_POST["users"]) or isset($_GET["id"]) or isset($_POST["message"]) and isset($_POST["chat-id"]))) {
    header("Location: chats.php");
    exit();
}

include "internal/properties.inc.php";
include "internal/classes/message.class.php";

// DB-Verbindung öffnen
$connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
mysqli_select_db($connection, DB_NAME);

/*
 * Erstelle den Chat an der Datenbank.
 * Das soll allerdings nur gemacht werden, wenn auch
 * das "start-chat"-Flag gesetzt ist.
 */
$error_inserting = false;
if (isset($_POST["start-chat"])) {
    
    if (! are_users_already_in_chat($_POST["users"])) {
        
        $chat_id = generate_chat_id();
        /*
         * Füge zuerst einen Eintrag mit dem aktuell aktiven Benutzer
         * in die Datenbank ein.
         */
        $sql = "INSERT INTO nimmtteil (benutzer, chat) VALUES ('" . $_SESSION["user"] . "', " . $chat_id . ")";
        $db_result = mysqli_query($connection, $sql);
        foreach ($_POST["users"] as $user) {
            $sql = "INSERT INTO nimmtteil (benutzer, chat) VALUES ('" . $user . "', " . $chat_id . ")";
            $db_result = mysqli_query($connection, $sql);
            if ($db_result == false) {
                $error_inserting = true;
                /*
                 * Alle Einträge löschen, die bisher geschrieben wurden.
                 * Das ist nur notwendig, da MySQL im Auto-Commit-Modus
                 * läuft. Wäre dieser deaktiviert, würde ein einfaches
                 * Rollback reichen.
                 */
                $delete_chat_sql = "DELETE FROM nimmtteil WHERE chat = " . $chat_id;
                echo $delete_chat_sql;
                mysqli_query($connection, $delete_chat_sql);
                break;
            }
        }
    } else {
        header("Location: chat.php?id=" . get_existing_chat($_POST["users"]));
        exit();
    }
} elseif (isset($_POST["message"]) and isset($_POST["chat-id"])) {
    $chat_id = $_POST["chat-id"];
    
    $message_id = generate_message_id();
    
    $message_text = $_POST["message"];
    $message_sender = $_SESSION["user"];
    $message_chat = $chat_id;
    $message_time = date("Y.m.d H:i:s", time());
    
    if ($message_text != "") {
        $sql = "INSERT INTO nachricht (id, sender, zeit, chat, text) VALUES (" . $message_id . ", '" . $message_sender . "', '" . $message_time . "', " . $chat_id . ", '" . $message_text . "')";
        $db_result = mysqli_query($connection, $sql);
        if ($db_result == false) {
            $error_inserting = true;
        }
    }
} elseif (isset($_GET["id"])) {
    $chat_id = $_GET["id"];
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

function generate_chat_id()
{
    global $connection;
    
    $id = rand(0, 2147483646);
    
    $check_sql = "SELECT * FROM nimmtteil WHERE chat = " . $id;
    $db_result = mysqli_query($connection, $check_sql);
    if (mysqli_fetch_assoc($db_result)) {
        return generate_chat_id();
    } else {
        return $id;
    }
}

function generate_message_id()
{
    global $connection;
    
    $id = rand(0, 2147483646);
    
    $check_sql = "SELECT * FROM nachricht WHERE id = " . $id;
    $db_result = mysqli_query($connection, $check_sql);
    if (mysqli_fetch_assoc($db_result)) {
        return generate_message_id();
    } else {
        return $id;
    }
}

function fetch_messages($chat_id)
{
    global $connection;
    
    $sql = "SELECT * FROM nachricht WHERE chat = " . $chat_id . " ORDER BY zeit ASC";
    $db_result = mysqli_query($connection, $sql);
    
    $messages = array();
    
    while ($row = mysqli_fetch_assoc($db_result)) {
        $message = new Message($row["id"]);
        $message->set_read($_SESSION["user"]);
        $messages[] = $message;
    }
    
    return $messages;
}

function are_users_already_in_chat($users)
{
    // Überprüfe hier, ob eine Chat-ID gefunden wurde. Wenn nicht, existiert kein Chat.
    return get_existing_chat($users) != 0;
}

/**
 * Fetches the chat number of the chat of the given users.
 *
 * @param array $users
 * @return number
 */
function get_existing_chat($users)
{
    global $connection;
    
    // Dieser SQL sucht alle Chats, in denen alle gegebenen Benutzer teilnehmen
    // Achtung: auch der angemeldete Benutzer muss hier geprüft werden
    $sql = "SELECT DISTINCT chat FROM nimmtteil WHERE chat IN (SELECT chat FROM nimmtteil WHERE benutzer = '" . $_SESSION["user"] . "')";
    
    foreach ($users as $user) {
        $sql .= " AND chat IN (SELECT chat FROM nimmtteil WHERE benutzer = '" . $user . "')";
    }
    
    $result = mysqli_query($connection, $sql);
    
    $chat_id = 0;
    
    while ($row = mysqli_fetch_assoc($result)) {
        $check_sql = "SELECT count(*) as count FROM nimmtteil WHERE chat = " . $row["chat"];
        $check_result = mysqli_query($connection, $check_sql);
        $check_row = mysqli_fetch_assoc($check_result);
        /*
         * Es kann vorkommen, dass alle Benutzer in einem Chat sind, gleichteig aber auch noch andere Benutzer
         * am Chat teilnehmen. Diese dürfen wir selbstverständlich nicht berücksichtigen.
         */
        if (count($users) + 1 == $check_row["count"]) {
            $chat_id = $row["chat"];
        }
    }
    return $chat_id;
}

?>

<html>

<head>
<title><?php echo create_title(); ?></title>
<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
<style type="text/css">
.chat-row {
	max-height: 60%;
	overflow-y: scroll;
}
</style>
</head>

<body>
	<div class="container">
		<div class="row mt-2">
			<div class="col">
				<h1><?php echo create_title(); ?></h1>
			</div>
			<div class="col-sm">
				<p class="text-right">
					<a href=<?php echo '"chat.php?id='.$chat_id.'"'?>
						class="btn btn-primary">Aktualisieren</a> <a href="chats.php"
						class="btn btn-primary">Zur &Uuml;bersicht</a> <a
						href="logout.php" class="btn btn-danger">Logout</a>
				</p>
			</div>
		</div>
		<div class="row chat-row">
			<?php
if ($error_inserting) {
    $text = "Es gab einen Fehler beim erstellen des Chats. Bitte gehe zur&uuml;ck zur &Uuml;bersicht.";
    $type = "danger";
    include "internal/message.inc.php";
} else {
    /*
     * Suche alle Nachrichten zum entsprechenden Chat in der Datenbank.
     */
    $messages = fetch_messages($chat_id);
    include "internal/chat-container.inc.php";
}
?>
		</div>
		<div class="row">
			<div class="col text-right">
				<form action=<?php echo '"chat.php?id='.$chat_id.'"';?>
					method="post">
					<div class="form-group">
						<textarea class="form-control" name="message" rows="5" cols="50"
							maxlenth="600"></textarea>
						<input type="hidden" name="chat-id"
							value=<?php echo '"'.$chat_id.'"'; ?> />
					</div>
					<input type="submit" value="Senden" class="btn btn-primary" />
				</form>
			</div>
		</div>
	</div>

	<script type="text/javascript" src="js/bootstrap.min.js"></script>
</body>

</html>

<?php mysqli_close($connection); ?>