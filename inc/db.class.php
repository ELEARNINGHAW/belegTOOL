<?php
#error_reporting(0  & ~E_DEPRECATED);

class DB
{
var $conn;
var $dbIDM;

function  __construct( $dbIDM = null )
{ require( "ini/db.ini.php" );
  $this->conn  = mysqli_connect( $server, $user, $pass );
  if (!$this -> conn -> connect_error)
	{	mysqli_select_db( $this->conn, $dbase  );

	}
	else
	{ echo( "<b>Verbindung zur IDM-DB konnte nicht hergestellt werden </b>" );
	}
	if( $dbIDM )
	{	$this->dbIDM = $dbIDM;
	}
}

function transSG( $sg )
{      if( $sg == '913' || $sg == '923' || $sg == '924' || $sg == '967' ) $ret = 'UT';
  else if( $sg == '925' || $sg == '926' || $sg == '968'                 ) $ret = 'VT';
  else if( $sg == '917' || $sg == '917' || $sg == '920'                 ) $ret = 'BT';
  else if( $sg == '112' || $sg == '971'                                 ) $ret = 'RE';
  else if( $sg == '904' || $sg == '970'                                 ) $ret = 'HC';
  else if( $sg == '921' || $sg == '922'                                 ) $ret = 'MT';
  else                                                                    $ret = 'XX';
  return $ret;
}

function getKohortStatus( $kohorte, $withAnz = false )  # Whereclausel, Anzahl Elemente
{
  #$status2 = 'W'; ## Nur nach Elementen mit Status 'W' (Wunsch) wird gesucht
       if ( $kohorte =='1')  $koho = ' AND idm2.mdl_haw_idm.semester = 1 ';  ## 1 ALLE Erstsemester
  else if ( $kohorte =='2')  $koho = ' AND idm2.mdl_haw_idm.semester != 1 '; ## 2 ALLE NICHT Erstsemester
  else if ( $kohorte =='3')  $koho = '';                                     ## 3 ALLE

  $statuslist =  ' ID=0';
  $sql_1 = "SELECT beleg.mdl_haw_wunschbelegliste.ID
  FROM beleg.mdl_haw_wunschbelegliste , idm2.mdl_haw_idm
  WHERE beleg.mdl_haw_wunschbelegliste.studID         = idm2.mdl_haw_idm.matrikelnr
  AND beleg.mdl_haw_wunschbelegliste.status          != 'B' ". $koho ."
  AND beleg.mdl_haw_wunschbelegliste.veranstaltungID != -1" ;

if( $kohorte != 3)
{ $sql_1 .= "\n AND ( beleg.mdl_haw_wunschbelegliste.phase = 1
                OR    beleg.mdl_haw_wunschbelegliste.phase = 2 ) ";
}  

$i = 0;

$result_1 = mysqli_query (  $this->conn, $sql_1  );

if ( $result_1 )
{ while ( $row = mysqli_fetch_array( $result_1, MYSQLI_ASSOC ) )
  {	$statuslist .= ' OR ID='.$row['ID']; $i++;
  }
}

$ret[ 'clausel' ] = $statuslist;
if ( $withAnz )
{  $ret[ 'anz' ] = $i;
}
	 
return $ret;
}
# status:  W,B
# Kohorte: 1 Alle Erstis,
#          2 Alle NICHT Erstis,
#          3 ALLE
function setStatus( $kohorte )
{ if (! isset( $_SESSION[ 'where' ][ $kohorte ][ 'clausel' ] ) ) ## 1. Aufruf - Noch kein Eintrag vorhanden
  { $set = $this -> getKohortStatus( $kohorte );
    $_SESSION[ 'where' ][ $kohorte ][ 'clausel' ] = $set[ 'statuslist' ] ;
    $_SESSION[ 'where' ][ $kohorte ][ 'kohote'  ] = $kohorte;
    $_SESSION[ 'where' ][ $kohorte ][ 'status'  ] = 'B';
    $_SESSION[ 'where' ][ $kohorte ][ 'css'     ] = 'W';
  }
  else
  { if(  $_SESSION[ 'where' ][ $kohorte ][ 'status' ] != 'B' )
    { $_SESSION[ 'where' ][$kohorte][ 'status' ] = 'B';
      $_SESSION[ 'where' ][$kohorte][ 'css'    ] = 'W';
    }
    else 
    { $_SESSION[ 'where' ][$kohorte][ 'status' ] = 'W';
      $_SESSION[ 'where' ][$kohorte][ 'css'    ] = 'B';
    }
  }
  
  $sql_1 = "UPDATE `beleg`.`mdl_haw_wunschbelegliste` "
          . "SET `status` = '" .$_SESSION[ 'where' ][ $kohorte ][ 'status' ]. "' "
          . "WHERE " .$_SESSION[ 'where' ][ $kohorte ][ 'clausel' ];

  $result_1 = mysqli_query (  $this -> conn, $sql_1  );
}

