<?php

/* kpro@tom20092016 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 */

global $default_charset, $current_user, $adb, $table_prefix;

class KpRigheManutenzioni_class{
	
    private $id = 0;

    public $assegnatario = 1;
    public $description = "";

    public $data_scadenza = "";
    public $componente = 0;
    public $check_list = 0;
    public $manutenzione = 0;
    public $frequenza_checklist = "";
    public $nome_riga_man = "Riga Manutenzione priva di nome";
    
    public function recuperaInformazioniElemento($id){
        global $adb, $table_prefix, $current_user;

        $q = "SELECT 
                righeman.kprighemanutenzioniid kprighemanutenzioniid,
                righeman.kp_nome_riga_man kp_nome_riga_man,
                righeman.kp_componente kp_componente,
                righeman.kp_check_list kp_check_list,
                righeman.frequenza_checklist frequenza_checklist,
                righeman.kp_data_scadenza kp_data_scadenza,
                righeman.kp_manutenzione kp_manutenzione,
                righeman.description description,
                ent.smownerid smownerid
                FROM {$table_prefix}_kprighemanutenzioni righeman
                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = righeman.kprighemanutenzioniid
                WHERE ent.deleted = 0 AND righeman.kprighemanutenzioniid = ".$id;
        $res = $adb->query($q);
        if($adb->num_rows($res) > 0){

            $righemanutenzioniid = $adb->query_result($res, 0, 'kprighemanutenzioniid');
            $righemanutenzioniid = html_entity_decode(strip_tags($righemanutenzioniid), ENT_QUOTES, $default_charset);
            $this->id = $righemanutenzioniid;

            $nome_riga_man = $adb->query_result($res, 0, 'kp_nome_riga_man');
            $nome_riga_man = html_entity_decode(strip_tags($nome_riga_man), ENT_QUOTES, $default_charset);
            $this->nome_riga_man = $nome_riga_man;

            $componente = $adb->query_result($res, 0, 'kp_componente');
            $componente = html_entity_decode(strip_tags($componente), ENT_QUOTES, $default_charset);
            if($componente == null || $componente == ""){
                $componente = 0;
            }
            $this->componente = $componente;

            $check_list = $adb->query_result($res, 0, 'kp_check_list');
            $check_list = html_entity_decode(strip_tags($check_list), ENT_QUOTES, $default_charset);
            if($check_list == null || $check_list == ""){
                $check_list = 0;
            }
            $this->check_list = $check_list;

            $manutenzione = $adb->query_result($res, 0, 'kp_manutenzione');
            $manutenzione = html_entity_decode(strip_tags($manutenzione), ENT_QUOTES, $default_charset);
            if($manutenzione == null || $manutenzione == ""){
                $manutenzione = 0;
            }
            $this->manutenzione = $manutenzione;

            $frequenza_checklist = $adb->query_result($res, 0, 'frequenza_checklist');
            $frequenza_checklist = html_entity_decode(strip_tags($frequenza_checklist), ENT_QUOTES, $default_charset);
            $this->frequenza_checklist = $frequenza_checklist;

            $data_scadenza = $adb->query_result($res, 0, 'kp_data_scadenza');
            $data_scadenza = html_entity_decode(strip_tags($data_scadenza), ENT_QUOTES, $default_charset);
            if($data_scadenza == null){
                $data_scadenza = "";
            }
            $this->data_scadenza = $data_scadenza;

            $description = $adb->query_result($res, 0, 'description');
            $description = html_entity_decode(strip_tags($description), ENT_QUOTES, $default_charset);
            $this->description = $description;

            $assegnatario = $adb->query_result($res, 0, 'smownerid');
            $assegnatario = html_entity_decode(strip_tags($assegnatario), ENT_QUOTES, $default_charset);
            $this->assegnatario = $assegnatario;

        }
        else{

            $this->id = 0;

        }

        return $this->id;

    }

    public function verificaEsistenza(){
        global $adb, $table_prefix, $current_user;

        $q = "SELECT 
                righeman.kprighemanutenzioniid kprighemanutenzioniid
                FROM {$table_prefix}_kprighemanutenzioni righeman
                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = righeman.kprighemanutenzioniid
                WHERE ent.deleted = 0 AND righeman.kp_componente = ".$this->componente." AND righeman.kp_check_list = ".$this->check_list." AND righeman.kp_manutenzione = ".$this->manutenzione;
        $res = $adb->query($q);
        if($adb->num_rows($res) > 0){

            $righemanutenzioniid = $adb->query_result($res, 0, 'kprighemanutenzioniid');
            $righemanutenzioniid = html_entity_decode(strip_tags($righemanutenzioniid), ENT_QUOTES, $default_charset);
            $this->id = $righemanutenzioniid;

        }
        else{

            $this->id = 0;

        }

        return $this->id;

    }

