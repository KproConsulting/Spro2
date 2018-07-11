<?php

/* kpro@tom05112015 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2015, Kpro Consulting Srl
 * @package ganttPianificazioni
 * @version 1.0
 */

require_once('kp2.php');

$rows = array();

if(isset($_GET['project'])){
	$project = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['project']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	$project = substr($project,0,100);
	
	$q_operazioni = "SELECT 
                            pro.projecttaskid projecttaskid,
                            pro.projecttask_no projecttask_no,
                            pro.projecttaskname projecttaskname,
                            pro.projecttasktype projecttasktype,
                            pro.projecttaskpriority projecttaskpriority,
                            pro.projecttaskprogress projecttaskprogress,
                            pro.startdate startdate,
                            pro.enddate enddate,
                            pro.operazione_prec operazione_prec,
                            pro.lead_time lead_time,
                            pro.risorsa risorsa_inc,
                            pro.data_fine_pianificata data_fine_pianificata,
                            pro.data_inizio_pian data_inizio_pian,
                            pro.lead_time_pian lead_time_pian,
                            pro.giorni_scostamento giorni_scostamento,
                            pro.tipo_scostamento_task tipo_scostamento_task,
                            pro.tipo_attivita_task tipo_attivita_task,
                            pro.ore_lavorate ore_lavorate,
                            pro.ore_previste ore_previste,
                            pro.kp_ora_inizio_task kp_ora_inizio_task,
                            pro.kp_ora_fine_task kp_ora_fine_task,
                            pro.kp_servizio kp_servizio,
							pro.kp_commessa commessa,
                            ent.smownerid assegnatario,
                            pro.description descrizione,
                            project.projectstatus projectstatus
                            FROM {$table_prefix}_projecttask pro
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = pro.projecttaskid
                            INNER JOIN {$table_prefix}_projecttaskcf procf ON procf.projecttaskid = pro.projecttaskid
                            INNER JOIN {$table_prefix}_project project ON project.projectid = pro.projectid
                            WHERE ent.deleted = 0 AND pro.projectid = ".$project."
                            ORDER BY pro.startdate ASC";
					
	$res_operazioni = $adb->query($q_operazioni);
	$num_operazioni = $adb->num_rows($res_operazioni);

	for($i=0; $i<$num_operazioni; $i++){
		$projecttaskid = $adb->query_result($res_operazioni, $i, 'projecttaskid');
		$projecttaskid = html_entity_decode(strip_tags($projecttaskid), ENT_QUOTES,$default_charset);
		
		$projecttask_no = $adb->query_result($res_operazioni, $i, 'projecttask_no');
		$projecttask_no = html_entity_decode(strip_tags($projecttask_no), ENT_QUOTES,$default_charset);
		
		$projecttaskname = $adb->query_result($res_operazioni, $i, 'projecttaskname');
		$projecttaskname = html_entity_decode(strip_tags($projecttaskname), ENT_QUOTES,$default_charset);
		
		$projecttasktype = $adb->query_result($res_operazioni, $i, 'projecttasktype');
		$projecttasktype = html_entity_decode(strip_tags($projecttasktype), ENT_QUOTES,$default_charset);
		
		$projecttaskpriority = $adb->query_result($res_operazioni, $i, 'projecttaskpriority');
		$projecttaskpriority = html_entity_decode(strip_tags($projecttaskpriority), ENT_QUOTES,$default_charset);
		
		$tipo_attivita_task = $adb->query_result($res_operazioni, $i, 'tipo_attivita_task');
		$tipo_attivita_task = html_entity_decode(strip_tags($tipo_attivita_task), ENT_QUOTES,$default_charset);
		
		$ore_lavorate = $adb->query_result($res_operazioni, $i, 'ore_lavorate');
		$ore_lavorate = html_entity_decode(strip_tags($ore_lavorate), ENT_QUOTES,$default_charset);
		if($ore_lavorate == '' || $ore_lavorate == null){
			$ore_lavorate = 0;
		}
		
		$ore_previste = $adb->query_result($res_operazioni, $i, 'ore_previste');
		$ore_previste = html_entity_decode(strip_tags($ore_previste), ENT_QUOTES,$default_charset);
		if($ore_previste == '' || $ore_previste == null){
			$ore_previste = 0;
		}
		
		$kp_ora_inizio_task = $adb->query_result($res_operazioni, $i, 'kp_ora_inizio_task');
		$kp_ora_inizio_task = html_entity_decode(strip_tags($kp_ora_inizio_task), ENT_QUOTES,$default_charset);
		if($kp_ora_inizio_task == null || $kp_ora_inizio_task == ''){
			$kp_ora_inizio_task = "00:00";
		}
		list($ora_inizio,$minuti_inizio) = explode(":",$kp_ora_inizio_task);
				
		$kp_ora_fine_task = $adb->query_result($res_operazioni, $i, 'kp_ora_fine_task');
		$kp_ora_fine_task = html_entity_decode(strip_tags($kp_ora_fine_task), ENT_QUOTES,$default_charset);
		if($kp_ora_fine_task == null || $kp_ora_fine_task == ''){
			$kp_ora_fine_task = "00:00";
		}
		list($ora_fine,$minuti_fine) = explode(":",$kp_ora_fine_task);
		
		$projecttaskprogress = $adb->query_result($res_operazioni, $i, 'projecttaskprogress');
		$projecttaskprogress = html_entity_decode(strip_tags($projecttaskprogress), ENT_QUOTES,$default_charset);
		if($projecttaskprogress == '--none--' || $projecttaskprogress == '' || $projecttaskprogress == null){
			$projecttaskprogress = 0.0001;
		}
		else{
			$projecttaskprogress = str_replace("%", "", $projecttaskprogress);
			$projecttaskprogress = $projecttaskprogress / 100;
		}
		
		$startdate = $adb->query_result($res_operazioni, $i, 'startdate');
		$startdate = html_entity_decode(strip_tags($startdate), ENT_QUOTES,$default_charset);
		if($startdate != '' && $startdate != null && $startdate != '0000-00-00'){
			list($anno_start,$mese_start,$giorno_start) = explode("-",$startdate);
			$startdate_inv = date("d-m-Y",mktime(0,0,0,$mese_start,$giorno_start,$anno_start));

			$startdate_ora_inv = $startdate_inv." ".$kp_ora_inizio_task;
		}
		else{
			$anno_start = '';
			$mese_start = '';
			$giorno_start = '';
			$startdate = '';
			$startdate_inv = '';
			$startdate_ora_inv = '';
		}
		
		$enddate = $adb->query_result($res_operazioni, $i, 'enddate');
		$enddate = html_entity_decode(strip_tags($enddate), ENT_QUOTES,$default_charset);
		if($enddate != '' && $enddate != null && $enddate != '0000-00-00'){
			list($anno_end,$mese_end,$giorno_end) = explode("-",$enddate);
			$enddate_inv = date("d-m-Y",mktime(0,0,0,$mese_end,$giorno_end,$anno_end));

			$enddate_ora_inv = $enddate_inv." ".$kp_ora_fine_task;
		}
		else{
			$anno_end = '';
			$mese_end = '';
			$giorno_end = '';
			$enddate = '';
			$enddate_inv = '';
			$enddate_ora_inv = '';
		}
		
		$operazione_prec = $adb->query_result($res_operazioni, $i, 'operazione_prec');
		$operazione_prec = html_entity_decode(strip_tags($operazione_prec), ENT_QUOTES,$default_charset);
		/* kpro@tom070220171115 */
		if($operazione_prec == null || $operazione_prec == 0 || $operazione_prec == ""){
			$operazione_prec = 0;
		}
		else{
			$operazione_prec = verificaEsistenzaRecord($operazione_prec);	//Verifica che l'operazione padre non sia stata cancellata
		}
		/* kpro@tom070220171115 end */
		
		$lead_time = $adb->query_result($res_operazioni, $i, 'lead_time');
		$lead_time = html_entity_decode(strip_tags($lead_time), ENT_QUOTES,$default_charset);
		if($lead_time == null || $lead_time == ''){
			$lead_time = 1;
		}
		
		$risorsa_inc = $adb->query_result($res_operazioni, $i, 'risorsa_inc');
		$risorsa_inc = html_entity_decode(strip_tags($risorsa_inc), ENT_QUOTES,$default_charset);
		if($risorsa_inc == null){
			$risorsa_inc = '';
		}
		
		$assegnatario = $adb->query_result($res_operazioni, $i, 'assegnatario');
		$assegnatario = html_entity_decode(strip_tags($assegnatario), ENT_QUOTES,$default_charset);
		
		$giorni_scostamento = $adb->query_result($res_operazioni, $i, 'giorni_scostamento');
		$giorni_scostamento = html_entity_decode(strip_tags($giorni_scostamento), ENT_QUOTES,$default_charset);
		if($giorni_scostamento == '' || $giorni_scostamento == null){
			$giorni_scostamento = 0;
		}
		
		$tipo_scostamento_task = $adb->query_result($res_operazioni, $i, 'tipo_scostamento_task');
		$tipo_scostamento_task = html_entity_decode(strip_tags($tipo_scostamento_task), ENT_QUOTES,$default_charset);
		if($tipo_scostamento_task == '' || $tipo_scostamento_task == null){
			$tipo_scostamento_task = "--Nessuno--";
		}
		
		$data_fine_pianificata = $adb->query_result($res_operazioni, $i, 'data_fine_pianificata');
		$data_fine_pianificata = html_entity_decode(strip_tags($data_fine_pianificata), ENT_QUOTES,$default_charset);
		if($data_fine_pianificata != '' && $data_fine_pianificata != null && $data_fine_pianificata != '0000-00-00'){
			list($anno,$mese,$giorno) = explode("-",$data_fine_pianificata);
			$data_fine_pianificata_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
		}
		else{
			$data_fine_pianificata = $enddate;
			$data_fine_pianificata_inv = $enddate_inv;
		}
		
		$data_inizio_pian = $adb->query_result($res_operazioni, $i, 'data_inizio_pian');
		$data_inizio_pian = html_entity_decode(strip_tags($data_inizio_pian), ENT_QUOTES,$default_charset);
		if($data_inizio_pian != '' && $data_inizio_pian != null && $data_inizio_pian != '0000-00-00'){
			list($anno,$mese,$giorno) = explode("-",$data_inizio_pian);
			$data_inizio_pian_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
		}
		else{
			$data_inizio_pian = $startdate;
			$data_inizio_pian_inv = $startdate_inv;
		}
		
		$lead_time_pian = $adb->query_result($res_operazioni, $i, 'lead_time_pian');
		$lead_time_pian = html_entity_decode(strip_tags($lead_time_pian), ENT_QUOTES,$default_charset);
		if($lead_time_pian == null || $lead_time_pian == ''){
			$lead_time_pian = 1;
		}
		
		$descrizione = $adb->query_result($res_operazioni, $i, 'descrizione');
		$descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES,$default_charset);
		
		$projectstatus = $adb->query_result($res_operazioni, $i, 'projectstatus');
		$projectstatus = html_entity_decode(strip_tags($projectstatus), ENT_QUOTES,$default_charset);
		
		$servizio = $adb->query_result($res_operazioni, $i, 'kp_servizio');
		$servizio = html_entity_decode(strip_tags($servizio), ENT_QUOTES,$default_charset);
		if($servizio == null || $servizio == '' || $servizio == 0){
			$servizio = 0;
		}

		$commessa = $adb->query_result($res_operazioni, $i, 'commessa');
		$commessa = html_entity_decode(strip_tags($commessa), ENT_QUOTES,$default_charset);
		if($commessa == null || $commessa == '' || $commessa == 0){
			$commessa = 0;
		}

		/* kpro@tom070220171115 */
		if($operazione_prec == 0){
			$operazione_prec = "";
		}
		/* kpro@tom070220171115 */
		
		$rows[] = array('projecttaskid' => $projecttaskid,
						'projecttask_no' => $projecttask_no,
						'projecttaskname' => $projecttaskname,
						'projecttasktype' => $projecttasktype,
						'projecttaskpriority' => $projecttaskpriority,
						'projecttaskprogress' => $projecttaskprogress,
						'ore_lavorate' => $ore_lavorate,
						'ore_previste' => $ore_previste,
						'startdate' => $startdate,
						'anno_start' => (int)$anno_start,
						'mese_start' => (int)$mese_start,
						'giorno_start' => (int)$giorno_start,
						'ora_inizio' => (int)$ora_inizio,
						'minuti_inizio' => (int)$minuti_inizio,
						'startdate_inv' => $startdate_inv,
						'enddate' => $enddate,
						'anno_end' => (int)$anno_end,
						'mese_end' => (int)$mese_end,
						'giorno_end' => (int)$giorno_end,
						'ora_fine' => (int)$ora_fine,
						'minuti_fine' => (int)$minuti_fine,
						'enddate_inv' => $enddate_inv,
						'operazione_padre' => $operazione_prec,
						'lead_time' => (int)$lead_time,
						'nome_risorsa' => $risorsa_inc,
						'giorni_scostamento' => $giorni_scostamento,
						'tipo_scostamento_task' => $tipo_scostamento_task,
						'data_fine_pianificata' => $data_fine_pianificata,
						'data_fine_pianificata_inv' => $data_fine_pianificata_inv,
						'data_inizio_pian' => $data_inizio_pian,
						'data_inizio_pian_inv' => $data_inizio_pian_inv,
						'lead_time_pian' => (int)$lead_time_pian,
						'assegnatario' => $assegnatario,
						'descrizione' => $descrizione,
						'projectstatus' => $projectstatus,
						'tipo_task' => $tipo_attivita_task,
						'startdate_ora_inv' => $startdate_ora_inv,
						'enddate_ora_inv' => $enddate_ora_inv,
						'commessa' => $commessa,
						'servizio' => $servizio);
	}
	
}
							
$json = json_encode($rows);
print $json;
	
?>
