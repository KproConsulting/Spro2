<?php

/* kpro@bid28092018 */

echo("Togliere il die!"); die;

include_once('../../config.inc.php');
chdir($root_directory);
include_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
require_once('modules/SproCore/SDK/KpSDK.php'); 
$Vtiger_Utils_Log = true;
global $adb, $table_prefix;
session_start();

echo '<br>INIZIO AGGIUNTA WIDGET MANCANTI<br>';

$moduli = array();
$calendario = false;
$messaggi = false;

$q_moduli = "SELECT name as nome_modulo
        FROM {$table_prefix}_tab
        WHERE isentitytype = 1
        AND version LIKE '%kp%'
        AND tabid NOT IN (
        SELECT tabid
        FROM {$table_prefix}_links
        WHERE linklabel IN ('DetailViewMyNotesWidget','DetailViewBlockCommentWidget')
        GROUP BY tabid)";
$res_moduli = $adb->query($q_moduli);
$num_moduli = $adb->num_rows($res_moduli);
for($i = 0; $i < $num_moduli; $i++){
    $nome_modulo = $adb->query_result($res_moduli, $i, 'nome_modulo');
    $nome_modulo = html_entity_decode(strip_tags($nome_modulo), ENT_QUOTES, $default_charset);

    $moduli[] = $nome_modulo;
}

//$moduli = array('TipiCorso');

foreach($moduli as $nome_modulo){
    echo '- '.$nome_modulo.'<br>';

    $tabid_modulo = KpSDK::getModuloTabid($nome_modulo);
    if($tabid_modulo != 0){

        echo '--- ModComments: ';
        $ModCommentsModuleInstance = Vtiger_Module::getInstance('ModComments');
        if ($ModCommentsModuleInstance) {
            $ModCommentsFocus = CRMEntity::getInstance('ModComments');
            $ModCommentsFocus->addWidgetTo($nome_modulo);
        }

        echo '<br>--- ChangeLog: ';
        $ChangeLogModuleInstance = Vtiger_Module::getInstance('ChangeLog');
        if ($ChangeLogModuleInstance) {
            $ChangeLogFocus = CRMEntity::getInstance('ChangeLog');
            $ChangeLogFocus->enableWidget($nome_modulo);
        }

        echo '<br>--- ModNotifications: ';
        $ModNotificationsModuleInstance = Vtiger_Module::getInstance('ModNotifications');
        if ($ModNotificationsModuleInstance) {
            $ModNotificationsCommonFocus = CRMEntity::getInstance('ModNotifications');
            $ModNotificationsCommonFocus->addWidgetTo($nome_modulo);
        }

        echo '<br>--- MyNotes: ';
        $MyNotesModuleInstance = Vtiger_Module::getInstance('MyNotes');
        if ($MyNotesModuleInstance) {
            $MyNotesCommonFocus = CRMEntity::getInstance('MyNotes');
            $MyNotesCommonFocus->addWidgetTo($nome_modulo);
        }

        if( $calendario ){
            echo '<br>--- Calendar: ';
            KpSDK::registraRelated($nome_modulo, "Calendar", "Activities", array("ADD", "SELECT"), "get_activities");
            KpSDK::registraModuloNelCalendario($nome_modulo);
        }

        if( $messaggi ){
            echo '<br>--- Messages: ';
            KpSDK::registraRelated($nome_modulo, "Messages", "Messages", array("ADD"), "get_messages_list");
        }

        echo '<br>';
    }

    echo '<br>';
}

echo '<br>FINE AGGIUNTA WIDGET MANCANTI<br>';
