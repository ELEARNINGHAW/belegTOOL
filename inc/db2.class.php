<?php
class DB
{
	var $conn;
  function __construct()
  {
    require("ini/db.IDM.ini.php");
    $this->conn  = mysqli_connect( $server, $user, $pass );
    
    if( $this->conn )
    {
      if (!mysqli_select_db(  $this->conn, $dbase )            ) 		echo "<br>ERROR: DB Select<br>";
      if (!mysqli_query($this->conn ,  "set names 'utf8'"    ) ) 		echo "<br>ERROR: DB UTF8<br>";
    }
    else
    {
      die("<b>ERROR:  IDM-DB no connection </b>");
    }
  }


	function getVorlesungsVerzeichnis( $sort= "veranstaltung")
	{
		if($sort =="")
			$sort = "veranstaltung";

		$sql_1 = "SELECT * FROM `mdl_haw_vl_verzeichnis`  ORDER BY `".$sort."ID`	ASC ";
		$result_1 = mysql_query( $sql_1, $this->conn );
		
		if ( $result_1 )
		{   $i = 0;
	 		while ( $row = mysql_fetch_array( $result_1, MYSQL_ASSOC ) )
			{
				$vl_verzeichnis[$i] = $row;
				$vl_verzeichnis[$i]['professor'] 		= $this->getProfessor( $row['professorID']  );
				$vl_verzeichnis[$i]['studiengang'] 		= $this->getStudiengang( $row['studiengangID']  );
				$vl_verzeichnis[$i]['veranstaltung'] 	= $this->getVeranstaltung( $row['veranstaltungID']  );
				$i++;
			}
		}
	return $vl_verzeichnis;
	}

