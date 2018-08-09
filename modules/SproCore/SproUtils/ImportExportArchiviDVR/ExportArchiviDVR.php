<?php

/* kpro@tom07082018 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2087, Kpro Consulting Srl
 */

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

require_once(__DIR__.'/KpMigrazioneDati.php');

$export = new KpMigrazioneDati();

$lista_moduli = array();
$lista_moduli[] = "KpRischiDVR";
$lista_moduli[] = "KpTipiMisureRiduttive";
$lista_moduli[] = "KpAttivitaDVR";
$lista_moduli[] = "KpTipologieImpianti";
$lista_moduli[] = "KpSostanzeChimiche";
$lista_moduli[] = "KpMaterialiUtilizzo";

$export->setModuliExport($lista_moduli);
$export->setPathExport(__DIR__);
$export->setDbName("archivi_dvr.db");
$export->runExport();

//$export->getRecordTabellaPricipale("KpRischiDVR");


?>