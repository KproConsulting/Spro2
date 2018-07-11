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

    $q_manutenzione = "SELECT 
                        man.tipo_manutenzione tipo_manutenzione, 
                        man.data_manutenzione data_manutenzione, 
                        man.stato_manutenzione stato_manutenzione,
                        man.manutenzione_name manutenzione_name,
                        man.description description,
                        man.kp_lavoro_caldo kp_lavoro_caldo,
                        man.kp_lavoro_quota kp_lavoro_quota,
                        man.kp_lavoro_spaz_co kp_lavoro_spaz_co,
                        DATE(ent.createdtime) data_creazione,
                        TIME(ent.createdtime) ora_creazione,
                        DATE(ent.modifiedtime) data_modifica,
                        TIME(ent.modifiedtime) ora_modifica
                        FROM {$table_prefix}_manutenzioni man
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = man.manutenzioniid
                        WHERE man.manutenzioniid = ".$manutenzione;

    $res_manutenzione = $adb->query($q_manutenzione);
    if($adb->num_rows($res_manutenzione)>0){
        $tipo_manutenzione = $adb->query_result($res_manutenzione,0,'tipo_manutenzione');
        $tipo_manutenzione = html_entity_decode(strip_tags($tipo_manutenzione), ENT_QUOTES,$default_charset);

        $data_manutenzione = $adb->query_result($res_manutenzione,0,'data_manutenzione');
        $data_manutenzione = html_entity_decode(strip_tags($data_manutenzione), ENT_QUOTES,$default_charset);
        if($data_manutenzione != NULL && $data_manutenzione != "0000-00-00" && $data_manutenzione != ""){
            list($anno_manutenzione,$mese_manutenzione,$giorno_manutenzione) = explode("-",$data_manutenzione);
            $data_manutenzione = date("d/m/Y",mktime(0,0,0,$mese_manutenzione,$giorno_manutenzione,$anno_manutenzione));
        }
        else{
            $data_manutenzione = "";
        }

        $stato_manutenzione = $adb->query_result($res_manutenzione,0,'stato_manutenzione');
        $stato_manutenzione = html_entity_decode(strip_tags($stato_manutenzione), ENT_QUOTES,$default_charset);

        $manutenzione_name = $adb->query_result($res_manutenzione,0,'manutenzione_name');
        $manutenzione_name = html_entity_decode(strip_tags($manutenzione_name), ENT_QUOTES,$default_charset);

        $description = $adb->query_result($res_manutenzione,0,'description');
        $description = html_entity_decode(strip_tags($description), ENT_QUOTES,$default_charset);
        
        $lavoro_caldo = $adb->query_result($res_manutenzione,0,'kp_lavoro_caldo');
        $lavoro_caldo = html_entity_decode(strip_tags($lavoro_caldo), ENT_QUOTES,$default_charset);
        if($lavoro_caldo == '1'){
            $lavoro_caldo = "Si";
        }
        else{
            $lavoro_caldo = "No";
        }
        
        $lavoro_quota = $adb->query_result($res_manutenzione,0,'kp_lavoro_quota');
        $lavoro_quota = html_entity_decode(strip_tags($lavoro_quota), ENT_QUOTES,$default_charset);
        if($lavoro_quota == '1'){
            $lavoro_quota = "Si";
        }
        else{
            $lavoro_quota = "No";
        }
        
        $lavoro_spaz_co = $adb->query_result($res_manutenzione,0,'kp_lavoro_spaz_co');
        $lavoro_spaz_co = html_entity_decode(strip_tags($lavoro_spaz_co), ENT_QUOTES,$default_charset);
        if($lavoro_spaz_co == '1'){
            $lavoro_spaz_co = "Si";
        }
        else{
            $lavoro_spaz_co = "No";
        }
        
        $frequenza_checklist = $adb->query_result($res_manutenzione,0,'frequenza_checklist');
        $frequenza_checklist = html_entity_decode(strip_tags($frequenza_checklist), ENT_QUOTES,$default_charset);
        if($frequenza_checklist == '' || $frequenza_checklist == null){
            $frequenza_checklist = "1";
        }
        
        $frequenza_checklist = (int)$frequenza_checklist;

        $data_creazione = $adb->query_result($res_manutenzione, 0, 'data_creazione');
        $data_creazione = html_entity_decode(strip_tags($data_creazione), ENT_QUOTES, $default_charset);
        if($data_creazione != NULL && $data_creazione != "0000-00-00" && $data_creazione != ""){
            list($anno, $mese, $giorno) = explode("-", $data_creazione);
            $data_creazione = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
        }
        else{
            $data_creazione = "";
        }

        $data_modifica = $adb->query_result($res_manutenzione, 0, 'data_modifica');
        $data_modifica = html_entity_decode(strip_tags($data_modifica), ENT_QUOTES, $default_charset);
        if($data_modifica != NULL && $data_modifica != "0000-00-00" && $data_modifica != ""){
            list($anno, $mese, $giorno) = explode("-", $data_modifica);
            $data_modifica = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
        }
        else{
            $data_modifica = "";
        }

        $ora_creazione = $adb->query_result($res_manutenzione, 0, 'ora_creazione');
        $ora_creazione = html_entity_decode(strip_tags($ora_creazione), ENT_QUOTES, $default_charset);

        $ora_modifica = $adb->query_result($res_manutenzione, 0, 'ora_modifica');
        $ora_modifica = html_entity_decode(strip_tags($ora_modifica), ENT_QUOTES, $default_charset);

        $data_creazione = $data_creazione." ".$ora_creazione;
        $data_modifica = $data_modifica." ".$ora_modifica;

    }

    $rows[] = array('tipo_manutenzione' => $tipo_manutenzione,
                    'data_manutenzione' => $data_manutenzione,
                    'stato_manutenzione' => $stato_manutenzione,
                    'manutenzione_name' => $manutenzione_name,
                    'description' => $description,
                    'lavoro_caldo' => $lavoro_caldo,
                    'lavoro_quota' => $lavoro_quota,
                    'lavoro_spaz_co' => $lavoro_spaz_co,
                    'data_creazione' => $data_creazione,
                    'data_modifica' => $data_modifica);
	
}
							
$json = json_encode($rows);
print $json;
	
?>
