<?php

/* kpro@tom20092016 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 */

global $default_charset, $current_user, $adb, $table_prefix;

class KpManutenzioni_class{
	
    private $id = 0;

    public $assegnatario = 1;
    public $description = "";
    
    public $manutenzione_name = "Manutenzione priva di nome";
    public $data_manutenzione = "";
    public $data_scad_manut = "";
    public $stato_manutenzione = "Creata";
    public $tipo_manutenzione = "Programmata";
    public $numero_generazione = 0;
    public $tempo_previsto = 0;
    public $ora_inizio = "08:00";
    public $ora_fine = "18:00";
	public $lavoro_caldo = '0';
	public $lavoro_quota = '0';
	public $lavoro_spaz_co = '0';
	public $tipologia_inter = "--Nessuno--";
	public $rinnovo_automati = '0';
    public $problema_di_sicurezza = '--Nessuno--';
    
    public function recuperaInformazioniElemento($id){
        global $adb, $table_prefix, $current_user;

        $q = "SELECT 
                man.manutenzioniid manutenzioniid,
                man.manutenzione_name manutenzione_name,
                man.data_manutenzione data_manutenzione,
                man.data_scad_manut data_scad_manut,
                man.stato_manutenzione stato_manutenzione,
                man.tipo_manutenzione tipo_manutenzione,
                man.numero_generazione numero_generazione,
                man.tempo_previsto tempo_previsto,
                man.tempo_effettivo tempo_effettivo,
                man.ora_inizio ora_inizio,
                man.ora_fine ora_fine,
                man.kp_tecnici_inc kp_tecnici_inc,
                man.kp_data_inizio_ef kp_data_inizio_ef,
                man.kp_data_fine_ef kp_data_fine_ef,
                man.kp_tipologia_guasto kp_tipologia_guasto,
                man.kp_tipologia_inter kp_tipologia_inter,
                man.kp_ricambo kp_ricambo,
                man.kp_causale_manutenz kp_causale_manutenz,
                man.kp_data_fine_prev kp_data_fine_prev,
				man.kp_lavoro_caldo kp_lavoro_caldo,
				man.kp_lavoro_quota kp_lavoro_quota,
				man.kp_lavoro_spaz_co kp_lavoro_spaz_co,
				man.kp_rinnovo_automati kp_rinnovo_automati,
                man.kp_problema_di_sic kp_problema_di_sic,
                man.description description,
                ent.smownerid smownerid
                FROM {$table_prefix}_manutenzioni man
                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = man.manutenzioniid
                WHERE ent.deleted = 0 AND man.manutenzioniid = ".$id;
        $res = $adb->query($q);
        if($adb->num_rows($res) > 0){
            $manutenzioniid = $adb->query_result($res, 0, 'manutenzioniid');
            $manutenzioniid = html_entity_decode(strip_tags($manutenzioniid), ENT_QUOTES, $default_charset);
            $this->id = $manutenzioniid;

            $manutenzione_name = $adb->query_result($res, 0, 'manutenzione_name');
            $manutenzione_name = html_entity_decode(strip_tags($manutenzione_name), ENT_QUOTES, $default_charset);
            $this->manutenzione_name = $manutenzione_name;

            $data_manutenzione = $adb->query_result($res, 0, 'data_manutenzione');
            $data_manutenzione = html_entity_decode(strip_tags($data_manutenzione), ENT_QUOTES, $default_charset);
            if($data_manutenzione == null){
                $data_manutenzione = "";
            }
            $this->data_manutenzione = $data_manutenzione;

            $data_scad_manut = $adb->query_result($res, 0, 'data_scad_manut');
            $data_scad_manut = html_entity_decode(strip_tags($data_scad_manut), ENT_QUOTES, $default_charset);
            if($data_scad_manut == null){
                $data_scad_manut = "";
            }
            $this->data_scad_manut = $data_scad_manut;

            $stato_manutenzione = $adb->query_result($res, 0, 'stato_manutenzione');
            $stato_manutenzione = html_entity_decode(strip_tags($stato_manutenzione), ENT_QUOTES, $default_charset);
            $this->stato_manutenzione = $stato_manutenzione;

            $tipo_manutenzione = $adb->query_result($res, 0, 'tipo_manutenzione');
            $tipo_manutenzione = html_entity_decode(strip_tags($tipo_manutenzione), ENT_QUOTES, $default_charset);
            $this->tipo_manutenzione = $tipo_manutenzione;

            $numero_generazione = $adb->query_result($res, 0, 'numero_generazione');
            $numero_generazione = html_entity_decode(strip_tags($numero_generazione), ENT_QUOTES, $default_charset);
            $this->numero_generazione = $numero_generazione;

            $ora_inizio = $adb->query_result($res, 0, 'ora_inizio');
            $ora_inizio = html_entity_decode(strip_tags($ora_inizio), ENT_QUOTES, $default_charset);
            $this->ora_inizio = $ora_inizio;

            $ora_fine = $adb->query_result($res, 0, 'ora_fine');
            $ora_fine = html_entity_decode(strip_tags($ora_fine), ENT_QUOTES, $default_charset);
            $this->ora_fine = $ora_fine;

            $description = $adb->query_result($res, 0, 'description');
            $description = html_entity_decode(strip_tags($description), ENT_QUOTES, $default_charset);
            $this->description = $description;

            $assegnatario = $adb->query_result($res, 0, 'smownerid');
            $assegnatario = html_entity_decode(strip_tags($assegnatario), ENT_QUOTES, $default_charset);
            $this->assegnatario = $assegnatario;
			
			$tipologia_inter = $adb->query_result($res, 0, 'kp_tipologia_inter');
            $tipologia_inter = html_entity_decode(strip_tags($tipologia_inter), ENT_QUOTES, $default_charset);
            $this->tipologia_inter = $tipologia_inter;
			
			$lavoro_caldo = $adb->query_result($res, 0, 'kp_lavoro_caldo');
            $lavoro_caldo = html_entity_decode(strip_tags($lavoro_caldo), ENT_QUOTES, $default_charset);
            $this->lavoro_caldo = $lavoro_caldo;
			
			$lavoro_quota = $adb->query_result($res, 0, 'kp_lavoro_quota');
            $lavoro_quota = html_entity_decode(strip_tags($lavoro_quota), ENT_QUOTES, $default_charset);
            $this->lavoro_quota = $lavoro_quota;
			
			$lavoro_spaz_co = $adb->query_result($res, 0, 'kp_lavoro_spaz_co');
            $lavoro_spaz_co = html_entity_decode(strip_tags($lavoro_spaz_co), ENT_QUOTES, $default_charset);
            $this->lavoro_spaz_co = $lavoro_spaz_co;
			
			$rinnovo_automati = $adb->query_result($res, 0, 'kp_rinnovo_automati');
            $rinnovo_automati = html_entity_decode(strip_tags($rinnovo_automati), ENT_QUOTES, $default_charset);
            $this->rinnovo_automati = $rinnovo_automati;

            $problema_di_sicurezza = $adb->query_result($res, 0, 'kp_problema_di_sic');
            $problema_di_sicurezza = html_entity_decode(strip_tags($problema_di_sicurezza), ENT_QUOTES, $default_charset);
            $this->problema_di_sicurezza = $problema_di_sicurezza;

        }
        else{

            $this->id = 0;

        }

        return $this->id;

    }

