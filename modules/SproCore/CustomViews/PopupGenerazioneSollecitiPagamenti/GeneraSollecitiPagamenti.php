<?php

/* kpro@bid15112017 */
/**
 * @author Bidese Jacopo
 * @copyright (c) 2017, Kpro Consulting Srl
 * @package GenerazioneSollecitiPagamenti
 * @version 1.0
 *
 */

include_once('../../../../config.inc.php');;
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
require_once("modules/PDFMaker/InventoryPDF.php");
require_once("include/mpdf/mpdf.php");

$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $default_charset, $site_URL;
$db="adb";
$vcv="vtiger_current_version";
$salt="site_URL";
session_start();

$current_user->id = 1;

$rows = array();
if(isset($_REQUEST['record'])){
    $scadenziarioid = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_REQUEST['record']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $scadenziarioid = substr($scadenziarioid,0,100);
	
	$q_scadenza = "SELECT scad.scadenziarioid scadenziarioid, 
				scad.azienda azienda, 
				acc.accountname accountname,
				scad.kp_business_unit kp_business_unit,
				scad.invoice invoice,
				ent.smownerid assegnatario
				FROM {$table_prefix}_scadenziario scad
				INNER JOIN {$table_prefix}_account acc ON acc.accountid = scad.azienda
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = scad.scadenziarioid
				WHERE ent.deleted = 0 AND scad.scadenziarioid = ".$scadenziarioid;

	$res_scadenza = $adb->query($q_scadenza);
	if($adb->num_rows($res_scadenza)>0){
		$invoice = $adb->query_result($res_scadenza,0,'invoice');
		$invoice = html_entity_decode(strip_tags($invoice), ENT_QUOTES,$default_charset);
		
		$business_unit = $adb->query_result($res_scadenza,0,'kp_business_unit');
		$business_unit = html_entity_decode(strip_tags($business_unit), ENT_QUOTES,$default_charset);
		if($business_unit == '' || $business_unit == null){
			$business_unit = 0;
		}
		
		$azienda = $adb->query_result($res_scadenza,0,'azienda');
		$azienda = html_entity_decode(strip_tags($azienda), ENT_QUOTES,$default_charset);
		
		$accountname = $adb->query_result($res_scadenza,0,'accountname');
		$accountname = html_entity_decode(strip_tags($accountname), ENT_QUOTES,$default_charset);
		
		$scadenziarioid = $adb->query_result($res_scadenza,0,'scadenziarioid');
		$scadenziarioid = html_entity_decode(strip_tags($scadenziarioid), ENT_QUOTES,$default_charset);
		
		$assegnatario = $adb->query_result($res_scadenza,0,'assegnatario');
		$assegnatario = html_entity_decode(strip_tags($assegnatario), ENT_QUOTES,$default_charset);
		
		$q_contatto = "SELECT inv.contactid
						FROM {$table_prefix}_invoice inv
						INNER JOIN {$table_prefix}_contactdetails cont ON cont.contactid = inv.contactid
						WHERE inv.invoiceid = ".$invoice;
		$res_contatto = $adb->query($q_contatto);
		if($adb->num_rows($res_contatto)>0){
			$contactid = $adb->query_result($res_contatto, 0, 'contactid');
			$contactid = html_entity_decode(strip_tags($contactid), ENT_QUOTES,$default_charset);
		}
		else{
			$contactid = 0;
		}

		$q_email = "SELECT acc.kp_email_invio_doc AS email_invio_documenti
				FROM {$table_prefix}_invoice inv
				INNER JOIN {$table_prefix}_account acc ON acc.accountid = inv.accountid
				WHERE inv.invoiceid = ".$invoice;
		$res_email = $adb->query($q_email);
		if($adb->num_rows($res_email)>0){
			$email = $adb->query_result($res_email, 0, 'email_invio_documenti');
			$email = html_entity_decode(strip_tags($email), ENT_QUOTES,$default_charset);			
			if($email != '' && $email != null){
				$email_sollecito = $email;
			}
		}
		else{
			$email_sollecito = '';
		}

		$data_corrente = date ("Y-m-d");
		
		//Verifica se esiste già un sollecito in stato creato per questo cliente
		$q_sollecito = "SELECT sol.kpsollecitipagamentiid 
					FROM {$table_prefix}_kpsollecitipagamenti sol
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sol.kpsollecitipagamentiid
					WHERE ent.deleted = 0 
					AND (sol.kp_stato_sollecito = 'Creato' 
					OR (sol.kp_stato_sollecito = 'Inviato' AND sol.kp_data_sollecito = '".$data_corrente."')) 
					AND sol.kp_azienda = ".$azienda;
		
		$res_sollecito = $adb->query($q_sollecito);
		if($adb->num_rows($res_sollecito)>0){
			$sollecitipagamentiid = $adb->query_result($res_sollecito,0,'kpsollecitipagamentiid');
			
			//Verifico se lo scadenziario in questione è già relazionato al sollecito trovato
			$q_rel = "(SELECT *
					FROM {$table_prefix}_crmentityrel
					WHERE crmid = ".$sollecitipagamentiid." AND module = 'KpSollecitiPagamenti' 
					AND relcrmid = ".$scadenziarioid." AND relmodule = 'Scadenziario')
					UNION
					(SELECT *
					FROM {$table_prefix}_crmentityrel
					WHERE crmid = ".$scadenziarioid." AND module = 'Scadenziario' 
					AND relcrmid = ".$sollecitipagamentiid." AND relmodule = 'KpSollecitiPagamenti')";
			$res_rel = $adb->query($q_rel);
			if($adb->num_rows($res_rel)==0){
				//Creo la relazione
				$insert_rel = "INSERT INTO {$table_prefix}_crmentityrel (crmid, module, relcrmid, relmodule)
							VALUES (".$sollecitipagamentiid.", 'KpSollecitiPagamenti', ".$scadenziarioid.", 'Scadenziario')";
				$adb->query($insert_rel);	
				
				AllegaDocumenti($invoice,$sollecitipagamentiid);

				$rows[] = array(
					"res"=>"ok"
				);
			}
			else{
				$rows[] = array(
					"res"=>"error"
				);
			}
		}
		else{
			//Creo il sollecito
			$new_sollecito = CRMEntity::getInstance('KpSollecitiPagamenti'); 
			$new_sollecito->column_fields['assigned_user_id'] = $assegnatario;
			$new_sollecito->column_fields['kp_nome_sollecito'] = 'Sollecito Pagamento di '.$accountname.' del '.$data_corrente;
			$new_sollecito->column_fields['kp_azienda'] = $azienda;
			if($contactid != 0 && $contactid != ''){
				$new_sollecito->column_fields['kp_risorsa'] = $contactid;
			}
			$new_sollecito->column_fields['kp_data_sollecito'] = $data_corrente;
			$new_sollecito->column_fields['kp_business_unit'] = $business_unit;
			$new_sollecito->column_fields['kp_stato_sollecito'] = 'Creato';
			$new_sollecito->column_fields['kp_email'] = $email_sollecito;
			$new_sollecito->save('KpSollecitiPagamenti', $longdesc=true, $offline_update=false, $triggerEvent=false); 
			$sollecitipagamentiid = $new_sollecito->id;
			
			//Creo la relazione
			$insert_rel = "INSERT INTO {$table_prefix}_crmentityrel (crmid, module, relcrmid, relmodule)
							VALUES (".$sollecitipagamentiid.", 'KpSollecitiPagamenti', ".$scadenziarioid.", 'Scadenziario')";
			$adb->query($insert_rel);	
			
			AllegaDocumenti($invoice,$sollecitipagamentiid);

			$rows[] = array(
				"res"=>"ok"
			);
		}
		
	}
	else{
		$rows[] = array(
			"res"=>"error"
		);
	}
}

