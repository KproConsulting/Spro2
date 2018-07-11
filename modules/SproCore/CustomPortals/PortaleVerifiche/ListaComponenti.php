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
    
    if(isset($_GET['nome_componente'])){
        $nome_componente = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['nome_componente']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $nome_componente = substr($nome_componente,0,255);
    }
    else{
        $nome_componente = '';
    }
    
    if(isset($_GET['matricola_componente'])){
        $matricola_componente = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['matricola_componente']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $matricola_componente = substr($matricola_componente,0,255);
    }
    else{
        $matricola_componente = '';
    }
    
    if(isset($_GET['impianto'])){
        $impianto = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['impianto']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $impianto = substr($impianto,0,255);
    }
    else{
        $impianto = '';
    }
    
    if(isset($_GET['locazione'])){
        $locazione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['locazione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $locazione = substr($locazione,0,255);
    }
    else{
        $locazione = '';
    }
	
    $q_componenti = "SELECT t.* FROM 
                        ((SELECT 
                        comp.compimpiantoid compimpiantoid,
                        comp.nome_componente nome_componente,
                        comp.matricola matricola,
                        comp.locazione locazione,
                        comp.data_ult_manutenz data_ult_manutenz,
                        comp.stato_componente stato_componente,
                        imp.impianto_name impianto_name,
                        imp.matricola_impianto matricola_impianto
                        FROM {$table_prefix}_compimpianto comp
                        INNER JOIN {$table_prefix}_crmentityrel rel ON rel.crmid = comp.compimpiantoid
                        INNER JOIN {$table_prefix}_impianti imp ON imp.impiantiid = comp.impianto     
                        WHERE rel.module = 'CompImpianto' AND rel.relmodule = 'Manutenzioni' AND rel.relcrmid = ".$manutenzione.")
                        UNION
                        (SELECT 
                        comp2.compimpiantoid compimpiantoid,
                        comp2.nome_componente nome_componente,
                        comp2.matricola matricola,
                        comp2.locazione locazione,
                        comp2.data_ult_manutenz data_ult_manutenz,
                        comp2.stato_componente stato_componente,
                        imp2.impianto_name impianto_name,
                        imp2.matricola_impianto matricola_impianto
                        FROM {$table_prefix}_compimpianto comp2
                        INNER JOIN {$table_prefix}_crmentityrel rel2 ON rel2.relcrmid = comp2.compimpiantoid
                        INNER JOIN {$table_prefix}_impianti imp2 ON imp2.impiantiid = comp2.impianto    
                        WHERE rel2.relmodule = 'CompImpianto' AND rel2.module = 'Manutenzioni' AND rel2.crmid = ".$manutenzione.")) AS t
                        WHERE t.compimpiantoid != 0";
                             
    if($nome_componente != ""){
        
        $q_componenti .= " AND t.nome_componente LIKE '%".$nome_componente."%'";
        
    }   
    
    if($matricola_componente != ""){
        
        $q_componenti .= " AND t.matricola LIKE '%".$matricola_componente."%'";
        
    }
    
    if($impianto != ""){
        
        $q_componenti .= " AND t.impianto_name LIKE '%".$impianto."%'";
        
    }
    
    if($locazione != ""){
        
        $q_componenti .= " AND t.locazione LIKE '%".$locazione."%'";
        
    }
    
    $q_componenti .= " ORDER BY t.impianto_name ASC, t.nome_componente ASC";
    
    $res_componenti = $adb->query($q_componenti);
    $num_componenti = $adb->num_rows($res_componenti);
	
    for($i=0; $i<$num_componenti; $i++){
        $compimpiantoid = $adb->query_result($res_componenti, $i, 'compimpiantoid');
        $compimpiantoid = html_entity_decode(strip_tags($compimpiantoid), ENT_QUOTES,$default_charset);
        
        $nome_componente = $adb->query_result($res_componenti, $i, 'nome_componente');
        $nome_componente = html_entity_decode(strip_tags($nome_componente), ENT_QUOTES,$default_charset);
        
        $matricola = $adb->query_result($res_componenti, $i, 'matricola');
        $matricola = html_entity_decode(strip_tags($matricola), ENT_QUOTES,$default_charset);
        
        $locazione = $adb->query_result($res_componenti, $i, 'locazione');
        $locazione = html_entity_decode(strip_tags($locazione), ENT_QUOTES,$default_charset);

        $data_ult_manutenz = $adb->query_result($res_componenti, $i, 'data_ult_manutenz');
        $data_ult_manutenz = html_entity_decode(strip_tags($data_ult_manutenz), ENT_QUOTES,$default_charset);
        if($data_ult_manutenz != NULL && $data_ult_manutenz != "0000-00-00" && $data_ult_manutenz != '' && $data_ult_manutenz != "1900-01-01"){
            list($anno_manutenzione,$mese_manutenzione,$giorno_manutenzione) = explode("-",$data_ult_manutenz);
            $data_ult_manutenz_inv = date("d-m-Y",mktime(0,0,0,$mese_manutenzione,$giorno_manutenzione,$anno_manutenzione));
        }
        else{
            $data_ult_manutenz = "";
            $data_ult_manutenz_inv = "";
        }

        $stato_componente = $adb->query_result($res_componenti, $i, 'stato_componente');
        $stato_componente = html_entity_decode(strip_tags($stato_componente), ENT_QUOTES,$default_charset);
        
        $impianto_name = $adb->query_result($res_componenti, $i, 'impianto_name');
        $impianto_name = html_entity_decode(strip_tags($impianto_name), ENT_QUOTES,$default_charset);
        
        $matricola_impianto = $adb->query_result($res_componenti, $i, 'matricola_impianto');
        $matricola_impianto = html_entity_decode(strip_tags($matricola_impianto), ENT_QUOTES,$default_charset);
        
        $situazione_check_list = situazioneCheckList($manutenzione,$compimpiantoid);

        $rows[] = array('compimpiantoid' => $compimpiantoid,
                        'nome_componente' => $nome_componente,
                        'matricola' => $matricola,
                        'locazione' => $locazione,
                        'data_ult_manutenz_inv' => $data_ult_manutenz_inv,
                        'impianto_name' => $impianto_name,
                        'matricola_impianto' => $matricola_impianto,
                        'situazione_check_list' => $situazione_check_list);

    }
	
}
							
$json = json_encode($rows);
print $json;

function situazioneCheckList($manutenzione,$componente){
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
                        WHERE ent.deleted = 0 AND es.componente = ".$componente." AND es.manutenzione = ".$manutenzione;
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