<?php

/* kpro@tom140316 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 * @package ganttPianificazioni
 * @version 2.0
 */

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

if(!isset($_SESSION['authenticated_user_id'])){
    header("Location: ".$site_URL."/index.php?module=Accounts&action=index");
}
else{
    $current_user->id = $_SESSION['authenticated_user_id'];
}

$rows = array();

if(isset($_GET['elemento_id']) && isset($_GET['modifica']) && isset($_GET['pianificazione_corrente'])){
    $elemento_id = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['elemento_id']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $elemento_id = substr($elemento_id,0,100);

    $modifica = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['modifica']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $modifica = substr($modifica,0,255);
    
    $pianificazione_corrente = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['pianificazione_corrente']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $pianificazione_corrente = substr($pianificazione_corrente,0,255);
     
    if(isset($_GET['id'])){
        $id = html_entity_decode(strip_tags($_GET['id']), ENT_QUOTES,$default_charset);
        $id = substr($id,0,255);
        list($task_id, $id_vecchia_risorsa) = explode('_', $id);
    }
    else{
        $id = '';
    }
    
    if(isset($_GET['risorsa'])){
        $risorsa = html_entity_decode(strip_tags($_GET['risorsa']), ENT_QUOTES,$default_charset);
        $risorsa = substr($risorsa,0,100);
    }
    else{
        $risorsa = 0;
    }
    
    if(isset($_GET['text'])){
        $text = html_entity_decode(strip_tags($_GET['text']), ENT_QUOTES,$default_charset);
        $text = substr($text,0,255);
    }
    else{
        $text = '';
    }
    
    if(isset($_GET['start_date'])){
        $start_date = html_entity_decode(strip_tags($_GET['start_date']), ENT_QUOTES,$default_charset);
        $start_date = substr($start_date,0,100);
        if($start_date != null && $start_date != ''){
            list($annoInizio,$meseInizio,$giornoInizio) = explode("-",$start_date);
        }
        else{
            $annoInizio = '';
            $meseInizio = '';
            $giornoInizio = '';
            $start_date = '';
        }
    }
    else{
        $start_date = '';
        $annoInizio = '';
        $meseInizio = '';
        $giornoInizio = '';
    }
    
    if(isset($_GET['start_hour'])){
        $start_hour = html_entity_decode(strip_tags($_GET['start_hour']), ENT_QUOTES,$default_charset);
        $start_hour = substr($start_hour,0,100);
    }
    else{
        $start_hour = '00:00';
    }
    list($oraInizio, $minutiInizio) = explode(':', $start_hour);
    
    if(isset($_GET['end_date'])){
        $end_date = html_entity_decode(strip_tags($_GET['end_date']), ENT_QUOTES,$default_charset);
        $end_date = substr($end_date,0,100);
        if($end_date != null && $end_date != ''){
            list($annoEnd,$meseEnd,$giornoEnd) = explode("-",$end_date);
        }
        else{
            $annoEnd = '';
            $meseEnd = '';
            $giornoEnd = '';
            $end_date = '';
        }   
    }
    else{
        $end_date = '';
        $annoEnd = '';
        $meseEnd = '';
        $giornoEnd = '';
    }
    
    if(isset($_GET['end_hour'])){
        $end_hour = html_entity_decode(strip_tags($_GET['end_hour']), ENT_QUOTES,$default_charset);
        $end_hour = substr($end_hour,0,100);
    }
    else{
        $end_hour = '00:00';
    }
    list($oraFine, $minutiFine) = explode(':', $end_hour);
    
    if(isset($_GET['durata'])){
        $durata = html_entity_decode(strip_tags($_GET['durata']), ENT_QUOTES,$default_charset);
        $durata = substr($durata,0,100);
    }
    else{
        $durata = 0;
    }
    
    $relazionato_a = 0;
    $projectstatus = "";
    
    if($id_vecchia_risorsa != $risorsa){
        aggiornaRisorsaTask($elemento_id,$id_vecchia_risorsa,$risorsa);
    }
    
    if($modifica == 'edit' && ($pianificazione_corrente == "si" || $pianificazione_corrente == "no")){
        
        $q_operazione = "SELECT 
                            task.projecttaskid projecttaskid,
                            task.projecttask_no projecttask_no,
                            task.projecttaskname projecttaskname,
                            task.projecttasktype projecttasktype,
                            task.projecttaskpriority projecttaskpriority,
                            task.projecttaskprogress projecttaskprogress,
                            task.startdate startdate,
                            task.enddate enddate,
                            task.operazione_prec operazione_prec,
                            task.lead_time lead_time,
                            task.data_fine_pianificata data_fine_pianificata,
                            task.giorni_scostamento giorni_scostamento,
                            proj.projectstatus projectstatus,
                            proj.linktoaccountscontacts linktoaccountscontacts,
                            ent.smownerid assegnatario,
                            task.description descrizione
                            FROM {$table_prefix}_projecttask task
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = task.projecttaskid
                            INNER JOIN {$table_prefix}_projecttaskcf taskcf ON taskcf.projecttaskid = task.projecttaskid
                            INNER JOIN {$table_prefix}_project proj ON  proj.projectid = task.projectid
                            WHERE ent.deleted = 0 AND task.projecttaskid = ".$elemento_id;

        $res_operazione = $adb->query($q_operazione);
        if($adb->num_rows($res_operazione)>0){

            $data_fine_pianificata = $adb->query_result($res_operazione,0,'data_fine_pianificata');
            $data_fine_pianificata = html_entity_decode(strip_tags($data_fine_pianificata), ENT_QUOTES,$default_charset);
            
            $projectstatus = $adb->query_result($res_operazione,0,'projectstatus');
            $projectstatus = html_entity_decode(strip_tags($projectstatus), ENT_QUOTES,$default_charset);
            
            $relazionato_a = $adb->query_result($res_operazione,0,'linktoaccountscontacts');
            $relazionato_a = html_entity_decode(strip_tags($relazionato_a), ENT_QUOTES,$default_charset);
            if($relazionato_a == null || $relazionato_a == ''){
                $relazionato_a = 0;
            }
            
            $scostamento = calcolaScostamento($projectstatus,$data_fine_pianificata,$start_date,$end_date);
            $giorni_scostamento = $scostamento['giorni_scostamento'];
            $tipo_scostamento = $scostamento['tipo_scostamento'];
            
            $text = addslashes($text);
            $start_date = addslashes($start_date);
            $end_date = addslashes($end_date);
            $start_hour = addslashes($start_hour);
            $end_hour = addslashes($end_hour);
            $durata = addslashes($durata);
            
            if($projectstatus == 'In pianificazione' || $projectstatus == 'prospecting'){

                $upd = "UPDATE {$table_prefix}_projecttask SET
                        projecttaskname = '".$text."',
                        startdate = '".$start_date."',
                        enddate = '".$end_date."',
                        data_fine_pianificata = '".$end_date."',
                        data_inizio_pian = '".$start_date."',
                        kp_ora_inizio_task = '".$start_hour."',
                        kp_ora_fine_task = '".$end_hour."',
                        lead_time = ".$durata.",
                        lead_time_pian = ".$durata.",
                        giorni_scostamento = 0,
                        tipo_scostamento_task = '".$tipo_scostamento."'  
                        WHERE projecttaskid = ".$elemento_id;
            }
            else{

                $upd = "UPDATE {$table_prefix}_projecttask SET
                        projecttaskname = '".$text."',
                        startdate = '".$start_date."',
                        enddate = '".$end_date."',
                        kp_ora_inizio_task = '".$start_hour."',
                        kp_ora_fine_task = '".$end_hour."',
                        lead_time = ".$durata.",
                        giorni_scostamento = ".$giorni_scostamento.",
                        tipo_scostamento_task = '".$tipo_scostamento."'
                        WHERE projecttaskid = ".$elemento_id;
            }
            //print_r($upd);die;
            $adb->query($upd);
            
            $datiPadre = aggiornamentoRisorsivoPadre($elemento_id,$projectstatus,$relazionato_a);
            
        }
        
    }
    
    $rows[] = array('id' => $elemento_id."_".$risorsa,
                    'elemento_id' => $elemento_id,
                    'modifica' => $modifica,
                    'pianificazione_corrente' => $pianificazione_corrente,
                    'start_date' => $start_date,
                    'end_date' => $end_date,
                    'start_hour' => $start_hour,
                    'end_hour' => $end_hour,
                    'giornoInizio' => (int)$giornoInizio,
                    'meseInizio' => (int)$meseInizio,
                    'annoInizio' => (int)$annoInizio,
                    'oraInizio' => (int)$oraInizio,
                    'minutiInizio' => (int)$minutiInizio,
                    'giornoEnd' => (int)$giornoEnd,
                    'meseEnd' => (int)$meseEnd,
                    'annoEnd' => (int)$annoEnd,
                    'oraFine' => (int)$oraFine,
                    'minutiFine' => (int)$minutiFine,
                    'giorni_scostamento' => $giorni_scostamento,
                    'tipo_scostamento_task' => $tipo_scostamento);
	
}

