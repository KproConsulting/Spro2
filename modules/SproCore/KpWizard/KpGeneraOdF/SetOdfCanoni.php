<?php

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

require_once('modules/SproCore/CustomViews/PopupGenerazioneOdfDaCanoni/odfDaCanoni_utils.php');

$rows = array();

if (!isset($_SESSION['authenticated_user_id'])) {

    $json = json_encode($rows);
    print $json;
    die;

    header("Location: ". $site_URL."/index.php");
	die; 
}
$current_user->id = $_SESSION['authenticated_user_id'];

if(isset($_POST['selezionati']) && isset($_POST['mese']) && isset($_POST['anno'])){ /* kpro@bid040920181110 */

    $selezionati = $_POST['selezionati'];

    /* kpro@bid040920181110 */
    $mese_fatturazione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_POST['mese']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $mese_fatturazione = substr($mese_fatturazione,0,100);

    $anno_fatturazione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_POST['anno']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $anno_fatturazione = substr($anno_fatturazione,0,100);
    /* kpro@bid040920181110 end */

    $totale_odf_creati = 0;
    $odf_prezzo = 0;
    $odf_senza_prezzo = 0;

    for($i = 0; $i < count($selezionati); $i++){
        $selezionato_corrente = $selezionati[$i];

        $risultato = generaOdfDaCanoni($selezionato_corrente, $mese_fatturazione, $anno_fatturazione); /* kpro@bid040920181110 */

        switch ($risultato){
            case "1":	
                $totale_odf_creati += 1;
                $odf_prezzo += 1;
                break;
            case "2":	
                $totale_odf_creati += 1;
                $odf_senza_prezzo += 1;
                break;
        }
    }

    $rows[] = array(
        "totale_odf_creati" => $totale_odf_creati,
        "odf_prezzo" => $odf_prezzo,
        "odf_senza_prezzo" => $odf_senza_prezzo
    );
}

$json = json_encode($rows);
print $json;

?>