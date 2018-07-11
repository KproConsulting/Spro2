<?php

/* kpro@bid21102016 */

/**
 * @author BideseJacopo
 * @copyright (c) 2016, Kpro Consulting Srl
 * @package importExport
 * @version 1.0
 */

require_once(__DIR__.'/../import_export_utils/import_utils.php'); /* kpro@bid190420171000 */

include_once(__DIR__.'/../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

$current_user->id = 1;

$path_ftp = "/home/erp/Import_Export/Export_da_Erp/";
$dir = 'Manutenzioni';
$dir_old = 'Old_File/';

$path_logs = __DIR__."/logs/";
$logs_file_name = $dir."_import_log.txt";
$error_logs_file_name = $dir."_import_error.txt";

$data_corrente = date("Y-m-d");

$errori = 0;
$record_creati = 0;
$record_aggiornati = 0;
$record_processati = 0;

$data_inizio_importazione = date("Y-m-d H:i:s");

$report_finale = " 
IMPORTAZIONE MANUTENZIONI delle ".$data_inizio_importazione;
$handle_log_file=fopen($path_logs.$logs_file_name, "w+");
fwrite($handle_log_file, $report_finale);
fclose($handle_log_file);

$report_finale = " 
ERRORI IMPORTAZIONE MANUTENZIONI delle ".$data_inizio_importazione;
$handle_log_file=fopen($path_logs.$error_logs_file_name, "w+");
fwrite($handle_log_file, $report_finale);
fclose($handle_log_file);

if (is_dir($path_ftp.$dir)) {
    try {

        if ($dh = opendir($path_ftp.$dir)) {

            while (($file = readdir($dh)) !== false) {
                $ext = pathinfo($file, PATHINFO_EXTENSION);

                if ($ext == 'csv' || $ext == 'CSV') {
                    $row = 1;
                    RimuoviCapoRigaCSVperImport($path_ftp.$dir . '/' . $file);
                    if (($handle = fopen($path_ftp.$dir . '/' . $file, "r")) !== false) {
                        $file_csv_size = filesize($path_ftp.$dir . '/'. $file);
                        while (($array_dati_riga = fgetcsv($handle, $file_csv_size, ";")) !== false) {

                            if($row == 1){
                                //StampaIntestazioneCSV($array_dati_riga,$path_logs,$logs_file_name);
                            }
                            else if ($row > 1) {
                                    for ($i = 0; $i < count($array_dati_riga); $i++) {

                                        if ($array_dati_riga[$i] != null) {
                                            $array_dati_riga[$i] = trim(rimuoviApiciStringaCsvPerImportCrm($array_dati_riga[$i]));
                                        } else {
                                            $array_dati_riga[$i] = '';
                                        }
                                    }
                                    
                                    $id_contratto_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[0], "Testo", false);
                                    $sezione_contratto_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[1], "Testo", false);
                                    $id_righe_mezzo_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[5], "Testo", false);
                                    $id_rapporto_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[4], "Testo", false);
                                    $stato_attivita_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[9], "Testo", false);
                                    $data_effettuazione_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[17], "DateTime", false);

                                    if($id_contratto_controllo != '' && $sezione_contratto_controllo != '' && $id_righe_mezzo_controllo != ''
                                        && $id_rapporto_controllo != '' && $stato_attivita_controllo == 'Eseguito' && $data_effettuazione_controllo != ''){

                                        $record_processati++;

                                        $id_crm_testata_manutenzione = ImportTestataManutenzione($array_dati_riga);

                                        if($id_crm_testata_manutenzione != 0){
                                            $tipo_componente_impianto = TipoComponenteImpianto($array_dati_riga);

                                            switch($tipo_componente_impianto){
                                                case 'ESTINTORI':     
                                                    $flag_sc_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[10], "Flag", false);
                                                    $flag_rev_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[11], "Flag", false);
                                                    $flag_coll_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[12], "Flag", false); 

                                                    if($flag_sc_controllo == '1' || $flag_sc_controllo == 1){
                                                        $tipologia_checklist = 'sorveglianza_control';
                                                        $res_import = ImportRigaManutenzione($array_dati_riga, $id_crm_testata_manutenzione, $tipologia_checklist, '');
                                                        if($res_import == 1){
                                                            $record_creati++;
                                                        }
                                                        else if($res_import == 0){
                                                            $record_aggiornati++;
                                                        }
                                                        else{
                                                            $errori++;

                                                            $report_finale = " 
Errore durante al creazione/aggiornamento della riga manutenzione";
                                                            $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                            fwrite($handle_log_file, $report_finale);
                                                            fclose($handle_log_file);
                                                        }
                                                    } 
                                                    if($flag_rev_controllo == '1' || $flag_rev_controllo == 1){
                                                        $data_prev_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[13], "DateTime", false);
                                                        if($data_prev_controllo != ''){
                                                            $tipologia_checklist = 'revisione';

                                                            $res_import = ImportRigaManutenzione($array_dati_riga, $id_crm_testata_manutenzione, $tipologia_checklist, $data_prev_controllo);
                                                            if($res_import == 1){
                                                                $record_creati++;
                                                            }
                                                            else if($res_import == 0){
                                                                $record_aggiornati++;
                                                            }
                                                            else{
                                                                $errori++;

                                                                $report_finale = " 
Errore durante al creazione/aggiornamento della riga manutenzione";
                                                                $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                                fwrite($handle_log_file, $report_finale);
                                                                fclose($handle_log_file);
                                                            }
                                                        }   
                                                        else{
                                                            $errori++;

                                                            $report_finale = " 
Data prossima revisione non corretta";
                                                            $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                            fwrite($handle_log_file, $report_finale);
                                                            fclose($handle_log_file);
                                                        }                                                     
                                                    }
                                                    if($flag_coll_controllo == '1' || $flag_coll_controllo == 1){
                                                        $data_pcoll_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[14], "DateTime", false);
                                                        if($data_pcoll_controllo != ''){
                                                            $tipologia_checklist = 'collaudo';

                                                            $res_import = ImportRigaManutenzione($array_dati_riga, $id_crm_testata_manutenzione, $tipologia_checklist, $data_pcoll_controllo);
                                                            if($res_import == 1){
                                                                $record_creati++;
                                                            }
                                                            else if($res_import == 0){
                                                                $record_aggiornati++;
                                                            }
                                                            else{
                                                                $errori++;

                                                                $report_finale = " 
Errore durante al creazione/aggiornamento della riga manutenzione";
                                                                $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                                fwrite($handle_log_file, $report_finale);
                                                                fclose($handle_log_file);
                                                            }
                                                        }
                                                        else{
                                                            $errori++;

                                                            $report_finale = " 
Data prossimo collaudo non corretta";
                                                            $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                            fwrite($handle_log_file, $report_finale);
                                                            fclose($handle_log_file);
                                                        }
                                                    }      
                                                    break;
                                                case 'IDRANTI':
                                                    $flag_visiva_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[15], "Flag", false);
                                                    $flag_pressione_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[16], "Flag", false);
                                                    $flag_coll_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[12], "Flag", false);    

                                                    if($flag_visiva_controllo == '1' || $flag_visiva_controllo == 1){
                                                        $tipologia_checklist = 'visivo';
                                                        $res_import = ImportRigaManutenzione($array_dati_riga, $id_crm_testata_manutenzione, $tipologia_checklist, '');
                                                        if($res_import == 1){
                                                            $record_creati++;
                                                        }
                                                        else if($res_import == 0){
                                                            $record_aggiornati++;
                                                        }
                                                        else{
                                                            $errori++;

                                                            $report_finale = " 
Errore durante al creazione/aggiornamento della riga manutenzione";
                                                            $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                            fwrite($handle_log_file, $report_finale);
                                                            fclose($handle_log_file);
                                                        }
                                                    } 
                                                    if($flag_pressione_controllo == '1' || $flag_pressione_controllo == 1){
                                                        $tipologia_checklist = 'pressione';
                                                        $res_import = ImportRigaManutenzione($array_dati_riga, $id_crm_testata_manutenzione, $tipologia_checklist, '');
                                                        if($res_import == 1){
                                                            $record_creati++;
                                                        }
                                                        else if($res_import == 0){
                                                            $record_aggiornati++;
                                                        }
                                                        else{
                                                            $errori++;

                                                            $report_finale = " 
Errore durante al creazione/aggiornamento della riga manutenzione";
                                                            $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                            fwrite($handle_log_file, $report_finale);
                                                            fclose($handle_log_file);
                                                        }                                                   
                                                    }
                                                    if($flag_coll_controllo == '1' || $flag_coll_controllo == 1){
                                                        $data_pcoll_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[14], "DateTime", false);
                                                        if($data_pcoll_controllo != ''){
                                                            $tipologia_checklist = 'collaudo';

                                                            $res_import = ImportRigaManutenzione($array_dati_riga, $id_crm_testata_manutenzione, $tipologia_checklist, $data_pcoll_controllo);
                                                            if($res_import == 1){
                                                                $record_creati++;
                                                            }
                                                            else if($res_import == 0){
                                                                $record_aggiornati++;
                                                            }
                                                            else{
                                                                $errori++;

                                                                $report_finale = " 
Errore durante al creazione/aggiornamento della riga manutenzione";
                                                                $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                                fwrite($handle_log_file, $report_finale);
                                                                fclose($handle_log_file);
                                                            }
                                                        }
                                                        else{
                                                            $errori++;

                                                            $report_finale = " 
Data prossimo collaudo non corretta";
                                                            $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                            fwrite($handle_log_file, $report_finale);
                                                            fclose($handle_log_file);
                                                        }
                                                    }
                                                    break;
                                                case 'IMPIANTO':
                                                    $tipologia_checklist = 'impianto';
                                                    $res_import = ImportRigaManutenzione($array_dati_riga, $id_crm_testata_manutenzione, $tipologia_checklist, '');
                                                    if($res_import == 1){
                                                        $record_creati++;
                                                    }
                                                    else if($res_import == 0){
                                                        $record_aggiornati++;
                                                    }
                                                    else{
                                                        $errori++;

                                                        $report_finale = " 
Errore durante al creazione/aggiornamento della riga manutenzione";
                                                        $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                        fwrite($handle_log_file, $report_finale);
                                                        fclose($handle_log_file);
                                                    }
                                                    break;
                                                case 'PPA':
                                                    $tipologia_checklist = 'ppa';
                                                    $res_import = ImportRigaManutenzione($array_dati_riga, $id_crm_testata_manutenzione, $tipologia_checklist, '');
                                                    if($res_import == 1){
                                                        $record_creati++;
                                                    }
                                                    else if($res_import == 0){
                                                        $record_aggiornati++;
                                                    }
                                                    else{
                                                        $errori++;

                                                        $report_finale = " 
Errore durante al creazione/aggiornamento della riga manutenzione";
                                                        $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                        fwrite($handle_log_file, $report_finale);
                                                        fclose($handle_log_file);
                                                    }
                                                    break;
                                                case 'SERRAMENTI':
                                                    $tipologia_checklist = 'serramenti';
                                                    $res_import = ImportRigaManutenzione($array_dati_riga, $id_crm_testata_manutenzione, $tipologia_checklist, '');
                                                    if($res_import == 1){
                                                        $record_creati++;
                                                    }
                                                    else if($res_import == 0){
                                                        $record_aggiornati++;
                                                    }
                                                    else{
                                                        $errori++;

                                                        $report_finale = " 
Errore durante al creazione/aggiornamento della riga manutenzione";
                                                        $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                        fwrite($handle_log_file, $report_finale);
                                                        fclose($handle_log_file);
                                                    }
                                                    break;
                                                case 'DETTAGLIO_ALTRI':
                                                    $tipologia_checklist = 'altri_mezzi';
                                                    $res_import = ImportRigaManutenzione($array_dati_riga, $id_crm_testata_manutenzione, $tipologia_checklist, '');
                                                    if($res_import == 1){
                                                        $record_creati++;
                                                    }
                                                    else if($res_import == 0){
                                                        $record_aggiornati++;
                                                    }
                                                    else{
                                                        $errori++;

                                                        $report_finale = " 
Errore durante al creazione/aggiornamento della riga manutenzione";
                                                        $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                        fwrite($handle_log_file, $report_finale);
                                                        fclose($handle_log_file);
                                                    }
                                                    break;
                                                    default:
                                                        $errori++;

                                                        $report_finale = " 
