<?php

/* kpro@tom05062017 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 * @package portaleVteSicurezza
 * @version 1.0
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

$rows = array();

$contact_id = 0;
$azienda = 0;
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
    
    $q_account = "SELECT accountid
					FROM {$table_prefix}_contactdetails 
					WHERE contactid = ".$contact_id;
	$res_account = $adb->query($q_account);
			
	if($adb->num_rows($res_account) > 0){
		
		$azienda = $adb->query_result($res_account, 0, 'accountid');
		$azienda = html_entity_decode(strip_tags($azienda), ENT_QUOTES,$default_charset);
		
	}
	else{

		$json = json_encode($rows);
		print $json;
		die;

	    header("Location: ../../login.php"); 
	}
	
}
else{

	$json = json_encode($rows);
	print $json;
	die;

    header("Location: ../../login.php"); 
}

if(isset($_GET['record']) && $azienda != 0){
	$record = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['record']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
	$record = substr($record, 0, 100);

	$query = "SELECT
				rigman.kprighemanutenzioniid kprighemanutenzioniid,
				rigman.kp_data_scadenza kp_data_scadenza,
				rigman.kp_componente kp_componente,
				rigman.kp_check_list kp_check_list,
				comp.impianto impianto,
				man.data_manutenzione data_manutenzione,
				imp.impianto_name impianto_name,
				comp.nome_componente nome_componente,
				checkl.nome_check_list nome_check_list,
				man.description description
				FROM {$table_prefix}_kprighemanutenzioni rigman
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rigman.kprighemanutenzioniid
				INNER JOIN {$table_prefix}_manutenzioni man ON man.manutenzioniid = rigman.kp_manutenzione
				INNER JOIN {$table_prefix}_crmentity entman ON entman.crmid = man.manutenzioniid
				INNER JOIN {$table_prefix}_compimpianto comp ON comp.compimpiantoid = rigman.kp_componente
				INNER JOIN {$table_prefix}_checklists checkl ON checkl.checklistsid = rigman.kp_check_list
				INNER JOIN {$table_prefix}_impianti imp ON imp.impiantiid = comp.impianto
				WHERE ent.deleted = 0 AND entman.deleted = 0 AND imp.azienda = ".$azienda." AND rigman.kprighemanutenzioniid = ".$record;

	$query .= " ORDER BY man.data_manutenzione DESC";
	
	$result_query = $adb->query($query);
	$num_result = $adb->num_rows($result_query);

	if( $num_result > 0 ){

		$righemanutenzioniid = $adb->query_result($result_query, 0, 'kprighemanutenzioniid');
		$righemanutenzioniid = html_entity_decode(strip_tags($righemanutenzioniid), ENT_QUOTES, $default_charset);

		$data_scadenza = $adb->query_result($result_query, 0, 'kp_data_scadenza');
		$data_scadenza = html_entity_decode(strip_tags($data_scadenza), ENT_QUOTES, $default_charset);
		if($data_scadenza == null || $data_scadenza == "" || $data_scadenza == "0000-00-00"){

			$data_scadenza = "";

		}
		else{

			$data_scadenza = new DateTime($data_scadenza);
			$data_scadenza = $data_scadenza->format('d/m/Y');


		}

		$data_manutenzione = $adb->query_result($result_query, 0, 'data_manutenzione');
		$data_manutenzione = html_entity_decode(strip_tags($data_manutenzione), ENT_QUOTES, $default_charset);
		if($data_manutenzione == null || $data_manutenzione == "" || $data_manutenzione == "0000-00-00"){

			$data_manutenzione = "";

		}
		else{

			$data_manutenzione = new DateTime($data_manutenzione);
			$data_manutenzione = $data_manutenzione->format('d/m/Y');


		}

		$impianto_name = $adb->query_result($result_query, 0, 'impianto_name');
		$impianto_name = html_entity_decode(strip_tags($impianto_name), ENT_QUOTES, $default_charset);

		$nome_componente = $adb->query_result($result_query, 0, 'nome_componente');
		$nome_componente = html_entity_decode(strip_tags($nome_componente), ENT_QUOTES, $default_charset);

		$nome_check_list = $adb->query_result($result_query, 0, 'nome_check_list');
		$nome_check_list = html_entity_decode(strip_tags($nome_check_list), ENT_QUOTES, $default_charset);

		$componente = $adb->query_result($result_query, 0, 'kp_componente');
		$componente = html_entity_decode(strip_tags($componente), ENT_QUOTES, $default_charset);

		$check_list = $adb->query_result($result_query, 0, 'kp_check_list');
		$check_list = html_entity_decode(strip_tags($check_list), ENT_QUOTES, $default_charset);

		$impianto = $adb->query_result($result_query, 0, 'impianto');
		$impianto = html_entity_decode(strip_tags($impianto), ENT_QUOTES, $default_charset);

		$description = $adb->query_result($result_query, 0, 'description');
		$description = html_entity_decode(strip_tags($description), ENT_QUOTES, $default_charset);
		
		$rows[] = array('id' => $righemanutenzioniid,
						'data_manutenzione' => $data_manutenzione,
						'data_scadenza' => $data_scadenza,
						'componente' => $componente,
						'check_list' => $check_list,
						'impianto' => $impianto,
						'impianto_name' => $impianto_name,
						'nome_componente' => $nome_componente,
						'nome_check_list' => $nome_check_list,
						'description' => $description);


	}

}

$json = json_encode($rows);
print $json;
	
?>
