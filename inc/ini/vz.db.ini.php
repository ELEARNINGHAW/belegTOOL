	<?php
	$liveserver = "lernserver.ls.haw-hamburg.de"; # Adresse des Liveservers ohne "www",
	# "www.meine.server.de = meine.server.de"
   
	if ( $_SERVER[ 'SERVER_NAME' ] == $liveserver )
	{   
		# Werte auf Produktivserver einstellen!
		$server = $opts['hn'] = "localhost"; 	# Adresse/IP/Name des MySQL-Server
		$user 	= $opts['un'] = "beleg"; 		# Username für die MySQL-DB
		$pass 	= $opts['pw'] = "belegbd"; 		# Kennwort für die MySQL-DB
		$dbase 	= $opts['db'] = "beleg"; 		# Name der standardmäßig verwendeten Datenbank
	}   

	else 
	{
		# Werte auf Entwicklungsserver einstellen!
		$server = $opts['hn'] = "localhost"; 	# Adresse/IP/Name des MySQL-Server
		$user 	= $opts['un'] = "beleg"; 		# Username für die MySQL-DB
		$pass 	= $opts['pw'] = "belegbd"; 		# Kennwort für die MySQL-DB
		$dbase 	= $opts['db'] = "beleg"; 		# Name der standardmäßig verwendeten Datenbank
	}
	?>
