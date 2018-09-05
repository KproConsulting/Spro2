<?php

/* kpro@bid17102016 */

/**
 * @author Bidese Jacopo
 * @copyright (c) 2016, Kpro Consulting Srl
 * @package InvioMassivoFatture
 * @version 1.0
 */

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
require_once("modules/PDFMaker/InventoryPDF.php");
require_once("include/mpdf/mpdf.php"); //crmv@30066
include_once('modules/Emails/mail.php');
include_once('modules/Emails/class.phpmailer.php');
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

function PulisciEmailDestinatari($emails){
    $result = array();
    
    $array_delimiters = array(',',';');
    $array_email = explode($array_delimiters[0], str_replace($array_delimiters, $array_delimiters[0], $emails));    
    foreach($array_email as $email){        
        $email = trim($email);
        if($email != '' && $email != null && filter_var($email, FILTER_VALIDATE_EMAIL)){             
            $result[] = $email;
        }        
    }
    
    return $result;
}

function GetTemplatePDF($business_unit, $tipo_documento){
    global $adb, $table_prefix, $default_charset;

    $template_pdf = 0;

    if($tipo_documento == 'Fattura'){
        $nome_campo_template = 'kp_template_fattura';
    }
    else{
        $nome_campo_template = 'kp_template_note_cr';
    }

    $q = "SELECT pdf.templateid
        FROM {$table_prefix}_kpbusinessunit bu
        INNER JOIN {$table_prefix}_pdfmaker pdf ON pdf.templateid = bu.{$nome_campo_template}
        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = bu.kpbusinessunitid
        WHERE ent.deleted = 0 AND pdf.module = 'Invoice'
        AND bu.kpbusinessunitid = ".$business_unit;
    $res = $adb->query($q);
    if($adb->num_rows($res) > 0){
        $template_pdf = $adb->query_result($res, 0, 'templateid');
        $template_pdf = html_entity_decode(strip_tags($template_pdf), ENT_QUOTES, $default_charset);
        if($template_pdf == '' || $template_pdf == null){
            $template_pdf = 0;
        }
    }

    return $template_pdf;
}

function GeneraPDF($record, $templateid, $relmodule, $documento_temporaneo, $cartella_documenti, $titolo_documento){
    global $adb, $table_prefix, $current_user, $default_charset, $site_URL, $root_directory;
    
    $result = array();
    
    $language = 'it_it';
    $name = "";

    if($cartella_documenti == 0){
        $cartella_documenti = 1;
    }

    if(!$documento_temporaneo){ 
        $utente = $current_user->id;
        $upload_file_path = decideFilePath();
    
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
            $document->column_fields["notecontent"] = ""; 
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
        }
    
        $date_var = date("Y-m-d H:i:s");
        //to get the owner id
        $ownerid = $document->column_fields["assigned_user_id"];
        if(!isset($ownerid) || $ownerid==""){
            $ownerid = $utente;
        }
    
        $current_id = $adb->getUniqueID($table_prefix."_crmentity");
    }
    else{
        $upload_file_path = dirname(__FILE__)."/temp_pdf/";
        $name = $titolo_documento;
    }
    
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

    if($name!=""){
        $file_name = $name.".pdf";
    }

    if(!$documento_temporaneo){
        $mpdf->Output($upload_file_path.$current_id."_".$file_name);
        
        $filesize = filesize($upload_file_path.$current_id."_".$file_name);
        $filetype = "application/pdf";
    
        $sql1 = "insert into ".$table_prefix."_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?, ?, ?, ?, ?, ?, ?)";
        $params1 = array($current_id, $utente, $ownerid, "Documents Attachment", "", $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
    
        $adb->pquery($sql1, $params1);
    
        $sql2="insert into ".$table_prefix."_attachments(attachmentsid, name, description, type, path) values(?, ?, ?, ?, ?)";
        $params2 = array($current_id, $file_name, "", $filetype, $upload_file_path);
        $result=$adb->pquery($sql2, $params2); 
    
        $sql3='insert into '.$table_prefix.'_seattachmentsrel values(?,?)';
        $adb->pquery($sql3, array($document_id, $current_id));
    
        $sql4="UPDATE ".$table_prefix."_notes SET filesize=?, filename=?, kp_data_documento=? WHERE notesid=?";
        $adb->pquery($sql4,array($filesize,$file_name,date('Y-m-d'),$document_id));

        $upload_file_path = $root_directory.$upload_file_path;
        $file_name = $current_id."_".$file_name;
    }
    else{
        $mpdf->Output($upload_file_path.$file_name);
    }

    $path_pdf_completo = $upload_file_path.$file_name;

    $result = array(
        "pdf" => $path_pdf_completo,
        "path" => $upload_file_path,
        "file_name" => $file_name
    );

    return $result;	
}

