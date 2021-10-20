<?php
class Data
{
	function exportBelegliste( $belegliste, $vl_verzeichnis,  $veranstaltungsFilterID )
	{ $head = "MATRIKELNR;VORNAME;NACHNAME;STUDIENGANG;SEMESTER;MAIL\r\n";
		$csv = '';
		$vorlesungsname = '';
		if( $belegliste )
		foreach( $belegliste as $bl )
		{ if( $bl[ 'vorlesung' ][ 'ID' ] == $veranstaltungsFilterID ||  $veranstaltungsFilterID == -2 )
			{
				$vorlesungsname =  $bl['vorlesung']['veranstaltung']['abk'] . "-" . $bl['vorlesung']['studiengang']['abk'] .  $bl['vorlesung']['semester'] . "-" . $bl['vorlesung']['professor']['abk'];
				$csv2  = 	 $bl['IDMuser']['matrikelnr'];
				$csv2 .= ";".$bl['IDMuser']['vorname']; 
				$csv2 .= ";".$bl['IDMuser']['nachname'];
#				$csv2 .= ";".$bl['IDMuser']['studiengang']['abk']; 
				$csv2 .= ";".$bl['IDMuser']['studiengang']; 
				$csv2 .= ";".$bl['IDMuser']['semester'];
				$csv2 .= ";".$bl['IDMuser']['mail']."\r\n" ;
				$csv  .= $csv2 ; 
#				$csv2 = "";
			}
		}
		
		$csv = $head.$csv;
		
		$vorlesungsname = str_replace('/', '',  $vorlesungsname );
		
		$filename = "download/".$vorlesungsname."_".date("Y-m-d-His",time()).".csv";
		
		$fh = fopen( $filename, "w" );
		fwrite( $fh, utf8_decode( $csv ) );
		fclose( $fh );
		$fp = fopen( $filename,"r");
		return "<br /><br /><a style=\"float:left;\" href=\"$filename\" class=\"download2\" target=\"blank\">Download Belegliste</a><br /><br/>";
	}
	
	
	function exportWunschbelegliste( $belegliste )
	{
		$head = "STATUS;MATRIKELNR;VORNAME;NACHNAME;STUDIENGANG;SEMESTER;MAIL;VERANSTALTUNG;VER_ABK;PROFESSOR;PROF_ABK;VORLESUNG_BEI;VERANST_ID\r\n";
		 
		for ($i=0; $i < sizeof($belegliste);$i++)
		{ 
			$bl .=  $belegliste[$i]['status'] ;							//
			$bl .=  ";".$belegliste[$i]['user']['matrikelnr'] ;				//
			$bl .=  ";".$belegliste[$i]['user']['vorname'] ;				//
			$bl .=  ";".$belegliste[$i]['user']['nachname'] ;				//
			$bl .=  ";".$belegliste[$i]['user']['studiengang'] ;			//
			$bl .=  ";".$belegliste[$i]['user']['semester'] ;			//
			$bl .=  ";".$belegliste[$i]['user']['mail'] ;					//
			$bl .=  ";".$belegliste[$i]['veranstaltung']['veranstaltung'] ;		//
			$bl .=  ";".$belegliste[$i]['veranstaltung']['veranstaltung_abk'] ;	//
			$bl .=  ";".$belegliste[$i]['veranstaltung']['professor'] ;			//
			$bl .=  ";".$belegliste[$i]['veranstaltung']['professor_abk'] ;		//	
			$bl .=  ";".$belegliste[$i]['veranstaltung']['vorlesung_bei'] ;		//
			$bl .=  ";".$belegliste[$i]['veranstaltungID']."\r\n" ;		//
			$csv .= $bl; 
			$bl = "";
		}
		
		$csv = $head.$csv;
		$filename = "download/wunsch_belegliste_".date("Y-m-d-His",time()).".csv";
		$fh = fopen($path.$filename,"w");
		fwrite($fh,utf8_decode( $csv ) );
		fclose($fh);
		$fp = fopen($path.$filename,"r");
		return "<a href=\"$filename\" class=\"download\" target=\"blank\">Download Wunschbelegliste</a>";
	}

