<?php 

class KpRiportaMinaccePrivacyImpianto { 

    static function getListaImpianti($filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    imp.impiantiid id,
                    imp.impianto_name nome,
                    imp.matricola_impianto matricola,
                    acc.accountname nome_azienda,
                    stab.nome_stabilimento nome_stabilimento
                    FROM {$table_prefix}_impianti imp
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = imp.impiantiid
                    INNER JOIN {$table_prefix}_account acc ON acc.accountid = imp.azienda
                    INNER JOIN {$table_prefix}_stabilimenti stab ON stab.stabilimentiid = imp.stabilimento
                    WHERE ent.deleted = 0";

        if( $filtro["matricola"] != "" ){
            $query .= " AND imp.matricola_impianto LIKE '%".$filtro["matricola"]."%'";
        }

        if( $filtro["nome_impianto"] != "" ){
            $query .= " AND imp.impianto_name LIKE '%".$filtro["nome_impianto"]."%'";
        }

        if( $filtro["azienda"] != "" ){
            $query .= " AND acc.accountname LIKE '%".$filtro["azienda"]."%'";
        }

        if( $filtro["stabilimento"] != "" ){
            $query .= " AND stab.nome_stabilimento LIKE '%".$filtro["stabilimento"]."%'";
        }
        
        $query .= " ORDER BY imp.impianto_name ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){
            
            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $matricola = $adb->query_result($result_query, $i, 'matricola');
            $matricola = html_entity_decode(strip_tags($matricola), ENT_QUOTES, $default_charset);

            $nome_azienda = $adb->query_result($result_query, $i, 'nome_azienda');
            $nome_azienda = html_entity_decode(strip_tags($nome_azienda), ENT_QUOTES, $default_charset);

            $nome_stabilimento = $adb->query_result($result_query, $i, 'nome_stabilimento');
            $nome_stabilimento = html_entity_decode(strip_tags($nome_stabilimento), ENT_QUOTES, $default_charset);

            $minacce_relazionate = self::getMinacceImpianto($id);

            $numero_minacce = count($minacce_relazionate);

            if( $numero_minacce > 0 ){

                $result[] = array("id" => $id,
                                    "nome" => $nome,
                                    "matricola" => $matricola,
                                    "nome_azienda" => $nome_azienda,
                                    "nome_stabilimento" => $nome_stabilimento,
                                    "numero_minacce" => $numero_minacce);

            }

        }

        return $result;
        
    }

    static function getMinacceImpianto($impianto){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT t.* FROM
                    ((SELECT 
                    rel.relcrmid id
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    WHERE ent.deleted = 0 AND rel.module = 'Impianti' AND rel.relmodule = 'KpMinaccePrivacy' AND rel.crmid = ".$impianto.")
                    UNION
                    (SELECT 
                    rel.crmid id
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.crmid
                    WHERE ent.deleted = 0 AND rel.relmodule = 'Impianti' AND rel.module = 'KpMinaccePrivacy' AND rel.relcrmid = ".$impianto.")) AS t
                    GROUP BY t.id";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $result[] = array("id" => $id);

        }

        return $result;

    }

    static function clonaMinaccePrivacyDaAltroImpianto($copia_da, $record){
        global $adb, $table_prefix, $default_charset, $current_user;

        $minacce_relazionate = self::getMinacceImpianto($record);

        if( count($minacce_relazionate) > 0 ){

            self::clearRelatedMinacceImpianto($record);

        }

        $minacce_relazionate = self::getMinacceImpianto($copia_da);

        foreach($minacce_relazionate as $minaccia){

            self::setRelazioneMinaccia($record, $minaccia["id"]);

        }

    }

    static function clearRelatedMinacceImpianto($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $delete = "DELETE FROM {$table_prefix}_crmentityrel
                    WHERE 
                    (crmid = ".$id." AND module = 'Impianti' AND relmodule = 'KpMinaccePrivacy')
                    OR
                    (relcrmid = ".$id." AND relmodule = 'Impianti' AND module = 'KpMinaccePrivacy')";
        
        $adb->query($delete);

    }

    static function setRelazioneMinaccia($impianto, $minaccia){
        global $adb, $table_prefix, $default_charset, $current_user;

        $insert = "INSERT INTO {$table_prefix}_crmentityrel (crmid, module, relcrmid, relmodule)
                    VALUES
                    (".$impianto.", 'Impianti', ".$minaccia.", 'KpMinaccePrivacy')";
                    
        $adb->query($insert);

    }


} 

?>