<?php
/*+**********************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * All Rights Reserved.
 ************************************************************************************/

/* kpro@tom270420170918 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

 
require_once('modules/VteCore/EditView.php');

global $adb, $table_prefix;

if(file_exists(dirname(__FILE__).'/EditViewKpC.php')){
	require_once(dirname(__FILE__).'/EditViewKpC.php');
}

if($disp_view != 'edit_view') {
    if(!$isduplicate) {
        $focus->column_fields['tcunits']='1';
        $focus->column_fields['worktime']='00:15';	//crmv@14132
        $focus->column_fields['timecardtypes']='Comment';
		$focus->column_fields['newtc']=0;	//crmv@14132
		$focus->column_fields['kp_ore_effettive']='00:05'; /* kpro@bid24112017 */
		
		/* kpro@tom270420170918 */

		if($_REQUEST['function'] == 'GenerateIntervento' && $_REQUEST['record_action'] != ''){

			$query = "SELECT 
						act.activityid activityid,
						act.subject subject,
						act.date_start date_start,
						act.time_start time_start,
						act.due_date due_date,
						act.time_end time_end,
						act.eventstatus eventstatus,
						act.duration_hours duration_hours,
						act.duration_minutes duration_minutes,
						actrel.crmid ticket,
						tick.title ticket_name,
						tick.parent_id parent_id,
						tick.kp_stabilimento kp_stabilimento,
						act.description description
						FROM {$table_prefix}_activity act
						INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = act.activityid
						INNER JOIN {$table_prefix}_seactivityrel actrel ON actrel.activityid = act.activityid
						LEFT JOIN {$table_prefix}_troubletickets tick ON tick.ticketid = actrel.crmid
						WHERE act.activityid = ".$_REQUEST['record_action'];
		
			$result_query = $adb->query($query);
			$num_result = $adb->num_rows($result_query);

			if($num_result > 0){

				$activityid = $adb->query_result($result_query, 0, 'activityid');
				$activityid = html_entity_decode(strip_tags($activityid), ENT_QUOTES, $default_charset);

				$activity_name = $adb->query_result($result_query, 0, 'subject');
				$activity_name = html_entity_decode(strip_tags($activity_name), ENT_QUOTES, $default_charset);

				$ticketid = $adb->query_result($result_query, 0, 'ticket');
				$ticketid = html_entity_decode(strip_tags($ticketid), ENT_QUOTES, $default_charset);
				
				if($ticketid != null && $ticketid != "" && $ticketid != 0){

					$focus->column_fields['ticket_id']= $ticketid;

				}

				$ticket_name = $adb->query_result($result_query, 0, 'ticket_name');
				$ticket_name = html_entity_decode(strip_tags($ticket_name), ENT_QUOTES, $default_charset);

				$date_start = $adb->query_result($result_query, 0, 'date_start');
				$date_start = html_entity_decode(strip_tags($date_start), ENT_QUOTES, $default_charset);

				$duration_hours = $adb->query_result($result_query, 0, 'duration_hours');
				$duration_hours = html_entity_decode(strip_tags($duration_hours), ENT_QUOTES, $default_charset);
				
				$duration_minutes = $adb->query_result($result_query, 0, 'duration_minutes');
				$duration_minutes = html_entity_decode(strip_tags($duration_minutes), ENT_QUOTES, $default_charset);

				$description = $adb->query_result($result_query, 0, 'description');
				$description = html_entity_decode(strip_tags($description), ENT_QUOTES, $default_charset);

				if($duration_hours == null || $duration_hours == "" || $duration_minutes == null || $duration_minutes == ""){
					$duration_hours = 0;
					$duration_minutes = 0;
				}
				else{
					$duration_hours = str_pad($duration_hours, 2, "0", STR_PAD_LEFT);
					$duration_minutes = str_pad($duration_minutes, 2, "0", STR_PAD_LEFT);
					$focus->column_fields['worktime']= $duration_hours.':'.$duration_minutes;
					$focus->column_fields['kp_ore_effettive']= $duration_hours.':'.$duration_minutes; /* kpro@bid24112017 */
				}

				$focus->column_fields['kp_titolo']= $activity_name;
				$focus->column_fields['workdate']= $date_start;
				$focus->column_fields['description']= $description;

			}

			$azienda = $adb->query_result($result_query,0,'parent_id');
			$azienda = html_entity_decode(strip_tags($azienda), ENT_QUOTES,$default_charset);

			$stabilimento = $adb->query_result($result_query,0,'kp_stabilimento');
			$stabilimento = html_entity_decode(strip_tags($stabilimento), ENT_QUOTES,$default_charset);

			$array_costi_trasferta = GetCostiTrasferta($azienda, $stabilimento);

			$focus->column_fields['kp_spese_autostrada'] = $array_costi_trasferta['pedaggio'];
			$focus->column_fields['kp_km_percorsi'] = $array_costi_trasferta['km_percorsi'];
			$focus->column_fields['kp_ore_viaggio'] = $array_costi_trasferta['ore_viaggio'];

			$focus->column_fields['kp_evento_id'] = $_REQUEST['record_action'];

		}

		/* kpro@tom270420170918 end */

        //crmv@19396
		if ($_REQUEST['ticket_id'] != '') {
			global $adb;
			$res = $adb->pquery("SELECT * from {$table_prefix}_troubletickets where ticketid = ?",array($_REQUEST['ticket_id']));
			if ($res && $adb->num_rows($res)>0){
				$focus->column_fields['ticketstatus']=$adb->query_result($res,0,'status');

				$azienda = $adb->query_result($res,0,'parent_id');
				$azienda = html_entity_decode(strip_tags($azienda), ENT_QUOTES,$default_charset);

				$stabilimento = $adb->query_result($res,0,'kp_stabilimento');
				$stabilimento = html_entity_decode(strip_tags($stabilimento), ENT_QUOTES,$default_charset);
			}
			else{
				$azienda = 0;
				$stabilimento = 0;
			}

			$array_costi_trasferta = GetCostiTrasferta($azienda, $stabilimento);

			$focus->column_fields['kp_spese_autostrada'] = $array_costi_trasferta['pedaggio'];
			$focus->column_fields['kp_km_percorsi'] = $array_costi_trasferta['km_percorsi'];
			$focus->column_fields['kp_ore_viaggio'] = $array_costi_trasferta['ore_viaggio'];

			$focus->column_fields['ticket_id'] = $_REQUEST['ticket_id'];				
		}
		//crmv@19396e
		
		$smarty->assign('BLOCKS', getBlocks($currentModule, $disp_view, $focus->mode, $focus->column_fields));
    }
}

