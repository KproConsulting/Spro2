<?php

/* kpro@tom25112015 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2015, Kpro Consulting Srl
 * @package portaleCustomTeam
 * @version 1.0
 */
include_once('../../../../config.inc.php'); 
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

if (!isset($_SESSION['authenticated_user_id'])) {
    header("Location: " . $site_URL . "/index.php?module=Accounts&action=index");
}
else{
    $utente = isset($_SESSION['authenticated_user_id']);
}

$rows = array(); 

if( isset($_REQUEST['id']) && isset($_REQUEST['id_prodotto']) && isset($_REQUEST['quantita']) ){
    $id = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['id']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $id = substr($id,0,255);
    
    $id_prodotto = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['id_prodotto']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $id_prodotto = substr($id_prodotto,0,255);
    
    $quantita = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['quantita']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $quantita = substr($quantita,0,255);
    
    /* kpro@bid090520170900 */
    if( isset($_REQUEST['data']) ){
        $data = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $data = substr($data,0,255);
    }
    else{
        $data = "";
    }
    /* kpro@bid090520170900 end */
    
    $riga = CRMEntity::getInstance('ListaDPI');  
    $riga->column_fields['consegnadpi'] = $id; 
    $riga->column_fields['assigned_user_id'] = $utente;
    $riga->column_fields['dpi'] = $id_prodotto;
    /* kpro@bid090520170900 */
    if($data != "" && $data != null){
        $riga->column_fields['scadenza'] = $data;
    }
    /* kpro@bid090520170900 end */
    $riga->column_fields['stato_listadpi'] = "Attivo";
    $riga->column_fields['quantita_consegnata'] = $quantita; 		
    $riga->save('ListaDPI', $longdesc=true, $offline_update=false, $triggerEvent=false);
    
    $new_riga = $riga->id; 

    UpdateCreator($new_riga, $utente);
        
    $rows[] = array("id" => $new_riga);
}

$json = json_encode($rows);
print $json;

function UpdateCreator($record, $creator){
    global $adb, $table_prefix;

    if($record != 0 && $record != '' && $record != null){
        $update_creator = "UPDATE {$table_prefix}_crmentity 
                        SET smcreatorid = {$creator}
                        WHERE crmid = ".$record;
        $adb->query($update_creator);
    }
}

?>