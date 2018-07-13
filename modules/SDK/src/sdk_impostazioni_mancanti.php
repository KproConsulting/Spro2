<?php 

/* kpro@bid200620181800 migrazione vte18.05 */

include_once('../../../config.inc.php'); 
chdir($root_directory); 
require_once('include/utils/utils.php'); 
include_once('vtlib/Vtiger/Module.php'); 
require_once('modules/SproCore/SDK/KpSDK.php'); 
$Vtiger_Utils_Log = true; 
global $adb, $table_prefix;
session_start(); 

KpSDK::aggiungiAreaImpostazioni($nome_blocco = 'LBL_STUDIO', $nome = "LBL_EXTWS_CONFIG", $icona = "extws_config.png", $nome_file = "ExtWSConfig", $descrizione = 'LBL_EXTWS_CONFIG_DESC');


?>
