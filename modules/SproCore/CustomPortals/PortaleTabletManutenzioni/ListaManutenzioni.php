<?php

/* kpro@tom190216 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 * @package manutenzioni
 * @version 1.0
 */

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $default_charset;
session_start();

if(!isset($_SESSION['authenticated_user_id'])){
    header("Location: ".$site_URL."/index.php?visualization_type=manutenzioni");
}
$current_user->id = $_SESSION['authenticated_user_id'];

require('user_privileges/requireUserPrivileges.php'); 
require('user_privileges/sharing_privileges_'.$current_user->id.'.php');

$fieldlabel = 'Assigned To';
global $noof_group_rows;

$editview_label[]=getTranslatedString($fieldlabel, $module_name);

//Security Checks
if($fieldlabel == 'Assigned To' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
{
    $result=get_current_user_access_groups($module_name);
}
else
{
    $result = get_group_options();
}
if($result) $nameArray = $adb->fetch_array($result);

if($value != '' && $value != 0)
    $assigned_user_id = $value;
else
    $assigned_user_id = $current_user->id;
if($fieldlabel == 'Assigned To' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
{
    $users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id,'private'), $assigned_user_id);
}
else
{
    $users_combo = get_select_options_array(get_user_array(FALSE, "Active", $assigned_user_id), $assigned_user_id);
}
if($noof_group_rows!=0)
{
	$groups_combo = '';
    if($fieldlabel == 'Assigned To' && $is_admin==false && $profileGlobalPermission[2] == 1 && ($defaultOrgSharingPermission[getTabid($module_name)] == 3 or $defaultOrgSharingPermission[getTabid($module_name)] == 0))
    {
        $groups_combo = get_select_options_array(get_group_array(FALSE, "Active", $assigned_user_id,'private'), $assigned_user_id);
    }
    else
    {
        $groups_combo = get_select_options_array(get_group_array(FALSE, "Active", $assigned_user_id), $assigned_user_id);
    }
}

$fieldvalue[]= $users_combo;
$fieldvalue_group[] = $groups_combo;

$myArray = $fieldvalue[0];
$keys = array_keys($myArray);

if($groups_combo != ''){
	$myArray_group = $fieldvalue_group[0];
	$keys_group = array_keys($myArray_group);
}

$elementCount  = count($fieldvalue[0]) + count($fieldvalue_group[0]);
$elementCountUser  = count($fieldvalue[0]);
$newArray = array();

$lista_assegnatari = '(';
for($y=0; $y<$elementCountUser; $y++){
    $user_id = $keys[$y];
    $queryUsers = "SELECT id, user_name 'username', first_name, last_name FROM {$table_prefix}_users WHERE id = {$user_id}";
    $result = $adb->query($queryUsers);
    $id = $adb->query_result($result, 0, 'id');
    $userName = $adb->query_result($result, 0, 'username');
    $firstName = $adb->query_result($result, 0, 'first_name');
    $lastName = $adb->query_result($result, 0, 'last_name');
    $newArray[] = array('id' => $id,
                        'user_name' => $userName,
                        'first_name' => $firstName,
                        'last_name' => $lastName);
	
	if($lista_assegnatari == '('){
		$lista_assegnatari  .= $id;
	}
	else{
		$lista_assegnatari  .= ",".$id;
	}
	
}

$elementCountGroup  = count($fieldvalue_group[0]);

for($y=0; $y<$elementCountGroup; $y++){
	$group_id = $keys_group[$y];
	$queryGroup = "SELECT groupid, groupname, description FROM {$table_prefix}_groups WHERE groupid = {$group_id}";

	$result_group = $adb->query($queryGroup);
    $groupid = $adb->query_result($result_group, 0, 'groupid');
    $groupname = $adb->query_result($result_group, 0, 'groupname');
    $description = $adb->query_result($result_group, 0, 'description');

    $newArray[] = array('id' => $groupid,
                        'user_name' => 'Gruppo: '.$groupname,
                        'first_name' => $description,
                        'last_name' => "");
						
	if($lista_assegnatari == '('){
		$lista_assegnatari  .= $groupid;
	}
	else{
		$lista_assegnatari  .= ",".$groupid;
	}
	
}

