<?php

/* kpro@tom190216 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 */
 
require_once(__DIR__.'/KproConfig.ini.php');
 
include_once(__DIR__.'/../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
require_once("modules/PDFMaker/InventoryPDF.php");
require_once("include/mpdf/mpdf.php"); 
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $default_charset, $site_URL, $root_directory;

global $path_cartella_firme;

session_start();

$rows = array();

if(isset($_POST['imagedata']) && isset($_POST['crmid'])){
    $imagedata= htmlspecialchars(addslashes(html_entity_decode(strip_tags($_POST['imagedata']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
	
    $crmid = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_POST['crmid']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $crmid = substr($crmid,0,100);

    $filename = $root_directory.$path_cartella_firme.$crmid."_jqScribbleImage.png";

    if(file_exists($filename)){ 
        $file_link_firma = unlink($site_URL."/".$path_cartella_firme.$crmid."_jqScribbleImage.png");
    } 

    $imagedata = substr($imagedata, strpos($imagedata, ",")+1);
    $imagedata = base64_decode($imagedata);
    $imgRes = imagecreatefromstring($imagedata);
    
    $firma = "";

    if($imgRes !== false && imagepng($imgRes, $filename) === true){
        
        $file_firma = $root_directory.$path_cartella_firme.$crmid."_jqScribbleImage.png";
    
        if(file_exists($file_firma)){ 
            $file_link_firma = $site_URL."/".$path_cartella_firme.$crmid."_jqScribbleImage.png";
            $firma = "<img src='".$file_link_firma."' style='max-width: 100%; float: left; max-height: 150px;'/>";
        }
        
    }
    
    $rows[] = array('firma' => $firma);

}

$json = json_encode($rows);
print $json;
	
?>
