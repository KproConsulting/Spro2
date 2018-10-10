<?php 

/* kpro@tom3012015 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2015, Kpro Consulting Srl
 * @package ganttPianificazioni
 * @version 1.0
 */

require_once('modules/ProjectPlan/ProjectPlan.php');

require_once('modules/SproCore/CustomViews/GanttProjectPlan/Utility.php');

class ProjectPlanKp extends ProjectPlan {

    var $list_fields = Array();

    var $list_fields_name = Array(
        'Project Name'=>'projectname',
        'Related to'=>'linktoaccountscontacts',
        'Start Date'=>'startdate',
        'Progress'=>'progress',
        'Tot. Ore a Budget'=>'tot_ore_budget',
        'Tot. Ore Eff.(Oggi)'=>'tot_ore_reali',
        'Status'=>'projectstatus'
    );

    function ProjectPlanKp(){
        global $table_prefix;
        parent::__construct();
        $this->list_fields = Array(
            'Project Name'=>Array($table_prefix.'_project'=>'projectname'),
            'Related to'=>Array($table_prefix.'_project'=>'linktoaccountscontacts'),
            'Start Date'=>Array($table_prefix.'_project'=>'startdate'),
            'Progress'=>Array($table_prefix.'_project'=>'progress'),
            'Tot. Ore a Budget'=>Array($table_prefix.'_project'=>'tot_ore_budget'),
            'Tot. Ore Eff.(Oggi)'=>Array($table_prefix.'_project'=>'tot_ore_reali'),
            'Status'=>Array($table_prefix.'_project'=>'projectstatus')
        );

    }

    function save_module($module){

        global $table_prefix, $adb;

        parent::save_module($module);  

        aggiornaRelazionatoAOperazioniProgetto($this->id);
    
        aggiornaStatoPianificazioneOperazioniProgetto($this->id);

    }

    /*function getExtraDetailTabs() {
		global $app_strings;
		
		$return = array();
		$others = CRMEntity::getExtraDetailTabs() ?: array();

		return array_merge($return, $others);
    }*/
    
    function getExtraDetailBlock($selectionProcesses=false) {
        global $mod_strings, $app_strings;
        require_once('Smarty_setup.php');
        $smarty = new vtigerCRM_Smarty();
        $smarty->assign('MOD',$mod_strings);
        $smarty->assign('APP',$app_strings);
        $smarty->assign('KpProjectPlanID',$this->id);
  
        $this->kpshowGanttPianificazioni = true;
        $this->kpshowCaricoPianificazioni = true;
        $this->kpshowSchedulerPianificazioni = true;

        $PMUtils = ProcessMakerUtils::getInstance();
        return $smarty->fetch('SproCore/KpGanttPianificazioni.tpl').$smarty->fetch('SproCore/KpCaricoPianificazioni.tpl').$smarty->fetch('SproCore/KpSchedulerPianificazioni.tpl');
    }