$lista_assegnatari .= ')';

$lista_tecnici = "";

$q_tecnici = "SELECT t.* FROM
                ((SELECT 
                tec.tecnicimanutentoriid tecnicimanutentoriid,
                tec.cognome cognome,
                tec.kp_nome nome,
                tec.colore colore
                FROM {$table_prefix}_tecnicimanutentori tec
                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tec.tecnicimanutentoriid
                WHERE ent.deleted = 0 AND ent.smownerid IN ".$lista_assegnatari.")
                UNION
                (SELECT 
                tec2.tecnicimanutentoriid tecnicimanutentoriid, 
                tec2.cognome cognome, 
                tec2.kp_nome nome, 
                tec2.colore colore 
                FROM {$table_prefix}_tecnicimanutentori tec2
                INNER JOIN {$table_prefix}_users us ON us.kp_tecnico_man = tec2.tecnicimanutentoriid
                WHERE us.id = ".$current_user->id.")) AS t
                GROUP BY t.tecnicimanutentoriid";
                
$res_tecnici = $adb->query($q_tecnici);
$num_tecnici = $adb->num_rows($res_tecnici);
for($i=0; $i<$num_tecnici; $i++){
    
    $id = $adb->query_result($res_tecnici, $i, 'tecnicimanutentoriid');
    $id = html_entity_decode(strip_tags($id), ENT_QUOTES,$default_charset);
    
    $cognome = $adb->query_result($res_tecnici, $i, 'cognome');
    $cognome = html_entity_decode(strip_tags($cognome), ENT_QUOTES,$default_charset);
    
    $nome = $adb->query_result($res_tecnici, $i, 'nome');
    $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES,$default_charset);
    
    $colore = $adb->query_result($res_tecnici, $i, 'colore');
    $colore = html_entity_decode(strip_tags($colore), ENT_QUOTES,$default_charset);

    if($lista_tecnici == ""){
        $lista_tecnici = $id;
    }
    else{
        $lista_tecnici .= ",".$id;
    }
    
}

$q_utente = "SELECT 
                tec.tecnicimanutentoriid tecnicimanutentoriid,
                tec.kp_resp_man_extra resp_man_extra,
                tec.kp_resp_man_ord resp_man_ord
                FROM {$table_prefix}_users us
                INNER JOIN {$table_prefix}_tecnicimanutentori tec ON tec.tecnicimanutentoriid = us.kp_tecnico_man
                WHERE us.id = ".$current_user->id;

$res_utente = $adb->query($q_utente);
if($adb->num_rows($res_utente) > 0){
    
    $tecnicimanutentoriid = $adb->query_result($res_utente, 0, 'tecnicimanutentoriid');
    $tecnicimanutentoriid = html_entity_decode(strip_tags($tecnicimanutentoriid), ENT_QUOTES,$default_charset);
    
    $resp_man_extra = $adb->query_result($res_utente, 0, 'resp_man_extra');
    $resp_man_extra = html_entity_decode(strip_tags($resp_man_extra), ENT_QUOTES,$default_charset);
    if($resp_man_extra == 1){
        $resp_man_extra = "si";
    }
    else{
        $resp_man_extra = "no";
    }
    
    $resp_man_ord = $adb->query_result($res_utente, 0, 'resp_man_ord');
    $resp_man_ord = html_entity_decode(strip_tags($resp_man_ord), ENT_QUOTES,$default_charset);
    if($resp_man_ord == 1){
        $resp_man_ord = "si";
    }
    else{
        $resp_man_ord = "no";
    }
    
}

