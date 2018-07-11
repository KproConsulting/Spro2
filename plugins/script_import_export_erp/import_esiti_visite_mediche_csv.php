<?php

/* kpro@bid09092016 */

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
$dir = 'Storico_Visite_Mediche';
$dir_old = 'Old_File/';

$path_logs = __DIR__."/logs/";
$logs_file_name = $dir."_import_log.txt";
$logs_file_name_limitazioni = $dir."_aggiornamento_risorse_import_log.txt";
$error_logs_file_name = $dir."_import_error.txt";

$data_corrente = date("Y-m-d");

$errori = 0;
$record_creati = 0;
$record_aggiornati = 0;
$record_processati = 0;

$data_inizio_importazione = date("Y-m-d H:i:s");

$report_finale = " 
IMPORTAZIONE ESITI VISITE MEDICHE delle ".$data_inizio_importazione;
$handle_log_file=fopen($path_logs.$logs_file_name, "w+");
fwrite($handle_log_file, $report_finale);
fclose($handle_log_file);

$report_finale = " 
ERRORI IMPORTAZIONE ESITI VISITE MEDICHE delle ".$data_inizio_importazione;
$handle_log_file=fopen($path_logs.$error_logs_file_name, "w+");
fwrite($handle_log_file, $report_finale);
fclose($handle_log_file);

