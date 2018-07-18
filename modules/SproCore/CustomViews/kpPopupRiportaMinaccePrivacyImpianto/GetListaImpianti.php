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

if(isset($_GET['matricola'])){
    $matricola = addslashes(html_entity_decode(strip_tags($_GET['matricola']), ENT_QUOTES, $default_charset));
    $matricola = substr($matricola, 0, 255);
}
else{
    $matricola = "";
}

if(isset($_GET['nome_impianto'])){
    $nome_impianto = addslashes(html_entity_decode(strip_tags($_GET['nome_impianto']), ENT_QUOTES, $default_charset));
    $nome_impianto = substr($nome_impianto, 0, 255);
}
else{
    $nome_impianto = "";
}

if(isset($_GET['azienda'])){
    $azienda = addslashes(html_entity_decode(strip_tags($_GET['azienda']), ENT_QUOTES, $default_charset));
    $azienda = substr($azienda, 0, 255);
}
else{
    $azienda = "";
}

if(isset($_GET['stabilimento'])){
    $stabilimento = addslashes(html_entity_decode(strip_tags($_GET['stabilimento']), ENT_QUOTES, $default_charset));
    $stabilimento = substr($stabilimento, 0, 255);
}
else{
    $stabilimento = "";
}

$filtro = array("matricola" => $matricola,
                "nome_impianto" => $nome_impianto,
                "azienda" => $azienda,
                "stabilimento" => $stabilimento);

$rows = KpRiportaMinaccePrivacyImpianto::getListaImpianti($filtro);

$json = json_encode($rows);
print $json;

?>