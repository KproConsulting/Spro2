<?php

/* kpro@bid24012018 */

/**
 * @author Bidese Jacopo
 * @copyright (c) 2018, Kpro Consulting Srl
 */

function generaOdfDaReportVisita($reportvisitaid){
    global $adb, $table_prefix,$current_user;

    require_once("modules/SproCore/SproUtils/spro_utils.php");
    
    $data_corrente = date("Y-m-d");

    $result = array(
        "odf_creati" => 0,
        "odf_con_prezzo" => 0,
        "odf_senza_prezzo" => 0
    );
	
	$q_visitreport = "SELECT v.visitreportname visitreportname,
                    v.visitreport_no visitreport_no, 
                    v.accountid accountid,
                    v.visitdate visitdate,
                    v.commessa commessa,
                    v.kp_servizio service_id,
                    v.kp_business_unit businessunit,
                    v.kp_ore_effettive ore_effettuate,
                    v.kp_ore_fatturate ore_fatturate,
                    v.kp_da_fatturare da_fatturare,
                    v.kp_tipo_rimborso tipo_rimborso,
                    v.spautostr pedaggio,
                    v.kmpercorsi km_percorsi,
                    v.kp_ore_viaggio ore_viaggio,
                    v.spvitoall vitto_alloggio,
                    v.spother altre_spese,
                    serv.servicename servicename,
                    serv.unit_price prezzo_servizio,
                    serv.service_usageunit service_usageunit,
                    ent.smownerid smownerid, 
                    v.description description
                    FROM {$table_prefix}_visitreport v
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = v.visitreportid
                    LEFT JOIN {$table_prefix}_service serv ON serv.serviceid = v.kp_servizio
                    WHERE ent.deleted = 0 AND v.visitreportid = ".$reportvisitaid;
	$ress_visitreport = $adb->query($q_visitreport);	
	if($adb->num_rows($ress_visitreport)>0){
        $visitreportname = $adb->query_result($ress_visitreport,0,'visitreportname');
        $visitreportname = html_entity_decode(strip_tags($visitreportname), ENT_QUOTES,$default_charset);
        if($visitreportname == null){
            $visitreportname = "";
        }

		$visitreport_no = $adb->query_result($ress_visitreport,0,'visitreport_no');
        $visitreport_no = html_entity_decode(strip_tags($visitreport_no), ENT_QUOTES,$default_charset);
		
		$cliente = $adb->query_result($ress_visitreport,0,'accountid');
        $cliente = html_entity_decode(strip_tags($cliente), ENT_QUOTES,$default_charset);
        if($cliente != 0 && $cliente != "" && $cliente != null){
            $q_verifica_cliente = "SELECT setype FROM {$table_prefix}_crmentity 
                                    WHERE crmid =".$cliente;
            $res_verifica_cliente = $adb->query($q_verifica_cliente);
            if($adb->num_rows($res_verifica_cliente)>0){	
                $setype = $adb->query_result($res_verifica_cliente,0,'setype');
                $setype = html_entity_decode(strip_tags($setype), ENT_QUOTES,$default_charset);

                if($setype != "Accounts"){
                    $cliente = 0;
                }

            }
            else{
                $cliente = 0;
            }
        }
        else{
            $cliente = 0;
        }

        if($cliente != 0){
            $q_dati_azienda = "SELECT acc.kp_listino
                            FROM {$table_prefix}_account acc
                            WHERE acc.accountid = ".$cliente;
            $res_dati_azienda = $adb->query($q_dati_azienda);
            if($adb->num_rows($res_dati_azienda) > 0){
                $listino_azienda = $adb->query_result($res_dati_azienda,0,'kp_listino');
                $listino_azienda = html_entity_decode(strip_tags($listino_azienda), ENT_QUOTES,$default_charset);
                if($listino_azienda == "" || $listino_azienda == null){
                    $listino_azienda = 0;
                }
            }
            else{
                $listino_azienda = 0;
            }
        }
        else{
            $listino_azienda = 0;
        }
		
		$visitdate = $adb->query_result($ress_visitreport,0,'visitdate');
		$visitdate = html_entity_decode(strip_tags($visitdate), ENT_QUOTES,$default_charset);
		
		$commessa = $adb->query_result($ress_visitreport,0,'commessa');
        $commessa = html_entity_decode(strip_tags($commessa), ENT_QUOTES,$default_charset);
        if($commessa == "" || $commessa == null){
            $commessa = 0;
        }
		
		$business_unit = $adb->query_result($ress_visitreport,0,'businessunit');
        $business_unit = html_entity_decode(strip_tags($business_unit), ENT_QUOTES,$default_charset);
        if($business_unit == "" || $business_unit == null){
            $business_unit = 0;
        }
		
		$smownerid = $adb->query_result($ress_visitreport,0,'smownerid');
		$smownerid = html_entity_decode(strip_tags($smownerid), ENT_QUOTES,$default_charset);
		
		$description = $adb->query_result($ress_visitreport,0,'description');
        $description = html_entity_decode(strip_tags($description), ENT_QUOTES,$default_charset);
        if($description == null){
            $description = "";
        }

        if($description != "" && $visitreportname != ""){
            $description = $visitreportname."
".$description;
        }
        elseif($description == "" && $visitreportname != ""){
            $description = $visitreportname;
        }
		
		$ore_effettuate = $adb->query_result($ress_visitreport,0,'ore_effettuate');
		$ore_effettuate = html_entity_decode(strip_tags($ore_effettuate), ENT_QUOTES,$default_charset); 
		if($ore_effettuate == null || $ore_effettuate == ""){
			$ore_effettuate = 0;
		}
		
		$ore_fatturate = $adb->query_result($ress_visitreport,0,'ore_fatturate');
		$ore_fatturate = html_entity_decode(strip_tags($ore_fatturate), ENT_QUOTES,$default_charset); 
		if($ore_fatturate == null || $ore_fatturate == ""){
			$ore_fatturate = 0;
		}
		
		$da_fatturare = $adb->query_result($ress_visitreport,0,'da_fatturare');
		$da_fatturare = html_entity_decode(strip_tags($da_fatturare), ENT_QUOTES,$default_charset); 
		
		$tipo_rimborso = $adb->query_result($ress_visitreport,0,'tipo_rimborso');
        $tipo_rimborso = html_entity_decode(strip_tags($tipo_rimborso), ENT_QUOTES,$default_charset); 

        $pedaggio = $adb->query_result($ress_visitreport,0,'pedaggio');
        $pedaggio = html_entity_decode(strip_tags($pedaggio), ENT_QUOTES,$default_charset);
        if($pedaggio == "" || $pedaggio == null){
            $pedaggio = 0;
        }

        $km_percorsi = $adb->query_result($ress_visitreport,0,'km_percorsi');
        $km_percorsi = html_entity_decode(strip_tags($km_percorsi), ENT_QUOTES,$default_charset);
        if($km_percorsi == "" || $km_percorsi == null){
            $km_percorsi = 0;
        }

        $ore_viaggio = $adb->query_result($ress_visitreport,0,'ore_viaggio');
        $ore_viaggio = html_entity_decode(strip_tags($ore_viaggio), ENT_QUOTES,$default_charset);
        if($ore_viaggio == "" || $ore_viaggio == null){
            $ore_viaggio = 0;
        }

        $vitto_alloggio = $adb->query_result($ress_visitreport,0,'vitto_alloggio');
        $vitto_alloggio = html_entity_decode(strip_tags($vitto_alloggio), ENT_QUOTES,$default_charset);
        if($vitto_alloggio == "" || $vitto_alloggio == null){
            $vitto_alloggio = 0;
        }

        $altre_spese = $adb->query_result($ress_visitreport,0,'altre_spese');
        $altre_spese = html_entity_decode(strip_tags($altre_spese), ENT_QUOTES,$default_charset);
        if($altre_spese == "" || $altre_spese == null){
            $altre_spese = 0;
        }

        $servizio = $adb->query_result($ress_visitreport,0,'service_id');
        $servizio = html_entity_decode(strip_tags($servizio), ENT_QUOTES,$default_charset);
        if($servizio == "" || $servizio == null){
            $servizio = 0;
        }
        
        $service_usageunit = $adb->query_result($ress_visitreport,0,'service_usageunit');
        $service_usageunit = html_entity_decode(strip_tags($service_usageunit), ENT_QUOTES,$default_charset);
		
		$prezzo_servizio = recuperaPrezzoServizio($listino_azienda, $servizio);		
		
        $prezzo_totale = $prezzo_servizio * $ore_fatturate;
        
        $odf = CRMEntity::getInstance('OdF');
        $odf->column_fields['tipo_odf'] = 'Report Attivita';
        $odf->column_fields['cliente_fatt'] = $cliente;
        $odf->column_fields['related_to'] = $reportvisitaid;
        $odf->column_fields['data_related_to'] = $visitdate;
        $odf->column_fields['rif_related_to'] = $visitreport_no;
        $odf->column_fields['prezzo_unitario'] = $prezzo_servizio;
        $odf->column_fields['qta_eseguita'] = $ore_effettuate;
        $odf->column_fields['qta_fatturata'] = $ore_fatturate;
        $odf->column_fields['prezzo_totale'] = $prezzo_totale;
        $odf->column_fields['servizio'] = $servizio;
        $odf->column_fields['kp_business_unit'] = $business_unit;
        $odf->column_fields['data_odf'] = $data_corrente;
        $odf->column_fields['stato_odf'] = 'Creato';
        $odf->column_fields['service_usageunit'] = $service_usageunit;
        $odf->column_fields['assigned_user_id'] = $smownerid;
        $odf->column_fields['total_notaxes'] = $prezzo_totale;
        $odf->column_fields['description'] = utf8_encode($description);
        $odf->column_fields['commessa'] = $commessa;
        $odf->save('OdF', $longdesc=true, $offline_update=false, $triggerEvent=false); 
		
		if( $prezzo_totale > 0){			
            $result['odf_creati']++;
            $result['odf_con_prezzo']++;
        }
        else{
            $result['odf_creati']++;
            $result['odf_senza_prezzo']++;
        }
        
        if($tipo_rimborso != 'No addebito'){

            $dati_nota_spesa = array(
                'azienda' => $cliente,
                'id_report_visita' => $reportvisitaid,
                'data' => $visitdate,
                'numero_report_visita' => $visitreport_no,
                'business_unit' => $business_unit,
                'assigned_user_id' => $smownerid,
                'commessa' => $commessa
            );

            $id_statici = getConfigurazioniIdStatici();

            if($tipo_rimborso == 'Addebito forfettario'){

                $id_statico = $id_statici["Programmi Custom - Generazione ODF da Report Attività - Servizio per rimborso forfettario"];
                if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
                    return;
                }
                else{
                    $servizio_rimborso = $id_statico["valore"];
                }
                
                $dati_servizio = recuperaDatiServizio($servizio_rimborso);
                
                $service_usageunit_rimborso = $dati_servizio['service_usageunit'];
                
                $prezzo_servizio_rimborso = recuperaPrezzoServizio($listino_azienda, $servizio_rimborso);
                $totale_prezzo_rimborso = $prezzo_servizio_rimborso;

                $descrizione_rimborso = '';
                
                $dati_nota_spesa['servizio'] = $servizio_rimborso;
                $dati_nota_spesa['service_usageunit'] = $service_usageunit_rimborso;
                $dati_nota_spesa['prezzo'] = $totale_prezzo_rimborso;
                $dati_nota_spesa['description'] = $descrizione_rimborso;

                creazioneOdfNotaSpesa($dati_nota_spesa);

                if($totale_prezzo_rimborso > 0){
                    $result['odf_creati']++;
                    $result['odf_con_prezzo']++;
                }
                else{
                    $result['odf_creati']++;
                    $result['odf_senza_prezzo']++;
                }
                
            }
            
            if($tipo_rimborso == 'Addebito a consuntivo'){

                $id_statico = $id_statici["Programmi Custom - Generazione ODF da Report Attività - Servizio per rimborso chilometrico"];
                if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
                    return;
                }
                else{
                    $servizio_rimborso = $id_statico["valore"];
                }
                
                $dati_servizio = recuperaDatiServizio($servizio_rimborso);
                
                $service_usageunit_rimborso = $dati_servizio['service_usageunit'];
                
                $prezzo_servizio_rimborso = recuperaPrezzoServizio($listino_azienda, $servizio_rimborso);

                $totale_prezzo_rimborso = $prezzo_servizio_rimborso * $km_percorsi;

                $descrizione_rimborso = "Costo chilometrico = ".$prezzo_servizio_rimborso." (Km = ".$km_percorsi."),";

                $dati_nota_spesa['servizio'] = $servizio_rimborso;
                $dati_nota_spesa['service_usageunit'] = $service_usageunit_rimborso;
                $dati_nota_spesa['prezzo'] = $totale_prezzo_rimborso;
                $dati_nota_spesa['description'] = $descrizione_rimborso;

                creazioneOdfNotaSpesa($dati_nota_spesa);

                if($totale_prezzo_rimborso > 0){
                    $result['odf_creati']++;
                    $result['odf_con_prezzo']++;
                }
                else{
                    $result['odf_creati']++;
                    $result['odf_senza_prezzo']++;
                }

            }

            if($tipo_rimborso == 'Addebito a consuntivo'){

                $id_statico = $id_statici["Programmi Custom - Generazione ODF da Report Attività - Servizio per rimborso orario"];
                if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
                    return;
                }
                else{
                    $servizio_rimborso = $id_statico["valore"];
                }
                
                $dati_servizio = recuperaDatiServizio($servizio_rimborso);
                
                $service_usageunit_rimborso = $dati_servizio['service_usageunit'];
                
                $prezzo_servizio_rimborso = recuperaPrezzoServizio($listino_azienda, $servizio_rimborso);

                $totale_prezzo_rimborso = $prezzo_servizio_rimborso * $ore_viaggio;

                $descrizione_rimborso = "Costo orario = ".$prezzo_servizio_rimborso." (Ore = ".$ore_viaggio.")";

                $dati_nota_spesa['servizio'] = $servizio_rimborso;
                $dati_nota_spesa['service_usageunit'] = $service_usageunit_rimborso;
                $dati_nota_spesa['prezzo'] = $totale_prezzo_rimborso;
                $dati_nota_spesa['description'] = $descrizione_rimborso;

                creazioneOdfNotaSpesa($dati_nota_spesa);

                if($totale_prezzo_rimborso > 0){
                    $result['odf_creati']++;
                    $result['odf_con_prezzo']++;
                }
                else{
                    $result['odf_creati']++;
                    $result['odf_senza_prezzo']++;
                }

            }

            if($tipo_rimborso == 'Addebito a consuntivo'){

                $id_statico = $id_statici["Programmi Custom - Generazione ODF da Report Attività - Servizio per rimborso autostrada, vitto alloggio e altre spese"];
                if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
                    return;
                }
                else{
                    $servizio_rimborso = $id_statico["valore"];
                }
                
                $dati_servizio = recuperaDatiServizio($servizio_rimborso);
                
                $service_usageunit_rimborso = $dati_servizio['service_usageunit'];

                $totale_prezzo_rimborso = $pedaggio + $vitto_alloggio + $altre_spese;

                $descrizione_rimborso = "Spese autostradali = ".$pedaggio.", 
