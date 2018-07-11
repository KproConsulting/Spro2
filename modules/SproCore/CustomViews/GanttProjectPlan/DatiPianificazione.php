<?php

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

if(isset($_SESSION['authenticated_user_id'])){
    //header("Location: ".$site_URL."/index.php?module=Accounts&action=index");
	
	$current_user->id = $_SESSION['authenticated_user_id'];
	$utente_portale = 'no';
	$app_fatture = true;
	$app_report_visit = true;
	$app_assistenza = true;
	$app_pianificazioni = true;
	
}
else if(isset($_SESSION['customer_sessionid'])){
	$contact_id = $_SESSION['customer_id'];
	$sessionid = $_SESSION['customer_sessionid'];
	$customer_name = $_SESSION['customer_name'];
	$utente_portale = 'si';
	$app_fatture = false;
	$app_report_visit = false;
	$app_assistenza = false;
	$app_pianificazioni = false;
	
	$q_account = "SELECT accountid,
					app_fatture,
					app_report_visit,
					app_pianificazioni,
					app_assistenza
					FROM {$table_prefix}_contactdetails 
					WHERE contactid = ".$contact_id;
	$res_account = $adb->query($q_account);
			
	if($adb->num_rows($res_account)>0){
		
		$azienda = $adb->query_result($res_account, 0, 'accountid');
		
		$app_fatture = $adb->query_result($res_account, 0, 'app_fatture');
		if($app_fatture == '1'){
			$app_fatture = true;
		}
		else{
			$app_fatture = false;
		}
		
		$app_report_visit = $adb->query_result($res_account, 0, 'app_report_visit');
		if($app_report_visit == '1'){
			$app_report_visit = true;
		}
		else{
			$app_report_visit = false;
		}
		
		$app_pianificazioni = $adb->query_result($res_account, 0, 'app_pianificazioni');
		if($app_pianificazioni == '1'){
			$app_pianificazioni = true;
		}
		else{
			$app_pianificazioni = false;
		}
		
		$app_assistenza = $adb->query_result($res_account, 0, 'app_assistenza');
		if($app_assistenza == '1'){
			$app_assistenza = true;
		}
		else{
			$app_assistenza = false;
		}
		
	}
	
}
else{
	die;
}

if(!$app_pianificazioni){
	header("Location: ".$site_URL."/portal/login.php");
	//die;
}

$rows = array();

if(isset($_GET['pianificazione'])){
	$pianificazione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['pianificazione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$pianificazione = substr($pianificazione,0,100);
	
	$q_pianificazione = "SELECT pro.projectname projectname,
							pro.projectstatus projectstatus,
							pro.startdate startdate,
							pro.actualenddate actualenddate,
							pro.description description
							FROM {$table_prefix}_project pro
							INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = pro.projectid
							WHERE pro.projectid = ".$pianificazione;
						
	$res_pianificazione = $adb->query($q_pianificazione);
	if($adb->num_rows($res_pianificazione)>0){
		$projectname = $adb->query_result($res_pianificazione,0,'projectname');
		$projectname = html_entity_decode(strip_tags($projectname), ENT_QUOTES,$default_charset);
		
		$projectstatus = $adb->query_result($res_pianificazione,0,'projectstatus');
		$projectstatus = html_entity_decode(strip_tags($projectstatus), ENT_QUOTES,$default_charset);
	
		$startdate = $adb->query_result($res_pianificazione,0,'startdate');
		if($startdate == null || $startdate == ''){
			$startdate = '';
		}
		else{
			list($anno,$mese,$giorno) = explode("-",$startdate);
			$startdate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
		}
		
		$actualenddate = $adb->query_result($res_pianificazione,0,'actualenddate');
		if($actualenddate == null || $actualenddate == ''){
			$actualenddate = '';
		}
		else{
			list($anno,$mese,$giorno) = explode("-",$actualenddate);
			$actualenddate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
		}
		
		$description = $adb->query_result($res_pianificazione,0,'description');
		$description = html_entity_decode(strip_tags($description), ENT_QUOTES,$default_charset);

		$rows[] = array('projectname' => $projectname,
						'projectstatus' => $projectstatus,
						'startdate' => $startdate,
						'startdate_inv' => $startdate_inv,
						'actualenddate' => $actualenddate,
						'actualenddate_inv' => $actualenddate_inv,
						'description' => $description);
	}
	
}
							
$json = json_encode($rows);
print $json;
	
?>