<?php

/* kpro@tom260116 */

/**
* @author Tomiello Marco
* @copyright (c) 2016, Kpro Consulting Srl
* @package ganttPianificazioniGenerale
* @version 1.0
*/

require_once('../TicketScheduler/TicketScheduler_utils.php');

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $default_charset;
session_start();

if(!isset($_SESSION['authenticated_user_id'])){
    header("Location: ".$site_URL."/index.php?module=Accounts&action=index");
}
$current_user->id = $_SESSION['authenticated_user_id'];
/*
require('user_privileges/requireUserPrivileges.php'); 
require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

$fieldlabel = 'Assigned To';
global $noof_group_rows;

$editview_label[]=getTranslatedString($fieldlabel, $module_name);

//Security Checks
if($fieldlabel == 'Assigned To' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
{
    $result=get_current_user_access_groups($module_name);
}
else
{
    $result = get_group_options();
}
if($result) $nameArray = $adb->fetch_array($result);

if($value != '' && $value != 0)
    $assigned_user_id = $value;
else
    $assigned_user_id = $current_user->id;
if($fieldlabel == 'Assigned To' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
{
    $users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id,'private'), $assigned_user_id);
}
else
{
    $users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id), $assigned_user_id);
}
if($noof_group_rows!=0)
{
	$groups_combo = '';
    if($fieldlabel == 'Assigned To' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
    {
        $groups_combo = get_select_options_array(get_group_array(FALSE, "Active", $assigned_user_id,'private'), $assigned_user_id);
    }
    else
    {
        $groups_combo = get_select_options_array(get_group_array(FALSE, "Active", $assigned_user_id), $assigned_user_id);
    }
}

$fieldvalue[]= $users_combo;
$fieldvalue_group[] = $groups_combo;

$myArray = $fieldvalue[0];
$keys = array_keys($myArray);

if($groups_combo != ''){
	$myArray_group = $fieldvalue_group[0];
	$keys_group = array_keys($myArray_group);
}

$elementCount  = count($fieldvalue[0]) + count($fieldvalue_group[0]);
$elementCountUser  = count($fieldvalue[0]);
$newArray = array();

$lista_assegnatari = '(';
for($y=0; $y<$elementCountUser; $y++){
    $user_id = $keys[$y];
    $queryUsers = "SELECT id, user_name 'username', first_name, last_name FROM {$table_prefix}_users WHERE id = {$user_id}";
    $result = $adb->query($queryUsers);
    $id = $adb->query_result($result, 0, 'id');
    $userName = $adb->query_result($result, 0, 'username');
    $firstName = $adb->query_result($result, 0, 'first_name');
    $lastName = $adb->query_result($result, 0, 'last_name');
    $newArray[] = array('id' => $id,
                        'user_name' => $userName,
                        'first_name' => $firstName,
                        'last_name' => $lastName);
	
	if($lista_assegnatari == '('){
		$lista_assegnatari  .= $id;
	}
	else{
		$lista_assegnatari  .= ",".$id;
	}
	
}

$elementCountGroup  = count($fieldvalue_group[0]);

for($y=0; $y<$elementCountGroup; $y++){
	$group_id = $keys_group[$y];
	$queryGroup = "SELECT groupid, groupname, description FROM {$table_prefix}_groups WHERE groupid = {$group_id}";

	$result_group = $adb->query($queryGroup);
    $groupid = $adb->query_result($result_group, 0, 'groupid');
    $groupname = $adb->query_result($result_group, 0, 'groupname');
    $description = $adb->query_result($result_group, 0, 'description');

    $newArray[] = array('id' => $groupid,
                        'user_name' => 'Gruppo: '.$groupname,
                        'first_name' => $description,
                        'last_name' => "");
						
	if($lista_assegnatari == '('){
		$lista_assegnatari  .= $groupid;
	}
	else{
		$lista_assegnatari  .= ",".$groupid;
	}
	
}

$lista_assegnatari .= ')';*/

$data_corrente = date ("Y-m-d");
list($anno_corrente,$mese_corrente,$giorno_corrente) = explode("-",$data_corrente);
$data_corrente_inv = date("d-m-Y",mktime(0,0,0,$mese_corrente,$giorno_corrente,$anno_corrente));

$rows = array();

