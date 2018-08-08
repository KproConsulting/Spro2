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

if(isset($_GET['formazione'])){
    $formazione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['formazione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $formazione = substr($formazione,0,100);
    
    if(isset($_GET['cognome'])){
        $cognome = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['cognome']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $cognome = substr($cognome,0,255);
    }
    else{
        $cognome = '';
    }
    
    if(isset($_GET['nome'])){
        $nome = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['nome']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $nome = substr($nome,0,255);
    }
    else{
        $nome = '';
    }
    
    if(isset($_GET['data_ultima_formazione'])){
        $data_ultima_formazione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data_ultima_formazione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $data_ultima_formazione = substr($data_ultima_formazione,0,255);
    }
    else{
        $data_ultima_formazione = '';
    }
    
    if(isset($_GET['data_scadenza'])){
        $data_scadenza = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data_scadenza']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $data_scadenza = substr($data_scadenza,0,255);
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
    
    $q_formazione = "select 
						form.kp_tipo_corso tipo_corso
						from {$table_prefix}_kpformazione form
						where form.kpformazioneid = ".$formazione;
	
	$res_formazione = $adb->query($q_formazione);
	if($adb->num_rows($res_formazione) > 0){
	
		$tipo_corso = $adb->query_result($res_formazione, 0, 'tipo_corso');
		$tipo_corso = html_entity_decode(strip_tags($tipo_corso), ENT_QUOTES,$default_charset);
		
	}
    
    $q_situazione = "select 
						sit.situazformazid situazformazid,
						tc.tipicorso_name tipicorso_name,
						sit.data_formazione data_formazione,
						sit.validita_formazione validita_formazione,
						sit.stato_formazione stato_formazione,
						sit.risorsa risorsa,
						cont.firstname firstname,
						cont.lastname lastname,
						sit.tipo_corso tipo_corso
						from {$table_prefix}_situazformaz sit
						inner join {$table_prefix}_crmentity ent on ent.crmid = sit.situazformazid
						inner join {$table_prefix}_tipicorso tc on tc.tipicorsoid = sit.tipo_corso
						inner join {$table_prefix}_contactdetails cont on cont.contactid = sit.risorsa
						where ent.deleted = 0 and tc.tipicorsoid = ".$tipo_corso." and sit.azienda = ".$azienda;
	
	if($cognome != ""){
		$q_situazione .= " and cont.lastname like '%".$cognome."%'";
	}
	if($nome != ""){
		$q_situazione .= " and cont.firstname like '%".$nome."%'";
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
	
	$q_situazione .= " order by cont.lastname asc, cont.firstname asc, sit.tipo_corso asc, sit.validita_formazione desc";
	
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
		
		$firstname = $adb->query_result($res_situazione, $i, 'firstname');
		$firstname = html_entity_decode(strip_tags($firstname), ENT_QUOTES,$default_charset);
		
		$lastname = $adb->query_result($res_situazione, $i, 'lastname');
		$lastname = html_entity_decode(strip_tags($lastname), ENT_QUOTES,$default_charset);
		
		$risorsa[$i] = $adb->query_result($res_situazione, $i, 'risorsa');
		$risorsa[$i] = html_entity_decode(strip_tags($risorsa[$i]), ENT_QUOTES,$default_charset);
		
		$tipo_corso[$i] = $adb->query_result($res_situazione, $i, 'tipo_corso');
		$tipo_corso[$i] = html_entity_decode(strip_tags($tipo_corso[$i]), ENT_QUOTES,$default_charset);
		
		$q_iscritto = "select 
						cont.contactid contactid
						from {$table_prefix}_kppartecipformaz part
						inner join {$table_prefix}_contactdetails cont on cont.contactid = part.kp_risorsa
						inner join {$table_prefix}_crmentity ent on ent.crmid = part.kppartecipformazid
						where ent.deleted = 0 and part.kp_formazione = ".$formazione." and cont.contactid = ".$risorsa[$i];
		$res_iscritto = $adb->query($q_iscritto);
		if($adb->num_rows($res_iscritto) > 0){
			
			$gia_iscritto = "true";
			
		}
		else{
			
			$gia_iscritto = "false";
			
		}
		
		if($i > 0 && $risorsa[$i] == $risorsa[$i - 1] && $tipo_corso[$i] == $tipo_corso[$i - 1]){
			continue;
		}
		else{
			$rows[] = array('situazformazid' => $situazformazid,
							'tipicorso_name' => $tipicorso_name,
							'data_formazione' => $data_formazione,
							'validita_formazione' => $validita_formazione,
							'validita_formazione' => $validita_formazione,
							'risorsa' => $risorsa[$i],
							'firstname' => $firstname,
							'lastname' => $lastname,
							'stato_formazione' => $stato_formazione,
							'gia_iscritto' => $gia_iscritto);
		}
		
	}
	
	$json = json_encode($rows);
	print $json;
	
}
?>
