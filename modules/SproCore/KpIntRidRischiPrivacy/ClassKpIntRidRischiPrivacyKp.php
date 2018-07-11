<?php 

/* kpro@20170918102153 */ 

/** 
 * @copyright (c) 2017, Kpro Consulting Srl 
 * 
 * Estensione classe KpIntRidRischiPrivacy 
 */ 

require_once('modules/KpIntRidRischiPrivacy/KpIntRidRischiPrivacy.php'); 

require_once('modules/SproCore/KpRilRischiPrivacy/ClassKpRilRischiPrivacyKp.php'); 

class KpIntRidRischiPrivacyKp extends KpIntRidRischiPrivacy { 

    public $kpshowInterventoRiduzioneRischiTab;

    var $list_fields = Array();
    
    var $list_fields_name = Array(
        'Soggetto' => 'kp_soggetto',
        'Data Intervento' => 'kp_data_intervento',
        'Azienda' => 'kp_azienda',
        'Stabilimento' => 'kp_stabilimento'
    );

    function KpIntRidRischiPrivacyKp(){
        global $table_prefix;
        parent::__construct();
        $this->list_fields = Array(
            'Soggetto'=>Array($table_prefix.'_kpintridrischiprivacy'=>'kp_soggetto'),
            'Data Intervento'=>Array($table_prefix.'_kpintridrischiprivacy'=>'kp_data_intervento'),
            'Azienda'=>Array($table_prefix.'_kpintridrischiprivacy'=>'kp_azienda'),
            'Stabilimento'=>Array($table_prefix.'_kpintridrischiprivacy'=>'kp_stabilimento')
        );
    }

    //Script modifica Funtion Save
    /*function save_module($module){

        global $table_prefix, $adb;

        parent::save_module($module);
        
    }*/
    
    function getExtraDetailBlock($selectionProcesses=false) {
        global $mod_strings, $app_strings, $adb;
        require_once('Smarty_setup.php');
        $smarty = new vtigerCRM_Smarty();
        $smarty->assign('MOD',$mod_strings);
        $smarty->assign('APP',$app_strings);
    
        $this->kpshowInterventoRiduzioneRischiTab = true;	/* kpro@150920170948 */ 

        $query = "SELECT 
                    probabilita_1,
                    probabilita_2,
                    probabilita_3,
                    probabilita_4,
                    probabilita_5,
                    magnitudo_1,
                    magnitudo_2,
                    magnitudo_3,
                    magnitudo_4,
                    magnitudo_5
                    FROM kp_settings_privacy";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $probabilita_1 = $adb->query_result($result_query, 0, 'probabilita_1');
            $probabilita_1 = html_entity_decode(strip_tags($probabilita_1), ENT_QUOTES, $default_charset);
            if($probabilita_1 == null){
                $probabilita_1 = '';
            }

            $probabilita_2 = $adb->query_result($result_query, 0, 'probabilita_2');
            $probabilita_2 = html_entity_decode(strip_tags($probabilita_2), ENT_QUOTES, $default_charset);
            if($probabilita_2 == null){
                $probabilita_2 = '';
            }

            $probabilita_3 = $adb->query_result($result_query, 0, 'probabilita_3');
            $probabilita_3 = html_entity_decode(strip_tags($probabilita_3), ENT_QUOTES, $default_charset);
            if($probabilita_3 == null){
                $probabilita_3 = '';
            }

            $probabilita_4 = $adb->query_result($result_query, 0, 'probabilita_4');
            $probabilita_4 = html_entity_decode(strip_tags($probabilita_4), ENT_QUOTES, $default_charset);
            if($probabilita_4 == null){
                $probabilita_4 = '';
            }

            $probabilita_5 = $adb->query_result($result_query, 0, 'probabilita_5');
            $probabilita_5 = html_entity_decode(strip_tags($probabilita_5), ENT_QUOTES, $default_charset);
            if($probabilita_5 == null){
                $probabilita_5 = '';
            }

            $magnitudo_1 = $adb->query_result($result_query, 0, 'magnitudo_1');
            $magnitudo_1 = html_entity_decode(strip_tags($magnitudo_1), ENT_QUOTES, $default_charset);
            if($magnitudo_1 == null){
                $magnitudo_1 = '';
            }

            $magnitudo_2 = $adb->query_result($result_query, 0, 'magnitudo_2');
            $magnitudo_2 = html_entity_decode(strip_tags($magnitudo_2), ENT_QUOTES, $default_charset);
            if($magnitudo_2 == null){
                $magnitudo_2 = '';
            }

            $magnitudo_3 = $adb->query_result($result_query, 0, 'magnitudo_3');
            $magnitudo_3 = html_entity_decode(strip_tags($magnitudo_3), ENT_QUOTES, $default_charset);
            if($magnitudo_3 == null){
                $magnitudo_3 = '';
            }

            $magnitudo_4 = $adb->query_result($result_query, 0, 'magnitudo_4');
            $magnitudo_4 = html_entity_decode(strip_tags($magnitudo_4), ENT_QUOTES, $default_charset);
            if($magnitudo_4 == null){
                $magnitudo_4 = '';
            }

            $magnitudo_5 = $adb->query_result($result_query, 0, 'magnitudo_5');
            $magnitudo_5 = html_entity_decode(strip_tags($magnitudo_5), ENT_QUOTES, $default_charset);
            if($magnitudo_5 == null){
                $magnitudo_5 = '';
            }

        }

        $smarty->assign("form_probabilita_1", $probabilita_1);
        $smarty->assign("form_probabilita_2", $probabilita_2);
        $smarty->assign("form_probabilita_3", $probabilita_3);
        $smarty->assign("form_probabilita_4", $probabilita_4);
        $smarty->assign("form_probabilita_5", $probabilita_5);

