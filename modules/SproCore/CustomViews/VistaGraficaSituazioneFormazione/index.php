<!DOCTYPE html>

<?php

/* kpro@tom29112016 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 */

require_once('KproConfig.ini.php');

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_language;
session_start();

//print_r($_SESSION);die;

if (!isset($_SESSION['authenticated_user_id'])) {
    header("Location: " . $site_URL . "/index.php?module=Accounts&action=index");
}
$current_user->id = $_SESSION['authenticated_user_id'];

$data_corrente = date("Y-m-d");
list($anno_corrente, $mese_corrente, $giorno_corrente) = explode("-", $data_corrente);
$data_corrente_inv = date("d-m-Y", mktime(0, 0, 0, $mese_corrente, $giorno_corrente, $anno_corrente));

?>

<html>
    <head>
        <title>Situazione Formazione</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!--<link rel="shortcut icon" href="img/sicurezza_favicon.ico" />--> <!--Per il VTE Sicurezza-->
		<!--<link rel="shortcut icon" href="img/VTE_favicon.ico" />--> <!--Per il VTE-->

        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/style_gantt.css">
		<link rel="stylesheet" type="text/css" href="css/jquery-ui-kpro.css">
		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		
		<!--Grid-->
		<link rel="stylesheet" type="text/css" href="codebase/fonts/font_roboto/roboto.css"/>
		<!--<link rel="stylesheet" type="text/css" href="codebase/dhtmlx.css"/>-->
		<link rel="stylesheet" type="text/css" href="codebase/dhtmlx_material.css"/>
        <script src="codebase/dhtmlx.js"></script>
        <!--Grid end-->
        
        <!--Gantt-->
        <script src="codebase_gantt/dhtmlxgantt.js" type="text/javascript" charset="utf-8"></script>
        <script src="codebase_gantt/locale/locale_it.js" charset="utf-8"></script>
        <script src="codebase_gantt/ext/dhtmlxgantt_marker.js" type="text/javascript" charset="utf-8"></script>
        <script src="codebase_gantt/ext/dhtmlxgantt_auto_scheduling.js" type="text/javascript" charset="utf-8"></script>
        <script src="codebase_gantt/ext/dhtmlxgantt_critical_path.js" type="text/javascript" charset="utf-8"></script>
        <script src='codebase_gantt/ext/dhtmlxgantt_multiselect.js' type='text/javascript' charset='utf-8'></script>
        <script src='codebase_gantt/ext/dhtmlxgantt_csp.js' type='text/javascript' charset='utf-8'></script>
        <script src='codebase_gantt/ext/dhtmlxgantt_grouping.js' type='text/javascript' charset='utf-8'></script>
        <!--<script src='codebase_gantt/ext/dhtmlxgantt_quick_info.js' type='text/javascript' charset='utf-8'></script>-->
        <script src='codebase_gantt/ext/dhtmlxgantt_fullscreen.js'></script>
        <script src="http://export.dhtmlx.com/gantt/api.js"></script>  
        <script src="codebase_gantt/ext/dhtmlxgantt_tooltip.js" type="text/javascript" charset="utf-8"></script>	<!--Serve per aprire i tooltip -->
        <script src="codebase_gantt/ext/dhtmlxgantt_smart_rendering.js" type="text/javascript" charset="utf-8"></script>	<!--Serve per incrementare le prestazioni -->
        <link rel="stylesheet" href="codebase_gantt/dhtmlxgantt.css" type="text/css" media="screen" title="no title" charset="utf-8">
        <!--Gantt end-->
	
        <script src="js/jquery-2.1.4.min.js"></script>  
		<script src="js/jquery-ui.min.js"></script>

		<!-- Material -->
		<link rel="stylesheet" href="css/material.min.css">
		<script src="js/material.min.js"></script>
		<!-- Material end -->

		<!-- Materialize -->
		<!--<link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
		<script type="text/javascript" src="js/materialize.min.js"></script>-->
		<!-- Materialize end -->

		<script src="js/general.js"></script>
        <script type="text/JavaScript">
            
            var indirizzo_crm = "<?php echo($site_URL); ?>";
            var utente = "<?php echo($current_user->id); ?>";
            var data_corrente = "<?php echo($data_corrente); ?>";
            var data_corrente_inv = "<?php echo($data_corrente_inv); ?>";

            var anno_corrente = "<?php echo($anno_corrente); ?>";
            var mese_corrente = "<?php echo($mese_corrente); ?>";
            var giorno_corrente = "<?php echo($giorno_corrente); ?>";
			
        </script>   
    </head>
    <body>

        <div id="scheduler_conteiner_div" class="mdl-card mdl-shadow--2dp" style="margin: auto; width: 98%; height: 95%; margin-top: 10px;">

            <div>

                <table style="width: 100%">
                    <tr>

                        <td> 
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">

                                <input type="text" class="mdl-textfield__input" id="form_azienda" autocomplete="on" list="datalist_aziende">
                                <label class="mdl-textfield__label" for="form_azienda">Azienda</label>

                            </div>

                            <datalist id="datalist_aziende"></datalist>

                        </td> 

                        <td> 
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <select class="mdl-textfield__input" id="form_stabilimento">
                                    <option value="all">Tutti</option>
                                </select>
                                <label class="mdl-textfield__label" for="form_stabilimento">Stabilimento</label>
                            </div>
                        </td> 

                        <td>			
                            <div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label">
                                <select class="mdl-textfield__input" id="form_tipo_corso">
                                    <option value="all">Tutti</option>
                                </select>
                                <label class="mdl-textfield__label" for="form_tipo_corso">Tipo Corso</label>
                            </div>
                        </td> 

                        <td>
                            <button id="bottone_aggiorna" class="mdl-button mdl-js-button mdl-js-ripple-effect">Aggiorna</button>
                        </td> 

                        <td>
                            <div class="caricamento mdl-spinner mdl-js-spinner is-active" style="display; none; vertical-align: middle;"></div>
                        </td>

                    </tr>
                </table>

            </div>

            <div id="filtri_div">
                <table id="filtri_table">
                    <tr>
                        <td id="filtri_td_left">
                            <button id="bottone_dettaglio" class="mdl-button mdl-js-button mdl-js-ripple-effect">Dettaglio</button>
                        </td>
                        <td id="filtri_td_center">

                            <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="scale2">
                                <input type="radio" id="scale2" class="mdl-radio__button" name="scale" value="2" checked>
                                <span class="mdl-radio__label">Settimana</span>
                            </label>

                            <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="scale3">
                                <input type="radio" id="scale3" class="mdl-radio__button" name="scale" value="3">
                                <span class="mdl-radio__label">Mese</span>
                            </label>

                            <label class="mdl-radio mdl-js-radio mdl-js-ripple-effect" for="scale4">
                                <input type="radio" id="scale4" class="mdl-radio__button" name="scale" value="4">
                                <span class="mdl-radio__label">Anno</span>
                            </label>

                        </td>
                        <td id="filtri_td_right">
                            <button id="bottone_filtri_stato" class="mdl-button mdl-js-button mdl-js-ripple-effect">Stato</button>

                            <button id="bottone_help" class="mdl-button mdl-js-button mdl-button--icon">
                                <i class="material-icons">help</i>
                            </button>

                        </td>
                    </tr>
                </table>
            </div>
            
            <div id="gantt_div" class="chart_div"></div>
						
        </div>

        <div id="alert_offline" title="Offline" style="display: none; text-align: center;">
            <span style="text-align: center; font-weight: bold;">Attenzione: Connessione internet persa!</span>          	 
        </div>

        <div id="popup_filtri_stato" title="Filtri" style="display: none;">

            <div class="mdl-card mdl-shadow--2dp" style="width:100%; height: 100%;">
				
				<div id="filtri_stato" class="mdl-card__supporting-text">

                    <h5>Considera Situazione:</h5>
                                    
                    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="check_in_scadenza">
                        <input type="checkbox" id="check_in_scadenza" name="In scadenza" class="mdl-checkbox__input" checked>
                        <span class="mdl-checkbox__label">In scadenza</span>
                    </label>

                    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="check_eseguire_entro">
                        <input type="checkbox" id="check_eseguire_entro" name="Eseguire entro" class="mdl-checkbox__input" >
                        <span class="mdl-checkbox__label">Eseguire entro</span>
                    </label>

                    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="check_scaduta">
                        <input type="checkbox" id="check_scaduta" name="Scaduta" class="mdl-checkbox__input" checked>
                        <span class="mdl-checkbox__label">Scaduta</span>
                    </label>

                    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="check_eseguita">
                        <input type="checkbox" id="check_eseguita" name="Eseguita" class="mdl-checkbox__input" >
                        <span class="mdl-checkbox__label">Eseguita</span>
                    </label>

                    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="check_in_corso_di_validita">
                        <input type="checkbox" id="check_in_corso_di_validita" name="In corso di validita" class="mdl-checkbox__input" >
                        <span class="mdl-checkbox__label">In corso di validita</span>
                    </label>

                    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="check_eseguito_corso_base">
                        <input type="checkbox" id="check_eseguito_corso_base" name="Eseguito corso base" class="mdl-checkbox__input" checked>
                        <span class="mdl-checkbox__label">Eseguito corso base</span>
                    </label>

                    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="check_non_eseguita">
                        <input type="checkbox" id="check_non_eseguita" name="Non eseguita" class="mdl-checkbox__input" checked>
                        <span class="mdl-checkbox__label">Non eseguita</span>
                    </label>
					
					<label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="check_non_eseguito_corso_base">
                        <input type="checkbox" id="check_non_eseguito_corso_base" name="Non eseguito corso base" class="mdl-checkbox__input" checked>
                        <span class="mdl-checkbox__label">Non eseguito corso base</span>
                    </label>
					
					<label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="check_non_eseguita_form_prec">
                        <input type="checkbox" id="check_non_eseguita_form_prec" name="Non eseguita formazione precedente" class="mdl-checkbox__input" checked>
                        <span class="mdl-checkbox__label">Non eseguita formazione precedente</span>
                    </label>

                    <label class="mdl-checkbox mdl-js-checkbox mdl-js-ripple-effect" for="check_valida_senza_scadenza">
                        <input type="checkbox" id="check_valida_senza_scadenza" name="Valida senza scadenza" class="mdl-checkbox__input">
                        <span class="mdl-checkbox__label">Valida senza scadenza</span>
                    </label>
				
				</div>
            
            </div>
                    	 
        </div>

        <div id="popup_help" title="Help" style="display: none;">

            <div class="mdl-card mdl-shadow--2dp" style="width:100%; height: 100%;">
				
                <h5>Legenda Stati:</h5>

                <table style="width: 100%;">

                    <tr>

                        <td style="width: 40px;">
                            <div class="overdue_indicator">!</div>
                        </td>

                        <td>
                            <span>Non eseguita, Scaduta, Non eseguito corso base, Non eseguita formazione precedente</span>
                        </td>

                    </tr>

                    <tr>

                        <td>
                            <div class="overdue_indicator_in_scadenza">!</div>
                        </td>

                        <td>
                            <span>In scadenza</span>
                        </td>

                    </tr>

                    <tr>

                        <td>
                            <div class="indicator_eseguita"></div>
                        </td>

                        <td>
                            <span>Eseguita, Eseguito corso base, Valida senza scadenza, In corso di validita', Eseguire entro</span>
                        </td>

                    </tr>

                </table>                    
                   
            </div>
                    	 
        </div>
        
	</body>
</html>