$data_corrente = date ("Y-m-d");
list($anno_corrente,$mese_corrente,$giorno_corrente) = explode("-",$data_corrente);
$data_corrente_inv = date("d-m-Y",mktime(0,0,0,$mese_corrente,$giorno_corrente,$anno_corrente));

if(isset($_GET['nome_manutenzione'])){
    $nome_manutenzione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['nome_manutenzione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $nome_manutenzione = substr($nome_manutenzione,0,255);
}
else{
    $nome_manutenzione = "";
}

if(isset($_GET['tipo_manutenzione'])){
    $tipo_manutenzione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['tipo_manutenzione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $tipo_manutenzione = substr($tipo_manutenzione,0,255);
}
else{
    $tipo_manutenzione = "";
}

if(isset($_GET['tecnico_incaricato'])){
    $tecnico_incaricato = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['tecnico_incaricato']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $tecnico_incaricato = substr($tecnico_incaricato,0,255);
}
else{
    $tecnico_incaricato = "";
}

if(isset($_GET['stato_manutenzione'])){
    $stato_manutenzione = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['stato_manutenzione']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $stato_manutenzione = substr($stato_manutenzione,0,255);
}
else{
    $stato_manutenzione = "";
}

if(isset($_GET['percentuale_fermo'])){
    $percentuale_fermo = htmlspecialchars(addslashes(html_entity_decode(strip_tags($_GET['percentuale_fermo']), ENT_QUOTES,$default_charset)), ENT_QUOTES,$default_charset);
    $percentuale_fermo = substr($percentuale_fermo,0,255);
    if($percentuale_fermo == ""){
		$percentuale_fermo = "default";
	}
}
else{
    $percentuale_fermo = "default";
}

$rows = array();

$q_manutenzioni = "SELECT t.* FROM
                    ((SELECT 
                    man.manutenzioniid manutenzioniid,
                    man.manutenzione_name manutenzione_name,
                    man.stato_manutenzione stato_manutenzione,
                    man.data_manutenzione data_manutenzione,
                    man.data_scad_manut data_scad_manut,
                    man.tipo_manutenzione tipo_manutenzione,
                    man.tempo_previsto tempo_previsto,
                    man.kp_tecnici_inc tecnici,
                    man.ora_inizio ora_inizio,
                    man.ora_fine ora_fine,
                    man.kp_problema_di_sic kp_problema_di_sic,
                    ent.smownerid assegnatario,
                    tec.tecnicimanutentoriid tecnicimanutentoriid,
                    tec.cognome cognome,
                    tec.kp_nome nome,
                    tec.colore colore
                    FROM {$table_prefix}_manutenzioni man
                    INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = man.manutenzioniid
                    LEFT JOIN {$table_prefix}_crmentityrel rel ON rel.crmid = man.manutenzioniid
                    LEFT JOIN {$table_prefix}_tecnicimanutentori tec ON tec.tecnicimanutentoriid = rel.relcrmid
                    LEFT JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = tec.tecnicimanutentoriid
                    WHERE ent.deleted = 0) 
                    UNION
                    (SELECT 
                    man2.manutenzioniid manutenzioniid,
                    man2.manutenzione_name manutenzione_name,
                    man2.stato_manutenzione stato_manutenzione,
                    man2.data_manutenzione data_manutenzione,
                    man2.data_scad_manut data_scad_manut,
                    man2.tipo_manutenzione tipo_manutenzione,
                    man2.tempo_previsto tempo_previsto,
                    man2.kp_tecnici_inc tecnici,
                    man2.ora_inizio ora_inizio,
                    man2.ora_fine ora_fine,
                    man2.kp_problema_di_sic kp_problema_di_sic,
                    ent3.smownerid assegnatario,
                    tec2.tecnicimanutentoriid tecnicimanutentoriid,
                    tec2.cognome cognome,
                    tec2.kp_nome nome,
                    tec2.colore colore
                    FROM {$table_prefix}_manutenzioni man2
                    INNER JOIN {$table_prefix}_crmentity ent3 ON ent3.crmid = man2.manutenzioniid
                    LEFT JOIN {$table_prefix}_crmentityrel rel2 ON rel2.crmid = man2.manutenzioniid
                    LEFT JOIN {$table_prefix}_tecnicimanutentori tec2 ON tec2.tecnicimanutentoriid = rel2.relcrmid
                    LEFT JOIN {$table_prefix}_crmentity ent4 ON ent4.crmid = tec2.tecnicimanutentoriid
                    WHERE ent3.deleted = 0)) AS t
                    WHERE t.stato_manutenzione IN ('Creata', 'Pianificata', 'In esecuzione') AND (t.tecnicimanutentoriid IN (".$lista_tecnici.") OR t.assegnatario IN ".$lista_assegnatari.")";
                 
if($nome_manutenzione != ""){
    $q_manutenzioni .= " AND t.manutenzione_name LIKE '%".$nome_manutenzione."%'";
}

if($tipo_manutenzione != "" && $tipo_manutenzione != "default"){
    $q_manutenzioni .= " AND t.tipo_manutenzione = '".$tipo_manutenzione."'";
}

if($tecnico_incaricato != ""){
    $q_manutenzioni .= " AND t.tecnici LIKE '%".$tecnico_incaricato."%'";
}

if($stato_manutenzione != "" && $stato_manutenzione != "default"){
    $q_manutenzioni .= " AND t.stato_manutenzione = '".$stato_manutenzione."'";
}

$q_manutenzioni .= " GROUP BY t.manutenzioniid
                    ORDER BY t.tipo_manutenzione ASC, t.data_manutenzione ASC, t.ora_inizio ASC";

$res_manutenzioni = $adb->query($q_manutenzioni);
$num_manutenzioni = $adb->num_rows($res_manutenzioni);
for($i=0; $i<$num_manutenzioni; $i++){
    $manutenzioniid = $adb->query_result($res_manutenzioni, $i, 'manutenzioniid');
    $manutenzioniid = html_entity_decode(strip_tags($manutenzioniid), ENT_QUOTES,$default_charset);
    
    $dati_impianto = impiantoManutenzione($manutenzioniid);
    $impiantoid = $dati_impianto['impianto'];
    $dati_fermo_impianto = fermiImpianto($impiantoid);
    $percentuale_fermo_impianto_desc = $dati_fermo_impianto['perc_fermo_imp_desc'];
    $percentuale_fermo_impianto = $dati_fermo_impianto['perc_fermo_imp'];

    $manutenzione_name = $adb->query_result($res_manutenzioni, $i, 'manutenzione_name');
    $manutenzione_name = html_entity_decode(strip_tags($manutenzione_name), ENT_QUOTES,$default_charset);

    $stato_manutenzione = $adb->query_result($res_manutenzioni, $i, 'stato_manutenzione');
    $stato_manutenzione = html_entity_decode(strip_tags($stato_manutenzione), ENT_QUOTES,$default_charset);

    $data_manutenzione= $adb->query_result($res_manutenzioni, $i, 'data_manutenzione');
    $data_manutenzione = html_entity_decode(strip_tags($data_manutenzione), ENT_QUOTES,$default_charset);

    $data_scad_manut = $adb->query_result($res_manutenzioni, $i, 'data_scad_manut');
    $data_scad_manut = html_entity_decode(strip_tags($data_scad_manut), ENT_QUOTES,$default_charset);

    $tipo_manutenzione = $adb->query_result($res_manutenzioni, $i, 'tipo_manutenzione');
    $tipo_manutenzione = html_entity_decode(strip_tags($tipo_manutenzione), ENT_QUOTES,$default_charset);

    $tempo_previsto = $adb->query_result($res_manutenzioni, $i, 'tempo_previsto');
    $tempo_previsto = html_entity_decode(strip_tags($tempo_previsto), ENT_QUOTES,$default_charset);

    $tecnici = $adb->query_result($res_manutenzioni, $i, 'tecnici');
    $tecnici = html_entity_decode(strip_tags($tecnici), ENT_QUOTES,$default_charset);
    
    $problema_di_sic = $adb->query_result($res_manutenzioni, $i, 'kp_problema_di_sic');
    $problema_di_sic = html_entity_decode(strip_tags($problema_di_sic), ENT_QUOTES,$default_charset);
    if($problema_di_sic == null || $problema_di_sic == ""){
		$problema_di_sic = "--Nessuno--";
	}

    $ora_inizio = $adb->query_result($res_manutenzioni, $i, 'ora_inizio');
    $ora_inizio = html_entity_decode(strip_tags($ora_inizio), ENT_QUOTES,$default_charset);
    if($ora_inizio == null || $ora_inizio == ""){
        $ora_inizio = "00:00";
    }

    $ora_fine = $adb->query_result($res_manutenzioni, $i, 'ora_fine');
    $ora_fine = html_entity_decode(strip_tags($ora_fine), ENT_QUOTES,$default_charset);
    if($ora_fine == null || $ora_fine == ""){
        $ora_fine = "01:00";
    }

    $cognome = $adb->query_result($res_manutenzioni, $i, 'cognome');
    $cognome = html_entity_decode(strip_tags($cognome), ENT_QUOTES,$default_charset);
    if($cognome == null || $cognome == ""){
        $cognome = "";
    }

    $nome = $adb->query_result($res_manutenzioni, $i, 'nome');
    $nome = html_entity_decode(strip_tags($nome), ENT_QUOTES,$default_charset);
    if($nome == null || $nome == ""){
        $nome = "";
    }

    $colore = $adb->query_result($res_manutenzioni, $i, 'colore');
    $colore = html_entity_decode(strip_tags($colore), ENT_QUOTES,$default_charset);
    if($colore == null || $colore == ""){
        $colore = "blue";
    }

    if($data_manutenzione != '' && $data_manutenzione != null){

        list($anno,$mese,$giorno) = explode("-",$data_manutenzione);
        $data_manutenzione_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));

        list($anno,$mese,$giorno) = explode("-",$data_manutenzione);
        $data_fine = date("Y-m-d",mktime(0,0,0,$mese,$giorno,$anno));

        $data_inizio = $data_manutenzione." ".$ora_inizio;
        $data_fine = $data_fine." ".$ora_fine;

    }
    else{

        $data_manutenzione = $data_corrente;
        list($anno,$mese,$giorno) = explode("-",$data_manutenzione);
        $data_manutenzione_inv = date("d-m-Y",mktime(0,0,0,$mese,$giorno,$anno));

        list($anno,$mese,$giorno) = explode("-",$data_manutenzione);
        $data_fine = date("Y-m-d",mktime(0,0,0,$mese,$giorno,$anno));

        $data_inizio = $data_manutenzione." ".$ora_inizio;
        $data_fine = $data_fine." ".$ora_fine;

    }
    
    $data_inizio_inv_ora = $data_manutenzione_inv." (".$ora_inizio." - ".$ora_fine.")";
    
    if($stato_manutenzione != "Creata" || ($stato_manutenzione == "Creata" && $tipo_manutenzione == "Programmata" && $resp_man_ord == "si") || ($stato_manutenzione == "Creata" && $tipo_manutenzione == "Extra" && $resp_man_extra == "si")){
		
		if($percentuale_fermo == "default" || $percentuale_fermo == $percentuale_fermo_impianto){
		
	        $rows[] = array('id' => $manutenzioniid,
	                        'data_inizio' => $data_inizio,
	                        'data_inizio_inv' => $data_manutenzione_inv,
	                        'data_inizio_inv_ora' => $data_inizio_inv_ora,
	                        'end_date' => $data_fine,
	                        'ora_inizio' => $ora_inizio,
	                        'ora_fine' => $ora_fine,
	                        'manutenzione_name' => $manutenzione_name,
	                        'color' => $colore,
	                        'tipo_manutenzione' => $tipo_manutenzione,
	                        'tempo_previsto' => $tempo_previsto,
	                        'tecnici_inc' => $tecnici,
	                        'stato_manutenzione' => $stato_manutenzione,
	                        'problema_di_sic' => $problema_di_sic,
	                        'percentuale_fermo_impianto_desc' => $percentuale_fermo_impianto_desc);
		
		}
		
    }

}

	
$json = json_encode($rows);
print $json;

