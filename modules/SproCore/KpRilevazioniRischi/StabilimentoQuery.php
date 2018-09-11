<?php
global $table_prefix;

$azienda = $_REQUEST['azienda'];

if( $azienda != '' && $azienda != 'undefined'){
	$query .= " AND ".$table_prefix."_stabilimenti.azienda = '".$azienda."' ";
}

?>