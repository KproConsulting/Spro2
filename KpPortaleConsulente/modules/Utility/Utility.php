<?php

/* kpro@tom07042017 */
		
/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

function verificaSeFornitoreAbilitatoPerAzienda($azienda, $fornitore){
    global $adb, $table_prefix, $current_user, $default_charset;

    $result = false;

    $query = "SELECT 
                tick.ticketid ticketid
                FROM {$table_prefix}_troubletickets tick
                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
                WHERE ent.deleted = 0 AND tick.kp_fornitore = ".$fornitore." AND tick.parent_id = ".$azienda;
    
    $result_query = $adb->query($query);
    $num_result = $adb->num_rows($result_query);

    if($num_result > 0){
        $result = true;
    }
    else{

        //Qualora non esiste alcun ticket per tale azienda verifico se ne esiste per una azienda dismessa relativa allo stesso cliente
        $dati_cliente_precedente = verificaSePresenteClientePrecedente($azienda);

        if($dati_cliente_precedente["esiste"]){

            //Se relativamente a tale cliente esiste un'azieda dismessa verifico se questa è abilitata per il fornitore
            $result = verificaSeFornitoreAbilitatoPerAzienda($dati_cliente_precedente["id"], $fornitore);

        }

    }

    return $result;

}

function getOrePianificateTicket($ticket, $fornitore){
    global $adb, $table_prefix, $current_user, $default_charset;

    $result = 0;

    $query = "SELECT 
				COALESCE(SUM(kp_durata_prevista), 0) tot_ore_previste
				FROM {$table_prefix}_activity act
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = act.activityid
				INNER JOIN {$table_prefix}_seactivityrel actrel ON actrel.activityid = act.activityid
				INNER JOIN {$table_prefix}_troubletickets tick ON tick.ticketid = actrel.crmid
				WHERE ent.deleted = 0  AND tick.ticketid = ".$ticket." AND tick.kp_fornitore = ".$fornitore;

    $result_query = $adb->query($query);
    $num_result = $adb->num_rows($result_query);

    if($num_result > 0){

        $tot_ore_previste = $adb->query_result($result_query, 0, 'tot_ore_previste');
	    $tot_ore_previste = html_entity_decode(strip_tags($tot_ore_previste), ENT_QUOTES, $default_charset);

        $result = $tot_ore_previste;

    }

    return $result;

}

function verificaSeFornitoreAbilitatoPerIntervento($intervento, $fornitore){
    global $adb, $table_prefix, $current_user, $default_charset;
	
	$result = false;

    $query = "SELECT 
				tc.timecardsid timecardsid
				FROM {$table_prefix}_timecards tc
				INNER JOIN {$table_prefix}_troubletickets tick ON tick.ticketid = tc.ticket_id
				WHERE tick.kp_fornitore = ".$fornitore." AND tc.timecardsid = ".$intervento;
    
    $result_query = $adb->query($query);
    $num_result = $adb->num_rows($result_query);

    if($num_result > 0){
        $result = true;
    }

    return $result;
	
}

function verificaSeFornitoreAbilitatoPerTicket($ticket, $fornitore){
    global $adb, $table_prefix, $current_user, $default_charset;
	
	$result = false;

    $query = "SELECT 
				tick.ticketid ticketid
				FROM {$table_prefix}_troubletickets tick
				WHERE tick.kp_fornitore = ".$fornitore." AND tick.ticketid = ".$ticket;
    
    $result_query = $adb->query($query);
    $num_result = $adb->num_rows($result_query);

    if($num_result > 0){
        $result = true;
    }

    return $result;
	
}

function verificaSeFornitoreAbilitatoPerEvento($evento, $fornitore){
    global $adb, $table_prefix, $current_user, $default_charset;
	
	$result = false;

    $query = "SELECT 
				act.activityid activityid
				FROM {$table_prefix}_activity act
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = act.activityid
				INNER JOIN {$table_prefix}_seactivityrel actrel ON actrel.activityid = act.activityid
				INNER JOIN {$table_prefix}_troubletickets tick ON tick.ticketid = actrel.crmid
				WHERE ent.deleted = 0 AND tick.kp_fornitore = ".$fornitore." AND act.activityid = ".$evento;
    
    $result_query = $adb->query($query);
    $num_result = $adb->num_rows($result_query);

    if($num_result > 0){
        $result = true;
    }

    return $result;
	
}