function impiantoManutenzione($manutenzione){
    global $adb, $table_prefix, $current_user, $default_charset;
    
    $q_componenti = "SELECT t.* FROM 
                        ((SELECT 
                        comp.compimpiantoid compimpiantoid,
                        comp.nome_componente nome_componente,
                        comp.matricola matricola,
                        comp.locazione locazione,
                        comp.data_ult_manutenz data_ult_manutenz,
                        comp.stato_componente stato_componente,
                        comp.impianto impianto,
                        imp.impianto_name impianto_name,
                        imp.matricola_impianto matricola_impianto
                        FROM {$table_prefix}_compimpianto comp
                        INNER JOIN {$table_prefix}_crmentityrel rel ON rel.crmid = comp.compimpiantoid
                        INNER JOIN {$table_prefix}_impianti imp ON imp.impiantiid = comp.impianto     
                        WHERE rel.module = 'CompImpianto' AND rel.relmodule = 'Manutenzioni' AND rel.relcrmid = ".$manutenzione.")
                        UNION
                        (SELECT 
                        comp2.compimpiantoid compimpiantoid,
                        comp2.nome_componente nome_componente,
                        comp2.matricola matricola,
                        comp2.locazione locazione,
                        comp2.data_ult_manutenz data_ult_manutenz,
                        comp2.stato_componente stato_componente,
                        comp2.impianto impianto,
                        imp2.impianto_name impianto_name,
                        imp2.matricola_impianto matricola_impianto
                        FROM {$table_prefix}_compimpianto comp2
                        INNER JOIN {$table_prefix}_crmentityrel rel2 ON rel2.relcrmid = comp2.compimpiantoid
                        INNER JOIN {$table_prefix}_impianti imp2 ON imp2.impiantiid = comp2.impianto    
                        WHERE rel2.relmodule = 'CompImpianto' AND rel2.module = 'Manutenzioni' AND rel2.crmid = ".$manutenzione.")) AS t
                        WHERE t.compimpiantoid != 0
                        ORDER BY t.impianto_name ASC, t.nome_componente ASC";
    
    $res_componenti = $adb->query($q_componenti);
    if($adb->num_rows($res_componenti) > 0){
		
		$impianto = $adb->query_result($res_componenti, 0, 'impianto');
        $impianto = html_entity_decode(strip_tags($impianto), ENT_QUOTES,$default_charset);
		
	}
	else{
		$impianto = 0;
	}
	
	$result = array('impianto' => $impianto);
    
    return $result;
    
}