        $smarty->assign("form_magnitudo_1", $magnitudo_1);
        $smarty->assign("form_magnitudo_2", $magnitudo_2);
        $smarty->assign("form_magnitudo_3", $magnitudo_3);
        $smarty->assign("form_magnitudo_4", $magnitudo_4);
        $smarty->assign("form_magnitudo_5", $magnitudo_5);

        $PMUtils = ProcessMakerUtils::getInstance();
        return $smarty->fetch('SproCore/KpInterventoRiduzioneRischiPrivacy.tpl');   /* kpro@150920170948 */ 
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

        /* kpro@150920170948 */ 
        if ($this->kpshowInterventoRiduzioneRischiTab) {
            $return[] = array('label'=>getTranslatedString('Intervento Riduzione Rischio','KpInterventiRiduzioneRischiTab'),'href'=>'','onclick'=>"kpChangeDetailTab('{$moduleName}', '{$this->id}', 'KpInterventiRiduzioneRischiTab', this)");
        }
        /* kpro@150920170948 end */ 

        return $return;
    }

    static function getInterventoRiduzioneRischioPrivacy($id){
        global $adb, $table_prefix, $default_charset, $current_user;
        
        $result = array();

        $focus_intervento = CRMEntity::getInstance('KpIntRidRischiPrivacy');
        $focus_intervento->retrieve_entity_info($id, "KpIntRidRischiPrivacy", $dieOnError=false); 
        
        $data_intervento = $focus_intervento->column_fields["kp_data_intervento"];
        $azienda = $focus_intervento->column_fields["kp_azienda"];
        $stabilimento = $focus_intervento->column_fields["kp_stabilimento"];

        $lista_impianti = KpRilRischiPrivacyKp::getImpiantiConMinaccePrivacy($azienda, $stabilimento, $data_intervento);
        
        foreach($lista_impianti as $impianto){

            $lista_minacce = self::getMinacceImpianto( $impianto["id"], $id );
                
            $result[] = array("impianto" => $impianto["id"],
                                "nome_impianto" => $impianto["nome"],
                                "lista_minacce" => $lista_minacce,
                                "numero_minacce" => count($lista_minacce));


        }

        return $result;
        
    }

    static function getMinacceImpianto($impianto, $intervento = 0){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT t.* FROM
                    ((SELECT 
                    imp.impiantiid impianto,
                    imp.impianto_name nome_impianto,
                    imp.azienda azienda,
                    imp.stabilimento stabilimento,
                    imp.stato_impianto stato_impianto,
                    rel.relcrmid minaccia,
                    minp.kp_nome_minaccia nome_minaccia
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_impianti imp ON imp.impiantiid = rel.crmid
                    INNER JOIN {$table_prefix}_crmentity entimp ON entimp.crmid = rel.crmid
                    INNER JOIN {$table_prefix}_crmentity entmin ON entmin.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kpminacceprivacy minp ON minp.kpminacceprivacyid = rel.relcrmid
                    WHERE entimp.deleted = 0 AND entmin.deleted = 0 AND rel.module = 'Impianti' AND rel.relmodule = 'KpMinaccePrivacy')
                    UNION
                    (SELECT 
                    imp.impiantiid impianto,
                    imp.impianto_name nome_impianto,
                    imp.azienda azienda,
                    imp.stabilimento stabilimento,
                    imp.stato_impianto stato_impianto,
                    rel.crmid minaccia,
                    minp.kp_nome_minaccia nome_minaccia
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_impianti imp ON imp.impiantiid = rel.relcrmid
                    INNER JOIN {$table_prefix}_crmentity entimp ON entimp.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_crmentity entmin ON entmin.crmid = rel.crmid
                    INNER JOIN {$table_prefix}_kpminacceprivacy minp ON minp.kpminacceprivacyid = rel.crmid
                    WHERE entimp.deleted = 0 AND entmin.deleted = 0 AND rel.module = 'KpMinaccePrivacy' AND rel.relmodule = 'Impianti')) AS t
                    WHERE t.stato_impianto = 'Attivo' AND t.impianto = ".$impianto."
                    GROUP BY t.minaccia
                    ORDER BY t.nome_minaccia ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'minaccia');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome_minaccia');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            if( $intervento != 0 ){

                $focus_intervento = CRMEntity::getInstance('KpIntRidRischiPrivacy');
                $focus_intervento->retrieve_entity_info($intervento, "KpIntRidRischiPrivacy", $dieOnError=false); 
                
                $data_intervento = $focus_intervento->column_fields["kp_data_intervento"];

                $dati_rilevazione = self::getDatiRilevazioneRischiPrivacy( $impianto, $id, $data_intervento );

                $dati_precedenti = self::getDatiInterventoRiduzioneRischiPrivacyPrecedente( $impianto, $id, $dati_rilevazione["data_rilevazione"], $data_intervento);

                if( $dati_precedenti["esiste"] ){

                    $probabilita_pre = $dati_precedenti["probabilita"];
                    $magnitudo_pre = $dati_precedenti["magnitudo"];
                    $rischio_pre = $dati_precedenti["rischio"];

                }
                else{

                    $probabilita_pre = $dati_rilevazione["probabilita"];
                    $magnitudo_pre = $dati_rilevazione["magnitudo"];
                    $rischio_pre = $dati_rilevazione["rischio"];

                }

                $dati_intervento = self::getDatiInterventoRiduzioneRischiPrivacy( $intervento, $impianto, $id );

            }

            $lista_misure = self::getMisureRiduzioneRischioPrivacy( $impianto, $id, $intervento );
            
            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "tempi_ripristino_pre" => $dati_rilevazione["tempi_ripristino"],
                                "probabilita_pre" => $probabilita_pre,
                                "magnitudo_pre" => $magnitudo_pre,
                                "rischio_pre" => $rischio_pre,
                                "probabilita_post" => $dati_intervento["probabilita"],
                                "magnitudo_post" => $dati_intervento["magnitudo"],
                                "rischio_post" => $dati_intervento["rischio"],
                                "descrizione" => $dati_intervento["descrizione"],
                                "lista_misure" => $lista_misure);

        }

        return $result;

    }

    static function getDatiInterventoRiduzioneRischiPrivacyPrecedente( $impianto, $minaccia, $da_data, $a_data ){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    riga.kp_probabilita_p probabilita,
                    riga.kp_magnitudo_p magnitudo,
                    riga.kp_rischio_p rischio,
                    riga.description descrizione
                    FROM {$table_prefix}_kprigintridriscprivac riga
                    INNER JOIN {$table_prefix}_crmentity entriga ON entriga.crmid = riga.kprigintridriscprivacid
                    INNER JOIN {$table_prefix}_kpintridrischiprivacy inter ON inter.kpintridrischiprivacyid = riga.kp_intervento
                    INNER JOIN {$table_prefix}_crmentity entinter ON entinter.crmid = inter.kpintridrischiprivacyid
                    WHERE entriga.deleted = 0 AND entinter.deleted = 0 AND riga.kp_impianto = ".$impianto." AND riga.kp_minaccia = ".$minaccia;

        $query .= " AND inter.kp_data_intervento >= '".$da_data."' && inter.kp_data_intervento < '".$a_data."'";

        $query .= " ORDER BY inter.kp_data_intervento DESC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $esiste = true;

            $probabilita = $adb->query_result($result_query, 0, 'probabilita');
            $probabilita = html_entity_decode(strip_tags($probabilita), ENT_QUOTES, $default_charset);

            $magnitudo = $adb->query_result($result_query, 0, 'magnitudo');
            $magnitudo = html_entity_decode(strip_tags($magnitudo), ENT_QUOTES, $default_charset);

            $rischio = $adb->query_result($result_query, 0, 'rischio');
            $rischio = html_entity_decode(strip_tags($rischio), ENT_QUOTES, $default_charset);

            $descrizione = $adb->query_result($result_query, 0, 'descrizione');
            $descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES, $default_charset);

        }
        else{

            $esiste = false;
            $probabilita = "";
            $magnitudo = "";
            $rischio = "";
            $descrizione = "";

        }

        $result = array("esiste" => $esiste,
                        "probabilita" => $probabilita,
                        "magnitudo" => $magnitudo,
                        "rischio" => $rischio,
                        "descrizione" => $descrizione);

        return $result;

    }

    static function getDatiInterventoRiduzioneRischiPrivacy( $intervento, $impianto, $minaccia ){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    riga.kp_probabilita_p probabilita,
                    riga.kp_magnitudo_p magnitudo,
                    riga.kp_rischio_p rischio,
                    riga.description descrizione
                    FROM {$table_prefix}_kprigintridriscprivac riga
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = riga.kprigintridriscprivacid
                    WHERE ent.deleted = 0 AND riga.kp_intervento = ".$intervento." AND riga.kp_impianto = ".$impianto." AND riga.kp_minaccia = ".$minaccia;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $probabilita = $adb->query_result($result_query, 0, 'probabilita');
            $probabilita = html_entity_decode(strip_tags($probabilita), ENT_QUOTES, $default_charset);

            $magnitudo = $adb->query_result($result_query, 0, 'magnitudo');
            $magnitudo = html_entity_decode(strip_tags($magnitudo), ENT_QUOTES, $default_charset);

            $rischio = $adb->query_result($result_query, 0, 'rischio');
            $rischio = html_entity_decode(strip_tags($rischio), ENT_QUOTES, $default_charset);

            $descrizione = $adb->query_result($result_query, 0, 'descrizione');
            $descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES, $default_charset);

        }
        else{

            $probabilita = "";
            $magnitudo = "";
            $rischio = "";
            $descrizione = "";

        }

        $result = array("probabilita" => $probabilita,
                        "magnitudo" => $magnitudo,
                        "rischio" => $rischio,
                        "descrizione" => $descrizione);

        return $result;

    }

    static function getMisureRiduzioneRischioPrivacy( $impianto, $minaccia, $intervento = 0 ){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT t.* FROM
                    ((SELECT 
                    mis.kpmisureprivacyid kpmisureprivacyid,
                    mis.kp_nome_misura kp_nome_misura,
                    mis.kp_perc_riduzione_r kp_perc_riduzione_r
                    FROM {$table_prefix}_crmentityrel entrel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = entrel.relcrmid
                    INNER JOIN {$table_prefix}_kpmisureprivacy mis ON mis.kpmisureprivacyid = entrel.relcrmid
                    WHERE ent.deleted = 0 AND entrel.module = 'KpMinaccePrivacy' AND entrel.relmodule = 'KpMisurePrivacy' AND entrel.crmid = ".$minaccia.")
                    UNION
                    (SELECT 
                    mis.kpmisureprivacyid kpmisureprivacyid,
                    mis.kp_nome_misura kp_nome_misura,
                    mis.kp_perc_riduzione_r kp_perc_riduzione_r
                    FROM {$table_prefix}_crmentityrel entrel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = entrel.crmid
                    INNER JOIN {$table_prefix}_kpmisureprivacy mis ON mis.kpmisureprivacyid = entrel.crmid
                    WHERE ent.deleted = 0 AND entrel.relmodule = 'KpMinaccePrivacy' AND entrel.module = 'KpMisurePrivacy' AND entrel.relcrmid = ".$minaccia.")) AS t
                    GROUP BY t.kpmisureprivacyid
                    ORDER BY t.kp_nome_misura ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'kpmisureprivacyid');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'kp_nome_misura');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $percentuale_riduzione = $adb->query_result($result_query, $i, 'kp_perc_riduzione_r');
            $percentuale_riduzione = html_entity_decode(strip_tags($percentuale_riduzione), ENT_QUOTES, $default_charset);

            $check_misura_attiva = self::checkIfMisuraAttiva($impianto, $minaccia, $id, $intervento);

            $dati_attuazione_misura = self::checkStatoMisuraIntervento($impianto, $minaccia, $id, $intervento);
            
            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "attiva" => $check_misura_attiva,
                                "attuata" => $dati_attuazione_misura["adozione_misura"],
                                "descrizione" => $dati_attuazione_misura["descrizione"],
                                "percentuale_riduzione" => $percentuale_riduzione);

        }

        return $result;

    }

    static function checkIfMisuraAttiva($impianto, $minaccia, $misura, $intervento = 0){
        global $adb, $table_prefix, $default_charset, $current_user;

        $debug = false;

        $stato_misura = false;

        $data_intervento = date("Y-m-d");

        $focus_impianto = CRMEntity::getInstance('Impianti');
        $focus_impianto->retrieve_entity_info($impianto, "Impianti", $dieOnError=false); 

        $azienda = $focus_impianto->column_fields["azienda"];
        $stabilimento = $focus_impianto->column_fields["stabilimento"];

        if( $intervento != 0 && $intervento != "" ){

            $focus_intervento = CRMEntity::getInstance('KpIntRidRischiPrivacy');
            $focus_intervento->retrieve_entity_info($intervento, "KpIntRidRischiPrivacy", $dieOnError=false); 

            $data_intervento = $focus_intervento->column_fields["kp_data_intervento"];
            
        }

        $stato_misura_rilevazione = self::checkStatoMisuraUltimaRilevazione($impianto, $minaccia, $misura, $data_intervento);

        $stato_misura = $stato_misura_rilevazione["attiva"];

        $data_rilevazione = $stato_misura_rilevazione["data_rilevazione"];

        $lista_interventi = self::listaInterventiImpiantoDaDataAData($azienda, $stabilimento, $data_rilevazione, $data_intervento);

        foreach( $lista_interventi as $intervento_temp ){

            $stato_misura_intervento = self::checkStatoMisuraIntervento($impianto, $minaccia, $misura, $intervento_temp);
            
            if( $stato_misura_intervento["misura_attiva"] == "Non attiva" && $stato_misura_intervento["adozione_misura"] == "No" ){

                $stato_misura = false;

            }
            elseif( $stato_misura_intervento["misura_attiva"] == "Non attiva" && $stato_misura_intervento["adozione_misura"] == "Si" ){

                //La misura risulta attiva solo se adottata da un intervento precedente all'attuale
                if( $intervento_temp == $intervento ){

                    $stato_misura = false;

                }
                else{

                    $stato_misura = true;

                }

            }
            elseif( $stato_misura_intervento["misura_attiva"] == "Attiva" ){

                $stato_misura = true;

            }

        }

        if( $stato_misura ){
            return "Si";
        }
        else{
            return "No";
        }

    }

    static function listaInterventiImpiantoDaDataAData($azienda, $stabilimento, $da_data, $a_data){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    inter.kpintridrischiprivacyid kpintridrischiprivacyid
                    FROM {$table_prefix}_kpintridrischiprivacy inter
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = inter.kpintridrischiprivacyid
                    WHERE ent.deleted = 0 AND inter.kp_azienda = ".$azienda." AND inter.kp_stabilimento = ".$stabilimento;

        if($da_data != ""){

            $query .= " AND inter.kp_data_intervento >= '".$da_data."' AND inter.kp_data_intervento <= '".$a_data."'";

        }
        else{

            $query .= " AND inter.kp_data_intervento <= '".$a_data."'";

        }

        $query .= " ORDER BY inter.kp_data_intervento ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'kpintridrischiprivacyid');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $result[] = $id;

        }

        return $result;

    }

    static function checkStatoMisuraUltimaRilevazione($impianto, $minaccia, $misura, $data_intervento){
        global $adb, $table_prefix, $default_charset, $current_user;

        $debug = false;

        $attiva = false;

        $data_rilevazione = "";

        $result = "";

        $dati_ultima_rilevazione = self::getRigaUltimaRilevazioneMinaccia($impianto, $minaccia, $data_intervento);

        if( $debug ){
            print_r($dati_ultima_rilevazione);die;
        }

        if( $dati_ultima_rilevazione["esiste"] ){

            if ( KpRilRischiPrivacyKp::checkIfMisuraAttiva( $dati_ultima_rilevazione["id"], $misura ) ){

                $attiva = true;

            }

            $data_rilevazione = $dati_ultima_rilevazione["data_rilevazione"];

        }

        $result = array("attiva" => $attiva,
                        "data_rilevazione" => $data_rilevazione);

        return $result;

    }

    static function getRigaUltimaRilevazioneMinaccia($impianto, $minaccia, $data){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    rig.kprigherilrischiprivaid kprigherilrischiprivaid,
                    ril.kp_data_rilevazione data_rilevazione
                    FROM {$table_prefix}_kprigherilrischipriva rig
                    INNER JOIN {$table_prefix}_crmentity entrig ON entrig.crmid = rig.kprigherilrischiprivaid
                    INNER JOIN {$table_prefix}_kprilrischiprivacy ril ON ril.kprilrischiprivacyid = rig.kp_rilevazione
                    INNER JOIN {$table_prefix}_crmentity entril ON entril.crmid = ril.kprilrischiprivacyid
                    WHERE entrig.deleted = 0 AND entril.deleted = 0 AND rig.kp_impianto = ".$impianto." AND rig.kp_minaccia = ".$minaccia." AND ril.kp_data_rilevazione <= '".$data."'
                    ORDER BY ril.kp_data_rilevazione DESC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $esiste = true;

            $id = $adb->query_result($result_query, 0, 'kprigherilrischiprivaid');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $data_rilevazione = $adb->query_result($result_query, 0, 'data_rilevazione');
            $data_rilevazione = html_entity_decode(strip_tags($data_rilevazione), ENT_QUOTES, $default_charset);

        }
        else{

            $esiste = false;
            $id = 0;
            $data_rilevazione = "";

        }

        $result = array("esiste" => $esiste,
                        "id" => $id,
                        "data_rilevazione" => $data_rilevazione);

        return $result;

    }

    static function checkStatoMisuraIntervento($impianto, $minaccia, $misura, $intervento){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $misura_attiva = "";
        $adozione_misura = "No";

        $query = "SELECT 
                    rig.kp_misura_attiva misura_attiva,
                    rig.kp_adozione_misura adozione_misura,
                    rig.description descrizione
                    FROM {$table_prefix}_kprigintridriscprivac rig
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rig.kprigintridriscprivacid
                    WHERE ent.deleted = 0 AND rig.kp_intervento = ".$intervento." AND rig.kp_impianto = ".$impianto."  AND rig.kp_minaccia = ".$minaccia." AND rig.kp_misura = ".$misura;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $misura_attiva_temp = $adb->query_result($result_query, 0, 'misura_attiva');
            $misura_attiva_temp = html_entity_decode(strip_tags($misura_attiva_temp), ENT_QUOTES, $default_charset);

            if( $misura_attiva_temp == '1' ){

                $misura_attiva = "Attiva";

            }
            else{

                $misura_attiva = "Non attiva";

            }

            $adozione_misura = $adb->query_result($result_query, 0, 'adozione_misura');
            $adozione_misura = html_entity_decode(strip_tags($adozione_misura), ENT_QUOTES, $default_charset);
            if($adozione_misura == null && $adozione_misura == ""){
                $adozione_misura = "No";
            }

            $descrizione = $adb->query_result($result_query, 0, 'descrizione');
            $descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES, $default_charset);
            if($descrizione == null && $descrizione == ""){
                $descrizione = "";
            }

        }

        $result = array("misura_attiva" => $misura_attiva,
                        "adozione_misura" => $adozione_misura,
                        "descrizione" => $descrizione);

        return $result;

    }

    static function getDatiRilevazioneRischiPrivacy($impianto, $minaccia, $da_data){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    righe.kp_tempi_ripristino tempi_ripristino,
                    righe.kp_probabilita_p probabilita,
                    righe.kp_magnitudo_p magnitudo,
                    righe.kp_rischio_p rischio,
                    ril.kp_data_rilevazione data_rilevazione
                    FROM {$table_prefix}_kprilrischiprivacy ril
                    INNER JOIN {$table_prefix}_crmentity entril ON entril.crmid = ril.kprilrischiprivacyid
                    INNER JOIN {$table_prefix}_kprigherilrischipriva righe ON righe.kp_rilevazione = ril.kprilrischiprivacyid
                    INNER JOIN {$table_prefix}_crmentity entrighe ON entrighe.crmid = righe.kprigherilrischiprivaid
                    WHERE entril.deleted = 0 AND entrighe.deleted = 0 AND ril.kp_data_rilevazione <= '".$da_data."' AND righe.kp_impianto = ".$impianto." AND righe.kp_minaccia = ".$minaccia."
                    ORDER BY ril.kp_data_rilevazione DESC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if($num_result > 0){

            $tempi_ripristino = $adb->query_result($result_query, 0, 'tempi_ripristino');
            $tempi_ripristino = html_entity_decode(strip_tags($tempi_ripristino), ENT_QUOTES, $default_charset);

            $probabilita = $adb->query_result($result_query, 0, 'probabilita');
            $probabilita = html_entity_decode(strip_tags($probabilita), ENT_QUOTES, $default_charset);

            $magnitudo = $adb->query_result($result_query, 0, 'magnitudo');
            $magnitudo = html_entity_decode(strip_tags($magnitudo), ENT_QUOTES, $default_charset);

            $rischio = $adb->query_result($result_query, 0, 'rischio');
            $rischio = html_entity_decode(strip_tags($rischio), ENT_QUOTES, $default_charset);

            $data_rilevazione = $adb->query_result($result_query, 0, 'data_rilevazione');
            $data_rilevazione = html_entity_decode(strip_tags($data_rilevazione), ENT_QUOTES, $default_charset);

        }
        else{

            $tempi_ripristino = "-";
            $probabilita = "-";
            $magnitudo = "-";
            $rischio = "-";
            $data_rilevazione = "";

        }

        $result = array("tempi_ripristino" => $tempi_ripristino,
                        "probabilita" => $probabilita,
                        "magnitudo" => $magnitudo,
                        "rischio" => $rischio,
                        "data_rilevazione" => $data_rilevazione);

        return $result;

    }

    static function setInterventoRiduzioneRischioPrivacy($id, $lista_valori){
        global $adb, $table_prefix, $default_charset, $current_user;
        
        $update = "UPDATE {$table_prefix}_kprigintridriscprivac SET
                    kp_aggiornato = '0'
                    WHERE kp_intervento = ".$id;
        $adb->query($update);
        
        foreach($lista_valori as $valore){

            if( $valore["attuata"] == "Si" ){

                self::setRigaInterventoRiduzioneRischioPrivacy($id, $valore);

            }

        }

        $update = "UPDATE {$table_prefix}_crmentity ent
                    INNER JOIN  {$table_prefix}_kprigintridriscprivac righe ON righe.kprigintridriscprivacid = ent.crmid 
                    SET
                    ent.deleted = 1
                    WHERE righe.kp_aggiornato = '0' AND righe.kp_tipo_int_privacy != 'Disattivazione misura' AND righe.kp_intervento = ".$id;
        
        $adb->query($update);

    }

    static function setRigaInterventoRiduzioneRischioPrivacy($id, $valore){
        global $adb, $table_prefix, $default_charset, $current_user;

        $verifica_esisenza = self::checkIfEsisteRigaInterventoRiduzione($id, $valore["impianto"], $valore["minaccia"], $valore["misura"]);

        if( $verifica_esisenza["esiste"] ){

            self::aggiornaRigaInterventoRiduzioneRischioPrivacy($verifica_esisenza["id"], $valore);

        }
        else{

            self::inserisciRigaInterventoRiduzioneRischioPrivacy($id, $valore, $valore["misura"]);

        }

    }

    static function setRigheInterventoRiduzioneRischioPrivacyMinacciaImpianto($id, $valore){
        global $adb, $table_prefix, $default_charset, $current_user;  
        
        $lista_misure = self::getMisureRiduzioneRischioPrivacy( $valore["minaccia"] );

        foreach($lista_misure as $misura){

            $verifica_esisenza = self::checkIfEsisteRigaInterventoRiduzione($id, $valore["impianto"], $valore["minaccia"], $misura["id"]);

            if( $verifica_esisenza["esiste"] ){
                
                self::aggiornaRigaInterventoRiduzioneRischioPrivacy($verifica_esisenza["id"], $valore);
    
            }
            else{
    
                self::inserisciRigaInterventoRiduzioneRischioPrivacy($id, $valore, $misura["id"]);
    
            }

        }

    }

    static function checkIfEsisteRigaInterventoRiduzione($intervento, $impianto, $minaccia, $misura){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    riga.kprigintridriscprivacid kprigintridriscprivacid
                    FROM {$table_prefix}_kprigintridriscprivac riga
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = riga.kprigintridriscprivacid
                    WHERE ent.deleted = 0 AND riga.kp_intervento = ".$intervento." AND riga.kp_impianto = ".$impianto." AND riga.kp_minaccia = ".$minaccia." AND riga.kp_misura = ".$misura;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $esiste = true;

            $id = $adb->query_result($result_query, 0, 'kprigintridriscprivacid');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

        }
        else{

            $esiste = false;
            $id = 0;

        }

        $result = array("esiste" => $esiste,
                        "id" => $id);

        return $result;

    }

    static function inserisciRigaInterventoRiduzioneRischioPrivacy($intervento, $valore, $misura){
        global $adb, $table_prefix, $default_charset, $current_user;

        $soggetto = "";

        $focus_intervento = CRMEntity::getInstance('KpIntRidRischiPrivacy');
        $focus_intervento->retrieve_entity_info($intervento, "KpIntRidRischiPrivacy", $dieOnError=false); 
        $data_intervento = $focus_intervento->column_fields["kp_data_intervento"];
        if($data_intervento != ""){
            $data_intervento_form = new DateTime($data_intervento);
            $data_intervento_form = $data_intervento_form->format('d/m/Y');
        }
        $assegnatario = $focus_intervento->column_fields["assigned_user_id"];

        $focus_impianto = CRMEntity::getInstance('Impianti');
        $focus_impianto->retrieve_entity_info($valore["impianto"], "Impianti", $dieOnError=false); 
        $nome_impianto = $focus_impianto->column_fields["impianto_name"];

        $focus_minaccia = CRMEntity::getInstance('KpMinaccePrivacy');
        $focus_minaccia->retrieve_entity_info($valore["minaccia"], "KpMinaccePrivacy", $dieOnError=false); 
        $nome_minaccia = $focus_minaccia->column_fields["kp_nome_minaccia"];

        $focus_misura = CRMEntity::getInstance('KpMisurePrivacy');
        $focus_misura->retrieve_entity_info($misura, "KpMisurePrivacy", $dieOnError=false); 
        $nome_misura = $focus_misura->column_fields["kp_nome_misura"];

        $dati_rilevazione = self::getDatiRilevazioneRischiPrivacy( $valore["impianto"], $valore["minaccia"], $data_intervento );

        $dati_precedenti = self::getDatiInterventoRiduzioneRischiPrivacyPrecedente( $valore["impianto"], $valore["minaccia"], $dati_rilevazione["data_rilevazione"], $data_intervento);

        if( $dati_precedenti["esiste"] ){
            
            $probabilita_pre = $dati_precedenti["probabilita"];
            $magnitudo_pre = $dati_precedenti["magnitudo"];
            $rischio_pre = $dati_precedenti["rischio"];

        }
        else{

            $probabilita_pre = $dati_rilevazione["probabilita"];
            $magnitudo_pre = $dati_rilevazione["magnitudo"];
            $rischio_pre = $dati_rilevazione["rischio"];

        }

        $soggetto = $nome_impianto." - ".$nome_minaccia." - ".$nome_misura." - ".$data_intervento_form;

        $focus_record = CRMEntity::getInstance('KpRigIntRidRiscPrivac');
        $focus_record->column_fields['assigned_user_id'] = $assegnatario;

        $focus_record->column_fields['kp_soggetto'] = $soggetto;
        $focus_record->column_fields['kp_intervento'] = $intervento;
        $focus_record->column_fields['kp_impianto'] = $valore["impianto"];
        $focus_record->column_fields['kp_minaccia'] = $valore["minaccia"];
        $focus_record->column_fields['kp_misura'] = $misura;

        $focus_record->column_fields['kp_magnitudo_pre'] = $magnitudo_pre;
        $focus_record->column_fields['kp_probabilita_pre'] = $probabilita_pre;
        $focus_record->column_fields['kp_rischio_pre'] = $rischio_pre;
        $focus_record->column_fields['kp_f_rischio_pr_pre'] = KpRilRischiPrivacyKp::getFraseDiRischio($rischio_pre);
            
        $focus_record->column_fields['kp_probabilita_p'] = $valore["probabilita"];
        $focus_record->column_fields['kp_magnitudo_p'] = $valore["magnitudo"];
        $focus_record->column_fields['kp_rischio_p'] = $valore["rischio"];
        $focus_record->column_fields['kp_frase_rischio_pr'] = KpRilRischiPrivacyKp::getFraseDiRischio($valore["rischio"]);
        $focus_record->column_fields['kp_adozione_misura'] = $valore["attuata"];
        $focus_record->column_fields['kp_misura_attiva'] = $valore["misura_attiva"];

        $focus_record->column_fields['kp_tipo_int_privacy'] = 'Attuazione misura';
        $focus_record->column_fields['kp_aggiornato'] = '1';
        $focus_record->save('KpRigIntRidRiscPrivac', $longdesc=true, $offline_update=false, $triggerEvent=false); 

        self::allineaRigheRiduzioneRischiPrivacy($focus_record->id);

    }

    static function aggiornaRigaInterventoRiduzioneRischioPrivacy($riga, $valore){
        global $adb, $table_prefix, $default_charset, $current_user;

        $focus_record = CRMEntity::getInstance('KpRigIntRidRiscPrivac');
        $focus_record->retrieve_entity_info($riga, "KpRigIntRidRiscPrivac", $dieOnError=false);
            
        $focus_record->column_fields['kp_probabilita_p'] = $valore["probabilita"];
        $focus_record->column_fields['kp_magnitudo_p'] = $valore["magnitudo"];
        $focus_record->column_fields['kp_rischio_p'] = $valore["rischio"];
        $focus_record->column_fields['kp_frase_rischio_pr'] = KpRilRischiPrivacyKp::getFraseDiRischio($valore["rischio"]);
        $focus_record->column_fields['kp_adozione_misura'] = $valore["attuata"];
        $focus_record->column_fields['kp_misura_attiva'] = $valore["misura_attiva"];

        $focus_record->column_fields['kp_aggiornato'] = '1';
        $focus_record->mode = 'edit';
        $focus_record->id = $riga;
        $focus_record->save('KpRigIntRidRiscPrivac', $longdesc=true, $offline_update=false, $triggerEvent=false);

        self::allineaRigheRiduzioneRischiPrivacy($riga);

    }

    static function getDatiRigaRiduzioneRischiPrivacy($intervento, $impianto, $minaccia, $misura ){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $verifica_esisenza = self::checkIfEsisteRigaInterventoRiduzione($intervento, $impianto, $minaccia, $misura);
        
        if( $verifica_esisenza["esiste"] ){

            $focus_riga = CRMEntity::getInstance('KpRigIntRidRiscPrivac');
            $focus_riga->retrieve_entity_info($verifica_esisenza["id"], "KpRigIntRidRiscPrivac", $dieOnError=false); 
            
            $adozione_misura = $focus_riga->column_fields["kp_adozione_misura"];
            $probabilita = $focus_riga->column_fields["kp_probabilita_p"];
            $magnitudo = $focus_riga->column_fields["kp_magnitudo_p"];
            $rischio = $focus_riga->column_fields["kp_rischio_p"];

        }
        else{

            $adozione_misura = "No";
            $probabilita = "-";
            $magnitudo = "-";
            $rischio = "-";

        }

        $result = array("adozione_misura" => $adozione_misura,
                        "probabilita" => $probabilita,
                        "magnitudo" => $magnitudo,
                        "rischio" => $rischio);

        return $result;

    }

    static function setDisattivazioneMisuraPrivacy($id, $impianto, $minaccia, $misura, $valori){
        global $adb, $table_prefix, $default_charset, $current_user;

        $verifica_esisenza = self::checkIfEsisteRigaInterventoRiduzione($id, $impianto, $minaccia, $misura);
        
        if( $verifica_esisenza["esiste"] ){

            self::aggiornaRigaDisattivazioneMisuraPrivacy($verifica_esisenza["id"], $valori);

        }
        else{

            self::inserisciRigaDisattivazioneMisuraPrivacy($id, $impianto, $minaccia, $misura, $valori);

        }

    }

    static function inserisciRigaDisattivazioneMisuraPrivacy($intervento, $impianto, $minaccia, $misura, $valori){
        global $adb, $table_prefix, $default_charset, $current_user;

        $soggetto = "";

        $focus_intervento = CRMEntity::getInstance('KpIntRidRischiPrivacy');
        $focus_intervento->retrieve_entity_info($intervento, "KpIntRidRischiPrivacy", $dieOnError=false); 
        $data_intervento = $focus_intervento->column_fields["kp_data_intervento"];
        if($data_intervento != ""){
            $data_intervento_form = new DateTime($data_intervento);
            $data_intervento_form = $data_intervento_form->format('d/m/Y');
        }
        $assegnatario = $focus_intervento->column_fields["assigned_user_id"];

        $focus_impianto = CRMEntity::getInstance('Impianti');
        $focus_impianto->retrieve_entity_info($impianto, "Impianti", $dieOnError=false); 
        $nome_impianto = $focus_impianto->column_fields["impianto_name"];

        $focus_minaccia = CRMEntity::getInstance('KpMinaccePrivacy');
        $focus_minaccia->retrieve_entity_info($minaccia, "KpMinaccePrivacy", $dieOnError=false); 
        $nome_minaccia = $focus_minaccia->column_fields["kp_nome_minaccia"];

        $focus_misura = CRMEntity::getInstance('KpMisurePrivacy');
        $focus_misura->retrieve_entity_info($misura, "KpMisurePrivacy", $dieOnError=false); 
        $nome_misura = $focus_misura->column_fields["kp_nome_misura"];

        $dati_rilevazione = self::getDatiRilevazioneRischiPrivacy( $impianto, $minaccia, $data_intervento );

        $dati_precedenti = self::getDatiInterventoRiduzioneRischiPrivacyPrecedente( $impianto, $minaccia, $dati_rilevazione["data_rilevazione"], $data_intervento);

        if( $dati_precedenti["esiste"] ){
            
            $probabilita_pre = $dati_precedenti["probabilita"];
            $magnitudo_pre = $dati_precedenti["magnitudo"];
            $rischio_pre = $dati_precedenti["rischio"];

        }
        else{

            $probabilita_pre = $dati_rilevazione["probabilita"];
            $magnitudo_pre = $dati_rilevazione["magnitudo"];
            $rischio_pre = $dati_rilevazione["rischio"];

        }

        $soggetto = $nome_impianto." - ".$nome_minaccia." - ".$nome_misura." - ".$data_intervento_form;

        $focus_record = CRMEntity::getInstance('KpRigIntRidRiscPrivac');
        $focus_record->column_fields['assigned_user_id'] = $assegnatario;

        $focus_record->column_fields['kp_soggetto'] = $soggetto;
        $focus_record->column_fields['kp_intervento'] = $intervento;
        $focus_record->column_fields['kp_impianto'] = $impianto;
        $focus_record->column_fields['kp_minaccia'] = $minaccia;
        $focus_record->column_fields['kp_misura'] = $misura;

        $focus_record->column_fields['kp_magnitudo_pre'] = $magnitudo_pre;
        $focus_record->column_fields['kp_probabilita_pre'] = $probabilita_pre;
        $focus_record->column_fields['kp_rischio_pre'] = $rischio_pre;
        $focus_record->column_fields['kp_f_rischio_pr_pre'] = KpRilRischiPrivacyKp::getFraseDiRischio($rischio_pre);
            
        $focus_record->column_fields['kp_probabilita_p'] = $valori["probabilita"];
        $focus_record->column_fields['kp_magnitudo_p'] = $valori["magnitudo"];
        $focus_record->column_fields['kp_rischio_p'] = $valori["rischio"];
        $focus_record->column_fields['kp_frase_rischio_pr'] = KpRilRischiPrivacyKp::getFraseDiRischio($valore["rischio"]);
        $focus_record->column_fields['kp_adozione_misura'] = "No";
        $focus_record->column_fields['kp_misura_attiva'] = '0';

        $focus_record->column_fields['kp_tipo_int_privacy'] = 'Disattivazione misura';
        $focus_record->column_fields['kp_aggiornato'] = '1';
        $focus_record->save('KpRigIntRidRiscPrivac', $longdesc=true, $offline_update=false, $triggerEvent=false); 

        self::allineaRigheRiduzioneRischiPrivacy($focus_record->id);

    }

    static function aggiornaRigaDisattivazioneMisuraPrivacy($riga, $valori){
        global $adb, $table_prefix, $default_charset, $current_user;

        $focus_record = CRMEntity::getInstance('KpRigIntRidRiscPrivac');
        $focus_record->retrieve_entity_info($riga, "KpRigIntRidRiscPrivac", $dieOnError=false);
            
        $focus_record->column_fields['kp_probabilita_p'] = $valori["probabilita"];
        $focus_record->column_fields['kp_magnitudo_p'] = $valori["magnitudo"];
        $focus_record->column_fields['kp_rischio_p'] = $valori["rischio"];
        $focus_record->column_fields['kp_frase_rischio_pr'] = KpRilRischiPrivacyKp::getFraseDiRischio($valore["rischio"]);
        $focus_record->column_fields['kp_misura_attiva'] = '0';

        $focus_record->column_fields['kp_tipo_int_privacy'] = 'Disattivazione misura';
        $focus_record->column_fields['kp_aggiornato'] = '1';
        $focus_record->mode = 'edit';
        $focus_record->id = $riga;
        $focus_record->save('KpRigIntRidRiscPrivac', $longdesc=true, $offline_update=false, $triggerEvent=false);

        self::allineaRigheRiduzioneRischiPrivacy($riga);

    }

    static function allineaRigheRiduzioneRischiPrivacy($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $focus_riga = CRMEntity::getInstance('KpRigIntRidRiscPrivac');
        $focus_riga->retrieve_entity_info($id, "KpRigIntRidRiscPrivac", $dieOnError=false); 

        $intervento = $focus_riga->column_fields["kp_intervento"];
        $impianto = $focus_riga->column_fields["kp_impianto"];
        $minaccia = $focus_riga->column_fields["kp_minaccia"];

        $probabilita_pre = $focus_riga->column_fields["kp_probabilita_pre"];
        $magnitudo_pre = $focus_riga->column_fields["kp_magnitudo_pre"];
        $rischio_pre = $focus_riga->column_fields["kp_rischio_pre"];

        $probabilita = $focus_riga->column_fields["kp_probabilita_p"];
        $magnitudo = $focus_riga->column_fields["kp_magnitudo_p"];
        $rischio = $focus_riga->column_fields["kp_rischio_p"];

        $update = "UPDATE {$table_prefix}_kprigintridriscprivac SET
                    kp_probabilita_pre = '".$probabilita_pre."',
                    kp_magnitudo_pre = '".$magnitudo_pre."',
                    kp_rischio_pre = '".$rischio_pre."',
                    kp_probabilita_p = '".$probabilita."',
                    kp_magnitudo_p = '".$magnitudo."',
                    kp_rischio_p = '".$rischio."'
                    WHERE kp_intervento = ".$intervento." AND kp_impianto = ".$impianto." AND kp_minaccia = ".$minaccia." AND kprigintridriscprivacid != ".$id;
    
        $adb->query($update); 

    }

    static function setNotaInterventoRiduzioneRischioPrivacy($id, $impianto, $minaccia, $misura, $nota){
        global $adb, $table_prefix, $default_charset, $current_user;

        $verifica_esisenza = self::checkIfEsisteRigaInterventoRiduzione($id, $impianto, $minaccia, $misura);
        
        if( $verifica_esisenza["esiste"] ){

            self::aggiornaNotaRigaInterventoRiduzioneRischioPrivacy($verifica_esisenza["id"], $nota);

        }

    }

    static function aggiornaNotaRigaInterventoRiduzioneRischioPrivacy($riga, $nota){
        global $adb, $table_prefix, $default_charset, $current_user;

        $nota = addslashes("$nota");
        
        $update = "UPDATE {$table_prefix}_kprigintridriscprivac SET
                    description = '".$nota."'
                    WHERE kprigintridriscprivacid = ".$riga;
        
        $adb->query($update);

    }

} 

?>