function getDatiAccount($id){
	global $adb, $table_prefix, $current_user, $site_URL, $default_language;

	$result = "";

	$query = "SELECT 
				acc.accountid accountid,
				acc.accountname accountname,
				acc.phone phone,
				acc.email1 email1,
				acc.crmv_vat_registration_number crmv_vat_registration_number,
				acc.crmv_social_security_number crmv_social_security_number,
				acc.external_code external_code,
				acc.kp_ex_codice_erp ex_codice_erp
				FROM {$table_prefix}_account acc
				WHERE acc.accountid = ".$id;

	$result_query = $adb->query($query);
	$num_result = $adb->num_rows($result_query);

	if($num_result > 0){

		$accountid = $adb->query_result($result_query, 0, 'accountid');
		$accountid = html_entity_decode(strip_tags($accountid), ENT_QUOTES, $default_charset);

		$accountname = $adb->query_result($result_query, 0, 'accountname');
		$accountname = html_entity_decode(strip_tags($accountname), ENT_QUOTES, $default_charset);

		$phone = $adb->query_result($result_query, 0, 'phone');
		$phone = html_entity_decode(strip_tags($phone), ENT_QUOTES, $default_charset);

		$email1 = $adb->query_result($result_query, 0, 'email1');
		$email1 = html_entity_decode(strip_tags($email1), ENT_QUOTES, $default_charset);

		$crmv_vat_registration_number = $adb->query_result($result_query, 0, 'crmv_vat_registration_number');
		$crmv_vat_registration_number = html_entity_decode(strip_tags($crmv_vat_registration_number), ENT_QUOTES, $default_charset);

		$crmv_social_security_number = $adb->query_result($result_query, 0, 'crmv_social_security_number');
		$crmv_social_security_number = html_entity_decode(strip_tags($crmv_social_security_number), ENT_QUOTES, $default_charset);

		$external_code = $adb->query_result($result_query, 0, 'external_code');
		$external_code = html_entity_decode(strip_tags($external_code), ENT_QUOTES, $default_charset);
		if($external_code == null){
			$external_code = "";
		}

		$ex_codice_erp = $adb->query_result($result_query, 0, 'ex_codice_erp');
		$ex_codice_erp = html_entity_decode(strip_tags($ex_codice_erp), ENT_QUOTES, $default_charset);
		

	}
	else{

		$accountid = 0;
		$accountname = "";
		$phone = "";
		$email1 = "";
		$crmv_vat_registration_number = "";
		$crmv_social_security_number = "";
		$ex_codice_erp = "";
		$external_code = "";

	}

	$result = array("accountid" => $accountid,
					"accountname" => $accountname,
					"phone" => $phone,
					"email1" => $email1,
					"crmv_vat_registration_number" => $crmv_vat_registration_number,
					"crmv_social_security_number" => $crmv_social_security_number,
					"ex_codice_erp" => $ex_codice_erp,
					"external_code" => $external_code);

	return $result;

}

function verificaSeClienteDismesso($id){
	global $adb, $table_prefix, $current_user, $site_URL, $default_language;

	$result = "";

	$dati_azienda = getDatiAccount($id);

	$dismesso = false;

	if($dati_azienda["external_code"] != ""){

		$query = "SELECT 
					acc.accountid accountid
					FROM {$table_prefix}_account acc 
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = acc.accountid
					WHERE ent.deleted = 0 AND acc.kp_ex_codice_erp = '".$dati_azienda["external_code"]."'";

		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if($num_result > 0){

			$accountid = $adb->query_result($result_query, 0, 'accountid');
			$accountid = html_entity_decode(strip_tags($accountid), ENT_QUOTES, $default_charset);

			//$dati_azienda_nuova_azienda = getDatiAccount($accountid);

			$dismesso = true;

		}

	}

	$result = array("dismesso" => $dismesso,
                     "id" => $accountid);

	return $result;

}

function verificaSePresenteClientePrecedente($id){
	global $adb, $table_prefix, $current_user, $site_URL, $default_language;

	$result = "";

    $esiste = false;
    $accountid = 0;

    $dati_azienda = getDatiAccount($id);

    if($dati_azienda["ex_codice_erp"] != ""){

        $query = "SELECT 
					acc.accountid accountid
					FROM {$table_prefix}_account acc 
					INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = acc.accountid
					WHERE ent.deleted = 0 AND acc.external_code = '".$dati_azienda["ex_codice_erp"]."'";

		$result_query = $adb->query($query);
		$num_result = $adb->num_rows($result_query);

		if($num_result > 0){

			$accountid = $adb->query_result($result_query, 0, 'accountid');
			$accountid = html_entity_decode(strip_tags($accountid), ENT_QUOTES, $default_charset);

            $esiste = true;

		}

    }

    $result = array("esiste" => $esiste,
                    "id" => $accountid);

    return $result;

}

