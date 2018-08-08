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
					sit.situazformazid situazformazid,
					tc.tipicorso_name tipicorso_name,
					sit.data_formazione data_formazione,
					sit.validita_formazione validita_formazione,
					sit.stato_formazione stato_formazione,
					sit.risorsa risorsa,
					sit.stabilimento stabilimento,
					sit.mansione mansione,
					sit.ore_previste ore_previste,
					sit.ore_effettuate ore_effettuate,
					cont.firstname firstname,
					cont.lastname lastname,
					stab.nome_stabilimento nome_stabilimento,
					man.mansione_name mansione_name,
					sit.mansione mansione,
					sit.tipo_corso tipo_corso
					from {$table_prefix}_situazformaz sit
					inner join {$table_prefix}_crmentity ent on ent.crmid = sit.situazformazid
					inner join {$table_prefix}_tipicorso tc on tc.tipicorsoid = sit.tipo_corso
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

$q_situazione .= " order by cont.lastname asc, cont.firstname asc, sit.mansione asc, sit.tipo_corso asc, sit.validita_formazione desc";

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
	switch($stato_formazione){
		case 'Non eseguita': $colore = 'red'; break;
		case 'Scaduta': $colore = 'red'; break;
		case 'Non eseguito corso base': $colore = 'red'; break;
		case 'Non eseguita formazione precedente': $colore = 'red'; break;
		case 'Eseguire entro': $colore = 'yellow'; break;
		case 'In scadenza': $colore = 'orange'; break;
		case 'Eseguita': $colore = 'green'; break;
		case 'Eseguito corso base': $colore = 'green'; break;
		case 'Valida senza scadenza': $colore = 'green'; break;
		case 'In corso di validita': $colore = 'green'; break;
		default: $colore = '';
	}
	
	$ore_previste = $adb->query_result($res_situazione, $i, 'ore_previste');
	$ore_previste = html_entity_decode(strip_tags($ore_previste), ENT_QUOTES,$default_charset);
	if($ore_previste == null || $ore_previste == ""){
		$ore_previste = 0;
	}
	
	$ore_effettuate = $adb->query_result($res_situazione, $i, 'ore_effettuate');
	$ore_effettuate = html_entity_decode(strip_tags($ore_effettuate), ENT_QUOTES,$default_charset);
	if($ore_effettuate == null || $ore_effettuate == ""){
		$ore_effettuate = 0;
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
	
	$tipo_corso[$i] = $adb->query_result($res_situazione, $i, 'tipo_corso');
	$tipo_corso[$i] = html_entity_decode(strip_tags($tipo_corso[$i]), ENT_QUOTES,$default_charset);
	
	if($i > 0 && $risorsa[$i] == $risorsa[$i - 1] && $mansione[$i] == $mansione[$i - 1] && $tipo_corso[$i] == $tipo_corso[$i - 1]){
		continue;
	}
	else{
		$rows[] = array('situazformazid' => $situazformazid,
						'tipicorso_name' => $tipicorso_name,
						'data_formazione' => $data_formazione,
						'validita_formazione' => $validita_formazione,
						'stato_formazione' => $stato_formazione,
						'ore_previste' => $ore_previste,
						'ore_effettuate' => $ore_effettuate,
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