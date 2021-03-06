<?php

/* kpro@tom17062016 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 * @package portaleVteSicurezza
 * @version 1.0
 */

require_once("../../PortalConfig.php");
include_once('../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_language;
session_start();

//print_r($_SESSION);die;

$contact_id = 0;
$default_language = "it_it";
if(isset($_SESSION['customer_sessionid'])){
	
    $contact_id = $_SESSION['customer_id'];
    $customer_account_id = $_SESSION['customer_account_id'];
    $sessionid = $_SESSION['customer_sessionid'];
    $default_language = $_SESSION['portal_login_language'];
  
}
else{
	$contact_id = 0;
}

require_once($portal_name.'/string/'.$default_language.'.php');

if($contact_id != 0){
    
    $q_account = "SELECT accountid
					FROM {$table_prefix}_contactdetails 
					WHERE contactid = ".$contact_id;
	$res_account = $adb->query($q_account);
			
	if($adb->num_rows($res_account) > 0){
		
		$azienda = $adb->query_result($res_account, 0, 'accountid');
		$azienda = html_entity_decode(strip_tags($azienda), ENT_QUOTES,$default_charset);
		
	}
	else{
	    header("Location: ../../login.php"); 
	}
	
}
else{
    header("Location: ../../login.php"); 
}

$rows = array();

if(isset($_GET['risorsa'])){
    $risorsa = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['risorsa']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $risorsa = substr($risorsa,0,100);
    
    if(isset($_GET['tipo_visita'])){
        $tipo_visita = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['tipo_visita']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $tipo_visita = substr($tipo_visita,0,255);
    }
    else{
        $tipo_visita = '';
    }
    
    if(isset($_GET['data_ultima_visita'])){
        $data_ultima_visita = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data_ultima_visita']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $data_ultima_visita = substr($data_ultima_visita,0,255);
    }
    else{
        $data_ultima_visita = '';
    }
    
    if(isset($_GET['data_scadenza'])){
        $data_scadenza = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data_scadenza']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $data_scadenza = substr($data_scadenza,0,255);
    }
    else{
        $data_scadenza = '';
    }
    
    if(isset($_GET['stato_situazione'])){
        $stato_situazione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['stato_situazione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
		
		if($stato_situazione != ""){
			$stato_situazione = explode(',', $stato_situazione);
			
			$lista_stati = "";
				
			foreach($stato_situazione as $stato){
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
    
    $q_situazione = "SELECT 
						sit.situazvisitemedid situazvisitemedid,
						tv.tipivisitamed_name tipivisitamed_name,
						sit.data_visita data_visita,
						sit.validita_visita validita_visita,
						sit.stato_sit_visita stato_sit_visita,
						sit.tipo_visita tipo_visita
						from {$table_prefix}_situazvisitemed sit
						inner join {$table_prefix}_crmentity ent on ent.crmid = sit.situazvisitemedid
						inner join {$table_prefix}_tipivisitamed tv on tv.tipivisitamedid = sit.tipo_visita
						where ent.deleted = 0 and sit.risorsa = ".$risorsa;
					
	if($tipo_visita != ""){
		$q_situazione .= " and tv.tipivisitamed_name like '%".$tipo_visita."%'";
	}
	if($lista_stati != ""){
		$q_situazione .= " and sit.stato_sit_visita in (".$lista_stati.")";
	}
	if($data_ultima_visita != ""){
		$q_situazione .= " and sit.data_visita = '".$data_ultima_visita."'";
	}
	if($data_scadenza != ""){
		$q_situazione .= " and sit.validita_visita = '".$data_scadenza."'";
	}
	
	$q_situazione .= " order by sit.tipo_visita asc, sit.validita_visita desc";

	$res_situazione = $adb->query($q_situazione);
	$num_situazione = $adb->num_rows($res_situazione);
	
	for($i=0; $i < $num_situazione; $i++){
		
		$situazvisitemedid = $adb->query_result($res_situazione, $i, 'situazvisitemedid');
		$situazvisitemedid = html_entity_decode(strip_tags($situazvisitemedid), ENT_QUOTES,$default_charset);
		
		$tipivisitamed_name = $adb->query_result($res_situazione, $i, 'tipivisitamed_name');
		$tipivisitamed_name = html_entity_decode(strip_tags($tipivisitamed_name), ENT_QUOTES,$default_charset);
		
		$data_visita = $adb->query_result($res_situazione, $i, 'data_visita');
		$data_visita = html_entity_decode(strip_tags($data_visita), ENT_QUOTES,$default_charset);
		if($data_visita != null && $data_visita != "" && $data_visita != "0000-00-00"){
			list($anno, $mese, $giorno) = explode("-", $data_visita);
            $data_visita = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
		else{
			$data_visita = "";
		}
		
		$validita_visita = $adb->query_result($res_situazione, $i, 'validita_visita');
		$validita_visita = html_entity_decode(strip_tags($validita_visita), ENT_QUOTES,$default_charset);
		if($validita_visita != null && $validita_visita != "" && $validita_visita != "0000-00-00"){
			list($anno, $mese, $giorno) = explode("-", $validita_visita);
            $validita_visita = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
		else{
			$validita_visita = "";
		}
		
		$stato_sit_visita = $adb->query_result($res_situazione, $i, 'stato_sit_visita');
		$stato_sit_visita = html_entity_decode(strip_tags($stato_sit_visita), ENT_QUOTES,$default_charset);
		switch($stato_sit_visita){
			case 'Non eseguita': $colore = 'red'; break;
			case 'Scaduta': $colore = 'red'; break;
			case 'In scadenza': $colore = 'orange'; break;
			case 'Eseguita': $colore = 'green'; break;
			default: $colore = '';
		}
		
		$tipo_visita[$i] = $adb->query_result($res_situazione, $i, 'tipo_visita');
		$tipo_visita[$i] = html_entity_decode(strip_tags($tipo_visita[$i]), ENT_QUOTES,$default_charset);
		
		if($i > 0 && $tipo_visita[$i] == $tipo_visita[$i - 1]){
			continue;
		}  
		else{
			$rows[] = array('situazvisitemedid' => $situazvisitemedid,
							'tipivisitamed_name' => $tipivisitamed_name,
							'data_visita' => $data_visita,
							'validita_visita' => $validita_visita,
							'stato_sit_visita' => $stato_sit_visita,
							'colore' => $colore);
		}
		
	}
	
	$json = json_encode($rows);
	print $json;
	
}
?>