<?php 

/* kpro@20170904111327 */ 

/** 
 * @copyright (c) 2017, Kpro Consulting Srl 
 * 
 * Estensione classe KpLettereNomina 
 */ 

require_once('modules/KpLettereNomina/KpLettereNomina.php'); 

class KpLettereNominaKp extends KpLettereNomina { 

    var $list_fields = Array();
    
    var $list_fields_name = Array(
        'Soggetto'=>'kp_soggetto',
        'Risorsa'=>'kp_risorsa',
        'Data'=>'kp_data',
        'Tipo Lettera'=>'kp_tipo_lettera',
        'Stato'=>'kp_stato',
        'Assigned To'=>'assigned_user_id'	
    );

    function KpLettereNominaKp(){
        global $table_prefix;
        parent::__construct();
        $this->list_fields = Array(
            'Soggetto'=>Array($table_prefix.'_kpletterenomina'=>'kp_soggetto'),
            'Risorsa'=>Array($table_prefix.'_kpletterenomina'=>'kp_risorsa'),
            'Data'=>Array($table_prefix.'_kpletterenomina'=>'kp_data'),
            'Tipo Lettera'=>Array($table_prefix.'_kpletterenomina'=>'kp_tipo_lettera'),
            'Stato'=>Array($table_prefix.'_kpletterenomina'=>'kp_stato'),
            'Assigned To'=>Array($table_prefix.'_crmentity'=>'smownerid')
        );

    }

    function save_module($module){
        
        global $table_prefix, $adb;

        parent::save_module($module);

        $this->setCampiAutocalcolati( $this->id );

        //$this->setMansioniLetteraDiNomina( $this->id );
        
        //$this->setCategorieLetteraDiNomina( $this->id );

        //$this->setOperazioniDiTrattamento( $this->id );
        
    }

    function setCampiAutocalcolati($id){
        global $adb, $table_prefix, $default_charset;

        $this->setCampiAutocalcolatiRisorsa( $this->id );
        
    }

    function setCampiAutocalcolatiRisorsa($id){
        global $adb, $table_prefix, $default_charset;

        if($this->column_fields['kp_risorsa'] != null && $this->column_fields['kp_risorsa'] != "" && $this->column_fields['kp_risorsa'] != 0){

            $focus_risorsa = CRMEntity::getInstance('Contacts');
            $focus_risorsa->retrieve_entity_info($this->column_fields['kp_risorsa'], "Contacts"); 
            
            $update = "UPDATE {$table_prefix}_kpletterenomina SET 
                        kp_azienda = ".$focus_risorsa->column_fields["account_id"].",
                        kp_stabilimento = ".$focus_risorsa->column_fields["stabilimento"].",
                        kp_fornitore = ".$focus_risorsa->column_fields["vendor_id"]."
                        WHERE kpletterenominaid = ".$id;
            $adb->query($update);

        }
    
    }

    static function checkLetteraDiNominaRisorsa($risorsa){
        global $adb, $table_prefix, $default_charset;

        $stato_lettera = self::getStatoLetteraDiNominaRisorsa($risorsa);
        
        if( $stato_lettera == "Lettera di nomina non valida!" ){
            //Metti in scaduto le varie lettere di nomina non valide

            self::setStatoScadutoLettereDiNominaRisorsa($risorsa);

        }

        if( $stato_lettera != "Lettera di nomina presente!" ){
            //Genera lettera di nomina

            self::setLetteraDiNominaRisorsa($risorsa);

        }

    }

    static function getStatoLetteraDiNominaRisorsa($risorsa){
        global $adb, $table_prefix, $default_charset;

        $stato = "Lettera di nomina presente!";

        $ultima_lettera_nomina = self::getUltimaLetteraDiNominaAttivaRisorsa($risorsa);

        if( $ultima_lettera_nomina["esiste"] ){

            if( !self::checkIfLetteraNominaValida($ultima_lettera_nomina) ){

                $stato = "Lettera di nomina non valida!";

            }

        }
        else{

            $stato = "Lettera di nomina non presente!";

        }

        return $stato;

    }

