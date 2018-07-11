<?php 

/* kpro@20170801163335 */ 

/** 
 * @copyright (c) 2017, Kpro Consulting Srl 
 * 
 * Estensione classe KpNotificheRevProc 
 */ 

require_once('modules/KpNotificheRevProc/KpNotificheRevProc.php'); 

class KpNotificheRevProcKp extends KpNotificheRevProc { 

    var $list_fields = Array();

	var $list_fields_name = Array(
		'Soggetto'=>'kp_soggetto',
		'Revisione Procedura'=>'kp_rev_procedura',
		'Procedura'=>'kp_procedura',
		'Risorsa'=>'kp_risorsa',
        'Data Notifica'=>'kp_data_notifica',
        'Stato Notifica'=>'kp_stato_notifica_r',
        'Assigned To'=>'assigned_user_id'
	);

	function KpNotificheRevProcKp(){
		global $table_prefix;
		parent::__construct();
		$this->list_fields = Array(
			'Soggetto'=>Array($table_prefix.'_kpnotificherevproc'=>'kp_soggetto'),
			'Revisione Procedura'=>Array($table_prefix.'_kpnotificherevproc'=>'kp_rev_procedura'),
			'Procedura'=>Array($table_prefix.'_kpnotificherevproc'=>'kp_procedura'),
			'Risorsa'=>Array($table_prefix.'_kpnotificherevproc'=>'kp_risorsa'),
            'Data Notifica'=>Array($table_prefix.'_kpnotificherevproc'=>'kp_data_notifica'),
            'Stato Notifica'=>Array($table_prefix.'_kpnotificherevproc'=>'kp_stato_notifica_r'),
            'Assigned To'=>Array($table_prefix.'_crmentity'=>'smownerid')
		);

	}

    function save_module($module){

		global $table_prefix, $adb;

		parent::save_module($module);

        $this->setCampiAutocalcolati( $this->id );

        if( $this->column_fields['kp_stato_notifica_r'] == "Confermata visione" && ( $this->column_fields['kp_data_visione'] == "" || $this->column_fields['kp_data_visione'] == "0000-00-00" || $this->column_fields['kp_data_visione'] == null ) ){

            $this->setDataVisione( $this->id );

        }
		
	}

    function setDataVisione($id){
		global $adb, $table_prefix, $default_charset;

        $data_corrente = date("Y-m-d");

        $update = "UPDATE {$table_prefix}_kpnotificherevproc SET 
                    kp_data_visione = '".$data_corrente."'
                    WHERE kpnotificherevprocid = ".$id;
        $adb->query($update);

    }

    function setCampiAutocalcolati($id){
		global $adb, $table_prefix, $default_charset;

        if($this->column_fields['kp_rev_procedura'] != null && $this->column_fields['kp_rev_procedura'] != "" && $this->column_fields['kp_rev_procedura'] != 0){
            
            $focus_revisione = CRMEntity::getInstance('KpRevisioniProcedure');
            $focus_revisione->retrieve_entity_info($this->column_fields['kp_rev_procedura'], "KpRevisioniProcedure"); 

            $focus_procedura = CRMEntity::getInstance('KpProcedure');
            $focus_procedura->retrieve_entity_info($focus_revisione->column_fields["kp_procedura"], "KpProcedure"); 

            $numero_revisione = $focus_revisione->column_fields["kp_numero_revisione"];
		    $numero_revisione_str = str_pad($numero_revisione, 3, "0", STR_PAD_LEFT);

            $nome_notifica = "Rev. ".$numero_revisione_str." - Procedura: ".$focus_procedura->column_fields["kp_nome_procedura"];
 
            $update = "UPDATE {$table_prefix}_kpnotificherevproc SET 
                        kp_soggetto = '".$nome_notifica."',
                        kp_procedura = ".$focus_revisione->column_fields["kp_procedura"].",
                        kp_data_notifica = '".$focus_revisione->column_fields["kp_data_revisione"]."',
                        description = '".$focus_revisione->column_fields["description"]."'
                        WHERE kpnotificherevprocid = ".$id;
            $adb->query($update);

        }

    }

    function getExtraDetailBlock($selectionProcesses=false) {
        global $mod_strings, $app_strings;
        require_once('Smarty_setup.php');
        $smarty = new vtigerCRM_Smarty();
        $smarty->assign('MOD',$mod_strings);
        $smarty->assign('APP',$app_strings);
  
        $this->kpshowRilevazioneRischiTab = true;

        $PMUtils = ProcessMakerUtils::getInstance();
        return $smarty->fetch('SproCore/KpNotificheRevProc.tpl');
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

        if ($this->kpshowRilevazioneRischiTab) {
			$return[] = array('label'=>getTranslatedString('Notifiche Revisioni','KpNotificaRevisioneTab'),'href'=>'','onclick'=>"kpChangeDetailTab('{$moduleName}', '{$this->id}', 'KpNotificaRevisioneTab', this)");
		}

		return $return;
	}



} 

?>