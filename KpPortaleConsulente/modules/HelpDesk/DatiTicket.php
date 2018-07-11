<?php

/* kpro@tom07042017 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

require_once("../Utility/Utility.php");

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
	    header("Location: login.php");
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

	if(!verificaSeFornitoreAbilitatoPerTicket($record, $fornitore)){
		die;
	}

	$query = "SELECT 
				tick.ticketid ticketid,
				tick.ticket_no ticket_no,
				tick.title title,
				tick.status status,
				tick.kp_data_inizio_pian kp_data_inizio_pian,
				tick.kp_data_fine_pian kp_data_fine_pian,
				tick.kp_ora_inizio_tick kp_ora_inizio_tick,
				tick.kp_ora_fine_tick kp_ora_fine_tick,
				tick.kp_tempo_previsto kp_tempo_previsto,
				tick.servizio servizio,
				tick.kp_data_consegna kp_data_consegna,
				acc.accountid accountid,
				acc.accountname accountname,
				serv.servicename servicename,
				serv.kp_abb_chiusura_tick kp_abb_chiusura_tick,
				serv.kp_nome_doc_portale_forn kp_nome_doc_portale_forn,
				ent.createdtime createdtime,
				ent.modifiedtime modifiedtime,
				tick.description description,
				stab.nome_stabilimento nome_stabilimento
				FROM {$table_prefix}_troubletickets tick
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
				INNER JOIN {$table_prefix}_account acc ON acc.accountid = tick.parent_id
				LEFT JOIN {$table_prefix}_service serv ON serv.serviceid = tick.servizio
				LEFT JOIN {$table_prefix}_stabilimenti stab ON stab.stabilimentiid = tick.kp_stabilimento
				WHERE ent.deleted = 0 AND tick.kp_fornitore = ".$fornitore." AND tick.ticketid = ".$record;

	$result_query = $adb->query($query);
	$num_result = $adb->num_rows($result_query);

	if($num_result > 0){

		$ticketid = $adb->query_result($result_query, 0, 'ticketid');
		$ticketid = html_entity_decode(strip_tags($ticketid), ENT_QUOTES, $default_charset);
		
		$ticket_no = $adb->query_result($result_query, 0, 'ticket_no');
		$ticket_no = html_entity_decode(strip_tags($ticket_no), ENT_QUOTES, $default_charset);

		$title = $adb->query_result($result_query, 0, 'title');
		$title = html_entity_decode(strip_tags($title), ENT_QUOTES, $default_charset);

		$status = $adb->query_result($result_query, 0, 'status');
		$status = html_entity_decode(strip_tags($status), ENT_QUOTES, $default_charset);

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
		
		$data_inizio_pian = $adb->query_result($result_query, 0, 'kp_data_inizio_pian');
		$data_inizio_pian = html_entity_decode(strip_tags($data_inizio_pian), ENT_QUOTES, $default_charset);
		if($data_inizio_pian != null && $data_inizio_pian != "" && $data_inizio_pian != "0000-00-00"){

			$data_inizio_pian_inv = new DateTime($data_inizio_pian);
			$data_inizio_pian_inv = $data_inizio_pian_inv->format('d/m/Y');

		}
		else{

			$data_inizio_pian = "";
			$data_inizio_pian_inv = "";

		}

		$data_fine_pian = $adb->query_result($result_query, 0, 'kp_data_fine_pian');
		$data_fine_pian = html_entity_decode(strip_tags($data_fine_pian), ENT_QUOTES, $default_charset);
		if($data_fine_pian != null && $data_fine_pian != "" && $data_fine_pian != "0000-00-00"){

			$data_fine_pian_inv = new DateTime($data_fine_pian);
			$data_fine_pian_inv = $data_fine_pian_inv->format('d/m/Y');

		}
		else{

			$data_fine_pian = "";
			$data_fine_pian_inv = "";

		}

		$tempo_previsto = $adb->query_result($result_query, 0, 'kp_tempo_previsto');
		$tempo_previsto = html_entity_decode(strip_tags($tempo_previsto), ENT_QUOTES, $default_charset);
		if($tempo_previsto == null || $tempo_previsto == ""){
			$tempo_previsto = 0;
		}

		$accountname = $adb->query_result($result_query, 0, 'accountname');
		$accountname = html_entity_decode(strip_tags($accountname), ENT_QUOTES, $default_charset);
		if($accountname == null || $accountname == ""){
			$accountname = "";
		}

		$servicename = $adb->query_result($result_query, 0, 'servicename');
		$servicename = html_entity_decode(strip_tags($servicename), ENT_QUOTES, $default_charset);
		if($servicename == null || $servicename == ""){
			$servicename = "";
		}

		$description = $adb->query_result($result_query, 0, 'description');
		$description = html_entity_decode(strip_tags($description), ENT_QUOTES, $default_charset);

		$nome_stabilimento = $adb->query_result($result_query, 0, 'nome_stabilimento');
		$nome_stabilimento = html_entity_decode(strip_tags($nome_stabilimento), ENT_QUOTES, $default_charset);
		if($nome_stabilimento == null){
			$nome_stabilimento = "";
		}

		$data_consegna = $adb->query_result($result_query, 0, 'kp_data_consegna');
		$data_consegna = html_entity_decode(strip_tags($data_consegna), ENT_QUOTES, $default_charset);
		if($data_consegna == null || $data_consegna == "0000-00-00"){
			$data_consegna = "";
			$data_consegna_inv = "";
		}
		else{
			$data_consegna_inv = $data_consegna;

			$data_consegna = new DateTime($data_consegna);
			$data_consegna = $data_consegna->format("d/m/Y");
		}

		$tempo_schedulato = getOrePianificateTicket($ticketid, $fornitore);

		$abb_chiusura_tick = $adb->query_result($result_query, 0, 'kp_abb_chiusura_tick');
		$abb_chiusura_tick = html_entity_decode(strip_tags($abb_chiusura_tick), ENT_QUOTES, $default_charset);
		if($abb_chiusura_tick == null || $abb_chiusura_tick == ""){
			$abb_chiusura_tick = "0";
		}

		$nome_doc_portale_forn = $adb->query_result($result_query, 0, 'kp_nome_doc_portale_forn');
		$nome_doc_portale_forn = html_entity_decode(strip_tags($nome_doc_portale_forn), ENT_QUOTES, $default_charset);
		if($nome_doc_portale_forn == null || $nome_doc_portale_forn == ""){
			$nome_doc_portale_forn = "";
		}
		
		$rows[] = array('ticketid' => $ticketid,
						'title' => $title,
						'stato' => $status,
						'tempo_previsto' => $tempo_previsto,
						'tempo_schedulato' => $tempo_schedulato,
						'accountname' => $accountname,
						'stabilimento_name' => $nome_stabilimento,
						'servicename' => $servicename,
						'abb_chiusura_tick' => $abb_chiusura_tick,
						'nome_doc_portale_forn' => $nome_doc_portale_forn,
						'data_consegna' => $data_consegna,
						'data_consegna_inv' => $data_consegna_inv,
						'description' => $description);
			
	}

}
	
$json = json_encode($rows);
print $json;
	
?>