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
					act.activityid activityid,
					act.subject subject,
					act.activitytype activitytype,
					act.date_start date_start,
					act.due_date due_date,
					act.time_start time_start,
					act.time_end time_end,
					act.duration_hours duration_hours,
					act.eventstatus eventstatus,
					act.priority priority,
					act.location location,
					act.visibility visibility,
					actrel.crmid elemento_relazionato,
					act.description description,
					ent.createdtime createdtime,
					ent.modifiedtime modifiedtime,
					ent.smownerid assegnatario_id,
					us.user_name user_name,
					us.first_name first_name,
					us.last_name last_name,
					us.phone_mobile phone_mobile,
					us.email1 email1
					FROM {$table_prefix}_activity act
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = act.activityid
					INNER JOIN {$table_prefix}_users us ON us.id = ent.smownerid
					LEFT JOIN {$table_prefix}_seactivityrel actrel ON actrel.activityid = act.activityid
					WHERE ent.deleted = 0";

		$query .= " AND act.eventstatus IS NOT NULL AND act.eventstatus != ''";

		$query .= " AND act.date_start >= '".$data_limite_inferiore."'";

		$query .= " AND ent.smownerid = ".$userid;
		
		$res_query = $adb->query($query);
		$num_result = $adb->num_rows($res_query);
		
		for($i=0; $i<$num_result; $i++){
			
			$id = $adb->query_result($res_query, $i, 'activityid');
			$id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

			$subject = $adb->query_result($res_query, $i, 'subject');
			$subject = html_entity_decode(strip_tags($subject), ENT_QUOTES, $default_charset);

			$type = "Evento";

			$activitytype = $adb->query_result($res_query, $i, 'activitytype');
			$activitytype = html_entity_decode(strip_tags($activitytype), ENT_QUOTES, $default_charset);

			$data_inizio = $adb->query_result($res_query, $i, 'date_start');
			$data_inizio = html_entity_decode(strip_tags($data_inizio), ENT_QUOTES, $default_charset);
			if($data_inizio == null){
				$data_inizio = "";
				$data_inizio_inv = "";
			}
			else{
				list($anno, $mese, $giorno) = explode("-", $data_inizio);
				$data_inizio_inv = date("d-m-Y", mktime(0, 0, 0, $mese, $giorno, $anno));
			}

			$data_fine = $adb->query_result($res_query, $i, 'due_date');
			$data_fine = html_entity_decode(strip_tags($data_fine), ENT_QUOTES, $default_charset);
			if($data_fine == null){
				$data_fine = "";
				$data_fine_inv = "";
			}
			else{
				list($anno, $mese, $giorno) = explode("-", $data_fine);
				$data_fine_inv = date("d-m-Y", mktime(0, 0, 0, $mese, $giorno, $anno));
			}

			$ora_inizio = $adb->query_result($res_query, $i, 'time_start');
			$ora_inizio = html_entity_decode(strip_tags($ora_inizio), ENT_QUOTES, $default_charset);

			$ora_fine = $adb->query_result($res_query, $i, 'time_end');
			$ora_fine = html_entity_decode(strip_tags($ora_fine), ENT_QUOTES, $default_charset);

			$duration_hours = $adb->query_result($res_query, $i, 'duration_hours');
			$duration_hours = html_entity_decode(strip_tags($duration_hours), ENT_QUOTES, $default_charset);

			$stato = $adb->query_result($res_query, $i, 'eventstatus');
			$stato = html_entity_decode(strip_tags($stato), ENT_QUOTES, $default_charset);

			switch ($stato) {
				case "Planned":
					$stato = "Pianificato";
					break;
				case "Held":
					$stato = "Eseguito";
					break;
				case "Not Held":
					$stato = "Non eseguito";
					break;
				default:
					$stato = $stato;
			}

			$priority = $adb->query_result($res_query, $i, 'priority');
			$priority = html_entity_decode(strip_tags($priority), ENT_QUOTES, $default_charset);

			$location = $adb->query_result($res_query, $i, 'location');
			$location = html_entity_decode(strip_tags($location), ENT_QUOTES, $default_charset);

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

			$accountid = 0;

			$elemento_relazionato = $adb->query_result($res_query, $i, 'elemento_relazionato');
			$elemento_relazionato = html_entity_decode(strip_tags($elemento_relazionato), ENT_QUOTES, $default_charset);
			if($elemento_relazionato == null || $elemento_relazionato == "" || $elemento_relazionato == 0){
				$elemento_relazionato = 0;
				$tipo_elemento_relazionato = "";
			}
			else{
				$tipo_elemento_relazionato = getElementType($elemento_relazionato);
				
				switch ($tipo_elemento_relazionato) {
					case "Accounts":
						$accountid = $elemento_relazionato;
						break;
					case "HelpDesk":
						$accountid = getAccountFromHelpDesk($elemento_relazionato);
						break;
					case "ProjectTask":
						$accountid = getAccountFromProjectTask($elemento_relazionato);
						break;
				}

			}

			if($accountid != 0){
				$dati_azienda = getDatiAccount($accountid);

				$accountname = $dati_azienda["nome_azienda"];
				$nome_referente = $dati_azienda["nome_azienda"];
				$telefono = $dati_azienda["telefono"];
				$website = $dati_azienda["website"];
				$email = $dati_azienda["email"];
				$indirizzo = $dati_azienda["indirizzo"];
				$citta = $dati_azienda["citta"];
				$provincia = $dati_azienda["provincia"];
				$cap = $dati_azienda["cap"];
				$nazione = $dati_azienda["nazione"];

				if( existStabilimentoField($tipo_elemento_relazionato) ) {

					$dati_stabilimento = getDatiStabilimento($tipo_elemento_relazionato, $elemento_relazionato);

					if($dati_stabilimento["nome_stabilimento"] != ""){

						$nome_referente = $dati_stabilimento["nome_stabilimento"];
						$indirizzo = $dati_stabilimento["indirizzo"];
						$citta = $dati_stabilimento["citta"];
						$provincia = $dati_stabilimento["provincia"];
						$cap = $dati_stabilimento["cap"];
						$nazione = $dati_stabilimento["nazione"];
					
					}

				}

			}
			else{
				$accountname = "";
				$nome_referente = "";
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
							'activitytype' => $activitytype,
							'data_inizio' => $data_inizio,
							'data_inizio_inv' => $data_inizio_inv,
							'ora_inizio' => $ora_inizio,
							'data_fine' => $data_fine,
							'data_fine_inv' => $data_fine_inv,
							'ora_fine' => $ora_fine,
							'duration_hours' => $duration_hours,
							'stato' => $stato,
							'priority' => $priority,
							'location' => $location,
							'accountid' => $accountid,
							'accountname' => $accountname,
							'assegnatario_id' => $assegnatario_id,
							'assegnatario_name' => $assegnatario_name,
							'nome_referente' => $nome_referente,
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

		$nome_azienda = "";
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

function getElementType($id){
	global $adb, $table_prefix, $default_charset;

	$result = "";

	$query = "SELECT 
				setype 
				FROM {$table_prefix}_crmentity
				WHERE crmid = ".$id;

	$res_query = $adb->query($query);
	$num_result = $adb->num_rows($res_query);

	if($num_result > 0){

		$setype = $adb->query_result($res_query, 0, 'setype');
		$setype = html_entity_decode(strip_tags($setype), ENT_QUOTES, $default_charset);

		$result = $setype;

	}

	return $result;

}

function getAccountFromHelpDesk($id){
	global $adb, $table_prefix, $default_charset;

	$result = 0;

	$query = "SELECT 
				parent_id 
				FROM {$table_prefix}_troubletickets
				WHERE ticketid = ".$id;

	$res_query = $adb->query($query);
	$num_result = $adb->num_rows($res_query);

	if($num_result > 0){

		$parent_id = $adb->query_result($res_query, 0, 'parent_id');
		$parent_id = html_entity_decode(strip_tags($parent_id), ENT_QUOTES, $default_charset);
		if($parent_id == null || $parent_id == ""){
			$parent_id = 0;
		}

		$result = $parent_id;

	}

	return $result;

}

function getAccountFromProjectTask($id){
	global $adb, $table_prefix, $default_charset;

	$result = 0;

	$query = "SELECT 
				kp_relazionato_a 
				FROM {$table_prefix}_projecttask
				WHERE projecttaskid = ".$id;

	$res_query = $adb->query($query);
	$num_result = $adb->num_rows($res_query);

	if($num_result > 0){

		$relazionato_a = $adb->query_result($res_query, 0, 'kp_relazionato_a');
		$relazionato_a = html_entity_decode(strip_tags($relazionato_a), ENT_QUOTES, $default_charset);
		if($relazionato_a == null || $relazionato_a == ""){
			$relazionato_a = 0;
		}

		$result = $relazionato_a;

	}

	return $result;

}

function existStabilimentoField($module){
	global $adb, $table_prefix, $default_charset;

	$result = false;

	if($module == "HelpDesk"){

		$query = "SELECT 
					fi.fieldid fieldid
					FROM {$table_prefix}_field fi
					INNER JOIN {$table_prefix}_tab tab ON tab.tabid = fi.tabid
					WHERE tab.name = 'HelpDesk' AND fi.fieldname = 'kp_stabilimento'";

	}

	if($query != ""){

		$res_query = $adb->query($query);
		$num_result = $adb->num_rows($res_query);

		if( $num_result > 0 ){

			$result = true;

		}

	}

	return $result;

}

function getDatiStabilimento($tipo, $id){
	global $adb, $table_prefix, $default_charset;

	$result = "";

	$nome_stabilimento = "";
	$nazione = "";
	$provincia = "";
	$citta = "";
	$indirizzo = "";
	$cap = "";

	if($tipo == "HelpDesk"){

		$query = "SELECT 
					stab.nome_stabilimento nome_stabilimento,
					stab.stato nazione,
					stab.provincia provincia,
					stab.citta citta,
					stab.indirizzo indirizzo,
					stab.cap cap
					FROM {$table_prefix}_troubletickets tick
					INNER JOIN {$table_prefix}_stabilimenti stab ON stab.stabilimentiid = tick.kp_stabilimento
					WHERE tick.ticketid = ".$id;
	
	}

	if($query != ""){

		$res_query = $adb->query($query);
		$num_result = $adb->num_rows($res_query);

		if($num_result > 0){

			$nome_stabilimento = $adb->query_result($res_query, 0, 'nome_stabilimento');
			$nome_stabilimento = html_entity_decode(strip_tags($nome_stabilimento), ENT_QUOTES, $default_charset);

			$nazione = $adb->query_result($res_query, 0, 'nazione');
			$nazione = html_entity_decode(strip_tags($nazione), ENT_QUOTES, $default_charset);

			$provincia = $adb->query_result($res_query, 0, 'provincia');
			$provincia = html_entity_decode(strip_tags($provincia), ENT_QUOTES, $default_charset);

			$citta = $adb->query_result($res_query, 0, 'citta');
			$citta = html_entity_decode(strip_tags($citta), ENT_QUOTES, $default_charset);

			$indirizzo = $adb->query_result($res_query, 0, 'indirizzo');
			$indirizzo = html_entity_decode(strip_tags($indirizzo), ENT_QUOTES, $default_charset);

			$cap = $adb->query_result($res_query, 0, 'cap');
			$cap = html_entity_decode(strip_tags($cap), ENT_QUOTES, $default_charset);

		}

	}

	$result = array("nome_stabilimento" => $nome_stabilimento,
					"nazione" => $nazione,
					"provincia" => $provincia,
					"citta" => $citta,
					"indirizzo" => $indirizzo,
					"cap" => $cap);

	return $result;

}
	
?>