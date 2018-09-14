<?php

/* kpro@tom14022018 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2018, Kpro Consulting Srl
 */

include_once('../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user;
session_start();

require_once('modules/SproCore/SproUtils/spro_utils.php');

if(isset($_REQUEST['crmid'])){
    $account = $_REQUEST['crmid'];
}
else{
    die; 
}

if($account != 0 && $account != ""){

    /*$q_data_ora = "SELECT CURDATE() data_corrente, CURTIME() ora_corrente";
    $res_data_corrente = $adb->query($q_data_ora);
    $data_corrente = $adb->query_result($res_data_corrente, 0, 'data_corrente');
    $ora_corrente = $adb->query_result($res_data_corrente, 0, 'ora_corrente');

    $patch_logs = __DIR__."/logs/";
    $logs_file_name = "situazione_formazione_mirata_log.txt";
    $text_log = sprintf("\nAccount %s Calcolo start: %s %s ", $account, $data_corrente, $ora_corrente);
    $log_file=fopen($patch_logs.$logs_file_name,"a+");
    fwrite($log_file, $text_log);
    fclose($log_file);*/

    $id_statici = getConfigurazioniIdStatici();
	$id_statico = $id_statici["Programmi Custom - Gestione Avvisi - Giorni per In Scadenza standard"];
	if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
		$default_in_scadenza = 150;
	}
	else{
		$default_in_scadenza = $id_statico["valore"];
	}

    $giorni_in_scadenza = getGiorniInScadenzaAzienda($account, $default_in_scadenza);

    calcolaSituazioneFormazioneAzienda($account, $giorni_in_scadenza);
    
    /*$res_data_corrente = $adb->query($q_data_ora);
    $data_corrente = $adb->query_result($res_data_corrente, 0, 'data_corrente');
    $ora_corrente = $adb->query_result($res_data_corrente, 0, 'ora_corrente');

    $text_log = " Calcolo end: ".$data_corrente." ".$ora_corrente;
    $log_file=fopen($patch_logs.$logs_file_name,"a+");
    fwrite($log_file, $text_log);
    fclose($log_file);*/

}

?>