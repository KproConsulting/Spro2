<?php

/* kpro@tom140220170900 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

include_once('../../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');

require_once('modules/SDK/src/KpAndroidApp/CheckVteLogin.php');

require_once('modules/SDK/src/KpAndroidApp/Utils.php');

require_once('modules/SDK/src/KpAndroidApp/KpCalendario/KproConfig.ini.php');

$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset, $templateid, $language;
session_start();

$rows = array();

if(isset($_POST['username']) && isset($_POST['password']) && isset($_POST['userid'])){
	$username = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_POST['username']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$username = substr($username,0,255);
	
	$password = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_POST['password']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$password = substr($password,0,255);
	
	$userid = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_POST['userid']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$userid = substr($userid,0,100);
	
	/*require('user_privileges/requireUserPrivileges.php'); 
	require('user_privileges/sharing_privileges_'.$userid.'.php');
	
	$fieldlabel = 'Assigned To';
	global $noof_group_rows;

	$editview_label[]=getTranslatedString($fieldlabel, $module_name);

	//Security Checks
	if($fieldlabel == 'Assigned To' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
	{
		$result=get_current_user_access_groups($module_name);
	}
	else
	{
		$result = get_group_options();
	}
	if($result) $nameArray = $adb->fetch_array($result);

	if($value != '' && $value != 0)
		$assigned_user_id = $value;
	else
		$assigned_user_id = $userid;
	if($fieldlabel == 'Assigned To' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
	{
		$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id,'private'), $assigned_user_id);
	}
	else
	{
		$users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id), $assigned_user_id);
	}
	if($noof_group_rows!=0)
	{
		$groups_combo = '';
		if($fieldlabel == 'Assigned To' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
		{
			$groups_combo = get_select_options_array(get_group_array(FALSE, "Active", $assigned_user_id,'private'), $assigned_user_id);
		}
		else
		{
			$groups_combo = get_select_options_array(get_group_array(FALSE, "Active", $assigned_user_id), $assigned_user_id);
		}
	}

	$fieldvalue[]= $users_combo;
	$fieldvalue_group[] = $groups_combo;

	$myArray = $fieldvalue[0];
	$keys = array_keys($myArray);

	if($groups_combo != ''){
		$myArray_group = $fieldvalue_group[0];
		$keys_group = array_keys($myArray_group);
	}

	$elementCount  = count($fieldvalue[0]) + count($fieldvalue_group[0]);
	$elementCountUser  = count($fieldvalue[0]);
	$newArray = array();

	$lista_assegnatari = '(';
	for($y=0; $y<$elementCountUser; $y++){
		$user_id = $keys[$y];
		$queryUsers = "SELECT id, user_name 'username', first_name, last_name FROM {$table_prefix}_users WHERE id = {$user_id}";
		$result = $adb->query($queryUsers);
		$id = $adb->query_result($result, 0, 'id');
		$userName = $adb->query_result($result, 0, 'username');
		$firstName = $adb->query_result($result, 0, 'first_name');
		$lastName = $adb->query_result($result, 0, 'last_name');
		$newArray[] = array('id' => $id,
							'user_name' => $userName,
							'first_name' => $firstName,
							'last_name' => $lastName);
		
		if($lista_assegnatari == '('){
			$lista_assegnatari  .= $id;
		}
		else{
			$lista_assegnatari  .= ",".$id;
		}
		
	}

	$elementCountGroup  = count($fieldvalue_group[0]);

	for($y=0; $y<$elementCountGroup; $y++){
		$group_id = $keys_group[$y];
		$queryGroup = "SELECT groupid, groupname, description FROM {$table_prefix}_groups WHERE groupid = {$group_id}";

		$result_group = $adb->query($queryGroup);
		$groupid = $adb->query_result($result_group, 0, 'groupid');
		$groupname = $adb->query_result($result_group, 0, 'groupname');
		$description = $adb->query_result($result_group, 0, 'description');

		$newArray[] = array('id' => $groupid,
							'user_name' => 'Gruppo: '.$groupname,
							'first_name' => $description,
							'last_name' => "");
							
		if($lista_assegnatari == '('){
			$lista_assegnatari  .= $groupid;
		}
		else{
			$lista_assegnatari  .= ",".$groupid;
		}
		
	}

	$lista_assegnatari .= ')';*/
	
	if(CheckLogin($username, $password)){

		$data_esportazione = date("Y-m-d H:i:s");

		$data_corrente = new DateTime( date("Y-m-d") );

		$decremento = new DateInterval("P2M");

		$data_limite_inferiore = $data_corrente->sub($decremento);

		$data_limite_inferiore = $data_limite_inferiore->format("Y-m-d");

		//La prima riga da esportare contiene informazioni di sistema come la data di ultima esportazione

		$rows[] = array('data_esportazione' => $data_esportazione);

		$query = "SELECT 
					tick.ticketid ticketid,
					tick.ticket_no ticket_no,
					tick.priority priority,
					tick.status status,
					tick.title title,
					tick.kp_data_inizio_pian kp_data_inizio_pian,
					tick.kp_data_fine_pian kp_data_fine_pian,
					tick.kp_ora_inizio_tick kp_ora_inizio_tick,
					tick.kp_ora_fine_tick kp_ora_fine_tick,
					tick.kp_tempo_previsto kp_tempo_previsto,
					tick.description description,
					ent.createdtime createdtime,
					ent.modifiedtime modifiedtime,
					acc.accountid accountid,
					acc.accountname accountname,
					ent.smownerid assegnatario_id,
					us.user_name user_name,
					us.first_name first_name,
					us.last_name last_name,
					us.phone_mobile phone_mobile,
					us.email1 email1
					FROM {$table_prefix}_troubletickets tick
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
					INNER JOIN {$table_prefix}_users us ON us.id = ent.smownerid
					LEFT JOIN {$table_prefix}_account acc ON acc.accountid = tick.parent_id
					WHERE ent.deleted = 0";

		$query .= " AND ((tick.kp_data_inizio_pian >= '".$data_limite_inferiore."')";

		$query .= " OR ( tick.status NOT IN ('Closed', 'Emesso OdF') AND (tick.kp_data_inizio_pian IS NULL OR tick.kp_data_inizio_pian = '' OR tick.kp_data_inizio_pian = '0000-00-00')) )";

		$query .= " AND ent.smownerid = ".$userid;
		
		$res_query = $adb->query($query);
		$num_result = $adb->num_rows($res_query);
		
		for($i=0; $i<$num_result; $i++){
			
			$id = $adb->query_result($res_query, $i, 'ticketid');
			$id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

			$subject = $adb->query_result($res_query, $i, 'title');
			$subject = html_entity_decode(strip_tags($subject), ENT_QUOTES, $default_charset);

			$type = "Ticket";

			$data_inizio = $adb->query_result($res_query, $i, 'kp_data_inizio_pian');
			$data_inizio = html_entity_decode(strip_tags($data_inizio), ENT_QUOTES, $default_charset);
			if($data_inizio == null || $data_inizio == "" || $data_inizio == "0000-00-00"){
				$data_inizio = date("Y-m-d");
			}
			list($anno, $mese, $giorno) = explode("-", $data_inizio);
			$data_inizio_inv = date("d-m-Y", mktime(0, 0, 0, $mese, $giorno, $anno));

			$data_fine = $adb->query_result($res_query, $i, 'kp_data_fine_pian');
			$data_fine = html_entity_decode(strip_tags($data_fine), ENT_QUOTES, $default_charset);
			if($data_fine == null || $data_fine == "" || $data_fine == "0000-00-00"){

				list($anno, $mese, $giorno) = explode("-", $data_inizio);
				$data_fine = date("Y-m-d", mktime(0, 0, 0, $mese, $giorno, $anno));
				$all_day = "si";

			}
			else{
				$all_day = "no";
			}
			list($anno, $mese, $giorno) = explode("-", $data_fine);
			$data_fine_inv = date("d-m-Y", mktime(0, 0, 0, $mese, $giorno, $anno));

			$ora_inizio = $adb->query_result($res_query, $i, 'kp_ora_inizio_tick');
			$ora_inizio = html_entity_decode(strip_tags($ora_inizio), ENT_QUOTES, $default_charset);
			if($ora_inizio == null || $ora_inizio == ""){
				$ora_inizio = "08:00";
			}

			$ora_fine = $adb->query_result($res_query, $i, 'kp_ora_fine_tick');
			$ora_fine = html_entity_decode(strip_tags($ora_fine), ENT_QUOTES, $default_charset);
			if($ora_fine == null || $ora_fine == ""){
				$ora_fine = "18:00";
			}

			$duration_hours = $adb->query_result($res_query, $i, 'kp_tempo_previsto');
			$duration_hours = html_entity_decode(strip_tags($duration_hours), ENT_QUOTES, $default_charset);

			$stato = $adb->query_result($res_query, $i, 'status');
			$stato = html_entity_decode(strip_tags($stato), ENT_QUOTES, $default_charset);

			switch ($stato) {
				case "Open":
					$stato = "Aperto";
					break;
				case "In Progress":
					$stato = "In corso";
					break;
				case "Wait For Response":
					$stato = "In attesa di risposta";
					break;
				case "Maintain":
					$stato = "Maintain";
					break;
				case "Caricato Documento":
					$stato = "Caricato Documento";
					break;
				default:
					$stato = $stato;
			}

			$priority = $adb->query_result($res_query, $i, 'priority');
			$priority = html_entity_decode(strip_tags($priority), ENT_QUOTES, $default_charset);

			$description = $adb->query_result($res_query, $i, 'description');
			$description = html_entity_decode(strip_tags($description), ENT_QUOTES, $default_charset);

			$createdtime = $adb->query_result($res_query, $i, 'createdtime');
			$createdtime = html_entity_decode(strip_tags($createdtime), ENT_QUOTES, $default_charset);

			$modifiedtime = $adb->query_result($res_query, $i, 'modifiedtime');
			$modifiedtime = html_entity_decode(strip_tags($modifiedtime), ENT_QUOTES, $default_charset);

			$assegnatario_id = $adb->query_result($res_query, $i, 'assegnatario_id');
			$assegnatario_id = html_entity_decode(strip_tags($assegnatario_id), ENT_QUOTES, $default_charset);

			$assegnatario_name = $adb->query_result($res_query, $i, 'user_name');
			$assegnatario_name = html_entity_decode(strip_tags($assegnatario_name), ENT_QUOTES, $default_charset);

			$accountid = $adb->query_result($res_query, $i, 'accountid');
			$accountid = html_entity_decode(strip_tags($accountid), ENT_QUOTES, $default_charset);
			if($accountid == null || $accountid == 0){
				$accountid = "";
			}

			$accountname = $adb->query_result($res_query, $i, 'accountname');
			$accountname = html_entity_decode(strip_tags($accountname), ENT_QUOTES, $default_charset);
			if($accountname == null){
				$accountname = "";
			}

			if($accountid != ""){
				$dati_azienda = getDatiAccount($accountid);

				$telefono = $dati_azienda["telefono"];
				$website = $dati_azienda["website"];
				$email = $dati_azienda["email"];
				$indirizzo = $dati_azienda["indirizzo"];
				$citta = $dati_azienda["citta"];
				$provincia = $dati_azienda["provincia"];
				$cap = $dati_azienda["cap"];
				$nazione = $dati_azienda["nazione"];
			}
			else{
				$telefono = "";
				$website = "";
				$email = "";
				$indirizzo = "";
				$citta = "";
				$provincia = "";
				$cap = "";
				$nazione = "";
			}

			$rows[] = array('id' => $id,
							'subject' => $subject,
							'type' => $type,
							'description' => $description,
							'data_inizio' => $data_inizio,
							'data_inizio_inv' => $data_inizio_inv,
							'ora_inizio' => $ora_inizio,
							'data_fine' => $data_fine,
							'data_fine_inv' => $data_fine_inv,
							'ora_fine' => $ora_fine,
							'all_day' => $all_day,
							'duration_hours' => $duration_hours,
							'stato' => $stato,
							'priority' => $priority,
							'accountid' => $accountid,
							'accountname' => $accountname,
							'assegnatario_id' => $assegnatario_id,
							'assegnatario_name' => $assegnatario_name,
							'nome_referente' => $accountname,
							'telefono' => $telefono,
							'website' => $website,
							'email' => $email,
							'indirizzo' => $indirizzo,
							'citta' => $citta,
							'provincia' => $provincia,
							'cap' => $cap,
							'nazione' => $nazione,
							'createdtime' => $createdtime,
							'modifiedtime' => $modifiedtime,
							'data_esportazione' => $data_esportazione);

		}
		
	}
	
}
						
