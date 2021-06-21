 <?php
    require_once( "../inc/db.class.php" );
    require_once( "../inc/db.IDM.class.php" );
 
  $dbIDM                  = new DBIDM();
  $db                     = new DB( $dbIDM );
 
  $id = $_GET['id'];
  $state = $_GET['p'] ;
  
  $db->setWishState( $id, $state );

  if ($state == 1) { $stateN = 'W'; }
  if ($state == 2) { $stateN = 'B'; }
  
  echo  '<a href="#" class="'.$stateN.'" onclick="return false;" id="alink'.$id.'a">'.$stateN.'</a>';
 
?>
