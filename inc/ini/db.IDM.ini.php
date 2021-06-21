	<?php
	$liveserver = "lernserver.el.haw-hamburg.de"; # Adresse des Liveservers ohne "www",
	# "www.meine.server.de = meine.server.de"
   
	if ( $_SERVER[ 'SERVER_NAME' ] == $liveserver )
	{   
		# Werte auf Produktivserver einstellen!
		$user 	= "IDM"; 			# Username f�r die MySQL-DB
		$pass 	= "IDMbd"; 		    # Kennwort f�r die MySQL-DB
		$server = "localhost";      # Adresse/IP/Name des MySQL-Server
		$dbase 	= "idm2"; 			# Name der standardm��ig verwendeten Datenbank
	}

	else 
	{
		# Werte auf Entwicklungsserver einstellen!
		$user 	= "IDM2"; 			# Username f�r die MySQL-DB
		$pass 	= "IDM2"; 		    # Kennwort f�r die MySQL-DB
		$server = "localhost";      # Adresse/IP/Name des MySQL-Server
		$dbase 	= "idm2"; 			# Name der standardm��ig verwendeten Datenbank
	}
	
	?>
