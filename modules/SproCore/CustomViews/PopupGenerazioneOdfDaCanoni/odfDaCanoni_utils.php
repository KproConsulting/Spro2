<?php
	
/* kpro@tom310316 */	
	
function generaOdfDaCanoni($canoni_id,$mese_fatturazione,$anno_fatturazione){
    global $adb, $table_prefix, $current_user;

    /**
     * @author Tomiello Marco
     * @copyright Kpro Consulting Srl
     * @package fatturazioneConOdf
     * @version 1.0
     * 
     * La funzione generaOdfDaCanoni permette di generare gli OdF partendo da un canone
     */

    $data_corrente = date("Y-m-d");

    $q_canoni = "SELECT 
                    c.canone_name canone_name,
                    c.account account,
                    c.servizio servizio,
                    c.prezzo importo_canone,
                    c.data_inizio data_inizio,
                    c.kp_business_unit kp_business_unit,
                    c.kp_agente kp_agente,
                    c.commessa commessa,
                    c.sales_order sales_order,
                    ent.smownerid smownerid,
                    c.description description
                    FROM {$table_prefix}_canoni c
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = c.canoniid
                    WHERE ent.deleted = 0 AND c.canoniid =".$canoni_id;
    $res_canoni = $adb->query($q_canoni);

    if($adb->num_rows($res_canoni)>0){
        
        $canone_name = $adb->query_result($res_canoni,0,'canone_name');
        $canone_name = html_entity_decode(strip_tags($canone_name), ENT_QUOTES,$default_charset);

        $account = $adb->query_result($res_canoni,0,'account');
        $account = html_entity_decode(strip_tags($account), ENT_QUOTES,$default_charset);

        $servizio = $adb->query_result($res_canoni,0,'servizio');
        $servizio = html_entity_decode(strip_tags($servizio), ENT_QUOTES,$default_charset);

        $importo_canone = $adb->query_result($res_canoni,0,'importo_canone');
        $importo_canone = html_entity_decode(strip_tags($importo_canone), ENT_QUOTES,$default_charset);
        if($importo_canone == '' || $importo_canone == null){
            $importo_canone = 0;
        }
        
        $business_unit = $adb->query_result($res_canoni,0,'kp_business_unit');
        $business_unit = html_entity_decode(strip_tags($business_unit), ENT_QUOTES,$default_charset);
        if($business_unit == '' || $business_unit == null){
            $business_unit = 0;
        }

        $agente = $adb->query_result($res_canoni,0,'kp_agente');
        $agente = html_entity_decode(strip_tags($agente), ENT_QUOTES,$default_charset);
        if($agente == '' || $agente == null){
            $agente = 0;
        }
        
        $commessa = $adb->query_result($res_canoni,0,'commessa');
        $commessa = html_entity_decode(strip_tags($commessa), ENT_QUOTES,$default_charset);
        if($commessa == null || $commessa == ""){
            $commessa = 0;
        }

        /* kpro@bid24112017 */
        $sales_order = $adb->query_result($res_canoni,0,'sales_order');
        $sales_order = html_entity_decode(strip_tags($sales_order), ENT_QUOTES,$default_charset);
        if($sales_order == "" || $sales_order == null){
            $sales_order = 0;
        }

        if($sales_order != 0 && $sales_order != '0'){
            $q_dati_ordine = "SELECT so.mod_pagamento,
                            so.contactid,
                            so.kp_conto_corrente,
                            so.kp_banca_cliente
                            FROM {$table_prefix}_salesorder so
                            WHERE so.salesorderid = ".$sales_order;
            $res_dati_ordine = $adb->query($q_dati_ordine);
            if($adb->num_rows($res_dati_ordine) > 0){
                $mod_pagamento = $adb->query_result($res_dati_ordine,0,'mod_pagamento');
                $mod_pagamento = html_entity_decode(strip_tags($mod_pagamento), ENT_QUOTES,$default_charset);
                if($mod_pagamento == "" || $mod_pagamento == null){
                    $mod_pagamento = 0;
                }

                $contatto = $adb->query_result($res_dati_ordine,0,'contactid');
                $contatto = html_entity_decode(strip_tags($contatto), ENT_QUOTES,$default_charset);
                if($contatto == "" || $contatto == null){
                    $contatto = 0;
                }

                $conto_corrente = $adb->query_result($res_dati_ordine,0,'kp_conto_corrente');
                $conto_corrente = html_entity_decode(strip_tags($conto_corrente), ENT_QUOTES,$default_charset);
                if($conto_corrente == "" || $conto_corrente == null){
                    $conto_corrente = 0;
                }

                $banca_cliente = $adb->query_result($res_dati_ordine,0,'kp_banca_cliente');
                $banca_cliente = html_entity_decode(strip_tags($banca_cliente), ENT_QUOTES,$default_charset);
                if($banca_cliente == "" || $banca_cliente == null){
                    $banca_cliente = 0;
                }
            }
            else{
                $mod_pagamento = 0;
                $contatto = 0;
                $conto_corrente = 0;
                $banca_cliente = "";
            }
        }
        else{
            $mod_pagamento = 0;
            $contatto = 0;
            $conto_corrente = 0;
            $banca_cliente = "";
        }
        /* kpro@bid24112017 end */

        $smownerid = $adb->query_result($res_canoni,0,'smownerid');
        $smownerid = html_entity_decode(strip_tags($smownerid), ENT_QUOTES,$default_charset);
        if($smownerid == null || $smownerid == "" || $smownerid == 0){
            $smownerid = 1;
        }

        $description = $adb->query_result($res_canoni,0,'description');
        $description = html_entity_decode(strip_tags($description), ENT_QUOTES,$default_charset);
        if($description == null){
            $description = "";
        }

        $data_inizio = $adb->query_result($res_canoni,0,'data_inizio');
        $data_inizio = html_entity_decode(strip_tags($data_inizio), ENT_QUOTES,$default_charset);

        $anno_cor = date("Y");
        $mese_cor = date("m");

        $giorno_fatturazione = '01';
        $data_related_to = date("Y-m-d",mktime(0,0,0,$mese_fatturazione,$giorno_fatturazione,$anno_fatturazione));

        if($importo_canone != 0){

            $q_verifica_es = "SELECT odfid FROM {$table_prefix}_odf
                                INNER JOIN {$table_prefix}_crmentity ON crmid = odfid
                                WHERE deleted = 0 AND related_to =".$canoni_id." AND MONTH(data_related_to) = ".$mese_fatturazione." 
                                AND YEAR(data_related_to) = ".$anno_fatturazione;
            $res_verifica_es = $adb->query($q_verifica_es);

            if($adb->num_rows($res_verifica_es)==0){

                $q_servizio = "SELECT service_usageunit FROM {$table_prefix}_service
                                INNER JOIN {$table_prefix}_crmentity ON crmid = serviceid
                                WHERE deleted = 0 AND serviceid =".$servizio;

                $ress_servizio = $adb->query($q_servizio); 							

                if($adb->num_rows($ress_servizio)>0){						 
                    $service_usageunit = $adb->query_result($ress_servizio,0,'service_usageunit'); 
                }

                /* kpro@bid211120181430 */
                ricalcoloMeseFatturazioneCanone($canoni_id);

                $competenza_canone = getCompetenzaCanone($canoni_id);
                if($competenza_canone != ""){
                    $description = $competenza_canone."
".$description;
                }
                /* kpro@bid211120181430 end */
                
                $odf = CRMEntity::getInstance('OdF');
                $odf->column_fields['tipo_odf'] = 'Canone';
                $odf->column_fields['cliente_fatt'] = $account;
                $odf->column_fields['related_to'] = $canoni_id;
                $odf->column_fields['prezzo_unitario'] = $importo_canone;
                $odf->column_fields['qta_eseguita'] = 1;
                $odf->column_fields['qta_fatturata'] = 1;
                $odf->column_fields['prezzo_totale'] = $importo_canone;
                $odf->column_fields['total_notaxes'] = $importo_canone;
                $odf->column_fields['servizio'] = $servizio;
                $odf->column_fields['kp_business_unit'] = $business_unit;
                $odf->column_fields['kp_agente'] = $agente;
                if($commessa != 0){
                    $odf->column_fields['commessa'] = $commessa;
                }
                $odf->column_fields['data_odf'] = $data_corrente;
                $odf->column_fields['data_related_to'] = $data_related_to;
                $odf->column_fields['rif_related_to'] = $canone_name." id:".$canoni_id;
                $odf->column_fields['stato_odf'] = 'Creato';
                $odf->column_fields['service_usageunit'] = $service_usageunit;
                $odf->column_fields['assigned_user_id'] = $smownerid;
                /* kpro@bid24112017 */
                $odf->column_fields['kp_mod_pagamento'] = $mod_pagamento;
                $odf->column_fields['kp_contatto'] = $contatto;
                $odf->column_fields['kp_conto_corrente'] = $conto_corrente;
                $odf->column_fields['kp_banca_cliente'] = $banca_cliente;
                /* kpro@bid24112017 end */
                $odf->column_fields['description'] = utf8_encode($description);
                $odf->save('OdF', $longdesc=true, $offline_update=false, $triggerEvent=false);
        
                $risultato = 1;
            }
        }
        else{
            $risultato = 2;
        }
    }

    return $risultato;
		
}

