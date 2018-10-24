<?php

/* kpro@tom20170628 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

require_once('KpBPMN.php');

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

//print_r($_SESSION);die;

$rows = array();

if (!isset($_SESSION['authenticated_user_id'])) {

    $json = json_encode($rows);
    print $json;
    die;

    header("Location: ". $site_URL."/index.php");
	die; 
}
$current_user->id = $_SESSION['authenticated_user_id'];

if(isset($_REQUEST['record'])){
    $record = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_REQUEST['record']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
    $record = substr($record, 0, 100);

    //$bpmn = Zend_Json::decode($_REQUEST['bpmn']);
    $bpmn = $_REQUEST['bpmn'];
    
    if( trim($bpmn) != ""){
        KpBPMN::aggiornaBPMNcrm($record, $bpmn);
    }
    else{
        die;
    }

    $rows[] = array("bpmn" => $bpmn);

}

$json = json_encode($rows);
print $json;

?>