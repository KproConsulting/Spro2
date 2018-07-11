<?php

/* kpro@tom04072017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 * @package gestioneAttestati
 * @version 1.0
 */

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

if(!isset($_SESSION['authenticated_user_id'])){
    header("Location: ".$site_URL."/index.php");
}
else{
	$current_user->id = $_SESSION['authenticated_user_id'];
}

require_once('FirmaGrafometrica_utils.php');

$utente = $current_user->id;
if($utente == 0 || $utente == ""){
	$utente = 1;
}

if(isset($_GET['record'])){
	$record = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['record']), ENT_QUOTES,$default_charset)), ENT_QUOTES, $default_charset);
	$record = substr($record, 0, 100);
	if($record == ""){
		$record = 0;
	}
}
else{
	$record = 0;
}

$target_dir = "modules/SproCore/CustomPortals/KpPortaleConsegnaDPI/signed/";
$target_file = $target_dir.basename($_FILES["firmacertafile"]["name"]);
$target_file_name = basename($_FILES["firmacertafile"]["name"]);
$target_file_type = $_FILES["firmacertafile"]["type"]; 
$target_file_size = $_FILES["firmacertafile"]["size"];
$uploadOk = 1;
$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);

// Check if file already exists
if (file_exists($target_file)) {
	@unlink($target_file);
    $uploadOk = 1;
}

// Check file size
if ($_FILES["fileToUpload"]["size"] > 500000) {
    echo "Sorry, your file is too large.";
    $uploadOk = 0;
}

// Allow certain file formats
if($imageFileType != "pdf") {
    echo "Sorry, only PDF files are allowed.";
    $uploadOk = 0;
}

// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
} else {
    if (move_uploaded_file($_FILES["firmacertafile"]["tmp_name"], $target_file)) {
        echo "The file ".basename( $_FILES["firmacertafile"]["name"]). " has been uploaded.";
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}

if($record != 0 && $uploadOk != 0){

	creaDocumentoConFirmaGrafometrica($record, $target_dir, $target_file_name, $target_file_type, $target_file_size, $utente);

	cancellaPdfTemplateTemporaneo($record);

}

function creaDocumentoConFirmaGrafometrica($record, $target_dir, $file_name, $file_type, $file_size, $utente){
	global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

	require_once("modules/SproCore/SproUtils/spro_utils.php");

	$dati_record = informazioniRecord($record);

	$q_ver_documento = "SELECT notes.notesid notesid FROM {$table_prefix}_notes notes 
						INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = notes.notesid
						INNER JOIN {$table_prefix}_senotesrel rel ON rel.notesid = notes.notesid
						WHERE ent.deleted = 0 AND rel.crmid = ".$record." AND notes.filename LIKE '%".$file_name."%'";
	$res_ver_documento = $adb->query($q_ver_documento);
	if($adb->num_rows($res_ver_documento) > 0){
		$document_id = $adb->query_result($res_ver_documento, 0, 'notesid');
		$document_id = html_entity_decode(strip_tags($document_id), ENT_QUOTES,$default_charset);

		$q_vecchi_att = "SELECT att.attachmentsid attachmentsid,
							att.name name,
							att.path path
							FROM {$table_prefix}_seattachmentsrel serel
							INNER JOIN {$table_prefix}_attachments att ON att.attachmentsid = serel.attachmentsid
							WHERE serel.crmid = ".$document_id;
		$res_vecchi_att = $adb->query($q_vecchi_att);
		$num_vecchi_att = $adb->num_rows($res_vecchi_att);
		for($i=0; $i<$num_vecchi_att; $i++){
			$vecchio_attachmentsid = $adb->query_result($res_vecchi_att, $i, 'attachmentsid');
			$vecchio_name = $adb->query_result($res_vecchi_att, $i, 'name');
			$vecchio_path = $adb->query_result($res_vecchi_att, $i, 'path');
			
			$vecchio_file_name = $vecchio_attachmentsid."_".$vecchio_name;
			@unlink($root_directory.$vecchio_path.$vecchio_file_name);
			
			$delete_old = "DELETE FROM {$table_prefix}_seattachmentsrel 
							WHERE crmid = ".$document_id." AND attachmentsid =".$vecchio_attachmentsid;
			$adb->query($delete_old);
			
		}

	}
	else{
		$id_statici = getConfigurazioniIdStatici();
        $id_statico = $id_statici["Documenti - Cartella Consegna DPI"];
        if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
            $cartella_documenti = 1;
        }
        else{
            $cartella_documenti = $id_statico["valore"];
        }

		$document = CRMEntity::getInstance('Documents'); 
		$document->parentid = $record;
		
		$document->column_fields["notes_title"] = "Consegna DPI ".$dati_record['consegna_no'];
		$document->column_fields["assigned_user_id"] = $utente;
		$document->column_fields["filename"] = $file_name;
		$document->column_fields['filetype'] = $file_type;
		$document->column_fields['filesize'] = $file_size;
		$document->column_fields["filelocationtype"] = "I"; 
		$document->column_fields["filestatus"] = "on";
		$document->column_fields["folderid"] = $cartella_documenti;
		$document->column_fields["stato_documento"] = '';
        $document->column_fields["kp_data_documento"] = date('Y-m-d');
        $document->column_fields["data_scadenza"] = '2999-12-31';
        $document->column_fields["kp_stato_avanzament"] = 'Inserito documento';

		$document->save("Documents");
		$document_id = $document->id;

	}

	$new_entityid = $adb->getUniqueID($table_prefix."_crmentity");
	//$upload_file_path = decideFilePath();

	rename($target_dir."/".$file_name, $target_dir."/".$new_entityid."_".$file_name);

	$ins_attach = "INSERT INTO {$table_prefix}_attachments (attachmentsid, name, description, type, path) VALUES
					(".$new_entityid.", '".$file_name."', '', '".$file_type."', '".$target_dir."')";
	$adb->query($ins_attach);

	$upd_entity = "INSERT INTO {$table_prefix}_crmentity (crmid, smcreatorid, smownerid, setype) VALUES
					(".$new_entityid.", 1, 1, 'Documents Attachment')";
	$adb->query($upd_entity);

	$ins_attach_rel	="INSERT INTO {$table_prefix}_seattachmentsrel (crmid, attachmentsid) VALUES
						(".$document_id.", ".$new_entityid.")";
	$adb->query($ins_attach_rel);	
	
	$upd_doc = "UPDATE {$table_prefix}_notes SET 
				filename = '".$file_name."',
				filetype = ".$file_size."
				WHERE notesid =".$document_id;
	$adb->query($upd_doc);	

	confermaConsegnaDpi($record);

}

