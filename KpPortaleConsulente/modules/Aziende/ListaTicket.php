<?php

/* kpro@tom13042017 */
		
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

if(isset($_GET['titolo'])){
	$search_titolo = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['titolo']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
	$search_titolo = substr($search_titolo, 0, 255);
}
else{
	$search_titolo = '';
}

if(isset($_GET['stabilimento'])){
	$search_stabilimento = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['stabilimento']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
	$search_stabilimento = substr($search_stabilimento, 0, 255);
}
else{
	$search_stabilimento = '';
}

if(isset($_GET['data_esecuzione'])){
	$search_data_esecuzione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data_esecuzione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$search_data_esecuzione = substr($search_data_esecuzione,0,255);
}
else{
	$search_data_esecuzione = '';
}

if(isset($_GET['stato'])){
	$search_stato = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['stato']), ENT_QUOTES,$default_charset)), ENT_QUOTES, $default_charset);
	
	if($search_stato != ""){
		$search_stato = explode(',', $search_stato);
		
		$search_lista_stati = "";
			
		foreach($search_stato as $stato){

			switch ($stato) {
				case "Aperto":
					$stato = "Open";
					break;
				case "In Corso":
					$stato = "In Progress";
					break;
				case "Chiuso":
					$stato = "Closed";
					break;
				case "In approvazione":
					$stato = "In approvazione";
					break;
				default:
					$stato = "";
			}

			if($search_lista_stati == ""){
				$search_lista_stati = "'".$stato."'";
			}
			else{
				$search_lista_stati .= ",'".$stato."'";
			}
		}
	}
	else{
		$search_lista_stati = "";
	}
	
}
else{
	$search_lista_stati = "";
}

