<?php

/* kpro@tom17062016 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 */

include_once('../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_language;
session_start();

//print_r($_SESSION);die;

if(!isset($_SESSION['authenticated_user_id'])){
    header("Location: ".$site_URL."/index.php?visualization_type=resp_linea");
}
$current_user->id = $_SESSION['authenticated_user_id'];

$rows = array();

if(isset($_GET['risorsa'])){
    $risorsa = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['risorsa']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $risorsa = substr($risorsa,0,100);
    
    if(isset($_GET['tipo_corso'])){
        $tipo_corso = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['tipo_corso']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $tipo_corso = substr($tipo_corso,0,255);
    }
    else{
        $tipo_corso = '';
    }
    
    if(isset($_GET['data_ultima_formazione'])){
        $data_ultima_formazione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data_ultima_formazione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $data_ultima_formazione = substr($data_ultima_formazione,0,255);
        if($data_ultima_formazione != null && $data_ultima_formazione != ""){
			list($giorno, $mese, $anno) = explode("/", $data_ultima_formazione);
            $data_ultima_formazione = date("Y-m-d", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
    }
    else{
        $data_ultima_formazione = '';
    }
    
    if(isset($_GET['data_scadenza'])){
        $data_scadenza = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data_scadenza']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $data_scadenza = substr($data_scadenza,0,255);
        if($data_scadenza != null && $data_scadenza != ""){
			list($giorno, $mese, $anno) = explode("/", $data_scadenza);
            $data_scadenza = date("Y-m-d", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
    }
    else{
        $data_scadenza = '';
    }
    
    if(isset($_GET['stato_situzione'])){
        $stato_situzione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['stato_situzione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
		
		if($stato_situzione != ""){
			$stato_situzione = explode(',', $stato_situzione);
			
			$lista_stati = "";
				
			foreach($stato_situzione as $stato){
				if($lista_stati == ""){
					$lista_stati = "'".$stato."'";
				}
				else{
					$lista_stati .= ",'".$stato."'";
				}
			}
		}
		else{
			$lista_stati = "";
		}
		
    }
    else{
        $lista_stati = "";
    }
    
    $q_situazione = "select 
						sit.situazformazid situazformazid,
						tc.tipicorso_name tipicorso_name,
						sit.data_formazione data_formazione,
						sit.validita_formazione validita_formazione,
						sit.stato_formazione stato_formazione,
						sit.tipo_corso tipo_corso
						from {$table_prefix}_situazformaz sit
						inner join {$table_prefix}_crmentity ent on ent.crmid = sit.situazformazid
						inner join {$table_prefix}_tipicorso tc on tc.tipicorsoid = sit.tipo_corso
						where ent.deleted = 0 and sit.risorsa = ".$risorsa;
					
	if($tipo_corso != ""){
		$q_situazione .= " and tc.tipicorso_name like '%".$tipo_corso."%'";
	}
	if($lista_stati != ""){
		$q_situazione .= " and sit.stato_formazione in (".$lista_stati.")";
	}
	if($data_ultima_formazione != ""){
		$q_situazione .= " and sit.data_formazione = '".$data_ultima_formazione."'";
	}
	if($data_scadenza != ""){
		$q_situazione .= " and sit.validita_formazione = '".$data_scadenza."'";
	}
	
	$q_situazione .= " order by sit.tipo_corso asc, sit.validita_formazione desc";

	$res_situazione = $adb->query($q_situazione);
	$num_situazione = $adb->num_rows($res_situazione);
	
	for($i=0; $i < $num_situazione; $i++){
		
		$situazformazid = $adb->query_result($res_situazione, $i, 'situazformazid');
		$situazformazid = html_entity_decode(strip_tags($situazformazid), ENT_QUOTES,$default_charset);
		
		$tipicorso_name = $adb->query_result($res_situazione, $i, 'tipicorso_name');
		$tipicorso_name = html_entity_decode(strip_tags($tipicorso_name), ENT_QUOTES,$default_charset);
		
		$data_formazione = $adb->query_result($res_situazione, $i, 'data_formazione');
		$data_formazione = html_entity_decode(strip_tags($data_formazione), ENT_QUOTES,$default_charset);
		if($data_formazione != null && $data_formazione != "" && $data_formazione != "0000-00-00"){
			list($anno, $mese, $giorno) = explode("-", $data_formazione);
            $data_formazione = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
		else{
			$data_formazione = "";
		}
		
		$validita_formazione = $adb->query_result($res_situazione, $i, 'validita_formazione');
		$validita_formazione = html_entity_decode(strip_tags($validita_formazione), ENT_QUOTES,$default_charset);
		if($validita_formazione != null && $validita_formazione != "" && $validita_formazione != "0000-00-00"){
			list($anno, $mese, $giorno) = explode("-", $validita_formazione);
            $validita_formazione = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
		else{
			$validita_formazione = "";
		}
		
		$stato_formazione = $adb->query_result($res_situazione, $i, 'stato_formazione');
		$stato_formazione = html_entity_decode(strip_tags($stato_formazione), ENT_QUOTES,$default_charset);
		
		$tipo_corso[$i] = $adb->query_result($res_situazione, $i, 'tipo_corso');
		$tipo_corso[$i] = html_entity_decode(strip_tags($tipo_corso[$i]), ENT_QUOTES,$default_charset);
		
		if($i > 0 && $tipo_corso[$i] == $tipo_corso[$i - 1]){
			continue;
		}  
		else{
			$rows[] = array('situazformazid' => $situazformazid,
							'tipicorso_name' => $tipicorso_name,
							'data_formazione' => $data_formazione,
							'validita_formazione' => $validita_formazione,
							'stato_formazione' => $stato_formazione);
		}
		
	}
	
	$json = json_encode($rows);
	print $json;
	
}
?>
