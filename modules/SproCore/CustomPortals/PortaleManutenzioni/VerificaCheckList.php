<?php

/* kpro@tom190216 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 * @package manutenzioni
 * @version 1.0
 */

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $default_charset;

$rows = array();

if(isset($_GET['manutenzione'])){
    $manutenzione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['manutenzione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $manutenzione = substr($manutenzione,0,100);
    
    if(isset($_GET['riga_manutenzione'])){
        $riga_manutenzione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['riga_manutenzione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $riga_manutenzione = substr($riga_manutenzione,0,100);
        if($riga_manutenzione == ""){
            $riga_manutenzione = 0;
        }
    }
    else{
        $riga_manutenzione = 0;
    }

    $situazione_check_list = situazioneCheckList($manutenzione, $riga_manutenzione);

    $rows[] = array('situazione_check_list' => $situazione_check_list);
	
}
							
$json = json_encode($rows);
print $json;

function situazioneCheckList($manutenzione, $riga_manutenzione){
    global $adb, $table_prefix, $current_user, $default_charset;
    
    $situazione_check_list = "Positivo";
    
    $q_tipo_verifica = "SELECT 
                        es.esitimanutenzioniid esitimanutenzioniid,
                        es.tipo_verifica tipo_verifica,
                        es.esito_manutenzione esito_manutenzione,
                        es.note_esito note_esito,
                        tp.nome_verifica nome_verifica,
                        tp.kp_fermo_impianto kp_fermo_impianto,
                        es.description descrizione_esito,
                        tp.description description
                        FROM {$table_prefix}_esitimanutenzioni es
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = es.esitimanutenzioniid
                        INNER JOIN {$table_prefix}_tipiverifiche tp ON tp.tipiverificheid = es.tipo_verifica
                        INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = tp.tipiverificheid
                        WHERE ent.deleted = 0 AND es.manutenzione = ".$manutenzione;
    
    if($riga_manutenzione != null && $riga_manutenzione != '' && $riga_manutenzione != 0){
        $q_tipo_verifica .= " AND es.kp_riga_manutenz = ".$riga_manutenzione;
    }
                                          
    $res_tipo_verifica = $adb->query($q_tipo_verifica);
    $num_tipo_verifica = $adb->num_rows($res_tipo_verifica);

    for($i=0; $i<$num_tipo_verifica; $i++){
        $esitimanutenzioniid = $adb->query_result($res_tipo_verifica, $i, 'esitimanutenzioniid');
        $esitimanutenzioniid = html_entity_decode(strip_tags($esitimanutenzioniid), ENT_QUOTES,$default_charset);

        $tipo_verifica = $adb->query_result($res_tipo_verifica, $i, 'tipo_verifica');
        $tipo_verifica = html_entity_decode(strip_tags($tipo_verifica), ENT_QUOTES,$default_charset);

        $esito_manutenzione = $adb->query_result($res_tipo_verifica, $i, 'esito_manutenzione');
        $esito_manutenzione = html_entity_decode(strip_tags($esito_manutenzione), ENT_QUOTES,$default_charset);
        
        if($esito_manutenzione == "Negativo"){
            $situazione_check_list = "Negativo";
            return $situazione_check_list;
        }
        else if($situazione_check_list == "Positivo" && $esito_manutenzione == "N.D."){
            $situazione_check_list = "N.D.";
        }
            
    }
    
    return $situazione_check_list;
    
}
	
?>