    public function generaNome(){
        global $adb, $table_prefix, $current_user;

        $q = "SELECT 
                comp.nome_componente nome_componente
                FROM {$table_prefix}_compimpianto comp
                WHERE comp.compimpiantoid = ".$this->componente;
        $res = $adb->query($q);
        if($adb->num_rows($res) > 0){

            $nome_componente = $adb->query_result($res, 0, 'nome_componente');
            $nome_componente = html_entity_decode(strip_tags($nome_componente), ENT_QUOTES, $default_charset);

        }
        else{

            $nome_componente = "Manca il componente";

        }

        $q = "SELECT 
                checkl.nome_check_list nome_check_list
                FROM {$table_prefix}_checklists checkl
                WHERE checkl.checklistsid = ".$this->check_list;
        $res = $adb->query($q);
        if($adb->num_rows($res) > 0){

            $nome_check_list = $adb->query_result($res, 0, 'nome_check_list');
            $nome_check_list = html_entity_decode(strip_tags($nome_check_list), ENT_QUOTES, $default_charset);

        }
        else{

            $nome_check_list = "Manca la check list";

        }

        $this->nome_riga_man = $nome_componente." - ".$nome_check_list;
		
		$this->nome_riga_man = substr($this->nome_riga_man, 0, 100);

        return $this->nome_riga_man;

    }

    public function calcolaDataDiScadenza(){
        global $adb, $table_prefix, $current_user;

        require_once('modules/SproCore/KpClass/KpManutenzioni_class.php');
        
        if($this->frequenza_checklist == null || $this->frequenza_checklist == ""){

            $frequenza_checklist_temp = 1;

        }
        
        $frequenza_checklist_temp = (int) $frequenza_checklist_temp;

        $manutenzione = new KpManutenzioni_class();
        $manutenzione->recuperaInformazioniElemento($this->manutenzione);
        $data_manutenzione = $manutenzione->data_manutenzione;

        if(($data_manutenzione != null && $data_manutenzione != "" && $data_manutenzione != "0000-00-00") && ($this->data_scadenza == null || $this->data_scadenza == "" || $this->data_scadenza == "0000-00-00")){

            list($anno, $mese, $giorno) = explode("-", $data_manutenzione);
            $this->data_scadenza = date("Y-m-d",mktime(0, 0, 0, $mese + $frequenza_checklist, $giorno, $anno));

        }

        return $this->data_scadenza;

    }

    public function aggiornaDataDiScadenza(){
        global $adb, $table_prefix, $current_user;

        calcolaDataDiScadenza();

        if($this->data_scadenza != null && $this->data_scadenza != "" && $this->data_scadenza != "0000-00-00"){
            
            $upd = "UPDATE {$table_prefix}_kprighemanutenzioni SET
                        kp_data_scadenza = '".$this->data_scadenza."'
                        WHERE kprighemanutenzioniid = ".$this->id;
            $adb->query($upd);

        }

    }