function getProfessor( $professorID )
{
     $sql_1 = "SELECT * FROM `mdl_haw_professoren` WHERE  `ID` =  $professorID";
	$result_1 = mysqli_query ( $this -> conn, $sql_1 );
	if ( $result_1 )
	{	while ( $row = mysqli_fetch_array( $result_1, MYSQLI_ASSOC ) )
		{	$professor = $row; 	}
	}
 	return $professor;
}

function getStudiengang( $studiengangID )
{
  $studiengang = null;
  $sql_1 = "SELECT * FROM `mdl_haw_studiengaenge` WHERE  `ID` =   $studiengangID";
	$result_1 = mysqli_query ( $this->conn, $sql_1 );
	if ( $result_1 )
	{ while ( $row = mysqli_fetch_array( $result_1, MYSQLI_ASSOC ) )
		{ $studiengang = $row;
		}
	}
  return $studiengang;
}

function getVeranstaltung( $veranstaltungID ) // zB Mat1
{    $sql_1 = "SELECT * FROM `mdl_haw_veranstaltungen` WHERE `ID`= $veranstaltungID";
	$result_1 = mysqli_query (  $this->conn, $sql_1  );
	if ( $result_1 )
	{	while ( $row = mysqli_fetch_array( $result_1, MYSQLI_ASSOC ) )
		{	$veranstaltung= $row; 	}
	}
  return $veranstaltung;
}

function getVorlesungsVerzeichnis()
{  	
	$sql_1 = "SELECT  * FROM `mdl_haw_vl_verzeichnis` ORDER BY `veranstaltungID`";
	$result_1 = mysqli_query (  $this -> conn, $sql_1  );
	if ( $result_1 )
	{	while ( $row = mysqli_fetch_array( $result_1, MYSQLI_ASSOC ) )
		{	$tmp[ 'ID'               ] 	= "";
	 		$tmp[ 'professor'        ] 	= "";
			$tmp[ 'studiengang'      ] 	= "";
			$tmp[ 'veranstaltung'    ] 	= "";
			$tmp[ 'semester'         ] 	= "";
			$tmp[ 'anzStudenten'     ]  = "";
 
			$tmp[ 'ID'               ]  = $row[ 'ID' ];
			$tmp[ 'professor'        ]  = $this -> getProfessor( $row[ 'professorID' ] );
 			$tmp[ 'studiengang'      ]	= $this -> getStudiengang( $row[ 'studiengangID' ] );
 			$tmp[ 'veranstaltung'    ]	= $this -> getVeranstaltung( $row[ 'veranstaltungID' ] );
 			$tmp[ 'semester'         ]  = $row[ 'semester' ];
			$tmp[ 'anzStudenten'     ]  = $this -> getAnzStudisInVeranstaltung( $row[ 'ID' ] );

			$vl_verzeichnis[ $row[ 'ID' ] ] 	= $tmp;
 
			unset( $tmp );
		}
	}
  	return $vl_verzeichnis;
}

