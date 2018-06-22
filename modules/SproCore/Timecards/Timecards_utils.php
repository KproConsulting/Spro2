<?php

/* kpro@bid201220171630 */

/**
 * @author Bidese Jacopo
 * @copyright (c) 2017, Kpro Consulting Srl
 */

function getCostoUtente($ticket_id, $utente){
    global $adb, $table_prefix, $current_user;

    $costo_utente = 0;

    $servizio = getServizioTicket($ticket_id);
    if($servizio != 0){

        $q = "SELECT prcrel.listprice 
            FROM {$table_prefix}_users us
            INNER JOIN {$table_prefix}_pricebook prc ON prc.pricebookid = us.kp_listino
            INNER JOIN {$table_prefix}_pricebookproductrel prcrel ON prcrel.pricebookid = prc.pricebookid
            INNER JOIN {$table_prefix}_service ser ON ser.serviceid = prcrel.productid
            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = prc.pricebookid
            WHERE ent.deleted = 0 AND us.status = 'Active'
            AND us.deleted = 0 AND us.id = {$utente}
            AND ser.serviceid = ".$servizio;
        $res = $adb->query($q);
        if($adb->num_rows($res) > 0){
            $costo_utente = $adb->query_result($res, 0, 'listprice');
            $costo_utente = html_entity_decode(strip_tags($costo_utente), ENT_QUOTES, $default_charset);
            if($costo_utente == "" || $costo_utente == null){
                $costo_utente = 0;
            }
        }
    }

    if($costo_utente == 0){
        $q = "SELECT us.kp_costo_utente
            FROM {$table_prefix}_users us
            WHERE us.status = 'Active'
            AND us.deleted = 0 AND us.id = {$utente}";
        $res = $adb->query($q);
        if($adb->num_rows($res) > 0){
            $costo_utente = $adb->query_result($res, 0, 'kp_costo_utente');
            $costo_utente = html_entity_decode(strip_tags($costo_utente), ENT_QUOTES, $default_charset);
            if($costo_utente == "" || $costo_utente == null){
                $costo_utente = 0;
            }
        }
    }
    
    return $costo_utente;
}

function getServizioTicket($ticket_id){
    global $adb, $table_prefix, $current_user;

    $servizio = 0;

    $q = "SELECT ser.serviceid
        FROM {$table_prefix}_troubletickets tick
        INNER JOIN {$table_prefix}_service ser ON ser.serviceid = tick.servizio
        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = ser.serviceid
        WHERE ent.deleted = 0 AND tick.ticketid = ".$ticket_id;
    $res = $adb->query($q);
    if($adb->num_rows($res) > 0){
        $servizio = $adb->query_result($res, 0, 'serviceid');
        $servizio = html_entity_decode(strip_tags($servizio), ENT_QUOTES, $default_charset);
        if($servizio == "" || $servizio == null){
            $servizio = 0;
        }
    }

    return $servizio;
}

function RicalcoloCostiTicket($ticket){

    RicalcoloCostoPrevisto($ticket);

    RicalcoloMarginePrevisto($ticket);
    
    RicalcoloCostoInterno($ticket);

    RicalcoloMargine($ticket);

    RicalcoloOreEffettive($ticket);

    RicalcoloDeltaOreEffettive($ticket);

    RicalcoloDeltaOreConsuntive($ticket);

    RicalcoloDeltaCosti($ticket);

    RicalcoloKmPercorsi($ticket);

    RicalcoloOreViaggio($ticket);
    
}

function RicalcoloDatiPianificazione($ticket){

    require_once('modules/SproCore/CustomViews/GanttProjectPlan/Utility.php');

    $task = getTicketTask($ticket);
    if($task != 0){
        calcolaOreLavorateRisorsaTask($task);

        calcolaOreLavorateTask($task);

        calcolaPercentualeCompletamentoTask($task);
    }

}

function getTicketTask($ticket){
    global $adb, $table_prefix, $current_user;

    $task = 0;

    $q = "SELECT tick.projecttaskid
        FROM {$table_prefix}_troubletickets tick
        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
        WHERE ent.deleted = 0 AND tick.ticketid = ".$ticket;
    $res = $adb->query($q);
    if($adb->num_rows($res) > 0){
        $task = $adb->query_result($res, 0, 'projecttaskid');
        $task = html_entity_decode(strip_tags($task), ENT_QUOTES, $default_charset);
        if($task == "" || $task == null){
            $task = 0;
        }
    }

    return $task;
}

