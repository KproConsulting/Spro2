<?php

/* kpro@tom06042017 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

require_once("../Utility/Utility.php");

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
    
    $query= "SELECT 
				vendor_id
				FROM {$table_prefix}_contactdetails 
				WHERE contactid = ".$contact_id;
	$result_query = $adb->query($query);
			
	if($adb->num_rows($result_query) > 0){
		
		$fornitore = $adb->query_result($result_query, 0, 'vendor_id');
		$fornitore = html_entity_decode(strip_tags($fornitore), ENT_QUOTES, $default_charset);
		
	}
	else{
	    header("Location: login.php");
	}
	
}
else{
    header("Location: ../../login.php"); 
}

$rows = array();

if(isset($_GET['record'])){
	$record = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['record']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
	$record = substr($record, 0, 100);

	if(!verificaSeFornitoreAbilitatoPerTicket($record, $fornitore)){
		die;
	}

	if(isset($_GET['ore'])){
        $search_ore = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['ore']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
        $search_ore = substr($search_ore, 0, 255);
    }
    else{
        $search_ore = '';
	}
	
	if(isset($_GET['ore_eff'])){
        $search_ore_eff = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['ore_eff']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
        $search_ore_eff = substr($search_ore_eff, 0, 255);
    }
    else{
        $search_ore_eff = '';
    }

	if(isset($_GET['data'])){
        $search_data = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
        $search_data = substr($search_data, 0, 255);
    }
    else{
        $search_data = '';
    }

	if(isset($_GET['inviato'])){
        $search_inviato = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['inviato']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
        $search_inviato = substr($search_inviato, 0, 255);
    }
    else{
        $search_inviato = '';
    }

	if(isset($_GET['titolo'])){
        $search_titolo = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['titolo']), ENT_QUOTES, $default_charset)), ENT_QUOTES, $default_charset);
        $search_titolo = substr($search_titolo, 0, 255);
    }
    else{
        $search_titolo = '';
    }

	$query = "SELECT timec.kp_titolo titolo,
				timec.timecardsid interventoid,
				timec.workdate workdate, 
				timec.worktime worktime,
				timec.kp_ore_effettive ore_effettive,
				timec.kp_inviato_rap kp_inviato_rap,
				timec.description description,
				ent.smownerid assegnatario,
				us.last_name last_name,
				us.first_name first_name
				FROM {$table_prefix}_timecards timec
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = timec.timecardsid
				INNER JOIN {$table_prefix}_users us ON us.id = ent.smownerid
				WHERE ent.deleted = 0 AND timec.ticket_id = ".$record;

	if($search_data != ""){
		$query .= " AND timec.workdate = '".$search_data."'";
	}

	if($search_ore != ""){
		$query .= " AND timec.worktime LIKE '%".$search_ore."%'";
	}

	if($search_ore_eff != ""){
		$query .= " AND timec.kp_ore_effettive LIKE '%".$search_ore_eff."%'";
	}

	if($search_inviato == "Si"){
		$query .= " AND timec.kp_inviato_rap = '1'";
	}
	elseif($search_inviato == "No"){
		$query .= " AND (timec.kp_inviato_rap = '0' OR timec.kp_inviato_rap IS NULL)";
	}

	if($search_titolo != ""){
		$query .= " AND timec.kp_titolo LIKE '%".$search_titolo."%'";
	}

	$query .= " ORDER BY timec.workdate ASC, timec.timecardsid ASC";
	
	$result_query = $adb->query($query);
	$num_result = $adb->num_rows($result_query);

	for($i=0; $i < $num_result; $i++){

		$interventoid = $adb->query_result($result_query, $i, 'interventoid');
		$interventoid = html_entity_decode(strip_tags($interventoid), ENT_QUOTES, $default_charset);

		$inviato_rap = $adb->query_result($result_query, $i, 'kp_inviato_rap');
		$inviato_rap = html_entity_decode(strip_tags($inviato_rap), ENT_QUOTES, $default_charset);
		if($inviato_rap == "1"){
			$inviato_rap = "Sì";
		}
		else{
			$inviato_rap = "No";
		}

		$workdate = $adb->query_result($result_query, $i, 'workdate');
		$workdate = html_entity_decode(strip_tags($workdate), ENT_QUOTES, $default_charset);
		list($anno, $mese, $giorno) = explode("-", $workdate);

		$workdate_inv = new DateTime($workdate);
		$workdate_inv = $workdate_inv->format('d/m/Y');

		$worktime = $adb->query_result($result_query, $i, 'worktime');
		list($ore_intervento, $minuti_intervento) = explode(":", $worktime);

		$ore_effettive = $adb->query_result($result_query, $i, 'ore_effettive');

		$titolo = $adb->query_result($result_query, $i, 'titolo');
		$titolo = html_entity_decode(strip_tags($titolo), ENT_QUOTES, $default_charset);

		$rows[] = array('interventoid' => $interventoid,
						'workdate' => $workdate,
						'workdate_inv' => $workdate_inv,
						'worktime' => $worktime,
						'ore_effettive' => $ore_effettive,
						'inviato_rap' => $inviato_rap,
						'titolo' => $titolo);

	}

}

$json = json_encode($rows);
print $json;
	
?>