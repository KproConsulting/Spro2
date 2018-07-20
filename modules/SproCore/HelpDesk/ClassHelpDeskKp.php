<?php 
require_once('modules/HelpDesk/HelpDesk.php');
require_once('modules/SproCore/SproUtils/spro_utils.php');

class HelpDeskKp extends HelpDesk {

    //Script modifica Related List
    var $list_fields = Array();

    var $list_fields_name = Array(
        'Ticket No'=>'ticket_no',
        'Title'=>'ticket_title',
        'Related To'=>'parent_id',
        'Status'=>'ticketstatus',
        'Data Consegna'=>'kp_data_consegna',
        'Assigned To'=>'assigned_user_id' 
    );

    function HelpDeskKp(){
        global $table_prefix;
        parent::__construct();
        $this->list_fields = Array(
            'Ticket No'=>Array($table_prefix.'_troubletickets'=>'ticket_no'),
            'Title'=>Array($table_prefix.'_troubletickets'=>'title'),
            'Related To'=>Array($table_prefix.'_troubletickets'=>'parent_id'),
            'Status'=>Array($table_prefix.'_troubletickets'=>'status'),
            'Data Consegna'=>Array($table_prefix.'_troubletickets'=>'kp_data_consegna'),
            'Assigned To'=>Array($table_prefix.'_crmentity'=>'smownerid') 
        );

    }
    
    //Script modifica Funtion Save
    function save_module($module){

        global $table_prefix, $adb;

        parent::save_module($module);

        if($this->column_fields['ticketstatus'] == 'Closed' && ($this->column_fields['data_esecuzione'] == '' || $this->column_fields['data_esecuzione'] == '0000-00-00') ){

            $data_corrente = date("Y-m-d");

            $upd_ticket = "UPDATE {$table_prefix}_troubletickets SET
                            data_esecuzione = '".$data_corrente."'
                            WHERE ticketid = ".$this->id;
            $res_upd_ticket = $adb->query($upd_ticket);

        }
        
        $q_tempo_ticket = "SELECT 
							tick.kp_tempo_previsto tempo_previsto_ticket,
							tick.servizio servizio,
							serv.kp_tempo_previsto tempo_previsto_servizio
							from {$table_prefix}_troubletickets tick
							inner join {$table_prefix}_service serv on serv.serviceid = tick.servizio
							where tick.ticketid = ".$this->id;
		$res_tempo_ticket = $adb->query($q_tempo_ticket);
		if($adb->num_rows($res_tempo_ticket)>0){
	        $tempo_previsto_ticket = $adb->query_result($res_tempo_ticket, 0, 'tempo_previsto_ticket');
	        $tempo_previsto_ticket = html_entity_decode(strip_tags($tempo_previsto_ticket), ENT_QUOTES,$default_charset);
	        
	        $servizio = $adb->query_result($res_tempo_ticket, 0, 'servizio');
	        $servizio = html_entity_decode(strip_tags($servizio), ENT_QUOTES,$default_charset);
	        
	        $tempo_previsto_servizio = $adb->query_result($res_tempo_ticket, 0, 'tempo_previsto_servizio');
	        $tempo_previsto_servizio = html_entity_decode(strip_tags($tempo_previsto_servizio), ENT_QUOTES,$default_charset);
	        
	        if(($tempo_previsto_ticket == null || $tempo_previsto_ticket == "" || $tempo_previsto_ticket == 0) && ($servizio != null && $servizio != "" && $servizio != 0) && ($tempo_previsto_servizio != null && $tempo_previsto_servizio != "" && $tempo_previsto_servizio != 0)){
			
				$upd_ticket = "UPDATE {$table_prefix}_troubletickets set
								kp_tempo_previsto = ".$tempo_previsto_servizio."
								where ticketid = ".$this->id;
				$adb->query($upd_ticket);
				
			}
	        
        }
        
        if($this->column_fields['ticketstatus'] == 'Closed' && ($this->column_fields['kp_generato_succ'] != 'on' && $this->column_fields['kp_generato_succ'] != '1')
        && ($this->column_fields['kp_ripetitivo'] == 'on' || $this->column_fields['kp_ripetitivo'] == '1')){

            $frequenza_fatturazione = $this->column_fields['frequenza_fatturazione'];
            $data_fine_fatturazione = $this->column_fields['kp_data_fine_fatt'];
            if($data_fine_fatturazione == null || $data_fine_fatturazione == '0000-00-00'){
                $data_fine_fatturazione = '';
            }
            $this->DuplicaTicketRipetitivo($this->id, $frequenza_fatturazione, $data_fine_fatturazione);
        }

        /* kpro@bid080220181130 */

        require_once('modules/SproCore/HelpDesk/HelpDesk_utils.php');

        RicalcoloStatoTicketSalesorder($this->column_fields['salesorder']);

        /* kpro@bid080220181130 end */

        /* kpro@bid281220171400 */

        require_once('modules/SproCore/Timecards/Timecards_utils.php');

        RicalcoloCostiTicket($this->id);

        RicalcoloDatiPianificazione($this->id);

        /* kpro@bid281220171400 end */

    }

