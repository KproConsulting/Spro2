<?php

/* kpro@tom190216 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 */
 
require_once(__DIR__.'/KproConfig.ini.php');

require_once(__DIR__.'/Firma_utils.php');

include_once(__DIR__.'/../../../../config.inc.php');
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

global $path_cartella_firme, $radice_nome_documento, $pdf_template_id, $pdf_template_relmodule, $cartella_crm_documento, $path_cartella_pdf_firmati;

session_start();

if(!isset($_SESSION['authenticated_user_id'])){
    header("Location: ".$site_URL."/index.php?module=Accounts&action=index");
}

$current_user->id = $_SESSION['authenticated_user_id'];

if(isset($_GET['record'])){
	$record = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['record']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$record = substr($record,0,100);
	
	if(isset($_GET['firmatario'])){
		$firmatario = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['firmatario']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
		$firmatario = substr($firmatario,0,255);
	}
	else{
		$firmatario = "";
	}
	
	$file_firma = $root_directory.$path_cartella_firme.$record."_jqScribbleImage.png";
    
    if(file_exists($file_firma)){ 
		//$file_link = $site_URL."/".$path_cartella_firme.$record."_jqScribbleImage.png";
		$file_link = $path_cartella_firme.$record."_jqScribbleImage.png";	/* kpro@tom090320171448 */
		$firma = "<img src='".$file_link."' style='max-width: 400px; float: left; max-height: 300px;'/>";
	}
	else{
		$firma = "";
	}
	
	$templateid = $pdf_template_id;
	$relmodule = $pdf_template_relmodule;
	$language = 'it_it';
	$record = $record;
	$titolo_documento = $radice_nome_documento.$record;
	$cartella_documenti = $cartella_crm_documento;
	$description = $radice_nome_documento.$record;

	$utente = $current_user->id;
    if($utente == null || $utente == "" || $utente == 0){
        $utente = 1;
    }
	
	$q_ver_documento = "SELECT notes.notesid notesid FROM {$table_prefix}_notes notes 
						INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = notes.notesid
						INNER JOIN {$table_prefix}_senotesrel rel ON rel.notesid = notes.notesid
						WHERE ent.deleted = 0 AND rel.crmid = ".$record." AND notes.title LIKE '%".$titolo_documento."%'";
	
	$res_ver_documento = $adb->query($q_ver_documento);
	if($adb->num_rows($res_ver_documento)>0){
		$document_id = $adb->query_result($res_ver_documento,0,'notesid');
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
		$document->parentid = $record;
		
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
		$document->column_fields["kp_data_documento"] = date('Y-m-d');
		$document->column_fields['kp_stato_avanzament'] = "Inserito documento";
		$document->column_fields["stato_documento"] = '';
		$document->column_fields['data_scadenza'] = '2999-12-31';

		$document->save("Documents", $longdesc=true, $offline_update=false, $triggerEvent=false);
		$document_id = $document->id;
	}
	
	$date_var = date("Y-m-d H:i:s");
	//to get the owner id
	$ownerid = $document->column_fields["assigned_user_id"];
	if(!isset($ownerid) || $ownerid==""){
		$ownerid = $utente;
	}
	
	$current_id = $adb->getUniqueID($table_prefix."_crmentity");
	
	$focus = CRMEntity::getInstance($relmodule);
	$focus->retrieve_entity_info($record,$relmodule);
	$focus->id = $record;

	$PDFContents = array();
	$TemplateContent = array();

	$PDFContent = PDFContent::getInstance($templateid, $relmodule, $focus, $language); 
	$pdf_content = $PDFContent->getContent();    

	$header_html = $pdf_content["header"];
	$body_html = $pdf_content["body"];
	$footer_html = $pdf_content["footer"];

	$body_html = str_replace("#firmatario#", $firmatario, $body_html);
    $body_html = str_replace("#firma#", $firma, $body_html);
	
	$Settings = $PDFContent->getSettings();
	if($name==""){    
		$name = $PDFContent->getFilename();
	}
				
	if ($Settings["orientation"] == "landscape"){
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
			$ListViewBlockContent[$templateid][$i][$record][] = $ListViewBlocks[1][$i];
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
			
	if (count($TemplateContent)> 0){
		
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
						
	//$upload_file_path = decideFilePath();
	$upload_file_path = $path_cartella_pdf_firmati;
	
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

	$sql4="UPDATE ".$table_prefix."_notes SET filesize=?, filename=? WHERE notesid=?";
	$adb->pquery($sql4,array($filesize,$file_name,$document_id));
	
	$result = $upload_file_path.$current_id."_".$file_name;
	
	$rows[] = array('documento' => $document_id);

	//cancellaPdfTemplateTemporaneo($record);
	
	$json = json_encode($rows);
	print $json;
	//echo $result;
	
}
	
?>
