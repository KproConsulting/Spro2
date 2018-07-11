<?php

/* kpro@tom3012015 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2015, Kpro Consulting Srl
 * @package ganttPianificazioni
 * @version 1.0
 */

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

require_once('Utility.php');

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

    $nuova_operazione = duplicaTask($operazione);
    
    duplicaTickets($operazione,$nuova_operazione);
    
    spostaLinkInUscitaDallaVecchiaAllaNuovaTask($operazione,$nuova_operazione);
    
    creaLinkTraVecchiaENuovaTask($operazione,$nuova_operazione);
    
    $tot_ore_previste = calcolaOrePrevisteTask($nuova_operazione);
    
    $upd_task = "UPDATE {$table_prefix}_projecttask SET
                    projecttaskprogress = '100%',
                    kp_stato_op_pian = 'Chiusa'
                    WHERE projecttaskid=".$operazione;
    $adb->query($upd_task);
    
    $rows[] = array('new_operazione_id' => $new_operazione_id);
    
}

$json = json_encode($rows);
print $json;

function duplicaTask($task){
    global $adb, $table_prefix, $default_charset, $current_user;
    
    /* kpro@tom170315 */
    
    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package ganttPianificazioni
     * @version 2.0
     */
    
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
                        task.tipo_scostamento_task tipo_scostamento_task,
                        task.tipo_attivita_task tipo_attivita_task,
                        task.data_inizio_pian data_inizio_pian,
                        task.lead_time_pian lead_time_pian,
                        task.risorsa risorsa,
                        task.kp_ora_inizio_task kp_ora_inizio_task,
                        task.kp_ora_fine_task kp_ora_fine_task,
                        task.projectid projectid,
                        task.kp_servizio kp_servizio,
                        ent.smownerid assegnatario,
                        task.description descrizione,
                        proj.projectstatus projectstatus,
                        proj.linktoaccountscontacts linktoaccountscontacts
                        FROM {$table_prefix}_projecttask task
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = task.projecttaskid
                        INNER JOIN {$table_prefix}_project proj ON proj.projectid = task.projectid
                        WHERE ent.deleted = 0 AND task.projecttaskid =".$task;
    
    $res_operazione = $adb->query($q_operazione);
    if($adb->num_rows($res_operazione)>0){
        
        $projecttaskname = $adb->query_result($res_operazione,0,'projecttaskname');
        $projecttaskname = html_entity_decode(strip_tags($projecttaskname), ENT_QUOTES,$default_charset);
        
        $projecttasktype = $adb->query_result($res_operazione,0,'projecttasktype');
        $projecttasktype = html_entity_decode(strip_tags($projecttasktype), ENT_QUOTES,$default_charset);
        
        $projecttaskpriority = $adb->query_result($res_operazione,0,'projecttaskpriority');
        $projecttaskpriority = html_entity_decode(strip_tags($projecttaskpriority), ENT_QUOTES,$default_charset);
        
        $startdate= $adb->query_result($res_operazione,0,'startdate');
        $startdate = html_entity_decode(strip_tags($startdate), ENT_QUOTES,$default_charset);
        if($startdate == null || $startdate == "1900-01-01"){
            $startdate = "";
        }
        
        $enddate = $adb->query_result($res_operazione,0,'enddate');
        $enddate = html_entity_decode(strip_tags($enddate), ENT_QUOTES,$default_charset);
        if($enddate == null || $enddate == "1900-01-01"){
            $enddate = "";
        }
        
        $operazione_prec = $adb->query_result($res_operazione,0,'operazione_prec');
        $operazione_prec = html_entity_decode(strip_tags($operazione_prec), ENT_QUOTES,$default_charset);
        if($operazione_prec == null || $operazione_prec == ""){
            $operazione_prec = 0;
        }
        
        $lead_time = $adb->query_result($res_operazione,0,'lead_time');
        $lead_time = html_entity_decode(strip_tags($lead_time), ENT_QUOTES,$default_charset);
        if($lead_time == null || $lead_time == ""){
            $lead_time = 1;
        }
        
        $data_fine_pianificata = $adb->query_result($res_operazione,0,'data_fine_pianificata');
        $data_fine_pianificata = html_entity_decode(strip_tags($data_fine_pianificata), ENT_QUOTES,$default_charset);
        if($data_fine_pianificata == null || $data_fine_pianificata == "1900-01-01"){
            $data_fine_pianificata = "";
        }
        
        $giorni_scostamento = $adb->query_result($res_operazione,0,'giorni_scostamento');
        $giorni_scostamento = html_entity_decode(strip_tags($giorni_scostamento), ENT_QUOTES,$default_charset);
        
        $tipo_scostamento_task = $adb->query_result($res_operazione,0,'tipo_scostamento_task');
        $tipo_scostamento_task = html_entity_decode(strip_tags($tipo_scostamento_task), ENT_QUOTES,$default_charset);
        
        $tipo_attivita_task = $adb->query_result($res_operazione,0,'tipo_attivita_task');
        $tipo_attivita_task = html_entity_decode(strip_tags($tipo_attivita_task), ENT_QUOTES,$default_charset);
        
        $data_inizio_pian = $adb->query_result($res_operazione,0,'data_inizio_pian');
        $data_inizio_pian = html_entity_decode(strip_tags($data_inizio_pian), ENT_QUOTES,$default_charset);
        if($data_inizio_pian == null || $data_inizio_pian == "1900-01-01"){
            $data_inizio_pian = "";
        }
        
        $lead_time_pian = $adb->query_result($res_operazione,0,'lead_time_pian');
        $lead_time_pian = html_entity_decode(strip_tags($lead_time_pian), ENT_QUOTES,$default_charset);
        if($lead_time_pian == null || $lead_time_pian == ""){
            $lead_time_pian = 1;
        }
        
        $risorsa = $adb->query_result($res_operazione,0,'risorsa');
        $risorsa = html_entity_decode(strip_tags($risorsa), ENT_QUOTES,$default_charset);

        $ora_inizio_task = $adb->query_result($res_operazione,0,'kp_ora_inizio_task');
        $ora_inizio_task = html_entity_decode(strip_tags($ora_inizio_task), ENT_QUOTES,$default_charset);
        
        $ora_fine_task = $adb->query_result($res_operazione,0,'kp_ora_fine_task');
        $ora_fine_task = html_entity_decode(strip_tags($ora_fine_task), ENT_QUOTES,$default_charset);
        
        $assegnatario = $adb->query_result($res_operazione,0,'assegnatario');
        $assegnatario = html_entity_decode(strip_tags($assegnatario), ENT_QUOTES,$default_charset);
        
        $descrizione = $adb->query_result($res_operazione,0,'descrizione');
        $descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES,$default_charset);
        
        $projectid = $adb->query_result($res_operazione,0,'projectid');
        $projectid = html_entity_decode(strip_tags($projectid), ENT_QUOTES,$default_charset);
        
        $projectstatus = $adb->query_result($res_operazione,0,'projectstatus');
        $projectstatus = html_entity_decode(strip_tags($projectstatus), ENT_QUOTES,$default_charset);
        
        $relazionato_a = $adb->query_result($res_operazione,0,'linktoaccountscontacts');
        $relazionato_a = html_entity_decode(strip_tags($relazionato_a), ENT_QUOTES,$default_charset);
        if($relazionato_a == null || $relazionato_a == ''){
            $relazionato_a = 0;
        }
        
        $servizio = $adb->query_result($res_operazione,0,'kp_servizio');
        $servizio = html_entity_decode(strip_tags($servizio), ENT_QUOTES,$default_charset);
        if($servizio == null || $servizio == ''){
            $servizio = 0;
        }

    }
    
    
    $new_operazione = CRMEntity::getInstance('ProjectTask'); 
    $new_operazione->column_fields['assigned_user_id'] = $assegnatario;
    $new_operazione->column_fields['projectid'] = $projectid;
    $new_operazione->column_fields['projecttaskname'] = $projecttaskname." (Spez.)";
    $new_operazione->column_fields['startdate'] = $startdate;
    $new_operazione->column_fields['enddate'] = $enddate;
    $new_operazione->column_fields['kp_ora_inizio_task'] = $ora_inizio_task;
    $new_operazione->column_fields['kp_ora_fine_task'] = $ora_fine_task;
    if($operazione_prec != null && $operazione_prec != "" && $operazione_prec != 0){
        $new_operazione->column_fields['operazione_prec'] = $operazione_prec;
    }
    $new_operazione->column_fields['lead_time'] = $lead_time;
    $new_operazione->column_fields['projecttaskprogress'] = "--none--";
    $new_operazione->column_fields['projecttaskpriority'] = $projecttaskpriority;
    $new_operazione->column_fields['tipo_attivita_task'] = $tipo_attivita_task;
    $new_operazione->column_fields['giorni_scostamento'] = $giorni_scostamento;
    $new_operazione->column_fields['data_fine_pianificata'] = $data_fine_pianificata;
    $new_operazione->column_fields['data_inizio_pian'] = $data_inizio_pian;
    $new_operazione->column_fields['lead_time_pian'] = $lead_time_pian;
    $new_operazione->column_fields['tipo_scostamento_task'] = $tipo_scostamento_task;
    if($relazionato_a != null && $relazionato_a != '' && $relazionato_a != 0){
        $new_operazione->column_fields['kp_relazionato_a'] = $relazionato_a;
    }
    $new_operazione->column_fields['kp_stato_pian'] = $projectstatus;
    $new_operazione->column_fields['kp_stato_op_pian'] = 'Attiva';
    $new_operazione->column_fields['risorsa'] = $risorsa;
    $new_operazione->column_fields['kp_task_spezzata'] = '1';
    $new_operazione->column_fields['kp_spezzata_da'] = $projecttaskname;
    if($servizio != null && $servizio != '' && $servizio != 0){
		$new_operazione->column_fields['kp_servizio'] = $servizio;
	}
    $new_operazione->column_fields['description'] = $descrizione;
    $new_operazione->save('ProjectTask', $longdesc=true, $offline_update=false, $triggerEvent=false); 
    $new_operazione_id = $new_operazione->id;
    
    return $new_operazione_id;
      
}

