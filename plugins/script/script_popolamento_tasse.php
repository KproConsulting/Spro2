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

echo "<br>POPOLAMENTO TASSE<br>";

$q = "UPDATE {$table_prefix}_account
    SET kp_tasse = '22'";
$adb->query($q);


$q = "UPDATE {$table_prefix}_quotes
    SET kp_tasse = '22'";
$adb->query($q);


$q = "UPDATE {$table_prefix}_salesorder
    SET kp_tasse = '22'";
$adb->query($q);


$q = "UPDATE {$table_prefix}_invoice
    SET kp_tasse = '22'";
$adb->query($q);

?>
