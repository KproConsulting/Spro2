<?php 
include_once('../../../config.inc.php'); 
chdir($root_directory); 
require_once('include/utils/utils.php'); 
include_once('vtlib/Vtiger/Module.php'); 
require_once('modules/SproCore/SDK/KpSDK.php'); 
$Vtiger_Utils_Log = true; 
global $adb, $table_prefix;
session_start(); 

//setTestataFileSDK();

$traduzioni = getItLabelFromSorgente();

$string = "\n";

foreach( $traduzioni as $traduzione ){

    if( !traduzioneGiaPresente($traduzione["module"], $traduzione["label"]) ){

        $module = addslashes($traduzione["module"]);
        $label = addslashes($traduzione["label"]);
        $trans_label = addslashes($traduzione["trans_label"]);
        $en_trans_label = addslashes($traduzione["en_trans_label"]);

        $string .= "\nSDK::setLanguageEntries('".$module."', '".$label."', array('it_it' => '".$trans_label."' ,'en_us' => '".$en_trans_label."'));";

    }

}

//print_r($string);

$nome_file = __DIR__."/sdk_traduzioni_mancanti.php";

$file = fopen($nome_file, "a");
fwrite($file, $string);
fclose($file);

function setTestataFileSDK(){
    global $adb, $table_prefix, $default_charset, $current_user;

    $nome_file = __DIR__."/sdk_traduzioni_mancanti.php";

    $header_file = "<?php";
    $header_file .= "\ninclude_once('../../../config.inc.php');";
    $header_file .= "\nchdir(\$root_directory);";
    $header_file .= "\nrequire_once('include/utils/utils.php');";
    $header_file .= "\ninclude_once('vtlib/Vtiger/Module.php');";
    $header_file .= "\nrequire_once('modules/SproCore/SDK/KpSDK.php');";
    $header_file .= "\n\$Vtiger_Utils_Log = true;";
    $header_file .= "\nglobal \$adb, \$table_prefix;";
    $header_file .= "\nsession_start();";

    $file = fopen($nome_file, "x+");
    fwrite($file, $header_file);
    fclose($file);

}

function getItLabelFromSorgente(){
    global $adb, $table_prefix, $default_charset, $current_user;

    $result = array();

    $query = "SELECT 
                languageid,
                module,
                language,
                label,
                trans_label
                FROM spro_1_dev.sdk_language 
                WHERE language = 'it_it'";

    $result_query = $adb->query($query);
    $num_result = $adb->num_rows($result_query);

    for($i=0; $i < $num_result; $i++){

        $module = $adb->query_result($result_query, $i, 'module');
        $module = html_entity_decode(strip_tags($module), ENT_QUOTES, $default_charset);

        $label = $adb->query_result($result_query, $i, 'label');
        $label = html_entity_decode(strip_tags($label), ENT_QUOTES, $default_charset);

        $trans_label = $adb->query_result($result_query, $i, 'trans_label');
        $trans_label = html_entity_decode(strip_tags($trans_label), ENT_QUOTES, $default_charset);

        $en_trans_label = getEnTraductionLabelFromSorgente($module, $label);

        $result[] = array("module" => $module,
                        "label" => $label,
                        "trans_label" => $trans_label,
                        "en_trans_label" => $en_trans_label);

    }

    return $result;

}

function getEnTraductionLabelFromSorgente($module, $label){
    global $adb, $table_prefix, $default_charset, $current_user;

    $trans_label = "";

    $query = "SELECT 
                languageid,
                module,
                language,
                label,
                trans_label
                FROM spro_1_dev.sdk_language 
                WHERE language = 'en_us' AND module = '".$module."' AND label = '".$label."'";

    $result_query = $adb->query($query);
    $num_result = $adb->num_rows($result_query);

    if( $num_result > 0 ){

        $trans_label = $adb->query_result($result_query, $i, 'trans_label');
        $trans_label = html_entity_decode(strip_tags($trans_label), ENT_QUOTES, $default_charset);

    }

    return $trans_label;

}

function traduzioneGiaPresente($module, $label){
    global $adb, $table_prefix, $default_charset, $current_user;

    $query = "SELECT 
                languageid,
                module,
                language,
                label,
                trans_label
                FROM spro.sdk_language 
                WHERE language = 'it_it' AND module = '".$module."' AND label = '".$label."'";

    $result_query = $adb->query($query);
    $num_result = $adb->num_rows($result_query);

    if( $num_result > 0 ){
        return true;
    }
    else{
        return false;
    }

}

?>