<?php
session_start();

#include ("checkreferer.php");  ## TODO CHECKER!!

# $_SESSION = null;
$_SESSION["r"] = 5;

$titel = "";
if (  $_SESSION["r"] == 3  ) 	$titel = "Dozenten Tool";
if (  $_SESSION["r"] == 4  )	$titel = "Koordinatoren Tool";
if (  $_SESSION["r"] == 5  )	$titel = "Admin Tool";

echo  "<html>
<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\" />
<meta http-equiv=\"cache-control\" content=\"no-cache\">
<meta http-equiv=\"pragma\" content=\"no-cache\">

<title>".$titel."</title>
</head>";

if ($_SESSION["r"] >=3 ) 
{
	echo "<frameset rows=\"40,*\" frameborder=\"no\" border=\"0\" framespacing=\"0\">
  <frame src=\"navi.php?' . SID . '\" name=\"topFrame\" scrolling=\"No\" noresize=\"noresize\" id=\"topFrame\" title=\"topFrame\" />
  <frame src=\"belegliste.php?' . SID . '\" name=\"mainFrame\" id=\"mainFrame\" title=\"mainFrame\" />
</frameset>
<noframes  ><body>
</body>
</noframes></html>";
}
?>