$smarty->assign("UMOD", array('LBL_CHANGE'=>$mod_strings['LBL_CHANGE']));
$smarty->assign("FCKEDITOR_DISPLAY",$FCKEDITOR_DISPLAY);

$smarty->display('salesEditView.tpl');

function GetCostiTrasferta($azienda, $stabilimento){
	global $adb, $table_prefix, $default_charset;

	$array_result = array();

	if($azienda != 0 && $azienda != "" && $azienda != null){
		$q_azienda = "SELECT * 
					FROM {$table_prefix}_account 
					WHERE accountid = ".$azienda;
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
		else{
			$distanza = 0;
			$ore_viaggio = 0;
			$pedaggio = 0;
		}
	}
	else{
		$azienda = 0;
		$distanza = 0;
		$ore_viaggio = 0;
		$pedaggio = 0;
	}

	if($stabilimento != 0 && $stabilimento != "" && $stabilimento != null){
		$q_stabilimento = "SELECT * 
					FROM {$table_prefix}_stabilimenti 
					WHERE stabilimentiid = ".$stabilimento;
		$res_stabilimento = $adb->query($q_stabilimento);
		if($adb->num_rows($res_stabilimento) > 0){
			$distanza_stab = $adb->query_result($res_stabilimento, 0, 'kp_km_percorsi');
			$distanza_stab = html_entity_decode(strip_tags($distanza_stab), ENT_QUOTES, $default_charset);
			if($distanza_stab == null || $distanza_stab == ""){
				$distanza_stab = 0;
			}

			$ore_viaggio_stab = $adb->query_result($res_stabilimento, 0, 'kp_ore_viaggio');
			$ore_viaggio_stab = html_entity_decode(strip_tags($ore_viaggio_stab), ENT_QUOTES, $default_charset);
			if($ore_viaggio_stab == null || $ore_viaggio_stab == ""){
				$ore_viaggio_stab = 0;
			}

			$pedaggio_stab = $adb->query_result($res_stabilimento, 0, 'kp_spese_autostrada');
			$pedaggio_stab = html_entity_decode(strip_tags($pedaggio_stab), ENT_QUOTES, $default_charset);
			if($pedaggio_stab == null || $pedaggio_stab == ""){
				$pedaggio_stab = 0;
			}
		}
		else{
			$distanza_stab = 0;
			$ore_viaggio_stab = 0;
			$pedaggio_stab = 0;
		}
	}
	else{
		$stabilimento = 0;
		$distanza_stab = 0;
		$ore_viaggio_stab = 0;
		$pedaggio_stab = 0;
	}

	if($stabilimento != 0){
		$array_result['pedaggio'] = $pedaggio_stab;
		$array_result['km_percorsi'] = $distanza_stab;
		$array_result['ore_viaggio'] = $ore_viaggio_stab;
	}
	else{
		$array_result['pedaggio'] = $pedaggio;
		$array_result['km_percorsi'] = $distanza;
		$array_result['ore_viaggio'] = $ore_viaggio;
	}

	return $array_result;
}