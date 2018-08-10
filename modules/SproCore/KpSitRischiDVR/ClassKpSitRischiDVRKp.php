<?php 

/* kpro@20180709143331 */ 

/** 
 * @copyright (c) 2018, Kpro Consulting Srl 
 * 
 * Estensione classe KpSitRischiDVR 
 */ 

require_once('modules/KpSitRischiDVR/KpSitRischiDVR.php'); 

class KpSitRischiDVRKp extends KpSitRischiDVR { 

    var $list_fields = Array();
    
    var $list_fields_name = Array(
        'Soggetto' => 'kp_soggetto',
        'Azienda' => 'kp_azienda',
        'Area Stabilimento' => 'kp_area_stab',
        'Collegato a' => 'kp_related_to',
        'Pericolo' => 'kp_rischio',
        'Valutazione Rischio' => 'kp_valutazione_risc',
        'Frase di Rischio' => 'kp_frase_risc_dvr'
    );

    function KpSitRischiDVRKp(){
        global $table_prefix;
        parent::__construct();
        $this->list_fields = Array(
            'Soggetto'=>Array($table_prefix.'_kpsitrischidvr'=>'kp_soggetto'),
            'Azienda'=>Array($table_prefix.'_kpsitrischidvr'=>'kp_azienda'),
            'Area Stabilimento'=>Array($table_prefix.'_kpsitrischidvr'=>'kp_area_stab'),
            'Collegato a'=>Array($table_prefix.'_kpsitrischidvr'=>'kp_related_to'),
            'Pericolo'=>Array($table_prefix.'_kpsitrischidvr'=>'kp_rischio'),
            'Valutazione Rischio'=>Array($table_prefix.'_kpsitrischidvr'=>'kp_valutazione_risc'),
            'Frase di Rischio'=>Array($table_prefix.'_kpsitrischidvr'=>'kp_frase_risc_dvr')
        );
    }

    //Script modifica Funtion Save
    /*function save_module($module){
        
        global $table_prefix, $adb;

        parent::save_module($module);


    }*/

    static function aggiornaSituazioneRischiDVR(){
        global $adb, $table_prefix, $default_charset, $current_user;

        $debug = false;

        $update = "UPDATE {$table_prefix}_kpsitrischidvr SET
                    kp_aggiornato = '0'";
        $adb->query($update);

        $filtro_aziende = array();

        $lista_aziende = self::getListaAziendeRilevate( $filtro_aziende );

        foreach($lista_aziende as $azienda){

            if( debug ){
                printf("\n<br /> - Calcolo Sit. Azienda %s ", $azienda["nome"] );
            }

            $filtro_stabilimenti = array("azienda_id" => $azienda["id"]);

            $lista_stabilimenti = self::getListaStabilimentiRilevati( $filtro_stabilimenti );

            foreach($lista_stabilimenti as $stabilimento){

                if( debug ){
                    printf("\n<br /> --- Calcolo Sit. Stabilimento %s ", $stabilimento["nome"] );
                }

                $filtro_aree = array("azienda_id" => $azienda["id"],
                                    "stabilimento_id" => $stabilimento["id"]);

                $lista_aree = self::getListaAreeRilevate( $filtro_aree );

                foreach($lista_aree as $area){

                    if( debug ){
                        printf("\n<br /> ----- Calcolo Sit. Area %s ", $area["nome"] );
                    }

                    self::setSituazioneRischiDVRArea($azienda["id"], $stabilimento["id"], $area["id"]);

                }

            }

        }

        $update = "UPDATE {$table_prefix}_crmentity ent
                    INNER JOIN {$table_prefix}_kpsitrischidvr sit ON sit.kpsitrischidvrid = ent.crmid 
                    SET
                    ent.deleted = 1
                    WHERE sit.kp_aggiornato = '0'";
        $adb->query($update);

    }

    static function getListaAziendeRilevate($filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    ril.kp_azienda id,
                    acc.accountname nome
                    FROM {$table_prefix}_kprilevazionirischi ril
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = ril.kprilevazionirischiid
                    INNER JOIN {$table_prefix}_account acc ON acc.accountid = ril.kp_azienda
                    INNER JOIN {$table_prefix}_crmentity entacc ON entacc.crmid = acc.accountid
                    WHERE ent.deleted = 0 AND entacc.deleted = 0";

        $query .= " GROUP BY ril.kp_azienda
                    ORDER BY acc.accountname ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);
            