Errore nel controllo del tipo componente impianto: ".$tipo_componente_impianto;
                                                        $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                        fwrite($handle_log_file, $report_finale);
                                                        fclose($handle_log_file);
                                            }
                                        }
                                        else{
                                            $errori++;

                                            $report_finale = " 
Errore nella creazione/controllo della testata manutenzione";
                                            $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                            fwrite($handle_log_file, $report_finale);
                                            fclose($handle_log_file);
                                        }
                                    }
                                    else{
                                        $errori++;

                                        $report_finale = " 
Record privo dei dati obbligatori o non in stato Eseguito: ".$id_contratto_controllo." - ".$sezione_contratto_controllo." - ".$id_righe_mezzo_controllo." - ".$id_rapporto_controllo." - ".$stato_attivita_controllo." - ".$data_effettuazione_controllo;
                                        $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                        fwrite($handle_log_file, $report_finale);
                                        fclose($handle_log_file);
                                    }
                                    
                                }
                            $row++;
                        }
                        fclose($handle);

                        copy($path_ftp.$dir.'/'.$file, $path_ftp.$dir.'/'.$dir_old.date("YmdHis").'_'.$file); /* kpro@bid180420170930 */
                        unlink($path_ftp.$dir.'/'.$file);

                        $data_fine_importazione = date("Y-m-d H:i:s");

                        $report_finale = " 
