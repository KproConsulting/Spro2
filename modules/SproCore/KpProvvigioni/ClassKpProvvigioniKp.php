<?php 

/* kpro@20171115155819 */ 

/** 
 * @copyright (c) 2017, Kpro Consulting Srl 
 * 
 * Estensione classe KpProvvigioni 
 */ 

require_once('modules/KpProvvigioni/KpProvvigioni.php'); 

class KpProvvigioniKp extends KpProvvigioni { 

    var $list_fields = Array();
    
    var $list_fields_name = Array(
        'Soggetto'=>'kp_soggetto',
        'Fornitore'=>'kp_fornitore',
        'Data'=>'kp_data',
        'OdF'=>'kp_odf',
        'Stato Provvigione'=>'kp_stato_provvigion',
        'Importo'=>'kp_importo'	
    );

    function KpProvvigioniKp(){
        global $table_prefix;
        parent::__construct();
        $this->list_fields = Array(
            'Soggetto'=>Array($table_prefix.'_kpprovvigioni'=>'kp_soggetto'),
            'Fornitore'=>Array($table_prefix.'_kpprovvigioni'=>'kp_fornitore'),
            'Data'=>Array($table_prefix.'_kpprovvigioni'=>'kp_data'),
            'OdF'=>Array($table_prefix.'_kpprovvigioni'=>'kp_odf'),
            'Stato Provvigione'=>Array($table_prefix.'_kpprovvigioni'=>'kp_stato_provvigion'),
            'Importo'=>Array($table_prefix.'_kpprovvigioni'=>'kp_importo')
        );

    }

    function save_module($module){
        
        global $table_prefix, $adb;

        parent::save_module($module);
        
    }

    static function generaProvvigioniDaFattura($fattura){
        global $adb, $table_prefix, $default_charset;

        $lista_odf = self::getOdfAgentiDaFattura( $fattura );
        
        foreach($lista_odf as $odf){

            self::generaProvvigioniDaOdf( $odf );

        }

    }

    static function getOdfAgentiDaFattura($fattura){
        global $adb, $table_prefix, $default_charset;

        $result = array();

        $query = "SELECT 
                    odf.odfid odfid
                    FROM {$table_prefix}_odf odf
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = odf.odfid
                    WHERE ent.deleted = 0 AND (odf.kp_agente IS NOT NULL AND odf.kp_agente != '' AND odf.kp_agente != 0) 
                    AND odf.kp_importo_provvigi > 0 AND odf.fattura = ".$fattura;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'odfid');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $result[] = $id;

        }