function duplicaTickets($task_origine, $task_destinazione){
    global $adb, $table_prefix, $default_charset;
    
    /* kpro@tom170315 */
    
    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package ganttPianificazioni
     * @version 2.0
     */

    $array_colonne = array(
        'title',
        'servizio',
        'area_aziendale',
        'parent_id',
        'salesorder',
        'kp_business_unit',
        'kp_agente',
        'commessa',
        'comment_line',
        'kp_data_elem_rif',
        'da_fatturare',
        'kp_stabilimento',
        'severity',
        'kp_fornitore',
        'kp_data_consegna',
        'canone',
        'projectplanid',
        'smownerid',
        'description'
    );
      
    $q_dati_ticket = "SELECT *
                FROM {$table_prefix}_troubletickets tick
                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
                WHERE ent.deleted = 0 AND tick.projecttaskid = ".$task_origine;
    
    $res_dati_ticket = $adb->query($q_dati_ticket);
    $num_dati_ticket = $adb->num_rows($res_dati_ticket);

    for($i = 0; $i < $num_dati_ticket; $i++){
        $old_ticketid = $adb->query_result($res_dati_ticket, $i, 'ticketid');
        $old_ticketid = html_entity_decode(strip_tags($old_ticketid), ENT_QUOTES,$default_charset);
        if($old_ticketid == "" || $old_ticketid == null){
            $old_ticketid = 0;
        }

        $prezzo = $adb->query_result($res_dati_ticket, $i, 'prezzo');
        $prezzo = html_entity_decode(strip_tags($prezzo), ENT_QUOTES,$default_charset);
        if($prezzo == "" || $prezzo == null){
            $prezzo = 0;
        }
        
        $so_line_id = $adb->query_result($res_dati_ticket, $i, 'so_line_id');
        $so_line_id = html_entity_decode(strip_tags($so_line_id), ENT_QUOTES,$default_charset);
        if($so_line_id == "" || $so_line_id == null){
            $so_line_id = 0;
        }
        
        $listprice = $adb->query_result($res_dati_ticket, $i, 'listprice');
        $listprice = html_entity_decode(strip_tags($listprice), ENT_QUOTES,$default_charset);
        if($listprice == "" || $listprice == null){
            $listprice = 0;
        }
        
        $quantity = $adb->query_result($res_dati_ticket, $i, 'quantity');
        $quantity = html_entity_decode(strip_tags($quantity), ENT_QUOTES,$default_charset);
        if($quantity == "" || $quantity == null){
            $quantity = 0;
        }
        
        $discount_percent = $adb->query_result($res_dati_ticket, $i, 'discount_percent');
        $discount_percent = html_entity_decode(strip_tags($discount_percent), ENT_QUOTES,$default_charset);
        if($discount_percent == "" || $discount_percent == null){
            $discount_percent = 0;
        }
        
        $discount_amount = $adb->query_result($res_dati_ticket, $i, 'discount_amount');
        $discount_amount = html_entity_decode(strip_tags($discount_amount), ENT_QUOTES,$default_charset);
        if($discount_amount == "" || $discount_amount == null){
            $discount_amount = 0;
        }
        
        $total_notaxes = $adb->query_result($res_dati_ticket, $i, 'total_notaxes');
        $total_notaxes = html_entity_decode(strip_tags($total_notaxes), ENT_QUOTES,$default_charset);
        if($total_notaxes == "" || $total_notaxes == null){
            $total_notaxes = 0;
        }

        $ore_previste = $adb->query_result($res_dati_ticket, $i, 'kp_tempo_previsto');
        $ore_previste = html_entity_decode(strip_tags($ore_previste), ENT_QUOTES,$default_charset);
        if($ore_previste == "" || $ore_previste == null){
            $ore_previste = 0;
        }

        $ore_lavorate = $adb->query_result($res_dati_ticket, $i, 'hours');
        $ore_lavorate = html_entity_decode(strip_tags($ore_lavorate), ENT_QUOTES,$default_charset);
        if($ore_lavorate == "" || $ore_lavorate == null){
            $ore_lavorate = 0;
        }
        
        if($ore_previste > $ore_lavorate){
            $residuo_ore = $ore_previste - $ore_lavorate;
        }
        else{
            $residuo_ore = 0;
        }

        $new_ticket = CRMEntity::getInstance('HelpDesk');
        $new_ticket->column_fields['ticketstatus'] = 'Open';
        $new_ticket->column_fields['projecttaskid'] = $task_destinazione;

        foreach($array_colonne as $nome_colonna){
            $nome_campo = $this->GetFieldName($nome_colonna, 13);
            if($nome_campo != ""){
                $valore = $adb->query_result($res_dati_ticket, $i, $nome_colonna);

                $new_ticket->column_fields[$nome_campo] = $valore;

            }
        }

        $new_ticket->save('HelpDesk', $longdesc=true, $offline_update=false, $triggerEvent=false);
        $new_ticketid = $new_ticket->id;

        $upd_new_ticket = "UPDATE {$table_prefix}_troubletickets SET
                    prezzo = {$prezzo},
                    so_line_id = {$so_line_id},
                    discount_percent = {$discount_percent},
                    discount_amount = {$discount_amount},
                    total_notaxes = {$total_notaxes},
                    quantity = {$quantity},
                    listprice = {$listprice},
                    kp_tempo_previsto = {$residuo_ore}
                    WHERE ticketid = ".$new_ticketid;
        $adb->query($upd_new_ticket);
        
        $focus_ticket = CRMEntity::getInstance('HelpDesk');
        $focus_ticket->retrieve_entity_info($id, "HelpDesk"); 
        $focus_ticket->column_fields['ticketstatus'] = "Closed";
        $focus_ticket->mode = 'edit';
        $focus_ticket->id = $old_ticketid;
        $focus_ticket->save('HelpDesk', $longdesc=true, $offline_update=false, $triggerEvent=false);
        
    }
    
}