Spese vito e alloggio = ".$vitto_alloggio.", 
Altre spese = ".$altre_spese;

                $dati_nota_spesa['servizio'] = $servizio_rimborso;
                $dati_nota_spesa['service_usageunit'] = $service_usageunit_rimborso;
                $dati_nota_spesa['prezzo'] = $totale_prezzo_rimborso;
                $dati_nota_spesa['description'] = $descrizione_rimborso;

                if($totale_prezzo_rimborso > 0){
                    creazioneOdfNotaSpesa($dati_nota_spesa);

                    $result['odf_creati']++;
                    $result['odf_con_prezzo']++;
                }

            }

        }
        
        $upd_visitreport = "UPDATE {$table_prefix}_visitreport
                            SET kp_stato_attivita = 'Emesso OdF'
                            WHERE visitreportid = ".$reportvisitaid;
        $adb->query($upd_visitreport);
		
	}
	
	return $result;
}

function creazioneOdfNotaSpesa($dati){
    global $adb, $table_prefix,$current_user;

    $data_corrente = date("Y-m-d");

    $odf = CRMEntity::getInstance('OdF');
    $odf->column_fields['tipo_odf'] = 'Nota Spesa';
    $odf->column_fields['cliente_fatt'] = $dati['azienda'];
    $odf->column_fields['related_to'] = $dati['id_report_visita'];
    $odf->column_fields['data_related_to'] = $dati['data'];
    $odf->column_fields['rif_related_to'] = $dati['numero_report_visita'];
    $odf->column_fields['prezzo_unitario'] = $dati['prezzo'];
    $odf->column_fields['qta_eseguita'] = 1;
    $odf->column_fields['qta_fatturata'] = 1;
    $odf->column_fields['prezzo_totale'] = $dati['prezzo'];
    $odf->column_fields['servizio'] = $dati['servizio'];
    $odf->column_fields['kp_business_unit'] = $dati['business_unit'];
    $odf->column_fields['data_odf'] = $data_corrente;
    $odf->column_fields['stato_odf'] = 'Creato';
    $odf->column_fields['service_usageunit'] = $dati['service_usageunit'];
    $odf->column_fields['assigned_user_id'] = $dati['assigned_user_id'];
    $odf->column_fields['total_notaxes'] = $dati['prezzo'];
    $odf->column_fields['description'] = $dati['description'];
    $odf->column_fields['commessa'] = $dati['commessa'];
    $odf->save('OdF', $longdesc=true, $offline_update=false, $triggerEvent=false); 
}

