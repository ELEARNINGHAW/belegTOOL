<?php
session_start();
$role = $_SESSION["r"];
 
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<title>Navi</title>
<style type="text/css">
<!--
body
{
	margin-left:20%;
}

#menu2 a.reiter:link, #menu2 a.reiter:visited 
{
	color: #fff;
	text-align: center;
	font-weight: bold;
	border-bottom: none;
	background: url(img/nav_lightblue.png) 0px 0px repeat-x; 
}

#menu2 a.reiter:hover, #menu2 a.reiter:active 
{
	color: #aaa;
	background: url(img/nav_lightblue.png) 0px -33px repeat-x; 
}


.reiter
 {
	font-family: Arial, Helvetica, sans-serif;
	font-size: 14px;
	background-color: #DFEFFF;
	margin: 5px;
	padding: 5px;
	height: 25px;
	border: 1px solid #000000;
	text-decoration: none;
	font-weight: 600;	
}
-->
</style>
</head>

<body>

<div id="menu2">
<?php 
if ($role >= 3 )
	echo "<a class=\"reiter\"  href=\"belegliste.php\" 				title=\"Wunschbelegungsliste\" 	target=\"mainFrame\">Belegungsliste</a>";
if ($role >= 4 )
	echo "<a class=\"reiter\"  href=\"studiverwaltung.php\"	 		title=\"Studiverwaltung\" 		target=\"mainFrame\">Studiverwaltung</a>";
if ($role >= 5 )
	echo "<a class=\"reiter\"  href=\"vorlesungsverzeichnis.php\" 	title=\"Vorlesungsverzeichnis\" 	target=\"mainFrame\">Vorlesungsverzeichnis</a>";
if ($role >= 5 )
	echo "<a class=\"reiter\"  href=\"basistables/tableindex.html\" title=\"TableIndex\" 				target=\"mainFrame\">Basis Tabellen</a>";

//	echo "<a class=\"reiter\"  href=\"index.php?ds=1\" title=\"Logout\" 				target=\"_parent\">X</a>";

?>
</div>
</body>
</html>