function getVorlesungsVerzeichnis2()
{  	
	$sql_1 = "SELECT
              mdl_haw_veranstaltungen.veranstaltung as veranstName,
              mdl_haw_veranstaltungen.abk           as veranstAbk,
              mdl_haw_vl_verzeichnis.semester       as veranstSem,              
              mdl_haw_vl_verzeichnis.ID             as veranstID,
              mdl_haw_studiengaenge.studiengang     as studiengName,
              mdl_haw_studiengaenge.abk             as studiengAbk,
              mdl_haw_studiengaenge.abk2            as studiengAbk2,
              mdl_haw_professoren.professor         as profName,
              mdl_haw_professoren.abk               as profAbk  
       FROM mdl_haw_professoren, mdl_haw_veranstaltungen, mdl_haw_studiengaenge, `mdl_haw_vl_verzeichnis`  
       WHERE mdl_haw_vl_verzeichnis.professorID    = mdl_haw_professoren.ID
       AND   mdl_haw_vl_verzeichnis.studiengangID  = mdl_haw_studiengaenge.ID
       AND  mdl_haw_vl_verzeichnis.veranstaltungID = mdl_haw_veranstaltungen.ID 
      ORDER BY `veranstName`, `studiengAbk`"; 
	$result_1 = mysqli_query (  $this->conn, $sql_1  );
  
  if ( $result_1 )
	{	while ( $row = mysqli_fetch_array( $result_1, MYSQLI_ASSOC ) )
		{  $key =  $row[ 'veranstAbk' ]. '|' .$row[ 'studiengAbk' ]. '|' .$row[ 'profAbk' ];
       $vl_verzeichnis[ $row[ 'veranstAbk' ] ][ $key ] = $row;
       $vl_verzeichnis[ $row[ 'veranstAbk' ] ][ $key ][ 'anzStudis' ] =  $this -> getNumberOfStudisInVeranst( $row[ 'veranstID' ] );
		}
	}
 return $vl_verzeichnis;
}

function getNumberOfStudisInVeranst( $veranstID )
{ $sql    = "SELECT COUNT(*) as anzStudis FROM mdl_haw_wunschbelegliste WHERE `veranstaltungID` =" .$veranstID;
  $result = mysqli_query( $this -> conn, $sql );
  if ( $result )
	$row = mysqli_fetch_array( $result, MYSQLI_ASSOC ); 

  return $row[ 'anzStudis' ];
}

function getAnzStudisInVeranstaltung( $veranstaltungID )
{ $tmp[] = "";
	$ret = 0;
	$sql_1 = "SELECT `veranstaltungID` FROM `mdl_haw_wunschbelegliste` WHERE `veranstaltungID` = ". $veranstaltungID;
	$result_1 = mysqli_query (  $this->conn, $sql_1  );

	if ( $result_1 )
	{	while ( $row = mysqli_fetch_array( $result_1, MYSQLI_ASSOC ) )
		{	$ret++;
      $tmp[] = "";
		}
	}
	return $ret;	
}

function getVorlesung( $ID )
{ $whereclausel = '';
	if ( $ID != 0 )  { $whereclausel = 'WHERE `ID` = $ID';   }

	$sql_1 = "SELECT DISTINCT * FROM `mdl_haw_vl_verzeichnis` ". $whereclausel ." ORDER BY `veranstaltungID`";
	$result_1 = mysqli_query (  $this -> conn, $sql_1  );

	if ( $result_1 )
	{	while ( $row = mysqli_fetch_array( $result_1, MYSQLI_ASSOC ) )
		{	$tmp[ 'ID'            ]	= $row[ 'ID' ];
			$tmp[ 'professor'     ] = $this -> getProfessor( $row[ 'professorID' ] );
			$tmp[ 'studiengang'   ] = $this -> getStudiengang( $row[ 'studiengangID' ] );
			$tmp[ 'veranstaltung' ]	= $this -> getVeranstaltung( $row[ 'veranstaltungID' ]);
			$tmp[ 'semester'      ] = $row[ 'semester' ];
			$vorlesungsListe[ $row[ 'ID' ] ] = $tmp;

			unset( $tmp );
		}
	}

  $_SESSION['vorlesungsliste'] = $vorlesungsListe;
  return $vorlesungsListe;
}