function recuperaDatiServizio($servizio){
    global $adb, $table_prefix, $current_user;

    $dati_servizio = array();
    
    $q = "SELECT ser.service_usageunit
        FROM {$table_prefix}_service ser
        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = ser.serviceid
        WHERE ent.deleted = 0 AND ser.serviceid = ".$servizio;
    $res = $adb->query($q);
    if($adb->num_rows($res) > 0){
        $service_usageunit = $adb->query_result($res,0,'service_usageunit');
        $service_usageunit = html_entity_decode(strip_tags($service_usageunit), ENT_QUOTES,$default_charset);
    }
    else{
        $service_usageunit = "";
    }

    $dati_servizio = array(
        "service_usageunit" => $service_usageunit
    );

    return $dati_servizio;
}

function recuperaPrezzoServizio($listino_azienda, $servizio){
    global $adb, $table_prefix, $current_user;
    
    require_once("modules/SproCore/SproUtils/spro_utils.php");
    
    $prezzo_listino = 0;

    if($listino_azienda != 0){
        $q_prezzo_listino = "SELECT pricerel.listprice AS prezzo_listino
                        FROM {$table_prefix}_pricebook price
                        INNER JOIN {$table_prefix}_pricebookproductrel pricerel ON pricerel.pricebookid = price.pricebookid
                            AND pricerel.productid = {$servizio}
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = price.pricebookid
                        WHERE ent.deleted = 0 AND price.pricebookid = {$listino_azienda}";
        $res_prezzo_listino = $adb->query($q_prezzo_listino);
        if($adb->num_rows($res_prezzo_listino) > 0){
            $prezzo_listino = $adb->query_result($res_prezzo_listino,0,'prezzo_listino');
            if($prezzo_listino == "" || $prezzo_listino == null){
                $prezzo_listino = 0;
            }
        }
    }
    
    if($prezzo_listino == 0){
        $id_statici = getConfigurazioniIdStatici();
        $id_statico = $id_statici["Generale - Listino standard (da utilizzare nel caso il cliente non ne abbia uno)"];
        if( $id_statico["valore"] == "" && $id_statico["valore"] == 0){
            return;
        }
        else{
            $listino_standard = $id_statico["valore"];
        }

        $q_prezzo_listino = "SELECT pricerel.listprice AS prezzo_listino
                        FROM {$table_prefix}_pricebook price
                        INNER JOIN {$table_prefix}_pricebookproductrel pricerel ON pricerel.pricebookid = price.pricebookid
                            AND pricerel.productid = {$servizio}
                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = price.pricebookid
                        WHERE ent.deleted = 0 AND price.pricebookid = ".$listino_standard;
        $res_prezzo_listino = $adb->query($q_prezzo_listino);
        if($adb->num_rows($res_prezzo_listino) > 0){
            $prezzo_listino = $adb->query_result($res_prezzo_listino,0,'prezzo_listino');
            if($prezzo_listino == "" || $prezzo_listino == null){
                $prezzo_listino = 0;
            }
        }
    }
	
	return $prezzo_listino;	
}
	
?>