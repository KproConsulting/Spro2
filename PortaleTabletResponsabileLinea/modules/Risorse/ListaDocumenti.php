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

$rows = array();

if(isset($_GET['risorsa'])){
    $risorsa = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['risorsa']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $risorsa = substr($risorsa,0,100);
    
    if(isset($_GET['nome_documento'])){
        $nome_documento = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['nome_documento']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $nome_documento = substr($nome_documento,0,255);
    }
    else{
        $nome_documento = '';
    }
    
    if(isset($_GET['data'])){
        $data = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $data = substr($data,0,255);
        if($data != null && $data != ""){
			list($giorno, $mese, $anno) = explode("/", $data);
            $data = date("Y-m-d", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
    }
    else{
        $data = '';
    }
    
    if(isset($_GET['data_scadenza'])){
        $data_scadenza = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data_scadenza']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $data_scadenza = substr($data_scadenza,0,255);
        if($data_scadenza != null && $data_scadenza != ""){
			list($giorno, $mese, $anno) = explode("/", $data_scadenza);
            $data_scadenza = date("Y-m-d", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
    }
    else{
        $data_scadenza = '';
    }
    
    if(isset($_GET['stato_documento'])){
        $stato_documento = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['stato_documento']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
		
		if($stato_documento != ""){
			$stato_documento = explode(',', $stato_documento);
			
			$lista_stati = "";
				
			foreach($stato_documento as $stato){
				if($lista_stati == ""){
					$lista_stati = "'".$stato."'";
				}
				else{
					$lista_stati .= ",'".$stato."'";
				}
			}
		}
		else{
			$lista_stati = "";
		}
		
    }
    else{
        $lista_stati = "";
    }
	
	$q_documenti = "SELECT attac.attachmentsid attachmentsid, 
					attac.name name, 
					attac.path path, 
					notes.title title, 
					notes.notesid notesid, 
					notes.folderid cartella_id,
					notes.data_scadenza data_scadenza,
					notes.stato_documento stato_documento,
					date(ent.createdtime) data_documento,
					ent.createdtime createdtime, 
					ent.modifiedtime modifiedtime 
					FROM {$table_prefix}_notes notes 
					INNER JOIN {$table_prefix}_notescf notescf ON notescf.notesid = notes.notesid 
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = notes.notesid 
					LEFT JOIN {$table_prefix}_senotesrel senote ON senote.notesid = notes.notesid
					LEFT JOIN {$table_prefix}_seattachmentsrel seattac ON seattac.crmid = notes.notesid 
					LEFT JOIN {$table_prefix}_attachments attac ON attac.attachmentsid = seattac.attachmentsid 
					WHERE ent.deleted = 0 AND senote.crmid = ".$risorsa;
					
	if($nome_documento != ""){
		$q_documenti .= " and notes.title like '%".$nome_documento."%'";
	}
	if($lista_stati != ""){
		$q_documenti .= " and notes.stato_documento in (".$lista_stati.")";
	}
	if($data != ""){
		$q_documenti .= " and date(ent.createdtime) = '".$data."'";
	}
	if($data_scadenza != ""){
		$q_documenti .= " and notes.data_scadenza = '".$data_scadenza."'";
	}
	
	$q_documenti .= " ORDER BY ent.createdtime DESC";

	$res_documenti = $adb->query($q_documenti);
	$num_documenti = $adb->num_rows($res_documenti);
	
	for($i=0; $i < $num_documenti; $i++){
		$title = $adb->query_result($res_documenti, $i, 'title');
		$title = html_entity_decode(strip_tags($title), ENT_QUOTES,$default_charset);
		
		$notesid = $adb->query_result($res_documenti, $i, 'notesid');
		$notesid = html_entity_decode(strip_tags($notesid), ENT_QUOTES,$default_charset);
		
		$attachmentsid = $adb->query_result($res_documenti, $i, 'attachmentsid');
		$attachmentsid = html_entity_decode(strip_tags($attachmentsid), ENT_QUOTES,$default_charset);
		
		$createdtime = $adb->query_result($res_documenti, $i, 'createdtime');
		$createdtime = html_entity_decode(strip_tags($createdtime), ENT_QUOTES,$default_charset);
		
		$modifiedtime = $adb->query_result($res_documenti, $i, 'modifiedtime');
		$modifiedtime = html_entity_decode(strip_tags($modifiedtime), ENT_QUOTES,$default_charset);
		
		$data_documento = $adb->query_result($res_documenti, $i, 'data_documento');
		$data_documento = html_entity_decode(strip_tags($data_documento), ENT_QUOTES,$default_charset);
		if($data_documento != null && $data_documento != ""){
			list($anno, $mese, $giorno) = explode("-", $data_documento);
            $data_documento = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
		
		$data_scadenza = $adb->query_result($res_documenti, $i, 'data_scadenza');
		$data_scadenza = html_entity_decode(strip_tags($data_scadenza), ENT_QUOTES,$default_charset);
		if($data_scadenza != null && $data_scadenza != ""){
			list($anno, $mese, $giorno) = explode("-", $data_scadenza);
            $data_scadenza = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
		}
		
		$stato_documento = $adb->query_result($res_documenti, $i, 'stato_documento');
		$stato_documento = html_entity_decode(strip_tags($stato_documento), ENT_QUOTES,$default_charset);
		
		$rows[] = array('notesid' => $notesid,
						'attachmentsid' => $attachmentsid,
						'title' => $title,
						'createdtime' => $createdtime,
						'modifiedtime' => $modifiedtime,
						'data_documento' => $data_documento,
						'data_scadenza' => $data_scadenza,
						'stato_documento' => $stato_documento);
		
	}
	
	$json = json_encode($rows);
	print $json;
	
}
?>