    function DuplicaTicketRipetitivo($record, $frequenza_fatturazione, $data_fine_fatturazione){
        global $table_prefix, $adb, $default_charset;

        $data_consegna = $this->column_fields['kp_data_consegna'];
        if($data_consegna == '' || $data_consegna == null || $data_consegna == '0000-00-00'){
            $data_consegna = date("Y-m-d");
        }
        /* kpro@bid200720181500 */
        $new_data_consegna = $this->CalcolaDataTicketRipetitivo($data_consegna, $frequenza_fatturazione);
        if($data_fine_fatturazione != '' && $new_data_consegna > $data_fine_fatturazione){
            $new_data_consegna = '';
        }
        /* kpro@bid200720181500 end */

        if($new_data_consegna != ''){
            /* kpro@bid200720181500 */
            $data_inizio_pianificata = $this->column_fields['kp_data_inizio_pian'];
            $new_data_inizio_pianificata = $this->CalcolaDataTicketRipetitivo($data_inizio_pianificata, $frequenza_fatturazione);
            /* kpro@bid200720181500 end */
            $q_dati_ticket = "SELECT *
                            FROM {$table_prefix}_troubletickets tick
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
                            WHERE ent.deleted = 0 AND tick.ticketid = ".$record;
            $res_dati_ticket = $adb->query($q_dati_ticket);
            if($adb->num_rows($res_dati_ticket) > 0){

                $array_colonne = array(
                    'title',
                    'servizio',
                    'area_aziendale',
                    'parent_id',
                    'salesorder',
                    'kp_business_unit',
                    'kp_agente',
                    'commessa',
                    'comment_line',
                    'kp_data_elem_rif',
                    'da_fatturare',
                    'kp_stabilimento',
                    'severity',
                    'kp_fornitore',
                    'kp_ripetitivo',
                    'frequenza_fatturazione',
                    'kp_data_fine_fatt',
                    'canone',
                    'projectplanid',
                    'projecttaskid',
                    'smownerid',
                    'description'
                );

                $prezzo = $adb->query_result($res_dati_ticket, 0, 'prezzo');
                $prezzo = html_entity_decode(strip_tags($prezzo), ENT_QUOTES,$default_charset);
                if($prezzo == "" || $prezzo == null){
                    $prezzo = 0;
                }
                
                $so_line_id = $adb->query_result($res_dati_ticket, 0, 'so_line_id');
                $so_line_id = html_entity_decode(strip_tags($so_line_id), ENT_QUOTES,$default_charset);
                if($so_line_id == "" || $so_line_id == null){
                    $so_line_id = 0;
                }
                
                $listprice = $adb->query_result($res_dati_ticket, 0, 'listprice');
                $listprice = html_entity_decode(strip_tags($listprice), ENT_QUOTES,$default_charset);
                if($listprice == "" || $listprice == null){
                    $listprice = 0;
                }
                
                $quantity = $adb->query_result($res_dati_ticket, 0, 'quantity');
                $quantity = html_entity_decode(strip_tags($quantity), ENT_QUOTES,$default_charset);
                if($quantity == "" || $quantity == null){
                    $quantity = 0;
                }
                
                $discount_percent = $adb->query_result($res_dati_ticket, 0, 'discount_percent');
                $discount_percent = html_entity_decode(strip_tags($discount_percent), ENT_QUOTES,$default_charset);
                if($discount_percent == "" || $discount_percent == null){
                    $discount_percent = 0;
                }
                
                $discount_amount = $adb->query_result($res_dati_ticket, 0, 'discount_amount');
                $discount_amount = html_entity_decode(strip_tags($discount_amount), ENT_QUOTES,$default_charset);
                if($discount_amount == "" || $discount_amount == null){
                    $discount_amount = 0;
                }
                
                $total_notaxes = $adb->query_result($res_dati_ticket, 0, 'total_notaxes');
                $total_notaxes = html_entity_decode(strip_tags($total_notaxes), ENT_QUOTES,$default_charset);
                if($total_notaxes == "" || $total_notaxes == null){
                    $total_notaxes = 0;
                }

                $kp_tempo_previsto = $adb->query_result($res_dati_ticket, 0, 'kp_tempo_previsto');
                $kp_tempo_previsto = html_entity_decode(strip_tags($kp_tempo_previsto), ENT_QUOTES,$default_charset);
                if($kp_tempo_previsto == "" || $kp_tempo_previsto == null){
                    $kp_tempo_previsto = 0;
                }

                $new_ticket = CRMEntity::getInstance('HelpDesk');
                $new_ticket->column_fields['ticketstatus'] = 'Open';
                $new_ticket->column_fields['kp_data_consegna'] = $new_data_consegna;
                /* kpro@bid200720181500 */
                if($new_data_inizio_pianificata != ''){
                    $new_ticket->column_fields['kp_data_inizio_pian'] = $new_data_inizio_pianificata;
                }
                /* kpro@bid200720181500 end */

                foreach($array_colonne as $nome_colonna){
                    $nome_campo = $this->GetFieldName($nome_colonna, 13);
                    if($nome_campo != ""){
                        $valore = $adb->query_result($res_dati_ticket, 0, $nome_colonna);
        
                        $new_ticket->column_fields[$nome_campo] = $valore;
        
                    }
                }

                $new_ticket->save('HelpDesk', $longdesc=true, $offline_update=false, $triggerEvent=false);
                $new_ticketid = $new_ticket->id;

                $upd_new_ticket = "UPDATE {$table_prefix}_troubletickets SET
                            prezzo = {$prezzo},
                            so_line_id = {$so_line_id},
                            discount_percent = {$discount_percent},
                            discount_amount = {$discount_amount},
                            total_notaxes = {$total_notaxes},
                            quantity = {$quantity},
                            listprice = {$listprice},
                            kp_tempo_previsto = {$kp_tempo_previsto}
                            WHERE ticketid = ".$new_ticketid;
                $adb->query($upd_new_ticket);

                $upd_ticket = "UPDATE {$table_prefix}_troubletickets
                        SET kp_generato_succ = '1'
                        WHERE ticketid = ".$record;
                $adb->query($upd_ticket);
            }
        }
    }
    /* kpro@bid200720181500 */
    function CalcolaDataTicketRipetitivo($data, $frequenza_fatturazione){
        global $table_prefix, $adb, $default_charset;

        $numeroMesiIncremento = calcolaNumeroMesiIncremento($frequenza_fatturazione);

        $data_dt = new DateTime($data);
        $giorno_consegna = $data_dt->format("d");
        $mese_consegna = $data_dt->format("m");
        $anno_consegna = $data_dt->format("Y");
        $data_temp = $anno_consegna."-".$mese_consegna."-01";

        $date = date_create($data_temp);
        date_add($date,date_interval_create_from_date_string($numeroMesiIncremento." months"));
        $new_mese_consegna = date_format($date,"m");
        $new_anno_consegna = date_format($date,"Y");

        $giorni_nel_mese = cal_days_in_month(CAL_GREGORIAN, $new_mese_consegna, $new_anno_consegna);
        if((int)$giorno_consegna > (int)$giorni_nel_mese){
            $giorno_consegna = $giorni_nel_mese;
        }

        $new_data = $new_anno_consegna."-".$new_mese_consegna."-".$giorno_consegna;

        return $new_data;
    }
    /* kpro@bid200720181500 end */

    function GetFieldName($nome_colonna, $modulo){
        global $adb, $table_prefix, $default_charset;
    
        $q = "SELECT fieldname
            FROM {$table_prefix}_field
            WHERE tabid = {$modulo}
            AND columnname = '{$nome_colonna}'";
        $res = $adb->query($q);
        if($adb->num_rows($res) > 0){
            $fieldname = $adb->query_result($res, 0, 'fieldname');
            $fieldname = html_entity_decode(strip_tags($fieldname), ENT_QUOTES,$default_charset);
            if($fieldname == null){
                $fieldname = "";
            }
        }
        else{
            $fieldname = "";
        }
    
        return $fieldname;
    }

}
?>
