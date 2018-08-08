<?php

/* kpro@tom20042017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

require_once('KpConfig.php');
require_once('KpMigrazioneDaAltroSistema.php');

include_once($root_sistema_destinazione.'config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $current_user, $adb, $table_prefix, $default_charset, $current_user;
session_start();
ini_set('memory_limit','256M');

$current_user->id = 1;

class KpAutogenerazioneEventiDaDatePianificateHelpDesk {

    private $delete_if_exist = false;

    public function run(){
        global $adb, $table_prefix, $default_charset, $current_user;

        $lista_ticket_pianificati = $this->getTicketPianificati();
        //print_r($lista_ticket_pianificati);

        foreach($lista_ticket_pianificati as $ticket){

            $this->setEventoTicket($ticket);

        }

    }

    public function setDeleteIfExist($status){
        global $adb, $table_prefix, $default_charset, $current_user;

        $delete_if_exist = $status;

    }

    private function getTicketPianificati(){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    tick.ticketid id,
                    tick.title nome,
                    tick.status stato,
                    tick.kp_data_inizio_pian data_inizio,
                    tick.kp_ora_inizio_tick ora_inizio,
                    tick.kp_tempo_previsto tempo_previsto,
                    ent.smownerid assegnatario
                    FROM {$table_prefix}_troubletickets tick
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
                    WHERE ent.deleted = 0 AND tick.status IN ('Open', 'In Progress', 'Wait For Response', 'Maintain', 'Caricato Documento', 'Da revisionare', 'In approvazione')
                    AND tick.kp_data_inizio_pian != '' AND tick.kp_data_inizio_pian != '000-00-00' AND tick.kp_ora_inizio_tick != '' AND tick.kp_ora_inizio_tick != '00:00'";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i=0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES,$default_charset);

            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES,$default_charset);

            $data_inizio = $adb->query_result($result_query, $i, 'data_inizio');
            $data_inizio = html_entity_decode(strip_tags($data_inizio), ENT_QUOTES,$default_charset);

            $ora_inizio = $adb->query_result($result_query, $i, 'ora_inizio');
            $ora_inizio = html_entity_decode(strip_tags($ora_inizio), ENT_QUOTES,$default_charset);

            $tempo_previsto = $adb->query_result($result_query, $i, 'tempo_previsto');
            $tempo_previsto = html_entity_decode(strip_tags($tempo_previsto), ENT_QUOTES,$default_charset);

            $assegnatario = $adb->query_result($result_query, $i, 'assegnatario');
            $assegnatario = html_entity_decode(strip_tags($assegnatario), ENT_QUOTES,$default_charset);

            $ora_fine = $this->getOraFine($ora_inizio, $tempo_previsto);

            $result[] = array("id" => $id,
                            "nome" => $nome,
                            "data_inizio" => $data_inizio,
                            "ora_inizio" => $ora_inizio,
                            "tempo_previsto" => $tempo_previsto,
                            "ora_fine" => $ora_fine,
                            "assegnatario" => $assegnatario);

        }

        return $result;

    }

    private function getOraFine($inizio, $tempo_previsto){
        global $adb, $table_prefix, $default_charset, $current_user;

        if($inizio == null || $inizio == ""){
            $inizio = "00:00";
        }

        if($tempo_previsto == null || $tempo_previsto == ""){
            $tempo_previsto = 0;
        }

        $tempo_previsto = (int) $tempo_previsto;

        list($ora, $minuti) = explode(":", $inizio);

        $ora_fine = (int)$ora + $tempo_previsto;

        $ora_fine = str_pad($ora_fine, 2, "0", STR_PAD_LEFT);

        $fine = $ora_fine.":".$minuti;

        return $fine;

    }

    private function setEventoTicket($ticket){
        global $adb, $table_prefix, $default_charset, $current_user;

        $esiste_evento = $this->checkIfEventoEsiste($ticket["id"]);

        if( $esiste_evento["esiste"] ){

        }
        else{

            $this->creaEvento($ticket);

        }


    }

    private function checkIfEventoEsiste($ticket_id){
        global $adb, $table_prefix, $default_charset, $current_user;

        $query = "SELECT 
                    act.activityid id
                    FROM {$table_prefix}_activity act 
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = act.activityid
                    INNER JOIN {$table_prefix}_seactivityrel actrel ON actrel.activityid = act.activityid
                    WHERE ent.deleted = 0 AND actrel.crmid = ".$ticket_id;

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $esiste = true;

            $id = $adb->query_result($result_query, 0, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES,$default_charset);

        }
        else{

            $esiste = false;

            $id = 0;

        }

        $result = array("esiste" => $esiste,
                        "id" => $id);

        return $result;

    }

    private function creaEvento($ticket){
        global $adb, $table_prefix, $default_charset, $current_user;

        $focus = CRMEntity::getInstance('Calendar');
        $focus->column_fields['assigned_user_id'] = $ticket["assegnatario"];
        $focus->column_fields['subject'] = $ticket["nome"];
        $focus->column_fields['activitytype'] = "Intervento";
        $focus->column_fields['date_start'] = $ticket["data_inizio"];
        $focus->column_fields['due_date'] = $ticket["data_inizio"];
        $focus->column_fields['time_start'] = $ticket["ora_inizio"]; 
        $focus->column_fields['time_end'] = $ticket["ora_fine"]; 
        $focus->column_fields['kp_durata_prevista'] = $ticket["tempo_previsto"]; 
        $focus->column_fields['eventstatus'] = "Planned";
        $focus->column_fields['priority'] = "High";
        $focus->column_fields['visibility'] = "Standard";
        $focus->column_fields['parent_id'] = $ticket["id"];
        $focus->save('Calendar', $longdesc=true, $offline_update=false, $triggerEvent=false);
        $record = $focus->id;

    }


}

$creator = new KpAutogenerazioneEventiDaDatePianificateHelpDesk();
$creator->setDeleteIfExist(true);
$creator->run();

?>