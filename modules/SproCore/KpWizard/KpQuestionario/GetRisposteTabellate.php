<?php

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

$rows = array();

if (!isset($_SESSION['authenticated_user_id'])) {

    $json = json_encode($rows);
    print $json;
    die;

    header("Location: ". $site_URL."/index.php");
	die; 
}
$current_user->id = $_SESSION['authenticated_user_id'];

if(isset($_GET['record'])){
    $search_record = addslashes(html_entity_decode(strip_tags($_GET['record']), ENT_QUOTES, $default_charset));
    $search_record = substr($search_record, 0, 100);
}
else{
    $search_record = 0;
}

if(isset($_GET['modulo'])){
    $search_modulo = addslashes(html_entity_decode(strip_tags($_GET['modulo']), ENT_QUOTES, $default_charset));
    $search_modulo = substr($search_modulo, 0, 255);
}
else{
    $search_modulo = '';
}

if(isset($_GET['template'])){
    $search_template = addslashes(html_entity_decode(strip_tags($_GET['template']), ENT_QUOTES, $default_charset));
    $search_template = substr($search_template, 0, 255);
}
else{
    $search_template = 0;
}

$lista_tipi_risposte = array();

$query = "SELECT dmq.kpdomandequestionariid,
        dmq.kp_ordinamento,
        dm.kpdomandeid,
        dm.kp_domanda,
        dm.kp_tipo_risposta,
        dm.description
        FROM {$table_prefix}_kpdomandequestionari dmq
        INNER JOIN {$table_prefix}_kpdomande dm ON dm.kpdomandeid = dmq.kp_domanda
        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = dmq.kpdomandequestionariid
        INNER JOIN {$table_prefix}_crmentity ent1 ON ent1.crmid = dm.kpdomandeid
        WHERE ent.deleted = 0 AND ent1.deleted = 0
        AND dmq.kp_template_quest = {$search_template}
        AND dm.kp_tipo_risposta LIKE '%picklist%'
        ORDER BY dmq.kp_ordinamento";
$result_query = $adb->query($query);
$num_result = $adb->num_rows($result_query);
if($num_result > 0){
    for($i = 0; $i < $num_result; $i++){
        $id_domanda_questionario = $adb->query_result($result_query, $i, 'kpdomandequestionariid');
        $id_domanda_questionario = html_entity_decode(strip_tags($id_domanda_questionario), ENT_QUOTES, $default_charset);
        if($id_domanda_questionario == "" || $id_domanda_questionario == null){
            $id_domanda_questionario = 0;
        }

        $ordinamento = $adb->query_result($result_query, $i, 'kp_ordinamento');
        $ordinamento = html_entity_decode(strip_tags($ordinamento), ENT_QUOTES, $default_charset);
        if($ordinamento == "" || $ordinamento == null){
            $ordinamento = 0;
        }

        $id_domanda = $adb->query_result($result_query, $i, 'kpdomandeid');
        $id_domanda = html_entity_decode(strip_tags($id_domanda), ENT_QUOTES, $default_charset);
        if($id_domanda == "" || $id_domanda == null){
            $id_domanda = 0;
        }

        $domanda = $adb->query_result($result_query, $i, 'kp_domanda');
        $domanda = html_entity_decode(strip_tags($domanda), ENT_QUOTES, $default_charset);
        if($domanda == null){
            $domanda = "";
        }

        $tipo_risposta = $adb->query_result($result_query, $i, 'kp_tipo_risposta');
        $tipo_risposta = html_entity_decode(strip_tags($tipo_risposta), ENT_QUOTES, $default_charset);
        if($tipo_risposta == null){
            $tipo_risposta = "";
        }

        $descrizione = $adb->query_result($result_query, $i, 'description');
        $descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES, $default_charset);
        if($descrizione == null){
            $descrizione = "";
        }

        $tipo_risposta_gia_passato = false;
        
        if (empty($lista_tipi_risposte)) {
            $lista_tipi_risposte[] = $tipo_risposta;
        } else {
            if (in_array($tipo_risposta, $lista_tipi_risposte)) {
                $tipo_risposta_gia_passato = true;
            } else {
                $lista_tipi_risposte[] = $tipo_risposta;
            }
        }

        $cont = array_search($tipo_risposta, $lista_tipi_risposte);

        if(!$tipo_risposta_gia_passato && $tipo_risposta != ""){
            $array_tipo_risposta = explode(' ',$tipo_risposta);
            $nome_campo_risposta = 'kp_risposta_pick_'.$array_tipo_risposta[1];

            $query_picklist = "SELECT code, value
                            FROM tbl_s_picklist_language
                            WHERE language = 'it_it'
                            AND field = '{$nome_campo_risposta}'
                            ORDER BY CAST(code AS UNSIGNED)";
            $res_picklist = $adb->query($query_picklist);
            $num_picklist = $adb->num_rows($res_picklist);
            if($num_picklist > 0){
                for($j = 0; $j < $num_picklist; $j++){
                    $codice_risposta = $adb->query_result($res_picklist, $j, 'code');
                    $codice_risposta = html_entity_decode(strip_tags($codice_risposta), ENT_QUOTES, $default_charset);

                    $valore_risposta = $adb->query_result($res_picklist, $j, 'value');
                    $valore_risposta = html_entity_decode(strip_tags($valore_risposta), ENT_QUOTES, $default_charset);

                    if($codice_risposta != 0 && $codice_risposta != '0' && strtolower($codice_risposta) != '--nessuno--' && strtolower($codice_risposta) != 'nessuno'
                        && strtolower($valore_risposta) != '--nessuno--' && strtolower($valore_risposta) != 'nessuno'){

                        $rows[$cont]["risposte"][] = array(
                            "codice" => $codice_risposta,
                            "valore" => $valore_risposta
                        );
                    }
                }
            }
        }

        $rows[$cont]["domande"][] = array(
            "id_domanda_questionario" => $id_domanda_questionario,
            "id_domanda" => $id_domanda,
            "ordinamento" => $ordinamento,
            "domanda" => $domanda,
            "descrizione" => $descrizione
        );
    }
}

$json = json_encode($rows);
print $json;
?>