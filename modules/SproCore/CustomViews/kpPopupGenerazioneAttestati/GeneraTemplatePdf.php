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
require_once("modules/PDFMaker/InventoryPDF.php");
require_once("include/mpdf/mpdf.php"); 
require_once('modules/Emails/mail.php');
require_once("modules/Emails/class.phpmailer.php");
require_once('modules/SproCore/SproUtils/spro_utils.php');

$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $default_charset, $site_URL;
$db="adb";
$vcv="vtiger_current_version";
$salt="site_URL";
session_start();

if(!isset($_SESSION['authenticated_user_id'])){
    header("Location: ".$site_URL."/index.php?module=Accounts&action=index");
}
$current_user->id = $_SESSION['authenticated_user_id'];

$rows = array();

if(isset($_GET['templates'])){
    $templates = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['templates']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    if($templates == null || $templates == ""){
        $templates = 0;
    }
    else{
        $templates = explode(',', $templates);
    }
}
else{
    $templates = 0;
}

if(isset($_GET['record']) && $templates != 0){
	$formazione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['record']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$formazione = substr($formazione,0,100);
	
	if(isset($_GET['invia_mail'])){
		$invia_mail = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['invia_mail']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
		$invia_mail = substr($invia_mail,0,100);
		if($invia_mail == 'true'){
			$invia_mail = 'true';
		}
		else{
			$invia_mail = 'false';
		}
	}
	else{
		$invia_mail = 'false';
	}
	
	foreach($templates as $template){
		
		$q_risorse = "SELECT 
						part.kppartecipformazid kppartecipformazid
						from {$table_prefix}_kppartecipformaz part
						inner join {$table_prefix}_crmentity ent on ent.crmid = part.kppartecipformazid
						where ent.deleted = 0 and (part.kp_validato_da = '' OR part.kp_validato_da IS NULL) 
						and part.kp_stato_partecip IN ('Eseguita', 'Eseguita parzialmente') and part.kp_formazione = ".$formazione;
		
		$res_risorse = $adb->query($q_risorse);
		$num_risorse = $adb->num_rows($res_risorse);
		
		for($j = 0; $j < $num_risorse; $j++){
			
			$partecipazione = $adb->query_result($res_risorse, $j, 'kppartecipformazid');
			$partecipazione = html_entity_decode(strip_tags($partecipazione), ENT_QUOTES,$default_charset);
			
			creaTemplatePdfKpro($template, $partecipazione, $invia_mail);
				
		}
	
	}
	
	$rows[] = array('result' => 'ok');
	
}

$json = json_encode($rows);
print $json;

