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