	function getWunschBelegliste( $sort = "stud" )
	{
	    if($sort =="")
			$sort = "stud";
			
		$sql_1 = "SELECT * FROM `mdl_haw_wunschbelegliste` ORDER BY `".$sort."ID`	ASC ";
		$result_1 = mysql_query( $sql_1, $this->conn );
		if ( $result_1 )
		{	
			while ( $row = mysql_fetch_array( $result_1, MYSQL_ASSOC ) )
			{
			    if( $row['professorID'] != -1 )
				{
					/* ---- USERDATEN   ---*/
					$sql_2 = "SELECT DISTINCT * FROM `mdl_haw_idm` WHERE `matrikelnr` = '".$row['studID']."'";
					$result_2 = mysql_query( $sql_2, $this->conn );
					if ( $result_2 )
					{	
						while ( $row2 = mysql_fetch_array( $result_2, MYSQL_ASSOC ) )
						{
							$studiengang = "";
							$sql_2a = "SELECT DISTINCT studiengang FROM `mdl_haw_studiengaenge` WHERE `ID` = '".$row2['studiengang']."'";
							$result_2a = mysql_query( $sql_2a, $this->conn );
							if ( $result_2a )
							{	
								while ( $row2a = mysql_fetch_array( $result_2a, MYSQL_ASSOC ) )
								$studiengang = trim($row2a['studiengang']);
							}
							$row2['studiengang'] = $studiengang; 
							$user_tmp = $row2;
						}
					}
				
					/* ---- VORLESUNGSDATEN   ---*/
					$sql_4 = "SELECT DISTINCT * FROM `mdl_haw_vl_verzeichnis` WHERE ID = ".$row['professorID']; /* ---- Professor ist hier Vorlesung im  Vorlesungsverzeichnis */
			
					$result_4 = mysql_query( $sql_4, $this->conn );
				
	
					if ( $result_4 == true  )
					{	
						while ( $vorlesungsVZ = mysql_fetch_array( $result_4, MYSQL_ASSOC )  )
						{  
							unset($vorlesung_tmp);
							/* ---- VERANSTATLUNGSDATEN   ---*/
							$sql_5 = "SELECT DISTINCT * FROM `mdl_haw_veranstaltungen` WHERE `ID` = '".$vorlesungsVZ['veranstaltungID']."'";
							$result_5 = mysql_query( $sql_5, $this->conn );
							if ( $result_5 )
							{	
								while ( $veranstaltung = mysql_fetch_array( $result_5, MYSQL_ASSOC ) )
								{	
									$vorlesung_tmp['veranstaltungID'] 	 = trim($veranstaltung['ID']);
									$vorlesung_tmp['veranstaltung'] 	 = trim($veranstaltung['veranstaltung']);
									$vorlesung_tmp['veranstaltung_abk']  = trim($veranstaltung['abk']);
								}
							}

							/* ---- PROFESSORENDATEN   ---*/
							$sql_6 = "SELECT DISTINCT * FROM `mdl_haw_professoren` WHERE `ID` = '".$vorlesungsVZ['professorID']."'";
							$result_6 = mysql_query( $sql_6, $this->conn );
							unset($professor);
							if ( $result_6 )
							{	
								while ( $professor = mysql_fetch_array( $result_6, MYSQL_ASSOC ) )
								{	
									$vorlesung_tmp['professor'] 	 = trim($professor['professor']);
									$vorlesung_tmp['professor_abk']  = trim($professor['abk']);
								}
							}
						
							/* ----STUDIENGANGDATEN   ---*/
							$sql_7 = "SELECT DISTINCT * FROM `mdl_haw_studiengaenge` WHERE `ID` = '".$vorlesungsVZ['studiengangID']."'";
							$result_7 = mysql_query( $sql_7, $this->conn );
							unset($studiengang);
							if ( $result_7 )
							{	
								while ( $studiengang = mysql_fetch_array( $result_7, MYSQL_ASSOC ) )
								{	
									$vorlesung_tmp['studiengangID']  = trim($studiengang['ID']);
									$vorlesung_tmp['vorlesung_bei']	 = trim($studiengang['studiengang']);
								}
							}
						}
					}
					
					$belegung['belegungsID']	= 	$row['ID'];			
					$belegung['veranstaltungID']= 	$row['professorID'];			
					$belegung['status']			= 	$row['status'];			
					$belegung['user'] 			= 	$user_tmp;			
					$belegung['veranstaltung']	=	$vorlesung_tmp;			
					$belegliste[] 				= 	$belegung;
				}   
			}
		}
		

				
		return $belegliste;
 	}

	function getBelegliste( $sort = "MATRIKELNR" )
	{
		$i = 0;
	    if($sort =="")
			$sort = "MATRIKELNR";

		$sql_1 = "SELECT * FROM `mdl_haw_belegliste`  ORDER BY `".$sort."`	ASC ";

		$result_1 = mysql_query( $sql_1, $this->conn );
		if ( $result_1 )
		{	
			while ( $row = mysql_fetch_array( $result_1, MYSQL_ASSOC ) )
			{
				$belegliste[$i]['status']			 	 		      =  $row['STATUS']; 		//  STATUS
				$belegliste[$i]['user']['matrikelnr'] 	 		      =  $row['MATRIKELNR']; 	//  MATRIKELNR
				$belegliste[$i]['user']['vorname']                    =  $row['VORNAME'];       //  VORNAME 	
				$belegliste[$i]['user']['nachname']                   =  $row['NACHNAME'];      //  NACHNAME 	
				$belegliste[$i]['user']['studiengang']                =  $row['STUDIENGANG'];   //  STUDIENGANG 
				$belegliste[$i]['user']['fachsemester']               =  $row['FACHSEMESTER'];  //  FACHSEMESTER 
				$belegliste[$i]['user']['mail']                       =  $row['MAIL'];          //  MAIL 	
				$belegliste[$i]['veranstaltung']['veranstaltung']     =  $row['VERANSTALTUNG']; //  VERANSTALTUNG
				$belegliste[$i]['veranstaltung']['veranstaltung_abk'] =  $row['VER_ABK'];       //  VER_ABK 	
				$belegliste[$i]['veranstaltung']['professor']         =  $row['PROFESSOR'];     //  PROFESSOR 	
				$belegliste[$i]['veranstaltung']['professor_abk']     =  $row['PROF_ABK'];      //  PROF_ABK 	
				$belegliste[$i]['veranstaltung']['vorlesung_bei']     =  $row['VORLESUNG_BEI']; //  VORLESUNG_BEI  
				$i++;
			}
		}
		return $belegliste;
	}