terminato alle ".$data_fine_importazione.": processati ".$record_processati.", creati ".$record_creati.", aggiornati ".$record_aggiornati.", errori ".$errori;
                        $handle_log_file=fopen($path_logs.$logs_file_name, "a+");
                        fwrite($handle_log_file, $report_finale);
                        fclose($handle_log_file);
                    }
                }
            }
            closedir($dh);
        }
    } catch (Exception $e) {
        print($e);
    }
}

function CalcolaScadenzaRigaManutenzione($data, $frequenza){
    $tipo_frequenza = "";
	$codice_tipo_frequenza = substr($frequenza,0,1);
    $frequenza = substr($frequenza,1);
	if($codice_tipo_frequenza == 'M'){
		$tipo_frequenza = "months";
	}
	else if($codice_tipo_frequenza == 'D'){
		$tipo_frequenza = "days";
	}
	
	if($tipo_frequenza != ""){
		$data_da_aggiungere=date_create($data);
		date_add($data_da_aggiungere,date_interval_create_from_date_string($frequenza." ".$tipo_frequenza));
		return date_format($data_da_aggiungere,"Y-m-d");
	}
	else{
		return date("Y-m-d");
	}
}

function ImportTestataManutenzione($array_dati_riga){
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $new_manutenzione = 0;

    $codice_manutenzione_gemma = $array_dati_riga[4];
    $note_interne = $array_dati_riga[18];
    $tecnico_manutenzione = $array_dati_riga[19];

    $data_effettuazione = normalizzaStringaCsvPerImportCrm($array_dati_riga[17], "DateTime", false);

    $data_effettuazione_datetime = new DateTime($data_effettuazione);
    $data_effettuazione_inv = $data_effettuazione_datetime->format("d-m-Y");

    $q_verifica_testata_manutenzione = "SELECT man.manutenzioniid
                                    FROM {$table_prefix}_manutenzioni man
                                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = man.manutenzioniid
                                    WHERE ent.deleted = 0 AND man.kp_cod_manut_gemma = '".$codice_manutenzione_gemma."'";
    $res_verifica_testata_manutenzione = $adb->query($q_verifica_testata_manutenzione);
    if($adb->num_rows($res_verifica_testata_manutenzione) == 0){
        $manutenzioni = CRMEntity::getInstance('Manutenzioni');
        $manutenzioni->column_fields['assigned_user_id'] = 1;
        $manutenzioni->column_fields['manutenzione_name'] = 'Manutenzione Gemma del '.$data_effettuazione_inv;
        $manutenzioni->column_fields['kp_cod_manut_gemma'] = $codice_manutenzione_gemma;
        $manutenzioni->column_fields['data_manutenzione'] = $data_effettuazione;
        $manutenzioni->column_fields['stato_manutenzione'] = 'Eseguita';
        $manutenzioni->column_fields['kp_gm_tecnico_manut'] = $tecnico_manutenzione;
        $manutenzioni->column_fields['kp_gm_note_interne'] = $note_interne;
        $manutenzioni->save('Manutenzioni', $longdesc=true, $offline_update=false, $triggerEvent=false);

        $new_manutenzione = $manutenzioni->id;
    }
    else{
        $new_manutenzione = $adb->query_result($res_verifica_testata_manutenzione, 0, 'manutenzioniid');
        $new_manutenzione = html_entity_decode(strip_tags($new_manutenzione), ENT_QUOTES, $default_charset);
    }

    if ($new_manutenzione == 0 || $new_manutenzione == '' || $new_manutenzione == null) {
        $new_manutenzione = 0;
    }

    return $new_manutenzione;
}

