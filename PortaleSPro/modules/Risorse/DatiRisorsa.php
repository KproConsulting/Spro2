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

if(isset($_GET['risorsa'])){
    $risorsa = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['risorsa']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $risorsa = substr($risorsa,0,100);
	
	$q_contatto = "SELECT 
					cont.contactid contactid,
					cont.firstname nome,
					cont.lastname cognome,
					cont.phone telefono,
					cont.email email,
					cont.data_fine_rap data_fine_rap,
					cont.data_assunzione data_assunzione,
					contsub.birthday birthday,
					addr.mailingcountry mailingcountry,
					addr.mailingcity mailingcity,
					addr.mailingstate mailingstate,
					addr.mailingstreet mailingstreet,
					addr.mailingzip mailingzip,
					cont.stabilimento stabilimento,
					stab.nome_stabilimento nome_stabilimento,
					cont.description description
					from {$table_prefix}_contactdetails cont
					left join {$table_prefix}_contactsubdetails contsub on contsub.contactsubscriptionid = cont.contactid
					left join {$table_prefix}_contactaddress addr on addr.contactaddressid = cont.contactid
					inner join {$table_prefix}_crmentity ent on ent.crmid = cont.contactid
					left join {$table_prefix}_stabilimenti stab on stab.stabilimentiid = cont.stabilimento
					where ent.deleted = 0 and cont.accountid = ".$azienda." and cont.contactid = ".$risorsa;
	
	$res_contatto = $adb->query($q_contatto);
	if($adb->num_rows($res_contatto) > 0){
	
		$contactid = $adb->query_result($res_contatto, 0, 'contactid');
		$contactid = html_entity_decode(strip_tags($contactid), ENT_QUOTES,$default_charset);
		
		$nome = $adb->query_result($res_contatto, 0, 'nome');
		$nome = html_entity_decode(strip_tags($nome), ENT_QUOTES,$default_charset);
		
		$cognome = $adb->query_result($res_contatto, 0, 'cognome');
		$cognome = html_entity_decode(strip_tags($cognome), ENT_QUOTES,$default_charset);
		
		$telefono = $adb->query_result($res_contatto, 0, 'telefono');
		$telefono = html_entity_decode(strip_tags($telefono), ENT_QUOTES,$default_charset);
		
		$email = $adb->query_result($res_contatto, 0, 'email');
		$email = html_entity_decode(strip_tags($email), ENT_QUOTES,$default_charset);
		
		$nome_stabilimento = $adb->query_result($res_contatto, 0, 'nome_stabilimento');
		$nome_stabilimento = html_entity_decode(strip_tags($nome_stabilimento), ENT_QUOTES,$default_charset);
		if($nome_stabilimento == null || $nome_stabilimento == ""){
			$nome_stabilimento = "";
		}
		
		$stabilimento = $adb->query_result($res_contatto, 0, 'stabilimento');
		$stabilimento = html_entity_decode(strip_tags($stabilimento), ENT_QUOTES,$default_charset);
		if($stabilimento == null || $stabilimento == ""){
			$stabilimento = 0;
		}
		
		$data_fine_rap = $adb->query_result($res_contatto, 0, 'data_fine_rap');
		$data_fine_rap = html_entity_decode(strip_tags($data_fine_rap), ENT_QUOTES,$default_charset);
		if($data_fine_rap != null && $data_fine_rap != ""){
			list($anno, $mese, $giorno) = explode("-", $data_fine_rap);
            $data_fine_rap = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
		else{
			$data_fine_rap = "";
		}
		
		$data_assunzione = $adb->query_result($res_contatto, 0, 'data_assunzione');
		$data_assunzione = html_entity_decode(strip_tags($data_assunzione), ENT_QUOTES,$default_charset);
		if($data_assunzione != null && $data_assunzione != ""){
			list($anno, $mese, $giorno) = explode("-", $data_assunzione);
            $data_assunzione = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
		else{
			$data_assunzione = "";
		}
		
		$birthday = $adb->query_result($res_contatto, 0, 'birthday');
		$birthday = html_entity_decode(strip_tags($birthday), ENT_QUOTES,$default_charset);
		if($birthday != null && $birthday != ""){
			list($anno, $mese, $giorno) = explode("-", $birthday);
            $birthday = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
		else{
			$birthday = "";
		}
		
		$mailingcountry = $adb->query_result($res_contatto, 0, 'mailingcountry');
		$mailingcountry = html_entity_decode(strip_tags($mailingcountry), ENT_QUOTES,$default_charset);
		if($mailingcountry == null || $mailingcountry == ""){
			$mailingcountry = "";
		}
		
		$mailingcity = $adb->query_result($res_contatto, 0, 'mailingcity');
		$mailingcity = html_entity_decode(strip_tags($mailingcity), ENT_QUOTES,$default_charset);
		if($mailingcity == null || $mailingcity == ""){
			$mailingcity = "";
		}
		
		$mailingstate = $adb->query_result($res_contatto, 0, 'mailingstate');
		$mailingstate = html_entity_decode(strip_tags($mailingstate), ENT_QUOTES,$default_charset);
		if($mailingstate == null || $mailingstate == ""){
			$mailingstate = "";
		}
		
		$mailingstreet = $adb->query_result($res_contatto, 0, 'mailingstreet');
		$mailingstreet = html_entity_decode(strip_tags($mailingstreet), ENT_QUOTES,$default_charset);
		if($mailingstreet == null || $mailingstreet == ""){
			$mailingstreet = "";
		}
		
		$mailingzip = $adb->query_result($res_contatto, 0, 'mailingzip');
		$mailingzip = html_entity_decode(strip_tags($mailingzip), ENT_QUOTES,$default_charset);
		if($mailingzip == null || $mailingzip == ""){
			$mailingzip = "";
		}
		
		$description = $adb->query_result($res_contatto, 0, 'description');
		$description = html_entity_decode(strip_tags($description), ENT_QUOTES,$default_charset);
		if($description == null || $description == ""){
			$description = "";
		}
		
		$rows[] = array('contactid' => $contactid,
						'nome' => $nome,
						'cognome' => $cognome,
						'telefono' => $telefono,
						'email' => $email,
						'stabilimento' => $stabilimento,
						'nome_stabilimento' => $nome_stabilimento,
						'birthday' => $birthday,
						'data_assunzione' => $data_assunzione,
						'data_fine_rap' => $data_fine_rap,
						'mailingcountry' => $mailingcountry,
						'mailingcity' => $mailingcity,
						'mailingstate' => $mailingstate,
						'mailingstreet' => $mailingstreet,
						'mailingzip' => $mailingzip,
						'description' => $description);
		
	}
	
	$json = json_encode($rows);
	print $json;
	
}
?>
