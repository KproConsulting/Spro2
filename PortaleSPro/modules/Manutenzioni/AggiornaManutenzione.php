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
require_once("modules/SproCore/SproUtils/spro_utils.php");
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_language, $default_charset;
session_start();

require_once('modules/SproCore/KpClass/KpManutenzioni_class.php');
require_once('modules/SproCore/KpClass/KpRigheManutenzioni_class.php');

require_once('plugins/script_schedulati/aggiorna_check_list.php');

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

if( isset($_GET['record']) && isset($_GET['impianto']) && isset($_GET['componente']) && isset($_GET['checklist']) && isset($_GET['data_esecuzione']) && $azienda != 0 ){
	$record = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['record']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
	$record = substr($record, 0, 100);

	$impianto = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['impianto']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
	$impianto = substr($impianto, 0, 100);

	$componente = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['componente']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
	$componente = substr($componente, 0, 100);

	$checklist = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['checklist']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
	$checklist = substr($checklist, 0, 100);

	$data_esecuzione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data_esecuzione']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
	$data_esecuzione = substr($data_esecuzione, 0, 100);

	if( isset($_GET['data_scadenza']) ){
		$data_scadenza = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data_scadenza']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
		$data_scadenza = substr($data_scadenza, 0, 100);
	}
	else{
		$data_scadenza = "";
	}

	if( isset($_GET['descrizione']) ){
		$descrizione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['descrizione']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
	}
	else{
		$descrizione = "";
	}

	$dati_checklist = getDatiCheckList($checklist);

	if($record == 0){

		$q_numero_generazione = "SELECT COALESCE(MAX(numero_generazione), 0) ultimo_numero_generazione							
									FROM {$table_prefix}_manutenzioni man
									INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = man.manutenzioniid
									WHERE ent.deleted = 0";

		$res_numero_generazione = $adb->query($q_numero_generazione);
		if($adb->num_rows($res_numero_generazione)>0){
			$ultimo_numero_generazione = $adb->query_result($res_numero_generazione, 0, 'ultimo_numero_generazione');
			$nuovo_numero_generazione = $ultimo_numero_generazione + 1;
		}

		$nome_manutenzione = "Man. Prog. ".$nuovo_numero_generazione;

		$nuova_manutenzione = new KpManutenzioni_class();
		$nuova_manutenzione->manutenzione_name = $nome_manutenzione;
		$nuova_manutenzione->numero_generazione = $nuovo_numero_generazione;
		$nuova_manutenzione->stato_manutenzione = "Eseguita";
		$nuova_manutenzione->tipo_manutenzione = "Programmata";
		$nuova_manutenzione->data_scad_manut = $data_scadenza;
		$nuova_manutenzione->data_manutenzione = $data_esecuzione;
		$nuova_manutenzione->description = $descrizione;
		$manutenzioneid = $nuova_manutenzione->salva();

		$nuova_riga_manutenzione = new KpRigheManutenzioni_class();
		$nuova_riga_manutenzione->componente = $componente;
		$nuova_riga_manutenzione->check_list = $checklist;
		$nuova_riga_manutenzione->manutenzione = $manutenzioneid;
		$nuova_riga_manutenzione->data_scadenza = $data_scadenza;
		$nuova_riga_manutenzione->frequenza_checklist = $dati_checklist["frequenza_checklist"];
		$riga_manutenzioneid = $nuova_riga_manutenzione->salva();

		$nuova_manutenzione->aggiornaTempoPrevistoManutenzione();

		$insert_rel1 = "INSERT INTO {$table_prefix}_crmentityrel (crmid, module, relcrmid, relmodule)
						VALUES (".$manutenzioneid.", 'Manutenzioni', ".$impianto.", 'Impianti')";
		$adb->query($insert_rel1);

		$rows[] = array('id' => $riga_manutenzioneid);

		InvioNotificaPortale($contact_id, "creato la manutenzione", $manutenzioneid, 'Manutenzioni', $nome_manutenzione);

	} else{

		$descrizione = addslashes($descrizione);
		$data_scadenza = addslashes($data_scadenza);
		$data_esecuzione = addslashes($data_esecuzione);

		$dati_riga_manutenzione = getRigaManutenzione($record);
		$dati_manutenzione = getDatiManutenzione($dati_riga_manutenzione["manutenzione"]);

		$update = "UPDATE {$table_prefix}_manutenzioni SET
					data_manutenzione = '".$data_esecuzione."',
					data_scad_manut = '".$data_scadenza."',
					description = '".$descrizione."'
					WHERE manutenzioniid = ".$dati_riga_manutenzione["manutenzione"];
		$adb->query($update);

		$update = "UPDATE {$table_prefix}_kprighemanutenzioni SET
					kp_data_scadenza = '".$data_scadenza."'
					WHERE kprighemanutenzioniid = ".$record;
		$adb->query($update);

		$rows[] = array('id' => $record);

		InvioNotificaPortale($contact_id, "modificato la manutenzione", $dati_riga_manutenzione["manutenzione"], 'Manutenzioni', $dati_manutenzione['nome_manutenzione']);
	}

	$q_giorni_in_scad = "SELECT giorni_in_scadenza FROM {$table_prefix}_gestioneavvisi
							INNER JOIN {$table_prefix}_crmentity ON crmid = gestioneavvisiid
							WHERE deleted = 0 AND tipo_avviso = 'Check List' AND stabilimento =".$azienda;
	$res_giorni_in_scad = $adb->query($q_giorni_in_scad);
	if($adb->num_rows($res_giorni_in_scad)>0){	
		$giorni_in_scadenza = $adb->query_result($res_giorni_in_scad,0,'giorni_in_scadenza');
	}
	else{
		$giorni_in_scadenza = 0;
	}
	
	aggiornaSituazioneComponente($componente, $azienda, $giorni_in_scadenza);

}

