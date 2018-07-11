<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
global $adb, $default_charset, $app_strings, $mod_strings, $current_language, $currentModule, $theme, $log, $table_prefix;

require_once('Smarty_setup.php');
require_once('data/Tracker.php');
require_once('include/CustomFieldUtil.php');
require_once('include/utils/utils.php');
require_once('include/FormValidationUtil.php');

$log->info("$currentModule detail view");

$focus = CRMEntity::getInstance($currentModule);
$smarty = new vtigerCRM_Smarty();

$category = getParentTab($currentModule);
$record = intval($_REQUEST['record']); // crmv@37463
$isduplicate = vtlib_purify($_REQUEST['isDuplicate']);

//added to fix the issue4600
$searchurl = getBasic_Advance_SearchURL();
$smarty->assign("SEARCH", $searchurl);
//4600 ends

if($record) {
	$focus->id = $record;
	$focus->mode = 'edit';
	$focus->retrieve_entity_info($record, $currentModule);
	$focus->firstname=$focus->column_fields['firstname'];
    $focus->lastname=$focus->column_fields['lastname'];
}
if($isduplicate == 'true') {
	$smarty->assign("DUPLICATE_FROM", $focus->id); // crmv@64542 - this is used for inventory modules
	$focus->id = '';
	$focus->mode = '';
}
if(empty($record) && $focus->mode != 'edit'){
	setObjectValuesFromRequest($focus);
}

$disp_view = getView($focus->mode);
//crmv@9434
$mode = $focus->mode;

// crmv@104568
$panelid = getCurrentPanelId($currentModule);
$smarty->assign("PANELID", $panelid);
$panelsAndBlocks = getPanelsAndBlocks($currentModule, $record);
$smarty->assign("PANEL_BLOCKS", Zend_Json::encode($panelsAndBlocks));
if ($InventoryUtils) {
	$binfo = $InventoryUtils->getInventoryBlockInfo($currentModule);
	$smarty->assign('PRODBLOCKINFO', $binfo);
}

if(file_exists(dirname(__FILE__).'/EditViewKpC.php')){
	require_once(dirname(__FILE__).'/EditViewKpC.php');
}

