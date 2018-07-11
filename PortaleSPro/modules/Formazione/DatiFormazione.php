<?php

/* kpro@tom17062016 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 * @package portaleVteSicurezza
 * @version 1.0
 */

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
    
    $q_account = "SELECT accountid
					FROM {$table_prefix}_contactdetails 
					WHERE contactid = ".$contact_id;
	$res_account = $adb->query($q_account);
			
	if($adb->num_rows($res_account) > 0){
		
		$azienda = $adb->query_result($res_account, 0, 'accountid');
		$azienda = html_entity_decode(strip_tags($azienda), ENT_QUOTES,$default_charset);
		
	}
	else{
	    header("Location: ../../login.php"); 
	}
	
}
else{
    header("Location: ../../login.php"); 
}

$rows = array();

if(isset($_GET['formazione'])){
    $formazione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['formazione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $formazione = substr($formazione,0,100);
    
    $q_formazione = "SELECT 
						form.kpformazioneid formazioneid,
						form.kp_nome_corso formazione_name,
						form.kp_data_scad_for validita_formazione,
						form.kp_data_formazione data_formazione,
						form.kp_tot_ore_formazio durata_formazione,
						form.kp_locazione locazione_formazione,
						form.kp_relatore relatore,
						form.kp_tipo_corso tipo_corso,
						form.description description,
						tc.tipicorso_name tipicorso_name
						from {$table_prefix}_kpformazione form
						inner join {$table_prefix}_crmentity ent on ent.crmid = form.kpformazioneid
						inner join {$table_prefix}_tipicorso tc on tc.tipicorsoid = form.kp_tipo_corso
						where form.kpformazioneid = ".$formazione;
	
	$res_formazione = $adb->query($q_formazione);
	if($adb->num_rows($res_formazione) > 0){
	
		$formazioneid = $adb->query_result($res_formazione, 0, 'formazioneid');
		$formazioneid = html_entity_decode(strip_tags($formazioneid), ENT_QUOTES,$default_charset);
		
		$formazione_name = $adb->query_result($res_formazione, 0, 'formazione_name');
		$formazione_name = html_entity_decode(strip_tags($formazione_name), ENT_QUOTES,$default_charset);
		
		$data_formazione = $adb->query_result($res_formazione, 0, 'data_formazione');
		$data_formazione = html_entity_decode(strip_tags($data_formazione), ENT_QUOTES,$default_charset);
		if($data_formazione != null && $data_formazione != ""){
			list($anno, $mese, $giorno) = explode("-", $data_formazione);
            $data_formazione = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
		else{
			$data_formazione = "";
		}
		
		$validita_formazione = $adb->query_result($res_formazione, 0, 'validita_formazione');
		$validita_formazione = html_entity_decode(strip_tags($validita_formazione), ENT_QUOTES,$default_charset);
		if($validita_formazione != null && $validita_formazione != ""){
			list($anno, $mese, $giorno) = explode("-", $validita_formazione);
            $validita_formazione = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
		else{
			$validita_formazione = "";
		}
		
		$durata_formazione = $adb->query_result($res_formazione, 0, 'durata_formazione');
		$durata_formazione = html_entity_decode(strip_tags($durata_formazione), ENT_QUOTES,$default_charset);
		
		$locazione_formazione = $adb->query_result($res_formazione, 0, 'locazione_formazione');
		$locazione_formazione = html_entity_decode(strip_tags($locazione_formazione), ENT_QUOTES,$default_charset);
		
		$relatore = $adb->query_result($res_formazione, 0, 'relatore');
		$relatore = html_entity_decode(strip_tags($relatore), ENT_QUOTES,$default_charset);
		
		$description = $adb->query_result($res_formazione, 0, 'description');
		$description = html_entity_decode(strip_tags($description), ENT_QUOTES,$default_charset);
		
		$tipo_corso = $adb->query_result($res_formazione, 0, 'tipo_corso');
		$tipo_corso = html_entity_decode(strip_tags($tipo_corso), ENT_QUOTES,$default_charset);
		
		$tipicorso_name = $adb->query_result($res_formazione, 0, 'tipicorso_name');
		$tipicorso_name = html_entity_decode(strip_tags($tipicorso_name), ENT_QUOTES,$default_charset);
		
		$rows[] = array('formazioneid' => $formazioneid,
						'formazione_name' => $formazione_name,
						'data_formazione' => $data_formazione,
						'validita_formazione' => $validita_formazione,
						'durata_formazione' => $durata_formazione,
						'locazione_formazione' => $locazione_formazione,
						'relatore' => $relatore,
						'description' => $description,
						'tipo_corso' => $tipo_corso,
						'tipicorso_name' => $tipicorso_name);
		
	}
	
	$json = json_encode($rows);
	print $json;
	
}
?>