function ricalcoloMeseFatturazioneCanone($canoni_id){
        
    /**
     * @author Tomiello Marco
     * @copyright Kpro Consulting Srl
     * @package fatturazioneConOdf
     * @version 1.0
     * 
     * La funzione ricalcoloMeseFatturazioneCanone ricalcola il mese di fatturazione del canone
     */

    global $adb, $table_prefix, $current_user;
    
    require_once('modules/SproCore/SproUtils/spro_utils.php');

    $q_canoni = "SELECT c.frequenza_fatturazione frequenza_fatturazione,
                    c.mese_fatturazione mese_fatturazione,
                    c.kp_anno_fatt kp_anno_fatt
                    FROM {$table_prefix}_canoni c
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = c.canoniid
                    WHERE ent.deleted = 0 AND c.canoniid =".$canoni_id;
    
    $res_canoni = $adb->query($q_canoni);

    if($adb->num_rows($res_canoni)>0){
        $frequenza_fatturazione = $adb->query_result($res_canoni,0,'frequenza_fatturazione');
        $frequenza_fatturazione = html_entity_decode(strip_tags($frequenza_fatturazione), ENT_QUOTES,$default_charset);
        
        $numero_mesi_incremento = calcolaNumeroMesiIncremento($frequenza_fatturazione);

        $mese_fatturazione = $adb->query_result($res_canoni,0,'mese_fatturazione');
        $mese_fatturazione = html_entity_decode(strip_tags($mese_fatturazione), ENT_QUOTES,$default_charset);

        $anno_fatturazione = $adb->query_result($res_canoni,0,'kp_anno_fatt');
        $anno_fatturazione = html_entity_decode(strip_tags($anno_fatturazione), ENT_QUOTES,$default_charset);
        
        if($numero_mesi_incremento != null && $numero_mesi_incremento != '' && $mese_fatturazione != null && $mese_fatturazione != '' && $anno_fatturazione != null && $anno_fatturazione != ''){
            $data_fatturazione_temp = $anno_fatturazione."-".$mese_fatturazione."-01";

            $date = date_create($data_fatturazione_temp);
            date_add($date,date_interval_create_from_date_string($numero_mesi_incremento." months"));
            $prossimo_mese_fatturazione = ltrim(date_format($date,"m"),'0');
            $prossimo_anno_fatturazione = date_format($date,"Y"); 

            $upd_canone = "UPDATE {$table_prefix}_canoni SET 
                            pros_mese_fatt = '".$prossimo_mese_fatturazione."',
                            kp_pros_anno_fatt = '".$prossimo_anno_fatturazione."'
                            WHERE canoniid = ".$canoni_id;
            $res_upd_canone = $adb->query($upd_canone);
        }

    }
	
}