function getTemplateMail($id){
	global $adb, $table_prefix, $current_user, $site_URL, $default_language;

	$result = "";

	$query = "SELECT 
				templatename,
				subject,
				description,
				body
				FROM {$table_prefix}_emailtemplates 
				WHERE templateid = ".$id;

    $result_query= $adb->query($query);
    if($adb->num_rows($result_query)>0){

        $templatename = $adb->query_result($result_query, 0, 'templatename');
        $templatename = html_entity_decode(strip_tags($templatename), ENT_QUOTES,$default_charset);
        
        $subject = $adb->query_result($result_query, 0, 'subject');
        $subject = html_entity_decode(strip_tags($subject), ENT_QUOTES,$default_charset);
        
        $description = $adb->query_result($result_query, 0, 'description');
        $description = html_entity_decode(strip_tags($description), ENT_QUOTES,$default_charset);
        
        $body = $adb->query_result($result_query,0,'body');
        $body = html_entity_decode(strip_tags($body), ENT_QUOTES,$default_charset);

    }
	else{
		$templatename = "";
		$subject = "";
		$description = "";
		$body = "";
	}

	$result = array('templatename' => $templatename,
					'subject' => $subject,
					'description' => $description,
					'body' => $body);

    return $result;

}

/*function send_mail_kp($module,$to_email,$from_name,$from_email,$subject,$contents,$cc='',$bcc='',$attachment='',$emailid='',$logo='',$newsletter_params='',&$mail_tmp='',$messageid='',$message_mode=''){
	global $adb, $log, $table_prefix;
	global $root_directory;
	global $HELPDESK_SUPPORT_EMAIL_ID, $HELPDESK_SUPPORT_NAME;
	
	$uploaddir = $root_directory ."/test/upload/";

	$adb->println("To id => '".$to_email."'\nSubject ==>'".$subject."'\nContents ==> '".$contents."'");

	if($from_email == ''){
		$from_email = getUserEmailId('user_name',$from_name);
	}
	
	$mail = new PHPMailer();
	
	$mail->Subject = $subject;
	
	if (is_array($contents)) {
		$mail->Body = $contents['html'];
		$mail->AltBody = $contents['text'];
	} else {
		$mail->Body = $contents;
		$mail->AltBody = strip_tags(preg_replace(array("/<p>/i","/<br>/i","/<br \/>/i"),array("\n","\n","\n"),$contents));
	}
	
	$mail->IsSMTP();
	setMailServerProperties($mail, $from_email);	
	$mail->From = $from_email;
	$mail->FromName = $from_name;
	if($to_email != '')
	{
		if(is_array($to_email)) {
			foreach($to_email as $e) {
				$mail->addAddress($e);
			}
		} else {
			$_tmp = explode(",",trim($to_email,","));
			foreach($_tmp as $e) {
				$mail->addAddress($e);
			}
		}
	}

	$mail->AddReplyTo($from_email);
	$mail->WordWrap = 50;
	
	if($attachment != '')
	{
		
		if(is_array($attachment)) {
			foreach($attachment as $e) {
				
				$q_allegato = "SELECT 
								att.attachmentsid attachmentsid,
								att.name filename,
								att.path cartella
								FROM {$table_prefix}_seattachmentsrel attrel
								INNER JOIN {$table_prefix}_attachments att ON att.attachmentsid = attrel.attachmentsid
								WHERE attrel.crmid = ".$e;
				$res_allegato = $adb->query($q_allegato);
				if($adb->num_rows($res_allegato)>0){
					$attachmentsid = $adb->query_result($res_allegato,0,'attachmentsid');
					$filename = $adb->query_result($res_allegato,0,'filename');
					$cartella = $adb->query_result($res_allegato,0,'cartella');
					
					$link_allegato = $cartella.$attachmentsid."_".$filename;
					$mail->AddAttachment($link_allegato);
				}
					
			}
		} else {
			
			$_tmp = explode(",",trim($attachment,","));
			foreach($_tmp as $e) {
				
				$q_allegato = "SELECT 
								att.attachmentsid attachmentsid,
								att.name filename,
								att.path cartella
								FROM {$table_prefix}_seattachmentsrel attrel
								INNER JOIN {$table_prefix}_attachments att ON att.attachmentsid = attrel.attachmentsid
								WHERE attrel.crmid = ".$e;
				$res_allegato = $adb->query($q_allegato);
				if($adb->num_rows($res_allegato)>0){
					$attachmentsid = $adb->query_result($res_allegato,0,'attachmentsid');
					$filename = $adb->query_result($res_allegato,0,'filename');
					$cartella = $adb->query_result($res_allegato,0,'cartella');
					
					$link_allegato = $cartella.$attachmentsid."_".$filename;
					$mail->AddAttachment($link_allegato);
				}
				
			}
		} 
	}
	
	$mail->IsHTML(true);

	if($newsletter_params && $newsletter_params['smtp_config']['enable'] === true) {
		if($newsletter_params['smtp_config']['smtp_auth'] == "true"){
			$mail->SMTPAuth = true;	
		} else {
			$mail->SMTPAuth = false;	
		}
		$mail->Host = $newsletter_params['smtp_config']['server'];		
		$mail->Username = $newsletter_params['smtp_config']['server_username'] ;	
        $mail->Password = $newsletter_params['smtp_config']['server_password'] ;	
	}

	setCCAddress($mail,'cc',$cc);
	setCCAddress($mail,'bcc',$bcc);

    if(empty($mail->Host)) {
		return 0;
    }

    if ($newsletter_params) {
    	if ($newsletter_params['sender'] != '') {
			$mail->Sender = $newsletter_params['sender'];
			$mail->addCustomHeader("Errors-To: ".$newsletter_params['sender']);
    	}
    	if ($newsletter_params['newsletterid'] != '') {
			$mail->addCustomHeader("X-MessageID: ".$newsletter_params['newsletterid']);
    	}
    	if ($newsletter_params['crmid'] != '') {
			$mail->addCustomHeader("X-ListMember: ".$newsletter_params['crmid']);
    	}
    	$mail->addCustomHeader("Precedence: bulk");
    }
    //crmv@22700e

    if (!empty($messageid) && in_array($message_mode,array('reply','reply_all','forward'))) {
    	$focusMessage = CRMentity::getInstance('Messages');
    	$result = $focusMessage->retrieve_entity_info_no_html($messageid,'Messages',false);
    	if (empty($result)) {	// no errors
	    	$mail->addCustomHeader("In-Reply-To: ".$focusMessage->column_fields['messageid']);
	    	$mail->addCustomHeader("References: ".$focusMessage->column_fields['mreferences'].$focusMessage->column_fields['messageid']);
			//TODO: $mail->addCustomHeader("Thread-Index: ");
    	}
    }
	
    $mail_status = MailSend($mail);
	
	if($mail_status == 1) {
		$mail_tmp = $mail;
	} else {
		$error_string ='Send mail failed! from '.$from_email.' to '.$to_email.' subject '.$subject.' reason:'.$mail_status;
		$log->fatal($error_string);
	}
	$mail_error = $mail_status;
	return $mail_error;
}*/

