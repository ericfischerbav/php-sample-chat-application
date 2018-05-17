<?php 

session_start();
if(!isset($_SESSION["user"])) {
    header("Location: login.php?not-authorized");
    exit();
}

include "internal/properties.inc.php";

$display_search_field = false;
$page_title = "";

if (isset($_GET["user"])) {
    $page_title = "Benutzer ausw&auml;hlen und zu Chat hinzuf&uuml;gen";
    
    // DB-Verbindung aufbauen
    $connection = mysqli_connect(DB_HOST, DB_USER, DB_PASSWORD);
    mysqli_select_db($connection, DB_NAME);
    
    // Suche in den Benutzern mit dem Suchquery
    $sql = "SELECT name FROM benutzer WHERE name LIKE '%".$_GET["user"]."%' OR name = '".$_GET["user"]."' AND NOT name = '".$_SESSION["user"]."'";
    $db_result = mysqli_query($connection, $sql);
    
    $users = array();
    while ($row = mysqli_fetch_assoc($db_result)) {
        $users[] = $row["name"];
    }
    
} else {
    $display_search_field = true;
    $page_title = "Benutzer suchen";
}

?>

<html>

	<head>
		<title><?php echo $page_title; ?></title>
		<link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
	</head>
	
	<body>
		<div class="container">
			<div class="row mt-2">
				<div class="col">
					<h1><?php echo $page_title; ?></h1>
				</div>
				<div class="col-sm">
					<p class="text-right">
						<a href="chats.php" class="btn btn-primary">Zur &Uuml;bersicht</a>
						<a href="logout.php" class="btn btn-danger">Logout</a>
					</p>
				</div>
			</div>
			<?php 
			if ($display_search_field) {
			    include "internal/searchfield.inc.php";
			} elseif (isset($users)) {
			    if(!empty($users))
			     include "internal/user-result-list.inc.php";
			    else {
			         echo '<div class="alert alert-danger">Keine Benutzer zu dieser Suche gefunden</div>';
			         include 'internal/searchfield.inc.php';
			     }
			}
			?>
		</div>
	
		<script type="text/javascript" src="js/bootstrap.min.js"></script>
	</body>

</html>