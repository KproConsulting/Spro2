<?php

/* kpro@tom06072017 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

require_once('modules/SproCore/KpPartecipFormaz/ClassKpPartecipFormazKp.php');
require_once('modules/SproCore/CustomViews/kpPopupGenerazioneOdfDaPartecipazioni/KpGenerazioneOdFDaPartecipazioni.php');
require_once('modules/SproCore/CustomViews/PopupGenerazioneFatturaDaOdf/fattureDaOdF_utils.php');

$rows = array();

if (!isset($_SESSION['authenticated_user_id'])) {

    $json = json_encode($rows);
    print $json;
    die;

    header("Location: ". $site_URL."/index.php");
	die; 
}
$current_user->id = $_SESSION['authenticated_user_id'];

$array_odf = array();

if( isset($_GET['ids']) ){

    $ids = $_GET['ids'];

    $array_ids = explode(",", $ids); 
    
    foreach($array_ids as $id){

        $odfid = KpGenerazioneOdFDaPartecipazioni::setOdfPartecipazione($id);
        if($odfid != ''){
            $array_odf[] = $odfid;
        }
    }

    if( isset($_GET['modalita']) ){
        $modalita = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['modalita']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
        $modalita = substr($modalita,0,100);
        if($modalita == null){
            $modalita = "";
        }
    }
    else{
        $modalita = "";
    }

    if($modalita == "Fattura" && count($array_odf) > 0){
        if( isset($_GET['data_fattura']) ){
            $data_fattura = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['data_fattura']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
            $data_fattura = substr($data_fattura,0,100);
            if($data_fattura != "" && $data_fattura != null){

                list($giorno, $mese, $anno) = explode("/", $data_fattura);
                $data_fattura = date("Y-m-d", mktime(0, 0, 0, $mese, $giorno, $anno));
                
            }
            else{
                $data_fattura = date("Y-m-d");
            }
        }
        else{
            $data_fattura = date("Y-m-d");
        }

        if( isset($_GET['modpagamento']) ){
            $modpagamento = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['modpagamento']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
            $modpagamento = substr($modpagamento,0,100);
            if($modpagamento == "--Nessuno--" || $modpagamento == "" || $modpagamento == null){
                $modpagamento = 0;
            }
        }
        else{
            $modpagamento = 0;
        }

        foreach($array_odf as $odf){

            $result = GeneraInvoiceDaOdF($odf, $data_fattura, $modpagamento);

        }
    }

    $rows[] = array("return" => "ok");

}

$json = json_encode($rows);
print $json;

?>