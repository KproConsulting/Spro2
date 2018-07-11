<?php

/* kpro@tom20092016 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 */

global $default_charset, $current_user, $adb, $table_prefix;

class KpEsitiManutenzioni_class{
	
    private $id = 0;

    public $assegnatario = 1;
    public $description = "";

    public $esito_manutenzione_no = "";
    public $manutenzione = 0;
    public $componente = 0;
    public $tipo_verifica = 0;
    public $esito_manutenzione = "N.D.";
    public $riga_manutenz = 0;
    public $note_esito = "";
    
    public function recuperaInformazioniElemento($id){
        global $adb, $table_prefix, $current_user;

        $q = "SELECT 
                esiti.esitimanutenzioniid esitimanutenzioniid,
                esiti.esito_manutenzione_no esito_manutenzione_no,
                esiti.manutenzione manutenzione,
                esiti.componente componente,
                esiti.tipo_verifica tipo_verifica,
                esiti.esito_manutenzione esito_manutenzione,
                esiti.note_esito note_esito,
                esiti.kp_riga_manutenz kp_riga_manutenz,
                esiti.description description,
                ent.smownerid smownerid
                FROM {$table_prefix}_esitimanutenzioni esiti
                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = esiti.esitimanutenzioniid
                WHERE ent.deleted = 0 AND esiti.esitimanutenzioniid = ".$id;
        $res = $adb->query($q);
        if($adb->num_rows($res) > 0){

            $esitimanutenzioniid = $adb->query_result($res, 0, 'esitimanutenzioniid');
            $esitimanutenzioniid = html_entity_decode(strip_tags($esitimanutenzioniid), ENT_QUOTES, $default_charset);
            $this->id = $esitimanutenzioniid;

            $esito_manutenzione_no = $adb->query_result($res, 0, 'esito_manutenzione_no');
            $esito_manutenzione_no = html_entity_decode(strip_tags($esito_manutenzione_no), ENT_QUOTES, $default_charset);
            $this->esito_manutenzione_no = $esito_manutenzione_no;

            $manutenzione = $adb->query_result($res, 0, 'manutenzione');
            $manutenzione = html_entity_decode(strip_tags($manutenzione), ENT_QUOTES, $default_charset);
            if($manutenzione == null || $manutenzione == ""){
                $manutenzione = 0;
            }
            $this->manutenzione = $manutenzione;

            $componente = $adb->query_result($res, 0, 'componente');
            $componente = html_entity_decode(strip_tags($componente), ENT_QUOTES, $default_charset);
            if($componente == null || $componente == ""){
                $componente = 0;
            }
            $this->componente = $componente;

            $tipo_verifica = $adb->query_result($res, 0, 'tipo_verifica');
            $tipo_verifica = html_entity_decode(strip_tags($tipo_verifica), ENT_QUOTES, $default_charset);
            if($tipo_verifica == null || $tipo_verifica == ""){
                $tipo_verifica = 0;
            }
            $this->tipo_verifica = $tipo_verifica;

            $esito_manutenzione = $adb->query_result($res, 0, 'esito_manutenzione');
            $esito_manutenzione = html_entity_decode(strip_tags($esito_manutenzione), ENT_QUOTES, $default_charset);
            $this->esito_manutenzione = $esito_manutenzione;

            $riga_manutenz = $adb->query_result($res, 0, 'kp_riga_manutenz');
            $riga_manutenz = html_entity_decode(strip_tags($riga_manutenz), ENT_QUOTES, $default_charset);
            if($riga_manutenz == null || $riga_manutenz == ""){
                $riga_manutenz = 0;
            }
            $this->riga_manutenz = $riga_manutenz;

            $note_esito = $adb->query_result($res, 0, 'note_esito');
            $note_esito = html_entity_decode(strip_tags($note_esito), ENT_QUOTES, $default_charset);
            $this->note_esito = $note_esito;

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
                esiti.esitimanutenzioniid esitimanutenzioniid
                FROM {$table_prefix}_esitimanutenzioni esiti
                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = esiti.esitimanutenzioniid
                WHERE ent.deleted = 0 AND esiti.componente = ".$this->componente." AND esiti.tipo_verifica = ".$this->tipo_verifica." AND esiti.kp_riga_manutenz = ".$this->riga_manutenz;
        $res = $adb->query($q);
        if($adb->num_rows($res) > 0){

            $esitimanutenzioniid = $adb->query_result($res, 0, 'esitimanutenzioniid');
            $esitimanutenzioniid = html_entity_decode(strip_tags($esitimanutenzioniid), ENT_QUOTES, $default_charset);
            $this->id = $esitimanutenzioniid;

        }
        else{

            $this->id = 0;

        }

        return $this->id;

    }

    public function salva(){
        global $adb, $table_prefix, $current_user;

        $this->verificaEsistenza();
        
        if($this->id == 0){
            
            $nuovo_elemento = CRMEntity::getInstance('EsitiManutenzioni'); 
            $nuovo_elemento->column_fields['assigned_user_id'] = $this->assegnatario;
            $nuovo_elemento->column_fields['manutenzione'] = $this->manutenzione;
            $nuovo_elemento->column_fields['componente'] = $this->componente;
            $nuovo_elemento->column_fields['tipo_verifica'] = $this->tipo_verifica;
            $nuovo_elemento->column_fields['esito_manutenzione'] = $this->esito_manutenzione;
            $nuovo_elemento->column_fields['kp_riga_manutenz'] = $this->riga_manutenz;
            $nuovo_elemento->column_fields['note_esito'] = $this->note_esito;
            if($this->description != ""){
                $nuovo_elemento->column_fields['description'] = $this->description;
            }
            $nuovo_elemento->save('EsitiManutenzioni', $longdesc=true, $offline_update=false, $triggerEvent=false); 

            $this->id = $nuovo_elemento->id;

            unset($nuovo_elemento);
        
        }
        else{
            
            $this->description = addslashes($this->description);

            $this->manutenzione = addslashes($this->manutenzione);
            $this->componente = addslashes($this->componente);
            $this->tipo_verifica = addslashes($this->tipo_verifica);
            $this->esito_manutenzione = addslashes($this->esito_manutenzione);
            $this->riga_manutenz = addslashes($this->riga_manutenz);
            $this->note_esito = addslashes($this->note_esito);

            $upd = "UPDATE {$table_prefix}_esitimanutenzioni SET
                    manutenzione = ".$this->manutenzione.",
                    componente = ".$this->componente.",
                    tipo_verifica = ".$this->tipo_verifica.",
                    esito_manutenzione = '".$this->esito_manutenzione."',
                    kp_riga_manutenz = ".$this->riga_manutenz.",
                    note_esito = '".$this->note_esito."'
                    WHERE esitimanutenzioniid = ".$this->id;
            $adb->query($upd);

        }

        return $this->id;

    }
	
}


	
