<?php

/* kpro@tom190216 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 * @package manutenzioni
 * @version 1.0
 */

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $default_charset;

$rows = array();

if(isset($_GET['componente'])){
    $componente = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['componente']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $componente = substr($componente,0,100);

    $q_componente = "SELECT com.nome_componente nome_componente,
                        com.matricola matricola,
                        com.locazione locazione,
                        com.data_ult_manutenz data_ult_manutenz,
                        com.stato_componente stato_componente,
                        com.proprieta_componente proprieta_componente,
                        com.famiglia_componente famiglia_componente,
                        com.altezza altezza,
                        com.costruttore costruttore,
                        com.tel_costruttore tel_costruttore,
                        com.costo costo,
                        com.kw_installati kw_installati,
                        com.tensione tensione,
                        com.consumo_aria consumo_aria,
                        com.olio olio,
                        com.impianto impianto,
                        ven.vendorname vendorname,
                        imp.impianto_name impianto_name
                        FROM {$table_prefix}_compimpianto com
                        LEFT JOIN {$table_prefix}_vendor ven ON ven.vendorid = com.venditore 
                        INNER JOIN {$table_prefix}_impianti imp ON imp.impiantiid = com.impianto 
                        WHERE com.compimpiantoid = ".$componente;
    $res_componente = $adb->query($q_componente);
    if($adb->num_rows($res_componente)>0){
        $nome_componente = $adb->query_result($res_componente,0,'nome_componente');
        $nome_componente = html_entity_decode(strip_tags($nome_componente), ENT_QUOTES,$default_charset);

        $matricola = $adb->query_result($res_componente,0,'matricola');
        $matricola = html_entity_decode(strip_tags($matricola), ENT_QUOTES,$default_charset);

        $locazione = $adb->query_result($res_componente,0,'locazione');
        $locazione = html_entity_decode(strip_tags($locazione), ENT_QUOTES,$default_charset);

        $data_ult_manutenz = $adb->query_result($res_componente,0,'data_ult_manutenz');
        $data_ult_manutenz = html_entity_decode(strip_tags($data_ult_manutenz), ENT_QUOTES,$default_charset);
        if($data_ult_manutenz == null){
            $data_ult_manutenz = "";
        }

        $stato_componente = $adb->query_result($res_componente,0,'stato_componente');
        $stato_componente = html_entity_decode(strip_tags($stato_componente), ENT_QUOTES,$default_charset);

        $proprieta_componente = $adb->query_result($res_componente,0,'proprieta_componente');
        $proprieta_componente = html_entity_decode(strip_tags($proprieta_componente), ENT_QUOTES,$default_charset);

        $famiglia_componente = $adb->query_result($res_componente,0,'famiglia_componente');
        $famiglia_componente = html_entity_decode(strip_tags($famiglia_componente), ENT_QUOTES,$default_charset);

        $altezza = $adb->query_result($res_componente,0,'altezza');
        $altezza = html_entity_decode(strip_tags($altezza), ENT_QUOTES,$default_charset);

        $costruttore = $adb->query_result($res_componente,0,'costruttore');
        $costruttore = html_entity_decode(strip_tags($costruttore), ENT_QUOTES,$default_charset);

        $tel_costruttore = $adb->query_result($res_componente,0,'tel_costruttore');
        $tel_costruttore = html_entity_decode(strip_tags($tel_costruttore), ENT_QUOTES,$default_charset);

        $costo = $adb->query_result($res_componente,0,'costo');
        $costo = html_entity_decode(strip_tags($costo), ENT_QUOTES,$default_charset);

        $kw_installati = $adb->query_result($res_componente,0,'kw_installati');
        $kw_installati = html_entity_decode(strip_tags($kw_installati), ENT_QUOTES,$default_charset);

        $tensione = $adb->query_result($res_componente,0,'tensione');
        $tensione = html_entity_decode(strip_tags($tensione), ENT_QUOTES,$default_charset);

        $consumo_aria = $adb->query_result($res_componente,0,'consumo_aria');
        $consumo_aria = html_entity_decode(strip_tags($consumo_aria), ENT_QUOTES,$default_charset);

        $olio = $adb->query_result($res_componente,0,'olio');
        $olio = html_entity_decode(strip_tags($olio), ENT_QUOTES,$default_charset);
        
        $impianto = $adb->query_result($res_componente,0,'impianto');
        $impianto = html_entity_decode(strip_tags($impianto), ENT_QUOTES,$default_charset);
        
        $fermi_impianto = fermiImpianto($impianto);

        $vendorname = $adb->query_result($res_componente,0,'vendorname');
        $vendorname = html_entity_decode(strip_tags($vendorname), ENT_QUOTES,$default_charset);
        if($vendorname == NULL){
            $vendorname = "";
        }
        
        $impianto_name = $adb->query_result($res_componente,0,'impianto_name');
        $impianto_name = html_entity_decode(strip_tags($impianto_name), ENT_QUOTES,$default_charset);
        if($impianto_name == NULL){
            $impianto_name = "";
        }

        $rows[] = array('nome_componente' => $nome_componente,
                        'matricola' => $matricola,
                        'locazione' => $locazione,
                        'data_ult_manutenz' => $data_ult_manutenz,
                        'stato_componente' => $stato_componente,
                        'proprieta_componente' => $proprieta_componente,
                        'famiglia_componente' => $famiglia_componente,
                        'altezza' => $altezza,
                        'costruttore' => $costruttore,
                        'tel_costruttore' => $tel_costruttore,
                        'costo' => $costo,
                        'kw_installati' => $kw_installati,
                        'tensione' => $tensione,
                        'consumo_aria' => $consumo_aria,
                        'olio' => $olio,
                        'vendorname' => $vendorname,
                        'impianto' => $impianto,
                        'nome_impianto' => $impianto_name,
                        'stato_impianto' => $fermi_impianto['stato_impianto'],
                        'perc_fermo_imp' => $fermi_impianto['perc_fermo_imp_desc'],
                        'data_inizio_inv' => $fermi_impianto['data_inizio_inv'],
                        'ora_inizio' => $fermi_impianto['ora_inizio'],
                        'description' => $fermi_impianto['description']);

    }
	
}
							