function informazioniRecord($record){
	global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

	$q_record = "SELECT dpi.consegnadpiid,
					dpi.consegna_no,cont.contactid,
					cont.lastname,cont.firstname,
					acc.accountid,acc.accountname,
					dpi.data_consegna,dpi.tipo_consegna,
					stab.stabilimentiid,stab.nome_stabilimento,
					dpi.description,dpi.stato_consegna
					FROM {$table_prefix}_consegnadpi dpi
					INNER JOIN {$table_prefix}_contactdetails cont ON cont.contactid = dpi.contatto
					INNER JOIN {$table_prefix}_account acc ON acc.accountid = dpi.azienda
					INNER JOIN {$table_prefix}_stabilimenti stab ON stab.stabilimentiid = dpi.stabilimento
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = dpi.consegnadpiid
					WHERE ent.deleted = 0 AND dpi.consegnadpiid = ".$record;

    $res_record = $adb->query($q_record);

    if($adb->num_rows($res_record) > 0){
		$consegnadpiid = $adb->query_result($res_record, 0, 'consegnadpiid');
		$consegnadpiid = html_entity_decode(strip_tags($consegnadpiid), ENT_QUOTES, $default_charset);

		$consegna_no = $adb->query_result($res_record, 0, 'consegna_no');
		$consegna_no = html_entity_decode(strip_tags($consegna_no), ENT_QUOTES, $default_charset);
		
		$contactid = $adb->query_result($res_record, 0, 'contactid');
		$contactid = html_entity_decode(strip_tags($contactid), ENT_QUOTES, $default_charset);
		
		$lastname = $adb->query_result($res_record, 0, 'lastname');
		$lastname = html_entity_decode(strip_tags($lastname), ENT_QUOTES, $default_charset);
		
		$firstname = $adb->query_result($res_record, 0, 'firstname');
		$firstname = html_entity_decode(strip_tags($firstname), ENT_QUOTES, $default_charset);
		
		$accountid = $adb->query_result($res_record, 0, 'accountid');
		$accountid = html_entity_decode(strip_tags($accountid), ENT_QUOTES, $default_charset);
		
		$accountname = $adb->query_result($res_record, 0, 'accountname');
		$accountname = html_entity_decode(strip_tags($accountname), ENT_QUOTES, $default_charset);
		
		$data_consegna = $adb->query_result($res_record, 0, 'data_consegna');
		$data_consegna = html_entity_decode(strip_tags($data_consegna), ENT_QUOTES, $default_charset);
		
		$tipo_consegna = $adb->query_result($res_record, 0, 'tipo_consegna');
		$tipo_consegna = html_entity_decode(strip_tags($tipo_consegna), ENT_QUOTES, $default_charset);
		
		$stabilimentiid = $adb->query_result($res_record, 0, 'stabilimentiid');
		$stabilimentiid = html_entity_decode(strip_tags($stabilimentiid), ENT_QUOTES, $default_charset);
		
		$nome_stabilimento = $adb->query_result($res_record, 0, 'nome_stabilimento');
		$nome_stabilimento = html_entity_decode(strip_tags($nome_stabilimento), ENT_QUOTES, $default_charset);
		
		$description = $adb->query_result($res_record, 0, 'description');
		$description = html_entity_decode(strip_tags($description), ENT_QUOTES, $default_charset);
		
		$stato_consegna = $adb->query_result($res_record, 0, 'stato_consegna');
		$stato_consegna = html_entity_decode(strip_tags($stato_consegna), ENT_QUOTES, $default_charset);
		if($stato_consegna == "" || $stato_consegna == null){
			$stato_consegna = "Non Confermata";
		}

	}
	else{
		$consegnadpiid = 0;
		$consegna_no = "";
	}

	$result = array('consegnadpiid' => $consegnadpiid,
					'consegna_no' => $consegna_no);
	
	return $result;

}

function confermaConsegnaDpi($record){
	global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

	$upd= "UPDATE {$table_prefix}_consegnadpi SET
								stato_consegna = 'Confermata'
								WHERE consegnadpiid = ".$record;
    $adb->query($upd);

}

?>
