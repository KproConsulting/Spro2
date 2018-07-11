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

if(isset($_GET['manutenzione']) && isset($_GET['componente'])){
    $manutenzione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['manutenzione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $manutenzione = substr($manutenzione,0,100);

    $componente = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['componente']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $componente = substr($componente,0,100);

    $q_tipo_verifica = "SELECT 
                        es.esitimanutenzioniid esitimanutenzioniid,
                        es.tipo_verifica tipo_verifica,
                        es.esito_manutenzione esito_manutenzione,
                        es.note_esito note_esito,
                        tp.nome_verifica nome_verifica,
                        tp.kp_fermo_impianto kp_fermo_impianto,
                        es.description descrizione_esito,
                        tp.description description
                        FROM {$table_prefix}_esitimanutenzioni es
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = es.esitimanutenzioniid
                        INNER JOIN {$table_prefix}_tipiverifiche tp ON tp.tipiverificheid = es.tipo_verifica
                        INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = tp.tipiverificheid
                        WHERE ent.deleted = 0 AND es.componente = ".$componente." AND es.manutenzione = ".$manutenzione;
    $res_tipo_verifica = $adb->query($q_tipo_verifica);
    $num_tipo_verifica = $adb->num_rows($res_tipo_verifica);

    for($i=0; $i<$num_tipo_verifica; $i++){
        $esitimanutenzioniid = $adb->query_result($res_tipo_verifica, $i, 'esitimanutenzioniid');
        $esitimanutenzioniid = html_entity_decode(strip_tags($esitimanutenzioniid), ENT_QUOTES,$default_charset);

        $tipo_verifica = $adb->query_result($res_tipo_verifica, $i, 'tipo_verifica');
        $tipo_verifica = html_entity_decode(strip_tags($tipo_verifica), ENT_QUOTES,$default_charset);

        $esito_manutenzione = $adb->query_result($res_tipo_verifica, $i, 'esito_manutenzione');
        $esito_manutenzione = html_entity_decode(strip_tags($esito_manutenzione), ENT_QUOTES,$default_charset);

        $note_esito = $adb->query_result($res_tipo_verifica, $i, 'note_esito');
        $note_esito = html_entity_decode(strip_tags($note_esito), ENT_QUOTES,$default_charset);

        $nome_verifica = $adb->query_result($res_tipo_verifica, $i, 'nome_verifica');
        $nome_verifica = html_entity_decode(strip_tags($nome_verifica), ENT_QUOTES,$default_charset);

        $descrizione_esito = $adb->query_result($res_tipo_verifica, $i, 'descrizione_esito');
        $descrizione_esito = html_entity_decode(strip_tags($descrizione_esito), ENT_QUOTES,$default_charset);
        
        $richiesto_fermo_impianto = $adb->query_result($res_tipo_verifica, $i, 'kp_fermo_impianto');
        $richiesto_fermo_impianto = html_entity_decode(strip_tags($richiesto_fermo_impianto), ENT_QUOTES,$default_charset);
        if($richiesto_fermo_impianto == null || $richiesto_fermo_impianto == 0){
            $richiesto_fermo_impianto = "no";
        }
        else{
            $richiesto_fermo_impianto = "si";
        }

        if($descrizione_esito != ""){
            $description = $descrizione_esito;
        }
        else{
            $description = $adb->query_result($res_tipo_verifica, $i, 'description');
            $description = html_entity_decode(strip_tags($description), ENT_QUOTES,$default_charset);
        }

        $q_foto = "SELECT COALESCE(COUNT(*), 0) numero_foto FROM {$table_prefix}_senotesrel senote
                    INNER JOIN {$table_prefix}_seattachmentsrel seattac ON seattac.crmid = senote.notesid
                    INNER JOIN {$table_prefix}_attachments attac ON attac.attachmentsid = seattac.attachmentsid
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = senote.notesid
                    WHERE ent.deleted = 0 AND senote.crmid =".$esitimanutenzioniid;
        $res_foto = $adb->query($q_foto);
        if($adb->num_rows($res_foto)>0){
            $numero_foto = $adb->query_result($res_foto,0,'numero_foto');
        }

        $q_documenti = "SELECT COALESCE(COUNT(*), 0) numero_documenti
                        FROM {$table_prefix}_senotesrel senote
                        LEFT JOIN {$table_prefix}_seattachmentsrel seattac ON seattac.crmid = senote.notesid
                        LEFT JOIN {$table_prefix}_attachments attac ON attac.attachmentsid = seattac.attachmentsid
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = senote.notesid
                        INNER JOIN {$table_prefix}_notes notes ON notes.notesid = senote.notesid
                        INNER JOIN {$table_prefix}_notescf notescf ON notescf.notesid = senote.notesid
                        WHERE ent.deleted = 0 AND attac.type NOT LIKE '%image%' AND senote.crmid =".$tipo_verifica."
                        ORDER BY title ASC";
        $res_documenti = $adb->query($q_documenti);
        if($adb->num_rows($res_documenti)>0){
            $numero_documenti = $adb->query_result($res_documenti,0,'numero_documenti');
        }

        $rows[] = array('esitimanutenzioniid' => $esitimanutenzioniid,
                        'tipo_verifica' => $tipo_verifica,
                        'esito_manutenzione' => $esito_manutenzione,
                        'note_esito' => $note_esito,
                        'nome_verifica' => $nome_verifica,
                        'description' => $description,
                        'numero_foto' => $numero_foto,
                        'numero_documenti' => $numero_documenti,
                        'richiesto_fermo_impianto' => $richiesto_fermo_impianto);

    }
	
}
					
$json = json_encode($rows);
print $json;
	
?>