    function getExtraDetailTabs() {
		global $adb, $table_prefix, $app_strings, $currentModule;
		
		if ($this->modulename == 'Activity') {
			$moduleName = ($this->column_fields['activitytype'] == 'Task' ? 'Calendar' : 'Events');
		} else {
			$moduleName	= $this->modulename;
		}
		
		$return = array();
		if ($this->has_detail_charts && vtlib_isModuleActive('Charts')) {
			$return[] = array('label'=>getTranslatedString('Charts','Charts'),'href'=>'','onclick'=>"kpChangeDetailTab('{$moduleName}', '{$this->id}', 'detailCharts', this)");
		}
		if ($this->showProcessGraphTab) {
			$return[] = array('label'=>getTranslatedString('Process Graph','Processes'),'href'=>'','onclick'=>"kpChangeDetailTab('{$moduleName}', '{$this->id}', 'ProcessGraph', this)");
		}

		if (vtlib_isModuleActive('ChangeLog')) {
			// if module ChangeLog is active and linked to the current add tab History
			$relationManager = RelationManager::getInstance();
			$relation = $relationManager->getRelations('ChangeLog',ModuleRelation::$TYPE_NTO1,$currentModule);
			if (!empty($relation)) {
				$return[] = array('label'=>getTranslatedString('LBL_HISTORY'),'href'=>'','onclick'=>"kpChangeDetailTab('{$moduleName}', '{$this->id}', 'HistoryTab', this)");
			}
		}

        if ($this->kpshowGanttPianificazioni) {
			$return[] = array('label'=>getTranslatedString('Gantt','KpGanttPianificazioniTab'),'href'=>'','onclick'=>"kpChangeDetailTab('{$moduleName}', '{$this->id}', 'KpGanttPianificazioniTab', this)");
        }
        if ($this->kpshowCaricoPianificazioni) {
			$return[] = array('label'=>getTranslatedString('Carico','KpCaricoPianificazioniTab'),'href'=>'','onclick'=>"kpChangeDetailTab('{$moduleName}', '{$this->id}', 'KpCaricoPianificazioniTab', this)");
        }
        if ($this->kpshowSchedulerPianificazioni) {
			$return[] = array('label'=>getTranslatedString('Scheduler','KpSchedulerPianificazioniTab'),'href'=>'','onclick'=>"kpChangeDetailTab('{$moduleName}', '{$this->id}', 'KpSchedulerPianificazioniTab', this)");
		}

		return $return;
    }
    
    function getPianificazioniAzienda($azienda, $filtro) {
        global $adb, $table_prefix, $default_charset;

        $result = array();
        
        $query = "SELECT 
                    proj.projectid id,
                    proj.projectname nome,
                    proj.project_no numero,
                    proj.startdate data_inizio,
                    proj.projectstatus stato
                    FROM {$table_prefix}_project proj
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = proj.projectid
                    WHERE ent.deleted = 0 AND proj.linktoaccountscontacts = ".$azienda;

        if( array_key_exists('numero', $filtro) && $filtro["numero"] != ""){
            $query .= " AND proj.project_no LIKE '%".$filtro["numero"]."%'";
        }

        if( array_key_exists('nome', $filtro) && $filtro["nome"] != ""){
            $query .= " AND proj.projectname LIKE '%".$filtro["nome"]."%'";
        }

        if( array_key_exists('data_inizio', $filtro) && $filtro["data_inizio"] != ""){
            $query .= " AND proj.startdate = '".$filtro["data_inizio"]."'";
        }

        if( array_key_exists('stato', $filtro) && $filtro["stato"] != "" && $filtro["stato"] != "all"){
            $query .= " AND proj.projectstatus = '".$filtro["stato"]."'";
        }
        else{
            $query .= " AND proj.projectstatus IN ('Aperto', 'In corso', 'In approvazione')";
        }

        $query .= " ORDER BY proj.startdate ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i=0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $numero = $adb->query_result($result_query, $i, 'numero');
            $numero = html_entity_decode(strip_tags($numero), ENT_QUOTES, $default_charset);

            $stato = $adb->query_result($result_query, $i, 'stato');
            $stato = html_entity_decode(strip_tags($stato), ENT_QUOTES, $default_charset);
            
            $data_inizio = $adb->query_result($result_query, $i, 'data_inizio');
            $data_inizio = html_entity_decode(strip_tags($data_inizio), ENT_QUOTES, $default_charset);
            if($data_inizio != null && $data_inizio != "" && $data_inizio != "0000-00-00"){
                list($anno, $mese, $giorno) = explode("-", $data_inizio);
                $data_inizio = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
            }
            else{
                $data_inizio = '';
            }
                    
            $result[] = array('id' => $id,
                            'nome' => $nome,
                            'numero' => $numero,
                            'stato' => $stato,
                            'data_inizio' => $data_inizio); 
            
        }