function getBelegliste( $matrikelNr, $vl_verzeichnis )
{	$belegliste = array();
	$sql_1 = "SELECT  * FROM `mdl_haw_wunschbelegliste` WHERE `studId` = '$matrikelNr'   ORDER BY `ID` ASC";
	$result_1 = mysqli_query (  $this -> conn, $sql_1  );
	if ( $result_1 )
	{	while ( $row = mysqli_fetch_array( $result_1, MYSQLI_ASSOC ) )
		{ if( $row[ 'veranstaltungID' ] != -1)
		  { $row[ 'veranstaltung' ] = $vl_verzeichnis[ $row[ 'veranstaltungID' ] ];
      }
      $belegliste[] = $row;
		}
	}
 	return $belegliste;
}

function getPhasen()
{ $sql_1 = "SELECT * FROM `mdl_haw_phasen` " ;

	$result_1 = mysqli_query (  $this -> conn, $sql_1  ); 	 ;
	if ( $result_1 )
	{	while ( $row = mysqli_fetch_array( $result_1, MYSQLI_ASSOC ) )
		{ $p = $row[ 'phase' ];
			$phasen[ $p ]  = $row[ 'timestamp' ];
		}
	}
	return $phasen;
}

/*
function getErstsemestermatnr()
{	$sql_1 = "SELECT `erstsemestermatnr` FROM `mdl_haw_erstsemestermatnr`";
	$result_1 = mysqli_query(  $this->conn, $sql_1  );
	if ( $result_1 )
	{	while ( $row = mysqli_fetch_array( $result_1, MYSQLI_ASSOC ) )
		{ $erstsemestermatnr = $row[ 'erstsemestermatnr' ];
		}
	}
	return $erstsemestermatnr;
}
*/

