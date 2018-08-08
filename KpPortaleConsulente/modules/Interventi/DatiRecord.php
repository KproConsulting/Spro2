<?php

/* kpro@tom07042017 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

//ini_set("display_errors", '1');

require_once("../../PortalConfig.php");
include_once('../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_language;
session_start();

//print_r($_SESSION);die;

$contact_id = 0;
$default_language = "it_it";
if(isset($_SESSION['customer_sessionid'])){
	
    $contact_id = $_SESSION['customer_id'];
    $customer_account_id = $_SESSION['customer_account_id'];
    $sessionid = $_SESSION['customer_sessionid'];
    $default_language = $_SESSION['portal_login_language'];
  
}
else{
	$contact_id = 0;
}

require_once($portal_name.'/string/'.$default_language.'.php');

if($contact_id != 0){
    
    $query= "SELECT 
				vendor_id
				FROM {$table_prefix}_contactdetails 
				WHERE contactid = ".$contact_id;
	$result_query = $adb->query($query);
			
	if($adb->num_rows($result_query) > 0){
		
		$fornitore = $adb->query_result($result_query, 0, 'vendor_id');
		$fornitore = html_entity_decode(strip_tags($fornitore), ENT_QUOTES, $default_charset);
		
	}
	else{
	    header("Location: ../../login.php");
		die;
	}
	
}
else{
    header("Location: ../../login.php"); 
	die;
}

$rows = array();

if(isset($_GET['record'])){
	$record = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['record']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
	$record = substr($record, 0, 100);

	$tipo_record = getTipoRecord($record);

	if($tipo_record == "Calendar"){

		$dati_record = getDatiEvento($record, $fornitore);

	}
	elseif($tipo_record == "Timecards"){

		$dati_record = getDatiIntervento($record, $fornitore);

	}
	
	$rows[] = $dati_record;
			
}

$json = json_encode($rows);

print $json;

function getTipoRecord($record){
	global $adb, $table_prefix, $current_user, $site_URL, $default_language;

	$result = "";

	$query = "SELECT 
				setype 
				FROM {$table_prefix}_crmentity 
				WHERE crmid = ".$record;

	$result_query = $adb->query($query);
	$num_result = $adb->num_rows($result_query);

	if($num_result > 0){

		$setype = $adb->query_result($result_query, 0, 'setype');
		$setype = html_entity_decode(strip_tags($setype), ENT_QUOTES, $default_charset);

		$result = $setype;

	}

	return $result;

}

function getDatiEvento($record, $fornitore){
	global $adb, $table_prefix, $current_user, $site_URL, $default_language, $default_charset;

	$result = "";

	$query = "SELECT 
				act.activityid activityid,
				act.subject subject,
				act.date_start date_start,
				act.time_start time_start,
				act.due_date due_date,
				act.time_end time_end,
				act.eventstatus eventstatus,
				act.kp_durata_prevista duration_hours,
				actrel.crmid ticket,
				tick.title ticket_name,
				acc.accountname accountname,
				stab.nome_stabilimento nome_stabilimento,
				serv.kp_abb_chiusura_tick kp_abb_chiusura_tick
				FROM {$table_prefix}_activity act
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = act.activityid
				INNER JOIN {$table_prefix}_seactivityrel actrel ON actrel.activityid = act.activityid
				INNER JOIN {$table_prefix}_troubletickets tick ON tick.ticketid = actrel.crmid
				INNER JOIN {$table_prefix}_account acc ON acc.accountid = tick.parent_id
				LEFT JOIN {$table_prefix}_stabilimenti stab ON stab.stabilimentiid = tick.kp_stabilimento
				LEFT JOIN {$table_prefix}_service serv ON serv.serviceid = tick.servizio
				WHERE ent.deleted = 0  AND tick.kp_fornitore = ".$fornitore." AND act.activityid = ".$record;
	
	$result_query = $adb->query($query);
	$num_result = $adb->num_rows($result_query);

	if($num_result > 0){

		$timecardsid = 0;

		$activityid = $adb->query_result($result_query, 0, 'activityid');
		$activityid = html_entity_decode(strip_tags($activityid), ENT_QUOTES, $default_charset);

		$activity_name = $adb->query_result($result_query, 0, 'subject');
		$activity_name = html_entity_decode(strip_tags($activity_name), ENT_QUOTES, $default_charset);

		$ticketid = $adb->query_result($result_query, 0, 'ticket');
		$ticketid = html_entity_decode(strip_tags($ticketid), ENT_QUOTES, $default_charset);

		$ticket_name = $adb->query_result($result_query, 0, 'ticket_name');
		$ticket_name = html_entity_decode(strip_tags($ticket_name), ENT_QUOTES, $default_charset);

		$accountname = $adb->query_result($result_query, 0, 'accountname');
		$accountname = html_entity_decode(strip_tags($accountname), ENT_QUOTES, $default_charset);

		$nome_stabilimento = $adb->query_result($result_query, 0, 'nome_stabilimento');
		$nome_stabilimento = html_entity_decode(strip_tags($nome_stabilimento), ENT_QUOTES, $default_charset);
		if($nome_stabilimento == null){
			$nome_stabilimento = "";
		}

		$duration_hours = $adb->query_result($result_query, 0, 'duration_hours');
		$duration_hours = html_entity_decode(strip_tags($duration_hours), ENT_QUOTES, $default_charset);
		if($duration_hours == null || $duration_hours == ""){
			$duration_hours = 0;	
		}
		$duration_hours = number_format($duration_hours, 0, '.', ',');

		$duration_minutes = 0;
		$duration_minutes = number_format($duration_minutes, 0, '.', ',');

		$data_evento = $adb->query_result($result_query, 0, 'date_start');
		$data_evento = html_entity_decode(strip_tags($data_evento), ENT_QUOTES, $default_charset);

		$data_evento_inv = new DateTime($data_evento);
		$data_evento_inv = $data_evento_inv->format('d/m/Y');

		$descrizione = "";

		$abb_chiusura_tick = $adb->query_result($result_query, 0, 'kp_abb_chiusura_tick');
		$abb_chiusura_tick = html_entity_decode(strip_tags($abb_chiusura_tick), ENT_QUOTES, $default_charset);
		if($abb_chiusura_tick == null || $abb_chiusura_tick == ""){
			$abb_chiusura_tick = "0";
		}
		
		$ora_inizio = $adb->query_result($result_query, 0, 'time_start');
		$ora_inizio = html_entity_decode(strip_tags($ora_inizio), ENT_QUOTES, $default_charset);
		if($ora_inizio == null || $ora_inizio == ""){
			$ora_inizio = "00:00";
		}

		$ora_fine = $adb->query_result($result_query, 0, 'time_end');
		$ora_fine = html_entity_decode(strip_tags($ora_fine), ENT_QUOTES, $default_charset);
		if($ora_fine == null || $ora_fine == ""){
			$ora_fine = "00:00";
		}

		$q_documenti = "SELECT 
						coalesce(count(*), 0) numero_documenti
						FROM {$table_prefix}_notes notes 
						INNER JOIN {$table_prefix}_notescf notescf ON notescf.notesid = notes.notesid 
						INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = notes.notesid 
						LEFT JOIN {$table_prefix}_senotesrel senote ON senote.notesid = notes.notesid
						LEFT JOIN {$table_prefix}_seattachmentsrel seattac ON seattac.crmid = notes.notesid 
						LEFT JOIN {$table_prefix}_attachments attac ON attac.attachmentsid = seattac.attachmentsid 
						WHERE ent.deleted = 0 AND notes.active_portal = 1 AND notes.folderid = 48 AND senote.crmid = ".$ticketid;

		$res_documenti = $adb->query($q_documenti);
		$num_documenti = $adb->num_rows($res_documenti);
		
		if($num_documenti > 0){
			
			$numero_documenti = $adb->query_result($res_documenti, 0, 'numero_documenti');
			$numero_documenti = html_entity_decode(strip_tags($numero_documenti), ENT_QUOTES,$default_charset);
		
		}
		else{
		
			$numero_documenti = 0;
		
		}

	}
	else{

		$timecardsid = 0;
		$duration_minutes = 0;
		$activityid = 0;
		$activity_name = "";
		$ticketid = 0;
		$ticket_name = "";
		$accountname = "";
		$nome_stabilimento = "";
		$duration_hours = 0;
		$descrizione = "";
		$data_evento = date("Y-m-d");
		$data_evento_inv = date("d/m/Y");
		$abb_chiusura_tick = "0";
		$numero_documenti = 0;
		$ora_inizio = "00:00";
		$ora_fine = "00:00";

	}

	$result = array("timecardsid" => $timecardsid,
					"activityid" => $activityid,
					"activity_name" => $activity_name,
					"ticketid" => $ticketid,
					"ticket_name" => $ticket_name,
					"accountname" => $accountname,
					"nome_stabilimento" => $nome_stabilimento,
					"duration_hours" => $duration_hours,
					"duration_minutes" => $duration_minutes,
					"descrizione" => $descrizione,
					"data_evento" => $data_evento,
					"data_evento_inv" => $data_evento_inv,
					"abb_chiusura_tick" => $abb_chiusura_tick,
					"numero_documenti_tick" => $numero_documenti,
					"ora_inizio" => $ora_inizio,
					"ora_fine" => $ora_fine);
	
	return $result;

}

function getDatiIntervento($record, $fornitore){
	global $adb, $table_prefix, $current_user, $site_URL, $default_language, $default_charset;

	$result = "";

	$query = "SELECT 
				tc.timecardsid timecardsid,
				tc.worktime worktime,
				tc.workdate workdate,
				tc.kp_ora_inizio ora_inizio,
				tc.kp_ora_fine ora_fine,
				act.activityid activityid,
				act.subject SUBJECT,
				act.date_start date_start,
				act.time_start time_start,
				act.due_date due_date,
				act.time_end time_end,
				act.eventstatus eventstatus,
				act.kp_durata_prevista duration_hours,
				tc.ticket_id ticket,
				tick.title ticket_name,
				acc.accountname accountname,
				stab.nome_stabilimento nome_stabilimento,
				serv.kp_abb_chiusura_tick kp_abb_chiusura_tick,
				tc.description description
				FROM {$table_prefix}_timecards tc
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tc.timecardsid
				INNER JOIN {$table_prefix}_activity act ON act.activityid = tc.kp_evento_id
				INNER JOIN {$table_prefix}_troubletickets tick ON tick.ticketid = tc.ticket_id
				INNER JOIN {$table_prefix}_account acc ON acc.accountid = tick.parent_id
				LEFT JOIN {$table_prefix}_stabilimenti stab ON stab.stabilimentiid = tick.kp_stabilimento
				LEFT JOIN {$table_prefix}_service serv ON serv.serviceid = tick.servizio
				WHERE ent.deleted = 0  AND tick.kp_fornitore = ".$fornitore." AND tc.timecardsid = ".$record;

	$result_query = $adb->query($query);
	$num_result = $adb->num_rows($result_query);

	if($num_result > 0){

		$timecardsid = $adb->query_result($result_query, 0, 'timecardsid');
		$timecardsid = html_entity_decode(strip_tags($timecardsid), ENT_QUOTES, $default_charset);

		$worktime = $adb->query_result($result_query, 0, 'worktime');
		$worktime = html_entity_decode(strip_tags($worktime), ENT_QUOTES, $default_charset);
		list($duration_hours, $duration_minutes) = explode(":", $worktime);

		$duration_hours = (int)$duration_hours; 
		$duration_hours = number_format($duration_hours, 0, '.', ',');

		$duration_minutes = (int)$duration_minutes; 
		$duration_minutes = number_format($duration_minutes, 0, '.', ',');

		$activityid = $adb->query_result($result_query, 0, 'activityid');
		$activityid = html_entity_decode(strip_tags($activityid), ENT_QUOTES, $default_charset);

		$activity_name = $adb->query_result($result_query, 0, 'subject');
		$activity_name = html_entity_decode(strip_tags($activity_name), ENT_QUOTES, $default_charset);

		$ticketid = $adb->query_result($result_query, 0, 'ticket');
		$ticketid = html_entity_decode(strip_tags($ticketid), ENT_QUOTES, $default_charset);

		$ticket_name = $adb->query_result($result_query, 0, 'ticket_name');
		$ticket_name = html_entity_decode(strip_tags($ticket_name), ENT_QUOTES, $default_charset);

		$accountname = $adb->query_result($result_query, 0, 'accountname');
		$accountname = html_entity_decode(strip_tags($accountname), ENT_QUOTES, $default_charset);

		$nome_stabilimento = $adb->query_result($result_query, 0, 'nome_stabilimento');
		$nome_stabilimento = html_entity_decode(strip_tags($nome_stabilimento), ENT_QUOTES, $default_charset);
		if($nome_stabilimento == null){
			$nome_stabilimento = "";
		}

		$data_evento = $adb->query_result($result_query, 0, 'workdate');
		$data_evento = html_entity_decode(strip_tags($data_evento), ENT_QUOTES, $default_charset);

		$data_evento_inv = new DateTime($data_evento);
		$data_evento_inv = $data_evento_inv->format('d/m/Y');

		$descrizione = $adb->query_result($result_query, 0, 'description');
		$descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES, $default_charset);

		$abb_chiusura_tick = $adb->query_result($result_query, 0, 'kp_abb_chiusura_tick');
		$abb_chiusura_tick = html_entity_decode(strip_tags($abb_chiusura_tick), ENT_QUOTES, $default_charset);
		if($abb_chiusura_tick == null || $abb_chiusura_tick == ""){
			$abb_chiusura_tick = "0";
		}
		
		$ora_inizio = $adb->query_result($result_query, 0, 'ora_inizio');
		$ora_inizio = html_entity_decode(strip_tags($ora_inizio), ENT_QUOTES, $default_charset);
		if($ora_inizio == null || $ora_inizio == ""){
			$ora_inizio = "00:00";
		}

		$ora_fine = $adb->query_result($result_query, 0, 'ora_fine');
		$ora_fine = html_entity_decode(strip_tags($ora_fine), ENT_QUOTES, $default_charset);
		if($ora_fine == null || $ora_fine == ""){
			$ora_fine = "00:00";
		}

		$q_documenti = "SELECT 
						coalesce(count(*), 0) numero_documenti
						FROM {$table_prefix}_notes notes 
						INNER JOIN {$table_prefix}_notescf notescf ON notescf.notesid = notes.notesid 
						INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = notes.notesid 
						LEFT JOIN {$table_prefix}_senotesrel senote ON senote.notesid = notes.notesid
						LEFT JOIN {$table_prefix}_seattachmentsrel seattac ON seattac.crmid = notes.notesid 
						LEFT JOIN {$table_prefix}_attachments attac ON attac.attachmentsid = seattac.attachmentsid 
						WHERE ent.deleted = 0 AND notes.active_portal = 1 AND notes.folderid = 48 AND senote.crmid = ".$ticketid;

		$res_documenti = $adb->query($q_documenti);
		$num_documenti = $adb->num_rows($res_documenti);
		
		if($num_documenti > 0){
			
			$numero_documenti = $adb->query_result($res_documenti, 0, 'numero_documenti');
			$numero_documenti = html_entity_decode(strip_tags($numero_documenti), ENT_QUOTES,$default_charset);
		
		}
		else{
		
			$numero_documenti = 0;
		
		}

	}
	else{

		$timecardsid = 0;
		$duration_minutes = 0;
		$activityid = 0;
		$activity_name = "";
		$ticketid = 0;
		$ticket_name = "";
		$accountname = "";
		$nome_stabilimento = "";
		$duration_hours = 0;
		$descrizione = "";
		$data_evento = date("Y-m-d");
		$data_evento_inv = date("d/m/Y");
		$abb_chiusura_tick = "0";
		$numero_documenti = 0;
		$ora_inizio = "00:00";
		$ora_fine = "00:00";

	}

	$result = array("timecardsid" => $timecardsid,
					"activityid" => $activityid,
					"activity_name" => $activity_name,
					"ticketid" => $ticketid,
					"ticket_name" => $ticket_name,
					"accountname" => $accountname,
					"nome_stabilimento" => $nome_stabilimento,
					"duration_hours" => $duration_hours,
					"duration_minutes" => $duration_minutes,
					"descrizione" => $descrizione,
					"data_evento" => $data_evento,
					"data_evento_inv" => $data_evento_inv,
					"abb_chiusura_tick" => $abb_chiusura_tick,
					"numero_documenti_tick" => $numero_documenti,
					"ora_inizio" => $ora_inizio,
					"ora_fine" => $ora_fine);

	return $result;

}
	
?>