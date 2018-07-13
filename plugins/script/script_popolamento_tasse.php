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

KpSDK::aggiungiAPickingListMultilinguaggio($nome_campo = "kp_tasse", $codice = '22', $valore = 'ALIQUOTA IVA 22%');

KpSDK::aggiungiAPickingListMultilinguaggio($nome_campo = "kp_tasse", $codice = 'E1', $valore = 'ESENTE ART.15 DPR 633/72');

KpSDK::aggiungiAPickingListMultilinguaggio($nome_campo = "kp_tasse", $codice = 'E2', $valore = 'NON IMP.ART.8 DPR 633/72');

$q = "UPDATE {$table_prefix}_account
    SET kp_tasse = '22'
    WHERE accountid = ".$id;
$adb->query($q);


$q = "UPDATE {$table_prefix}_quotes
    SET kp_tasse = '22'
    WHERE quoteid = ".$id;
$adb->query($q);


$q = "UPDATE {$table_prefix}_salesorder
    SET kp_tasse = '22'
    WHERE salesorderid = ".$id;
$adb->query($q);


$q = "UPDATE {$table_prefix}_invoice
    SET kp_tasse = '22'
    WHERE invoiceid = ".$id;
$adb->query($q);

?>