if($mode != 'edit'){
	if($_REQUEST['function'] == 'GenerateReportVisite' && $_REQUEST['record_action'] != ''){
		//Query per estrarre i dati dall'evento di calendario
		$query = "SELECT va.subject,
				va.date_start,
				va.duration_hours,
				va.duration_minutes,
				sa.crmid,
				ent.smownerid,
				va.description
				FROM {$table_prefix}_activity va
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = va.activityid 
				LEFT JOIN {$table_prefix}_seactivityrel sa ON sa.activityid = va.activityid
				WHERE ent.deleted = 0 AND va.activityid = ".$_REQUEST['record_action'];

		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);
		
		if($num_result > 0){
			$subject = $adb->query_result($result_query,0,'subject');
			$subject = html_entity_decode(strip_tags($subject), ENT_QUOTES, $default_charset);
			if($subject == null){
				$subject = "";
			}

			$date_start = $adb->query_result($result_query,0,'date_start');
			$date_start = html_entity_decode(strip_tags($date_start), ENT_QUOTES, $default_charset);

			$duration_hours = $adb->query_result($result_query, 0, 'duration_hours');
			$duration_hours = html_entity_decode(strip_tags($duration_hours), ENT_QUOTES, $default_charset);
			
			$duration_minutes = $adb->query_result($result_query, 0, 'duration_minutes');
			$duration_minutes = html_entity_decode(strip_tags($duration_minutes), ENT_QUOTES, $default_charset);

			$description = $adb->query_result($result_query,0,'description');
			$description = html_entity_decode(strip_tags($description), ENT_QUOTES, $default_charset);
			if($description == null){
				$description = "";
			}

			$accountid = 0;
			$potentialid = 0;

			$crmid = $adb->query_result($result_query,0,'crmid');
			$crmid = html_entity_decode(strip_tags($crmid), ENT_QUOTES, $default_charset);
			if($crmid != "" && $crmid != null && $crmid != 0){
				$q = "SELECT setype
					FROM {$table_prefix}_crmentity
					WHERE crmid = ".$crmid;
				$res = $adb->query($q);
				if($adb->num_rows($res) > 0){
					$setype = $adb->query_result($res, 0, 'setype');
					$setype = html_entity_decode(strip_tags($setype), ENT_QUOTES, $default_charset);

					switch($setype){
						case "Potentials":
							$potentialid = $crmid;
							break;
						case "Accounts":
							$accountid = $crmid;
							break;
					}
				}
			}

			if($potentialid != 0){
				$q_azienda = "SELECT acc.accountid
							FROM {$table_prefix}_potential pot
							INNER JOIN {$table_prefix}_account acc ON acc.accountid = pot.related_to
							WHERE pot.potentialid = ".$potentialid;
				$res_azienda = $adb->query($q_azienda);
				if($adb->num_rows($res_azienda) > 0){
					$accountid = $adb->query_result($res_azienda, 0, 'accountid');
					$accountid = html_entity_decode(strip_tags($accountid), ENT_QUOTES, $default_charset);
					if($accountid == null || $accountid == ""){
						$accountid = 0;
					}
				}
			}

			if($accountid != 0){
				$q_azienda = "SELECT kp_km_percorsi,
							kp_ore_viaggio,
							kp_spese_autostrada
							FROM {$table_prefix}_account
							WHERE accountid = ".$accountid;
				$res_azienda = $adb->query($q_azienda);
				if($adb->num_rows($res_azienda) > 0){
					$distanza = $adb->query_result($res_azienda, 0, 'kp_km_percorsi');
					$distanza = html_entity_decode(strip_tags($distanza), ENT_QUOTES, $default_charset);
					if($distanza == null || $distanza == ""){
						$distanza = 0;
					}

					$ore_viaggio = $adb->query_result($res_azienda, 0, 'kp_ore_viaggio');
					$ore_viaggio = html_entity_decode(strip_tags($ore_viaggio), ENT_QUOTES, $default_charset);
					if($ore_viaggio == null || $ore_viaggio == ""){
						$ore_viaggio = 0;
					}

					$pedaggio = $adb->query_result($res_azienda, 0, 'kp_spese_autostrada');
					$pedaggio = html_entity_decode(strip_tags($pedaggio), ENT_QUOTES, $default_charset);
					if($pedaggio == null || $pedaggio == ""){
						$pedaggio = 0;
					}
				}
			}

			$smownerid = $adb->query_result($result_query,0,'smownerid');
			$smownerid = html_entity_decode(strip_tags($smownerid), ENT_QUOTES, $default_charset);

			//Popolo i campi del report visite
			if($duration_hours != null && $duration_hours != "" && $duration_minutes != null && $duration_minutes != ""){
				if($duration_minutes != 0){
					$duration_minutes = round($duration_minutes / 60, 2);
				}
				$event_duration = $duration_hours + $duration_minutes;
				$focus->column_fields['kp_ore_effettive'] = $event_duration;
				$focus->column_fields['kp_ore_fatturate'] = $event_duration;
			}
			$focus->column_fields['visitreportname'] = $subject;
			$focus->column_fields['visitdate'] = $date_start;
			$focus->column_fields['description'] = $description;
			if($accountid != 0){
				$focus->column_fields['accountid'] = $accountid;
				$focus->column_fields['kmpercorsi'] = $distanza;
				$focus->column_fields['kp_ore_viaggio'] = $ore_viaggio;
				$focus->column_fields['spautostr'] = $pedaggio;
			}
			if($potentialid != 0){
				$focus->column_fields['kp_opportunita'] = $potentialid;
			}
			$focus->column_fields['assigned_user_id'] = $smownerid;
			$focus->column_fields['evento'] = $_REQUEST['record_action'];
		}
		
	}
} 

$smarty->assign("BLOCKS",getBlocks($currentModule,$disp_view,$mode,$focus->column_fields,'',$blockVisibility));	//crmv@99316
$smarty->assign('BLOCKVISIBILITY', $blockVisibility);	//crmv@99316

if($disp_view != 'edit_view') {
	//merge check - start
	$smarty->assign("MERGE_USER_FIELDS",implode(',',get_merge_user_fields($currentModule))); //crmv_utils
	//ends
}
// crmv@104568e

$smarty->assign('OP_MODE',$disp_view);
$smarty->assign('APP', $app_strings);
$smarty->assign('MOD', $mod_strings);
$smarty->assign('MODULE', $currentModule);
// TODO: Update Single Module Instance name here.
$smarty->assign('SINGLE_MOD', 'SINGLE_'.$currentModule);
$smarty->assign('CATEGORY', $category);
$smarty->assign("THEME", $theme);
$smarty->assign('IMAGE_PATH', "themes/$theme/images/");
$smarty->assign('ID', $focus->id);
$smarty->assign('MODE', $focus->mode);
$smarty->assign('CREATEMODE', vtlib_purify($_REQUEST['createmode']));

// crmv@42752
if ($_REQUEST['hide_button_list'] == '1') {
	$smarty->assign('HIDE_BUTTON_LIST', '1');
}
// crmv@42752e

if(isset($cust_fld))
{
	$smarty->assign("CUSTOMFIELD", $cust_fld);
}
$smarty->assign("PRINT_URL", "phprint.php?jt=".session_id().$GLOBALS['request_string']);

$smarty->assign('CHECK', Button_Check($currentModule));
$smarty->assign('DUPLICATE', $isduplicate);