function getDatiTicket($id){
    global $table_prefix, $adb, $default_charset;

    $query = "SELECT 
                tick.salesorder salesorder,
                tick.title title,
				tick.kp_data_consegna kp_data_consegna,
				tick.servizio servizio,
				tick.parent_id parent_id,
                ent.smownerid assegnatario
                FROM {$table_prefix}_troubletickets tick
                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
                WHERE tick.ticketid = ".$id;

    $result_query = $adb->query($query);
    $num_result = $adb->num_rows($result_query);

    if($num_result > 0){

        $salesorder = $adb->query_result($result_query, 0, 'salesorder');
        $salesorder = html_entity_decode(strip_tags($salesorder), ENT_QUOTES, $default_charset);
        if($salesorder == null || $salesorder == ""){
            $salesorder = 0;
        }

        $title = $adb->query_result($result_query, 0, 'title');
        $title = html_entity_decode(strip_tags($title), ENT_QUOTES, $default_charset);
        if($title == null || $title == ""){
            $title = "";
        }

        $assegnatario = $adb->query_result($result_query, 0, 'assegnatario');
        $assegnatario = html_entity_decode(strip_tags($assegnatario), ENT_QUOTES, $default_charset);
        if($assegnatario == null || $assegnatario == ""){
            $assegnatario = 1;
        }

		$data_consegna = $adb->query_result($result_query, 0, 'kp_data_consegna');
        $data_consegna = html_entity_decode(strip_tags($data_consegna), ENT_QUOTES, $default_charset);
        if($data_consegna == null || $data_consegna == "0000-00-00"){
            $data_consegna = "";
        }

		$servizio = $adb->query_result($result_query, 0, 'servizio');
        $servizio = html_entity_decode(strip_tags($servizio), ENT_QUOTES, $default_charset);
        if($servizio == null || $servizio == ""){
			$servizio = 0;
			$ore_minime_richieste = 0;
		}
		else{
			$dati_servizio = getDatiServizio($servizio);
			$ore_minime_richieste = $dati_servizio["ore_minime_richieste"];
		}

		$azienda = $adb->query_result($result_query, 0, 'parent_id');
        $azienda = html_entity_decode(strip_tags($azienda), ENT_QUOTES, $default_charset);
        if($azienda == null || $azienda == ""){
            $azienda = 0;
		}
		
		$ore_seguite = getOreEseguiteTicket($id);

    }
    else{

        $salesorder = 0;
        $title = "";
        $assegnatario = 1;
		$data_consegna = "";
		$servizio = 0;
		$azienda = 0;
		$ore_minime_richieste = 0;
		$ore_seguite = 0;

    }

    $result = array("salesorder" => $salesorder,
                    "title" => $title,
					"data_consegna" => $data_consegna,
					"servizio" => $servizio,
					"ore_minime_richieste" => $ore_minime_richieste,
					"ore_seguite" => $ore_seguite,
					"azienda" => $azienda,
                    "assegnatario" => $assegnatario);

    return $result;

}

