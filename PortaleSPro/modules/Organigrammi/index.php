<!DOCTYPE html>

<?php

/* kpro@tom20170628 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

require_once("kp.php");

?>

<html>
    <head>
        <title>Portale SPro</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
        
        <link rel="icon" type="image/png" href="../../img/S-PRO-FAVICON.ico">

		<script src="../../js/jquery-2.1.4.min.js"></script>  

		<link rel="stylesheet" type="text/css" href="../../css/style_index.css">

		<link rel="stylesheet" type="text/css" href="../../codebase/fonts/font_roboto/roboto.css"/>
		<link rel="stylesheet" type="text/css" href="../../codebase/dhtmlx.css"/>
		<script src="../../codebase/dhtmlx.js"></script>

		<script src="../../js/material.min.js"></script>

		<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link rel="stylesheet" href="../../css/material.min.css">
		<link rel="stylesheet" href="css/style.css">

		<link rel="stylesheet" type="text/css" href="../../../modules/SproCore/CustomViews/KpOrganigrammaCreator/dhtmlx_organigrammi/diagram.css"/>
		<script src="../../../modules/SproCore/CustomViews/KpOrganigrammaCreator/dhtmlx_organigrammi/diagram.js"></script>

		<link rel="stylesheet" type="text/css" href="../../../themes/spro//vte_bootstrap.css">
		<link rel="stylesheet" type="text/css" href="../../../themes/spro//ripples.min.css">
		<link rel="stylesheet" type="text/css" href="../../../themes/spro//datetimepicker.css">
		<link rel="stylesheet" type="text/css" href="../../../themes/spro//style.css">

		<script language="JavaScript" type="text/javascript" src="../../../themes/spro//js/bootstrap/bootstrap.min.js"></script>
		<script language="JavaScript" type="text/javascript" src="../../../themes/spro//js/bootstrap/bs_conflicts.js"></script>
		<script language="JavaScript" type="text/javascript" src="../../../themes/spro//js/arrive.min.js"></script>
		<script language="JavaScript" type="text/javascript" src="../../../themes/spro//js/moment-l18n.js"></script>
		<script language="JavaScript" type="text/javascript" src="../../../themes/spro//js/material/ripples.js"></script>
		<script language="JavaScript" type="text/javascript" src="../../../themes/spro//js/material/material.js"></script>
		<script language="JavaScript" type="text/javascript" src="../../../themes/spro//js/material/datetimepicker.js"></script>
		<script language="JavaScript" type="text/javascript" src="../../../themes/spro//js/jquery.dropdown.js"></script>
		<script language="JavaScript" type="text/javascript" src="../../../themes/spro//js/select2/select2.min.js"></script>
		<script language="JavaScript" type="text/javascript" src="../../../themes/spro//js/select2/i18n/it.js"></script>
		<script language="JavaScript" type="text/javascript" src="../../../themes/spro//js/select2/i18n/en.js"></script>
		<script language="JavaScript" type="text/javascript" src="../../../themes/spro//index.js"></script>

		<script src="js/htmlEntities.js"></script>
		<script src="js/general.js"></script>

        <script type="text/JavaScript">
			
			//Traduzioni
            var string_salva = "<?php echo($string_salva); ?>";
            var string_chiudi = "<?php echo($string_chiudi); ?>";
            var string_prosegui = "<?php echo($string_prosegui); ?>";
            var string_annulla = "<?php echo($string_annulla); ?>";
            var string_termina = "<?php echo($string_termina); ?>";
            var string_documenti = "<?php echo($string_documenti); ?>";            
            //Traduzioni end
            
            var contact_id = "<?php echo($contact_id); ?>";
            var aziendaid = "<?php echo($azienda); ?>";
            var accountname = "<?php echo($accountname); ?>";
            var indirizzo_crm = "<?php echo($site_URL); ?>";
            var default_language = "<?php echo($default_language); ?>";
			
        </script>   
    </head>
    <body>

		<div class="mdl-layout mdl-js-layout mdl-layout--fixed-header mdl-layout--fixed-tabs">
			<header class="mdl-layout__header">
				
				<!-- title -->
				<div class="mdl-layout__header-row" style="width:100%; margin: 0px; padding-left: 10px; padding-right: 0px">
					
					<table id="table_header">
						<tr>
							<td id="td_menu"></td>
							<td id="td_titolo_pagina"><span id="titolo_pagina"></span></td>
							<td id="td_caricamento_pagina" style="text-align: center;"><div class="caricamento mdl-spinner mdl-js-spinner is-active" style="display: none;"></div></td>
							<td style="text-align: right; vertical-align: middle; padding-right: 10px">
								<image src="../../img/S-PRO-LOGO-HEADER-BLUE.png" style="max-widh:30px; max-height:30px" />
							</td>
							<td style="text-align: right; vertical-align: middle; padding-right: 10px; width: 100px;">
								<a href="../../../index.php?visualization_type=resp_linea&module=Users&action=Logout" id="button_logout" class="menu_head_button" title="Esci"><image style="max-widh:30px; max-height:30px" id="logout" src='../../img/logout.png' /></a>
							</td>
						</tr>
					</table>
					
				</div>
				<!-- title end -->
			
				<!-- tabs -->
				<!--<div class="mdl-layout__tab-bar mdl-js-ripple-effect">
					<a href="#fixed-tab-1" class="mdl-layout__tab is-active">Tab 1</a>
					<a href="#fixed-tab-2" class="mdl-layout__tab">Tab 2</a>
					<a href="#fixed-tab-3" class="mdl-layout__tab">Tab 3</a>
				</div>-->
				<!-- tabs end -->
			
			</header>

			<!-- navigation-bar -->

			<?php include($portal_name.'/modules/navbar.php'); ?>
			
			<!-- navigation-bar end -->
			
			<main class="mdl-layout__content">

				<div id="layoutObj" style="margin-top: 10px; height: 100%"></div>

				<div id="graphContainer" style="display: none; height: 100%;">

					<button type="button" style="position: absolute !important; top: 0px; right: 0px; z-index: 99;" id="bottone_zoom_piu" class="btn btn-default btn-sm">
						<span class="glyphicon glyphicon-plus"></span> 
					</button>

					<button type="button" style="position: absolute !important; top: 0px; left: 0px; z-index: 99;" id="bottone_zoom_meno" class="btn btn-default btn-sm">
						<span class="glyphicon glyphicon-minus"></span> 
					</button>

				</div>

				<div id="dettagliContainer" style="display: none; height: 100%; padding: 0px !important;"></div>

				<div id="popup_generico" class="modal fade" role="dialog" data-backdrop="true" data-keyboard="true"></div>

				<!-- Popup -->

				<!--<div id="alert_offline" class="modal" style="background-color: #ffcc00; vertical-align: middle; text-align: center; color: red; font-weight: bold;">
					<div class="modal-content">
						<span>Attenzione: Connessione Internet persa!</span>  
					</div>
				</div>-->
				
				<!-- Popup end -->
			</main>

		</div>

	</body>

</html>
