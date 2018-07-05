<?php

/* kpro@tom12122016 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 */

include_once('../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $current_user, $adb, $table_prefix;
session_start();

if(!isset($_SESSION['authenticated_user_id'])){
    header("Location: ".$site_URL."/index.php");
}
$current_user->id = $_SESSION['authenticated_user_id'];

$utente = $current_user->id;

$utente_demo = "false";
$lista_utenti_demo = array(8, 23, 25);
if(in_array($utente, $lista_utenti_demo)){
    $utente_demo = "true";
}

$query = "SELECT 
            is_admin 
            FROM vte_users 
            WHERE id = ".$utente;
            
$result_query = $adb->query($query);
$num_result = $adb->num_rows($result_query);

if($num_result > 0){

    $is_admin = $adb->query_result($result_query, 0, 'is_admin');

    if( $is_admin == 'on' || $is_admin == '1' || $is_admin == true ){
        $is_admin = true;
    }
    else{
        $is_admin = false;
    }

}
else{

    $is_admin = false;

}

$result = array('utente' => $current_user->id,
                'is_admin' => $is_admin,
                'utente_demo' => $utente_demo);

$json = json_encode($result);
	
print $json;
    
?>
	
