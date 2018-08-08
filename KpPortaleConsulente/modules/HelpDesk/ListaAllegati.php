<?php

/* kpro@tom10042017 */
		
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
require_once('modules/SproCore/SproUtils/spro_utils.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_language;
session_start();

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
	}
	
}
else{
    header("Location: ../../login.php"); 
}

$rows = array();

if(isset($_GET['record'])){
    $record = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['record']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $record = substr($record,0,100);

	if(!verificaSeFornitoreAbilitatoPerTicket($record, $fornitore)){
		die;
	}

	$dati_ticket = getDatiTicket($record);

	$numero_ticket_aperti = getNumeroTicketApertiPerCliente($dati_ticket["azienda"], $fornitore);

	$fornitore_relazionato = checkIfFornitoreRelazionatoAzienda($dati_ticket["azienda"], $fornitore);
    
    if(isset($_GET['nome_documento'])){
        $search_nome_documento = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['nome_documento']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $search_nome_documento = substr($search_nome_documento,0,255);
    }
    else{
        $search_nome_documento = '';
    }

	if(isset($_GET['stato_documento'])){
        $search_stato = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['stato_documento']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
    
		if($search_stato != ""){

			$search_stato = explode(',', $search_stato);
			
			$search_lista_stati = "";
				
			foreach($search_stato as $stato){

				if($search_lista_stati == ""){
					$search_lista_stati = "'".$stato."'";
				}
				else{
					$search_lista_stati .= ",'".$stato."'";
				}
			}
		
		}

	}
	else{
		$search_lista_stati = "";
	}
    
    if(isset($_GET['data'])){
        $search_data = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $search_data = substr($search_data,0,255);
    }
    else{
        $search_data = '';
	}
	
	$id_statici = getConfigurazioniIdStatici();
	$id_statico = $id_statici["Documenti - Cartella Caricati da Portale (Allegati Vari Ticket)"];
	if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
		return;
	}
	else{
		$cartella_documenti = $id_statico["valore"];
	}
	
	$q_documenti = "SELECT attac.attachmentsid attachmentsid, 
					attac.name name, 
					attac.path path, 
					notes.title title, 
					notes.notesid notesid, 
					notes.filelocationtype filelocationtype,
					notes.folderid cartella_id,
					notes.data_scadenza data_scadenza,
					notes.kp_stato_avanzament stato_documento,
					notes.kp_data_documento data_documento,
					date(ent.createdtime) data_creazione,
					ent.createdtime createdtime, 
					ent.modifiedtime modifiedtime 
					FROM {$table_prefix}_notes notes 
					INNER JOIN {$table_prefix}_notescf notescf ON notescf.notesid = notes.notesid 
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = notes.notesid 
					LEFT JOIN {$table_prefix}_senotesrel senote ON senote.notesid = notes.notesid
					LEFT JOIN {$table_prefix}_seattachmentsrel seattac ON seattac.crmid = notes.notesid 
					INNER JOIN {$table_prefix}_attachments attac ON attac.attachmentsid = seattac.attachmentsid 
					WHERE ent.deleted = 0 AND (notes.kp_annullato_portal = '0' OR notes.kp_annullato_portal = '' 
					OR notes.kp_annullato_portal IS NULL) AND notes.active_portal = 1 AND notes.folderid = {$cartella_documenti} 
					AND senote.crmid = ".$record;
					
	if($search_nome_documento != ""){
		$q_documenti .= " AND notes.title like '%".$search_nome_documento."%'";
	}

	if($search_lista_stati != ""){
		$q_documenti .= " AND notes.kp_stato_avanzament IN ('', ".$search_lista_stati.")";
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

		if($search_data == "" || ($search_data != "" && $data_documento == $search_data)){

			if( $numero_ticket_aperti > 0 || $fornitore_relazionato ){
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
			}
			else{
				$attachmentsid = 0;
				$tipo_download = "";
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