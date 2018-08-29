<?php

/* kpro@bid210420170930 */

/**
 * @author Bidese Jacopo
 * @copyright (c) 2017, Kpro Consulting Srl
 * @package KpImportCustom
 * @version 1.0
 */

require_once('../../../../plugins/import_export_utils/import_utils.php');

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

$current_user->id = 1;

$rows = array();
if(isset($_REQUEST['server_filename'])){
	$file = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_REQUEST['server_filename']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$file = substr($file,0,255);

    $extension = pathinfo($file, PATHINFO_EXTENSION);

	$filename_no_ext = basename($file,'.'.$extension);

    $path = dirname(__FILE__)."/temp";

    $path_logs = dirname(__FILE__)."/logs/";
    $error_logs_file_name = $filename_no_ext."_error_log.txt";

    $errori = 0;
    $record_processati = 0;

    $impianti_creati = 0;
    $componenti_creati = 0;
    $checklists_create = 0;
    $relazioni_componente_checklist = 0;

    $data_inizio_importazione = date("Y-m-d H:i:s");

    $report_finale = "
ERRORI IMPORTAZIONE IMPIANTI delle ".$data_inizio_importazione;
    $handle_log_file=fopen($path_logs.$error_logs_file_name, "w+");
    fwrite($handle_log_file, $report_finale);
    fclose($handle_log_file);

    $report_finale = "
DETTAGLIO ERRORI IMPORTAZIONE IMPIANTI delle ".$data_inizio_importazione;
    $handle_log_file=fopen($path_logs.'dettaglio_errori.txt', "w+");
    fwrite($handle_log_file, $report_finale);
    fclose($handle_log_file);

    if (is_dir($path)) {
        try {
            if ($dh = opendir($path)) {
                if(file_exists($path. '/' .$file)) {
                    $ext = pathinfo($file, PATHINFO_EXTENSION);
                    
                    if ($ext == 'csv' || $ext == 'CSV') {
                        $row = 1;
                        RimuoviCapoRigaCSVperImport($path . '/' . $file);
                        if (($handle = fopen($path . '/' . $file, "r")) !== false) {
                            
                            while (($array_dati_riga = fgetcsv($handle, 0, ";")) !== false) {
                                
                                if($row == 1){
                                    
                                }
                                else if ($row > 1) {
                                    $record_processati++;
                                    
                                    for ($i = 0; $i < count($array_dati_riga); $i++) {
                                        if ($array_dati_riga[$i] != null) {
                                            $array_dati_riga[$i] = trim(rimuoviApiciStringaCsvPerImportCrm($array_dati_riga[$i]));
                                        } else {
                                            $array_dati_riga[$i] = '';
                                        }
                                    }
                                    
                                    $azienda_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[0], "Testo", false);
                                    $stabilimento_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[1], "Testo", false);
                                    $nome_impianto_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[2], "Testo", false);
                                    $data_attivazione_impianto_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[3], "Data", false);
                                    $nome_componente_controllo = normalizzaStringaCsvPerImportCrm($array_dati_riga[9], "Testo", false);
                                    
                                    if($azienda_controllo != '' && $stabilimento_controllo != '' && $nome_impianto_controllo != '' 
                                        && $data_attivazione_impianto_controllo != '' && $nome_componente_controllo != ''){                                        
                                            
                                        $id_crm_azienda = ControlloAzienda($array_dati_riga); 
                                        if($id_crm_azienda != 0){ 

                                            $id_crm_stabilimento = ControlloStabilimento($array_dati_riga, $id_crm_azienda);
                                            if($id_crm_stabilimento != 0){
                                                
                                                $id_crm_impianto = ImportImpianti($array_dati_riga, $id_crm_azienda, $id_crm_stabilimento);
                                                if($id_crm_impianto != 0){
                                                    
                                                    $res_import_componenti = ImportComponenti($array_dati_riga, $id_crm_impianto);
                                                    if($res_import_componenti == 1){
                                                        $componenti_creati++;
                                                    }
                                                }
                                                else{
                                                    $errori++;

                                                    $report_finale = "
    ".$nome_impianto_controllo.": Impianto non trovato";
                                                    $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                    fwrite($handle_log_file, $report_finale);
                                                    fclose($handle_log_file);
                                                }
                                            }
                                            else{
                                                $errori++;

                                                $report_finale = "
    ".$stabilimento_controllo.": Stabilimento non trovato";
                                                $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                                fwrite($handle_log_file, $report_finale);
                                                fclose($handle_log_file);
                                            }
                                        }
                                        else{
                                            $errori++;

                                            $report_finale = "
    ".$azienda_controllo.": Azienda non trovata";
                                            $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                            fwrite($handle_log_file, $report_finale);
                                            fclose($handle_log_file);
                                        }
                                    }
                                    else{
                                        $errori++;

                                        $report_finale = "
    Record privo dei campi obbligatori: ".$azienda_controllo." - ".$stabilimento_controllo." - ".$nome_impianto_controllo." - ".$data_attivazione_impianto_controllo." - ".$nome_componente_controllo;
                                        $handle_log_file=fopen($path_logs.$error_logs_file_name, "a+");
                                        fwrite($handle_log_file, $report_finale);
                                        fclose($handle_log_file);
                                    }
                                }
                                $row++;
                            }
                            fclose($handle);

                            unlink($path.'/'.$file);
                        }
                    }
                }
                closedir($dh);
            }
        } catch (Exception $e) {
            print($e);
        }
    }

    $rows[] = array(
        "Processati"=>$record_processati,
        "Impianti creati"=>$impianti_creati,
        "Componenti creati"=>$componenti_creati,
        "Checklists create"=>$checklists_create,
        "Relazioni Componente-Checklist create"=>$relazioni_componente_checklist,
        "Errori"=>$errori
    );
}

