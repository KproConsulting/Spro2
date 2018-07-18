<?php 

die("Togliere il die!");

include_once(__DIR__.'/../../config.inc.php'); 
chdir($root_directory); 
require_once('include/utils/utils.php'); 
include_once('vtlib/Vtiger/Module.php'); 
require_once('modules/SproCore/SDK/KpSDK.php'); 
$Vtiger_Utils_Log = true; 
global $adb, $table_prefix;
session_start(); 

echo "<br>POPOLAMENTO TIPO DOCUMENTO FATTURE<br>";


$q = "UPDATE {$table_prefix}_invoice
    SET kp_tipo_documento = 'Fattura'";
$adb->query($q);

?>
