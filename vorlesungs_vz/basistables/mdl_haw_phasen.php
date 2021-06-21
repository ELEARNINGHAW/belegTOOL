<?php

$err_level = error_reporting(0);
session_start();
require_once("mte/mte.php");
$tabledit = new MySQLtabledit();

		#####################
		# required settings #
		#####################

# database settings:
$tabledit->database = 'beleg';
$tabledit->host = 'localhost';
$tabledit->user = 'beleg';
$tabledit->pass = 'belegbd';

# table of the database you want to edit:
$tabledit->table = 'mdl_haw_phasen';

# the primary key of the table (must be AUTO_INCREMENT):
$tabledit->primary_key = 'phase';

# the fields you want to see in "list view"
# Always add the primary key (`employeeNumber)`: 
$tabledit->fields_in_list_view = array('ID', 'phase','timestamp','desc');

		#####################
		# optional settings #
		#####################

# Head of the page (<h1>head_1<h1>):
$tabledit->head_1 = "Phasen";

# language ('en' for English, 'nl' for Dutch):
$tabledit->language = 'en';

# numbers of rows/records in "list view": 
$tabledit->num_rows_list_view = 100;

# required fields in edit or add record: 
$tabledit->fields_required = array( 'timestamp');

# Fields you want to edit (remove this to edit all the fields).
#$tabledit->fields_to_edit = array(''timestamp',''','email','job');



# Make selectlist on inputfield based on another table
# in this example: `employees`.`job` is based on `jobs`.`jobname`: 
/*
$tabledit->lookup_table = array(
	'job' => array(
		'query' => "SELECT `id`, `jobname` FROM `jobs`;",
		'option_value' => 'id',
		'option_text' => 'jobname'
	)
);
*/

$tabledit->width_editor = '100%';
$tabledit->width_input_fields = '500px';
$tabledit->width_text_fields = '498px';
$tabledit->height_text_fields = '200px';


# warning no .htaccess ('on' or 'off'): 
$tabledit->no_htaccess_warning = 'off';


		####################################
		# connect, show editor, disconnect #
		####################################

$tabledit->database_connect();

echo "<!DOCTYPE html>
<html lang='en'>
<head>

	<meta charset='utf-8'>

	<title>Editor</title>
	</head>
	<body>
";

$tabledit->do_it();

echo "
	</body>
	</html>"
;

$tabledit->database_disconnect();
?>



<?php session_start();?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<title>Phasen</title>

<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>

<?php

if (isset($_SESSION["r"])) 
{

require_once("../../inc/ini/phpMyEdit.ini.php");

$opts['tb'] = 'mdl_haw_phasen';


$opts['fdd']['ID'] = array(
  'name'     => 'ID',
  'select'   => 'T',
  'options'  => 'AVCPDR', // auto increment
  'maxlen'   => 3,
  'default'  => '0',
  'sort'     => true
);

$opts['fdd']['phase'] = array(
  'name'     => 'phase',
  'select'   => 'T',
  'maxlen'   => 3,
  'sort'     => true
  );

$opts['fdd']['timestamp'] = array(
  'name'     => 'timestamp',
  'select'   => 'T',
  'maxlen'   => 11,
  'sort'     => true
);

$opts['fdd']['desc'] = array(
  'name'     => 'desc',
  'select'   => 'T',
  'maxlen'   => 150,
  'sort'     => true
  );


// Now important call to phpMyEdit
require_once 'phpMyEdit.class.php';
new phpMyEdit($opts);
}

else
{
	die("ERROR");
}


?>

</body>
</html>
