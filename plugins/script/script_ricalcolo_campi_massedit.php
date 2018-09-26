<?php

/* kpro@bid18092018 */

echo("Togliere il die!"); die;

include_once('../../config.inc.php');
chdir($root_directory);
include_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix;
session_start();

echo '<b>INIZIO PULIZIA CAMPI MASSEDIT<br>';

//Toglie dal massedit tutti i campi nascosti o in sola lettura che sono attivi nel massedit
$q = "UPDATE {$table_prefix}_field
    SET masseditable = 0
    WHERE readonly IN (99,100)
    AND masseditable = 1
    AND tablename NOT LIKE '{$table_prefix}_crmentity'
    AND tablename NOT LIKE '{$table_prefix}_employees'
    AND tablename NOT LIKE '{$table_prefix}_users'
    AND tablename NOT LIKE '{$table_prefix}_myfiles'";
$adb->query($q);

//Aggiunge il massedit per tutti i campi con uitype standard che non nascosti e in sola lettura che non sono attivi nel massedit
$q = "UPDATE {$table_prefix}_field
    SET masseditable = 1
    WHERE readonly NOT IN (99,100)
    AND masseditable = 0
    AND uitype IN (1,5,7,10,15,1015,19,21,56,71)
    AND tablename NOT LIKE '%comments'
    AND tablename NOT LIKE '{$table_prefix}_crmentity'
    AND tablename NOT LIKE '{$table_prefix}_employees'
    AND tablename NOT LIKE '{$table_prefix}_users'
    AND tablename NOT LIKE '{$table_prefix}_myfiles'
    AND tablename NOT LIKE '{$table_prefix}_changelog'
    AND tablename NOT LIKE '{$table_prefix}_invoice_recurring_info'
    AND tablename NOT LIKE '{$table_prefix}_messages'
    AND tablename NOT LIKE '{$table_prefix}_emaildetails'
    AND tablename NOT LIKE '{$table_prefix}_modnotifications'
    AND tablename NOT LIKE '{$table_prefix}_processes'";
$adb->query($q);

echo '<b>FINE PULIZIA CAMPI MASSEDIT<br>';
