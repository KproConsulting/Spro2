<?php
global $default_charset,$adb,$table_prefix,$autocomplete_return_function;
$forfield = htmlspecialchars($_REQUEST['forfield'], ENT_QUOTES, $default_charset);
$list_result_count = $i-1;
$value = getValue($ui_col_array,$list_result,$fieldname,$focus,$module,$entity_id,$list_result_count,"search",$focus->popup_type);
if(isset($forfield) && $forfield != '' && $focus->popup_type != 'detailview') {
    $value1 = strip_tags($value);
    $value = htmlspecialchars(addslashes(html_entity_decode(strip_tags($value), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset); // Remove any previous html conversion

    $result1 = $adb->query('SELECT * FROM '.$table_prefix.'_tipicorso WHERE tipicorsoid = '.$entity_id);
    if ($result1 && $adb->num_rows($result1)>0) {
        
        $validita_tipi_corso = $adb->query_result($result1, 0, 'validita_tipi_corso');
        $validita_tipi_corso = html_entity_decode(strip_tags($validita_tipi_corso), ENT_QUOTES, $default_charset);
        if($validita_tipi_corso == null){
            $validita_tipi_corso = "";
        }

        $tipicorso_name = $adb->query_result($result1, 0, 'tipicorso_name');
        $tipicorso_name = html_entity_decode(strip_tags($tipicorso_name), ENT_QUOTES, $default_charset);

        $nome_proposto = $adb->query_result($result1, 0, 'kp_nome_proposto');
        $nome_proposto = html_entity_decode(strip_tags($nome_proposto), ENT_QUOTES, $default_charset);
        if($nome_proposto != null && $nome_proposto != ""){
			$nome_formazione = $nome_proposto;
		}
		else{
			$nome_formazione = $tipicorso_name;
		}
		
		$anno_corrente = date("Y");

		$nome_formazione = str_replace('%a', $anno_corrente, $nome_formazione);
         
    }

    $autocomplete_return_function[$entity_id] = "set_return_frequenza_tipo_corso($entity_id, \"$value\", \"$forfield\", \"$validita_tipi_corso\", \"$nome_formazione\");";
    $value = "<a href='javascript:void(0);' onclick='{$autocomplete_return_function[$entity_id]}closePopup();'>$value1</a>"; //crmv@21048m
}

?>