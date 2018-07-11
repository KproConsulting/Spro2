<?php

/* kpro@tom310316 */

function generaOdfDaTicket($ticket){
    
    /**
     * @author Tomiello Marco
     * @copyright Kpro Consulting Srl
     * @package fatturazioneConOdf
     * @version 1.0
     * 
     * Questa funzione genera gli OdF dai ticket
     */

    global $adb, $table_prefix,$current_user;

    $data_corrente = date("Y-m-d");

    $q_ticket = "SELECT 
                    tick.ticket_no ticket_no,
                    tick.ticketid ticketid,
                    tick.title title,
                    tick.hours hours,
                    tick.salesorder sales_order,
                    tick.servizio servizio,
                    tick.data_esecuzione data_chiusura,
                    tick.parent_id cliente,
                    tick.kp_business_unit kp_business_unit,
                    tick.kp_agente kp_agente,
                    tick.da_fatturare da_fatturare,
                    tick.discount_percent discount_percent,
                    tick.discount_amount discount_amount,
                    tick.total_notaxes total_notaxes,
                    tick.comment_line comment_line,
                    tick.quantity quantity,
                    tick.listprice listprice,
                    tick.so_line_id so_line_id,
                    tick.commessa commessa,
                    tick.prezzo prezzo,
                    serv.servicename servicename,
                    serv.unit_price prezzo_servizio,
                    serv.service_usageunit service_usageunit,
                    tick.description description,
                    ent.smownerid smownerid
                    FROM {$table_prefix}_troubletickets tick
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
                    LEFT JOIN {$table_prefix}_service serv ON serv.serviceid = tick.servizio
                    WHERE tick.ticketid = ".$ticket;

    $res_ticket = $adb->query($q_ticket);
    if($adb->num_rows($res_ticket)>0){
        $ticket_no = $adb->query_result($res_ticket,0,'ticket_no');
        $ticket_no = html_entity_decode(strip_tags($ticket_no), ENT_QUOTES,$default_charset);

        $title = $adb->query_result($res_ticket,0,'title');
        $title = html_entity_decode(strip_tags($title), ENT_QUOTES,$default_charset);

        $ore = $adb->query_result($res_ticket,0,'hours');
        $ore = html_entity_decode(strip_tags($ore), ENT_QUOTES,$default_charset);
        if($ore == "" || $ore == null){
            $ore = 0;
        }

        $sales_order = $adb->query_result($res_ticket,0,'sales_order');
        $sales_order = html_entity_decode(strip_tags($sales_order), ENT_QUOTES,$default_charset);
        if($sales_order == "" || $sales_order == null){
            $sales_order = 0;
        }

        /* kpro@bid24112017 */
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

        $servizio = $adb->query_result($res_ticket,0,'servizio');
        $servizio = html_entity_decode(strip_tags($servizio), ENT_QUOTES,$default_charset);
        if($servizio == "" || $servizio == null){
            $servizio = 0;
        }

        $cliente = $adb->query_result($res_ticket,0,'cliente');
        $cliente = html_entity_decode(strip_tags($cliente), ENT_QUOTES,$default_charset);
        if($cliente == "" || $cliente == null){
            $cliente = 0;
        }
        else{
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

        $data_chiusura = $adb->query_result($res_ticket,0,'data_chiusura');
        $data_chiusura = html_entity_decode(strip_tags($data_chiusura), ENT_QUOTES,$default_charset);

        $servicename = $adb->query_result($res_ticket,0,'servicename');
        $servicename = html_entity_decode(strip_tags($servicename), ENT_QUOTES,$default_charset);

        $prezzo_servizio = $adb->query_result($res_ticket,0,'prezzo_servizio');
        $prezzo_servizio = html_entity_decode(strip_tags($prezzo_servizio), ENT_QUOTES,$default_charset);
        if($prezzo_servizio == "" || $prezzo_servizio == null){
            $prezzo_servizio = 0;
        }

        $description = $adb->query_result($res_ticket,0,'description');
        $description = html_entity_decode(strip_tags($description), ENT_QUOTES,$default_charset);
        
        $listprice = $adb->query_result($res_ticket,0,'listprice');
        $listprice = html_entity_decode(strip_tags($listprice), ENT_QUOTES,$default_charset);
        if($listprice == "" || $listprice == null){
            $listprice = 0;
        }
        
        $quantity = $adb->query_result($res_ticket,0,'quantity');
        $quantity = html_entity_decode(strip_tags($quantity), ENT_QUOTES,$default_charset);
        if($quantity == "" || $quantity == null){
            $quantity = 0;
        }
        
        $prezzo = $adb->query_result($res_ticket,0,'prezzo');
        $prezzo = html_entity_decode(strip_tags($prezzo), ENT_QUOTES,$default_charset);
        if($prezzo == "" || $prezzo == null){
            $prezzo = 0;
        }
        
        $business_unit = $adb->query_result($res_ticket,0,'kp_business_unit');
        $business_unit = html_entity_decode(strip_tags($business_unit), ENT_QUOTES,$default_charset);
        if($business_unit == '' || $business_unit == null){
            $business_unit = 0;
        }

        $agente = $adb->query_result($res_ticket,0,'kp_agente');
        $agente = html_entity_decode(strip_tags($agente), ENT_QUOTES,$default_charset);
        if($agente == '' || $agente == null){
            $agente = 0;
        }
        
        $service_usageunit = $adb->query_result($res_ticket,0,'service_usageunit');
        $service_usageunit = html_entity_decode(strip_tags($service_usageunit), ENT_QUOTES,$default_charset);
        
        $smownerid = $adb->query_result($res_ticket,0,'smownerid');
        $smownerid = html_entity_decode(strip_tags($smownerid), ENT_QUOTES,$default_charset);
        
        $so_line_id = $adb->query_result($res_ticket,0,'so_line_id');
        $so_line_id = html_entity_decode(strip_tags($so_line_id), ENT_QUOTES,$default_charset);
        if($so_line_id == "" || $so_line_id == null){
            $so_line_id = 0;
        }
        
        $discount_percent = $adb->query_result($res_ticket,0,'discount_percent');
        $discount_percent = html_entity_decode(strip_tags($discount_percent), ENT_QUOTES,$default_charset);
        if($discount_amount == "" || $discount_amount == null){
            $discount_amount = 0;
        }
        
        $discount_amount = $adb->query_result($res_ticket,0,'discount_amount');
        $discount_amount = html_entity_decode(strip_tags($discount_amount), ENT_QUOTES,$default_charset);
        if($discount_amount == "" || $discount_amount == null){
            $discount_amount = 0;
        }
        
        $total_notaxes = $adb->query_result($res_ticket,0,'total_notaxes');
        $total_notaxes = html_entity_decode(strip_tags($total_notaxes), ENT_QUOTES,$default_charset);
        if($total_notaxes == "" || $total_notaxes == null){
            $total_notaxes = 0;
        }
        
        $comment_line = $adb->query_result($res_ticket,0,'comment_line');
        $comment_line = html_entity_decode(strip_tags($comment_line), ENT_QUOTES,$default_charset);
        if($comment_line == null){
            $comment_line = "";
        }
        
        $commessa = $adb->query_result($res_ticket,0,'commessa');
        $commessa = html_entity_decode(strip_tags($commessa), ENT_QUOTES,$default_charset);
        if($commessa == "" || $commessa == null){
            $commessa = 0;
        }

        if($quantity == 0){
            $quantity = 1;
        }

        if($prezzo == 0){
            $prezzo = $total_notaxes;
        }

        if($listprice == 0){
            $listprice = $total_notaxes;
        }

        if($servizio != 0 && $cliente != 0){

            if($total_notaxes != 0){

                if($quantity != 0){

                    $q_verifica_es = "SELECT odfid FROM {$table_prefix}_odf
                                        INNER JOIN {$table_prefix}_crmentity ON crmid = odfid
                                        WHERE deleted = 0 AND related_to =".$ticket;
                    $res_verifica_es = $adb->query($q_verifica_es);

                    if($adb->num_rows($res_verifica_es)==0){
                        
                        $odf = CRMEntity::getInstance('OdF');
                        $odf->column_fields['tipo_odf'] = 'Ticket';
                        $odf->column_fields['cliente_fatt'] = $cliente;
                        $odf->column_fields['related_to'] = $ticket;
                        $odf->column_fields['data_related_to'] = $data_chiusura;
                        $odf->column_fields['rif_related_to'] = $ticket_no;
                        $odf->column_fields['prezzo_unitario'] = $listprice;
                        $odf->column_fields['qta_eseguita'] = $quantity;
                        $odf->column_fields['qta_fatturata'] = $quantity;
                        $odf->column_fields['prezzo_totale'] = $prezzo;
                        $odf->column_fields['servizio'] = $servizio;
                        $odf->column_fields['kp_business_unit'] = $business_unit;
                        $odf->column_fields['kp_agente'] = $agente;
                        $odf->column_fields['data_odf'] = $data_corrente;
                        $odf->column_fields['stato_odf'] = 'Creato';
                        $odf->column_fields['service_usageunit'] = $service_usageunit;
                        $odf->column_fields['assigned_user_id'] = $smownerid;
                        $odf->column_fields['so_line_id'] = $so_line_id;
                        $odf->column_fields['discount_percent'] = $discount_percent;
                        $odf->column_fields['discount_amount'] = $discount_amount;
                        $odf->column_fields['total_notaxes'] = $total_notaxes;
                        $odf->column_fields['comment_line'] = $comment_line;
                        $odf->column_fields['description'] = utf8_encode($description);
                        $odf->column_fields['commessa'] = $commessa;
                        /* kpro@bid24112017 */
                        $odf->column_fields['kp_mod_pagamento'] = $mod_pagamento;
                        $odf->column_fields['kp_contatto'] = $contatto;
                        $odf->column_fields['kp_conto_corrente'] = $conto_corrente;
                        $odf->column_fields['kp_banca_cliente'] = $banca_cliente;
                        /* kpro@bid24112017 end */
                        $odf->save('OdF', $longdesc=true, $offline_update=false, $triggerEvent=false); 

                        $upd_ticket = "UPDATE {$table_prefix}_troubletickets SET
                                        status = 'Emesso OdF'
                                        WHERE ticketid = ".$ticket;
                        $res_upd_ticket = $adb->query($upd_ticket);

                        $risultato = 1;
                    }

                }
                else{
                    $risultato = 4;
                }

            }
            else{
                $risultato = 3;
            }

        }
        else if($servizio == 0){
            $risultato = 2;
        }
        else if($cliente == 0){
            $risultato = 5;
        }

    }

    return $risultato;

}
	
?>