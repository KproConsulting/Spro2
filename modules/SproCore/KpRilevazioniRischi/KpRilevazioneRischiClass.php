<?php

/* kpro@tom03042018 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2018, Kpro Consulting Srl
 */

class KpRilevazioneRischiClass {

    /**
     * - static function getTemplate($record, $modulo)
     * - static function getTemplateArea($record)
     * - static function getDatiRilevazione($record)
     * - static function getRuoliRelazionatiArea($area)
     * - static function getPericoliRelazionatiArea($area)
     * - static function getPericoloRilevazione($rilevazione, $pericolo, $related_to = 0)
     * - static function setRigaRilevazioneRischio($rilevazione, $dati)
     * - static function checkRigaRilevazioneRischio($rilevazione, $pericolo, $related_to)
     * - static function getTypeRelatedTo($related_to)
     * - static function decodeMagnitudo($magnitudo)
     * - static function decodeProbabilita($probabilita)
     * - static function getFraseDiRischio($rischio)
     * - static function setRuoloRigaRilevazioneRischio($riga, $ruolo)
     * - static function checkIfRuoloRelazionatoARiga($riga, $ruolo)
     */

    static function getTemplate($record, $modulo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        switch($modulo){
            case "Area":
                $result = self::getTemplateArea($record);
                break;
            case "Attivita":
                $result = self::getTemplateAttivita($record);
                break;
            case "Impianti":
                $result = self::getTemplateImpianti($record);
                break;
            case "SostanzeChimiche":
                $result = self::getTemplateSostanzeChimiche($record);
                break;
            case "MaterialiUtilizzo":
                $result = self::getTemplateMaterialiUtilizzo($record);
                break;
        }

        return $result;

    }