$json = json_encode($rows);
print $json;

function AllegaDocumenti($id_record,$id_destinazione){
	global $adb, $table_prefix;
	/* kpro@bid180920180920 */
	require_once("modules/SproCore/SproUtils/spro_utils.php");

	$id_statici = getConfigurazioniIdStatici();
	
	$id_statico = $id_statici["Documenti - Cartella Fatture"];
	if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
		$cartella_fatture = 0;
	}
	else{
		$cartella_fatture = $id_statico["valore"];
	}

	$id_statico = $id_statici["Documenti - Cartella Fatture Proforma"];
	if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
		$cartella_fatture_proforma = 0;
	}
	else{
		$cartella_fatture_proforma = $id_statico["valore"];
	}

	$q_documenti = "SELECT notes.notesid notesid,
					notes.title title,
					notes.filetype filetype,
					att.attachmentsid attachmentsid,
					att.NAME filename,
					att.path cartella
					FROM {$table_prefix}_senotesrel notesrel 
					INNER JOIN {$table_prefix}_notes notes ON notes.notesid = notesrel.notesid
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = notes.notesid
					INNER JOIN {$table_prefix}_seattachmentsrel attrel ON attrel.crmid = notes.notesid
					INNER JOIN {$table_prefix}_attachments att ON att.attachmentsid = attrel.attachmentsid
					WHERE ent.deleted = 0 AND notes.filetype = 'application/pdf' 
					AND notesrel.crmid = ".$id_record;
	
	if($cartella_fatture != 0 && $cartella_fatture_proforma != 0 && $cartella_fatture != $cartella_fatture_proforma){
		$q_documenti .= " AND notes.folderid IN (".$cartella_fatture.",".$cartella_fatture_proforma.")";
	}
	elseif($cartella_fatture != 0){
		$q_documenti .= " AND notes.folderid = ".$cartella_fatture;
	}
	elseif($cartella_fatture_proforma != 0){
		$q_documenti .= " AND notes.folderid = ".$cartella_fatture_proforma;
	}
	/* kpro@bid180920180920 end */
	$res_documenti = $adb->query($q_documenti);
	$num_documenti = $adb->num_rows($res_documenti);
	for($k=0; $k<$num_documenti; $k++){	
		$notesid = $adb->query_result($res_documenti, $k, 'notesid');
		
		$q_verifica_se_gia_coll = "SELECT *
								FROM {$table_prefix}_senotesrel 
								WHERE relmodule = 'KpSollecitiPagamenti' 
								AND crmid = ".$id_destinazione." AND notesid = ".$notesid;
		$res_verifica_se_gia_coll = $adb->query($q_verifica_se_gia_coll);
		if($adb->num_rows($res_verifica_se_gia_coll)==0){
		
			$ins_doc = "INSERT INTO {$table_prefix}_senotesrel (crmid, notesid, relmodule)
							VALUES (".$id_destinazione.", ".$notesid.", 'KpSollecitiPagamenti')";
			$adb->query($ins_doc);
			
		}
		
	}
	
}

?>