    public function generaEsiti(){
        global $adb, $table_prefix, $current_user;

        require_once('modules/SproCore/KpClass/KpEsitiManutenzioni_class.php');

        if($this->check_list != null && $this->check_list != "" && $this->check_list !=0){

            //Vado a verifica i Tipi Verifica relazionati alla check list e creo una situazione check list per ognuno di essi
            $q_tipi_verifica = "(SELECT entrel.crmid tipo_verifica FROM {$table_prefix}_crmentityrel entrel
                                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = entrel.crmid
                                WHERE ent.deleted = 0 AND entrel.module = 'TipiVerifiche' AND entrel.relmodule = 'CheckLists' AND entrel.relcrmid = ".$this->check_list.")
                                UNION
                                (SELECT entrel2.relcrmid tipo_verifica FROM {$table_prefix}_crmentityrel entrel2
                                INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = entrel2.relcrmid
                                WHERE ent2.deleted = 0 AND entrel2.relmodule = 'TipiVerifiche' AND entrel2.module = 'CheckLists' AND entrel2.crmid = ".$this->check_list.")";
                                    
            $res_tipi_verifica = $adb->query($q_tipi_verifica);
            $num_tipi_verifica = $adb->num_rows($res_tipi_verifica);
            
            for($i = 0; $i < $num_tipi_verifica; $i++){
                $tipo_verifica = $adb->query_result($res_tipi_verifica, $i, 'tipo_verifica');
                $tipo_verifica = html_entity_decode(strip_tags($tipo_verifica), ENT_QUOTES, $default_charset);

                $nuovo_esito = new KpEsitiManutenzioni_class();
                $nuovo_esito->manutenzione = $this->manutenzione;
                $nuovo_esito->componente = $this->componente;
                $nuovo_esito->tipo_verifica = $tipo_verifica;
                $nuovo_esito->riga_manutenz = $this->id;
                $nuovo_esito->salva();
                unset($nuovo_esito);

            }

        }

    }

    public function salva(){
        global $adb, $table_prefix, $current_user;

        $this->verificaEsistenza();

        $this->generaNome();
		
		if($this->verificaCampiObbbligatori() == 1){
        
			if($this->id == 0){
				
				$nuovo_elemento = CRMEntity::getInstance('KpRigheManutenzioni'); 
				$nuovo_elemento->column_fields['assigned_user_id'] = $this->assegnatario;
				$nuovo_elemento->column_fields['kp_nome_riga_man'] = $this->nome_riga_man;
				if($this->componente != null && $this->componente != "" && $this->componente != 0){
					$nuovo_elemento->column_fields['kp_componente'] = $this->componente;
				}
				/*if($this->check_list != null && $this->check_list != "" && $this->check_list != 0){
					$nuovo_elemento->column_fields['kp_check_list'] = $this->check_list;
				}*/
				if($this->manutenzione != null && $this->manutenzione != "" && $this->manutenzione != 0){
					$nuovo_elemento->column_fields['kp_manutenzione'] = $this->manutenzione;
				}
				if($this->frequenza_checklist != null && $this->frequenza_checklist != ""){
					$nuovo_elemento->column_fields['frequenza_checklist'] = $this->frequenza_checklist;
				}
				if($this->data_scadenza != null && $this->data_scadenza != ""){
					$nuovo_elemento->column_fields['kp_data_scadenza'] = $this->data_scadenza;
				}
				if($this->description != null && $this->description != ""){
					$nuovo_elemento->column_fields['description'] = $this->description;
				}
				$nuovo_elemento->save('KpRigheManutenzioni', $longdesc=true, $offline_update=false, $triggerEvent=false);
			
				$this->id = $nuovo_elemento->id;
				
				$upd = "UPDATE {$table_prefix}_kprighemanutenzioni SET
						kp_check_list = ".$this->check_list."
						WHERE kprighemanutenzioniid = ".$this->id;
				$adb->query($upd);

				unset($nuovo_elemento);
			
			}
			else{

				$this->description = addslashes($this->description);
				
				$this->nome_riga_man = addslashes($this->nome_riga_man);
				$this->data_scadenza = addslashes($this->data_scadenza);
				$this->componente = addslashes($this->componente);
				$this->check_list = addslashes($this->check_list);
				$this->manutenzione = addslashes($this->manutenzione);
				$this->frequenza_checklist = addslashes($this->frequenza_checklist);

				$upd = "UPDATE {$table_prefix}_kprighemanutenzioni SET
						kp_nome_riga_man = '".$this->nome_riga_man."',
						kp_componente = ".$this->componente.",
						kp_check_list = ".$this->check_list.",
						kp_manutenzione = ".$this->manutenzione.",
						frequenza_checklist = '".$this->frequenza_checklist."',
						kp_data_scadenza = '".$this->data_scadenza."'
						WHERE kprighemanutenzioniid = ".$this->id;
				$adb->query($upd);

			}
			
		}

        return $this->id;

    }
	
	public function verificaCampiObbbligatori(){
		
		if($this->nome_riga_man == null || $this->nome_riga_man == ""){
			return 0;
		}
		elseif($this->assegnatario == null || $this->assegnatario == "" || $this->assegnatario == 0){
			return 0;
		}
		elseif($this->componente == null || $this->componente == "" || $this->componente == 0){
			return 0;
		}
		elseif($this->check_list == null || $this->check_list == "" || $this->check_list == 0){
			return 0;
		}
		elseif($this->manutenzione == null || $this->manutenzione == "" || $this->manutenzione == 0){
			return 0;
		}
		else{
			return 1;
		}
		
	}
	
}


	