$json = json_encode($rows);
print $json;

function fermiImpianto($impianto){
    global $adb, $table_prefix, $current_user, $default_charset;
    
    $result = ""; 
    
    $q_fermi_impianto = "SELECT 
                            ferm.fermiimpiantoid fermiimpiantoid,
                            ferm.nome_fermo_impianto nome_fermo_impianto,
                            ferm.kp_perc_fermo_imp kp_perc_fermo_imp,
                            ferm.kp_data_inizio kp_data_inizio,
                            ferm.kp_data_fine kp_data_fine,
                            ferm.kp_ora_inizio kp_ora_inizio,
                            ferm.kp_ora_fine kp_ora_fine,
                            ferm.description description
                            FROM {$table_prefix}_fermiimpianto ferm
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = ferm.fermiimpiantoid
                            WHERE ent.deleted = 0 AND ferm.kp_impianto = ".$impianto."
                            ORDER BY ferm.kp_data_inizio DESC, ferm.kp_ora_inizio DESC";
    $res_fermi_impianto = $adb->query($q_fermi_impianto);
    
    if($adb->num_rows($res_fermi_impianto) > 0){
        
        $fermiimpiantoid = $adb->query_result($res_fermi_impianto, 0, 'fermiimpiantoid');
        $fermiimpiantoid = html_entity_decode(strip_tags($fermiimpiantoid), ENT_QUOTES,$default_charset);
        
        $nome_fermo_impianto = $adb->query_result($res_fermi_impianto, 0, 'nome_fermo_impianto');
        $nome_fermo_impianto = html_entity_decode(strip_tags($nome_fermo_impianto), ENT_QUOTES,$default_charset);
        
        $perc_fermo_imp = $adb->query_result($res_fermi_impianto, 0, 'kp_perc_fermo_imp');
        $perc_fermo_imp = html_entity_decode(strip_tags($perc_fermo_imp), ENT_QUOTES,$default_charset);
        $perc_fermo_imp_desc = $perc_fermo_imp."%";
        
        $data_inizio = $adb->query_result($res_fermi_impianto, 0, 'kp_data_inizio');
        $data_inizio = html_entity_decode(strip_tags($data_inizio), ENT_QUOTES,$default_charset);
        if($data_inizio == null || $data_inizio == "1900-01-01"){
            $data_inizio = "";
        }
        else{
            list($anno,$mese,$giorno) = explode("-",$data_inizio);
            $data_inizio_inv = date("d/m/Y",mktime(0,0,0,$mese,$giorno,$anno));
        }
        
        $data_fine = $adb->query_result($res_fermi_impianto, 0, 'kp_data_fine');
        $data_fine = html_entity_decode(strip_tags($data_fine), ENT_QUOTES,$default_charset);
        if($data_fine == null || $data_fine == "1900-01-01"){
            $data_fine = "";
        }
        
        $ora_inizio = $adb->query_result($res_fermi_impianto, 0, 'kp_ora_inizio');
        $ora_inizio = html_entity_decode(strip_tags($ora_inizio), ENT_QUOTES,$default_charset);
        
        $ora_fine = $adb->query_result($res_fermi_impianto, 0, 'kp_ora_fine');
        $ora_fine = html_entity_decode(strip_tags($ora_fine), ENT_QUOTES,$default_charset);
        
        $description = $adb->query_result($res_fermi_impianto, 0, 'description');
        $description = html_entity_decode(strip_tags($description), ENT_QUOTES,$default_charset);
        
        if($data_fine == ""){
            $stato_impianto = "Fermo";
        }
        else{
            $stato_impianto = "In funzione";
        }
        
    }
    else{
        $fermiimpiantoid = 0;
        $nome_fermo_impianto = "";
        $perc_fermo_imp = "0";
        $perc_fermo_imp_desc = "0%";
        $data_inizio = "";
        $data_fine = "";
        $ora_inizio = "";
        $ora_fine = "";
        $description = "";
        $stato_impianto = "In funzione";
    }
    
    $result = array('fermiimpiantoid' => $fermiimpiantoid,
                    'nome_fermo_impianto' => $nome_fermo_impianto,
                    'perc_fermo_imp' => $perc_fermo_imp,
                    'perc_fermo_imp_desc' => $perc_fermo_imp_desc,
                    'data_inizio' => $data_inizio,
                    'data_inizio_inv' => $data_inizio_inv,
                    'data_fine' => $data_fine,
                    'ora_inizio' => $ora_inizio,
                    'ora_fine' => $ora_fine,
                    'description' => $description,
                    'stato_impianto' => $stato_impianto);
    
    return $result;
    
}
	
?>