$json = json_encode($rows);
print $json;

function ControlloAzienda($dati_riga) {
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $nome_azienda = $dati_riga[0];
    $nome_azienda_controllo = addslashes($nome_azienda);
    
    $q_verifica_azienda = "SELECT acc.accountid
                        FROM {$table_prefix}_account acc
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = acc.accountid
                        WHERE ent.deleted = 0 AND acc.accountname LIKE '" . $nome_azienda_controllo . "'";

    $res_verifica_azienda = $adb->query($q_verifica_azienda);    
    if ($adb->num_rows($res_verifica_azienda) > 0) {
        $new_account = $adb->query_result($res_verifica_azienda, 0, 'accountid');
        $new_account = html_entity_decode(strip_tags($new_account), ENT_QUOTES, $default_charset);
    }

    if ($new_account == 0 || $new_account == '' || $new_account == null) {
        $new_account = 0;
    }

    return $new_account;
}

function ControlloStabilimento($dati_riga, $id_crm_azienda) {
    global $adb, $table_prefix, $default_charset;

    $nome_stabilimento = $dati_riga[1];
    $nome_stabilimento_controllo = addslashes($nome_stabilimento);
    $nome_azienda = $dati_riga[0];

    //$nome_stabilimento = $nome_azienda." - ".$nome_stabilimento;

    $q_verifica_stabilimento = "SELECT stab.stabilimentiid
                        FROM {$table_prefix}_stabilimenti stab
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = stab.stabilimentiid
                        WHERE ent.deleted = 0 
                        AND stab.azienda = ".$id_crm_azienda."
                        AND stab.nome_stabilimento LIKE '" . $nome_stabilimento_controllo . "'";

    $res_verifica_stabilimento = $adb->query($q_verifica_stabilimento);
    if ($adb->num_rows($res_verifica_stabilimento) > 0) {
        $new_stabilimento = $adb->query_result($res_verifica_stabilimento, 0, 'stabilimentiid');
        $new_stabilimento = html_entity_decode(strip_tags($new_stabilimento), ENT_QUOTES, $default_charset);
    }

    if ($new_stabilimento == 0 || $new_stabilimento == '' || $new_stabilimento == null) {
        $new_stabilimento = 0;
    }

    return $new_stabilimento;
}

function ImportImpianti($dati_riga, $id_crm_azienda, $id_crm_stabilimento) {
    global $adb, $table_prefix, $current_user, $site_URL, $default_charset;

    $nome_impianto = $dati_riga[2];
    $nome_impianto_controllo = addslashes($nome_impianto);
    $data_attivazione_impianto = normalizzaStringaCsvPerImportCrm($dati_riga[3], "Data", false);
    $matricola_impianto = $dati_riga[4];

    $campo_agg_imp1 = $dati_riga[5];
    $campo_agg_imp2 = $dati_riga[6];
    $campo_agg_imp3 = $dati_riga[7];
    $campo_agg_imp4 = $dati_riga[8];
    
    $q_verifica_impianto = "SELECT imp.impiantiid
                        FROM {$table_prefix}_impianti imp
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = imp.impiantiid
                        WHERE ent.deleted = 0 
                        AND imp.azienda = " . $id_crm_azienda . "
                        AND imp.stabilimento = " . $id_crm_stabilimento . "
                        AND imp.impianto_name LIKE '" . $nome_impianto_controllo . "'";

    $res_verifica_impianto = $adb->query($q_verifica_impianto);    
    if ($adb->num_rows($res_verifica_impianto) == 0) {
        $impianto = CRMEntity::getInstance('Impianti');
        $impianto->column_fields['impianto_name'] = $nome_impianto;
        $impianto->column_fields['matricola_impianto'] = $matricola_impianto;
        $impianto->column_fields['azienda'] = $id_crm_azienda;
        $impianto->column_fields['stabilimento'] = $id_crm_stabilimento;
        $impianto->column_fields['data_attivazione_imp'] = $data_attivazione_impianto;
        $impianto->column_fields['stato_impianto'] = 'Attivo';
        $impianto->column_fields['assigned_user_id'] = 1;
        $impianto->save('Impianti', $longdesc=true, $offline_update=false, $triggerEvent=false);
        
        $new_impianto = $impianto->id;

        $GLOBALS['impianti_creati']++;
    } else {
        $new_impianto = $adb->query_result($res_verifica_impianto, 0, 'impiantiid');
        $new_impianto = html_entity_decode(strip_tags($new_impianto), ENT_QUOTES, $default_charset);
    }

    if ($new_impianto == 0 || $new_impianto == '' || $new_impianto == null) {
        $new_impianto = 0;
    }

    return $new_impianto;
}