function setDB( $param, $IDMuser , $belegliste, $vl_verzeichnis )
{ if( $param[ 'column' ] == "delete" )
	{ $sql_1 = "DELETE FROM `mdl_haw_wunschbelegliste` WHERE `ID` = ". $param[ 'ID' ] .";";
	}	
	else if( $param[ 'column' ] == "neuerBeleglistenEintrag" )
	{ $sql_1 = "INSERT INTO `mdl_haw_wunschbelegliste` ( `studID`,  `veranstaltungID`, `timestamp`, `status`, `checksum`) VALUES ( '".$IDMuser[ 'matrikelnr' ]."', '-1', NOW(), '', '".$IDMuser[ 'matrikelnr' ]."')";
	}
	else if( $param[ 'column' ] == "studiengangID" )                                                 	/*Alle Eintr�ge in der Belegliste werden gel�scht wenn das Studiengang ge�ndert wird */
	{ $sql_1 = "DELETE FROM `mdl_haw_wunschbelegliste` WHERE `studID` =".$IDMuser[ 'matrikelnr' ];
  }
	else if( $param[ 'column' ] == "semester" )	                                                      /*Alle Eintr�ge in der Belegliste werden gel�scht wenn das Semester ge�ndert wird */
	{ $sql_1 = "DELETE FROM `mdl_haw_wunschbelegliste` WHERE `studID` =".$IDMuser[ 'matrikelnr' ];
		mysqli_query (  $this->conn, $sql_1  );
	}
	else if( $param[ 'column' ] == "update2" )                                                        // Update von Checksumme und Veranstaltungs ID
	{ if ( isset( $param[ 'phase' ] ) ) // Argument:Phase nur bei Existenz mit in den SQL Queue
		{  $p = "`phase` =  ".$param[ 'phase' ]. " ,";	}
		else
		{  $p = '';	}
		
		$sql_1 = "UPDATE `mdl_haw_wunschbelegliste` SET `checksum` = '".$param[ 'checksum' ]."',  $p  `veranstaltungID` = '".$param[ 'value' ]."' WHERE  `ID` = ".$param[ 'ID' ];
	 
		$result_1 = mysqli_query(  $this->conn, $sql_1  );
 
    if( sizeof( $belegliste ) > 0 )
		{	foreach ( $belegliste as $bl )
			{	if( $bl[ 'ID' ] ==   $param[ 'ID' ]  )
				{	$status =  $bl[ 'status' ];
				}
			}
    }
		$sql_1 = "UPDATE `mdl_haw_wunschbelegliste` SET `timestamp` = NOW( ) , `status` = '" .$status. "' WHERE  `ID` = " .$param[ 'ID' ];
	 
	 	if( $param[ 'value' ] == -1 )
		{	$sql_1 = "DELETE FROM `mdl_haw_wunschbelegliste` WHERE `ID` = ". $param[ 'ID' ] .";";
		}	

		$result_1 = mysqli_query(  $this->conn, $sql_1  );

	}
	else if( $param[ 'column' ] == "update3" )
	{ $sql_1 = "INSERT INTO `mdl_haw_wunschbelegliste` ( `studID`,  `veranstaltungID`, `timestamp`, `status`, `checksum`) VALUES ( '".$IDMuser[ 'matrikelnr' ]."', '-1', NOW(), '', '".$IDMuser[ 'matrikelnr' ]."')";
		$result_1 = mysqli_query(  $this->conn, $sql_1  );
		
		// Update von Checksumme und Veranstaltungs ID
		$sql_1 = "UPDATE `mdl_haw_wunschbelegliste` SET `checksum` = '".$param[ 'checksum' ]."',   `veranstaltungID` = '".$param[ 'value' ]."' WHERE  `checksum` = ".$param[ 'ID' ];
		$result_1 = mysqli_query(  $this->conn, $sql_1  );

		$sql_1 = "UPDATE `mdl_haw_wunschbelegliste` SET `timestamp` = NOW( ) , `status` = 'M' WHERE  `checksum` = " .$param[ 'ID' ];
		$result_1 = mysqli_query(  $this->conn, $sql_1  );
	}
	
	else
	{	// Update von Checksumme und Veranstaltungs ID
		$sql_1 = "UPDATE `mdl_haw_wunschbelegliste` SET `checksum` = '".$param[ 'checksum' ]."',  `phase` =  '".$param[ 'phase' ]."' ,  `".$param[ 'column' ]."` = '".$param[ 'value' ]."' WHERE  `ID` = ".$param[ 'ID' ];
		
		$result_1 = mysqli_query(  $this->conn, $sql_1  );
		$belegliste = $this->getBelegliste( $IDMuser[ 'matrikelnr' ], $vl_verzeichnis );	
		$belegliste = $this->isBooked( $IDMuser, $belegliste );
	  
		if( sizeof( $belegliste ) >0 )
		foreach ( $belegliste as $bl )
		{	if( $bl[ 'ID' ] ==   $param[ 'ID' ] )
			{	$status =  $bl[ 'status' ];
			}
		}
		$sql_1 = "UPDATE `mdl_haw_wunschbelegliste` SET `timestamp` = NOW( ) , `status` = '".$status."' WHERE  `ID` = ".$param[ 'ID' ];

		if( $param[ 'value' ] == -1 ) 
		{	$sql_1 = "DELETE FROM `mdl_haw_wunschbelegliste` WHERE `ID` = ". $param[ 'ID' ] .";";
		}	
		$result_1 = mysqli_query(  $this->conn, $sql_1  );
	}

	if( !isset($result_1) )
	{	$result_1 = mysqli_query(  $this->conn, $sql_1 );
	}
	return $belegliste;
}

