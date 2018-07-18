<?php

/* kpro@tom17112016 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 */

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
require_once('modules/SproCore/SproUtils/spro_utils.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_language;
session_start();

$id_statici = getConfigurazioniIdStatici();

$id_statico = $id_statici["PDF Maker - Template Rapportino Intervento"];
if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
    $pdf_template_id = 0;
}
else{
    $pdf_template_id = $id_statico["valore"];
}

$id_statico = $id_statici["Template Email - Template Rapportino Intervento"];
if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
    $template_mail_id = 0;
}
else{
    $template_mail_id = $id_statico["valore"];
}

$radice_nome_documento = "PDF Rapportino Intervento ";

$path_cartella_temporanea = "modules/SproCore/CustomViews/FirmaInterventoHelpDesk/temp/";

$pdf_template_relmodule = "Timecards";

$path_cartella_firme = "modules/SproCore/CustomViews/FirmaInterventoHelpDesk/firme/";

$path_cartella_pdf_firmati = "modules/SproCore/CustomViews/FirmaInterventoHelpDesk/signed/";

$cartella_crm_documento = 1;
	
?>
