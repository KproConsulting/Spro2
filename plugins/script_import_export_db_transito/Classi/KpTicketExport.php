<?php

/* kpro@tom01062017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

global $default_charset, $current_user, $adb, $table_prefix;

class KpTicketExport{

	public $id = 0;
	public $overwriteIfExist = false;

	private $id_testata_cal_orc = "";
	private $id_riga_cal_orc = "";
	private $id_testata_ors = "";
	private $id_riga_ors = "";
	private $tipo_documento_cal_orc = "";
	private $numero_documento_cal_orc = "";
	private $data_documento_cal_orc = "";
	private $posizione_riga_cal_orc = "";
	private $data_pianificata = "";
	private $data_chiusura = "";
	private $stato_ticket = "";
	private $numero_documento_ors = "";
	private $data_documento_ors = "";
	private $posizione_riga_ors = "";
	private $fornitore = "";
	private $totale_ore_pianificate = 0;
	private $totale_ore_eseguite = 0;
	private $esportato = false;		//I ticket vengono esportati solo se sono chiusi e non sono già stati esportati in precedenza

	public function __construct($id) {
		
		/* kpro@tom01062017 */

		/**
		* @author Tomiello Marco
		* @copyright (c) 2017, Kpro Consulting Srl
		*
		* Costruttore classe
		*/

		$this->id = $id;

	}

	public function export(){
		global $adb, $table_prefix, $current_user, $default_charset;

		/* kpro@tom01062017 */

		/**
		* @author Tomiello Marco
		* @copyright (c) 2017, Kpro Consulting Srl
		*
		* Funzione per l'export dati del record
		*/

		$this->getDatiRecord();

		if( $this->overwriteIfExist ){

			if ( $this->recordAlreadyExist() ){

				$this->updateRecordOnImportExportDB();

			}
			else{

				$this->insertRecordOnImportExportDB();

			}

		}
		else{

			$this->insertRecordOnImportExportDB();

		}

	}

	private function getDatiRecord(){
		global $adb, $table_prefix, $current_user, $default_charset;

		/* kpro@tom01062017 */

		/**
		* @author Tomiello Marco
		* @copyright (c) 2017, Kpro Consulting Srl
		*
		* Funzione per il recupero dati del record
		*/

		$query = "SELECT 
					tick.kp_id_testata kp_id_testata,
					tick.kp_id_riga kp_id_riga,
					tick.kp_id_testata_ors kp_id_testata_ors,
					tick.kp_id_riga_ors kp_id_riga_ors,
					tick.kp_tipo_doc_ticket kp_tipo_doc_ticket,
					tick.kp_numero_documento kp_numero_documento,
					tick.kp_data_documento kp_data_documento,
					tick.kp_posizione_riga kp_posizione_riga,
					tick.kp_data_inizio_pian kp_data_inizio_pian,
					tick.data_esecuzione data_esecuzione,
					tick.status status,
					tick.kp_numero_documento_ors kp_numero_documento_ors,
					tick.kp_data_documento_ors kp_data_documento_ors,
					tick.kp_posizione_riga_ors kp_posizione_riga_ors,
					tick.kp_fornitore kp_fornitore,
					tick.kp_esportato kp_esportato
					FROM {$table_prefix}_troubletickets tick
					WHERE ticketid = ".$this->id;

		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if( $num_result > 0 ){

			$this->id_testata_cal_orc = $adb->query_result($result_query, 0, 'kp_id_testata');
            $this->id_testata_cal_orc = html_entity_decode(strip_tags( $this->id_testata_cal_orc ), ENT_QUOTES, $default_charset);
			$this->id_testata_cal_orc = addslashes( $this->id_testata_cal_orc );

			$this->id_riga_cal_orc = $adb->query_result($result_query, 0, 'kp_id_riga');
            $this->id_riga_cal_orc = html_entity_decode(strip_tags( $this->id_riga_cal_orc ), ENT_QUOTES, $default_charset);
			$this->id_riga_cal_orc = addslashes( $this->id_riga_cal_orc );

			$this->id_testata_ors = $adb->query_result($result_query, 0, 'kp_id_testata_ors');
            $this->id_testata_ors = html_entity_decode(strip_tags( $this->id_testata_ors ), ENT_QUOTES, $default_charset);
			$this->id_testata_ors = addslashes( $this->id_testata_ors );

			$this->id_riga_ors = $adb->query_result($result_query, 0, 'kp_id_riga_ors');
            $this->id_riga_ors = html_entity_decode(strip_tags( $this->id_riga_ors ), ENT_QUOTES, $default_charset);
			$this->id_riga_ors = addslashes( $this->id_riga_ors );

			$this->tipo_documento_cal_orc = $adb->query_result($result_query, 0, 'kp_tipo_doc_ticket');
            $this->tipo_documento_cal_orc = html_entity_decode(strip_tags( $this->tipo_documento_cal_orc ), ENT_QUOTES, $default_charset);
			$this->tipo_documento_cal_orc = addslashes( $this->tipo_documento_cal_orc );

			$this->numero_documento_cal_orc = $adb->query_result($result_query, 0, 'kp_numero_documento');
            $this->numero_documento_cal_orc = html_entity_decode(strip_tags( $this->numero_documento_cal_orc ), ENT_QUOTES, $default_charset);
			$this->numero_documento_cal_orc = addslashes( $this->numero_documento_cal_orc );

			$this->data_documento_cal_orc = $adb->query_result($result_query, 0, 'kp_data_documento');
            $this->data_documento_cal_orc = html_entity_decode(strip_tags( $this->data_documento_cal_orc ), ENT_QUOTES, $default_charset);
			$this->data_documento_cal_orc = addslashes( $this->data_documento_cal_orc );
			if($this->data_documento_cal_orc == null || $this->data_documento_cal_orc == "0000-00-00"){
				$this->data_documento_cal_orc = "";
			}

			$this->posizione_riga_cal_orc = $adb->query_result($result_query, 0, 'kp_posizione_riga');
            $this->posizione_riga_cal_orc = html_entity_decode(strip_tags( $this->posizione_riga_cal_orc ), ENT_QUOTES, $default_charset);
			$this->posizione_riga_cal_orc = addslashes( $this->posizione_riga_cal_orc );

			$this->data_pianificata = $adb->query_result($result_query, 0, 'kp_data_inizio_pian');
            $this->data_pianificata = html_entity_decode(strip_tags( $this->data_pianificata ), ENT_QUOTES, $default_charset);
			$this->data_pianificata = addslashes( $this->data_pianificata );
			if($this->data_pianificata == null || $this->data_pianificata == "0000-00-00"){
				$this->data_pianificata = "";
			}

			$this->data_chiusura = $adb->query_result($result_query, 0, 'data_esecuzione');
            $this->data_chiusura = html_entity_decode(strip_tags( $this->data_chiusura ), ENT_QUOTES, $default_charset);
			$this->data_chiusura = addslashes( $this->data_chiusura );
			if($this->data_chiusura == null || $this->data_chiusura == "0000-00-00"){
				$this->data_chiusura = "";
			}

			$this->stato_ticket = $adb->query_result($result_query, 0, 'status');
            $this->stato_ticket = html_entity_decode(strip_tags( $this->stato_ticket ), ENT_QUOTES, $default_charset);
			$this->stato_ticket = addslashes( $this->stato_ticket );

			$this->numero_documento_ors = $adb->query_result($result_query, 0, 'kp_numero_documento_ors');
            $this->numero_documento_ors = html_entity_decode(strip_tags( $this->numero_documento_ors ), ENT_QUOTES, $default_charset);
			$this->numero_documento_ors = addslashes( $this->numero_documento_ors );

			$this->data_documento_ors = $adb->query_result($result_query, 0, 'kp_data_documento_ors');
            $this->data_documento_ors = html_entity_decode(strip_tags( $this->data_documento_ors ), ENT_QUOTES, $default_charset);
			$this->data_documento_ors = addslashes( $this->data_documento_ors );
			if($this->data_documento_ors == null || $this->data_documento_ors == "0000-00-00"){
				$this->data_documento_ors = "";
			}

			$this->posizione_riga_ors = $adb->query_result($result_query, 0, 'kp_posizione_riga_ors');
            $this->posizione_riga_ors = html_entity_decode(strip_tags( $this->posizione_riga_ors ), ENT_QUOTES, $default_charset);
			$this->posizione_riga_ors = addslashes( $this->posizione_riga_ors );

			$this->esportato = $adb->query_result($result_query, 0, 'kp_esportato');
            $this->esportato = html_entity_decode(strip_tags( $this->esportato ), ENT_QUOTES, $default_charset);
			$this->esportato = addslashes( $this->esportato );
			if( $this->esportato == "1" ){
				$this->esportato = true;
			}
			else{
				$this->esportato = false;
			}

			$this->fornitore = $adb->query_result($result_query, 0, 'kp_fornitore');
            $this->fornitore = html_entity_decode(strip_tags( $this->fornitore ), ENT_QUOTES, $default_charset);
			$this->fornitore = addslashes( $this->fornitore );
			if($this->fornitore == null || $this->fornitore == ""){
				$this->fornitore = 0;
			}

			$this->totale_ore_pianificate = $this->getTotaleOrePianificate(true);

			$this->totale_ore_eseguite = $this->getTotaleOreEseguite(true);

			$dati_inizio_pianificati = $this->getDataInizioPianificata(true);

			$dati_fine_pianificati = $this->getDataFinePianificata(true);

			$this->data_pianificata = $dati_fine_pianificati["data_fine"];

		}

	}

	private function recordAlreadyExist(){
		global $adb, $table_prefix, $current_user, $default_charset;

		/* kpro@tom01062017 */

		/**
		* @author Tomiello Marco
		* @copyright (c) 2017, Kpro Consulting Srl
		*
		* Questa funzione verifica se il record esiste già nel sistema
		*/

		$result = false;

		$query = "SELECT 
					*
					FROM import_export.sic_tickets
					WHERE codice_crm = '".$this->id."'";

		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if( $num_result > 0 ){

			$result = true;

		}

		return $result;

	}

	private function updateRecordOnImportExportDB(){
		global $adb, $table_prefix, $current_user, $default_charset;

		/* kpro@tom01062017 */

		/**
		* @author Tomiello Marco
		* @copyright (c) 2017, Kpro Consulting Srl
		*
		* Funzione per l'aggiornamento dei dati nel database di transito
		*/

		if( !$this->esportato && $this->stato_ticket == "Closed" && $this->id_testata_cal_orc != null && $this->id_testata_cal_orc != "" ){

			$data_export = date("YmdHis");

			$update = "UPDATE import_export.sic_tickets SET
						data_export = '".$data_export."',
						id_testata_cal_orc = '".$this->id_testata_cal_orc."',
						id_riga_cal_orc = '".$this->id_riga_cal_orc."',
						id_testata_ors = '".$this->id_testata_ors."',
						id_riga_ors = '".$this->id_riga_ors."',
						tipo_documento_cal_orc = '".$this->tipo_documento_cal_orc."',
						numero_documento_cal_orc = '".$this->numero_documento_cal_orc."',
						data_documento_cal_orc = '".$this->data_documento_cal_orc."',
						posizione_riga_cal_orc = '".$this->posizione_riga_cal_orc."',
						data_pianificata = '".$this->data_pianificata."',
						data_chiusura = '".$this->data_chiusura."',
						stato_ticket = '".$this->stato_ticket."',
						numero_documento_ors = '".$this->numero_documento_ors."',
						data_documento_ors = '".$this->data_documento_ors."',
						posizione_riga_ors = '".$this->posizione_riga_ors."',
						totale_ore_pianificate = ".$this->totale_ore_pianificate.",
						totale_ore_eseguite = ".$this->totale_ore_eseguite."
						WHERE codice_crm = '".$this->id."'";
			
			$adb->query($update);

			//printf(" --> Updated");

		}

	}

	private function insertRecordOnImportExportDB(){
		global $adb, $table_prefix, $current_user, $default_charset;

		/* kpro@tom01062017 */

		/**
		* @author Tomiello Marco
		* @copyright (c) 2017, Kpro Consulting Srl
		*
		* Funzione per l'inserimento dei dati nel database di transito
		*/

		if( !$this->esportato && $this->stato_ticket == "Closed" && $this->id_testata_cal_orc != null && $this->id_testata_cal_orc != "" ){

			$data_export = date("YmdHis");

			$insert = "INSERT INTO import_export.sic_tickets 
						(data_export,
						codice_crm,
						id_testata_cal_orc,
						id_riga_cal_orc,
						id_testata_ors,
						id_riga_ors,
						tipo_documento_cal_orc,
						numero_documento_cal_orc,
						data_documento_cal_orc,
						posizione_riga_cal_orc,
						data_pianificata,
						data_chiusura,
						stato_ticket,
						numero_documento_ors,
						data_documento_ors,
						posizione_riga_ors,
						totale_ore_pianificate,
						totale_ore_eseguite)
						VALUES 
						('".$data_export."',
						'".$this->id."',
						'".$this->id_testata_cal_orc."',
						'".$this->id_riga_cal_orc."',
						'".$this->id_testata_ors."',
						'".$this->id_riga_ors."',
						'".$this->tipo_documento_cal_orc."',
						'".$this->numero_documento_cal_orc."',
						'".$this->data_documento_cal_orc."',
						'".$this->posizione_riga_cal_orc."',
						'".$this->data_pianificata."',
						'".$this->data_chiusura."',
						'".$this->stato_ticket."',
						'".$this->numero_documento_ors."',
						'".$this->data_documento_ors."',
						'".$this->posizione_riga_ors."',
						".$this->totale_ore_pianificate.",
						".$this->totale_ore_eseguite.")";

			$adb->query($insert);

			//printf(" --> Inserted");

		}

	}

	private function getTotaleOrePianificate($aggiorna_ticket){
		global $adb, $table_prefix, $current_user, $default_charset;

		/* kpro@tom01062017 */

		/**
		* @author Tomiello Marco
		* @copyright (c) 2017, Kpro Consulting Srl
		*
		* Funzione per il calcolo delle ore pianificate
		*/

		$result = 0;

		$query = "SELECT 
					COALESCE(SUM(kp_durata_prevista), 0) tot_ore_pianificate
					FROM {$table_prefix}_activity act
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = act.activityid
					INNER JOIN {$table_prefix}_seactivityrel actrel ON actrel.activityid = act.activityid
					INNER JOIN {$table_prefix}_troubletickets tick ON tick.ticketid = actrel.crmid
					WHERE ent.deleted = 0  AND tick.ticketid = ".$this->id." AND tick.kp_fornitore = ".$this->fornitore;

		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if($num_result > 0){

			$tot_ore_pianificate = $adb->query_result($result_query, 0, 'tot_ore_pianificate');
			$tot_ore_pianificate = html_entity_decode(strip_tags($tot_ore_pianificate), ENT_QUOTES, $default_charset);

			$result = $tot_ore_pianificate;

		}

		if( $aggiorna_ticket ){

			$update = "UPDATE vte_troubletickets SET
						kp_ore_pianificate = ".$tot_ore_pianificate."
						WHERE ticketid = ".$this->id;
			$adb->query($update);

		}

		return $result;

	}

	private function getTotaleOreEseguite($aggiorna_ticket){
		global $adb, $table_prefix, $current_user, $default_charset;

		/* kpro@tom01062017 */

		/**
		* @author Tomiello Marco
		* @copyright (c) 2017, Kpro Consulting Srl
		*
		* Funzione per il calcolo delle ore eseguite
		*/

		$result = 0;

		$totale_minuti = 0;

		$query = "SELECT 
					tc.worktime worktime
					FROM {$table_prefix}_timecards tc
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tc.timecardsid
					WHERE ent.deleted = 0 AND tc.ticket_id = ".$this->id;

		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		for( $i = 0; $i < $num_result; $i++){

			$worktime = $adb->query_result($result_query, $i, 'worktime');
			$worktime = html_entity_decode(strip_tags($worktime), ENT_QUOTES, $default_charset);

			list($ore, $minuti) = explode(":", $worktime); 

			$ore = (int)$ore;
			$minuti = (int)$minuti;

			$totale_minuti = $totale_minuti + $minuti + ($ore * 60);

		}

		if($totale_minuti > 0){

			$result = $totale_minuti / 60;
		
		}

		if( $aggiorna_ticket ){

			$update = "UPDATE vte_troubletickets SET
						hours = ".$result."
						WHERE ticketid = ".$this->id;
			$adb->query($update);

		}

		return $result;

	}

	private function getDataInizioPianificata($aggiorna_ticket){
		global $adb, $table_prefix, $current_user, $default_charset;

		/* kpro@tom01062017 */

		/**
		* @author Tomiello Marco
		* @copyright (c) 2017, Kpro Consulting Srl
		*
		* Funzione per il calcolo della prima data pianificata
		*/

		$result = "";

		$query = "SELECT 
					act.activityid activityid,
					act.subject subject,
					act.date_start date_start,
					act.time_start time_start,
					act.due_date due_date,
					act.time_end time_end,
					act.eventstatus eventstatus,
					act.kp_durata_prevista duration_hours,
					actrel.crmid ticket
					FROM {$table_prefix}_activity act
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = act.activityid
					INNER JOIN {$table_prefix}_seactivityrel actrel ON actrel.activityid = act.activityid
					WHERE ent.deleted = 0  AND act.activitytype IN ('Intervento') AND actrel.crmid = ".$this->id."
					ORDER BY act.date_start ASC, act.time_start ASC";

		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if( $num_result > 0 ){

			$data_inizio = $adb->query_result($result_query, $i, 'date_start');
			$data_inizio = html_entity_decode(strip_tags($data_inizio), ENT_QUOTES, $default_charset);

			$ora_inizio = $adb->query_result($result_query, $i, 'time_start');
			$ora_inizio = html_entity_decode(strip_tags($ora_inizio), ENT_QUOTES, $default_charset);

		}
		else{

			$data_inizio = "";
			$ora_inizio = "";

		}

		$result = array("data_inizio" => $data_inizio,
						"ora_inizio" => $ora_inizio);

		if( $aggiorna_ticket ){

			$update = "UPDATE vte_troubletickets SET
						kp_data_inizio_pian = '".$data_inizio."',
						kp_ora_inizio_tick = '".$ora_inizio."'
						WHERE ticketid = ".$this->id;
			$adb->query($update);

		}
					
		return $result;

	}

	function getDataFinePianificata($aggiorna_ticket){
		global $adb, $table_prefix, $current_user, $default_charset;

		/* kpro@tom01062017 */

		/**
		* @author Tomiello Marco
		* @copyright (c) 2017, Kpro Consulting Srl
		*
		* Funzione per il calcolo della ultima data di fine pianificata
		*/

		$result = "";

		$query = "SELECT 
					act.activityid activityid,
					act.subject subject,
					act.date_start date_start,
					act.time_start time_start,
					act.due_date due_date,
					act.time_end time_end,
					act.eventstatus eventstatus,
					act.kp_durata_prevista duration_hours,
					actrel.crmid ticket
					FROM {$table_prefix}_activity act
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = act.activityid
					INNER JOIN {$table_prefix}_seactivityrel actrel ON actrel.activityid = act.activityid
					WHERE ent.deleted = 0  AND act.activitytype IN ('Intervento') AND actrel.crmid = ".$this->id."
					ORDER BY act.due_date DESC, act.time_end DESC";

		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if( $num_result > 0 ){

			$data_fine = $adb->query_result($result_query, $i, 'due_date');
			$data_fine = html_entity_decode(strip_tags($data_fine), ENT_QUOTES, $default_charset);

			$ora_fine = $adb->query_result($result_query, $i, 'time_end');
			$ora_fine = html_entity_decode(strip_tags($ora_fine), ENT_QUOTES, $default_charset);

		}
		else{

			$data_fine = "";
			$ora_fine = "";

		}

		$result = array("data_fine" => $data_fine,
						"ora_fine" => $ora_fine);

		if( $aggiorna_ticket ){

			$update = "UPDATE vte_troubletickets SET
						kp_data_fine_pian = '".$data_fine."',
						kp_ora_fine_tick = '".$ora_fine."'
						WHERE ticketid = ".$this->id;
			$adb->query($update);

		}
					
		return $result;

	}

}

?>