function getDatiServizio($id){
    global $table_prefix, $adb, $default_charset;

    $query = "SELECT 
                serv.servicename servicename,
                serv.kp_codice_erp kp_codice_erp,
				serv.kp_abb_chiusura_tick kp_abb_chiusura_tick,
				serv.kp_ore_minime_ric kp_ore_minime_ric,
                ent.smownerid assegnatario
                FROM {$table_prefix}_service serv
                INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = serv.serviceid
                WHERE serv.serviceid = ".$id;

    $result_query = $adb->query($query);
    $num_result = $adb->num_rows($result_query);

    if($num_result > 0){

        $servicename = $adb->query_result($result_query, 0, 'servicename');
        $servicename = html_entity_decode(strip_tags($servicename), ENT_QUOTES, $default_charset);

        $codice_erp = $adb->query_result($result_query, 0, 'kp_codice_erp');
        $codice_erp = html_entity_decode(strip_tags($codice_erp), ENT_QUOTES, $default_charset);
        if($codice_erp == null || $codice_erp == ""){
            $codice_erp = "";
        }

        $assegnatario = $adb->query_result($result_query, 0, 'assegnatario');
        $assegnatario = html_entity_decode(strip_tags($assegnatario), ENT_QUOTES, $default_charset);
        if($assegnatario == null || $assegnatario == ""){
            $assegnatario = 1;
        }

		$abb_chiusura_tick = $adb->query_result($result_query, 0, 'kp_abb_chiusura_tick');
        $abb_chiusura_tick = html_entity_decode(strip_tags($abb_chiusura_tick), ENT_QUOTES, $default_charset);
        if($abb_chiusura_tick == "1"){
            $abb_chiusura_tick = true;
        }
		else{
			$abb_chiusura_tick = false;
		}

		$ore_minime_richieste = $adb->query_result($result_query, 0, 'kp_ore_minime_ric');
        $ore_minime_richieste = html_entity_decode(strip_tags($ore_minime_richieste), ENT_QUOTES, $default_charset);
        if($ore_minime_richieste == null || $ore_minime_richieste == ""){
            $ore_minime_richieste = 0;
        }

    }
    else{

        $servicename = "";
        $codice_erp = "";
		$assegnatario = 1;
		$ore_minime_richieste = 0;
		$abb_chiusura_tick = false;

    }

    $result = array("servicename" => $servicename,
                    "codice_erp" => $codice_erp,
					"abb_chiusura_tick" => $abb_chiusura_tick,
					"ore_minime_richieste" => $ore_minime_richieste,
                    "assegnatario" => $assegnatario);

    return $result;

}

