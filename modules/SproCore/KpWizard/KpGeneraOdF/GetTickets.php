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

if(isset($_GET['mese'])){
    $search_mese= addslashes(html_entity_decode(strip_tags($_GET['mese']), ENT_QUOTES, $default_charset));
    $search_mese = substr($search_mese, 0, 100);
}
else{
    $search_mese = 0;
}

if(isset($_GET['anno'])){
    $search_anno = addslashes(html_entity_decode(strip_tags($_GET['anno']), ENT_QUOTES, $default_charset));
    $search_anno = substr($search_anno, 0, 255);
}
else{
    $search_anno = 0;
}

if(isset($_GET['nome'])){
    $search_nome = addslashes(html_entity_decode(strip_tags($_GET['nome']), ENT_QUOTES, $default_charset));
    $search_nome = substr($search_nome, 0, 255);
}
else{
    $search_nome = '';
}

if(isset($_GET['azienda'])){
    $search_azienda = addslashes(html_entity_decode(strip_tags($_GET['azienda']), ENT_QUOTES, $default_charset));
    $search_azienda = substr($search_azienda, 0, 255);
}
else{
    $search_azienda = '';
}

if(isset($_GET['servizio'])){
    $search_servizio = addslashes(html_entity_decode(strip_tags($_GET['servizio']), ENT_QUOTES, $default_charset));
    $search_servizio = substr($search_servizio, 0, 255);
}
else{
    $search_servizio = '';
}

/* kpro@bid040920181110 aggiunto campo ore nel select della query */
$query = "SELECT tick.ticketid id,
        tick.title nome,
        tick.hours ore,
        acc.accountid,
        acc.accountname azienda,
        ser.serviceid,
        ser.servicename servizio,
        tick.total_notaxes prezzo
        FROM {$table_prefix}_troubletickets tick
        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
        LEFT JOIN {$table_prefix}_account acc ON acc.accountid = tick.parent_id
        LEFT JOIN {$table_prefix}_service ser ON ser.serviceid = tick.servizio
        WHERE ent.deleted = 0 AND tick.da_fatturare = '1'
        AND tick.status = 'Closed'
        AND MONTH(tick.data_esecuzione) = {$search_mese}
        AND YEAR(tick.data_esecuzione) = {$search_anno}";
if($search_nome != ""){
    $query .= " AND tick.title LIKE '%{$search_nome}%'";
}
if($search_azienda != ""){
    $query .= " AND acc.accountname LIKE '%{$search_azienda}%'";
}
if($search_servizio != ""){
    $query .= " AND ser.servicename LIKE '%{$search_servizio}%'";
}
$query .= " ORDER BY tick.title ASC";
$result_query = $adb->query($query);
$num_result = $adb->num_rows($result_query);
if($num_result > 0){
    for($i = 0; $i < $num_result; $i++){

        $id = $adb->query_result($result_query, $i, 'id');
        $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

        $nome = $adb->query_result($result_query, $i, 'nome');
        $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

        $id_azienda = $adb->query_result($result_query, $i, 'accountid');
        $id_azienda = html_entity_decode(strip_tags($id_azienda), ENT_QUOTES, $default_charset);
        if($id_azienda == '' || $id_azienda == null){
            $id_azienda = 0;
        }

        $azienda = $adb->query_result($result_query, $i, 'azienda');
        $azienda = html_entity_decode(strip_tags($azienda), ENT_QUOTES, $default_charset);
        if($azienda == null){
            $azienda = "";
        }

        $id_servizio = $adb->query_result($result_query, $i, 'serviceid');
        $id_servizio = html_entity_decode(strip_tags($id_servizio), ENT_QUOTES, $default_charset);
        if($id_servizio == '' || $id_servizio == null){
            $id_servizio = 0;
        }

        $servizio = $adb->query_result($result_query, $i, 'servizio');
        $servizio = html_entity_decode(strip_tags($servizio), ENT_QUOTES, $default_charset);
        if($servizio == null){
            $servizio = "";
        }

        $prezzo = $adb->query_result($result_query, $i, 'prezzo');
        $prezzo = html_entity_decode(strip_tags($prezzo), ENT_QUOTES, $default_charset);
        if($prezzo == '' || $prezzo == null){
            $prezzo = 0;
        }
        /* kpro@bid040920181110 */
        $ore = $adb->query_result($result_query, $i, 'ore');
        $ore = html_entity_decode(strip_tags($ore), ENT_QUOTES, $default_charset);
        if($ore == '' || $ore == null){
            $ore = 0;
        }
        $ore = (float)$ore;
        /* kpro@bid040920181110 end */

        $errore = ControlloTicket($id, $id_azienda, $id_servizio, $prezzo);

        $prezzo = number_format($prezzo, 2, ',', '.');
        $ore = number_format($ore, 2, ',', '.'); /* kpro@bid040920181110 */
    
        /* kpro@bid040920181110 */
        $rows[] = array(
            "id" => $id,
            "nome" => $nome,
            "azienda" => $azienda,
            "servizio" => $servizio,
            "prezzo" => $prezzo,
            "ore" => $ore,
            "errore" => $errore
        );
        /* kpro@bid040920181110 end */
    }
}

$json = json_encode($rows);
print $json;

function ControlloTicket($record, $id_azienda, $id_servizio, $prezzo){
    global $adb, $table_prefix, $default_charset;

    $errore = "";

    if($id_azienda != 0){

        if($id_servizio != 0){

            if($prezzo != 0){

                $q_verifica_es = "SELECT odfid FROM {$table_prefix}_odf
                                INNER JOIN {$table_prefix}_crmentity ON crmid = odfid
                                WHERE deleted = 0 AND related_to = ".$record;
                $res_verifica_es = $adb->query($q_verifica_es);
                if($adb->num_rows($res_verifica_es) > 0){
                    $errore = "OdF già generato!";
                }
            }
            else{
                $errore = "Prezzo non valido!";
            }
        }
        else{
            $errore = "Nessun servizio collegato!";
        }
    }
    else{
        $errore = "Nessuna azienda collegata!";
    }
    
    return $errore;
}

?>