<?php 

/* kpro@tom21112016 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 */

require_once('modules/KpRilevazRischiRig/KpRilevazRischiRig.php');

class KpRilevazRischiRigKp extends KpRilevazRischiRig {

	//Script modifica Related List
	var $list_fields = Array();

	var $list_fields_name = Array(
		'Nome Riga'=>'kp_nome_riga',
		'Rischio'=>'kp_rischio',
		'Area Stabilimento'=>'kp_area_stab',
		'Gravita'=>'kp_gravita_rischio',
		'Probabilita'=>'kp_probabilita_risc' 
	);

	function KpRilevazRischiRigKp(){
		global $table_prefix;
		parent::__construct();
		$this->list_fields = Array(
			'Nome Riga'=>Array($table_prefix.'_kprilevazrischirig'=>'kp_nome_riga'),
			'Rischio'=>Array($table_prefix.'_kprilevazrischirig'=>'kp_rischio'),
			'Area Stabilimento'=>Array($table_prefix.'_kprilevazrischirig'=>'kp_area_stab'),
			'Gravita'=>Array($table_prefix.'_kprilevazrischirig'=>'kp_gravita_rischio'),
			'Probabilita'=>Array($table_prefix.'_kprilevazrischirig'=>'kp_probabilita_risc') 
		);

	}
	
	//Script modifica Funtion Save
	/*function save_module($module){

		global $table_prefix, $adb;

		parent::save_module($module);
		
	}*/

}
?>