function isBooked( $IDMuser, $belegliste )
{ if ( sizeof( $belegliste ) > 0 )
  for( $i=0; $i< sizeof( $belegliste ); $i++ )  // foreach ($belegliste as $bl)
  {
		$blStudiengang 		  =  $belegliste[ $i ][ 'veranstaltung' ][ 'studiengang' ][ 'ID' ];
		$blSemester 	      =  $belegliste[ $i ][ 'veranstaltung' ][ 'semester'    ];
	 
    $userStudiengang 	  =  $IDMuser[ 'studiengang' ];
		$userSemester       =  $IDMuser[ 'semester'    ];
	
		if ( $blStudiengang == $userStudiengang && $blSemester == $userSemester   )
		{	$belegliste[ $i ][ 'status' ] = "B";
		}
		else
		{	$belegliste[ $i ][ 'status' ] = "W";
		}
	}
	return $belegliste;
}

function setWishState( $id, $state )
{ if ( $state == 1 ) {  $sql_1 = "UPDATE `beleg`.`mdl_haw_wunschbelegliste` SET `status` = 'W' WHERE `mdl_haw_wunschbelegliste`.`ID` = ".$id;  }
  if ( $state == 2 ) {  $sql_1 = "UPDATE `beleg`.`mdl_haw_wunschbelegliste` SET `status` = 'B' WHERE `mdl_haw_wunschbelegliste`.`ID` = ".$id;  }
	$result_1 = mysqli_query(  $this->conn, $sql_1  );
}


function getVLVZStudiengaenge()  
{ $sql_1 = "SELECT DISTINCT `studiengangID` FROM `mdl_haw_vl_verzeichnis`";
	$result_1 = mysqli_query( $this->conn, $sql_1 );
	if ( $result_1 )
	{	while ( $row = mysqli_fetch_array( $result_1, MYSQLI_ASSOC ) )
		{ $sql_2 = "SELECT DISTINCT * FROM `mdl_haw_studiengaenge` WHERE ID = ".$row['studiengangID'];
			$result_2 = mysqli_query( $this -> conn, $sql_2 );
			if ( $result_2 )
			{	while ( $row2 = mysqli_fetch_array( $result_2, MYSQLI_ASSOC ) )
				{	$studiengaenge[] = $row2;
				}
			}
		}
	}
	return $studiengaenge;	
}

function getVeranstaltungsListe()
{	$sql_1 = "SELECT DISTINCT `veranstaltungID` FROM `mdl_haw_vl_verzeichnis`";
	$result_1 = mysqli_query( $this -> conn, $sql_1 );
 
	if ( $result_1 )
	{	while ( $row = mysqli_fetch_array( $result_1, MYSQLI_ASSOC ) )
		{	$sql_2 = "SELECT DISTINCT * FROM `mdl_haw_veranstaltungen` WHERE ID = ".$row['veranstaltungID'];
			$result_2 = mysqli_query( $this -> conn, $sql_2 );
			if ( $result_2 )
			{	while ( $row2 = mysqli_fetch_array( $result_2, MYSQLI_ASSOC ) )
				{ $veranstaltungen[] = $row2;
				}
			}
		}
	}
	return $veranstaltungen;
}


function getVorlesungsListe()
{ $sql_3 = "SELECT * FROM `mdl_haw_veranstaltungen";
  $result_3 = mysqli_query( $this -> conn, $sql_3);
  
  while ( $row = mysqli_fetch_array( $result_3, MYSQLI_ASSOC ) )
  {  $vorlesung[] = $row;
  }
  return $vorlesung;
}


function deb($var)
{
	echo "<pre>";
	print_r($var);
	echo "</pre>";
}

