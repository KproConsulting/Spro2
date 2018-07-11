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
        AND dm.kp_tipo_risposta LIKE '%testo%'
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

        $descrizione = $adb->query_result($result_query, $i, 'description');
        $descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES, $default_charset);
        if($descrizione == null){
            $descrizione = "";
        }

        $rows[] = array(
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