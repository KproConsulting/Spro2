<?php 

/* kpro@20171227160924 */ 

/** 
 * @copyright (c) 2017, Kpro Consulting Srl 
 * 
 * Estensione classe Commesse 
 */ 

require_once('modules/Commesse/Commesse.php'); 

class CommesseKp extends Commesse { 

    //Script modifica Related List
	var $list_fields = Array();

	var $list_fields_name = Array(
        'Numero Commessa'=>'kp_numero_commessa',
		'Nome Commessa'=>'commessa_name',
        'Azienda'=>'account',
        'Data Inizio Commessa'=>'data_inizio',
		'Tipo Commessa'=>'tipo_commessa',
		'Stato Commessa'=>'stato_commessa',
		'Totale Fatturato'=>'kp_tot_fatturato'  
	);

	function CommesseKp(){
		global $table_prefix;
		parent::__construct();
		$this->list_fields = Array(
            'Numero Commessa'=>Array($table_prefix.'_commesse'=>'kp_numero_commessa'),
			'Nome Commessa'=>Array($table_prefix.'_commesse'=>'commessa_name'),
            'Azienda'=>Array($table_prefix.'_commesse'=>'account'),
            'Data Inizio Commessa'=>Array($table_prefix.'_commesse'=>'data_inizio'), 
			'Tipo Commessa'=>Array($table_prefix.'_commesse'=>'tipo_commessa'), 
			'Stato Commessa'=>Array($table_prefix.'_commesse'=>'stato_commessa'),
			'Totale Fatturato'=>Array($table_prefix.'_commesse'=>'kp_tot_fatturato')    
		);

	}

} 

?>