function fermiImpianto($impianto){
    global $adb, $table_prefix, $current_user, $default_charset;
    
    $result = ""; 
    
    $q_fermi_impianto = "SELECT 
                            ferm.fermiimpiantoid fermiimpiantoid,
                            ferm.nome_fermo_impianto nome_fermo_impianto,
                            ferm.kp_perc_fermo_imp kp_perc_fermo_imp,
                            ferm.kp_data_inizio kp_data_inizio,
                            ferm.kp_data_fine kp_data_fine,
                            ferm.kp_ora_inizio kp_ora_inizio,
                            ferm.kp_ora_fine kp_ora_fine,
                            ferm.description description
                            FROM {$table_prefix}_fermiimpianto ferm
                            INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = ferm.fermiimpiantoid
                            WHERE ent.deleted = 0 AND ferm.kp_impianto = ".$impianto."
                            ORDER BY ferm.kp_data_inizio DESC, ferm.kp_ora_inizio DESC";
    $res_fermi_impianto = $adb->query($q_fermi_impianto);
    
    if($adb->num_rows($res_fermi_impianto) > 0){
        
        $fermiimpiantoid = $adb->query_result($res_fermi_impianto, 0, 'fermiimpiantoid');
        $fermiimpiantoid = html_entity_decode(strip_tags($fermiimpiantoid), ENT_QUOTES,$default_charset);
        
        $nome_fermo_impianto = $adb->query_result($res_fermi_impianto, 0, 'nome_fermo_impianto');
        $nome_fermo_impianto = html_entity_decode(strip_tags($nome_fermo_impianto), ENT_QUOTES,$default_charset);
        
        $perc_fermo_imp = $adb->query_result($res_fermi_impianto, 0, 'kp_perc_fermo_imp');
        $perc_fermo_imp = html_entity_decode(strip_tags($perc_fermo_imp), ENT_QUOTES,$default_charset);
        $perc_fermo_imp_desc = $perc_fermo_imp."%";
        
        $data_inizio = $adb->query_result($res_fermi_impianto, 0, 'kp_data_inizio');
        $data_inizio = html_entity_decode(strip_tags($data_inizio), ENT_QUOTES,$default_charset);
        if($data_inizio == null || $data_inizio == "1900-01-01"){
            $data_inizio = "";
        }
        else{
            list($anno,$mese,$giorno) = explode("-",$data_inizio);
            $data_inizio_inv = date("d/m/Y",mktime(0,0,0,$mese,$giorno,$anno));
        }
        
        $data_fine = $adb->query_result($res_fermi_impianto, 0, 'kp_data_fine');
        $data_fine = html_entity_decode(strip_tags($data_fine), ENT_QUOTES,$default_charset);
        if($data_fine == null || $data_fine == "1900-01-01"){
            $data_fine = "";
        }
        
        $ora_inizio = $adb->query_result($res_fermi_impianto, 0, 'kp_ora_inizio');
        $ora_inizio = html_entity_decode(strip_tags($ora_inizio), ENT_QUOTES,$default_charset);
        
        $ora_fine = $adb->query_result($res_fermi_impianto, 0, 'kp_ora_fine');
        $ora_fine = html_entity_decode(strip_tags($ora_fine), ENT_QUOTES,$default_charset);
        
        $description = $adb->query_result($res_fermi_impianto, 0, 'description');
        $description = html_entity_decode(strip_tags($description), ENT_QUOTES,$default_charset);
        
        if($data_fine == ""){
            $stato_impianto = "Fermo";
        }
        else{
            $stato_impianto = "In funzione";
        }
        
    }
    else{
        $fermiimpiantoid = 0;
        $nome_fermo_impianto = "";
        $perc_fermo_imp = "0";
        $perc_fermo_imp_desc = "0%";
        $data_inizio = "";
        $data_fine = "";
        $ora_inizio = "";
        $ora_fine = "";
        $description = "";
        $stato_impianto = "In funzione";
    }
    
    $result = array('fermiimpiantoid' => $fermiimpiantoid,
                    'nome_fermo_impianto' => $nome_fermo_impianto,
                    'perc_fermo_imp' => $perc_fermo_imp,
                    'perc_fermo_imp_desc' => $perc_fermo_imp_desc,
                    'data_inizio' => $data_inizio,
                    'data_inizio_inv' => $data_inizio_inv,
                    'data_fine' => $data_fine,
                    'ora_inizio' => $ora_inizio,
                    'ora_fine' => $ora_fine,
                    'description' => $description,
                    'stato_impianto' => $stato_impianto);
    
    return $result;
    
}
	
?>
