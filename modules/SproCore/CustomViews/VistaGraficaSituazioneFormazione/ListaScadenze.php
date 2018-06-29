<?php

include_once('../../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix, $current_user, $site_URL, $default_charset;
session_start();

if(!isset($_SESSION['authenticated_user_id'])){
    header("Location: ".$site_URL."/index.php?module=Accounts&action=index");
}
else{
	$current_user->id = $_SESSION['authenticated_user_id'];
}

$data_corrente = date ("Y-m-d");
list($anno_corrente,$mese_corrente,$giorno_corrente) = explode("-",$data_corrente);
$data_corrente_inv = date("d-m-Y",mktime(0,0,0,$mese_corrente,$giorno_corrente,$anno_corrente));

$data_corrente_end_inv = date("d-m-Y",mktime(0,0,0,$mese_corrente,$giorno_corrente+1,$anno_corrente));

if(isset($_GET['azienda'])){
    $azienda = addslashes(html_entity_decode(strip_tags($_GET['azienda']), ENT_QUOTES, $default_charset));
    $azienda = substr($azienda, 0, 255);
    $azienda = trim($azienda);
    if($azienda == ''){
        $azienda = 'all';
    }
}
else{
    $azienda = 'all';
}

if(isset($_GET['stabilimento'])){
    $stabilimento = addslashes(html_entity_decode(strip_tags($_GET['stabilimento']), ENT_QUOTES, $default_charset));
    $stabilimento = substr($stabilimento, 0, 255);
    $stabilimento = trim($stabilimento);
    if($stabilimento == ''){
        $stabilimento = 'all';
    }
}
else{
    $stabilimento = 'all';
}

if(isset($_GET['tipo_corso'])){
    $tipo_corso = addslashes(html_entity_decode(strip_tags($_GET['tipo_corso']), ENT_QUOTES, $default_charset));
    $tipo_corso = substr($tipo_corso, 0, 255);
    $tipo_corso = trim($tipo_corso);
    if($tipo_corso == ''){
        $tipo_corso = 'all';
    }
}
else{
    $tipo_corso = 'all';
}

$rows = array();

$q_operazioni = "SELECT 
                    sit.situazformazid situazformazid,
                    sit.situazformaz_no situazformaz_no,
                    sit.tipo_corso tipo_corso,
                    sit.formazione formazione,
                    sit.validita_formazione validita_formazione,
                    sit.data_formazione data_formazione,
                    sit.mansione_risorsa mansione_risorsa,
                    sit.risorsa risorsa,
                    sit.mansione mansione,
                    sit.stato_formazione stato_formazione,
                    sit.azienda azienda,
                    sit.stabilimento stabilimento,
                    sit.ore_previste ore_previste,
                    sit.ore_effettuate ore_effettuate,
                    tp.tipicorso_name tipicorso_name,
                    CONCAT(cont.lastname, ' ', cont.firstname) nome_risorsa,
                    man.mansione_name mansione_name,
                    acc.accountname accountname,
                    stab.nome_stabilimento nome_stabilimento
                    FROM vte_situazformaz sit
                    INNER JOIN vte_crmentity ent ON ent.crmid = sit.situazformazid
                    INNER JOIN vte_tipicorso tp ON tp.tipicorsoid = sit.tipo_corso
                    INNER JOIN vte_contactdetails cont ON cont.contactid = sit.risorsa
                    INNER JOIN vte_mansioni man ON man.mansioniid = sit.mansione
                    INNER JOIN vte_account acc ON acc.accountid = sit.azienda
                    LEFT JOIN vte_stabilimenti stab ON stab.stabilimentiid = sit.stabilimento
                    WHERE ent.deleted = 0 AND sit.stato_formazione IN ('Non eseguita', 'Scaduta', 'Eseguire entro', 'Eseguito corso base', 'Non eseguito corso base', 'Non eseguita formazione precedente')";

//$q_operazioni .= " AND sit.situazformazid = 16175";

if($azienda != "all"){

    $q_operazioni .= " AND acc.accountname LIKE '%".$azienda."%'";

}

if($stabilimento != "all"){

    $q_operazioni .= " AND sit.stabilimento = ".$stabilimento;

}

if($tipo_corso != "all"){

    $q_operazioni .= " AND sit.tipo_corso = ".$tipo_corso;

}

/* kpro@tom310320171128 */
$data_corrente = date("Y-m-d");
$data_corrente = new DateTime($data_corrente);
$incremento = new DateInterval('P5Y');
$decremento = new DateInterval('P4M');

$da_data = $data_corrente->sub($decremento);
$da_data = $da_data->format('Y-m-d');

$a_data = $data_corrente->add($incremento);
$a_data = $a_data->format('Y-m-d');

$q_operazioni .= " AND sit.validita_formazione <= '".$a_data."'";
/* kpro@tom310320171128 end */

$q_operazioni .= " ORDER BY nome_risorsa ASC, sit.validita_formazione ASC";
						
$res_operazioni = $adb->query($q_operazioni);
$num_operazioni = $adb->num_rows($res_operazioni);

for($i=0; $i<$num_operazioni; $i++){

    $situazformazid = $adb->query_result($res_operazioni, $i, 'situazformazid');
    $situazformazid = html_entity_decode(strip_tags($situazformazid), ENT_QUOTES,$default_charset);

    $situazformaz_no = $adb->query_result($res_operazioni, $i, 'situazformaz_no');
    $situazformaz_no = html_entity_decode(strip_tags($situazformaz_no), ENT_QUOTES,$default_charset);

    $tipo_corso = $adb->query_result($res_operazioni, $i, 'tipo_corso');
    $tipo_corso = html_entity_decode(strip_tags($tipo_corso), ENT_QUOTES,$default_charset);

    $formazione = $adb->query_result($res_operazioni, $i, 'formazione');
    $formazione = html_entity_decode(strip_tags($formazione), ENT_QUOTES,$default_charset);

    $validita_formazione = $adb->query_result($res_operazioni, $i, 'validita_formazione');
    $validita_formazione = html_entity_decode(strip_tags($validita_formazione), ENT_QUOTES,$default_charset);
    if($validita_formazione == "" || $validita_formazione == null || $validita_formazione == '0000-00-00'){

        $validita_formazione = $data_corrente;

    }
    if($validita_formazione != '' && $validita_formazione != null && $validita_formazione != '0000-00-00'){

        $startdate = $validita_formazione;

        list($anno_start, $mese_start, $giorno_start) = explode("-", $startdate);
		
        $startdate_inv = date("d-m-Y", mktime(0, 0, 0, $mese_start, $giorno_start, $anno_start));
        
        $enddate_inv = date("d-m-Y", mktime(0, 0, 0,$mese_start, $giorno_start + 1, $anno_start));

    }
    else{
        $anno_start = $anno_corrente;
        $mese_start = $mese_corrente;
        $giorno_start = $giorno_corrente;
        $startdate = $data_corrente;
        $startdate_inv = $data_corrente_inv;
        $enddate_inv = $enddate_inv;
    }

    $data_formazione = $adb->query_result($res_operazioni, $i, 'data_formazione');
    $data_formazione = html_entity_decode(strip_tags($data_formazione), ENT_QUOTES,$default_charset);
    if($data_formazione != '' && $data_formazione != null && $data_formazione != '0000-00-00'){

        list($anno, $mese, $giorno) = explode("-", $data_formazione);
        $data_formazione = date("d-m-Y", mktime(0, 0, 0, $mese, $giorno, $anno));
        
        $data_formazione = date("d-m-Y", mktime(0, 0, 0,$mese, $giorno + 1, $anno));

    }
    else{
        $data_formazione = "";
    }

    $risorsa = $adb->query_result($res_operazioni, $i, 'risorsa');
    $risorsa = html_entity_decode(strip_tags($risorsa), ENT_QUOTES,$default_charset);

    $mansione = $adb->query_result($res_operazioni, $i, 'mansione');
    $mansione = html_entity_decode(strip_tags($mansione), ENT_QUOTES,$default_charset);

    $stato_formazione = $adb->query_result($res_operazioni, $i, 'stato_formazione');
    $stato_formazione = html_entity_decode(strip_tags($stato_formazione), ENT_QUOTES,$default_charset);

    $azienda = $adb->query_result($res_operazioni, $i, 'azienda');
    $azienda = html_entity_decode(strip_tags($azienda), ENT_QUOTES,$default_charset);

    $stabilimento = $adb->query_result($res_operazioni, $i, 'stabilimento');
    $stabilimento = html_entity_decode(strip_tags($stabilimento), ENT_QUOTES,$default_charset);

    $ore_previste = $adb->query_result($res_operazioni, $i, 'ore_previste');
    $ore_previste = html_entity_decode(strip_tags($ore_previste), ENT_QUOTES,$default_charset);

    $ore_effettuate = $adb->query_result($res_operazioni, $i, 'ore_effettuate');
    $ore_effettuate = html_entity_decode(strip_tags($ore_effettuate), ENT_QUOTES,$default_charset);

    $tipicorso_name = $adb->query_result($res_operazioni, $i, 'tipicorso_name');
    $tipicorso_name = html_entity_decode(strip_tags($tipicorso_name), ENT_QUOTES,$default_charset);

    $nome_risorsa = $adb->query_result($res_operazioni, $i, 'nome_risorsa');
    $nome_risorsa = html_entity_decode(strip_tags($nome_risorsa), ENT_QUOTES,$default_charset);

    $mansione_name = $adb->query_result($res_operazioni, $i, 'mansione_name');
    $mansione_name = html_entity_decode(strip_tags($mansione_name), ENT_QUOTES,$default_charset);

    $accountname = $adb->query_result($res_operazioni, $i, 'accountname');
    $accountname = html_entity_decode(strip_tags($accountname), ENT_QUOTES,$default_charset);

    $nome_stabilimento = $adb->query_result($res_operazioni, $i, 'nome_stabilimento');
    $nome_stabilimento = html_entity_decode(strip_tags($nome_stabilimento), ENT_QUOTES,$default_charset);

    $lead_time = 1;

    $projecttaskprogress = 0;

    $projecttaskname = $nome_risorsa." - ".$mansione_name." - ".$tipicorso_name;

    $rows[] = array('projecttaskid' => $situazformazid,
                    'projecttask_no' => $situazformaz_no,
                    'projecttaskname' => $projecttaskname,
                    'startdate' => $startdate,
                    'startdate_inv' => $startdate_inv,
                    'enddate_inv' => $enddate_inv,
                    'anno_start' => $anno_start,
                    'mese_start' => $mese_start,
                    'giorno_start' => $giorno_start,
                    'deadline' => $startdate,
                    'deadline_inv' => $startdate_inv,
                    'lead_time' => $lead_time,
                    'projecttaskprogress' => $projecttaskprogress,
                    'stato' => $stato_formazione,
                    'padre' => '',
                    'tipo_operazione' => 'Operazione',
                    'accountname' => $accountname,
                    'nome_stabilimento' => $nome_stabilimento,
                    'nome_risorsa' => $nome_risorsa,
                    'tipicorso_name' => $tipicorso_name,
                    'mansione_name' => $mansione_name);
	
}
	
$json = json_encode($rows);
print $json;
	
?>