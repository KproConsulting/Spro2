<?php

/* kpro@tom28112017 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

require_once('modules/SproCore/CustomViews/kpPopupRiportaMinaccePrivacyImpianto/KpRiportaMinaccePrivacyImpianto.php');

$rows = array();

if (!isset($_SESSION['authenticated_user_id'])) {

    $json = json_encode($rows);
    print $json;
    die;

    header("Location: ". $site_URL."/index.php");
	die; 
}
$current_user->id = $_SESSION['authenticated_user_id'];


if( isset($_GET['record']) && isset($_GET['copia_da']) ){
    $record = addslashes(html_entity_decode(strip_tags($_GET['record']), ENT_QUOTES,$default_charset));
    $record = substr($record, 0, 100);

    $copia_da = addslashes(html_entity_decode(strip_tags($_GET['copia_da']), ENT_QUOTES,$default_charset));
    $copia_da = substr($copia_da, 0, 100);

    KpRiportaMinaccePrivacyImpianto::clonaMinaccePrivacyDaAltroImpianto($copia_da, $record);

    $rows[] = array("return" => "ok");

}

$json = json_encode($rows);
print $json;

?>