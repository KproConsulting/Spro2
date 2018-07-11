<?php

/* kpro@tom05112015 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2015, Kpro Consulting Srl
 * @package ganttPianificazioni
 * @version 1.0
 */

/* kpro@tom070220171115 */
require_once('Utility.php');
/* kpro@tom070220171115 end */

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

if(isset($_GET['project'])){
	$project = $_GET['project'];
}
else{
	die; 
}

if(!isset($_SESSION['authenticated_user_id'])){
    header("Location: ".$site_URL."/index.php?module=Accounts&action=index");
}
else{
    $current_user->id = $_SESSION['authenticated_user_id'];
}

$rows = array();

if(isset($_GET['project'])){
	$project = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['project']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$project = substr($project,0,100);
	
	$q_scadenze = "SELECT 
                        mil.projectmilestoneid projectmilestoneid,
                        mil.projectmilestonename projectmilestonename,
                        mil.projectmilestone_no projectmilestone_no,
                        mil.projectmilestonedate projectmilestonedate,
                        mil.projectmilestype projectmilestype,
                        mil.operazione_prec operazione_padre,
                        mil.description description
                        FROM {$table_prefix}_projectmilestone mil
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = mil.projectmilestoneid
                        WHERE ent.deleted = 0 AND mil.projectid = ".$project."
                        ORDER BY mil.projectmilestonedate ASC";
						
	$res_scadenze = $adb->query($q_scadenze);
	$num_scadenze = $adb->num_rows($res_scadenze);

	for($i=0; $i<$num_scadenze; $i++){
		$projectmilestoneid = $adb->query_result($res_scadenze, $i, 'projectmilestoneid');
		$projectmilestoneid = html_entity_decode(strip_tags($projectmilestoneid), ENT_QUOTES,$default_charset);
		
		$projectmilestonename = $adb->query_result($res_scadenze, $i, 'projectmilestonename');
		$projectmilestonename = html_entity_decode(strip_tags($projectmilestonename), ENT_QUOTES,$default_charset);
		
		$projectmilestone_no = $adb->query_result($res_scadenze, $i, 'projectmilestone_no');
		$projectmilestone_no = html_entity_decode(strip_tags($projectmilestone_no), ENT_QUOTES,$default_charset);
		
		$description = $adb->query_result($res_scadenze, $i, 'description');
		$description = html_entity_decode(strip_tags($description), ENT_QUOTES,$default_charset);
		
		$operazione_padre = $adb->query_result($res_scadenze, $i, 'operazione_padre');
		$operazione_padre = html_entity_decode(strip_tags($operazione_padre), ENT_QUOTES,$default_charset);
		/* kpro@tom070220171115 */
		if($operazione_padre == null || $operazione_padre == 0 || $operazione_padre == ""){
			$operazione_padre = 0;
		}
		else{
			$operazione_padre = verificaEsistenzaRecord($operazione_padre);	//Verifica che l'operazione padre non sia stata cancellata
		}
		/* kpro@tom070220171115 end */
		
		$risorsa = "";
        $nome_risorsa = "";
		
		$projectmilestonedate = $adb->query_result($res_scadenze, $i, 'projectmilestonedate');
		$projectmilestonedate = html_entity_decode(strip_tags($projectmilestonedate), ENT_QUOTES,$default_charset);
		if($projectmilestonedate != '' && $projectmilestonedate != null && $projectmilestonedate != '0000-00-00'){
			list($anno,$mese,$giorno) = explode("-",$projectmilestonedate);
			$projectmilestonedate_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
		}
		else{
			$anno = '';
			$mese = '';
			$giorno = '';
			$projectmilestonedate = '';
			$projectmilestonedate_inv = '';
		}

		/* kpro@tom070220171115 */
		if($operazione_padre == 0){
			$operazione_padre = "";
		}
		/* kpro@tom070220171115 */
		
		$rows[] = array('projectmilestoneid' => $projectmilestoneid,
						'projectmilestonename' => $projectmilestonename,
						'projectmilestone_no' => $projectmilestone_no,
						'projectmilestonedate' => $projectmilestonedate,
						'projectmilestonedate_inv' => $projectmilestonedate_inv,
						'description' => $description,
						'anno' => $anno,
						'mese' => $mese,
						'giorno' => $giorno,
						'operazione_padre' => $operazione_padre,
						'risorsa' => $risorsa,
						'nome_risorsa' => $nome_risorsa,
						'data_fine_pianificata' => $projectmilestonedate,
						'data_fine_pianificata_inv' => $projectmilestonedate_inv,
						'data_inizio_pian' => $projectmilestonedate,
						'data_inizio_pian_inv' => $projectmilestonedate_inv);
	}
	
}
							
$json = json_encode($rows);
print $json;
	
?>