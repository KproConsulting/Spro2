<!DOCTYPE html>

<?php

/* kpro@tom07092016 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 */
 
require_once(__DIR__.'/KproConfig.ini.php');

include_once(__DIR__.'/../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_language;

global $template_mail_id; 

session_start();

//print_r($_SESSION);die;

if(!isset($_SESSION['authenticated_user_id'])){
	
    header("Location: ".$site_URL."/index.php?visualization_type=".$visualization_type);
    
}
$current_user->id = $_SESSION['authenticated_user_id'];

$default_language = $_SESSION[authenticated_user_language];
require_once('string/'.$default_language.'.php');

if(isset($_REQUEST['record'])){
    $record = $_REQUEST['record'];
}
else{
    die;
}

$return_module = "";
if(isset($_REQUEST['return_module'])){
    $return_module = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_REQUEST['return_module']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $return_module = substr($return_module,0,100);
}
else{
    $return_module = '';
}

$data_corrente = date("Y-m-d");
list($anno_corrente, $mese_corrente, $giorno_corrente) = explode("-",$data_corrente);
$data_corrente_inv = date("d/m/Y", mktime(0, 0, 0, $mese_corrente, $giorno_corrente, $anno_corrente));

?>

<html>
    <head>
        <title>Portale Firma Intervento</title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="../../../../themes/logos/VTE_favicon.ico" /> <!--Per il VTE Sicurezza-->
		<!--<link rel="shortcut icon" href="img/VTE_favicon.ico" />--> <!--Per il VTE-->

		<script src="js/jquery-2.1.4.min.js"></script>  
		<script src="js/jquery-ui.min.js"></script>
		<script type="text/javascript" src="js/materialize.min.js"></script>
		<script src="js/material.min.js"></script>
        <script src="js/general_mail.js"></script>

		<link rel="stylesheet" type="text/css" href="codebase/fonts/font_roboto/roboto.css"/>
		<link rel="stylesheet" type="text/css" href="codebase/dhtmlx_material.css"/>
        <script src="codebase/dhtmlx.js"></script>
		
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
		<link type="text/css" rel="stylesheet" href="css/materialize.min.css"  media="screen,projection"/>
		<link rel="stylesheet" href="css/material.min.css">
		<link rel="stylesheet" type="text/css" href="css/style.css">

		<link rel="stylesheet" type="text/css" href="css/jquery.datetimepicker.css">
        <script src="js/jquery.datetimepicker.full.js"></script>
		
        <script type="text/JavaScript">
			
            //Traduzioni
            var string_salva = "<?php echo($string_salva); ?>";
            var string_chiudi = "<?php echo($string_chiudi); ?>";
            var string_prosegui = "<?php echo($string_prosegui); ?>";
            var string_annulla = "<?php echo($string_annulla); ?>";
            var string_termina = "<?php echo($string_termina); ?>";
            
            //Traduzioni end
            
            var indirizzo_crm = "<?php echo($site_URL); ?>";
            var record = "<?php echo($record); ?>"
            var utente = "<?php echo($current_user->id); ?>"
            var return_module = "<?php echo($return_module); ?>";
			
			var template_mail_id = "<?php echo($template_mail_id); ?>";
			
        </script>   
    </head>
    <body>
        <div id="general">

			<!-- sidebar -->
			
            <div id="nav_div">
                <div id="div_torna_indietro">
                    <table id="table_torna_indietro">
                        <tr id="tr_torna_a_precedente">
                            <td class="td_list_img_header">
                                <a data-position="bottom" data-delay="50" data-tooltip="Torna a precedente" class='torna_a_precedente tooltipped'><image class='immagine_indietro' src='img/indietro.png' /></a>
                            </td>
                            <td class="td_list_name_header">
                                <span id="torna_indietro">Indietro</span>
                            </td>
                        </tr>
                        <tr>
                            <td colspan = '2' class="td_logo" id="div_img_program">
                                <image style="max-widh:150px; max-height:150px" src="img/etta_150_150.png" />
                            </td>
                        </tr>
                    </table>
                </div> 
                
                <!--<div id="div_rilevazione_tempi">
                    <table>
                        <tr>
                            <td>
                                <a data-position="bottom" data-delay="50" data-tooltip="Pausa" class="btn-floating waves-effect waves-light tooltipped yellow" id="pausa_tempo"><i class="material-icons">pause</i></a>
                            </td>
                            <td>
                                <a data-position="bottom" data-delay="50" data-tooltip="Start" class="btn-floating waves-effect waves-light tooltipped green" id="start_tempo"><i class="material-icons">play_arrow</i></a>
                            </td>
                            <td>
                                <a data-position="bottom" data-delay="50" data-tooltip="Stop" class="btn-floating waves-effect medium waves-light tooltipped red" id="stop_tempo"><i class="material-icons">stop</i></a>
                            </td>
                        </tr>
                    </table>
                </div>-->   
                
                <!-- pulsanti laterali -->
                <ul class="list">
					
                    <li id="button_page1" name="Destinatari">
						<table>
							<tr>
								<td class="td_list_img">
									<image src="img/destinatari_40_40.png" />
									<a id="number_destinatari" class="menu_list_number waves-effect waves-circle waves-light btn-floating secondary-content red" style="display: none;"></a>
								</td>
								<td class="td_list_name">
									Destinatari
								</td>
							</tr>
						</table>
					</li>

					<li id="button_page2" name="Testo">
						<table>
							<tr>
								<td class="td_list_img">
									<image src="img/email_40_40.png" />
									<a class="menu_list_number waves-effect waves-circle waves-light btn-floating secondary-content red" style="display: none;"></a>
								</td>
								<td class="td_list_name">
									Testo
								</td>
							</tr>
						</table>
					</li>

					<li id="button_page3" name="Allegati">
						<table>
							<tr>
								<td class="td_list_img">
									<image src="img/allegati_40_40.png" />
									<a id="number_allegati" class="menu_list_number waves-effect waves-circle waves-light btn-floating secondary-content red" style="display: none;"></a>
								</td>
								<td class="td_list_name">
									Allegati
								</td>
							</tr>
						</table>
					</li>

                </ul>
                <!-- pulsanti laterali end-->
                
            </div>
            
            <!-- sidebar end -->

            <div id="content"> 
				
				<!-- header -->
				
                <div id="header_titolo">
                    <table id="table_header">
                        <tr>
                            <td id="td_menu_button"><button type="button" id="menu_button"><image src="img/menu_blue.png" /></button></td>
                            <td id="td_titolo_pagina"><span id="titolo_pagina"></span></td>
                            <td id="td_clock"><span id="clock"></span></td>
                        </tr>
                    </table>
                </div>
                
                <!-- header end -->
                
                <!-- panel -->

                <div class="panel" id="page1" data-footer="none" selected="true">    
					
					<table style="width: 100%; margin: auto;">
						<tr>
							<td style="width: 40%;">
								
							</td>
							<td style="text-align: center; width: 20%;">
								
								<!-- Disco di caricamento -->
								<div class="caricamento preloader-wrapper small active" style="display: none;">
									<div class="spinner-layer spinner-yellow-only">
										<div class="circle-clipper left">
											<div class="circle"></div>
										</div>
										<div class="gap-patch">
											<div class="circle"></div>
										</div>
										<div class="circle-clipper right">
											<div class="circle"></div>
										</div>
									</div>
								</div>
								<!-- Disco di caricamento end -->
								
							</td>
							
							<!-- Tasti modifica/ salva -->
							
							<td style="text-align: right; width: 40%;">

								<a class="waves-effect waves-light btn green right" id="bottone_invia_mail"><i class="material-icons left">send</i>Invia Mail</a>

							</td>
							
							<!-- Tasti modifica/ salva end -->
							
						</tr>
					</table>

					<div class="col s12">
						<ul class="tabs" id="tabs_page1">
							<li class="tab col s6" id="tab_lista_destinatari"><a class="active" href="#pag_lista_destinatari">Lista Destinatari</a></li>
							<li class="tab col s6" id="tab_lista_contatti"><a href="#pag_lista_contatti">Lista Contatti</a></li>
						</ul>
					</div>
					
					<div id="pag_lista_destinatari" class="col s12">
						<div class="card">
							
							<table class="striped" style="width: 100%;">
								<thead>	
									<tr>
										<th style="width: 80px; text-align: center;">
										</th>
										<th>
											<span>Cognome</span>
										</th>
										<th>
											<span>Nome</span>
										</th>
										<th>
											<span>Azienda</span>
										</th>
										<th>
											<span>Email</span>
										</th>

									</tr>
								</thead>	
								
								<tbody id="body_tabella_destinatari">
									<tr><td colspan='5' style='text-align: center;'><em>Nessun destinatario trovato!</em></td></tr>
								</tbody>
								
							</table>	
							
						</div>

						<div id="bottone_aggiungi_destinatario" class="fixed-action-btn" style="bottom: 30px; right: 30px;">
							<a class="btn-floating btn-large waves-effect waves-light green">
								<i class="large material-icons">person_add</i>
							</a>
						</div>

					</div>
					
					<div id="pag_lista_contatti" class="col s12">
						
						<div class="card">
							
							<table class="striped" style="width: 100%;">
								<thead>	
									<tr>
										<th style="width: 80px; text-align: center;">
										</th>
										<th>
											<div class="input-field col s12">
												<input id="search_cognome_contatto" type="text" placeholder="Cognome">
												<label for="search_cognome_contatto">Cognome</label>
											</div>
										</th>
										<th>
											<div class="input-field col s12">
												<input id="search_nome_contatto" type="text" placeholder="Nome">
												<label for="search_nome_contatto">Nome</label>
											</div>
										</th>
										<th>
											<div class="input-field col s12">
												<input id="search_azienda_contatto" type="text" placeholder="Azienda">
												<label for="search_azienda_contatto">Azienda</label>
											</div>
										</th>
										<th>
											<div class="input-field col s12">
												<input id="search_email_contatto" type="text" placeholder="Email">
												<label for="search_email_contatto">Email</label>
											</div>
										</th>

									</tr>
								</thead>	
								
								<tbody id="body_tabella_contatti">
									<tr><td colspan='6' style='text-align: center;'><em>Nessun contatto trovata!</em></td></tr>
								</tbody>
								
							</table>
							
						</div>

						<div id="bottone_ritorna_lista_destinatari" class="fixed-action-btn" style="bottom: 30px; right: 30px;">
							<a class="btn-floating btn-large waves-effect waves-light amber">
								<i class="large material-icons">arrow_back</i>
							</a>
						</div>

					</div>
					         
                </div>

				<div class="panel" id="page2" data-footer="none">

					<table style="width: 100%;">
						<tr>
							<td style="width: 40%;">
								<h4 style="margin: 0px;">Dettagli Email</h4>
							</td>
							<td style="text-align: center; width: 20%;">
								
								<!-- Disco di caricamento -->
								<div class="caricamento preloader-wrapper small active" style="display: none;">
									<div class="spinner-layer spinner-yellow-only">
										<div class="circle-clipper left">
											<div class="circle"></div>
										</div>
										<div class="gap-patch">
											<div class="circle"></div>
										</div>
										<div class="circle-clipper right">
											<div class="circle"></div>
										</div>
									</div>
								</div>
								<!-- Disco di caricamento end -->
								
							</td>
							
							<td style="text-align: right; width: 40%;">

							</td>
							
						</tr>
					</table>   

					<div class="card">

						<div class="row">

							<div class="input-field col s12">
								<i class="material-icons prefix">email</i>
								<input id="form_oggetto_mail" type="text">
								<label for="form_oggetto_mail">Oggetto Mail</label>
							</div>

							<div class="row">
								<div class="input-field col s12">
									<i class="material-icons prefix">mode_edit</i>
									<textarea id="form_testo_mail" class="materialize-textarea"></textarea>
									<label for="form_testo_mail">Testo Mail</label>
								</div>
							</div>

						</div>

						<div style="display: none;">
                            <span id="template_default"></span>
						</div>

					</div>

				</div>

				<div class="panel" id="page3" data-footer="none">      

					<table style="width: 100%; margin: auto;">
						<tr>
							<td style="width: 40%;">
								
							</td>
							<td style="text-align: center; width: 20%;">
								
								<!-- Disco di caricamento -->
								<div class="caricamento preloader-wrapper small active" style="display: none;">
									<div class="spinner-layer spinner-yellow-only">
										<div class="circle-clipper left">
											<div class="circle"></div>
										</div>
										<div class="gap-patch">
											<div class="circle"></div>
										</div>
										<div class="circle-clipper right">
											<div class="circle"></div>
										</div>
									</div>
								</div>
								<!-- Disco di caricamento end -->
								
							</td>
							
							<!-- Tasti modifica/ salva -->
							
							<td style="text-align: right; width: 40%;">

							</td>
							
							<!-- Tasti modifica/ salva end -->
							
						</tr>
					</table>

					<div class="col s12">
						<ul class="tabs" id="tabs_page3">
							<li class="tab col s6" id="tab_lista_allegati"><a class="active" href="#pag_lista_allegati">Lista Allegati</a></li>
							<li class="tab col s6" id="tab_lista_documenti"><a href="#pag_lista_documenti">Lista Documenti</a></li>
						</ul>
					</div>
					
					<div id="pag_lista_allegati" class="col s12">
						<div class="card">
							
							<table class="striped" style="width: 100%;">
								<thead>	
									<tr>
									
										<th style="width: 80px; text-align: center;">
										</th>
										<th>
											<span>Titolo</span>
										</th>
										<th style="width: 80px; text-align: center;">
										</th>

									</tr>
								</thead>	
								
								<tbody id="body_tabella_allegati">
									<tr><td colspan='5' style='text-align: center;'><em>Nessun allegato trovato!</em></td></tr>
								</tbody>
								
							</table>	
							
						</div>

						<div id="bottone_aggiungi_allegato" class="fixed-action-btn" style="bottom: 30px; right: 30px;">
							<a class="btn-floating btn-large waves-effect waves-light green">
								<i class="large material-icons">attach_file</i>
							</a>
						</div>

					</div>
					
					<div id="pag_lista_documenti" class="col s12">
						
						<div class="card">
							
							<table class="striped" style="width: 100%;">
								<thead>	
									<tr>
										<th style="width: 80px; text-align: center;">
										</th>
										<th>
											<span>Titolo</span>
										</th>

									</tr>
								</thead>	
								
								<tbody id="body_tabella_documenti">
									<tr><td colspan='6' style='text-align: center;'><em>Nessun documento trovato!</em></td></tr>
								</tbody>
								
							</table>
							
						</div>

						<div id="bottone_ritorna_lista_allegati" class="fixed-action-btn" style="bottom: 30px; right: 30px;">
							<a class="btn-floating btn-large waves-effect waves-light amber">
								<i class="large material-icons">arrow_back</i>
							</a>
						</div>

					</div>

				</div>
                
				<!-- panel end -->
               
            </div>
            
        </div>
        
        <!-- popup -->
        
        <div id="alert_offline" class="modal" style="background-color: #ffcc00; vertical-align: middle; text-align: center; color: red; font-weight: bold;">
			<div class="modal-content">
				<span>Attenzione: Connessione Internet persa!</span>  
			</div>
		</div>

		<div id="popup_salvataggio_in_corso" class="modal" style="vertical-align: middle; text-align: center; font-weight: bold;">
			<div class="modal-content">
				<span>Salvataggio in corso!</span>  
			</div>
		</div>

		<div id="progress_status" style="display: none;">0%</div>

		<!-- popup end -->
        
    </body>
</html>
