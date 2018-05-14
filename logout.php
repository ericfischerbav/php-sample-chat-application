<?php 
session_start();
session_destroy();
?>

<html>

	<head>
		<title>Logout</title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
	</head>
	
	<body>
		<div class="container">
			<h1>Logout</h1>
			<div class="alert alert-success" role="alert">
				Sie wurden erfolgreich ausgeloggt.
			</div>
			<p><a href="index.php">Zur Startseite</a></p>
		</div>
	
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
	</body>

</html>