$json = json_encode($rows);
print $json;

function dateDiff($tipo, $partenza, $fine){
    
    if($partenza != "" && $fine != ""){
        
        if($fine < $partenza){
            $partenza_new = $fine;
            $fine = $partenza;
            $partenza = $partenza_new;
        }
    
        switch ($tipo){
            case "A" : $tipo = 365;
                break;
            case "M" : $tipo = (365 / 12);
                break;
            case "S" : $tipo = (365 / 52);
                break;
            case "G" : $tipo = 1;
                break;
        }
        $arr_partenza = explode("-", $partenza);
        $partenza_gg = $arr_partenza[2];
        $partenza_mm = $arr_partenza[1];
        $partenza_aa = $arr_partenza[0];
        $arr_fine = explode("-", $fine);
        $fine_gg = $arr_fine[2];
        $fine_mm = $arr_fine[1];
        $fine_aa = $arr_fine[0];
        $date_diff = mktime(12, 0, 0, $fine_mm, $fine_gg, $fine_aa) - mktime(12, 0, 0, $partenza_mm, $partenza_gg, $partenza_aa);
        $date_diff  = floor(($date_diff / 60 / 60 / 24) / $tipo);
        return $date_diff;
        
    }
    else{
        return 0;
    }
    
}

function dataInizioPadre($padre,$stato_pianificazione){
    global $adb, $table_prefix, $default_charset;
    
    $data_inizio = '';
    $ora_inizio = '00:00';

    if($stato_pianificazione == 'In pianificazione' || $stato_pianificazione == 'prospecting'){
        
        $q_data_inizio = "SELECT pro.projecttaskid projecttaskid,
                            pro.data_inizio_pian data_inizio,
                            pro.kp_ora_inizio_task ora_inizio
                            FROM {$table_prefix}_projecttask pro
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = pro.projecttaskid
                            WHERE ent.deleted = 0 AND pro.operazione_prec = ".$padre."
                            ORDER BY pro.data_inizio_pian ASC, pro.kp_ora_inizio_task ASC";
   
    }
    else{
        
        $q_data_inizio = "SELECT pro.projecttaskid projecttaskid,
                            pro.startdate data_inizio,
                            pro.kp_ora_inizio_task ora_inizio
                            FROM {$table_prefix}_projecttask pro
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = pro.projecttaskid
                            WHERE ent.deleted = 0 AND pro.operazione_prec = ".$padre."
                            ORDER BY pro.startdate ASC, pro.kp_ora_inizio_task ASC";
        
    }
    $res_data_inizio = $adb->query($q_data_inizio);
    if($adb->num_rows($res_data_inizio)>0){
        $data_inizio = $adb->query_result($res_data_inizio,0,'data_inizio');
        $data_inizio = html_entity_decode(strip_tags($data_inizio), ENT_QUOTES,$default_charset);
        
        $ora_inizio = $adb->query_result($res_data_inizio,0,'ora_inizio');
        $ora_inizio = html_entity_decode(strip_tags($ora_inizio), ENT_QUOTES,$default_charset);
    }
    else{
        $data_inizio = '';
        $ora_inizio = '00:00';
    }
    
    $result = array('data_inizio' => $data_inizio,
                    'ora_inizio' => $ora_inizio);

    return $result;
	
}

