<?php  

die("Togliere il die!");

include_once(__DIR__.'/../../config.inc.php'); 
chdir($root_directory); 
require_once('include/utils/utils.php'); 
include_once('vtlib/Vtiger/Module.php'); 
$Vtiger_Utils_Log = true; 
global $adb, $table_prefix, $default_charset;
session_start(); 

$debug = false;
$versione = '18.05';

$aggiornati = 0;
$num_moduli_custom = 0;
$errori = 0;
$moduli_errori = array();

echo "AGGIORNAMENTO MODULI INIZIATO<br>";

$path_template_modulo = __DIR__.'/../../vtlib/ModuleDir/'.$versione.'/ModuleFile.php';
if(file_exists($path_template_modulo)){
    
    $q_moduli_custom = "SELECT tabid,name
                    FROM {$table_prefix}_tab
                    WHERE isentitytype = 1
                    AND customized = 1
                    AND name NOT IN ('Employees')";

    if($debug){
        //$q_moduli_custom .= " AND name = 'KpRIBA'";
    }

    $res_moduli_custom = $adb->query($q_moduli_custom);
    $num_moduli_custom = $adb->num_rows($res_moduli_custom);
    for($i = 0; $i < $num_moduli_custom; $i++){
        $tabid = $adb->query_result($res_moduli_custom, $i, 'tabid');
        $tabid = html_entity_decode(strip_tags($tabid), ENT_QUOTES, $default_charset);

        $nome_modulo = $adb->query_result($res_moduli_custom, $i, 'name');
        $nome_modulo = html_entity_decode(strip_tags($nome_modulo), ENT_QUOTES, $default_charset);

        echo "---> Aggiornamento modulo ".$nome_modulo.'...<br>';

        $path_file_modulo = __DIR__.'/../../modules/'.$nome_modulo.'/'.$nome_modulo.'.php';    
        if(file_exists($path_file_modulo)){

            echo "------> Recupero dati modulo -> ";

            $dati_modulo = getCampoPrincipaleModuloDaDatabase($tabid);
            if(empty($dati_modulo)){

                $dati_modulo = getCampoPrincipaleModuloDaFile($tabid, $path_file_modulo);

            }

            if(!empty($dati_modulo)){

                $dati_template_modulo = array(
                    'classe_modulo' => 'ModuleClass',
                    'campo_id_modulo' => 'payslipid',
                    'campo_principale_modulo' => 'payslipname',
                    'label_campo_principale_modulo' => 'Payslip Name',
                    'tabella_principale_modulo' => '_payslip'
                );

                $dati_modulo_aggiornato = array(
                    'classe_modulo' => $nome_modulo,
                    'campo_id_modulo' => strtolower($nome_modulo).'id',
                    'campo_principale_modulo' => $dati_modulo['campo_principale'],
                    'label_campo_principale_modulo' => $dati_modulo['label_campo_principale'],
                    'tabella_principale_modulo' => '_'.strtolower($nome_modulo)
                );

                echo "OK<br>";

                if($debug){
                    print_r($dati_template_modulo);
                    echo "<br>";
                    print_r($dati_modulo_aggiornato);
                    echo "<br>";
                }

                echo "------> Scrittura file aggiornato -> ";

                $res = scriviFileModuloAggiornato($dati_template_modulo, $dati_modulo_aggiornato, $nome_modulo, $path_file_modulo, $path_template_modulo);

                if($res){
                    echo "OK<br>";

                    $aggiornati++;
                }
                else{
                    echo "ERROR<br>";

                    $moduli_errori[] = $nome_modulo;
                    $errori++;
                }

            }
            else{
                echo "ERROR<br>";

                $moduli_errori[] = $nome_modulo;
                $errori++;
            }

        }
        else{
            echo "ERROR: file modulo non presente<br>";

            $moduli_errori[] = $nome_modulo;
            $errori++;
        }

    }

}

echo "AGGIORNAMENTO MODULI TERMINATO: moduli ".$num_moduli_custom.", aggiornati ".$aggiornati."<br>";
if(!empty($moduli_errori)){
    print_r($moduli_errori);
}