	function getBelegung()
	{
		$i = 0;
		$sql_1 = "SELECT * FROM `mdl_haw_vl_verzeichnis` ";
		$result_1 = mysql_query( $sql_1, $this->conn );
 
		// fachsemester  ID  sum  professorID  studiengangID  veranstaltungID
		if ( $result_1 )
		{	
			while ( $row = mysql_fetch_array( $result_1, MYSQL_ASSOC ) )
			{
				$tmp['belegung'] 		= $row;
				$tmp['studigengang'] 	= $this->getStudiengang( $row['studiengangID']);
				$tmp['professor'] 		= $this->getProfessor( $row['professorID']) ; 
				$tmp['veranstaltung'] 	= $this->getVeranstaltung( $row['veranstaltungID']) ;
				$tmp['anzahlstudenten']	= $this->getAnzahlStudentenInVeranstaltung( $row['ID'] );
				$anzahlGesamtStudenten += $tmp['anzahlstudenten'];

				$vorlesung['anzahlGesamtStudenten'] 			= $anzahlGesamtStudenten;
				$vorlesung[] 			= $tmp;
			}
		}
	 
	return $vorlesung;
	}
		
	function setDB( $action )
	{ 
		if( $action == "new" ) 
		{
	        $sql_1 = 'INSERT INTO `mdl_haw_vl_verzeichnis` ( `sum`, `professorID`, `studiengangID`, `veranstaltungID`) VALUES ( \'\', -1 , -1, -1)';
		}	

		if( $action == "delete" ) 
		{
			$sql_1 = "DELETE FROM `mdl_haw_vl_verzeichnis` WHERE `mdl_haw_vl_verzeichnis`.`ID` = ". $_GET[ 'id' ] .";"; 
		}	

		if( $action == "update" ) 
		{
			$sql_1 = "UPDATE `mdl_haw_vl_verzeichnis` SET `sum` = '".$_GET[ 'sum' ]."',   `".$_GET[ 'col' ]."` = '".$_GET[ 'val' ]."' WHERE `mdl_haw_vl_verzeichnis`.`ID` = ".$_GET[ 'id' ]." LIMIT 1 ;";
		}	
		$result_1 = mysql_query( $sql_1, $this->conn );
	}


	function getAllLists()
	{   
		$lists = "";

		$sql_1 = "SELECT * FROM `mdl_haw_professoren";
		$sql_2 = "SELECT * FROM `mdl_haw_studiengaenge";
		$sql_3 = "SELECT * FROM `mdl_haw_veranstaltungen";

		
		$result_1 = mysql_query($sql_1, $this->conn);
		$result_2 = mysql_query($sql_2, $this->conn);
		$result_3 = mysql_query($sql_3, $this->conn);
		

		if ( $result_1 )
		{
			while ( $row = mysql_fetch_array( $result_1, MYSQL_ASSOC ) )
			{
				$professoren[] = $row;
			}
			$lists[ 'professoren' ] = $professoren;
		}

		if ( $result_2 )
		{
			while ( $row = mysql_fetch_array( $result_2, MYSQL_ASSOC ) )
			{
				$studiengaenge[] = $row;
			}
			$lists[ 'studiengaenge' ] = $studiengaenge;
		}

		if ($result_3)
		{
			while ( $row = mysql_fetch_array( $result_3, MYSQL_ASSOC ) )
			{
				$veranstaltungen[] = $row;
			}
			$lists[ 'veranstaltungen' ] = $veranstaltungen;
		}
    print_r($lists); die();
		return $lists;
	}