function GetFieldName($nome_colonna, $modulo){
    global $adb, $table_prefix, $default_charset;

    $q = "SELECT fieldname
        FROM {$table_prefix}_field
        WHERE tabid = {$modulo}
        AND columnname = '{$nome_colonna}'";
    $res = $adb->query($q);
    if($adb->num_rows($res) > 0){
        $fieldname = $adb->query_result($res, 0, 'fieldname');
        $fieldname = html_entity_decode(strip_tags($fieldname), ENT_QUOTES,$default_charset);
        if($fieldname == null){
            $fieldname = "";
        }
    }
    else{
        $fieldname = "";
    }

    return $fieldname;
}

function creaLinkTraVecchiaENuovaTask($task_vecchia,$task_nuova){
    global $adb, $table_prefix, $default_charset, $current_user;
    
    /* kpro@tom170315 */
    
    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package ganttPianificazioni
     * @version 2.0
     */
    
    $q_operazione = "SELECT 
                        task.projectid projectid
                        FROM {$table_prefix}_projecttask task
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = task.projecttaskid
                        WHERE ent.deleted = 0 AND task.projecttaskid =".$task_vecchia;
    
    $res_operazione = $adb->query($q_operazione);
    if($adb->num_rows($res_operazione)>0){
        
        $projectid = $adb->query_result($res_operazione,0,'projectid');
        $projectid = html_entity_decode(strip_tags($projectid), ENT_QUOTES,$default_charset);
        
        $new_relazione = CRMEntity::getInstance('RelazioniOp'); 
        $new_relazione->column_fields['assigned_user_id'] = $current_user->id;
        $new_relazione->column_fields['pianificazione'] = $projectid;
        $new_relazione->column_fields['operazione'] = $task_vecchia;
        $new_relazione->column_fields['operazione_target'] = $task_nuova;
        $new_relazione->column_fields['tipo_relazione_task'] = '0';
        $new_relazione->save('RelazioniOp', $longdesc=true, $offline_update=false, $triggerEvent=false);

    }
     
}