function creaTemplatePdfKpro($template, $partecipante, $invia_mail){
	global $adb, $table_prefix, $current_user, $default_charset, $site_URL;

	require_once('modules/SproCore/SproUtils/spro_utils.php');
	
	$templateid = $template;
	$relmodule = 'KpPartecipFormaz';
	$language = 'it_it';
	$kprecord = 0;
	$titolo_documento = "";
	$description = "";

	$id_statici = getConfigurazioniIdStatici();
    $id_statico = $id_statici["Documenti - Cartella Attestati"];
    if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
        $cartella_documenti = 1;
    }
    else{
        $cartella_documenti = $id_statico["valore"];
    }
	
	$utente = $current_user->id;
	if($utente == null || $utente == "" || $utente == 0){
		$utente = 1;
	}
	
	$q_utente = "SELECT last_name,
				first_name,
				email1
				FROM {$table_prefix}_users 
				WHERE id = ".$utente;

    $res_utente = $adb->query($q_utente);
    if($adb->num_rows($res_utente)>0){
        $cognome_utente = $adb->query_result($res_utente,0,'last_name');
        $cognome_utente = html_entity_decode(strip_tags($cognome_utente), ENT_QUOTES,$default_charset);
        
        $nome_utente = $adb->query_result($res_utente,0,'first_name');
        $nome_utente = html_entity_decode(strip_tags($nome_utente), ENT_QUOTES,$default_charset);
        
        $mail_utente = $adb->query_result($res_utente,0,'email1');
        $mail_utente = html_entity_decode(strip_tags($mail_utente), ENT_QUOTES,$default_charset);
    }
	
	$q_risorse = "SELECT 
					part.kppartecipformazid kppartecipformazid,
					part.kp_risorsa risorsa,
					cont.lastname cognome,
					cont.firstname nome,
					part.kp_azienda azienda,
					part.kp_formazione formazione,
					form.kp_nome_corso titolo_corso,
					acc.kp_email_invio_doc email_azienda
					from {$table_prefix}_kppartecipformaz part
					inner join {$table_prefix}_contactdetails cont on cont.contactid = part.kp_risorsa
					inner join {$table_prefix}_kpformazione form on form.kpformazioneid = part.kp_formazione
					left join {$table_prefix}_account acc on acc.accountid = part.kp_azienda
					where part.kppartecipformazid = ".$partecipante;
		
	$res_risorse = $adb->query($q_risorse);
	$num_risorse = $adb->num_rows($res_risorse);
	
	if($num_risorse > 0){
		
		$risorsa = $adb->query_result($res_risorse, 0, 'risorsa');
		$risorsa = html_entity_decode(strip_tags($risorsa), ENT_QUOTES,$default_charset);
		
		$cognome = $adb->query_result($res_risorse, 0, 'cognome');
		$cognome = html_entity_decode(strip_tags($cognome), ENT_QUOTES,$default_charset);
		
		$nome = $adb->query_result($res_risorse, 0, 'nome');
		$nome = html_entity_decode(strip_tags($nome), ENT_QUOTES,$default_charset);
		
		$titolo_corso = $adb->query_result($res_risorse, 0, 'titolo_corso');
		$titolo_corso = html_entity_decode(strip_tags($titolo_corso), ENT_QUOTES,$default_charset);
		
		$email_azienda = $adb->query_result($res_risorse, 0, 'email_azienda');
		$email_azienda = html_entity_decode(strip_tags($email_azienda), ENT_QUOTES,$default_charset);
		if($email_azienda == null){
			$email_azienda = "";
		}
		
		$azienda = $adb->query_result($res_risorse, 0, 'azienda');
		$azienda = html_entity_decode(strip_tags($azienda), ENT_QUOTES,$default_charset);
		if($azienda == null || $azienda == ""){
			$azienda = 0;
		}
		
		$formazione = $adb->query_result($res_risorse, 0, 'formazione');
		$formazione = html_entity_decode(strip_tags($formazione), ENT_QUOTES,$default_charset);
		if($formazione == null || $formazione == ""){
			$formazione = 0;
		}
		
		$kprecord = $partecipante;
		$titolo_documento = $partecipante." - Attestato ".$titolo_corso." ".$cognome." ".$nome;
		$description = "Attestato ".$titolo_corso." ".$cognome." ".$nome;
	
		$q_ver_documento = "SELECT notes.notesid notesid,
							notes.kp_num_doc_spec numero_attestato 
							FROM {$table_prefix}_notes notes 
							INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = notes.notesid
							INNER JOIN {$table_prefix}_senotesrel rel ON rel.notesid = notes.notesid
							WHERE ent.deleted = 0 AND rel.crmid = ".$kprecord." AND notes.title LIKE '".$partecipante." - Attestato%'";
	    $res_ver_documento = $adb->query($q_ver_documento);
	    if($adb->num_rows($res_ver_documento)>0){
			$document_id = $adb->query_result($res_ver_documento,0,'notesid');

			$numero_attestato = $adb->query_result($res_ver_documento,0,'numero_attestato');

	        $file_name = "doc_".$document_id.date("ymdHi").".pdf";
	
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
	            unlink($root_directory.$vecchio_path.$vecchio_file_name);
	
	            $delete_old = "DELETE FROM {$table_prefix}_seattachmentsrel 
	                            WHERE crmid = ".$document_id." AND attachmentsid =".$vecchio_attachmentsid;
	            $adb->query($delete_old);
	
	        }
	
	    }
	    else{
	        $document = CRMEntity::getInstance('Documents'); 
	        $document->parentid = $kprecord;
	
	        $file_name = "doc_".$document->parentid.date("ymdHi").".pdf";
	
	        $document->column_fields["notes_title"] = $titolo_documento;
	        $document->column_fields["assigned_user_id"] = $utente;
	        $document->column_fields["filename"] = $file_name;
	        $document->column_fields["notecontent"] = $description; 
	        $document->column_fields["filetype"] = "application/pdf"; 
	        $document->column_fields["filesize"] = ""; 
	        $document->column_fields["filelocationtype"] = "I"; 
	        $document->column_fields["fileversion"] = '';
	        $document->column_fields["filestatus"] = "on";
			$document->column_fields["folderid"] = $cartella_documenti;
			$document->column_fields["stato_documento"] = '';
			$document->column_fields["kp_data_documento"] = date('Y-m-d');
			$document->column_fields["data_scadenza"] = '2999-12-31';
			$document->column_fields["kp_stato_avanzament"] = 'Inserito documento';
	
	        $document->save("Documents", $longdesc=true, $offline_update=false, $triggerEvent=false);
			$document_id = $document->id;
			
			$numero_attestato = recuperaNumeroAttestato($document_id);
	    }
		
		$date_var = date("Y-m-d H:i:s");

	    $ownerid = $document->column_fields["assigned_user_id"];
	    if(!isset($ownerid) || $ownerid==""){
	        $ownerid = $utente;
	    }
	
	    $current_id = $adb->getUniqueID($table_prefix."_crmentity");
	
	    $focus = CRMEntity::getInstance($relmodule);
	    $focus->retrieve_entity_info($kprecord,$relmodule);
	    $focus->id = $kprecord;
	
	    $PDFContents = array();
	    $TemplateContent = array();
	
	    $PDFContent = PDFContent::getInstance($templateid, $relmodule, $focus, $language); 
	    $pdf_content = $PDFContent->getContent();    
	
	    $header_html = $pdf_content["header"];
	    $body_html = $pdf_content["body"];
		$footer_html = $pdf_content["footer"];

		if($numero_attestato != "" && $numero_attestato != null){		
			$body_html = str_replace("#numero_documento_specifico#",$numero_attestato,$body_html);
		}
	
	    $Settings = $PDFContent->getSettings();
	    if($name==""){    
	        $name = $PDFContent->getFilename();
	    }
	
	    if($Settings["orientation"] == "landscape"){
	        $format = $Settings["format"]."-L";
	    }
	    else{
	        $format = $Settings["format"];
	    }
	
	    $ListViewBlocks = array();
	    if(strpos($body_html,"#LISTVIEWBLOCK_START#") !== false && strpos($body_html,"#LISTVIEWBLOCK_END#") !== false){
	        preg_match_all("|#LISTVIEWBLOCK_START#(.*)#LISTVIEWBLOCK_END#|sU", $body_html, $ListViewBlocks, PREG_PATTERN_ORDER);
	    }		
	
	    if (count($ListViewBlocks) > 0){
						
	        $TemplateContent[$templateid] = $pdf_content;
	        $TemplateSettings[$templateid] = $Settings;
	
	        $num_listview_blocks = count($ListViewBlocks[0]);
	        for($i=0; $i<$num_listview_blocks; $i++){
	            $ListViewBlock[$templateid][$i] = $ListViewBlocks[0][$i];
	            $ListViewBlockContent[$templateid][$i][$kprecord][] = $ListViewBlocks[1][$i];
	        }   
	    }
	    else{
	        if (!isset($mpdf)){           
	            $mpdf=new mPDF('',$format,'','Arial',$Settings["margin_left"],$Settings["margin_right"],0,0,$Settings["margin_top"],$Settings["margin_bottom"]);  
	            $mpdf->SetAutoFont();
	            @$mpdf->SetHTMLHeader($header_html);
	        }
	        else{
	            @$mpdf->SetHTMLHeader($header_html);
	            @$mpdf->WriteHTML('<pagebreak sheet-size="'.$format.'" margin-left="'.$Settings["margin_left"].'mm" margin-right="'.$Settings["margin_right"].'mm" margin-top="0mm" margin-bottom="0mm" margin-header="'.$Settings["margin_top"].'mm" margin-footer="'.$Settings["margin_bottom"].'mm" />');
	        }     
	        @$mpdf->SetHTMLFooter($footer_html);
	        @$mpdf->WriteHTML($body_html);
	    }
	
	    if(count($TemplateContent)> 0){
	
	        foreach($TemplateContent AS $templateid => $TContent){
	            $header_html = $TContent["header"];
	            $body_html = $TContent["body"];
	            $footer_html = $TContent["footer"];
	
	            $Settings = $TemplateSettings[$templateid];
	
	            foreach($ListViewBlock[$templateid] AS $id => $text){
	                $replace = "";
	                foreach($Records as $record){  
	                    $replace .= implode("",$ListViewBlockContent[$templateid][$id][$record]);   
	                }
	
	                $body_html = str_replace($text,$replace,$body_html);
	            }
	
	            if ($Settings["orientation"] == "landscape"){
	                $format = $Settings["format"]."-L";
	            }
	            else{
	                $format = $Settings["format"];
	            }
	
	
	            if (!isset($mpdf)){           
	                $mpdf=new mPDF('',$format,'','Arial',$Settings["margin_left"],$Settings["margin_right"],0,0,$Settings["margin_top"],$Settings["margin_bottom"]);  
	                $mpdf->SetAutoFont();
	                @$mpdf->SetHTMLHeader($header_html);
	            }
	            else{
	                @$mpdf->SetHTMLHeader($header_html);
	                @$mpdf->WriteHTML('<pagebreak sheet-size="'.$format.'" margin-left="'.$Settings["margin_left"].'mm" margin-right="'.$Settings["margin_right"].'mm" margin-top="0mm" margin-bottom="0mm" margin-header="'.$Settings["margin_top"].'mm" margin-footer="'.$Settings["margin_bottom"].'mm" />');
	            }     
	            @$mpdf->SetHTMLFooter($footer_html);
	            @$mpdf->WriteHTML($body_html);
	        }
	    }
	
	    /*Questa parte servirebbe se volessi salvare il documento sul mio computer
	    $mpdf->Output('cache/'.$name.'.pdf');
	
	    @ob_clean();
	    header('Content-Type: application/pdf');
	    header("Content-length: ".filesize("./cache/$name.pdf"));
	    header("Cache-Control: private");
	    header("Content-Disposition: attachment; filename=$name.pdf");
	    header("Content-Description: PHP Generated Data");
	    echo fread(fopen("./cache/$name.pdf", "r"),filesize("./cache/$name.pdf"));
	
	    @unlink("cache/$name.pdf");*/
	
	    $upload_file_path = decideFilePath();
	
	    if($name!=""){
	        $file_name = $name.".pdf";
	    }
	
	    $mpdf->Output($upload_file_path.$current_id."_".$file_name);
	
	    $filesize = filesize($upload_file_path.$current_id."_".$file_name);
	    $filetype = "application/pdf";
	
	    $sql1 = "insert into ".$table_prefix."_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?, ?, ?, ?, ?, ?, ?)";
	    $params1 = array($current_id, $utente, $ownerid, "Documents Attachment", $description, $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
	
	    $adb->pquery($sql1, $params1);
	
	    $sql2="insert into ".$table_prefix."_attachments(attachmentsid, name, description, type, path) values(?, ?, ?, ?, ?)";
	    $params2 = array($current_id, $file_name, $description, $filetype, $upload_file_path);
	    $result=$adb->pquery($sql2, $params2);
	
	    $sql3='insert into '.$table_prefix.'_seattachmentsrel values(?,?)';
	    $adb->pquery($sql3, array($document_id, $current_id));
	
	    $sql4="UPDATE ".$table_prefix."_notes SET filesize=?, filename=?, kp_data_documento=? WHERE notesid=?";
        $adb->pquery($sql4,array($filesize,$file_name,date('Y-m-d'),$document_id));
	
	    $result = $upload_file_path.$current_id."_".$file_name;
	    
	    $ins_risorsa = "INSERT INTO {$table_prefix}_senotesrel (crmid, notesid, relmodule) VALUES
						(".$risorsa.", ".$document_id.", 'Contacts')";
		$adb->query($ins_risorsa);
		
		/*if($formazione != 0){
			$ins_formazione = "INSERT INTO {$table_prefix}_senotesrel (crmid, notesid, relmodule) VALUES
								(".$formazione.", ".$document_id.", 'Formazione')";
			$adb->query($ins_formazione);
		}*/
		
		if($azienda != 0){
			$ins_azienda = "INSERT INTO {$table_prefix}_senotesrel (crmid, notesid, relmodule) VALUES
							(".$azienda.", ".$document_id.", 'Accounts')";
			$adb->query($ins_azienda);	

			calcolaAziendeRelazionateAlDocumento($document_id); /* kpro@bid070920181520 */
		}
		
		if($email_azienda != null && $invia_mail == 'true'){
			$modulo = "KpPartecipFormaz";
			$nome_mittente = $cognome_utente." ".$nome_utente;
			$indirizzo_mittente = $mail_utente;
			$allegati = GetLinkAllegato($document_id);
			$cc = '';
			$bcc = '';
			$soggetto_mail = $description;
			$corpo_mail = $description;

			$mail = send_mail_kp($modulo,$email_azienda,$nome_mittente,$indirizzo_mittente,$soggetto_mail,$corpo_mail,$cc,$bcc,$allegati);
		}
	
	}

}

