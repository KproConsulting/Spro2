<?php

/* kpro@tom060620171442 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

include_once(__DIR__.'/../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $default_charset;;
session_start();

function AggiornaCheckList($azienda,$giorni_in_scadenza){
	global $adb, $table_prefix,$current_user, $default_charset;;
	
	$q_vecchi = "UPDATE {$table_prefix}_situazchecklist SET
					aggiornato = '0'
					WHERE azienda =".$azienda;
	$adb->query($q_vecchi);
	
	$q_componenti = "SELECT 
						comp.compimpiantoid compimpiantoid
						FROM {$table_prefix}_compimpianto comp
						INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = comp.compimpiantoid
						INNER JOIN {$table_prefix}_impianti imp ON imp.impiantiid = comp.impianto
						WHERE ent.deleted = 0 AND comp.stato_componente = 'Attivo' AND imp.azienda = ".$azienda;
	
	$res_componenti = $adb->query($q_componenti);
	$num_componenti = $adb->num_rows($res_componenti);

	for($i=0; $i<$num_componenti; $i++){
		$compimpiantoid = $adb->query_result($res_componenti,$i,'compimpiantoid');
		$compimpiantoid = html_entity_decode(strip_tags($compimpiantoid), ENT_QUOTES,$default_charset);
		
		aggiornaSituazioneComponente($compimpiantoid, $azienda, $giorni_in_scadenza);
		
	}
	
	$del_vecchi = "SELECT situazchecklistid FROM {$table_prefix}_situazchecklist 
					WHERE aggiornato = '0' AND azienda =".$azienda;
	$res_del_vecchi = $adb->query($del_vecchi);
	$num_del_vecchi = $adb->num_rows($res_del_vecchi);
	for($i=0; $i<$num_del_vecchi; $i++){
		$vecchioid = $adb->query_result($res_del_vecchi,$i,'situazchecklistid');
		
		$q_delete = "UPDATE {$table_prefix}_crmentity SET
						deleted = 1
						WHERE setype = 'SituazCheckList' AND crmid = ".$vecchioid;
		$adb->query($q_delete);
		
		/*$q_delete1 = "DELETE FROM {$table_prefix}_situazchecklist WHERE situazchecklistid =".$vecchioid;
		$adb->query($q_delete1);
		$q_delete2 = "DELETE FROM {$table_prefix}_crmentity WHERE setype = 'SituazCheckList' AND crmid =".$vecchioid;
		$adb->query($q_delete2);*/
		
	}		
	
}

