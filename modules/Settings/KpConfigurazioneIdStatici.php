<?php

/* kpro@bid16042018 */

/**
 * @author Bidese Jacopo
 * @copyright (c) 2018, Kpro Consulting Srl
 */

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


$check_table = "SHOW TABLES LIKE 'kp_settings_config_id_statici'";

$result_check_table = $adb->query($check_table);
$num_check_table = $adb->num_rows($result_check_table);

if( $num_check_table == 0 ){

    $create_table = "CREATE TABLE IF NOT EXISTS `kp_settings_config_id_statici` (
        `id_configurazione` int NOT NULL AUTO_INCREMENT,
        `nome_area_configurazione` varchar(255) NOT NULL,
        `nome_configurazione` varchar(255) NOT NULL,
        `valore` int(19) NOT NULL,
        PRIMARY KEY (`id_configurazione`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";

    $adb->query($create_table);

    $q_insert_data = "INSERT into `kp_settings_config_id_statici` 
    (`nome_area_configurazione`, `nome_configurazione`, `valore`) 
    values
    ('Programmi Custom','Generazione ODF da Report Attività - Servizio per rimborso forfettario',0),
    ('Programmi Custom','Generazione ODF da Report Attività - Servizio per rimborso chilometrico',0),
    ('Programmi Custom','Generazione ODF da Report Attività - Servizio per rimborso orario',0),
    ('Programmi Custom','Generazione ODF da Report Attività - Servizio per rimborso autostrada, vitto alloggio e altre spese',0),
    ('Generale','Listino standard (da utilizzare nel caso il cliente non ne abbia uno)',0),
    ('Template Email','Invio Solleciti Pagamenti',0),
    ('Template Email','Invio Avvisi di Scadenza',0),
    ('Programmi Custom','Invio Avvisi di Scadenza - Giorni di anticipo',4),
    ('PDF Maker','Invio Massivo Fatture - Template per invio tickets',0),
    ('PDF Maker','Invio Massivo Fatture - Template per invio report attività',0),
    ('Template Email','Invio Massivo Fatture - Invio Fatture',0),
    ('Template Email','Invio Massivo Fatture - Invio Note di Credito',0),
    ('Template Email','Invio Massivo Fatture - Invio Fatture Proforma',0),
    ('Programmi Custom','Gestione Avvisi - Giorni per In Scadenza standard',30),
    ('Documenti','Cartella Fatture',51),
    ('Documenti','Cartella Note di Credito',56),
    ('Documenti','Cartella Fatture Proforma',51),
    ('Documenti','Cartella Preventivi',52),
    ('Programmi Custom','Invio Massivo Fatture - Utente invio mail',1),
    ('Programmi Custom','Invio Solleciti Pagamenti - Utente invio mail',1),
    ('Programmi Custom','Invio Avvisi di Scadenza - Utente invio mail',1),
    ('Documenti','Cartella Attestati',28),
    ('Template Email','Portale SPro - Invio notifiche creazione/modifica',0),
    ('Programmi Custom','Generazione Fatture da ODF - Servizio per addebito spese RIBA',0),
    ('Documenti','Cartella Documenti Processi',0),
    ('Documenti','Cartella Processi Approvati',0),
    ('Documenti','Cartella Stampa Processi',0),
    ('PDF Maker','Template Approvazione Processi',0),
    ('PDF Maker','Template Stampa Processi',0),
    ('Programmi Custom','Statistiche Settimanali Tickets - Numero settimane da mostrare (esclusa prec. e corrente)',3),
    ('Programmi Custom','Statistiche Mensili Tickets - Numero mesi da mostrare (escluso prec. e corrente)',3),
    ('Documenti','Cartella Foto Esiti Manutenzioni',0),
    ('Documenti','Cartella Procedure',0),
    ('Documenti','Cartella PDF Doc. Caricati da Portale (Documenti Ticket)',0),
    ('Documenti','Cartella Rapportini Interventi',0),
    ('Documenti','Cartella Caricati da Portale (Allegati Vari Ticket)',0),
    ('Documenti','Cartella Caricati da Portale (Documenti Ticket)',0),
    ('Documenti','Cartella Master Liberi',0),
    ('Documenti','Cartella Master Servizi',0),
    ('Documenti','Cartella Organigrammi Approvati',0),
    ('PDF Maker','Template Approvazione Organigrammi',0),
    ('PDF Maker','Template Consegna DPI',0),
    ('Documenti','Cartella Consegna DPI',0),
    ('Documenti','Cartella Stampa Organigrammi',0),
    ('PDF Maker','Template Stampa Organigrammi',0),
    ('Documenti','Cartella Documenti Pubblici Portale Fornitori',0),    
    ('Template Email','Template Rapportino Intervento',0),
    ('Template Email','Portale Fornitore - Invio segnalazioni',0),
    ('Programmi Custom','Portale Fornitore - Utente invio mail',1),
    ('PDF Maker','Template Rapportino Intervento',0),
    ('Programmi Custom','Portale Fornitore - Ora inizio giornata lavorativa',8),
    ('Programmi Custom','Portale Fornitore - Ora inizio pausa lavorativa',12),
    ('Programmi Custom','Portale Fornitore - Ora fine pausa lavorativa',14),
    ('Programmi Custom','Portale Fornitore - Ora fine giornata lavorativa',18),
    ('Documenti','Cartella Esempi Import Custom',0),
    ('PDF Maker','Template Prospetto Rilevazione Rischi',0),
    ('PDF Maker','Template DVR',0)";

    $adb->query($q_insert_data);

}

$html_tabella = "";

$query_aree = "SELECT nome_area_configurazione
            FROM kp_settings_config_id_statici
            GROUP BY nome_area_configurazione
            ORDER BY nome_area_configurazione";

$result_query_aree = $adb->query($query_aree);
$num_result_aree = $adb->num_rows($result_query_aree);

if($num_result_aree > 0){
    for($i = 0; $i < $num_result_aree; $i++){
        $nome_area_configurazione = $adb->query_result($result_query_aree, $i, 'nome_area_configurazione');
        $nome_area_configurazione = html_entity_decode(strip_tags($nome_area_configurazione), ENT_QUOTES, $default_charset);
        if($nome_area_configurazione == null){
            $nome_area_configurazione = '';
        }

        $html_tabella .= "<h5><b>".$nome_area_configurazione."</b></h5>";
        
        $query = "SELECT 
                id_configurazione,
                nome_configurazione,
                valore
                FROM kp_settings_config_id_statici
                WHERE nome_area_configurazione = '".$nome_area_configurazione."'
                ORDER BY nome_configurazione";

        $result_query = $adb->query($query);
        $num_result = $adb->num_rows($result_query);

        if( $num_result > 0 ){

            $html_tabella .= "<table width=100% class='table table-striped' >"; 

            for($j = 0; $j < $num_result; $j++){
            
                $id_configurazione = $adb->query_result($result_query, $j, 'id_configurazione');
                $id_configurazione = html_entity_decode(strip_tags($id_configurazione), ENT_QUOTES, $default_charset);
                if($id_configurazione == '' && $id_configurazione == null){
                    $id_configurazione = 0;
                }

                $nome_configurazione = $adb->query_result($result_query, $j, 'nome_configurazione');
                $nome_configurazione = html_entity_decode(strip_tags($nome_configurazione), ENT_QUOTES, $default_charset);
                if($nome_configurazione == null){
                    $nome_configurazione = '';
                }

                $valore = $adb->query_result($result_query, $j, 'valore');
                $valore = html_entity_decode(strip_tags($valore), ENT_QUOTES, $default_charset);
                if($valore == '' && $valore == null){
                    $valore = 0;
                }

                $html_tabella .= "<tr><td style='width: 85%;'>".$nome_configurazione."</td>
				<td><div class='form-group'>
					<input style='text-align:right;' type='number' class='form-control' id='form_config_".$id_configurazione."' value='".$valore."' min='0' step='1' >
				</div></td></tr>";

            }

            $html_tabella .= "</table><hr/>"; 
        }

    }
}

$smarty->assign("tabella_id_statici", $html_tabella);

$smarty->display('SproCore/Settings/KpConfigurazioneIdStatici.tpl');

?>