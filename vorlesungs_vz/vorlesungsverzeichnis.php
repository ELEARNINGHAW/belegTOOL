<?php 
session_start();
#include ("checkreferer.php");

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta http-equiv="cache-control" content="no-cache">
<meta http-equiv="pragma" content="no-cache">
<link	rel="stylesheet" type="text/css" href="lib/style.css" />
<script	src="lib/lib.js" type="text/javascript"></script>
<title>Vorlesungs VZ</title>
</head>
<body>
<?php



if (isset($_SESSION["r"]))
{
  $role = $_SESSION["r"];

  require_once( "../inc/vz.db.class.php" );
  require_once( "../inc/vz.render.class.php" );

  $render 			= new Render;
  $DB 			  	= new DB;
  if (isset($_GET[ 'a' ]))
    
  {    
    $action 			= $_GET[ 'a' ]; //----------  Datenbank update ---------- 
		$DB->setDB( $action );
	}
  //echo "<br>". $action;
	$lists			 = $DB->getAllLists();									//- Assoc Array mit allen Professoren, Studiengänen, Veranstaltungen
	$vl_verzeichnis 	 = $DB->getVorlesungsVerzeichnis( );						// -- Struktur und Werte der DB ausgeben ----------
  
  $html  		 = "" ;
	$html 			.= "<h1>Vorlesungs VZ</h1>";
	$html 			.= "\n\r<form name='vlvz' action='#'>";
	$html 			.= $render->printVorlesungsverzeichnisAuswahl($vl_verzeichnis, $lists, $role );
	echo $html; 														// -- Auswahlmatrix mit dem gesamten Vorlesungsverzeichnis ausgeben
}
else
{
	die("ERROR");
}




function deb( $dmp, $stop = 0 )
{
  echo "<pre>";
  print_r($dmp);
  echo "</pre>";
if ($stop){ die();}
}  


?>
<form  name="param" action="#">
<input name="a" 	  type="hidden" value="update" >
<input name="col" 	type="hidden" >
<input name="val" 	type="hidden" >
<input name="id"  	type="hidden" >
<input name="sum" 	type="hidden" >
</form>
</body>
</html>