    static function getTemplateArea($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        $records = array();

        $table = "";

        $dati_rilevazione = self::getDatiRilevazione($record);
        $ruoli_relazionati = self::getRuoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );
        $pericoli_relazionati = self::getPericoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );

        $related_to = $dati_rilevazione["area_stabilimento"];

        $table = "<table style='width: 99%; margin: auto;' class='table table-striped table-hover tabella_rilevazione'>";
        $table .= "<thead>";
        $table .= "<tr>";

        $table .= "<th style='width: 150px;'>Area</th>";
        $table .= "<th>Pericoli</th>";
        $table .= "<th style='width: 140px;'>Probabilità</th>";
        $table .= "<th style='width: 140px;'>Magnitudo</th>";
        $table .= "<th style='width: 120px;'>Rischio</th>";

        foreach( $ruoli_relazionati as $ruolo ){
            $table .= "<th style='width: 40px; text-align:center;'><span class='vertical-rl'>".$ruolo["nome"]."</span></th>";
        }

        $table .= "</tr>";
        $table .= "</thead>";

        $table .= "<tbody>";

        $prima_riga = true;

        if( count($pericoli_relazionati) > 0 ){

            foreach($pericoli_relazionati as $pericolo){

                $dati_riga = self::getPericoloRilevazione($record, $pericolo["id"], $dati_rilevazione["area_stabilimento"]);

                $table .= "<tr id='".$dati_rilevazione["area_stabilimento"]."_".$pericolo["id"]."' class='tr_pericolo'>";

                if($prima_riga){

                    $prima_riga = false;
                    
                    $table .= "<td rowspan='".count($pericoli_relazionati)."'><b><span>";
                    $table .= $dati_rilevazione["nome_area_stabilimento"];
                    $table .= "</span></b></td>";

                }

                $table .= "<td class='td_pericolo' style='vertical-align: middle;'>";
                $table .= "<div class='checkbox'>";
                $table .= "<label>";
                if( $dati_riga["check"] ){
                    $table .= "<input type='checkbox' class='td_attivo' checked readonly disabled >";
                }
                else{
                    $table .= "<input type='checkbox' class='td_attivo' readonly disabled >";
                }
                $table .= "<b><span style='vertical-align: middle; margin-left: 10px;' >".$pericolo["nome"]."</span></b></label>";
                $table .= "</div>";
                $table .= "</b></td>";

                if( $dati_riga["check"] && $dati_riga["rischio"] != "" ){
                    $table .= "<td class='td_probabilita' style='vertical-align: middle'>".$dati_riga["probabilita"]."</td>";
                    $table .= "<td class='td_magnitudo' style='vertical-align: middle'>".$dati_riga["magnitudo"]."</td>";
                    $table .= "<td class='td_rischio' style='vertical-align: middle'>".$dati_riga["rischio"]." - ".$dati_riga["frase_di_rischio"]."</td>";
                }
                else{
                    $table .= "<td class='td_probabilita' style='vertical-align: middle'></td>";
                    $table .= "<td class='td_magnitudo' style='vertical-align: middle'></td>";
                    $table .= "<td class='td_rischio' style='vertical-align: middle'></td>";
                }

                $table .= self::getTabellaRuoli($ruoli_relazionati, $related_to, $dati_riga, $pericolo["id"]);

                $table .= "</tr>";

                $records[] = array("related_to" => $related_to,
                                    "pericolo_id" => $pericolo["id"],
                                    "pericolo_nome" => $pericolo["nome"]);

            }

        }

        $table .= "</tbody>";

        $table .= "</table>";

        $result = array("table" => $table,
                        "records" => $records);

        return $result;

    }

    static function getTabellaRuoli($ruoli_relazionati, $related_to, $dati_riga, $pericolo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $table = "";

        foreach( $ruoli_relazionati as $ruolo ){

            $table .= "<td style='vertical-align: middle;'>";
            $table .= "<div class='checkbox'>";
            $table .= "<label>";

            if( $dati_riga["check"] && $dati_riga["rischio"] != "" ){

                if( self::checkIfRuoloRelazionatoARiga($dati_riga["id_riga_ril"], $ruolo["id"]) ){
                    $table .= "<input type='checkbox' id='pericolo_ruolo_".$related_to."_".$pericolo."_".$ruolo["id"]."' checked readonly disabled >";
                }
                else{
                    $table .= "<input type='checkbox' id='pericolo_ruolo_".$related_to."_".$pericolo."_".$ruolo["id"]."' readonly disabled >";
                }
                
            }
            else{
                $table .= "<input type='checkbox' id='pericolo_ruolo_".$related_to."_".$pericolo."_".$ruolo["id"]."' readonly disabled >";
            }

            $table .= "<b><span style='vertical-align: middle;'></span></b></label>";
            $table .= "</div>";
            $table .= "</b></td>";

        }

        return $table;

    }

    static function getDatiRilevazione($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $focus_rilevazione = CRMEntity::getInstance('KpRilevazioniRischi');
        $focus_rilevazione->retrieve_entity_info($record, "KpRilevazioniRischi", $dieOnError=false); 
        $area_stabilimento = $focus_rilevazione->column_fields["kp_area_stab"];

        $focus_area = CRMEntity::getInstance('KpAreeStabilimento');
        $focus_area->retrieve_entity_info($area_stabilimento, "KpAreeStabilimento", $dieOnError=false); 
        $nome_area_stabilimento = $focus_area->column_fields["kp_nome_area"];
        $azienda = $focus_area->column_fields["kp_azienda"];
        $stabilimento = $focus_area->column_fields["kp_stabilimento"];

        $result = array("area_stabilimento" => $area_stabilimento,
                        "nome_area_stabilimento" => $nome_area_stabilimento,
                        "azienda" => $azienda,
                        "stabilimento" => $stabilimento);

        return $result;

    }

    static function getRuoliRelazionatiArea($area){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT t.* FROM
                    ((SELECT 
                    rel.relcrmid id,
                    ruol.kp_nome_ruolo nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kpruoli ruol ON ruol.kpruoliid = rel.relcrmid
                    WHERE ent.deleted = 0 AND rel.module = 'KpAreeStabilimento' AND rel.relmodule = 'KpRuoli' AND rel.crmid = ".$area.")
                    UNION
                    (SELECT 
                    rel.crmid id,
                    ruol.kp_nome_ruolo nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kpruoli ruol ON ruol.kpruoliid = rel.crmid
                    WHERE ent.deleted = 0 AND rel.relmodule = 'KpAreeStabilimento' AND rel.module = 'KpRuoli' AND rel.relcrmid = ".$area.")) AS t
                    ORDER BY t.nome ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);
            
            $result[] = array("id" => $id,
                                "nome" => $nome);

        }

        return $result;

    }

    static function getPericoliRelazionatiArea($area){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT t.* FROM
                    ((SELECT 
                    rel.relcrmid id,
                    risch.kp_nome_rischio nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kprischidvr risch ON risch.kprischidvrid = rel.relcrmid
                    WHERE ent.deleted = 0 AND rel.module = 'KpAreeStabilimento' AND rel.relmodule = 'KpRischiDVR' AND rel.crmid = ".$area.")
                    UNION
                    (SELECT 
                    rel.crmid id,
                    risch.kp_nome_rischio nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kprischidvr risch ON risch.kprischidvrid = rel.crmid
                    WHERE ent.deleted = 0 AND rel.relmodule = 'KpAreeStabilimento' AND rel.module = 'KpRischiDVR' AND rel.relcrmid = ".$area.")) AS t
                    ORDER BY t.nome ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);
            
            $result[] = array("id" => $id,
                                "nome" => $nome);

        }

        return $result;

    }

    static function getPericoloRilevazione($rilevazione, $pericolo, $related_to = 0){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $focus_pericolo = CRMEntity::getInstance('KpRischiDVR');
        $focus_pericolo->retrieve_entity_info($pericolo, "KpRischiDVR", $dieOnError=false); 
        $nome_pericolo = $focus_pericolo->column_fields["kp_nome_rischio"];

        $soggeto_a_misura = $focus_pericolo->column_fields["kp_soggeto_a_misura"];
        if($soggeto_a_misura == '1' || $soggeto_a_misura == 'on'){
            $soggeto_a_misura = true;
        }
        else{
            $soggeto_a_misura = false;
        }

        $nome_misurazione = $focus_pericolo->column_fields["kp_nome_misurazione"];
        $nome_misurazione = trim($nome_misurazione);
        if($nome_misurazione == null || $nome_misurazione == ""){
            $nome_misurazione = "Misurazione";
        }

        $help_probabilita = $focus_pericolo->column_fields["kp_help_probabilita"];
        if($help_probabilita == null || $help_probabilita == ""){
            $help_probabilita = "";
        }

        $help_magnitudo = $focus_pericolo->column_fields["kp_help_magnitudo"];
        if($help_magnitudo == null || $help_magnitudo == ""){
            $help_magnitudo = "";
        }

        $check = true;

        $dati_rilevazione = self::getDatiRilevazione($rilevazione);
        $ruoli_relazionati = self::getRuoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );
        $lista_ruoli = array();

        $nome_related_to = self::getNomeRelatedTo($rilevazione, $related_to);

        $typeRelatedTo = self::getTypeRelatedTo( $related_to );

        if( $typeRelatedTo == "KpAreeStabilimento" ){
            $related_to = 0;
        }

        $riga = self::checkRigaRilevazioneRischio($rilevazione, $pericolo, $related_to);
        
        if( $riga["esiste"] ){

            $focus_riga = CRMEntity::getInstance('KpRilevazRischiRig');
            $focus_riga->retrieve_entity_info($riga["id"], "KpRilevazRischiRig", $dieOnError=false); 
            
            $magnitudo = $focus_riga->column_fields["kp_gravita_rischio"];
            $magnitudo = html_entity_decode(strip_tags($magnitudo), ENT_QUOTES, $default_charset);

            $probabilita = $focus_riga->column_fields["kp_probabilita_risc"];
            $probabilita = html_entity_decode(strip_tags($probabilita), ENT_QUOTES, $default_charset);

            if( $magnitudo == "" || $magnitudo == null || $probabilita == "" || $probabilita == null ){

                $rischio = "";
                $frase_di_rischio = "";

            }
            else{

                $rischio = $focus_riga->column_fields["kp_valutazione_risc"];
                $rischio = html_entity_decode(strip_tags($rischio), ENT_QUOTES, $default_charset);
                if( $rischio == 0 || $rischio == "" || $rischio == null ){
                    $rischio = "";
                }

                $frase_di_rischio = $focus_riga->column_fields["kp_frase_risc_dvr"];
                $frase_di_rischio = html_entity_decode(strip_tags($frase_di_rischio), ENT_QUOTES, $default_charset);
                if( $frase_di_rischio == null ){
                    $frase_di_rischio = "";
                }

            }

            $misurazione = $focus_riga->column_fields["kp_misurazione"];
            $misurazione = html_entity_decode(strip_tags($misurazione), ENT_QUOTES, $default_charset);

            $attivo = $focus_riga->column_fields["kp_attivo"];
            $attivo = html_entity_decode(strip_tags($attivo), ENT_QUOTES, $default_charset);

            $descrizione = $focus_riga->column_fields["description"];
            $descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES, $default_charset);
            if( $descrizione == null ){
                $descrizione = "";
            }

            if( $attivo == '1' ){
                $check = true;
                $id_riga_ril = $riga["id"];
            }
            else{
                $check = false;
                $id_riga_ril = 0;
            }

        }
        else{

            $magnitudo = "";
            $probabilita = "";
            $rischio = "";
            $frase_di_rischio = "";
            $check = false;
            $id_riga_ril = 0;
            $misurazione = 0;
            $descrizione = "";

        }

        for( $i = 0; $i < count($ruoli_relazionati); $i++ ){

            if($riga["esiste"] && $rischio != "" ){
                //printf("\n<br />".$riga["id"]."->".$ruoli_relazionati[$i]["id"]."->".$rischio);
                $check_ruolo =  self::checkIfRuoloRelazionatoARiga($riga["id"], $ruoli_relazionati[$i]["id"]);
            }
            else{
                $check_ruolo = false;
            }

            $lista_ruoli[] = array("id" => $ruoli_relazionati[$i]["id"],
                                    "nome" => $ruoli_relazionati[$i]["nome"],
                                    "check" => $check_ruolo);
            
        }

        $result = array("id" => $pericolo,
                        "nome" => $nome_pericolo,
                        "nome_related_to" => $nome_related_to,
                        "check" => $check,
                        "id_riga_ril" => $id_riga_ril,
                        "magnitudo" => $magnitudo,
                        "probabilita" => $probabilita,
                        "rischio" => $rischio,
                        "misurazione" => $misurazione,
                        "frase_di_rischio" => $frase_di_rischio,
                        "soggeto_a_misura" => $soggeto_a_misura,
                        "nome_misurazione" => $nome_misurazione,
                        "help_probabilita" => $help_probabilita,
                        "help_magnitudo" => $help_magnitudo,
						"descrizione" => $descrizione,
                        "lista_ruoli" => $lista_ruoli);

        /*printf("\n<br />");
        print_r($result);
        printf("\n<br />");*/

        return $result;

    }

    static function setRigaRilevazioneRischio($rilevazione, $dati){
        global $adb, $table_prefix, $default_charset, $current_user;

        $typeRelatedTo = self::getTypeRelatedTo( $dati["related_to"] );

        if( $typeRelatedTo == "KpAreeStabilimento" ){
            $related_to = 0;
        }
        else{
            $related_to = $dati["related_to"];
        }

        $riga = self::checkRigaRilevazioneRischio($rilevazione, $dati["pericolo"], $related_to);

        $magnitudo = self::decodeMagnitudo($dati["magnitudo"]);
        $probabilita = self::decodeProbabilita($dati["probabilita"]);
        $frase_di_rischio = self::getFraseDiRischio($dati["rischio"]);

        $dati_rilevazione = self::getDatiRilevazione($rilevazione);

        $focus_pericolo = CRMEntity::getInstance('KpRischiDVR');
        $focus_pericolo->retrieve_entity_info($dati["pericolo"], "KpRischiDVR", $dieOnError=false); 
        
        $nome_pericolo = $focus_pericolo->column_fields["kp_nome_rischio"];
        $nome_pericolo = html_entity_decode(strip_tags($nome_pericolo), ENT_QUOTES, $default_charset);

        $nome_riga = $nome_pericolo;

        if( $riga["esiste"] && $dati["attivo"] == '1' ){

            $focus = CRMEntity::getInstance('KpRilevazRischiRig');
            $focus->retrieve_entity_info( $riga["id"], "KpRilevazRischiRig" );
            $focus->column_fields['kp_nome_riga'] = $nome_riga;
            $focus->column_fields['kp_attivo'] = $dati["attivo"];
            $focus->column_fields['kp_gravita_rischio'] = $magnitudo;
            $focus->column_fields['kp_probabilita_risc'] = $probabilita;
            $focus->column_fields['kp_valutazione_risc'] = $dati["rischio"];
            $focus->column_fields['kp_frase_risc_dvr'] = $frase_di_rischio;
            $focus->column_fields['kp_area_stab'] = $dati_rilevazione["area_stabilimento"];
            $focus->column_fields['kp_misurazione'] = $dati["misurazione"];
			$focus->column_fields['description'] = $dati["descrizione"];
            $focus->mode = 'edit';
            $focus->id = $riga["id"];
            $focus->save('KpRilevazRischiRig', $longdesc=true, $offline_update=false, $triggerEvent=false);

            $nome_riga = addslashes($nome_riga);

            $update = "UPDATE {$table_prefix}_kprilevazrischirig SET
                        kp_nome_riga = '".$nome_riga."'
                        WHERE kprilevazrischirigid = ".$focus->id;
            $adb->query($update);

        }
        elseif( $riga["esiste"] ){

            $update = "UPDATE {$table_prefix}_crmentity SET
                        deleted = 1
                        WHERE crmid = ".$riga["id"];
            $adb->query($update);          

        }
        else{

            $focus = CRMEntity::getInstance('KpRilevazRischiRig');
            $focus->column_fields['assigned_user_id'] = $current_user->id;
            $focus->column_fields['kp_nome_riga'] = $nome_riga;
            $focus->column_fields['kp_rilevazione'] = $rilevazione;
            $focus->column_fields['kp_rischio'] = $dati["pericolo"];
            $focus->column_fields['kp_related_to'] = $related_to;
            $focus->column_fields['kp_attivo'] = $dati["attivo"];
            $focus->column_fields['kp_gravita_rischio'] = $magnitudo;
            $focus->column_fields['kp_probabilita_risc'] = $probabilita;
            $focus->column_fields['kp_valutazione_risc'] = $dati["rischio"];
            $focus->column_fields['kp_frase_risc_dvr'] = $frase_di_rischio;
            $focus->column_fields['kp_area_stab'] = $dati_rilevazione["area_stabilimento"];
            $focus->column_fields['kp_misurazione'] = $dati["misurazione"];
			$focus->column_fields['description'] = $dati["descrizione"];
            $focus->save('KpRilevazRischiRig', $longdesc=true, $offline_update=false, $triggerEvent=false); 

            $nome_riga = addslashes($nome_riga);

            $update = "UPDATE {$table_prefix}_kprilevazrischirig SET
                        kp_nome_riga = '".$nome_riga."'
                        WHERE kprilevazrischirigid = ".$focus->id;
            $adb->query($update);

            $ruoli_relazionati = self::getRuoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );

            for($i = 0; $i < count($ruoli_relazionati); $i++){

                self::setRuoloRigaRilevazioneRischio($focus->id, $ruoli_relazionati[$i]["id"]);

            }

        }

        return self::getPericoloRilevazione($rilevazione, $dati["pericolo"], $dati["related_to"]);

    }

    static function checkRigaRilevazioneRischio($rilevazione, $pericolo, $related_to){
        global $adb, $table_prefix, $default_charset, $current_user;

        $query = "SELECT 
                    rig.kprilevazrischirigid id
                    FROM {$table_prefix}_kprilevazrischirig rig
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rig.kprilevazrischirigid
                    WHERE ent.deleted = 0 AND rig.kp_rilevazione = ".$rilevazione." AND rig.kp_rischio = ".$pericolo;

        if( $related_to == "" || $related_to == 0 ){
            $query .= " AND (rig.kp_related_to = '' OR rig.kp_related_to = 0)";
        }
        else{
            $query .= " AND rig.kp_related_to = ".$related_to;
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

    static function getTypeRelatedTo($related_to){
        global $adb, $table_prefix, $default_charset, $current_user;

        $setype = "";

        $query = "SELECT 
                    setype 
                    FROM {$table_prefix}_crmentity 
                    WHERE crmid = ".$related_to;
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if($num_result > 0){

            $setype = $adb->query_result($result_query, 0, 'setype');
            $setype = html_entity_decode(strip_tags($setype), ENT_QUOTES, $default_charset);

        }
  
        return $setype;

    }

    static function decodeMagnitudo($magnitudo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        switch ($magnitudo) {
            case 1:
                $result = "1 - Lieve";
                break;
            case 2:
                $result = "2 - Medio";
                break;
            case 3:
                $result = "3 - Alto";
                break;
            case 4:
                $result = "4 - Molto alto";
                break;
            default:
                $result = "";
        }

        return $result;

    }

    static function decodeProbabilita($probabilita){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        switch ($probabilita) {
            case 1:
                $result = "1 - Improbabile";
                break;
            case 2:
                $result = "2 - Poco Probabile";
                break;
            case 3:
                $result = "3 - Probabile";
                break;
            case 4:
                $result = "4 - Altamente Probabile";
                break;
            default:
                $result = "";
        }

        return $result;
        
    }

    static function getFraseDiRischio($rischio){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        if( $rischio >= 1 && $rischio <= 2){
            $result = "Minimo";
        }
        elseif($rischio > 2 && $rischio <= 4){
            $result = "Modesto";
        }
        elseif($rischio > 4 && $rischio <= 8){
            $result = "Rilevante";
        }
        elseif($rischio > 8 && $rischio <= 12){
            $result = "Grave";
        }
        elseif($rischio > 12){
            $result = "Molto grave";
        }

        return $result;
        
    }

    static function setRuoloRigaRilevazioneRischio($riga, $ruolo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $focus_riga = CRMEntity::getInstance('KpRilevazRischiRig');
        $focus_riga->retrieve_entity_info($riga, "KpRilevazRischiRig", $dieOnError=false); 

        $related_to = $focus_riga->column_fields["kp_related_to"];

        $ruolo_da_relazionare = false;

        if( $related_to == null || $related_to == "" || $related_to == 0 ){
            $ruolo_da_relazionare = true;
        }
        else{
            $ruolo_da_relazionare = self::checkIfRuoloRelazionatoARelatedTo($related_to, $ruolo);
        }

        if( !self::checkIfRuoloRelazionatoARiga($riga, $ruolo) && $ruolo_da_relazionare ){

            $insert = "INSERT INTO {$table_prefix}_crmentityrel (crmid, module, relcrmid, relmodule) 
                        VALUES
                        (".$riga.", 'KpRilevazRischiRig', ".$ruolo.", 'KpRuoli')";

            $adb->query($insert);

        }

    }

    static function checkIfRuoloRelazionatoARiga($riga, $ruolo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $query = "SELECT 
                    * 
                    FROM {$table_prefix}_crmentityrel
                    WHERE (crmid = ".$riga." AND module = 'KpRilevazRischiRig' AND relcrmid = ".$ruolo." AND relmodule = 'KpRuoli') OR (crmid = ".$ruolo." AND module = 'KpRuoli' AND relcrmid = ".$riga." AND relmodule = 'KpRilevazRischiRig')";
       
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if($num_result > 0){

            return true;

        }
        else{
            return false;
        }

    }

    static function getMisureRiduttivePericolo($record, $dati){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $typeRelatedTo = self::getTypeRelatedTo( $dati["related_to"] );

        if( $typeRelatedTo == "KpAreeStabilimento" ){
            $related_to = 0;
        }
        else{
            $related_to = $dati["related_to"];
        }

        $dati_rilevazione = self::getDatiRilevazione($record);

        $query = "SELECT 
                    mis.kpmisureriduttiveid id,
                    mis.kp_nome_misura nome,
                    mis.kp_tipo_misura tipo_misura,
                    mis.kp_eseguire_entro eseguire_entro,
                    mis.kp_stato_misura_rid stato_misura
                    FROM {$table_prefix}_kpmisureriduttive mis
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = mis.kpmisureriduttiveid
                    WHERE ent.deleted = 0 AND mis.kp_stato_misura_rid != 'Adottata' AND mis.kp_pericolo = ".$dati["pericolo"]." AND mis.kp_area_stab = ".$dati_rilevazione["area_stabilimento"];

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

            $stato_misura = $adb->query_result($result_query, $i, 'stato_misura');
            $stato_misura = html_entity_decode(strip_tags($stato_misura), ENT_QUOTES, $default_charset);
            
            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "tipo_misura" => $tipo_misura,
                                "eseguire_entro" => $eseguire_entro,
                                "stato_misura" => $stato_misura);

        }

        return $result;

    }

    static function getListaTipiMisureRiduttive($rilevazione, $pericolo, $related_to, $filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        /*  //Questo blocco di codice mi permetterebbe di escludere i tipi verifica già associati
        $dati = array("related_to" => $related_to,
                        "pericolo" => $pericolo); 

        $misure_associate = self::getMisureRiduttivePericolo($rilevazione, $dati);

        $misure_associate_str = "";

        foreach($misure_associate as $misura_associata){

            if($misure_associate_str == ""){
                $misure_associate_str = $misura_associata["tipo_misura"];
            }
            else{
                $misure_associate_str .= ",".$misura_associata["tipo_misura"];
            }

        }*/

        $query = "SELECT 
                    tipmis.kptipimisureriduttiveid id,
                    tipmis.kp_nome_misura nome,
                    tipmis.kp_categoria_misura categoria_misura
                    FROM {$table_prefix}_kptipimisureriduttive tipmis
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tipmis.kptipimisureriduttiveid
                    WHERE ent.deleted = 0";

        if($filtro["tipo_misura"] != ""){
            $query .= " AND tipmis.kp_nome_misura LIKE '%".$filtro["tipo_misura"]."%'";
        }

        if($filtro["categoria_misura"] != ""){
            $query .= " AND tipmis.kp_categoria_misura LIKE '%".$filtro["categoria_misura"]."%'";
        }

        /*  //Questo blocco di codice mi permetterebbe di escludere i tipi verifica già associati
        if($misure_associate_str != ""){
            $query .= " AND tipmis.kptipimisureriduttiveid NOT IN (".$misure_associate_str.")";
        }
        */

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $categoria_misura = $adb->query_result($result_query, $i, 'categoria_misura');
            $categoria_misura = html_entity_decode(strip_tags($categoria_misura), ENT_QUOTES, $default_charset);

            $result[] = array("id" => $id,
                            "nome" => $nome,
                            "categoria_misura" => $categoria_misura);

        }

        return $result;

    }

    static function getNomeRelatedTo($rilevazione, $related_to){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $typeRelatedTo = self::getTypeRelatedTo( $related_to );

        if( $typeRelatedTo == "KpAreeStabilimento" ){

            $dati_rilevazione = self::getDatiRilevazione($rilevazione);
            $result = $dati_rilevazione["nome_area_stabilimento"];

        }
        elseif( $typeRelatedTo == "KpTipologieImpianti" ){

            $focus = CRMEntity::getInstance('KpTipologieImpianti');
            $focus->retrieve_entity_info($related_to, "KpTipologieImpianti", $dieOnError=false); 
            $result = $focus->column_fields["kp_nome_tipologia"];

        }
        elseif( $typeRelatedTo == "KpAttivitaDVR" ){

            $focus = CRMEntity::getInstance('KpAttivitaDVR');
            $focus->retrieve_entity_info($related_to, "KpAttivitaDVR", $dieOnError=false); 
            $result = $focus->column_fields["kp_nome_attivita"];

        }
        elseif( $typeRelatedTo == "KpSostanzeChimiche" ){

            $focus = CRMEntity::getInstance('KpSostanzeChimiche');
            $focus->retrieve_entity_info($related_to, "KpSostanzeChimiche", $dieOnError=false); 
            $result = $focus->column_fields["kp_nome_sostanza"];

        }
        elseif( $typeRelatedTo == "KpMaterialiUtilizzo" ){

            $focus = CRMEntity::getInstance('KpMaterialiUtilizzo');
            $focus->retrieve_entity_info($related_to, "KpMaterialiUtilizzo", $dieOnError=false); 
            $result = $focus->column_fields["kp_nome_materiale"];

        }

        return $result;

    }

    static function setMisuraRiduzioneRischio($rilevazione, $dati){
        global $adb, $table_prefix, $default_charset, $current_user;

        $typeRelatedTo = self::getTypeRelatedTo( $dati["related_to"] );

        if( $typeRelatedTo == "KpAreeStabilimento" ){
            $related_to = 0;
        }
        else{
            $related_to = $dati["related_to"];
        }

        $misura =  self::checkMisuraRiduzioneRischio($rilevazione, $related_to, $dati["pericolo"], $dati["tipo_misura"]);

        $dati_rilevazione = self::getDatiRilevazione($rilevazione);

        if( $misura["esiste"] ){

            $focus = CRMEntity::getInstance('KpMisureRiduttive');
            $focus->retrieve_entity_info( $misura["id"], "KpMisureRiduttive" );
            $focus->column_fields['kp_nome_misura'] = $dati["nome_intervento"];
            $focus->column_fields['kp_eseguire_entro'] = $dati["adottare_entro"];
            $focus->column_fields['kp_riduzione_prob'] = $dati["riduzione_probabilita"];
            $focus->column_fields['kp_riduzione_magn'] = $dati["riduzione_magnitudo"];
            $focus->column_fields['description'] = $dati["nota"];
            $focus->mode = 'edit';
            $focus->id = $misura["id"];
            $focus->save('KpMisureRiduttive', $longdesc=true, $offline_update=false, $triggerEvent=false);

        }
        else{

            $focus = CRMEntity::getInstance('KpMisureRiduttive');
            $focus->column_fields['assigned_user_id'] = $current_user->id;
            $focus->column_fields['kp_nome_misura'] = $dati["nome_intervento"];
            $focus->column_fields['kp_tipo_misura'] = $dati["tipo_misura"];
            $focus->column_fields['kp_area_stab'] = $dati_rilevazione["area_stabilimento"];
            $focus->column_fields['kp_azienda'] = $dati_rilevazione["azienda"];
            $focus->column_fields['kp_stabilimento'] = $dati_rilevazione["stabilimento"];
            $focus->column_fields['kp_eseguire_entro'] = $dati["adottare_entro"];
            if($related_to != 0){
                $focus->column_fields['kp_related_to'] = $related_to;
            }
            $focus->column_fields['kp_pericolo'] = $dati["pericolo"];
            $focus->column_fields['kp_riduzione_prob'] = $dati["riduzione_probabilita"];
            $focus->column_fields['kp_riduzione_magn'] = $dati["riduzione_magnitudo"];
            $focus->column_fields['description'] = $dati["nota"];
            $focus->column_fields['kp_stato_misura_rid'] = 'Da adottare';
            $focus->save('KpMisureRiduttive', $longdesc=true, $offline_update=false, $triggerEvent=false); 
            
        }

        $nome_intervento = addslashes($dati["nome_intervento"]);
        $description = addslashes($dati["nota"]);

        $update = "UPDATE {$table_prefix}_kpmisureriduttive SET
                    kp_nome_misura = '".$nome_intervento."',
                    description = '".$description."'
                    WHERE kpmisureriduttiveid = ".$focus->id;
        $adb->query($update);

        return $focus->id;

    }

    static function checkMisuraRiduzioneRischio($record, $related_to, $pericolo, $tipo_misura){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $typeRelatedTo = self::getTypeRelatedTo( $related_to );

        if( $typeRelatedTo == "KpAreeStabilimento" ){
            $related_to = 0;
        }

        $dati_rilevazione = self::getDatiRilevazione($record);

        $query = "SELECT 
                    mis.kpmisureriduttiveid id
                    FROM {$table_prefix}_kpmisureriduttive mis
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = mis.kpmisureriduttiveid
                    WHERE ent.deleted = 0 AND mis.kp_stato_misura_rid != 'Adottata' AND mis.kp_pericolo = ".$pericolo." AND mis.kp_area_stab = ".$dati_rilevazione["area_stabilimento"]." AND mis.kp_tipo_misura = ".$tipo_misura;

        if($related_to != 0){
            $query .= " AND mis.kp_related_to = ".$related_to;
        }
        else{
            $query .= " AND (mis.kp_related_to = 0 OR mis.kp_related_to = '')";
        }

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $esiste = true;

            $id = $adb->query_result($result_query, 0, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

        }
        else{

            $esiste = false;

            $id = 0;

        }

        $result = array("id" => $id,
                        "esiste" => $esiste);

        return $result;

    }

    static function setLinkRuolo($record, $related_to, $pericolo, $ruolo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $typeRelatedTo = self::getTypeRelatedTo( $related_to );

        if( $typeRelatedTo == "KpAreeStabilimento" ){
            $related_to = 0;
        }

        $riga = self::checkRigaRilevazioneRischio($record, $pericolo, $related_to);
        
        if( $riga["esiste"] ){

            if( !self::checkIfRuoloRelazionatoARiga($riga["id"], $ruolo) ){

                $insert = "INSERT INTO {$table_prefix}_crmentityrel (crmid, module, relcrmid, relmodule) 
                            VALUES
                            (".$riga["id"].", 'KpRilevazRischiRig', ".$ruolo.", 'KpRuoli')";
                $adb->query($insert);

            }

        }

        return "ok";

    }

    static function unsetLinkRuolo($record, $related_to, $pericolo, $ruolo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $typeRelatedTo = self::getTypeRelatedTo( $related_to );

        if( $typeRelatedTo == "KpAreeStabilimento" ){
            $related_to = 0;
        }

        $riga = self::checkRigaRilevazioneRischio($record, $pericolo, $related_to);
        
        if( $riga["esiste"] ){

            if( self::checkIfRuoloRelazionatoARiga($riga["id"], $ruolo) ){

                $delete = "DELETE FROM {$table_prefix}_crmentityrel 
                            WHERE module = 'KpRilevazRischiRig' AND relmodule = 'KpRuoli' AND crmid = ".$riga["id"]." AND relcrmid = ".$ruolo;
                $adb->query($delete);

                $delete = "DELETE FROM {$table_prefix}_crmentityrel 
                            WHERE module = 'KpRuoli' AND relmodule = 'KpRilevazRischiRig' AND crmid = ".$ruolo." AND relcrmid = ".$riga["id"];
                $adb->query($delete);
                
            }

        }

        return "ok";

    }

    static function getTemplateAttivita($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        $records = array();

        $table = "";

        $dati_rilevazione = self::getDatiRilevazione($record);
        $ruoli_relazionati = self::getRuoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );
        $attivita_relazionate = self::getAttivitaRelazionateArea( $dati_rilevazione["area_stabilimento"] );

        $table = "<table style='width: 99%; margin: auto;' class='table table-striped table-hover tabella_rilevazione'>";
        $table .= "<thead>";
        $table .= "<tr>";

        $table .= "<th style='width: 150px;'>Attività</th>";
        $table .= "<th>Pericoli</th>";
        $table .= "<th style='width: 140px;'>Probabilità</th>";
        $table .= "<th style='width: 140px;'>Magnitudo</th>";
        $table .= "<th style='width: 120px;'>Rischio</th>";

        foreach( $ruoli_relazionati as $ruolo ){
            $table .= "<th style='width: 40px; text-align:center;'><span class='vertical-rl'>".$ruolo["nome"]."</span></th>";
        }

        $table .= "</tr>";
        $table .= "</thead>";

        $table .= "<tbody>";

        $prima_riga = true;

        foreach($attivita_relazionate as $attivita){

            if( count($attivita["lista_pericoli"]) > 0 ){

                $prima_riga = true;

                foreach($attivita["lista_pericoli"] as $pericolo){

                    $dati_riga = self::getPericoloRilevazione($record, $pericolo["id"], $attivita["id"]);

                    $table .= "<tr id='".$attivita["id"]."_".$pericolo["id"]."' class='tr_pericolo'>";

                    if($prima_riga){

                        $prima_riga = false;
                        
                        $table .= "<td rowspan='".count($attivita["lista_pericoli"])."'><b><span>";
                        $table .= $attivita["nome"];
                        $table .= "</span></b></td>";

                    }

                    $table .= "<td class='td_pericolo' style='vertical-align: middle;'>";
                    $table .= "<div class='checkbox'>";
                    $table .= "<label>";
                    if( $dati_riga["check"] ){
                        $table .= "<input type='checkbox' class='td_attivo' checked readonly disabled >";
                    }
                    else{
                        $table .= "<input type='checkbox' class='td_attivo' readonly disabled >";
                    }
                    $table .= "<b><span style='vertical-align: middle; margin-left: 10px;' >".$pericolo["nome"]."</span></b></label>";
                    $table .= "</div>";
                    $table .= "</b></td>";

                    if( $dati_riga["check"] && $dati_riga["rischio"] != ""){
                        $table .= "<td class='td_probabilita' style='vertical-align: middle'>".$dati_riga["probabilita"]."</td>";
                        $table .= "<td class='td_magnitudo' style='vertical-align: middle'>".$dati_riga["magnitudo"]."</td>";
                        $table .= "<td class='td_rischio' style='vertical-align: middle'>".$dati_riga["rischio"]." - ".$dati_riga["frase_di_rischio"]."</td>";
                    }
                    else{
                        $table .= "<td class='td_probabilita' style='vertical-align: middle'></td>";
                        $table .= "<td class='td_magnitudo' style='vertical-align: middle'></td>";
                        $table .= "<td class='td_rischio' style='vertical-align: middle'></td>";
                    }

                    $table .= self::getTabellaRuoli($ruoli_relazionati, $attivita["id"], $dati_riga, $pericolo["id"]);

                    $table .= "</tr>";

                    $records[] = array("related_to" => $attivita["id"],
                                        "pericolo_id" => $pericolo["id"],
                                        "pericolo_nome" => $pericolo["nome"]);

                }      

            }

        }

        $table .= "</tbody>";

        $table .= "</table>";

        $result = array("table" => $table,
                        "records" => $records);

        return $result;

    }

    static function getAttivitaRelazionateArea($area){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT t.* FROM
                    ((SELECT 
                    rel.relcrmid id,
                    att.kp_nome_attivita nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kpattivitadvr att ON att.kpattivitadvrid = rel.relcrmid
                    WHERE ent.deleted = 0 AND rel.module = 'KpAreeStabilimento' AND rel.relmodule = 'KpAttivitaDVR' AND rel.crmid = ".$area.")
                    UNION
                    (SELECT 
                    rel.crmid id,
                    att.kp_nome_attivita nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kpattivitadvr att ON att.kpattivitadvrid = rel.crmid
                    WHERE ent.deleted = 0 AND rel.relmodule = 'KpAreeStabilimento' AND rel.module = 'KpAttivitaDVR' AND rel.relcrmid = ".$area.")) AS t
                    ORDER BY t.nome ASC";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $pericoli_relazionati = self::getPericoliRelazionatiAttivita( $id );
            
            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "lista_pericoli" => $pericoli_relazionati);

        }

        return $result;

    }

    static function getPericoliRelazionatiAttivita($attivita){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT t.* FROM
                    ((SELECT 
                    rel.relcrmid id,
                    risch.kp_nome_rischio nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kprischidvr risch ON risch.kprischidvrid = rel.relcrmid
                    WHERE ent.deleted = 0 AND rel.module = 'KpAttivitaDVR' AND rel.relmodule = 'KpRischiDVR' AND rel.crmid = ".$attivita.")
                    UNION
                    (SELECT 
                    rel.crmid id,
                    risch.kp_nome_rischio nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kprischidvr risch ON risch.kprischidvrid = rel.crmid
                    WHERE ent.deleted = 0 AND rel.relmodule = 'KpAttivitaDVR' AND rel.module = 'KpRischiDVR' AND rel.relcrmid = ".$attivita.")) AS t
                    ORDER BY t.nome ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);
            
            $result[] = array("id" => $id,
                                "nome" => $nome);

        }

        return $result;

    }

    static function checkIfRuoloRelazionatoARelatedTo($related_to, $ruolo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $query = "SELECT 
                    * 
                    FROM {$table_prefix}_crmentityrel
                    WHERE (crmid = ".$related_to." AND relcrmid = ".$ruolo." AND relmodule = 'KpRuoli') OR (crmid = ".$ruolo." AND module = 'KpRuoli' AND relcrmid = ".$related_to.")";
       
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if($num_result > 0){

            return true;

        }
        else{
            return false;
        }

    }

    static function getTemplateImpianti($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        $records = array();

        $table = "";

        $dati_rilevazione = self::getDatiRilevazione($record);
        $ruoli_relazionati = self::getRuoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );
        $tipi_impianti_relazionati = self::getTipiImpiantiRelazionatiArea( $dati_rilevazione["area_stabilimento"] );

        $table = "<table style='width: 99%; margin: auto;' class='table table-striped table-hover tabella_rilevazione'>";
        $table .= "<thead>";
        $table .= "<tr>";

        $table .= "<th style='width: 150px;'>Tipi Impianto</th>";
        $table .= "<th>Pericoli</th>";
        $table .= "<th style='width: 140px;'>Probabilità</th>";
        $table .= "<th style='width: 140px;'>Magnitudo</th>";
        $table .= "<th style='width: 120px;'>Rischio</th>";

        foreach( $ruoli_relazionati as $ruolo ){
            $table .= "<th style='width: 40px; text-align:center;'><span class='vertical-rl'>".$ruolo["nome"]."</span></th>";
        }

        $table .= "</tr>";
        $table .= "</thead>";

        $table .= "<tbody>";

        $prima_riga = true;

        foreach($tipi_impianti_relazionati as $tipo_impianto){

            if( count($tipo_impianto["lista_pericoli"]) > 0 ){

                $prima_riga = true;

                foreach($tipo_impianto["lista_pericoli"] as $pericolo){

                    $dati_riga = self::getPericoloRilevazione($record, $pericolo["id"], $tipo_impianto["id"]);

                    $table .= "<tr id='".$tipo_impianto["id"]."_".$pericolo["id"]."' class='tr_pericolo'>";

                    if($prima_riga){

                        $prima_riga = false;
                        
                        $table .= "<td rowspan='".count($tipo_impianto["lista_pericoli"])."'><b><span>";
                        $table .= $tipo_impianto["nome"];
                        $table .= "</span></b></td>";

                    }

                    $table .= "<td class='td_pericolo' style='vertical-align: middle;'>";
                    $table .= "<div class='checkbox'>";
                    $table .= "<label>";
                    if( $dati_riga["check"] ){
                        $table .= "<input type='checkbox' class='td_attivo' checked readonly disabled >";
                    }
                    else{
                        $table .= "<input type='checkbox' class='td_attivo' readonly disabled >";
                    }
                    $table .= "<b><span style='vertical-align: middle; margin-left: 10px;' >".$pericolo["nome"]."</span></b></label>";
                    $table .= "</div>";
                    $table .= "</b></td>";

                    if( $dati_riga["check"] && $dati_riga["rischio"] != "" ){
                        $table .= "<td class='td_probabilita' style='vertical-align: middle'>".$dati_riga["probabilita"]."</td>";
                        $table .= "<td class='td_magnitudo' style='vertical-align: middle'>".$dati_riga["magnitudo"]."</td>";
                        $table .= "<td class='td_rischio' style='vertical-align: middle'>".$dati_riga["rischio"]." - ".$dati_riga["frase_di_rischio"]."</td>";
                    }
                    else{
                        $table .= "<td class='td_probabilita' style='vertical-align: middle'></td>";
                        $table .= "<td class='td_magnitudo' style='vertical-align: middle'></td>";
                        $table .= "<td class='td_rischio' style='vertical-align: middle'></td>";
                    }

                    $table .= self::getTabellaRuoli($ruoli_relazionati, $tipo_impianto["id"], $dati_riga, $pericolo["id"]);

                    $table .= "</tr>";

                    $records[] = array("related_to" => $tipo_impianto["id"],
                                        "pericolo_id" => $pericolo["id"],
                                        "pericolo_nome" => $pericolo["nome"]);

                }      

            }

        }

        $table .= "</tbody>";

        $table .= "</table>";

        $result = array("table" => $table,
                        "records" => $records);

        return $result;

    }

    static function getTipiImpiantiRelazionatiArea($area){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT t.* FROM
                    ((SELECT 
                    rel.relcrmid id,
                    imp.kp_nome_tipologia nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kptipologieimpianti imp ON imp.kptipologieimpiantiid = rel.relcrmid
                    WHERE ent.deleted = 0 AND rel.module = 'KpAreeStabilimento' AND rel.relmodule = 'KpTipologieImpianti' AND rel.crmid = ".$area.")
                    UNION
                    (SELECT 
                    rel.crmid id,
                    imp.kp_nome_tipologia nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kptipologieimpianti imp ON imp.kptipologieimpiantiid = rel.crmid
                    WHERE ent.deleted = 0 AND rel.relmodule = 'KpAreeStabilimento' AND rel.module = 'KpTipologieImpianti' AND rel.relcrmid = ".$area.")) AS t
                    ORDER BY t.nome ASC";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $pericoli_relazionati = self::getPericoliRelazionatiTipoImpianto( $id );
            
            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "lista_pericoli" => $pericoli_relazionati);

        }

        return $result;

    }

    static function getPericoliRelazionatiTipoImpianto($tipo_impianto){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT t.* FROM
                    ((SELECT 
                    rel.relcrmid id,
                    risch.kp_nome_rischio nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kprischidvr risch ON risch.kprischidvrid = rel.relcrmid
                    WHERE ent.deleted = 0 AND rel.module = 'KpTipologieImpianti' AND rel.relmodule = 'KpRischiDVR' AND rel.crmid = ".$tipo_impianto.")
                    UNION
                    (SELECT 
                    rel.crmid id,
                    risch.kp_nome_rischio nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kprischidvr risch ON risch.kprischidvrid = rel.crmid
                    WHERE ent.deleted = 0 AND rel.relmodule = 'KpTipologieImpianti' AND rel.module = 'KpRischiDVR' AND rel.relcrmid = ".$tipo_impianto.")) AS t
                    ORDER BY t.nome ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);
            
            $result[] = array("id" => $id,
                                "nome" => $nome);

        }

        return $result;

    }

    static function getTemplateSostanzeChimiche($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        $records = array();

        $table = "";

        $dati_rilevazione = self::getDatiRilevazione($record);
        $ruoli_relazionati = self::getRuoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );
        $sostanze_chiniche_relazionate = self::getSostanzeChimicheRelazionateArea( $dati_rilevazione["area_stabilimento"] );

        $table = "<table style='width: 99%; margin: auto;' class='table table-striped table-hover tabella_rilevazione'>";
        $table .= "<thead>";
        $table .= "<tr>";

        $table .= "<th style='width: 150px;'>Sostanze Chimiche</th>";
        $table .= "<th>Pericoli</th>";
        $table .= "<th style='width: 140px;'>Probabilità</th>";
        $table .= "<th style='width: 140px;'>Magnitudo</th>";
        $table .= "<th style='width: 120px;'>Rischio</th>";

        foreach( $ruoli_relazionati as $ruolo ){
            $table .= "<th style='width: 40px; text-align:center;'><span class='vertical-rl'>".$ruolo["nome"]."</span></th>";
        }

        $table .= "</tr>";
        $table .= "</thead>";

        $table .= "<tbody>";

        $prima_riga = true;

        foreach($sostanze_chiniche_relazionate as $sostanze_chimica){

            if( count($sostanze_chimica["lista_pericoli"]) > 0 ){

                $prima_riga = true;

                foreach($sostanze_chimica["lista_pericoli"] as $pericolo){

                    $dati_riga = self::getPericoloRilevazione($record, $pericolo["id"], $sostanze_chimica["id"]);

                    $table .= "<tr id='".$sostanze_chimica["id"]."_".$pericolo["id"]."' class='tr_pericolo'>";

                    if($prima_riga){

                        $prima_riga = false;
                        
                        $table .= "<td rowspan='".count($sostanze_chimica["lista_pericoli"])."'><b><span>";
                        $table .= $sostanze_chimica["nome"];
                        $table .= "</span></b></td>";

                    }

                    $table .= "<td class='td_pericolo' style='vertical-align: middle;'>";
                    $table .= "<div class='checkbox'>";
                    $table .= "<label>";
                    if( $dati_riga["check"] ){
                        $table .= "<input type='checkbox' class='td_attivo' checked readonly disabled >";
                    }
                    else{
                        $table .= "<input type='checkbox' class='td_attivo' readonly disabled >";
                    }
                    $table .= "<b><span style='vertical-align: middle; margin-left: 10px;' >".$pericolo["nome"]."</span></b></label>";
                    $table .= "</div>";
                    $table .= "</b></td>";

                    if( $dati_riga["check"] && $dati_riga["rischio"] != "" ){
                        $table .= "<td class='td_probabilita' style='vertical-align: middle'>".$dati_riga["probabilita"]."</td>";
                        $table .= "<td class='td_magnitudo' style='vertical-align: middle'>".$dati_riga["magnitudo"]."</td>";
                        $table .= "<td class='td_rischio' style='vertical-align: middle'>".$dati_riga["rischio"]." - ".$dati_riga["frase_di_rischio"]."</td>";
                    }
                    else{
                        $table .= "<td class='td_probabilita' style='vertical-align: middle'></td>";
                        $table .= "<td class='td_magnitudo' style='vertical-align: middle'></td>";
                        $table .= "<td class='td_rischio' style='vertical-align: middle'></td>";
                    }
                    
                    $table .= self::getTabellaRuoli($ruoli_relazionati, $sostanze_chimica["id"], $dati_riga, $pericolo["id"]);

                    $table .= "</tr>";

                    $records[] = array("related_to" => $sostanze_chimica["id"],
                                        "pericolo_id" => $pericolo["id"],
                                        "pericolo_nome" => $pericolo["nome"]);

                }      

            }

        }

        $table .= "</tbody>";

        $table .= "</table>";

        $result = array("table" => $table,
                        "records" => $records);

        return $result;

    }

    static function getSostanzeChimicheRelazionateArea($area){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT t.* FROM
                    ((SELECT 
                    rel.relcrmid id,
                    sc.kp_nome_sostanza nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kpsostanzechimiche sc ON sc.kpsostanzechimicheid = rel.relcrmid
                    WHERE ent.deleted = 0 AND rel.module = 'KpAreeStabilimento' AND rel.relmodule = 'KpSostanzeChimiche' AND rel.crmid = ".$area.")
                    UNION
                    (SELECT 
                    rel.crmid id,
                    sc.kp_nome_sostanza nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kpsostanzechimiche sc ON sc.kpsostanzechimicheid = rel.crmid
                    WHERE ent.deleted = 0 AND rel.relmodule = 'KpAreeStabilimento' AND rel.module = 'KpSostanzeChimiche' AND rel.relcrmid = ".$area.")) AS t
                    ORDER BY t.nome ASC";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $pericoli_relazionati = self::getPericoliRelazionatiSostanzeChimiche( $id );
            
            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "lista_pericoli" => $pericoli_relazionati);

        }

        return $result;

    }

    static function getPericoliRelazionatiSostanzeChimiche($sostanza_chimica){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT t.* FROM
                    ((SELECT 
                    rel.relcrmid id,
                    risch.kp_nome_rischio nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kprischidvr risch ON risch.kprischidvrid = rel.relcrmid
                    WHERE ent.deleted = 0 AND rel.module = 'KpSostanzeChimiche' AND rel.relmodule = 'KpRischiDVR' AND rel.crmid = ".$sostanza_chimica.")
                    UNION
                    (SELECT 
                    rel.crmid id,
                    risch.kp_nome_rischio nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kprischidvr risch ON risch.kprischidvrid = rel.crmid
                    WHERE ent.deleted = 0 AND rel.relmodule = 'KpSostanzeChimiche' AND rel.module = 'KpRischiDVR' AND rel.relcrmid = ".$sostanza_chimica.")) AS t
                    ORDER BY t.nome ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);
            
            $result[] = array("id" => $id,
                                "nome" => $nome);

        }

        return $result;

    }

    static function getTemplateMaterialiUtilizzo($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        $records = array();

        $table = "";

        $dati_rilevazione = self::getDatiRilevazione($record);
        $ruoli_relazionati = self::getRuoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );
        $materiali_relazionati = self::getMaterialiUtilizzoRelazionatiArea( $dati_rilevazione["area_stabilimento"] );

        $table = "<table style='width: 99%; margin: auto;' class='table table-striped table-hover tabella_rilevazione'>";
        $table .= "<thead>";
        $table .= "<tr>";

        $table .= "<th style='width: 150px;'>Materiali di Utilizzo</th>";
        $table .= "<th>Pericoli</th>";
        $table .= "<th style='width: 140px;'>Probabilità</th>";
        $table .= "<th style='width: 140px;'>Magnitudo</th>";
        $table .= "<th style='width: 120px;'>Rischio</th>";

        foreach( $ruoli_relazionati as $ruolo ){
            $table .= "<th style='width: 40px; text-align:center;'><span class='vertical-rl'>".$ruolo["nome"]."</span></th>";
        }

        $table .= "</tr>";
        $table .= "</thead>";

        $table .= "<tbody>";

        $prima_riga = true;

        foreach($materiali_relazionati as $materiale){

            if( count($materiale["lista_pericoli"]) > 0 ){

                $prima_riga = true;

                foreach($materiale["lista_pericoli"] as $pericolo){

                    $dati_riga = self::getPericoloRilevazione($record, $pericolo["id"], $materiale["id"]);

                    $table .= "<tr id='".$materiale["id"]."_".$pericolo["id"]."' class='tr_pericolo'>";

                    if($prima_riga){

                        $prima_riga = false;
                        
                        $table .= "<td rowspan='".count($materiale["lista_pericoli"])."'><b><span>";
                        $table .= $materiale["nome"];
                        $table .= "</span></b></td>";

                    }

                    $table .= "<td class='td_pericolo' style='vertical-align: middle;'>";
                    $table .= "<div class='checkbox'>";
                    $table .= "<label>";
                    if( $dati_riga["check"] ){
                        $table .= "<input type='checkbox' class='td_attivo' checked readonly disabled >";
                    }
                    else{
                        $table .= "<input type='checkbox' class='td_attivo' readonly disabled >";
                    }
                    $table .= "<b><span style='vertical-align: middle; margin-left: 10px;' >".$pericolo["nome"]."</span></b></label>";
                    $table .= "</div>";
                    $table .= "</b></td>";

                    if( $dati_riga["check"] && $dati_riga["rischio"] != "" ){
                        $table .= "<td class='td_probabilita' style='vertical-align: middle'>".$dati_riga["probabilita"]."</td>";
                        $table .= "<td class='td_magnitudo' style='vertical-align: middle'>".$dati_riga["magnitudo"]."</td>";
                        $table .= "<td class='td_rischio' style='vertical-align: middle'>".$dati_riga["rischio"]." - ".$dati_riga["frase_di_rischio"]."</td>";
                    }
                    else{
                        $table .= "<td class='td_probabilita' style='vertical-align: middle'></td>";
                        $table .= "<td class='td_magnitudo' style='vertical-align: middle'></td>";
                        $table .= "<td class='td_rischio' style='vertical-align: middle'></td>";
                    }
                    
                    $table .= self::getTabellaRuoli($ruoli_relazionati, $materiale["id"], $dati_riga, $pericolo["id"]);

                    $table .= "</tr>";

                    $records[] = array("related_to" => $materiale["id"],
                                        "pericolo_id" => $pericolo["id"],
                                        "pericolo_nome" => $pericolo["nome"]);

                }      

            }

        }

        $table .= "</tbody>";

        $table .= "</table>";

        $result = array("table" => $table,
                        "records" => $records);

        return $result;

    }

    static function getMaterialiUtilizzoRelazionatiArea($area){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT t.* FROM
                    ((SELECT 
                    rel.relcrmid id,
                    mat.kp_nome_materiale nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kpmaterialiutilizzo mat ON mat.kpmaterialiutilizzoid = rel.relcrmid
                    WHERE ent.deleted = 0 AND rel.module = 'KpAreeStabilimento' AND rel.relmodule = 'KpMaterialiUtilizzo' AND rel.crmid = ".$area.")
                    UNION
                    (SELECT 
                    rel.crmid id,
                    mat.kp_nome_materiale nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kpmaterialiutilizzo mat ON mat.kpmaterialiutilizzoid = rel.crmid
                    WHERE ent.deleted = 0 AND rel.relmodule = 'KpAreeStabilimento' AND rel.module = 'KpMaterialiUtilizzo' AND rel.relcrmid = ".$area.")) AS t
                    ORDER BY t.nome ASC";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $pericoli_relazionati = self::getPericoliRelazionatiMaterialiUtilizzo( $id );
            
            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "lista_pericoli" => $pericoli_relazionati);

        }

        return $result;

    }

    static function getPericoliRelazionatiMaterialiUtilizzo($materiale){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT t.* FROM
                    ((SELECT 
                    rel.relcrmid id,
                    risch.kp_nome_rischio nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kprischidvr risch ON risch.kprischidvrid = rel.relcrmid
                    WHERE ent.deleted = 0 AND rel.module = 'KpMaterialiUtilizzo' AND rel.relmodule = 'KpRischiDVR' AND rel.crmid = ".$materiale.")
                    UNION
                    (SELECT 
                    rel.crmid id,
                    risch.kp_nome_rischio nome
                    FROM {$table_prefix}_crmentityrel rel
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rel.relcrmid
                    INNER JOIN {$table_prefix}_kprischidvr risch ON risch.kprischidvrid = rel.crmid
                    WHERE ent.deleted = 0 AND rel.relmodule = 'KpMaterialiUtilizzo' AND rel.module = 'KpRischiDVR' AND rel.relcrmid = ".$materiale.")) AS t
                    ORDER BY t.nome ASC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);
            
            $result[] = array("id" => $id,
                                "nome" => $nome);

        }

        return $result;

    }

    static function getTabellaRuoliPDF($ruoli_relazionati, $related_to, $dati_riga, $pericolo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $table = "";

        foreach( $ruoli_relazionati as $ruolo ){

            $table .= "<td style='vertical-align: middle; text-align:center; border-bottom:1px solid silver;'>";

            if( $dati_riga["check"] ){

                if( self::checkIfRuoloRelazionatoARiga($dati_riga["id_riga_ril"], $ruolo["id"]) ){
                    $table .= "<input type='checkbox' checked='checked' />";
                }
                else{
                    $table .= "<input type='checkbox' />";
                }
                
            }
            else{
                $table .= "<input type='checkbox' />";
            }

            $table .= "</td>";

        }

        return $table;

    }

    static function getTemplateAreaPDF($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        $table = "";

        $dati_rilevazione = self::getDatiRilevazione($record);
        $ruoli_relazionati = self::getRuoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );
        $pericoli_relazionati = self::getPericoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );

        $related_to = $dati_rilevazione["area_stabilimento"];

        $table = "<table border='0' cellpadding='3' cellspacing='1' style='width:100%; border-collapse:collapse;'>";
        $table .= "<tbody>";
        $table .= "<tr>";

        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Area</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 200px;'><b>Pericoli</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Probabilità</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Magnitudo</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Rischio</b></td>";

        foreach( $ruoli_relazionati as $ruolo ){
            $table .= "<td style='border-bottom:2px solid silver; width: 40px; text-align:center;'><b>".$ruolo["nome"]."</b></td>";
        }

        $table .= "</tr>";

        $prima_riga = true;

        if( count($pericoli_relazionati) > 0 ){

            foreach($pericoli_relazionati as $pericolo){

                $dati_riga = self::getPericoloRilevazione($record, $pericolo["id"], $dati_rilevazione["area_stabilimento"]);

                $table .= "<tr>";

                if($prima_riga){

                    $prima_riga = false;
                    
                    $table .= "<td style='border-bottom:1px solid silver; vertical-align: top;' rowspan='".count($pericoli_relazionati)."'><b><span>";
                    $table .= $dati_rilevazione["nome_area_stabilimento"];
                    $table .= "</span></b></td>";

                }

                $table .= "<td style='vertical-align: middle; border-bottom:1px solid silver;'>";
                if( $dati_riga["check"] ){
                    $table .= "<input type='checkbox' checked='checked' />";
                }
                else{
                    $table .= "<input type='checkbox' />";
                }
                $table .= "&nbsp; &nbsp;<b>".$pericolo["nome"]."</b>";
                $table .= "</td>";

                if( $dati_riga["check"] ){
                    $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'>".$dati_riga["probabilita"]."</td>";
                    $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'>".$dati_riga["magnitudo"]."</td>";
                    $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'>".$dati_riga["rischio"]." - ".$dati_riga["frase_di_rischio"]."</td>";
                }
                else{
                    $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'></td>";
                    $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'></td>";
                    $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'></td>";
                }

                $table .= self::getTabellaRuoliPDF($ruoli_relazionati, $related_to, $dati_riga, $pericolo["id"]);

                $table .= "</tr>";

            }

        }

        $table .= "</tbody>";

        $table .= "</table>";

        return $table;

    }

    static function getTemplateAttivitaPDF($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        $table = "";

        $dati_rilevazione = self::getDatiRilevazione($record);
        $ruoli_relazionati = self::getRuoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );
        $attivita_relazionate = self::getAttivitaRelazionateArea( $dati_rilevazione["area_stabilimento"] );

        $table = "<table border='0' cellpadding='3' cellspacing='1' style='width:100%; border-collapse:collapse;'>";
        $table .= "<tbody>";
        $table .= "<tr>";

        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Attività</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 200px;'><b>Pericoli</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Probabilità</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Magnitudo</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Rischio</b></td>";

        foreach( $ruoli_relazionati as $ruolo ){
            $table .= "<td style='border-bottom:2px solid silver; width: 40px; text-align:center;'><b>".$ruolo["nome"]."</b></td>";
        }

        $table .= "</tr>";

        $prima_riga = true;

        foreach($attivita_relazionate as $attivita){

            if( count($attivita["lista_pericoli"]) > 0 ){

                $prima_riga = true;

                foreach($attivita["lista_pericoli"] as $pericolo){

                    $dati_riga = self::getPericoloRilevazione($record, $pericolo["id"], $attivita["id"]);

                    $table .= "<tr>";

                    if($prima_riga){

                        $prima_riga = false;
                        
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: top;' rowspan='".count($attivita["lista_pericoli"])."'><b><span>";
                        $table .= $attivita["nome"];
                        $table .= "</span></b></td>";

                    }

                    $table .= "<td style='vertical-align: middle; border-bottom:1px solid silver;'>";
                    if( $dati_riga["check"] ){
                        $table .= "<input type='checkbox' checked='checked' />";
                    }
                    else{
                        $table .= "<input type='checkbox' />";
                    }
                    $table .= "&nbsp; &nbsp;<b>".$pericolo["nome"]."</b>";
                    $table .= "</td>";

                    if( $dati_riga["check"] ){
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'>".$dati_riga["probabilita"]."</td>";
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'>".$dati_riga["magnitudo"]."</td>";
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'>".$dati_riga["rischio"]." - ".$dati_riga["frase_di_rischio"]."</td>";
                    }
                    else{
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'></td>";
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'></td>";
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'></td>";
                    }

                    $table .= self::getTabellaRuoliPDF($ruoli_relazionati, $attivita["id"], $dati_riga, $pericolo["id"]);

                    $table .= "</tr>";

                }      

            }

        }

        $table .= "</tbody>";

        $table .= "</table>";

        return $table;

    }

    static function getTemplateImpiantiPDF($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        $table = "";

        $dati_rilevazione = self::getDatiRilevazione($record);
        $ruoli_relazionati = self::getRuoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );
        $tipi_impianti_relazionati = self::getTipiImpiantiRelazionatiArea( $dati_rilevazione["area_stabilimento"] );

        $table = "<table border='0' cellpadding='3' cellspacing='1' style='width:100%; border-collapse:collapse;'>";
        $table .= "<tbody>";
        $table .= "<tr>";

        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Tipi Impianto</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 200px;'><b>Pericoli</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Probabilità</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Magnitudo</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Rischio</b></td>";

        foreach( $ruoli_relazionati as $ruolo ){
            $table .= "<td style='border-bottom:2px solid silver; width: 40px; text-align:center;'><b>".$ruolo["nome"]."</b></td>";
        }

        $table .= "</tr>";

        $prima_riga = true;

        foreach($tipi_impianti_relazionati as $tipo_impianto){

            if( count($tipo_impianto["lista_pericoli"]) > 0 ){

                $prima_riga = true;

                foreach($tipo_impianto["lista_pericoli"] as $pericolo){

                    $dati_riga = self::getPericoloRilevazione($record, $pericolo["id"], $tipo_impianto["id"]);

                    $table .= "<tr>";

                    if($prima_riga){

                        $prima_riga = false;
                        
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: top;' rowspan='".count($tipo_impianto["lista_pericoli"])."'><b><span>";
                        $table .= $tipo_impianto["nome"];
                        $table .= "</span></b></td>";

                    }

                    $table .= "<td style='vertical-align: middle; border-bottom:1px solid silver;'>";
                    if( $dati_riga["check"] ){
                        $table .= "<input type='checkbox' checked='checked' />";
                    }
                    else{
                        $table .= "<input type='checkbox' />";
                    }
                    $table .= "&nbsp; &nbsp;<b>".$pericolo["nome"]."</b>";
                    $table .= "</td>";

                    if( $dati_riga["check"] ){
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'>".$dati_riga["probabilita"]."</td>";
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'>".$dati_riga["magnitudo"]."</td>";
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'>".$dati_riga["rischio"]." - ".$dati_riga["frase_di_rischio"]."</td>";
                    }
                    else{
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'></td>";
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'></td>";
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'></td>";
                    }

                    $table .= self::getTabellaRuoliPDF($ruoli_relazionati, $tipo_impianto["id"], $dati_riga, $pericolo["id"]);

                    $table .= "</tr>";

                }      

            }

        }

        $table .= "</tbody>";

        $table .= "</table>";

        return $table;

    }

    static function getTemplateSostanzeChimichePDF($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        $table = "";

        $dati_rilevazione = self::getDatiRilevazione($record);
        $ruoli_relazionati = self::getRuoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );
        $sostanze_chiniche_relazionate = self::getSostanzeChimicheRelazionateArea( $dati_rilevazione["area_stabilimento"] );

        $table = "<table border='0' cellpadding='3' cellspacing='1' style='width:100%; border-collapse:collapse;'>";
        $table .= "<tbody>";
        $table .= "<tr>";

        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Sostanze Chimiche</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 200px;'><b>Pericoli</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Probabilità</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Magnitudo</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Rischio</b></td>";

        foreach( $ruoli_relazionati as $ruolo ){
            $table .= "<td style='border-bottom:2px solid silver; width: 40px; text-align:center;'><b>".$ruolo["nome"]."</b></td>";
        }

        $table .= "</tr>";

        $prima_riga = true;

        foreach($sostanze_chiniche_relazionate as $sostanze_chimica){

            if( count($sostanze_chimica["lista_pericoli"]) > 0 ){

                $prima_riga = true;

                foreach($sostanze_chimica["lista_pericoli"] as $pericolo){

                    $dati_riga = self::getPericoloRilevazione($record, $pericolo["id"], $sostanze_chimica["id"]);

                    $table .= "<tr>";

                    if($prima_riga){

                        $prima_riga = false;
                        
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: top;' rowspan='".count($sostanze_chimica["lista_pericoli"])."'><b><span>";
                        $table .= $sostanze_chimica["nome"];
                        $table .= "</span></b></td>";

                    }

                    $table .= "<td style='vertical-align: middle; border-bottom:1px solid silver;'>";
                    if( $dati_riga["check"] ){
                        $table .= "<input type='checkbox' checked='checked' />";
                    }
                    else{
                        $table .= "<input type='checkbox' />";
                    }
                    $table .= "&nbsp; &nbsp;<b>".$pericolo["nome"]."</b>";
                    $table .= "</td>";

                    if( $dati_riga["check"] ){
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'>".$dati_riga["probabilita"]."</td>";
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'>".$dati_riga["magnitudo"]."</td>";
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'>".$dati_riga["rischio"]." - ".$dati_riga["frase_di_rischio"]."</td>";
                    }
                    else{
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'></td>";
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'></td>";
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'></td>";
                    }
                    
                    $table .= self::getTabellaRuoliPDF($ruoli_relazionati, $sostanze_chimica["id"], $dati_riga, $pericolo["id"]);

                    $table .= "</tr>";

                }      

            }

        }

        $table .= "</tbody>";

        $table .= "</table>";

        return $table;

    }

    static function getTemplateMaterialiUtilizzoPDF($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        $table = "";

        $dati_rilevazione = self::getDatiRilevazione($record);
        $ruoli_relazionati = self::getRuoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );
        $materiali_relazionati = self::getMaterialiUtilizzoRelazionatiArea( $dati_rilevazione["area_stabilimento"] );

        $table = "<table border='0' cellpadding='3' cellspacing='1' style='width:100%; border-collapse:collapse;'>";
        $table .= "<tbody>";
        $table .= "<tr>";

        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Materiali di Utilizzo</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 200px;'><b>Pericoli</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Probabilità</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Magnitudo</b></td>";
        $table .= "<td style='border-bottom:2px solid silver; width: 120px;'><b>Rischio</b></td>";

        foreach( $ruoli_relazionati as $ruolo ){
            $table .= "<td style='border-bottom:2px solid silver; width: 40px; text-align:center;'><b>".$ruolo["nome"]."</b></td>";
        }

        $table .= "</tr>";

        $prima_riga = true;

        foreach($materiali_relazionati as $materiale){

            if( count($materiale["lista_pericoli"]) > 0 ){

                $prima_riga = true;

                foreach($materiale["lista_pericoli"] as $pericolo){

                    $dati_riga = self::getPericoloRilevazione($record, $pericolo["id"], $materiale["id"]);

                    $table .= "<tr>";

                    if($prima_riga){

                        $prima_riga = false;
                        
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: top;' rowspan='".count($materiale["lista_pericoli"])."'><b><span>";
                        $table .= $materiale["nome"];
                        $table .= "</span></b></td>";

                    }

                    $table .= "<td style='vertical-align: middle; border-bottom:1px solid silver;'>";
                    if( $dati_riga["check"] ){
                        $table .= "<input type='checkbox' checked='checked' />";
                    }
                    else{
                        $table .= "<input type='checkbox' />";
                    }
                    $table .= "&nbsp; &nbsp;<b>".$pericolo["nome"]."</b>";
                    $table .= "</td>";

                    if( $dati_riga["check"] ){
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'>".$dati_riga["probabilita"]."</td>";
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'>".$dati_riga["magnitudo"]."</td>";
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'>".$dati_riga["rischio"]." - ".$dati_riga["frase_di_rischio"]."</td>";
                    }
                    else{
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'></td>";
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'></td>";
                        $table .= "<td style='border-bottom:1px solid silver; vertical-align: middle'></td>";
                    }
                    
                    $table .= self::getTabellaRuoliPDF($ruoli_relazionati, $materiale["id"], $dati_riga, $pericolo["id"]);

                    $table .= "</tr>";

                }      

            }

        }

        $table .= "</tbody>";

        $table .= "</table>";

        return $table;

    }

    static function getPDF($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__."/../../../modules/PDFMaker/InventoryPDF.php");
        require_once(__DIR__."/../../../include/mpdf/mpdf.php"); 
        require_once(__DIR__."/../../../modules/SproCore/SproUtils/spro_utils.php");

        $tabella_area = self::getTemplateAreaPDF($record);

        $tabella_attivita = self::getTemplateAttivitaPDF($record);

        $tabella_impianti = self::getTemplateImpiantiPDF($record);

        $tabella_sostanze_chimiche = self::getTemplateSostanzeChimichePDF($record);

        $tabella_sostanze_materiali_utilizzo = self::getTemplateMaterialiUtilizzoPDF($record);

        $id_statici = getConfigurazioniIdStatici();
        $id_statico = $id_statici["PDF Maker - Template Prospetto Rilevazione Rischi"];
        if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
            die;
        }
        else{
            $templateid = $id_statico["valore"];
        }

        $relmodule = "KpRilevazioniRischi";
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

        $body_html = str_replace("#TABELLA_RISCHI_AREA#", $tabella_area, $body_html);
        $body_html = str_replace("#TABELLA_RISCHI_ATTIVITA#", $tabella_attivita, $body_html);
        $body_html = str_replace("#TABELLA_RISCHI_IMPIANTI#", $tabella_impianti, $body_html);
        $body_html = str_replace("#TABELLA_RISCHI_SOSTANZE_CHIMICHE#", $tabella_sostanze_chimiche, $body_html);
        $body_html = str_replace("#TABELLA_RISCHI_MATERIALI#", $tabella_sostanze_materiali_utilizzo, $body_html);

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
        
        //Questa parte servirebbe se volessi salvare il documento sul mio computer
        $mpdf->Output('cache/'.$name.'.pdf');

        @ob_clean();
        header('Content-Type: application/pdf');
        header("Content-length: ".filesize("./cache/$name.pdf"));
        header("Cache-Control: private");
        header("Content-Disposition: attachment; filename=$name.pdf");
        header("Content-Description: PHP Generated Data");
        echo fread(fopen("./cache/$name.pdf", "r"),filesize("./cache/$name.pdf"));
                    
        @unlink("cache/$name.pdf");
                            
        //$upload_file_path = decideFilePath();

    }

    static function getTabellaRuoliExcel(&$objPHPExcel, &$column, &$row, $ruoli_relazionati, $related_to, $dati_riga, $pericolo){
        global $adb, $table_prefix, $default_charset, $current_user;
        
        foreach( $ruoli_relazionati as $ruolo ){

            if( $dati_riga["check"] ){

                if( self::checkIfRuoloRelazionatoARiga($dati_riga["id_riga_ril"], $ruolo["id"]) ){

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, 'X');
                }
                
            }

            $column++;
        }

        return $objPHPExcel;

    }

    static function getTemplateAreaExcel($record, &$objPHPExcel, $numero_foglio){
        global $adb, $table_prefix, $default_charset, $current_user;

        $dati_rilevazione = self::getDatiRilevazione($record);
        $ruoli_relazionati = self::getRuoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );
        $pericoli_relazionati = self::getPericoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );

        $related_to = $dati_rilevazione["area_stabilimento"];

        if($numero_foglio > 0){
            $objPHPExcel->createSheet($numero_foglio);
        }

        $objPHPExcel->setActiveSheetIndex($numero_foglio);

        $objPHPExcel->getActiveSheet()->setTitle("Rischi Area");

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'Area')
                ->setCellValue('C1', 'Pericoli')
                ->setCellValue('D1', 'Probabilità')
                ->setCellValue('E1', 'Magnitudo')
                ->setCellValue('F1', 'Rischio');

        $column = 'G';
        foreach( $ruoli_relazionati as $ruolo ){
            $objPHPExcel->getActiveSheet()->setCellValue($column.'1', $ruolo["nome"]);
            $column++;
        }

        $prima_riga = true;

        if( count($pericoli_relazionati) > 0 ){

            $row = 2;

            foreach($pericoli_relazionati as $pericolo){

                $column = 'A';

                $dati_riga = self::getPericoloRilevazione($record, $pericolo["id"], $dati_rilevazione["area_stabilimento"]);

                if($prima_riga){

                    $prima_riga = false;

                    $objPHPExcel->getActiveSheet()->mergeCells($column.$row.':'.$column.(count($pericoli_relazionati) + $row - 1));

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_rilevazione["nome_area_stabilimento"]);

                }

                $column++;

                if( $dati_riga["check"] ){
                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, 'X');
                }

                $column++;

                $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["nome"]);

                $column++;

                if( $dati_riga["check"] ){
                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_riga["probabilita"]);
                    $column++;
                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_riga["magnitudo"]);
                    $column++;
                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_riga["rischio"]." - ".$dati_riga["frase_di_rischio"]);
                    $column++;
                }
                else{
                    $column++;
                    $column++;
                    $column++;
                }

                self::getTabellaRuoliExcel($objPHPExcel, $column, $row, $ruoli_relazionati, $related_to, $dati_riga, $pericolo["id"]);

                $row++;
            }

        }

        self::setStyleExcel($objPHPExcel, $column, $row, $numero_foglio);

    }

    static function getTemplateAttivitaExcel($record, &$objPHPExcel, $numero_foglio){
        global $adb, $table_prefix, $default_charset, $current_user;

        $dati_rilevazione = self::getDatiRilevazione($record);
        $ruoli_relazionati = self::getRuoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );
        $attivita_relazionate = self::getAttivitaRelazionateArea( $dati_rilevazione["area_stabilimento"] );

        if($numero_foglio > 0){
            $objPHPExcel->createSheet($numero_foglio);
        }

        $objPHPExcel->setActiveSheetIndex($numero_foglio);

        $objPHPExcel->getActiveSheet()->setTitle("Rischi Attività");

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'Attività')
                ->setCellValue('C1', 'Pericoli')
                ->setCellValue('D1', 'Probabilità')
                ->setCellValue('E1', 'Magnitudo')
                ->setCellValue('F1', 'Rischio');

        $column = 'G';
        foreach( $ruoli_relazionati as $ruolo ){
            $objPHPExcel->getActiveSheet()->setCellValue($column.'1', $ruolo["nome"]);
            $column++;
        }

        $prima_riga = true;

        $row = 2;

        foreach($attivita_relazionate as $attivita){

            if( count($attivita["lista_pericoli"]) > 0 ){

                $prima_riga = true;

                foreach($attivita["lista_pericoli"] as $pericolo){

                    $column = 'A';

                    $dati_riga = self::getPericoloRilevazione($record, $pericolo["id"], $attivita["id"]);

                    if($prima_riga){

                        $prima_riga = false;

                        $objPHPExcel->getActiveSheet()->mergeCells($column.$row.':'.$column.(count($attivita["lista_pericoli"]) + $row - 1));

                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $attivita["nome"]);

                    }

                    $column++;

                    if( $dati_riga["check"] ){
                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, 'X');
                    }

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["nome"]);

                    $column++;

                    if( $dati_riga["check"] ){
                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_riga["probabilita"]);
                        $column++;
                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_riga["magnitudo"]);
                        $column++;
                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_riga["rischio"]." - ".$dati_riga["frase_di_rischio"]);
                        $column++;
                    }
                    else{
                        $column++;
                        $column++;
                        $column++;
                    }

                    self::getTabellaRuoliExcel($objPHPExcel, $column, $row, $ruoli_relazionati, $attivita["id"], $dati_riga, $pericolo["id"]);

                    $row++;

                }      

            }

        }

        self::setStyleExcel($objPHPExcel, $column, $row, $numero_foglio);

    }

    static function getTemplateImpiantiExcel($record, &$objPHPExcel, $numero_foglio){
        global $adb, $table_prefix, $default_charset, $current_user;

        $dati_rilevazione = self::getDatiRilevazione($record);
        $ruoli_relazionati = self::getRuoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );
        $tipi_impianti_relazionati = self::getTipiImpiantiRelazionatiArea( $dati_rilevazione["area_stabilimento"] );

        if($numero_foglio > 0){
            $objPHPExcel->createSheet($numero_foglio);
        }

        $objPHPExcel->setActiveSheetIndex($numero_foglio);

        $objPHPExcel->getActiveSheet()->setTitle("Rischi Impianti");

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'Tipi Impianto')
                ->setCellValue('C1', 'Pericoli')
                ->setCellValue('D1', 'Probabilità')
                ->setCellValue('E1', 'Magnitudo')
                ->setCellValue('F1', 'Rischio');

        $column = 'G';
        foreach( $ruoli_relazionati as $ruolo ){
            $objPHPExcel->getActiveSheet()->setCellValue($column.'1', $ruolo["nome"]);
            $column++;
        }

        $prima_riga = true;

        $row = 2;

        foreach($tipi_impianti_relazionati as $tipo_impianto){

            if( count($tipo_impianto["lista_pericoli"]) > 0 ){

                $prima_riga = true;

                foreach($tipo_impianto["lista_pericoli"] as $pericolo){

                    $column = 'A';

                    $dati_riga = self::getPericoloRilevazione($record, $pericolo["id"], $tipo_impianto["id"]);

                    if($prima_riga){

                        $prima_riga = false;

                        $objPHPExcel->getActiveSheet()->mergeCells($column.$row.':'.$column.(count($tipo_impianto["lista_pericoli"]) + $row - 1));

                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $tipo_impianto["nome"]);

                    }

                    $column++;

                    if( $dati_riga["check"] ){
                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, 'X');
                    }

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["nome"]);

                    $column++;

                    if( $dati_riga["check"] ){
                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_riga["probabilita"]);
                        $column++;
                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_riga["magnitudo"]);
                        $column++;
                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_riga["rischio"]." - ".$dati_riga["frase_di_rischio"]);
                        $column++;
                    }
                    else{
                        $column++;
                        $column++;
                        $column++;
                    }

                    self::getTabellaRuoliExcel($objPHPExcel, $column, $row, $ruoli_relazionati, $tipo_impianto["id"], $dati_riga, $pericolo["id"]);

                    $row++;

                }      

            }

        }

        self::setStyleExcel($objPHPExcel, $column, $row, $numero_foglio);

    }

    static function getTemplateSostanzeChimicheExcel($record, &$objPHPExcel, $numero_foglio){
        global $adb, $table_prefix, $default_charset, $current_user;

        $dati_rilevazione = self::getDatiRilevazione($record);
        $ruoli_relazionati = self::getRuoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );
        $sostanze_chiniche_relazionate = self::getSostanzeChimicheRelazionateArea( $dati_rilevazione["area_stabilimento"] );

        if($numero_foglio > 0){
            $objPHPExcel->createSheet($numero_foglio);
        }

        $objPHPExcel->setActiveSheetIndex($numero_foglio);

        $objPHPExcel->getActiveSheet()->setTitle("Rischi Sostanze Chimiche");

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'Sostanze Chimiche')
                ->setCellValue('C1', 'Pericoli')
                ->setCellValue('D1', 'Probabilità')
                ->setCellValue('E1', 'Magnitudo')
                ->setCellValue('F1', 'Rischio');

        $column = 'G';
        foreach( $ruoli_relazionati as $ruolo ){
            $objPHPExcel->getActiveSheet()->setCellValue($column.'1', $ruolo["nome"]);
            $column++;
        }

        $prima_riga = true;

        $row = 2;

        foreach($sostanze_chiniche_relazionate as $sostanze_chimica){

            if( count($sostanze_chimica["lista_pericoli"]) > 0 ){

                $prima_riga = true;

                foreach($sostanze_chimica["lista_pericoli"] as $pericolo){

                    $column = 'A';

                    $dati_riga = self::getPericoloRilevazione($record, $pericolo["id"], $sostanze_chimica["id"]);

                    if($prima_riga){

                        $prima_riga = false;

                        $objPHPExcel->getActiveSheet()->mergeCells($column.$row.':'.$column.(count($sostanze_chimica["lista_pericoli"]) + $row - 1));

                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $sostanze_chimica["nome"]);

                    }

                    $column++;

                    if( $dati_riga["check"] ){
                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, 'X');
                    }

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["nome"]);

                    $column++;

                    if( $dati_riga["check"] ){
                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_riga["probabilita"]);
                        $column++;
                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_riga["magnitudo"]);
                        $column++;
                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_riga["rischio"]." - ".$dati_riga["frase_di_rischio"]);
                        $column++;
                    }
                    else{
                        $column++;
                        $column++;
                        $column++;
                    }

                    self::getTabellaRuoliExcel($objPHPExcel, $column, $row, $ruoli_relazionati, $sostanze_chimica["id"], $dati_riga, $pericolo["id"]);

                    $row++;

                }      

            }

        }

        self::setStyleExcel($objPHPExcel, $column, $row, $numero_foglio);

    }

    static function getTemplateMaterialiUtilizzoExcel($record, &$objPHPExcel, $numero_foglio){
        global $adb, $table_prefix, $default_charset, $current_user;

        $dati_rilevazione = self::getDatiRilevazione($record);
        $ruoli_relazionati = self::getRuoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );
        $materiali_relazionati = self::getMaterialiUtilizzoRelazionatiArea( $dati_rilevazione["area_stabilimento"] );

        if($numero_foglio > 0){
            $objPHPExcel->createSheet($numero_foglio);
        }

        $objPHPExcel->setActiveSheetIndex($numero_foglio);

        $objPHPExcel->getActiveSheet()->setTitle("Rischi Mat. di Utilizzo");

        $objPHPExcel->getActiveSheet()
                ->setCellValue('A1', 'Materiali di Utilizzo')
                ->setCellValue('C1', 'Pericoli')
                ->setCellValue('D1', 'Probabilità')
                ->setCellValue('E1', 'Magnitudo')
                ->setCellValue('F1', 'Rischio');

        $column = 'G';
        foreach( $ruoli_relazionati as $ruolo ){
            $objPHPExcel->getActiveSheet()->setCellValue($column.'1', $ruolo["nome"]);
            $column++;
        }

        $prima_riga = true;

        $row = 2;

        foreach($materiali_relazionati as $materiale){

            if( count($materiale["lista_pericoli"]) > 0 ){

                $prima_riga = true;

                foreach($materiale["lista_pericoli"] as $pericolo){

                    $column = 'A';

                    $dati_riga = self::getPericoloRilevazione($record, $pericolo["id"], $materiale["id"]);

                    if($prima_riga){

                        $prima_riga = false;

                        $objPHPExcel->getActiveSheet()->mergeCells($column.$row.':'.$column.(count($materiale["lista_pericoli"]) + $row - 1));

                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $materiale["nome"]);

                    }

                    $column++;

                    if( $dati_riga["check"] ){
                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, 'X');
                    }

                    $column++;

                    $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $pericolo["nome"]);

                    $column++;

                    if( $dati_riga["check"] ){
                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_riga["probabilita"]);
                        $column++;
                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_riga["magnitudo"]);
                        $column++;
                        $objPHPExcel->getActiveSheet()->setCellValue($column.$row, $dati_riga["rischio"]." - ".$dati_riga["frase_di_rischio"]);
                        $column++;
                    }
                    else{
                        $column++;
                        $column++;
                        $column++;
                    }

                    self::getTabellaRuoliExcel($objPHPExcel, $column, $row, $ruoli_relazionati, $materiale["id"], $dati_riga, $pericolo["id"]);

                    $row++;

                }      

            }

        }

        self::setStyleExcel($objPHPExcel, $column, $row, $numero_foglio);

    }

    static function getExcel($record){
        global $adb, $table_prefix, $default_charset, $current_user;
        
        require_once(__DIR__."/../../../include/PHPExcel/PHPExcel.php");

        $objPHPExcel = new PHPExcel();

        $objPHPExcel->getProperties()->setCreator("Kpro Consulting")
                ->setLastModifiedBy("Kpro Consulting")
                ->setTitle("Excel Rilevazione Rischi ".$data_corrente_inv)
                ->setSubject("Excel Rilevazione Rischi ".$data_corrente_inv)
                ->setDescription("Excel Rilevazione Rischi ".$data_corrente_inv." for Office 2007 XLSX, generated using PHP classes.")
                ->setKeywords("office 2007 openxml php")
                ->setCategory("Excel Rilevazione Rischi ".$data_corrente_inv);

        $numero_foglio = 0;
       
        self::getTemplateAreaExcel($record, $objPHPExcel, $numero_foglio);

        $numero_foglio++;

        self::getTemplateAttivitaExcel($record, $objPHPExcel, $numero_foglio);

        $numero_foglio++;

        self::getTemplateImpiantiExcel($record, $objPHPExcel, $numero_foglio);

        $numero_foglio++;

        self::getTemplateSostanzeChimicheExcel($record, $objPHPExcel, $numero_foglio);

        $numero_foglio++;

        self::getTemplateMaterialiUtilizzoExcel($record, $objPHPExcel, $numero_foglio);

        $name = date("YmdHis")."_Rilevazione_Rischi";

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
        $objPHPExcel->getActiveSheet()->getStyle("C2:C".$row)->getFont()->setBold(true);

        $objPHPExcel->getActiveSheet()->getStyle("G1:".$column."1")->getAlignment()->setWrapText(true);
        $objPHPExcel->getActiveSheet()->getStyle("A2:".$column.$row)->getAlignment()->setWrapText(true);

        $style_alignment_horizontal_center = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle("G1:".$column.$row)->applyFromArray($style_alignment_horizontal_center);
        $objPHPExcel->getActiveSheet()->getStyle("B1:B".$row)->applyFromArray($style_alignment_horizontal_center);

        $style_alignment_vertical_top = array(
            'alignment' => array(
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_TOP,
            )
        );
        $objPHPExcel->getActiveSheet()->getStyle("A2:A".$row)->applyFromArray($style_alignment_vertical_top);

        $objPHPExcel->getActiveSheet()->getStyle("G1:".$column."1")->getAlignment()->setTextRotation(-90);

        foreach(range('C','F') as $columnID) {
            $objPHPExcel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }

        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(30);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(5);
        $objPHPExcel->getActiveSheet()->getColumnDimension("G:".$column)->setWidth(10);

        $objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(150);

        $objPHPExcel->getActiveSheet()
            ->getPageSetup()
            ->setPaperSize(PHPExcel_Worksheet_PageSetup::PAPERSIZE_A4);

        return $objPHPExcel;
    }

    static function riportaRigheDaSituazioneRischiDVR($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        self::riportaRigheAreaDaSituazioneRischiDVR($record);

        self::riportaRigheAttivitaDaSituazioneRischiDVR($record);

        self::riportaRigheImpiantiDaSituazioneRischiDVR($record);

        self::riportaRigheSostanzeDaSituazioneRischiDVR($record);

        self::riportaRigheMaterialiDaSituazioneRischiDVR($record);

    }

    static function riportaRigheAreaDaSituazioneRischiDVR($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__.'/../KpSitRischiDVR/ClassKpSitRischiDVRKp.php');

        $dati_rilevazione = self::getDatiRilevazione($record);
        $pericoli_relazionati = self::getPericoliRelazionatiArea( $dati_rilevazione["area_stabilimento"] );

        $related_to = 0;

        foreach($pericoli_relazionati as $pericolo){

            $filtro_situazione = array("azienda_id" => $dati_rilevazione["azienda"],
                                        "stabilimento_id" => $dati_rilevazione["stabilimento"],
                                        "area_id" => $dati_rilevazione["area_stabilimento"],
                                        "related_to" => $related_to,
                                        "pericolo_id" => $pericolo["id"]);

            $situazione_rischio = KpSitRischiDVRKp::getRigaSituazioneByFiltro($filtro_situazione);

            if( $situazione_rischio["esiste"] ){

                $nome_riga = $pericolo["nome"];

                $focus = CRMEntity::getInstance('KpRilevazRischiRig');
                $focus->column_fields['assigned_user_id'] = $current_user->id;
                $focus->column_fields['kp_nome_riga'] = $nome_riga;
                $focus->column_fields['kp_rilevazione'] = $record;
                $focus->column_fields['kp_rischio'] = $pericolo["id"];
                $focus->column_fields['kp_related_to'] = $related_to;
                $focus->column_fields['kp_attivo'] = '1';
                $focus->column_fields['kp_gravita_rischio'] = $situazione_rischio["gravita_rischio"];
                $focus->column_fields['kp_probabilita_risc'] = $situazione_rischio["probabilita_rischio"];
                $focus->column_fields['kp_valutazione_risc'] = $situazione_rischio["valutazione_rischio"];
                $focus->column_fields['kp_frase_risc_dvr'] = $situazione_rischio["frase_di_rischio"];
                $focus->column_fields['kp_area_stab'] = $dati_rilevazione["area_stabilimento"];
                $focus->column_fields['kp_misurazione'] = $situazione_rischio["misurazione"];
                $focus->column_fields['description'] = $situazione_rischio["descrizione"];
                $focus->save('KpRilevazRischiRig', $longdesc=true, $offline_update=false, $triggerEvent=false); 

                $lista_ruoli = KpSitRischiDVRKp::getRuoliRelazionatiRigaSituazione( $situazione_rischio["id"] );
                
                foreach( $lista_ruoli as $ruolo ){

                    if( self::checkIfRuoloRelazionatoArea($dati_rilevazione["area_stabilimento"], $ruolo["id"]) ){

                        $insert = "INSERT INTO {$table_prefix}_crmentityrel (crmid, module, relcrmid, relmodule) 
                                    VALUES
                                    (".$focus->id.", 'KpRilevazRischiRig', ".$ruolo["id"].", 'KpRuoli')";
                        $adb->query($insert);

                        self::setRuoloRigaRilevazioneRischio($focus->id, $ruolo["id"]);

                    }
                    
                }

            }

        }

    }

    static function riportaRigheAttivitaDaSituazioneRischiDVR($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__.'/../KpSitRischiDVR/ClassKpSitRischiDVRKp.php');

        $dati_rilevazione = self::getDatiRilevazione($record);
        $attivita_relazionate = self::getAttivitaRelazionateArea( $dati_rilevazione["area_stabilimento"] );

        foreach($attivita_relazionate as $attivita){

            foreach($attivita["lista_pericoli"] as $pericolo){

                $filtro_situazione = array("azienda_id" => $dati_rilevazione["azienda"],
                                        "stabilimento_id" => $dati_rilevazione["stabilimento"],
                                        "area_id" => $dati_rilevazione["area_stabilimento"],
                                        "related_to" => $attivita["id"],
                                        "pericolo_id" => $pericolo["id"]);

                $situazione_rischio = KpSitRischiDVRKp::getRigaSituazioneByFiltro($filtro_situazione);

                if( $situazione_rischio["esiste"] ){

                    $nome_riga = $pericolo["nome"];

                    $focus = CRMEntity::getInstance('KpRilevazRischiRig');
                    $focus->column_fields['assigned_user_id'] = $current_user->id;
                    $focus->column_fields['kp_nome_riga'] = $nome_riga;
                    $focus->column_fields['kp_rilevazione'] = $record;
                    $focus->column_fields['kp_rischio'] = $pericolo["id"];
                    $focus->column_fields['kp_related_to'] = $attivita["id"];
                    $focus->column_fields['kp_attivo'] = '1';
                    $focus->column_fields['kp_gravita_rischio'] = $situazione_rischio["gravita_rischio"];
                    $focus->column_fields['kp_probabilita_risc'] = $situazione_rischio["probabilita_rischio"];
                    $focus->column_fields['kp_valutazione_risc'] = $situazione_rischio["valutazione_rischio"];
                    $focus->column_fields['kp_frase_risc_dvr'] = $situazione_rischio["frase_di_rischio"];
                    $focus->column_fields['kp_area_stab'] = $dati_rilevazione["area_stabilimento"];
                    $focus->column_fields['kp_misurazione'] = $situazione_rischio["misurazione"];
                    $focus->column_fields['description'] = $situazione_rischio["descrizione"];
                    $focus->save('KpRilevazRischiRig', $longdesc=true, $offline_update=false, $triggerEvent=false); 

                    $lista_ruoli = KpSitRischiDVRKp::getRuoliRelazionatiRigaSituazione( $situazione_rischio["id"] );
                    
                    foreach( $lista_ruoli as $ruolo ){

                        if( self::checkIfRuoloRelazionatoArea($dati_rilevazione["area_stabilimento"], $ruolo["id"]) ){

                            $insert = "INSERT INTO {$table_prefix}_crmentityrel (crmid, module, relcrmid, relmodule) 
                                        VALUES
                                        (".$focus->id.", 'KpRilevazRischiRig', ".$ruolo["id"].", 'KpRuoli')";
                            $adb->query($insert);
    
                        }
                        
                    }

                }

            }

        }

    }

    static function riportaRigheImpiantiDaSituazioneRischiDVR($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__.'/../KpSitRischiDVR/ClassKpSitRischiDVRKp.php');

        $dati_rilevazione = self::getDatiRilevazione($record);
        $tipi_impianti_relazionati = self::getTipiImpiantiRelazionatiArea( $dati_rilevazione["area_stabilimento"] );

        foreach($tipi_impianti_relazionati as $tipo_impianto){

            foreach($tipo_impianto["lista_pericoli"] as $pericolo){

                $filtro_situazione = array("azienda_id" => $dati_rilevazione["azienda"],
                                        "stabilimento_id" => $dati_rilevazione["stabilimento"],
                                        "area_id" => $dati_rilevazione["area_stabilimento"],
                                        "related_to" => $tipo_impianto["id"],
                                        "pericolo_id" => $pericolo["id"]);

                $situazione_rischio = KpSitRischiDVRKp::getRigaSituazioneByFiltro($filtro_situazione);

                if( $situazione_rischio["esiste"] ){

                    $nome_riga = $pericolo["nome"];

                    $focus = CRMEntity::getInstance('KpRilevazRischiRig');
                    $focus->column_fields['assigned_user_id'] = $current_user->id;
                    $focus->column_fields['kp_nome_riga'] = $nome_riga;
                    $focus->column_fields['kp_rilevazione'] = $record;
                    $focus->column_fields['kp_rischio'] = $pericolo["id"];
                    $focus->column_fields['kp_related_to'] = $tipo_impianto["id"];
                    $focus->column_fields['kp_attivo'] = '1';
                    $focus->column_fields['kp_gravita_rischio'] = $situazione_rischio["gravita_rischio"];
                    $focus->column_fields['kp_probabilita_risc'] = $situazione_rischio["probabilita_rischio"];
                    $focus->column_fields['kp_valutazione_risc'] = $situazione_rischio["valutazione_rischio"];
                    $focus->column_fields['kp_frase_risc_dvr'] = $situazione_rischio["frase_di_rischio"];
                    $focus->column_fields['kp_area_stab'] = $dati_rilevazione["area_stabilimento"];
                    $focus->column_fields['kp_misurazione'] = $situazione_rischio["misurazione"];
                    $focus->column_fields['description'] = $situazione_rischio["descrizione"];
                    $focus->save('KpRilevazRischiRig', $longdesc=true, $offline_update=false, $triggerEvent=false); 

                    $lista_ruoli = KpSitRischiDVRKp::getRuoliRelazionatiRigaSituazione( $situazione_rischio["id"] );
                    
                    foreach( $lista_ruoli as $ruolo ){

                        if( self::checkIfRuoloRelazionatoArea($dati_rilevazione["area_stabilimento"], $ruolo["id"]) ){

                            $insert = "INSERT INTO {$table_prefix}_crmentityrel (crmid, module, relcrmid, relmodule) 
                                        VALUES
                                        (".$focus->id.", 'KpRilevazRischiRig', ".$ruolo["id"].", 'KpRuoli')";
                            $adb->query($insert);
    
                        }
                        
                    }

                }

            }

        }

    }

    static function riportaRigheSostanzeDaSituazioneRischiDVR($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__.'/../KpSitRischiDVR/ClassKpSitRischiDVRKp.php');

        $dati_rilevazione = self::getDatiRilevazione($record);
        $sostanze_chiniche_relazionate = self::getSostanzeChimicheRelazionateArea( $dati_rilevazione["area_stabilimento"] );

        foreach($sostanze_chiniche_relazionate as $sostanze_chimica){

            foreach($sostanze_chimica["lista_pericoli"] as $pericolo){

                $filtro_situazione = array("azienda_id" => $dati_rilevazione["azienda"],
                                        "stabilimento_id" => $dati_rilevazione["stabilimento"],
                                        "area_id" => $dati_rilevazione["area_stabilimento"],
                                        "related_to" => $sostanze_chimica["id"],
                                        "pericolo_id" => $pericolo["id"]);

                $situazione_rischio = KpSitRischiDVRKp::getRigaSituazioneByFiltro($filtro_situazione);

                if( $situazione_rischio["esiste"] ){

                    $nome_riga = $pericolo["nome"];

                    $focus = CRMEntity::getInstance('KpRilevazRischiRig');
                    $focus->column_fields['assigned_user_id'] = $current_user->id;
                    $focus->column_fields['kp_nome_riga'] = $nome_riga;
                    $focus->column_fields['kp_rilevazione'] = $record;
                    $focus->column_fields['kp_rischio'] = $pericolo["id"];
                    $focus->column_fields['kp_related_to'] = $sostanze_chimica["id"];
                    $focus->column_fields['kp_attivo'] = '1';
                    $focus->column_fields['kp_gravita_rischio'] = $situazione_rischio["gravita_rischio"];
                    $focus->column_fields['kp_probabilita_risc'] = $situazione_rischio["probabilita_rischio"];
                    $focus->column_fields['kp_valutazione_risc'] = $situazione_rischio["valutazione_rischio"];
                    $focus->column_fields['kp_frase_risc_dvr'] = $situazione_rischio["frase_di_rischio"];
                    $focus->column_fields['kp_area_stab'] = $dati_rilevazione["area_stabilimento"];
                    $focus->column_fields['kp_misurazione'] = $situazione_rischio["misurazione"];
                    $focus->column_fields['description'] = $situazione_rischio["descrizione"];
                    $focus->save('KpRilevazRischiRig', $longdesc=true, $offline_update=false, $triggerEvent=false); 

                    $lista_ruoli = KpSitRischiDVRKp::getRuoliRelazionatiRigaSituazione( $situazione_rischio["id"] );
                    
                    foreach( $lista_ruoli as $ruolo ){

                        if( self::checkIfRuoloRelazionatoArea($dati_rilevazione["area_stabilimento"], $ruolo["id"]) ){

                            $insert = "INSERT INTO {$table_prefix}_crmentityrel (crmid, module, relcrmid, relmodule) 
                                        VALUES
                                        (".$focus->id.", 'KpRilevazRischiRig', ".$ruolo["id"].", 'KpRuoli')";
                            $adb->query($insert);
    
                        }
                        
                    }

                }

            }

        }

    }

    static function riportaRigheMaterialiDaSituazioneRischiDVR($record){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__.'/../KpSitRischiDVR/ClassKpSitRischiDVRKp.php');

        $dati_rilevazione = self::getDatiRilevazione($record);
        $materiali_relazionati = self::getMaterialiUtilizzoRelazionatiArea( $dati_rilevazione["area_stabilimento"] );

        foreach($materiali_relazionati as $materiale){

            foreach($materiale["lista_pericoli"] as $pericolo){

                $filtro_situazione = array("azienda_id" => $dati_rilevazione["azienda"],
                                        "stabilimento_id" => $dati_rilevazione["stabilimento"],
                                        "area_id" => $dati_rilevazione["area_stabilimento"],
                                        "related_to" => $materiale["id"],
                                        "pericolo_id" => $pericolo["id"]);

                $situazione_rischio = KpSitRischiDVRKp::getRigaSituazioneByFiltro($filtro_situazione);

                if( $situazione_rischio["esiste"] ){

                    $nome_riga = $pericolo["nome"];

                    $focus = CRMEntity::getInstance('KpRilevazRischiRig');
                    $focus->column_fields['assigned_user_id'] = $current_user->id;
                    $focus->column_fields['kp_nome_riga'] = $nome_riga;
                    $focus->column_fields['kp_rilevazione'] = $record;
                    $focus->column_fields['kp_rischio'] = $pericolo["id"];
                    $focus->column_fields['kp_related_to'] = $materiale["id"];
                    $focus->column_fields['kp_attivo'] = '1';
                    $focus->column_fields['kp_gravita_rischio'] = $situazione_rischio["gravita_rischio"];
                    $focus->column_fields['kp_probabilita_risc'] = $situazione_rischio["probabilita_rischio"];
                    $focus->column_fields['kp_valutazione_risc'] = $situazione_rischio["valutazione_rischio"];
                    $focus->column_fields['kp_frase_risc_dvr'] = $situazione_rischio["frase_di_rischio"];
                    $focus->column_fields['kp_area_stab'] = $dati_rilevazione["area_stabilimento"];
                    $focus->column_fields['kp_misurazione'] = $situazione_rischio["misurazione"];
                    $focus->column_fields['description'] = $situazione_rischio["descrizione"];
                    $focus->save('KpRilevazRischiRig', $longdesc=true, $offline_update=false, $triggerEvent=false); 

                    $lista_ruoli = KpSitRischiDVRKp::getRuoliRelazionatiRigaSituazione( $situazione_rischio["id"] );
                    
                    foreach( $lista_ruoli as $ruolo ){

                        if( self::checkIfRuoloRelazionatoArea($dati_rilevazione["area_stabilimento"], $ruolo["id"]) ){

                            $insert = "INSERT INTO {$table_prefix}_crmentityrel (crmid, module, relcrmid, relmodule) 
                                        VALUES
                                        (".$focus->id.", 'KpRilevazRischiRig', ".$ruolo["id"].", 'KpRuoli')";
                            $adb->query($insert);
    
                        }
                        
                    }

                }

            }

        }

    }

    static function checkIfRuoloRelazionatoArea($area, $ruolo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $query = "SELECT
                    *
                    FROM {$table_prefix}_crmentityrel rel
                    WHERE 
                    (rel.module = 'KpAreeStabilimento' AND rel.crmid = ".$area." AND rel.relmodule = 'KpRuoli' AND rel.relcrmid = ".$ruolo.")
                    OR
                    (rel.module = 'KpRuoli' AND rel.crmid = ".$ruolo." AND rel.relmodule = 'KpAreeStabilimento' AND rel.relcrmid = ".$area.")";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){
            return true;
        }
        else{
            return false;
        }

    }



}

?>