function getGesamtBelegliste( $sort = "veranstaltung" )
{
  $belegliste = '';

  $sql_1 = "SELECT * FROM `mdl_haw_wunschbelegliste` ORDER BY `".$sort."ID`	ASC ";

  $result_1 = mysqli_query( $this -> conn, $sql_1 );

  if ( $result_1 )
  { while ( $row = mysqli_fetch_array( $result_1, MYSQLI_ASSOC ) )
    { $tmp[ 'ID'              ] = $row[ 'ID'              ];
      $tmp[ 'status'          ] = $row[ 'status'          ];
      $tmp[ 'phase'           ] = $row[ 'phase'           ];
      $tmp[ 'veranstaltungID' ] = $row[ 'veranstaltungID' ];

      if( $row[ 'veranstaltungID' ] != '-1'   )
	    {
        $tmp[ 'IDMuser' ] = $this->dbIDM->getIDMuser( $row[ 'studID' ], 'M' ) ;  				                      #$this->deb($tmp[ 'IDMuser' ]);
        $tmp[ 'IDMuser' ][ 'studiengang' ]=    $this->transSG( $tmp[ 'IDMuser' ][ 'studiengang' ] );
        $tmp[ 'vorlesung' ] = $_SESSION[ 'vorlesungsliste' ][ $row[ 'veranstaltungID' ] ];
        $belegliste[] = $tmp;
      }
    }
  }
  return $belegliste;
}

	function getAllLists()
	{ $lists = "";

		$sql_1 = "SELECT * FROM `mdl_haw_professoren`";
		$sql_2 = "SELECT * FROM `mdl_haw_studiengaenge`";
		$sql_3 = "SELECT * FROM `mdl_haw_veranstaltungen`";
		
		$result_1 = mysqli_query( $this->conn, $sql_1);
		$result_2 = mysqli_query( $this->conn, $sql_2);
		$result_3 = mysqli_query( $this->conn, $sql_3);

		if ( $result_1 )
		{	while ( $row = mysqli_fetch_array( $result_1, MYSQLI_ASSOC ) )
			{	$professoren[] = $row;
			}
			$lists[ 'professoren' ] = $professoren;
		}

		if ( $result_2 )
		{	while ( $row = mysqli_fetch_array( $result_2, MYSQLI_ASSOC ) )
			{	$studiengaenge[] = $row;
			}
			$lists[ 'studiengaenge' ] = $studiengaenge;
		}

		if ($result_3)
		{	while ( $row = mysqli_fetch_array( $result_3, MYSQLI_ASSOC ) )
			{	$veranstaltungen[] = $row;
			}
			$lists[ 'veranstaltungen' ] = $veranstaltungen;
		}
		return $lists;
	}
}

/*

----------------------------
 	ID 	studiengang
----------------------------
	1 	Medizintechnik
	2 	Biomedical Engineering
	3 	Hazard Control
	4 	Rescue Engineering
	5 	Biotechnologie
	6 	Bioprocess Engineering
	7 	Umwelttechnik
	8 	Enviromental Engineering
	9 	Verfahrenstechnik
	10 	Process Engineering
	11 	Oekotrophologie
	12 	Feed Sciences
	13 	Health Sciences
	14 	Public Health

----------------------------
	ID 	professor 	abk
----------------------------
	1	Heitmann  	Heit
	2 	Maas 		Maa
	3 	Sawatzki 	Swi
	4 	Siegers 	Sie
	5 	Teschke 	Teb
	6 	Kampschulte Kps
	7 	Rodenhausen Rod
	8 	Schiemann 	Smn
	9 	Foerger 	Foer
	10 	Tolg 		Tlg
	12 	Kohlhoff 	Koh
	13 	Strehlow 	Str
	14 	Dildey 		Dil
	15 	Letzig 		Let
	16 	Baumann 	Bau
	17 	Kober 		Kob
	
----------------------------
 	ID 	veranstaltung 	abk
----------------------------
	1 	Mathematik 1 	Mat1
	2 	Mathematik 2 	Mat2
	3 	Mathematik 3 	Mat3
	5 	Physik 2 		Phy2
	7 	Informatik 2 	Inf2
	4 	Physik 1 		Phy1
	6 	Informatik 1 	Inf1
	8 	Informatik 3 	Inf3
*/



?>