$json = json_encode($rows);
print $json;

function getDatiCheckList($id){
	global $adb, $table_prefix, $default_charset;

	$result = "";

	$query = "SELECT 
				checkl.nome_check_list nome_check_list,
				checkl.frequenza_checklist frequenza_checklist
				FROM {$table_prefix}_checklists checkl
				WHERE checkl.checklistsid = ".$id;

	$result_query = $adb->query($query);
	$num_result = $adb->num_rows($result_query);

	if( $num_result > 0 ){

		$nome_check_list = $adb->query_result($result_query, 0, 'nome_check_list');
		$nome_check_list = html_entity_decode(strip_tags($nome_check_list), ENT_QUOTES, $default_charset);
		
		$frequenza_checklist = $adb->query_result($result_query, 0, 'frequenza_checklist');
		$frequenza_checklist = html_entity_decode(strip_tags($frequenza_checklist), ENT_QUOTES, $default_charset);

	}
	else{

		$nome_check_list = "";
		$frequenza_checklist = "";

	}

	$result = array("nome_check_list" => $nome_check_list,
					"frequenza_checklist" => $frequenza_checklist);

	return $result;

}

function getRigaManutenzione($id){
	global $adb, $table_prefix, $default_charset;

	$result = "";

	$query = "SELECT 
				rigman.kp_manutenzione kp_manutenzione
				FROM {$table_prefix}_kprighemanutenzioni rigman
				WHERE rigman.kprighemanutenzioniid = ".$id;

	$result_query = $adb->query($query);
	$num_result = $adb->num_rows($result_query);

	if( $num_result > 0 ){

		$manutenzione = $adb->query_result($result_query, 0, 'kp_manutenzione');
		$manutenzione = html_entity_decode(strip_tags($manutenzione), ENT_QUOTES, $default_charset);

	}
	else{

		$manutenzione = 0;

	}

	$result = array("manutenzione" => $manutenzione);

	return $result;

}

function getDatiManutenzione($id){
	global $adb, $table_prefix, $default_charset;

	$result = "";

	$query = "SELECT 
				man.manutenzione_name manutenzione_name
				FROM {$table_prefix}_manutenzioni man
				WHERE man.manutenzioniid = ".$id;

	$result_query = $adb->query($query);
	$num_result = $adb->num_rows($result_query);

	if( $num_result > 0 ){

		$manutenzione_name = $adb->query_result($result_query, 0, 'manutenzione_name');
		$manutenzione_name = html_entity_decode(strip_tags($manutenzione_name), ENT_QUOTES, $default_charset);

	}
	else{

		$manutenzione_name = "";

	}

	$result = array("nome_manutenzione" => $manutenzione_name);

	return $result;

}
	
?>