if($focus->mode == 'edit' || $isduplicate) {
	$recordName = @array_values(getEntityName($currentModule, $record));	//crmv@30447
	$recordName = $recordName[0];
	$smarty->assign('NAME', $recordName);
	$smarty->assign('UPDATEINFO',updateInfo($record));
}

if(isset($_REQUEST['campaignid']))		 $smarty->assign("campaignid",vtlib_purify($_REQUEST['campaignid']));
if(isset($_REQUEST['return_module']))    $smarty->assign("RETURN_MODULE", vtlib_purify($_REQUEST['return_module']));
if(isset($_REQUEST['return_action']))    $smarty->assign("RETURN_ACTION", vtlib_purify($_REQUEST['return_action']));
if(isset($_REQUEST['return_id']))        $smarty->assign("RETURN_ID", vtlib_purify($_REQUEST['return_id']));
if (isset($_REQUEST['return_viewname'])) $smarty->assign("RETURN_VIEWNAME", vtlib_purify($_REQUEST['return_viewname']));

// Field Validation Information
$tabid = getTabid($currentModule);
$validationUitypes = array(); //mycrmv@158844
$validationData = getDBValidationData($focus->tab_name,$tabid,$validationUitypes,$focus);	//crmv@96450 //mycrmv@158844
$validationArray = split_validationdataArray($validationData);

$smarty->assign("VALIDATION_DATA_FIELDNAME",$validationArray['fieldname']);
$smarty->assign("VALIDATION_DATA_FIELDDATATYPE",$validationArray['datatype']);
$smarty->assign("VALIDATION_DATA_FIELDLABEL",$validationArray['fieldlabel']);
$smarty->assign("VALIDATION_DATA_FIELDUITYPE",implode(',', array_values($validationUitypes))); //mycrmv@158844

// In case you have a date field
$smarty->assign("CALENDAR_LANG", $app_strings['LBL_JSCALENDAR_LANG']);
$smarty->assign("CALENDAR_DATEFORMAT", parse_calendardate($app_strings['NTC_DATE_FORMAT']));

global $adb;
// Module Sequence Numbering
$mod_seq_field = getModuleSequenceField($currentModule);
if($focus->mode != 'edit' && $mod_seq_field != null) {
		$autostr = getTranslatedString('MSG_AUTO_GEN_ON_SAVE');
		$mod_seq_string = $adb->pquery("SELECT prefix, cur_id from {$table_prefix}_modentity_num where semodule = ? and active=1",array($currentModule));
        $mod_seq_prefix = $adb->query_result($mod_seq_string,0,'prefix');
        $mod_seq_no = $adb->query_result($mod_seq_string,0,'cur_id');
        if($adb->num_rows($mod_seq_string) == 0 || $focus->checkModuleSeqNumber($focus->table_name, $mod_seq_field['column'], $mod_seq_prefix.$mod_seq_no)) {
			echo '<br><font color="#FF0000"><b>'. getTranslatedString('LBL_DUPLICATE'). ' '. getTranslatedString($mod_seq_field['label'])
				.' - '. getTranslatedString('LBL_CLICK') .' <a href="index.php?module=Settings&action=CustomModEntityNo&parenttab=Settings&selmodule='.$currentModule.'">'.getTranslatedString('LBL_HERE').'</a> '
				. getTranslatedString('LBL_TO_CONFIGURE'). ' '. getTranslatedString($mod_seq_field['label']) .'</b></font>';
        } else {
        	$smarty->assign("MOD_SEQ_ID",$autostr);
        }
} else {
	$smarty->assign("MOD_SEQ_ID", $focus->column_fields[$mod_seq_field['name']]);
}
// END

// Gather the help information associated with fields
$smarty->assign('FIELDHELPINFO', vtlib_getFieldHelpInfo($currentModule));
// END

//$picklistDependencyDatasource = Vtiger_DependencyPicklist::getPicklistDependencyDatasource($currentModule);
//$smarty->assign("PICKIST_DEPENDENCY_DATASOURCE", Zend_Json::encode($picklistDependencyDatasource));

//crmv@57221
$CU = CRMVUtils::getInstance();
$smarty->assign("OLD_STYLE", $CU->getConfigurationLayout('old_style'));
//crmv@57221e

//crmv@92272
if ($_REQUEST['mass_edit_mode'] == '1') {
	$smarty->assign('MASS_EDIT','1');
}
//crmv@92272e

//crmv@100495
require_once('modules/Settings/ProcessMaker/ProcessMakerUtils.php');
$PMUtils = ProcessMakerUtils::getInstance();
if ($PMUtils->showRunProcessesButton($currentModule, $focus->id)) $smarty->assign('SHOW_RUN_PROCESSES_BUTTON',true);
//crmv@100495e

if($focus->mode == 'edit') {
	$smarty->display('salesEditView.tpl');
} else {
	$smarty->display('CreateView.tpl');
}

?>