function dataFinePadre($padre,$stato_pianificazione){
    global $adb, $table_prefix, $default_charset;
    
    $data_fine = '';
    $ora_fine = '00:00';
    
    if($stato_pianificazione == 'In pianificazione' || $stato_pianificazione == 'prospecting'){
        
        $q_data_fine = "SELECT pro.projecttaskid projecttaskid,
                            pro.data_fine_pianificata data_fine,
                            pro.kp_ora_fine_task ora_fine
                            FROM {$table_prefix}_projecttask pro
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = pro.projecttaskid
                            WHERE ent.deleted = 0 AND pro.operazione_prec = ".$padre."
                            ORDER BY pro.data_fine_pianificata DESC, pro.kp_ora_fine_task DESC";

    }
    else{
        
        $q_data_fine = "SELECT pro.projecttaskid projecttaskid,
                            pro.enddate data_fine,
                            pro.kp_ora_fine_task ora_fine
                            FROM {$table_prefix}_projecttask pro
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = pro.projecttaskid
                            WHERE ent.deleted = 0 AND pro.operazione_prec = ".$padre."
                            ORDER BY pro.enddate DESC, pro.kp_ora_fine_task DESC";

    }
    $res_data_fine = $adb->query($q_data_fine);
    if($adb->num_rows($res_data_fine)>0){
        $data_fine = $adb->query_result($res_data_fine,0,'data_fine');
        $data_fine = html_entity_decode(strip_tags($data_fine), ENT_QUOTES,$default_charset);	
        
        $ora_fine = $adb->query_result($res_data_fine,0,'ora_fine');
        $ora_fine = html_entity_decode(strip_tags($ora_fine), ENT_QUOTES,$default_charset);
    }
    else{
        $data_fine = '';
        $ora_fine = '00:00';
    }

    $result = array('data_fine' => $data_fine,
                    'ora_fine' => $ora_fine);

    return $result;
	
}

