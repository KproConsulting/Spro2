<?php

/* kpro@tom05112015 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2015, Kpro Consulting Srl
 * @package ganttPianificazioni
 * @version 1.0
 */

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

if(!isset($_SESSION['authenticated_user_id'])){
    header("Location: ".$site_URL."/index.php?module=Accounts&action=index");
}
else{
	$current_user->id = $_SESSION['authenticated_user_id'];
}

$rows = array();

if(isset($_GET['project']) && isset($_GET['scadenza'])){
	$project = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['project']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$project = substr($project,0,100);
	
	$scadenza = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['scadenza']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$scadenza = substr($scadenza,0,100);
	
	if(isset($_GET['modifica'])){
		$modifica = html_entity_decode(strip_tags($_GET['modifica']), ENT_QUOTES,$default_charset);
		$modifica = substr($modifica,0,255);
	}
	else{
		$modifica = '';
	}
	
	if(isset($_GET['text'])){
		$text = html_entity_decode(strip_tags($_GET['text']), ENT_QUOTES,$default_charset);
		$text = substr($text,0,255);
	}
	else{
		$text = '';
	}
	
	if(isset($_GET['start_date'])){
		$start_date = html_entity_decode(strip_tags($_GET['start_date']), ENT_QUOTES,$default_charset);
		$start_date = substr($start_date,0,255);
	}
	else{
		$start_date = '';
	}
	
	if(isset($_GET['descrizione'])){
		$descrizione = html_entity_decode(strip_tags($_GET['descrizione']), ENT_QUOTES,$default_charset);
	}
	else{
		$descrizione = '';
	}
	
	if(isset($_GET['parent'])){
		$parent = html_entity_decode(strip_tags($_GET['parent']), ENT_QUOTES,$default_charset);
		$parent = substr($parent,0,100);
		if($parent == ''){
			$parent = 0;
		}
	}
	else{
		$parent = 0;
	}
	
	if($modifica == 'new'){
		$new_scadenza = CRMEntity::getInstance('ProjectMilestone'); 
		$new_scadenza->column_fields['assigned_user_id'] = $current_user->id;
		$new_scadenza->column_fields['projectid'] = $project;
		$new_scadenza->column_fields['projectmilestonename'] = $text;
		$new_scadenza->column_fields['projectmilestonedate'] = $start_date;
		if($parent != 0){
			$new_scadenza->column_fields['operazione_prec'] = $parent;
		}
		$new_scadenza->column_fields['description'] = $descrizione;
		$new_scadenza->save('ProjectMilestone', $longdesc=true, $offline_update=false, $triggerEvent=false); 
		$new_scadenza_id = $new_scadenza->id;
		
	}	
	else if($modifica == 'edit'){
		
		$text = addslashes($text);
		$start_date = addslashes($start_date);
		$descrizione = addslashes($descrizione);
		
		if($projectstatus == 'In pianificazione'){
			
			$upd = "UPDATE {$table_prefix}_projectmilestone SET
					projectmilestonename = '".$text."',
					projectmilestonedate = '".$start_date."',
					operazione_prec = ".$parent.",
					description = '".$descrizione."'
					WHERE projectmilestoneid =".$scadenza;
		}
		else{
			
			$upd = "UPDATE {$table_prefix}_projectmilestone SET
					projectmilestonename = '".$text."',
					projectmilestonedate = '".$start_date."',
					operazione_prec = ".$parent.",
					description = '".$descrizione."'
					WHERE projectmilestoneid =".$scadenza;
		}

		$adb->query($upd);
		
		$new_scadenza_id = $scadenza;
		
	}
	else if($modifica == 'delete'){
		
		$upd_ent = "UPDATE {$table_prefix}_crmentity SET
					deleted = 1
					WHERE crmid =".$scadenza;
		$adb->query($upd_ent);
		$new_scadenza_id = $scadenza;
		
		//Quando elimino una operazione devo anche cancellare tutte le relazione collegate ad essa
		
		$q_relazioni = "SELECT 
						rel.relazioniopid relazioniopid
						FROM {$table_prefix}_relazioniop rel
						INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relazioniopid
						WHERE ent.deleted = 0 AND (rel.operazione = ".$scadenza." OR rel.operazione_target = ".$scadenza.") 
						AND rel.pianificazione = ".$project;
						
		$res_relazioni = $adb->query($q_relazioni);
		$num_relazioni = $adb->num_rows($res_relazioni);

		for($i=0; $i<$num_relazioni; $i++){
			$relazioniopid = $adb->query_result($res_relazioni, $i, 'relazioniopid');
			$relazioniopid = html_entity_decode(strip_tags($relazioniopid), ENT_QUOTES,$default_charset);
			
			$upd_ent = "UPDATE {$table_prefix}_crmentity SET
						deleted = 1
						WHERE crmid =".$relazioniopid;
			$adb->query($upd_ent);
			
		}
		
	}
	
	$rows[] = array('id' => $scadenza,
					'nuovo_id' => $new_scadenza_id);
	
}
							
$json = json_encode($rows);
print $json;
	
?>