<?php
class PHYDB extends SQLite3
{
	var $db;

	function PHYDB(  $db = null )
	{
		if( $db )
		{ 	$this->db = $db;
		}
		else
		{	$this->db = new SQLite3( '../inc/physikp.s3db' );		
			if( $this->db )
			{  
			}
			else
			{	return( "<b>KEINE Verbindung zur Datenbank möglich</b>" );
			}
		}
	}
	
	function getLabVeranstaltung( $sgID, $laborder = 0  )
	{
        if ( $laborder == 0 )
        {
			$SQL = "SELECT * FROM laborveranstaltung where ID=\"".$sgID."\"";
        }
        else 
        {
            $SQL = "SELECT * FROM laborveranstaltung where sgID=\"".$sgID."\" AND  laborder =\"".$laborder ."\"  ";
        }
        $result =  $this->db->query($SQL );
     
        while ( $veranstaltung = $result->fetchArray() )	// TODO -- kann optimiert werden --
        {	$list[]				= $veranstaltung;
        }

		return $list;
    }
	
	function getBelegung( )
	{
			$list = null;
            $SQL = "SELECT * FROM terminbelegung where studiID=\"".$_SESSION['IDMuser']['akennung']."\"";
	  
			$result =  $this->db->query($SQL );
            if( $result )
			while ( $veranstaltung  = $result->fetchArray() )	// TODO -- kann optimiert werden --
			{	$list[]				= $veranstaltung;
			}
			return $list;
    }
	
	
	function insertNewTermin( )// Neuer Satz in DB anlegen mit Standarddaten   
	{
		$SQL = "INSERT INTO terminbelegung   
		( timestamp
		, laborterminID 
		, listenplatz
		, studiID
		, status
		) 
		
		VALUES
		( 
  		  \"" .date( "d.m.Y H:i") ."\",
  		  \"".$_SESSION['laborterminID']."\",
		  \"". $_SESSION['listenplatz']."\",
		  \"". $_SESSION['IDMuser']['akennung']."\",
		  \"". $_SESSION['status']."\"
		)";
		echo ($SQL);
		return  $this->db->exec( $SQL );
	}
    

	
}
?>