function aggiornamentoRisorsivoPadre($operazione,$statoPianificazione,$cliente){
    global $adb, $table_prefix, $default_charset;
    
    $operazionePadre = 0;
    $leadtime_padre = 0;
    $startdate_padre = '';
    $enddate_padre = '';
    $startdatehour_padre_inv = '';
    $enddatehour_padre_inv = '';

    $q_operazione = "SELECT op.operazione_prec operazione_prec FROM {$table_prefix}_projecttask op
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = op.operazione_prec
                        WHERE ent.deleted = 0 AND op.projecttaskid = ".$operazione;
    //printf($q_operazione);die;
    
    $res_operazione = $adb->query($q_operazione);
    if($adb->num_rows($res_operazione)>0){
        $operazionePadre = $adb->query_result($res_operazione,0,'operazione_prec');
        $operazionePadre = html_entity_decode(strip_tags($operazionePadre), ENT_QUOTES,$default_charset);

        if($operazionePadre != 0 && $operazionePadre != '' && $operazionePadre != null){
            
            $datiInizioPadre = dataInizioPadre($operazionePadre,$statoPianificazione);
            $startdate_padre = $datiInizioPadre['data_inizio'];
            $starthour_padre = $datiInizioPadre['ora_inizio'];
            if($starthour_padre == null || $starthour_padre == ''){
                $starthour_padre = '00:00';
            }
            
            if($startdate_padre != null && $startdate_padre != ''){
                list($annoStartPadre,$meseStartPadre,$giornoStartPadre) = explode("-",$startdate_padre);
                $startdatehour_padre_inv = date("d-m-Y",mktime(0,0,0,$meseStartPadre,$giornoStartPadre,$annoStartPadre));
                
                $startdatehour_padre_inv = $startdatehour_padre_inv." ".$starthour_padre;
                
                list($oraStartPadre, $minutiStartPadre) = explode(':', $starthour_padre);
            }    
            
            $datiFinePadre = dataFinePadre($operazionePadre,$statoPianificazione);
            $enddate_padre = $datiFinePadre['data_fine'];
            $enddhour_padre = $datiFinePadre['ora_fine'];
            if($enddhour_padre == null || $enddhour_padre == ''){
                $enddhour_padre = '00:00';
            }
            
            if($enddate_padre != null && $enddate_padre != ''){
                list($annoEndPadre,$meseEndPadre,$giornoEndPadre) = explode("-",$enddate_padre);
                $enddatehour_padre_inv = date("d-m-Y",mktime(0,0,0,$meseEndPadre,$giornoEndPadre,$annoEndPadre));
                
                $enddatehour_padre_inv = $enddatehour_padre_inv." ".$enddhour_padre;
                
                list($oraEndPadre, $minutiEndPadre) = explode(':', $enddhour_padre);
            } 
            
            $leadtime_padre = dateDiff("G", $startdate_padre, $enddate_padre);

            if($statoPianificazione == 'In pianificazione' || $statoPianificazione == 'prospecting'){
                $upd = "UPDATE {$table_prefix}_projecttask SET
                        tipo_attivita_task = 'Raggruppamento',
                        lead_time = ".$leadtime_padre.",
                        startdate = '".$startdate_padre."',
                        enddate = '".$enddate_padre."',
                        lead_time_pian = ".$leadtime_padre.",
                        data_inizio_pian = '".$startdate_padre."',
                        data_fine_pianificata = '".$enddate_padre."',
                        kp_ora_inizio_task = '".$starthour_padre."',
                        kp_ora_fine_task = '".$enddhour_padre."',   
                        kp_relazionato_a = ".$cliente.", 
                        kp_stato_pian = '".$statoPianificazione."'    
                        WHERE projecttaskid =".$operazionePadre;
            }
            else{
                $upd = "UPDATE {$table_prefix}_projecttask SET
                        tipo_attivita_task = 'Raggruppamento',
                        lead_time = ".$leadtime_padre.",
                        startdate = '".$startdate_padre."',
                        enddate = '".$enddate_padre."',
                        kp_ora_inizio_task = '".$starthour_padre."',
                        kp_ora_fine_task = '".$enddhour_padre."',  
                        kp_relazionato_a = ".$cliente.", 
                        kp_stato_pian = '".$statoPianificazione."'    
                        WHERE projecttaskid =".$operazionePadre;
            }
            //printf($upd);die;
            $adb->query($upd);

            aggiornamentoRisorsivoPadre($operazionePadre,$statoPianificazione,$cliente);
            
        }

    }
    
    $result = array('operazionePadre' => $operazionePadre,
                    'leadtime_padre' => $leadtime_padre,
                    'startdate_padre' => $startdate_padre,
                    'enddate_padre' => $enddate_padre,
                    'starthour_padre' => $starthour_padre,
                    'enddhour_padre' => $enddhour_padre,
                    'startdatehour_padre_inv' => $startdatehour_padre_inv,
                    'enddatehour_padre_inv' => $enddatehour_padre_inv,
                    'giornoStartPadre' => $giornoStartPadre,
                    'meseStartPadre' => $meseStartPadre,
                    'annoStartPadre' => $annoStartPadre,
                    'oraStartPadre' => $oraStartPadre,
                    'minutiStartPadre' => $minutiStartPadre,
                    'giornoEndPadre' => $giornoEndPadre,
                    'meseEndPadre' => $meseEndPadre,
                    'annoEndPadre' => $annoEndPadre,
                    'oraEndPadre' => $oraEndPadre,
                    'minutiEndPadre' => $minutiEndPadre);
    
    return $result; 
	
}

