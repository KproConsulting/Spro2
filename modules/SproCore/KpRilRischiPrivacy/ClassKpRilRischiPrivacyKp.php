<?php 

/* kpro@20170914165304 */ 

/** 
 * @copyright (c) 2017, Kpro Consulting Srl 
 * 
 * Estensione classe KpRilRischiPrivacy 
 */ 

require_once('modules/KpRilRischiPrivacy/KpRilRischiPrivacy.php'); 

class KpRilRischiPrivacyKp extends KpRilRischiPrivacy { 

    public $kpshowRilevazioneRischiTab;

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
    
        $this->kpshowRilevazioneRischiTab = true;	/* kpro@150920170948 */ 

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
        return $smarty->fetch('SproCore/KpRilevazioneRischiPrivacy.tpl');   /* kpro@150920170948 */ 
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
        if ($this->kpshowRilevazioneRischiTab) {
            $return[] = array('label'=>getTranslatedString('Rilevazione Rischio','KpRilevazioneRischiTab'),'href'=>'','onclick'=>"kpChangeDetailTab('{$moduleName}', '{$this->id}', 'KpRilevazioneRischiTab', this)");
        }
        /* kpro@150920170948 end */ 

        return $return;
    }

    static function getRilevazioneRischioPrivacy($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $focus_rilevazione = CRMEntity::getInstance('KpRilRischiPrivacy');
        $focus_rilevazione->retrieve_entity_info($id, "KpRilRischiPrivacy", $dieOnError=false); 
        
        $data_rilevazione = $focus_rilevazione->column_fields["kp_data_rilevazione"];
        $azienda = $focus_rilevazione->column_fields["kp_azienda"];
        $stabilimento = $focus_rilevazione->column_fields["kp_stabilimento"];

        $lista_impianti = self::getImpiantiConMinaccePrivacy($azienda, $stabilimento, $data_rilevazione);

        $lista_opzioni_tempi_ripristino = self::getOpzioniTempiDiRipristino();

        foreach($lista_impianti as $impianto){

            $lista_minacce = self::getMinacceImpianto( $impianto["id"], $id );

            $result[] = array("impianto" => $impianto["id"],
                                "nome_impianto" => $impianto["nome"],
                                "opzioni_tempi_ripristino" => $lista_opzioni_tempi_ripristino,
                                "lista_minacce" => $lista_minacce,
                                "numero_minacce" => count($lista_minacce));

        }

        return $result;

    }

    static function getImpiantiConMinaccePrivacy($azienda, $stabilimento, $data){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT t.* FROM
                    ((SELECT 
                    imp.impiantiid impianto,
                    imp.impianto_name nome_impianto,
                    imp.azienda azienda,
                    imp.stabilimento stabilimento,
                    imp.stato_impianto stato_impianto,
                    imp.data_attivazione_imp data_attivazione_imp,
                    imp.data_dismissione_imp data_dismissione_imp
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_impianti imp ON imp.impiantiid = rel.crmid
                    INNER JOIN {$table_prefix}_crmentity entimp ON entimp.crmid = rel.crmid
                    INNER JOIN {$table_prefix}_crmentity entmin ON entmin.crmid = rel.relcrmid
                    WHERE entimp.deleted = 0 AND entmin.deleted = 0 AND rel.module = 'Impianti' AND rel.relmodule = 'KpMinaccePrivacy')
                    UNION
                    (SELECT 
                    imp.impiantiid impianto,
                    imp.impianto_name nome_impianto,
                    imp.azienda azienda,
                    imp.stabilimento stabilimento,
                    imp.stato_impianto stato_impianto,
                    imp.data_attivazione_imp data_attivazione_imp,
                    imp.data_dismissione_imp data_dismissione_imp
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_impianti imp ON imp.impiantiid = rel.relcrmid
                    INNER JOIN {$table_prefix}_crmentity entimp ON entimp.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_crmentity entmin ON entmin.crmid = rel.crmid
                    WHERE entimp.deleted = 0 AND entmin.deleted = 0 AND rel.module = 'KpMinaccePrivacy' AND rel.relmodule = 'Impianti')) AS t
                    WHERE t.data_attivazione_imp <= '".$data."' AND (t.data_dismissione_imp IS NULL OR t.data_dismissione_imp = '' OR t.data_dismissione_imp > '".$data."') AND t.azienda = ".$azienda." AND t.stabilimento = ".$stabilimento."
                    GROUP BY t.impianto
                    ORDER BY t.nome_impianto ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'impianto');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome_impianto');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);
            
            $result[] = array("id" => $id,
                                "nome" => $nome);

        }

        return $result;

    }

    static function getMinacceImpianto($impianto, $rilevazione = 0){
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

            if($rilevazione != 0){

                $verifica_esisenza = self::checkIfEsisteRigaRilevazione($rilevazione, $impianto, $id);

            }
            else{

                $verifica_esisenza["esiste"] = false;

            }

            if( $verifica_esisenza["esiste"] ){

                $focus_riga_rilevazione = CRMEntity::getInstance('KpRigheRilRischiPriva');
                $focus_riga_rilevazione->retrieve_entity_info($verifica_esisenza["id"], "KpRigheRilRischiPriva", $dieOnError=false);

                $tempi_ripristino = $focus_riga_rilevazione->column_fields["kp_tempi_ripristino"];
                $probabilita = $focus_riga_rilevazione->column_fields["kp_probabilita_p"];
                $magnitudo = $focus_riga_rilevazione->column_fields["kp_magnitudo_p"];
                $rischio = $focus_riga_rilevazione->column_fields["kp_rischio_p"];
                $descrizione = $focus_riga_rilevazione->column_fields["description"];

                $lista_misure = self::getMisureRiduzioneRischioPrivacy( $id, $verifica_esisenza["id"] );

            }
            else{
                
                $tempi_ripristino = "-";
                $probabilita = "-";
                $magnitudo = "-";
                $rischio = "-";
                $descrizione = "";

                $lista_misure = self::getMisureRiduzioneRischioPrivacy( $id, 0 );
    
            }
            
            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "tempi_ripristino" => $tempi_ripristino,
                                "probabilita" => $probabilita,
                                "magnitudo" => $magnitudo,
                                "rischio" => $rischio,
                                "descrizione" => $descrizione,
                                "lista_misure" => $lista_misure);

        }

        return $result;

    }

    static function getMisureRiduzioneRischioPrivacy( $minaccia, $riga_rilevazione ){
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

        $attiva = "No";

        if ( $riga_rilevazione != 0 && self::checkIfMisuraAttiva( $riga_rilevazione, $id ) ){

            $attiva = "Si";

        }

        $result[] = array("id" => $id,
                            "nome" => $nome,
                            "percentuale_riduzione" => $percentuale_riduzione,
                            "attiva" => $attiva);

        }

        return $result;

    }

    static function checkIfMisuraAttiva( $riga_rilevazione, $misura ){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = false;

        $query = "SELECT 
                    * 
                    FROM vte_crmentityrel
                    WHERE 
                    (crmid = ".$riga_rilevazione." AND module = 'KpRigheRilRischiPriva' AND relcrmid = ".$misura." AND relmodule = 'KpMisurePrivacy')
                    OR
                    (relcrmid = ".$riga_rilevazione." AND relmodule = 'KpRigheRilRischiPriva' AND crmid = ".$misura." AND module = 'KpMisurePrivacy')";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){
            
            $result = true;

        }

        return $result;

    }

    static function getOpzioniTempiDiRipristino(){
        global $adb, $table_prefix, $default_charset, $current_user;
        
        $result = array();

        $query = "SELECT 
                    kp_tempi_ripristino 
                    FROM {$table_prefix}_kp_tempi_ripristino";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $tempi_ripristino = $adb->query_result($result_query, $i, 'kp_tempi_ripristino');
            $tempi_ripristino = html_entity_decode(strip_tags($tempi_ripristino), ENT_QUOTES, $default_charset);

            $result[] = $tempi_ripristino;

        }

        return $result;

    }

    static function setRilevazioneRischioPrivacy($id, $lista_valori){
        global $adb, $table_prefix, $default_charset, $current_user;

        $update = "UPDATE {$table_prefix}_kprigherilrischipriva SET
                    kp_aggiornato = '0'
                    WHERE kp_rilevazione = ".$id;
        $adb->query($update);

        foreach($lista_valori as $valore){

            self::setRigaRilevazioneRischioPrivacy($id, $valore);

        }

        $update = "UPDATE {$table_prefix}_crmentity ent
                    INNER JOIN  {$table_prefix}_kprigherilrischipriva righe ON rige.kprigherilrischiprivaid = ent.crmid 
                    SET
                    ent.deleted = 1
                    WHERE righe.kp_aggiornato = '0' AND righe.kp_rilevazione = ".$id;
        $adb->query($update);

    }

    static function setRigaRilevazioneRischioPrivacy($id, $valore){
        global $adb, $table_prefix, $default_charset, $current_user;

        $verifica_esisenza = self::checkIfEsisteRigaRilevazione($id, $valore["impianto"], $valore["minaccia"]);

        if( $verifica_esisenza["esiste"] ){

            self::aggiornaRigaRilevazioneRischioPrivacy($verifica_esisenza["id"], $valore);

        }
        else{

            self::inserisciRigaRilevazioneRischioPrivacy($id, $valore);

        }

    }

    static function checkIfEsisteRigaRilevazione($rilevazione, $impianto, $minaccia){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    riga.kprigherilrischiprivaid kprigherilrischiprivaid
                    FROM {$table_prefix}_kprigherilrischipriva riga
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = riga.kprigherilrischiprivaid
                    WHERE ent.deleted = 0 AND riga.kp_rilevazione = ".$rilevazione." AND riga.kp_impianto = ".$impianto." AND riga.kp_minaccia = ".$minaccia;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $esiste = true;

            $id = $adb->query_result($result_query, 0, 'kprigherilrischiprivaid');
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

    static function aggiornaRigaRilevazioneRischioPrivacy($riga, $valore){
        global $adb, $table_prefix, $default_charset, $current_user;

        $focus_record = CRMEntity::getInstance('KpRigheRilRischiPriva');
        $focus_record->retrieve_entity_info($riga, "KpRigheRilRischiPriva", $dieOnError=false);

        $focus_record->column_fields['kp_probabilita_p'] = $valore["probabilita"];
        $focus_record->column_fields['kp_magnitudo_p'] = $valore["magnitudo"];
        $focus_record->column_fields['kp_rischio_p'] = $valore["rischio"];
        $focus_record->column_fields['kp_frase_rischio_pr'] = self::getFraseDiRischio($valore["rischio"]);
        $focus_record->column_fields['kp_tempi_ripristino'] = $valore["ripristino"];

        $focus_record->column_fields['kp_aggiornato'] = '1';
        $focus_record->mode = 'edit';
        $focus_record->id = $riga;
        $focus_record->save('KpRigheRilRischiPriva', $longdesc=true, $offline_update=false, $triggerEvent=false);

        self::setMisureAttiveImpianto( $riga, $valore["array_misure"] );

    }

    static function inserisciRigaRilevazioneRischioPrivacy($rilevazione, $valore){
        global $adb, $table_prefix, $default_charset, $current_user;

        $soggetto = "";

        $focus_rilevazione = CRMEntity::getInstance('KpRilRischiPrivacy');
        $focus_rilevazione->retrieve_entity_info($rilevazione, "KpRilRischiPrivacy", $dieOnError=false); 
        $data_rilevazione = $focus_rilevazione->column_fields["kp_data_rilevazione"];
        if($data_rilevazione != ""){
            $data_rilevazione_form = new DateTime($data_rilevazione);
            $data_rilevazione_form = $data_rilevazione_form->format('d/m/Y');
        }
        $assegnatario = $focus_rilevazione->column_fields["assigned_user_id"];

        $focus_impianto = CRMEntity::getInstance('Impianti');
        $focus_impianto->retrieve_entity_info($valore["impianto"], "Impianti", $dieOnError=false); 
        $nome_impianto = $focus_impianto->column_fields["impianto_name"];

        $focus_minaccia = CRMEntity::getInstance('KpMinaccePrivacy');
        $focus_minaccia->retrieve_entity_info($valore["minaccia"], "KpMinaccePrivacy", $dieOnError=false); 
        $nome_minaccia = $focus_minaccia->column_fields["kp_nome_minaccia"];

        $soggetto = $nome_impianto." - ".$nome_minaccia." - ".$data_rilevazione_form;

        $focus_record = CRMEntity::getInstance('KpRigheRilRischiPriva');
        $focus_record->column_fields['assigned_user_id'] = $assegnatario;

        $focus_record->column_fields['kp_soggetto'] = $soggetto;
        $focus_record->column_fields['kp_rilevazione'] = $rilevazione;
        $focus_record->column_fields['kp_impianto'] = $valore["impianto"];
        $focus_record->column_fields['kp_minaccia'] = $valore["minaccia"];

        $focus_record->column_fields['kp_probabilita_p'] = $valore["probabilita"];
        $focus_record->column_fields['kp_magnitudo_p'] = $valore["magnitudo"];
        $focus_record->column_fields['kp_rischio_p'] = $valore["rischio"];
        $focus_record->column_fields['kp_frase_rischio_pr'] = self::getFraseDiRischio($valore["rischio"]);
        $focus_record->column_fields['kp_tempi_ripristino'] = $valore["ripristino"];

        $focus_record->column_fields['kp_aggiornato'] = '1';
        $focus_record->save('KpRigheRilRischiPriva', $longdesc=true, $offline_update=false, $triggerEvent=false); 

        self::setMisureAttiveImpianto( $focus_record->id, $valore["array_misure"] );

    }

    static function setMisureAttiveImpianto($riga_rilevazione, $array_misure){
        global $adb, $table_prefix, $default_charset, $current_user;

        self::clearRelatedMisureAttiviImpianti( $riga_rilevazione );

        if( count($array_misure) > 0 ){

            foreach( $array_misure as $misura ){

                if( $misura["attiva"] == "Si" && $misura["misura"] != null && $misura["misura"] != "" && $misura["misura"] != 0 ){

                    $insert = "INSERT INTO {$table_prefix}_crmentityrel (crmid, module, relcrmid, relmodule)
                                VALUES
                                (".$riga_rilevazione.", 'KpRigheRilRischiPriva', ".$misura["misura"].", 'KpMisurePrivacy')";
                    
                    $adb->query($insert);

                }

            }

        }

    }

    static function clearRelatedMisureAttiviImpianti($riga_rilevazione){
        global $adb, $table_prefix, $default_charset, $current_user;

        $delete = "DELETE FROM {$table_prefix}_crmentityrel
                    WHERE 
                    (crmid = ".$riga_rilevazione." AND module = 'KpRigheRilRischiPriva' AND relmodule = 'KpMisurePrivacy')
                    OR
                    (relcrmid = ".$riga_rilevazione." AND relmodule = 'KpRigheRilRischiPriva' AND module = 'KpMisurePrivacy')";

        $adb->query($delete);

    }

    static function setNotaRilevazioneRischioPrivacy($id, $impianto, $minaccia, $nota){
        global $adb, $table_prefix, $default_charset, $current_user;

        $verifica_esisenza = self::checkIfEsisteRigaRilevazione($id, $impianto, $minaccia);

        if( $verifica_esisenza["esiste"] ){
            
            self::aggiornaNotaInRigaRilevazioneRischioPrivacy($verifica_esisenza["id"], $nota);

        }
        else{

            self::inserisciRigaRilevazioneRischioPrivacyConNota($id, $impianto, $minaccia, $nota);

        }

    }

    static function aggiornaNotaInRigaRilevazioneRischioPrivacy($riga, $nota){
        global $adb, $table_prefix, $default_charset, $current_user;

        $nota = addslashes("$nota");

        $update = "UPDATE {$table_prefix}_kprigherilrischipriva SET
                    description = '".$nota."'
                    WHERE kprigherilrischiprivaid = ".$riga;
        
        $adb->query($update);

    }

    static function inserisciRigaRilevazioneRischioPrivacyConNota($rilevazione, $impianto, $minaccia, $nota){
        global $adb, $table_prefix, $default_charset, $current_user;

        $soggetto = "";

        $focus_rilevazione = CRMEntity::getInstance('KpRilRischiPrivacy');
        $focus_rilevazione->retrieve_entity_info($rilevazione, "KpRilRischiPrivacy", $dieOnError=false); 
        $data_rilevazione = $focus_rilevazione->column_fields["kp_data_rilevazione"];
        if($data_rilevazione != ""){
            $data_rilevazione_form = new DateTime($data_rilevazione);
            $data_rilevazione_form = $data_rilevazione_form->format('d/m/Y');
        }
        $assegnatario = $focus_rilevazione->column_fields["assigned_user_id"];

        $focus_impianto = CRMEntity::getInstance('Impianti');
        $focus_impianto->retrieve_entity_info($impianto, "Impianti", $dieOnError=false); 
        $nome_impianto = $focus_impianto->column_fields["impianto_name"];

        $focus_minaccia = CRMEntity::getInstance('KpMinaccePrivacy');
        $focus_minaccia->retrieve_entity_info($minaccia, "KpMinaccePrivacy", $dieOnError=false); 
        $nome_minaccia = $focus_minaccia->column_fields["kp_nome_minaccia"];

        $soggetto = $nome_impianto." - ".$nome_minaccia." - ".$data_rilevazione_form;

        $focus_record = CRMEntity::getInstance('KpRigheRilRischiPriva');
        $focus_record->column_fields['assigned_user_id'] = $assegnatario;

        $focus_record->column_fields['kp_soggetto'] = $soggetto;
        $focus_record->column_fields['kp_rilevazione'] = $rilevazione;
        $focus_record->column_fields['kp_impianto'] = $impianto;
        $focus_record->column_fields['kp_minaccia'] = $minaccia;

        $focus_record->column_fields['description'] = $nota;

        $focus_record->column_fields['kp_aggiornato'] = '1';
        $focus_record->save('KpRigheRilRischiPriva', $longdesc=true, $offline_update=false, $triggerEvent=false); 

    }

    static function getFraseDiRischio($valore_rischio) {
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "--Nessuno--";
        
        if ($valore_rischio > 0 && $valore_rischio <= 5) {
            $result = "Irrilevante";
        } else if ($valore_rischio > 5 && $valore_rischio <= 10) {
            $result = "Minore";
        } else if ($valore_rischio > 10 && $valore_rischio <= 15) {
            $result = "Moderato";
        } else if ($valore_rischio > 15 && $valore_rischio <= 20) {
            $result = "Significativo";
        } else if ($valore_rischio > 20 && $valore_rischio <= 25) {
            $result = "Estremo";
        }
    
        return $result;

    }


} 

?>