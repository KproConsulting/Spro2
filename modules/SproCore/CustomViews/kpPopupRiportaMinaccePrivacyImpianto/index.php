<!DOCTYPE html>

<?php

/* kpro@tom28112017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL;
global $small_page_title;
session_start();

require_once('modules/SproCore/CustomViews/kpPopupRiportaMinaccePrivacyImpianto/KpRiportaMinaccePrivacyImpianto.php');

if( isset($_REQUEST['id']) ){
	$id = $_REQUEST['id'];
}
else{
	die;
}

?>

<html>
    <head>
        <title></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        
        <link rel="stylesheet" type="text/css" href="modules/SproCore/CustomViews/kpPopupRiportaMinaccePrivacyImpianto/css/style.css">
        <script src="include/js/jquery.js"></script> 
        <script src="modules/SproCore/CustomViews/kpPopupRiportaMinaccePrivacyImpianto/js/htmlEntities.js"></script>
        <script src="modules/SproCore/CustomViews/kpPopupRiportaMinaccePrivacyImpianto/js/general.js"></script>
		
        <script type="text/JavaScript">
            
			var utente = "<?php echo($current_user->id); ?>";
			var record_id = "<?php echo($id); ?>";

        </script>
    </head>
    <body>
		
		<header>
            <?php 
            $small_page_title = "<span style='color:black; font-weight:bold;'>Seleziona l'impianto da cui si desidera copiare le minacce privacy collegate</span>";
            include('themes/SmallHeader.php'); ?>
        </header>
        
        <section id="DivForm">
			
			<div class="card" style="width: 98%; margin-rigt: 1%; margin-left: 1%;">

                <table style='width: 100%; margin: 0px;' class='table table-striped'>
                    <thead>
                        <tr>

                            <th style='width: 80px;'></th>

                            <th>
                                <div class='form-group'>
                                    <label for='search_matricola'>Matricola</label>
                                    <input type='text' class='form-control' id='search_matricola' >
                                </div>
                            </th>

                            <th>
                                <div class='form-group'>
                                    <label for='search_nome_impianto'>Nome Impianto</label>
                                    <input type='text' class='form-control' id='search_nome_impianto' >
                                </div>
                            </th>

                            <th>
                                <div class='form-group'>
                                    <label for='search_azienda'>Azienda</label>
                                    <input type='text' class='form-control' id='search_azienda' >
                                </div>
                            </th>

                            <th>
                                <div class='form-group'>
                                    <label for='search_stabilimento'>Stabilimento</label>
                                    <input type='text' class='form-control' id='search_stabilimento' >
                                </div>
                            </th>

                            <th style='width: 100px;'>
                                <div class='form-group'>
                                    <label for='readonly_numero_minacce'>Nr. Minacce</label>
                                    <input type='text' class='form-control' id='readonly_numero_minacce' readonly disables >
                                </div>
                            </th>

                        </tr>
                    </thead>

                    <tbody id="tabella_lista_impianti">
                    </tbody>

                </table>
				
			</div>
			
		</section>
		
    </body>
</html>
