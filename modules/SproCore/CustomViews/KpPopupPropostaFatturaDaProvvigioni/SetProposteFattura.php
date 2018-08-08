<?php

/* kpro@tom07092017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

require_once('modules/SproCore/KpProvvigioni/ClassKpProvvigioniKp.php');

$current_user->id = $_SESSION['authenticated_user_id'];

$rows = array();

if(isset($_POST['ids'])){
    $ids = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_POST['ids']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	
	$lista_provvigioni = explode(',', $ids);

	KpProvvigioniKp::setProposteFatturaDaLista($lista_provvigioni);

}

$json = json_encode($rows);
print $json;

?>