function GetPDFTickets($record, $template_pdf){
    global $adb, $table_prefix, $default_charset;

    $allegati = "";

    if($template_pdf != 0){
    
        $q = "SELECT tick.ticketid,
            tick.ticket_no
            FROM {$table_prefix}_troubletickets tick
            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
            WHERE ent.deleted = 0
            AND tick.ticket_no IN (
                SELECT odf.rif_related_to
                FROM {$table_prefix}_odf odf
                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = odf.odfid
                WHERE ent.deleted = 0
                AND odf.tipo_odf = 'Ticket'
                AND odf.fattura = {$record}
            )";
        $res = $adb->query($q);
        $num = $adb->num_rows($res);
        if($num > 0){
            for($i = 0; $i < $num; $i++){
                $ticketid = $adb->query_result($res, $i, 'ticketid');

                $ticket_no = $adb->query_result($res, $i, 'ticket_no');
                $ticket_no = html_entity_decode(strip_tags($ticket_no), ENT_QUOTES, $default_charset);

                $dati_pdf = GeneraPDF($ticketid, $template_pdf, 'HelpDesk', true, "", "Ticket_nr_".$ticket_no);

                if($allegati == ""){
                    $allegati .= $dati_pdf["pdf"];
                }
                else{
                    $allegati .= ",".$dati_pdf["pdf"];
                }
            }
        }
    }

    return $allegati;
}

function GetPDFReportVisita($record, $template_pdf){
    global $adb, $table_prefix, $default_charset;

    $allegati = "";

    if($template_pdf != 0){

        $q = "SELECT rep.visitreportid,
            rep.visitreport_no
            FROM {$table_prefix}_visitreport rep
            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rep.visitreportid
            WHERE ent.deleted = 0
            AND rep.visitreport_no IN (
                SELECT odf.rif_related_to
                FROM {$table_prefix}_odf odf
                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = odf.odfid
                WHERE ent.deleted = 0
                AND odf.tipo_odf = 'Report Attivita'
                AND odf.fattura = {$record}
            )";
        $res = $adb->query($q);
        $num = $adb->num_rows($res);
        if($num > 0){
            for($i = 0; $i < $num; $i++){
                $visitreportid = $adb->query_result($res, $i, 'visitreportid');

                $visitreport_no = $adb->query_result($res, $i, 'visitreport_no');
                $visitreport_no = html_entity_decode(strip_tags($visitreport_no), ENT_QUOTES, $default_charset);

                $dati_pdf = GeneraPDF($visitreportid, $template_pdf, 'Visitreport', true, "", "Report_Attivita_nr_".$visitreport_no);

                if($allegati == ""){
                    $allegati .= $dati_pdf["pdf"];
                }
                else{
                    $allegati .= ",".$dati_pdf["pdf"];
                }
            }
        }
    }

    return $allegati;
}

