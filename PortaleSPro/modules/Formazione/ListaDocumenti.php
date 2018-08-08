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
    
    if(isset($_GET['nome_documento'])){
        $nome_documento = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['nome_documento']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $nome_documento = substr($nome_documento,0,255);
    }
    else{
        $nome_documento = '';
    }
    
    if(isset($_GET['data'])){
        $data_documento_filtro = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $data_documento_filtro = substr($data_documento_filtro,0,255);
    }
    else{
        $data_documento_filtro = '';
    }
    
    if(isset($_GET['data_scadenza'])){
        $data_scadenza = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data_scadenza']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $data_scadenza = substr($data_scadenza,0,255);
    }
    else{
        $data_scadenza = '';
    }
    
    if(isset($_GET['stato_documento'])){
        $stato_documento = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['stato_documento']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
		
		if($stato_documento != ""){
			$stato_documento = explode(',', $stato_documento);
			
			$lista_stati = "";
				
			foreach($stato_documento as $stato){
				if($lista_stati == ""){
					$lista_stati = "'".$stato."'";
				}
				else{
					$lista_stati .= ",'".$stato."'";
				}
			}
		}
		else{
			$lista_stati = "";
		}
		
    }
    else{
        $lista_stati = "";
    }
	
	$q_documenti = "SELECT attac.attachmentsid attachmentsid, 
					attac.name name, 
					attac.path path, 
					notes.title title, 
					notes.notesid notesid, 
					notes.folderid cartella_id,
					notes.data_scadenza data_scadenza,
					notes.stato_documento stato_documento,
					notes.kp_data_documento data_documento,
					date(ent.createdtime) data_creazione,
					ent.createdtime createdtime, 
					ent.modifiedtime modifiedtime,
					notes.filelocationtype filelocationtype
					FROM {$table_prefix}_notes notes 
					INNER JOIN {$table_prefix}_notescf notescf ON notescf.notesid = notes.notesid 
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = notes.notesid 
					LEFT JOIN {$table_prefix}_senotesrel senote ON senote.notesid = notes.notesid
					LEFT JOIN {$table_prefix}_seattachmentsrel seattac ON seattac.crmid = notes.notesid 
					LEFT JOIN {$table_prefix}_attachments attac ON attac.attachmentsid = seattac.attachmentsid 
					WHERE ent.deleted = 0 AND senote.crmid = ".$formazione." AND notes.active_portal = 1";
					
	if($nome_documento != ""){
		$q_documenti .= " and notes.title like '%".$nome_documento."%'";
	}
	if($lista_stati != ""){
		$q_documenti .= " and notes.stato_documento in (".$lista_stati.")";
	}
	if($data_scadenza != ""){
		$q_documenti .= " and notes.data_scadenza = '".$data_scadenza."'";
	}
	
	$q_documenti .= " ORDER BY ent.createdtime DESC";

	$res_documenti = $adb->query($q_documenti);
	$num_documenti = $adb->num_rows($res_documenti);
	
	for($i=0; $i < $num_documenti; $i++){
		$title = $adb->query_result($res_documenti, $i, 'title');
		$title = html_entity_decode(strip_tags($title), ENT_QUOTES,$default_charset);
		
		$notesid = $adb->query_result($res_documenti, $i, 'notesid');
		$notesid = html_entity_decode(strip_tags($notesid), ENT_QUOTES,$default_charset);

		$data_documento = $adb->query_result($res_documenti, $i, 'data_documento');
		$data_documento = html_entity_decode(strip_tags($data_documento), ENT_QUOTES,$default_charset);
		if($data_documento != null && $data_documento != "" && $data_documento != "0000-00-00"){
			list($anno, $mese, $giorno) = explode("-", $data_documento);
			$data_documento_inv = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
		else{
			$data_documento = $adb->query_result($res_documenti, $i, 'data_creazione');
			$data_documento = html_entity_decode(strip_tags($data_documento), ENT_QUOTES,$default_charset);
			if($data_documento != null && $data_documento != "" && $data_documento != "0000-00-00"){
				list($anno, $mese, $giorno) = explode("-", $data_documento);
				$data_documento_inv = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
			}
		}

		if($data_documento_filtro == "" || ($data_documento_filtro != "" && $data_documento == $data_documento_filtro)){
		
			$filelocationtype = $adb->query_result($res_documenti, $i, 'filelocationtype');
			$filelocationtype = html_entity_decode(strip_tags($filelocationtype), ENT_QUOTES,$default_charset);
			if($filelocationtype == "E"){
				$attachmentsid = 0;

				$tipo_download = "Esterno";
			}
			else{
				$attachmentsid = $adb->query_result($res_documenti, $i, 'attachmentsid');
				$attachmentsid = html_entity_decode(strip_tags($attachmentsid), ENT_QUOTES,$default_charset);
				
				$tipo_download = "Interno";
			}
			
			$createdtime = $adb->query_result($res_documenti, $i, 'createdtime');
			$createdtime = html_entity_decode(strip_tags($createdtime), ENT_QUOTES,$default_charset);
			
			$modifiedtime = $adb->query_result($res_documenti, $i, 'modifiedtime');
			$modifiedtime = html_entity_decode(strip_tags($modifiedtime), ENT_QUOTES,$default_charset);
			
			$data_scadenza = $adb->query_result($res_documenti, $i, 'data_scadenza');
			$data_scadenza = html_entity_decode(strip_tags($data_scadenza), ENT_QUOTES,$default_charset);
			if($data_scadenza != null && $data_scadenza != ""){
				list($anno, $mese, $giorno) = explode("-", $data_scadenza);
				$data_scadenza = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
			}
			
			$stato_documento = $adb->query_result($res_documenti, $i, 'stato_documento');
			$stato_documento = html_entity_decode(strip_tags($stato_documento), ENT_QUOTES,$default_charset);
			
			$rows[] = array('notesid' => $notesid,
							'attachmentsid' => $attachmentsid,
							'title' => $title,
							'createdtime' => $createdtime,
							'modifiedtime' => $modifiedtime,
							'data_documento' => $data_documento_inv,
							'data_scadenza' => $data_scadenza,
							'stato_documento' => $stato_documento,
							'tipo' => $tipo_download);
		}
		
	}
	
	$json = json_encode($rows);
	print $json;
	
}
?>