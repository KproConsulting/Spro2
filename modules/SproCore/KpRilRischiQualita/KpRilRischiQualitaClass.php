<?php

/* kpro@tom1306201803042018 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2018, Kpro Consulting Srl
 */

class KpRilRischiQualitaClass {

    /**
     * 
     */

    static function getDatiRilevazione($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $focus_rilevazione = CRMEntity::getInstance('KpRilRischiQualita');
        $focus_rilevazione->retrieve_entity_info($id, "KpRilRischiQualita", $dieOnError=false); 

        $azienda = $focus_rilevazione->column_fields["kp_azienda"];
        $stabilimento = $focus_rilevazione->column_fields["kp_stabilimento"];
        $data_rilevazione = $focus_rilevazione->column_fields["kp_data_rilevazione"];

        $result = array("azienda" => $azienda,
                        "stabilimento" => $stabilimento,
                        "data_rilevazione" => $data_rilevazione);

        return $result;

    }

    static function getDatiRilevazioneProcesso($processo, $rilevazione){
        global $adb, $table_prefix, $default_charset, $current_user;

        $records = array();

        $table = "";

        $table = "<table style='width: 99%; margin: auto;' class='table table-striped table-hover tabella_rilevazione'>";
        $table .= "<thead>";
        $table .= "<tr>";

        $table .= "<th>Attivita'</th>";
        $table .= "<th>Rischi</th>";
        $table .= "<th style='width: 140px;'>Probabilità</th>";
        $table .= "<th style='width: 140px;'>Magnitudo</th>";
        $table .= "<th style='width: 120px;'>Rischio</th>";

        $table .= "</tr>";
        $table .= "</thead>";

        $table .= "<tbody>";

        $lista_task = KpBPMN::getElementiProcedura($processo, array("only_task" => true));

        foreach( $lista_task as $task ){

            $prima_riga = true;
        
            $lista_rischi = KpBPMN::getRischiQualitaElementoProcedura($task["id"], array());

            foreach($lista_rischi as $rischio){

                $table .= "<tr id='".$task["id"]."_".$rischio["id"]."' class='tr_rischio'>";

                $dati_riga = self::getRischioRilevazione($rilevazione, $rischio["id"], $task["id"]);

                if($prima_riga){

                    $prima_riga = false;
                    
                    $table .= "<td rowspan='".count($lista_rischi)."'><b><span style='vertical-align: middle; padding-top: 40px;'>";
                    $table .= $task["nome"];
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
                $table .= "<b><span style='vertical-align: middle; margin-left: 10px;' >".$rischio["nome"]."</span></b></label>";
                $table .= "</div>";
                $table .= "</b></td>";

                if( $dati_riga["check"] ){
                    $table .= "<td class='td_probabilita' style='vertical-align: middle'>".$dati_riga["probabilita"]."</td>";
                    $table .= "<td class='td_magnitudo' style='vertical-align: middle'>".$dati_riga["magnitudo"]."</td>";
                    $table .= "<td class='td_rischio' style='vertical-align: middle'>".$dati_riga["rischio"]." - ".$dati_riga["frase_di_rischio"]."</td>";
                }
                else{
                    $table .= "<td class='td_probabilita' style='vertical-align: middle'></td>";
                    $table .= "<td class='td_magnitudo' style='vertical-align: middle'></td>";
                    $table .= "<td class='td_rischio' style='vertical-align: middle'></td>";
                }

                $table .= "</tr>";

                $records[] = array("attivita" => $task["id"],
                                    "rischio" => $rischio["id"],
                                    "rischio_nome" => $rischio["nome"]);

            }
            

        }

        $table .= "</tbody>";

        $table .= "</table>";

        $result = array("table" => $table,
                        "records" => $records);

        return $result;

    }

    static function getRischioRilevazione($rilevazione, $rischio_id, $attivita){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $focus_rischio = CRMEntity::getInstance('KpRischiQualita');
        $focus_rischio->retrieve_entity_info($rischio_id, "KpRischiQualita", $dieOnError=false); 

        $nome_rischio = $focus_rischio->column_fields["kp_nome_rischio"];
        $nome_rischio = html_entity_decode(strip_tags($nome_rischio), ENT_QUOTES, $default_charset);

        $check = true;

        $focus_attivita = CRMEntity::getInstance('KpEntitaProcedure');
        $focus_attivita->retrieve_entity_info($attivita, "KpEntitaProcedure", $dieOnError=false); 
        
        $nome_attivita = $focus_attivita->column_fields["kp_nome_entita"];
        $nome_attivita = html_entity_decode(strip_tags($nome_attivita), ENT_QUOTES, $default_charset);

        $riga = self::checkRigaRilevazioneRischio($rilevazione, $rischio_id, $attivita);
        
        if( $riga["esiste"] ){

            $focus_riga = CRMEntity::getInstance('KpRigheRilRischiQual');
            $focus_riga->retrieve_entity_info($riga["id"], "KpRigheRilRischiQual", $dieOnError=false); 
            
            $magnitudo = $focus_riga->column_fields["kp_gravita_risc_q"];
            $probabilita = $focus_riga->column_fields["kp_prob_risc_q"];
            $rischio = $focus_riga->column_fields["kp_valutazione_risc"];
            $frase_di_rischio = $focus_riga->column_fields["kp_frase_risc_qual"];
            $attivo = $focus_riga->column_fields["kp_attivo"];

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

        }

        $result = array("id" => $rischio_id,
                        "nome" => $nome_rischio,
                        "nome_attivita" => $nome_attivita,
                        "check" => $check,
                        "id_riga_ril" => $id_riga_ril,
                        "magnitudo" => $magnitudo,
                        "probabilita" => $probabilita,
                        "rischio" => $rischio,
                        "frase_di_rischio" => $frase_di_rischio);

        return $result;

    }

    static function checkRigaRilevazioneRischio($rilevazione, $rischio, $attivita){
        global $adb, $table_prefix, $default_charset, $current_user;

        $query = "SELECT 
                    rig.kprigherilrischiqualid id
                    FROM {$table_prefix}_kprigherilrischiqual rig
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = rig.kprigherilrischiqualid
                    WHERE ent.deleted = 0 AND rig.kp_rilevazione = ".$rilevazione." AND rig.kp_rischio = ".$rischio;

        $query .= " AND rig.kp_attivita = ".$attivita;
       
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

    static function setRigaRilevazioneRischio($rilevazione, $dati){
        global $adb, $table_prefix, $default_charset, $current_user;

        $riga = self::checkRigaRilevazioneRischio($rilevazione, $dati["rischio_id"], $dati["attivita"]);

        $magnitudo = self::decodeMagnitudo($dati["magnitudo"]);
        $probabilita = self::decodeProbabilita($dati["probabilita"]);
        $frase_di_rischio = self::getFraseDiRischio($dati["rischio"]);

        $focus_rischio = CRMEntity::getInstance('KpRischiQualita');
        $focus_rischio->retrieve_entity_info($dati["rischio_id"], "KpRischiQualita", $dieOnError=false); 
        
        $nome_rischio = $focus_rischio->column_fields["kp_nome_rischio"];
        $nome_rischio = html_entity_decode(strip_tags($nome_rischio), ENT_QUOTES, $default_charset);

        $focus_attivita = CRMEntity::getInstance('KpEntitaProcedure');
        $focus_attivita->retrieve_entity_info($dati["attivita"], "KpEntitaProcedure", $dieOnError=false); 
        
        $processo = $focus_attivita->column_fields["kp_procedura"];
        $processo = html_entity_decode(strip_tags($processo), ENT_QUOTES, $default_charset);

        $nome_attivita = $focus_attivita->column_fields["kp_nome_entita"];
        $nome_attivita = html_entity_decode(strip_tags($nome_attivita), ENT_QUOTES, $default_charset);

        $nome_riga = $nome_attivita." - ".$nome_rischio;
        if( count($nome_riga) > 100 ){
            $nome_riga = substr($nome_riga, 0, 100);
        }

        if( $riga["esiste"] && $dati["attivo"] == '1' ){

            $focus = CRMEntity::getInstance('KpRigheRilRischiQual');
            $focus->retrieve_entity_info( $riga["id"], "KpRigheRilRischiQual" );
            $focus->column_fields['kp_soggetto'] = $nome_riga;
            $focus->column_fields['kp_attivo'] = $dati["attivo"];
            $focus->column_fields['kp_gravita_risc_q'] = $magnitudo;
            $focus->column_fields['kp_prob_risc_q'] = $probabilita;
            $focus->column_fields['kp_valutazione_risc'] = $dati["rischio"];
            $focus->column_fields['kp_frase_risc_qual'] = $frase_di_rischio;
            $focus->mode = 'edit';
            $focus->id = $riga["id"];
            $focus->save('KpRigheRilRischiQual', $longdesc=true, $offline_update=false, $triggerEvent=false);

        }
        elseif( $riga["esiste"] ){

            $update = "UPDATE {$table_prefix}_crmentity SET
                        deleted = 1
                        WHERE crmid = ".$riga["id"];
            $adb->query($update);          

        }
        else{

            $focus = CRMEntity::getInstance('KpRigheRilRischiQual');
            $focus->column_fields['assigned_user_id'] = $current_user->id;
            $focus->column_fields['kp_soggetto'] = $nome_riga;
            $focus->column_fields['kp_rilevazione'] = $rilevazione;
            $focus->column_fields['kp_rischio'] = $dati["rischio_id"];
            $focus->column_fields['kp_attivita'] = $dati["attivita"];
            $focus->column_fields['kp_processo'] = $processo;
            $focus->column_fields['kp_attivo'] = $dati["attivo"];
            $focus->column_fields['kp_gravita_risc_q'] = $magnitudo;
            $focus->column_fields['kp_prob_risc_q'] = $probabilita;
            $focus->column_fields['kp_valutazione_risc'] = $dati["rischio"];
            $focus->column_fields['kp_frase_risc_qual'] = $frase_di_rischio;
            $focus->save('KpRigheRilRischiQual', $longdesc=true, $offline_update=false, $triggerEvent=false); 

        }

        $nome_riga = addslashes($nome_riga);

        $update = "UPDATE {$table_prefix}_kprigherilrischiqual SET
                    kp_soggetto = '".$nome_riga."'
                    WHERE kprigherilrischiqualid = ".$focus->id;
        $adb->query($update);

        return self::getRischioRilevazione($rilevazione, $dati["rischio_id"], $dati["attivita"]);

    }

    static function decodeMagnitudo($magnitudo){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        switch ($magnitudo) {
            case 1:
                $result = "1 - Trascurabile";
                break;
            case 2:
                $result = "2 - Contenuto";
                break;
            case 3:
                $result = "3 - Significativo";
                break;
            case 4:
                $result = "4 - Rilevante";
                break;
            case 5:
                $result = "5 - Catastrofico";
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
                $result = "2 - Raro";
                break;
            case 3:
                $result = "3 - Possibile";
                break;
            case 4:
                $result = "4 - Probabile";
                break;
            case 5:
                $result = "5 - Molto Probabile";
                break;
            default:
                $result = "";
        }

        return $result;
        
    }

    static function getFraseDiRischio($rischio){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        if( $rischio >= 1 && $rischio <= 5){
            $result = "Irrilevante";
        }
        elseif($rischio > 5 && $rischio <= 10){
            $result = "Minore";
        }
        elseif($rischio > 10 && $rischio <= 15){
            $result = "Moderato";
        }
        elseif($rischio > 15 && $rischio <= 20){
            $result = "Significativo";
        }
        elseif($rischio > 20 && $rischio <= 25){
            $result = "Estremo";
        }

        return $result;
        
    }

    static function getMisureMigliorativeRischio($record, $dati){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    mis.kpmisuremigliorativeid id,
                    mis.kp_nome_misura nome,
                    mis.kp_eseguire_entro eseguire_entro,
                    mis.kp_stato_misura_mig stato_misura
                    FROM {$table_prefix}_kpmisuremigliorative mis
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = mis.kpmisuremigliorativeid
                    WHERE ent.deleted = 0 AND mis.kp_stato_misura_mig != 'Adottata' AND mis.kp_rischio = ".$dati["rischio"];

        $query .= " AND mis.kp_attivita = ".$dati["attivita"];

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for( $i = 0; $i < $num_result; $i++ ){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

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
                                "eseguire_entro" => $eseguire_entro,
                                "stato_misura" => $stato_misura);

        }

        return $result;

    }

    static function setMisuraMigliorativaRischio($rilevazione, $dati){
        global $adb, $table_prefix, $default_charset, $current_user;

        $focus_attivita = CRMEntity::getInstance('KpEntitaProcedure');
        $focus_attivita->retrieve_entity_info($dati["attivita"], "KpEntitaProcedure", $dieOnError=false); 
        
        $processo = $focus_attivita->column_fields["kp_procedura"];
        $processo = html_entity_decode(strip_tags($processo), ENT_QUOTES, $default_charset);

        $focus_rilevazione = CRMEntity::getInstance('KpRilRischiQualita');
        $focus_rilevazione->retrieve_entity_info($rilevazione, "KpRilRischiQualita", $dieOnError=false); 

        $azienda = $focus_rilevazione->column_fields["kp_azienda"];
        $azienda = html_entity_decode(strip_tags($azienda), ENT_QUOTES, $default_charset);
        
        $stabilimento = $focus_rilevazione->column_fields["kp_stabilimento"];
        $stabilimento = html_entity_decode(strip_tags($stabilimento), ENT_QUOTES, $default_charset);

        $focus = CRMEntity::getInstance('KpMisureMigliorative');
        $focus->column_fields['assigned_user_id'] = $current_user->id;
        $focus->column_fields['kp_nome_misura'] = $dati["nome_intervento"];
        $focus->column_fields['kp_tipo_misura_migl'] = $dati["tipo_misura"];
        $focus->column_fields['kp_azienda'] = $azienda;
        $focus->column_fields['kp_stabilimento'] = $stabilimento;
        $focus->column_fields['kp_eseguire_entro'] = $dati["adottare_entro"];
        $focus->column_fields['kp_attivita'] = $dati["attivita"];
        $focus->column_fields['kp_processo'] = $processo;
        $focus->column_fields['kp_rischio'] = $dati["rischio"];
        $focus->column_fields['kp_rid_prob_qual'] = $dati["riduzione_probabilita"];
        $focus->column_fields['kp_rid_magn_qual'] = $dati["riduzione_magnitudo"];
        $focus->column_fields['description'] = $dati["nota"];
        $focus->column_fields['kp_stato_misura_mig'] = 'Da adottare';
        $focus->save('KpMisureMigliorative', $longdesc=true, $offline_update=false, $triggerEvent=false); 

        $nome_intervento = addslashes($dati["nome_intervento"]);
        $description = addslashes($dati["nota"]);

        $update = "UPDATE {$table_prefix}_kpmisuremigliorative SET
                    kp_nome_misura = '".$nome_intervento."',
                    description = '".$description."'
                    WHERE kpmisuremigliorativeid = ".$focus->id;
        $adb->query($update);

        return $focus->id;

    }


}

?>