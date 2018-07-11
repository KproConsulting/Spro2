<?php

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $default_charset;

$rows = array();

if(isset($_GET['manutenzione'])){
	$manutenzione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['manutenzione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$manutenzione = substr($manutenzione,0,100);
	
	if(isset($_GET['componente'])){
		$componente = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['componente']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
		$componente = substr($componente,0,100);
	}
	else{
		$componente = 0;
	}

	$q_materiali_sostituiti = "SELECT
								ms.matsostituitiid matsostituitiid,
								ms.matsostituiti_no matsostituiti_no,
								ms.manutenzione manutenzione,
								ms.componente_imp componente_imp,
								ms.prodotto prodotto,
								ms.quantita quantita,
								ms.data_sostituzione data_sostituzione,
								prod.productname productname,
								ms.description description,
								comp.nome_componente nome_componente
								FROM {$table_prefix}_matsostituiti ms
								INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = ms.matsostituitiid
								INNER JOIN {$table_prefix}_compimpianto comp ON comp.compimpiantoid = ms.componente_imp
								LEFT JOIN {$table_prefix}_products prod ON prod.productid = ms.prodotto
								WHERE ent.deleted = 0 AND ms.manutenzione = ".$manutenzione;
	if($componente != 0){
		$q_materiali_sostituiti .= " AND ms.componente_imp = ".$componente; 
	}
	$q_materiali_sostituiti .= " ORDER BY ms.matsostituitiid ASC";
	
	$res_materiali_sostituiti = $adb->query($q_materiali_sostituiti);
	$num_materiali_sostituiti = $adb->num_rows($res_materiali_sostituiti);
	
	for($i=0; $i<$num_materiali_sostituiti; $i++){
		$matsostituitiid = $adb->query_result($res_materiali_sostituiti, $i, 'matsostituitiid');
		$matsostituiti_no = $adb->query_result($res_materiali_sostituiti, $i, 'matsostituiti_no');
		$manutenzione = $adb->query_result($res_materiali_sostituiti, $i, 'manutenzione');
		$componente_imp = $adb->query_result($res_materiali_sostituiti, $i, 'componente_imp');
		$prodotto = $adb->query_result($res_materiali_sostituiti, $i, 'prodotto');
		$quantita = $adb->query_result($res_materiali_sostituiti, $i, 'quantita');
		$data_sostituzione = $adb->query_result($res_materiali_sostituiti, $i, 'data_sostituzione');
		$productname = $adb->query_result($res_materiali_sostituiti, $i, 'productname');
		$productname = html_entity_decode(strip_tags($productname), ENT_QUOTES,$default_charset);
		if($productname == null){
			$productname = '';
		}
		
		$description = $adb->query_result($res_materiali_sostituiti, $i, 'description');
		$description = html_entity_decode(strip_tags($description), ENT_QUOTES,$default_charset);
		
		$nome_componente = $adb->query_result($res_materiali_sostituiti, $i, 'nome_componente');
		$nome_componente = html_entity_decode(strip_tags($nome_componente), ENT_QUOTES,$default_charset);
		
		$rows[] = array('matsostituitiid' => $matsostituitiid,
						'matsostituiti_no' => $matsostituiti_no,
						'manutenzione' => $manutenzione,
						'componente_imp' => $componente_imp,
						'prodotto' => $prodotto,
						'quantita' => $quantita,
						'data_sostituzione' => $data_sostituzione,
						'productname' => $productname,
						'description' => $description,
						'nome_componente' => $nome_componente);
		
	}
	
}
					
$json = json_encode($rows);
print $json;
	
?>