if(isset($_GET['azienda'])){
	$azienda = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['azienda']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
	$azienda = substr($azienda, 0, 100);

	if(!verificaSeFornitoreAbilitatoPerAzienda($azienda, $fornitore)){

		header("Location: index.php?default_language=".$default_language); 
		die;
		
	}

	$dati_cliente_precedente = verificaSePresenteClientePrecedente($azienda);

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
				tick.data_esecuzione data_esecuzione,
				acc.accountid accountid,
				acc.accountname accountname,
				serv.servicename servicename,
				ent.createdtime createdtime,
				ent.modifiedtime modifiedtime,
				stab.nome_stabilimento nome_stabilimento
				FROM {$table_prefix}_troubletickets tick
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
				INNER JOIN {$table_prefix}_account acc ON acc.accountid = tick.parent_id
				LEFT JOIN {$table_prefix}_service serv ON serv.serviceid = tick.servizio
				LEFT JOIN {$table_prefix}_stabilimenti stab ON stab.stabilimentiid = tick.kp_stabilimento
				WHERE ent.deleted = 0 AND tick.status != 'Annullato' AND tick.kp_fornitore = ".$fornitore;

	if($dati_cliente_precedente["esiste"]){
		$query .= " AND (tick.parent_id = ".$azienda." OR tick.parent_id = ".$dati_cliente_precedente["id"].")";
	}
	else{
		$query .= " AND tick.parent_id = ".$azienda;
	}
	
	if($search_titolo != ""){
		$query .= " AND tick.title LIKE '%".$search_titolo."%'";
	}

	if($search_lista_stati != ""){
		$query .= " AND tick.status IN (".$search_lista_stati.")";
	}

	if($search_stabilimento != ""){
		$query .= " AND stab.nome_stabilimento LIKE '%".$search_stabilimento."%'";
	}

	if($search_data_esecuzione != ""){
		$query .= " AND tick.data_esecuzione = '".$search_data_esecuzione."'";
	}

	$query .= " ORDER BY ent.createdtime ASC";

	//print($query); die;

	$result_query = $adb->query($query);
	$num_result = $adb->num_rows($result_query);

	for($i=0; $i < $num_result; $i++){

		$ticketid = $adb->query_result($result_query, $i, 'ticketid');
		$ticketid = html_entity_decode(strip_tags($ticketid), ENT_QUOTES, $default_charset);
		
		$ticket_no = $adb->query_result($result_query, $i, 'ticket_no');
		$ticket_no = html_entity_decode(strip_tags($ticket_no), ENT_QUOTES, $default_charset);

		$title = $adb->query_result($result_query, $i, 'title');
		$title = html_entity_decode(strip_tags($title), ENT_QUOTES, $default_charset);

		$status = $adb->query_result($result_query, $i, 'status');
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
			case "In approvazione":
				$status = "In approvazione";
				break;
			default:
				$status = "";
		}

		if($status != "Chiuso" && $status != ""){

			$data_esecuzione = $adb->query_result($result_query, $i, 'data_esecuzione');
			$data_esecuzione = html_entity_decode(strip_tags($data_esecuzione), ENT_QUOTES, $default_charset);
			if($data_esecuzione != null && $data_esecuzione != "" && $data_esecuzione != "0000-00-00"){

				$data_esecuzione_inv = new DateTime($data_esecuzione);
				$data_esecuzione_inv = $data_esecuzione_inv->format('d/m/Y');

			}
			else{

				$data_esecuzione = "";
				$data_esecuzione_inv = "";

			}
			
			$data_inizio_pian = $adb->query_result($result_query, $i, 'kp_data_inizio_pian');
			$data_inizio_pian = html_entity_decode(strip_tags($data_inizio_pian), ENT_QUOTES, $default_charset);
			if($data_inizio_pian != null && $data_inizio_pian != "" && $data_inizio_pian != "0000-00-00"){

				$data_inizio_pian_inv = new DateTime($data_inizio_pian);
				$data_inizio_pian_inv = $data_inizio_pian_inv->format('d/m/Y');

			}
			else{

				$data_inizio_pian = "";
				$data_inizio_pian_inv = "";

			}

			$data_fine_pian = $adb->query_result($result_query, $i, 'kp_data_fine_pian');
			$data_fine_pian = html_entity_decode(strip_tags($data_fine_pian), ENT_QUOTES, $default_charset);
			if($data_fine_pian != null && $data_fine_pian != "" && $data_fine_pian != "0000-00-00"){

				$data_fine_pian_inv = new DateTime($data_fine_pian);
				$data_fine_pian_inv = $data_fine_pian_inv->format('d/m/Y');

			}
			else{

				$data_fine_pian = "";
				$data_fine_pian_inv = "";

			}

			$tempo_previsto = $adb->query_result($result_query, $i, 'kp_tempo_previsto');
			$tempo_previsto = html_entity_decode(strip_tags($tempo_previsto), ENT_QUOTES, $default_charset);
			if($tempo_previsto == null || $tempo_previsto == ""){
				$tempo_previsto = 0;
			}

			$accountname = $adb->query_result($result_query, $i, 'accountname');
			$accountname = html_entity_decode(strip_tags($accountname), ENT_QUOTES, $default_charset);
			if($accountname == null || $accountname == ""){
				$accountname = "";
			}

			$servicename = $adb->query_result($result_query, $i, 'servicename');
			$servicename = html_entity_decode(strip_tags($servicename), ENT_QUOTES, $default_charset);
			if($servicename == null || $servicename == ""){
				$servicename = "";
			}

			$nome_stabilimento = $adb->query_result($result_query, $i, 'nome_stabilimento');
			$nome_stabilimento = html_entity_decode(strip_tags($nome_stabilimento), ENT_QUOTES, $default_charset);
			if($nome_stabilimento == null){
				$nome_stabilimento = "";
			}
			
			$rows[] = array('ticketid' => $ticketid,
							'title' => $title,
							'stato' => $status,
							'tempo_previsto' => $tempo_previsto,
							'tempo_schedulato' => 0,
							'accountname' => $accountname,
							'stabilimento_name' => $nome_stabilimento,
							'data_esecuzione_inv' => $data_esecuzione_inv,
							'servicename' => $servicename);
		}
			
	}

}
	
$json = json_encode($rows);
print $json;
	
?>