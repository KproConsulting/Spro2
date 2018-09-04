<?php

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

require_once('modules/SproCore/CustomViews/PopupGenerazioneOdfDaTicket/odfDaTicket_utils.php');

$rows = array();

if (!isset($_SESSION['authenticated_user_id'])) {

    $json = json_encode($rows);
    print $json;
    die;

    header("Location: ". $site_URL."/index.php");
	die; 
}
$current_user->id = $_SESSION['authenticated_user_id'];

if(isset($_POST['selezionati'])){

    $selezionati = $_POST['selezionati'];

    $totale_ticket_da_fatturare = count($selezionati); /* kpro@bid040920181110 */
    $totale_odf_creati = 0;
    $totale_ticket_senza_servizio = 0;
    $totale_ticket_senza_prezzo = 0;
    $totale_ticket_senza_tempo = 0;
    $totale_ticket_senza_cliente = 0;

    for($i = 0; $i < count($selezionati); $i++){
        $selezionato_corrente = $selezionati[$i];

        $risultato = generaOdfDaTicket($selezionato_corrente);

        switch ($risultato){
            case "1":	
                $totale_odf_creati += 1;
                break;
            case "2":	
                $totale_ticket_senza_servizio += 1;
                break;
            case "3":	
                $totale_ticket_senza_prezzo += 1;
                break;
            case "4":	
                $totale_ticket_senza_tempo += 1;
                break;
            case "5":	
                $totale_ticket_senza_cliente += 1;
                break;
        }
    }

    $rows[] = array(
        "totale_ticket_da_fatturare" => $totale_ticket_da_fatturare,
        "totale_odf_creati" => $totale_odf_creati,
        "totale_ticket_senza_servizio" => $totale_ticket_senza_servizio,
        "totale_ticket_senza_prezzo" => $totale_ticket_senza_prezzo,
        "totale_ticket_senza_tempo" => $totale_ticket_senza_tempo,
        "totale_ticket_senza_cliente" => $totale_ticket_senza_cliente
    );

}

$json = json_encode($rows);
print $json;

?>