	function exportHELIOSliste(  $gesamtBelegliste , $vl_verzeichnis  )
	{ $allStudentsMulti = array();
    
    foreach( $vl_verzeichnis as $vlvz )
		{
			$professortmp[]			= 	$vlvz['professor']['abk'];	
			$veranstaltungtmp[] 	=	$vlvz['veranstaltung']['abk'];
		}		
 
		$professorList 				= array_unique( $professortmp );
		$i = 0;
		$phash = "Prof;CODE\r\n";
		foreach ( $professorList as $pl )
		{	$professorenHELIOShash [$pl] = "".chr( $i + 65 );
			$phash.= chr( $i + 65 ).";".$pl."\r\n";
			$i++;
		}
 
		$vhash = "Veranst;CODE\r\n";
		$veranstaltungList			= array_unique( $veranstaltungtmp );
		$i = 1;
		foreach ( $veranstaltungList as $vl )
		{	//$code = chr( $i + 65 );
			$code ="".$i; 
			$veranstaltungHELIOShash[$vl] = "".$code;
			$vhash.= $code .";".$vl."\r\n";
			$i++;
		}

		if ( $gesamtBelegliste != '' ) 
    { foreach  ( $gesamtBelegliste  as $bel )
		  { $allStudentsMulti [] 	= $bel ['IDMuser']['matrikelnr']. ";" . $bel ['IDMuser']['vorname'] . ";" . $bel ['IDMuser']['nachname']. ";";
  		}
		
	  	$allStudents  = array_unique( $allStudentsMulti  );
    }
    
		$csv = "Matrikel;Name;Vorname;CODE\r\n";

	  if (isset($allStudents))
    foreach ( $allStudents as $studs )
		{ $matNr  =  explode(";",$studs );
			$csv .= $studs;
			for ( $i = 0 ; $i < sizeof( $gesamtBelegliste ) ; $i++ )
			{ if ( $gesamtBelegliste[$i]['IDMuser']['matrikelnr'] ==   $matNr[0] && $gesamtBelegliste[$i]['status'] == 'B'  )// Nur Bestätigte Belegungen kommen in die Liste
				{  $csv .= $professorenHELIOShash[$gesamtBelegliste[$i]['vorlesung']['professor']['abk']] ;
           $csv	.= $veranstaltungHELIOShash[$gesamtBelegliste[$i]['vorlesung']['veranstaltung']['abk']];
				}		
			}
			$csv  .="\r\n";
		}
	
		$csv = $csv.$phash.$vhash;
		$filename = "download/HELIOS_export_".date("Y-m-d-His",time()).".csv";
		$fh = fopen($filename,"w");
		fwrite($fh,utf8_decode( $csv ) );
		fclose($fh);
		$fp = fopen($filename,"r");
		return "<a href=\"$filename\" style=\"float:left;\" class=\"download\" target=\"blank\">Download HELIOS Export</a>";
 	}

		
	function exportHELIOSliste2(  $gesamtBelegliste , $vl_verzeichnis, $filterListe  )
	{	foreach( $vl_verzeichnis as $vlvz )
		{	$professortmp[]			= 	$vlvz['professor']['abk'];
			$veranstaltungtmp[] 	=	$vlvz['veranstaltung']['abk'];
		}		
 
		$professorList 				= array_unique( $professortmp );
		$i = 0;
		$phash = "Prof;CODE\r\n";
		foreach ( $professorList as $pl )
		{ $professorenHELIOShash [$pl] = "".chr( $i + 65 );
			$phash.= chr( $i + 65 ).";".$pl."\r\n";
			$i++;
		}
 
		$vhash = "Veranst;CODE\r\n";
		$veranstaltungList = array_unique( $veranstaltungtmp );
		$i = 1;
		foreach ( $veranstaltungList as $vl )
		{	$code = "" . $i;
			$veranstaltungHELIOShash[$vl] = "" . $code;
			$vhash.= $code . ";" . $vl . "\r\n";
			$i++;
		}
  
		foreach  ( $gesamtBelegliste  as $bel ) 
    {  $allStudentsMulti[]	= $bel ['IDMuser']['matrikelnr']. ";" . $bel ['IDMuser']['vorname'] . ";" . $bel ['IDMuser']['nachname']. ";";
    }
		$allStudents  = array_unique( $allStudentsMulti  );
  
    $csv ="Matrikel;Name;Vorname;CODE\r\n";
    $csv2	='';
    
    foreach ( $allStudents as $studs )
		{ $hatVeranstaltung = false;
		  $matNr  =  explode(";",$studs );
			for ( $i = 0 ; $i < sizeof( $gesamtBelegliste ) ; $i++ )
			{  if ( $gesamtBelegliste[$i]['IDMuser']['matrikelnr'] ==   $matNr[0] )
			   {  foreach ($filterListe as $filter)
						{ $ID = $gesamtBelegliste[ $i ][ 'vorlesung' ][ 'ID' ];
							if ( $ID ==  $filter  && $gesamtBelegliste[ $i ][ 'status' ] == 'B'  )// Nur Bestätigte Belegungen kommen in die Liste
							{ $csv2	.= $professorenHELIOShash[   $gesamtBelegliste[ $i ][ 'vorlesung' ][ 'professor'     ][ 'abk' ] ];
								$csv2	.= $veranstaltungHELIOShash[ $gesamtBelegliste[ $i ][ 'vorlesung' ][ 'veranstaltung' ][ 'abk' ] ];
                $veranst[ $ID ][ 'prof' ] = $gesamtBelegliste[ $i ][ 'vorlesung' ][ 'professor'     ][ 'abk'  ];
                $veranst[ $ID ][ 'stdg' ] = $gesamtBelegliste[ $i ][ 'vorlesung' ][ 'studiengang'   ][ 'abk2' ];
                $veranst[ $ID ][ 'vorl' ] = $gesamtBelegliste[ $i ][ 'vorlesung' ][ 'veranstaltung' ][ 'abk'  ];
                $hatVeranstaltung = true;
              }
						}
					}
			}		
      
			if( $hatVeranstaltung )
			{	$csv .= $studs;
			  $csv .= $csv2;
  			$csv  .="\r\n";
  			$csv2 = "";
			}	
}

$fname='';

foreach ($veranst as $ver)
{
   #$fname .= '.'.$ver['prof'].''.$ver['stdg'].''.$ver['vorl'];
}

$csv3  = $csv.$phash.$vhash;
$path ="download/";
$filename = ( "HELIOS_".date("Y-m-d-His",time())."".$fname.".csv" );
$filename = str_replace( '/',  '', $filename );
$fh = fopen($path.$filename,"w");
fwrite($fh,utf8_decode( $csv3 ) );
fclose($fh);
$fp = fopen($path.$filename,"r");
//$this->makeDownload($filename, $path, "text/plain");

return "<a href=\"$path$filename\" style=\"float:left;\" class=\"download\" target=\"blank\">Download SELECT HELIOS Export</a>";
}

function makeDownload($file, $dir, $type) 
{
  //ob_start('');
  ob_end_clean();
  header("Content-Type:$type");
  header("Content-Disposition: attachment; filename=".$file);
  readfile($dir.$file);
  die();
} 

function exportVorlesungsverzeichnisAuswahl($vl_verzeichnis, $lists )
{ $head = "STUDIENGANG;VERANSTALTUNG;	PROFESSOR\r\n";
  for ($i=0; $i < sizeof($vl_verzeichnis);$i++)
  { $vorlesung = $vl_verzeichnis[$id]  ;
    $nid = $vorlesung['ID'];
  
    $bl .=  " ".$lists[$nid]['studiengaenge'];

    $csv .= $bl;
    $bl = "";
  }
}

function getStudiPerVL($vl_verzeichnis)
{ // Sortierte Listen mit Studiengängen u. Veranstaltungen ohne Redundanzen erstellen
  foreach ( $vl_verzeichnis as $vz)
  {  print_r($vz);
  }
}


function sortVorlesungsVerzeichnis($vl_verzeichnis)
{
  // Sortierte Listen mit Studiengängen u. Veranstaltungen ohne Redundanzen erstellen
  foreach ( $vl_verzeichnis as $vz)
  { $sg[] = $vz[ 'studiengang'   ][ 'abk' ];
    $va[] = $vz[ 'veranstaltung' ][ 'abk' ];
  }

  $sg = array_unique( $sg );
  $va = array_unique( $va );
  sort( $sg );
  sort( $va );

  for ( $i = 0; $i < sizeof( $sg );  ++$i )
  { $sgTMP[ $i ][ 'value' ] = $sg[ $i ] ;
    $sgTMP[ $i ][ 'count' ] = $i;
  }
  $sg = $sgTMP;

  for ( $j = 0; $j < sizeof( $va );  ++$j )
  { $vaTMP[ $j ][ 'value' ] = $va[ $j ] ;
    $vaTMP[ $j ][ 'count' ] = $j;
  }
  $va = $vaTMP;
  


  
  
  // Array initialisieren
  for ( $i = 0; $i < sizeof( $sg );  ++$i )
  { for ( $j = 0; $j < sizeof( $va );  ++$j )
    { $vl_verzeichnisTMP[$i][$j] = 0;
    }
  }
  
  foreach ( $vl_verzeichnis as $vlvz )
  { $x = $y = 0;
  
    for ( $i = 0; $i < sizeof( $sg );  ++$i )
    { if ( $vlvz['studiengang']['abk'] ==  $sg[$i]['value'] )
      { $x =  $sg[$i]['count'];
      }
    }

    for ( $j = 0; $j < sizeof( $va );  ++$j )
    { if ( $vlvz['veranstaltung']['abk'] ==  $va[$j]['value'] )
      { $y = $va[$j]['count'];
      }
    }

    $vl_verzeichnisTMP[$x][$y] = $vlvz;
  }
  #deb($vl_verzeichnisTMP,1);
  return $vl_verzeichnisTMP;
}


function logIt($log)
{ $filename = "download/log.txt";
  $logline = $log. ", " . date("Y-m-d-His",time())."\n";

  $fh = fopen( $filename, "a" );
  fwrite($fh,utf8_decode( $logline ) );
  fclose($fh);
}

function deb($var)
{ echo "<pre>";
  print_r($var);
  echo "</pre>";
}
}
?>