        return $result;

    }

    static function generaProvvigioniDaOdf($odf){
        global $adb, $table_prefix, $default_charset;

        $provvigioneid = self::getProvvigioneByOdf( $odf );
        
        if($provvigioneid == 0){

            self::generaProvvigione( $odf );

        }

    }

    static function getDatiOdf($odf){
        global $adb, $table_prefix, $default_charset;

        $result = "";

        $focus_odf = CRMEntity::getInstance('OdF');
        $focus_odf->retrieve_entity_info($odf, "OdF"); 
        
        $odf_no = $focus_odf->column_fields["odf_no"];

        $cliente_fatt = $focus_odf->column_fields["cliente_fatt"];

        $servizio = $focus_odf->column_fields["servizio"];

        $fattura = $focus_odf->column_fields["fattura"];
        if($fattura == null || $fattura == ""){
            $fattura = 0;
        }

        $agente = $focus_odf->column_fields["kp_agente"];
        if($agente == null || $agente == ""){
            $agente = 0;
        }

        $importo_provvigioni = $focus_odf->column_fields["kp_importo_provvigi"];

        $tabella_provvigioni = $focus_odf->column_fields["kp_tabella_provvigi"];
        
        if($fattura != null && $fattura != "" && $fattura != 0){

            $focus_fattura = CRMEntity::getInstance('Invoice');
            $focus_fattura->retrieve_entity_info($fattura, "Invoice"); 

            $invoice_number = $focus_fattura->column_fields["invoice_number"];

        }
        else{

            $invoice_number = "";

        }

        if($agente != null && $agente != "" && $agente != 0){
            
            $focus_agente = CRMEntity::getInstance('KpAgenti');
            $focus_agente->retrieve_entity_info($agente, "KpAgenti"); 

            $fornitore = $focus_agente->column_fields["kp_fornitore"];

        }
        else{

            $fornitore = 0;

        }

        $result = array("id" => $odf,
                        "odf_no" => $odf_no,
                        "cliente_fatturazione" => $cliente_fatt,
                        "servizio" => $servizio,
                        "fattura" => $fattura,
                        "numero_fattura" => $invoice_number,
                        "agente" => $agente,
                        "importo_provvigioni" => $importo_provvigioni,
                        "tabella_provvigioni" => $tabella_provvigioni,
                        "fornitore" => $fornitore);
        
        return $result;

    }

    static function getProvvigioneByOdf($odf){
        global $adb, $table_prefix, $default_charset;
        
        $result = 0;

        $query = "SELECT 
                    prov.kpprovvigioniid kpprovvigioniid
                    FROM {$table_prefix}_kpprovvigioni prov
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = prov.kpprovvigioniid
                    WHERE ent.deleted = 0 AND prov.kp_odf = ".$odf;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if($num_result > 0){

            $id = $adb->query_result($result_query, 0, 'kpprovvigioniid');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $result = $id;

        }

        return $result;

    }

    static function generaProvvigione($odf){
        global $adb, $table_prefix, $default_charset, $current_user;
        
        $dati_odf = self::getDatiOdf( $odf );

        $data_corrente = date("Y-m-d");

        $soggetto = "Prov. rif. OdF ".$dati_odf["odf_no"];

        $new_record = CRMEntity::getInstance('KpProvvigioni'); 

        $new_record->column_fields['assigned_user_id'] = 1; 
        $new_record->column_fields['kp_soggetto'] = $soggetto; 
        $new_record->column_fields['kp_agente'] = $dati_odf["agente"]; 
        $new_record->column_fields['kp_fornitore'] = $dati_odf["fornitore"]; 
        $new_record->column_fields['kp_odf'] = $dati_odf["id"]; 
        $new_record->column_fields['kp_fattura'] = $dati_odf["fattura"]; 
        $new_record->column_fields['kp_data'] = $data_corrente; 
        $new_record->column_fields['kp_importo'] = $dati_odf["importo_provvigioni"]; 
        $new_record->column_fields['kp_servizio'] = $dati_odf["servizio"]; 
        $new_record->column_fields['kp_tabella_provvigi'] = $dati_odf["tabella_provvigioni"]; 
        $new_record->column_fields['kp_numero_fattura'] = $dati_odf["numero_fattura"]; 
        $new_record->column_fields['kp_stato_provvigion'] = "Emessa"; 

        $new_record->save('KpProvvigioni', $longdesc=true, $offline_update=false, $triggerEvent=false);

    }

    static function getListaProvvigioniSelezionateValide($lista_provvigioni_selezionate){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();
        
        foreach($lista_provvigioni_selezionate as $provvigione){
            
            $focus_provvigione = CRMEntity::getInstance('KpProvvigioni');
            $focus_provvigione->retrieve_entity_info($provvigione, "KpProvvigioni"); 
            
            if( $focus_provvigione->column_fields["kp_stato_provvigion"] == "Emessa" ){

                $result[] = $provvigione;

            }

        }

        return $result;

    }

    static function getListaFornitoriProvvigioniSelezionateValide($lista_provvigioni_selezionate_valide){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        foreach($lista_provvigioni_selezionate_valide as $provvigione){

            $focus_provvigione = CRMEntity::getInstance('KpProvvigioni');
            $focus_provvigione->retrieve_entity_info($provvigione, "KpProvvigioni"); 

            $fornitore = $focus_provvigione->column_fields["kp_fornitore"];

            if( !in_array($fornitore, $result) ){

                $result[] = $fornitore;

            }

        }

        return $result;

    }

    static function setProposteFatturaDaLista($lista_provvigioni){
        global $adb, $table_prefix, $default_charset, $current_user;

        foreach($lista_provvigioni as $provvigione){

            self::setPropostaFatturaDaProvvigione($provvigione);

        }

    }

    static function setPropostaFatturaDaProvvigione($provvigione){
        global $adb, $table_prefix, $default_charset, $current_user;

        if( !self::checkIfPropostaFatturaAlreadyEsistForThisProvvigione($provvigione) ){

            $focus_provvigione = CRMEntity::getInstance('KpProvvigioni');
            $focus_provvigione->retrieve_entity_info($provvigione, "KpProvvigioni"); 
    
            $fornitore = $focus_provvigione->column_fields["kp_fornitore"];

            $proposta_di_fattura = self::checkIfPropostaFatturaAlreadyEsistForThisFornitore($fornitore);

            if( $proposta_di_fattura["esiste"] ){

                $riga_proposta = self::checkIfRigaPropostaFatturaAlreadyEsistForThisProvvigione($proposta_di_fattura["id"], $provvigione);

                if( !$riga_proposta["esiste"] ){

                    self::setRigaPropostaDiFattura( $proposta_di_fattura["id"], $provvigione );

                }

            }
            else{

                self::creaPropostaDiFattura( $provvigione );

            }

        }
        
    }

    static function checkIfPropostaFatturaAlreadyEsistForThisProvvigione($provvigione){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = false;

        $focus_provvigione = CRMEntity::getInstance('KpProvvigioni');
        $focus_provvigione->retrieve_entity_info($provvigione, "KpProvvigioni", $dieOnError=false); 

        $prop_di_fattura = $focus_provvigione->column_fields["kp_prop_di_fattura"];
        if($prop_di_fattura != null && $prop_di_fattura != "" && $prop_di_fattura != 0){

            $query = "SELECT 
                        po.purchaseorderid purchaseorderid
                        FROM {$table_prefix}_purchaseorder po
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = po.purchaseorderid
                        WHERE ent.deleted = 0 AND po.purchaseorderid = ".$prop_di_fattura;

            $result_query = $adb->query($query);
            $num_result = $adb->num_rows($result_query);

            if($num_result > 0){

                $result = true;

            }

        }

        return $result;

    }

    static function checkIfPropostaFatturaAlreadyEsistForThisFornitore($fornitore){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $esiste = false;
        $id = 0;

        $query = "SELECT 
                    po.purchaseorderid purchaseorderid
                    FROM {$table_prefix}_purchaseorder po
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = po.purchaseorderid
                    WHERE ent.deleted = 0 AND po.postatus = 'Autocreato' AND po.vendorid = ".$fornitore;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $esiste = true;
            
            $id = $adb->query_result($result_query, 0, 'purchaseorderid');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

        }

        $result = array("esiste" => $esiste,
                        "id" => $id);

        return $result;

    }

    static function creaPropostaDiFattura($provvigione){
        global $adb, $table_prefix, $default_charset, $current_user;

        $focus_provvigione = CRMEntity::getInstance('KpProvvigioni');
        $focus_provvigione->retrieve_entity_info($provvigione, "KpProvvigioni", $dieOnError=false); 

        $fornitore = $focus_provvigione->column_fields["kp_fornitore"];
        $assegnatario = $focus_provvigione->column_fields["assigned_user_id"];

        if( $fornitore != null && $fornitore != "" && $fornitore != 0 ){

            $focus_fornitore = CRMEntity::getInstance('Vendors');
            $focus_fornitore->retrieve_entity_info($fornitore, "Vendors", $dieOnError=false); 

            $vendorname = $focus_fornitore->column_fields["vendorname"];
            $street = $focus_fornitore->column_fields["street"];
            $pobox = $focus_fornitore->column_fields["pobox"];
            $city = $focus_fornitore->column_fields["city"];
            $state = $focus_fornitore->column_fields["state"];
            $country = $focus_fornitore->column_fields["country"];
            $postalcode = $focus_fornitore->column_fields["postalcode"];
            $mod_pagamento = $focus_fornitore->column_fields["mod_pagamento"];

            $data_ordine = date("Y-m-d");

            $query = "SELECT 
                        contactid 
                        FROM {$table_prefix}_contactdetails
                        INNER JOIN {$table_prefix}_crmentity ON crmid=contactid
                        WHERE deleted = 0 AND rif_fatturazione = '1' AND vendor_id = ".$fornitore; 
            
            $result_query = $adb->query($query);
            $num_result = $adb->num_rows($result_query);
            
            if( $num_result > 0 ){

                $contatto = $adb->query_result($res_contatto,0,'contactid');
                $contatto = html_entity_decode(strip_tags($contatto), ENT_QUOTES,$default_charset);

            }
            else{

                $contatto = 0;

            }

            $new_po = CRMEntity::getInstance('PurchaseOrder');
            $new_po->column_fields['assigned_user_id'] = $assegnatario;
            $new_po->column_fields['subject'] = 'Proposta di Fattura '.$vendorname;
            $new_po->column_fields['vendor_id'] = $fornitore;
            $new_po->column_fields['contact_id'] = $contatto;
            $new_po->column_fields['postatus'] = 'Autocreato';

            $new_po->column_fields['bill_street'] = $street;
            $new_po->column_fields['bill_city'] = $city;
            $new_po->column_fields['bill_state'] = $state;
            $new_po->column_fields['bill_code'] = $postalcode;
            $new_po->column_fields['bill_country'] = $country;
            $new_po->column_fields['ship_street'] = $street;
            $new_po->column_fields['ship_city'] = $city;
            $new_po->column_fields['ship_state'] = $state;
            $new_po->column_fields['ship_code'] = $postalcode;
            $new_po->column_fields['ship_country'] = $country;

            $new_po->column_fields['kp_data_oda'] = $data_ordine;
            $new_po->column_fields['data_pagamento1'] = $data_ordine;
            $new_po->column_fields['perc_pagamento1'] = 100;
            $new_po->column_fields['mod_pagamento'] = $mod_pagamento;

            $new_po->column_fields['currency_id'] = 1;
            $new_po->column_fields['hdnTaxType'] = 'individual';
            $new_po->column_fields['hdnSubTotal'] = 0;
            $new_po->column_fields['hdnGrandTotal'] = 0;
            $new_po->column_fields['currency_id'] = 1;
            $new_po->column_fields['conversion_rate'] = 1;
            $new_po->column_fields['hdnDiscountPercent'] = '0';
            $new_po->column_fields['hdnDiscountAmount'] = 0;
            $new_po->column_fields['hdnS_H_Amount'] = 0;
            $new_po->save('PurchaseOrder', $longdesc=true, $offline_update=false, $triggerEvent=false); 
            $purchaseorderid = $new_po->id;

            $del_righe = "DELETE FROM {$table_prefix}_inventoryproductrel
                            WHERE id = ".$purchaseorderid;
            $adb->query($del_righe);

            $upd_po = "UPDATE {$table_prefix}_purchaseorder SET 
                        subtotal = 0, 
                        total = 0, 
                        taxtype = 'individual'
                        WHERE purchaseorderid = ".$purchaseorderid;
            $adb->query($upd_po);

            self::setRigaPropostaDiFattura( $purchaseorderid, $provvigione );

        }


    }

    static function setRigaPropostaDiFattura($purchaseorderid, $provvigione){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once('modules/SproCore/SalesOrder/classe_line.php');

        $focus_provvigione = CRMEntity::getInstance('KpProvvigioni');
        $focus_provvigione->retrieve_entity_info($provvigione, "KpProvvigioni", $dieOnError=false); 

        $servizio = $focus_provvigione->column_fields["kp_servizio"];
        $importo = $focus_provvigione->column_fields["kp_importo"];
        $soggetto = $focus_provvigione->column_fields["kp_soggetto"];
        $fornitore = $focus_provvigione->column_fields["kp_fornitore"];
        $numero_fattura = $focus_provvigione->column_fields["kp_numero_fattura"];
        $agente = $focus_provvigione->column_fields["kp_agente"];
        $odf = $focus_provvigione->column_fields["kp_odf"];

        $focus_fornitore = CRMEntity::getInstance('Vendors');
        $focus_fornitore->retrieve_entity_info($fornitore, "Vendors", $dieOnError=false); 

        $focus_agente = CRMEntity::getInstance('KpAgenti');
        $focus_agente->retrieve_entity_info($agente, "KpAgenti", $dieOnError=false); 
        $nome_agente = $focus_agente->column_fields["kp_nome_agente"];

        $focus_odf = CRMEntity::getInstance('OdF');
        $focus_odf->retrieve_entity_info($odf, "OdF", $dieOnError=false); 
        $cliente_fatt = $focus_odf->column_fields["cliente_fatt"];

        $focus_cliente = CRMEntity::getInstance('Accounts');
        $focus_cliente->retrieve_entity_info($cliente_fatt, "Accounts", $dieOnError=false); 
        $accountname = $focus_cliente->column_fields["accountname"];

        $descrizione = "Rif. Fattura ".$numero_fattura.", Cliente ".$accountname.", Agente ".$nome_agente;

        $total_notaxes = 0;
        $linetotal = 0;

        $nuova_riga = new LineKP();
        $nuova_riga->id = $purchaseorderid;
        $nuova_riga->productid = $servizio;
        $nuova_riga->quantity = 1;
        $nuova_riga->listprice = $importo;
        $nuova_riga->comment = $soggetto;
        $nuova_riga->description = $descrizione;
        $nuova_riga->relmodule = 'PurchaseOrder';
        $nuova_riga->total_notaxes = $importo;
        $nuova_riga->salva();

        $focus_provvigione->column_fields["kp_stato_provvigion"] = "Generata proposta di fattura";
        $focus_provvigione->column_fields["kp_prop_di_fattura"] = $purchaseorderid;
        $focus_provvigione->mode = 'edit';
        $focus_provvigione->id = $provvigione;
        $focus_provvigione->save('KpProvvigioni', $longdesc=true, $offline_update=false, $triggerEvent=false);

    }

    static function checkIfRigaPropostaFatturaAlreadyEsistForThisProvvigione($purchaseorderid, $provvigione){
        global $adb, $table_prefix, $default_charset, $current_user;

        $esiste = false;
        $id = 0;

        $focus_provvigione = CRMEntity::getInstance('KpProvvigioni');
        $focus_provvigione->retrieve_entity_info($provvigione, "KpProvvigioni", $dieOnError=false); 

        $servizio = $focus_provvigione->column_fields["kp_servizio"];
        $soggetto = $focus_provvigione->column_fields["kp_soggetto"];

        $query = "SELECT 
                    lineitem_id 
                    FROM {$table_prefix}_inventoryproductrel
                    WHERE comment = '".$soggetto."' AND id = ".$purchaseorderid." AND productid = ".$servizio;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $esiste = true;
            
            $id = $adb->query_result($result_query, 0, 'lineitem_id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

        }

        $result = array("esiste" => $esiste,
                        "id" => $id);

        return $result;

    }

    static function getTabellaListaProvvigioniSelezionate($ids){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";
        
        $lista_provvigioni = array();

        foreach($ids as $id){
            
            $lista_provvigioni[] = self::getDatiProvvigione($id);
        
        }

        $result = "<table style='width: 100%; margin: 0px;' class='table table-striped'>";
        $result .= "<thead>";
        $result .= "<tr>";
        $result .= "<th style='text-align: left;'>Soggetto</th>";
        $result .= "<th style='text-align: left;'>Fornitore</th>";
        $result .= "<th style='text-align: left;'>Agente</th>";
        $result .= "<th style='text-align: left;'>Data</th>";
        $result .= "<th style='text-align: left;'>Servizio</th>";
        $result .= "<th style='text-align: left;'>Importo</th>";
        $result .= "<th style='text-align: left;'>Nota</th>";
        $result .= "</tr>";
        $result .= "</thead>";
        $result .= "<tbody>";

        foreach($lista_provvigioni as $provvigione){

            $check_provvigione = self::checkIfProvvigioneFatturabile($provvigione);

            if( !$check_provvigione["fatturabile"] ){
                $result .= "<tr style='background-color: red; color: white;'>";
            }
            else{
                $result .= "<tr>";
            }

            $result .= "<td>".$provvigione["nome"]."</td>";
            $result .= "<td>".$provvigione["nome_fornitore"]."</td>";
            $result .= "<td>".$provvigione["nome_agente"]."</td>";

            $data = $provvigione["data"];
            if($data != "" && $data != null){
                $data = new DateTime($data);
                $data = $data->format('d/m/Y');	
            }
            $result .= "<td>".$data."</td>";

            $result .= "<td>".$provvigione["nome_servizio"]."</td>";
            $result .= "<td>".$provvigione["importo"]."</td>";
            $result .= "<td>".$check_provvigione["nota"]."</td>";

            $result .= "</tr>";
            
        }

        $result .= "</tbody>";
        
        return $result;
        
    }

    static function getDatiProvvigione($id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";
        
        $focus_provvigione = CRMEntity::getInstance('KpProvvigioni');
        $focus_provvigione->retrieve_entity_info($id, "KpProvvigioni", $dieOnError=false); 

        $nome = $focus_provvigione->column_fields["kp_soggetto"];
        $agente = $focus_provvigione->column_fields["kp_agente"];
        $fornitore = $focus_provvigione->column_fields["kp_fornitore"];
        $fattura = $focus_provvigione->column_fields["kp_fattura"];
        $data = $focus_provvigione->column_fields["kp_data"];
        $importo = $focus_provvigione->column_fields["kp_importo"];
        $stato = $focus_provvigione->column_fields["kp_stato_provvigion"];
        $servizio = $focus_provvigione->column_fields["kp_servizio"];
        $numero_fattura = $focus_provvigione->column_fields["kp_numero_fattura"];

        if($agente != null && $agente != "" && $agente != 0){

            $focus_agente = CRMEntity::getInstance('KpAgenti');
            $focus_agente->retrieve_entity_info($agente, "KpAgenti", $dieOnError=false); 

            $nome_agente = $focus_agente->column_fields["kp_nome_agente"];

        }
        else{

            $agente = 0;
            $nome_agente = "";

        }

        if($fornitore != null && $fornitore != "" && $fornitore != 0){
            
            $focus_fornitore = CRMEntity::getInstance('Vendors');
            $focus_fornitore->retrieve_entity_info($fornitore, "Vendors", $dieOnError=false); 

            $nome_fornitore = $focus_fornitore->column_fields["vendorname"];

        }
        else{

            $fornitore = 0;
            $nome_fornitore = "";

        }

        if($servizio != null && $servizio != "" && $servizio != 0){
            
            $focus_servizio = CRMEntity::getInstance('Services');
            $focus_servizio->retrieve_entity_info($servizio, "Services", $dieOnError=false); 

            $nome_servizio = $focus_servizio->column_fields["servicename"];

        }
        else{

            $servizio = 0;
            $nome_servizio = "";

        }

        $result = array("id" => $id,
                        "nome" =>$nome,
                        "data" =>$data,
                        "importo" =>$importo,
                        "stato" =>$stato,
                        "numero_fattura" =>$numero_fattura,
                        "agente" =>$agente,
                        "nome_agente" =>$nome_agente,
                        "fornitore" =>$fornitore,
                        "nome_fornitore" =>$nome_fornitore,
                        "servizio" =>$servizio,
                        "nome_servizio" =>$nome_servizio);

        return $result;

    }

    static function checkIfProvvigioneFatturabile($provvigione){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $fatturabile = false;

        $nota = "";

        if( $provvigione["stato"] != "Emessa" ){
            
            if($nota == ""){
                $nota .= "Impossibile emettere la proposta di fattura: ";
            }

            $nota .= "La provvigione è in stato '".$provvigione["stato"]."';";
        }

        if( $provvigione["fornitore"] == 0 ){
            
            if($nota == ""){
                $nota .= "Impossibile emettere la proposta di fattura: ";
            }

            $nota .= "Non è stato collegato alcun fornitore alla provvigione;";
        }

        if( $provvigione["servizio"] == 0 ){
            
            if($nota == ""){
                $nota .= "Impossibile emettere la proposta di fattura: ";
            }

            $nota .= "Non è stato collegato alcun servizio alla provvigione;";
        }

        if( $provvigione["importo"] = 0 ){
            
            if($nota == ""){
                $nota .= "Impossibile emettere la proposta di fattura: ";
            }

            $nota .= "L'importo da emettere non può essere nullo;";
        }

        if($nota == ""){
            $fatturabile = true;
            $nota .= "Proposta di fattura correttamente emettibile!";
        }
       
        $result = array("fatturabile" => $fatturabile,
                        "nota" => $nota);

        return $result;

    }

    static function getListaProvvigioniSelezionateFatturabili($ids){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();
        
        $lista_provvigioni = array();
        
        foreach($ids as $id){
            
            $lista_provvigioni[] = self::getDatiProvvigione($id);
        
        }

        foreach($lista_provvigioni as $provvigione){
            
            $check_provvigione = self::checkIfProvvigioneFatturabile($provvigione);

            if( $check_provvigione["fatturabile"] ){

                $result[] = $provvigione["id"];

            }

        }

        return $result;

    }



} 

?>