	function uploadBelegliste()
	{
	if($_FILES["file"])
	{

		if ($_FILES["file"]["error"] > 0)
		{
			$out .= "Error: " . $_FILES["file"]["error"] . "<br />";
		}
		else
		{
			
			$csv = file($_FILES["file"]["tmp_name"]);
			for($i = 1; $i< sizeof($csv); $i++)
			{
				$V = explode(";", $csv[$i] );
				$sql_1 ="INSERT INTO `mdl_haw_belegliste` (  `STATUS`,`MATRIKELNR`, `VORNAME`, `NACHNAME`, `STUDIENGANG`, `FACHSEMESTER`, `MAIL`, `VERANSTALTUNG`, `VER_ABK`, `PROFESSOR`, `PROF_ABK` , `VORLESUNG_BEI` )";
				$sql_1 .=" VALUES ( '".$V['0']."', '".$V['1']."','".$V['2']."','".$V['3']."','".$V['4']."','".$V['5']."','".$V['6']."','".$V['7']."','".$V['8']."'  ,'".$V['9']."' ,'".$V['10']."' ,'".$V['11']."')";
				$result_1 = mysql_query( $sql_1, $this->conn );
			}
		}
	}
	else
	{	
		$out="<form action=\"#\" method=\"post\"
		enctype=\"multipart/form-data\">
		<label for=\"file\">Filename:</label>
		<input type=\"file\" name=\"file\" id=\"file\" /> 
		<br />
		<input type=\"submit\" name=\"submit\" value=\"Submit\" />
		</form>";
	}	
	return $out;
	}
	

	
	
	function getStudiengang( $studiengangID )
	{
		$sql_1 = "SELECT * FROM `mdl_haw_studiengaenge` WHERE `ID` = ".$studiengangID ;
		 
		$result_1 = mysql_query($sql_1, $this->conn);
		if ( $result_1 )
		{
			while ( $row = mysql_fetch_array( $result_1, MYSQL_ASSOC ) )
			{
				$studiengang = $row;
			}
		}
		return $studiengang;
	}
	
	function getProfessor( $professorID )
	{
		$sql_1 = "SELECT * FROM `mdl_haw_professoren` WHERE `ID` = ".$professorID ;
		 
		$result_1 = mysql_query($sql_1, $this->conn);
		if ( $result_1 )
		{
			while ( $row = mysql_fetch_array( $result_1, MYSQL_ASSOC ) )
			{
				$professor = $row;
			}
		}
		return $professor;
	}

	function getVeranstaltung( $veranstaltungID )
	{
		$sql_1 = "SELECT * FROM `mdl_haw_veranstaltungen` WHERE `ID` = ".$veranstaltungID ;
	 
		$result_1 = mysql_query($sql_1, $this->conn);
		if ( $result_1 )
		{
			while ( $row = mysql_fetch_array( $result_1, MYSQL_ASSOC ) )
			{
				$veranstaltung = $row;
			}
		}
		return $veranstaltung;
	}
	
	function getAnzahlStudentenInVeranstaltung( $ID )
	{
		$sql_1 = "SELECT *  FROM  `mdl_haw_wunschbelegliste` WHERE `professorID` = ".$ID ;
		
		$result_1 = mysql_query($sql_1, $this->conn);
		if ( $result_1 )
		{
			while ( $row = mysql_fetch_array( $result_1, MYSQL_ASSOC ) )
			{
				$ID2[] = $row;
			}
		}

		$data = sizeof($ID2);	
		return $data;
	}
	
	function deb($var)
	{
		echo "<pre>";
		print_r($var);
		echo "</pre>";
	}	
}
?>
