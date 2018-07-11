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

$rows = array();

if(isset($_GET['record'])){
    $record = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['record']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $record = substr($record,0,255);

    $q_testata = "SELECT dpi.consegnadpiid,
                dpi.consegna_no,cont.contactid,
                cont.lastname,cont.firstname,
                acc.accountid,acc.accountname,
                dpi.data_consegna,dpi.tipo_consegna,
                stab.stabilimentiid,stab.nome_stabilimento,
                dpi.description,dpi.stato_consegna
                FROM {$table_prefix}_consegnadpi dpi
                INNER JOIN {$table_prefix}_contactdetails cont ON cont.contactid = dpi.contatto
                INNER JOIN {$table_prefix}_account acc ON acc.accountid = dpi.azienda
                INNER JOIN {$table_prefix}_stabilimenti stab ON stab.stabilimentiid = dpi.stabilimento
                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = dpi.consegnadpiid
                WHERE ent.deleted = 0 AND dpi.consegnadpiid = ".$record;

    $res_testata = $adb->query($q_testata);

    if($adb->num_rows($res_testata) > 0){
        $consegnadpiid = $adb->query_result($res_testata, 0, 'consegnadpiid');
        $consegnadpiid = html_entity_decode(strip_tags($consegnadpiid), ENT_QUOTES, $default_charset);

        $consegna_no = $adb->query_result($res_testata, 0, 'consegna_no');
        $consegna_no = html_entity_decode(strip_tags($consegna_no), ENT_QUOTES, $default_charset);
        
        $contactid = $adb->query_result($res_testata, 0, 'contactid');
        $contactid = html_entity_decode(strip_tags($contactid), ENT_QUOTES, $default_charset);
        
        $lastname = $adb->query_result($res_testata, 0, 'lastname');
        $lastname = html_entity_decode(strip_tags($lastname), ENT_QUOTES, $default_charset);
        
        $firstname = $adb->query_result($res_testata, 0, 'firstname');
        $firstname = html_entity_decode(strip_tags($firstname), ENT_QUOTES, $default_charset);
        
        $accountid = $adb->query_result($res_testata, 0, 'accountid');
        $accountid = html_entity_decode(strip_tags($accountid), ENT_QUOTES, $default_charset);
        
        $accountname = $adb->query_result($res_testata, 0, 'accountname');
        $accountname = html_entity_decode(strip_tags($accountname), ENT_QUOTES, $default_charset);
        
        $data_consegna = $adb->query_result($res_testata, 0, 'data_consegna');
        $data_consegna = html_entity_decode(strip_tags($data_consegna), ENT_QUOTES, $default_charset);

        $data_consegna_datetime = new DateTime($data_consegna);
        $data_consegna_inv = $data_consegna_datetime->format("d/m/Y");
        
        $tipo_consegna = $adb->query_result($res_testata, 0, 'tipo_consegna');
        $tipo_consegna = html_entity_decode(strip_tags($tipo_consegna), ENT_QUOTES, $default_charset);
        
        $stabilimentiid = $adb->query_result($res_testata, 0, 'stabilimentiid');
        $stabilimentiid = html_entity_decode(strip_tags($stabilimentiid), ENT_QUOTES, $default_charset);
        
        $nome_stabilimento = $adb->query_result($res_testata, 0, 'nome_stabilimento');
        $nome_stabilimento = html_entity_decode(strip_tags($nome_stabilimento), ENT_QUOTES, $default_charset);
        
        $description = $adb->query_result($res_testata, 0, 'description');
        $description = html_entity_decode(strip_tags($description), ENT_QUOTES, $default_charset);
        
        $stato_consegna = $adb->query_result($res_testata, 0, 'stato_consegna');
        $stato_consegna = html_entity_decode(strip_tags($stato_consegna), ENT_QUOTES, $default_charset);
        if($stato_consegna == "" || $stato_consegna == null){
            $stato_consegna = "Non Confermata";
        }

        $rows[] = array(
            "id" => $consegnadpiid,
            "numero" => $consegna_no,
            "data" => $data_consegna,
            "data_inv"=>$data_consegna_inv,
            "tipo_consegna" => $tipo_consegna,
            "stato" => $stato_consegna,
            "risorsa" => $contactid,
            "nome_risorsa" => $lastname." ".$firstname,
            "azienda" => $accountid,
            "nome_azienda" => $accountname,
            "stabilimento" => $stabilimentiid,
            "nome_stabilimento" => $nome_stabilimento,
            "descrizione" => $description
        );
        
    }
}

$json = json_encode($rows);
print $json;
?>