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
session_start();

if (!isset($_SESSION['authenticated_user_id'])) {
    header("Location: ".$site_URL."/index.php");
	die; 
}

$current_user->id = $_SESSION['authenticated_user_id'];

$rows = array();

if(isset($_GET['manutenzione']) && isset($_GET['tecnico']) && isset($_GET['tipo_aggiornamento']) && isset($_GET['stato_rilevazione']) && isset($_GET['data']) && isset($_GET['ora']) && isset($_GET['time'])){
    $manutenzione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['manutenzione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $manutenzione = substr($manutenzione,0,100);

    $tecnico = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['tecnico']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $tecnico = substr($tecnico,0,100);
    
    $tipo_aggiornamento = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['tipo_aggiornamento']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $tipo_aggiornamento = substr($tipo_aggiornamento,0,100);
    
    $stato_rilevazione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['stato_rilevazione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $stato_rilevazione = substr($stato_rilevazione,0,100);
    
    $data = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $data = substr($data,0,100);
    if($data != ''){
        list($giorno,$mese,$anno) = explode("/",$data);
        
        $giorno = (int)$giorno;
        $giorno = str_pad($giorno, 2, "0", STR_PAD_LEFT);
        
        $mese = (int)$mese;
        $mese = str_pad($mese, 2, "0", STR_PAD_LEFT);
        
        $anno = (int)$anno;
        
        $data = date("Y-m-d",mktime(0,0,0,$mese,$giorno,$anno));
    }
    
    $ora = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['ora']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $ora = substr($ora,0,100);
    
    $time = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['time']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $time = substr($time,0,255);
    
    if(isset($_GET['causale'])){
        $causale = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['causale']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $causale = substr($causale,0,255);
    }
    else{
        $causale = "";
    }
    
    if(isset($_GET['descrizione'])){
        $descrizione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['descrizione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    }
    else{
        $descrizione = "";
    }
    
    $utente = $current_user->id;
    if($utente == null || $utente == ''){
        $utente = 1;
    }        
     
    $stato_manutenzione = "";
            
    //printf("Ora stato: %s data: %s ora: %s",$stato_rilevazione,$data,$ora);die;
    
    $q_tecnico = "SELECT 
                    tec.cognome cognome,
                    tec.kp_nome kp_nome,
                    tec.kp_related_to kp_related_to,
                    tec.kp_tipo_tec_man kp_tipo_tec_man,
                    tec.colore colore,
                    tec.kp_cons_in_carico kp_cons_in_carico,
                    tec.kp_delega kp_delega
                    FROM {$table_prefix}_tecnicimanutentori tec
                    WHERE tec.tecnicimanutentoriid = ".$tecnico;
    $res_tecnico = $adb->query($q_tecnico);
    if($adb->num_rows($res_tecnico)>0){
        
        $cognome = $adb->query_result($res_tecnico,0,'cognome');
        $cognome = html_entity_decode(strip_tags($cognome), ENT_QUOTES,$default_charset);
        
        $nome = $adb->query_result($res_tecnico,0,'kp_nome');
        $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES,$default_charset);
        
        $related_to = $adb->query_result($res_tecnico,0,'kp_related_to');
        $related_to = html_entity_decode(strip_tags($related_to), ENT_QUOTES,$default_charset);
        
        $tipo_tec_man = $adb->query_result($res_tecnico,0,'kp_tipo_tec_man');
        $tipo_tec_man = html_entity_decode(strip_tags($tipo_tec_man), ENT_QUOTES,$default_charset);
        
        $colore = $adb->query_result($res_tecnico,0,'colore');
        $colore = html_entity_decode(strip_tags($colore), ENT_QUOTES,$default_charset);
        
        $cons_in_carico = $adb->query_result($res_tecnico,0,'kp_cons_in_carico');
        $cons_in_carico = html_entity_decode(strip_tags($cons_in_carico), ENT_QUOTES,$default_charset);
        
        $delega = $adb->query_result($res_tecnico,0,'kp_delega');
        $delega = html_entity_decode(strip_tags($delega), ENT_QUOTES,$default_charset);
        
    }
    
    if($tipo_aggiornamento == 'Start'){
        
        chiusuraRilevazionePrecedente($manutenzione,$tecnico,$data,$ora,$time);
        
        $tipo_reg_tempi = 'Ore lavorate';
        
        $nuova_rilevazione = CRMEntity::getInstance('TempiManutenzioni'); 
        $nuova_rilevazione->column_fields['assigned_user_id'] = $utente;
        $nuova_rilevazione->column_fields['soggetto'] = $data." ".$ora." ".$cognome." ".$nome." (Start)";
        $nuova_rilevazione->column_fields['kp_manutenzione'] = $manutenzione;
        $nuova_rilevazione->column_fields['kp_risorsa'] = $tecnico;
        $nuova_rilevazione->column_fields['kp_tipo_reg_tempi'] = $tipo_reg_tempi;
        $nuova_rilevazione->column_fields['kp_stato_reg_tempi'] = 'In corso';
        $nuova_rilevazione->column_fields['kp_data_inizio_reg'] = $data;
        $nuova_rilevazione->column_fields['kp_ora_inizio_reg'] = $ora;
        $nuova_rilevazione->column_fields['kp_time_start'] = $time;
        if($causale != "" && $causale != "--none--"){
            $nuova_rilevazione->column_fields['kp_causale_tempi'] = $causale;
        }
        if($descrizione != ""){
            $nuova_rilevazione->column_fields['description'] = $descrizione;
        }
        $nuova_rilevazione->save('TempiManutenzioni', $longdesc=true, $offline_update=false, $triggerEvent=false); 
        $nuova_rilevazione_id = $nuova_rilevazione->id;
        
        $stato_manutenzione = "In esecuzione";
        
        $upd_manutenzione = "UPDATE {$table_prefix}_manutenzioni SET
                                stato_manutenzione = '".$stato_manutenzione."'
                                WHERE manutenzioniid = ".$manutenzione;
        $adb->query($upd_manutenzione);
        
        calcolaDataInizioEffettiva($manutenzione);
        
    }
    elseif($tipo_aggiornamento == 'Pausa'){
        
        chiusuraRilevazionePrecedente($manutenzione,$tecnico,$data,$ora,$time);
        
        $tipo_reg_tempi = 'Pausa';
        
        $nuova_rilevazione = CRMEntity::getInstance('TempiManutenzioni'); 
        $nuova_rilevazione->column_fields['assigned_user_id'] = $utente;
        $nuova_rilevazione->column_fields['soggetto'] = $data." ".$ora." ".$cognome." ".$nome." (Pausa)";
        $nuova_rilevazione->column_fields['kp_manutenzione'] = $manutenzione;
        $nuova_rilevazione->column_fields['kp_risorsa'] = $tecnico;
        $nuova_rilevazione->column_fields['kp_tipo_reg_tempi'] = $tipo_reg_tempi;
        $nuova_rilevazione->column_fields['kp_stato_reg_tempi'] = 'In corso';
        $nuova_rilevazione->column_fields['kp_data_inizio_reg'] = $data;
        $nuova_rilevazione->column_fields['kp_ora_inizio_reg'] = $ora;
        $nuova_rilevazione->column_fields['kp_time_start'] = $time;
        if($causale != "" && $causale != "--none--"){
            $nuova_rilevazione->column_fields['kp_causale_tempi'] = $causale;
        }
        if($descrizione != ""){
            $nuova_rilevazione->column_fields['description'] = $descrizione;
        }
        $nuova_rilevazione->save('TempiManutenzioni', $longdesc=true, $offline_update=false, $triggerEvent=false); 
        $nuova_rilevazione_id = $nuova_rilevazione->id;
        
    }
    elseif($tipo_aggiornamento == 'Stop'){
        
        chiusuraRilevazionePrecedente($manutenzione,$tecnico,$data,$ora,$time);
        
    }
    elseif($tipo_aggiornamento == 'Termina'){
        
        chiusuraRilevazionePrecedente($manutenzione,$tecnico,$data,$ora,$time);
        
        calcolaDataFineEffettiva($manutenzione);
        
        calcolaDataScadenzaManutenzione($manutenzione);

        verificaRinnovoAutomatico($manutenzione);
        
    }
    else{
        $nuova_rilevazione_id = 0;
        $tipo_reg_tempi = '';
    }

    $rows[] = array('nuova_rilevazione_id' => $nuova_rilevazione_id,
                    'tipo_reg_tempi' => $tipo_reg_tempi,
                    'data' => $data,
                    'ora' => $ora,
                    'time' => $time,
                    'tipo_aggiornamento' => $tipo_aggiornamento,
                    'stato_manutenzione' => $stato_manutenzione);

    $json = json_encode($rows);
    print $json;

}

function chiusuraRilevazionePrecedente($manutenzione,$tecnico,$data,$ora,$time){
    global $adb, $table_prefix, $default_charset;
    
    $q_tempi = "SELECT 
                    temp.tempimanutenzioniid tempimanutenzioniid,
                    temp.kp_tipo_reg_tempi kp_tipo_reg_tempi,
                    temp.kp_causale_tempi kp_causale_tempi, 
                    temp.kp_data_inizio_reg kp_data_inizio_reg,
                    temp.kp_ora_inizio_reg kp_ora_inizio_reg,
                    temp.kp_time_start kp_time_start
                    FROM {$table_prefix}_tempimanutenzioni temp
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = temp.tempimanutenzioniid
                    WHERE ent.deleted = 0 AND temp.kp_stato_reg_tempi = 'In corso' AND temp.kp_manutenzione = ".$manutenzione." AND temp.kp_risorsa = ".$tecnico."
                    ORDER BY temp.tempimanutenzioniid DESC";

    $res_tempi = $adb->query($q_tempi);
    if($adb->num_rows($res_tempi)>0){
        
        $tempimanutenzioniid_prec = $adb->query_result($res_tempi,0,'tempimanutenzioniid');
        $tempimanutenzioniid_prec = html_entity_decode(strip_tags($tempimanutenzioniid_prec), ENT_QUOTES,$default_charset);
        $tempimanutenzioniid_prec = addslashes($tempimanutenzioniid_prec);
        
        $data_inizio_reg_prec = $adb->query_result($res_tempi,0,'kp_data_inizio_reg');
        $data_inizio_reg_prec = html_entity_decode(strip_tags($data_inizio_reg_prec), ENT_QUOTES,$default_charset);
        $data_inizio_reg_prec = addslashes($data_inizio_reg_prec);
        
        $ora_inizio_reg_prec = $adb->query_result($res_tempi,0,'kp_ora_inizio_reg');
        $ora_inizio_reg_prec = html_entity_decode(strip_tags($ora_inizio_reg_prec), ENT_QUOTES,$default_charset);
        $ora_inizio_reg_prec = addslashes($ora_inizio_reg_prec);
        
        $time_start = $adb->query_result($res_tempi,0,'kp_time_start');
        $time_start = html_entity_decode(strip_tags($time_start), ENT_QUOTES,$default_charset);
        $time_start = addslashes($time_start);
        
        $tempo_totale = (((int)$time - (int)$time_start) / 1000) / 60;
        
        $upd_ril_prec = "UPDATE {$table_prefix}_tempimanutenzioni SET
                            kp_data_fine_reg = '".$data."',
                            kp_ora_fine_reg = '".$ora."',
                            kp_time_end = ".$time.",
                            kp_tempo_totale = ".$tempo_totale.",
                            kp_stato_reg_tempi = 'Chiuso'
                            WHERE tempimanutenzioniid = ".$tempimanutenzioniid_prec;
        
        $adb->query($upd_ril_prec);
        
    }
    
}

function calcolaDataInizioEffettiva($manutenzione){
    global $adb, $table_prefix, $default_charset;
    
    $q_time_start = "SELECT 
                        tem.tempimanutenzioniid tempimanutenzioniid,
                        tem.kp_data_inizio_reg kp_data_inizio_reg,
                        tem.kp_ora_inizio_reg kp_ora_inizio_reg
                        FROM {$table_prefix}_tempimanutenzioni tem
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tem.tempimanutenzioniid
                        WHERE ent.deleted = 0 AND tem.kp_manutenzione = ".$manutenzione."
                        ORDER BY tem.kp_time_start ASC";
    $res_time_start = $adb->query($q_time_start);
    if($adb->num_rows($res_time_start)>0){
        
        $tempimanutenzioniid = $adb->query_result($res_time_start,0,'tempimanutenzioniid');
        $tempimanutenzioniid = html_entity_decode(strip_tags($tempimanutenzioniid), ENT_QUOTES,$default_charset);
        $tempimanutenzioniid = addslashes($tempimanutenzioniid);
        
        $data_inizio_reg = $adb->query_result($res_time_start,0,'kp_data_inizio_reg');
        $data_inizio_reg = html_entity_decode(strip_tags($data_inizio_reg), ENT_QUOTES,$default_charset);
        $data_inizio_reg = addslashes($data_inizio_reg);
        if($data_inizio_reg != null && $data_inizio_reg != ""){
            list($anno,$mese,$giorno) = explode("-",$data_inizio_reg);    
            $data_inizio_reg = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
        }
        else{
            $data_inizio_reg = "";
        }
        
        $ora_inizio_reg = $adb->query_result($res_time_start,0,'kp_ora_inizio_reg');
        $ora_inizio_reg = html_entity_decode(strip_tags($ora_inizio_reg), ENT_QUOTES,$default_charset);
        $ora_inizio_reg = addslashes($ora_inizio_reg);
        
    }
    else{
        $data_inizio_reg = "";
        $ora_inizio_reg = "";
    }
    
    $upd_manutenzione = "UPDATE {$table_prefix}_manutenzioni SET
                            kp_data_inizio_ef = '".$data_inizio_reg." ".$ora_inizio_reg."'
                            WHERE manutenzioniid = ".$manutenzione;
    $adb->query($upd_manutenzione);
    
}

function calcolaDataFineEffettiva($manutenzione){
    global $adb, $table_prefix, $default_charset;
    
    $q_time_end = "SELECT 
                        tem.tempimanutenzioniid tempimanutenzioniid,
                        tem.kp_data_fine_reg kp_data_fine_reg,
                        tem.kp_ora_fine_reg kp_ora_fine_reg
                        FROM vte_tempimanutenzioni tem
                        INNER JOIN vte_crmentity ent ON ent.crmid = tem.tempimanutenzioniid
                        WHERE ent.deleted = 0 AND tem.kp_stato_reg_tempi = 'Chiuso' AND (tem.kp_time_end != 0 AND tem.kp_time_end IS NOT NULL AND tem.kp_time_end != '') AND tem.kp_manutenzione = ".$manutenzione."
                        ORDER BY tem.kp_time_end DESC";
    
    $res_time_end = $adb->query($q_time_end);
    if($adb->num_rows($res_time_end)>0){
        
        $tempimanutenzioniid = $adb->query_result($res_time_end,0,'tempimanutenzioniid');
        $tempimanutenzioniid = html_entity_decode(strip_tags($tempimanutenzioniid), ENT_QUOTES,$default_charset);
        $tempimanutenzioniid = addslashes($tempimanutenzioniid);
        
        $data_fine_reg = $adb->query_result($res_time_end,0,'kp_data_fine_reg');
        $data_fine_reg = html_entity_decode(strip_tags($data_fine_reg), ENT_QUOTES,$default_charset);
        $data_fine_reg = addslashes($data_fine_reg);
        if($data_fine_reg != null && $data_fine_reg != ""){
            list($anno,$mese,$giorno) = explode("-",$data_fine_reg);    
            $data_fine_reg = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
        }
        else{
            $data_fine_reg = "";
        }
        
        $ora_fine_reg = $adb->query_result($res_time_end,0,'kp_ora_fine_reg');
        $ora_fine_reg = html_entity_decode(strip_tags($ora_fine_reg), ENT_QUOTES,$default_charset);
        $ora_fine_reg = addslashes($ora_fine_reg);
        
    }
    else{
        $data_fine_reg = "";
        $ora_fine_reg = "";
    }
    
    $upd_manutenzione = "UPDATE {$table_prefix}_manutenzioni SET
                            kp_data_fine_ef = '".$data_fine_reg." ".$ora_fine_reg."',
                            stato_manutenzione = 'Eseguita'
                            WHERE manutenzioniid = ".$manutenzione;
    
    $adb->query($upd_manutenzione);
    
}

function calcolaDataScadenzaManutenzione($manutenzione){
    global $adb, $table_prefix, $default_charset;
    
    $q_manutenzione = "SELECT 
                        tipo_manutenzione, 
                        data_manutenzione
                        FROM {$table_prefix}_manutenzioni
                        INNER JOIN {$table_prefix}_crmentity ON crmid = manutenzioniid
                        WHERE manutenzioniid = ".$manutenzione;

    $res_manutenzione = $adb->query($q_manutenzione);
    if($adb->num_rows($res_manutenzione)>0){
        $tipo_manutenzione = $adb->query_result($res_manutenzione,0,'tipo_manutenzione');
        $tipo_manutenzione = html_entity_decode(strip_tags($tipo_manutenzione), ENT_QUOTES,$default_charset);

        $data_manutenzione = $adb->query_result($res_manutenzione,0,'data_manutenzione');
        $data_manutenzione = html_entity_decode(strip_tags($data_manutenzione), ENT_QUOTES,$default_charset);
        
    }

    $q_righe_manutenzione = "SELECT 
                                righe.kprighemanutenzioniid kprighemanutenzioniid,
                                righe.frequenza_checklist frequenza_checklist,
                                righe.kp_data_scadenza kp_data_scadenza
                                FROM {$table_prefix}_kprighemanutenzioni righe
                                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = righe.kprighemanutenzioniid
                                WHERE ent.deleted = 0 AND righe.kp_manutenzione = ".$manutenzione;
    $res_righe_manutenzione = $adb->query($q_righe_manutenzione);
    $num_righe_manutenzione = $adb->num_rows($res_righe_manutenzione);
    for($i = 0; $i < $num_righe_manutenzione; $i++){

        $kprighemanutenzioniid = $adb->query_result($res_righe_manutenzione, $i, 'kprighemanutenzioniid');
        $kprighemanutenzioniid = html_entity_decode(strip_tags($kprighemanutenzioniid), ENT_QUOTES, $default_charset);

        $frequenza_checklist = $adb->query_result($res_righe_manutenzione, $i, 'frequenza_checklist');
        $frequenza_checklist = html_entity_decode(strip_tags($frequenza_checklist), ENT_QUOTES, $default_charset);
        if($frequenza_checklist == '' || $frequenza_checklist == null){
            $frequenza_checklist = "I";
        }

        $data_scadenza = $adb->query_result($res_righe_manutenzione, $i, 'kp_data_scadenza');
        $data_scadenza = html_entity_decode(strip_tags($data_scadenza), ENT_QUOTES, $default_charset);
        if($data_scadenza == '' || $data_scadenza == null){
            $data_scadenza = "";
        }

        calcolaDataScadenzaRigaManutenzione($manutenzione, $tipo_manutenzione, $data_manutenzione, $kprighemanutenzioniid, $frequenza_checklist, $data_scadenza);

    }
    
}

function calcolaDataScadenzaRigaManutenzione($manutenzione, $tipo_manutenzione, $data_manutenzione, $kprighemanutenzioniid, $frequenza_checklist, $data_scadenza){
    global $adb, $table_prefix, $default_charset;

    if($tipo_manutenzione == "Extra"){

        $data_scad_manut = "";

    }
    else{

        $tipo_incremento = $frequenza_checklist[0];

        $incremento = substr($frequenza_checklist, 1); 

        $incremento = (int) $incremento;

        $data_odierna = date("Y-m-d");
		list($anno, $mese, $giorno) = explode("-", $data_odierna);

        switch($tipo_incremento) {
            case "I":
                $data_scad_manut = "";
                break;
            case "D":
                $data_scad_manut = date("Y-m-d",mktime(0, 0, 0, $mese, $giorno + $incremento, $anno));
                break;
            case "M":
                $data_scad_manut = date("Y-m-d",mktime(0, 0, 0, $mese + $incremento, $giorno, $anno));
                break;
            default:
                $data_scad_manut = "";
        }

    }

    if($data_scadenza == null || $data_scadenza == "" || $data_scadenza == "0000-00-00"){

        $upd = "UPDATE {$table_prefix}_kprighemanutenzioni SET
                kp_data_scadenza = '".$data_scad_manut."'
                WHERE kprighemanutenzioniid = ".$kprighemanutenzioniid;
        $adb->query($upd);
        
    }

}

function verificaRinnovoAutomatico($manutenzione){
    global $adb, $table_prefix, $default_charset;
    
    $q_manutenzione = "SELECT 
                        kp_rinnovo_automati
                        FROM {$table_prefix}_manutenzioni
                        INNER JOIN {$table_prefix}_crmentity ON crmid = manutenzioniid
                        WHERE manutenzioniid = ".$manutenzione;

    $res_manutenzione = $adb->query($q_manutenzione);
    if($adb->num_rows($res_manutenzione)>0){

        $rinnovo_automatico = $adb->query_result($res_manutenzione,0,'kp_rinnovo_automati');
        $rinnovo_automatico = html_entity_decode(strip_tags($rinnovo_automatico), ENT_QUOTES,$default_charset);
        if($rinnovo_automatico == '1'){
            
            rinnovaAutomaticamenteManutenzione($manutenzione);

        }
        
    }

}

function rinnovaAutomaticamenteManutenzione($manutenzione){
    global $adb, $table_prefix, $default_charset;

    require_once('modules/SproCore/KpClass/KpManutenzioni_class.php');
    require_once('modules/SproCore/KpClass/KpRigheManutenzioni_class.php');

    $q_manutenzione = "SELECT 
                        man.tipo_manutenzione tipo_manutenzione, 
                        man.data_manutenzione data_manutenzione,
                        man.kp_tipologia_inter kp_tipologia_inter,
                        man.kp_lavoro_caldo kp_lavoro_caldo,
                        man.kp_lavoro_quota kp_lavoro_quota,
                        man.kp_lavoro_spaz_co kp_lavoro_spaz_co,
                        man.kp_rinnovo_automati kp_rinnovo_automati,
                        man.description description,
                        ent.smownerid smownerid
                        FROM {$table_prefix}_manutenzioni man
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = man.manutenzioniid
                        WHERE man.manutenzioniid = ".$manutenzione;

    $res_manutenzione = $adb->query($q_manutenzione);
    if($adb->num_rows($res_manutenzione)>0){
        $tipo_manutenzione = $adb->query_result($res_manutenzione,0,'tipo_manutenzione');
        $tipo_manutenzione = html_entity_decode(strip_tags($tipo_manutenzione), ENT_QUOTES,$default_charset);

        $data_manutenzione = $adb->query_result($res_manutenzione,0,'data_manutenzione');
        $data_manutenzione = html_entity_decode(strip_tags($data_manutenzione), ENT_QUOTES,$default_charset);

        $tipologia_inter = $adb->query_result($res_manutenzione,0,'kp_tipologia_inter');
        $tipologia_inter = html_entity_decode(strip_tags($tipologia_inter), ENT_QUOTES,$default_charset);

        $lavoro_caldo = $adb->query_result($res_manutenzione,0,'kp_lavoro_caldo');
        $lavoro_caldo = html_entity_decode(strip_tags($lavoro_caldo), ENT_QUOTES,$default_charset);

        $lavoro_quota = $adb->query_result($res_manutenzione,0,'kp_lavoro_quota');
        $lavoro_quota = html_entity_decode(strip_tags($lavoro_quota), ENT_QUOTES,$default_charset);

        $lavoro_spaz_co = $adb->query_result($res_manutenzione,0,'kp_lavoro_spaz_co');
        $lavoro_spaz_co = html_entity_decode(strip_tags($lavoro_spaz_co), ENT_QUOTES,$default_charset);

        $rinnovo_automati = $adb->query_result($res_manutenzione,0,'kp_rinnovo_automati');
        $rinnovo_automati = html_entity_decode(strip_tags($rinnovo_automati), ENT_QUOTES,$default_charset);

        $description = $adb->query_result($res_manutenzione,0,'description');
        $description = html_entity_decode(strip_tags($description), ENT_QUOTES,$default_charset);

        $assegnatario = $adb->query_result($res_manutenzione,0,'smownerid');
        $assegnatario = html_entity_decode(strip_tags($assegnatario), ENT_QUOTES,$default_charset);
        
    }

    $q_righe_manutenzione = "SELECT 
                                righe.kprighemanutenzioniid kprighemanutenzioniid,
                                righe.frequenza_checklist frequenza_checklist,
                                righe.kp_data_scadenza kp_data_scadenza,
                                righe.kp_componente kp_componente,
                                righe.kp_check_list kp_check_list,
                                comp.impianto impianto
                                FROM {$table_prefix}_kprighemanutenzioni righe
                                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = righe.kprighemanutenzioniid
                                INNER JOIN {$table_prefix}_compimpianto comp ON comp.compimpiantoid  = righe.kp_componente
                                WHERE ent.deleted = 0 AND righe.kp_manutenzione = ".$manutenzione."
                                ORDER BY righe.frequenza_checklist";
    $res_righe_manutenzione = $adb->query($q_righe_manutenzione);
    $num_righe_manutenzione = $adb->num_rows($res_righe_manutenzione);
    for($i = 0; $i < $num_righe_manutenzione; $i++){

        $kprighemanutenzioniid[$i] = $adb->query_result($res_righe_manutenzione, $i, 'kprighemanutenzioniid');
        $kprighemanutenzioniid[$i] = html_entity_decode(strip_tags($kprighemanutenzioniid[$i]), ENT_QUOTES, $default_charset);

        $frequenza_checklist[$i] = $adb->query_result($res_righe_manutenzione, $i, 'frequenza_checklist');
        $frequenza_checklist[$i] = html_entity_decode(strip_tags($frequenza_checklist[$i]), ENT_QUOTES, $default_charset);

        $data_scadenza[$i] = $adb->query_result($res_righe_manutenzione, $i, 'kp_data_scadenza');
        $data_scadenza[$i] = html_entity_decode(strip_tags($data_scadenza[$i]), ENT_QUOTES, $default_charset);

        $componente[$i] = $adb->query_result($res_righe_manutenzione, $i, 'kp_componente');
        $componente[$i] = html_entity_decode(strip_tags($componente[$i]), ENT_QUOTES, $default_charset);

        $check_list[$i] = $adb->query_result($res_righe_manutenzione, $i, 'kp_check_list');
        $check_list[$i] = html_entity_decode(strip_tags($check_list[$i]), ENT_QUOTES, $default_charset);
        $check_list[$i] = trim($check_list[$i]);
        $check_list[$i] = (int) $check_list[$i];

        $impianto[$i] = $adb->query_result($res_righe_manutenzione, $i, 'impianto');
        $impianto[$i] = html_entity_decode(strip_tags($impianto[$i]), ENT_QUOTES, $default_charset);

        if($i == 0 || $frequenza_checklist[$i] != $frequenza_checklist[$i -1]){

            $nuovo_numero_generazione = recuperaNumeratoreManutenzione();

        }

        $manutenzioneid[$i] = 0;

        $nuova_manutenzione = new KpManutenzioni_class();
        $nuova_manutenzione->manutenzione_name = "Man. Prog. ".$nuovo_numero_generazione;
        $nuova_manutenzione->numero_generazione = $nuovo_numero_generazione;
        $nuova_manutenzione->stato_manutenzione = "Creata";
        $nuova_manutenzione->tipo_manutenzione = $tipo_manutenzione;
        $nuova_manutenzione->data_manutenzione = $data_scadenza[$i];
        $nuova_manutenzione->tipologia_inter = $tipologia_inter;
        $nuova_manutenzione->lavoro_caldo = $lavoro_caldo;
        $nuova_manutenzione->lavoro_quota = $lavoro_quota;
        $nuova_manutenzione->lavoro_spaz_co = $lavoro_spaz_co;
        $nuova_manutenzione->rinnovo_automati = $rinnovo_automati;
        $nuova_manutenzione->description = $description;
        $nuova_manutenzione->assegnatario = $assegnatario;
        $manutenzioneid[$i] = $nuova_manutenzione->salva();

        if($manutenzioneid[$i] != null && $manutenzioneid[$i] != "" && $manutenzioneid[$i] != 0){
            
            $nuova_riga_manutenzione = new KpRigheManutenzioni_class();
            $nuova_riga_manutenzione->componente = $componente[$i];
            $nuova_riga_manutenzione->check_list = $check_list[$i];
            $nuova_riga_manutenzione->manutenzione = $manutenzioneid[$i];
            $nuova_riga_manutenzione->frequenza_checklist = $frequenza_checklist[$i];
            $nuova_riga_manutenzione->data_scadenza = "";
            $nuova_riga_manutenzione->assegnatario = $assegnatario;
            $nuova_riga_manutenzione->salva();

            $nuova_manutenzione->aggiornaTempoPrevistoManutenzione();

            $q_ver_relazione_impianto = "SELECT *FROM {$table_prefix}_crmentityrel 
                                            WHERE crmid = ".$manutenzioneid[$i]." AND module = 'Manutenzioni' AND relcrmid = ".$impianto[$i]." AND relmodule = 'Impianti'";
            
            $res_ver_relazione_impianto = $adb->query($q_ver_relazione_impianto);
            if($adb->num_rows($res_ver_relazione_impianto) == 0){

                $insert_rel1 = "INSERT INTO {$table_prefix}_crmentityrel (crmid, module, relcrmid, relmodule)
                                VALUES (".$manutenzioneid[$i].", 'Manutenzioni', ".$impianto[$i].", 'Impianti')";
                $adb->query($insert_rel1);

            }

            unset($nuova_manutenzione);
            unset($nuova_riga_manutenzione);
        }
        else{
            unset($nuova_manutenzione);
        }

    }

}

function recuperaNumeratoreManutenzione(){
    global $adb, $table_prefix, $default_charset;

    $nuovo_numero_generazione = 1;

    $q_numero_generazione = "SELECT COALESCE(MAX(numero_generazione), 0) ultimo_numero_generazione							
                            FROM {$table_prefix}_manutenzioni man
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = man.manutenzioniid
                            WHERE ent.deleted = 0";

    $res_numero_generazione = $adb->query($q_numero_generazione);
    if($adb->num_rows($res_numero_generazione)>0){
        $ultimo_numero_generazione = $adb->query_result($res_numero_generazione,0,'ultimo_numero_generazione');
        $nuovo_numero_generazione = $ultimo_numero_generazione + 1;
    }

    return $nuovo_numero_generazione;

}
	
?>