function RicalcoloCostoPrevisto($ticket){
    global $adb, $table_prefix, $current_user;

    $costo_previsto = 0;

    $q = "SELECT tick.kp_tempo_previsto,
        ent.smownerid
        FROM {$table_prefix}_troubletickets tick
        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
        WHERE ent.deleted = 0 AND tick.ticketid = ".$ticket;
    $res = $adb->query($q);
    if($adb->num_rows($res) > 0){
        $tempo_previsto = $adb->query_result($res, 0, 'kp_tempo_previsto');
        $tempo_previsto = html_entity_decode(strip_tags($tempo_previsto), ENT_QUOTES, $default_charset);
        if($tempo_previsto == "" || $tempo_previsto == null){
            $tempo_previsto = 0;
        }

        $utente = $adb->query_result($res, 0, 'smownerid');
        $utente = html_entity_decode(strip_tags($utente), ENT_QUOTES, $default_charset);
        if($utente == "" || $utente == null){
            $utente = 1;
        }

        $costo_utente = getCostoUtente($ticket, $utente);

        $costo_previsto = $costo_utente * $tempo_previsto;
    }

    $update = "UPDATE {$table_prefix}_troubletickets
        SET kp_costo_prev = {$costo_previsto}
        WHERE ticketid = ".$ticket;
    $adb->query($update);
}

function RicalcoloMarginePrevisto($ticket){
    global $adb, $table_prefix, $current_user;
    
    $q = "SELECT tick.total_notaxes,
        tick.kp_costo_prev
        FROM {$table_prefix}_troubletickets tick
        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
        WHERE ent.deleted = 0 AND tick.da_fatturare = '1'
        AND tick.ticketid = ".$ticket;
    $res = $adb->query($q);
    if($adb->num_rows($res) > 0){
        $prezzo_da_fatturare = $adb->query_result($res, 0, 'total_notaxes');
        $prezzo_da_fatturare = html_entity_decode(strip_tags($prezzo_da_fatturare), ENT_QUOTES, $default_charset);
        if($prezzo_da_fatturare == "" || $prezzo_da_fatturare == null){
            $prezzo_da_fatturare = 0;
        }

        $costo_previsto = $adb->query_result($res, 0, 'kp_costo_prev');
        $costo_previsto = html_entity_decode(strip_tags($costo_previsto), ENT_QUOTES, $default_charset);
        if($costo_previsto == "" || $costo_previsto == null){
            $costo_previsto = 0;
        }

        $margine_previsto = $prezzo_da_fatturare - $costo_previsto;

        $update = "UPDATE {$table_prefix}_troubletickets
            SET kp_margine_prev = {$margine_previsto}
            WHERE ticketid = ".$ticket;
        $adb->query($update);
    }
}

function RicalcoloCostoInterno($ticket){
    global $adb, $table_prefix, $current_user;

    $costo_ticket = 0;

    $q = "SELECT tmc.kp_costo_interno 
        FROM {$table_prefix}_troubletickets tick
        INNER JOIN {$table_prefix}_timecards tmc ON tmc.ticket_id = tick.ticketid
        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tmc.timecardsid
        WHERE ent.deleted = 0 AND tick.ticketid = ".$ticket;
    $res = $adb->query($q);
    $num = $adb->num_rows($res);
    if($num > 0){
        for($i = 0; $i < $num; $i++){
            $costo_intervento = $adb->query_result($res, $i, 'kp_costo_interno');
            $costo_intervento = html_entity_decode(strip_tags($costo_intervento), ENT_QUOTES, $default_charset);
            if($costo_intervento == "" || $costo_intervento == null){
                $costo_intervento = 0;
            }

            $costo_ticket += $costo_intervento;
        }
    }

    $update = "UPDATE {$table_prefix}_troubletickets
        SET kp_costo_interno = {$costo_ticket}
        WHERE ticketid = ".$ticket;
    $adb->query($update);
}

function RicalcoloMargine($ticket){
    global $adb, $table_prefix, $current_user;

    $q = "SELECT tick.total_notaxes,
    tick.kp_costo_interno
    FROM {$table_prefix}_troubletickets tick
    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
    WHERE ent.deleted = 0 AND tick.da_fatturare = '1'
    AND tick.ticketid = ".$ticket;
    $res = $adb->query($q);
    if($adb->num_rows($res) > 0){
        $prezzo_da_fatturare = $adb->query_result($res, 0, 'total_notaxes');
        $prezzo_da_fatturare = html_entity_decode(strip_tags($prezzo_da_fatturare), ENT_QUOTES, $default_charset);
        if($prezzo_da_fatturare == "" || $prezzo_da_fatturare == null){
            $prezzo_da_fatturare = 0;
        }

        $costo_ticket = $adb->query_result($res, 0, 'kp_costo_interno');
        $costo_ticket = html_entity_decode(strip_tags($costo_ticket), ENT_QUOTES, $default_charset);
        if($costo_ticket == "" || $costo_ticket == null){
            $costo_ticket = 0;
        }

        $margine = $prezzo_da_fatturare - $costo_ticket;

        $update = "UPDATE {$table_prefix}_troubletickets
            SET kp_margine = {$margine}
            WHERE ticketid = ".$ticket;
        $adb->query($update);
    }
}