    public function verificaEsistenza(){
        global $adb, $table_prefix, $current_user;

        $q = "SELECT 
                man.manutenzioniid manutenzioniid
                FROM {$table_prefix}_manutenzioni man
                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = man.manutenzioniid
                WHERE ent.deleted = 0 AND man.stato_manutenzione = 'Creata' AND man.numero_generazione = ".$this->numero_generazione;
        $res = $adb->query($q);
        if($adb->num_rows($res) > 0){

            $manutenzioniid = $adb->query_result($res, 0, 'manutenzioniid');
            $manutenzioniid = html_entity_decode(strip_tags($manutenzioniid), ENT_QUOTES, $default_charset);
            $this->id = $manutenzioniid;

        }
        else{

            $this->id = 0;

        }

        return $this->id;

    }

    public function aggiornaRigheManutenzione(){
        global $adb, $table_prefix, $current_user;

        require_once('modules/SproCore/KpClass/KpRigheManutenzioni_class.php');
        
        if($this->id != null && $this->id != "" && $this->id !=0){

            $q_righe_manutenzioni = "SELECT 
                                        righe.kprighemanutenzioniid kprighemanutenzioniid
                                        FROM {$table_prefix}_kprighemanutenzioni righe
                                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = righe.kprighemanutenzioniid
                                        WHERE ent.deleted = 0 AND righe.kp_manutenzione = ".$this->id;
            $res_righe_manutenzioni = $adb->query($q_righe_manutenzioni);
            $num_righe_manutenzioni = $adb->num_rows($res_righe_manutenzioni);
           
            for($i = 0; $i < $num_righe_manutenzioni; $i++){
                $kprighemanutenzioniid = $adb->query_result($res_righe_manutenzioni, $i, 'kprighemanutenzioniid');
                $kprighemanutenzioniid = html_entity_decode(strip_tags($kprighemanutenzioniid), ENT_QUOTES, $default_charset);

                $riga_manutenzione = new KpRigheManutenzioni_class();
                $riga_manutenzione->recuperaInformazioniElemento($kprighemanutenzioniid);
                $riga_manutenzione->generaEsiti();
                unset($riga_manutenzione);

            }

        }

    }