            $result[] = array("id" => $id,
                                "nome" => $nome);

        }

        return $result;

    }

    static function getListaStabilimentiRilevati($filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    ril.kp_stabilimento id,
                    stab.nome_stabilimento nome
                    FROM {$table_prefix}_kprilevazionirischi ril
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = ril.kprilevazionirischiid
                    INNER JOIN {$table_prefix}_account acc ON acc.accountid = ril.kp_azienda
                    INNER JOIN {$table_prefix}_crmentity entacc ON entacc.crmid = acc.accountid
                    INNER JOIN {$table_prefix}_stabilimenti stab ON stab.stabilimentiid = ril.kp_stabilimento
                    WHERE ent.deleted = 0 AND entacc.deleted = 0";

        if( $filtro["azienda_id"] && $filtro["azienda_id"] != 0 ){
            $query .= " AND acc.accountid = ".$filtro["azienda_id"];
        }

        $query .= " GROUP BY ril.kp_stabilimento
                    ORDER BY stab.nome_stabilimento ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);
            
            $result[] = array("id" => $id,
                                "nome" => $nome);

        }

        return $result;

    }

    static function getListaAreeRilevate($filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    ril.kp_area_stab id,
                    aree.kp_nome_area nome
                    FROM {$table_prefix}_kprilevazionirischi ril
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = ril.kprilevazionirischiid
                    INNER JOIN {$table_prefix}_account acc ON acc.accountid = ril.kp_azienda
                    INNER JOIN {$table_prefix}_crmentity entacc ON entacc.crmid = acc.accountid
                    INNER JOIN {$table_prefix}_stabilimenti stab ON stab.stabilimentiid = ril.kp_stabilimento
                    INNER JOIN {$table_prefix}_kpareestabilimento aree ON aree.kpareestabilimentoid = ril.kp_area_stab
                    WHERE ent.deleted = 0 AND entacc.deleted = 0";

        if( $filtro["azienda_id"] && $filtro["azienda_id"] != 0 ){
            $query .= " AND acc.accountid = ".$filtro["azienda_id"];
        }

        if( $filtro["stabilimento_id"] && $filtro["stabilimento_id"] != 0 ){
            $query .= " AND stab.stabilimentiid = ".$filtro["stabilimento_id"];
        }

        $query .= " GROUP BY ril.kp_area_stab
                    ORDER BY aree.kp_nome_area ASC";

        //print($query);die;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);
            
            $result[] = array("id" => $id,
                                "nome" => $nome);

        }

        return $result;

    }

    static function setSituazioneRischiDVRArea($azienda, $stabilimento, $area){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__."/../KpRilevazioniRischi/KpRilevazioneRischiClass.php");

        $debug = false;

        $dati_rilevazione = self::getDatiUltimaRilevazioneRischiArea( $azienda, $stabilimento, $area );
        
        //Devo quindi sottrarre tutte le misure attuate oltre da lata dell'ultima rilevazione

        if( $dati_rilevazione["esiste"] ){

            $righe_rilevazione = $dati_rilevazione["righe"];

            foreach( $righe_rilevazione as $riga_rilevazione ){

                if( $debug ){

                    printf("\n<br /> ------- Calcolo Riga %s ", $riga_rilevazione["nome"] );

                }

                $dati_situazione_esistente = self::checkIfEsisteSituazioneRischio( $azienda, $stabilimento, $area, $riga_rilevazione["related_to"], $riga_rilevazione["rischio_id"] );

                $soggetto = $riga_rilevazione["nome"];

                if( $dati_situazione_esistente["esiste"] ){

                    $id = $dati_situazione_esistente["id"];

                    $focus = CRMEntity::getInstance('KpSitRischiDVR');
                    $focus->retrieve_entity_info($id, "KpSitRischiDVR", $dieOnError=false);
                    $focus->column_fields['assigned_user_id'] =  1;

                    $focus->column_fields['kp_soggetto'] = $soggetto;
                    $focus->column_fields['kp_rilevazione_risc'] = $dati_rilevazione["id"];
                    $focus->column_fields['kp_data_rilevazione'] = $dati_rilevazione["data_rilevazione"];
                    $focus->column_fields['kp_gravita_rischio'] = $riga_rilevazione["gravita_rischio"];
                    $focus->column_fields['kp_probabilita_risc'] = $riga_rilevazione["probabilita_risc"];
                    $focus->column_fields['kp_misurazione'] = $riga_rilevazione["misurazione"];
                    $focus->column_fields['kp_valutazione_risc'] = $riga_rilevazione["valutazione_risc"];
                    $focus->column_fields['kp_frase_risc_dvr'] = $riga_rilevazione["frase_risc_dvr"];
                    $focus->column_fields['description'] = $riga_rilevazione["description"];

                    $focus->column_fields['kp_aggiornato'] = '1';
                    
                    $focus->mode = 'edit';
                    $focus->id = $id;
                    $focus->save('KpSitRischiDVR', $longdesc=true, $offline_update=false, $triggerEvent=false);

                }
                else{

                    $focus = CRMEntity::getInstance('KpSitRischiDVR');
                    $focus->column_fields['assigned_user_id'] =  1;

                    $focus->column_fields['kp_soggetto'] = $soggetto;
                    $focus->column_fields['kp_azienda'] = $azienda;
                    $focus->column_fields['kp_stabilimento'] = $stabilimento;
                    $focus->column_fields['kp_area_stab'] = $area;
                    $focus->column_fields['kp_rilevazione_risc'] = $dati_rilevazione["id"];
                    $focus->column_fields['kp_data_rilevazione'] = $dati_rilevazione["data_rilevazione"];
                    $focus->column_fields['kp_related_to'] = $riga_rilevazione["related_to"];
                    $focus->column_fields['kp_rischio'] = $riga_rilevazione["rischio_id"];
                    $focus->column_fields['kp_gravita_rischio'] = $riga_rilevazione["gravita_rischio"];
                    $focus->column_fields['kp_probabilita_risc'] = $riga_rilevazione["probabilita_risc"];
                    $focus->column_fields['kp_misurazione'] = $riga_rilevazione["misurazione"];
                    $focus->column_fields['kp_valutazione_risc'] = $riga_rilevazione["valutazione_risc"];
                    $focus->column_fields['kp_frase_risc_dvr'] = $riga_rilevazione["frase_risc_dvr"];
                    $focus->column_fields['description'] = $riga_rilevazione["description"];

                    $focus->column_fields['kp_aggiornato'] = '1';
                    $focus->save('KpSitRischiDVR', $longdesc=true, $offline_update=false, $triggerEvent=false);

                    $id = $focus->id;

                }

                self::clearRelatedSituazione($id, "KpRilevazRischiRig");

                self::setRelatedSituazione( $id, "KpRilevazRischiRig", array($riga_rilevazione["id"]) );

                $lista_ruoli = self::getRuoliRelazionatiRigaRilevazione( $riga_rilevazione["id"] );

                self::setRelatedSituazione( $id, "KpRuoli", $lista_ruoli );

                $gravita_rischio = $riga_rilevazione["gravita_rischio"];
                list($gravita_rischio, $gravita_rischio_desc) = explode('-', $gravita_rischio);
                $gravita_rischio = trim($gravita_rischio);
                $gravita_rischio = (int)$gravita_rischio;

                $probabilita_rischio = $riga_rilevazione["probabilita_risc"];
                list($probabilita_rischio, $probabilita_rischio_desc) = explode('-', $probabilita_rischio);
                $probabilita_rischio = trim($probabilita_rischio);
                $probabilita_rischio = (int)$probabilita_rischio;

                if( $debug ){

                    printf(" --> Gravita Iniziale %s, Probabilita Iniziale %s ", $gravita_rischio, $probabilita_rischio );

                }

                $filtro_misure = array("azienda_id" => $azienda,
                                        "stabilimento_id" => $stabilimento,
                                        "area_id" => $area,
                                        "related_to" => $riga_rilevazione["related_to"],
                                        "pericolo_id" => $riga_rilevazione["rischio_id"],
                                        "stati" => "('Adottata')",
                                        "da_data" => $dati_rilevazione["data_rilevazione"],
                                        "a_data" => "");

                $lista_misure_riduzione = self::getMisureRiduzioneRischio( $filtro_misure );

                self::clearRelatedSituazione($id, "KpMisureRiduttive");

                foreach($lista_misure_riduzione as $misura){

                    //Aggiungo la relazione tra il record di situazione e la misura

                    $gravita_rischio = $gravita_rischio - (int)$misura["riduzione_magnitudo"];

                    $probabilita_rischio = $probabilita_rischio - (int)$misura["riduzione_probabilita"];

                    $insert = "INSERT INTO {$table_prefix}_crmentityrel (crmid, module, relcrmid, relmodule)
                                VALUES
                                (".$id.", 'KpSitRischiDVR', ".$misura["id"].", 'KpMisureRiduttive')";
                    
                    $adb->query($insert);

                }

                if( $gravita_rischio <= 0 ){
                    $gravita_rischio = 1;
                }

                if( $probabilita_rischio <= 0 ){
                    $probabilita_rischio = 1;
                }

                $livello_rischio = $gravita_rischio * $probabilita_rischio;

                $gravita_rischio_pick = self::getPickingListDaValore("kp_gravita_rischio", $gravita_rischio);
                $gravita_rischio_pick = addslashes(  $gravita_rischio_pick["nome"] );

                $probabilita_rischio_pick = self::getPickingListDaValore("kp_probabilita_risc", $probabilita_rischio);
                $probabilita_rischio_pick = addslashes(  $probabilita_rischio_pick["nome"] );

                $frase_di_rischio = KpRilevazioneRischiClass::getFraseDiRischio($livello_rischio);

                $update = "UPDATE {$table_prefix}_kpsitrischidvr SET
                            kp_gravita_rischio = '".$gravita_rischio_pick."',
                            kp_probabilita_risc = '".$probabilita_rischio_pick."',
                            kp_valutazione_risc = ".$livello_rischio.",
                            kp_frase_risc_dvr = '".$frase_di_rischio."'
                            WHERE kpsitrischidvrid = ".$id;
                $adb->query($update);

                if( $debug ){

                    printf(" --> Gravita Finale %s, Probabilita Finale %s ", $gravita_rischio, $probabilita_rischio );

                }           

            }

        }
        
    }

    static function getDatiUltimaRilevazioneRischiArea($azienda, $stabilimento, $area){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $testata_ultima_ril = self::getTestataUltimaRilevazioneRischiArea( $azienda, $stabilimento, $area );

        if( $testata_ultima_ril["esiste"] ){

            $esiste = true;

            $righe_ultima_ril = self::getRigheRilevazioneRischi( $testata_ultima_ril["id"] );

        }
        else{

            $esiste = false;

            $righe_ultima_ril = array();

        }

        $result = array("esiste" => $esiste,
                        "id" => $testata_ultima_ril["id"],
                        "data_rilevazione" => $testata_ultima_ril["data_rilevazione"],
                        "righe" => $righe_ultima_ril);

        return $result;

    }

    static function getTestataUltimaRilevazioneRischiArea($azienda, $stabilimento, $area){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    ril.kprilevazionirischiid id,
                    ril.kp_data_rilevazione data_rilevazione
                    FROM {$table_prefix}_kprilevazionirischi ril
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = ril.kprilevazionirischiid
                    WHERE ent.deleted = 0 AND ril.kp_azienda = ".$azienda." AND ril.kp_stabilimento = ".$stabilimento." AND ril.kp_area_stab = ".$area."
                    ORDER BY ril.kp_data_rilevazione DESC";
     
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if($num_result > 0){

            $esiste = true;

            $id = $adb->query_result($result_query, 0, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $data_rilevazione = $adb->query_result($result_query, 0, 'data_rilevazione');
            $data_rilevazione = html_entity_decode(strip_tags($data_rilevazione), ENT_QUOTES, $default_charset);

        }
        else{

            $esiste = false;
            $id  = 0;
            $data_rilevazione  = "";

        }

        $result = array("esiste" => $esiste,
                        "id" => $id,
                        "data_rilevazione" => $data_rilevazione);

        return $result;

    }

    static function getRigheRilevazioneRischi( $rilevazione ){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    rilrig.kprilevazrischirigid id,
                    rilrig.kp_nome_riga nome,
                    rilrig.kp_rischio rischio_id,
                    rilrig.kp_gravita_rischio gravita_rischio,
                    rilrig.kp_probabilita_risc probabilita_risc,
                    rilrig.kp_related_to related_to,
                    rilrig.kp_valutazione_risc valutazione_risc,
                    rilrig.kp_frase_risc_dvr frase_risc_dvr,
                    rilrig.kp_misurazione misurazione,
                    rilrig.description description,
                    risc.kp_nome_rischio nome_rischio,
                    risc.kp_soggeto_a_misura soggeto_a_misura,
                    risc.kp_nome_misurazione nome_misurazione,
                    risc.kp_cat_pericolo cat_pericolo,
                    risc.kp_natura_pericolo natura_pericolo,
                    entrel.setype type_related_to
                    FROM {$table_prefix}_kprilevazrischirig rilrig
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rilrig.kprilevazrischirigid
                    INNER JOIN {$table_prefix}_kprischidvr risc ON risc.kprischidvrid = rilrig.kp_rischio
                    LEFT JOIN {$table_prefix}_crmentity entrel ON entrel.crmid = rilrig.kp_related_to
                    INNER JOIN {$table_prefix}_crmentity entrisc ON entrisc.crmid = risc.kprischidvrid
                    WHERE ent.deleted = 0 AND entrisc.deleted = 0 AND rilrig.kp_attivo = '1' AND rilrig.kp_rilevazione = ".$rilevazione."
                    ORDER BY rilrig.kprilevazrischirigid DESC";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $rischio_id = $adb->query_result($result_query, $i, 'rischio_id');
            $rischio_id = html_entity_decode(strip_tags($rischio_id), ENT_QUOTES, $default_charset);

            $gravita_rischio = $adb->query_result($result_query, $i, 'gravita_rischio');
            $gravita_rischio = html_entity_decode(strip_tags($gravita_rischio), ENT_QUOTES, $default_charset);
            if($gravita_rischio == null || $gravita_rischio == ""){
                $gravita_rischio = 0;
            }

            $probabilita_risc = $adb->query_result($result_query, $i, 'probabilita_risc');
            $probabilita_risc = html_entity_decode(strip_tags($probabilita_risc), ENT_QUOTES, $default_charset);
            if($probabilita_risc == null || $probabilita_risc == ""){
                $probabilita_risc = 0;
            }

            $related_to = $adb->query_result($result_query, $i, 'related_to');
            $related_to = html_entity_decode(strip_tags($related_to), ENT_QUOTES, $default_charset);
            if( $related_to == null || $related_to == "" ){
                $related_to = 0;
            }

            $valutazione_risc = $adb->query_result($result_query, $i, 'valutazione_risc');
            $valutazione_risc = html_entity_decode(strip_tags($valutazione_risc), ENT_QUOTES, $default_charset);

            $frase_risc_dvr = $adb->query_result($result_query, $i, 'frase_risc_dvr');
            $frase_risc_dvr = html_entity_decode(strip_tags($frase_risc_dvr), ENT_QUOTES, $default_charset);

            $misurazione = $adb->query_result($result_query, $i, 'misurazione');
            $misurazione = html_entity_decode(strip_tags($misurazione), ENT_QUOTES, $default_charset);

            $description = $adb->query_result($result_query, $i, 'description');
            $description = html_entity_decode(strip_tags($description), ENT_QUOTES, $default_charset);

            $nome_rischio = $adb->query_result($result_query, $i, 'nome_rischio');
            $nome_rischio = html_entity_decode(strip_tags($nome_rischio), ENT_QUOTES, $default_charset);

            $soggeto_a_misura = $adb->query_result($result_query, $i, 'soggeto_a_misura');
            $soggeto_a_misura = html_entity_decode(strip_tags($soggeto_a_misura), ENT_QUOTES, $default_charset);

            $nome_misurazione = $adb->query_result($result_query, $i, 'nome_misurazione');
            $nome_misurazione = html_entity_decode(strip_tags($nome_misurazione), ENT_QUOTES, $default_charset);

            $cat_pericolo = $adb->query_result($result_query, $i, 'cat_pericolo');
            $cat_pericolo = html_entity_decode(strip_tags($cat_pericolo), ENT_QUOTES, $default_charset);

            $natura_pericolo = $adb->query_result($result_query, $i, 'natura_pericolo');
            $natura_pericolo = html_entity_decode(strip_tags($natura_pericolo), ENT_QUOTES, $default_charset);

            $type_related_to = $adb->query_result($result_query, $i, 'type_related_to');
            $type_related_to = html_entity_decode(strip_tags($type_related_to), ENT_QUOTES, $default_charset);
            if( $type_related_to == null ){
                $type_related_to = "";
            }
            
            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "rischio_id" => $rischio_id,
                                "gravita_rischio" => $gravita_rischio,
                                "probabilita_risc" => $probabilita_risc,
                                "related_to" => $related_to,
                                "valutazione_risc" => $valutazione_risc,
                                "frase_risc_dvr" => $frase_risc_dvr,
                                "misurazione" => $misurazione,
                                "description" => $description,
                                "nome_rischio" => $nome_rischio,
                                "soggeto_a_misura" => $soggeto_a_misura,
                                "nome_misurazione" => $nome_misurazione,
                                "cat_pericolo" => $cat_pericolo,
                                "natura_pericolo" => $natura_pericolo,
                                "type_related_to" => $type_related_to);

        }

        return $result;
        
    }

    static function getMisureRiduzioneRischio( $filtro ){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    mis.kpmisureriduttiveid id,
                    mis.kp_nome_misura nome,
                    mis.kp_tipo_misura tipo_misura,
                    mis.kp_riduzione_prob riduzione_prob,
                    mis.kp_riduzione_magn riduzione_magn,
                    mis.kp_stato_misura_rid stato_misura_rid,
                    mis.kp_eseguire_entro eseguire_entro,
                    mis.kp_data_adozione data_adozione,
                    mis.description description,
                    mis.kp_related_to related_to,
                    mis.kp_azienda azienda,
                    mis.kp_stabilimento stabilimento,
                    mis.kp_area_stab area
                    FROM {$table_prefix}_kpmisureriduttive mis
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = mis.kpmisureriduttiveid
                    WHERE ent.deleted = 0";

        if( $filtro["azienda_id"] && $filtro["azienda_id"] != 0 ){
            $query .= " AND mis.kp_azienda = ".$filtro["azienda_id"];
        }

        if( $filtro["stabilimento_id"] && $filtro["stabilimento_id"] != 0 ){
            $query .= " AND mis.kp_stabilimento = ".$filtro["stabilimento_id"];
        }

        if( $filtro["area_id"] && $filtro["area_id"] != 0 ){
            $query .= " AND mis.kp_area_stab = ".$filtro["area_id"];
        }

        if( $filtro["related_to"] && $filtro["related_to"] != 0 ){
            $query .= " AND mis.kp_related_to = ".$filtro["related_to"];
        }
        elseif($filtro["related_to"]){
            $query .= " AND (mis.kp_related_to IS NULL OR mis.kp_related_to = '' OR mis.kp_related_to = 0)";
        }

        if( $filtro["pericolo_id"] && $filtro["pericolo_id"] != 0 ){
            $query .= " AND mis.kp_pericolo = ".$filtro["pericolo_id"];
        }

        if( $filtro["stati"] && $filtro["stati"] != "" ){
            $query .= " AND mis.kp_stato_misura_rid IN ".$filtro["stati"];
        }

        if( $filtro["da_data"] && $filtro["da_data"] != "" ){
            $query .= " AND mis.kp_data_adozione > '".$filtro["da_data"]."'";
        }

        $query .= " ORDER BY mis.kp_eseguire_entro ASC";

        //print($query);die;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $riduzione_prob = $adb->query_result($result_query, $i, 'riduzione_prob');
            $riduzione_prob = html_entity_decode(strip_tags($riduzione_prob), ENT_QUOTES, $default_charset);
            if($riduzione_prob == null || $riduzione_prob == ""){
                $riduzione_prob = 0;
            }

            $eseguire_entro = $adb->query_result($result_query, $i, 'eseguire_entro');
            $eseguire_entro = html_entity_decode(strip_tags($eseguire_entro), ENT_QUOTES, $default_charset);
            if($eseguire_entro == null || $eseguire_entro == "" || $eseguire_entro == "0000-00-00"){
                $eseguire_entro = "";
            }
            else{
                $eseguire_entro = new DateTime($eseguire_entro);
                $eseguire_entro = $eseguire_entro->format('d-m-Y');
            }

            $stato_misura_rid = $adb->query_result($result_query, $i, 'stato_misura_rid');
            $stato_misura_rid = html_entity_decode(strip_tags($stato_misura_rid), ENT_QUOTES, $default_charset);
            if($stato_misura_rid == null){
                $stato_misura_rid = "";
            }

            $descrizione = $adb->query_result($result_query, $i, 'description');
            $descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES, $default_charset);
            if($descrizione == null){
                $descrizione = "";
            }

            $riduzione_magn = $adb->query_result($result_query, $i, 'riduzione_magn');
            $riduzione_magn = html_entity_decode(strip_tags($riduzione_magn), ENT_QUOTES, $default_charset);
            if($riduzione_magn == null || $riduzione_magn == ""){
                $riduzione_magn = 0;
            }


            $related_to = $adb->query_result($result_query, $i, 'related_to');
            $related_to = html_entity_decode(strip_tags($related_to), ENT_QUOTES, $default_charset);
            if($related_to == null || $related_to == ""){
                $related_to = 0;
            }

            $azienda = $adb->query_result($result_query, $i, 'azienda');
            $azienda = html_entity_decode(strip_tags($azienda), ENT_QUOTES, $default_charset);
            if($azienda == null || $azienda == ""){
                $azienda = 0;
            }

            $stabilimento = $adb->query_result($result_query, $i, 'stabilimento');
            $stabilimento = html_entity_decode(strip_tags($stabilimento), ENT_QUOTES, $default_charset);
            if($stabilimento == null || $stabilimento == ""){
                $stabilimento = 0;
            }

            $area = $adb->query_result($result_query, $i, 'area');
            $area = html_entity_decode(strip_tags($area), ENT_QUOTES, $default_charset);
            if($area == null || $area == ""){
                $area = 0;
            }

            if( $filtro["ruolo_id"] && $filtro["ruolo_id"] != 0 ){

                $filtro_situazione = array("azienda_id" => $azienda,
                                            "stabilimento_id" => $stabilimento,
                                            "area_id" => $area,
                                            "related_to" => $related_to,
                                            "ruolo_id" => $filtro["ruolo_id"]);

                $riga_situazione = self::getRigaSituazioneByFiltro($filtro_situazione);

                if( !$riga_situazione["esiste"] ){
                    continue;
                }

                $lista_ruoli = self::getRuoliRelazionatiRigaSituazione( $riga_situazione["id"] );

                $check = false;

                foreach( $lista_ruoli as $ruolo_in_esame ){

                    if( $ruolo_in_esame["id"] == $filtro["ruolo_id"] ){

                        $check = true;

                    }

                }

                if( !$check ){
                    continue;
                }

            }

            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "stato_misura" => $stato_misura_rid,
                                "eseguire_entro" => $eseguire_entro,
                                "descrizione" => $descrizione,
                                "riduzione_probabilita" => $riduzione_prob,
                                "riduzione_magnitudo" => $riduzione_magn);

        }

        return $result;

    }

    static function getRuoliRelazionatiRigaRilevazione( $riga_rilevazione ){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT t.* FROM
                    ((SELECT 
                    rel.relcrmid id
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    WHERE ent.deleted = 0 AND rel.module = 'KpRilevazRischiRig' AND rel.relmodule = 'KpRuoli' AND rel.crmid = ".$riga_rilevazione.")
                    UNION
                    (SELECT 
                    rel.crmid id
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.crmid
                    WHERE ent.deleted = 0 AND rel.relmodule = 'KpRilevazRischiRig' AND rel.module = 'KpRuoli' AND rel.relcrmid = ".$riga_rilevazione.")) AS t
                    GROUP BY t.id";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $result[] = $id;

        }

        return $result;

    }

    static function clearRelatedSituazione($id, $relmodule){
        global $adb, $table_prefix, $default_charset, $current_user;

        $delete = "DELETE FROM {$table_prefix}_crmentityrel
                    WHERE 
                    (crmid = ".$id." AND module = 'KpSitRischiDVR' AND relmodule = '".$relmodule."')
                    OR
                    (relcrmid = ".$id." AND relmodule = 'KpSitRischiDVR' AND module = '".$relmodule."')";
        
        $adb->query($delete);

    }

    static function setRelatedSituazione($id, $relmodule, $relids){
        global $adb, $table_prefix, $default_charset, $current_user;

        self::clearRelatedSituazione($id, $relmodule);

        if( count($relids) > 0 ){

            foreach($relids as $relid){

                if($relid != null && $relid != "" && $relid != 0){

                    $insert = "INSERT INTO {$table_prefix}_crmentityrel (crmid, module, relcrmid, relmodule)
                                VALUES
                                (".$id.", 'KpSitRischiDVR', ".$relid.", '".$relmodule."')";
                    
                    $adb->query($insert);

                }

            }

        }

    }

    static function checkIfEsisteSituazioneRischio( $azienda, $stabilimento, $area, $related_to, $rischio ){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    sit.kpsitrischidvrid id 
                    FROM {$table_prefix}_kpsitrischidvr sit
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sit.kpsitrischidvrid
                    WHERE ent.deleted = 0 AND sit.kp_azienda = ".$azienda." AND sit.kp_stabilimento = ".$stabilimento." AND sit.kp_area_stab = ".$area." AND sit.kp_rischio = ".$rischio;

        if( $related_to != "" && $related_to != 0  && $related_to != '0' ){

            $query .= " AND sit.kp_related_to = ".$related_to;

        }
        else{

            $query .= " AND (sit.kp_related_to IS NULL OR sit.kp_related_to = '' OR sit.kp_related_to = 0)";

        }

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if($num_result > 0){

            $esiste = true;

            $id = $adb->query_result($result_query, 0, 'id');
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

    static function getPickingListDaValore($nome_picklist, $valore){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        if( $valore == '0' || $valore == '' ){
            $valore = '1';
        }

        $query = "SELECT 
                    ".$nome_picklist." nome 
                    FROM {$table_prefix}_".$nome_picklist."
                    WHERE ".$nome_picklist." LIKE '".$valore." -%'";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if($num_result > 0){

            $esiste = true;

            $nome = $adb->query_result($result_query, 0, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

        }
        else{

            $esiste = false;
            $nome = "";

        }

        $result = array("esiste" => $esiste,
                        "nome" => $nome);

        return $result;

    }

    static function getAlbero($azienda, $stabilimento, $ruolo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $lista_aziende = self::getAziendeSituazione($azienda);

        foreach( $lista_aziende as $azienda ){

            $lista_stabilimenti = self::getStabilimentiSituazione($azienda["id"], $stabilimento);

            if( count($lista_stabilimenti) > 0 ){

                $result[] = array("id" => $azienda["id"],
                                    "nome" => $azienda["nome"],
                                    "stabilimenti" => $lista_stabilimenti);

            }
            
        }

        return $result;

    }

    static function getAziendeSituazione($azienda, $filtro = array()){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    sit.kp_azienda id,
                    acc.accountname nome,
                    accbill.bill_city citta
                    FROM {$table_prefix}_kpsitrischidvr sit
                    INNER JOIN {$table_prefix}_account acc ON acc.accountid = sit.kp_azienda
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sit.kpsitrischidvrid
                    LEFT JOIN {$table_prefix}_accountbillads accbill ON accbill.accountaddressid = acc.accountid
                    WHERE ent.deleted = 0";

        if( $azienda != 0 ){
            $query .= " AND sit.kp_azienda = ".$azienda;
        }

        if( $filtro["nome_azienda"] && $filtro["nome_azienda"] != "" ){
            $query .= " AND acc.accountname LIKE '%".$filtro["nome_azienda"]."%'";
        }

        if( $filtro["citta"] && $filtro["citta"] != "" ){
            $query .= " AND accbill.bill_city LIKE '%".$filtro["citta"]."%'";
        }

        $query .= " GROUP BY acc.accountname
                    ORDER BY acc.accountname ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $citta = $adb->query_result($result_query, $i, 'citta');
            $citta = html_entity_decode(strip_tags($citta), ENT_QUOTES, $default_charset);

            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "citta" => $citta);

        }
            
        return $result;

    }

    static function getStabilimentiSituazione($azienda, $stabilimento){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    sit.kp_stabilimento id,
                    stab.nome_stabilimento nome
                    FROM {$table_prefix}_kpsitrischidvr sit
                    INNER JOIN {$table_prefix}_stabilimenti stab ON stab.stabilimentiid = sit.kp_stabilimento
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sit.kpsitrischidvrid
                    WHERE ent.deleted = 0";

        if( $azienda != 0 ){
            $query .= " AND sit.kp_azienda = ".$azienda;
        }

        if( $stabilimento != 0 ){
            $query .= " AND sit.kp_stabilimento = ".$stabilimento;
        }

        $query .= " GROUP BY stab.nome_stabilimento
                    ORDER BY stab.nome_stabilimento ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $lista_aree = self::getAreeSituazione($azienda, $id);

            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "aree" => $lista_aree);

        }
            
        return $result;

    }


    static function getAreeSituazione($azienda, $stabilimento, $area = 0, $ruolo = 0){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    sit.kp_area_stab id,
                    aree.kp_nome_area nome
                    FROM {$table_prefix}_kpsitrischidvr sit
                    INNER JOIN {$table_prefix}_kpareestabilimento aree ON aree.kpareestabilimentoid = sit.kp_area_stab
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sit.kpsitrischidvrid
                    WHERE ent.deleted = 0";

        if( $azienda != 0 ){
            $query .= " AND sit.kp_azienda = ".$azienda;
        }

        if( $stabilimento != 0 ){
            $query .= " AND sit.kp_stabilimento = ".$stabilimento;
        }

        if( $area != 0 ){
            $query .= " AND sit.kp_area_stab = ".$area;
        }

        $query .= " GROUP BY aree.kp_nome_area
                    ORDER BY aree.kp_nome_area ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            if( $ruolo != 0 && !self::esisteRigaCollegaARuolo($azienda, $stabilimento, $id, "All", $ruolo)){
                continue;
            }

            $result[] = array("id" => $id,
                                "nome" => $nome);

        }
            
        return $result;

    }

    static function getSituazione($filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "<div id='situazione_div'>";

        switch($filtro["tipo_visualizzazione"]){
            case "All":
                $result .= "<h5 style='padding-left: 10px;'>Rischi Area</h5>";
                $dati_situazione_area = self::getTemplateArea($filtro);
                $result .= $dati_situazione_area["table"];

                $result .= "<hr /><h5 style='padding-left: 10px;'>Rischi Attivit&agrave</h5>";
                $dati_situazione_attivita = self::getTemplateAttivita($filtro);
                $result .= $dati_situazione_attivita["table"];
                
                $result .= "<hr /><h5 style='padding-left: 10px;'>Rischi Impianti</h5>";
                $dati_situazione_impianti = self::getTemplateImpianti($filtro);
                $result .= $dati_situazione_impianti["table"];
                
                $result .= "<hr /><h5 style='padding-left: 10px;'>Rischi Sostanze Chimiche</h5>";
                $dati_situazione_sostanze = self::getTemplateSostanzeChimiche($filtro);
                $result .= $dati_situazione_sostanze["table"];
                
                $result .= "<hr /><h5 style='padding-left: 10px;'>Rischi Materiali di Utilizzo</h5>";
                $dati_situazione_materiali = self::getTemplateMaterialiUtilizzo($filtro);
                $result .= $dati_situazione_materiali["table"];

                break;
            case "Area":
                $result .= "<h5 style='padding-left: 10px;'>Rischi Area</h5>";
                $dati_situazione_area = self::getTemplateArea($filtro);
                $result .= $dati_situazione_area["table"];
                break;
            case "Attivita":
                $result .= "<h5 style='padding-left: 10px;'>Rischi Attivit&agrave</h5>";
                $dati_situazione_attivita = self::getTemplateAttivita($filtro);
                $result .= $dati_situazione_attivita["table"];
                break;
            case "Impianti":
                $result .= "<h5 style='padding-left: 10px;'>Rischi Impianti</h5>";
                $dati_situazione_impianti = self::getTemplateImpianti($filtro);
                $result .= $dati_situazione_impianti["table"];
                break;
            case "SostanzeChimiche":
                $result .= "<h5 style='padding-left: 10px;'>Rischi Sostanze Chimiche</h5>";
                $dati_situazione_sostanze = self::getTemplateSostanzeChimiche($filtro);
                $result .= $dati_situazione_sostanze["table"];
                break;
            case "MaterialiUtilizzo":
                $result .= "<h5 style='padding-left: 10px;'>Rischi Materiali di Utilizzo</h5>";
                $dati_situazione_materiali = self::getTemplateMaterialiUtilizzo($filtro);
                $result .= $dati_situazione_materiali["table"];
                break;
        }

        $result .= "</div>";

        return $result;

    }

    static function getTemplateArea($filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__."/../KpRilevazioniRischi/KpRilevazioneRischiClass.php");

        $records = array();

        $table = "";

        $lista_aree = self::getAreeSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $filtro["ruolo"]);

        $lista_ruoli = self::getListaRuoliAree($lista_aree, $filtro["ruolo"]);

        $table = "<table style='margin: 0px;' class='table table-striped table-hover tabella_rilevazione'>";
        $table .= "<thead>";
        $table .= "<tr>";

        $table .= "<th style='width: 150px;'>Area</th>";
        $table .= "<th>Pericoli</th>";
        $table .= "<th style='width: 140px;'>Probabilità</th>";
        $table .= "<th style='width: 140px;'>Magnitudo</th>";
        $table .= "<th style='width: 120px;'>Rischio</th>";

        foreach( $lista_ruoli as $ruolo ){
            $table .= "<th style='width: 40px; text-align:center;'><span class='vertical-rl'>".$ruolo["nome"]."</span></th>";
        }

        $table .= "</tr>";
        $table .= "</thead>";

        $table .= "<tbody>";

        $almento_una_riga = false;

        foreach( $lista_aree as $area ){

            $prima_riga = true;

            $lista_pericoli_area = self::getPericoliRilevatiArea( $filtro["azienda"], $filtro["stabilimento"], $area["id"], 0, $filtro["ruolo"] );

            foreach($lista_pericoli_area as $pericolo){

                $almento_una_riga = true;

                $table .= "<tr id='".$pericolo["id_situazione"]."_".$pericolo["id"]."' class='tr_pericolo'>";

                if($prima_riga){

                    $prima_riga = false;
                    
                    $table .= "<td rowspan='".count($lista_pericoli_area)."' style='vertical-align: top; padding-top: 15px;' ><b><span>";
                    $table .= $area["nome"];
                    $table .= "</span></b></td>";

                }

                $table .= "<td style='vertical-align: middle;'>";
                $table .= "<b><span style='vertical-align: middle;' >".$pericolo["nome"]."</span></b>";
                $table .= "</td>";
                
                $table .= "<td class='td_probabilita' style='vertical-align: middle'>".$pericolo["probabilita"]."</td>";
                $table .= "<td class='td_magnitudo' style='vertical-align: middle'>".$pericolo["magnitudo"]."</td>";
                $table .= "<td class='td_rischio' style='vertical-align: middle'>".$pericolo["rischio"]." - ".$pericolo["frase_di_rischio"]."</td>";

                $table .= self::getTabellaRuoli($lista_ruoli, $pericolo["id_situazione"]);

                $table .= "</tr>";

            }

            $records[] = array("area_id" => $area["id"],
                                "area_nome" => $area["nome"],
                                "lista_pericoli" => $lista_pericoli_area);

        }

        if( !$almento_una_riga ){
            $table .= "<tr><td colspan='1000' style='text-align: center;'><em>Nessun rischio rilevato!</em></td></tr>";
        }

        $table .= "</tbody>";

        $table .= "</table>";

        $result = array("table" => $table,
                        "records" => $records);

        return $result;

    }

    static function getTabellaRuoli($lista_ruoli, $riga_situazione){
        global $adb, $table_prefix, $default_charset, $current_user;

        $table = "";

        foreach( $lista_ruoli as $ruolo ){

            $table .= "<td style='vertical-align: middle;'>";
            $table .= "<div class='checkbox'>";
            $table .= "<label>";

            if( self::checkIfRuoloRelazionatoARiga($riga_situazione, $ruolo["id"]) ){
                $table .= "<input type='checkbox' id='pericolo_ruolo_".$riga_situazione."_".$ruolo["id"]."' checked readonly disabled >";
            }
            else{
                $table .= "<input type='checkbox' id='pericolo_ruolo_".$riga_situazione."_".$ruolo["id"]."' readonly disabled >";
            }
                
            $table .= "<b><span style='vertical-align: middle;'></span></b></label>";
            $table .= "</div>";
            $table .= "</b></td>";

        }

        return $table;

    }

    static function checkIfRuoloRelazionatoARiga($riga, $ruolo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $query = "SELECT 
                    * 
                    FROM {$table_prefix}_crmentityrel
                    WHERE (crmid = ".$riga." AND module = 'KpSitRischiDVR' AND relcrmid = ".$ruolo." AND relmodule = 'KpRuoli') OR (crmid = ".$ruolo." AND module = 'KpRuoli' AND relcrmid = ".$riga." AND relmodule = 'KpSitRischiDVR')";
       
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if($num_result > 0){

            return true;

        }
        else{
            return false;
        }

    }

    static function getPericoliRilevatiArea($azienda, $stabilimento, $area, $related_to = "All", $ruolo = 0){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT
                    sit.kp_rischio id,
                    risc.kp_nome_rischio nome,
                    sit.kpsitrischidvrid id_situazione,
                    risc.kp_nome_rischio nome,
                    sit.kp_valutazione_risc rischio,
                    sit.kp_gravita_rischio magnitudo,
                    sit.kp_probabilita_risc probabilita,
                    sit.kp_frase_risc_dvr frase_di_rischio,
                    sit.description description
                    FROM {$table_prefix}_kpsitrischidvr sit
                    INNER JOIN {$table_prefix}_kprischidvr risc ON risc.kprischidvrid = sit.kp_rischio
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sit.kpsitrischidvrid
                    WHERE ent.deleted = 0 AND sit.kp_azienda = ".$azienda." AND sit.kp_stabilimento = ".$stabilimento;

        if( $area != 0 ){
            $query .= " AND sit.kp_area_stab = ".$area;
        }

        if($related_to === "All"){
            
        }
        elseif( $related_to != 0 ){
           
            $query .= " AND sit.kp_related_to = ".$related_to;

        }
        elseif( $related_to == 0 ){

            $query .= " AND (sit.kp_related_to IS NULL OR sit.kp_related_to = '' OR sit.kp_related_to = 0)";

        }

        $query .= " GROUP BY sit.kp_rischio
                    ORDER BY risc.kp_nome_rischio ASC";

        //print($query);die;
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $id_situazione = $adb->query_result($result_query, $i, 'id_situazione');
            $id_situazione = html_entity_decode(strip_tags($id_situazione), ENT_QUOTES, $default_charset);

            $rischio = $adb->query_result($result_query, $i, 'rischio');
            $rischio = html_entity_decode(strip_tags($rischio), ENT_QUOTES, $default_charset);

            $magnitudo = $adb->query_result($result_query, $i, 'magnitudo');
            $magnitudo = html_entity_decode(strip_tags($magnitudo), ENT_QUOTES, $default_charset);

            $probabilita = $adb->query_result($result_query, $i, 'probabilita');
            $probabilita = html_entity_decode(strip_tags($probabilita), ENT_QUOTES, $default_charset);

            $frase_di_rischio = $adb->query_result($result_query, $i, 'frase_di_rischio');
            $frase_di_rischio = html_entity_decode(strip_tags($frase_di_rischio), ENT_QUOTES, $default_charset);

            $descrizione = $adb->query_result($result_query, $i, 'description');
            $descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES, $default_charset);

            if( $ruolo != 0 && !self::checkIfRuoloRelazionatoARiga($id_situazione, $ruolo) ){
                continue;
            }

            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "id_situazione" => $id_situazione,
                                "rischio" => $rischio,
                                "magnitudo" => $magnitudo,
                                "probabilita" => $probabilita,
                                "frase_di_rischio" => $frase_di_rischio,
                                "descrizione" => $descrizione);

        }
        
        return $result;

    }

    static function getListaRuoliAree($lista_aree, $filtro_ruolo = 0){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__."/../KpRilevazioniRischi/KpRilevazioneRischiClass.php");

        $array_ruoli = array();

        $lista_ruoli = array();

        foreach( $lista_aree as $area ){

            $ruoli_relazionati = KpRilevazioneRischiClass::getRuoliRelazionatiArea( $area["id"] );

            foreach( $ruoli_relazionati as $ruolo ){

                if( !in_array( $ruolo["id"], $array_ruoli ) ){

                    if( $filtro_ruolo != 0 && $ruolo["id"] != $filtro_ruolo){
                        continue;
                    }

                    $array_ruoli[] = $ruolo["id"];
                    $lista_ruoli[] = $ruolo;
                }

            }

        }

        return $lista_ruoli;

    }

    static function getTemplateAttivita($filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__."/../KpRilevazioniRischi/KpRilevazioneRischiClass.php");

        $records = array();

        $table = "";

        $lista_aree = self::getAreeSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $filtro["ruolo"]);

        $lista_ruoli = self::getListaRuoliAree($lista_aree, $filtro["ruolo"]);

        $table = "<table style='margin: 0px;' class='table table-striped table-hover tabella_rilevazione'>";
        $table .= "<thead>";
        $table .= "<tr>";

        $table .= "<th style='width: 150px;'>Area</th>";
        $table .= "<th style='width: 150px;'>Attivit&agrave</th>";
        $table .= "<th>Pericoli</th>";
        $table .= "<th style='width: 140px;'>Probabilità</th>";
        $table .= "<th style='width: 140px;'>Magnitudo</th>";
        $table .= "<th style='width: 120px;'>Rischio</th>";

        foreach( $lista_ruoli as $ruolo ){
            $table .= "<th style='width: 40px; text-align:center;'><span class='vertical-rl'>".$ruolo["nome"]."</span></th>";
        }

        $table .= "</tr>";
        $table .= "</thead>";

        $table .= "<tbody>";

        $almento_una_riga = false;

        foreach( $lista_aree as $area ){

            $prima_riga_livello1 = true;

            $lista_attivita_area = self::getAttivitaAreaSituazione($filtro["azienda"], $filtro["stabilimento"], $area["id"], $filtro["ruolo"]);

            $num_rowspan_liv1 = 0;
            foreach($lista_attivita_area as $attivita_temp){
                $num_rowspan_liv1 = $num_rowspan_liv1 + count( $attivita_temp["lista_pericoli"] );
            }

            $array_attivita = array();

            foreach($lista_attivita_area as $attivita){

                $prima_riga_livello2 = true;

                $lista_pericoli = $attivita["lista_pericoli"];

                foreach($lista_pericoli as $pericolo){

                    $almento_una_riga = true;

                    $table .= "<tr id='".$pericolo["id_situazione"]."_".$pericolo["id"]."' class='tr_pericolo'>";

                    if($prima_riga_livello1){

                        $prima_riga_livello1 = false;
                        
                        $table .= "<td rowspan='".$num_rowspan_liv1."' style='vertical-align: top; padding-top: 15px;' ><b><span>";
                        $table .= $area["nome"];
                        $table .= "</span></b></td>";
    
                    }

                    if($prima_riga_livello2){

                        $prima_riga_livello2 = false;
                        
                        $table .= "<td rowspan='".count($lista_pericoli)."' style='vertical-align: top; padding-top: 15px;' ><b><span>";
                        $table .= $attivita["nome"];
                        $table .= "</span></b></td>";
    
                    }

                    $table .= "<td style='vertical-align: middle;'>";
                    $table .= "<b><span style='vertical-align: middle;' >".$pericolo["nome"]."</span></b>";
                    $table .= "</td>";
                    
                    $table .= "<td class='td_probabilita' style='vertical-align: middle'>".$pericolo["probabilita"]."</td>";
                    $table .= "<td class='td_magnitudo' style='vertical-align: middle'>".$pericolo["magnitudo"]."</td>";
                    $table .= "<td class='td_rischio' style='vertical-align: middle'>".$pericolo["rischio"]." - ".$pericolo["frase_di_rischio"]."</td>";

                    $table .= self::getTabellaRuoli($lista_ruoli, $pericolo["id_situazione"]);

                    $table .= "</tr>";

                }

                $array_attivita[] = array("attivita_id" => $attivita["id"],
                                            "attivita_nome" => $attivita["nome"],
                                            "lista_pericoli" => $lista_pericoli);

            }

            $records[] = array("area_id" => $area["id"],
                                "area_nome" => $area["nome"],
                                "lista_attivita" => $array_attivita);

        }

        if( !$almento_una_riga ){
            $table .= "<tr><td colspan='1000' style='text-align: center;'><em>Nessun rischio rilevato!</em></td></tr>";
        }

        $table .= "</tbody>";

        $table .= "</table>";

        $result = array("table" => $table,
                        "records" => $records);

        return $result;

    }

    static function getAttivitaAreaSituazione($azienda, $stabilimento, $area = 0, $ruolo = 0, $filtro = array()){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    sit.kp_related_to id,
                    att.kp_nome_attivita nome,
                    sit.kpsitrischidvrid id_situazione,
                    att.kp_tipo_att_dvr tipo_attivita
                    FROM {$table_prefix}_kpsitrischidvr sit
                    INNER JOIN {$table_prefix}_kpattivitadvr att ON att.kpattivitadvrid = sit.kp_related_to
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sit.kpsitrischidvrid
                    INNER JOIN {$table_prefix}_crmentity entrel ON entrel.crmid = sit.kp_related_to
                    WHERE ent.deleted = 0 AND entrel.setype = 'KpAttivitaDVR'";

        if( $azienda != 0 ){
            $query .= " AND sit.kp_azienda = ".$azienda;
        }

        if( $stabilimento != 0 ){
            $query .= " AND sit.kp_stabilimento = ".$stabilimento;
        }

        if( $area != 0 && $area != "All" ){
            $query .= " AND sit.kp_area_stab = ".$area;
        }

        if( count($filtro) > 0 && $filtro["tipo_attivita"] && $filtro["tipo_attivita"] != "" ){
            $query .= " AND att.kp_tipo_att_dvr = '".$filtro["tipo_attivita"]."'";
        }

        $query .= " GROUP BY att.kp_nome_attivita
                    ORDER BY att.kp_nome_attivita ASC";

        //print_r($query);

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $id_situazione = $adb->query_result($result_query, $i, 'id_situazione');
            $id_situazione = html_entity_decode(strip_tags($id_situazione), ENT_QUOTES, $default_charset);

            if( $ruolo != 0 && !self::esisteRigaCollegaARuolo($azienda, $stabilimento, $area, $id, $ruolo) ){
                continue;
            }

            $lista_pericoli = self::getPericoliRilevatiArea( $azienda, $stabilimento, $area, $id, $ruolo);

            if( count($lista_pericoli) == 0){
                continue;
            }

            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "lista_pericoli" => $lista_pericoli);

        }
            
        return $result;

    }

    static function getTemplateImpianti($filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__."/../KpRilevazioniRischi/KpRilevazioneRischiClass.php");

        $records = array();

        $table = "";

        $lista_aree = self::getAreeSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $filtro["ruolo"]);

        $lista_ruoli = self::getListaRuoliAree($lista_aree, $filtro["ruolo"]);

        $table = "<table style='margin: 0px;' class='table table-striped table-hover tabella_rilevazione'>";
        $table .= "<thead>";
        $table .= "<tr>";

        $table .= "<th style='width: 150px;'>Area</th>";
        $table .= "<th style='width: 150px;'>Impianto</th>";
        $table .= "<th>Pericoli</th>";
        $table .= "<th style='width: 140px;'>Probabilità</th>";
        $table .= "<th style='width: 140px;'>Magnitudo</th>";
        $table .= "<th style='width: 120px;'>Rischio</th>";

        foreach( $lista_ruoli as $ruolo ){
            $table .= "<th style='width: 40px; text-align:center;'><span class='vertical-rl'>".$ruolo["nome"]."</span></th>";
        }

        $table .= "</tr>";
        $table .= "</thead>";

        $table .= "<tbody>";

        $almento_una_riga = false;

        foreach( $lista_aree as $area ){

            $prima_riga_livello1 = true;

            $lista_impianti_area = self::getImpiantiAreaSituazione($filtro["azienda"], $filtro["stabilimento"], $area["id"], $filtro["ruolo"]);

            $num_rowspan_liv1 = 0;
            foreach($lista_impianti_area as $impianto_temp){
                $num_rowspan_liv1 = $num_rowspan_liv1 + count( $impianto_temp["lista_pericoli"] );
            }

            $array_impianti = array();

            foreach($lista_impianti_area as $impianto){

                $prima_riga_livello2 = true;

                $lista_pericoli = $impianto["lista_pericoli"];

                foreach($lista_pericoli as $pericolo){

                    $almento_una_riga = true;

                    $table .= "<tr id='".$pericolo["id_situazione"]."_".$pericolo["id"]."' class='tr_pericolo'>";

                    if($prima_riga_livello1){

                        $prima_riga_livello1 = false;
                        
                        $table .= "<td rowspan='".$num_rowspan_liv1."' style='vertical-align: top; padding-top: 15px;' ><b><span>";
                        $table .= $area["nome"];
                        $table .= "</span></b></td>";
    
                    }

                    if($prima_riga_livello2){

                        $prima_riga_livello2 = false;
                        
                        $table .= "<td rowspan='".count($lista_pericoli)."' style='vertical-align: top; padding-top: 15px;' ><b><span>";
                        $table .= $impianto["nome"];
                        $table .= "</span></b></td>";
    
                    }

                    $table .= "<td style='vertical-align: middle;'>";
                    $table .= "<b><span style='vertical-align: middle;' >".$pericolo["nome"]."</span></b>";
                    $table .= "</td>";
                    
                    $table .= "<td class='td_probabilita' style='vertical-align: middle'>".$pericolo["probabilita"]."</td>";
                    $table .= "<td class='td_magnitudo' style='vertical-align: middle'>".$pericolo["magnitudo"]."</td>";
                    $table .= "<td class='td_rischio' style='vertical-align: middle'>".$pericolo["rischio"]." - ".$pericolo["frase_di_rischio"]."</td>";

                    $table .= self::getTabellaRuoli($lista_ruoli, $pericolo["id_situazione"]);

                    $table .= "</tr>";

                }

                $array_impianti[] = array("impianto_id" => $impianto["id"],
                                            "impianto_nome" => $impianto["nome"],
                                            "lista_pericoli" => $lista_pericoli);

            }

            $records[] = array("area_id" => $area["id"],
                                "area_nome" => $area["nome"],
                                "lista_impianti" => $array_impianti);

        }

        if( !$almento_una_riga ){
            $table .= "<tr><td colspan='1000' style='text-align: center;'><em>Nessun rischio rilevato!</em></td></tr>";
        }

        $table .= "</tbody>";

        $table .= "</table>";

        $result = array("table" => $table,
                        "records" => $records);

        return $result;

    }

    static function getImpiantiAreaSituazione($azienda, $stabilimento, $area = 0, $ruolo = 0){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    sit.kp_related_to id,
                    imp.kp_nome_tipologia nome
                    FROM {$table_prefix}_kpsitrischidvr sit
                    INNER JOIN {$table_prefix}_kptipologieimpianti imp ON imp.kptipologieimpiantiid = sit.kp_related_to
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sit.kpsitrischidvrid
                    INNER JOIN {$table_prefix}_crmentity entrel ON entrel.crmid = sit.kp_related_to
                    WHERE ent.deleted = 0 AND entrel.setype = 'KpTipologieImpianti'";

        if( $azienda != 0 ){
            $query .= " AND sit.kp_azienda = ".$azienda;
        }

        if( $stabilimento != 0 ){
            $query .= " AND sit.kp_stabilimento = ".$stabilimento;
        }

        if( $area != 0 ){
            $query .= " AND sit.kp_area_stab = ".$area;
        }

        $query .= " GROUP BY imp.kp_nome_tipologia
                    ORDER BY imp.kp_nome_tipologia ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            if( $ruolo != 0 && !self::esisteRigaCollegaARuolo($azienda, $stabilimento, $area, $id, $ruolo) ){
                continue;
            }

            $lista_pericoli = self::getPericoliRilevatiArea( $azienda, $stabilimento, $area, $id, $ruolo );
            
            if( count($lista_pericoli) == 0){
                continue;
            }

            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "lista_pericoli" => $lista_pericoli);

        }
            
        return $result;

    }

    static function getTemplateSostanzeChimiche($filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__."/../KpRilevazioniRischi/KpRilevazioneRischiClass.php");

        $records = array();

        $table = "";

        $lista_aree = self::getAreeSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $filtro["ruolo"]);

        $lista_ruoli = self::getListaRuoliAree($lista_aree, $filtro["ruolo"]);

        $table = "<table style='margin: 0px;' class='table table-striped table-hover tabella_rilevazione'>";
        $table .= "<thead>";
        $table .= "<tr>";

        $table .= "<th style='width: 150px;'>Area</th>";
        $table .= "<th style='width: 150px;'>Impianto</th>";
        $table .= "<th>Pericoli</th>";
        $table .= "<th style='width: 140px;'>Probabilità</th>";
        $table .= "<th style='width: 140px;'>Magnitudo</th>";
        $table .= "<th style='width: 120px;'>Rischio</th>";

        foreach( $lista_ruoli as $ruolo ){
            $table .= "<th style='width: 40px; text-align:center;'><span class='vertical-rl'>".$ruolo["nome"]."</span></th>";
        }

        $table .= "</tr>";
        $table .= "</thead>";

        $table .= "<tbody>";

        $almento_una_riga = false;

        foreach( $lista_aree as $area ){

            $prima_riga_livello1 = true;

            $lista_sostanze_area = self::getSostanzeChimicheAreaSituazione($filtro["azienda"], $filtro["stabilimento"], $area["id"], $filtro["ruolo"]);

            $num_rowspan_liv1 = 0;
            foreach($lista_sostanze_area as $sostanza_temp){
                $num_rowspan_liv1 = $num_rowspan_liv1 + count( $sostanza_temp["lista_pericoli"] );
            }

            $array_sostanze = array();

            foreach($lista_sostanze_area as $sostanza){

                $prima_riga_livello2 = true;

                $lista_pericoli = $sostanza["lista_pericoli"];

                foreach($lista_pericoli as $pericolo){

                    $almento_una_riga = true;

                    $table .= "<tr id='".$pericolo["id_situazione"]."_".$pericolo["id"]."' class='tr_pericolo'>";

                    if($prima_riga_livello1){

                        $prima_riga_livello1 = false;
                        
                        $table .= "<td rowspan='".$num_rowspan_liv1."' style='vertical-align: top; padding-top: 15px;' ><b><span>";
                        $table .= $area["nome"];
                        $table .= "</span></b></td>";
    
                    }

                    if($prima_riga_livello2){

                        $prima_riga_livello2 = false;
                        
                        $table .= "<td rowspan='".count($lista_pericoli)."' style='vertical-align: top; padding-top: 15px;' ><b><span>";
                        $table .= $sostanza["nome"];
                        $table .= "</span></b></td>";
    
                    }

                    $table .= "<td style='vertical-align: middle;'>";
                    $table .= "<b><span style='vertical-align: middle;' >".$pericolo["nome"]."</span></b>";
                    $table .= "</td>";
                    
                    $table .= "<td class='td_probabilita' style='vertical-align: middle'>".$pericolo["probabilita"]."</td>";
                    $table .= "<td class='td_magnitudo' style='vertical-align: middle'>".$pericolo["magnitudo"]."</td>";
                    $table .= "<td class='td_rischio' style='vertical-align: middle'>".$pericolo["rischio"]." - ".$pericolo["frase_di_rischio"]."</td>";

                    $table .= self::getTabellaRuoli($lista_ruoli, $pericolo["id_situazione"]);

                    $table .= "</tr>";

                }

                $array_sostanze[] = array("sostanza_id" => $sostanza["id"],
                                            "sostanza_nome" => $sostanza["nome"],
                                            "lista_pericoli" => $lista_pericoli);

            }

            $records[] = array("area_id" => $area["id"],
                                "area_nome" => $area["nome"],
                                "lista_sostanze" => $array_sostanze);

        }

        if( !$almento_una_riga ){
            $table .= "<tr><td colspan='1000' style='text-align: center;'><em>Nessun rischio rilevato!</em></td></tr>";
        }

        $table .= "</tbody>";

        $table .= "</table>";

        $result = array("table" => $table,
                        "records" => $records);

        return $result;

    }

    static function getSostanzeChimicheAreaSituazione($azienda, $stabilimento, $area = 0, $ruolo = 0){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    sit.kp_related_to id,
                    sost.kp_nome_sostanza nome
                    FROM {$table_prefix}_kpsitrischidvr sit
                    INNER JOIN {$table_prefix}_kpsostanzechimiche sost ON sost.kpsostanzechimicheid = sit.kp_related_to
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sit.kpsitrischidvrid
                    INNER JOIN {$table_prefix}_crmentity entrel ON entrel.crmid = sit.kp_related_to
                    WHERE ent.deleted = 0 AND entrel.setype = 'KpSostanzeChimiche'";

        if( $azienda != 0 ){
            $query .= " AND sit.kp_azienda = ".$azienda;
        }

        if( $stabilimento != 0 ){
            $query .= " AND sit.kp_stabilimento = ".$stabilimento;
        }

        if( $area != 0 ){
            $query .= " AND sit.kp_area_stab = ".$area;
        }

        $query .= " GROUP BY sost.kp_nome_sostanza
                    ORDER BY sost.kp_nome_sostanza ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            if( $ruolo != 0 && !self::esisteRigaCollegaARuolo($azienda, $stabilimento, $area, $id, $ruolo) ){
                continue;
            }

            $lista_pericoli = self::getPericoliRilevatiArea( $azienda, $stabilimento, $area, $id, $ruolo );

            if( count($lista_pericoli) == 0){
                continue;
            }

            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "lista_pericoli" => $lista_pericoli);

        }
            
        return $result;

    }

    static function getTemplateMaterialiUtilizzo($filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__."/../KpRilevazioniRischi/KpRilevazioneRischiClass.php");

        $records = array();

        $table = "";

        $lista_aree = self::getAreeSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $filtro["ruolo"]);

        $lista_ruoli = self::getListaRuoliAree($lista_aree, $filtro["ruolo"]);

        $table = "<table style='margin: 0px;' class='table table-striped table-hover tabella_rilevazione'>";
        $table .= "<thead>";
        $table .= "<tr>";

        $table .= "<th style='width: 150px;'>Area</th>";
        $table .= "<th style='width: 150px;'>Impianto</th>";
        $table .= "<th>Pericoli</th>";
        $table .= "<th style='width: 140px;'>Probabilità</th>";
        $table .= "<th style='width: 140px;'>Magnitudo</th>";
        $table .= "<th style='width: 120px;'>Rischio</th>";

        foreach( $lista_ruoli as $ruolo ){
            $table .= "<th style='width: 40px; text-align:center;'><span class='vertical-rl'>".$ruolo["nome"]."</span></th>";
        }

        $table .= "</tr>";
        $table .= "</thead>";

        $table .= "<tbody>";

        $almento_una_riga = false;

        foreach( $lista_aree as $area ){

            $prima_riga_livello1 = true;

            $lista_materiali_area = self::getMaterialiUtilizzoAreaSituazione($filtro["azienda"], $filtro["stabilimento"], $area["id"], $filtro["ruolo"]);

            $num_rowspan_liv1 = 0;
            foreach($lista_materiali_area as $materiale_temp){
                $num_rowspan_liv1 = $num_rowspan_liv1 + count( $materiale_temp["lista_pericoli"] );
            }

            $array_materiale = array();

            foreach($lista_materiali_area as $materiale){

                $prima_riga_livello2 = true;

                $lista_pericoli = $materiale["lista_pericoli"];

                foreach($lista_pericoli as $pericolo){

                    $almento_una_riga = true;

                    $table .= "<tr id='".$pericolo["id_situazione"]."_".$pericolo["id"]."' class='tr_pericolo'>";

                    if($prima_riga_livello1){

                        $prima_riga_livello1 = false;
                        
                        $table .= "<td rowspan='".$num_rowspan_liv1."' style='vertical-align: top; padding-top: 15px;' ><b><span>";
                        $table .= $area["nome"];
                        $table .= "</span></b></td>";
    
                    }

                    if($prima_riga_livello2){

                        $prima_riga_livello2 = false;
                        
                        $table .= "<td rowspan='".count($lista_pericoli)."' style='vertical-align: top; padding-top: 15px;' ><b><span>";
                        $table .= $materiale["nome"];
                        $table .= "</span></b></td>";
    
                    }

                    $table .= "<td style='vertical-align: middle;'>";
                    $table .= "<b><span style='vertical-align: middle;' >".$pericolo["nome"]."</span></b>";
                    $table .= "</td>";
                    
                    $table .= "<td class='td_probabilita' style='vertical-align: middle'>".$pericolo["probabilita"]."</td>";
                    $table .= "<td class='td_magnitudo' style='vertical-align: middle'>".$pericolo["magnitudo"]."</td>";
                    $table .= "<td class='td_rischio' style='vertical-align: middle'>".$pericolo["rischio"]." - ".$pericolo["frase_di_rischio"]."</td>";

                    $table .= self::getTabellaRuoli($lista_ruoli, $pericolo["id_situazione"]);

                    $table .= "</tr>";

                }

                $array_materiale[] = array("materiale_id" => $materiale["id"],
                                            "materiale_nome" => $materiale["nome"],
                                            "lista_pericoli" => $lista_pericoli); 

            }

            $records[] = array("area_id" => $area["id"],
                                "area_nome" => $area["nome"],
                                "lista_materiali" => $array_materiale);

        }

        if( !$almento_una_riga ){
            $table .= "<tr><td colspan='1000' style='text-align: center;'><em>Nessun rischio rilevato!</em></td></tr>";
        }

        $table .= "</tbody>";

        $table .= "</table>";

        $result = array("table" => $table,
                        "records" => $records);

        return $result;

    }

    static function getMaterialiUtilizzoAreaSituazione($azienda, $stabilimento, $area = 0, $ruolo = 0){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    sit.kp_related_to id,
                    mat.kp_nome_materiale nome
                    FROM {$table_prefix}_kpsitrischidvr sit
                    INNER JOIN {$table_prefix}_kpmaterialiutilizzo mat ON mat.kpmaterialiutilizzoid = sit.kp_related_to
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sit.kpsitrischidvrid
                    INNER JOIN {$table_prefix}_crmentity entrel ON entrel.crmid = sit.kp_related_to
                    WHERE ent.deleted = 0 AND entrel.setype = 'KpMaterialiUtilizzo'";

        if( $azienda != 0 ){
            $query .= " AND sit.kp_azienda = ".$azienda;
        }

        if( $stabilimento != 0 ){
            $query .= " AND sit.kp_stabilimento = ".$stabilimento;
        }

        if( $area != 0 ){
            $query .= " AND sit.kp_area_stab = ".$area;
        }

        $query .= " GROUP BY mat.kp_nome_materiale
                    ORDER BY mat.kp_nome_materiale ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            if( $ruolo != 0 && !self::esisteRigaCollegaARuolo($azienda, $stabilimento, $area, $id, $ruolo) ){
                continue;
            }

            $lista_pericoli = self::getPericoliRilevatiArea( $azienda, $stabilimento, $area, $id, $ruolo );

            if( count($lista_pericoli) == 0){
                continue;
            }

            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "lista_pericoli" => $lista_pericoli);

        }
            
        return $result;

    }

    static function getRigaSituazione($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT
                    sit.kp_azienda azienda,
                    sit.kp_stabilimento stabilimento,
                    aree.kp_nome_area nome_area,
                    sit.kp_area_stab area_stabilimento,
                    sit.kp_related_to related_to,
                    sit.kp_rischio rischio,
                    risc.kp_nome_rischio nome_rischio,
                    sit.kp_valutazione_risc valutazione_rischio,
                    sit.kp_gravita_rischio magnitudo,
                    sit.kp_probabilita_risc probabilita,
                    sit.kp_frase_risc_dvr frase_di_rischio,
                    sit.kp_misurazione misurazione,
                    risc.kp_soggeto_a_misura soggeto_a_misura,
                    risc.kp_nome_misurazione nome_misurazione,
                    sit.description description
                    FROM {$table_prefix}_kpsitrischidvr sit
                    INNER JOIN {$table_prefix}_kprischidvr risc ON risc.kprischidvrid = sit.kp_rischio
                    INNER JOIN {$table_prefix}_kpareestabilimento aree ON aree.kpareestabilimentoid = sit.kp_area_stab
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sit.kpsitrischidvrid
                    WHERE ent.deleted = 0 AND sit.kpsitrischidvrid = ".$id;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $azienda = $adb->query_result($result_query, 0, 'azienda');
            $azienda = html_entity_decode(strip_tags($azienda), ENT_QUOTES, $default_charset);

            $stabilimento = $adb->query_result($result_query, 0, 'stabilimento');
            $stabilimento = html_entity_decode(strip_tags($stabilimento), ENT_QUOTES, $default_charset);

            $area_stabilimento = $adb->query_result($result_query, 0, 'area_stabilimento');
            $area_stabilimento = html_entity_decode(strip_tags($area_stabilimento), ENT_QUOTES, $default_charset);

            $rischio = $adb->query_result($result_query, 0, 'rischio');
            $rischio = html_entity_decode(strip_tags($rischio), ENT_QUOTES, $default_charset);

            $nome_area = $adb->query_result($result_query, 0, 'nome_area');
            $nome_area = html_entity_decode(strip_tags($nome_area), ENT_QUOTES, $default_charset);

            $nome_rischio = $adb->query_result($result_query, 0, 'nome_rischio');
            $nome_rischio = html_entity_decode(strip_tags($nome_rischio), ENT_QUOTES, $default_charset);

            $valutazione_rischio = $adb->query_result($result_query, 0, 'valutazione_rischio');
            $valutazione_rischio = html_entity_decode(strip_tags($valutazione_rischio), ENT_QUOTES, $default_charset);

            $magnitudo = $adb->query_result($result_query, 0, 'magnitudo');
            $magnitudo = html_entity_decode(strip_tags($magnitudo), ENT_QUOTES, $default_charset);

            $probabilita = $adb->query_result($result_query, 0, 'probabilita');
            $probabilita = html_entity_decode(strip_tags($probabilita), ENT_QUOTES, $default_charset);

            $frase_di_rischio = $adb->query_result($result_query, 0, 'frase_di_rischio');
            $frase_di_rischio = html_entity_decode(strip_tags($frase_di_rischio), ENT_QUOTES, $default_charset);

            $misurazione = $adb->query_result($result_query, 0, 'misurazione');
            $misurazione = html_entity_decode(strip_tags($misurazione), ENT_QUOTES, $default_charset);

            $soggeto_a_misura = $adb->query_result($result_query, 0, 'soggeto_a_misura');
            $soggeto_a_misura = html_entity_decode(strip_tags($soggeto_a_misura), ENT_QUOTES, $default_charset);

            $nome_misurazione = $adb->query_result($result_query, 0, 'nome_misurazione');
            $nome_misurazione = html_entity_decode(strip_tags($nome_misurazione), ENT_QUOTES, $default_charset);

            $descrizione = $adb->query_result($result_query, 0, 'description');
            $descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES, $default_charset);

            $related_to = $adb->query_result($result_query, 0, 'related_to');
            $related_to = html_entity_decode(strip_tags($related_to), ENT_QUOTES, $default_charset);
            if( $related_to != '' && $related_to != 0 ){

                $dati_related_to = self::getDatiRelatedTo($related_to);

                $nome_related_to = $dati_related_to["nome"];
                $type_related_to = $dati_related_to["type"];

            }
            else{
                $nome_related_to = "";
                $type_related_to = "";
            }

        }
        else{

            $nome_area = "";
            $nome_rischio = "";
            $valutazione_rischio = "";
            $magnitudo = "";
            $probabilita = "";
            $frase_di_rischio = "";
            $misurazione = "";
            $soggeto_a_misura = "0";
            $nome_misurazione = "";
            $nome_related_to = "";
            $type_related_to = "";
            $descrizione = "";

        }

        $lista_ruoli = self::getRuoliRelazionatiRigaSituazione( $id );

        $lista_misure = self::getMisureRiduttivePericolo( $azienda, $stabilimento, $area_stabilimento, $related_to, $rischio );

        $result = array("nome_area" => $nome_area,
                        "nome_rischio" => $nome_rischio,
                        "valutazione_rischio" => $valutazione_rischio,
                        "magnitudo" => $magnitudo,
                        "probabilita" => $probabilita,
                        "frase_di_rischio" => $frase_di_rischio,
                        "misurazione" => $misurazione,
                        "soggeto_a_misura" => $soggeto_a_misura,
                        "nome_misurazione" => $nome_misurazione,
                        "nome_related_to" => $nome_related_to,
                        "type_related_to" => $type_related_to,
                        "descrizione" => $descrizione,
                        "lista_ruoli" => $lista_ruoli,
                        "lista_misure" => $lista_misure);

        return $result;

    }

    static function getDatiRelatedTo($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    setype
                    FROM vte_crmentity
                    WHERE crmid = ".$id;
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $setype = $adb->query_result($result_query, 0, 'setype');
            $setype = html_entity_decode(strip_tags($setype), ENT_QUOTES, $default_charset);

            switch ($setype) {
                case "KpTipologieImpianti":
                    $query = "SELECT 
                                kp_nome_tipologia nome
                                FROM {$table_prefix}_kptipologieimpianti
                                WHERE kptipologieimpiantiid = ".$id;
                    break;
                case "KpAttivitaDVR":
                    $query = "SELECT 
                                kp_nome_attivita nome
                                FROM {$table_prefix}_kpattivitadvr
                                WHERE kpattivitadvrid = ".$id;
                    break;
                case "KpMaterialiUtilizzo":
                    $query = "SELECT 
                                kp_nome_materiale nome
                                FROM {$table_prefix}_kpmaterialiutilizzo
                                WHERE kpmaterialiutilizzoid = ".$id;
                    break;
                case "KpSostanzeChimiche":
                    $query = "SELECT 
                                kp_nome_sostanza nome
                                FROM {$table_prefix}_kpsostanzechimiche
                                WHERE kpsostanzechimicheid = ".$id;
                    break;
                default:
                    return array("type" => "",
                                "nome" => "");
            }

            $result_query = $adb->query($query);
            $num_result = $adb->num_rows($result_query);

            if( $num_result > 0 ){

                $nome = $adb->query_result($result_query, 0, 'nome');
                $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            }
            else{
                $nome = "";
            }

        }

        $result = array("type" => $setype,
                        "nome" => $nome);

        return $result;

    }

    static function getRuoliRelazionatiRigaSituazione( $riga_situazione ){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT t.* FROM
                    ((SELECT 
                    rel.relcrmid id,
                    ruol.kp_nome_ruolo nome,
                    ruol.kp_mansione mansione_collegata,
                    mans.mansione_name nome_mansione_collegata
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kpruoli ruol ON ruol.kpruoliid = rel.relcrmid
                    LEFT JOIN {$table_prefix}_mansioni mans ON mans.mansioniid = ruol.kp_mansione
                    WHERE ent.deleted = 0 AND rel.module = 'KpSitRischiDVR' AND rel.relmodule = 'KpRuoli' AND rel.crmid = ".$riga_situazione.")
                    UNION
                    (SELECT 
                    rel.crmid id,
                    ruol.kp_nome_ruolo nome,
                    ruol.kp_mansione mansione_collegata,
                    mans.mansione_name nome_mansione_collegata
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.crmid
                    INNER JOIN {$table_prefix}_kpruoli ruol ON ruol.kpruoliid = rel.crmid
                    LEFT JOIN {$table_prefix}_mansioni mans ON mans.mansioniid = ruol.kp_mansione
                    WHERE ent.deleted = 0 AND rel.relmodule = 'KpSitRischiDVR' AND rel.module = 'KpRuoli' AND rel.relcrmid = ".$riga_situazione.")) AS t
                    GROUP BY t.id";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $mansione_collegata = $adb->query_result($result_query, $i, 'mansione_collegata');
            $mansione_collegata = html_entity_decode(strip_tags($mansione_collegata), ENT_QUOTES, $default_charset);
            if( $mansione_collegata == null || $mansione_collegata == '' ){
                $mansione_collegata = 0;
            }

            $nome_mansione_collegata = $adb->query_result($result_query, $i, 'nome_mansione_collegata');
            $nome_mansione_collegata = html_entity_decode(strip_tags($nome_mansione_collegata), ENT_QUOTES, $default_charset);
            if( $nome_mansione_collegata == null ){
                $nome_mansione_collegata = '';
            }
            
            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "mansione_collegata" => $mansione_collegata,
                                "nome_mansione_collegata" => $nome_mansione_collegata);

        }

        return $result;

    }

    static function getMisureRiduttivePericolo( $azienda, $stabilimento, $area_stabilimento, $related_to, $rischio ){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__."/../KpRilevazioniRischi/KpRilevazioneRischiClass.php");

        $result = array();

        $typeRelatedTo = KpRilevazioneRischiClass::getTypeRelatedTo( related_to );

        if( $typeRelatedTo == "KpAreeStabilimento" ){
            $related_to = 0;
        }

        $query = "SELECT 
                    mis.kpmisureriduttiveid id,
                    mis.kp_nome_misura nome,
                    mis.kp_tipo_misura tipo_misura,
                    mis.kp_eseguire_entro eseguire_entro,
                    mis.kp_stato_misura_rid stato_misura,
                    mis.kp_data_adozione data_adozione
                    FROM {$table_prefix}_kpmisureriduttive mis
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = mis.kpmisureriduttiveid
                    WHERE ent.deleted = 0 AND mis.kp_pericolo = ".$rischio." AND mis.kp_azienda = ".$azienda." AND mis.kp_stabilimento = ".$stabilimento." AND mis.kp_area_stab = ".$area_stabilimento;

        if($related_to != 0){
            $query .= " AND mis.kp_related_to = ".$related_to;
        }
        else{
            $query .= " AND (mis.kp_related_to = 0 OR mis.kp_related_to = '')";
        }

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $tipo_misura = $adb->query_result($result_query, $i, 'tipo_misura');
            $tipo_misura = html_entity_decode(strip_tags($tipo_misura), ENT_QUOTES, $default_charset);

            $eseguire_entro = $adb->query_result($result_query, $i, 'eseguire_entro');
            $eseguire_entro = html_entity_decode(strip_tags($eseguire_entro), ENT_QUOTES, $default_charset);
            if( $eseguire_entro != "" && $eseguire_entro != "0000-00-00"){
                $eseguire_entro_temp = new DateTime($eseguire_entro);
                $eseguire_entro = $eseguire_entro_temp->format('d/m/Y');
            }
            else{
                $eseguire_entro = "";
            }

            $data_adozione = $adb->query_result($result_query, $i, 'data_adozione');
            $data_adozione = html_entity_decode(strip_tags($data_adozione), ENT_QUOTES, $default_charset);
            if( $data_adozione != "" && $data_adozione != "0000-00-00"){
                $data_adozione_temp = new DateTime($data_adozione);
                $data_adozione = $data_adozione_temp->format('d/m/Y');
            }
            else{
                $data_adozione = "";
            }

            $stato_misura = $adb->query_result($result_query, $i, 'stato_misura');
            $stato_misura = html_entity_decode(strip_tags($stato_misura), ENT_QUOTES, $default_charset);
            
            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "tipo_misura" => $tipo_misura,
                                "eseguire_entro" => $eseguire_entro,
                                "data_adozione" => $data_adozione,
                                "stato_misura" => $stato_misura);

        }

        return $result;

    }

    static function getRuoli($azienda, $stabilimento, $area, $nome_ruolo, $nome_mansione){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $array_ruoli = array();

        $righe_situazione = self::getRigheSituazione($azienda, $stabilimento, $area, "All");
        
        foreach( $righe_situazione as $riga ){
            
            $ruoli_relazionati = self::getRuoliRelazionatiRigaSituazione( $riga );

            foreach( $ruoli_relazionati as $ruolo ){

                $check = true;

                if( $check && trim($nome_ruolo) != "" && strpos( $ruolo["nome"], trim($nome_ruolo) ) === FALSE ){
                    $check = false;
                    continue;
                }

                if( $check && trim($nome_mansione) != "" && strpos( $ruolo["nome_mansione_collegata"], trim($nome_mansione) ) === FALSE ){
                    $check = false;
                    continue;
                }

                if( $check && in_array($ruolo["id"], $array_ruoli) ){
                    $check = false;
                    continue;
                }

                if( $check ){

                    $array_ruoli[] = $ruolo["id"];
                    $result[] = $ruolo;

                }

            }

        }

        return $result;

    }

    static function getRigheSituazione($azienda, $stabilimento = 0, $area = 0, $related_to = "All"){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    sit.kpsitrischidvrid id
                    FROM {$table_prefix}_kpsitrischidvr sit
                    INNER JOIN {$table_prefix}_kpareestabilimento aree ON aree.kpareestabilimentoid = sit.kp_area_stab
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sit.kpsitrischidvrid
                    WHERE ent.deleted = 0";

        if( $azienda != 0 ){
            $query .= " AND sit.kp_azienda = ".$azienda;
        }

        if( $stabilimento != 0 ){
            $query .= " AND sit.kp_stabilimento = ".$stabilimento;
        }

        if( $area != 0 ){
            $query .= " AND sit.kp_area_stab = ".$area;
        }

        if( $related_to == 0 ){
            $query .= " AND (sit.kp_related_to IS NULL OR sit.kp_related_to = '' OR sit.kp_related_to = 0)";
        }
        elseif( $related_to != 0 && $related_to != "All" ){
            $query .= " AND sit.kp_related_to = ".$related_to;
        }

        $query .= " GROUP BY sit.kpsitrischidvrid";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $result[] = $id;

        }
            
        return $result;

    }

    static function esisteRigaCollegaARuolo($azienda, $stabilimento, $area, $related_to, $ruolo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = false;

        $righe_situazione = self::getRigheSituazione($azienda, $stabilimento, $area, $related_to);
        
        foreach( $righe_situazione as $riga ){

            if( self::checkIfRuoloRelazionatoARiga($riga, $ruolo) ){

                $result = true;
                return $result;

            }

        }

        return $result;

    }

    static function getExcel($filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__."/../../../include/PHPExcel/PHPExcel.php");

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()->setCreator("Kpro Consulting")
            ->setLastModifiedBy("Kpro Consulting")
            ->setTitle("Excel Situazione Rischi ".$data_corrente_inv)
            ->setSubject("Excel Situazione Rischi ".$data_corrente_inv)
            ->setDescription("Excel Situazione Rischi ".$data_corrente_inv." for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Excel Situazione Rischi ".$data_corrente_inv);

        $numero_foglio = 0;
        
        self::getTemplateAreaExcel($filtro, $objPHPExcel, $numero_foglio);
       
        $numero_foglio++;

        self::getTemplateAttivitaExcel($filtro, $objPHPExcel, $numero_foglio);
        
        $numero_foglio++;

        self::getTemplateImpiantiExcel($filtro, $objPHPExcel, $numero_foglio);

        $numero_foglio++;

        self::getTemplateSostanzeChimicheExcel($filtro, $objPHPExcel, $numero_foglio);

        $numero_foglio++;

        self::getTemplateMaterialiUtilizzoExcel($filtro, $objPHPExcel, $numero_foglio);

        $name = date("YmdHis")."_Situazione_Rischi";

        $excel_type = 'Excel5';
        $excel_ext = 'xls';
        $app_type = 'application/vnd.ms-excel';
        
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, $excel_type);
        
        $objWriter->save('cache/'.$name.'.'.$excel_ext);
        
        if(file_exists('cache/'.$name.'.'.$excel_ext)){

            @ob_clean();
            header("Content-Type: {$app_type}");
            header("Content-length: ".filesize("./cache/$name.$excel_ext"));
            header("Cache-Control: private");
            header("Content-Disposition: attachment; filename=$name.$excel_ext");
            header("Content-Description: PHP Generated Data");
            echo fread(fopen("./cache/$name.$excel_ext", "r"),filesize("./cache/$name.$excel_ext"));
                        
            @unlink("cache/$name.$excel_ext");
        }
        else{
            printf("Errore nella generazione dell'Excel!");
        }

    }

    static function setStyleExcel(&$objPHPExcel, $column, $row, $numero_foglio){
        global $adb, $table_prefix, $default_charset, $current_user;

        $objPHPExcel->setActiveSheetIndex($numero_foglio);

        $objPHPExcel->getActiveSheet()->getStyle("A1:".$column."1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A2:A".$row)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("B2:B".$row)->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->getStyle("F1:".$column."1")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle("A2:".$column.$row)->getAlignment()->setWrapText(true);

        $style_alignment_horizontal_center = array(
                'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle("F1:".$column.$row)->applyFromArray($style_alignment_horizontal_center);

        $style_alignment_vertical_top = array(
                'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle("A2:A".$row)->applyFromArray($style_alignment_vertical_top);

        $objPHPExcel->getActiveSheet()->getStyle("F1:".$column."1")->getAlignment()->setTextRotation(-90);

        foreach(range('C','E') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension("F:".$column)->setWidth(10);

        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(150);

        $objPHPExcel->getActiveSheet()
            ->getPageSetup()
            ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        return $objPHPExcel;
    }

    static function setStyleExcelRag(&$objPHPExcel, $column, $row, $numero_foglio){
        global $adb, $table_prefix, $default_charset, $current_user;

        $objPHPExcel->setActiveSheetIndex($numero_foglio);

        $objPHPExcel->getActiveSheet()->getStyle("A1:".$column."1")->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("A2:A".$row)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("B2:B".$row)->getFont()->setBold(true);
        $objPHPExcel->getActiveSheet()->getStyle("C2:C".$row)->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->getStyle("G1:".$column."1")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle("A2:".$column.$row)->getAlignment()->setWrapText(true);

        $style_alignment_horizontal_center = array(
                'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle("G1:".$column.$row)->applyFromArray($style_alignment_horizontal_center);

        $style_alignment_vertical_top = array(
                'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle("A2:A".$row)->applyFromArray($style_alignment_vertical_top);
        $objPHPExcel->getActiveSheet()->getStyle("B2:B".$row)->applyFromArray($style_alignment_vertical_top);

        $objPHPExcel->getActiveSheet()->getStyle("G1:".$column."1")->getAlignment()->setTextRotation(-90);

        foreach(range('D','F') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(40);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(60);
        $objPHPExcel->getActiveSheet()->getColumnDimension("G:".$column)->setWidth(10);

        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(150);

        $objPHPExcel->getActiveSheet()
            ->getPageSetup()
            ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        return $objPHPExcel;
    }

    static function getTemplateAreaExcel($filtro, &$objPHPExcel, $numero_foglio){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__."/../KpRilevazioniRischi/KpRilevazioneRischiClass.php");

        $dati_situazione_aree = self::getTemplateArea($filtro);
        $dati_situazione_aree = $dati_situazione_aree["records"];

        if($numero_foglio > 0){
            $objPHPExcel->createSheet($numero_foglio);
        }

        $objPHPExcel->setActiveSheetIndex($numero_foglio);

        $objPHPExcel->getActiveSheet()->setTitle("Rischi Area");

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'Area')
                ->setCellValue('B1', 'Pericoli')
                ->setCellValue('C1', 'Probabilità')
                ->setCellValue('D1', 'Magnitudo')
                ->setCellValue('E1', 'Rischio');

        $column = 'F';

        $lista_aree = self::getAreeSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $filtro["ruolo"]);

        $lista_ruoli = self::getListaRuoliAree($lista_aree, $filtro["ruolo"]);

        foreach( $lista_ruoli as $ruolo ){
            $objPHPExcel->getActiveSheet()->setCellValue($column.'1', $ruolo["nome"]);
            $column++;
        }

        $row = 2;

        foreach( $dati_situazione_aree as $dati_situazione_area ){

            $prima_riga = true;

            foreach( $dati_situazione_area["lista_pericoli"] as $pericolo ){

                $column = 'A';

                if($prima_riga){

                    $prima_riga = false;

                    $objPHPExcel->getActiveSheet()->mergeCells($column.$row.':'.$column.(count($dati_situazione_area["lista_pericoli"]) + $row - 1));

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_situazione_area["area_nome"]);

                }

                $column++;

                $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["nome"]);

                $column++;

                $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["probabilita"]);

                $column++;

                $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["magnitudo"]);

                $column++;

                $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["rischio"]." - ".$pericolo["frase_di_rischio"]);

                $column++;

                self::getTabellaRuoliExcel($objPHPExcel, $column, $row, $lista_ruoli, $pericolo["id_situazione"]);

                $row++;

            }

        }

        self::setStyleExcel($objPHPExcel, $column, $row, $numero_foglio);

    }

    static function getTabellaRuoliExcel(&$objPHPExcel, &$column, &$row, $lista_ruoli, $riga_situazione){
        global $adb, $table_prefix, $default_charset, $current_user;
        
        foreach( $lista_ruoli as $ruolo ){

            if( self::checkIfRuoloRelazionatoARiga($riga_situazione, $ruolo["id"]) ){

                $objPHPExcel->getActiveSheet()->setCellValue($column.$row, 'X');

            }

            $column++;
        }

        return $objPHPExcel;

    }

    static function getTemplateAttivitaExcel($filtro, $objPHPExcel, $numero_foglio){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__."/../KpRilevazioniRischi/KpRilevazioneRischiClass.php");

        $dati_situazione_attivita = self::getTemplateAttivita($filtro);
        $dati_situazione_attivita = $dati_situazione_attivita["records"];

        if($numero_foglio > 0){
            $objPHPExcel->createSheet($numero_foglio);
        }

        $objPHPExcel->setActiveSheetIndex($numero_foglio);

        $objPHPExcel->getActiveSheet()->setTitle("Rischi Attività");

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'Area')
                ->setCellValue('B1', 'Attività')
                ->setCellValue('C1', 'Pericoli')
                ->setCellValue('D1', 'Probabilità')
                ->setCellValue('E1', 'Magnitudo')
                ->setCellValue('F1', 'Rischio');

        $column = 'G';

        $lista_aree = self::getAreeSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $filtro["ruolo"]);

        $lista_ruoli = self::getListaRuoliAree($lista_aree, $filtro["ruolo"]);

        foreach( $lista_ruoli as $ruolo ){
            $objPHPExcel->getActiveSheet()->setCellValue($column.'1', $ruolo["nome"]);
            $column++;
        }

        $row = 2;

        foreach( $dati_situazione_attivita as $dati_situazione ){

            $prima_riga_livello1 = true;

            $lista_attivita_area = $dati_situazione["lista_attivita"];

            $num_rowspan_liv1 = 0;
            foreach($lista_attivita_area as $attivita_temp){
                $num_rowspan_liv1 = $num_rowspan_liv1 + count( $attivita_temp["lista_pericoli"] );
            }

            foreach( $lista_attivita_area as $attivita ){

                $prima_riga_livello2 = true;

                foreach( $attivita["lista_pericoli"] as $pericolo ){

                    $column = 'A';

                    if($prima_riga_livello1){

                        $prima_riga_livello1 = false;

                        $objPHPExcel->getActiveSheet()->mergeCells($column.$row.':'.$column.($num_rowspan_liv1 + $row - 1));

                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_situazione["area_nome"]);

                    }

                    $column++;

                    if($prima_riga_livello2){

                        $prima_riga_livello2 = false;

                        $objPHPExcel->getActiveSheet()->mergeCells($column.$row.':'.$column.(count($attivita["lista_pericoli"]) + $row - 1));

                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $attivita["attivita_nome"]);

                    }

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["nome"]);

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["probabilita"]);

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["magnitudo"]);

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["rischio"]." - ".$pericolo["frase_di_rischio"]);

                    $column++;

                    self::getTabellaRuoliExcel($objPHPExcel, $column, $row, $lista_ruoli, $pericolo["id_situazione"]);

                    $row++;

                }

            }

        }

        self::setStyleExcelRag($objPHPExcel, $column, $row, $numero_foglio);

    }

    static function getTemplateImpiantiExcel($filtro, $objPHPExcel, $numero_foglio){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__."/../KpRilevazioniRischi/KpRilevazioneRischiClass.php");

        $dati_situazione_impianti = self::getTemplateImpianti($filtro);
        $dati_situazione_impianti = $dati_situazione_impianti["records"];

        if($numero_foglio > 0){
            $objPHPExcel->createSheet($numero_foglio);
        }

        $objPHPExcel->setActiveSheetIndex($numero_foglio);

        $objPHPExcel->getActiveSheet()->setTitle("Rischi Impianti");

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'Area')
                ->setCellValue('B1', 'Impianti')
                ->setCellValue('C1', 'Pericoli')
                ->setCellValue('D1', 'Probabilità')
                ->setCellValue('E1', 'Magnitudo')
                ->setCellValue('F1', 'Rischio');

        $column = 'G';

        $lista_aree = self::getAreeSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $filtro["ruolo"]);

        $lista_ruoli = self::getListaRuoliAree($lista_aree, $filtro["ruolo"]);

        foreach( $lista_ruoli as $ruolo ){
            $objPHPExcel->getActiveSheet()->setCellValue($column.'1', $ruolo["nome"]);
            $column++;
        }

        $row = 2;

        foreach( $dati_situazione_impianti as $dati_situazione ){

            $prima_riga_livello1 = true;

            $lista_impianti_area = $dati_situazione["lista_impianti"];

            $num_rowspan_liv1 = 0;
            foreach($lista_impianti_area as $impianto_temp){
                $num_rowspan_liv1 = $num_rowspan_liv1 + count( $impianto_temp["lista_pericoli"] );
            }

            foreach( $lista_impianti_area as $impianto ){

                $prima_riga_livello2 = true;

                foreach( $impianto["lista_pericoli"] as $pericolo ){

                    $column = 'A';

                    if($prima_riga_livello1){

                        $prima_riga_livello1 = false;

                        $objPHPExcel->getActiveSheet()->mergeCells($column.$row.':'.$column.($num_rowspan_liv1 + $row - 1));

                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_situazione["area_nome"]);

                    }

                    $column++;

                    if($prima_riga_livello2){

                        $prima_riga_livello2 = false;

                        $objPHPExcel->getActiveSheet()->mergeCells($column.$row.':'.$column.(count($impianto["lista_pericoli"]) + $row - 1));

                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $impianto["impianto_nome"]);

                    }

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["nome"]);

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["probabilita"]);

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["magnitudo"]);

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["rischio"]." - ".$pericolo["frase_di_rischio"]);

                    $column++;

                    self::getTabellaRuoliExcel($objPHPExcel, $column, $row, $lista_ruoli, $pericolo["id_situazione"]);

                    $row++;

                }

            }

        }

        self::setStyleExcelRag($objPHPExcel, $column, $row, $numero_foglio);

    }

    static function getTemplateSostanzeChimicheExcel($filtro, $objPHPExcel, $numero_foglio){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__."/../KpRilevazioniRischi/KpRilevazioneRischiClass.php");

        $dati_situazione_sostanze = self::getTemplateSostanzeChimiche($filtro);
        $dati_situazione_sostanze = $dati_situazione_sostanze["records"];

        if($numero_foglio > 0){
            $objPHPExcel->createSheet($numero_foglio);
        }

        $objPHPExcel->setActiveSheetIndex($numero_foglio);

        $objPHPExcel->getActiveSheet()->setTitle("Rischi Sostanze Chimiche");

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'Area')
                ->setCellValue('B1', 'Sostanza Chimica')
                ->setCellValue('C1', 'Pericoli')
                ->setCellValue('D1', 'Probabilità')
                ->setCellValue('E1', 'Magnitudo')
                ->setCellValue('F1', 'Rischio');

        $column = 'G';

        $lista_aree = self::getAreeSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $filtro["ruolo"]);

        $lista_ruoli = self::getListaRuoliAree($lista_aree, $filtro["ruolo"]);

        foreach( $lista_ruoli as $ruolo ){
            $objPHPExcel->getActiveSheet()->setCellValue($column.'1', $ruolo["nome"]);
            $column++;
        }

        $row = 2;

        foreach( $dati_situazione_sostanze as $dati_situazione ){

            $prima_riga_livello1 = true;

            $lista_sostanze_area = $dati_situazione["lista_sostanze"];

            $num_rowspan_liv1 = 0;
            foreach($lista_sostanze_area as $sostanza_temp){
                $num_rowspan_liv1 = $num_rowspan_liv1 + count( $sostanza_temp["lista_pericoli"] );
            }

            foreach( $lista_sostanze_area as $sostanza ){

                $prima_riga_livello2 = true;

                foreach( $sostanza["lista_pericoli"] as $pericolo ){

                    $column = 'A';

                    if($prima_riga_livello1){

                        $prima_riga_livello1 = false;

                        $objPHPExcel->getActiveSheet()->mergeCells($column.$row.':'.$column.($num_rowspan_liv1 + $row - 1));

                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_situazione["area_nome"]);

                    }

                    $column++;

                    if($prima_riga_livello2){

                        $prima_riga_livello2 = false;

                        $objPHPExcel->getActiveSheet()->mergeCells($column.$row.':'.$column.(count($sostanza["lista_pericoli"]) + $row - 1));

                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $sostanza["sostanza_nome"]);

                    }

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["nome"]);

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["probabilita"]);

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["magnitudo"]);

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["rischio"]." - ".$pericolo["frase_di_rischio"]);

                    $column++;

                    self::getTabellaRuoliExcel($objPHPExcel, $column, $row, $lista_ruoli, $pericolo["id_situazione"]);

                    $row++;

                }

            }

        }

        self::setStyleExcelRag($objPHPExcel, $column, $row, $numero_foglio);

    }

    static function getTemplateMaterialiUtilizzoExcel($filtro, $objPHPExcel, $numero_foglio){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__."/../KpRilevazioniRischi/KpRilevazioneRischiClass.php");

        $dati_situazione_materiale = self::getTemplateMaterialiUtilizzo($filtro);
        $dati_situazione_materiale = $dati_situazione_materiale["records"];

        if($numero_foglio > 0){
            $objPHPExcel->createSheet($numero_foglio);
        }

        $objPHPExcel->setActiveSheetIndex($numero_foglio);

        $objPHPExcel->getActiveSheet()->setTitle("Rischi Materiali di Utilizzo");

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'Area')
                ->setCellValue('B1', 'Materiale di Utilizzo')
                ->setCellValue('C1', 'Pericoli')
                ->setCellValue('D1', 'Probabilità')
                ->setCellValue('E1', 'Magnitudo')
                ->setCellValue('F1', 'Rischio');

        $column = 'G';

        $lista_aree = self::getAreeSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $filtro["ruolo"]);

        $lista_ruoli = self::getListaRuoliAree($lista_aree, $filtro["ruolo"]);

        foreach( $lista_ruoli as $ruolo ){
            $objPHPExcel->getActiveSheet()->setCellValue($column.'1', $ruolo["nome"]);
            $column++;
        }

        $row = 2;

        foreach( $dati_situazione_materiale as $dati_situazione ){

            $prima_riga_livello1 = true;

            $lista_materiali_area = $dati_situazione["lista_materiali"];

            $num_rowspan_liv1 = 0;
            foreach($lista_materiali_area as $materiale_temp){
                $num_rowspan_liv1 = $num_rowspan_liv1 + count( $materiale_temp["lista_pericoli"] );
            }

            foreach( $lista_materiali_area as $materiale ){

                $prima_riga_livello2 = true;

                foreach( $materiale["lista_pericoli"] as $pericolo ){

                    $column = 'A';

                    if($prima_riga_livello1){

                        $prima_riga_livello1 = false;

                        $objPHPExcel->getActiveSheet()->mergeCells($column.$row.':'.$column.($num_rowspan_liv1 + $row - 1));

                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_situazione["area_nome"]);

                    }

                    $column++;

                    if($prima_riga_livello2){

                        $prima_riga_livello2 = false;

                        $objPHPExcel->getActiveSheet()->mergeCells($column.$row.':'.$column.(count($materiale["lista_pericoli"]) + $row - 1));

                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $materiale["materiale_nome"]);

                    }

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["nome"]);

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["probabilita"]);

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["magnitudo"]);

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["rischio"]." - ".$pericolo["frase_di_rischio"]);

                    $column++;

                    self::getTabellaRuoliExcel($objPHPExcel, $column, $row, $lista_ruoli, $pericolo["id_situazione"]);

                    $row++;

                }

            }

        }

        self::setStyleExcelRag($objPHPExcel, $column, $row, $numero_foglio);

    }

    static function getPDF($filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $lista_aree = self::getAreeSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $filtro["ruolo"]);
        
        $lista_ruoli = self::getListaRuoliAree($lista_aree, $filtro["ruolo"]);

        $lista_pdf = array();

        $path_temp = date("YmdHis")."_".rand(0 , 100000);
        
        foreach( $lista_ruoli as $ruolo ){

            $lista_pdf[] = self::getPDFRuolo( $ruolo["id"], $filtro, $path_temp );

        }

        $focus_azienda = CRMEntity::getInstance('Accounts');
        $focus_azienda->retrieve_entity_info($filtro["azienda"], "Accounts", $dieOnError=false); 

        $accountname = $focus_azienda->column_fields["accountname"];
        $accountname = html_entity_decode(strip_tags($accountname), ENT_QUOTES, $default_charset);

        $nome_zip = "DVR_".self::setNomeFile($accountname).".zip";

        self::zipPath($nome_zip, "cache/".$path_temp);

        $file_zip = "cache/".$path_temp."/".$nome_zip;

        if (file_exists($file_zip)) {

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file_zip).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file_zip));
            readfile($file_zip);

            @unlink($file_zip);

        }

        foreach( $lista_pdf as $pdf_file ){

            if (file_exists($pdf_file)) {
                @unlink($pdf_file);
            }

        }

        @rmdir("cache/".$path_temp."/");
                    
    }

    static function getPDFRuolo($ruolo, $filtro, $path){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__."/../../../modules/PDFMaker/InventoryPDF.php");
        require_once(__DIR__."/../../../include/mpdf/mpdf.php"); 
        require_once(__DIR__."/../../../modules/SproCore/SproUtils/spro_utils.php");

        $dati_ruolo = self::getDatiRuolo($ruolo);

        $tabella_ruolo = self::getTemplateRuoloPDF($ruolo, $filtro);
        
        $tabella_area = self::getTemplateAreaRuoloPDF($ruolo, $filtro);
        
        $tabella_attivita = self::getTemplateAttivitaRuoloPDF($ruolo, $filtro);

        $tabella_impianti = self::getTemplateImpiantiRuoloPDF($ruolo, $filtro);
        
        $tabella_sostanze_chimiche = self::getTemplateSostanzeChimicheRuoloPDF($ruolo, $filtro);

        $tabella_materiali_utilizzo = self::getTemplateMaterialiUtilizzoRuoloPDF($ruolo, $filtro);

        $tabella_misure_riduttive = self::getTemplateMisureRiduttiveRuoloPDF($ruolo, $filtro);
        
        $nome_file = "DVR_".self::setNomeFile($dati_ruolo["nome"]);

        $id_statici = getConfigurazioniIdStatici();
        $id_statico = $id_statici["PDF Maker - Template DVR"];
        if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
            die;
        }
        else{
            $templateid = $id_statico["valore"];
        }

        $lista_pericoli_area = self::getPericoliRilevatiArea( $filtro["azienda"], $filtro["stabilimento"], 0, "All", $ruolo );

        if( count($lista_pericoli_area) > 0 ){
            $record = $lista_pericoli_area[0]["id_situazione"];
        }
        
        $relmodule = "KpSitRischiDVR";
        $language = "it_it";
        $record = $record;

        $focus = CRMEntity::getInstance($relmodule);
        $focus->retrieve_entity_info($record, $relmodule);
        $focus->id = $record;

        $PDFContents = array();
        $TemplateContent = array();

        $PDFContent = PDFContent::getInstance($templateid, $relmodule, $focus, $language); 
        $pdf_content = $PDFContent->getContent();    

        $header_html = $pdf_content["header"];
        $body_html = $pdf_content["body"];
        $footer_html = $pdf_content["footer"];

        $header_html = str_replace("#NOME_RUOLO#", strtoupper($dati_ruolo["nome"]), $header_html);
        $body_html = str_replace("#NOME_RUOLO#", strtoupper($dati_ruolo["nome"]), $body_html);

        $body_html = str_replace("#TABELLA_DATI_RUOLO#", $tabella_ruolo, $body_html);
        $body_html = str_replace("#TABELLA_RISCHI_AREA#", $tabella_area, $body_html);
        $body_html = str_replace("#TABELLA_RISCHI_ATTIVITA#", $tabella_attivita, $body_html);
        $body_html = str_replace("#TABELLA_RISCHI_IMPIANTI#", $tabella_impianti, $body_html);
        $body_html = str_replace("#TABELLA_RISCHI_SOSTANZE_CHIMICHE#", $tabella_sostanze_chimiche, $body_html);
        $body_html = str_replace("#TABELLA_RISCHI_MATERIALI#", $tabella_materiali_utilizzo, $body_html);
        $body_html = str_replace("#TABELLA_MISURE_RIDUTTIVE#", $tabella_misure_riduttive, $body_html);
       
        $Settings = $PDFContent->getSettings();
        if($name==""){    
            $name = $PDFContent->getFilename();
        }
                    
        if ($Settings["orientation"] == "landscape"){
            $format = $Settings["format"]."-L";
        }
        else{
            $format = $Settings["format"];
        }

        $ListViewBlocks = array();
        if(strpos($body_html,"#LISTVIEWBLOCK_START#") !== false && strpos($body_html,"#LISTVIEWBLOCK_END#") !== false){
            preg_match_all("|#LISTVIEWBLOCK_START#(.*)#LISTVIEWBLOCK_END#|sU", $body_html, $ListViewBlocks, PREG_PATTERN_ORDER);
        }		
        
        if (count($ListViewBlocks) > 0){
                        
            $TemplateContent[$templateid] = $pdf_content;
            $TemplateSettings[$templateid] = $Settings;
                        
            $num_listview_blocks = count($ListViewBlocks[0]);
            for($i=0; $i<$num_listview_blocks; $i++){
                $ListViewBlock[$templateid][$i] = $ListViewBlocks[0][$i];
                $ListViewBlockContent[$templateid][$i][$record][] = $ListViewBlocks[1][$i];
            }   
        }
        else{
            if (!isset($mpdf)){           
                $mpdf=new mPDF('',$format,'','Arial',$Settings["margin_left"],$Settings["margin_right"],0,0,$Settings["margin_top"],$Settings["margin_bottom"]);  
                $mpdf->SetAutoFont();
                @$mpdf->SetHTMLHeader($header_html);
            }
            else{
                @$mpdf->SetHTMLHeader($header_html);
                @$mpdf->WriteHTML('<pagebreak sheet-size="'.$format.'" margin-left="'.$Settings["margin_left"].'mm" margin-right="'.$Settings["margin_right"].'mm" margin-top="0mm" margin-bottom="0mm" margin-header="'.$Settings["margin_top"].'mm" margin-footer="'.$Settings["margin_bottom"].'mm" />');
            }     
            @$mpdf->SetHTMLFooter($footer_html);
            @$mpdf->WriteHTML($body_html);
        }
                
        if (count($TemplateContent)> 0){
            
            foreach($TemplateContent AS $templateid => $TContent){
                $header_html = $TContent["header"];
                $body_html = $TContent["body"];
                $footer_html = $TContent["footer"];
                    
                $Settings = $TemplateSettings[$templateid];
                    
                foreach($ListViewBlock[$templateid] AS $id => $text){
                    $replace = "";
                    foreach($Records as $record){  
                        $replace .= implode("",$ListViewBlockContent[$templateid][$id][$record]);   
                    }
                        
                    $body_html = str_replace($text,$replace,$body_html);
                }
                    
                if ($Settings["orientation"] == "landscape"){
                    $format = $Settings["format"]."-L";
                }
                else{
                    $format = $Settings["format"];
                }
                    
                    
                if (!isset($mpdf)){           
                    $mpdf=new mPDF('',$format,'','Arial',$Settings["margin_left"],$Settings["margin_right"],0,0,$Settings["margin_top"],$Settings["margin_bottom"]);  
                    $mpdf->SetAutoFont();
                    @$mpdf->SetHTMLHeader($header_html);
                }
                else{
                    @$mpdf->SetHTMLHeader($header_html);
                    @$mpdf->WriteHTML('<pagebreak sheet-size="'.$format.'" margin-left="'.$Settings["margin_left"].'mm" margin-right="'.$Settings["margin_right"].'mm" margin-top="0mm" margin-bottom="0mm" margin-header="'.$Settings["margin_top"].'mm" margin-footer="'.$Settings["margin_bottom"].'mm" />');
                }     
                @$mpdf->SetHTMLFooter($footer_html);
                @$mpdf->WriteHTML($body_html);
            }
        }

        //return $mpdf->Output('', 'S');
        //return $body_html;

        $target_dir = 'cache/'.$path.'/';

        if(!($dir = @opendir($target_dir))) {
            Mkdir($target_dir, 0777);
        }
        
        //Questa parte servirebbe se volessi salvare il documento sul mio computer
        $mpdf->Output($target_dir.$nome_file.'.pdf');

        /*@ob_clean();
        header('Content-Type: application/pdf');
        header("Content-length: ".filesize(".$target_dir.$nome_file.pdf"));
        header("Cache-Control: private");
        header("Content-Disposition: attachment; filename=$nome_file.pdf");
        header("Content-Description: PHP Generated Data");
        echo fread(fopen("./cache/$nome_file.pdf", "r"),filesize(".$target_dir.$nome_file.pdf"));*/
                    
        //@unlink("$target_dir.$nome_file.pdf");

        return $target_dir.$nome_file.'.pdf';

    }

    static function getTemplateAreaRuoloPDF($ruolo, $filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $table = "";

        $lista_aree = self::getAreeSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $ruolo);

        $table = "<table border='1' cellpadding='3' cellspacing='0' style='width:100%; border-collapse:collapse; font-size: 12px;'>";
        $table .= "<thead>";
        $table .= "<tr style='background-color: #DBEDFF;' >";

        $table .= "<th colspan='2' >FATTORI DI RISCHIO</th>";
        $table .= "<th>SITUAZIONE RISCONTRATA</th>";
        $table .= "<th colspan='2'>ENTITA' DEL RISCHIO</th>";

        $table .= "</tr>";
        $table .= "</thead>";

        $table .= "<tbody>";
        
        $almento_una_riga = false;

        foreach( $lista_aree as $area ){

            $prima_riga = true;

            $lista_pericoli_area = self::getPericoliRilevatiArea( $filtro["azienda"], $filtro["stabilimento"], $area["id"], 0, $ruolo );
            
            foreach($lista_pericoli_area as $pericolo){

                $almento_una_riga = true;

                $table .= "<tr>";

                if($prima_riga){

                    $prima_riga = false;

                    $table .= "<td rowspan='".count($lista_pericoli_area)."' style='width: 200px; background-color: #DBEDFF; vertical-align: top; padding-top: 15px;' ><b><span>";
                    $table .= $area["nome"];
                    $table .= "</span></b></td>";

                }

                $table .= "<td style='vertical-align: middle; width: 300px;'>";
                $table .= "<b><span style='vertical-align: middle;' >".$pericolo["nome"]."</span></b>";
                $table .= "</td>";

                $table .= "<td style='vertical-align: top;'>";
                $table .= "<span style='vertical-align: top;' >".$pericolo["descrizione"]."</span>";
                $table .= "</td>";
                
                $table .= "<td style='vertical-align: middle; text-align: center; width: 70px;'>";
                $table .= "<b><span style='vertical-align: middle;' >".$pericolo["rischio"]."</span></b>";

                $table .= "<br />";
                
                $magnitudo = $pericolo["magnitudo"];
                list($magnitudo_val, $magnitudo_desc) = explode(" - ", $magnitudo); 
                
                $table .= "<span style='vertical-align: middle; font-size: 10px;' >D=".$magnitudo_val."</span>";

                $table .= "<br />";

                $probabilita = $pericolo["probabilita"];
                list($probabilita_val, $probabilita_desc) = explode(" - ", $probabilita); 
                
                $table .= "<span style='vertical-align: middle; font-size: 10px;' >P=".$probabilita_val."</span>";

                $table .= "</td>";

                $table .= "<td style='vertical-align: middle; text-align: center; width: 100px;'>";
                $table .= "<b><span style='vertical-align: middle; color: red;' >".$pericolo["frase_di_rischio"]."</span></b>";
                $table .= "</td>";

                $table .= "</tr>";

            }

        }
  
        $table .= "</tbody>";

        $table .= "</table>";

        if( !$almento_una_riga ){
            $table = "";
        }
        
        return $table;

    }

    static function getTemplateAttivitaRuoloPDF($ruolo, $filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $table = "";

        $lista_aree = self::getAreeSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $ruolo);

        $table = "<table border='1' cellpadding='3' cellspacing='0' style='width:100%; border-collapse:collapse; font-size: 12px;'>";
        $table .= "<thead>";
        $table .= "<tr style='background-color: #DBEDFF;' >";

        $table .= "<th colspan='3'>FATTORI DI RISCHIO</th>";
        $table .= "<th>SITUAZIONE RISCONTRATA</th>";
        $table .= "<th colspan='2'>ENTITA' DEL RISCHIO</th>";

        $table .= "</tr>";
        $table .= "</thead>";

        $table .= "<tbody>";

        $almento_una_riga = false;

        foreach( $lista_aree as $area ){

            $prima_riga_livello1 = true;

            $lista_attivita_area = self::getAttivitaAreaSituazione($filtro["azienda"], $filtro["stabilimento"], $area["id"], $ruolo);

            $num_rowspan_liv1 = 0;
            foreach($lista_attivita_area as $attivita_temp){
                $num_rowspan_liv1 = $num_rowspan_liv1 + count( $attivita_temp["lista_pericoli"] );
            }

            foreach($lista_attivita_area as $attivita){

                $prima_riga_livello2 = true;

                $lista_pericoli = $attivita["lista_pericoli"];

                foreach($lista_pericoli as $pericolo){

                    $almento_una_riga = true;

                    $table .= "<tr>";

                    if($prima_riga_livello1){

                        $prima_riga_livello1 = false;
                        
                        $table .= "<td rowspan='".$num_rowspan_liv1."' style='width: 125px; background-color: #DBEDFF; vertical-align: top; padding-top: 15px;' ><b><span>";
                        $table .= $area["nome"];
                        $table .= "</span></b></td>";
    
                    }

                    if($prima_riga_livello2){

                        $prima_riga_livello2 = false;
                        
                        $table .= "<td rowspan='".count($lista_pericoli)."' style='width: 125px; background-color: #DBEDFF; vertical-align: top; padding-top: 15px;' ><b><span>";
                        $table .= $attivita["nome"];
                        $table .= "</span></b></td>";
    
                    }

                    $table .= "<td style='vertical-align: middle; width: 300px;'>";
                    $table .= "<b><span style='vertical-align: middle;' >".$pericolo["nome"]."</span></b>";
                    $table .= "</td>";

                    $table .= "<td style='vertical-align: top;'>";
                    $table .= "<span style='vertical-align: top;' >".$pericolo["descrizione"]."</span>";
                    $table .= "</td>";

                    $table .= "<td style='vertical-align: middle; text-align: center; width: 70px;'>";
                    $table .= "<b><span style='vertical-align: middle;' >".$pericolo["rischio"]."</span></b>";

                    $table .= "<br />";

                    $magnitudo = $pericolo["magnitudo"];
                    list($magnitudo_val, $magnitudo_desc) = explode(" - ", $magnitudo); 

                    $table .= "<span style='vertical-align: middle; font-size: 10px;' >D=".$magnitudo_val."</span>";

                    $table .= "<br />";

                    $probabilita = $pericolo["probabilita"];
                    list($probabilita_val, $probabilita_desc) = explode(" - ", $probabilita); 

                    $table .= "<span style='vertical-align: middle; font-size: 10px;' >P=".$probabilita_val."</span>";

                    $table .= "</td>";

                    $table .= "<td style='vertical-align: middle; text-align: center; width: 100px;'>";
                    $table .= "<b><span style='vertical-align: middle; color: red;' >".$pericolo["frase_di_rischio"]."</span></b>";
                    $table .= "</td>";

                    $table .= "</tr>";


                }


            }

        }
  
        $table .= "</tbody>";

        $table .= "</table>";

        if( !$almento_una_riga ){
            $table = "";
        }

        return $table;

    }

    static function getTemplateImpiantiRuoloPDF($ruolo, $filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $table = "";

        $lista_aree = self::getAreeSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $ruolo);

        $table = "<table border='1' cellpadding='3' cellspacing='0' style='width:100%; border-collapse:collapse; font-size: 12px;'>";
        $table .= "<thead>";
        $table .= "<tr style='background-color: #DBEDFF;' >";

        $table .= "<th colspan='3'>FATTORI DI RISCHIO</th>";
        $table .= "<th>SITUAZIONE RISCONTRATA</th>";
        $table .= "<th colspan='2'>ENTITA' DEL RISCHIO</th>";

        $table .= "</tr>";
        $table .= "</thead>";

        $table .= "<tbody>";

        $almento_una_riga = false;

        foreach( $lista_aree as $area ){

            $prima_riga_livello1 = true;

            $lista_impianti_area = self::getImpiantiAreaSituazione($filtro["azienda"], $filtro["stabilimento"], $area["id"], $ruolo);

            $num_rowspan_liv1 = 0;
            foreach($lista_impianti_area as $impianto_temp){
                $num_rowspan_liv1 = $num_rowspan_liv1 + count( $impianto_temp["lista_pericoli"] );
            }

            foreach($lista_impianti_area as $impianto){

                $prima_riga_livello2 = true;

                $lista_pericoli = $impianto["lista_pericoli"];

                foreach($lista_pericoli as $pericolo){

                    $almento_una_riga = true;

                    $table .= "<tr>";

                    if($prima_riga_livello1){

                        $prima_riga_livello1 = false;
                        
                        $table .= "<td rowspan='".$num_rowspan_liv1."' style='width: 125px; background-color: #DBEDFF; vertical-align: top; padding-top: 15px;' ><b><span>";
                        $table .= $area["nome"];
                        $table .= "</span></b></td>";
    
                    }

                    if($prima_riga_livello2){

                        $prima_riga_livello2 = false;
                        
                        $table .= "<td rowspan='".count($lista_pericoli)."' style='width: 125px; background-color: #DBEDFF; vertical-align: top; padding-top: 15px;' ><b><span>";
                        $table .= $impianto["nome"];
                        $table .= "</span></b></td>";
    
                    }

                    $table .= "<td style='vertical-align: middle; width: 300px;'>";
                    $table .= "<b><span style='vertical-align: middle;' >".$pericolo["nome"]."</span></b>";
                    $table .= "</td>";

                    $table .= "<td style='vertical-align: top;'>";
                    $table .= "<span style='vertical-align: top;' >".$pericolo["descrizione"]."</span>";
                    $table .= "</td>";

                    $table .= "<td style='vertical-align: middle; text-align: center; width: 70px;'>";
                    $table .= "<b><span style='vertical-align: middle;' >".$pericolo["rischio"]."</span></b>";

                    $table .= "<br />";

                    $magnitudo = $pericolo["magnitudo"];
                    list($magnitudo_val, $magnitudo_desc) = explode(" - ", $magnitudo); 

                    $table .= "<span style='vertical-align: middle; font-size: 10px;' >D=".$magnitudo_val."</span>";

                    $table .= "<br />";

                    $probabilita = $pericolo["probabilita"];
                    list($probabilita_val, $probabilita_desc) = explode(" - ", $probabilita); 

                    $table .= "<span style='vertical-align: middle; font-size: 10px;' >P=".$probabilita_val."</span>";

                    $table .= "</td>";

                    $table .= "<td style='vertical-align: middle; text-align: center; width: 100px;'>";
                    $table .= "<b><span style='vertical-align: middle; color: red;' >".$pericolo["frase_di_rischio"]."</span></b>";
                    $table .= "</td>";

                    $table .= "</tr>";


                }


            }

        }
  
        $table .= "</tbody>";

        $table .= "</table>";

        if( !$almento_una_riga ){
            $table = "";
        }

        return $table;

    }

    static function getTemplateSostanzeChimicheRuoloPDF($ruolo, $filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $table = "";

        $lista_aree = self::getAreeSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $ruolo);

        $table = "<table border='1' cellpadding='3' cellspacing='0' style='width:100%; border-collapse:collapse; font-size: 12px;'>";
        $table .= "<thead>";
        $table .= "<tr style='background-color: #DBEDFF;' >";

        $table .= "<th colspan='3'>FATTORI DI RISCHIO</th>";
        $table .= "<th>SITUAZIONE RISCONTRATA</th>";
        $table .= "<th colspan='2'>ENTITA' DEL RISCHIO</th>";

        $table .= "</tr>";
        $table .= "</thead>";

        $table .= "<tbody>";

        $almento_una_riga = false;

        foreach( $lista_aree as $area ){

            $prima_riga_livello1 = true;

            $lista_sostanze_area = self::getSostanzeChimicheAreaSituazione($filtro["azienda"], $filtro["stabilimento"], $area["id"], $ruolo);

            $num_rowspan_liv1 = 0;
            foreach($lista_sostanze_area as $sostanza_temp){
                $num_rowspan_liv1 = $num_rowspan_liv1 + count( $sostanza_temp["lista_pericoli"] );
            }

            foreach($lista_sostanze_area as $sostanza){

                $prima_riga_livello2 = true;

                $lista_pericoli = $sostanza["lista_pericoli"];

                foreach($lista_pericoli as $pericolo){

                    $almento_una_riga = true;

                    $table .= "<tr>";

                    if($prima_riga_livello1){

                        $prima_riga_livello1 = false;
                        
                        $table .= "<td rowspan='".$num_rowspan_liv1."' style='width: 125px; background-color: #DBEDFF; vertical-align: top; padding-top: 15px;' ><b><span>";
                        $table .= $area["nome"];
                        $table .= "</span></b></td>";
    
                    }

                    if($prima_riga_livello2){

                        $prima_riga_livello2 = false;
                        
                        $table .= "<td rowspan='".count($lista_pericoli)."' style='width: 125px; background-color: #DBEDFF; vertical-align: top; padding-top: 15px;' ><b><span>";
                        $table .= $sostanza["nome"];
                        $table .= "</span></b></td>";
    
                    }

                    $table .= "<td style='vertical-align: middle; width: 300px;'>";
                    $table .= "<b><span style='vertical-align: middle;' >".$pericolo["nome"]."</span></b>";
                    $table .= "</td>";

                    $table .= "<td style='vertical-align: top;'>";
                    $table .= "<span style='vertical-align: top;' >".$pericolo["descrizione"]."</span>";
                    $table .= "</td>";

                    $table .= "<td style='vertical-align: middle; text-align: center; width: 70px;'>";
                    $table .= "<b><span style='vertical-align: middle;' >".$pericolo["rischio"]."</span></b>";

                    $table .= "<br />";

                    $magnitudo = $pericolo["magnitudo"];
                    list($magnitudo_val, $magnitudo_desc) = explode(" - ", $magnitudo); 

                    $table .= "<span style='vertical-align: middle; font-size: 10px;' >D=".$magnitudo_val."</span>";

                    $table .= "<br />";

                    $probabilita = $pericolo["probabilita"];
                    list($probabilita_val, $probabilita_desc) = explode(" - ", $probabilita); 

                    $table .= "<span style='vertical-align: middle; font-size: 10px;' >P=".$probabilita_val."</span>";

                    $table .= "</td>";

                    $table .= "<td style='vertical-align: middle; text-align: center; width: 100px;'>";
                    $table .= "<b><span style='vertical-align: middle; color: red;' >".$pericolo["frase_di_rischio"]."</span></b>";
                    $table .= "</td>";

                    $table .= "</tr>";


                }


            }

        }
  
        $table .= "</tbody>";

        $table .= "</table>";

        if( !$almento_una_riga ){
            $table = "";
        }

        return $table;

    }

    static function getTemplateMaterialiUtilizzoRuoloPDF($ruolo, $filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $table = "";

        $lista_aree = self::getAreeSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $ruolo);

        $table = "<table border='1' cellpadding='3' cellspacing='0' style='width:100%; border-collapse:collapse; font-size: 12px;'>";
        $table .= "<thead>";
        $table .= "<tr style='background-color: #DBEDFF;' >";

        $table .= "<th colspan='3'>FATTORI DI RISCHIO</th>";
        $table .= "<th>SITUAZIONE RISCONTRATA</th>";
        $table .= "<th colspan='2'>ENTITA' DEL RISCHIO</th>";

        $table .= "</tr>";
        $table .= "</thead>";

        $table .= "<tbody>";

        $almento_una_riga = false;

        foreach( $lista_aree as $area ){

            $prima_riga_livello1 = true;

            $lista_materiali_area = self::getMaterialiUtilizzoAreaSituazione($filtro["azienda"], $filtro["stabilimento"], $area["id"], $ruolo);

            $num_rowspan_liv1 = 0;
            foreach($lista_materiali_area as $materiale_temp){
                $num_rowspan_liv1 = $num_rowspan_liv1 + count( $materiale_temp["lista_pericoli"] );
            }

            foreach($lista_materiali_area as $materiale){

                $prima_riga_livello2 = true;

                $lista_pericoli = $materiale["lista_pericoli"];

                foreach($lista_pericoli as $pericolo){

                    $almento_una_riga = true;

                    $table .= "<tr>";

                    if($prima_riga_livello1){

                        $prima_riga_livello1 = false;
                        
                        $table .= "<td rowspan='".$num_rowspan_liv1."' style='width: 125px; background-color: #DBEDFF; vertical-align: top; padding-top: 15px;' ><b><span>";
                        $table .= $area["nome"];
                        $table .= "</span></b></td>";
    
                    }

                    if($prima_riga_livello2){

                        $prima_riga_livello2 = false;
                        
                        $table .= "<td rowspan='".count($lista_pericoli)."' style='width: 125px; background-color: #DBEDFF; vertical-align: top; padding-top: 15px;' ><b><span>";
                        $table .= $materiale["nome"];
                        $table .= "</span></b></td>";
    
                    }

                    $table .= "<td style='vertical-align: middle; width: 300px;'>";
                    $table .= "<b><span style='vertical-align: middle;' >".$pericolo["nome"]."</span></b>";
                    $table .= "</td>";

                    $table .= "<td style='vertical-align: top;'>";
                    $table .= "<span style='vertical-align: top;' >".$pericolo["descrizione"]."</span>";
                    $table .= "</td>";

                    $table .= "<td style='vertical-align: middle; text-align: center; width: 70px;'>";
                    $table .= "<b><span style='vertical-align: middle;' >".$pericolo["rischio"]."</span></b>";

                    $table .= "<br />";

                    $magnitudo = $pericolo["magnitudo"];
                    list($magnitudo_val, $magnitudo_desc) = explode(" - ", $magnitudo); 

                    $table .= "<span style='vertical-align: middle; font-size: 10px;' >D=".$magnitudo_val."</span>";

                    $table .= "<br />";

                    $probabilita = $pericolo["probabilita"];
                    list($probabilita_val, $probabilita_desc) = explode(" - ", $probabilita); 

                    $table .= "<span style='vertical-align: middle; font-size: 10px;' >P=".$probabilita_val."</span>";

                    $table .= "</td>";

                    $table .= "<td style='vertical-align: middle; text-align: center; width: 100px;'>";
                    $table .= "<b><span style='vertical-align: middle; color: red;' >".$pericolo["frase_di_rischio"]."</span></b>";
                    $table .= "</td>";

                    $table .= "</tr>";


                }

            }

        }
  
        $table .= "</tbody>";

        $table .= "</table>";

        if( !$almento_una_riga ){
            $table = "";
        }

        return $table;

    }

    static function getDatiRuolo($ruolo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $focus_ruolo = CRMEntity::getInstance('KpRuoli');
        $focus_ruolo->retrieve_entity_info($ruolo, "KpRuoli", $dieOnError=false); 

        $nome = $focus_ruolo->column_fields["kp_nome_ruolo"];
        $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

        $descrizione = $focus_ruolo->column_fields["description"];
        $descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES, $default_charset);

        $result = array("nome" => $nome,
                        "descrizione" => $descrizione);

        return $result;

    }

    static function setNomeFile ($str = ''){
        global $adb, $table_prefix, $default_charset, $current_user;

        $str = strip_tags($str); 
        $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
        $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
        //$str = strtolower($str);
        $str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
        $str = htmlentities($str, ENT_QUOTES, "utf-8");
        $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
        $str = str_replace(' ', '_', $str);
        $str = rawurlencode($str);
        $str = str_replace('%', '_', $str);

        return $str;

    }

    static function zipPath($nome_zip, $path){
        global $adb, $table_prefix, $default_charset, $current_user, $root_directory;

        $path_completa = $root_directory."/".$path."/";

        $command= "python3 ".__DIR__."/kpZipArchive.py ".$nome_zip." ".$path;  
        exec($command, $out, $status);

    }

    static function getTemplateRuoloPDF($ruolo, $filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $table = "<table border='1' cellpadding='3' cellspacing='0' style='width:100%; border-collapse:collapse; font-size: 12px;'>";
        $table .= "<thead>";
        $table .= "<tr style='background-color: #DBEDFF;' >";

        $table .= "<th>REPARTO</th>";
        $table .= "<th>DESCRIZIONE MANSIONE</th>";
        $table .= "<th><b>MACCHINE UTILIZZATE</th>";
        $table .= "<th>SOSTANZE UTILIZZATE</th>";

        $table .= "</tr>";
        $table .= "</thead>";

        $table .= "<tbody>";

        $table .= "</tbody>";

        $table .= "<tr style='vertical-align: top;'>";

        $table .= "<td style='vertical-align: top; width: 25%;'>";
        $lista_aree = self::getAreeSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $ruolo);
        foreach($lista_aree as $area){
            $table .= "<span>- ".$area["nome"]."</span><br />";
        }
        $table .= "</td>";

        $table .= "<td style='vertical-align: top; width: 25%;'>";
        $lista_attivita_ordinarie = self::getAttivitaAreaSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $ruolo, array("tipo_attivita" => "Ordinaria"));
        if( count($lista_attivita_ordinarie) > 0 ){
            $table .= "<span><b>OPERAZIONI ROUTINARIE:</b></span><br />";
            foreach($lista_attivita_ordinarie as $attivita_ordinaria){
                $table .= "<span>- ".$attivita_ordinaria["nome"]."</span><br />";
            }
        }
        $lista_attivita_non_ordinarie = self::getAttivitaAreaSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $ruolo, array("tipo_attivita" => "Non Ordinaria"));
        if( count($lista_attivita_non_ordinarie) > 0 ){
            $table .= "<hr /><span><b>OPERAZIONI NON ROUTINARIE:</b></span><br />";
            foreach($lista_attivita_non_ordinarie as $attivita_non_ordinaria){
                $table .= "<span>- ".$attivita_non_ordinaria["nome"]."</span><br />";
            }
        }
        $table .= "</td>";

        $table .= "<td style='vertical-align: top; width: 25%;'>";
        $lista_impianti = self::getImpiantiAreaSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $ruolo);
        foreach($lista_impianti as $impianto){
            $table .= "<span>- ".$impianto["nome"]."</span><br />";
        }
        $table .= "</td>";

        $table .= "<td style='vertical-align: top; width: 25%;'>";
        $lista_sostanze = self::getSostanzeChimicheAreaSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $ruolo);
        foreach($lista_sostanze as $sostanza){
            $table .= "<span>- ".$sostanza["nome"]."</span><br />";
        }
        $table .= "</td>";

        $table .= "</tr>";

        $table .= "</table>";

        //print($table);die;

        return $table;

    }

    static function getTemplateMisureRiduttiveRuoloPDF($ruolo, $filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $table = "";

        $lista_aree = self::getAreeSituazione($filtro["azienda"], $filtro["stabilimento"], $filtro["area"], $ruolo);

        $table = "<table border='1' cellpadding='3' cellspacing='0' style='width:100%; border-collapse:collapse; font-size: 12px;'>";
        $table .= "<thead>";
        $table .= "<tr style='background-color: #DBEDFF;' >";

        $table .= "<th>ATTIVITA' DI MIGLIORAMENTO</th>";
        $table .= "<th>ENTRO</th>";
        $table .= "<th>STATO</th>";

        $table .= "</tr>";
        $table .= "</thead>";

        $table .= "<tbody>";

        $filtro = array("azienda_id" => $filtro["azienda"],
                        "stabilimento_id" => $filtro["stabilimento"],
                        "area_id" => $filtro["area"],
                        "ruolo_id" => $ruolo);

        $lista_misure = self::getMisureRiduzioneRischio($filtro);

        //print_r($lista_misure);die;

        $almento_una_riga = false;

        foreach( $lista_misure as $misura ){

            $almento_una_riga = true;
           
            $table .= "<tr>";

            $table .= "<td>";
            $table .= "<b>".$misura["nome"]."</b>";
            if( $misura["descrizione"] != "" ){
                $table .= "<br />".$misura["descrizione"];
            }
            $table .= "</td>";

            $table .= "<td style='width: 120px; text-align: center;'>".$misura["eseguire_entro"]."</td>";
            $table .= "<td style='width: 120px; text-align: center;'>".$misura["stato_misura"]."</td>";

            $table .= "</tr>";

        }
  
        $table .= "</tbody>";

        $table .= "</table>";

        //print($table);die;

        if( !$almento_una_riga ){
            $table = "";
        }

        return $table;

    }

    static function getRigaSituazioneByFiltro($filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT
                    sit.kpsitrischidvrid id,
                    sit.kp_gravita_rischio gravita_rischio,
                    sit.kp_probabilita_risc probabilita_risc,
                    sit.kp_misurazione misurazione,
                    sit.kp_valutazione_risc valutazione_risc,
                    sit.kp_frase_risc_dvr frase_risc_dvr,
                    sit.description description
                    FROM {$table_prefix}_kpsitrischidvr sit
                    INNER JOIN {$table_prefix}_kprischidvr risc ON risc.kprischidvrid = sit.kp_rischio
                    INNER JOIN {$table_prefix}_kpareestabilimento aree ON aree.kpareestabilimentoid = sit.kp_area_stab
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sit.kpsitrischidvrid
                    WHERE ent.deleted = 0";

        if( $filtro["azienda_id"] && $filtro["azienda_id"] != 0 ){
            $query .= " AND sit.kp_azienda = ".$filtro["azienda_id"];
        }

        if( $filtro["stabilimento_id"] && $filtro["stabilimento_id"] != 0 ){
            $query .= " AND sit.kp_stabilimento = ".$filtro["stabilimento_id"];
        }

        if( $filtro["area_id"] && $filtro["area_id"] != 0 ){
            $query .= " AND sit.kp_area_stab = ".$filtro["area_id"];
        }

        if( $filtro["related_to"] && $filtro["related_to"] != 0 ){
            $query .= " AND sit.kp_related_to = ".$filtro["related_to"];
        }
        else{
            $query .= " AND (sit.kp_related_to IS NULL OR sit.kp_related_to = '' OR sit.kp_related_to = 0)";
        }

        if( $filtro["pericolo_id"] && $filtro["pericolo_id"] != 0 ){
            $query .= " AND sit.kp_rischio = ".$filtro["pericolo_id"];
        }

        //print($query);die;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $esiste = true;

            $id = $adb->query_result($result_query, 0, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $gravita_rischio = $adb->query_result($result_query, 0, 'gravita_rischio');
            $gravita_rischio = html_entity_decode(strip_tags($gravita_rischio), ENT_QUOTES, $default_charset);

            $probabilita_rischio = $adb->query_result($result_query, 0, 'probabilita_risc');
            $probabilita_rischio = html_entity_decode(strip_tags($probabilita_rischio), ENT_QUOTES, $default_charset);

            $misurazione = $adb->query_result($result_query, 0, 'misurazione');
            $misurazione = html_entity_decode(strip_tags($misurazione), ENT_QUOTES, $default_charset);

            $valutazione_rischio = $adb->query_result($result_query, 0, 'valutazione_risc');
            $valutazione_rischio = html_entity_decode(strip_tags($valutazione_rischio), ENT_QUOTES, $default_charset);

            $frase_di_rischio = $adb->query_result($result_query, 0, 'frase_risc_dvr');
            $frase_di_rischio = html_entity_decode(strip_tags($frase_di_rischio), ENT_QUOTES, $default_charset);

            $descrizione = $adb->query_result($result_query, 0, 'description');
            $descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES, $default_charset);

        }
        else{

            $esiste = false;
            $id = "";
            $gravita_rischio = "";
            $probabilita_rischio = "";
            $misurazione = "";
            $valutazione_rischio = "";
            $frase_di_rischio = "";
            $descrizione = "";

        }

        $result = array("esiste" => $esiste,
                        "id" => $id,
                        "gravita_rischio" => $gravita_rischio,
                        "probabilita_rischio" => $probabilita_rischio,
                        "misurazione" => $misurazione,
                        "valutazione_rischio" => $valutazione_rischio,
                        "frase_di_rischio" => $frase_di_rischio,
                        "descrizione" => $description);

        return $result;

    }


} 

?>