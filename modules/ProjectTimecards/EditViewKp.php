<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

/* kpro@tom020520171019 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

 
require_once('modules/{$table_prefix}Core/EditView.php');

global $adb, $table_prefix, $current_user;

if(file_exists(dirname(__FILE__).'/EditViewKpC.php')){
	require_once(dirname(__FILE__).'/EditViewKpC.php');
}

if ($focus->mode == 'edit'){
	$smarty->display('salesEditView.tpl');
}
else{

	/* kpro@tom020520171019 */

	if(!$isduplicate) {

		if($_REQUEST['function'] == 'GenerateInterventoOperazione' && $_REQUEST['record_action'] != ''){

			$query = "SELECT 
						act.activityid activityid,
						act.subject subject,
						act.date_start date_start,
						act.time_start time_start,
						act.due_date due_date,
						act.time_end time_end,
						act.eventstatus eventstatus,
						act.duration_hours duration_hours,
						actrel.crmid operazione,
						task.projecttaskname projecttaskname,
						act.description description
						FROM {$table_prefix}_activity act
						INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = act.activityid
						INNER JOIN {$table_prefix}_seactivityrel actrel ON actrel.activityid = act.activityid
						LEFT JOIN {$table_prefix}_projecttask task ON task.projecttaskid = actrel.crmid
						WHERE act.activityid = ".$_REQUEST['record_action'];
		
			$result_query = $adb->query($query);
			$num_result = $adb->num_rows($result_query);

			if($num_result > 0){

				$activityid = $adb->query_result($result_query, 0, 'activityid');
				$activityid = html_entity_decode(strip_tags($activityid), ENT_QUOTES, $default_charset);

				$activity_name = $adb->query_result($result_query, 0, 'subject');
				$activity_name = html_entity_decode(strip_tags($activity_name), ENT_QUOTES, $default_charset);

				$operazione = $adb->query_result($result_query, 0, 'operazione');
				$operazione = html_entity_decode(strip_tags($operazione), ENT_QUOTES, $default_charset);
				
				if($operazione != null && $operazione != "" && $operazione != 0){

					$focus->column_fields['task'] = $operazione;

				}

				$projecttaskname = $adb->query_result($result_query, 0, 'projecttaskname');
				$projecttaskname = html_entity_decode(strip_tags($projecttaskname), ENT_QUOTES, $default_charset);

				$date_start = $adb->query_result($result_query, 0, 'date_start');
				$date_start = html_entity_decode(strip_tags($date_start), ENT_QUOTES, $default_charset);

				$duration_hours = $adb->query_result($result_query, 0, 'duration_hours');
				$duration_hours = html_entity_decode(strip_tags($duration_hours), ENT_QUOTES, $default_charset);

				$description = $adb->query_result($result_query, 0, 'description');
				$description = html_entity_decode(strip_tags($description), ENT_QUOTES, $default_charset);

				if($duration_hours == null || $duration_hours == ""){
					$duration_hours = 0;
				}

				$focus->column_fields['ore_lavorate'] = $duration_hours;
				$focus->column_fields['data'] = $date_start;
				$focus->column_fields['description'] = $description;

			}

			$focus->column_fields['kp_evento_id'] = $_REQUEST['record_action'];

		}

		$utente = $current_user->id;

		$query = "SELECT 
					us.kp_risorsa_pianific risorsa_pianific
					FROM {$table_prefix}_users us
					WHERE us.id = ".$utente;
	
		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if($num_result > 0){

			$risorsa_pianific = $adb->query_result($result_query, 0, 'risorsa_pianific');
			$risorsa_pianific = html_entity_decode(strip_tags($risorsa_pianific), ENT_QUOTES, $default_charset);

			if($risorsa_pianific != null && $risorsa_pianific != "" && $risorsa_pianific != 0){

				$focus->column_fields['risorsa'] = $risorsa_pianific;

			}

		}

		$smarty->assign('BLOCKS', getBlocks($currentModule, $disp_view, $focus->mode, $focus->column_fields));

	}

	/* kpro@tom020520171019 end */

	$smarty->display('CreateView.tpl');
}