function spostaLinkInUscitaDallaVecchiaAllaNuovaTask($task_vecchia,$task_nuova){
    global $adb, $table_prefix, $default_charset, $current_user;
    
    /* kpro@tom170315 */
    
    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package ganttPianificazioni
     * @version 2.0
     */
    
    $q_link = "SELECT link.relazioniopid relazioniopid
                FROM {$table_prefix}_relazioniop link
                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = link.relazioniopid
                INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = link.operazione_target
                WHERE ent.deleted = 0 AND ent2.deleted = 0 AND link.tipo_relazione_task = '0' 
                AND link.operazione = ".$task_vecchia." AND link.operazione_target != ".$task_nuova;
    $res_link = $adb->query($q_link);
    $num_link = $adb->num_rows($res_link);

    for($i=0; $i<$num_link; $i++){
        $relazioniopid = $adb->query_result($res_link, $i, 'relazioniopid');
        $relazioniopid = html_entity_decode(strip_tags($relazioniopid), ENT_QUOTES,$default_charset);
        
        $upd = "UPDATE {$table_prefix}_relazioniop SET
                operazione = ".$task_nuova.",
                tipo_relazione_task = '0'
                WHERE relazioniopid=".$relazioniopid;
        $adb->query($upd);
        
    }
    
}

?>