function getNumeroTicketApertiPerCliente($azienda, $fornitore){
    global $table_prefix, $adb, $default_charset;

	$result = 0;

	$query = "SELECT 
				COALESCE(COUNT(*), 0) numero_ticket_aperti
				FROM {$table_prefix}_troubletickets tick
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tick.ticketid
				INNER JOIN {$table_prefix}_account acc ON acc.accountid = tick.parent_id
				LEFT JOIN {$table_prefix}_service serv ON serv.serviceid = tick.servizio
				WHERE ent.deleted = 0 AND tick.status NOT IN ('Closed') AND tick.kp_fornitore = ".$fornitore." AND tick.parent_id = ".$azienda;

	$result_query = $adb->query($query);
	$num_result = $adb->num_rows($result_query);

	if( $num_result > 0 ){

		$numero_ticket_aperti = $adb->query_result($result_query, 0, 'numero_ticket_aperti');
		$numero_ticket_aperti = html_entity_decode(strip_tags($numero_ticket_aperti), ENT_QUOTES, $default_charset);

	}
	else{

		$numero_ticket_aperti = 0;

	}

	$result = $numero_ticket_aperti;

	return $result;

}

function checkIfFornitoreRelazionatoAzienda($azienda, $fornitore){
    global $table_prefix, $adb, $default_charset;

	$result = false;

	$query = "SELECT 
				*
				FROM {$table_prefix}_crmentityrel
				WHERE (crmid = ".$azienda." AND module = 'Accounts' AND relcrmid = ".$fornitore." AND relmodule = 'Vendors')
				OR
				(relcrmid = ".$azienda." AND relmodule = 'Accounts' AND crmid = ".$fornitore." AND module = 'Vendors');";

	$result_query = $adb->query($query);
	$num_result = $adb->num_rows($result_query);

	if( $num_result > 0 ){

		$result = true;

	}

	return $result;

}

function getOreEseguiteTicket($ticket){
	global $table_prefix, $adb, $default_charset;
	
	$result = 0;

	$ore_seguite = 0;

	$query = "SELECT 
				tc.worktime worktime
				FROM {$table_prefix}_timecards tc
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = tc.timecardsid
				WHERE ent.deleted = 0 AND tc.ticket_id = ".$ticket;

	$result_query = $adb->query($query);
	$num_result = $adb->num_rows($result_query);

	for($i = 0; $i < $num_result; $i++){

		$worktime = $adb->query_result($result_query, $i, 'worktime');
		$worktime = html_entity_decode(strip_tags($worktime), ENT_QUOTES, $default_charset);

		list($ore, $minuti) = explode(":", $worktime); 

		$ore = (int)$ore;

		$minuti = ((int)$minuti) / 60 ;

		$ore_tot = $ore + $minuti;

		$ore_seguite += $ore_tot;

	}

	$result = $ore_seguite;

	return $result;

}

function getDataUltimoInterventoTicket($ticket){
	global $table_prefix, $adb, $default_charset;

	$result = "";

	$query = "SELECT
				timec.workdate workdate
				FROM {$table_prefix}_timecards timec
				INNER JOIN {$table_prefix}_crmentity ent ON ent.crmid = timec.timecardsid
				WHERE ent.deleted = 0 AND timec.ticket_id = ".$ticket."
				ORDER BY timec.workdate DESC";

	$result_query = $adb->query($query);
	$num_result = $adb->num_rows($result_query);

	if( $num_result > 0 ){

		$workdate = $adb->query_result($result_query, 0, 'workdate');
		$workdate = html_entity_decode(strip_tags($workdate), ENT_QUOTES, $default_charset);

		$result = $workdate;

	}

	return $result;

}

function getUtenteRisorsaFornitore($contact_id){
	global $table_prefix, $adb, $default_charset;

	$utente = 1;

	$q = "SELECT id 
		FROM {$table_prefix}_users
		WHERE status = 'Active'
		AND deleted = 0
		AND risorsa_collegata = ".$contact_id;
	$res = $adb->query($q);
	if($adb->num_rows($res) > 0){
		$utente = $adb->query_result($res, 0, 'id');
		$utente = html_entity_decode(strip_tags($utente), ENT_QUOTES, $default_charset);
		if($utente == '' || $utente == null || $utente == 0){
			$utente = 1;
		}
	}

	return $utente;
}
	
?>