function TipoComponenteImpianto($array_dati_riga){
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $id_contratto = $array_dati_riga[0];
    $sezione_contratto = $array_dati_riga[1];
    $id_riga_mezzo = $array_dati_riga[5];

    $q_verifica_componente = "SELECT comp.kp_gm_tipo_impianto
                    FROM {$table_prefix}_compimpianto comp
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = comp.compimpiantoid
                    WHERE ent.deleted = 0
                    AND comp.kp_gm_id_contratto = '".$id_contratto."'
                    AND comp.kp_gm_sez_contratto = '".$sezione_contratto."'
                    AND comp.kp_gm_id_riga_mezzo = '".$id_riga_mezzo."'";
    $res_verifica_componente = $adb->query($q_verifica_componente);
    if($adb->num_rows($res_verifica_componente) > 0){
        $tipo_componente_impianto = $adb->query_result($res_verifica_componente, 0, 'kp_gm_tipo_impianto');
        $tipo_componente_impianto = html_entity_decode(strip_tags($tipo_componente_impianto), ENT_QUOTES, $default_charset);

        return $tipo_componente_impianto;
    }
    else{
        return "";
    }
}

function ImportRigaManutenzione($array_dati_riga, $id_crm_testata_manutenzione, $tipologia_checklist, $data_scadenza_riga_manutenzione){
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $q_verifica_checklist = "SELECT chk.checklistsid,
                        chk.nome_check_list,
                        chk.frequenza_checklist
                        FROM {$table_prefix}_checklists chk
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = chk.checklistsid
                        WHERE ent.deleted = 0 
                        AND chk.kp_specialita_check = '".$tipologia_checklist."'";
    $res_verifica_checklist = $adb->query($q_verifica_checklist);
    if($adb->num_rows($res_verifica_checklist) > 0){
        $id_crm_checklist = $adb->query_result($res_verifica_checklist, 0, 'checklistsid');
        $id_crm_checklist = html_entity_decode(strip_tags($id_crm_checklist), ENT_QUOTES, $default_charset);

        $nome_checklist = $adb->query_result($res_verifica_checklist, 0, 'nome_check_list');
        $nome_checklist = html_entity_decode(strip_tags($nome_checklist), ENT_QUOTES, $default_charset);

        $frequenza = $adb->query_result($res_verifica_checklist, 0, 'frequenza_checklist');
        $frequenza = html_entity_decode(strip_tags($frequenza), ENT_QUOTES, $default_charset);

        $id_contratto = $array_dati_riga[0];
        $sezione_contratto = $array_dati_riga[1];
        $id_riga_mezzo = $array_dati_riga[5];

        if($data_scadenza_riga_manutenzione == ''){
            $data_effettuazione = normalizzaStringaCsvPerImportCrm($array_dati_riga[17], "DateTime", false);
            $data_scadenza_riga_manutenzione = CalcolaScadenzaRigaManutenzione($data_effettuazione, $frequenza);
        }

        $q_verifica_componente = "SELECT comp.compimpiantoid
                        FROM {$table_prefix}_compimpianto comp
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = comp.compimpiantoid
                        WHERE ent.deleted = 0
                        AND comp.kp_gm_id_contratto = '".$id_contratto."'
                        AND comp.kp_gm_sez_contratto = '".$sezione_contratto."'
                        AND comp.kp_gm_id_riga_mezzo = '".$id_riga_mezzo."'";
        $res_verifica_componente = $adb->query($q_verifica_componente);
        if($adb->num_rows($res_verifica_componente) > 0){
            $id_crm_componente = $adb->query_result($res_verifica_componente, 0, 'compimpiantoid');
            $id_crm_componente = html_entity_decode(strip_tags($id_crm_componente), ENT_QUOTES, $default_charset);

            $nome_componente = $array_dati_riga[7];
            $note_riga = $array_dati_riga[8];
            $nome_riga_manutenzione = $nome_componente.' - '.$nome_checklist;

            $q_verifica_riga_manutenzione = "SELECT righe.kprighemanutenzioniid
                                        FROM {$table_prefix}_kprighemanutenzioni righe
                                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = righe.kprighemanutenzioniid
                                        WHERE ent.deleted = 0 
                                        AND righe.kp_manutenzione = ".$id_crm_testata_manutenzione."
                                        AND righe.kp_componente = ".$id_crm_componente."
                                        AND righe.kp_check_list = ".$id_crm_checklist;
            $res_verifica_riga_manutenzione = $adb->query($q_verifica_riga_manutenzione);
            if($adb->num_rows($res_verifica_riga_manutenzione) == 0){
                $riga = CRMEntity::getInstance('KpRigheManutenzioni');
                $riga->column_fields['assigned_user_id'] = 1;
                $riga->column_fields['kp_nome_riga_man'] = $nome_riga_manutenzione;
                $riga->column_fields['kp_manutenzione'] = $id_crm_testata_manutenzione;
                $riga->column_fields['kp_componente'] = $id_crm_componente;
                $riga->column_fields['kp_check_list'] = $id_crm_checklist;
                $riga->column_fields['frequenza_checklist'] = $frequenza;
                $riga->column_fields['kp_data_scadenza'] = $data_scadenza_riga_manutenzione;
                $riga->column_fields['description'] = $note_riga;
                $riga->save('KpRigheManutenzioni', $longdesc=true, $offline_update=false, $triggerEvent=false);

                $new_riga_manutenzione = $riga->id;

                return 1;
            }
            else{
                $id_riga_manutenzione = $adb->query_result($res_verifica_riga_manutenzione, 0, 'kprighemanutenzioniid');
                $id_riga_manutenzione = html_entity_decode(strip_tags($id_riga_manutenzione), ENT_QUOTES, $default_charset);

                $note_riga = normalizzaStringaCsvPerImportCrm($note_riga, "Testo", true);
                $nome_riga_manutenzione = normalizzaStringaCsvPerImportCrm($nome_riga_manutenzione, "Testo", true);

                $q_update_riga_manutenzione = "UPDATE {$table_prefix}_kprighemanutenzioni SET
                            kp_nome_riga_man = '".$nome_riga_manutenzione."',
                            kp_manutenzione = ".$id_crm_testata_manutenzione.",
                            kp_componente = ".$id_crm_componente.",
                            kp_check_list = ".$id_crm_checklist.",
                            frequenza_checklist = '".$frequenza."',
                            kp_data_scadenza = '".$data_scadenza_riga_manutenzione."',
                            description = '".$note_riga."'
                            WHERE kprighemanutenzioniid = ".$id_riga_manutenzione;
                $adb->query($q_update_riga_manutenzione);

                return 0;
            }
        }
        else{
            return -1;
        }
    }
    else{
        return -1;
    }
}

/*

0) IDCONTRATTO 
1) SEZIONECONTRATTO 
2) CODCLIENTE 
3) CODDEST 
4) IDRAPPORTO 
5) IdrigaMezzo 
6) CodMezzo 
7) DescrizioneMezzo 
8) Note 
9) Descr_statoattivita 
10) flag_sc 
11) flag_rev 
12) flag_coll 
13) data_prev 
14) data_pcoll 
15) flag_visiva 
16) flag_pressione 
17) DATA_EFF 
18) NOTE_INTERNE 
19) DescTecnicoEff 

*/

?>