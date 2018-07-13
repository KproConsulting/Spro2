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

echo "<br>CONVERSIONE TASSE<br>";

//Modifico il campo da picklist a picklist multilinguaggio
$q = "SELECT picklistid
    FROM {$table_prefix}_picklist
    WHERE name = 'kp_tasse'";
$res = $adb->query($q);
if($adb->num_rows($res) > 0){
    $picklistid = $adb->query_result($res, 0, 'picklistid');
    $picklistid = html_entity_decode(strip_tags($picklistid), ENT_QUOTES, $default_charset);
    if($picklistid != "" && $picklistid != null){
        //Modifico l'uitype del campo per renderlo una picklist multilinguaggio
        $q = "UPDATE {$table_prefix}_field
            SET uitype = 1015
            WHERE fieldname = 'kp_tasse'";
        $adb->query($q);

        //Copio i valori nella tabella dei valori delle picklist multilinguaggio
        $q = "SELECT * 
            FROM {$table_prefix}_kp_tasse";
        $res = $adb->query($q);
        $num = $adb->num_rows($res);
        if($num > 0){
            for($i = 0; $i < $num; $i++){
                $valore = $adb->query_result($res, $i, 'kp_tasse');
                $valore = html_entity_decode(strip_tags($valore), ENT_QUOTES, $default_charset);
                if($valore != "" && $valore != null){
                    if($valore == 'ALIQUOTA IVA 22%'){
                        $codice = '22';
                    }
                    else{
                        $codice = 'E'.$i;
                    }

                    echo "- $valore -> $codice<br>";

                    KpSDK::aggiungiAPickingListMultilinguaggio($nome_campo = "kp_tasse", $codice = $codice, $valore = $valore);

                }
            }
        }

        //Cancello le tabelle e i record che riguardano la picklist standard
        $q = "DROP TABLE {$table_prefix}_kp_tasse";
        $adb->query($q);

        $q = "DROP TABLE {$table_prefix}_kp_tasse_seq";
        $adb->query($q);

        $q = "DELETE FROM {$table_prefix}_picklist
            WHERE picklistid = ".$picklistid;
        $adb->query($q);

        $q = "DELETE FROM {$table_prefix}_role2picklist
            WHERE picklistid = ".$picklistid;
        $adb->query($q);
    }
}

echo "<br>CONVERSIONE TASSE: Aziende<br>";

//Aggiorno i valori del campo con il codice corrispondente al valore
$q = "SELECT accountid,
    kp_tasse
    FROM {$table_prefix}_account";
$res = $adb->query($q);
$num = $adb->num_rows($res);
if($num > 0){
    for($i = 0; $i < $num; $i++){
        $id = $adb->query_result($res, $i, 'accountid');
        $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

        $valore = $adb->query_result($res, $i, 'kp_tasse');
        $valore = html_entity_decode(strip_tags($valore), ENT_QUOTES, $default_charset);
        if($valore != "" && $valore != null){

            $codice = getCodicePicklistMultilinguaggio('kp_tasse', $valore);

            echo "- Azienda $id $valore -> $codice<br>";

            $q = "UPDATE {$table_prefix}_account
                SET kp_tasse = '{$codice}'
                WHERE accountid = ".$id;
            $adb->query($q);
        }
    }
}

echo "<br>CONVERSIONE TASSE: Preventivi<br>";

$q = "SELECT quoteid,
    kp_tasse
    FROM {$table_prefix}_quotes";
$res = $adb->query($q);
$num = $adb->num_rows($res);
if($num > 0){
    for($i = 0; $i < $num; $i++){
        $id = $adb->query_result($res, $i, 'quoteid');
        $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

        $valore = $adb->query_result($res, $i, 'kp_tasse');
        $valore = html_entity_decode(strip_tags($valore), ENT_QUOTES, $default_charset);
        if($valore != "" && $valore != null){

            $codice = getCodicePicklistMultilinguaggio('kp_tasse', $valore);

            echo "- Preventivo $id $valore -> $codice<br>";

            $q = "UPDATE {$table_prefix}_quotes
                SET kp_tasse = '{$codice}'
                WHERE quoteid = ".$id;
            $adb->query($q);
        }
    }
}

echo "<br>CONVERSIONE TASSE: Ordini di Vendita<br>";

$q = "SELECT salesorderid,
    kp_tasse
    FROM {$table_prefix}_salesorder";
$res = $adb->query($q);
$num = $adb->num_rows($res);
if($num > 0){
    for($i = 0; $i < $num; $i++){
        $id = $adb->query_result($res, $i, 'salesorderid');
        $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

        $valore = $adb->query_result($res, $i, 'kp_tasse');
        $valore = html_entity_decode(strip_tags($valore), ENT_QUOTES, $default_charset);
        if($valore != "" && $valore != null){

            $codice = getCodicePicklistMultilinguaggio('kp_tasse', $valore);

            echo "- OdV $id $valore -> $codice<br>";

            $q = "UPDATE {$table_prefix}_salesorder
                SET kp_tasse = '{$codice}'
                WHERE salesorderid = ".$id;
            $adb->query($q);
        }
    }
}

echo "<br>CONVERSIONE TASSE: Fatture<br>";

$q = "SELECT invoiceid,
    kp_tasse
    FROM {$table_prefix}_invoice";
$res = $adb->query($q);
$num = $adb->num_rows($res);
if($num > 0){
    for($i = 0; $i < $num; $i++){
        $id = $adb->query_result($res, $i, 'invoiceid');
        $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

        $valore = $adb->query_result($res, $i, 'kp_tasse');
        $valore = html_entity_decode(strip_tags($valore), ENT_QUOTES, $default_charset);
        if($valore != "" && $valore != null){

            $codice = getCodicePicklistMultilinguaggio('kp_tasse', $valore);

            echo "- Fattura $id $valore -> $codice<br>";

            $q = "UPDATE {$table_prefix}_invoice
                SET kp_tasse = '{$codice}'
                WHERE invoiceid = ".$id;
            $adb->query($q);
        }
    }
}

function getCodicePicklistMultilinguaggio($field, $value){
    global $adb, $table_prefix, $default_charset;

    $code = '';

    $q = "SELECT code
        FROM tbl_s_picklist_language
        WHERE field = '{$field}'
        AND value = '{$value}'
        GROUP BY code";
    $res = $adb->query($q);
    if($adb->num_rows($res) > 0){
        $code = $adb->query_result($res, 0, 'code');
        $code = html_entity_decode(strip_tags($code), ENT_QUOTES, $default_charset);
        if($code == null){
            $code = '';
        }
    }

    return $code;
}

?>
