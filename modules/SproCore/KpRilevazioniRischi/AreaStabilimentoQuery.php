<?php
global $table_prefix;

$azienda = $_REQUEST['azienda'];
$stabilimento = $_REQUEST['stabilimento'];

if( $azienda != '' && $azienda != 'undefined'){
	$query .= " AND ".$table_prefix."_kpareestabilimento.kp_azienda = '".$azienda."' ";
}

if( $stabilimento != '' && $stabilimento != 'undefined'){
	$query .= " AND ".$table_prefix."_kpareestabilimento.kp_stabilimento = '".$stabilimento."' ";
}

?>