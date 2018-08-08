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
	$risorsa = substr($risorsa,0,255);
}
else{
	$risorsa = '';
}

if(isset($_GET['mansione'])){
	$mansione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['mansione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$mansione = substr($mansione,0,255);
}
else{
	$mansione = '';
}

if(isset($_GET['stabilimento'])){
	$stabilimento = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['stabilimento']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$stabilimento = substr($stabilimento,0,255);
}
else{
	$stabilimento = '';
}

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

if(isset($_GET['stato'])){
	$stato_situzione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['stato']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	
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

$q_situazione = "SELECT 
					sit.situazvisitemedid situazvisitemedid,
					tv.tipivisitamed_name tipivisitamed_name,
					sit.data_visita data_visita,
					sit.validita_visita validita_visita,
					sit.stato_sit_visita stato_sit_visita,
					cont.firstname firstname,
					cont.lastname lastname,
					stab.nome_stabilimento nome_stabilimento,
					man.mansione_name mansione_name,
					sit.risorsa risorsa,
					sit.mansione mansione,
					sit.tipo_visita tipo_visita
					from {$table_prefix}_situazvisitemed sit
					inner join {$table_prefix}_crmentity ent on ent.crmid = sit.situazvisitemedid
					inner join {$table_prefix}_tipivisitamed tv on tv.tipivisitamedid = sit.tipo_visita
					inner join {$table_prefix}_mansioni man on man.mansioniid = sit.mansione
					inner join {$table_prefix}_contactdetails cont on cont.contactid = sit.risorsa
					left join {$table_prefix}_stabilimenti stab on stab.stabilimentiid = sit.stabilimento
					where ent.deleted = 0 and sit.azienda = ".$azienda;

if($risorsa != ""){
	$q_situazione .= " and CONCAT(cont.lastname, ' ', cont.firstname) like '%".$risorsa."%'";
}
if($mansione != ""){
	$q_situazione .= " and man.mansione_name like '%".$mansione."%'";
}
if($stabilimento != ""){
	$q_situazione .= " and stab.nome_stabilimento like '%".$stabilimento."%'";
}
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

$q_situazione .= " order by cont.lastname asc, cont.firstname asc, sit.mansione asc, sit.tipo_visita asc, sit.validita_visita desc";

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
	
	$firstname = $adb->query_result($res_situazione, $i, 'firstname');
	$firstname = html_entity_decode(strip_tags($firstname), ENT_QUOTES,$default_charset);
	
	$lastname = $adb->query_result($res_situazione, $i, 'lastname');
	$lastname = html_entity_decode(strip_tags($lastname), ENT_QUOTES,$default_charset);
	
	$nome_stabilimento = $adb->query_result($res_situazione, $i, 'nome_stabilimento');
	$nome_stabilimento = html_entity_decode(strip_tags($nome_stabilimento), ENT_QUOTES,$default_charset);
	
	$mansione_name = $adb->query_result($res_situazione, $i, 'mansione_name');
	$mansione_name = html_entity_decode(strip_tags($mansione_name), ENT_QUOTES,$default_charset);
	
	$risorsa[$i] = $adb->query_result($res_situazione, $i, 'risorsa');
	$risorsa[$i] = html_entity_decode(strip_tags($risorsa[$i]), ENT_QUOTES,$default_charset);
	
	$mansione[$i] = $adb->query_result($res_situazione, $i, 'mansione');
	$mansione[$i] = html_entity_decode(strip_tags($mansione[$i]), ENT_QUOTES,$default_charset);
	
	$tipo_visita[$i] = $adb->query_result($res_situazione, $i, 'tipo_visita');
	$tipo_visita[$i] = html_entity_decode(strip_tags($tipo_visita[$i]), ENT_QUOTES,$default_charset);
	
	if($i > 0 && $risorsa[$i] == $risorsa[$i - 1] && $mansione[$i] == $mansione[$i - 1] && $tipo_visita[$i] == $tipo_visita[$i - 1]){
		continue;
	}
	else{
		$rows[] = array('situazvisitemedid' => $situazvisitemedid,
						'tipivisitamed_name' => $tipivisitamed_name,
						'data_visita' => $data_visita,
						'validita_visita' => $validita_visita,
						'stato_sit_visita' => $stato_sit_visita,
						'firstname' => $firstname,
						'lastname' => $lastname,
						'nome_stabilimento' => $nome_stabilimento,
						'mansione_name' => $mansione_name,
						'colore' => $colore);
	}
		
}
	
$json = json_encode($rows);
print $json;
	
?>