function ImportComponenti($dati_riga, $id_crm_impianto) {
    global $adb, $table_prefix, $default_charset;

    $nome_componente = $dati_riga[9];
    $nome_componente_controllo = addslashes($nome_componente);
    $matricola_componente = $dati_riga[10];
    $locazione_componente = $dati_riga[11];

    $campo_agg_comp1 = $dati_riga[12];
    $campo_agg_comp2 = $dati_riga[13];
    $campo_agg_comp3 = $dati_riga[14];
    $campo_agg_comp4 = $dati_riga[15];

    $q_verifica_componente = "SELECT comp.compimpiantoid
                        FROM {$table_prefix}_compimpianto comp
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = comp.compimpiantoid
                        WHERE ent.deleted = 0 
                        AND comp.impianto = ".$id_crm_impianto."
                        AND comp.nome_componente LIKE '".$nome_componente_controllo."'";

    $res_verifica_componente = $adb->query($q_verifica_componente);
    if($adb->num_rows($res_verifica_componente) == 0){
        $componente = CRMEntity::getInstance('CompImpianto');          
        $componente->column_fields['assigned_user_id'] = 1;
        $componente->column_fields['nome_componente'] = $nome_componente; 
        $componente->column_fields['matricola'] = $matricola_componente;
        $componente->column_fields['impianto'] = $id_crm_impianto; 
        $componente->column_fields['locazione'] = $locazione_componente;   
        $componente->column_fields['stato_componente'] = 'Attivo';        
        $componente->save('CompImpianto', $longdesc=true, $offline_update=false, $triggerEvent=false);

        $new_componente = $componente->id;

        ImportChecklists($new_componente, $dati_riga);

        return 1;
    }
    else{
        $id_componente = $adb->query_result($res_verifica_componente, 0, 'compimpiantoid');
        $id_componente = html_entity_decode(strip_tags($id_componente), ENT_QUOTES, $default_charset);

        $nome_componente = normalizzaStringaCsvPerImportCrm($nome_componente, "Testo", true);
        $matricola_componente = normalizzaStringaCsvPerImportCrm($matricola_componente, "Testo", true);
        $locazione_componente = normalizzaStringaCsvPerImportCrm($locazione_componente, "Testo", true);

        $q_update_componente = "UPDATE {$table_prefix}_compimpianto SET
                            matricola = '".$matricola_componente."',
                            locazione = '".$locazione_componente."'                          
                            WHERE compimpiantoid = ".$id_componente;
        $adb->query($q_update_componente);

        ImportChecklists($id_componente, $dati_riga);

        return 0;
    }
}

function ImportChecklists($id_componente, $dati_riga){
    global $adb, $table_prefix, $default_charset;

    $numero_colonna_partenza = 16;
    $numero_colonne_record = 4;
    $numero_record = 5;
    $i = 0;
    while($i < $numero_record){
        $numero_colonna = $numero_colonna_partenza + ($i * $numero_colonne_record);

        $codice_checklists = $dati_riga[$numero_colonna];
        $numero_colonna++;
        $nome_checklists = $dati_riga[$numero_colonna];
        $numero_colonna++;
        $tempo_checklists = $dati_riga[$numero_colonna];
        $numero_colonna++;
        $validita_checklists = $dati_riga[$numero_colonna];

        $id_checklists = CreaChecklists($codice_checklists, $nome_checklists, $tempo_checklists, $validita_checklists);
        if($id_checklists != 0){
            $res_relazione = RelazionaComponenteChecklist($id_checklists, $id_componente);
            if($res_relazione == 0 || $res_relazione == '0'){
                $report_finale = " 
    Errore nella creazione della relazione componente-checklist: ".$codice_checklists." - ".$id_componente." relazione gia presente";
                $handle_log_file=fopen($GLOBALS['path_logs'].$GLOBALS['error_logs_file_name'], "a+"); /* kpro@bid290820181730 */
                fwrite($handle_log_file, $report_finale);
                fclose($handle_log_file);
            }
        }
        else{
            $report_finale = " 
    Errore nella creazione della checklist: ".$codice_checklists;
            $handle_log_file=fopen($GLOBALS['path_logs'].$GLOBALS['error_logs_file_name'], "a+"); /* kpro@bid290820181730 */
            fwrite($handle_log_file, $report_finale);
            fclose($handle_log_file);
        }

        $i++;
    }

}