        return $result;

    }

    function getOperazioniPianificazione($id, $azienda = null) {
        global $adb, $table_prefix, $default_charset;

        $result = array();

        $query = "SELECT 
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
                    WHERE ent.deleted = 0 AND pro.projectid = ".$id;

        if( $azienda != null ){

            $query .= " AND project.linktoaccountscontacts = ".$azienda;

        }

        $query .= " ORDER BY pro.startdate ASC";

        //print($query);die;
					
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i=0; $i<$num_result; $i++){
            $projecttaskid = $adb->query_result($result_query, $i, 'projecttaskid');
            $projecttaskid = html_entity_decode(strip_tags($projecttaskid), ENT_QUOTES,$default_charset);
            
            $projecttask_no = $adb->query_result($result_query, $i, 'projecttask_no');
            $projecttask_no = html_entity_decode(strip_tags($projecttask_no), ENT_QUOTES,$default_charset);
            
            $projecttaskname = $adb->query_result($result_query, $i, 'projecttaskname');
            $projecttaskname = html_entity_decode(strip_tags($projecttaskname), ENT_QUOTES,$default_charset);
            
            $projecttasktype = $adb->query_result($result_query, $i, 'projecttasktype');
            $projecttasktype = html_entity_decode(strip_tags($projecttasktype), ENT_QUOTES,$default_charset);
            
            $projecttaskpriority = $adb->query_result($result_query, $i, 'projecttaskpriority');
            $projecttaskpriority = html_entity_decode(strip_tags($projecttaskpriority), ENT_QUOTES,$default_charset);
            
            $tipo_attivita_task = $adb->query_result($result_query, $i, 'tipo_attivita_task');
            $tipo_attivita_task = html_entity_decode(strip_tags($tipo_attivita_task), ENT_QUOTES,$default_charset);
            
            $ore_lavorate = $adb->query_result($result_query, $i, 'ore_lavorate');
            $ore_lavorate = html_entity_decode(strip_tags($ore_lavorate), ENT_QUOTES,$default_charset);
            if($ore_lavorate == '' || $ore_lavorate == null){
                $ore_lavorate = 0;
            }
            
            $ore_previste = $adb->query_result($result_query, $i, 'ore_previste');
            $ore_previste = html_entity_decode(strip_tags($ore_previste), ENT_QUOTES,$default_charset);
            if($ore_previste == '' || $ore_previste == null){
                $ore_previste = 0;
            }
            
            $kp_ora_inizio_task = $adb->query_result($result_query, $i, 'kp_ora_inizio_task');
            $kp_ora_inizio_task = html_entity_decode(strip_tags($kp_ora_inizio_task), ENT_QUOTES,$default_charset);
            if($kp_ora_inizio_task == null || $kp_ora_inizio_task == ''){
                $kp_ora_inizio_task = "00:00";
            }
            list($ora_inizio,$minuti_inizio) = explode(":",$kp_ora_inizio_task);
                    
            $kp_ora_fine_task = $adb->query_result($result_query, $i, 'kp_ora_fine_task');
            $kp_ora_fine_task = html_entity_decode(strip_tags($kp_ora_fine_task), ENT_QUOTES,$default_charset);
            if($kp_ora_fine_task == null || $kp_ora_fine_task == ''){
                $kp_ora_fine_task = "00:00";
            }
            list($ora_fine,$minuti_fine) = explode(":",$kp_ora_fine_task);
            
            $projecttaskprogress = $adb->query_result($result_query, $i, 'projecttaskprogress');
            $projecttaskprogress = html_entity_decode(strip_tags($projecttaskprogress), ENT_QUOTES,$default_charset);
            if($projecttaskprogress == '--none--' || $projecttaskprogress == '' || $projecttaskprogress == null){
                $projecttaskprogress = 0.0001;
            }
            else{
                $projecttaskprogress = str_replace("%", "", $projecttaskprogress);
                $projecttaskprogress = $projecttaskprogress / 100;
            }
            
            $startdate = $adb->query_result($result_query, $i, 'startdate');
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
            
            $enddate = $adb->query_result($result_query, $i, 'enddate');
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
            
            $operazione_prec = $adb->query_result($result_query, $i, 'operazione_prec');
            $operazione_prec = html_entity_decode(strip_tags($operazione_prec), ENT_QUOTES,$default_charset);
            
            if($operazione_prec == null || $operazione_prec == 0 || $operazione_prec == ""){
                $operazione_prec = 0;
            }
            else{
                $operazione_prec = $this->verificaEsistenzaRecord($operazione_prec);	//Verifica che l'operazione padre non sia stata cancellata
            }
            
            $lead_time = $adb->query_result($result_query, $i, 'lead_time');
            $lead_time = html_entity_decode(strip_tags($lead_time), ENT_QUOTES,$default_charset);
            if($lead_time == null || $lead_time == ''){
                $lead_time = 1;
            }
            
            $risorsa_inc = $adb->query_result($result_query, $i, 'risorsa_inc');
            $risorsa_inc = html_entity_decode(strip_tags($risorsa_inc), ENT_QUOTES,$default_charset);
            if($risorsa_inc == null){
                $risorsa_inc = '';
            }
            
            $assegnatario = $adb->query_result($result_query, $i, 'assegnatario');
            $assegnatario = html_entity_decode(strip_tags($assegnatario), ENT_QUOTES,$default_charset);
            
            $giorni_scostamento = $adb->query_result($result_query, $i, 'giorni_scostamento');
            $giorni_scostamento = html_entity_decode(strip_tags($giorni_scostamento), ENT_QUOTES,$default_charset);
            if($giorni_scostamento == '' || $giorni_scostamento == null){
                $giorni_scostamento = 0;
            }
            
            $tipo_scostamento_task = $adb->query_result($result_query, $i, 'tipo_scostamento_task');
            $tipo_scostamento_task = html_entity_decode(strip_tags($tipo_scostamento_task), ENT_QUOTES,$default_charset);
            if($tipo_scostamento_task == '' || $tipo_scostamento_task == null){
                $tipo_scostamento_task = "--Nessuno--";
            }
            
            $data_fine_pianificata = $adb->query_result($result_query, $i, 'data_fine_pianificata');
            $data_fine_pianificata = html_entity_decode(strip_tags($data_fine_pianificata), ENT_QUOTES,$default_charset);
            if($data_fine_pianificata != '' && $data_fine_pianificata != null && $data_fine_pianificata != '0000-00-00'){
                list($anno,$mese,$giorno) = explode("-",$data_fine_pianificata);
                $data_fine_pianificata_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
            }
            else{
                $data_fine_pianificata = $enddate;
                $data_fine_pianificata_inv = $enddate_inv;
            }
            
            $data_inizio_pian = $adb->query_result($result_query, $i, 'data_inizio_pian');
            $data_inizio_pian = html_entity_decode(strip_tags($data_inizio_pian), ENT_QUOTES,$default_charset);
            if($data_inizio_pian != '' && $data_inizio_pian != null && $data_inizio_pian != '0000-00-00'){
                list($anno,$mese,$giorno) = explode("-",$data_inizio_pian);
                $data_inizio_pian_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));
            }
            else{
                $data_inizio_pian = $startdate;
                $data_inizio_pian_inv = $startdate_inv;
            }
            
            $lead_time_pian = $adb->query_result($result_query, $i, 'lead_time_pian');
            $lead_time_pian = html_entity_decode(strip_tags($lead_time_pian), ENT_QUOTES,$default_charset);
            if($lead_time_pian == null || $lead_time_pian == ''){
                $lead_time_pian = 1;
            }
            
            $descrizione = $adb->query_result($result_query, $i, 'descrizione');
            $descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES,$default_charset);
            
            $projectstatus = $adb->query_result($result_query, $i, 'projectstatus');
            $projectstatus = html_entity_decode(strip_tags($projectstatus), ENT_QUOTES,$default_charset);
            
            $servizio = $adb->query_result($result_query, $i, 'kp_servizio');
            $servizio = html_entity_decode(strip_tags($servizio), ENT_QUOTES,$default_charset);
            if($servizio == null || $servizio == '' || $servizio == 0){
                $servizio = 0;
            }

            $commessa = $adb->query_result($result_query, $i, 'commessa');
            $commessa = html_entity_decode(strip_tags($commessa), ENT_QUOTES,$default_charset);
            if($commessa == null || $commessa == '' || $commessa == 0){
                $commessa = 0;
            }

            if($operazione_prec == 0){
                $operazione_prec = "";
            }
            
            $result[] = array('projecttaskid' => $projecttaskid,
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

        return $result;

    }

    function verificaEsistenzaRecord($crmid){
        global $adb, $table_prefix, $current_user;
    
        $query = "SELECT 
                    *
                    FROM {$table_prefix}_crmentity 
                    WHERE deleted = 0 AND crmid = ".$crmid;
        $res_query = $adb->query($query);
        $num_result = $adb->num_rows($res_query);
    
        if($num_result > 0){
            return $crmid;
        }
        else{
            return 0;
        }
    
    }

    function getScadenzePianificazione($id, $azienda = null) {
        global $adb, $table_prefix, $default_charset;

        $result = array();

        $query = "SELECT 
                    mil.projectmilestoneid projectmilestoneid,
                    mil.projectmilestonename projectmilestonename,
                    mil.projectmilestone_no projectmilestone_no,
                    mil.projectmilestonedate projectmilestonedate,
                    mil.projectmilestype projectmilestype,
                    mil.operazione_prec operazione_padre,
                    mil.description description
                    FROM {$table_prefix}_projectmilestone mil
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = mil.projectmilestoneid
                    INNER JOIN {$table_prefix}_project project ON project.projectid = mil.projectid
                    WHERE ent.deleted = 0 AND mil.projectid = ".$id;

        if( $azienda != null ){

            $query .= " AND project.linktoaccountscontacts = ".$azienda;

        }

        $query .= " ORDER BY mil.projectmilestonedate ASC";

        //print($query);die;
						
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i=0; $i<$num_result; $i++){
            $projectmilestoneid = $adb->query_result($result_query, $i, 'projectmilestoneid');
            $projectmilestoneid = html_entity_decode(strip_tags($projectmilestoneid), ENT_QUOTES,$default_charset);
            
            $projectmilestonename = $adb->query_result($result_query, $i, 'projectmilestonename');
            $projectmilestonename = html_entity_decode(strip_tags($projectmilestonename), ENT_QUOTES,$default_charset);
            
            $projectmilestone_no = $adb->query_result($result_query, $i, 'projectmilestone_no');
            $projectmilestone_no = html_entity_decode(strip_tags($projectmilestone_no), ENT_QUOTES,$default_charset);
            
            $description = $adb->query_result($result_query, $i, 'description');
            $description = html_entity_decode(strip_tags($description), ENT_QUOTES,$default_charset);
            
            $operazione_padre = $adb->query_result($result_query, $i, 'operazione_padre');
            $operazione_padre = html_entity_decode(strip_tags($operazione_padre), ENT_QUOTES,$default_charset);

            if($operazione_padre == null || $operazione_padre == 0 || $operazione_padre == ""){
                $operazione_padre = 0;
            }
            else{
                $operazione_padre = $this->verificaEsistenzaRecord($operazione_padre);	//Verifica che l'operazione padre non sia stata cancellata
            }
            
            $risorsa = "";
            $nome_risorsa = "";
            
            $projectmilestonedate = $adb->query_result($result_query, $i, 'projectmilestonedate');
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

            if($operazione_padre == 0){
                $operazione_padre = "";
            }
            
            $result[] = array('projectmilestoneid' => $projectmilestoneid,
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

        return $result;

    }

    function getRelazioniPianificazione($id, $azienda = null) {
        global $adb, $table_prefix, $default_charset;

        $result = array();

        $query = "SELECT 
                    rel.relazioniopid relazioniopid,
                    rel.operazione operazione,
                    rel.operazione_target operazione_target,
                    rel.tipo_relazione_task tipo_relazione_task
                    FROM {$table_prefix}_relazioniop rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relazioniopid
                    INNER JOIN {$table_prefix}_project project ON project.projectid = rel.pianificazione
                    WHERE ent.deleted = 0 AND rel.pianificazione = ".$id;

        if( $azienda != null ){

            $query .= " AND project.linktoaccountscontacts = ".$azienda;

        }

        $query .= " ORDER BY rel.relazioniopid ASC";

        //print($query);die;
                
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i=0; $i<$num_result; $i++){
            $relazioniopid = $adb->query_result($result_query, $i, 'relazioniopid');
            $relazioniopid = html_entity_decode(strip_tags($relazioniopid), ENT_QUOTES,$default_charset);
            
            $operazione = $adb->query_result($result_query, $i, 'operazione');
            $operazione = html_entity_decode(strip_tags($operazione), ENT_QUOTES,$default_charset);

            if($operazione == 0 && $operazione == '' && $operazione == null){
                $operazione = 0;
            }
            else{
                $operazione = $this->verificaEsistenzaRecord($operazione);	//Verifica che l'operazione non sia stata cancellata
            }
            
            $operazione_target = $adb->query_result($result_query, $i, 'operazione_target');
            $operazione_target = html_entity_decode(strip_tags($operazione_target), ENT_QUOTES,$default_charset);

            if($operazione_target == 0 && $operazione_target == '' && $operazione_target == null){
                $operazione_target = 0;
            }
            else{
                $operazione_target = verificaEsistenzaRecord($operazione_target);	//Verifica che l'operazione non sia stata cancellata
            }
            
            $tipo_relazione_task = $adb->query_result($result_query, $i, 'tipo_relazione_task');
            $tipo_relazione_task = html_entity_decode(strip_tags($tipo_relazione_task), ENT_QUOTES,$default_charset);
        
            if($operazione != 0 && $operazione_target != 0){
        
                $result[] = array('relazioniopid' => $relazioniopid,
                                'operazione' => $operazione,
                                'operazione_target' => $operazione_target,
                                'tipo_relazione_task' => $tipo_relazione_task);

            }

        }

        return $result;

    }

    function getDocumentiPianificazione($id, $filtro = array(), $azienda = null, $portale = false) {
        global $adb, $table_prefix, $default_charset;

        $result = array();

        $query = "SELECT attac.attachmentsid attachmentsid, 
                    attac.name name, 
                    attac.path path, 
                    notes.title title, 
                    notes.notesid notesid, 
                    notes.filelocationtype filelocationtype,
                    notes.folderid cartella_id,
                    notes.data_scadenza data_scadenza,
                    notes.stato_documento stato_documento,
                    notes.kp_data_documento data_documento,
                    date(ent.createdtime) data_creazione,
                    ent.createdtime createdtime, 
                    ent.modifiedtime modifiedtime 
                    FROM {$table_prefix}_notes notes 
                    INNER JOIN {$table_prefix}_notescf notescf ON notescf.notesid = notes.notesid 
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = notes.notesid 
                    LEFT JOIN {$table_prefix}_senotesrel senote ON senote.notesid = notes.notesid
                    LEFT JOIN {$table_prefix}_seattachmentsrel seattac ON seattac.crmid = notes.notesid 
                    LEFT JOIN {$table_prefix}_attachments attac ON attac.attachmentsid = seattac.attachmentsid 
                    LEFT JOIN {$table_prefix}_project project ON project.projectid = senote.crmid
                    WHERE ent.deleted = 0 AND senote.crmid = ".$id;

        if( $azienda != null ){
            $query .= " AND project.linktoaccountscontacts = ".$azienda;
        }

        if( $portale ){
            $query .= " AND notes.active_portal = 1";
        }

        if( array_key_exists('nome_documento', $filtro) && $filtro["nome_documento"] != ""){
            $query .= " AND notes.title LIKE '%".$filtro["nome_documento"]."%'";
        }

        if( array_key_exists('data_documento', $filtro) && $filtro["data_documento"] != ""){
            $query .= " AND notes.kp_data_documento = '".$filtro["data_documento"]."'";
        }

        if( array_key_exists('data_scadenza', $filtro) && $filtro["data_scadenza"] != ""){
            $query .= " AND notes.data_scadenza = '".$filtro["data_scadenza"]."'";
        }

        if( array_key_exists('stato_documento', $filtro) && $filtro["stato_documento"] != "" && $filtro["stato_documento"] != "all"){
            
            $stato_documento = explode(',', $filtro["stato_documento"]);
                
            $lista_stati = "";
                
            foreach($stato_documento as $stato){
                if($lista_stati == ""){
                    $lista_stati = "'".$stato."'";
                }
                else{
                    $lista_stati .= ",'".$stato."'";
                }
            }

            $query .= " AND notes.stato_documento IN (".$lista_stati.")";
        }

        $query .= " ORDER BY ent.createdtime DESC";

        //print_r($query);die;
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i=0; $i<$num_result; $i++){
            
            $title = $adb->query_result($result_query, $i, 'title');
            $title = html_entity_decode(strip_tags($title), ENT_QUOTES, $default_charset);
            
            $notesid = $adb->query_result($result_query, $i, 'notesid');
            $notesid = html_entity_decode(strip_tags($notesid), ENT_QUOTES, $default_charset);

            $data_documento = $adb->query_result($result_query, $i, 'data_documento');
            $data_documento = html_entity_decode(strip_tags($data_documento), ENT_QUOTES, $default_charset);
            if($data_documento != null && $data_documento != "" && $data_documento != "0000-00-00"){
                list($anno, $mese, $giorno) = explode("-", $data_documento);
                $data_documento_inv = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
            }
            else{
                $data_documento = $adb->query_result($result_query, $i, 'data_creazione');
                $data_documento = html_entity_decode(strip_tags($data_documento), ENT_QUOTES, $default_charset);
                if($data_documento != null && $data_documento != "" && $data_documento != "0000-00-00"){
                    list($anno, $mese, $giorno) = explode("-", $data_documento);
                    $data_documento_inv = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
                }
            }

            if($data_documento_filtro == "" || ($data_documento_filtro != "" && $data_documento == $data_documento_filtro)){
            
                $filelocationtype = $adb->query_result($result_query, $i, 'filelocationtype');
                $filelocationtype = html_entity_decode(strip_tags($filelocationtype), ENT_QUOTES, $default_charset);
                if($filelocationtype == "E"){
                    $attachmentsid = 0;

                    $tipo_download = "Esterno";
                }
                else{
                    $attachmentsid = $adb->query_result($result_query, $i, 'attachmentsid');
                    $attachmentsid = html_entity_decode(strip_tags($attachmentsid), ENT_QUOTES, $default_charset);
                    
                    $tipo_download = "Interno";
                }
            
                $createdtime = $adb->query_result($result_query, $i, 'createdtime');
                $createdtime = html_entity_decode(strip_tags($createdtime), ENT_QUOTES, $default_charset);
                
                $modifiedtime = $adb->query_result($result_query, $i, 'modifiedtime');
                $modifiedtime = html_entity_decode(strip_tags($modifiedtime), ENT_QUOTES, $default_charset);
                
                $data_scadenza = $adb->query_result($result_query, $i, 'data_scadenza');
                $data_scadenza = html_entity_decode(strip_tags($data_scadenza), ENT_QUOTES, $default_charset);
                if($data_scadenza != null && $data_scadenza != ""){
                    list($anno, $mese, $giorno) = explode("-", $data_scadenza);
                    $data_scadenza = date("d/m/Y", mktime(0, 0, 0, $mese, $giorno, $anno));
                }
                
                $stato_documento = $adb->query_result($result_query, $i, 'stato_documento');
                $stato_documento = html_entity_decode(strip_tags($stato_documento), ENT_QUOTES, $default_charset);
                
                $result[] = array('notesid' => $notesid,
                                'attachmentsid' => $attachmentsid,
                                'title' => $title,
                                'createdtime' => $createdtime,
                                'modifiedtime' => $modifiedtime,
                                'data_documento' => $data_documento_inv,
                                'data_scadenza' => $data_scadenza,
                                'stato_documento' => $stato_documento,
                                'tipo' => $tipo_download);

            }

        }

        return $result;

    }


}
?>