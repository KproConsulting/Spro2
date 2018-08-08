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

class KpAllineamentoCanoniVecchiaGestione {

    private $debug = false;
    private $limit = null;

    public function run(){
        global $adb, $table_prefix, $default_charset, $current_user;

        if($this->debug){
            printf("\nStart!");
        }

        $lista_canoni = $this->getListaCanoni();

        foreach($lista_canoni as $canone){

            $this->allineaCanone($canone);

        }

        if($this->debug){
            printf("\nEnd!");
        }

    }

    public function setDebug($value){
        global $adb, $table_prefix, $default_charset, $current_user;

        $this->debug = $value;

    }

    public function setLimit($value){
        global $adb, $table_prefix, $default_charset, $current_user;

        $this->limit = $value;

    }

    private function getListaCanoni(){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = array();

        $query = "SELECT 
                    can.canoniid id,
                    can.canone_name nome,
                    can.account account,
                    can.data_inizio data_inizio,
                    can.data_fine data_fine,
                    can.stato_canone stato_canone,
                    can.tipo_canone tipo_canone,
                    can.servizio servizio,
                    can.prezzo prezzo,
                    can.data_inizio_fatt data_inizio_fatt,
                    can.frequenza_fatturazione frequenza_fatturazione
                    FROM {$table_prefix}_canoni can
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = can.canoniid
                    WHERE ent.deleted = 0 AND can.data_inizio_fatt != ''";

        //$query .= " AND can.canoniid = 254016";

        if( $this->limit != null ){
            $query .= " LIMIT ".$this->limit;
        }

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        for($i = 0; $i < $num_result; $i++){

            $id = $adb->query_result($result_query, $i, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);
            
            $nome = $adb->query_result($result_query, $i, 'nome');
            $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES, $default_charset);

            $account = $adb->query_result($result_query, $i, 'account');
            $account = html_entity_decode(strip_tags($account), ENT_QUOTES, $default_charset);

            $data_inizio = $adb->query_result($result_query, $i, 'data_inizio');
            $data_inizio = html_entity_decode(strip_tags($data_inizio), ENT_QUOTES, $default_charset);

            $data_fine = $adb->query_result($result_query, $i, 'data_fine');
            $data_fine = html_entity_decode(strip_tags($data_fine), ENT_QUOTES, $default_charset);

            $stato_canone = $adb->query_result($result_query, $i, 'stato_canone');
            $stato_canone = html_entity_decode(strip_tags($stato_canone), ENT_QUOTES, $default_charset);

            $tipo_canone = $adb->query_result($result_query, $i, 'tipo_canone');
            $tipo_canone = html_entity_decode(strip_tags($tipo_canone), ENT_QUOTES, $default_charset);

            $servizio = $adb->query_result($result_query, $i, 'servizio');
            $servizio = html_entity_decode(strip_tags($servizio), ENT_QUOTES, $default_charset);

            $prezzo = $adb->query_result($result_query, $i, 'prezzo');
            $prezzo = html_entity_decode(strip_tags($prezzo), ENT_QUOTES, $default_charset);

            $data_inizio_fatt = $adb->query_result($result_query, $i, 'data_inizio_fatt');
            $data_inizio_fatt = html_entity_decode(strip_tags($data_inizio_fatt), ENT_QUOTES, $default_charset);

            $frequenza_fatturazione = $adb->query_result($result_query, $i, 'frequenza_fatturazione');
            $frequenza_fatturazione = html_entity_decode(strip_tags($frequenza_fatturazione), ENT_QUOTES, $default_charset);
            
            $result[] = array("id" => $id,
                                "nome" => $nome,
                                "account" => $account,
                                "data_inizio" => $data_inizio,
                                "data_fine" => $data_fine,
                                "stato_canone" => $stato_canone,
                                "tipo_canone" => $tipo_canone,
                                "servizio" => $servizio,
                                "prezzo" => $prezzo,
                                "data_inizio_fatt" => $data_inizio_fatt,
                                "frequenza_fatturazione" => $frequenza_fatturazione);

        }