function scriviFileModuloAggiornato($dati_template_modulo, $dati_modulo_aggiornato, $nome_modulo, $path_file_modulo, $path_template_modulo){
    global $adb, $table_prefix, $default_charset;

    $res = false;

    if($GLOBALS['debug']){
        $path = __DIR__.'/'.date('YmdHis').'_kp_aggiornamento_moduli/modules/'.$nome_modulo;
        if(!is_dir($path)){

            mkdir($path, 0777, true);

        }
    }
    else{
        $path = __DIR__.'/../../modules/'.$nome_modulo;
    }

    if(is_dir($path)){

        $file_modulo = file_get_contents($path_template_modulo, false);

        $new_file_modulo = $file_modulo;

        foreach($dati_template_modulo as $key => $value){

            $new_file_modulo = str_replace($value, $dati_modulo_aggiornato[$key], $new_file_modulo);

        }

        if($GLOBALS['debug']){

            $res_copy = true;

        }
        else{
            $path_file_backup = $path.'/'.$nome_modulo.'_old_agg.php';
            if(file_exists($path_file_backup)){

                unlink($path_file_backup);

            }

            $res_copy = copy($path_file_modulo, $path_file_backup);

        }

        if($res_copy){

            $myfile = fopen($path.'/'.$nome_modulo.'.php', "w+");
            fwrite($myfile, $new_file_modulo);
            fclose($myfile);

            $res = true;

        }

    }

    return $res;
}

function getCampoPrincipaleModuloDaDatabase($tabid){
    global $adb, $table_prefix, $default_charset;

    $dati_modulo = array();

    $q = "SELECT campi.fieldname,
        campi.fieldlabel
        FROM {$table_prefix}_entityname ent
        INNER JOIN {$table_prefix}_field campi ON campi.fieldname = ent.fieldname
        WHERE ent.tabid = {$tabid} AND campi.tabid = {$tabid}
        AND ent.fieldname NOT LIKE '%,%'";
    $res = $adb->query($q);
    if($adb->num_rows($res) > 0){
        $fieldname = $adb->query_result($res, 0, 'fieldname');
        $fieldname = html_entity_decode(strip_tags($fieldname), ENT_QUOTES, $default_charset);

        $fieldlabel = $adb->query_result($res, 0, 'fieldlabel');
        $fieldlabel = html_entity_decode(strip_tags($fieldlabel), ENT_QUOTES, $default_charset);

        $dati_modulo = array(
            'campo_principale' => $fieldname,
            'label_campo_principale' => $fieldlabel
        );
    }

    return $dati_modulo;
}

function getCampoPrincipaleModuloDaFile($tabid, $path_file_modulo){
    global $adb, $table_prefix, $default_charset;

    $dati_modulo = array();
        
    $file_modulo = file_get_contents($path_file_modulo, false);

    $pos_variabile = strpos($file_modulo, "popup_fields");
    if ($pos_variabile !== false) {
        
        $pos_inizio_valore = strpos($file_modulo, "'", $pos_variabile);
        if ($pos_inizio_valore !== false) {
            $pos_inizio_valore++;
            $pos_fine_valore = strpos($file_modulo, "'", $pos_inizio_valore);
            if ($pos_fine_valore !== false) {
                
                $lunghezza_valore = $pos_fine_valore - $pos_inizio_valore;

                $nome_campo_principale = substr($file_modulo, $pos_inizio_valore, $lunghezza_valore);

                $q = "SELECT fieldlabel
                    FROM {$table_prefix}_field
                    WHERE tabid = ".$tabid." 
                    AND fieldname = '".$nome_campo_principale."'";
                $res = $adb->query($q);
                if($adb->num_rows($res) > 0){
                    $fieldlabel = $adb->query_result($res, 0, 'fieldlabel');
                    $fieldlabel = html_entity_decode(strip_tags($fieldlabel), ENT_QUOTES, $default_charset);

                    $dati_modulo = array(
                        'campo_principale' => $nome_campo_principale,
                        'label_campo_principale' => $fieldlabel
                    );
                }

            }

        }

    }

    return $dati_modulo;
}
