<?php 
error_reporting(1);
session_start();
#include ("checkreferer.php");#
#$_SESSION["r"] = null;

#$_SESSION["r"] = 5;
$_SESSION[ 'where' ] = '';

if (isset($_SESSION["r"]))
{
    $role = $_SESSION["r"];

    require_once( "../inc/belegliste.class.php" );
    require_once( "../inc/db.class.php" );
    require_once( "../inc/db.IDM.class.php" );
    require_once( "../inc/vz.data.class.php" );
    require_once( "../inc/vz.render.class.php" );
    require_once( "htmlheader.php" );
    
    $dbIDM                  = new DBIDM();
    $db                     = new DB( $dbIDM );
    $bl                     = new Belegliste( $db, $dbIDM );
    $data                   = new Data;
    $render                 = new Render;
   
    $can_edit               = $bl->isChangeable();              # Bei Nutzer, die Umbuchungen vornehmen dürfen.
    $veranstaltungsFilterID = $bl->getFilterID();               # gewählter Filter
    $filterListe            = $bl->getHeliosAuswahl();          # gewählte Listen für Anzeige und den HELIOS Export
    $param                  = $bl->getParams();                 # Übergebenen POST und GET Parameter
    $vorlesungsliste        = $db->getVorlesung(0 );       # aktueller Veranstalungsplan: Studigengang - Semester - Professor
    $gesamtBelegliste       = $db->getGesamtBelegliste();       # gesamte Wunsch-Belegliste mit allen Buchungen der Studis
    $vl_verzeichnis2        = $db->getVorlesungsVerzeichnis2(); #
    $vl_verzeichnis         = $db->getVorlesungsVerzeichnis();  #


    if( isset ( $param[ 'action' ] ) )
    {  
       $data->logIt( $param[ 'checksum' ].",".$param[ 'filterID' ]);
       $db->setDB( $param, 0 , $gesamtBelegliste, $vl_verzeichnis );
       $vl_verzeichnis     = $db->getVorlesungsVerzeichnis();
        
       $gesamtBelegliste   = $db->getGesamtBelegliste();
    }

    if( isset ( $param[ 'A' ] ) )
    {   
      if ($param['A'] ==  1){ $db->setStatus( 1 ); } #Alle Erstis
      if ($param['A'] ==  2){ $db->setStatus( 2 ); } # Alle NICHT Erstis
      if ($param['A'] ==  3){ $db->setStatus( 3 ); } #ALLE
    }
    
                                         $html2 .= $render->renderVeranstaltungsMenu( $vl_verzeichnis2, $role )."<br />";  # Auswahlmenu
                                          
   if( $veranstaltungsFilterID != -2)   { $html2 .= $data->exportBelegliste(       $gesamtBelegliste , $vl_verzeichnis, $veranstaltungsFilterID ); }
   if( $veranstaltungsFilterID == -2)   { $html2 .= $data->exportHELIOSliste(      $gesamtBelegliste , $vl_verzeichnis  ); }

   if( $filterListe )                   { $html2 .= $data->exportHELIOSliste2(     $gesamtBelegliste , $vl_verzeichnis, $filterListe  );    }
    
                                         $html .= $render->renderGesamtBelegliste( $gesamtBelegliste , $vl_verzeichnis, $veranstaltungsFilterID, $can_edit  );
                                         $html .= $render->renderSemesterAnzahlSummary(    $gesamtBelegliste , $veranstaltungsFilterID  );

                                         $html .= $render->printForm();
                                          $html .= '</body></html>';
            
                                          echo $html;
                                          echo '<div  style="display:inline-block; float:left; width:220px;">';
                                          echo $html2;
                                          echo '</div>';
}

else 
{   # die("SESSION CLOESED");
}

function deb( $dmp, $stop = 0 )
{
  echo "<pre>";
  print_r($dmp);
  echo "</pre>";
if ($stop){ die();}
}  

?>