function GetLinkAllegato($document_id){
	global $adb, $table_prefix;

	$link_allegato = "";

	$q_allegato = "SELECT 
					att.attachmentsid attachmentsid,
					att.name filename,
					att.path cartella
					FROM {$table_prefix}_seattachmentsrel attrel
					INNER JOIN {$table_prefix}_attachments att ON att.attachmentsid = attrel.attachmentsid
					WHERE attrel.crmid = ".$document_id;
	$res_allegato = $adb->query($q_allegato);
	if($adb->num_rows($res_allegato)>0){
		$attachmentsid = $adb->query_result($res_allegato,0,'attachmentsid');
		$filename = $adb->query_result($res_allegato,0,'filename');
		$cartella = $adb->query_result($res_allegato,0,'cartella');
		
		$link_allegato = $cartella.$attachmentsid."_".$filename;
	}

	return $link_allegato;
}

function recuperaNumeroAttestato($documento){
    global $adb, $table_prefix,$current_user;
    
    /**
     * @author Bidese Jacopo
     * @copyright (c) 2018, Kpro Consulting Srl
     * 
     * Questo script recupera il numero dell'attestato
     */

	$doc_number = "";
            
	$q_numeratore = "SELECT num.use_prefix, 
					num.start_sequence, 
					num.modulenumberingid
					FROM {$table_prefix}_modulenumbering num
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = num.modulenumberingid
					WHERE ent.deleted = 0 AND num.select_module = '8A'";

	$res_numeratore = $adb->query($q_numeratore);
	if($adb->num_rows($res_numeratore)>0){
		$use_prefix = $adb->query_result($res_numeratore, 0, 'use_prefix'); 
		$use_prefix = html_entity_decode(strip_tags($use_prefix), ENT_QUOTES,$default_charset);

		$start_sequence = $adb->query_result($res_numeratore, 0, 'start_sequence'); 
		$start_sequence = html_entity_decode(strip_tags($start_sequence), ENT_QUOTES,$default_charset);
		
		$modulenumberingid = $adb->query_result($res_numeratore, 0, 'modulenumberingid'); 
		$modulenumberingid = html_entity_decode(strip_tags($modulenumberingid), ENT_QUOTES,$default_charset);
		
		$doc_number = $use_prefix.$start_sequence;
					
		$upd_doc = "UPDATE {$table_prefix}_notes
					SET kp_num_doc_spec = '".$doc_number."'
					WHERE notesid =".$documento;
		$adb->query($upd_doc);
		
		$length_sequence = strlen($start_sequence);			
		$start_sequence = (int)$start_sequence;

		$start_sequence++;
		$start_sequence = str_pad($start_sequence, $length_sequence, "0", STR_PAD_LEFT);
		
		$upd_numeratore = "UPDATE {$table_prefix}_modulenumbering
							SET start_sequence ='".$start_sequence."'
							WHERE modulenumberingid =".$modulenumberingid;
		$adb->query($upd_numeratore);
						
	}

	return $doc_number;
 
}
  
?>
