<?php 
session_start();
error_reporting(0);
include ("checkreferer.php");
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html   xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta   http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<meta   http-equiv="cache-control" content="no-cache">
<meta   http-equiv="pragma" content="no-cache">
<link   rel="stylesheet" type="text/css" href="lib/style.css" />
<script src="lib/lib.js" type="text/javascript"></script>
<title>Neuer Studi</title>
<script>
function setAction( action )
{
    document.form1.action.value=action;
}

function cf() /* clear form */
{
    document.form1.matrikel.value ="";
    document.form1.vorname.value  ="";
    document.form1.nachname.value ="";
    document.form1.mail.value     ="";
}   
</script>

</head>
<body>

<?php 
if (isset($_SESSION["r"])) /* Zugriff nur mit entsprechender Rolle  */
{

/*
echo "<br>POST<br><pre>";
print_r($_POST);
echo "</pre>";
*/

$IDMuser[ 'mail'       ] = $_POST[ 'mail'     ];
$IDMuser[ 'vorname'    ] = $_POST[ 'vorname'  ];
$IDMuser[ 'nachname'   ] = $_POST[ 'nachname' ];
$IDMuser[ 'matrikelnr' ] = $_POST[ 'matrikel' ];
$IDMuser[ 'akennung'   ] = $_POST[ 'akennung' ];
$action                  = $_POST[ 'action'   ];
 
require_once("../inc/belegliste.class.php");
require_once("../inc/db.class.php");
require_once("../inc/db.IDM.class.php");

$db                         = new DB;
$dbIDM                      = new DBIDM();
$bl                         = new Belegliste($db, $dbIDM);

$studiengaenge              = $db->getVLVZStudiengaenge();
$vl_verzeichnis             = $db->getVorlesungsVerzeichnis();
 
if( $action )
{  
    if(  $action == "test"  )
    {   if     ($IDMuser[ 'akennung'   ])   {	$IDMuser = $dbIDM->getIDMuser( $IDMuser[ 'akennung'   ], "A" );}
        else if($IDMuser[ 'matrikelnr' ])	{	$IDMuser = $dbIDM->getIDMuser( $IDMuser[ 'matrikelnr' ], "M" );}
    }
    
    if(  $action == "save"  )
    {   unset($err);
        if( !$IDMuser[ 'akennung'  ] ) $err[ 'akennung'   ] = "<span class=\"rot\">a-Kennung fehlt<span>";
        if( !$IDMuser[ 'matrikelnr'] ) $err[ 'matrikelnr' ] = "<span class=\"rot\">Matrikelnummer fehlt<span>";
        if( !$IDMuser[ 'vorname'   ] ) $err[ 'vorname'    ] = "<span class=\"rot\">Vorname fehlt<span>";
        if( !$IDMuser[ 'nachname'  ] ) $err[ 'nachname'   ] = "<span class=\"rot\">Nachname fehlt<span>";

        if (!$err)
        {  
            $IDMuserTMP = $dbIDM->getIDMuser( $IDMuser[ 'akennung' ] );  // Studiengang und Fachsemester werden ausgelesen und neu gepeichert
            $IDMuser[ 'studiengang'  ]	= $IDMuserTMP[ 'studiengang'  ]; 
            $IDMuser[ 'semester' ]  = $IDMuserTMP[ 'semester' ];
            $dbIDM->setIDMuser( $IDMuser );	 
        }
        $IDMuser = $dbIDM->getIDMuser( $IDMuser[ 'akennung' ] );
    
    } 
}

$html .= "<h1>Studiverwaltung</h1>";

$html .= "<div style=\" border:#039 5px solid; position:relative;  padding:20px;  top:10px; left:0px; width:330px; height:225px;\"> 
<h3 style=\"text-align: center; width: 310px;\">User Stammdaten</h3> 
<form id=\"form1\" name=\"form1\" method=\"post\" action=\"#\">
    <table>
        <tr><td> <label>HAWKennung   </label></td><td><input size=\"7\"  type=\"text\" name=\"akennung\"    value=\"". $IDMuser[ 'akennung'   ]  ."\"  onchange=\"cf();\" />".$err[ 'akennung' ]     ."<input name=\"Suchen\"  type=\"submit\" value=\"- SEARCH -\" onclick=\"setAction('test'); 	\" /></td></tr>
        <tr><td> <label>Matrikel    </label></td><td><input size=\"7\"  type=\"text\" name=\"matrikel\"    value=\"". $IDMuser[ 'matrikelnr' ]  ."\"                     />".$err[ 'matrikelnr' ]   ."</td></tr>
        <tr><td> <label>Vorname     </label></td><td><input size=\"30\" type=\"text\" name=\"vorname\"     value=\"". $IDMuser[ 'vorname'    ]  ."\"                     />".$err[ 'vorname'    ]   ."</td></tr>
        <tr><td> <label>Nachname    </label></td><td><input size=\"30\" type=\"text\" name=\"nachname\"    value=\"". $IDMuser[ 'nachname'   ]  ."\"                     />".$err[ 'nachname'   ]   ."</td></tr>
        <tr><td> <label>Email       </label></td><td><input size=\"30\" type=\"text\" name=\"mail\"        value=\"". $IDMuser[ 'mail'       ]  ."\"                     />                         </td></tr>
    </table>
<br>

<input type=\"hidden\" name=\"action\" value=\"\" />
<input name=\"Anlegen\" type=\"submit\" value=\"- SAVE -\" onclick=\"setAction('save'); \" />
 </form>
</div>
";
 
/* 
echo "<br>IDMuser<br><pre>";
print_r($IDMuser);
echo "</pre>";
*/
 
$idm =
"?u="   .rawurlencode( base64_encode( $IDMuser[ 'akennung'    ] ))
."&fn=" .rawurlencode( base64_encode( $IDMuser[ 'vorname'     ] ))
."&ln=" .rawurlencode( base64_encode( $IDMuser[ 'nachname'    ] ))
."&m="  .rawurlencode( base64_encode( $IDMuser[ 'mail'        ] ))
."&se=" .rawurlencode( base64_encode( $IDMuser[ 'semester'    ] ))
."&sg=" .rawurlencode( base64_encode( $IDMuser[ 'studiengang' ] ))
."&ln=" .rawurlencode( base64_encode( $IDMuser[ 'nachname'    ] ))
."&dp=" .rawurlencode( base64_encode( $IDMuser[ 'department'  ] ))        
."&id=" .rawurlencode( base64_encode( $IDMuser[ 'matrikelnr'  ] ))
."&sv=" .rawurlencode( base64_encode( 'true'  ));
        
$html .= "<div style=\" border:#039 5px solid; position:relative;  padding:20px;  top:-265px; left:400px; width:230px; height:550px;\">  
<h3 style=\"text-align: center; width: 220px;\">Belegdaten</h3>
<iframe frameborder=\"0\" src=\"../belegliste/index.php".$idm."\" width=\"220\" height=\"500\"></iframe></div>";

echo  $html;
}

else 
{
    die("ERROR");
}

?>
 
</body>
</html>