function aggiornaSituazioneComponente($compimpiantoid, $azienda, $giorni_in_scadenza){
	global $adb, $table_prefix, $default_charset;
	
	$q_componenti = "SELECT 
						comp.compimpiantoid compimpiantoid,
						comp.data data,
						imp.stabilimento stabilimento,
						comp.impianto impianto,
						comp.kp_gm_ubicazione kp_gm_ubicazione
						FROM {$table_prefix}_compimpianto comp
						INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = comp.compimpiantoid
						INNER JOIN {$table_prefix}_impianti imp ON imp.impiantiid = comp.impianto
						WHERE ent.deleted = 0 AND comp.compimpiantoid = ".$compimpiantoid;
	
	$res_componenti = $adb->query($q_componenti);
	$num_componenti = $adb->num_rows($res_componenti);

	if( $num_componenti > 0){

		$data_componente = $adb->query_result($res_componenti,0,'data');
		$data_componente = html_entity_decode(strip_tags($data_componente), ENT_QUOTES,$default_charset);

		$stabilimento = $adb->query_result($res_componenti,0,'stabilimento');
		$stabilimento = html_entity_decode(strip_tags($stabilimento), ENT_QUOTES,$default_charset);

		$impianto = $adb->query_result($res_componenti,0,'impianto');
		$impianto = html_entity_decode(strip_tags($impianto), ENT_QUOTES,$default_charset);

		$ubicazione_componente = $adb->query_result($res_componenti,0,'kp_gm_ubicazione');
		$ubicazione_componente = html_entity_decode(strip_tags($ubicazione_componente), ENT_QUOTES,$default_charset);
		if($ubicazione_componente == null){
			$ubicazione_componente = "";
		}
		
		$q_check_list = "(SELECT 
							entrel.relcrmid check_list_id 
							FROM {$table_prefix}_compimpianto comp
							INNER JOIN {$table_prefix}_crmentityrel entrel ON entrel.crmid = comp.compimpiantoid
							INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = entrel.relcrmid
							WHERE ent.deleted = 0 AND entrel.module = 'CompImpianto' AND entrel.relmodule = 'CheckLists' AND comp.compimpiantoid = ".$compimpiantoid.")
							UNION
							(SELECT 
							entrel2.crmid check_list_id 
							FROM {$table_prefix}_compimpianto comp2
							INNER JOIN {$table_prefix}_crmentityrel entrel2 ON entrel2.relcrmid = comp2.compimpiantoid
							INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = entrel2.crmid
							WHERE ent2.deleted = 0 AND entrel2.relmodule = 'CompImpianto' AND entrel2.module = 'CheckLists' AND comp2.compimpiantoid = ".$compimpiantoid.")";
		
		$res_check_list = $adb->query($q_check_list);
		$num_check_list = $adb->num_rows($res_check_list);

		for($y=0; $y<$num_check_list; $y++){
			$check_list_id = $adb->query_result($res_check_list,$y,'check_list_id');
			$check_list_id = html_entity_decode(strip_tags($check_list_id), ENT_QUOTES,$default_charset);
			
			$q_dati_checklist = "SELECT 
									checkl.cod_check_list cod_check_list,
									checkl.tempo_previsto tempo_previsto,
									checkl.kp_specialita_check kp_specialita_check,
									checkl.frequenza_checklist frequenza_checklist,
									checkl.kp_freq_ck_generica kp_freq_ck_generica,
									checkl.description description
									from {$table_prefix}_checklists checkl
									inner join {$table_prefix}_crmentity ent on ent.crmid = checkl.checklistsid
									where ent.deleted = 0 and checkl.checklistsid = ".$check_list_id;
			
			$res_dati_checklist = $adb->query($q_dati_checklist);
			if($adb->num_rows($res_dati_checklist)>0){
				$cod_check_list = $adb->query_result($res_dati_checklist, 0, 'cod_check_list');
				$cod_check_list = html_entity_decode(strip_tags($cod_check_list), ENT_QUOTES,$default_charset);
				
				$tempo_previsto = $adb->query_result($res_dati_checklist, 0, 'tempo_previsto');
				$tempo_previsto = html_entity_decode(strip_tags($tempo_previsto), ENT_QUOTES,$default_charset);
				if($tempo_previsto == null || $tempo_previsto == ""){
					$tempo_previsto = 0;
				}
				
				$specialita_check = $adb->query_result($res_dati_checklist, 0, 'kp_specialita_check');
				$specialita_check = html_entity_decode(strip_tags($specialita_check), ENT_QUOTES,$default_charset);
				
				$frequenza_checklist = $adb->query_result($res_dati_checklist, 0, 'frequenza_checklist');
				$frequenza_checklist = html_entity_decode(strip_tags($frequenza_checklist), ENT_QUOTES,$default_charset);

				$frequenza_checklist_fittizia = $adb->query_result($res_dati_checklist, 0, 'kp_freq_ck_generica');
				$frequenza_checklist_fittizia = html_entity_decode(strip_tags($frequenza_checklist_fittizia), ENT_QUOTES,$default_charset);
				
				$description = $adb->query_result($res_dati_checklist, 0, 'description');
				$description = html_entity_decode(strip_tags($description), ENT_QUOTES,$default_charset);
				
				$q_gestione_avvisi = "select 
										gest.giorni_in_scadenza giorni_in_scadenza
										from vte_gestioneavvisi gest
										inner join vte_crmentity ent on ent.crmid = gest.gestioneavvisiid
										where ent.deleted = 0 and gest.tipo_avviso = 'Check List' and gest.stabilimento = ".$azienda." and gest.frequenza_checklist= '".$frequenza_checklist."'";
				$res_gestione_avvisi = $adb->query($q_gestione_avvisi);
				if($adb->num_rows($res_gestione_avvisi)>0){
					$giorni_in_scadenza_avviso = $adb->query_result($res_gestione_avvisi, 0, 'giorni_in_scadenza');
					$giorni_in_scadenza_avviso = html_entity_decode(strip_tags($giorni_in_scadenza_avviso), ENT_QUOTES,$default_charset);
					
					if($giorni_in_scadenza_avviso != null && $giorni_in_scadenza_avviso  != "" && $giorni_in_scadenza_avviso != 0){
						$giorni_in_scadenza = $giorni_in_scadenza_avviso;
					}
					
				}
				
			}
			else{
				$cod_check_list = "";
				$tempo_previsto = 0;
				$specialita_check = "";
				$frequenza_checklist = "";
				$description = "";
			}
	
			$data_cor = date ("Y-m-d");
			list($anno_cor,$mese_cor,$giorno_cor) = explode("-",$data_cor);
			$in_scadenza = date("Y-m-d",mktime(0,0,0,$mese_cor,$giorno_cor + $giorni_in_scadenza,$anno_cor));
			
			$q_manutenzioni = "SELECT 
								man.manutenzioniid manutenzioneid, 
								man.data_manutenzione data_manutenzione,
								man.manutenzione_name manutenzione_name,
								rig.kprighemanutenzioniid kprighemanutenzioniid,
								rig.kp_data_scadenza data_scad_manut				
								FROM {$table_prefix}_manutenzioni man
								INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = man.manutenzioniid
								INNER JOIN {$table_prefix}_kprighemanutenzioni rig ON rig.kp_manutenzione = man.manutenzioniid
								INNER JOIN {$table_prefix}_crmentity entrig ON entrig.crmid = rig.kprighemanutenzioniid
								WHERE ent.deleted = 0 AND entrig.deleted = 0 AND man.stato_manutenzione = 'Eseguita' AND rig.kp_componente = ".$compimpiantoid." AND rig.kp_check_list = ".$check_list_id."
								ORDER BY rig.kp_data_scadenza DESC";
			
			$res_manutenzioni = $adb->query($q_manutenzioni);
			if($adb->num_rows($res_manutenzioni)>0){
				$manutenzioneid = $adb->query_result($res_manutenzioni, 0, 'manutenzioneid');
				$manutenzioneid = html_entity_decode(strip_tags($manutenzioneid), ENT_QUOTES, $default_charset);

				$data_scad_manut = $adb->query_result($res_manutenzioni,0,'data_scad_manut');
				$data_scad_manut = html_entity_decode(strip_tags($data_scad_manut), ENT_QUOTES, $default_charset);
				if($data_scad_manut == null){
					$data_scad_manut = '';
				}
				$data_manutenzione = $adb->query_result($res_manutenzioni,0,'data_manutenzione');
				$data_manutenzione = html_entity_decode(strip_tags($data_manutenzione), ENT_QUOTES, $default_charset);
				if($data_manutenzione == null){
					$data_manutenzione = '';
				}
				
				$manutenzione_name = $adb->query_result($res_manutenzioni,0,'manutenzione_name');
				$manutenzione_name = html_entity_decode(strip_tags($manutenzione_name), ENT_QUOTES, $default_charset);
				if($manutenzione_name == null){
					$manutenzione_name = '';
				}
				
				if($data_scad_manut <= $data_cor){
					$stato_check_list = 'Scaduta';
				}
				elseif($data_scad_manut > $data_cor && $data_scad_manut <= $in_scadenza){
					$stato_check_list = 'In scadenza';
				}
				else{
					$stato_check_list = 'Eseguita';
				}
			}
			else{
				if($data_componente != '' && $data_componente != null && $frequenza_checklist_fittizia != '' && $frequenza_checklist_fittizia != null){
					$manutenzione_name = 'Manutenzione Fittizia Autogenerata';
					$manutenzioneid = 0;
					$data_manutenzione = '';
					$data_scad_manut = CalcolaScadenzaManutenzioneFittizia($data_componente, $frequenza_checklist_fittizia);

					if($data_scad_manut <= $data_cor){
						$stato_check_list = 'Scaduta';
					}
					elseif($data_scad_manut > $data_cor && $data_scad_manut <= $in_scadenza){
						$stato_check_list = 'In scadenza';
					}
					else{
						$stato_check_list = 'Eseguita';
					}
				}
				else{
					$manutenzione_name = '';
					$manutenzioneid = 0;
					$data_scad_manut = '';
					$data_manutenzione = '';
					$stato_check_list = 'Non eseguita';
				}
			}

			$q_prossima_manutenzioni = "SELECT 
										man.manutenzioniid manutenzioneid, 
										man.data_manutenzione data_manutenzione,
										man.manutenzione_name manutenzione_name,
										rig.kprighemanutenzioniid kprighemanutenzioniid,
										rig.kp_data_scadenza data_scad_manut				
										FROM {$table_prefix}_manutenzioni man
										INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = man.manutenzioniid
										INNER JOIN {$table_prefix}_kprighemanutenzioni rig ON rig.kp_manutenzione = man.manutenzioniid
										INNER JOIN {$table_prefix}_crmentity entrig ON entrig.crmid = rig.kprighemanutenzioniid
										WHERE ent.deleted = 0 AND entrig.deleted = 0 AND (man.stato_manutenzione = 'Creata' OR man.stato_manutenzione = 'Pianificata' OR man.stato_manutenzione = 'In esecuzione') AND rig.kp_componente = ".$compimpiantoid." AND rig.kp_check_list = ".$check_list_id."
										ORDER BY man.data_manutenzione ASC";
			
			$res_prossima_manutenzioni = $adb->query($q_prossima_manutenzioni);
			if($adb->num_rows($res_prossima_manutenzioni)>0){
				$pros_manutenzioneid = $adb->query_result($res_prossima_manutenzioni,0,'manutenzioneid');
				$pros_manutenzioneid = html_entity_decode(strip_tags($pros_manutenzioneid), ENT_QUOTES, $default_charset);
			}
			else{
				$pros_manutenzioneid = 0;
			}
			
			$q_verifica = "SELECT 
							sit.situazchecklistid situazchecklistid 
							FROM {$table_prefix}_situazchecklist sit
							INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sit.situazchecklistid
							WHERE ent.deleted = 0 AND sit.check_list = ".$check_list_id." AND sit.componente = ".$compimpiantoid;
			
			$res_verifica = $adb->query($q_verifica);
			if($adb->num_rows($res_verifica)>0){
				
				$situazchecklistid = $adb->query_result($res_verifica,0,'situazchecklistid');
				$situazchecklistid = html_entity_decode(strip_tags($situazchecklistid), ENT_QUOTES, $default_charset);
				
				$cod_check_list = addslashes($cod_check_list);
				$tempo_previsto = addslashes($tempo_previsto);
				$specialita_check = addslashes($specialita_check);
				$frequenza_checklist = addslashes($frequenza_checklist);
				$description = addslashes($description);
				
				$q_upd_sit = "UPDATE {$table_prefix}_situazchecklist SET
								impianto = ".$impianto.",
								azienda = ".$azienda.",
								componente = ".$compimpiantoid.",
								check_list = ".$check_list_id.",
								data_ult_man = '".$data_manutenzione."',
								n_ult_man = '".$manutenzione_name."',
								data_scad_man = '".$data_scad_manut."',
								prossima_manutenzione = ".$pros_manutenzioneid.",
								stato_sit_check = '".$stato_check_list."',
								kp_specialita_check = '".$specialita_check."',
								cod_check_list = '".$cod_check_list."',
								tempo_previsto = ".$tempo_previsto.",
								frequenza_checklist = '".$frequenza_checklist."',
								kp_gm_ubicazione = '".$ubicazione_componente."',
								aggiornato = '1',
								description = '".$description."'
								WHERE situazchecklistid =".$situazchecklistid;
				
				$adb->query($q_upd_sit);
			
			}
			else{
				$situazione_check_list = CRMEntity::getInstance('SituazCheckList'); 
				$situazione_check_list->column_fields['assigned_user_id'] = '1';
				$situazione_check_list->column_fields['impianto'] = $impianto;
				$situazione_check_list->column_fields['azienda'] = $azienda;
				$situazione_check_list->column_fields['componente'] = $compimpiantoid;
				$situazione_check_list->column_fields['check_list'] = $check_list_id;
				$situazione_check_list->column_fields['stato_sit_check'] = $stato_check_list;
				if($pros_manutenzioneid != 0 && $pros_manutenzioneid != ''){
					$situazione_check_list->column_fields['prossima_manutenzione'] = $pros_manutenzioneid;
				}
				$situazione_check_list->column_fields['data_ult_man'] = $data_manutenzione;
				$situazione_check_list->column_fields['n_ult_man'] = $manutenzione_name;
				$situazione_check_list->column_fields['data_scad_man'] = $data_scad_manut;
				$situazione_check_list->column_fields['kp_specialita_check'] = $specialita_check;
				$situazione_check_list->column_fields['cod_check_list'] = $cod_check_list;
				$situazione_check_list->column_fields['tempo_previsto'] = $tempo_previsto;
				$situazione_check_list->column_fields['frequenza_checklist'] = $frequenza_checklist;
				$situazione_check_list->column_fields['kp_gm_ubicazione'] = $ubicazione_componente;
				$situazione_check_list->column_fields['description'] = $description;
				$situazione_check_list->column_fields['aggiornato'] = '1';
				$situazione_check_list->save('SituazCheckList', $longdesc=true, $offline_update=false, $triggerEvent=false); 
			}
		}
		
	}
	
}

function CalcolaScadenzaManutenzioneFittizia($data, $frequenza){
    $tipo_frequenza = "";
	$codice_tipo_frequenza = substr($frequenza,0,1);
    $frequenza = substr($frequenza,1);
	if($codice_tipo_frequenza == 'M'){
		$tipo_frequenza = "months";
	}
	else if($codice_tipo_frequenza == 'D'){
		$tipo_frequenza = "days";
	}
	
	if($tipo_frequenza != ""){
		$data_da_aggiungere=date_create($data);
		date_add($data_da_aggiungere,date_interval_create_from_date_string($frequenza." ".$tipo_frequenza));
		return date_format($data_da_aggiungere,"Y-m-d");
	}
	else{
		return date("Y-m-d");
	}
}

?>