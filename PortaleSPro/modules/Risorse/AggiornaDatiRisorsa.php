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
require_once("modules/SproCore/SproUtils/spro_utils.php");
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

if(isset($_GET['contactid']) && isset($_GET['cognome'])){
    $contactid = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['contactid']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $contactid = substr($contactid,0,100);
    
    $cognome = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['cognome']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$cognome = substr($cognome,0,255);
    
    if(isset($_GET['nome'])){
        $nome = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['nome']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $nome = substr($nome,0,255);
    }
    else{
        $nome = '';
    }
    
    if(isset($_GET['telefono'])){
        $telefono = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['telefono']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $telefono = substr($telefono,0,255);
    }
    else{
        $telefono = '';
    }
    
    if(isset($_GET['email'])){
        $email = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['email']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $email = substr($email,0,255);
    }
    else{
        $email = '';
    }
    
    if(isset($_GET['stabilimento'])){
        $stabilimento = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['stabilimento']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $stabilimento = substr($stabilimento,0,255);
        if($stabilimento == ""){
			$stabilimento = 0;
		}
    }
    else{
        $stabilimento = 0;
    }
	
	if(isset($_GET['birthday'])){
        $birthday = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['birthday']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $birthday = substr($birthday,0,255);
    }
    else{
        $birthday = '';
    }
    
    if(isset($_GET['data_assunzione'])){
        $data_assunzione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data_assunzione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $data_assunzione = substr($data_assunzione,0,255);
    }
    else{
        $data_assunzione = '';
    }
    
    if(isset($_GET['data_fine_rap'])){
        $data_fine_rap = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data_fine_rap']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $data_fine_rap = substr($data_fine_rap,0,255);
    }
    else{
        $data_fine_rap = '';
    }
    
    if(isset($_GET['description'])){
        $description = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['description']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    }
    else{
        $description = '';
    }
    
    if(isset($_GET['mailingcountry'])){
        $mailingcountry = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['mailingcountry']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $mailingcountry = substr($mailingcountry,0,255);
    }
    else{
        $mailingcountry = '';
    }
    
    if(isset($_GET['mailingstate'])){
        $mailingstate = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['mailingstate']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $mailingstate = substr($mailingstate,0,255);
    }
    else{
        $mailingstate = '';
    }
    
    if(isset($_GET['mailingcity'])){
        $mailingcity = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['mailingcity']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $mailingcity = substr($mailingcity,0,255);
    }
    else{
        $mailingcity = '';
    }
    
    if(isset($_GET['mailingstreet'])){
        $mailingstreet = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['mailingstreet']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $mailingstreet = substr($mailingstreet,0,255);
    }
    else{
        $mailingstreet = '';
    }
    
    if(isset($_GET['mailingzip'])){
        $mailingzip = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['mailingzip']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $mailingzip = substr($mailingzip,0,255);
    }
    else{
        $mailingzip = '';
    }
    
    if($contactid == 0 || $contactid == ""){
		
		$q_assegnatario = "select smownerid
							from {$table_prefix}_crmentity 
							where crmid = ".$azienda;
		$res_assegnatario = $adb->query($q_assegnatario);
		if($adb->num_rows($res_assegnatario) > 0){
		
			$assegnatario = $adb->query_result($res_assegnatario, 0, 'smownerid');
			$assegnatario = html_entity_decode(strip_tags($assegnatario), ENT_QUOTES,$default_charset);
			
		}
		else{
			$assegnatario = 1;
		}	

		$nuovo_contatto = CRMEntity::getInstance('Contacts'); 
        $nuovo_contatto->column_fields['assigned_user_id'] = $assegnatario;
        $nuovo_contatto->column_fields['lastname'] = $cognome;
        $nuovo_contatto->column_fields['account_id'] = $azienda;
        $nuovo_contatto->column_fields['stabilimento'] = $stabilimento;
        if($nome != ""){
			$nuovo_contatto->column_fields['firstname'] = $nome;
		}
        if($telefono != ""){
			$nuovo_contatto->column_fields['phone'] = $telefono;
		}
        $nuovo_contatto->column_fields['birthday'] = $birthday;
        $nuovo_contatto->column_fields['email'] = $email;
        $nuovo_contatto->column_fields['data_assunzione'] = $data_assunzione;
        $nuovo_contatto->column_fields['data_fine_rap'] = $data_fine_rap;
        $nuovo_contatto->column_fields['mailingcountry'] = $mailingcountry;
        $nuovo_contatto->column_fields['mailingcity'] = $mailingcity;
        $nuovo_contatto->column_fields['mailingstate'] = $mailingstate;
        $nuovo_contatto->column_fields['mailingstreet'] = $mailingstreet;
        $nuovo_contatto->column_fields['mailingzip'] = $mailingzip;
        if($description != ''){
            $nuovo_contatto->column_fields['description'] = $description;
        }
        $nuovo_contatto->save('Contacts', $longdesc=true, $offline_update=false, $triggerEvent=false); 
        $contactid = $nuovo_contatto->id;

        InvioNotificaPortale($contact_id, "creato la risorsa", $contactid, 'Contacts', $cognome.' '.$nome);
	}
	else{
		
		$cognome = addslashes($cognome);
		$nome = addslashes($nome);
		$telefono = addslashes($telefono);
		$email = addslashes($email);
		$stabilimento = addslashes($stabilimento);
		$description = addslashes($description);
		$mailingcountry = addslashes($mailingcountry);
		$mailingstate = addslashes($mailingstate);
		$mailingcity = addslashes($mailingcity);
		$mailingstreet = addslashes($mailingstreet);
		$mailingzip = addslashes($mailingzip);
		
		$upd_cont = "UPDATE {$table_prefix}_contactdetails set
						lastname = '".$cognome."',
						firstname = '".$nome."',
						phone = '".$telefono."',
						email = '".$email."',
						data_fine_rap = '".$data_fine_rap."',
						data_assunzione = '".$data_assunzione."',
                        stabilimento = ".$stabilimento.",
                        description = '".$description."'
						where contactid = ".$contactid; 
		$adb->query($upd_cont);
		
		$upd_cont_de = "UPDATE {$table_prefix}_contactsubdetails set
						birthday = '".$birthday."'
						where contactsubscriptionid = ".$contactid; 
		$adb->query($upd_cont_de);
		
		$upd_cont_add = "UPDATE {$table_prefix}_contactaddress set
							mailingcountry = '".$mailingcountry."',
							mailingcity = '".$mailingcity."',
							mailingstate = '".$mailingstate."',
							mailingstreet = '".$mailingstreet."',
							mailingzip = '".$mailingzip."'
							where contactaddressid = ".$contactid; 
		$adb->query($upd_cont_add);
        
        $res = InvioNotificaPortale($contact_id, "modificato la risorsa", $contactid, 'Contacts', $cognome.' '.$nome);

    }
	
	$rows[] = array('record' => $contactid);
	
	$json = json_encode($rows);
	print $json;
	
}
?>