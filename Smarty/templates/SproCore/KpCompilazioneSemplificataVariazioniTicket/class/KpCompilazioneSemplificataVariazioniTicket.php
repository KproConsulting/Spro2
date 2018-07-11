<?php

/* kpro@tom12072017 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

class KpCompilazioneSemplificataVariazioniTicket {

    /**

        static function: 
            -

    */

    static function getListaVariazioni($filtro){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();
    
        $query = "SELECT 
                    var.kpvariazioniticketid id,
                    var.kp_fornitore fornitore,
                    var.kp_costo costo,
                    var.kp_data_consegna data_consegna,
                    var.kp_causale_var_tic causale_var_tic,
                    tick.ticketid ticket,
                    acc.accountid account,
                    stab.stabilimentiid stabilimento,
                    tick.title nome_ticket,
                    tick.kp_rspp rspp,
                    tick.kp_pm pm,
                    tick.kp_ref_formazione ref_formazione,
                    tick.kp_ref_medico ref_medico,
                    tick.kp_ref_segreteria ref_segreteria,
                    tick.kp_data_consegna data_ticket,
                    acc.accountname nome_azienda,
                    stab.nome_stabilimento nome_stabilimento,
                    forn.vendorname nome_fornitore,
                    serv.serviceid servizio,
                    ent.smownerid assegnatario,
                    us.user_name user_name,
                    var.description descrizione
                    FROM {$table_prefix}_kpvariazioniticket var
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = var.kpvariazioniticketid
                    INNER JOIN {$table_prefix}_troubletickets tick ON tick.ticketid = var.kp_ticket
                    INNER JOIN {$table_prefix}_account acc ON acc.accountid = tick.parent_id
                    LEFT JOIN {$table_prefix}_stabilimenti stab ON stab.stabilimentiid = tick.kp_stabilimento
                    LEFT JOIN {$table_prefix}_vendor forn ON forn.vendorid = var.kp_fornitore
                    INNER JOIN {$table_prefix}_service serv ON serv.serviceid = tick.servizio
                    INNER JOIN {$table_prefix}_users us on us.id = ent.smownerid
                    WHERE ent.deleted = 0 AND var.kp_tipo_var_ticket = 'Assegnazione ORS' AND var.kp_stato_var_ticket = 'Da approvare'";  

        if($filtro["assegnatario"] == "me"){
            $query .= " AND ent.smownerid = ".$current_user->id;
        }
        elseif($filtro["assegnatario"] == "altri"){
            $query .= " AND ent.smownerid != ".$current_user->id;
        }
        elseif($filtro["assegnatario"] != ""){
            $query .= " AND ent.smownerid = ".$filtro["assegnatario"];
        }

        if($filtro["cliente"] != ""){
            $query .= " AND acc.accountname LIKE '%".$filtro["cliente"]."%'";
        }

        if($filtro["stabilimento"] != ""){
            $query .= " AND stab.nome_stabilimento LIKE '%".$filtro["stabilimento"]."%'";
        }

        if($filtro["ticket"] != ""){
            $query .= " AND tick.title LIKE '%".$filtro["ticket"]."%'";
        }

        $query .= " ORDER BY acc.accountname ASC, stab.nome_stabilimento ASC, tick.kp_data_consegna ASC";
        
        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES,$default_charset);

            $fornitore = $adb->query_result($result_query, $i, 'fornitore');
            $fornitore = html_entity_decode(strip_tags($fornitore), ENT_QUOTES,$default_charset);
            if($fornitore == null && $fornitore == ""){
                $fornitore = 0;
            }

            $costo = $adb->query_result($result_query, $i, 'costo');
            $costo = html_entity_decode(strip_tags($costo), ENT_QUOTES,$default_charset);

            $data_consegna = $adb->query_result($result_query, $i, 'data_consegna');
            $data_consegna = html_entity_decode(strip_tags($data_consegna), ENT_QUOTES,$default_charset);
            if($data_consegna != null && $data_consegna != ""){
                $data_consegna = new DateTime($data_consegna);
                $data_consegna = $data_consegna->format('d/m/Y');
            }

            $causale_var_tic = $adb->query_result($result_query, $i, 'causale_var_tic');
            $causale_var_tic = html_entity_decode(strip_tags($causale_var_tic), ENT_QUOTES,$default_charset);

            $ticket = $adb->query_result($result_query, $i, 'ticket');
            $ticket = html_entity_decode(strip_tags($ticket), ENT_QUOTES,$default_charset);

            $account = $adb->query_result($result_query, $i, 'account');
            $account = html_entity_decode(strip_tags($account), ENT_QUOTES,$default_charset);

            $stabilimento = $adb->query_result($result_query, $i, 'stabilimento');
            $stabilimento = html_entity_decode(strip_tags($stabilimento), ENT_QUOTES,$default_charset);

            $nome_ticket = $adb->query_result($result_query, $i, 'nome_ticket');
            $nome_ticket = html_entity_decode(strip_tags($nome_ticket), ENT_QUOTES,$default_charset);

            $rspp = $adb->query_result($result_query, $i, 'rspp');
            $rspp = html_entity_decode(strip_tags($rspp), ENT_QUOTES,$default_charset);

            $pm = $adb->query_result($result_query, $i, 'pm');
            $pm = html_entity_decode(strip_tags($pm), ENT_QUOTES,$default_charset);

            $ref_formazione = $adb->query_result($result_query, $i, 'ref_formazione');
            $ref_formazione = html_entity_decode(strip_tags($ref_formazione), ENT_QUOTES,$default_charset);

            $ref_medico = $adb->query_result($result_query, $i, 'ref_medico');
            $ref_medico = html_entity_decode(strip_tags($ref_medico), ENT_QUOTES,$default_charset);

            $ref_segreteria = $adb->query_result($result_query, $i, 'ref_segreteria');
            $ref_segreteria = html_entity_decode(strip_tags($ref_segreteria), ENT_QUOTES,$default_charset);

            $data_ticket = $adb->query_result($result_query, $i, 'data_ticket');
            $data_ticket = html_entity_decode(strip_tags($data_ticket), ENT_QUOTES,$default_charset);

            $nome_azienda = $adb->query_result($result_query, $i, 'nome_azienda');
            $nome_azienda = html_entity_decode(strip_tags($nome_azienda), ENT_QUOTES,$default_charset);

            $nome_stabilimento = $adb->query_result($result_query, $i, 'nome_stabilimento');
            $nome_stabilimento = html_entity_decode(strip_tags($nome_stabilimento), ENT_QUOTES,$default_charset);

            $nome_fornitore = $adb->query_result($result_query, $i, 'nome_fornitore');
            $nome_fornitore = html_entity_decode(strip_tags($nome_fornitore), ENT_QUOTES,$default_charset);
            if($nome_fornitore == null && $nome_fornitore == ""){
                $nome_fornitore = "";
            }

            $servizio = $adb->query_result($result_query, $i, 'servizio');
            $servizio = html_entity_decode(strip_tags($servizio), ENT_QUOTES,$default_charset);

            $assegnatario = $adb->query_result($result_query, $i, 'assegnatario');
            $assegnatario = html_entity_decode(strip_tags($assegnatario), ENT_QUOTES,$default_charset);

            $user_name = $adb->query_result($result_query, $i, 'user_name');
            $user_name = html_entity_decode(strip_tags($user_name), ENT_QUOTES,$default_charset);

            $descrizione = $adb->query_result($result_query, $i, 'descrizione');
            $descrizione = html_entity_decode(strip_tags($descrizione), ENT_QUOTES,$default_charset);

            $result[] = array("id" => $id,
                            "fornitore" => $fornitore,
                            "costo" => $costo,
                            "data_consegna" => $data_consegna,
                            "causale_var_tic" => $causale_var_tic,
                            "ticket" => $ticket,
                            "account" => $account,
                            "stabilimento" => $stabilimento,
                            "nome_ticket" => $nome_ticket,
                            "rspp" => $rspp,
                            "pm" => $pm,
                            "ref_formazione" => $ref_formazione,
                            "ref_medico" => $ref_medico,
                            "ref_segreteria" => $ref_segreteria,
                            "data_ticket" => $data_ticket,
                            "nome_azienda" => $nome_azienda,
                            "nome_stabilimento" => $nome_stabilimento,
                            "nome_fornitore" => $nome_fornitore,
                            "servizio" => $servizio,
                            "assegnatario" => $assegnatario,
                            "user_name" => $user_name,
                            "descrizione" => $descrizione);

        }

        return $result;
        
    }

    static function getDatalistFornitori(){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    vend.vendorid id,
                    vend.vendorname value
                    FROM {$table_prefix}_vendor vend
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = vend.vendorid
                    WHERE ent.deleted = 0";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES,$default_charset);

            $value = $adb->query_result($result_query, $i, 'value');
            $value = html_entity_decode(strip_tags($value), ENT_QUOTES,$default_charset);

            $result[] = array("id" => $id,
                                "value" => $value);

        }

        return $result;        

    }

    static function setVariazioneTicket($id, $fornitore, $costo, $data_consegna, $descrizione, $approva_variazione){
        global $adb, $table_prefix, $default_charset, $current_user;

        $focus = CRMEntity::getInstance('KpVariazioniTicket');
        $focus->retrieve_entity_info($id, "KpVariazioniTicket");   
        $focus->column_fields['kp_fornitore'] = $fornitore;
        $focus->column_fields['kp_costo'] = $costo;
        $focus->column_fields['kp_data_consegna'] = $data_consegna;
        $focus->column_fields['description'] = $descrizione;
        if( $approva_variazione ){
            $focus->column_fields['kp_stato_var_ticket'] = "Approvato";
        }
        $focus->mode = 'edit';
        $focus->id = $id;
        $focus->save('KpVariazioniTicket', $longdesc=true, $offline_update=false, $triggerEvent=false);

    }

    static function getListaAssegnatari(){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        require('user_privileges/requireUserPrivileges.php'); 
        require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

        $fieldlabel = 'Assigned To';
        global $noof_group_rows;

        $editview_label[]=getTranslatedString($fieldlabel, $module_name);

        //Security Checks
        if($fieldlabel == 'Assigned To' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
        {
            $result=get_current_user_access_groups($module_name);
        }
        else
        {
            $result = get_group_options();
        }
        if($result) $nameArray = $adb->fetch_array($result);

        if($value != '' && $value != 0)
            $assigned_user_id = $value;
        else
            $assigned_user_id = $current_user->id;
        if($fieldlabel == 'Assigned To' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
        {
            $users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id,'private'), $assigned_user_id);
        }
        else
        {
            $users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id), $assigned_user_id);
        }
        if($noof_group_rows!=0)
        {
            $groups_combo = '';
            if($fieldlabel == 'Assigned To' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
            {
                $groups_combo = get_select_options_array(get_group_array(FALSE, "Active", $assigned_user_id,'private'), $assigned_user_id);
            }
            else
            {
                $groups_combo = get_select_options_array(get_group_array(FALSE, "Active", $assigned_user_id), $assigned_user_id);
            }
        }

        $fieldvalue[]= $users_combo;
        $fieldvalue_group[] = $groups_combo;

        $myArray = $fieldvalue[0];
        $keys = array_keys($myArray);

        if($groups_combo != ''){
            $myArray_group = $fieldvalue_group[0];
            $keys_group = array_keys($myArray_group);
        }

        $elementCount  = count($fieldvalue[0]) + count($fieldvalue_group[0]);
        $elementCountUser  = count($fieldvalue[0]);
        $array_utenti = array();

        $lista_assegnatari = '(';
        for($y=0; $y<$elementCountUser; $y++){

            $user_id = $keys[$y];
            $queryUsers = "SELECT id, user_name 'username', first_name, last_name FROM {$table_prefix}_users WHERE id = {$user_id}";
            $result = $adb->query($queryUsers);
            $id = $adb->query_result($result, 0, 'id');
            $userName = $adb->query_result($result, 0, 'username');
            $firstName = $adb->query_result($result, 0, 'first_name');
            $lastName = $adb->query_result($result, 0, 'last_name');
            $array_utenti[] = array('id' => $id,
                                    'user_name' => $userName,
                                    'first_name' => $firstName,
                                    'last_name' => $lastName);
            
            if($lista_assegnatari == '('){
                $lista_assegnatari  .= $id;
            }
            else{
                $lista_assegnatari  .= ",".$id;
            }
            
        }

        $elementCountGroup  = count($fieldvalue_group[0]);

        $array_gruppi = array();

        for($y=0; $y<$elementCountGroup; $y++){
            $group_id = $keys_group[$y];
            $queryGroup = "SELECT groupid, groupname, description FROM {$table_prefix}_groups WHERE groupid = {$group_id}";

            $result_group = $adb->query($queryGroup);
            $groupid = $adb->query_result($result_group, 0, 'groupid');
            $groupname = $adb->query_result($result_group, 0, 'groupname');
            $description = $adb->query_result($result_group, 0, 'description');

            $array_gruppi[] = array('id' => $groupid,
                                    'user_name' => 'Gruppo: '.$groupname,
                                    'first_name' => $description,
                                    'last_name' => "");
                                
            if($lista_assegnatari == '('){
                $lista_assegnatari  .= $groupid;
            }
            else{
                $lista_assegnatari  .= ",".$groupid;
            }
            
        }

        $lista_assegnatari .= ')';

        $result = array("utente_corrente" => $current_user->id,
                        "utenti" => $array_utenti,
                        "gruppi" => $array_gruppi);

        return $result;

    }

    static function getDatalistServizi(){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    ser.serviceid id,
                    ser.servicename value
                    FROM {$table_prefix}_service ser
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = ser.serviceid
                    WHERE ent.deleted = 0";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES,$default_charset);

            $value = $adb->query_result($result_query, $i, 'value');
            $value = html_entity_decode(strip_tags($value), ENT_QUOTES,$default_charset);

            $result[] = array("id" => $id,
                                "value" => $value);

        }

        return $result;        

    }

   


}

?>