    public function calcolaTempoPrevistoManutenzione(){
         global $adb, $table_prefix, $current_user;

         $this->tempo_previsto = 0;

         if($this->id != null && $this->id != "" && $this->id !=0){

            $q_righe_manutenzioni = "SELECT 
                                        righe.kprighemanutenzioniid kprighemanutenzioniid,
                                        checkl.tempo_previsto tempo_previsto
                                        FROM {$table_prefix}_kprighemanutenzioni righe
                                        INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = righe.kprighemanutenzioniid
                                        INNER JOIN {$table_prefix}_checklists checkl ON checkl.checklistsid = righe.kp_check_list
                                        WHERE ent.deleted = 0 AND righe.kp_manutenzione = ".$this->id;
            $res_righe_manutenzioni = $adb->query($q_righe_manutenzioni);
            $num_righe_manutenzioni = $adb->num_rows($res_righe_manutenzioni);
           
            for($i = 0; $i < $num_righe_manutenzioni; $i++){

                $kprighemanutenzioniid = $adb->query_result($res_righe_manutenzioni, $i, 'kprighemanutenzioniid');
                $kprighemanutenzioniid = html_entity_decode(strip_tags($kprighemanutenzioniid), ENT_QUOTES, $default_charset);

                $tempo_previsto_check = $adb->query_result($res_righe_manutenzioni, $i, 'tempo_previsto');
                $tempo_previsto_check = html_entity_decode(strip_tags($tempo_previsto_check), ENT_QUOTES, $default_charset);
                if($tempo_previsto_check == null || $tempo_previsto_check == ""){
                    $tempo_previsto_check = 0;
                }
                $tempo_previsto_check= $tempo_previsto_check / 60;	//Ho il tempo in ore

                $this->tempo_previsto = $this->tempo_previsto + $tempo_previsto_check;

            }

        }

        return $this->tempo_previsto;

    }

    public function aggiornaTempoPrevistoManutenzione(){
         global $adb, $table_prefix, $current_user;

         if($this->id != null && $this->id != "" && $this->id !=0){

            $this->calcolaTempoPrevistoManutenzione();

            $upd = "UPDATE {$table_prefix}_manutenzioni SET
                    tempo_previsto = ".$this->tempo_previsto."
                    WHERE manutenzioniid = ".$this->id;
            $adb->query($upd);

         }

    }

