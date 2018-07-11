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

require('user_privileges/requireUserPrivileges.php'); 
require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

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
    $assigned_user_id = $current_user->id;
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

$lista_assegnatari .= ')';

$rows = array();

if(isset($_GET['risorsa'])){
    $risorsa = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['risorsa']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $risorsa = substr($risorsa,0,100);
	
	$q_contatto = "SELECT 
					cont.contactid contactid,
					cont.firstname nome,
					cont.lastname cognome,
					cont.phone telefono,
					cont.email email,
					cont.data_fine_rap data_fine_rap,
					cont.data_assunzione data_assunzione,
					contsub.birthday birthday,
					addr.mailingcountry mailingcountry,
					addr.mailingcity mailingcity,
					addr.mailingstate mailingstate,
					addr.mailingstreet mailingstreet,
					addr.mailingzip mailingzip,
					cont.stabilimento stabilimento,
					stab.nome_stabilimento nome_stabilimento,
					cont.description description
					from {$table_prefix}_contactdetails cont
					left join {$table_prefix}_contactsubdetails contsub on contsub.contactsubscriptionid = cont.contactid
					left join {$table_prefix}_contactaddress addr on addr.contactaddressid = cont.contactid
					inner join {$table_prefix}_crmentity ent on ent.crmid = cont.contactid
					left join {$table_prefix}_stabilimenti stab on stab.stabilimentiid = cont.stabilimento
					where ent.deleted = 0 and cont.contactid = ".$risorsa." and ent.smownerid IN ".$lista_assegnatari;
	
	$res_contatto = $adb->query($q_contatto);
	if($adb->num_rows($res_contatto) > 0){
	
		$contactid = $adb->query_result($res_contatto, 0, 'contactid');
		$contactid = html_entity_decode(strip_tags($contactid), ENT_QUOTES,$default_charset);
	
		$nome = $adb->query_result($res_contatto, 0, 'nome');
		$nome = html_entity_decode(strip_tags($nome), ENT_QUOTES,$default_charset);
		
		$cognome = $adb->query_result($res_contatto, 0, 'cognome');
		$cognome = html_entity_decode(strip_tags($cognome), ENT_QUOTES,$default_charset);
		
		$telefono = $adb->query_result($res_contatto, 0, 'telefono');
		$telefono = html_entity_decode(strip_tags($telefono), ENT_QUOTES,$default_charset);
		
		$email = $adb->query_result($res_contatto, 0, 'email');
		$email = html_entity_decode(strip_tags($email), ENT_QUOTES,$default_charset);
		
		$nome_stabilimento = $adb->query_result($res_contatto, 0, 'nome_stabilimento');
		$nome_stabilimento = html_entity_decode(strip_tags($nome_stabilimento), ENT_QUOTES,$default_charset);
		if($nome_stabilimento == null || $nome_stabilimento == ""){
			$nome_stabilimento = "";
		}
		
		$stabilimento = $adb->query_result($res_contatto, 0, 'stabilimento');
		$stabilimento = html_entity_decode(strip_tags($stabilimento), ENT_QUOTES,$default_charset);
		if($stabilimento == null || $stabilimento == ""){
			$stabilimento = 0;
		}
		
		$data_fine_rap = $adb->query_result($res_contatto, 0, 'data_fine_rap');
		$data_fine_rap = html_entity_decode(strip_tags($data_fine_rap), ENT_QUOTES,$default_charset);
		if($data_fine_rap != null && $data_fine_rap != ""){
			list($anno, $mese, $giorno) = explode("-", $data_fine_rap);
            $data_fine_rap = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
		else{
			$data_fine_rap = "";
		}
		
		$data_assunzione = $adb->query_result($res_contatto, 0, 'data_assunzione');
		$data_assunzione = html_entity_decode(strip_tags($data_assunzione), ENT_QUOTES,$default_charset);
		if($data_assunzione != null && $data_assunzione != ""){
			list($anno, $mese, $giorno) = explode("-", $data_assunzione);
            $data_assunzione = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
		else{
			$data_assunzione = "";
		}
		
		$birthday = $adb->query_result($res_contatto, 0, 'birthday');
		$birthday = html_entity_decode(strip_tags($birthday), ENT_QUOTES,$default_charset);
		if($birthday != null && $birthday != ""){
			list($anno, $mese, $giorno) = explode("-", $birthday);
            $birthday = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
		else{
			$birthday = "";
		}
		
		$mailingcountry = $adb->query_result($res_contatto, 0, 'mailingcountry');
		$mailingcountry = html_entity_decode(strip_tags($mailingcountry), ENT_QUOTES,$default_charset);
		if($mailingcountry == null || $mailingcountry == ""){
			$mailingcountry = "";
		}
		
		$mailingcity = $adb->query_result($res_contatto, 0, 'mailingcity');
		$mailingcity = html_entity_decode(strip_tags($mailingcity), ENT_QUOTES,$default_charset);
		if($mailingcity == null || $mailingcity == ""){
			$mailingcity = "";
		}
		
		$mailingstate = $adb->query_result($res_contatto, 0, 'mailingstate');
		$mailingstate = html_entity_decode(strip_tags($mailingstate), ENT_QUOTES,$default_charset);
		if($mailingstate == null || $mailingstate == ""){
			$mailingstate = "";
		}
		
		$mailingstreet = $adb->query_result($res_contatto, 0, 'mailingstreet');
		$mailingstreet = html_entity_decode(strip_tags($mailingstreet), ENT_QUOTES,$default_charset);
		if($mailingstreet == null || $mailingstreet == ""){
			$mailingstreet = "";
		}
		
		$mailingzip = $adb->query_result($res_contatto, 0, 'mailingzip');
		$mailingzip = html_entity_decode(strip_tags($mailingzip), ENT_QUOTES,$default_charset);
		if($mailingzip == null || $mailingzip == ""){
			$mailingzip = "";
		}
		
		$description = $adb->query_result($res_contatto, 0, 'description');
		$description = html_entity_decode(strip_tags($description), ENT_QUOTES,$default_charset);
		if($description == null || $description == ""){
			$description = "";
		}
		
		$rows[] = array('contactid' => $contactid,
						'nome' => $nome,
						'cognome' => $cognome,
						'telefono' => $telefono,
						'email' => $email,
						'stabilimento' => $stabilimento,
						'nome_stabilimento' => $nome_stabilimento,
						'birthday' => $birthday,
						'data_assunzione' => $data_assunzione,
						'data_fine_rap' => $data_fine_rap,
						'mailingcountry' => $mailingcountry,
						'mailingcity' => $mailingcity,
						'mailingstate' => $mailingstate,
						'mailingstreet' => $mailingstreet,
						'mailingzip' => $mailingzip,
						'description' => $description);
		
	}
	
	$json = json_encode($rows);
	print $json;
	
}
?>