$json = json_encode($rows);
print $json;
//print $_GET['cb'] ."(".$json.");"; 

function getDatiAccount($id){
	global $adb, $table_prefix, $default_charset;

	$result = "";

	$query = "SELECT 
				acc.accountid accountid,
				acc.accountname accountname,
				acc.phone phone,
				acc.website website,
				acc.otherphone otherphone,
				acc.email1 email1,
				billadd.bill_street bill_street,
				billadd.bill_city bill_city,
				billadd.bill_state bill_state,
				billadd.bill_code bill_code,
				billadd.bill_country bill_country
				FROM {$table_prefix}_account acc
				INNER JOIN {$table_prefix}_accountbillads billadd ON billadd.accountaddressid = acc.accountid
				WHERE acc.accountid = ".$id;

	$res_query = $adb->query($query);
	$num_result = $adb->num_rows($res_query);

	if($num_result > 0){

		$nome_azienda = $adb->query_result($res_query, 0, 'accountname');
		$nome_azienda = html_entity_decode(strip_tags($nome_azienda), ENT_QUOTES, $default_charset);

		$telefono = $adb->query_result($res_query, 0, 'phone');
		$telefono = html_entity_decode(strip_tags($telefono), ENT_QUOTES, $default_charset);

		$website = $adb->query_result($res_query, 0, 'website');
		$website = html_entity_decode(strip_tags($website), ENT_QUOTES, $default_charset);

		$email = $adb->query_result($res_query, 0, 'email1');
		$email = html_entity_decode(strip_tags($email), ENT_QUOTES, $default_charset);

		$indirizzo = $adb->query_result($res_query, 0, 'bill_street');
		$indirizzo = html_entity_decode(strip_tags($indirizzo), ENT_QUOTES, $default_charset);

		$citta = $adb->query_result($res_query, 0, 'bill_city');
		$citta = html_entity_decode(strip_tags($citta), ENT_QUOTES, $default_charset);

		$provincia = $adb->query_result($res_query, 0, 'bill_state');
		$provincia = html_entity_decode(strip_tags($provincia), ENT_QUOTES, $default_charset);

		$cap = $adb->query_result($res_query, 0, 'bill_code');
		$cap = html_entity_decode(strip_tags($cap), ENT_QUOTES, $default_charset);

		$nazione = $adb->query_result($res_query, 0, 'bill_country');
		$nazione = html_entity_decode(strip_tags($nazione), ENT_QUOTES, $default_charset);

	}
	else{

		$nome_azienda = 0;
		$telefono = "";
		$website = "";
		$email = "";
		$indirizzo = "";
		$citta = "";
		$provincia = "";
		$cap = "";
		$nazione = "";

	}

	$result = array("nome_azienda" => $nome_azienda,
					"telefono" => $telefono,
					"website" => $website,
					"email" => $email,
					"indirizzo" => $indirizzo,
					"citta" => $citta,
					"provincia" => $provincia,
					"cap" => $cap,
					"nazione" => $nazione);

	return $result;
}
	
?>