function CreaChecklists($codice_checklists, $nome_checklists, $tempo_checklists, $validita_checklists){
    global $adb, $table_prefix, $default_charset;

    $codice_validita_checklist = GetCodePicklistMultilinguaggio('frequenza_checklist', 'it_it', $validita_checklists);

    if($codice_checklists != "" && $codice_checklists != null && $nome_checklists != "" && $nome_checklists != null
        && $tempo_checklists != "" && $tempo_checklists != null && $tempo_checklists > 0){
            
        $q_controllo_checklist = "SELECT chk.checklistsid
                                FROM {$table_prefix}_checklists chk
                                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = chk.checklistsid
                                WHERE ent.deleted = 0 AND chk.cod_check_list LIKE '".$codice_checklists."'";
        $res_controllo_checklist = $adb->query($q_controllo_checklist);
        if($adb->num_rows($res_controllo_checklist) == 0){
            $checklist = CRMEntity::getInstance('CheckLists');          
            $checklist->column_fields['assigned_user_id'] = 1;
            $checklist->column_fields['cod_check_list'] = $codice_checklists; 
            $checklist->column_fields['nome_check_list'] = $nome_checklists; 
            $checklist->column_fields['lead_time'] = 1; 
            $checklist->column_fields['tempo_previsto'] = $tempo_checklists;
            if($codice_validita_checklist != ""){ 
                $checklist->column_fields['frequenza_checklist'] = $codice_validita_checklist; 
            }
            $checklist->save('CheckLists', $longdesc=true, $offline_update=false, $triggerEvent=false);

            $id_checklists = $checklist->id;

            $GLOBALS['checklists_create']++;
        }
        else{
            $id_checklists = $adb->query_result($res_controllo_checklist, 0, 'checklistsid');
            $id_checklists = html_entity_decode(strip_tags($id_checklists), ENT_QUOTES, $default_charset);
        }
    }
    else{
        $id_checklists = 0;
    }

    if ($id_checklists == 0 || $id_checklists == '' || $id_checklists == null) {
        $id_checklists = 0;
    }

    return $id_checklists;

}

function RelazionaComponenteChecklist($id_checklists, $id_componente){
    global $adb, $table_prefix, $default_charset;

    $q_verifica_relazione = "SELECT * 
                        FROM {$table_prefix}_crmentityrel entrel
                        WHERE (entrel.crmid = ".$id_checklists." AND entrel.relcrmid = ".$id_componente.")
                        OR (entrel.relcrmid = ".$id_checklists." AND entrel.crmid = ".$id_componente.")";
    $res_verifica_relazione = $adb->query($q_verifica_relazione);
    if($adb->num_rows($res_verifica_relazione) == 0){

        $q_insert_relazione = "INSERT INTO {$table_prefix}_crmentityrel (crmid,module,relcrmid,relmodule)
                            VALUES (".$id_checklists.", 'CheckLists', ".$id_componente.", 'CompImpianto')";
        $adb->query($q_insert_relazione);

        $GLOBALS['relazioni_componente_checklist']++; 

        return 1;
    }
    else{
        return 0;
    }

}

function GetCodePicklistMultilinguaggio($field, $language, $valore){
    global $adb, $table_prefix, $default_charset;

    $codice_picklist = "";
    $q_picklist = "SELECT code
                FROM tbl_s_picklist_language
                WHERE field = '{$field}'
                AND language = '{$language}'
                AND (code LIKE '{$valore}' OR value LIKE '{$valore}')"; /* kpro@bid290820181815 */
    $res_picklist = $adb->query($q_picklist);
    if($adb->num_rows($res_picklist) > 0){
        $codice_picklist = $adb->query_result($res_picklist, 0, 'code');
        $codice_picklist = html_entity_decode(strip_tags($codice_picklist), ENT_QUOTES, $default_charset);
        if($codice_picklist == null){
            $codice_picklist = "";
        }
    }

    return $codice_picklist;
}

?>
