<?php

/* kpro@bid15122016 */

/**
 * @author Bidese Jacopo
 * @copyright (c) 2016, Kpro Consulting Srl
 */
include_once('../../../../config.inc.php');  
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

if (!isset($_SESSION['authenticated_user_id'])) {
    header("Location: ".$site_URL."/index.php?visualization_type=resp_linea");
}

$rows = array();

if(isset($_GET['impianto'])){
    $impianto = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['impianto']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $impianto = substr($impianto,0,255);

    if(isset($_GET['nome'])){
        $nome = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['nome']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $nome = substr($nome,0,255);
    }
    else{
        $nome = "";
    }

    if(isset($_GET['matricola'])){
        $matricola = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['matricola']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $matricola = substr($matricola,0,255);
    }
    else{
        $matricola = "";
    }

    if(isset($_GET['locazione'])){
        $locazione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['locazione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $locazione = substr($locazione,0,255);
    }
    else{
        $locazione = "";
    }

    $q_componente = "SELECT comp.compimpiantoid,
                    comp.nome_componente,
                    comp.matricola,
                    comp.locazione,
                    comp.description
                    FROM {$table_prefix}_compimpianto comp
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = comp.compimpiantoid
                    WHERE ent.deleted = 0 AND comp.impianto = ".$impianto;

    if($nome != ""){
        $q_componente .= " AND comp.nome_componente LIKE '%".$nome."%'";
    }    
    if($matricola != ""){
        $q_componente .= " AND comp.matricola LIKE '%".$matricola."%'";
    }
    if($locazione != ""){
        $q_componente .= " AND comp.locazione LIKE '%".$locazione."%'";
    }
                
    $res_componenti = $adb->query($q_componente);
    $num_componenti = $adb->num_rows($res_componenti);

    if($num_componenti > 0){
        for ($i = 0; $i < $num_componenti; $i++) {
            $id_componente = $adb->query_result($res_componenti, $i, 'compimpiantoid');
            $id_componente = html_entity_decode(strip_tags($id_componente), ENT_QUOTES, $default_charset);

            $nome_componente = $adb->query_result($res_componenti, $i, 'nome_componente');
            $nome_componente = html_entity_decode(strip_tags($nome_componente), ENT_QUOTES, $default_charset);

            $matricola_componente = $adb->query_result($res_componenti, $i, 'matricola');
            $matricola_componente = html_entity_decode(strip_tags($matricola_componente), ENT_QUOTES, $default_charset);

            $locazione_componente = $adb->query_result($res_componenti, $i, 'locazione');
            $locazione_componente = html_entity_decode(strip_tags($locazione_componente), ENT_QUOTES, $default_charset);

            $descrizione_componente = $adb->query_result($res_componenti, $i, 'description');
            $descrizione_componente = html_entity_decode(strip_tags($descrizione_componente), ENT_QUOTES, $default_charset);

            $rows[] = array(
                "id" => $id_componente,
                "nome" => $nome_componente,
                "matricola" => $matricola_componente,
                "locazione" => $locazione_componente,
                "descrizione" => $descrizione_componente
            );
        }
    }
}

$json = json_encode($rows);
print $json;
?>