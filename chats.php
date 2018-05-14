<?php 

session_start();
if (!isset($_SESSION["user"])) {
    header("Location: login.php");
    exit();
}

?>

<html>

	<head>
		<title>Chat-&Uuml;bersicht</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
	</head>
	
	<body>
		<div class="container">
			<h1>Chat-&Uuml;bersicht</h1>
			<p><a href="start-chat.php" class="btn btn-primary">Neuen Chat starten</a> <a href="logout.php" class="btn btn-secondary">Logout</a></p>
		</div>
		
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
	</body>

</html>