    static function getUltimaLetteraDiNominaAttivaRisorsa($risorsa){
        global $adb, $table_prefix, $default_charset;

        $query = "SELECT 
                    let.kpletterenominaid kpletterenominaid,
                    let.kp_soggetto kp_soggetto,
                    let.kp_risorsa kp_risorsa,
                    let.kp_data kp_data,
                    let.kp_stato kp_stato,
                    let.kp_tipo_lettera kp_tipo_lettera
                    FROM {$table_prefix}_kpletterenomina let
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = let.kpletterenominaid
                    WHERE ent.deleted = 0 AND let.kp_tipo_lettera = 'Lettera di incarico' AND let.kp_stato != 'Scaduta' AND let.kp_risorsa = ".$risorsa."
                    ORDER BY let.kp_data DESC";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $esiste = true;

            $id = $adb->query_result($result_query, 0, 'kpletterenominaid');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, 0, 'kp_soggetto');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);
            
            $data = $adb->query_result($result_query, 0, 'kp_data');
            $data = html_entity_decode(strip_tags($data), ENT_QUOTES, $default_charset);
            
            $risorsa = $adb->query_result($result_query, 0, 'kp_risorsa');
			$risorsa = html_entity_decode(strip_tags($risorsa), ENT_QUOTES, $default_charset);

        }
        else{

            $esiste = false;
            $id = 0;
            $nome = "";
            $data = "";
            $risorsa = 0;

        }

        $result = array("esiste" => $esiste,
                        "id" => $id,
                        "nome" => $nome,
                        "data" => $data,
                        "risorsa" => $risorsa);

        return $result;

    }

    static function checkIfLetteraNominaValida($lettera_nomina){
        global $adb, $table_prefix, $default_charset;
        
        $query = "SELECT 
                    manr.mansionirisorsaid mansionirisorsaid
                    FROM {$table_prefix}_mansionirisorsa manr
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = manr.mansionirisorsaid
                    WHERE ent.deleted = 0 AND manr.kp_mansione_privacy = '1' AND manr.stato_mansione = 'Attiva' AND manr.data_inizio > '".$lettera_nomina["data"]."' AND manr.risorsa = ".$lettera_nomina["risorsa"]."
                    ORDER BY manr.data_inizio ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $result = false;

        }
        else{

            $result = true;

        }

        return $result;

    }

    static function setLetteraDiNominaRisorsa($risorsa){
        global $adb, $table_prefix, $default_charset;

        $focus_risorsa = CRMEntity::getInstance('Contacts');
        $focus_risorsa->retrieve_entity_info($risorsa, "Contacts"); 

        $soggetto = "Lettera di incarico ".$focus_risorsa->column_fields["lastname"]." ".$focus_risorsa->column_fields["firstname"]." del ".date("d-m-Y");

        $letteraNomina = CRMEntity::getInstance('KpLettereNomina');
        $letteraNomina->column_fields['assigned_user_id'] = 1;
        $letteraNomina->column_fields['kp_soggetto'] = $soggetto;
        $letteraNomina->column_fields['kp_risorsa'] = $risorsa;
        $letteraNomina->column_fields['kp_data'] = date("Y-m-d");
        $letteraNomina->column_fields['kp_stato'] = "Da consegnare";
        $letteraNomina->column_fields['kp_tipo_lettera'] = "Lettera di incarico";
        $letteraNomina->save('KpLettereNomina', $longdesc=true, $offline_update=false, $triggerEvent=false); 

        self::setMansioniLetteraDiNomina( $letteraNomina->id );

        self::setCategorieLetteraDiNomina( $letteraNomina->id );

        self::setOperazioniDiTrattamento( $letteraNomina->id );

    }

    static function setMansioniLetteraDiNomina($id){
        global $adb, $table_prefix, $default_charset;

        $focus = CRMEntity::getInstance('KpLettereNomina');
        $focus->retrieve_entity_info($id, "KpLettereNomina"); 

        $risorsa = $focus->column_fields["kp_risorsa"];

        $lista_mansioni_privacy = self::getMansioniPrivacyRisorsa( $risorsa );
        
        foreach($lista_mansioni_privacy as $mansione){
            
            if( !self::checkIfMansioneAlredyRelated($id, $mansione) ){

                $insert = "INSERT INTO vte_crmentityrel 
                            (crmid, module, relcrmid, relmodule)
                            VALUES
                            (".$id.", 'KpLettereNomina', ".$mansione.", 'Mansioni')";

                $adb->query($insert);

            }

        }

    }

    static function getMansioniPrivacyRisorsa($risorsa){
        global $adb, $table_prefix, $default_charset;

        $result = array();

        $query = "SELECT 
                    manr.mansione mansione
                    FROM {$table_prefix}_mansionirisorsa manr
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = manr.mansionirisorsaid
                    WHERE ent.deleted = 0 AND manr.kp_mansione_privacy = '1' AND manr.stato_mansione = 'Attiva' AND manr.risorsa = ".$risorsa."
                    ORDER BY manr.data_inizio ASC";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'mansione');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $result[] = $id;

        }

        return $result;

    }

    static function checkIfMansioneAlredyRelated($lettera_di_nomina, $mansione){
        global $adb, $table_prefix, $default_charset;

        $result = false;

        $query = "SELECT 
                    *
                    FROM vte_crmentityrel
                    WHERE ( crmid = ".$lettera_di_nomina." AND module = 'KpLettereNomina' AND relcrmid = ".$mansione." AND relmodule = 'Mansioni' )
                    OR
                    ( crmid = ".$mansione." AND module = 'Mansioni' AND relcrmid = ".$lettera_di_nomina." AND relmodule = 'KpLettereNomina' )";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $result = true;

        }

        return $result;

    }

    static function setCategorieLetteraDiNomina($id){
        global $adb, $table_prefix, $default_charset;

        $focus = CRMEntity::getInstance('KpLettereNomina');
        $focus->retrieve_entity_info($id, "KpLettereNomina"); 

        $risorsa = $focus->column_fields["kp_risorsa"];

        $lista_mansioni_risorsa_privacy = self::getMansioniRisorsaPrivacyRisorsa( $risorsa );
        
        foreach($lista_mansioni_risorsa_privacy as $mansione_risorsa){

            $lista_categorie_privacy = self::getCategoriePrivacyMansioneRisorsa( $mansione_risorsa );

            foreach($lista_categorie_privacy as $categoria_privacy){

                if( !self::checkIfCategoriaPrivacyAlredyRelated($id, $categoria_privacy) ){

                    $insert = "INSERT INTO vte_crmentityrel 
                                (crmid, module, relcrmid, relmodule)
                                VALUES
                                (".$id.", 'KpLettereNomina', ".$categoria_privacy.", 'KpCategoriePrivacy')";

                    $adb->query($insert);

                }

            }

        }

    }

    static function getMansioniRisorsaPrivacyRisorsa($risorsa){
        global $adb, $table_prefix, $default_charset;

        $result = array();

        $query = "SELECT 
                    manr.mansionirisorsaid mansionirisorsaid
                    FROM {$table_prefix}_mansionirisorsa manr
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = manr.mansionirisorsaid
                    WHERE ent.deleted = 0 AND manr.kp_mansione_privacy = '1' AND manr.stato_mansione = 'Attiva' AND manr.risorsa = ".$risorsa."
                    ORDER BY manr.data_inizio ASC";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'mansionirisorsaid');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $result[] = $id;

        }

        return $result;

    }

    static function getCategoriePrivacyMansioneRisorsa($mansione_risorsa){
        global $adb, $table_prefix, $default_charset;

        $result = array();

        $query = "(SELECT 
                    crmrel.relcrmid categoria_privacy
                    FROM {$table_prefix}_crmentityrel crmrel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = crmrel.relcrmid
                    WHERE ent.deleted = 0 AND crmrel.module = 'MansioniRisorsa' AND crmrel.relmodule = 'KpCategoriePrivacy' AND crmrel.crmid = ".$mansione_risorsa.")
                    UNION
                    (SELECT 
                    crmrel.crmid categoria_privacy
                    FROM {$table_prefix}_crmentityrel crmrel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = crmrel.crmid
                    WHERE ent.deleted = 0 AND crmrel.module = 'KpCategoriePrivacy' AND crmrel.relmodule = 'MansioniRisorsa' AND crmrel.relcrmid = ".$mansione_risorsa.")";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'categoria_privacy');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $result[] = $id;

        }

        return $result;

    }

    static function checkIfCategoriaPrivacyAlredyRelated($lettera_di_nomina, $categoria_privacy){
        global $adb, $table_prefix, $default_charset;

        $result = false;

        $query = "SELECT 
                    *
                    FROM vte_crmentityrel
                    WHERE ( crmid = ".$lettera_di_nomina." AND module = 'KpLettereNomina' AND relcrmid = ".$categoria_privacy." AND relmodule = 'KpCategoriePrivacy' )
                    OR
                    ( crmid = ".$categoria_privacy." AND module = 'KpCategoriePrivacy' AND relcrmid = ".$lettera_di_nomina." AND relmodule = 'KpLettereNomina' )";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $result = true;

        }

        return $result;

    }
    static function setStatoScadutoLettereDiNominaRisorsa($risorsa){
        global $adb, $table_prefix, $default_charset;

        $lista_lettere_di_nomina = self::getLettereDiNominaAttiveRisorsa($risorsa);
        
        foreach($lista_lettere_di_nomina as $lettera_di_nomina){

            $focus = CRMEntity::getInstance('KpLettereNomina');
            $focus->retrieve_entity_info($lettera_di_nomina, "KpLettereNomina");
            $focus->column_fields['kp_stato'] = "Scaduta";    
            $focus->mode = 'edit';
            $focus->id = $lettera_di_nomina;
            $focus->save('KpLettereNomina', $longdesc=true, $offline_update=false, $triggerEvent=false);

        }

    }

    static function getLettereDiNominaAttiveRisorsa($risorsa){
        global $adb, $table_prefix, $default_charset;

        $result = array();

        $query = "SELECT 
                    let.kpletterenominaid kpletterenominaid
                    FROM {$table_prefix}_kpletterenomina let
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = let.kpletterenominaid
                    WHERE ent.deleted = 0 AND let.kp_tipo_lettera = 'Lettera di incarico' AND let.kp_stato != 'Scaduta' AND let.kp_risorsa = ".$risorsa."
                    ORDER BY let.kp_data ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, 0, 'kpletterenominaid');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $result[] = $id;

        }

        return $result;

    }

    static function checkLettereDiNominaRisorse(){
        global $adb, $table_prefix, $default_charset;

        $lista_risorse_privacy = self::getRisorseConMansioniPrivacy();

        foreach($lista_risorse_privacy as $risorsa){

            self::checkLetteraDiNominaRisorsa( $risorsa );

        }
        
    }

    static function getRisorseConMansioniPrivacy(){
        global $adb, $table_prefix, $default_charset;

        $result = array();

        $query = "SELECT 
                    manr.risorsa risorsa
                    FROM {$table_prefix}_mansionirisorsa manr
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = manr.mansionirisorsaid
                    WHERE ent.deleted = 0 AND manr.kp_mansione_privacy = '1' AND manr.stato_mansione = 'Attiva'
                    GROUP BY manr.risorsa";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, 0, 'risorsa');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $result[] = $id;

        }

        return $result;
        
    }

    static function setOperazioniDiTrattamento($id){
        global $adb, $table_prefix, $default_charset;

        $array_operazioni_trattamento = array();

        $lista_categorie_privacy = self::getCategoriePrivacyLetteraDiNomina($id);
        
        foreach($lista_categorie_privacy as $categoria_privacy){

            $operazioni_trattamento = $categoria_privacy["operazioni_trattamento"];
            $lista_operazioni_trattamento = explode("\|##\|", $operazioni_trattamento);
            
            foreach($lista_operazioni_trattamento as $operazione_trattamento){

                $operazione_trattamento = trim($operazione_trattamento);
                if( !in_array($operazione_trattamento, $array_operazioni_trattamento) ){

                    $array_operazioni_trattamento[] = $operazione_trattamento;

                }

            }

        }

        $string_operazioni_trattamento = "";

        foreach($array_operazioni_trattamento as $operazione_trattamento){

            if($string_operazioni_trattamento == ""){

                $string_operazioni_trattamento = $operazione_trattamento;

            }
            else{

                $string_operazioni_trattamento .= " |##| ".$operazione_trattamento;

            }

        }

        $string_operazioni_trattamento = addslashes($string_operazioni_trattamento);

        $update = "UPDATE {$table_prefix}_kpletterenomina SET
                    kp_op_trattamento = '".$string_operazioni_trattamento."'
                    WHERE kpletterenominaid = ".$id;
        $adb->query($update);

    }

    static function getCategoriePrivacyLetteraDiNomina($id){
        global $adb, $table_prefix, $default_charset;

        $result = array();
        
        $query = "(SELECT 
                    crmrel.relcrmid categoria_privacy,
                    cat.kp_op_trattamento kp_op_trattamento
                    FROM {$table_prefix}_crmentityrel crmrel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = crmrel.relcrmid
                    INNER JOIN {$table_prefix}_kpcategorieprivacy cat ON cat.kpcategorieprivacyid = crmrel.relcrmid
                    WHERE ent.deleted = 0 AND crmrel.module = 'KpLettereNomina' AND crmrel.relmodule = 'KpCategoriePrivacy' AND crmrel.crmid = ".$id.")
                    UNION
                    (SELECT 
                    crmrel.crmid categoria_privacy,
                    cat.kp_op_trattamento kp_op_trattamento
                    FROM {$table_prefix}_crmentityrel crmrel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = crmrel.crmid
                    INNER JOIN {$table_prefix}_kpcategorieprivacy cat ON cat.kpcategorieprivacyid = crmrel.crmid
                    WHERE ent.deleted = 0 AND crmrel.module = 'KpCategoriePrivacy' AND crmrel.relmodule = 'KpLettereNomina' AND crmrel.relcrmid = ".$id.")";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'categoria_privacy');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $operazioni_trattamento = $adb->query_result($result_query, $i, 'kp_op_trattamento');
            $operazioni_trattamento = html_entity_decode(strip_tags($operazioni_trattamento), ENT_QUOTES, $default_charset);
            
            $result[] = array("id" => $id,
                                "operazioni_trattamento" => $operazioni_trattamento);

        }

        return $result;

    }

} 

?>