/* kpro@bid211120181430 */
function getCompetenzaCanone($canone){
    global $adb, $table_prefix, $current_user;

    $res = '';

    $q = "SELECT c.frequenza_fatturazione frequenza_fatturazione,
        c.mese_fatturazione mese_fatturazione,
        c.kp_anno_fatt kp_anno_fatt,
        c.pros_mese_fatt pros_mese_fatt,
        c.kp_pros_anno_fatt kp_pros_anno_fatt
        FROM {$table_prefix}_canoni c
        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = c.canoniid
        WHERE ent.deleted = 0 AND c.canoniid =".$canone;
    
    $res = $adb->query($q);

    if($adb->num_rows($res)>0){
        $frequenza_fatturazione = $adb->query_result($res,0,'frequenza_fatturazione');
        $frequenza_fatturazione = html_entity_decode(strip_tags($frequenza_fatturazione), ENT_QUOTES,$default_charset);

        $mese_fatturazione = $adb->query_result($res,0,'mese_fatturazione');
        $mese_fatturazione = html_entity_decode(strip_tags($mese_fatturazione), ENT_QUOTES,$default_charset);
        if($mese_fatturazione == null){
            $mese_fatturazione = "";
        }

        $anno_fatturazione = $adb->query_result($res,0,'kp_anno_fatt');
        $anno_fatturazione = html_entity_decode(strip_tags($anno_fatturazione), ENT_QUOTES,$default_charset);
        if($anno_fatturazione == null){
            $anno_fatturazione = "";
        }

        $prossimo_mese_fatturazione = $adb->query_result($res,0,'pros_mese_fatt');
        $prossimo_mese_fatturazione = html_entity_decode(strip_tags($prossimo_mese_fatturazione), ENT_QUOTES,$default_charset);
        if($prossimo_mese_fatturazione == null){
            $prossimo_mese_fatturazione = "";
        }

        $prossimo_anno_fatturazione = $adb->query_result($res,0,'kp_pros_anno_fatt');
        $prossimo_anno_fatturazione = html_entity_decode(strip_tags($prossimo_anno_fatturazione), ENT_QUOTES,$default_charset);
        if($prossimo_anno_fatturazione == null){
            $prossimo_anno_fatturazione = "";
        }

        if($mese_fatturazione != "" && $anno_fatturazione != "" && $prossimo_mese_fatturazione != "" && $prossimo_anno_fatturazione != ""){

            if($frequenza_fatturazione == 'Mensile'){
                $res = 'Competenza del '.$mese_fatturazione.'/'.$anno_fatturazione;
            }
            else{
                $res = 'Competenza dal '.$mese_fatturazione.'/'.$anno_fatturazione.' al '.$prossimo_mese_fatturazione.'/'.$prossimo_anno_fatturazione;
            }
        }
    }

    return $res;
}
/* kpro@bid211120181430 end */

?>