function RicalcoloOreEffettive($ticket){
    global $adb, $table_prefix, $current_user, $default_charset;

    $tempo_effettivo = 0;
    $array_delimiters = array(':','.','-','/');

    $q = "SELECT tmc.kp_ore_effettive AS ore_effettive
        FROM {$table_prefix}_troubletickets tick
        INNER JOIN {$table_prefix}_timecards tmc ON tmc.ticket_id = tick.ticketid
        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
        INNER JOIN {$table_prefix}_crmentity ent1 ON ent1.crmid = tmc.timecardsid
        WHERE ent.deleted = 0 AND ent1.deleted = 0
        AND tick.ticketid = ".$ticket;
    $res = $adb->query($q);
    $num = $adb->num_rows($res);
    if($num > 0){
        $tot_ore = 0;
        $tot_minuti = 0;
        for($i = 0; $i < $num; $i++){
            $ore_effettive = $adb->query_result($res, $i, 'ore_effettive');
            $ore_effettive = html_entity_decode(strip_tags($ore_effettive), ENT_QUOTES,$default_charset);
            if($ore_effettive != "" && $ore_effettive != null){
                $pattern = '/^[0-2]{1}[0-9]{1}.[0-5]{1}[0-9]{1}$/';
			    if(preg_match($pattern, $ore_effettive)){
                    $array_ora_esploso = explode($array_delimiters[0], str_replace($array_delimiters, $array_delimiters[0], $ore_effettive));
                    $tot_ore += (int)$array_ora_esploso[0];
                    $tot_minuti += (int)$array_ora_esploso[1];	
                }
            }
        }

        $tempo_effettivo = round((($tot_ore * 60) + $tot_minuti) / 60, 2);
    }

    $update = "UPDATE {$table_prefix}_troubletickets
        SET kp_ore_effettive = {$tempo_effettivo}
        WHERE ticketid = ".$ticket;
    $adb->query($update);
}

function RicalcoloDeltaOreEffettive($ticket){
    global $adb, $table_prefix, $current_user;

    $q = "SELECT tick.kp_tempo_previsto,
    tick.kp_ore_effettive
    FROM {$table_prefix}_troubletickets tick
    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
    WHERE ent.deleted = 0 AND tick.ticketid = ".$ticket;
    $res = $adb->query($q);
    if($adb->num_rows($res) > 0){
        $tempo_previsto = $adb->query_result($res, 0, 'kp_tempo_previsto');
        $tempo_previsto = html_entity_decode(strip_tags($tempo_previsto), ENT_QUOTES, $default_charset);
        if($tempo_previsto == "" || $tempo_previsto == null){
            $tempo_previsto = 0;
        }

        $ore_effettive = $adb->query_result($res, 0, 'kp_ore_effettive');
        $ore_effettive = html_entity_decode(strip_tags($ore_effettive), ENT_QUOTES, $default_charset);
        if($ore_effettive == "" || $ore_effettive == null){
            $ore_effettive = 0;
        }

        $delta = $tempo_previsto - $ore_effettive;

        $update = "UPDATE {$table_prefix}_troubletickets
            SET kp_delta_ore = {$delta}
            WHERE ticketid = ".$ticket;
        $adb->query($update);
    }
}

function RicalcoloDeltaOreConsuntive($ticket){
    global $adb, $table_prefix, $current_user;

    $q = "SELECT tick.kp_tempo_previsto,
    tick.hours
    FROM {$table_prefix}_troubletickets tick
    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
    WHERE ent.deleted = 0 AND tick.ticketid = ".$ticket;
    $res = $adb->query($q);
    if($adb->num_rows($res) > 0){
        $tempo_previsto = $adb->query_result($res, 0, 'kp_tempo_previsto');
        $tempo_previsto = html_entity_decode(strip_tags($tempo_previsto), ENT_QUOTES, $default_charset);
        if($tempo_previsto == "" || $tempo_previsto == null){
            $tempo_previsto = 0;
        }

        $ore_consuntive = $adb->query_result($res, 0, 'hours');
        $ore_consuntive = html_entity_decode(strip_tags($ore_consuntive), ENT_QUOTES, $default_charset);
        if($ore_consuntive == "" || $ore_consuntive == null){
            $ore_consuntive = 0;
        }

        $delta = $tempo_previsto - $ore_consuntive;

        $update = "UPDATE {$table_prefix}_troubletickets
            SET kp_delta_ore_cons = {$delta}
            WHERE ticketid = ".$ticket;
        $adb->query($update);
    }
}

