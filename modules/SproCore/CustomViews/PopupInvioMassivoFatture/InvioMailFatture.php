<?php

/* kpro@bid17102016 */

/**
 * @author Bidese Jacopo
 * @copyright (c) 2016, Kpro Consulting Srl
 * @package InvioMassivoFatture
 * @version 1.0
 */

require_once('InvioMailFatture_utils.php');

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

$rows = array();
if(isset($_REQUEST['record'])){
    $record = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_REQUEST['record']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $record = substr($record,0,100);

    $allegati = "";

    $q_dati_invio_fattura = "SELECT acc.kp_email_invio_doc AS email_invio_documenti,
                        acc.kp_email_invio_doc2 AS email_invio_documenti_2,
                        inv.invoice_number,
                        inv.taxtype,
                        inv.invoicestatus,
                        inv.kp_avviso_fattura,
                        inv.kp_tipo_documento,
                        bu.kpbusinessunitid,
                        cont.lastname,
                        cont.firstname,
                        acc.accountname,
                        acc.kp_allegati_fatture AS invio_allegati_fatture
                        FROM {$table_prefix}_invoice inv
                        INNER JOIN {$table_prefix}_account acc ON acc.accountid = inv.accountid
                        INNER JOIN {$table_prefix}_accountscf acccf ON acccf.accountid = acc.accountid
                        LEFT JOIN {$table_prefix}_contactdetails cont ON cont.contactid = inv.contactid
                        INNER JOIN {$table_prefix}_kpbusinessunit bu ON bu.kpbusinessunitid = inv.kp_business_unit
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = inv.invoiceid
                        INNER JOIN {$table_prefix}_crmentity ent1 ON ent1.crmid = acc.accountid
                        WHERE ent.deleted = 0 AND ent1.deleted = 0
                        AND (inv.invoicestatus = 'Approved' 
                        OR (inv.kp_tipo_documento = 'Fattura' AND inv.invoicestatus = 'Approvata Proforma'))
                        AND inv.invoiceid = ".$record; 

    $res_dati_invio_fattura = $adb->query($q_dati_invio_fattura);
    if($adb->num_rows($res_dati_invio_fattura) > 0){

        $email_azienda = $adb->query_result($res_dati_invio_fattura, 0, 'email_invio_documenti');
        $email_azienda = html_entity_decode(strip_tags($email_azienda), ENT_QUOTES, $default_charset);
        $email_azienda = trim($email_azienda);
        if($email_azienda == null){
            $email_azienda = '';
        }

        $altre_email = $adb->query_result($res_dati_invio_fattura, 0, 'email_invio_documenti_2');
        $altre_email = html_entity_decode(strip_tags($altre_email), ENT_QUOTES, $default_charset);
        $altre_email = trim($altre_email);
        if($altre_email == null){
            $altre_email = '';
        }

        $business_unit = $adb->query_result($res_dati_invio_fattura, 0, 'kpbusinessunitid');
        $business_unit = html_entity_decode(strip_tags($business_unit), ENT_QUOTES, $default_charset);
        if($business_unit == '' || $business_unit == null){
            $business_unit = 0;
        }

        $tipo_documento = $adb->query_result($res_dati_invio_fattura, 0, 'kp_tipo_documento');
        $tipo_documento = html_entity_decode(strip_tags($tipo_documento), ENT_QUOTES, $default_charset);

        $email_destinatari = $email_azienda;
        if($altre_email != ''){
            $email_destinatari .= ','.$altre_email;
        }

        $email_destinatari = PulisciEmailDestinatari($email_destinatari);

        $template_pdf = GetTemplatePDF($business_unit, $tipo_documento);

        if(!empty($email_destinatari) && $template_pdf != 0){

            $cognome_contatto = $adb->query_result($res_dati_invio_fattura, 0, 'lastname');
            $cognome_contatto = html_entity_decode(strip_tags($cognome_contatto), ENT_QUOTES, $default_charset);
            if($cognome_contatto == null){
                $cognome_contatto = "";
            }
        
            $nome_contatto = $adb->query_result($res_dati_invio_fattura, 0, 'firstname');
            $nome_contatto = html_entity_decode(strip_tags($nome_contatto), ENT_QUOTES, $default_charset);
            if($nome_contatto == null){
                $nome_contatto = "";
            }
        
            $nome_azienda = $adb->query_result($res_dati_invio_fattura, 0, 'accountname');
            $nome_azienda = html_entity_decode(strip_tags($nome_azienda), ENT_QUOTES, $default_charset);

            $numero_fattura = $adb->query_result($res_dati_invio_fattura, 0, 'invoice_number');
            $numero_fattura = html_entity_decode(strip_tags($numero_fattura), ENT_QUOTES, $default_charset);

            $invio_allegati_fatture = $adb->query_result($res_dati_invio_fattura, 0, 'invio_allegati_fatture');
            $invio_allegati_fatture = html_entity_decode(strip_tags($invio_allegati_fatture), ENT_QUOTES, $default_charset);

            $taxtype = $adb->query_result($res_dati_invio_fattura, 0, 'taxtype');
            $taxtype = html_entity_decode(strip_tags($taxtype), ENT_QUOTES, $default_charset);

            $stato_fattura = $adb->query_result($res_dati_invio_fattura, 0, 'invoicestatus');
            $stato_fattura = html_entity_decode(strip_tags($stato_fattura), ENT_QUOTES, $default_charset);

            $avviso_fattura = $adb->query_result($res_dati_invio_fattura, 0, 'kp_avviso_fattura');
            $avviso_fattura = html_entity_decode(strip_tags($avviso_fattura), ENT_QUOTES, $default_charset);

            $dati_record = array(
                "nome_azienda" => $nome_azienda,
                "email_azienda" => $email_destinatari,
                "numero_fattura" => $numero_fattura,
                "cognome_contatto" => $cognome_contatto,
                "nome_contatto" => $nome_contatto,
                "tipo_documento" => $tipo_documento
            );
            /* kpro@bid050920181500 */

            $id_statici = getConfigurazioniIdStatici();

            if($tipo_documento == 'Fattura'){
                if($stato_fattura == 'Approvata Proforma'){
                    $id_statico = $id_statici["Documenti - Cartella Fatture Proforma"];
                    if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
                        $cartella_documenti = 0;
                    }
                    else{
                        $cartella_documenti = $id_statico["valore"];
                    }
                    $dati_pdf_fattura = GeneraPDF($record, $template_pdf, 'Invoice', false, $cartella_documenti, "Doc. AutoGenerato - PDF Proforma ".$record);
                }
                else{
                    $id_statico = $id_statici["Documenti - Cartella Fatture"];
                    if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
                        $cartella_documenti = 0;
                    }
                    else{
                        $cartella_documenti = $id_statico["valore"];
                    }
                    $dati_pdf_fattura = GeneraPDF($record, $template_pdf, 'Invoice', false, $cartella_documenti, "Doc. AutoGenerato - PDF Fattura ".$record);
                }
            }
            else{
                $id_statico = $id_statici["Documenti - Cartella Note di Credito"];
                if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
                    $cartella_documenti = 0;
                }
                else{
                    $cartella_documenti = $id_statico["valore"];
                }
                $dati_pdf_fattura = GeneraPDF($record, $template_pdf, 'Invoice', false, $cartella_documenti, "Doc. AutoGenerato - PDF Nota di Credito ".$record);
            }

            $allegati .= $dati_pdf_fattura["pdf"];

            if($invio_allegati_fatture == "Tutti" || $invio_allegati_fatture == "Tickets"){
                $id_statico = $id_statici["PDF Maker - Invio Massivo Fatture - Template per invio tickets"];
                if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
                    $template_pdf_tickets = 0;
                }
                else{
                    $template_pdf_tickets = $id_statico["valore"];
                }
                $pdf_ticket = GetPDFTickets($record, $template_pdf_tickets);
                if($pdf_ticket != ""){
                    $allegati .= ",".$pdf_ticket;
                }
            }

            if($invio_allegati_fatture == "Tutti" || $invio_allegati_fatture == "Report Attivita"){
                $id_statico = $id_statici["PDF Maker - Invio Massivo Fatture - Template per invio report attività"];
                if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
                    $template_pdf_report_attivita = 0;
                }
                else{
                    $template_pdf_report_attivita = $id_statico["valore"];
                }
                $pdf_report_visita = GetPDFReportVisita($record, $template_pdf_report_attivita);
                if($pdf_report_visita != ""){
                    $allegati .= ",".$pdf_report_visita;
                }
            }

            if($tipo_documento == 'Fattura'){
                if($stato_fattura == 'Approvata Proforma'){
                    $id_statico = $id_statici["Template Email - Invio Massivo Fatture - Invio Fatture Proforma"];
                    if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
                        $template_email = 0;
                    }
                    else{
                        $template_email = $id_statico["valore"];
                    }
                    $res_invio_mail = InvioMail($record, $template_email, $dati_record, $allegati);
                }
                else{
                    $id_statico = $id_statici["Template Email - Invio Massivo Fatture - Invio Fatture"];
                    if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
                        $template_email = 0;
                    }
                    else{
                        $template_email = $id_statico["valore"];
                    }
                    $res_invio_mail = InvioMail($record, $template_email, $dati_record, $allegati);
                }
            }
            else{
                $id_statico = $id_statici["Template Email - Invio Massivo Fatture - Invio Note di Credito"];
                if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
                    $template_email = 0;
                }
                else{
                    $template_email = $id_statico["valore"];
                }
                $res_invio_mail = InvioMail($record, $template_email, $dati_record, $allegati);
            }

            if($res_invio_mail){
                if($tipo_documento == 'Fattura'){
                    if($stato_fattura == 'Approvata Proforma'){
                        $q_update_stato_fattura = "UPDATE {$table_prefix}_invoice SET
                                                invoicestatus = 'Spedita Proforma'
                                                WHERE invoiceid = ".$record;
                        $adb->query($q_update_stato_fattura);
                    }
                    else{
                        if($avviso_fattura == '1'){
                            $q_update_stato_fattura = "UPDATE {$table_prefix}_invoice SET
                                                invoicestatus = 'Paid'
                                                WHERE invoiceid = ".$record;
                            $adb->query($q_update_stato_fattura);
                        }
                        else{
                            $q_update_stato_fattura = "UPDATE {$table_prefix}_invoice SET
                                                invoicestatus = 'Sent'
                                                WHERE invoiceid = ".$record;
                            $adb->query($q_update_stato_fattura);
                        }
                    }
                }
                else{
                    $q_update_stato_fattura = "UPDATE {$table_prefix}_invoice SET
                                            invoicestatus = 'Sent'
                                            WHERE invoiceid = ".$record;
                    $adb->query($q_update_stato_fattura);
                }

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
            $rows[] = array(
                "res"=>"error"
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
	
?>
