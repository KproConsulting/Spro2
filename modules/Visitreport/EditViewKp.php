<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/
/* kpro@bid200620181800 migrazione vte18.05 */
 //crmv@30447
require_once('modules/VteCore/EditView.php');

global $adb, $default_charset, $table_prefix;

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

$smarty->display('salesEditView.tpl');
/* kpro@bid200620181800 migrazione vte18.05 end */

?>
