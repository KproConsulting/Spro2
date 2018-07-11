<?php

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $default_charset;

$rows = array();

if(isset($_GET['materiale_sostituito'])){
	$materiale_sostituito = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['materiale_sostituito']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$materiale_sostituito = substr($materiale_sostituito,0,100);
	
	if(isset($_GET['quantita'])){
		$quantita = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['quantita']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
		$quantita = substr($quantita,0,100);
	}
	else{
		$quantita = 0;
	}
	
	if(isset($_GET['note'])){
		$note = addslashes(html_entity_decode(strip_tags($_GET['note']), ENT_QUOTES,$default_charset));
		if($note == null){
			$note = '';
		}
	}
	else{
		$note = '';
	}
	
	$upd_mat_sost = "UPDATE {$table_prefix}_matsostituiti SET 
						quantita = ".$quantita.",
						description = '".$note."'
						WHERE matsostituitiid =".$materiale_sostituito;
	$adb->query($upd_mat_sost);
	
	$rows[] = array('result' => 'ok');
									
	$json = json_encode($rows);
	print $json;

}
	
?>