function calcolaScostamento($statoPianificazione,$dataFinePianificata,$dataInizio,$dataFine){
    global $adb, $table_prefix, $default_charset;
    
    if(($dataFinePianificata != '' && $dataFinePianificata != null && $dataFinePianificata != '0000-00-00') && ($dataInizio != '' && $dataInizio != null && $dataInizio != '0000-00-00') && ($dataFine != '' && $dataFine != null && $dataFine != '0000-00-00')){
        
        if(($statoPianificazione != 'In pianificazione' && $statoPianificazione != 'prospecting') && $statoPianificazione != '' & $statoPianificazione != null){
            
            //printf("Data Fine: ".$dataFine." Data Fine Pianificata: ".$dataFinePianificata);die;
            
            if($dataFinePianificata < $dataFine){
                $giorni_scostamento = datediff("G", $dataFinePianificata, $dataFine);
                $tipo_scostamento = "Negativo";
            }
            else if ($dataFinePianificata > $dataFine){
                $giorni_scostamento = datediff("G", $dataFine, $dataFinePianificata);
                $tipo_scostamento = "Positivo";
            }
            else{
                $giorni_scostamento = 0;
                $tipo_scostamento = "--Nessuno--";
            }
        }
        else{
            $giorni_scostamento = 0;
            $tipo_scostamento = "--Nessuno--";
        }
        
    }
    else{
        
        $tipo_scostamento = "--Nessuno--";
        $giorni_scostamento = 0;
                  
    }
    
    //printf("Tipo scostamento: ".$tipo_scostamento." Giorni scostamento: ".$giorni_scostamento);die;
    
    $result = array('tipo_scostamento' => $tipo_scostamento,
                    'giorni_scostamento' => $giorni_scostamento);
    
    return $result;
    
}

function aggiornaRisorsaTask($task,$vecchia_risorsa,$nuova_risorsa){
    global $adb, $table_prefix, $default_charset;
    
    require_once('Utility.php');
    
    $q_task_resurce = "SELECT tr.taskresourcesid taskresourcesid 
                        FROM {$table_prefix}_taskresources tr
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tr.taskresourcesid
                        WHERE ent.deleted = 0 AND tr.task = ".$task." AND tr.risorsa = ".$vecchia_risorsa;
       
    $res_task_resurce = $adb->query($q_task_resurce);
    if($adb->num_rows($res_task_resurce)>0){
        $taskresourcesid = $adb->query_result($res_task_resurce,0,'taskresourcesid');
        $taskresourcesid = html_entity_decode(strip_tags($taskresourcesid), ENT_QUOTES,$default_charset);
        
        $upd_task_resurce = "UPDATE {$table_prefix}_taskresources SET
                              risorsa = ".$nuova_risorsa."
                              WHERE taskresourcesid = ".$taskresourcesid;
        $adb->query($upd_task_resurce);
        
        $nomi_risorse = sommaNomiRisorseTask($task);
        
    }
    
}
	
?>