function InvioMail($record, $templatemailid, $dati_record, $allegati){
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    require_once("modules/SproCore/SproUtils/spro_utils.php");

    $res_invio_mail = false;

    $id_statici = getConfigurazioniIdStatici();
    $id_statico = $id_statici["Programmi Custom - Invio Massivo Fatture - Utente invio mail"];
    if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
        $utente_invio_mail = 1;
    }
    else{
        $utente_invio_mail = $id_statico["valore"];
    }

    $q_utente = "SELECT last_name,
                        first_name,
                        email1
                        FROM {$table_prefix}_users 
                        WHERE id = ".$utente_invio_mail;
    $res_utente = $adb->query($q_utente);

    $cognome_utente = $adb->query_result($res_utente, 0, 'last_name');
    $cognome_utente = html_entity_decode(strip_tags($cognome_utente), ENT_QUOTES, $default_charset);

    $nome_utente = $adb->query_result($res_utente, 0, 'first_name');
    $nome_utente = html_entity_decode(strip_tags($nome_utente), ENT_QUOTES, $default_charset);

    $mail_utente = $adb->query_result($res_utente, 0, 'email1');
    $mail_utente = html_entity_decode(strip_tags($mail_utente), ENT_QUOTES, $default_charset);

    $res_mail = ComponiEmail($templatemailid, $cognome_utente, $nome_utente, $mail_utente, $dati_record["email_azienda"], $dati_record["numero_fattura"], $dati_record["nome_azienda"], $dati_record["tipo_documento"], $allegati); /* kpro@bid050920181500 */

    if($res_mail){
        $res_invio_mail = true;
    }

    return $res_invio_mail;
}

function ComponiEmail($templatemailid, $cognome_utente, $nome_utente, $mail_utente, $email_azienda, $numero_fattura, $nome_azienda, $tipo_documento, $allegati) { /* kpro@bid050920181500 */
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    require_once("modules/SproCore/SproUtils/spro_utils.php");

    $q_select_template = "SELECT subject,body
        FROM {$table_prefix}_emailtemplates
        WHERE deleted = 0 AND templateid = ".$templatemailid;
    $res_select_template = $adb->query($q_select_template);
    if($adb->num_rows($res_select_template) > 0){
        $soggetto_mail = decode_html($adb->query_result($res_select_template, 0, 'subject'));

        $soggetto_mail = str_replace('_numero_fattura_',$numero_fattura,$soggetto_mail);
        $soggetto_mail = str_replace('$Invoice||invoice_number$',$numero_fattura,$soggetto_mail); /* kpro@bid050920181500 */

        $soggetto_mail = str_replace('_tipo_documento_',$tipo_documento,$soggetto_mail); /* kpro@bid050920181500 */
        $soggetto_mail = str_replace('$Invoice||kp_tipo_documento$',$tipo_documento,$soggetto_mail); /* kpro@bid050920181500 */

        $corpo_mail = decode_html($adb->query_result($res_select_template, 0, 'body'));

        $corpo_mail = str_replace('_nome_azienda_',$nome_azienda,$corpo_mail);
        $corpo_mail = str_replace('$Invoice|Accounts#account_id|accountname$',$nome_azienda,$corpo_mail); /* kpro@bid050920181500 */

        $corpo_mail = str_replace('_numero_fattura_',$numero_fattura,$corpo_mail);
        $corpo_mail = str_replace('$Invoice||invoice_number$',$numero_fattura,$corpo_mail); /* kpro@bid050920181500 */

        $corpo_mail = str_replace('_tipo_documento_',$tipo_documento,$corpo_mail); /* kpro@bid050920181500 */
        $corpo_mail = str_replace('$Invoice||kp_tipo_documento$',$tipo_documento,$corpo_mail); /* kpro@bid050920181500 */

        $nome_mittente = $cognome_utente . " " . $nome_utente;
        $cc = '';
        $bcc = '';

        $mail = send_mail_kp("", $email_azienda, $nome_mittente, $mail_utente, $soggetto_mail, $corpo_mail, $cc, $bcc, $allegati);

        if ($mail == 1) {
            return true;
        } else {
            return false;
        }
    }
    else{
        return false;
    }
}
	
?>
