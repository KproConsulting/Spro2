<?php

/* kpro@tom08022018 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2018, Kpro Consulting Srl
 */

class KpOrganigramma {

    /**

        static function: 
            - getRisorse($organigramma, $filtro)
            - getDatiOrganigramma($id)
            - getRuoli($organigramma, $filtro)
            - setOrganigramma($record, $elementi_organigramma)
            - getImmagineContatto($id)
    */

    static function getRisorse($organigramma, $filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();
        
        //$dati_organigramma = self::getDatiOrganigramma($organigramma);

        $query = "SELECT 
                    cont.contactid id,
                    cont.firstname nome,
                    cont.lastname cognome,
                    acc.accountname nome_azienda,
                    stab.nome_stabilimento nome_stabilimento
                    FROM {$table_prefix}_contactdetails cont
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = cont.contactid
                    LEFT JOIN {$table_prefix}_account acc ON acc.accountid = cont.accountid
                    LEFT JOIN {$table_prefix}_stabilimenti stab on stab.stabilimentiid = cont.stabilimento
                    WHERE ent.deleted = 0";

        if( $filtro["nome"] != "" ){
            $query .= " AND CONCAT(cont.firstname, ' ', cont.lastname) LIKE '%".$filtro["nome"]."%'";
        }

        if( $filtro["azienda"] != "" ){

            $query .= " AND acc.accountname LIKE '%".$filtro["azienda"]."%'";

        }

        if( $filtro["stabilimento"] != "" ){

            $query .= " AND stab.nome_stabilimento LIKE '%".$filtro["stabilimento"]."%'";

        }

        $query .= " ORDER BY cont.lastname ASC, cont.firstname ASC";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i=0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES,$default_charset);

            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES,$default_charset);

            $cognome = $adb->query_result($result_query, $i, 'cognome');
            $cognome = html_entity_decode(strip_tags($cognome), ENT_QUOTES,$default_charset);

            $nome_azienda = $adb->query_result($result_query, $i, 'nome_azienda');
            $nome_azienda = html_entity_decode(strip_tags($nome_azienda), ENT_QUOTES,$default_charset);

            $nome_stabilimento = $adb->query_result($result_query, $i, 'nome_stabilimento');
            $nome_stabilimento = html_entity_decode(strip_tags($nome_stabilimento), ENT_QUOTES,$default_charset);

            $dati_immagine = self::getImmagineContatto($id);
            $immagine = $dati_immagine["immagine"];

            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "cognome" => $cognome,
                                "immagine" => $immagine,
                                "nome_azienda" => $nome_azienda,
                                "nome_stabilimento" => $nome_stabilimento); 

        }

        return $result;

    }

    static function getDatiOrganigramma($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    org.kporganigrammiid id,
                    org.kp_nome_organigramma nome,
                    org.kp_azienda azienda,
                    org.kp_numero_revisione numero_revisione,
                    org.kp_revisione_di revisione_di,
                    org.kp_stato_organigramma stato_organigramma,
                    org.kp_disegnato_da disegnato_da,
                    ent.smownerid assegnatario,
                    org.description description,
                    acc.accountname nome_azienda
                    FROM {$table_prefix}_kporganigrammi org
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = org.kporganigrammiid
                    INNER JOIN {$table_prefix}_account acc ON acc.accountid = org.kp_azienda
                    WHERE org.kporganigrammiid = ".$id;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $id = $adb->query_result($result_query, 0, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES,$default_charset);

            $nome = $adb->query_result($result_query, 0, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES,$default_charset);

            $azienda = $adb->query_result($result_query, 0, 'azienda');
            $azienda = html_entity_decode(strip_tags($azienda), ENT_QUOTES,$default_charset);

            $assegnatario = $adb->query_result($result_query, 0, 'assegnatario');
            $assegnatario = html_entity_decode(strip_tags($assegnatario), ENT_QUOTES,$default_charset);

            $nome_azienda = $adb->query_result($result_query, 0, 'nome_azienda');
            $nome_azienda = html_entity_decode(strip_tags($nome_azienda), ENT_QUOTES,$default_charset);

            $descrizione = $adb->query_result($result_query, 0, 'description');
            $descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES,$default_charset);

            $disegnato_da = $adb->query_result($result_query, 0, 'disegnato_da');
            $disegnato_da = html_entity_decode(strip_tags($disegnato_da), ENT_QUOTES,$default_charset);

            $numero_revisione = $adb->query_result($result_query, 0, 'numero_revisione');
            $numero_revisione = html_entity_decode(strip_tags($numero_revisione), ENT_QUOTES,$default_charset);
            if( $numero_revisione == null || $numero_revisione == "" ){
                $numero_revisione = 0;
            }

            $revisione_di = $adb->query_result($result_query, 0, 'revisione_di');
            $revisione_di = html_entity_decode(strip_tags($revisione_di), ENT_QUOTES,$default_charset);
            if( $revisione_di == null || $revisione_di == "" ){
                $revisione_di = 0;
            }

            $stato_organigramma = $adb->query_result($result_query, 0, 'stato_organigramma');
            $stato_organigramma = html_entity_decode(strip_tags($stato_organigramma), ENT_QUOTES,$default_charset);

            $settings_procedure = self::getSettingsProcedure();

            $richiedi_approvazione = $settings_procedure["richiedi_approvazione"];

        }
        else{
            
            $id = 0;
            $nome = "";
            $azienda = 0;
            $assegnatario = 1;
            $nome_azienda = "";
            $descrizione = "";
            $numero_revisione = 0;
            $revisione_di = 0;
            $stato_organigramma = "";
            $richiedi_approvazione = "0";
            $disegnato_da = "";

        }

        $result = array("id" => $id,
                        "nome" => $nome,
                        "azienda" => $azienda,
                        "nome_azienda" => $nome_azienda,
                        "assegnatario" => $assegnatario,
                        "numero_revisione" => $numero_revisione,
                        "revisione_di" => $revisione_di,
                        "stato_organigramma" => $stato_organigramma,
                        "richiedi_approvazione" => $richiedi_approvazione,
                        "disegnato_da" => $disegnato_da,
                        "descrizione" => $descrizione);

        return $result;

    }

    static function getRuoli($organigramma, $filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();
        
        $dati_organigramma = self::getDatiOrganigramma($organigramma);

        $query = "SELECT 
                    ruol.kpruoliid id,
                    ruol.kp_nome_ruolo nome
                    FROM {$table_prefix}_kpruoli ruol
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = ruol.kpruoliid
                    WHERE ent.deleted = 0";

        if( $filtro["nome"] != "" ){
            $query .= " AND ruol.kp_nome_ruolo LIKE '%".$filtro["nome"]."%'";
        }

        $query .= " ORDER BY ruol.kp_nome_ruolo ASC";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i=0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES,$default_charset);

            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES,$default_charset);

            $result[] = array("id" => $id,
                                "nome" => $nome); 

        }

        return $result;

    }

    static function setOrganigramma($record, $elementi_organigramma){
        global $adb, $table_prefix, $default_charset, $current_user;

        $update = "UPDATE {$table_prefix}_kpentitaorganigrammi SET
                    kp_aggiornato = '0'
                    WHERE kp_organigramma = ".$record;
        $adb->query($update);

        foreach($elementi_organigramma as $elemento){
            
            self::setElementoOrganigramma($record, $elemento);

        }

        $update = "UPDATE {$table_prefix}_kpentitaorganigrammi entorg
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = entorg.kpentitaorganigrammiid
                    SET 
                    ent.deleted = 1
                    WHERE entorg.kp_aggiornato = '0' AND entorg.kp_organigramma = ".$record;
        $adb->query($update);

    }

    static function setElementoOrganigramma($record, $elemento){
        global $adb, $table_prefix, $default_charset, $current_user;

        //printf("\nSet Element %s: ", $elemento["id"]);

        $check_elemento = self::checkElementoOrganigramma($record, $elemento);

        if( $check_elemento["esiste"] ){

            //printf("Start Update Element");

            self::updateElementoOrganigramma($check_elemento["id"], $record, $elemento);

            //printf(" --> End Update Element");

        }
        else{

            //printf("Start Insert Element");

            self::insertElementoOrganigramma($record, $elemento);

            //printf(" --> End Insert Element");

        }

    }

    static function checkElementoOrganigramma($record, $elemento){
        global $adb, $table_prefix, $default_charset, $current_user;

        $esiste = false;
        $id = 0;

        $result = "";

        if( $elemento["id_crm"] != null && $elemento["id_crm"] != "" ){

            $id = $elemento["id_crm"];
            $esiste = true;

        }
        else{

            $query = "SELECT 
                        entorg.kpentitaorganigrammiid id
                        FROM {$table_prefix}_kpentitaorganigrammi entorg
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = entorg.kpentitaorganigrammiid
                        WHERE ent.deleted = 0 AND entorg.kp_id_creator = '".$elemento["id"]."' AND entorg.kp_organigramma = ".$record;
            
            $result_query = $adb->query($query);
            $num_result = $adb->num_rows($result_query);

            if( $num_result > 0 ){

                $id = $adb->query_result($result_query, 0, 'id');
                $id = html_entity_decode(strip_tags($id), ENT_QUOTES,$default_charset);

                $esiste = true;

            }
            else{

                $id = 0;
                $esiste = false;

            }
            
        }

        $result = array("esiste" => $esiste,
                        "id" => $id);

        return $result;

    }

    static function checkElementoByOrganigrammaId($record, $org_id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $esiste = false;
        $id = 0;

        $result = "";

        $query = "SELECT 
                    entorg.kpentitaorganigrammiid id
                    FROM {$table_prefix}_kpentitaorganigrammi entorg
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = entorg.kpentitaorganigrammiid
                    WHERE ent.deleted = 0 AND entorg.kp_id_creator = '".$org_id."' AND entorg.kp_organigramma = ".$record;
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $id = $adb->query_result($result_query, 0, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES,$default_charset);

            $esiste = true;

        }
        else{

            $id = 0;
            $esiste = false;

        }

        $result = array("esiste" => $esiste,
                        "id" => $id);

        return $result;

    }

    static function insertElementoOrganigramma($organigramma, $elemento){
        global $adb, $table_prefix, $default_charset, $current_user;

        $nome_entita = self::getNomeEntitaOrganigrammi($elemento);
        
        if($elemento["subordinato_a"] != null && $elemento["subordinato_a"] != ""){ 
            $dati_subordinato_a = self::checkElementoByOrganigrammaId($organigramma, $elemento["subordinato_a"]);
            $subordinato_a = $dati_subordinato_a["id"];
        }
        else{
            $subordinato_a = 0;
        }

        $focus = CRMEntity::getInstance('KpEntitaOrganigrammi');
        $focus->column_fields['assigned_user_id'] = $current_user->id;
        $focus->column_fields['kp_nome_entita'] = $nome_entita;
        if($elemento["risorsa"] != null && $elemento["risorsa"] != "" && $elemento["risorsa"] != 0){ 
            $focus->column_fields['kp_risorsa'] = $elemento["risorsa"];
        }
        if($elemento["ruolo"] != null && $elemento["ruolo"] != "" && $elemento["ruolo"] != 0){ 
            $focus->column_fields['kp_ruolo'] = $elemento["ruolo"];
        }
        $focus->column_fields['kp_organigramma'] = $organigramma;
        $focus->column_fields['kp_subordinato_a'] = $subordinato_a;
        if( $elemento["staff"] ){
            $focus->column_fields['kp_in_staff'] = "1";
        }
        else{
            $focus->column_fields['kp_in_staff'] = "0";
        }
        $focus->column_fields['kp_colore'] = $elemento["colore"];

        if($elemento["open"] == true || $elemento["open"] == 'true' || $elemento["open"] == 1){
            $focus->column_fields['kp_chiuso'] = '0';
        }
        else{
            $focus->column_fields['kp_chiuso'] = '1';
        }
        
        if($elemento["dir"] == "vertical"){
            $focus->column_fields['kp_verticale'] = '1';
        }
        else{
            $focus->column_fields['kp_verticale'] = '0';
        }

        $focus->column_fields['kp_id_creator'] = $elemento["id"];
        $focus->column_fields['kp_aggiornato'] = '1';
        $focus->save('KpEntitaOrganigrammi', $longdesc=true, $offline_update=false, $triggerEvent=false); 

        $nuovo_id = $focus->id;

        return $nuovo_id;

    }

    static function updateElementoOrganigramma($id, $organigramma, $elemento){
        global $adb, $table_prefix, $default_charset, $current_user;

        $nome_entita = self::getNomeEntitaOrganigrammi($elemento);
        
        if($elemento["subordinato_a"] != null && $elemento["subordinato_a"] != ""){ 
            $dati_subordinato_a = self::checkElementoByOrganigrammaId($organigramma, $elemento["subordinato_a"]);
            $subordinato_a = $dati_subordinato_a["id"];
        }
        else{
            $subordinato_a = 0;
        }

        $focus = CRMEntity::getInstance('KpEntitaOrganigrammi');
        $focus->retrieve_entity_info($id, "KpEntitaOrganigrammi");
        $focus->column_fields['assigned_user_id'] = $current_user->id;
        $focus->column_fields['kp_nome_entita'] = $nome_entita;
        $focus->column_fields['kp_risorsa'] = $elemento["risorsa"];
        $focus->column_fields['kp_ruolo'] = $elemento["ruolo"];
        $focus->column_fields['kp_subordinato_a'] = $subordinato_a;
        if( $elemento["staff"] ){
            $focus->column_fields['kp_in_staff'] = "1";
        }
        else{
            $focus->column_fields['kp_in_staff'] = "0";
        }
        $focus->column_fields['kp_colore'] = $elemento["colore"];

        if($elemento["open"] == true || $elemento["open"] == 'true' || $elemento["open"] == 1){
            $focus->column_fields['kp_chiuso'] = '0';
        }
        else{
            $focus->column_fields['kp_chiuso'] = '1';
        }
        
        if($elemento["dir"] == "vertical"){
            $focus->column_fields['kp_verticale'] = '1';
        }
        else{
            $focus->column_fields['kp_verticale'] = '0';
        }

        $focus->column_fields['kp_aggiornato'] = '1';
        $focus->mode = 'edit';
        $focus->id = $id;
        $focus->save('KpEntitaOrganigrammi', $longdesc=true, $offline_update=false, $triggerEvent=false); 

        return $id;

    }

    static function getDatiRisorsa($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    cont.contactid id,
                    cont.firstname nome,
                    cont.lastname cognome
                    FROM {$table_prefix}_contactdetails cont
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = cont.contactid
                    WHERE ent.deleted = 0 AND cont.contactid = ".$id;
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0){

            $nome = $adb->query_result($result_query, 0, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES,$default_charset);

            $cognome = $adb->query_result($result_query, 0, 'cognome');
            $cognome = html_entity_decode(strip_tags($cognome), ENT_QUOTES,$default_charset);

        }
        else{

            $nome = "";
            $cognome = "";

        }

        $result = array("nome" => $nome,
                        "cognome" => $cognome); 

        return $result;

    }

    static function getDatiRuolo($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    ruol.kpruoliid id,
                    ruol.kp_nome_ruolo nome
                    FROM {$table_prefix}_kpruoli ruol
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = ruol.kpruoliid
                    WHERE ent.deleted = 0 AND ruol.kpruoliid = ".$id;
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0){

            $nome = $adb->query_result($result_query, 0, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES,$default_charset);

        }
        else{

            $nome = "";

        }

        $result = array("nome" => $nome); 

        return $result;

    }

    static function getNomeEntitaOrganigrammi($elemento){
        global $adb, $table_prefix, $default_charset, $current_user;

        $nome_entita = "";

        if($elemento["risorsa"] != null && $elemento["risorsa"] != "" && $elemento["risorsa"] != 0){ 
            $dati_risorsa = self::getDatiRisorsa( $elemento["risorsa"] );
            $nome_risorsa = $dati_risorsa["cognome"]." ".$dati_risorsa["nome"];
        }
        else{
            $nome_risorsa = "";
        }

        if($elemento["ruolo"] != null && $elemento["ruolo"] != "" && $elemento["ruolo"] != 0){ 
            $dati_ruolo = self::getDatiRuolo( $elemento["ruolo"] );
            $nome_ruolo = $dati_ruolo["nome"];
        }
        else{
            $nome_ruolo = "";
        }

        if($nome_risorsa != "" && $nome_ruolo != ""){
            $nome_entita = $nome_risorsa." - ".$nome_ruolo;
        }
        elseif($nome_risorsa != "" && $nome_ruolo == ""){
            $nome_entita = $nome_risorsa;
        }
        elseif($nome_risorsa == "" && $nome_ruolo != ""){
            $nome_entita = $nome_ruolo;
        }
        else{
            $nome_entita = $elemento["id"];
        }

        return $nome_entita;

    }

    static function getOrganigramma($id){
        global $adb, $table_prefix, $default_charset, $current_user;
        
        $result = array();

        $entita_padri = self::getEntitaOrganigrammaPadri($id);

        foreach($entita_padri as $entita_padre){

            $entita_figlie = self::getEntitaOrganigrammaFiglie($id, $entita_padre["id"]);

            if($entita_padre["risorsa"] != "" && $entita_padre["risorsa"] != 0){
                $dati_immagine = self::getImmagineContatto($entita_padre["risorsa"]);
                $immagine = $dati_immagine["immagine"];
            }
            else{
                $immagine = "";
            }

            $result[] = array("id" => $entita_padre["id"],
                                "nome" => $entita_padre["nome"],
                                "risorsa" => $entita_padre["risorsa"],
                                "ruolo" => $entita_padre["ruolo"],
                                "subordinato_a" => 0,
                                "subordinato_a_org_id" => "",
                                "in_staff" => $entita_padre["in_staff"],
                                "colore" => $entita_padre["colore"],
                                "chiuso" => $entita_padre["chiuso"],
                                "verticale" => $entita_padre["verticale"],
                                "id_creator" => $entita_padre["id_creator"],
                                "nome_risorsa" => $entita_padre["nome_risorsa"],
                                "cognome_risorsa" => $entita_padre["cognome_risorsa"],
                                "nome_ruolo" => $entita_padre["nome_ruolo"],
                                "immagine" => $immagine,
                                "entita_figlie" => $entita_figlie); 

        }

        return $result;

    }

    static function getEntitaOrganigrammaPadri($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    entorg.kpentitaorganigrammiid id,
                    entorg.kp_nome_entita nome,
                    entorg.kp_risorsa risorsa,
                    entorg.kp_ruolo ruolo,
                    entorg.kp_subordinato_a subordinato_a,
                    entorg.kp_in_staff in_staff,
                    entorg.kp_colore colore,
                    entorg.kp_chiuso chiuso,
                    entorg.kp_verticale verticale,
                    entorg.kp_id_creator id_creator,
                    cont.firstname nome_risorsa,
                    cont.lastname cognome_risorsa,
                    ruol.kp_nome_ruolo nome_ruolo
                    FROM {$table_prefix}_kpentitaorganigrammi entorg
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = entorg.kpentitaorganigrammiid
                    LEFT JOIN {$table_prefix}_contactdetails cont ON cont.contactid = entorg.kp_risorsa
                    LEFT JOIN {$table_prefix}_kpruoli ruol ON ruol.kpruoliid = entorg.kp_ruolo
                    WHERE ent.deleted = 0 AND (entorg.kp_subordinato_a IS NULL OR entorg.kp_subordinato_a = '' OR entorg.kp_subordinato_a = 0) AND entorg.kp_organigramma = ".$id;
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i=0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES,$default_charset);

            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES,$default_charset);

            $risorsa = $adb->query_result($result_query, $i, 'risorsa');
            $risorsa = html_entity_decode(strip_tags($risorsa), ENT_QUOTES,$default_charset);

            $ruolo = $adb->query_result($result_query, $i, 'ruolo');
            $ruolo = html_entity_decode(strip_tags($ruolo), ENT_QUOTES,$default_charset);

            $subordinato_a = $adb->query_result($result_query, $i, 'subordinato_a');
            $subordinato_a = html_entity_decode(strip_tags($subordinato_a), ENT_QUOTES,$default_charset);
            if($subordinato_a == null || $subordinato_a == ""){
                $subordinato_a = 0;
                $subordinato_a_ord_id = "";
            }
            else{
                $subordinato_a_ord_id = self::getSubordinatoOrdId($subordinato_a);
            }

            $in_staff = $adb->query_result($result_query, $i, 'in_staff');
            $in_staff = html_entity_decode(strip_tags($in_staff), ENT_QUOTES,$default_charset);

            $colore = $adb->query_result($result_query, $i, 'colore');
            $colore = html_entity_decode(strip_tags($colore), ENT_QUOTES,$default_charset);

            $chiuso = $adb->query_result($result_query, $i, 'chiuso');
            $chiuso = html_entity_decode(strip_tags($chiuso), ENT_QUOTES,$default_charset);

            $verticale = $adb->query_result($result_query, $i, 'verticale');
            $verticale = html_entity_decode(strip_tags($verticale), ENT_QUOTES,$default_charset);

            $id_creator = $adb->query_result($result_query, $i, 'id_creator');
            $id_creator = html_entity_decode(strip_tags($id_creator), ENT_QUOTES,$default_charset);

            $nome_risorsa = $adb->query_result($result_query, $i, 'nome_risorsa');
            $nome_risorsa = html_entity_decode(strip_tags($nome_risorsa), ENT_QUOTES,$default_charset);

            $cognome_risorsa = $adb->query_result($result_query, $i, 'cognome_risorsa');
            $cognome_risorsa = html_entity_decode(strip_tags($cognome_risorsa), ENT_QUOTES,$default_charset);

            $nome_ruolo = $adb->query_result($result_query, $i, 'nome_ruolo');
            $nome_ruolo = html_entity_decode(strip_tags($nome_ruolo), ENT_QUOTES,$default_charset);

            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "risorsa" => $risorsa,
                                "ruolo" => $ruolo,
                                "subordinato_a" => $subordinato_a,
                                "subordinato_a_org_id" => $subordinato_a_ord_id,
                                "in_staff" => $in_staff,
                                "colore" => $colore,
                                "chiuso" => $chiuso,
                                "verticale" => $verticale,
                                "id_creator" => $id_creator,
                                "nome_risorsa" => $nome_risorsa,
                                "cognome_risorsa" => $cognome_risorsa,
                                "nome_ruolo" => $nome_ruolo); 

        }

        return $result;

    }

    static function getEntitaOrganigrammaFiglie($organigramma, $padre){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    entorg.kpentitaorganigrammiid id,
                    entorg.kp_nome_entita nome,
                    entorg.kp_risorsa risorsa,
                    entorg.kp_ruolo ruolo,
                    entorg.kp_subordinato_a subordinato_a,
                    entorg.kp_in_staff in_staff,
                    entorg.kp_colore colore,
                    entorg.kp_chiuso chiuso,
                    entorg.kp_verticale verticale,
                    entorg.kp_id_creator id_creator,
                    cont.firstname nome_risorsa,
                    cont.lastname cognome_risorsa,
                    ruol.kp_nome_ruolo nome_ruolo
                    FROM {$table_prefix}_kpentitaorganigrammi entorg
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = entorg.kpentitaorganigrammiid
                    LEFT JOIN {$table_prefix}_contactdetails cont ON cont.contactid = entorg.kp_risorsa
                    LEFT JOIN {$table_prefix}_kpruoli ruol ON ruol.kpruoliid = entorg.kp_ruolo
                    WHERE ent.deleted = 0 AND entorg.kp_subordinato_a = ".$padre." AND entorg.kp_organigramma = ".$organigramma;
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i=0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES,$default_charset);

            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES,$default_charset);

            $risorsa = $adb->query_result($result_query, $i, 'risorsa');
            $risorsa = html_entity_decode(strip_tags($risorsa), ENT_QUOTES,$default_charset);

            $ruolo = $adb->query_result($result_query, $i, 'ruolo');
            $ruolo = html_entity_decode(strip_tags($ruolo), ENT_QUOTES,$default_charset);

            $subordinato_a = $adb->query_result($result_query, $i, 'subordinato_a');
            $subordinato_a = html_entity_decode(strip_tags($subordinato_a), ENT_QUOTES,$default_charset);
            if($subordinato_a == null || $subordinato_a == ""){
                $subordinato_a = 0;
                $subordinato_a_ord_id = "";
            }
            else{
                $subordinato_a_ord_id = self::getSubordinatoOrdId($subordinato_a);
            }

            $in_staff = $adb->query_result($result_query, $i, 'in_staff');
            $in_staff = html_entity_decode(strip_tags($in_staff), ENT_QUOTES,$default_charset);

            $colore = $adb->query_result($result_query, $i, 'colore');
            $colore = html_entity_decode(strip_tags($colore), ENT_QUOTES,$default_charset);

            $chiuso = $adb->query_result($result_query, $i, 'chiuso');
            $chiuso = html_entity_decode(strip_tags($chiuso), ENT_QUOTES,$default_charset);

            $verticale = $adb->query_result($result_query, $i, 'verticale');
            $verticale = html_entity_decode(strip_tags($verticale), ENT_QUOTES,$default_charset);

            $id_creator = $adb->query_result($result_query, $i, 'id_creator');
            $id_creator = html_entity_decode(strip_tags($id_creator), ENT_QUOTES,$default_charset);

            $nome_risorsa = $adb->query_result($result_query, $i, 'nome_risorsa');
            $nome_risorsa = html_entity_decode(strip_tags($nome_risorsa), ENT_QUOTES,$default_charset);

            $cognome_risorsa = $adb->query_result($result_query, $i, 'cognome_risorsa');
            $cognome_risorsa = html_entity_decode(strip_tags($cognome_risorsa), ENT_QUOTES,$default_charset);

            $nome_ruolo = $adb->query_result($result_query, $i, 'nome_ruolo');
            $nome_ruolo = html_entity_decode(strip_tags($nome_ruolo), ENT_QUOTES,$default_charset);

            $entita_figlie = self::getEntitaOrganigrammaFiglie($organigramma, $id);

            if($risorsa != "" && $risorsa != 0){
                $dati_immagine = self::getImmagineContatto($risorsa);
                $immagine = $dati_immagine["immagine"];
            }
            else{
                $immagine = "";
            }

            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "risorsa" => $risorsa,
                                "ruolo" => $ruolo,
                                "subordinato_a" => $subordinato_a,
                                "subordinato_a_org_id" => $subordinato_a_ord_id,
                                "in_staff" => $in_staff,
                                "colore" => $colore,
                                "chiuso" => $chiuso,
                                "verticale" => $verticale,
                                "id_creator" => $id_creator,
                                "nome_risorsa" => $nome_risorsa,
                                "cognome_risorsa" => $cognome_risorsa,
                                "nome_ruolo" => $nome_ruolo,
                                "immagine" => $immagine,
                                "entita_figlie" => $entita_figlie); 

        }

        return $result;

    }

    static function getImmagineContatto($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    rel.attachmentsid id,
                    att.name name,
                    att.path path,
                    att.type type
                    FROM {$table_prefix}_seattachmentsrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.attachmentsid 
                    INNER JOIN {$table_prefix}_attachments att ON att.attachmentsid = rel.attachmentsid 
                    LEFT JOIN {$table_prefix}_contactdetails cont ON cont.contactid = rel.crmid 
                    WHERE ent.deleted = 0 AND ent.setype = 'Contacts Image' AND rel.crmid = ".$id;
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $esiste = true;

            $id = $adb->query_result($result_query, 0, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES,$default_charset);

            $name = $adb->query_result($result_query, 0, 'name');
            $name = html_entity_decode(strip_tags($name), ENT_QUOTES,$default_charset);

            $path = $adb->query_result($result_query, 0, 'path');
            $path = html_entity_decode(strip_tags($path), ENT_QUOTES,$default_charset);

            $type = $adb->query_result($result_query, 0, 'type');
            $type = html_entity_decode(strip_tags($type), ENT_QUOTES,$default_charset);

            $immagine = $path.$id."_".$name;

        }
        else{

            $esiste = false;

            $id = 0;
            $name = "";
            $path = "";
            $type = "";
            $immagine = "";

        }

        $result = array("esiste" => $esiste,
                        "id" => $id,
                        "name" => $name,
                        "path" => $path,
                        "immagine" => $immagine);

        return $result;

    }

    static function getSubordinatoOrdId($subordinato_a){
        global $adb, $table_prefix, $default_charset, $current_user;

        $query = "SELECT 
                    entorg.kp_id_creator id_creator
                    FROM {$table_prefix}_kpentitaorganigrammi entorg
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = entorg.kpentitaorganigrammiid
                    WHERE ent.deleted = 0 AND entorg.kpentitaorganigrammiid = ".$subordinato_a;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $id_creator = $adb->query_result($result_query, 0, 'id_creator');
            $id_creator = html_entity_decode(strip_tags($id_creator), ENT_QUOTES,$default_charset);

        }
        else{

            $id_creator = "";

        }

        return $id_creator;

    }

    static function setLinksEntitaOrganigramma($organigramma, $links){
        global $adb, $table_prefix, $default_charset, $current_user;

        foreach($links as $link){
            
            self::setLinkEntitaOrganigramma($organigramma, $link);

        }

    }

    static function setLinkEntitaOrganigramma($record, $link){
        global $adb, $table_prefix, $default_charset, $current_user;

        //printf("\nSet Link Element %s: ", $link["to"]);

        $check_elemento = self::checkElementoByOrganigrammaId($record, $link["to"]);
        
        if( $check_elemento["esiste"] ){

            //printf("Start Update Link Element");

            self::updateLinkElementoOrganigramma($check_elemento["id"], $record, $link);

            //printf(" --> End Update Link Element");

        }

    }

    static function updateLinkElementoOrganigramma($id, $organigramma, $link){
        global $adb, $table_prefix, $default_charset, $current_user;
        
        if($link["from"] != null && $link["from"] != ""){ 
            $dati_subordinato_a = self::checkElementoByOrganigrammaId($organigramma, $link["from"]);
            $subordinato_a = $dati_subordinato_a["id"];
        }
        else{
            $subordinato_a = 0;
        }

        $update = "UPDATE {$table_prefix}_kpentitaorganigrammi SET
                    kp_subordinato_a = ".$subordinato_a."
                    WHERE kpentitaorganigrammiid = ".$id;
        $adb->query($update);

    }

    static function getAlberoOrganigrammi($filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $lista_aziende = self::getListaAziende($filtro["azienda"], $filtro["stabilimento"]);

        foreach( $lista_aziende as $azienda ){

            $filtro_organigrammi= array("nome_organigramma" => "",
                                        "azienda" => $azienda["id"],
                                        "stabilimento" => $filtro["stabilimento"]);

            $lista_organigrammi = self::getOrganigrammi($filtro_organigrammi);

            $result[] = array("id" => $azienda["id"],
                                "nome" => $azienda["nome"],
                                "lista_organigrammi" => $lista_organigrammi);

        }

        return $result;

    }

    static function getListaAziende($azienda, $stabilimento){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    org.kp_azienda id,
                    acc.accountname nome
                    FROM {$table_prefix}_kporganigrammi org
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = org.kporganigrammiid
                    INNER JOIN {$table_prefix}_account acc ON acc.accountid = org.kp_azienda
                    WHERE ent.deleted = 0";
					
		if($azienda != 0){
            $query .= " AND org.kp_azienda = ".$azienda;
        }

        $query .= " GROUP BY org.kp_azienda
                    ORDER BY acc.accountname ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i=0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES,$default_charset);

            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES,$default_charset);

            $result[] = array("id" => $id,
                                "nome" => $nome);

        }

        return $result;

    }

    static function getOrganigrammi($filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $query = "SELECT 
                    org.kporganigrammiid id,
                    org.kp_nome_organigramma nome,
                    org.kp_azienda azienda,
                    acc.accountname nome_azienda
                    FROM {$table_prefix}_kporganigrammi org
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = org.kporganigrammiid
                    INNER JOIN {$table_prefix}_account acc ON acc.accountid = org.kp_azienda
                    WHERE ent.deleted = 0";

        $query .= " AND org.kp_stato_organigramma = 'Attivo'";

        if( $filtro["azienda"] != "" ){

            $query .= " AND org.kp_azienda = ".$filtro["azienda"];

        }

        $query .= " ORDER BY org.kp_nome_organigramma ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i=0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES,$default_charset);

            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES,$default_charset);

            $result[] = array("id" => $id,
                                "nome" => $nome);

        }

        return $result;


    }

    static function getProcessiUnitaOrganizzativa($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $focus_unit_organizzativa = CRMEntity::getInstance('KpEntitaOrganigrammi');
        $focus_unit_organizzativa->retrieve_entity_info($id, "KpEntitaOrganigrammi", $dieOnError=false); 

        $ruolo = $focus_unit_organizzativa->column_fields["kp_ruolo"];
        $organigramma = $focus_unit_organizzativa->column_fields["kp_organigramma"];

        $focus_organigramma = CRMEntity::getInstance('KpOrganigrammi');
        $focus_organigramma->retrieve_entity_info($organigramma, "KpOrganigrammi", $dieOnError=false); 

        $azienda = $focus_organigramma->column_fields["kp_azienda"];
        
        $lista_processi_azienda = KpBPMN::getProcessiAzienda($azienda, false);

        foreach($lista_processi_azienda as $processo){

            $lista_task = KpBPMN::getElementiProceduraRuolo($processo["id"], $ruolo);

            if( count($lista_task) > 0 ){

                $result[] = array("processo" => $processo["id"],
                                    "nome_processo" => $processo["nome"],
                                    "lista_attivita" => $lista_task);

            }

        }

        return $result;

    }

    static function setRevisione($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $dati_organigramma = self::getDatiOrganigramma($id);

        $focus = CRMEntity::getInstance('KpOrganigrammi');
        $focus->column_fields['assigned_user_id'] = $current_user->id;
        $focus->column_fields['kp_nome_organigramma'] = $dati_organigramma["nome"];
        $focus->column_fields['kp_azienda'] = $dati_organigramma["azienda"];
        $focus->column_fields['kp_data_organigramma'] = date("Y-m-d");
        $focus->column_fields['kp_numero_revisione'] = (int)$dati_organigramma["numero_revisione"] + 1;
        if( $dati_organigramma["descrizione"] != "" ){
            $focus->column_fields['description'] = $dati_organigramma["descrizione"];
        }
        $focus->column_fields['kp_revisione_di'] = $id;
        $focus->column_fields['kp_stato_organigramma'] = 'In sviluppo';
        $focus->save('KpOrganigrammi', $longdesc = true, $offline_update = false, $triggerEvent = false);

        self::riportaRelazioniDocumenti($id, $focus->id);

        $lista_elementi = self::getElementiOrganigramma($id, array());
        
        foreach($lista_elementi as $elemento){

            self::dupplicaElementoOrganigramma($elemento, $focus->id);

        }

        self::setLinksEntitaOrganigrammaRevisione($id, $focus->id);
        
        $result = $focus->id;

        return $result;

    }

    static function setLinksEntitaOrganigrammaRevisione($organigramma_originale, $organigramma_revisione){
        global $adb, $table_prefix, $default_charset, $current_user;

        $lista_elementi = self::getElementiOrganigramma($organigramma_revisione, array());

        foreach($lista_elementi as $elemento){
            
            $cor_elemento_originale = self::getElementoByIdcreator($elemento["id_creator"], $organigramma_originale);
            
            if( $cor_elemento_originale["esiste"] ){
                
                $crmid_subbordinato_originale = $cor_elemento_originale["subordinato_a"];

                $id_creator_subbordinato_originale = self::getSubordinatoOrdId($crmid_subbordinato_originale);
                
                if( $id_creator_subbordinato_originale != "" ){

                    $cor_elemento_revisione = self::getElementoByIdcreator($id_creator_subbordinato_originale, $organigramma_revisione);
                    
                    if( $cor_elemento_revisione["esiste"] ){

                        $focus = CRMEntity::getInstance('KpEntitaOrganigrammi');
                        $focus->retrieve_entity_info($elemento["id"], "KpEntitaOrganigrammi");
                        $focus->column_fields['kp_subordinato_a'] = $cor_elemento_revisione["id"];
                        $focus->mode = 'edit';
                        $focus->id = $elemento["id"];
                        $focus->save('KpEntitaOrganigrammi', $longdesc=true, $offline_update=false, $triggerEvent=false); 

                    }

                }

            }

        }

    }

    static function getElementoByIdcreator($id_creator, $organigramma){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    entorg.kpentitaorganigrammiid id,
                    entorg.kp_subordinato_a subordinato_a
                    FROM {$table_prefix}_kpentitaorganigrammi entorg
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = entorg.kpentitaorganigrammiid
                    WHERE ent.deleted = 0 AND entorg.kp_id_creator = '".$id_creator."' AND entorg.kp_organigramma = ".$organigramma;
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $id = $adb->query_result($result_query, 0, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $subordinato_a = $adb->query_result($result_query, 0, 'subordinato_a');
            $subordinato_a = html_entity_decode(strip_tags($subordinato_a), ENT_QUOTES, $default_charset);

            $esiste = true;

        }
        else{

            $id = 0;
            $subordinato_a = 0;
            $esiste = false;

        }

        $result = array("esiste" => $esiste,
                        "id" => $id,
                        "subordinato_a" => $subordinato_a);

        return $result;

    }

    static function dupplicaElementoOrganigramma($elemento, $organigramma){
        global $adb, $table_prefix, $default_charset, $current_user;

        $focus = CRMEntity::getInstance('KpEntitaOrganigrammi');
        $focus->column_fields['assigned_user_id'] = $current_user->id;
        $focus->column_fields['kp_nome_entita'] = $elemento["nome"];
        if( $elemento["risorsa"] != 0){
            $focus->column_fields['kp_risorsa'] = $elemento["risorsa"];
        }
        if( $elemento["ruolo"] != 0){
            $focus->column_fields['kp_ruolo'] = $elemento["ruolo"];
        }
        $focus->column_fields['kp_organigramma'] = $organigramma;
        $focus->column_fields['kp_in_staff'] = $elemento["in_staff"];
        $focus->column_fields['kp_colore'] = $elemento["colore"];
        $focus->column_fields['kp_chiuso'] = $elemento["chiuso"];
        $focus->column_fields['kp_verticale'] = $elemento["verticale"];
        $focus->column_fields['kp_id_creator'] = $elemento["id_creator"];
        if($elemento["descrizione"] != ""){
            $focus->column_fields['description'] = $elemento["descrizione"];
        }

        $focus->save('KpEntitaOrganigrammi', $longdesc = true, $offline_update = false, $triggerEvent = false);

        self::riportaRelazioniDocumenti($id, $focus->id);

    }

    static function getElementiOrganigramma($id, $filtro = array()){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    entorg.kpentitaorganigrammiid id,
                    entorg.kp_nome_entita nome,
                    entorg.kp_risorsa risorsa,
                    entorg.kp_ruolo ruolo,
                    entorg.kp_subordinato_a subordinato_a,
                    entorg.kp_in_staff in_staff,
                    entorg.kp_colore colore,
                    entorg.kp_chiuso chiuso,
                    entorg.kp_verticale verticale,
                    entorg.kp_id_creator id_creator,
                    entorg.description description
                    FROM {$table_prefix}_kpentitaorganigrammi entorg
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = entorg.kpentitaorganigrammiid
                    WHERE ent.deleted = 0 AND entorg.kp_organigramma = ".$id;

        $query .= " ORDER BY entorg.kp_id_creator ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i=0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $risorsa = $adb->query_result($result_query, $i, 'risorsa');
            $risorsa = html_entity_decode(strip_tags($risorsa), ENT_QUOTES, $default_charset);
            if( $risorsa == "" || $risorsa == null ){
                $risorsa = 0;
            }

            $ruolo = $adb->query_result($result_query, $i, 'ruolo');
            $ruolo = html_entity_decode(strip_tags($ruolo), ENT_QUOTES, $default_charset);
            if( $ruolo == "" || $ruolo == null ){
                $ruolo = 0;
            }

            $subordinato_a = $adb->query_result($result_query, $i, 'subordinato_a');
            $subordinato_a = html_entity_decode(strip_tags($subordinato_a), ENT_QUOTES, $default_charset);

            $in_staff = $adb->query_result($result_query, $i, 'in_staff');
            $in_staff = html_entity_decode(strip_tags($in_staff), ENT_QUOTES, $default_charset);

            $colore = $adb->query_result($result_query, $i, 'colore');
            $colore = html_entity_decode(strip_tags($colore), ENT_QUOTES, $default_charset);

            $chiuso = $adb->query_result($result_query, $i, 'chiuso');
            $chiuso = html_entity_decode(strip_tags($chiuso), ENT_QUOTES, $default_charset);

            $verticale = $adb->query_result($result_query, $i, 'verticale');
            $verticale = html_entity_decode(strip_tags($verticale), ENT_QUOTES, $default_charset);

            $id_creator = $adb->query_result($result_query, $i, 'id_creator');
            $id_creator = html_entity_decode(strip_tags($id_creator), ENT_QUOTES, $default_charset);

            $description = $adb->query_result($result_query, $i, 'description');
            $description = html_entity_decode(strip_tags($description), ENT_QUOTES, $default_charset);

            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "risorsa" => $risorsa,
                                "ruolo" => $ruolo,
                                "subordinato_a" => $subordinato_a,
                                "in_staff" => $in_staff,
                                "colore" => $colore,
                                "chiuso" => $chiuso,
                                "verticale" => $verticale,
                                "id_creator" => $id_creator,
                                "description" => $description);

        }

        return $result;

    }

    static function riportaRelazioniDocumenti($id_origine, $id_destinazione){
        global $adb, $table_prefix, $default_charset, $current_user;

        $lista_relazioni_documento = self::getListaDocumentiRelazionati($id_origine);

        foreach($lista_relazioni_documento as $relazione_documento){

            $query = "SELECT 
                        crmid
                        FROM {$table_prefix}_senotesrel
                        WHERE notesid = ".$relazione_documento['id']." AND crmid = ".$id_destinazione;

            $result_query = $adb->query($query);
            $num_result = $adb->num_rows($result_query);

            if($num_result == 0){

                $insert = "INSERT INTO {$table_prefix}_senotesrel (crmid, notesid, relmodule)
                            VALUES
                            (".$id_destinazione.", ".$relazione_documento['id'].", '".$relazione_documento['modulo']."')";

                $adb->query($insert);

            }

        }

    }

    static function getListaDocumentiRelazionati($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();
        
        $query = "SELECT 
                    notesid,
                    relmodule
                    FROM {$table_prefix}_senotesrel
                    WHERE crmid = ".$id;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);
        
        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, 'notesid');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $modulo = $adb->query_result($result_query, $i, 'relmodule');
            $modulo = html_entity_decode(strip_tags($modulo), ENT_QUOTES, $default_charset);

            $result[] = array("id" => $id,
                                "modulo" => $modulo);

        }
        
        return $result;

    }

    static function getSettingsProcedure(){
        global $adb, $table_prefix, $default_charset;

        $result = "";

        $richiedi_approvazione = "0";

        $query_verifica = "SHOW TABLES LIKE 'kp_settings_procedure'";

        $result_query_verifica = $adb->query($query_verifica);
        $num_result_verifica = $adb->num_rows($result_query_verifica);

        if( $num_result_verifica > 0 ){

            $query = "SELECT 
                        richiedi_approvazione 
                        FROM kp_settings_procedure";

            $result_query = $adb->query($query);
            $num_result = $adb->num_rows($result_query);
            if( $num_result > 0 ){

                $richiedi_approvazione = $adb->query_result($result_query, $i, 'richiedi_approvazione');
                $richiedi_approvazione = html_entity_decode(strip_tags($richiedi_approvazione), ENT_QUOTES,$default_charset);   
                if($richiedi_approvazione == "" || $richiedi_approvazione == null){
                    $richiedi_approvazione = "0";
                }

            }

        }

        $result = array("richiedi_approvazione" => $richiedi_approvazione);
  
        return $result;

    }

    static function gestioneNotificheRevisione($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $focus_revisione = CRMEntity::getInstance('KpResRevisioniOrg');
        $focus_revisione->retrieve_entity_info($id, "KpResRevisioniOrg", $dieOnError=false); 

        $organigramma = $focus_revisione->column_fields["kp_organigramma"];

        $risorse_da_notificare = self::getRisorseOrganigramma( $organigramma );
  
        foreach($risorse_da_notificare as $risorsa){

            self::generaNotificaRevisione($id, $risorsa["id"]);

        }

    }

    static function getRisorseOrganigramma($organigramma){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    entorg.kp_risorsa id
                    FROM {$table_prefix}_kpentitaorganigrammi entorg
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = entorg.kpentitaorganigrammiid
                    WHERE ent.deleted = 0 AND entorg.kp_organigramma = ".$organigramma."
                    GROUP BY entorg.kp_risorsa";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);
        
        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $result[] = array("id" => $id);

        }

        return $result;

    }

    static function getUtenteRisorsa($risorsa){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    id,
                    user_name,
                    first_name,
                    last_name
                    FROM {$table_prefix}_users
                    WHERE status = 'Active' AND risorsa_collegata = ".$risorsa;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0){

            $id = $adb->query_result($result_query, 0, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

        }
        else{

            $id = 0;

        }

        $result = array("id" => $id);

        return $result;

    }

    static function generaNotificaRevisione($revisione, $risorsa){
        global $adb, $table_prefix, $default_charset, $current_user;

        $dati_utente = self::getUtenteRisorsa($risorsa);

        if($dati_utente["id"] != 0 && $dati_utente["id"] != ""){

            $dati_notifica = self::getNotificaRevisioneRisorsa($revisione, $risorsa);
            if( !$dati_notifica["esiste"] ){

                $focus_revisione = CRMEntity::getInstance('KpResRevisioniOrg');
                $focus_revisione->retrieve_entity_info($revisione, "KpResRevisioniOrg"); 

                $focus_organigramma = CRMEntity::getInstance('KpOrganigrammi');
                $focus_organigramma->retrieve_entity_info($focus_revisione->column_fields["kp_organigramma"], "KpOrganigrammi"); 

                $numero_revisione = $focus_revisione->column_fields["kp_numero_revisione"];
                $numero_revisione_str = str_pad($numero_revisione, 3, "0", STR_PAD_LEFT);

                $nome_notifica = "Rev. ".$numero_revisione_str." - Organigramma: ".$focus_organigramma->column_fields["kp_nome_organigramma"];

                $focus = CRMEntity::getInstance('KpNotificheRevOrg');
                $focus->column_fields['assigned_user_id'] = $dati_utente["id"];
                $focus->column_fields['kp_soggetto'] = $nome_notifica;
                $focus->column_fields['kp_rev_organigramma'] = $revisione;
                $focus->column_fields['kp_risorsa'] = $risorsa;
                $focus->column_fields['kp_organigramma'] = $focus_revisione->column_fields["kp_organigramma"];
                $focus->column_fields['kp_data_notifica'] = $focus_revisione->column_fields["kp_data_revisione"];
                $focus->column_fields['description'] = $focus_revisione->column_fields["description"];
                $focus->column_fields['kp_stato_notifica_r'] = "Eseguita notifica";
                $focus->save('KpNotificheRevOrg', $longdesc = true, $offline_update = false, $triggerEvent = true);

            }

        }

    }

    static function getNotificaRevisioneRisorsa($revisione, $risorsa){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    notf.kpnotificherevorgid id
                    FROM {$table_prefix}_kpnotificherevorg notf
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = notf.kpnotificherevorgid
                    WHERE ent.deleted = 0 AND notf.kp_rev_organigramma = ".$revisione." AND notf.kp_risorsa = ".$risorsa;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0){

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

    static function creaRevisioneOrganigramma($organigramma, $descrizione){
        global $adb, $table_prefix, $default_charset, $current_user;

        $data_corrente = date("Y-m-d");

        $data_formattata = new DateTime($data_corrente);
        $data_formattata = $data_formattata->format('d/m/Y');

        $dati_nuovo_organigramma = self::getDatiOrganigramma($organigramma);
        $numero_revisione = $dati_nuovo_organigramma["numero_revisione"];
        
        if( $dati_nuovo_organigramma["revisione_di"] != 0 && $dati_nuovo_organigramma["revisione_di"] != "" ){
            
            $update = "UPDATE {$table_prefix}_kporganigrammi SET
                        kp_stato_organigramma = 'Revisionato',
                        kp_rev_in_data = '".$data_corrente."'
                        WHERE kporganigrammiid = ".$dati_nuovo_organigramma["revisione_di"];
            $adb->query($update);

        }

        $update = "UPDATE {$table_prefix}_kporganigrammi SET
                    kp_stato_organigramma = 'Attivo',
                    kp_data_organigramma = '".$data_corrente."',
                    kp_data_revisione = '".$data_corrente."'
                    WHERE kporganigrammiid = ".$organigramma;
        $adb->query($update);

        $numero_revisione_str = str_pad($numero_revisione, 3, "0", STR_PAD_LEFT);

		$nome_revisione = "Rev. ".$numero_revisione_str." del ".$data_formattata;

        $focus = CRMEntity::getInstance('KpResRevisioniOrg');
        $focus->column_fields['assigned_user_id'] = $current_user->id;
        $focus->column_fields['kp_nome_revisione'] = $nome_revisione;
        $focus->column_fields['kp_organigramma'] = $organigramma;
        $focus->column_fields['description'] = $descrizione; 
        $focus->column_fields['kp_data_revisione'] = $data_corrente; 
        $focus->column_fields['kp_numero_revisione'] = $numero_revisione;
        $focus->save('KpResRevisioniOrg', $longdesc = true, $offline_update = false, $triggerEvent = false);

        //self::gestioneNotificheRevisione($focus->id);

    }

    static function getAlberoNotifiche($filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $lista_notifiche_eseguite = array();

        $lista_notifiche_visionate = array();

        $lista_date_notifiche_eseguite = self::getDateNotificheUtente($filtro["utente"], "Eseguita notifica");

        $lista_date_notifiche_visionate = self::getDateNotificheUtente($filtro["utente"], "Confermata visione");

        foreach( $lista_date_notifiche_eseguite as $data_notifica ){

            $filtro_notifiche = array("data_notifica" => $data_notifica,
                                        "stato" => "Eseguita notifica",
                                        "utente" => $filtro["utente"]);

            $lista_notifiche = self::getNotificheUtentePerData($filtro_notifiche);

            $data_notifica = new DateTime($data_notifica);
            $data_notifica = $data_notifica->format("d/m/Y");

            $lista_notifiche_eseguite[] = array("data_notifica" => $data_notifica,
                                                "lista_notifiche" => $lista_notifiche);


        }

        foreach( $lista_date_notifiche_visionate as $data_notifica ){

            $filtro_notifiche = array("data_notifica" => $data_notifica,
                                        "stato" => "Confermata visione",
                                        "utente" => $filtro["utente"]);

            $lista_notifiche = self::getNotificheUtentePerData($filtro_notifiche);

            $data_notifica = new DateTime($data_notifica);
            $data_notifica = $data_notifica->format("d/m/Y");

            $lista_notifiche_visionate[] = array("data_notifica" => $data_notifica,
                                                "lista_notifiche" => $lista_notifiche);
                                                

        }

        $result = array("eseguita_notifica" => $lista_notifiche_eseguite,
                        "confermata_visione" => $lista_notifiche_visionate);

        return $result;

    }

    static function getDateNotificheUtente($utente, $stato){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    notf.kp_data_notifica kp_data_notifica
                    FROM {$table_prefix}_kpnotificherevorg notf
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = notf.kpnotificherevorgid
                    WHERE ent.deleted = 0";

        if($utente != ""){
            $query .= " AND ent.smownerid = ".$utente;
        }

        if($stato != ""){
            $query .= " AND notf.kp_stato_notifica_r = '".$stato."'";
        }

        $query .= " GROUP BY notf.kp_data_notifica
                    ORDER BY notf.kp_data_notifica DESC";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $data_notifica = $adb->query_result($result_query, $i, 'kp_data_notifica');
            $data_notifica = html_entity_decode(strip_tags($data_notifica), ENT_QUOTES, $default_charset);

            $result[] = $data_notifica;

        }

        return $result;

    }

    static function getNotificheUtentePerData($filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    notf.kpnotificherevorgid id,
                    notf.kp_soggetto nome,
                    notf.kp_data_notifica kp_data_notifica,
                    notf.kp_data_visione kp_data_visione,
                    notf.kp_stato_notifica_r kp_stato_notifica_r,
                    notf.kp_risorsa kp_risorsa
                    FROM {$table_prefix}_kpnotificherevorg notf
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = notf.kpnotificherevorgid
                    WHERE ent.deleted = 0";

        if($filtro["utente"] != ""){
            $query .= " AND ent.smownerid = ".$filtro["utente"];
        }

        if($filtro["stato"] != ""){
            $query .= " AND notf.kp_stato_notifica_r = '".$filtro["stato"]."'";
        }

        if($filtro["data_notifica"] != ""){
            $query .= " AND notf.kp_data_notifica = '".$filtro["data_notifica"]."'";
        }

        $query .= " ORDER BY notf.kpnotificherevorgid DESC";

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

    static function getNotificaRevisioneById($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    notf.kpnotificherevorgid id,
                    notf.kp_soggetto nome,
                    notf.kp_data_notifica kp_data_notifica,
                    notf.kp_data_visione kp_data_visione,
                    notf.kp_stato_notifica_r kp_stato_notifica_r,
                    notf.kp_risorsa kp_risorsa,
                    notf.kp_organigramma kp_organigramma,
                    org.kp_revisione_di kp_revisione_di,
                    notf.description descrizione
                    FROM {$table_prefix}_kpnotificherevorg notf
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = notf.kpnotificherevorgid
                    INNER JOIN {$table_prefix}_kporganigrammi org ON org.kporganigrammiid = notf.kp_organigramma
                    WHERE ent.deleted = 0 AND notf.kpnotificherevorgid = ".$id;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $nome = $adb->query_result($result_query, 0, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $descrizione = $adb->query_result($result_query, 0, 'descrizione');
            $descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES, $default_charset);

            $organigramma = $adb->query_result($result_query, 0, 'kp_organigramma');
            $organigramma = html_entity_decode(strip_tags($organigramma), ENT_QUOTES, $default_charset);

            $revisione_di = $adb->query_result($result_query, 0, 'kp_revisione_di');
            $revisione_di = html_entity_decode(strip_tags($revisione_di), ENT_QUOTES, $default_charset);

            $stato_notifica = $adb->query_result($result_query, 0, 'kp_stato_notifica_r');
            $stato_notifica = html_entity_decode(strip_tags($stato_notifica), ENT_QUOTES, $default_charset);

            $data_notifica = $adb->query_result($result_query, 0, 'kp_data_notifica');
            $data_notifica = html_entity_decode(strip_tags($data_notifica), ENT_QUOTES, $default_charset);

        }
        else{

            $id = 0;
            $nome = "";
            $descrizione = "";
            $organigramma = 0;
            $revisione_di = 0;
            $stato_notifica = "";
            $data_notifica = "";

        }
        
        $result = array("id" => $id,
                        "nome" => $nome,
                        "descrizione" => $descrizione,
                        "stato_notifica" => $stato_notifica,
                        "data_notifica" => $data_notifica,
                        "organigramma" => $organigramma,
                        "revisione_di" => $revisione_di);

        return $result;

    }

    static function setVisioneNotifica($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $focus = CRMEntity::getInstance('KpNotificheRevOrg');
        $focus->retrieve_entity_info($id, "KpNotificheRevOrg");
        $focus->column_fields['kp_stato_notifica_r'] = 'Confermata visione';
        $focus->column_fields['kp_data_visione'] = date("Y-m-d");
        $focus->mode = 'edit';
        $focus->id = $id;
        $focus->save('KpNotificheRevOrg', $longdesc = true, $offline_update = false, $triggerEvent = false);

    }

    static function setPDFApprovazioneOrganigramma($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__."/../../../modules/PDFMaker/InventoryPDF.php");
        require_once(__DIR__."/../../../include/mpdf/mpdf.php"); 

        $file_firma_esecutore = __DIR__."/../CustomViews/KpOrganigrammaCreator/firme/".$record."_esecutore_jqScribbleImage.png";
        
        if( file_exists($file_firma_esecutore) ){ 
            $firma_esecutore = "<img src='".$file_firma_esecutore."' style='max-width: 100%; float: left; max-height: 150px;'/>";
        }
        else{
            $firma_esecutore = "";
        }

        $file_firma_approvatore = __DIR__."/../CustomViews/KpOrganigrammaCreator/firme/".$record."_approvatore_jqScribbleImage.png";

        if( file_exists($file_firma_approvatore) ){ 
            $firma_approvatore = "<img src='".$file_firma_approvatore."' style='max-width: 100%; float: left; max-height: 150px;'/>";
        }
        else{
            $firma_approvatore = "";
        }

        /*$file_svg = __DIR__."/svg/".$record.".svg";

        if( file_exists($file_svg) ){ 

            $svg = "";

            $file_svg = fopen($file_svg, "r");

            while( !feof($file_svg) ){

                $svg .= fgets($file_svg);

            }

            fclose($file_svg);

            $image = new IMagick();  
            $image->setBackgroundColor(new ImagickPixel('transparent'));  
            $image->readImageBlob($svg);  
            $image->setImageFormat("png32");

            $file_png = __DIR__."/svg/".$record.".png";

            $image->writeImage($file_png);
            $image->clear();
            $image->destroy();

            if( file_exists($file_png) ){ 

                $svg = "<img src='".$file_png."' style='max-width: 100%; float: left; max-height: 100%;'/>";

            }
            else{
                $file_png = "";
            }

        }
        else{
            $file_png = "";
        }*/

        $id_statici = self::getConfigurazioniIdStatici();

        $id_statico_templateid = $id_statici["PDF Maker - Template Approvazione Organigrammi"];
        if( $id_statico_templateid["valore"] == "" && $id_statico_templateid["valore"] == 0){
            return;
        }

        $id_statico_cartella = $id_statici["Documenti - Cartella Organigrammi Approvati"];
        if( $id_statico_cartella["valore"] == "" && $id_statico_cartella["valore"] == 0){
            return;
        }

        $templateid = $id_statico_templateid["valore"];
        $relmodule = 'KpOrganigrammi';
        $language = 'it_it';
        $record = $record;
        $titolo_documento = "Approvazione Organigramma ".$record;
        $cartella_documenti = $id_statico_cartella["valore"];
        $description = "Approvazione Organigramma ".$record;

        $utente = $current_user->id;
        if($utente == null || $utente == "" || $utente == 0){
            $utente = 1;
        }

        $query = "SELECT 
                    notes.notesid notesid 
                    FROM {$table_prefix}_notes notes 
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = notes.notesid
                    INNER JOIN {$table_prefix}_senotesrel rel ON rel.notesid = notes.notesid
                    WHERE ent.deleted = 0 AND rel.crmid = ".$record." AND notes.title LIKE '%".$titolo_documento."%'";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if($num_result > 0){

            $document_id = $adb->query_result($result_query, 0, 'notesid');
            $document_id = html_entity_decode(strip_tags($document_id), ENT_QUOTES,$default_charset);

            $file_name = "doc_".$document_id.date("ymdHi").".pdf";
            
            $query = "SELECT att.attachmentsid attachmentsid,
                                att.name name,
                                att.path path
                                FROM {$table_prefix}_seattachmentsrel serel
                                INNER JOIN {$table_prefix}_attachments att ON att.attachmentsid = serel.attachmentsid
                                WHERE serel.crmid = ".$document_id;

            $result_query = $adb->query($query);
            $num_result = $adb->num_rows($result_query);
                
            for( $i=0; $i < $num_result; $i++ ){

                $vecchio_attachmentsid = $adb->query_result($result_query, $i, 'attachmentsid');
                $vecchio_attachmentsid = html_entity_decode(strip_tags($vecchio_attachmentsid), ENT_QUOTES,$default_charset);

                $vecchio_name = $adb->query_result($result_query, $i, 'name');
                $vecchio_name = html_entity_decode(strip_tags($vecchio_name), ENT_QUOTES,$default_charset);

                $vecchio_path = $adb->query_result($result_query, $i, 'path');
                $vecchio_path = html_entity_decode(strip_tags($vecchio_path), ENT_QUOTES,$default_charset);
                
                $vecchio_file_name = $vecchio_attachmentsid."_".$vecchio_name;
                @unlink($root_directory.$vecchio_path.$vecchio_file_name);
                
                $delete = "DELETE FROM {$table_prefix}_seattachmentsrel 
                            WHERE crmid = ".$document_id." AND attachmentsid =".$vecchio_attachmentsid;
                $adb->query($delete);
                
            }
            
        }
        else{
            $document = CRMEntity::getInstance('Documents'); 
            $document->parentid = $record;
            
            $file_name = "doc_".$document->parentid.date("ymdHi").".pdf";
            
            $document->column_fields["notes_title"] = $titolo_documento;
            $document->column_fields["assigned_user_id"] = $utente;
            $document->column_fields["filename"] = $file_name;
            $document->column_fields["notecontent"] = $description; 
            $document->column_fields["filetype"] = "application/pdf"; 
            $document->column_fields["filesize"] = ""; 
            $document->column_fields["filelocationtype"] = "I"; 
            $document->column_fields["fileversion"] = '';
            $document->column_fields["filestatus"] = "on";
            $document->column_fields["folderid"] = $cartella_documenti;

            $document->save("Documents", $longdesc=true, $offline_update=false, $triggerEvent=false);
            $document_id = $document->id;

        }

        $date_var = date("Y-m-d H:i:s");
        //to get the owner id
        $ownerid = $document->column_fields["assigned_user_id"];
        if(!isset($ownerid) || $ownerid==""){
            $ownerid = $utente;
        }

        $current_id = $adb->getUniqueID($table_prefix."_crmentity");
	
        $focus = CRMEntity::getInstance($relmodule);
        $focus->retrieve_entity_info($record,$relmodule);
        $focus->id = $record;

        $PDFContents = array();
        $TemplateContent = array();

        $PDFContent = PDFContent::getInstance($templateid, $relmodule, $focus, $language); 
        $pdf_content = $PDFContent->getContent();    

        $header_html = $pdf_content["header"];
        $body_html = $pdf_content["body"];
        $footer_html = $pdf_content["footer"];
        
        //$body_html = str_replace("#svg#", $svg, $body_html);
        $body_html = str_replace("#firma_esecutore#", $firma_esecutore, $body_html);
        $body_html = str_replace("#firma_approvatore#", $firma_approvatore, $body_html);

        $footer_html = str_replace("#firma_esecutore#", $firma_esecutore, $footer_html);
        $footer_html = str_replace("#firma_approvatore#", $firma_approvatore, $footer_html);
	
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

        $upload_file_path = __DIR__."/../CustomViews/KpOrganigrammaCreator/organigrammi_approvati/";

        if ( !is_dir($upload_file_path) ) {
            mkdir($upload_file_path, 0777);
            chown($upload_file_path, "www-data");
            chgrp($upload_file_path, "www-data");
        }

        if($name!=""){
            $file_name = $name.".pdf";
        }
    
        $mpdf->Output($upload_file_path.$current_id."_".$file_name);
    
        $filesize = filesize($upload_file_path.$current_id."_".$file_name);
        $filetype = "application/pdf";
        
        $sql1 = "insert into ".$table_prefix."_crmentity (crmid,smcreatorid,smownerid,setype,description,createdtime,modifiedtime) values(?, ?, ?, ?, ?, ?, ?)";
        $params1 = array($current_id, $utente, $ownerid, "Documents Attachment", $description, $adb->formatDate($date_var, true), $adb->formatDate($date_var, true));
    
        $adb->pquery($sql1, $params1);
    
        $sql2="insert into ".$table_prefix."_attachments(attachmentsid, name, description, type, path) values(?, ?, ?, ?, ?)";
        $params2 = array($current_id, $file_name, $description, $filetype, $upload_file_path);
        $result=$adb->pquery($sql2, $params2);
    
        $sql3='insert into '.$table_prefix.'_seattachmentsrel values(?,?)';
        $adb->pquery($sql3, array($document_id, $current_id));

        $sql4="UPDATE ".$table_prefix."_notes SET filesize=?, filename=? WHERE notesid=?";
        $adb->pquery($sql4,array($filesize,$file_name,$document_id));
        
        $result = $upload_file_path.$current_id."_".$file_name;

        $file_firma_esecutore = __DIR__."/../CustomViews/KpOrganigrammaCreator/firme/".$record."_esecutore_jqScribbleImage.png";
        if( file_exists($file_firma_esecutore) ){ 
            @unlink($file_firma_esecutore);
        }

        $file_firma_approvatore = __DIR__."/../CustomViews/KpOrganigrammaCreator/firme/".$record."_approvatore_jqScribbleImage.png";
        if( file_exists($file_firma_approvatore) ){ 
            @unlink($file_firma_approvatore);
        }

        /*$file_svg = __DIR__."/svg/".$record.".svg";
        if( file_exists($file_svg) ){ 
            @unlink($file_svg);
        }

        $file_png = __DIR__."/svg/".$record.".png";
        if( file_exists($file_png) ){ 
            @unlink($file_png);
        }*/

    }

    static function getConfigurazioniIdStatici(){
        global $adb, $table_prefix, $default_charset, $current_user, $dbconfig;

        $result = array();

        $query = "SELECT 
                    * 
                    FROM information_schema.tables
                    WHERE table_schema = '".$dbconfig['db_name']."' AND table_name = 'kp_settings_config_id_statici'";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);
            
        if( $num_result > 0 ){

            $query = "SELECT 
                        * 
                        FROM kp_settings_config_id_statici";

            $result_query = $adb->query($query);
            $num_result = $adb->num_rows($result_query);

            for( $i=0; $i < $num_result; $i++ ){

                $id_configurazione = $adb->query_result($result_query, $i, 'id_configurazione');
                $id_configurazione = html_entity_decode(strip_tags($id_configurazione), ENT_QUOTES,$default_charset);
                
                $nome_area_configurazione = $adb->query_result($result_query, $i, 'nome_area_configurazione');
                $nome_area_configurazione = html_entity_decode(strip_tags($nome_area_configurazione), ENT_QUOTES,$default_charset);

                $nome_configurazione = $adb->query_result($result_query, $i, 'nome_configurazione');
                $nome_configurazione = html_entity_decode(strip_tags($nome_configurazione), ENT_QUOTES,$default_charset);

                $valore = $adb->query_result($result_query, $i, 'valore');
                $valore = html_entity_decode(strip_tags($valore), ENT_QUOTES,$default_charset);

                $chiave = $nome_area_configurazione." - ".$nome_configurazione;

                $result[$chiave] = array("nome_area_configurazione" => $nome_area_configurazione,
                                        "nome_configurazione" => $nome_configurazione,
                                        "valore" => $valore);

            }

        }

        return $result;

    }


}

?>