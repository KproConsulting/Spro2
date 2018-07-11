<?php

/* kpro@tom25112015 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2015, Kpro Consulting Srl
 * @package portaleCustomTeam
 * @version 1.0
 */

include_once('../../../../config.inc.php'); 
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

if (!isset($_SESSION['authenticated_user_id'])) {
    header("Location: " . $site_URL . "/index.php?module=Accounts&action=index");
}
else{
    $utente = $_SESSION['authenticated_user_id'];
}

$rows = array(); 

if(isset($_GET['id']) && isset($_GET['variazione'])){
    
    $id = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['id']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $id = substr($id,0,255);

    $variazione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['variazione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $variazione = substr($variazione,0,255);

    if($variazione == 'creazione'){    
        if(isset($_GET['data']) && isset($_GET['risorsa']) && isset($_GET['azienda']) 
            && isset($_GET['stabilimento']) && isset($_GET['tipo_consegna']) && isset($_GET['descrizione'])){
                
            $data = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
            $data = substr($data,0,255);
            
            $risorsa = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['risorsa']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
            $risorsa = substr($risorsa,0,255);
            
            $azienda = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['azienda']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
            $azienda = substr($azienda,0,255);
            
            $stabilimento = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['stabilimento']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
            $stabilimento = substr($stabilimento,0,255);
            
            $descrizione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['descrizione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
            $descrizione = substr($descrizione,0,255); 
            if($descrizione == "" || $descrizione == null){
                $descrizione = " ";
            }

            $tipo_consegna = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['tipo_consegna']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
            $tipo_consegna = substr($tipo_consegna,0,255);
            
            if($id == "new"){
                $dpi = CRMEntity::getInstance('ConsegnaDPI');  
                $dpi->column_fields['data_consegna'] = $data; 
                $dpi->column_fields['assigned_user_id'] = $utente;
                $dpi->column_fields['contatto'] = $risorsa;
                $dpi->column_fields['azienda'] = $azienda;
                $dpi->column_fields['stabilimento'] = $stabilimento;
                $dpi->column_fields['tipo_consegna'] = $tipo_consegna;	
                $dpi->column_fields['stato_consegna'] = "Non Confermata";	
                $dpi->column_fields['description'] = $descrizione;     
                $dpi->save('ConsegnaDPI', $longdesc=true, $offline_update=false, $triggerEvent=false);
                
                $new_dpi = $dpi->id; 

                UpdateCreator($new_dpi, $utente);
                
                $rows[] = array("id" => $new_dpi);
            }
            else{
                $q_check = "SELECT *
                        FROM {$table_prefix}_consegnadpi
                        INNER JOIN {$table_prefix}_crmentity ON crmid = consegnadpiid
                        WHERE deleted = 0 AND consegnadpiid = ".$id;        
                $res_check = $adb->query($q_check);
                if($adb->num_rows($res_check) > 0){           
                    
                    $descrizione = addslashes($descrizione);

                    $q_update = "UPDATE {$table_prefix}_consegnadpi SET
                            data_consegna = '".$data."',
                            contatto = ".$risorsa.",
                            azienda = ".$azienda.",
                            stabilimento = ".$stabilimento.",
                            tipo_consegna = '".$tipo_consegna."',
                            description = '".$descrizione."'
                            WHERE consegnadpiid = ".$id;
                    $adb->query($q_update);
                    
                    $q_get_righe = "SELECT listadpiid
                                FROM {$table_prefix}_listadpi
                                INNER JOIN {$table_prefix}_crmentity ON crmid = listadpiid
                                WHERE deleted = 0 AND consegnadpi = ".$id;
                    $res_get_righe = $adb->query($q_get_righe);
                    $num_get_righe = $adb->num_rows($res_get_righe);
                    if($num_get_righe > 0){
                        for($i = 0; $i < $num_get_righe; $i++){
                            $id_riga = $adb->query_result($res_get_righe, $i, 'listadpiid');
                            $id_riga = html_entity_decode(strip_tags($id_riga), ENT_QUOTES, $default_charset);
                            
                            $q_upd_riga = "UPDATE {$table_prefix}_listadpi SET
                                    contatto = ".$risorsa.",
                                    azienda = ".$azienda.",
                                    stabilimento = ".$stabilimento."
                                    WHERE listadpiid = ".$id_riga;
                            $adb->query($q_upd_riga);
                        }
                    }
                    
                    $rows[] = array("id" => $id);
                }        
            }
        }
    }
    elseif($variazione == 'descrizione'){
        if(isset($_GET['descrizione'])){
            $descrizione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['descrizione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
            $descrizione = substr($descrizione,0,255); 
            if($descrizione == "" || $descrizione == null){
                $descrizione = " ";
            }
            if($id != "new"){
                $descrizione = addslashes($descrizione);

                $q_update2 = "UPDATE {$table_prefix}_consegnadpi SET
                        description = '".$descrizione."'
                        WHERE consegnadpiid = ".$id;
                $adb->query($q_update2);

                $rows[] = array("id" => $id);
            }
        }
    }
    elseif ($variazione == 'tipo_consegna') {
        if(isset($_GET['tipo_consegna'])){
            $tipo_consegna = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['tipo_consegna']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
            $tipo_consegna = substr($tipo_consegna,0,255);

            if($id != "new"){
                $tipo_consegna = addslashes($tipo_consegna);

                $q_update = "UPDATE {$table_prefix}_consegnadpi SET
                        tipo_consegna = '".$tipo_consegna."'
                        WHERE consegnadpiid = ".$id;
                $adb->query($q_update);

                $rows[] = array("id" => $id);
            }
        }
    }
}

$json = json_encode($rows);
print $json;

function UpdateCreator($record, $creator){
    global $adb, $table_prefix;

    if($record != 0 && $record != '' && $record != null){
        $update_creator = "UPDATE {$table_prefix}_crmentity 
                        SET smcreatorid = {$creator}
                        WHERE crmid = ".$record;
        $adb->query($update_creator);
    }
}

?>