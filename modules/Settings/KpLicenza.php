<?php

/* kpro@tom31072017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

require_once('modules/SproCore/SproUtils/KpLicenza.php');
require_once('include/utils/utils.php');
require_once('Smarty_setup.php');
global $app_strings;
global $mod_strings;
global $currentModule;
global $theme;
$theme_path="themes/".$theme."/";
$image_path=$theme_path."images/";
global $current_language, $adb;

$smarty = new vtigerCRM_Smarty;

$smarty->assign("MOD", return_module_language($current_language,'Settings'));
$smarty->assign("CMOD", $mod_strings);
$smarty->assign("APP", $app_strings);
$smarty->assign("IMAGE_PATH",$image_path);
$smarty->assign("THEME", $theme);


if(!Vtiger_Utils::CheckTable('kp_aree_programmi')) {
    Vtiger_Utils::CreateTable(
        'kp_aree_programmi',
        "kp_id INT(19) NOTNULL PRIMARY,
        kp_nome VARCHAR(100)", 
    true);
}

if(!Vtiger_Utils::CheckTable('kp_programmi')) {
    Vtiger_Utils::CreateTable(
        'kp_programmi',
        "kp_id INT(19) NOTNULL PRIMARY,
        kp_nome VARCHAR(100),
        kp_limite_utenti VARCHAR(1),
        kp_areaid INT(19),
        kp_order INT(19)", 
    true);
}

$lista_moduli_kpro = KpLicenza::getModuliKpro();

$tabella_licenze_moduli = "<table width=100% class='table table-striped'>";

$area = "";

foreach($lista_moduli_kpro as $modulo){

    $tabella_licenze_moduli .= "<tr>";

    if($modulo["area"] != $area){

        $area = $modulo["area"]; 

        if($modulo["numero_elementi_area"] > 0){
            $tabella_licenze_moduli .= "<td style='vertical-align: top;' rowspan='".$modulo["numero_elementi_area"]."'>";
            $tabella_licenze_moduli .= "<div class='checkbox'>";
            $tabella_licenze_moduli .= "<label>";
            $tabella_licenze_moduli .= "<input class='check_area_kpro' type='checkbox' id='area_".$modulo["area_id"]."' value='area_".$modulo["area_id"]."'>";
            $tabella_licenze_moduli .= "<b><span style='vertical-align: middle; margin-left: 10px;' >".$modulo["area"]."</span></b></label>";
            $tabella_licenze_moduli .= "</div>";
            $tabella_licenze_moduli .= "</b></td>";
        }
        else{
            $tabella_licenze_moduli .= "<td style='vertical-align: middle;'><b>";
            $tabella_licenze_moduli .= $modulo["area"];
            $tabella_licenze_moduli .= "</b></td>";
        }

    }
    elseif($modulo["area"] == $area && $modulo["area"] == ""){

        $tabella_licenze_moduli .= "<td style='vertical-align: middle;'><b>";
        $tabella_licenze_moduli .= $modulo["area"];
        $tabella_licenze_moduli .= "</b></td>";

    }

    $tabella_licenze_moduli .= "<td style='width: 40px;'>";
    $tabella_licenze_moduli .= "<div class='checkbox'>";
    $tabella_licenze_moduli .= "<label>";
    if($modulo["stato"] != "Non attivo"){
        $tabella_licenze_moduli .= "<input class='check_module_kpro check_area_".$modulo["area_id"]."' type='checkbox' id='".$modulo["nome"]."' value='".$modulo["nome"]."' checked>";
    }
    else{
        $tabella_licenze_moduli .= "<input class='check_module_kpro check_area_".$modulo["area_id"]."' type='checkbox' id='".$modulo["nome"]."' value='".$modulo["nome"]."'>";
    }
    $tabella_licenze_moduli .= "</label>";
    $tabella_licenze_moduli .= "</div>";
    $tabella_licenze_moduli .= "</td>";
    $tabella_licenze_moduli .= "<td style='vertical-align: middle;'>".$modulo["nome"]." - ".$modulo["label"];
    if($modulo["stato"] != "Non attivo"){
        $tabella_licenze_moduli .= " (Stato: ".$modulo["stato"];

        if($modulo["data_validita"] == "" && $modulo["stato"] != "Non attivo"){
            $tabella_licenze_moduli .= " - Scadenza: Illimitata";
        }
        elseif($modulo["stato"] != "Non attivo"){
            $tabella_licenze_moduli .= " - Scadenza: ".$modulo["data_validita"];
        }

        $tabella_licenze_moduli .= ")";

    }
    $tabella_licenze_moduli .= "</td>";
    $tabella_licenze_moduli .= "</tr>";

}

$tabella_licenze_moduli .= "</table>";

$smarty->assign("tabella_licenze_moduli", $tabella_licenze_moduli);



$lista_programmi_kpro = KpLicenza::getProgrammiKpro();

$tabella_licenze_programmi = "<table width=100% class='table table-striped'>";

$area = "";

foreach($lista_programmi_kpro as $programma){

    $tabella_licenze_programmi .= "<tr>";

    if($programma["area"] != $area){

        $area = $programma["area"]; 

        if($programma["numero_elementi_area"] > 0){
            $tabella_licenze_programmi .= "<td style='vertical-align: top;' rowspan='".$programma["numero_elementi_area"]."'>";
            $tabella_licenze_programmi .= "<div class='checkbox'>";
            $tabella_licenze_programmi .= "<label>";
            $tabella_licenze_programmi .= "<input class='check_area_prog_kpro' type='checkbox' id='area_prog_".$programma["area_id"]."' value='area_".$programma["area_id"]."'>";
            $tabella_licenze_programmi .= "<b><span style='vertical-align: middle; margin-left: 10px;' >".$programma["area"]."</span></b></label>";
            $tabella_licenze_programmi .= "</div>";
            $tabella_licenze_programmi .= "</b></td>";
        }
        else{
            $tabella_licenze_programmi .= "<td style='vertical-align: middle;'><b>";
            $tabella_licenze_programmi .= $programma["area"];
            $tabella_licenze_programmi .= "</b></td>";
        }

    }
    elseif($programma["area"] == $area && $programma["area"] == ""){

        $tabella_licenze_programmi .= "<td style='vertical-align: middle;'><b>";
        $tabella_licenze_programmi .= $programma["area"];
        $tabella_licenze_programmi .= "</b></td>";

    }

    $tabella_licenze_programmi .= "<td style='width: 40px;'>";
    $tabella_licenze_programmi .= "<div class='checkbox'>";
    $tabella_licenze_programmi .= "<label>";
    if($programma["stato"] != "Non attivo"){
        $tabella_licenze_programmi .= "<input class='check_prog_kpro check_prog_".$programma["area_id"]."' type='checkbox' id='prog_".$programma["id"]."' value='".$programma["id"]."' checked>";
    }
    else{
        $tabella_licenze_programmi .= "<input class='check_prog_kpro check_prog_".$programma["area_id"]."' type='checkbox' id='prog_".$programma["id"]."' value='".$programma["id"]."'>";
    }
    $tabella_licenze_programmi .= "</label>";
    $tabella_licenze_programmi .= "</div>";
    $tabella_licenze_programmi .= "</td>";
    $tabella_licenze_programmi .= "<td style='vertical-align: middle;'>".$programma["nome"];
    if($programma["stato"] != "Non attivo"){
        $tabella_licenze_programmi .= " (Stato: ".$programma["stato"];

        if($programma["data_validita"] == "" && $programma["stato"] != "Non attivo"){
            $tabella_licenze_programmi .= " - Scadenza: Illimitata";
        }
        elseif($programma["stato"] != "Non attivo"){
            $tabella_licenze_programmi .= " - Scadenza: ".$programma["data_validita"];
        }

        if($programma["limite_utenti"] == '1' && $programma["stato"] != "Non attivo"){
            if( $programma["numero_utenti"] == "" || $programma["numero_utenti"] == 0 ){
                $tabella_licenze_programmi .= " - Utenti: Illimitati)";
            }
            else{
                $tabella_licenze_programmi .= " - Utenti: ".$programma["numero_utenti"].")";
            }
        }
        else{
            $tabella_licenze_programmi .= ")";
        }
    }
    $tabella_licenze_programmi .= "</td>";
    $tabella_licenze_programmi .= "<td style='width: 200px; vertical-align: middle; text-align: right;'>";
    if($programma["limite_utenti"] == '1'){

        $tabella_licenze_programmi .= "<div class='form-group'>";
        $tabella_licenze_programmi .= "<label for='numero_utenti_pro_".$programma["id"]."'></label>";
        $tabella_licenze_programmi .= "<input type='number' style='text-align: right;' class='form-control' id='numero_utenti_pro_".$programma["id"]."'  placeholder='Numero utenti' value=".$programma["numero_utenti"].">";
        $tabella_licenze_programmi .= "</div>";

    }
    $tabella_licenze_programmi .= "</td>";
    $tabella_licenze_programmi .= "</td>";
    $tabella_licenze_programmi .= "</tr>";

}

$tabella_licenze_programmi .= "</table>";

$smarty->assign("tabella_licenze_programmi", $tabella_licenze_programmi);





$smarty->display('SproCore/Settings/KpLicenza.tpl');

?>
