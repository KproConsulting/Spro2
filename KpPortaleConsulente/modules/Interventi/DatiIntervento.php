<?php

/* kpro@tom07042017 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

require_once("../../PortalConfig.php");
include_once('../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_language, $default_charset;
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

	$query = "SELECT 
				act.activityid activityid,
				act.subject SUBJECT,
				act.date_start date_start,
				act.time_start time_start,
				act.due_date due_date,
				act.time_end time_end,
				act.eventstatus eventstatus,
				act.kp_durata_prevista duration_hours,
				timec.ticket_id ticket,
				tick.title ticket_name,
				tick.status status,
				acc.accountname accountname,
				stab.nome_stabilimento nome_stabilimento,
				timec.timecardsid timecardsid,
				timec.workdate workdate,
				timec.worktime worktime,
				timec.kp_ore_effettive ore_effettive,
				timec.kp_ora_inizio ora_inizio,
				timec.kp_ora_fine ora_fine,
				timec.kp_titolo titolo,
				timec.description description
				FROM {$table_prefix}_timecards timec
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = timec.timecardsid 
				INNER JOIN {$table_prefix}_activity act ON act.activityid = timec.kp_evento_id
				INNER JOIN {$table_prefix}_troubletickets tick ON tick.ticketid = timec.ticket_id
				INNER JOIN {$table_prefix}_account acc ON acc.accountid = tick.parent_id
				LEFT JOIN {$table_prefix}_stabilimenti stab ON stab.stabilimentiid = tick.kp_stabilimento
				WHERE ent.deleted = 0  AND tick.kp_fornitore = ".$fornitore." AND timec.timecardsid = ".$record;

	$result_query = $adb->query($query);
	$num_result = $adb->num_rows($result_query);

	if($num_result > 0){

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

		$data_evento = $adb->query_result($result_query, 0, 'date_start');
		$data_evento = html_entity_decode(strip_tags($data_evento), ENT_QUOTES, $default_charset);

		$data_evento_inv = new DateTime($data_evento);
		$data_evento_inv = $data_evento_inv->format('d/m/Y');

		$descrizione = $adb->query_result($result_query, 0, 'description');
		$descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES, $default_charset);

		$workdate = $adb->query_result($result_query, 0, 'workdate');
		$workdate = html_entity_decode(strip_tags($workdate), ENT_QUOTES, $default_charset);

		$workdate_inv = new DateTime($workdate);
		$workdate_inv = $workdate_inv->format('d/m/Y');

		$worktime = $adb->query_result($result_query, 0, 'worktime');
		$worktime = html_entity_decode(strip_tags($worktime), ENT_QUOTES, $default_charset);
		list($ora, $minuti) = explode(":", $worktime); 

		$ore_effettive = $adb->query_result($result_query, 0, 'ore_effettive');
		$ore_effettive = html_entity_decode(strip_tags($ore_effettive), ENT_QUOTES, $default_charset);
		if($ore_effettive != "" && $ore_effettive != null){
			list($ora_eff, $minuti_eff) = explode(":", $ore_effettive); 
		}
		else{
			$ora_eff = 0;
			$minuti_eff = 0;
		}

		$status = $adb->query_result($result_query, 0, 'status');
		$status = html_entity_decode(strip_tags($status), ENT_QUOTES, $default_charset);
		
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

		$titolo = $adb->query_result($result_query, 0, 'titolo');
		$titolo = html_entity_decode(strip_tags($titolo), ENT_QUOTES, $default_charset);
		if($titolo == null){
			$titolo = "";
		}

		switch ($status) {
			case "Open":
				$status = "Aperto";
				break;
			case "In Progress":
				$status = "In Corso";
				break;
			case "Closed":
				$status = "Chiuso";
				break;
			case "Wait For Response":
				$status = "";
				break;
			case "Maintain":
				$status = "";
				break;
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

		$rows[] = array("activityid" => $activityid,
						"activity_name" => $activity_name,
						"ticketid" => $ticketid,
						"ticket_name" => $ticket_name,
						"ticket_stato" => $status,
						"accountname" => $accountname,
						"nome_stabilimento" => $nome_stabilimento,
						"duration_hours" => $duration_hours,
						"descrizione" => $descrizione,
						"data_evento" => $data_evento,
						"data_evento_inv" => $data_evento_inv,
						"workdate" => $workdate,
						"workdate_inv" => $workdate_inv,
						"ora" => $ora,
						"minuti" => $minuti,
						"ora_eff" => $ora_eff,
						"minuti_eff" => $minuti_eff,
						"ora_inizio" => $ora_inizio,
						"ora_fine" => $ora_fine,
						"numero_documenti_tick" => $numero_documenti,
						"titolo" => $titolo);

	}
			
}
	
$json = json_encode($rows);
print $json;
	
?>