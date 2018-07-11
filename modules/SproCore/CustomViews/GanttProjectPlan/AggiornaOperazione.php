<?php

/* kpro@tom3012015 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2015, Kpro Consulting Srl
 * @package ganttPianificazioni
 * @version 1.0
 */

/* kpro@tom070220171115 */
require_once('Utility.php');
/* kpro@tom070220171115 end */

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

if(isset($_GET['project']) && isset($_GET['operazione'])){
    $project = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['project']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $project = substr($project,0,100);

    $operazione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['operazione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $operazione = substr($operazione,0,100);
    
    $q_progetto = "SELECT projectstatus,
                    linktoaccountscontacts
                    FROM {$table_prefix}_project
                    WHERE projectid = ".$project;
    $res_progetto = $adb->query($q_progetto);
    if($adb->num_rows($res_progetto)>0){
        
        $projectstatus = $adb->query_result($res_progetto,0,'projectstatus');
        $projectstatus = html_entity_decode(strip_tags($projectstatus), ENT_QUOTES,$default_charset);
        
        $relazionato_a = $adb->query_result($res_progetto,0,'linktoaccountscontacts');
        $relazionato_a = html_entity_decode(strip_tags($relazionato_a), ENT_QUOTES,$default_charset);
        if($relazionato_a == null || $relazionato_a == ''){
            $relazionato_a = 0;
        }
        
    }    

    $q_operazione = "SELECT 
                        pro.projecttaskid projecttaskid,
                        pro.projecttask_no projecttask_no,
                        pro.projecttaskname projecttaskname,
                        pro.projecttasktype projecttasktype,
                        pro.projecttaskpriority projecttaskpriority,
                        pro.projecttaskprogress projecttaskprogress,
                        pro.startdate startdate,
                        pro.enddate enddate,
                        pro.operazione_prec operazione_prec,
                        pro.lead_time lead_time,
                        pro.data_fine_pianificata data_fine_pianificata,
                        pro.giorni_scostamento giorni_scostamento,
                        ent.smownerid assegnatario,
                        pro.description descrizione
                        FROM {$table_prefix}_projecttask pro
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = pro.projecttaskid
                        INNER JOIN {$table_prefix}_projecttaskcf procf ON procf.projecttaskid = pro.projecttaskid
                        WHERE ent.deleted = 0 AND pro.projectid = ".$project." AND pro.projecttaskid =".$operazione;
    
    $res_operazione = $adb->query($q_operazione);
    if($adb->num_rows($res_operazione)>0){

        $data_fine_pianificata = $adb->query_result($res_operazione,0,'data_fine_pianificata');
        $data_fine_pianificata = html_entity_decode(strip_tags($data_fine_pianificata), ENT_QUOTES,$default_charset);

    }
    
    if(isset($_GET['modifica'])){
        $modifica = html_entity_decode(strip_tags($_GET['modifica']), ENT_QUOTES,$default_charset);
        $modifica = substr($modifica,0,255);
    }
    else{
        $modifica = '';
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
        $start_date = substr($start_date,0,255);
        
        if($start_date != null && $start_date != ''){
            list($annoInizio,$meseInizio,$giornoInizio) = explode("-",$start_date);
            $enddatehour_padre_inv = date("d-m-Y",mktime(0,0,0,$meseInizio,$giornoInizio,$annoInizio));
        }
        else{
            $annoInizio = '';
            $meseInizio = '';
            $giornoInizio = '';
        }
    }
    else{
        $start_date = '';
        $annoInizio = '';
        $meseInizio = '';
        $giornoInizio = '';
    }
    
    if(isset($_GET['end_date'])){
        $end_date = html_entity_decode(strip_tags($_GET['end_date']), ENT_QUOTES,$default_charset);
        $end_date = substr($end_date,0,255);
        
        if($end_date != null && $end_date != ''){
            list($annoEnd,$meseEnd,$giornoEnd) = explode("-",$end_date);
            $enddatehour_padre_inv = date("d-m-Y",mktime(0,0,0,$meseEnd,$giornoEnd,$annoEnd));
        }
        else{
            $annoEnd = '';
            $meseEnd = '';
            $giornoEnd = '';
        }    
    }
    else{
        $end_date = '';
        $annoEnd = '';
        $meseEnd = '';
        $giornoEnd = '';
    }
    
    if(isset($_GET['start_hour'])){
        $start_hour = html_entity_decode(strip_tags($_GET['start_hour']), ENT_QUOTES,$default_charset);
        $start_hour = substr($start_hour,0,255);
    }
    else{
        $start_hour = '00:00';
    }
    list($oraInizio, $minutiInizio) = explode(':', $start_hour);
    
    if(isset($_GET['end_hour'])){
        $end_hour = html_entity_decode(strip_tags($_GET['end_hour']), ENT_QUOTES,$default_charset);
        $end_hour = substr($end_hour,0,255);
    }
    else{
        $end_hour = '00:00';
    }
    list($oraFine, $minutiFine) = explode(':', $end_hour);

    if(isset($_GET['descrizione'])){
        $descrizione = html_entity_decode(strip_tags($_GET['descrizione']), ENT_QUOTES,$default_charset);
    }
    else{
        $descrizione = '';
    }
    
    $scostamento = calcolaScostamento($projectstatus,$data_fine_pianificata,$start_date,$end_date);
    
    $giorni_scostamento = $scostamento['giorni_scostamento'];
    $tipo_scostamento = $scostamento['tipo_scostamento'];

    if(isset($_GET['duration'])){
        $duration = html_entity_decode(strip_tags($_GET['duration']), ENT_QUOTES,$default_charset);
        $duration = substr($duration,0,255);
    }
    else{
        $duration = '';
    }

    if(isset($_GET['parent'])){
        $parent = html_entity_decode(strip_tags($_GET['parent']), ENT_QUOTES,$default_charset);
        $parent = substr($parent,0,100);
        if($parent == ''){
            $parent = 0;
        }
    }
    else{
        $parent = 0;
    }

    if(isset($_GET['progress'])){
        $progress = html_entity_decode(strip_tags($_GET['progress']), ENT_QUOTES,$default_charset);
        $progress = substr($progress,0,100);
        if($progress != 0 && $progress != ''){
            $progress = calcolaPercentualeOperazione($progress);
        }
        else{
            $progress = "--none--";
        }
    }
    else{
        $progress = "--none--";
    }

    if(isset($_GET['priority'])){
        $priority = html_entity_decode(strip_tags($_GET['priority']), ENT_QUOTES,$default_charset);
        $priority = substr($priority,0,255);
    }
    else{
        $priority = "--none--";
    }

    /* kpro@tom070220171115 */
    if(isset($_GET['type'])){
        $type = html_entity_decode(strip_tags($_GET['type']), ENT_QUOTES, $default_charset);
        $type = substr($type, 0, 255);
        if($type == "project"){
            $type = "Raggruppamento";
        }
        elseif($type == "task"){
            $type = "Attivita";
        }
        else{
            $type = "Attivita";
        }
    }
    else{
        $type = "Attivita";
    }
    /* kpro@tom070220171115 end */
    
    if(isset($_GET['servizio'])){
        $servizio = html_entity_decode(strip_tags($_GET['servizio']), ENT_QUOTES, $default_charset);
        $servizio = substr($servizio, 0, 100);
        if($servizio == null || $servizio == ""){
			$servizio = 0;
		}
    }
    else{
        $servizio = 0;
    }

    if(isset($_GET['commessa'])){
        $commessa = html_entity_decode(strip_tags($_GET['commessa']), ENT_QUOTES, $default_charset);
        $commessa = substr($commessa, 0, 100);
        if($commessa == null || $commessa == ""){
			$commessa = 0;
		}
    }
    else{
        $commessa = 0;
    }

    if($commessa == 0){
        $q = "SELECT pro.kp_commessa
            FROM {$table_prefix}_project pro
            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = pro.projectid
            WHERE ent.deleted = 0 AND pro.projectid = ".$project;
        $res = $adb->query($q);
        if($adb->num_rows($res) > 0){
            $commessa = $adb->query_result($res, 0, 'kp_commessa');
            $commessa = html_entity_decode(strip_tags($commessa), ENT_QUOTES,$default_charset);
            if($commessa == '' || $commessa == null){
                $commessa = 0;
            }
        }
    }

    $startdate_padre = "";
    $enddate_padre = "";
    $leadtime_padre = "";
    $startdatehour_padre_inv = "";
    $enddatehour_padre_inv = "";
    $giornoStartPadre = "";
    $meseStartPadre = "";
    $annoStartPadre = "";
    $oraStartPadre = "";
    $minutiStartPadre = "";
    $giornoEndPadre = "";
    $meseEndPadre = "";
    $annoEndPadre = "";
    $oraEndPadre = "";
    $minutiEndPadre = "";
    
    if($modifica == 'new'){
        $new_operazione = CRMEntity::getInstance('ProjectTask'); 
        $new_operazione->column_fields['assigned_user_id'] = $current_user->id;
        $new_operazione->column_fields['projectid'] = $project;
        $new_operazione->column_fields['projecttaskname'] = $text;
        $new_operazione->column_fields['startdate'] = $start_date;
        $new_operazione->column_fields['enddate'] = $end_date;
        $new_operazione->column_fields['kp_ora_inizio_task'] = $start_hour;
        $new_operazione->column_fields['kp_ora_fine_task'] = $end_hour;
        $new_operazione->column_fields['operazione_prec'] = $parent;
        $new_operazione->column_fields['lead_time'] = $duration;
        $new_operazione->column_fields['projecttaskprogress'] = $progress;
        $new_operazione->column_fields['projecttaskpriority'] = $priority;
        $new_operazione->column_fields['tipo_attivita_task'] = 'Attivita';
        if($projectstatus == 'In pianificazione' || $projectstatus == 'prospecting'){
            $new_operazione->column_fields['giorni_scostamento'] = 0;
            $new_operazione->column_fields['data_fine_pianificata'] = $end_date;
            $new_operazione->column_fields['data_inizio_pian'] = $start_date;
            $new_operazione->column_fields['lead_time_pian'] = $duration;
        }
        else{
            $new_operazione->column_fields['giorni_scostamento'] = $giorni_scostamento;
        }
        $new_operazione->column_fields['tipo_scostamento_task'] = $tipo_scostamento;
        if($relazionato_a != null && $relazionato_a != '' && $relazionato_a != 0){
            $new_operazione->column_fields['kp_relazionato_a'] = $relazionato_a;
        }
        $new_operazione->column_fields['kp_stato_pian'] = $projectstatus;
        $new_operazione->column_fields['kp_stato_op_pian'] = 'Attiva';
        if($servizio != 0){
			$new_operazione->column_fields['kp_servizio'] = $servizio;
        }
        if($commessa != 0){
			$new_operazione->column_fields['kp_commessa'] = $commessa;
		}
        $new_operazione->column_fields['description'] = $descrizione;
        $new_operazione->save('ProjectTask', $longdesc=true, $offline_update=false, $triggerEvent=false); 
        $new_operazione_id = $new_operazione->id;

        if($parent != 0 && $parent != null && $parent != ''){
            
            $datiPadre = aggiornamentoRisorsivoPadre($new_operazione_id,$projectstatus,$relazionato_a);
            
            $startdate_padre = $datiPadre['startdate_padre'];
            $enddate_padre = $datiPadre['enddate_padre'];
            $leadtime_padre = $datiPadre['leadtime_padre'];
            $startdatehour_padre_inv = $datiPadre['startdatehour_padre_inv'];
            $enddatehour_padre_inv = $datiPadre['enddatehour_padre_inv'];
            $giornoStartPadre = $datiPadre['giornoStartPadre'];
            $meseStartPadre = $datiPadre['meseStartPadre'];
            $annoStartPadre = $datiPadre['annoStartPadre'];
            $oraStartPadre = $datiPadre['oraStartPadre'];
            $minutiStartPadre = $datiPadre['minutiStartPadre'];
            $giornoEndPadre = $datiPadre['giornoEndPadre'];
            $meseEndPadre = $datiPadre['meseEndPadre'];
            $annoEndPadre = $datiPadre['annoEndPadre'];
            $oraEndPadre = $datiPadre['oraEndPadre'];
            $minutiEndPadre = $datiPadre['minutiEndPadre'];

            $tipo_padre = 'Raggruppamento';
        }
        else{
            $tipo_padre = 'Attivita';
        }

    }	
    else if($modifica == 'edit'){

        $text = addslashes($text);
        $start_date = addslashes($start_date);
        $end_date = addslashes($end_date);
        $duration = addslashes($duration);
        $parent = addslashes($parent);
        $progress = addslashes($progress);
        $priority = addslashes($priority);
        $descrizione = addslashes($descrizione);
        $relazionato_a = addslashes($relazionato_a);
        $projectstatus = addslashes($projectstatus);

        if($projectstatus == 'In pianificazione' || $projectstatus == 'prospecting'){

            /* kpro@tom070220171115 */
            $upd = "UPDATE {$table_prefix}_projecttask SET
                    projecttaskname = '".$text."',
                    startdate = '".$start_date."',
                    enddate = '".$end_date."',
                    data_fine_pianificata = '".$end_date."',
                    data_inizio_pian = '".$start_date."',
                    kp_ora_inizio_task = '".$start_hour."',
                    kp_ora_fine_task = '".$end_hour."',
                    giorni_scostamento = 0,
                    tipo_scostamento_task = '".$tipo_scostamento."',
                    operazione_prec = ".$parent.",
                    lead_time = ".$duration.",
                    lead_time_pian = ".$duration.",
                    projecttaskprogress = '".$progress."',
                    projecttaskpriority = '".$priority."',
                    kp_relazionato_a = ".$relazionato_a.", 
                    kp_stato_pian = '".$projectstatus."',
                    tipo_attivita_task = '".$type."',
                    kp_servizio = ".$servizio.",
                    kp_commessa = ".$commessa.",
                    description = '".$descrizione."'
                    WHERE projecttaskid = ".$operazione;
            /* kpro@tom070220171115 end */

        }
        else{

            /* kpro@tom070220171115 */
            $upd = "UPDATE {$table_prefix}_projecttask SET
                    projecttaskname = '".$text."',
                    startdate = '".$start_date."',
                    enddate = '".$end_date."',
                    kp_ora_inizio_task = '".$start_hour."',
                    kp_ora_fine_task = '".$end_hour."',
                    giorni_scostamento = ".$giorni_scostamento.",
                    tipo_scostamento_task = '".$tipo_scostamento."',
                    operazione_prec = ".$parent.",
                    lead_time = ".$duration.",
                    projecttaskprogress = '".$progress."',
                    projecttaskpriority = '".$priority."',
                    kp_relazionato_a = ".$relazionato_a.", 
                    kp_stato_pian = '".$projectstatus."',
                    tipo_attivita_task = '".$type."',
                    kp_servizio = ".$servizio.",
                    kp_commessa = ".$commessa.",
                    description = '".$descrizione."'
                    WHERE projecttaskid = ".$operazione;
            /* kpro@tom070220171115 end */

        }
        //print_r($upd);die;
        $adb->query($upd);
        
        // Aggiorno anche tutti i ticket collegati
        AggiornaTicketCollegati($operazione, $project);

        $new_operazione_id = $operazione;

        if($parent != 0 && $parent != null && $parent != ''){

            $datiPadre = aggiornamentoRisorsivoPadre($operazione,$projectstatus,$relazionato_a);
            
            $startdate_padre = $datiPadre['startdate_padre'];
            $enddate_padre = $datiPadre['enddate_padre'];
            $leadtime_padre = $datiPadre['leadtime_padre'];
            $startdatehour_padre_inv = $datiPadre['startdatehour_padre_inv'];
            $enddatehour_padre_inv = $datiPadre['enddatehour_padre_inv'];
            $giornoStartPadre = $datiPadre['giornoStartPadre'];
            $meseStartPadre = $datiPadre['meseStartPadre'];
            $annoStartPadre = $datiPadre['annoStartPadre'];
            $oraStartPadre = $datiPadre['oraStartPadre'];
            $minutiStartPadre = $datiPadre['minutiStartPadre'];
            $giornoEndPadre = $datiPadre['giornoEndPadre'];
            $meseEndPadre = $datiPadre['meseEndPadre'];
            $annoEndPadre = $datiPadre['annoEndPadre'];
            $oraEndPadre = $datiPadre['oraEndPadre'];
            $minutiEndPadre = $datiPadre['minutiEndPadre'];

            $tipo_padre = 'Raggruppamento';

        }
        else{
            $tipo_padre = 'Attivita';
        }

    }
    else if($modifica == 'delete'){

        $upd_ent = "UPDATE {$table_prefix}_crmentity SET
                    deleted = 1
                    WHERE crmid =".$operazione;
        $adb->query($upd_ent);
        $new_operazione_id = $operazione;

        //Elimino tutti i ticket collegati
        EliminaTicketCollegati($operazione);

        //Quando elimino una operazione devo anche cancellare tutte le relazione collegate ad essa

        $q_relazioni = "SELECT 
                        rel.relazioniopid relazioniopid
                        FROM {$table_prefix}_relazioniop rel
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relazioniopid
                        WHERE ent.deleted = 0 AND (rel.operazione = ".$operazione." OR rel.operazione_target = ".$operazione.") 
                        AND rel.pianificazione = ".$project;

        $res_relazioni = $adb->query($q_relazioni);
        $num_relazioni = $adb->num_rows($res_relazioni);

        for($i=0; $i<$num_relazioni; $i++){
            $relazioniopid = $adb->query_result($res_relazioni, $i, 'relazioniopid');
            $relazioniopid = html_entity_decode(strip_tags($relazioniopid), ENT_QUOTES,$default_charset);

            $upd_ent = "UPDATE {$table_prefix}_crmentity SET
                        deleted = 1
                        WHERE crmid =".$relazioniopid;
            $adb->query($upd_ent);

        }

        if($parent != 0 && $parent != null && $parent != ''){
            $q_operazioni = "SELECT 
                                pro.projecttaskid projecttaskid
                                FROM {$table_prefix}_projecttask pro
                                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = pro.projecttaskid
                                WHERE ent.deleted = 0 AND pro.projectid = ".$project." AND pro.operazione_prec = ".$parent;
                                
            $res_operazioni = $adb->query($q_operazioni);
            $num_operazioni = $adb->num_rows($res_operazioni);
            if($num_operazioni == 0){
                
                $dati_padre = "SELECT startdate, 
                                enddate,
                                kp_ora_inizio_task,
                                kp_ora_fine_task
                                FROM {$table_prefix}_projecttask 
                                WHERE projecttaskid = ".$parent;
                
                $res_dati_padre = $adb->query($dati_padre);
                if($adb->num_rows($res_dati_padre)>0){

                    $startdate_padre = $adb->query_result($res_dati_padre,0,'startdate');
                    $startdate_padre = html_entity_decode(strip_tags($startdate_padre), ENT_QUOTES,$default_charset);
                    
                    $enddate_padre = $adb->query_result($res_dati_padre,0,'enddate');
                    $enddate_padre = html_entity_decode(strip_tags($enddate_padre), ENT_QUOTES,$default_charset);
                    
                    $starthour_padre = $adb->query_result($res_dati_padre,0,'kp_ora_inizio_task');
                    $starthour_padre = html_entity_decode(strip_tags($starthour_padre), ENT_QUOTES,$default_charset);
                    
                    $endhour_padre = $adb->query_result($res_dati_padre,0,'kp_ora_fine_task');
                    $endhour_padre = html_entity_decode(strip_tags($endhour_padre), ENT_QUOTES,$default_charset);
                    
                    if($startdate_padre != null && $startdate_padre != ''){
                        list($annoStartPadre,$meseStartPadre,$giornoStartPadre) = explode("-",$startdate_padre);
                        $startdatehour_padre_inv = date("d-m-Y",mktime(0,0,0,$meseStartPadre,$giornoStartPadre,$annoStartPadre));

                        $startdatehour_padre_inv = $startdatehour_padre_inv." ".$starthour_padre;

                        list($oraStartPadre, $minutiStartPadre) = explode(':', $starthour_padre);
                    }   
                    
                    if($enddate_padre != null && $enddate_padre != ''){
                        list($annoEndPadre,$meseEndPadre,$giornoEndPadre) = explode("-",$enddate_padre);
                        $enddatehour_padre_inv = date("d-m-Y",mktime(0,0,0,$meseEndPadre,$giornoEndPadre,$annoEndPadre));

                        $enddatehour_padre_inv = $enddatehour_padre_inv." ".$endhour_padre;

                        list($oraEndPadre, $minutiEndPadre) = explode(':', $endhour_padre);
                    }
                    
                }
                
                $upd = "UPDATE {$table_prefix}_projecttask SET
                        tipo_attivita_task = 'Attivita'
                        WHERE projecttaskid =".$parent;
                $adb->query($upd);

                $tipo_padre = 'Attivita';
            }
            else{
                $datiPadre = aggiornamentoRisorsivoPadre($operazione,$projectstatus,$relazionato_a);
            
                $startdate_padre = $datiPadre['startdate_padre'];
                $enddate_padre = $datiPadre['enddate_padre'];
                $leadtime_padre = $datiPadre['leadtime_padre'];
                $startdatehour_padre_inv = $datiPadre['startdatehour_padre_inv'];
                $enddatehour_padre_inv = $datiPadre['enddatehour_padre_inv'];
                $giornoStartPadre = $datiPadre['giornoStartPadre'];
                $meseStartPadre = $datiPadre['meseStartPadre'];
                $annoStartPadre = $datiPadre['annoStartPadre'];
                $oraStartPadre = $datiPadre['oraStartPadre'];
                $minutiStartPadre = $datiPadre['minutiStartPadre'];
                $giornoEndPadre = $datiPadre['giornoEndPadre'];
                $meseEndPadre = $datiPadre['meseEndPadre'];
                $annoEndPadre = $datiPadre['annoEndPadre'];
                $oraEndPadre = $datiPadre['oraEndPadre'];
                $minutiEndPadre = $datiPadre['minutiEndPadre'];

                $tipo_padre = 'Raggruppamento';
            }
        }
        else{
            $tipo_padre = 'Attivita';
        }

    }

    $rows[] = array('id' => $operazione,
                    'nuovo_id' => $new_operazione_id,
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
                    'tipo_scostamento_task' => $tipo_scostamento,
                    'padre' => $parent,
                    'tipo_padre' => $tipo_padre,
                    'startdate_padre' => $startdate_padre,
                    'enddate_padre' => $enddate_padre,
                    'startdatehour_padre_inv' => $startdatehour_padre_inv,
                    'enddatehour_padre_inv' => $enddatehour_padre_inv,
                    'giornoStartPadre' => (int)$giornoStartPadre,
                    'meseStartPadre' => (int)$meseStartPadre,
                    'annoStartPadre' => (int)$annoStartPadre,
                    'oraStartPadre' => (int)$oraStartPadre,
                    'minutiStartPadre' => (int)$minutiStartPadre,
                    'giornoEndPadre' => (int)$giornoEndPadre,
                    'meseEndPadre' => (int)$meseEndPadre,
                    'annoEndPadre' => (int)$annoEndPadre,
                    'oraEndPadre' => (int)$oraEndPadre,
                    'minutiEndPadre' => (int)$minutiEndPadre);
	
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

    /* kpro@tom070220171115 */
    require_once('Utility.php');
    /* kpro@tom070220171115 end */
    
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

        /* kpro@tom070220171115 */
        if($operazionePadre != 0 && $operazionePadre != '' && $operazionePadre != null){
		    $operazionePadre = verificaEsistenzaRecord($operazionePadre);	//Verifica che l'operazione padre non sia stata cancellata
        }
		/* kpro@tom070220171115 end */

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

function calcolaPercentualeOperazione($progress){
    global $adb, $table_prefix, $default_charset;
    
    $progress = $progress * 100;
    switch ($progress) {
        case ($progress < 6):
            $progress = "--none--";
            break;
        case ($progress >= 6 && $progress < 16):
            $progress = "10%";
            break;
        case ($progress >= 16 && $progress < 26):
            $progress = "20%";
            break;
        case ($progress >= 26 && $progress < 36):
            $progress = "30%";
            break;
        case ($progress >= 36 && $progress < 46):
            $progress = "40%";
            break;
        case ($progress >= 46 && $progress < 56):
            $progress = "50%";
            break;
        case ($progress >= 56 && $progress < 66):
            $progress = "60%";
            break;
        case ($progress >= 66 && $progress < 76):
            $progress = "70%";
            break;
        case ($progress >= 76 && $progress < 86):
            $progress = "80%";
            break;
        case ($progress >= 86 && $progress < 96):
            $progress = "90%";
            break;
        case ($progress >= 96):
            $progress = "100%";
            break;
        default:
           $progress = "--none--";
    }
    
    return $progress;
    
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

function AggiornaTicketCollegati($task, $project){
    global $adb, $table_prefix, $default_charset;
    
    require_once('Utility.php');
    
    $q = "SELECT tick.ticketid,
        ent.smownerid
        FROM {$table_prefix}_troubletickets tick
        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
        WHERE ent.deleted = 0 AND tick.projecttaskid = ".$task;
    $res = $adb->query($q);
    $num = $adb->num_rows($res);
    if($num > 0){
        for($i = 0; $i < $num; $i++){
            $ticket = $adb->query_result($res, $i, 'ticketid');
            $ticket = html_entity_decode(strip_tags($ticket), ENT_QUOTES,$default_charset);

            $utente = $adb->query_result($res, $i, 'smownerid');
            $utente = html_entity_decode(strip_tags($utente), ENT_QUOTES,$default_charset);

            $dati_per_ticket = GetDatiPerTicket($task);
            $dati_utente = GetDatiUtente($utente);

            $titolo_ticket = $dati_per_ticket['nome_task']." - ".$dati_utente['cognome']." ".$dati_utente['nome'];

            $focus_ticket = CRMEntity::getInstance('HelpDesk');
            $focus_ticket->retrieve_entity_info($ticket, "HelpDesk"); 
            $focus_ticket->column_fields['ticket_title'] =  $titolo_ticket;
            $focus_ticket->column_fields['kp_data_consegna'] = $dati_per_ticket['data_consegna'];
            $focus_ticket->column_fields['parent_id'] = $dati_per_ticket['id_azienda'];
            $focus_ticket->column_fields['kp_business_unit'] = $dati_per_ticket['business_unit'];
            $focus_ticket->column_fields['kp_agente'] = $dati_per_ticket['agente'];
            $focus_ticket->column_fields['kp_km_percorsi'] = $dati_per_ticket['km_percorsi'];
            $focus_ticket->column_fields['kp_ore_viaggio'] = $dati_per_ticket['ore_viaggio'];
            $focus_ticket->column_fields['servizio'] = $dati_per_ticket['id_servizio'];
            $focus_ticket->column_fields['commessa'] = $dati_per_ticket['commessa'];
            $focus_ticket->column_fields['area_aziendale'] = $dati_per_ticket['area_aziendale'];
            $focus_ticket->column_fields['projectplanid'] = $project;
            $focus_ticket->column_fields['projecttaskid'] = $task;
            $focus_ticket->mode = 'edit';
            $focus_ticket->id = $ticket;
            $focus_ticket->save('HelpDesk', $longdesc=true, $offline_update=false, $triggerEvent=false);

        }
    }

}
	
?>