$report_finale = " 
AGG. LIMITAZIONI DA IMPORTAZIONE ESITI VISITE MEDICHE delle ".$data_inizio_importazione;
$handle_log_file=fopen($path_logs.$logs_file_name_limitazioni, "w+");
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
                        PulisciLimitazioniPrescrizioniRisorse();
                        $array_tipi_visita_generici = GetTipiVisitaMedicaGenerici();

                        $id_tipo_visita_generico_scadenza = $array_tipi_visita_generici[0];
                        $id_tipo_visita_generico_no_scadenza = $array_tipi_visita_generici[1];

                        if($id_tipo_visita_generico_scadenza != 0 && $id_tipo_visita_generico_no_scadenza != 0){

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

                                    $codice_fiscale_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[3], "Testo", false);
                                    $data_visita_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[4], "Data", false);
                                    $descrizione_tipo_visita_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[7], "Testo", false);
                                    
                                    if($codice_fiscale_controllo != "" && $data_visita_controllo != "" && $descrizione_tipo_visita_controllo != ""){

                                        $record_processati++;

                                        $array_dati_tipi_visita = ImportTipiVisite($array_dati_riga);

                                        $id_crm_tipo_visita_specifico = $array_dati_tipi_visita[0];
                                        $nome_tipo_visita = $array_dati_tipi_visita[1];

                                        if($id_crm_tipo_visita_specifico != 0){

                                            $periodicita_visita_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[5], "Numero", false);
                                            
                                            if($periodicita_visita_controllo <= 0){
                                                $id_crm_tipo_visita = $id_tipo_visita_generico_no_scadenza;
                                            }
                                            else{
                                                $id_crm_tipo_visita = $id_tipo_visita_generico_scadenza;
                                            }
                                            
                                            $res_import = ImportStoricoVisite($array_dati_riga, $id_crm_tipo_visita, $nome_tipo_visita, $id_crm_tipo_visita_specifico);
                                            if($res_import == 1){
                                                $record_creati++;
                                            }
                                            else if($res_import == 0){
                                                $record_aggiornati++;
                                            }
                                            else{
                                                $errori++;

                                                $report_finale = " 
Nessuna risorsa trovata con codice fiscale: ".$codice_fiscale_controllo;
                                                $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                fwrite($handle_log_file, $report_finale);
                                                fclose($handle_log_file);
                                            }
                                        }
                                        else{
                                            $errori++;

                                            $report_finale = " 
Errore nella creazione/controllo del tipo visita medica";
                                            $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                            fwrite($handle_log_file, $report_finale);
                                            fclose($handle_log_file);
                                        }
                                    }
                                    else{
                                        $errori++;

                                        $report_finale = " 
Record privo dei dati obbligatori: ".$codice_fiscale_controllo." - ".$data_visita_controllo." - ".$descrizione_tipo_visita_controllo;
                                        $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                        fwrite($handle_log_file, $report_finale);
                                        fclose($handle_log_file);
                                    }
                                }
                                $row++;
                            }
                        }
                        else{
                            $errori++;

                            $report_finale = " 
Tipi visita medica generici non presenti!";
                            $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                            fwrite($handle_log_file, $report_finale);
                            fclose($handle_log_file);
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

function GetTipiVisitaMedicaGenerici(){
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $array_return_tipi_visita_generici = array();
    $id_tipo_visita_generico_scadenza = 0;
    $id_tipo_visita_generico_no_scadenza = 0;

    $q_verifica_tipo_visita = "SELECT tvm.tipivisitamedid
                        FROM {$table_prefix}_tipivisitamed tvm
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tvm.tipivisitamedid
                        WHERE ent.deleted = 0 AND tvm.kp_cod_tipo_visita = 'TVM1'";
    $res_verifica_tipo_visita = $adb->query($q_verifica_tipo_visita);
    if($adb->num_rows($res_verifica_tipo_visita) > 0){
        $id_tipo_visita_generico_scadenza = $adb->query_result($res_verifica_tipo_visita, 0, 'tipivisitamedid');
        $id_tipo_visita_generico_scadenza = html_entity_decode(strip_tags($id_tipo_visita_generico_scadenza), ENT_QUOTES, $default_charset);
    }

    $q_verifica_tipo_visita1 = "SELECT tvm.tipivisitamedid
                        FROM {$table_prefix}_tipivisitamed tvm
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tvm.tipivisitamedid
                        WHERE ent.deleted = 0 AND tvm.kp_cod_tipo_visita = 'TVM0'";
    $res_verifica_tipo_visita1 = $adb->query($q_verifica_tipo_visita1);
    if($adb->num_rows($res_verifica_tipo_visita1) > 0){
        $id_tipo_visita_generico_no_scadenza = $adb->query_result($res_verifica_tipo_visita1, 0, 'tipivisitamedid');
        $id_tipo_visita_generico_no_scadenza = html_entity_decode(strip_tags($id_tipo_visita_generico_no_scadenza), ENT_QUOTES, $default_charset);
    }

    if ($id_tipo_visita_generico_scadenza == 0 || $id_tipo_visita_generico_scadenza == '' || $id_tipo_visita_generico_scadenza == null) {
        $id_tipo_visita_generico_scadenza = 0;
    }

    if ($id_tipo_visita_generico_no_scadenza == 0 || $id_tipo_visita_generico_no_scadenza == '' || $id_tipo_visita_generico_no_scadenza == null) {
        $id_tipo_visita_generico_no_scadenza = 0;
    }

    $array_return_tipi_visita_generici[] = $id_tipo_visita_generico_scadenza;
    $array_return_tipi_visita_generici[] = $id_tipo_visita_generico_no_scadenza;

    return $array_return_tipi_visita_generici;
}

function ImportTipiVisite($array_dati_riga){
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $new_tipo_visita = 0;
    $array_dati_tipi_visita = array();

    $descrizione_tipo_visita = $array_dati_riga[7];

    $q_verifica_tipo_visita = "SELECT tvm.tipivisitamedid,
                        tvm.tipivisitamed_name
                        FROM {$table_prefix}_tipivisitamed tvm
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tvm.tipivisitamedid
                        WHERE ent.deleted = 0 AND tvm.tipivisitamed_name = '".$descrizione_tipo_visita."'";
    $res_verifica_tipo_visita = $adb->query($q_verifica_tipo_visita);
    if($adb->num_rows($res_verifica_tipo_visita) == 0){
        $tipo_visita = CRMEntity::getInstance('TipiVisitaMed');
        $tipo_visita->column_fields['tipivisitamed_name'] = $descrizione_tipo_visita;
        $tipo_visita->column_fields['kp_validita_tipi_visita'] = '999';
        $tipo_visita->column_fields['assigned_user_id'] = 1;
        $tipo_visita->save('TipiVisitaMed', $longdesc=true, $offline_update=false, $triggerEvent=false);

        $new_tipo_visita = $tipo_visita->id;

        $nome_tipo_visita = $descrizione_tipo_visita;
    }
    else{
        $new_tipo_visita = $adb->query_result($res_verifica_tipo_visita, 0, 'tipivisitamedid');
        $new_tipo_visita = html_entity_decode(strip_tags($new_tipo_visita), ENT_QUOTES, $default_charset);

        $nome_tipo_visita = $adb->query_result($res_verifica_tipo_visita, 0, 'tipivisitamed_name');
        $nome_tipo_visita = html_entity_decode(strip_tags($nome_tipo_visita), ENT_QUOTES, $default_charset);
    }

    if ($new_tipo_visita == 0 || $new_tipo_visita == '' || $new_tipo_visita == null) {
        $new_tipo_visita = 0;
    }

    $array_dati_tipi_visita[] = $new_tipo_visita;
    $array_dati_tipi_visita[] = $nome_tipo_visita;

    return $array_dati_tipi_visita;
}

function ImportStoricoVisite($array_dati_riga, $id_crm_tipo_visita, $nome_tipo_visita, $id_crm_tipo_visita_specifico){
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $codice_fiscale = $array_dati_riga[3];

    $q_verifica_risorsa = "SELECT cont.contactid,
                        cont.accountid,
                        cont.stabilimento,
                        cont.kp_evm_prescrizioni
                        FROM {$table_prefix}_contactdetails cont
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = cont.contactid
                        INNER JOIN {$table_prefix}_crmentity ent1 ON ent1.crmid = cont.accountid
                        INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = cont.stabilimento
                        WHERE ent.deleted = 0 AND ent1.deleted = 0 AND ent2.deleted = 0
                        AND cont.kp_codice_fiscale = '".$codice_fiscale."'";
    $res_verifica_risorsa = $adb->query($q_verifica_risorsa);
    if($adb->num_rows($res_verifica_risorsa) > 0){
        $id_crm_risorsa = $adb->query_result($res_verifica_risorsa, 0, 'contactid');
        $id_crm_risorsa = html_entity_decode(strip_tags($id_crm_risorsa), ENT_QUOTES, $default_charset);

        $id_crm_azienda = $adb->query_result($res_verifica_risorsa, 0, 'accountid');
        $id_crm_azienda = html_entity_decode(strip_tags($id_crm_azienda), ENT_QUOTES, $default_charset);

        $id_crm_stabilimento = $adb->query_result($res_verifica_risorsa, 0, 'stabilimento');
        $id_crm_stabilimento = html_entity_decode(strip_tags($id_crm_stabilimento), ENT_QUOTES, $default_charset);

        $lista_prescrizioni = $adb->query_result($res_verifica_risorsa, 0, 'kp_evm_prescrizioni');
        $lista_prescrizioni = html_entity_decode(strip_tags($lista_prescrizioni), ENT_QUOTES, $default_charset);

        $data_visita = normalizzaStringaCsvPerImportCrm($array_dati_riga[4], "Data", false);
        $periodicita_visita = normalizzaStringaCsvPerImportCrm($array_dati_riga[5], "Numero", false);
        $nome_medico = $array_dati_riga[12];
        $data_validita_visita = CalcolaValiditaVisita($data_visita,$periodicita_visita);

        $q_verifica_esito_visita = "SELECT esit.esitivisitemedicheid
                                    FROM {$table_prefix}_esitivisitemediche esit
                                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = esit.esitivisitemedicheid
                                    WHERE ent.deleted = 0 
                                    AND esit.risorsa = ".$id_crm_risorsa."
                                    AND esit.tipo_visita_medica = ".$id_crm_tipo_visita."
                                    AND esit.data_visita = '".$data_visita."'";
        $res_verifica_esito_visita = $adb->query($q_verifica_esito_visita);
        if($adb->num_rows($res_verifica_esito_visita) == 0){
            $esito_visita = CRMEntity::getInstance('EsitiVisiteMediche');
            $esito_visita->column_fields['risorsa'] = $id_crm_risorsa;
            $esito_visita->column_fields['tipo_visita_medica'] = $id_crm_tipo_visita;
            $esito_visita->column_fields['data_visita'] = $data_visita;
            $esito_visita->column_fields['data_fine_validita'] = $data_validita_visita;
            $esito_visita->column_fields['kp_azienda'] = $id_crm_azienda;
            $esito_visita->column_fields['kp_stabilimento'] = $id_crm_stabilimento;
            $esito_visita->column_fields['kp_evm_nome_medico'] = $nome_medico;
            $esito_visita->column_fields['description'] = $nome_tipo_visita;
            $esito_visita->column_fields['kp_id_tipo_visita'] = $id_crm_tipo_visita_specifico;
            $esito_visita->column_fields['assigned_user_id'] = 1;
            $esito_visita->save('EsitiVisiteMediche', $longdesc=true, $offline_update=false, $triggerEvent=false);

            $new_esito_visita = $esito_visita->id;

            AggiornaAnagraficaRisorsa($array_dati_riga, $id_crm_risorsa, $lista_prescrizioni);

            return 1;
        }
        else{
            $new_esito_visita = $adb->query_result($res_verifica_esito_visita, 0, 'esitivisitemedicheid');
            $new_esito_visita = html_entity_decode(strip_tags($new_esito_visita), ENT_QUOTES, $default_charset);

            $nome_medico = normalizzaStringaCsvPerImportCrm($array_dati_riga[12], "Testo", true);
            $nome_tipo_visita = addslashes($nome_tipo_visita);

            $q_update_esito_visita = "UPDATE {$table_prefix}_esitivisitemediche SET
                                    risorsa = ".$id_crm_risorsa.",
                                    tipo_visita_medica = ".$id_crm_tipo_visita.",
                                    data_visita = '".$data_visita."',
                                    data_fine_validita = '".$data_validita_visita."',
                                    kp_azienda = ".$id_crm_azienda.",
                                    kp_stabilimento = ".$id_crm_stabilimento.",
                                    kp_evm_nome_medico = '".$nome_medico."',
                                    kp_id_tipo_visita = ".$id_crm_tipo_visita_specifico.",
                                    description = '".$nome_tipo_visita."'
                                    WHERE esitivisitemedicheid = ".$new_esito_visita;
            $adb->query($q_update_esito_visita);

            AggiornaAnagraficaRisorsa($array_dati_riga, $id_crm_risorsa, $lista_prescrizioni);

            return 0;
        }
    }
    else{
        return -1;
    }
}

function AggiornaAnagraficaRisorsa($array_dati_riga, $id_crm_risorsa, $lista_prescrizioni_cont){
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $dir = 'Storico_Visite_Mediche';
    $path_logs = "logs/";
    $logs_file_name = $dir."_aggiornamento_risorse_import_log.txt";

    $data_visita = normalizzaStringaCsvPerImportCrm($array_dati_riga[4], "Data", false);
    $codice_idoneita = normalizzaStringaCsvPerImportCrm($array_dati_riga[8], "Numero", false);
    $lista_limitazioni = normalizzaStringaCsvPerImportCrm($array_dati_riga[9], "Testo", true);
    $lista_prescrizioni = normalizzaStringaCsvPerImportCrm($array_dati_riga[10], "Testo", true);

    list($anno_visita, $mese_visita, $giorno_visita) = explode("-", $data_visita);
    $data_visita_inv = date("d-m-Y", mktime(0, 0, 0, $mese_visita, $giorno_visita, $anno_visita));

    $dataoravisita_inv = $data_visita_inv;
    
    if(($lista_limitazioni != '' && $lista_limitazioni != null) || ($lista_prescrizioni != '' && $lista_prescrizioni != null)){
        $lista_prescrizioni = $dataoravisita_inv . ": " .$lista_limitazioni . " - " . $lista_prescrizioni;

        if ($lista_prescrizioni_cont == '' || $lista_prescrizioni_cont == null) {
            $lista_prescrizioni_cont = $lista_prescrizioni;
        } else {
            if($lista_prescrizioni_cont != $lista_prescrizioni){
                $lista_prescrizioni_cont = $lista_prescrizioni_cont . "<br />" . $lista_prescrizioni;
            } 
        }
    }

    $lista_prescrizioni_cont = addslashes($lista_prescrizioni_cont);

    if($codice_idoneita == '1' || $codice_idoneita == 1){
        $q_update_risorsa = "UPDATE {$table_prefix}_contactdetails SET
                            limitazioni = 'No',
                            kp_evm_prescrizioni = '".$lista_prescrizioni_cont."'
                            WHERE contactid = ".$id_crm_risorsa;
        $adb->query($q_update_risorsa);
    }
    else if($codice_idoneita == '11' || $codice_idoneita == 11){
        $q_update_risorsa = "UPDATE {$table_prefix}_contactdetails SET
                            kp_evm_prescrizioni = '".$lista_prescrizioni_cont."'
                            WHERE contactid = ".$id_crm_risorsa;
        $adb->query($q_update_risorsa);
    }
    else{
        $q_update_risorsa = "UPDATE {$table_prefix}_contactdetails SET
                            limitazioni = 'Si',
                            kp_evm_prescrizioni = '".$lista_prescrizioni_cont."'
                            WHERE contactid = ".$id_crm_risorsa;
        $adb->query($q_update_risorsa);
    }

    $report_finale = " 
Aggiornamento limitazioni e prescrizioni risorsa ".$id_crm_risorsa.", codice idoneita = ".$codice_idoneita;
    $handle_log_file=fopen($path_logs.$logs_file_name, "a+");
    fwrite($handle_log_file, $report_finale);
    fclose($handle_log_file);
}

function PulisciLimitazioniPrescrizioniRisorse(){
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $q_update_risorsa = "UPDATE {$table_prefix}_contactdetails SET
                        limitazioni = 'No',
                        kp_evm_prescrizioni = ''";
    $adb->query($q_update_risorsa);
}

function CalcolaValiditaVisita($data_visita,$periodicita_visita){
    if($periodicita_visita <= 0 || $periodicita_visita > 120){
        return '2999-12-31';
    }
    else{
        $data_da_aggiungere=date_create($data_visita);
        date_add($data_da_aggiungere,date_interval_create_from_date_string($periodicita_visita." months"));
        return date_format($data_da_aggiungere,"Y-m-d");
    }
}

/*

0) CodConto 
1) DscConto1 
2) CognomeNome 
3) CodiceFiscale 
4) DataVisita 
5) PeriodicitaVisita 
6) CodiceArticoloMetodo 
7) DescrizioneTipoVisita 
8) CodIdoneita 
9) Descrizione 
10) PrescrizioniAggiuntive 
11) Diagnosi 
12) NomeMedico 

*/

?>