        return $result;

    }

    private function allineaCanone($canone){
        global $adb, $table_prefix, $default_charset, $current_user;

        require_once(__DIR__.'/../../modules/SproCore/SproUtils/spro_utils.php');

        if($this->debug){
            printf("\n- Allineamento canone %s, %s frequenza fatturazione %s", $canone["id"], $canone["nome"], $canone["frequenza_fatturazione"] );
        }

        $ultimo_odf_approvato = $this->getUltimoOdf( $canone["id"], "('Approvato', 'Fatturato')" );

        if($this->debug){
            printf("\n--- Ultimo odf approvato o fatturato %s, %s", $ultimo_odf_approvato["id"], $ultimo_odf_approvato["data_odf"]);
        }

        $ultimo_odf_da_approvare = $this->getUltimoOdf( $canone["id"], "('Creato')" );

        if($this->debug){
            printf("\n--- Ultimo odf da approvare %s, %s", $ultimo_odf_da_approvare["id"], $ultimo_odf_da_approvare["data_odf"]);
        }

        $esiste_odf = false;

        if( $ultimo_odf_approvato["esiste"] ){

            if( $ultimo_odf_da_approvare["esiste"] && $ultimo_odf_da_approvare["data_odf"] >= $ultimo_odf_approvato["data_odf"] ){

                $odf_pendenti = true;
                $data_odf = $ultimo_odf_da_approvare["data_odf"];

                if($this->debug){
                    printf("\n-----> Ho un OdF pendente");
                }

            }
            else{

                $odf_pendenti = false;
                $data_odf = $ultimo_odf_approvato["data_odf"];

                if($this->debug){
                    printf("\n-----> NON ho OdF pendenti");
                }

            }

        }
        elseif( $ultimo_odf_approvato["esiste"]  ){

            $odf_pendenti = true;
            $data_odf = $ultimo_odf_da_approvare["data_odf"];

            if($this->debug){
                printf("\n-----> Ho un OdF pendente");
            }

        }
        else{

            $odf_pendenti = true;
            $data_odf = $canone["data_inizio_fatt"];

            if($this->debug){
                printf("\n-----> NON ho OdF");
            }

        }

        //Se ho un OdF pendente devo popolare i campi mese_fatturazione e kp_anno_fatt sulla base della data dell'OdF pendente
        //e i campi pros_mese_fatt e kp_pros_anno_fatt calcolando, sulla base della data dell'OdF pendente, la data della prossima fatturazione.

        //Se NON ho un OdF pendente devo popolare i campi mese_fatturazione e kp_anno_fatt sulla base della data dell'OdF fatturato/approvato
        //a cui sommero la frequenza di aggiornamento.

        $numero_mesi_incremento = calcolaNumeroMesiIncremento($canone["frequenza_fatturazione"]);

        if( $odf_pendenti ){

            list ($anno, $mese, $giorno) = explode('-', $data_odf);

            $data_fatturazione_temp = $anno."-".$mese."-01";
            $date = date_create($data_fatturazione_temp);
            date_add($date,date_interval_create_from_date_string($numero_mesi_incremento." months"));
            
            $prossimo_mese_fatturazione = ltrim(date_format($date,"m"),'0');
            $prossimo_anno_fatturazione = date_format($date,"Y"); 
            
            $corrente_anno_fatturazione = $anno;
            $corrente_mese_fatturazione = $mese;
            
            $corrente_mese_fatturazione = $this->nomalizzazioneMese($corrente_mese_fatturazione);

            $update = "UPDATE {$table_prefix}_canoni SET 
                        pros_mese_fatt = '".$prossimo_mese_fatturazione."',
                        kp_pros_anno_fatt = '".$prossimo_anno_fatturazione."',
                        kp_anno_fatt = '".$corrente_anno_fatturazione."',
                        mese_fatturazione = '".$corrente_mese_fatturazione."'
                        WHERE canoniid = ".$canone["id"];

        }
        else{

            list ($anno, $mese, $giorno) = explode('-', $data_odf);

            $data_fatturazione_temp = $anno."-".$mese."-01";
            $date = date_create($data_fatturazione_temp);
            date_add($date,date_interval_create_from_date_string($numero_mesi_incremento." months"));
            
            $corrente_mese_fatturazione = ltrim(date_format($date,"m"),'0');
            $corrente_anno_fatturazione = date_format($date,"Y"); 

            $update = "UPDATE {$table_prefix}_canoni SET 
                        pros_mese_fatt = '',
                        kp_pros_anno_fatt = '',
                        kp_anno_fatt = '".$corrente_anno_fatturazione."',
                        mese_fatturazione = '".$corrente_mese_fatturazione."'
                        WHERE canoniid = ".$canone["id"];

        }

        if($this->debug){
            printf("\n-------> Query %s", $update);
        }

        $adb->query($update);

    }

    private function nomalizzazioneMese($mese){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        switch ($mese) {
            case '01':
                $result = '1';
                break;
            case '02':
                $result = '2';
                break;
            case '03':
                $result = '3';
                break;
            case '04':
                $result = '4';
                break;
            case '05':
                $result = '5';
                break;
            case '06':
                $result = '6';
                break;
            case '07':
                $result = '7';
                break;
            case '08':
                $result = '8';
                break;
            case '09':
                $result = '9';
                break;
            default:
                $result = $mese;
        }

        return $result;

    }

    private function getUltimoOdf($canone_id, $stato){
        global $adb, $table_prefix, $default_charset, $current_user;

        $result = "";

        $query = "SELECT 
                    odf.odfid id,
                    odf.data_odf data_odf
                    FROM vte_odf odf
                    INNER JOIN vte_crmentity ent ON ent.crmid = odf.odfid
                    WHERE ent.deleted = 0 AND odf.data_odf != '' AND odf.related_to = ".$canone_id;

        if( $stato != "" ){
            $query .= " AND stato_odf IN ".$stato;
        }

        $query .= " ORDER BY odf.data_odf DESC";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0){

            $esiste = true;

            $id = $adb->query_result($result_query, 0, 'id');
            $id = html_entity_decode(strip_tags($id), ENT_QUOTES, $default_charset);

            $data_odf = $adb->query_result($result_query, 0, 'data_odf');
            $data_odf = html_entity_decode(strip_tags($data_odf), ENT_QUOTES, $default_charset);

        }
        else{

            $esiste = false;
            $id = 0;
            $data_odf = "";

        }

        $result = array("esiste" => $esiste,
                        "id" => $id,
                        "data_odf" => $data_odf);

        return $result;

    }


}

$allineatore = new KpAllineamentoCanoniVecchiaGestione();
$allineatore->setDebug(false);
$allineatore->setLimit(null);
$allineatore->run();


?>