if(isset($_GET['progetto']) && isset($_GET['risorse_selezionate'])){
    $risorse_selezionate = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['risorse_selezionate']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    
    $progetto = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['progetto']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $progetto = substr($progetto,0,100);
    
    if(isset($_GET['filtro_pianificazioni'])){
	$filtro_pianificazioni = addslashes(html_entity_decode(strip_tags($_GET['filtro_pianificazioni']), ENT_QUOTES,$default_charset));
	$filtro_pianificazioni = substr($filtro_pianificazioni,0,255);
        if($filtro_pianificazioni == '' || $filtro_pianificazioni == 'none'){
            $filtro_pianificazioni = 'all';
        }
    }
    else{
        $filtro_pianificazioni = 'all';
    }
    
    if(isset($_GET['filtro_calendario'])){
	$filtro_calendario = addslashes(html_entity_decode(strip_tags($_GET['filtro_calendario']), ENT_QUOTES,$default_charset));
	$filtro_calendario = substr($filtro_calendario,0,255);
        if($filtro_calendario == ''){
            $filtro_calendario = 'si';
        }
    }
    else{
        $filtro_calendario = 'si';
    }
    
    if(isset($_GET['filtro_ticket'])){
	$filtro_ticket = addslashes(html_entity_decode(strip_tags($_GET['filtro_ticket']), ENT_QUOTES,$default_charset));
	$filtro_ticket = substr($filtro_ticket,0,255);
        if($filtro_ticket == ''){
            $filtro_ticket = 'si';
        }
    }
    else{
        $filtro_ticket = 'si';
    }
    
    $lista_projectresourcesid = "";
    
    $q_risorse = "SELECT 
                    res.projectresourcesid projectresourcesid,
                    res.cognome cognome,
                    res.nome nome,
                    res.colore colore
                    FROM {$table_prefix}_projectresources res
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = res.projectresourcesid
                    WHERE ent.deleted = 0";
    
    if($risorse_selezionate != '' && $risorse_selezionate != null && $risorse_selezionate != 'none' && $risorse_selezionate != 'All'){
        $q_risorse .= " AND res.projectresourcesid IN (".$risorse_selezionate.")";
    }
    
    $res_risorse  = $adb->query($q_risorse);
    $num_risorse = $adb->num_rows($res_risorse);

    for($i=0; $i<$num_risorse; $i++){
        $projectresourcesid = $adb->query_result($res_risorse, $i, 'projectresourcesid');
        $projectresourcesid = html_entity_decode(strip_tags($projectresourcesid), ENT_QUOTES,$default_charset);
        
        if($lista_projectresourcesid == ""){
			$lista_projectresourcesid .= $projectresourcesid;
		}
		else{
			$lista_projectresourcesid .= ",".$projectresourcesid;
		}

        $cognome = $adb->query_result($res_risorse, $i, 'cognome');
        $cognome = html_entity_decode(strip_tags($cognome), ENT_QUOTES,$default_charset);
        
        $nome = $adb->query_result($res_risorse, $i, 'nome');
        $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES,$default_charset);

        $colore = $adb->query_result($res_risorse, $i, 'colore');
        $colore = html_entity_decode(strip_tags($colore), ENT_QUOTES,$default_charset);
        if($colore == null || $colore == ''){
            $colore = "Green";
        }
    }
             
	if($filtro_pianificazioni == 'all' || $filtro_pianificazioni == 'All'){
		
		$q_operazioni_in_altre_pianificazioni = "SELECT 
													taskres.taskresourcesid taskresourcesid,
													taskres.task task,
													taskres.ore_previste ore_previste,
													task.projecttaskname projecttaskname,
													task.startdate startdate,
													task.enddate enddate,
													task.kp_ora_inizio_task ora_inizio_task,
													task.kp_ora_fine_task ora_fine_task,
													proj.projectname projectname,
													task.projectid projectid,
													task.lead_time lead_time,
													taskres.risorsa risorsa
													FROM {$table_prefix}_taskresources taskres
													INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = taskres.taskresourcesid
													INNER JOIN {$table_prefix}_projecttask task ON task.projecttaskid = taskres.task
													INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = task.projecttaskid 
													INNER JOIN {$table_prefix}_project proj ON proj.projectid = task.projectid
													WHERE ent.deleted = 0 AND ent2.deleted = 0 AND task.tipo_attivita_task = 'Attivita' AND proj.projectstatus IN ('In corso', 'Aperto', 'In pianificazione', 'prospecting', 'in progress', 'initiated') AND task.projecttaskprogress != '100%' AND task.kp_stato_op_pian NOT IN ('Chiusa', 'Sospesa') AND taskres.risorsa IN (".$lista_projectresourcesid.") AND task.projectid NOT IN (".$progetto.")";

		$res_operazioni_in_altre_pianificazioni = $adb->query($q_operazioni_in_altre_pianificazioni);
		$num_operazioni_in_altre_pianificazioni = $adb->num_rows($res_operazioni_in_altre_pianificazioni);

		for($y=0; $y<$num_operazioni_in_altre_pianificazioni; $y++){

			$task = $adb->query_result($res_operazioni_in_altre_pianificazioni, $y, 'task');
			$task = html_entity_decode(strip_tags($task), ENT_QUOTES,$default_charset);

			$ore_previste = $adb->query_result($res_operazioni_in_altre_pianificazioni, $y, 'lead_time');
			$ore_previste = html_entity_decode(strip_tags($ore_previste), ENT_QUOTES,$default_charset);
			if($ore_previste == null || $ore_previste == ''){
				$ore_previste = 0;
			}

			$projecttaskname = $adb->query_result($res_operazioni_in_altre_pianificazioni, $y, 'projecttaskname');
			$projecttaskname = html_entity_decode(strip_tags($projecttaskname), ENT_QUOTES,$default_charset);

			$startdate = $adb->query_result($res_operazioni_in_altre_pianificazioni, $y, 'startdate');
			$startdate = html_entity_decode(strip_tags($startdate), ENT_QUOTES,$default_charset);

			$enddate = $adb->query_result($res_operazioni_in_altre_pianificazioni, $y, 'enddate');
			$enddate = html_entity_decode(strip_tags($enddate), ENT_QUOTES,$default_charset);

			$ora_inizio_task = $adb->query_result($res_operazioni_in_altre_pianificazioni, $y, 'ora_inizio_task');
			$ora_inizio_task = html_entity_decode(strip_tags($ora_inizio_task), ENT_QUOTES,$default_charset);

			$ora_fine_task = $adb->query_result($res_operazioni_in_altre_pianificazioni, $y, 'ora_fine_task');
			$ora_fine_task = html_entity_decode(strip_tags($ora_fine_task), ENT_QUOTES,$default_charset);

			$projectname = $adb->query_result($res_operazioni_in_altre_pianificazioni, $y, 'projectname');
			$projectname = html_entity_decode(strip_tags($projectname), ENT_QUOTES,$default_charset);
			
			$projectid = $adb->query_result($res_operazioni_in_altre_pianificazioni, $y, 'projectid');
			$projectid = html_entity_decode(strip_tags($projectid), ENT_QUOTES,$default_charset);
			
			$risorsa = $adb->query_result($res_operazioni_in_altre_pianificazioni, $y, 'risorsa');
			$risorsa = html_entity_decode(strip_tags($risorsa), ENT_QUOTES,$default_charset);

			if($startdate != null && $startdate != ""){

				list($anno,$mese,$giorno) = explode("-",$startdate);
				$startdate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));

				$startdate = $startdate." ".$ora_inizio_task;
			}
			else{

				list($anno,$mese,$giorno) = explode("-",$data_corrente);
				$startdate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));

				$startdate = $data_corrente." ".$ora_inizio_task;
			}

			if($enddate != null && $enddate != ""){

				list($anno,$mese,$giorno) = explode("-",$enddate);
				$enddate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));

				$enddate = $enddate." ".$ora_fine_task;
			}
			else{

				list($anno,$mese,$giorno) = explode("-",$data_corrente);
				$enddate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));

				$enddate = $data_corrente." ".$ora_fine_task;
			}

			$colore = "white";
			
			if($ore_previste > 0){
				
				$rows[] = array('id' => $task."_".$risorsa,
								'taskid' => $task,
								'start_date' => $startdate,
								'end_date' => $enddate,
								'text' => $projecttaskname,
								'color' => $colore,
								'risorsa' => $risorsa,
								'startdate_inv' => $startdate_inv,
								'enddate_inv' => $enddate_inv,
								'ora_inizio' => $ora_inizio_task,
								'ora_fine' => $ora_fine_task,
								'ore_previste' => $ore_previste,
								'projectname' => $projectname,
								'projectid' => $projectid,
								'pianificazione_corrente' => 'no');
								
			}

		}
		
		$q_operazioni = "SELECT 
                            taskres.taskresourcesid taskresourcesid,
                            taskres.task task,
                            taskres.ore_previste ore_previste,
                            task.projecttaskname projecttaskname,
                            task.startdate startdate,
                            task.enddate enddate,
                            task.kp_ora_inizio_task ora_inizio_task,
                            task.kp_ora_fine_task ora_fine_task,
                            proj.projectname projectname,
							task.projectid projectid,
							task.lead_time lead_time,
							taskres.risorsa risorsa
                            FROM {$table_prefix}_taskresources taskres
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = taskres.taskresourcesid
                            INNER JOIN {$table_prefix}_projecttask task ON task.projecttaskid = taskres.task
                            INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = task.projecttaskid
                            INNER JOIN {$table_prefix}_project proj ON proj.projectid = task.projectid
                            WHERE ent.deleted = 0 AND ent2.deleted = 0 AND task.tipo_attivita_task = 'Attivita'
                            AND proj.projectstatus IN ('In corso', 'Aperto', 'In pianificazione', 'prospecting', 'in progress', 'initiated') 
                            AND task.projecttaskprogress != '100%' AND (task.kp_stato_op_pian NOT IN ('Chiusa', 'Sospesa') OR task.kp_stato_op_pian is null)
                            AND taskres.risorsa IN (".$lista_projectresourcesid.") AND task.projectid IN (".$progetto.")";
                         
        $res_operazioni = $adb->query($q_operazioni);
        $num_operazioni = $adb->num_rows($res_operazioni);

        for($y=0; $y<$num_operazioni; $y++){
            
            $task = $adb->query_result($res_operazioni, $y, 'task');
            $task = html_entity_decode(strip_tags($task), ENT_QUOTES,$default_charset);
            
            $ore_previste = $adb->query_result($res_operazioni, $y, 'lead_time');
            $ore_previste = html_entity_decode(strip_tags($ore_previste), ENT_QUOTES,$default_charset);
            if($ore_previste == null || $ore_previste == ''){
                $ore_previste = 0;
            }
            
            $projecttaskname = $adb->query_result($res_operazioni, $y, 'projecttaskname');
            $projecttaskname = html_entity_decode(strip_tags($projecttaskname), ENT_QUOTES,$default_charset);
            
            $startdate = $adb->query_result($res_operazioni, $y, 'startdate');
            $startdate = html_entity_decode(strip_tags($startdate), ENT_QUOTES,$default_charset);
            
            $enddate = $adb->query_result($res_operazioni, $y, 'enddate');
            $enddate = html_entity_decode(strip_tags($enddate), ENT_QUOTES,$default_charset);
            
            $ora_inizio_task = $adb->query_result($res_operazioni, $y, 'ora_inizio_task');
            $ora_inizio_task = html_entity_decode(strip_tags($ora_inizio_task), ENT_QUOTES,$default_charset);
            
            $ora_fine_task = $adb->query_result($res_operazioni, $y, 'ora_fine_task');
            $ora_fine_task = html_entity_decode(strip_tags($ora_fine_task), ENT_QUOTES,$default_charset);
            
            $projectname = $adb->query_result($res_operazioni, $y, 'projectname');
            $projectname = html_entity_decode(strip_tags($projectname), ENT_QUOTES,$default_charset);
            
            $projectid = $adb->query_result($res_operazioni, $y, 'projectid');
            $projectid = html_entity_decode(strip_tags($projectid), ENT_QUOTES,$default_charset);
            
            $risorsa = $adb->query_result($res_operazioni, $y, 'risorsa');
            $risorsa = html_entity_decode(strip_tags($risorsa), ENT_QUOTES,$default_charset);
            
            if($startdate != null && $startdate != ""){
                
                list($anno,$mese,$giorno) = explode("-",$startdate);
                $startdate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
                
                $startdate = $startdate." ".$ora_inizio_task;
            }
            else{
                
                list($anno,$mese,$giorno) = explode("-",$data_corrente);
                $startdate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
                
                $startdate = $data_corrente." ".$ora_inizio_task;
            }
            
            if($enddate != null && $enddate != ""){
                
                list($anno,$mese,$giorno) = explode("-",$enddate);
                $enddate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
                
                $enddate = $enddate." ".$ora_fine_task;
            }
            else{
                
                list($anno,$mese,$giorno) = explode("-",$data_corrente);
                $enddate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
                
                $enddate = $data_corrente." ".$ora_fine_task;
            }
            
            $colore = "white";
            
            if($ore_previste > 0){
	            $rows[] = array('id' => $task."_".$risorsa,
	                            'taskid' => $task,
	                            'start_date' => $startdate,
	                            'end_date' => $enddate,
	                            'text' => $projecttaskname,
	                            'color' => $colore,
	                            'risorsa' => $risorsa,
	                            'startdate_inv' => $startdate_inv,
	                            'enddate_inv' => $enddate_inv,
	                            'ora_inizio' => $ora_inizio_task,
	                            'ora_fine' => $ora_fine_task,
	                            'ore_previste' => $ore_previste,
	                            'projectname' => $projectname,
	                            'projectid' => $projectid,
	                            'pianificazione_corrente' => 'si');
	        }
                     
        }
	
	}
	else{
		
		$lista_pianificazioni = explode(",", $filtro_pianificazioni); 
		
		$lista_pianificazioni_temp = "";
		$visualizza_atri = "false";
		
		for($i = 0; $i < count($lista_pianificazioni); $i++){
			
			if($lista_pianificazioni[$i] == "altri"){
				$visualizza_atri = "true";
			}
			else{
				if($lista_pianificazioni_temp == ""){
					$lista_pianificazioni_temp .= $lista_pianificazioni[$i];
				}
				else{
					$lista_pianificazioni_temp .= ",".$lista_pianificazioni[$i];
				}
			}
		
		}
		
		if($lista_pianificazioni_temp != ""){
			
			$q_operazioni = "SELECT 
	                            taskres.taskresourcesid taskresourcesid,
	                            taskres.task task,
	                            taskres.ore_previste ore_previste,
	                            task.projecttaskname projecttaskname,
	                            task.startdate startdate,
	                            task.enddate enddate,
	                            task.kp_ora_inizio_task ora_inizio_task,
	                            task.kp_ora_fine_task ora_fine_task,
	                            proj.projectname projectname,
								task.projectid projectid,
								task.lead_time lead_time,
								taskres.risorsa risorsa
	                            FROM {$table_prefix}_taskresources taskres
	                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = taskres.taskresourcesid
	                            INNER JOIN {$table_prefix}_projecttask task ON task.projecttaskid = taskres.task
	                            INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = task.projecttaskid
	                            INNER JOIN {$table_prefix}_project proj ON proj.projectid = task.projectid
	                            WHERE ent.deleted = 0 AND ent2.deleted = 0 AND task.tipo_attivita_task = 'Attivita'
	                            AND proj.projectstatus IN ('In corso', 'Aperto', 'In pianificazione', 'prospecting', 'in progress', 'initiated') 
	                            AND task.projecttaskprogress != '100%' AND (task.kp_stato_op_pian NOT IN ('Chiusa', 'Sospesa') OR task.kp_stato_op_pian is null)
	                            AND taskres.risorsa IN (".$lista_projectresourcesid.") AND task.projectid IN (".$lista_pianificazioni_temp.")";
	                   
	        $res_operazioni = $adb->query($q_operazioni);
	        $num_operazioni = $adb->num_rows($res_operazioni);
	
	        for($y=0; $y<$num_operazioni; $y++){
	            
	            $task = $adb->query_result($res_operazioni, $y, 'task');
	            $task = html_entity_decode(strip_tags($task), ENT_QUOTES,$default_charset);
	            
	            $ore_previste = $adb->query_result($res_operazioni, $y, 'lead_time');
	            $ore_previste = html_entity_decode(strip_tags($ore_previste), ENT_QUOTES,$default_charset);
	            if($ore_previste == null || $ore_previste == ''){
	                $ore_previste = 0;
	            }
	            
	            $projecttaskname = $adb->query_result($res_operazioni, $y, 'projecttaskname');
	            $projecttaskname = html_entity_decode(strip_tags($projecttaskname), ENT_QUOTES,$default_charset);
	            
	            $startdate = $adb->query_result($res_operazioni, $y, 'startdate');
	            $startdate = html_entity_decode(strip_tags($startdate), ENT_QUOTES,$default_charset);
	            
	            $enddate = $adb->query_result($res_operazioni, $y, 'enddate');
	            $enddate = html_entity_decode(strip_tags($enddate), ENT_QUOTES,$default_charset);
	            
	            $ora_inizio_task = $adb->query_result($res_operazioni, $y, 'ora_inizio_task');
	            $ora_inizio_task = html_entity_decode(strip_tags($ora_inizio_task), ENT_QUOTES,$default_charset);
	            
	            $ora_fine_task = $adb->query_result($res_operazioni, $y, 'ora_fine_task');
	            $ora_fine_task = html_entity_decode(strip_tags($ora_fine_task), ENT_QUOTES,$default_charset);
	            
	            $projectname = $adb->query_result($res_operazioni, $y, 'projectname');
	            $projectname = html_entity_decode(strip_tags($projectname), ENT_QUOTES,$default_charset);
	            
	            $projectid = $adb->query_result($res_operazioni, $y, 'projectid');
	            $projectid = html_entity_decode(strip_tags($projectid), ENT_QUOTES,$default_charset);
	            
	            $risorsa = $adb->query_result($res_operazioni, $y, 'risorsa');
	            $risorsa = html_entity_decode(strip_tags($risorsa), ENT_QUOTES,$default_charset);
	            
	            if($startdate != null && $startdate != ""){
	                
	                list($anno,$mese,$giorno) = explode("-",$startdate);
	                $startdate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
	                
	                $startdate = $startdate." ".$ora_inizio_task;
	            }
	            else{
	                
	                list($anno,$mese,$giorno) = explode("-",$data_corrente);
	                $startdate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
	                
	                $startdate = $data_corrente." ".$ora_inizio_task;
	            }
	            
	            if($enddate != null && $enddate != ""){
	                
	                list($anno,$mese,$giorno) = explode("-",$enddate);
	                $enddate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
	                
	                $enddate = $enddate." ".$ora_fine_task;
	            }
	            else{
	                
	                list($anno,$mese,$giorno) = explode("-",$data_corrente);
	                $enddate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
	                
	                $enddate = $data_corrente." ".$ora_fine_task;
	            }
	            
	            $colore = "white";
	            
	            if($ore_previste > 0){
		            $rows[] = array('id' => $task."_".$risorsa,
		                            'taskid' => $task,
		                            'start_date' => $startdate,
		                            'end_date' => $enddate,
		                            'text' => $projecttaskname,
		                            'color' => $colore,
		                            'risorsa' => $risorsa,
		                            'startdate_inv' => $startdate_inv,
		                            'enddate_inv' => $enddate_inv,
		                            'ora_inizio' => $ora_inizio_task,
		                            'ora_fine' => $ora_fine_task,
		                            'ore_previste' => $ore_previste,
		                            'projectname' => $projectname,
		                            'projectid' => $projectid,
		                            'pianificazione_corrente' => 'si');
	            }
	                     
	        }
			
		}
		
		if($visualizza_atri == "true"){
			
			$q_operazioni = "SELECT 
	                            taskres.taskresourcesid taskresourcesid,
	                            taskres.task task,
	                            taskres.ore_previste ore_previste,
	                            task.projecttaskname projecttaskname,
	                            task.startdate startdate,
	                            task.enddate enddate,
	                            task.kp_ora_inizio_task ora_inizio_task,
	                            task.kp_ora_fine_task ora_fine_task,
	                            proj.projectname projectname,
								task.projectid projectid,
								task.lead_time lead_time,
								taskres.risorsa risorsa
	                            FROM {$table_prefix}_taskresources taskres
	                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = taskres.taskresourcesid
	                            INNER JOIN {$table_prefix}_projecttask task ON task.projecttaskid = taskres.task
	                            INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = task.projecttaskid
	                            INNER JOIN {$table_prefix}_project proj ON proj.projectid = task.projectid
	                            WHERE ent.deleted = 0 AND ent2.deleted = 0 AND task.tipo_attivita_task = 'Attivita'
	                            AND proj.projectstatus IN ('In corso', 'Aperto', 'In pianificazione', 'prospecting', 'in progress', 'initiated') 
	                            AND task.projecttaskprogress != '100%' AND (task.kp_stato_op_pian NOT IN ('Chiusa', 'Sospesa') OR task.kp_stato_op_pian is null)
	                            AND taskres.risorsa IN (".$lista_projectresourcesid.") AND task.projectid NOT IN (".$progetto.")";
	                            
	        $res_operazioni = $adb->query($q_operazioni);
	        $num_operazioni = $adb->num_rows($res_operazioni);
	
	        for($y=0; $y<$num_operazioni; $y++){
	            
	            $task = $adb->query_result($res_operazioni, $y, 'task');
	            $task = html_entity_decode(strip_tags($task), ENT_QUOTES,$default_charset);
	            
	            $ore_previste = $adb->query_result($res_operazioni, $y, 'lead_time');
	            $ore_previste = html_entity_decode(strip_tags($ore_previste), ENT_QUOTES,$default_charset);
	            if($ore_previste == null || $ore_previste == ''){
	                $ore_previste = 0;
	            }
	            
	            $projecttaskname = $adb->query_result($res_operazioni, $y, 'projecttaskname');
	            $projecttaskname = html_entity_decode(strip_tags($projecttaskname), ENT_QUOTES,$default_charset);
	            
	            $startdate = $adb->query_result($res_operazioni, $y, 'startdate');
	            $startdate = html_entity_decode(strip_tags($startdate), ENT_QUOTES,$default_charset);
	            
	            $enddate = $adb->query_result($res_operazioni, $y, 'enddate');
	            $enddate = html_entity_decode(strip_tags($enddate), ENT_QUOTES,$default_charset);
	            
	            $ora_inizio_task = $adb->query_result($res_operazioni, $y, 'ora_inizio_task');
	            $ora_inizio_task = html_entity_decode(strip_tags($ora_inizio_task), ENT_QUOTES,$default_charset);
	            
	            $ora_fine_task = $adb->query_result($res_operazioni, $y, 'ora_fine_task');
	            $ora_fine_task = html_entity_decode(strip_tags($ora_fine_task), ENT_QUOTES,$default_charset);
	            
	            $projectname = $adb->query_result($res_operazioni, $y, 'projectname');
	            $projectname = html_entity_decode(strip_tags($projectname), ENT_QUOTES,$default_charset);
	            
	            $projectid = $adb->query_result($res_operazioni, $y, 'projectid');
	            $projectid = html_entity_decode(strip_tags($projectid), ENT_QUOTES,$default_charset);
	            
	            $risorsa = $adb->query_result($res_operazioni, $y, 'risorsa');
	            $risorsa = html_entity_decode(strip_tags($risorsa), ENT_QUOTES,$default_charset);
	            
	            if($startdate != null && $startdate != ""){
	                
	                list($anno,$mese,$giorno) = explode("-",$startdate);
	                $startdate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
	                
	                $startdate = $startdate." ".$ora_inizio_task;
	            }
	            else{
	                
	                list($anno,$mese,$giorno) = explode("-",$data_corrente);
	                $startdate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
	                
	                $startdate = $data_corrente." ".$ora_inizio_task;
	            }
	            
	            if($enddate != null && $enddate != ""){
	                
	                list($anno,$mese,$giorno) = explode("-",$enddate);
	                $enddate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
	                
	                $enddate = $enddate." ".$ora_fine_task;
	            }
	            else{
	                
	                list($anno,$mese,$giorno) = explode("-",$data_corrente);
	                $enddate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
	                
	                $enddate = $data_corrente." ".$ora_fine_task;
	            }
	            
	            $colore = "white";
	            
	            if($ore_previste > 0){
		            $rows[] = array('id' => $task."_".$risorsa,
		                            'taskid' => $task,
		                            'start_date' => $startdate,
		                            'end_date' => $enddate,
		                            'text' => $projecttaskname,
		                            'color' => $colore,
		                            'risorsa' => $risorsa,
		                            'startdate_inv' => $startdate_inv,
		                            'enddate_inv' => $enddate_inv,
		                            'ora_inizio' => $ora_inizio_task,
		                            'ora_fine' => $ora_fine_task,
		                            'ore_previste' => $ore_previste,
		                            'projectname' => $projectname,
		                            'projectid' => $projectid,
		                            'pianificazione_corrente' => 'no');
	            }
	                     
	        }
			
		}

	}    
        
	if($filtro_calendario == 'si'){
		
		$q_eventi_calendario = "SELECT 
								act.activityid activityid,
								act.subject subject,
								act.date_start date_start,
								act.time_start time_start,
								act.time_end time_end,
								act.due_date due_date,
								us.kp_risorsa_pianific risorsa
								FROM {$table_prefix}_activity act
								INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = act.activityid
								INNER JOIN {$table_prefix}_users us ON us.id = ent.smownerid
								WHERE ent.deleted = 0 AND act.eventstatus = 'Planned' AND us.kp_risorsa_pianific IN (".$lista_projectresourcesid.")";
		$res_eventi_calendario = $adb->query($q_eventi_calendario);
		$num_eventi_calendario = $adb->num_rows($res_eventi_calendario);

		for($y=0; $y<$num_eventi_calendario; $y++){
			
			$activityid = $adb->query_result($res_eventi_calendario, $y, 'activityid');
			$activityid = html_entity_decode(strip_tags($activityid), ENT_QUOTES,$default_charset);

			$subject = $adb->query_result($res_eventi_calendario, $y, 'subject');
			$subject = html_entity_decode(strip_tags($subject), ENT_QUOTES,$default_charset);
			
			$date_start = $adb->query_result($res_eventi_calendario, $y, 'date_start');
			$date_start = html_entity_decode(strip_tags($date_start), ENT_QUOTES,$default_charset);
			
			$time_start = $adb->query_result($res_eventi_calendario, $y, 'time_start');
			$time_start = html_entity_decode(strip_tags($time_start), ENT_QUOTES,$default_charset);
			
			$time_end = $adb->query_result($res_eventi_calendario, $y, 'time_end');
			$time_end = html_entity_decode(strip_tags($time_end), ENT_QUOTES,$default_charset);
			
			$due_date = $adb->query_result($res_eventi_calendario, $y, 'due_date');
			$due_date = html_entity_decode(strip_tags($due_date), ENT_QUOTES,$default_charset);
			
			$risorsa = $adb->query_result($res_eventi_calendario, $y, 'risorsa');
			$risorsa = html_entity_decode(strip_tags($risorsa), ENT_QUOTES,$default_charset);
			
			if($date_start != null && $date_start != ""){
			
				list($anno,$mese,$giorno) = explode("-",$date_start);
				$startdate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));

				$startdate = $date_start." ".$time_start;
			}
			else{

				list($anno,$mese,$giorno) = explode("-",$data_corrente);
				$startdate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));

				$startdate = $data_corrente." ".$time_start;
			}

			if($due_date != null && $due_date != ""){

				list($anno,$mese,$giorno) = explode("-",$due_date);
				$enddate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));

				$enddate = $due_date." ".$time_end;
			}
			else{

				list($anno,$mese,$giorno) = explode("-",$data_corrente);
				$enddate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));

				$enddate = $data_corrente." ".$time_end;
			}

			$colore = "white";
			
			$rows[] = array('id' => $activityid."_".$risorsa,
							'taskid' => $activityid,
							'start_date' => $startdate,
							'end_date' => $enddate,
							'text' => $subject,
							'color' => $colore,
							'risorsa' => $risorsa,
							'startdate_inv' => $startdate_inv,
							'enddate_inv' => $enddate_inv,
							'ora_inizio' => $time_start,
							'ora_fine' => $time_end,
							'ore_previste' => 0,
							'projectname' => '',
							'projectid' => 0,
							'pianificazione_corrente' => 'calendario');


		}
		
	}
	
	if($filtro_ticket == 'si'){
		
		$q_eventi_ticket = "SELECT 
		                    tick.ticketid ticketid,
		                    tick.title title,
		                    tick.priority priority,
		                    tick.status status,
		                    tick.category category,
		                    tick.kp_data_inizio_pian kp_data_inizio_pian,
		                    tick.kp_data_fine_pian kp_data_fine_pian,
		                    tick.kp_ora_inizio_tick kp_ora_inizio_tick,
		                    tick.kp_ora_fine_tick kp_ora_fine_tick,
		                    tick.kp_tempo_previsto tempo_previsto,
		                    tick.parent_id parent_id,
		                    acc.accountname accountname,
		                    ent.createdtime createdtime,
		                    ent.modifiedtime modifiedtime,
							us.id utente,
							us.kp_risorsa_pianific risorsa
		                    FROM {$table_prefix}_troubletickets tick
		                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
		                    INNER JOIN {$table_prefix}_users us ON us.id = ent.smownerid
		                    LEFT JOIN {$table_prefix}_account acc ON acc.accountid = tick.parent_id
		                    WHERE ent.deleted = 0 AND tick.status IN ('Open', 'In Progress', 'Wait For Response', 'Risposto Dal Cliente', 'In Approvazione', 'Da Eseguire') AND us.status = 'Active' AND us.kp_considera_carico = '1' AND us.kp_risorsa_pianific IN (".$lista_projectresourcesid.")";
		
		$res_venti_ticket = $adb->query($q_eventi_ticket);
		$num_venti_ticket = $adb->num_rows($res_venti_ticket);

		for($y=0; $y<$num_venti_ticket; $y++){
			
			$ticketid = $adb->query_result($res_venti_ticket, $y, 'ticketid');
			$ticketid = html_entity_decode(strip_tags($activityid), ENT_QUOTES,$default_charset);

			$title = $adb->query_result($res_venti_ticket, $y, 'title');
			$title = html_entity_decode(strip_tags($title), ENT_QUOTES,$default_charset);
			
			$date_start = $adb->query_result($res_venti_ticket, $y, 'kp_data_inizio_pian');
			$date_start = html_entity_decode(strip_tags($date_start), ENT_QUOTES,$default_charset);
			
			$time_start = $adb->query_result($res_venti_ticket, $y, 'kp_ora_inizio_tick');
			$time_start = html_entity_decode(strip_tags($time_start), ENT_QUOTES,$default_charset);
			if($time_start == null || $time_start == "" || $time_start == 0){
				$time_start = "08:00";
			}
			
			$time_end = $adb->query_result($res_venti_ticket, $y, 'kp_ora_fine_tick');
			$time_end = html_entity_decode(strip_tags($time_end), ENT_QUOTES,$default_charset);
			
			$date_end = $adb->query_result($res_venti_ticket, $y, 'kp_data_fine_pian');
			$date_end = html_entity_decode(strip_tags($date_end), ENT_QUOTES,$default_charset);
			
			$risorsa = $adb->query_result($res_venti_ticket, $y, 'risorsa');
			$risorsa = html_entity_decode(strip_tags($risorsa), ENT_QUOTES,$default_charset);
			
			$tempo_previsto = $adb->query_result($res_venti_ticket, $y, 'tempo_previsto');
			$tempo_previsto = html_entity_decode(strip_tags($tempo_previsto), ENT_QUOTES,$default_charset);
			if($tempo_previsto == null || $tempo_previsto == "" || $tempo_previsto == 0){
				$tempo_previsto = 0;
			}
			
			if($date_start != null && $date_start != ""){
			
				list($anno, $mese, $giorno) = explode("-", $date_start);
				$startdate_inv = date("d-m-Y", mktime(0, 0, 0, $mese, $giorno, $anno));

				$startdate = $date_start." ".$time_start;
			}
			else{

				$date_start = $data_corrente;

				list($anno,$mese,$giorno) = explode("-",$date_start);
				$startdate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));

				$startdate = $date_start." ".$time_start;
			}

			if($date_end != null && $date_end != "" && $date_end != "0000-00-00"){

				list($anno,$mese,$giorno) = explode("-",$date_end);
				$enddate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));

				$enddate = $date_end." ".$time_end;
			}
			else{
				
				//Se la data fine non e' stata popolata la calcolo sulla base della data di inizio e della durata

				if($tempo_previsto != "" && $tempo_previsto != 0){
					$dati_inizio_fine_tick = calcolaOraFineKpro($date_start, $time_start, $tempo_previsto);
				}
				else{
					$dati_inizio_fine_tick = calcolaOraFineKpro($date_start, $time_start, 2);
				}

				$enddate = $dati_inizio_fine_tick['data_fine'];
	            $time_end = $dati_inizio_fine_tick['tempo_fine'];

				$enddate = $enddate." ".$time_end;

				list($anno, $mese, $giorno) = explode("-", $enddate);
				$enddate_inv = date("d-m-Y", mktime(0, 0, 0, $mese, $giorno, $anno));
	
			}

			$colore = "white";
			
			$rows[] = array('id' => $ticketid."_".$risorsa,
							'taskid' => $ticketid,
							'start_date' => $startdate,
							'end_date' => $enddate,
							'text' => $title,
							'color' => $colore,
							'risorsa' => $risorsa,
							'startdate_inv' => $startdate_inv,
							'enddate_inv' => $enddate_inv,
							'ora_inizio' => $time_start,
							'ora_fine' => $time_end,
							'ore_previste' => $tempo_previsto,
							'projectname' => '',
							'projectid' => 0,
							'pianificazione_corrente' => 'ticket');


		}
		
	}
                                                             
}    
										
$json = json_encode($rows);
	
print $json;
	
?>
