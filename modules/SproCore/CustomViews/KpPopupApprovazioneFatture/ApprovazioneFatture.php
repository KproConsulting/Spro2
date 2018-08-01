<?php

/* kpro@bid27062018 */

/**
 * @author Bidese Jacopo
 * @copyright (c) 2018, Kpro Consulting Srl
 * @package Approvazione Fatture
 * @version 1.0
 */

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $default_charset, $site_URL;
session_start();

if(!isset($_SESSION['authenticated_user_id'])){
    header("Location: ".$site_URL."/index.php?module=Accounts&action=index");
}

$current_user->id = $_SESSION['authenticated_user_id'];

$rows = array();
if(isset($_REQUEST['record'])){
    $record = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_REQUEST['record']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $record = substr($record,0,100);

    $q = "SELECT inv.kp_tipo_documento,
        inv.kp_avviso_fattura
        FROM {$table_prefix}_invoice inv
        INNER JOIN {$table_prefix}_account acc ON acc.accountid = inv.accountid
        LEFT JOIN {$table_prefix}_contactdetails cont ON cont.contactid = inv.contactid
        INNER JOIN {$table_prefix}_kpbusinessunit bu ON bu.kpbusinessunitid = inv.kp_business_unit
        INNER JOIN {$table_prefix}_modpagamento pag ON pag.modpagamentoid = inv.mod_pagamento
        INNER JOIN {$table_prefix}_inventoryproductrel righe ON righe.id = inv.invoiceid
        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = inv.invoiceid
        INNER JOIN {$table_prefix}_crmentity ent1 ON ent1.crmid = acc.accountid
        INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = bu.kpbusinessunitid
        INNER JOIN {$table_prefix}_crmentity ent3 ON ent3.crmid = pag.modpagamentoid
        WHERE ent.deleted = 0 AND ent1.deleted = 0 AND ent2.deleted = 0 AND ent3.deleted = 0
        AND inv.invoicestatus IN ('AutoCreated', 'Created', 'Creata Proforma')
        AND inv.invoiceid = ".$record; 

    $res = $adb->query($q);
    if($adb->num_rows($res) > 0){

        $tipo_documento = $adb->query_result($res, 0, 'kp_tipo_documento');
        $tipo_documento = html_entity_decode(strip_tags($tipo_documento), ENT_QUOTES, $default_charset);

        $avviso_fattura = $adb->query_result($res, 0, 'kp_avviso_fattura');
        $avviso_fattura = html_entity_decode(strip_tags($avviso_fattura), ENT_QUOTES, $default_charset);

        if($avviso_fattura == '1'){
            $stato_fattura = 'Approvata Proforma';
        }
        else{
            $stato_fattura = 'Approved';
        }

        $focus_fattura = CRMEntity::getInstance('Invoice');
        $focus_fattura->retrieve_entity_info($record, "Invoice"); 
        $focus_fattura->column_fields['invoicestatus'] = $stato_fattura;
        $focus_fattura->column_fields['kp_controllo_approv'] = '1';
        $focus_fattura->mode = 'edit';
        $focus_fattura->id = $record;
        $_REQUEST['ajxaction'] = 'DETAILVIEW'; /* kpro@bid010820181550 */ //avoid rows save
        $focus_fattura->save('Invoice', $longdesc=true, $offline_update=false, $triggerEvent=false);

        $rows[] = array(
            "res"=>"ok"
        );
    }
    else{
        $rows[] = array(
            "res"=>"error"
        );
    }
	
}

$json = json_encode($rows);
print $json;
	
?>
