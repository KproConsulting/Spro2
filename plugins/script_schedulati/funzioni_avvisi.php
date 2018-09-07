<?php

include_once(__DIR__.'/../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
require_once('modules/Emails/mail.php');
require_once('modules/ModNotifications/ModNotifications.php');
include_once('vtlib/Vtiger/Module.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix;
session_start();

function ContattiDaAvvisare($gestioneavvisiid){
	global $adb, $table_prefix,$current_user;

	$q_contatti = "(SELECT rel1.relcrmid contactid FROM {$table_prefix}_crmentityrel rel1
					INNER JOIN {$table_prefix}_crmentity ent1 ON ent1.crmid = rel1.relcrmid
					WHERE ent1.deleted = 0 AND rel1.crmid =".$gestioneavvisiid." 
					AND rel1.module ='GestioneAvvisi' AND rel1.relmodule ='Contacts')
					UNION 
					(SELECT rel2.crmid contactid FROM {$table_prefix}_crmentityrel rel2
					INNER JOIN {$table_prefix}_crmentity ent2 ON ent2.crmid = rel2.crmid
					WHERE ent2.deleted = 0 AND rel2.relcrmid =".$gestioneavvisiid." 
					AND rel2.relmodule ='GestioneAvvisi' AND rel2.module ='Contacts')";
	
	$res_contatti = $adb->query($q_contatti);			
	$num_contatti = $adb->num_rows($res_contatti);
	for($i=0; $i<$num_contatti; $i++){
		
		$contactid[$i] = $adb->query_result($res_contatti,$i,'contactid');
	
	}
	
	return $contactid;
}

function GetDatiAzienda($azienda){
	global $adb, $table_prefix,$default_charset;

	$q = "SELECT acc.accountname
		FROM {$table_prefix}_account acc
		INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = acc.accountid
		WHERE ent.deleted = 0 AND acc.accountid = ".$azienda;
	$res = $adb->query($q);
	if($adb->num_rows($res) > 0){
		$nome_azienda = $adb->query_result($res, 0, 'accountname');
        $nome_azienda = html_entity_decode(strip_tags($nome_azienda), ENT_QUOTES, $default_charset);
	}
	else{
		$nome_azienda = "";
	}

	$dati_azienda = array(
		"nome" => $nome_azienda
	);

	return $dati_azienda;
}

function GetDatiFornitore($fornitore){
	global $adb, $table_prefix,$default_charset;

	$q = "SELECT vend.vendorname
		FROM {$table_prefix}_vendor vend
		INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = vend.vendorid
		WHERE ent.deleted = 0 AND vend.vendorid = ".$fornitore;
	$res = $adb->query($q);
	if($adb->num_rows($res) > 0){
		$nome_fornitore = $adb->query_result($res, 0, 'vendorname');
        $nome_fornitore = html_entity_decode(strip_tags($nome_fornitore), ENT_QUOTES, $default_charset);
	}
	else{
		$nome_fornitore = "";
	}

	$dati_fornitore = array(
		"nome" => $nome_fornitore
	);

	return $dati_fornitore;
}
	
function AvvisiImpianti($gestioneavvisiid,$azienda,$giorni_in_scadenza,$indirizzo_mittente,$nome_mittente){
	require_once('plugins/script_schedulati/aggiorna_impianti.php');
	global $adb, $table_prefix,$current_user;
	
	AggiornaImpianti($azienda,$giorni_in_scadenza);
	
	$contatti_da_avvisare = ContattiDaAvvisare($gestioneavvisiid);

	$dati_azienda = GetDatiAzienda($azienda);
	
	$testo_mail = "";
	
	$q_impianti = "SELECT impiantiid, impianto_name, data_scad_manutenzione, stato_man_imp 
					FROM {$table_prefix}_impianti 
					INNER JOIN {$table_prefix}_crmentity ON crmid = impiantiid
					WHERE deleted = 0 AND (stato_man_imp = 'Scaduta' OR stato_man_imp = 'Non eseguita' OR stato_man_imp = 'In scadenza') 
					AND azienda =".$azienda; 
					
	$res_impianti = $adb->query($q_impianti);			
	$num_impianti = $adb->num_rows($res_impianti);
	for($i=0; $i<$num_impianti; $i++){

		$impianto_id = $adb->query_result($res_impianti,$i,'impiantiid');
		$impianto_name = $adb->query_result($res_impianti,$i,'impianto_name');
		$stato_man_imp = $adb->query_result($res_impianti,$i,'stato_man_imp');
		$data_scad_manutenzione = $adb->query_result($res_impianti,$i,'data_scad_manutenzione');
		
		$testo_mail .= "- L'impianto ".$impianto_name." ha il seguente stato delle manutenzioni: ".$stato_man_imp."<br />";
	
		//Qualora oltre alla mail volessi anche creare delle notifiche la parte di script sottostante fa questo
		/*$text = "L'impianto ".$impianto_name." ha il seguente stato delle manutenzioni: ".$stato_man_imp;
		$focus = CRMEntity::getInstance('ModNotifications');
		$focus->c(
			array(
				'assigned_user_id' => 1,
				'related_to' => $impianto_id,
				'mod_not_type' => 'Changed record',
				'createdtime' => date('Y-m-d H:i:s'),
				'modifiedtime' => date('Y-m-d H:i:s'),
				'description' => $text,
				),false
			);*/
		//End script notifica

	}

	if($testo_mail != ''){
		$soggetto_mail = "Alert Impianti (".$dati_azienda['nome'].")";

		$testo_mail = "Notifiche Impianti:<br />".$testo_mail;
		
		foreach($contatti_da_avvisare as $contatto_mail){
			$q_mail_destinatario = "SELECT email FROM {$table_prefix}_contactdetails WHERE contactid =".$contatto_mail;
			$res_mail_destinatario = $adb->query($q_mail_destinatario);
			if($adb->num_rows($res_mail_destinatario)>0){
				$email_destinatario = $adb->query_result($res_mail_destinatario,0,'email');
				$mail = send_mail('GestioneAvvisi',$email_destinatario,$nome_mittente,$indirizzo_mittente,$soggetto_mail,$testo_mail);
			}
		}
	}
	 
	$control = 'si';
	
	return $control;

}

function AvvisiFormazione($gestioneavvisiid,$azienda,$giorni_in_scadenza,$indirizzo_mittente,$nome_mittente){
	require_once('plugins/script_schedulati/aggiorna_formazione.php');
    require_once('modules/SproCore/SproUtils/spro_utils.php');
	global $adb, $table_prefix,$current_user;

    calcolaSituazioneFormazioneAzienda($azienda,$giorni_in_scadenza);
	
	$contatti_da_avvisare = ContattiDaAvvisare($gestioneavvisiid);

	$dati_azienda = GetDatiAzienda($azienda);
	
	$testo_mail = "";
	
	$q_formazione = "SELECT stato_formazione, firstname, lastname, tipicorso_name, mansione_name, mansione_risorsa 
						FROM {$table_prefix}_situazformaz
						INNER JOIN {$table_prefix}_contactdetails ON contactid = risorsa
						INNER JOIN {$table_prefix}_crmentity ON crmid = situazformazid
						INNER JOIN {$table_prefix}_tipicorso ON tipicorsoid = tipo_corso
						INNER JOIN {$table_prefix}_mansioni ON mansioniid = mansione
						WHERE deleted = 0 AND (stato_formazione = 'Scaduta' OR stato_formazione = 'Non eseguita' OR stato_formazione = 'In scadenza') 
						AND accountid = ".$azienda."
						ORDER BY firstname";

	$res_formazione = $adb->query($q_formazione);			
	$num_formazione = $adb->num_rows($res_formazione);
	for($i=0; $i<$num_formazione; $i++){

		$stato_formazione = $adb->query_result($res_formazione,$i,'stato_formazione');
		$firstname = $adb->query_result($res_formazione,$i,'firstname');
		$lastname = $adb->query_result($res_formazione,$i,'lastname');
		$tipicorso_name = $adb->query_result($res_formazione,$i,'tipicorso_name');
		$mansione_name = $adb->query_result($res_formazione,$i,'mansione_name');
		$mansione_risorsa = $adb->query_result($res_formazione,$i,'mansione_risorsa');
		
		$testo_mail .= "- ".$firstname." ".$lastname." ha il corso ".$tipicorso_name." relativo alla mansione ".$mansione_name." in stato ".$stato_formazione."<br />";
		
		//Qualora oltre alla mail volessi anche creare delle notifiche la parte di script sottostante fa questo
		/*$text = $firstname." ".$lastname." ha il corso ".$tipicorso_name." relativo alla mansione ".$mansione_name." in stato ".$stato_formazione;
		$focus = CRMEntity::getInstance('ModNotifications');
		$focus->saveFastNotification(
			array(
				'assigned_user_id' => 1,
				'related_to' => $mansione_risorsa,
				'mod_not_type' => 'Changed record',
				'createdtime' => date('Y-m-d H:i:s'),
				'modifiedtime' => date('Y-m-d H:i:s'),
				'description' => $text,
				),false
			);*/
		//End script notifica

	}

	if($testo_mail != ''){
		$soggetto_mail = "Alert Formazione (".$dati_azienda['nome'].")";

		$testo_mail = "Notifiche Formazione:<br />".$testo_mail;
		
		foreach($contatti_da_avvisare as $contatto_mail){
			$q_mail_destinatario = "SELECT email FROM {$table_prefix}_contactdetails WHERE contactid =".$contatto_mail;
			$res_mail_destinatario = $adb->query($q_mail_destinatario);
			if($adb->num_rows($res_mail_destinatario)>0){
				$email_destinatario = $adb->query_result($res_mail_destinatario,0,'email');
				$mail = send_mail('GestioneAvvisi',$email_destinatario,$nome_mittente,$indirizzo_mittente,$soggetto_mail,$testo_mail);
			}
		}
	}
	 
	$control = 'si';
	
	return $control;
}

function AvvisiVisiteMediche($gestioneavvisiid,$azienda,$giorni_in_scadenza,$indirizzo_mittente,$nome_mittente){
	require_once('plugins/script_schedulati/aggiorna_visite_mediche.php');
	global $adb, $table_prefix,$current_user;

	AggiornaVisiteMediche($azienda,$giorni_in_scadenza);
	
	$contatti_da_avvisare = ContattiDaAvvisare($gestioneavvisiid);

	$dati_azienda = GetDatiAzienda($azienda);
	
	$testo_mail = "";
	
	$q_visitamedica = "SELECT stato_sit_visita, firstname, lastname, tipivisitamed_name, mansione_name, mansione_risorsa 
						FROM {$table_prefix}_situazvisitemed
						INNER JOIN {$table_prefix}_contactdetails ON contactid = risorsa
						INNER JOIN {$table_prefix}_crmentity ON crmid = situazvisitemedid
						INNER JOIN {$table_prefix}_tipivisitamed ON tipivisitamedid = tipo_visita
						INNER JOIN {$table_prefix}_mansioni ON mansioniid = mansione
						WHERE deleted = 0 AND (stato_sit_visita = 'Scaduta' OR stato_sit_visita = 'Non eseguita' OR stato_sit_visita = 'In scadenza') 
						AND accountid = ".$azienda."
						ORDER BY firstname";
	
	$res_visitamedica = $adb->query($q_visitamedica);			
	$num_visitamedica = $adb->num_rows($res_visitamedica);
	for($i=0; $i<$num_visitamedica; $i++){

		$stato_sit_visita = $adb->query_result($res_visitamedica,$i,'stato_sit_visita');
		$firstname = $adb->query_result($res_visitamedica,$i,'firstname');
		$lastname = $adb->query_result($res_visitamedica,$i,'lastname');
		$tipivisitamed_name = $adb->query_result($res_visitamedica,$i,'tipivisitamed_name');
		$mansione_name = $adb->query_result($res_visitamedica,$i,'mansione_name');
		$mansione_risorsa = $adb->query_result($res_visitamedica,$i,'mansione_risorsa');
		
		$testo_mail .= "- ".$firstname." ".$lastname." ha la visita medica ".$tipivisitamed_name." relativa alla mansione ".$mansione_name." in stato ".$stato_sit_visita."<br />";
		
		//Qualora oltre alla mail volessi anche creare delle notifiche la parte di script sottostante fa questo
		/*$text = $firstname." ".$lastname." ha la visita medica ".$tipivisitamed_name." relativa alla mansione ".$mansione_name." in stato ".$stato_sit_visita;
		$focus = CRMEntity::getInstance('ModNotifications');
		$focus->saveFastNotification(
			array(
				'assigned_user_id' => 1,
				'related_to' => $mansione_risorsa,
				'mod_not_type' => 'Changed record',
				'createdtime' => date('Y-m-d H:i:s'),
				'modifiedtime' => date('Y-m-d H:i:s'),
				'description' => $text,
				),false
			);*/
		//End script notifica

	}

	if($testo_mail != ''){
		$soggetto_mail = "Alert Visite Mediche (".$dati_azienda['nome'].")";

		$testo_mail = "Notifiche Visite Mediche:<br />".$testo_mail;
		
		foreach($contatti_da_avvisare as $contatto_mail){
			$q_mail_destinatario = "SELECT email FROM {$table_prefix}_contactdetails WHERE contactid =".$contatto_mail;
			$res_mail_destinatario = $adb->query($q_mail_destinatario);
			if($adb->num_rows($res_mail_destinatario)>0){
				$email_destinatario = $adb->query_result($res_mail_destinatario,0,'email');
				$mail = send_mail('GestioneAvvisi',$email_destinatario,$nome_mittente,$indirizzo_mittente,$soggetto_mail,$testo_mail);
			}
		}
	}
	 
	$control = 'si';
	
	return $control;
}

function AvvisiDocumenti($gestioneavvisiid,$azienda,$giorni_in_scadenza,$indirizzo_mittente,$nome_mittente){
	//require_once('plugins/script_schedulati/aggiorna_documenti.php'); /* kpro@bid070920181520 */
	global $adb, $table_prefix,$current_user;

	//AggiornaDocumenti($azienda,$giorni_in_scadenza);
	
	$contatti_da_avvisare = ContattiDaAvvisare($gestioneavvisiid);

	$dati_azienda = GetDatiAzienda($azienda);
	
	$testo_mail = "";
	$lista_documenti = array();

	$q = "SELECT stab.stabilimentiid,
		stab.nome_stabilimento
		FROM {$table_prefix}_stabilimenti stab
		INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = stab.stabilimentiid
		WHERE ent.deleted = 0 AND stab.azienda = ".$azienda;
	
	$res = $adb->query($q);
	$num = $adb->num_rows($res);
	
	for($i = 0; $i < $num; $i++){	

		$stabilimento = $adb->query_result($res, $i, 'stabilimentiid');
		$stabilimento = html_entity_decode(strip_tags($stabilimento), ENT_QUOTES, $default_charset);

		$nome_stabilimento = $adb->query_result($res, $i, 'nome_stabilimento');
		$nome_stabilimento = html_entity_decode(strip_tags($nome_stabilimento), ENT_QUOTES, $default_charset);

		$q_documenti = "SELECT * 
					FROM (
						SELECT note.notesid notesid, 
						note.title title, 
						td.nome_tipo_documento nome_tipo_documento,
						note.data_scadenza data_scadenza, 
						note.stato_documento stato_documento 
						FROM {$table_prefix}_notes note
						LEFT JOIN {$table_prefix}_tipidocumenti td ON td.tipidocumentiid = note.kp_tipo_documento
						INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = note.notesid
						INNER JOIN {$table_prefix}_senotesrel noterel ON noterel.notesid = note.notesid
						WHERE ent.deleted = 0 AND (note.stato_documento = 'Scaduto' OR note.stato_documento = 'In scadenza')
						AND noterel.crmid = {$stabilimento}
						UNION
						SELECT 0 AS notesid,
						td.nome_tipo_documento title,
						td.nome_tipo_documento nome_tipo_documento,
						'' AS data_scadenza,
						sitdoc.kp_stato_sit_doc stato_documento
						FROM {$table_prefix}_kpsituazionedocumenti sitdoc
						INNER JOIN {$table_prefix}_tipidocumenti td ON td.tipidocumentiid = sitdoc.kp_tipo_documento
						INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sitdoc.kpsituazionedocumentiid
						WHERE ent.deleted = 0 AND sitdoc.kp_stato_sit_doc = 'Non eseguito'
						AND sitdoc.kp_azienda = {$azienda} AND sitdoc.kp_stabilimento = {$stabilimento}
					) AS i
					ORDER BY i.notesid, i.stato_documento, i.data_scadenza";

		$res_documenti = $adb->query($q_documenti);			
		$num_documenti = $adb->num_rows($res_documenti);
		
		for($j = 0; $j < $num_documenti; $j++){
			$documentoid = $adb->query_result($res_documenti,$j,'notesid');
			if($documentoid != 0){
				$lista_documenti[] = $documentoid;
			}
		
			$title = $adb->query_result($res_documenti,$j,'title');
			$title = html_entity_decode(strip_tags($title), ENT_QUOTES,$default_charset);

			$nome_tipo_documento = $adb->query_result($res_documenti,$j,'nome_tipo_documento');
			$nome_tipo_documento = html_entity_decode(strip_tags($nome_tipo_documento), ENT_QUOTES,$default_charset);
			if($nome_tipo_documento == null){
				$nome_tipo_documento = "";
			}

			if($nome_tipo_documento != ""){
				$title .= ' ('.$nome_tipo_documento.')';
			}
			
			$data_scadenza = $adb->query_result($res_documenti,$j,'data_scadenza');
			if($data_scadenza != '' && $data_scadenza != null && $data_scadenza != '0000-00-00'){
				$data_scadenza_dt = new DateTime($data_scadenza);
				$data_scadenza = $data_scadenza_dt->format("d-m-Y");
			}

			$stato_documento[$j] = $adb->query_result($res_documenti,$j,'stato_documento');

			if($j == 0){
				$testo_mail .= "<br />Stabilimento <b>".$nome_stabilimento."</b><br />";
			}
			
			if($j == 0 || $stato_documento[$j] != $stato_documento[$j-1]){
				if($stato_documento[$j] == 'In scadenza'){
					$testo_mail .= "- I seguenti documenti sono in scadenza:<br />";
				}
				elseif($stato_documento[$j] == 'Scaduto'){
					$testo_mail .= "- I seguenti documenti sono scaduti:<br />";
				}
				elseif($stato_documento[$j] == 'Non eseguito'){
					$testo_mail .= "- I seguenti tipi documenti non sono ancora stati redatti:<br />";
				}
			}
			
			if($stato_documento[$j] == 'In scadenza'){
				$testo_mail .= "--- ".$title." scadra' in data ".$data_scadenza."<br />";
			}
			elseif($stato_documento[$j] == 'Scaduto'){
				$testo_mail .= "--- ".$title." e' scaduto in data ".$data_scadenza."<br />";
			}
			elseif($stato_documento[$j] == 'Non eseguito'){
				$testo_mail .= "--- ".$title."<br />";
			}	

			//Qualora oltre alla mail volessi anche creare delle notifiche la parte di script sottostante fa questo
			/*if($documentoid != 0){
				$text = "Il documento ".$title." e' ".$stato_documento[$j];
				$focus = CRMEntity::getInstance('ModNotifications');
				$focus->saveFastNotification(
					array(
						'assigned_user_id' => 1,
						'related_to' => $documentoid,
						'mod_not_type' => 'Changed record',
						'createdtime' => date('Y-m-d H:i:s'),
						'modifiedtime' => date('Y-m-d H:i:s'),
						'description' => $text,
					),false
				);
			}*/
			//End script notifica

		}
	}

	$cont = 0;
	
	$q_documenti = "SELECT note.notesid notesid, 
					note.title title, 
					td.nome_tipo_documento nome_tipo_documento,
					note.data_scadenza data_scadenza, 
					note.stato_documento stato_documento 
					FROM {$table_prefix}_notes note
					LEFT JOIN {$table_prefix}_tipidocumenti td ON td.tipidocumentiid = note.kp_tipo_documento
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = note.notesid
					INNER JOIN {$table_prefix}_senotesrel noterel ON noterel.notesid = note.notesid
					WHERE ent.deleted = 0 AND (note.stato_documento = 'Scaduto' OR note.stato_documento = 'In scadenza') 
					AND noterel.crmid = ".$azienda."
					ORDER BY stato_documento, data_scadenza";
	$res_documenti = $adb->query($q_documenti);			
	$num_documenti = $adb->num_rows($res_documenti);
	
	for($i=0; $i<$num_documenti; $i++){
		$documentoid = $adb->query_result($res_documenti,$i,'notesid');

		if (!in_array($documentoid, $lista_documenti)) {
		
			$title = $adb->query_result($res_documenti,$i,'title');
			$title = html_entity_decode(strip_tags($title), ENT_QUOTES,$default_charset);

			$nome_tipo_documento = $adb->query_result($res_documenti,$i,'nome_tipo_documento');
			$nome_tipo_documento = html_entity_decode(strip_tags($nome_tipo_documento), ENT_QUOTES,$default_charset);
			if($nome_tipo_documento == null){
				$nome_tipo_documento = "";
			}

			if($nome_tipo_documento != ""){
				$title .= ' ('.$nome_tipo_documento.')';
			}
			
			$data_scadenza = $adb->query_result($res_documenti,$i,'data_scadenza');
			if($data_scadenza != '' && $data_scadenza != null && $data_scadenza != '0000-00-00'){
				$data_scadenza_dt = new DateTime($data_scadenza);
				$data_scadenza = $data_scadenza_dt->format("d-m-Y");
			}

			$stato_documento[$cont] = $adb->query_result($res_documenti,$i,'stato_documento');

			if($cont == 0){
				$testo_mail .= "<br /><b>Documenti collegati solo all'azienda</b><br />";
			}
			
			if($cont == 0 || $stato_documento[$cont] != $stato_documento[$cont-1]){
				if($stato_documento[$cont] == 'In scadenza'){
					$testo_mail .= "- I seguenti documenti sono in scadenza:<br />";
				}
				elseif($stato_documento[$cont] == 'Scaduto'){
					$testo_mail .= "- I seguenti documenti sono scaduti:<br />";
				}
			}
			
			if($stato_documento[$cont] == 'In scadenza'){
				$testo_mail .= "--- ".$title." scadra' in data ".$data_scadenza."<br />";
			}
			elseif($stato_documento[$cont] == 'Scaduto'){
				$testo_mail .= "--- ".$title." e' scaduto in data ".$data_scadenza."<br />";
			}
			
			//Qualora oltre alla mail volessi anche creare delle notifiche la parte di script sottostante fa questo
			/*$text = "Il documento ".$title." e' ".$stato_documento[$cont];
			$focus = CRMEntity::getInstance('ModNotifications');
			$focus->saveFastNotification(
				array(
					'assigned_user_id' => 1,
					'related_to' => $documentoid,
					'mod_not_type' => 'Changed record',
					'createdtime' => date('Y-m-d H:i:s'),
					'modifiedtime' => date('Y-m-d H:i:s'),
					'description' => $text,
					),false
				);*/
			//End script notifica

			$cont++;
		}
	
	}
	
	if($testo_mail != ''){
		$soggetto_mail = "Alert Documenti Aziende (".$dati_azienda['nome'].")";

		$testo_mail = "Notifiche Documenti Aziende:<br /><br />".$testo_mail;
		
		foreach($contatti_da_avvisare as $contatto_mail){
			$q_mail_destinatario = "SELECT email FROM {$table_prefix}_contactdetails WHERE contactid =".$contatto_mail;
			$res_mail_destinatario = $adb->query($q_mail_destinatario);
			if($adb->num_rows($res_mail_destinatario)>0){
				$email_destinatario = $adb->query_result($res_mail_destinatario,0,'email');
				$mail = send_mail('GestioneAvvisi',$email_destinatario,$nome_mittente,$indirizzo_mittente,$soggetto_mail,$testo_mail);
			}
		}
	}
	 
	$control = 'si';
	
	return $control;
	
}

function AvvisiDocumentiFornitori($gestioneavvisiid,$fornitore,$giorni_in_scadenza,$indirizzo_mittente,$nome_mittente){
	//require_once('plugins/script_schedulati/aggiorna_documenti.php'); /* kpro@bid070920181520 */
	global $adb, $table_prefix,$current_user;

	//AggiornaDocumenti($fornitore,$giorni_in_scadenza);
	
	$contatti_da_avvisare = ContattiDaAvvisare($gestioneavvisiid);

	$dati_fornitore = GetDatiFornitore($fornitore);
	
	$testo_mail = "";
	$lista_documenti = array();

	$q = "SELECT cont.contactid,
		CONCAT(cont.lastname,' ',cont.firstname) AS nome_risorsa
		FROM {$table_prefix}_contactdetails cont
		INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = cont.contactid
		WHERE ent.deleted = 0 AND cont.vendor_id = 0".$fornitore;
	
	$res = $adb->query($q);
	$num = $adb->num_rows($res);
	
	for($i = 0; $i < $num; $i++){	

		$risorsa = $adb->query_result($res, $i, 'contactid');
		$risorsa = html_entity_decode(strip_tags($risorsa), ENT_QUOTES, $default_charset);

		$nome_risorsa = $adb->query_result($res, $i, 'nome_risorsa');
		$nome_risorsa = html_entity_decode(strip_tags($nome_risorsa), ENT_QUOTES, $default_charset);

		$q_documenti = "SELECT * 
					FROM (
						SELECT note.notesid notesid, 
						note.title title, 
						td.nome_tipo_documento nome_tipo_documento,
						note.data_scadenza data_scadenza, 
						note.stato_documento stato_documento 
						FROM {$table_prefix}_notes note
						LEFT JOIN {$table_prefix}_tipidocumenti td ON td.tipidocumentiid = note.kp_tipo_documento
						INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = note.notesid
						INNER JOIN {$table_prefix}_senotesrel noterel ON noterel.notesid = note.notesid
						WHERE ent.deleted = 0 AND (note.stato_documento = 'Scaduto' OR note.stato_documento = 'In scadenza')
						AND noterel.crmid = {$risorsa}
						UNION
						SELECT 0 AS notesid,
						td.nome_tipo_documento title,
						td.nome_tipo_documento nome_tipo_documento,
						'' AS data_scadenza,
						sitdoc.kp_stato_sit_doc_f stato_documento
						FROM {$table_prefix}_kpsituazionedocfornit sitdoc
						INNER JOIN {$table_prefix}_tipidocumenti td ON td.tipidocumentiid = sitdoc.kp_tipo_documento
						INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sitdoc.kpsituazionedocfornitid
						WHERE ent.deleted = 0 AND sitdoc.kp_stato_sit_doc_f = 'Non eseguito'
						AND sitdoc.kp_fornitore = {$fornitore} AND sitdoc.kp_risorsa_fornit = {$risorsa}
					) AS i
					ORDER BY i.notesid, i.stato_documento, i.data_scadenza";

		$res_documenti = $adb->query($q_documenti);			
		$num_documenti = $adb->num_rows($res_documenti);
		
		for($j = 0; $j < $num_documenti; $j++){
			$documentoid = $adb->query_result($res_documenti,$j,'notesid');
			if($documentoid != 0){
				$lista_documenti[] = $documentoid;
			}
		
			$title = $adb->query_result($res_documenti,$j,'title');
			$title = html_entity_decode(strip_tags($title), ENT_QUOTES,$default_charset);

			$nome_tipo_documento = $adb->query_result($res_documenti,$j,'nome_tipo_documento');
			$nome_tipo_documento = html_entity_decode(strip_tags($nome_tipo_documento), ENT_QUOTES,$default_charset);
			if($nome_tipo_documento == null){
				$nome_tipo_documento = "";
			}

			if($nome_tipo_documento != ""){
				$title .= ' ('.$nome_tipo_documento.')';
			}
			
			$data_scadenza = $adb->query_result($res_documenti,$j,'data_scadenza');
			if($data_scadenza != '' && $data_scadenza != null && $data_scadenza != '0000-00-00'){
				$data_scadenza_dt = new DateTime($data_scadenza);
				$data_scadenza = $data_scadenza_dt->format("d-m-Y");
			}

			$stato_documento[$j] = $adb->query_result($res_documenti,$j,'stato_documento');

			if($j == 0){
				$testo_mail .= "<br />Risorsa <b>".$nome_risorsa."</b><br />";
			}
			
			if($j == 0 || $stato_documento[$j] != $stato_documento[$j-1]){
				if($stato_documento[$j] == 'In scadenza'){
					$testo_mail .= "- I seguenti documenti sono in scadenza:<br />";
				}
				elseif($stato_documento[$j] == 'Scaduto'){
					$testo_mail .= "- I seguenti documenti sono scaduti:<br />";
				}
				elseif($stato_documento[$j] == 'Non eseguito'){
					$testo_mail .= "- I seguenti tipi documenti non sono ancora stati redatti:<br />";
				}
			}
			
			if($stato_documento[$j] == 'In scadenza'){
				$testo_mail .= "--- ".$title." scadra' in data ".$data_scadenza."<br />";
			}
			elseif($stato_documento[$j] == 'Scaduto'){
				$testo_mail .= "--- ".$title." e' scaduto in data ".$data_scadenza."<br />";
			}
			elseif($stato_documento[$j] == 'Non eseguito'){
				$testo_mail .= "--- ".$title."<br />";
			}	

			//Qualora oltre alla mail volessi anche creare delle notifiche la parte di script sottostante fa questo
			/*if($documentoid != 0){
				$text = "Il documento ".$title." e' ".$stato_documento[$j];
				$focus = CRMEntity::getInstance('ModNotifications');
				$focus->saveFastNotification(
					array(
						'assigned_user_id' => 1,
						'related_to' => $documentoid,
						'mod_not_type' => 'Changed record',
						'createdtime' => date('Y-m-d H:i:s'),
						'modifiedtime' => date('Y-m-d H:i:s'),
						'description' => $text,
					),false
				);
			}*/
			//End script notifica

		}
	}

	$cont = 0;
	
	$q_documenti = "SELECT * 
				FROM (
					SELECT note.notesid notesid, 
					note.title title, 
					td.nome_tipo_documento nome_tipo_documento,
					note.data_scadenza data_scadenza, 
					note.stato_documento stato_documento 
					FROM {$table_prefix}_notes note
					LEFT JOIN {$table_prefix}_tipidocumenti td ON td.tipidocumentiid = note.kp_tipo_documento
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = note.notesid
					INNER JOIN {$table_prefix}_senotesrel noterel ON noterel.notesid = note.notesid
					WHERE ent.deleted = 0 AND (note.stato_documento = 'Scaduto' OR note.stato_documento = 'In scadenza')
					AND noterel.crmid = {$fornitore}
					UNION
					SELECT 0 AS notesid,
					td.nome_tipo_documento title,
					td.nome_tipo_documento nome_tipo_documento,
					'' AS data_scadenza,
					sitdoc.kp_stato_sit_doc_f stato_documento
					FROM {$table_prefix}_kpsituazionedocfornit sitdoc
					INNER JOIN {$table_prefix}_tipidocumenti td ON td.tipidocumentiid = sitdoc.kp_tipo_documento
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = sitdoc.kpsituazionedocfornitid
					WHERE ent.deleted = 0 AND sitdoc.kp_stato_sit_doc_f = 'Non eseguito'
					AND sitdoc.kp_fornitore = {$fornitore} AND (sitdoc.kp_risorsa_fornit IS NULL 
						OR sitdoc.kp_risorsa_fornit = '' OR sitdoc.kp_risorsa_fornit = 0)
				) AS i
				ORDER BY i.notesid, i.stato_documento, i.data_scadenza";
	$res_documenti = $adb->query($q_documenti);			
	$num_documenti = $adb->num_rows($res_documenti);
	
	for($i=0; $i<$num_documenti; $i++){
		$documentoid = $adb->query_result($res_documenti,$i,'notesid');

		if (!in_array($documentoid, $lista_documenti)) {
		
			$title = $adb->query_result($res_documenti,$i,'title');
			$title = html_entity_decode(strip_tags($title), ENT_QUOTES,$default_charset);

			$nome_tipo_documento = $adb->query_result($res_documenti,$i,'nome_tipo_documento');
			$nome_tipo_documento = html_entity_decode(strip_tags($nome_tipo_documento), ENT_QUOTES,$default_charset);
			if($nome_tipo_documento == null){
				$nome_tipo_documento = "";
			}

			if($nome_tipo_documento != ""){
				$title .= ' ('.$nome_tipo_documento.')';
			}
			
			$data_scadenza = $adb->query_result($res_documenti,$i,'data_scadenza');
			if($data_scadenza != '' && $data_scadenza != null && $data_scadenza != '0000-00-00'){
				$data_scadenza_dt = new DateTime($data_scadenza);
				$data_scadenza = $data_scadenza_dt->format("d-m-Y");
			}

			$stato_documento[$cont] = $adb->query_result($res_documenti,$i,'stato_documento');

			if($cont == 0){
				$testo_mail .= "<br /><b>Documenti collegati solo al fornitore</b><br />";
			}
			
			if($cont == 0 || $stato_documento[$cont] != $stato_documento[$cont-1]){
				if($stato_documento[$cont] == 'In scadenza'){
					$testo_mail .= "-I seguenti documenti sono in scadenza:<br />";
				}
				elseif($stato_documento[$cont] == 'Scaduto'){
					$testo_mail .= "-I seguenti documenti sono scaduti:<br />";
				}
			}
			
			if($stato_documento[$cont] == 'In scadenza'){
				$testo_mail .= "--- ".$title." scadra' in data ".$data_scadenza."<br />";
			}
			elseif($stato_documento[$cont] == 'Scaduto'){
				$testo_mail .= "--- ".$title." e' scaduto in data ".$data_scadenza."<br />";
			}
			
			//Qualora oltre alla mail volessi anche creare delle notifiche la parte di script sottostante fa questo
			/*$text = "Il documento ".$title." e' ".$stato_documento[$cont];
			$focus = CRMEntity::getInstance('ModNotifications');
			$focus->saveFastNotification(
				array(
					'assigned_user_id' => 1,
					'related_to' => $documentoid,
					'mod_not_type' => 'Changed record',
					'createdtime' => date('Y-m-d H:i:s'),
					'modifiedtime' => date('Y-m-d H:i:s'),
					'description' => $text,
					),false
				);*/
			//End script notifica

			$cont++;
		}
	
	}
	
	if($testo_mail != ''){
		$soggetto_mail = "Alert Documenti Fornitori (".$dati_fornitore['nome'].")";

		$testo_mail = "Notifiche Documenti Fornitori:<br /><br />".$testo_mail;
		
		foreach($contatti_da_avvisare as $contatto_mail){
			$q_mail_destinatario = "SELECT email FROM {$table_prefix}_contactdetails WHERE contactid =".$contatto_mail;
			$res_mail_destinatario = $adb->query($q_mail_destinatario);
			if($adb->num_rows($res_mail_destinatario)>0){
				$email_destinatario = $adb->query_result($res_mail_destinatario,0,'email');
				$mail = send_mail('GestioneAvvisi',$email_destinatario,$nome_mittente,$indirizzo_mittente,$soggetto_mail,$testo_mail);
			}
		}
	}
	 
	$control = 'si';
	
	return $control;
	
}

function AvvisiCheckList($gestioneavvisiid,$azienda,$giorni_in_scadenza,$indirizzo_mittente,$nome_mittente){
	require_once('plugins/script_schedulati/aggiorna_check_list.php');
	global $adb, $table_prefix,$current_user;
	
	$contatti_da_avvisare = ContattiDaAvvisare($gestioneavvisiid);

	$dati_azienda = GetDatiAzienda($azienda);
	
	$testo_mail = "";
	
	$q_checklist = "SELECT 
					sit.situazchecklistid situazchecklistid,
					sit.stato_sit_check stato_sit_check,
					sit.data_scad_man data_scad_man,
					imp.impianto_name impianto_name,
					comp.nome_componente nome_componente,
					checkl.nome_check_list nome_check_list
					from {$table_prefix}_situazchecklist sit
					inner join {$table_prefix}_crmentity ent on ent.crmid = sit.situazchecklistid
					inner join {$table_prefix}_impianti imp on imp.impiantiid = sit.impianto
					inner join {$table_prefix}_compimpianto comp on comp.compimpiantoid = sit.componente
					inner join {$table_prefix}_checklists checkl on checkl.checklistsid = sit.check_list
					where ent.deleted = 0 and sit.stato_sit_check in ('Non eseguita', 'Scaduta', 'In scadenza')
					order by sit.stato_sit_check, sit.data_scad_man asc, sit.impianto asc, sit.componente asc";
	$res_checklist = $adb->query($q_checklist);			
	$num_checklist = $adb->num_rows($res_checklist);
	
	for($i=0; $i<$num_checklist; $i++){
		$situazchecklistid = $adb->query_result($res_checklist, $i, 'situazchecklistid');
		$situazchecklistid = html_entity_decode(strip_tags($situazchecklistid), ENT_QUOTES,$default_charset);
		
		$stato_sit_check[$i] = $adb->query_result($res_checklist, $i, 'stato_sit_check');
		$stato_sit_check[$i] = html_entity_decode(strip_tags($stato_sit_check[$i]), ENT_QUOTES,$default_charset);
		
		$impianto_name = $adb->query_result($res_checklist, $i, 'impianto_name');
		$impianto_name = html_entity_decode(strip_tags($impianto_name), ENT_QUOTES,$default_charset);
		
		$nome_componente = $adb->query_result($res_checklist, $i, 'nome_componente');
		$nome_componente = html_entity_decode(strip_tags($nome_componente), ENT_QUOTES,$default_charset);
		
		$nome_check_list = $adb->query_result($res_checklist, $i, 'nome_check_list');
		$nome_check_list = html_entity_decode(strip_tags($nome_check_list), ENT_QUOTES,$default_charset);
		
		$data_scad_man = $adb->query_result($res_checklist, $i, 'data_scad_man');
		$data_scad_man = html_entity_decode(strip_tags($data_scad_man), ENT_QUOTES,$default_charset);
		if($data_scad_man != '' && $data_scad_man != null && $data_scad_man != '0000-00-00'){
			$data_scad_man_dt = new DateTime($data_scad_man);
			$data_scad_man = $data_scad_man_dt->format("d-m-Y");
		}
		
		if($i==0 || $stato_sit_check[$i] != $stato_sit_check[$i-1]){
			if($stato_sit_check[$i] == 'In scadenza'){
				$testo_mail .= "<br /><br />Le seguenti check list sono in scadenza:<br />";
			}
			elseif($stato_sit_check[$i] == 'Scaduta'){
				$testo_mail .= "<br /><br />Le seguenti check list sono scadute:<br />";
			}
			elseif($stato_sit_check[$i] == 'Non eseguita'){
				$testo_mail .= "<br /><br />Le seguenti check list non sono state eseguite:<br />";
			}
		}
		
		if($stato_sit_check[$i] == 'In scadenza'){
			$testo_mail .= "- La check list ".$nome_check_list." per il componente ".$nome_componente." (".$impianto_name.") scadra' in data ".$data_scad_man."<br />";
		}
		elseif($stato_sit_check[$i] == 'Scaduta'){
			$testo_mail .= "- La check list ".$nome_check_list." per il componente ".$nome_componente." (".$impianto_name.") e' scaduta in data ".$data_scad_man."<br />";
		}
		elseif($stato_sit_check[$i] == 'Non eseguita'){
			$testo_mail .= "- La check list ".$nome_check_list." per il componente ".$nome_componente." (".$impianto_name.") non e' stata eseguita<br />";
		}
		
		//Qualora oltre alla mail volessi anche creare delle notifiche la parte di script sottostante fa questo
		/*$text = "La check list ".$nome_check_list." e' ".$stato_sit_check[$i];
		$focus = CRMEntity::getInstance('ModNotifications');
		$focus->saveFastNotification(
			array(
				'assigned_user_id' => 1,
				'related_to' => $situazchecklistid,
				'mod_not_type' => 'Changed record',
				'createdtime' => date('Y-m-d H:i:s'),
				'modifiedtime' => date('Y-m-d H:i:s'),
				'description' => $text,
				),false
			);*/
		//End script notifica
		
	}
	
	if($testo_mail != ''){
		$soggetto_mail = "Alert Check List Impianti (".$dati_azienda['nome'].")";

		$testo_mail = "Notifiche Check List Impianti:<br />".$testo_mail;
		
		foreach($contatti_da_avvisare as $contatto_mail){
			$q_mail_destinatario = "SELECT email FROM {$table_prefix}_contactdetails WHERE contactid =".$contatto_mail;
			$res_mail_destinatario = $adb->query($q_mail_destinatario);
			if($adb->num_rows($res_mail_destinatario)>0){
				$email_destinatario = $adb->query_result($res_mail_destinatario,0,'email');
				$email_destinatario = html_entity_decode(strip_tags($email_destinatario), ENT_QUOTES,$default_charset);
				
				$mail = send_mail('GestioneAvvisi',$email_destinatario,$nome_mittente,$indirizzo_mittente,$soggetto_mail,$testo_mail);
			}
		}
	}
	 
	$control = 'si';
	
	return $control;
	
}

function AvvisiLettereDiNomina($gestioneavvisiid, $azienda, $giorni_in_scadenza, $indirizzo_mittente, $nome_mittente){
	global $adb, $table_prefix, $current_user;

	$contatti_da_avvisare = ContattiDaAvvisare($gestioneavvisiid);

	$dati_azienda = GetDatiAzienda($azienda);
	
	$testo_mail = "";

	$query = "SELECT 
				let.kpletterenominaid kpletterenominaid,
				let.kp_soggetto kp_soggetto,
				let.kp_risorsa kp_risorsa,
				let.kp_azienda kp_azienda,
				let.kp_fornitore kp_fornitore,
				let.kp_stabilimento kp_stabilimento,
				let.kp_data kp_data,
				let.kp_stato kp_stato,
				let.kp_tipo_lettera kp_tipo_lettera,
				cont.lastname lastname,
				cont.firstname firstname
				FROM {$table_prefix}_kpletterenomina let
				INNER JOIN {$table_prefix}_contactdetails cont ON cont.contactid = let.kp_risorsa
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = let.kpletterenominaid
				WHERE ent.deleted = 0 AND let.kp_stato = 'Da consegnare' 
				AND let.kp_azienda = ".$azienda;

	$result_query = $adb->query($query);			
	$num_result = $adb->num_rows($result_query);
	
	for($i = 0; $i < $num_result; $i++){

		$id = $adb->query_result($result_query, $i, 'kpletterenominaid');
		$id = html_entity_decode(strip_tags($id), ENT_QUOTES,$default_charset);

		$soggetto = $adb->query_result($result_query, $i, 'kp_soggetto');
		$soggetto = html_entity_decode(strip_tags($soggetto), ENT_QUOTES,$default_charset);

		$testo_mail .= "- ".$soggetto."<br />";

	}

	if($testo_mail != ''){
		$soggetto_mail = "Alert Lettere di Nomina (".$dati_azienda['nome'].")";

		$testo_mail = "Notifiche Lettere di Nomina:<br /><br />
Le seguenti lettere di nomina non sono state consegnate:<br />".$testo_mail;
		
		foreach($contatti_da_avvisare as $contatto_mail){
			$q_mail_destinatario = "SELECT email FROM {$table_prefix}_contactdetails WHERE contactid =".$contatto_mail;
			$res_mail_destinatario = $adb->query($q_mail_destinatario);
			if($adb->num_rows($res_mail_destinatario)>0){
				$email_destinatario = $adb->query_result($res_mail_destinatario,0,'email');
				$email_destinatario = html_entity_decode(strip_tags($email_destinatario), ENT_QUOTES,$default_charset);
				
				$mail = send_mail('GestioneAvvisi', $email_destinatario, $nome_mittente, $indirizzo_mittente, $soggetto_mail, $testo_mail);
			}
		}
	}
	 
	$control = 'si';
	
	return $control;

}

?>