function RicalcoloDeltaCosti($ticket){
    global $adb, $table_prefix, $current_user;

    $q = "SELECT tick.kp_costo_prev,
    tick.kp_costo_interno
    FROM {$table_prefix}_troubletickets tick
    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
    WHERE ent.deleted = 0 AND tick.ticketid = ".$ticket;
    $res = $adb->query($q);
    if($adb->num_rows($res) > 0){
        $costo_previsto = $adb->query_result($res, 0, 'kp_costo_prev');
        $costo_previsto = html_entity_decode(strip_tags($costo_previsto), ENT_QUOTES, $default_charset);
        if($costo_previsto == "" || $costo_previsto == null){
            $costo_previsto = 0;
        }

        $costo_ticket = $adb->query_result($res, 0, 'kp_costo_interno');
        $costo_ticket = html_entity_decode(strip_tags($costo_ticket), ENT_QUOTES, $default_charset);
        if($costo_ticket == "" || $costo_ticket == null){
            $costo_ticket = 0;
        }

        $delta = $costo_previsto - $costo_ticket;

        $update = "UPDATE {$table_prefix}_troubletickets
            SET kp_delta_costi = {$delta}
            WHERE ticketid = ".$ticket;
        $adb->query($update);
    }
}

function RicalcoloKmPercorsi($ticket){
    global $adb, $table_prefix, $current_user;

    $km_percorsi_ticket = 0;

    $q = "SELECT tmc.kp_km_percorsi,
        tmc.kp_attiv_eseguita
        FROM {$table_prefix}_troubletickets tick
        INNER JOIN {$table_prefix}_timecards tmc ON tmc.ticket_id = tick.ticketid
        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tmc.timecardsid
        WHERE ent.deleted = 0 AND tick.ticketid = ".$ticket;
    $res = $adb->query($q);
    $num = $adb->num_rows($res);
    if($num > 0){
        for($i = 0; $i < $num; $i++){
            $attivita_eseguita = $adb->query_result($res, $i, 'kp_attiv_eseguita');
            $attivita_eseguita = html_entity_decode(strip_tags($attivita_eseguita), ENT_QUOTES, $default_charset);
            if($attivita_eseguita == null){
                $attivita_eseguita = "";
            }

            $km_percorsi_intervento = $adb->query_result($res, $i, 'kp_km_percorsi');
            $km_percorsi_intervento = html_entity_decode(strip_tags($km_percorsi_intervento), ENT_QUOTES, $default_charset);
            if($km_percorsi_intervento == "" || $km_percorsi_intervento == null){
                $km_percorsi_intervento = 0;
            }

            if($attivita_eseguita == "Dal cliente"){
                $km_percorsi_ticket += $km_percorsi_intervento;
            }
        }
    }

    $update = "UPDATE {$table_prefix}_troubletickets
        SET kp_km_percorsi = {$km_percorsi_ticket}
        WHERE ticketid = ".$ticket;
    $adb->query($update);
}

function RicalcoloOreViaggio($ticket){
    global $adb, $table_prefix, $current_user;

    $ore_viaggio_ticket = 0;

    $q = "SELECT tmc.kp_ore_viaggio,
        tmc.kp_attiv_eseguita
        FROM {$table_prefix}_troubletickets tick
        INNER JOIN {$table_prefix}_timecards tmc ON tmc.ticket_id = tick.ticketid
        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tmc.timecardsid
        WHERE ent.deleted = 0 AND tick.ticketid = ".$ticket;
    $res = $adb->query($q);
    $num = $adb->num_rows($res);
    if($num > 0){
        for($i = 0; $i < $num; $i++){
            $attivita_eseguita = $adb->query_result($res, $i, 'kp_attiv_eseguita');
            $attivita_eseguita = html_entity_decode(strip_tags($attivita_eseguita), ENT_QUOTES, $default_charset);
            if($attivita_eseguita == null){
                $attivita_eseguita = "";
            }

            $ore_viaggio_intervento = $adb->query_result($res, $i, 'kp_ore_viaggio');
            $ore_viaggio_intervento = html_entity_decode(strip_tags($ore_viaggio_intervento), ENT_QUOTES, $default_charset);
            if($ore_viaggio_intervento == "" || $ore_viaggio_intervento == null){
                $ore_viaggio_intervento = 0;
            }

            if($attivita_eseguita == "Dal cliente"){
                $ore_viaggio_ticket += $ore_viaggio_intervento;
            }
        }
    }

    $update = "UPDATE {$table_prefix}_troubletickets
        SET kp_ore_viaggio = {$ore_viaggio_ticket}
        WHERE ticketid = ".$ticket;
    $adb->query($update);
}

?>
