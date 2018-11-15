<?php
global $default_charset,$adb,$table_prefix,$autocomplete_return_function;
$forfield = htmlspecialchars($_REQUEST['forfield'], ENT_QUOTES, $default_charset);
$list_result_count = $i-1;
$value = getValue($ui_col_array,$list_result,$fieldname,$focus,$module,$entity_id,$list_result_count,"search",$focus->popup_type);
if(isset($forfield) && $forfield != '' && $focus->popup_type != 'detailview') {
    $value1 = strip_tags($value);
    $value = htmlspecialchars(addslashes(html_entity_decode(strip_tags($value), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset); // Remove any previous html conversion

    $result1 = $adb->query('SELECT * FROM '.$table_prefix.'_account acc 
                        LEFT JOIN '.$table_prefix.'_kpbusinessunit bu ON bu.kpbusinessunitid = acc.kp_business_unit
                        WHERE acc.accountid = '.$entity_id); /* kpro@bid151120181550 */
    if ($result1 && $adb->num_rows($result1)>0) {
        
        $distanza = $adb->query_result($result1, 0, 'kp_km_percorsi');
        $distanza = html_entity_decode(strip_tags($distanza), ENT_QUOTES, $default_charset);
        if($distanza == null || $distanza == ""){
            $distanza = 0;
        }

        $ore_viaggio = $adb->query_result($result1, 0, 'kp_ore_viaggio');
        $ore_viaggio = html_entity_decode(strip_tags($ore_viaggio), ENT_QUOTES, $default_charset);
        if($ore_viaggio == null || $ore_viaggio == ""){
            $ore_viaggio = 0;
        }

        $pedaggio = $adb->query_result($result1, 0, 'kp_spese_autostrada');
        $pedaggio = html_entity_decode(strip_tags($pedaggio), ENT_QUOTES, $default_charset);
        if($pedaggio == null || $pedaggio == ""){
            $pedaggio = 0;
        }
        /* kpro@bid151120181550 */
        $business_unit = $adb->query_result($result1, 0, 'kpbusinessunitid');
        $business_unit = html_entity_decode(strip_tags($business_unit), ENT_QUOTES, $default_charset);
        if($business_unit == null || $business_unit == ""){
            $business_unit = 0;
        }

        $nome_business_unit = $adb->query_result($result1, 0, 'kp_nome_business_un');
        $nome_business_unit = html_entity_decode(strip_tags($nome_business_unit), ENT_QUOTES, $default_charset);
        if($nome_business_unit == null){
            $nome_business_unit = "";
        }
        /* kpro@bid151120181550 end */
    }
    else{
        $distanza = 0;
        $ore_viaggio = 0;
        $pedaggio = 0;
        /* kpro@bid151120181550 */
        $business_unit = 0; 
        $nome_business_unit = "";
        /* kpro@bid151120181550 end */
    }

    $distanza = formatUserNumber($distanza);
    $ore_viaggio = formatUserNumber($ore_viaggio);
    $pedaggio = formatUserNumber($pedaggio);

    $autocomplete_return_function[$entity_id] = "set_return_dati_azienda_to_report_attivita($entity_id, \"$value\", \"$forfield\", \"$distanza\", \"$ore_viaggio\", \"$pedaggio\", \"$business_unit\", \"$nome_business_unit\");"; /* kpro@bid151120181550 */
    $value = "<a href='javascript:void(0);' onclick='{$autocomplete_return_function[$entity_id]}closePopup();'>$value1</a>"; //crmv@21048m
}
?>
