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
	
	$q_materiale_sostituito = "SELECT 
								ms.quantita quantita, 
								prod.productname productname,
								ms.description description
								FROM {$table_prefix}_matsostituiti ms
								INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = ms.matsostituitiid
								INNER JOIN {$table_prefix}_products prod ON prod.productid = ms.prodotto
								WHERE ent.deleted = 0 AND ms.matsostituitiid = ".$materiale_sostituito;
								
	$res_materiale_sostituito = $adb->query($q_materiale_sostituito);
	if($adb->num_rows($res_materiale_sostituito)>0){
		$quantita = $adb->query_result($res_materiale_sostituito,0,'quantita');
		$quantita = html_entity_decode(strip_tags($quantita), ENT_QUOTES,$default_charset);

		$description = $adb->query_result($res_materiale_sostituito,0,'description');
		$description = html_entity_decode(strip_tags($description), ENT_QUOTES,$default_charset);

		$nome_prodotto = $adb->query_result($res_materiale_sostituito,0,'productname');
		$nome_prodotto = html_entity_decode(strip_tags($nome_prodotto), ENT_QUOTES,$default_charset);
		
		$rows[] = array('quantita' => $quantita,
						'description' => $description,
						'nome_prodotto' => $nome_prodotto);
		
	}
	
}
							
$json = json_encode($rows);
print $json;
	
?>