    public function salva(){
        global $adb, $table_prefix, $current_user;

        $this->verificaEsistenza();
        
        if($this->id == 0){
            
            $nuovo_elemento = CRMEntity::getInstance('Manutenzioni'); 
            $nuovo_elemento->column_fields['assigned_user_id'] = $this->assegnatario;
            $nuovo_elemento->column_fields['manutenzione_name'] = $this->manutenzione_name;
            $nuovo_elemento->column_fields['data_manutenzione'] = $this->data_manutenzione;
            $nuovo_elemento->column_fields['data_scad_manut'] = $this->data_scad_manut;
            $nuovo_elemento->column_fields['stato_manutenzione'] = $this->stato_manutenzione;
            $nuovo_elemento->column_fields['tipo_manutenzione'] = $this->tipo_manutenzione;
            $nuovo_elemento->column_fields['numero_generazione'] = $this->numero_generazione;
            $nuovo_elemento->column_fields['ora_inizio'] = $this->ora_inizio;
            $nuovo_elemento->column_fields['ora_fine'] = $this->ora_fine;
			$nuovo_elemento->column_fields['kp_tipologia_inter'] = $this->tipologia_inter;
			$nuovo_elemento->column_fields['kp_lavoro_caldo'] = $this->lavoro_caldo;
			$nuovo_elemento->column_fields['kp_lavoro_spaz_co'] = $this->lavoro_spaz_co;
			$nuovo_elemento->column_fields['kp_lavoro_quota'] = $this->lavoro_quota;
			$nuovo_elemento->column_fields['kp_rinnovo_automati'] = $this->rinnovo_automati;
            $nuovo_elemento->column_fields['kp_problema_di_sic'] = $this->problema_di_sicurezza;
            if($this->description != ""){
                $nuovo_elemento->column_fields['description'] = $this->description;
            }
            $nuovo_elemento->save('Manutenzioni', $longdesc=true, $offline_update=false, $triggerEvent=false); 
            
            $this->id = $nuovo_elemento->id;

            unset($nuovo_elemento);
        
        }
        else{

            $this->description = addslashes($this->description);
            
            $this->manutenzione_name = addslashes($this->manutenzione_name);
            $this->data_manutenzione = addslashes($this->data_manutenzione);
            $this->data_scad_manut = addslashes($this->data_scad_manut);
            $this->stato_manutenzione = addslashes($this->stato_manutenzione);
            $this->tipo_manutenzione = addslashes($this->tipo_manutenzione);
            $this->numero_generazione = addslashes($this->numero_generazione);
            $this->ora_inizio = addslashes($this->ora_inizio);
            $this->tipologia_inter = addslashes($this->tipologia_inter);
			$this->lavoro_caldo = addslashes($this->lavoro_caldo);
			$this->lavoro_spaz_co = addslashes($this->lavoro_spaz_co);
			$this->lavoro_quota = addslashes($this->lavoro_quota);
			$this->rinnovo_automati = addslashes($this->rinnovo_automati);
            $this->problema_di_sicurezza = addslashes($this->problema_di_sicurezza);

            $upd = "UPDATE {$table_prefix}_manutenzioni SET
                    manutenzione_name = '".$this->manutenzione_name."',
                    data_manutenzione = '".$this->data_manutenzione."',
                    data_scad_manut = '".$this->data_scad_manut."',
                    stato_manutenzione = '".$this->stato_manutenzione."',
                    tipo_manutenzione = '".$this->tipo_manutenzione."',
                    numero_generazione = ".$this->numero_generazione.",
                    ora_inizio = '".$this->ora_inizio."',
                    ora_fine = '".$this->ora_fine."',
					kp_tipologia_inter = '".$this->tipologia_inter."',
					kp_lavoro_caldo = '".$this->lavoro_caldo."',
					kp_lavoro_spaz_co = '".$this->lavoro_spaz_co."',
					kp_lavoro_quota = '".$this->lavoro_quota."',
					kp_rinnovo_automati = '".$this->rinnovo_automati."',
                    kp_problema_di_sic = '".$this->problema_di_sicurezza."'
                    WHERE manutenzioniid = ".$this->id;
            $adb->query($upd);

        }

        return $this->id;

    }
	
}


	
