<?php

/* kpro@tom12072017 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

class KpMigrazioneDati {

    /**

        public function: 
            - setPathExport($db_path)
            - setDbName($nome_db)
            - setModuliExport($array_moduli)
            - runExport()
            - setPathImport($db_path)
            - setModuliImport($array_moduli)
            - runImport();

    */

    private $db_path;
    private $nome_db;
    private $lista_moduli;
    private $nome_campo_aggancio;

    public function __construct(){

        $this->db_path = __DIR__;
        $this->nome_db = "export.db";
        $this->lista_moduli = array();
        $this->nome_campo_aggancio = "kp_id_sis_origine";

    }

    public function setPathExport($db_path){
        global $adb, $table_prefix, $default_charset;

        $this->db_path = $db_path;

    }

    public function setPathImport($db_path){
        global $adb, $table_prefix, $default_charset;

        $this->db_path = $db_path;

    }

    public function setDbName($nome_db){
        global $adb, $table_prefix, $default_charset;

        $this->nome_db = $nome_db;

    }

    public function setModuliExport($array_moduli){

        $this->lista_moduli = $array_moduli;

    }

    public function setModuliImport($array_moduli){

        $this->lista_moduli = $array_moduli;

    }

    public function runExport(){
        global $adb, $table_prefix, $default_charset;

        $this->creaDbExport();
        $this->exportDati();

    }

    private function creaDbExport(){
        global $adb, $table_prefix, $default_charset;

        printf("\n- Start creazione DB %s", date("Y-m-d H:i:s"));

        $database = $this->db_path."/".$this->nome_db;

        if ( file_exists($database) ) {
            @unlink($database);
        }

        $db = new SQLite3($database);

        $list_query = array();

        $query_crmentityrel = "CREATE TABLE IF NOT EXISTS 
                                vte_crmentityrel 
                                (crmid INTEGER(19),
                                module VARCHAR(100),
                                relcrmid INTEGER(19),
                                relmodule VARCHAR(100),
                                PRIMARY KEY (crmid, relcrmid)
                                )";

        $list_query[] = $query_crmentityrel;

        foreach( $list_query as $query ){
            $db->query($query);
        }

        $db->close();

        foreach($this->lista_moduli as $modulo){
            $this->creaTabellaDbExport($database, $modulo);
        }   

        printf("\n- End creazione DB %s\n", date("Y-m-d H:i:s"));

    }

    private function creaTabellaDbExport($database, $nome_modulo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $db = new SQLite3($database);

        $tabelle_modulo = $this->getTablesModulo($nome_modulo);

        foreach($tabelle_modulo as $tabella){

            $query = $this->getDynamicQueryCreateTable($tabella);
            $db->query($query);

        }

        printf("\n--- Creato DB modulo %s %s", $nome_modulo, date("Y-m-d H:i:s"));

        $db->close();

    }

    private function getTablesModulo($nome_modulo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    fiel.tablename tablename
                    FROM {$table_prefix}_field fiel
                    INNER JOIN {$table_prefix}_tab tab ON tab.tabid = fiel.tabid
                    WHERE tab.name = '".$nome_modulo."'
                    GROUP BY fiel.tablename";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){
            
            $tablename = $adb->query_result($result_query, $i, 'tablename');
            $tablename = html_entity_decode(strip_tags($tablename), ENT_QUOTES, $default_charset);

            $result[] = $tablename;

        }

        return $result;

    }

    private function getDynamicQueryCreateTable($nome_tabella){
        global $adb, $table_prefix, $default_charset, $current_user, $dbconfig;

        $dati_tabella = $this->getStrutturaDatabase($nome_tabella);
        //print_r($dati_tabella);die;

        $query = "CREATE TABLE IF NOT EXISTS ".$nome_tabella;

        $campi_tabella = "";

        foreach($dati_tabella as $campo){

            if($campi_tabella != ""){
                $campi_tabella .= ", ";
            }

            $campi_tabella .= $campo["nome"]." ".$campo["tipo_sqlite"];

            if( $campo["column_key"] == "PRI" ){
                $campi_tabella .= " PRIMARY KEY";
            }

        }

        if($campi_tabella == ""){
            return "";
        }
        else{

            $query = $query."(".$campi_tabella.")";
            return $query;

        }

    }

    private function getStrutturaDatabase($nome_tabella){
        global $adb, $table_prefix, $default_charset, $current_user, $dbconfig;

        $result = array();

        $query = "SELECT 
                    COLUMN_NAME nome,
                    COLUMN_TYPE tipo,
                    DATA_TYPE data_type,
                    CHARACTER_MAXIMUM_LENGTH max_character,
                    NUMERIC_PRECISION numeric_precision,
                    NUMERIC_SCALE numeric_scale,
                    COLUMN_KEY column_key
                    FROM INFORMATION_SCHEMA.COLUMNS 
                    WHERE TABLE_SCHEMA = '".$dbconfig['db_name']."' AND TABLE_NAME = '".$nome_tabella."'";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);
       
        for( $i = 0; $i < $num_result; $i++ ){

            $nome = $adb->query_result($result_query, $i, 'nome');
			$nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $tipo = $adb->query_result($result_query, $i, 'tipo');
            $tipo = html_entity_decode(strip_tags($tipo), ENT_QUOTES, $default_charset);

            $data_type = $adb->query_result($result_query, $i, 'data_type');
            $data_type = html_entity_decode(strip_tags($data_type), ENT_QUOTES, $default_charset);

            $max_character = $adb->query_result($result_query, $i, 'max_character');
            $max_character = html_entity_decode(strip_tags($max_character), ENT_QUOTES, $default_charset);

            $numeric_precision = $adb->query_result($result_query, $i, 'numeric_precision');
            $numeric_precision = html_entity_decode(strip_tags($numeric_precision), ENT_QUOTES, $default_charset);

            $numeric_scale = $adb->query_result($result_query, $i, 'numeric_scale');
            $numeric_scale = html_entity_decode(strip_tags($numeric_scale), ENT_QUOTES, $default_charset);

            $column_key = $adb->query_result($result_query, $i, 'column_key');
            $column_key = html_entity_decode(strip_tags($column_key), ENT_QUOTES, $default_charset);
            
            $tipo_sqlite = $this->datatypeConversionSqlite($data_type, $max_character, $numeric_precision, $numeric_scale);

            $result[] = array("nome" => $nome,
                                "tipo" => $tipo,
                                "tipo_sqlite" => $tipo_sqlite,
                                "column_key" => $column_key);

        }

        return $result;

    }

    private function datatypeConversionSqlite($data_type, $max_character, $numeric_precision, $numeric_scale){
        global $adb, $table_prefix, $default_charset, $current_user;

        $integer = array('int', 'tinyint');

        $real = array('decimal');

        $text = array('date', 'datetime', 'text', 'timestamp', 'varchar');

        $blob = array('longblob', 'longtext', 'blob');

        if($max_character == null){
            $max_character = "255";
        }

        if($numeric_precision == null){
            $numeric_precision = "15";
        }

        if($numeric_scale == null){
            $numeric_scale = "0";
        }

        $result = "";

        if( in_array($data_type, $integer) ){

            $result = "INTEGER(".$numeric_precision.")";

        }
        elseif( in_array($data_type, $real) ){

            $result = "REAL(".$numeric_precision.",".$numeric_scale.")";

        }
        elseif( in_array($data_type, $text) ){

            $result = "VARCHAR(".$max_character.")";

        }
        elseif( in_array($data_type, $blob) ){

            $result = "BLOB";

        }

        return $result;

    }

    private function exportDati(){
        global $adb, $table_prefix, $default_charset;
        
        foreach($this->lista_moduli as $modulo){
            $this->exportModulo($modulo);
        }   

        $this->exportRelatedModuli();

    }

    private function exportModulo($modulo){
        global $adb, $table_prefix, $default_charset;

        printf("\n- Start export modulo %s %s", $modulo, date("Y-m-d H:i:s"));

        $database = $this->db_path."/".$this->nome_db;

        $db = new SQLite3($database);

        $records = $this->getRecordTabellaPricipale($modulo);

        $modulo_entity = $this->getModuloEntity($modulo);

        $query_tabella_pricipale = $this->getQueryInsert($modulo_entity["tablename"], $records);

        $db->query($query_tabella_pricipale);

        $records_entity = $this->getRecordTabellaEntity($modulo, $records);

        $query_tabella_entity = $this->getQueryInsert($table_prefix."_crmentity" , $records_entity);

        $db->query($query_tabella_entity);

        $db->close();

        printf("\n--- Esportati %s record", count($records));

        printf("\n- End export modulo %s %s\n", $modulo, date("Y-m-d H:i:s"));

    }

    public function getRecordTabellaEntity($modulo, $records){
        global $adb, $table_prefix, $default_charset, $current_user;

        $modulo_entity = $this->getModuloEntity($modulo);

        $in = "(";

        foreach( $records as $record ){

            if( $in == "(" ){
                $in .= $record[ $modulo_entity["entityidfield"] ];
            }
            else{
                $in .= ", ".$record[ $modulo_entity["entityidfield"] ];
            }

        }

        $in .= ")";

        $tabella = $table_prefix."_crmentity";

        $dati_struttura_tabella_entity = $this->getStrutturaDatabase( $tabella );

        foreach( $dati_struttura_tabella_entity as $dato_struttura ){

            if( $query == "" ){
                $query .= "SELECT ".$dato_struttura["nome"];
            }
            else{
                $query .= ", ".$dato_struttura["nome"];
            }

        }

        $query .= " FROM ".$tabella;

        $query .= " WHERE crmid IN ".$in;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        $records = array();

        for( $i = 0; $i < $num_result; $i++ ){

            $record = array();

            foreach( $dati_struttura_tabella_entity as $dato_struttura ){

                $temp = $adb->query_result($result_query, $i, $dato_struttura["nome"]);
                $temp = html_entity_decode(strip_tags($temp), ENT_QUOTES, $default_charset);
                $temp = addslashes($temp);

                $record[ $dato_struttura["nome"] ] = $temp;

            }

            $records[] = $record;

        }

        return $records;

    }

    public function getRecordTabellaPricipale($modulo){
        global $adb, $table_prefix, $default_charset, $current_user;
        
        $modulo_entity = $this->getModuloEntity($modulo);

        $dati_struttura_tabella_pricipale = $this->getStrutturaDatabase( $modulo_entity["tablename"] );

        $query = "";

        foreach( $dati_struttura_tabella_pricipale as $dato_struttura ){

            if( $query == "" ){
                $query .= "SELECT princ.".$dato_struttura["nome"]." ".$dato_struttura["nome"];
            }
            else{
                $query .= ", princ.".$dato_struttura["nome"]." ".$dato_struttura["nome"];
            }

        }

        if( $query == "" ){

            return array();

        }

        $query .= " FROM ".$modulo_entity["tablename"]." princ
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = princ.".$modulo_entity["entityidfield"]."
                    WHERE ent.deleted = 0";

        //$query .= " LIMIT 2"; //Da Togliere

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        $records = array();

        for( $i = 0; $i < $num_result; $i++ ){

            $record = array();

            foreach( $dati_struttura_tabella_pricipale as $dato_struttura ){

                $temp = $adb->query_result($result_query, $i, $dato_struttura["nome"]);
                $temp = html_entity_decode(strip_tags($temp), ENT_QUOTES, $default_charset);
                $temp = addslashes($temp);

                $record[ $dato_struttura["nome"] ] = $temp;

            }

            $records[] = $record;

        }

        return $records;

    }

    public function getQueryInsert($table, $records){
        global $adb, $table_prefix, $default_charset, $current_user;

        $array_no_apici = array('decimal', 'int', 'tinyint');

        $dati_struttura_tabella = $this->getStrutturaDatabase( $table );

        $query = "";

        foreach( $dati_struttura_tabella as $dato_struttura ){

            if( $query == "" ){
                $query .= "INSERT INTO ".$table." (".$dato_struttura["nome"];
            }
            else{
                $query .= ", ".$dato_struttura["nome"];
            }

        }

        if( $query == "" ){

            return "";

        }

        $query .= ") VALUES";

        $insert = "";

        foreach( $records as $record ){

            if( $insert == "" ){
                $insert .= " (";
            }
            else{
                $insert .= ", (";
            }

            $i = 0;

            foreach( $dati_struttura_tabella as $dato_struttura ){

                if($i > 0){
                    $insert .= ", ";
                }

                if( in_array($dato_struttura["tipo"], $array_no_apici) ){
                    $insert .= $record[ $dato_struttura["nome"] ];
                }
                else{
                    $insert .= '"'.$record[ $dato_struttura["nome"] ].'"';
                }

                $i++;
                
            }

            $insert .= ")";

        }

        $query .= $insert;

        return $query;

    }

    private function getModuloEntity($nome_modulo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";
        
        $query = "SELECT 
                    tablename,
                    fieldname,
                    entityidfield
                    FROM {$table_prefix}_entityname 
                    WHERE modulename = '".$nome_modulo."'";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if($num_result > 0){

            $tablename = $adb->query_result($result_query, 0, 'tablename');
            $tablename = html_entity_decode(strip_tags($tablename), ENT_QUOTES, $default_charset);

            $fieldname = $adb->query_result($result_query, 0, 'fieldname');
            $fieldname = html_entity_decode(strip_tags($fieldname), ENT_QUOTES, $default_charset);

            $entityidfield = $adb->query_result($result_query, 0, 'entityidfield');
            $entityidfield = html_entity_decode(strip_tags($entityidfield), ENT_QUOTES, $default_charset);

            $result = array("tablename" => $tablename,
                            "fieldname" => $fieldname,
                            "entityidfield" => $entityidfield);

        }

        return $result;

    }

    private function exportRelatedModuli(){
        global $adb, $table_prefix, $default_charset, $current_user;

        printf("\n- Start export related %s", date("Y-m-d H:i:s"));

        $entitie_id = $this->getAllEntityIdExported();

        foreach( $entitie_id as $entity_id ){

            $this->exportRelatedEntity($entity_id, $entitie_id);

        }

        printf("\n- End export related %s\n", date("Y-m-d H:i:s"));

    }

    private function getAllEntityIdExported(){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $database = $this->db_path."/".$this->nome_db;

        $db = new SQLite3($database);

        $query = "SELECT 
                    crmid
                    FROM {$table_prefix}_crmentity
                    WHERE deleted = 0";

        $result_query = $db->query($query);
        while( $row = $result_query->fetchArray() ){

            if ( !in_array($row["crmid"], $result) ){

                $result[] = $row["crmid"];

            }

        }

        $db->close();

        return $result;

    }

    private function exportRelatedEntity($entity_id, $entitie_id = array()){
        global $adb, $table_prefix, $default_charset, $current_user;

        $query = "(SELECT 
                    rel.crmid crmid,
                    rel.module module,
                    rel.relcrmid relcrmid,
                    rel.relmodule relmodule
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    WHERE ent.deleted = 0 AND rel.crmid = ".$entity_id.")
                    UNION
                    (SELECT 
                    rel.relcrmid crmid,
                    rel.relmodule module,
                    rel.crmid relcrmid,
                    rel.module relmodule
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.crmid
                    WHERE ent.deleted = 0 AND rel.relcrmid = ".$entity_id.")";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){

            $crmid = $adb->query_result($result_query, $i, "crmid");
            $crmid = html_entity_decode(strip_tags($crmid), ENT_QUOTES, $default_charset);

            $module = $adb->query_result($result_query, $i, "module");
            $module = html_entity_decode(strip_tags($module), ENT_QUOTES, $default_charset);

            $relcrmid = $adb->query_result($result_query, $i, "relcrmid");
            $relcrmid = html_entity_decode(strip_tags($relcrmid), ENT_QUOTES, $default_charset);

            $relmodule = $adb->query_result($result_query, $i, "relmodule");
            $relmodule = html_entity_decode(strip_tags($relmodule), ENT_QUOTES, $default_charset);
     
            if( count($entitie_id) == 0 || in_array($relcrmid, $entitie_id)  ){

                $exist = $this->checkIfRelatedExistInExportDb($crmid, $relcrmid);

                if( !$exist ){

                    $database = $this->db_path."/".$this->nome_db;

                    $db = new SQLite3($database);

                    $insert = "INSERT INTO {$table_prefix}_crmentityrel 
                                (crmid, module, relcrmid, relmodule)
                                VALUES
                                (".$crmid.", '".$module."', ".$relcrmid.", '".$relmodule."')";
                    
                    $db->query($insert);

                    $db->close();

                }

            }

        }

    }

    private function checkIfRelatedExistInExportDb($crmid, $relcrmid){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = false;

        $database = $this->db_path."/".$this->nome_db;

        $db = new SQLite3($database);

        $query = "SELECT 
                    *
                    FROM {$table_prefix}_crmentityrel
                    WHERE
                    (crmid = ".$crmid." AND relcrmid = ".$relcrmid.")
                    OR
                    (crmid = ".$relcrmid." AND relcrmid = ".$crmid.")";

        $result_query = $db->query($query);
        $row = $result_query->fetchArray();

        if($row != false){
            $result = true;
        }

        $db->close();

        return $result;

    }

    public function runImport(){
        global $adb, $table_prefix, $default_charset, $current_user;

        foreach($this->lista_moduli as $modulo){
            $this->importModulo($modulo);
        }

        $this->importRelatedModuli();

    }

    private function importModulo($modulo){
        global $adb, $table_prefix, $default_charset, $current_user;

        printf("\n- Start import modulo %s %s", $modulo, date("Y-m-d H:i:s"));

        $modulo_entity = $this->getModuloEntity($modulo);

        $records = $this->getRecordDbExportTabellaPricipale($modulo);

        foreach( $records as $record ){

            $exist = $this->checkIfRecordAlreadyExist($modulo, $record[ $modulo_entity["entityidfield"] ] );

            if( $exist["esiste"] ){

                $id = $exist["id"];

                $id = $this->updateRecord($modulo, $record, $id);

                printf("\n--- Aggiornato record id %s %s", $id, date("Y-m-d H:i:s"));

            }
            else{

                $id = $this->insertRecord($modulo, $record);

                printf("\n--- Inserito record id %s %s", $id, date("Y-m-d H:i:s"));

            }

        }

        printf("\n- End import modulo %s %s\n", $modulo, date("Y-m-d H:i:s"));

    }

    public function getRecordDbExportTabellaPricipale($modulo){
        global $adb, $table_prefix, $default_charset, $current_user;
        
        $modulo_entity = $this->getModuloEntity($modulo);

        $dati_struttura_tabella_pricipale = $this->getStrutturaDatabaseExport( $modulo_entity["tablename"] );

        $query = "";

        foreach( $dati_struttura_tabella_pricipale as $dato_struttura ){

            if( $query == "" ){
                $query .= "SELECT princ.".$dato_struttura["nome"]." ".$dato_struttura["nome"];
            }
            else{
                $query .= ", princ.".$dato_struttura["nome"]." ".$dato_struttura["nome"];
            }

        }

        if( $query == "" ){

            return array();

        }

        $records = array();

        $database = $this->db_path."/".$this->nome_db;

        $db = new SQLite3($database);

        $query .= " FROM ".$modulo_entity["tablename"]." princ
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = princ.".$modulo_entity["entityidfield"]."
                    WHERE ent.deleted = 0";
        
        $result_query = $db->query($query);
        while( $row = $result_query->fetchArray() ){

            $record = array();

            foreach( $dati_struttura_tabella_pricipale as $dato_struttura ){

                $temp = $row[ $dato_struttura["nome"] ];
                $temp = html_entity_decode(strip_tags($temp), ENT_QUOTES, $default_charset);

                $record[ $dato_struttura["nome"] ] = $temp;

            }

            $records[ $record[ $modulo_entity["entityidfield"] ] ] = $record;

        }

        return $records;

    }

    public function getStrutturaDatabaseExport($tabella){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $database = $this->db_path."/".$this->nome_db;

        $db = new SQLite3($database);

        $query = "pragma table_info(".$tabella.")";

        $result_query = $db->query($query);
        while( $row = $result_query->fetchArray() ){

            $nome = $row["name"];
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $type = $row["type"];
            $type = html_entity_decode(strip_tags($type), ENT_QUOTES, $default_charset);

            $result[] = array("nome" => $nome,
                                "type" => $type);

        }

        $db->close();

        return $result;

    }

    private function getModuloFields($modulo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    fie.fieldid id,
                    fie.columnname columnname,
                    fie.tablename tablename,
                    fie.uitype uitype,
                    fie.fieldname fieldname,
                    fie.fieldlabel fieldlabel
                    FROM {$table_prefix}_field fie
                    INNER JOIN {$table_prefix}_tab tab ON tab.tabid = fie.tabid
                    WHERE tab.name = '".$modulo."'";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, "id");
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $columnname = $adb->query_result($result_query, $i, "columnname");
            $columnname = html_entity_decode(strip_tags($columnname), ENT_QUOTES, $default_charset);

            $tablename = $adb->query_result($result_query, $i, "tablename");
            $tablename = html_entity_decode(strip_tags($tablename), ENT_QUOTES, $default_charset);

            $uitype = $adb->query_result($result_query, $i, "uitype");
            $uitype = html_entity_decode(strip_tags($uitype), ENT_QUOTES, $default_charset);

            $fieldname = $adb->query_result($result_query, $i, "fieldname");
            $fieldname = html_entity_decode(strip_tags($fieldname), ENT_QUOTES, $default_charset);

            $fieldlabel = $adb->query_result($result_query, $i, "fieldlabel");
            $fieldlabel = html_entity_decode(strip_tags($fieldlabel), ENT_QUOTES, $default_charset);

            $result[] = array("id" => $id,
                                "columnname" => $columnname,
                                "tablename" => $tablename,
                                "uitype" => $uitype,
                                "fieldname" => $fieldname,
                                "fieldlabel" => $fieldlabel);

        }

        return $result;

    }

    public function checkIfRecordAlreadyExist( $modulo, $id ){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $modulo_entity = $this->getModuloEntity($modulo);

        $query = "SELECT 
                    princ.".$modulo_entity["entityidfield"]." id
                    FROM ".$modulo_entity["tablename"]." princ 
                    INNER JOIN vte_crmentity ent ON ent.crmid = princ.".$modulo_entity["entityidfield"]."
                    WHERE ent.deleted = 0 AND princ.".$this->nome_campo_aggancio." = '".$id."'";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $esiste = true;

            $id = $adb->query_result($result_query, 0, "id");
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

    private function insertRecord($modulo, $record){
        global $adb, $table_prefix, $default_charset, $current_user;

        $current_user->id = 1;

        $modulo_entity = $this->getModuloEntity($modulo);

        $modulo_fields = $this->getModuloFields($modulo);

        $focus = CRMEntity::getInstance($modulo);

        foreach( $modulo_fields as $field ){

            if( $record[ $field["columnname"] ] != null && $record[ $field["columnname"] ] != "" ){

                if( $field["uitype"] == 10 ){

                    $relmodule = $this->getRecordSetTypeDbExport( $record[ $field["columnname"] ] );

                    if( $relmodule != "" ){

                        $relcrmid = $this->getElementoCorrispondenteDestinazione($relmodule, $record[ $field["columnname"] ]);

                        if( $relcrmid["esiste"] ){

                            $focus->column_fields[ $field["fieldname"] ] = $relcrmid["id"];

                        }
                        else{
                            printf("\n-----> Impossibile popolare campo relazionato %s", $field["fieldlabel"]);
                        }

                    }
                    else{
                        printf("\n-----> Impossibile popolare campo relazionato %s", $field["fieldlabel"]);
                    }

                }
                else{
                    $focus->column_fields[ $field["fieldname"] ] = $record[ $field["columnname"] ];
                }

            }

        }

        $focus->column_fields["assigned_user_id"] = 1;

        $focus->column_fields[ $this->nome_campo_aggancio ] = $record[ $modulo_entity["entityidfield"] ];

        if( $focus->column_fields["description"] == "" ){

            $record_entity = $this->getRecordDbExportTabellaEntity( $record[ $modulo_entity["entityidfield"] ] );

            $focus->column_fields["description"] = $record_entity["description"];

        }

        $focus->save($modulo, $longdesc=true, $offline_update=false, $triggerEvent=false);

        $result = $focus->id;

        return $result;

    }

    private function updateRecord($modulo, $record, $id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $current_user->id = 1;

        $modulo_entity = $this->getModuloEntity($modulo);

        $modulo_fields = $this->getModuloFields($modulo);

        $focus = CRMEntity::getInstance($modulo);
        $focus->retrieve_entity_info($id, $modulo); 

        foreach( $modulo_fields as $field ){

            if( $record[ $field["columnname"] ] != null && $record[ $field["columnname"] ] != "" ){

                if( $field["uitype"] == 10 ){

                    $relmodule = $this->getRecordSetTypeDbExport( $record[ $field["columnname"] ] );

                    if( $relmodule != "" ){

                        $relcrmid = $this->getElementoCorrispondenteDestinazione($relmodule, $record[ $field["columnname"] ]);

                        if( $relcrmid["esiste"] ){

                            $focus->column_fields[ $field["fieldname"] ] = $relcrmid["id"];

                        }
                        else{
                            printf("\n-----> Impossibile popolare campo relazionato %s", $field["fieldlabel"]);
                        }

                    }
                    else{
                        printf("\n-----> Impossibile popolare campo relazionato %s", $field["fieldlabel"]);
                    }

                }
                else{
                    $focus->column_fields[ $field["fieldname"] ] = $record[ $field["columnname"] ];
                }

            }

        }

        if( $focus->column_fields["description"] == "" ){

            $record_entity = $this->getRecordDbExportTabellaEntity( $record[ $modulo_entity["entityidfield"] ] );

            $focus->column_fields["description"] = $record_entity["description"];

        }

        $focus->mode = 'edit';
        $focus->id = $id; 
        $focus->save($modulo, $longdesc=true, $offline_update=false, $triggerEvent=false);

        $result = $focus->id;

        return $result;

    }

    public function getRecordDbExportTabellaEntity($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $dati_struttura_tabella = $this->getStrutturaDatabaseExport( $table_prefix."_crmentity" );

        $query = "";

        foreach( $dati_struttura_tabella as $dato_struttura ){

            if( $query == "" ){
                $query .= "SELECT ".$dato_struttura["nome"];
            }
            else{
                $query .= ", ".$dato_struttura["nome"];
            }

        }

        if( $query == "" ){

            return array();

        }

        $database = $this->db_path."/".$this->nome_db;

        $db = new SQLite3($database);

        $query .= " FROM {$table_prefix}_crmentity
                    WHERE deleted = 0 AND crmid = ".$id;
        
        $result_query = $db->query($query);
        while( $row = $result_query->fetchArray() ){

            $record = array();

            foreach( $dati_struttura_tabella as $dato_struttura ){

                $temp = $row[ $dato_struttura["nome"] ];
                $temp = html_entity_decode(strip_tags($temp), ENT_QUOTES, $default_charset);

                $record[ $dato_struttura["nome"] ] = $temp;

            }

        }

        $db->close();

        return $record;

    }

    private function getRecordSetTypeDbExport($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $setype = "";

        $database = $this->db_path."/".$this->nome_db;

        $db = new SQLite3($database);

        $query = "SELECT 
                    setype
                    FROM {$table_prefix}_crmentity
                    WHERE deleted = 0 AND crmid = ".$id;

        $result_query = $db->query($query);
        while( $row = $result_query->fetchArray() ){

            $setype = $row["setype"];
            $setype = html_entity_decode(strip_tags($setype), ENT_QUOTES, $default_charset);

        }

        $db->close();

        return $setype;

    }

    private function getElementoCorrispondenteDestinazione($modulo, $id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $modulo_entity = $this->getModuloEntity($modulo);

        $query = "SELECT 
                    princ.".$modulo_entity["entityidfield"]." id
                    FROM ".$modulo_entity["tablename"]." princ 
                    INNER JOIN vte_crmentity ent ON ent.crmid = princ.".$modulo_entity["entityidfield"]."
                    WHERE ent.deleted = 0 AND princ.".$this->nome_campo_aggancio." = '".$id."'";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $esiste = true;

            $id = $adb->query_result($result_query, 0, "id");
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

    private function importRelatedModuli(){
        global $adb, $table_prefix, $default_charset, $current_user;

        printf("\n- Start import related %s", date("Y-m-d H:i:s"));

        $records = $this->getRecordDbExportRelated();

        foreach($records as $record){

            $crmid = $this->getElementoCorrispondenteDestinazione($record["module"], $record["crmid"]);

            $relcrmid = $this->getElementoCorrispondenteDestinazione($record["relmodule"], $record["relcrmid"]);

            if( $crmid["esiste"] && $relcrmid["esiste"] ){

                $exist = $this->checkIfRelatedExist($crmid["id"], $relcrmid["id"]);

                if( !$exist ){

                    $insert = "INSERT INTO {$table_prefix}_crmentityrel (crmid, module, relcrmid, relmodule)
                                VALUES
                                (".$crmid["id"].", '".$record["module"]."', ".$relcrmid["id"].", '".$record["relmodule"]."')";

                    $adb->query($insert);

                    printf("\n--- Creazione relazione %s %s -> %s %s %s", $record["module"], $crmid["id"], $record["relmodule"], $relcrmid["id"], date("Y-m-d H:i:s"));

                }
                else{

                    printf("\n--- Relazione %s %s -> %s %s già esistente", $record["module"], $crmid["id"], $record["relmodule"], $relcrmid["id"]);
                
                }

            }
            else{

                printf("\n--- Impossibile creare relazione tra %s %s -> %s %s", $record["module"], $crmid["id"], $record["relmodule"], $relcrmid["id"]); 

            }

        }

        printf("\n- End import related %s\n", date("Y-m-d H:i:s"));

    }

    private function getRecordDbExportRelated(){
        global $adb, $table_prefix, $default_charset, $current_user;

        $records = array();

        $database = $this->db_path."/".$this->nome_db;

        $db = new SQLite3($database);

        $query = "SELECT 
                    crmid,
                    module,
                    relcrmid,
                    relmodule
                    FROM {$table_prefix}_crmentityrel";
        
        $result_query = $db->query($query);
        while( $row = $result_query->fetchArray() ){

            $crmid = $row["crmid"];
            $crmid = html_entity_decode(strip_tags($crmid), ENT_QUOTES, $default_charset);

            $module = $row["module"];
            $module = html_entity_decode(strip_tags($module), ENT_QUOTES, $default_charset);

            $relcrmid = $row["relcrmid"];
            $relcrmid = html_entity_decode(strip_tags($relcrmid), ENT_QUOTES, $default_charset);

            $relmodule = $row["relmodule"];
            $relmodule = html_entity_decode(strip_tags($relmodule), ENT_QUOTES, $default_charset);

            $records[] = array("crmid" => $crmid,
                                "module" => $module,
                                "relcrmid" => $relcrmid,
                                "relmodule" => $relmodule);

        }

        return $records;

    }

    private function checkIfRelatedExist($crmid, $relcrmid){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = false;

        $query = "SELECT 
                    *
                    FROM {$table_prefix}_crmentityrel
                    WHERE
                    (crmid = ".$crmid." AND relcrmid = ".$relcrmid.")
                    OR
                    (crmid = ".$relcrmid." AND relcrmid = ".$crmid.")";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $result = true;

        }

        return $result;

    }



}

?>