/* kpro@bid24042018 */
/**
 * @author Bidese Jacopo
 * @copyright (c) 2018, Kpro Consulting Srl
 */

var larghezza_schermo;
var altezza_schermo;
var in_salvataggio = false;

// Extra

var jtable_canoni;
var jtable_tickets;
var jtable_report_attivita;
var jform_anno_fatturazione;
var jform_mese_fatturazione;
var jsearch_table_canoni_nome_canone;
var jsearch_table_canoni_azienda;
var jsearch_table_canoni_servizio;
var jcheck_canoni;
var jcheck_tickets;
var jcheck_report_attivita;
var jsearch_table_tickets_nome_ticket;
var jsearch_table_tickets_azienda;
var jsearch_table_tickets_servizio;
var jsearch_table_ra_nome_attivita;
var jsearch_table_ra_azienda;
var jsearch_table_ra_servizio;
var jsearch_table_ra_tipo_rimborso;
var jtable_ordini;
var jsearch_table_ordini_soggetto;
var jsearch_table_ordini_azienda;
var jsearch_table_ordini_data;
var jsearch_table_ordini_stato_ticket;
var jsearch_table_ordini_stato;

var filtro_canoni = {};
var filtro_tickets = {};
var filtro_report_attivita = {};
var filtro_ordini = {};

var ordini_generati = [];

// Extra End


window.addEventListener("load", function() {

    inizializzaStep();

    inizializzaWizard();

    inizializza();

    inizializzaExtra();

});

function reSize() {

    larghezza_schermo = window.innerWidth;
    altezza_schermo = window.innerHeight;

}

function in_array(needle, haystack, argStrict) {
    //needle: valore da ricercare
    //haystack: l'array in cui verificare se il testo contenuto della variabile 'needle' è presente

    var key = '',
        strict = !!argStrict;
    if (strict) {
        for (key in haystack) {
            if (haystack[key] === needle) {
                return true;
            }
        }
    } else {
        for (key in haystack) {
            if (haystack[key] == needle) {
                return true;
            }
        }
    }
    return false;
}

function inizializzaWizard() {

    // Toolbar extra buttons
    var btnFinish = jQuery('<button></button>').text('Fine')
        .addClass('btn btn-info')
        .on('click', function() {

            if (!jQuery(this).hasClass('disabled')) {
                var elmForm = jQuery("#myForm");
                if (elmForm) {
                    elmForm.validator('validate');
                    var elmErr = elmForm.find('.has-error');
                    if (elmErr && elmErr.length > 0) {
                        alert('Oops we still have error in the form');
                        return false;
                    } else {

                        chiudiPopUp();

                        return false;
                    }
                }
            }

        });

    var btnCancel = jQuery('<button></button>').text('Cancella')
        .addClass('btn btn-danger')
        .on('click', function() {
            jQuery('#smartwizard').smartWizard("reset");
            jQuery('#myForm').find("input, textarea").val("");
            chiudiPopUp();
        });

    var btnGenera = jQuery('<button></button>').text('Genera')
        .addClass('btn btn-success')
        .on('click', function() {
            
            var current_step_href = jQuery('#smartwizard > ul > li.active > a').prop('href');
            var current_step = current_step_href.substring(current_step_href.length - 6);
            if(current_step == 'step-2'){
                generaOdfCanoni();
            }
            else if(current_step == 'step-3'){
                generaOdfTickets();
            }
            else if(current_step == 'step-4'){
                generaOdfReportAttivita();
            }
        });

    // Smart Wizard
    jQuery('#smartwizard').smartWizard({
        selected: 0,
        theme: 'circles',
        transitionEffect: 'fade',
        toolbarSettings: {
            toolbarPosition: 'top',
            toolbarExtraButtons: [btnFinish, btnGenera, btnCancel]
        },
        anchorSettings: {
            markDoneStep: true, // add done css
            markAllPreviousStepsAsDone: true, // When a step selected by url hash, all previous steps are marked done
            removeDoneStepOnNavigateBack: true, // While navigate back done step after active step will be cleared
            enableAnchorOnDoneStep: true // Enable/Disable the done steps navigation
        }
    });

    jQuery("#smartwizard").on("leaveStep", function(e, anchorObject, stepNumber, stepDirection) {
        var elmForm = jQuery("#form-step-" + stepNumber);
        // stepDirection === 'forward' :- this condition allows to do the form validation 
        // only on forward navigation, that makes easy navigation on backwards still do the validation when going next
        if (stepDirection === 'forward' && elmForm) {
            elmForm.validator('validate');
            var elmErr = elmForm.children('.has-error');
            if (elmErr && elmErr.length > 0) {
                // Form validation failed
                return false;
            }

            //Controlli personalizzati sullo step
            if (stepNumber == 0) {

                anno = jform_anno_fatturazione.val();
                mese = jform_mese_fatturazione.val();

                getStep2();

            } 
            else if (stepNumber == 1) {

                /* kpro@bid040920181110 */
                if(ControlloSelezionati('check_canone_')){
                    getStep3();
                }
                else{
                    return false;
                }
                /* kpro@bid040920181110 end */

            } else if (stepNumber == 2) {

                /* kpro@bid040920181110 */
                if(ControlloSelezionati('check_ticket_')){
                    getStep4();
                }
                else{
                    return false;
                }
                /* kpro@bid040920181110 end */

            } else if (stepNumber == 3) {

                /* kpro@bid040920181110 */
                if(ControlloSelezionati('check_report_attivita_')){
                    getStep5();
                }
                else{
                    return false;
                }
                /* kpro@bid040920181110 end */

            } 

            //Controlli personalizzati sullo step end

        }
        return true;
    });

    jQuery("#smartwizard").on("showStep", function(e, anchorObject, stepNumber, stepDirection) {
        // Abilita il tasto 'Fine' solo nell'ultimo step
        if (stepNumber > 0 && stepNumber < 4) {
            jQuery('.btn-success').removeClass('disabled');
            jQuery('.btn-success').show();
        } else {
            jQuery('.btn-success').addClass('disabled');
            jQuery('.btn-success').hide();
        }

        if (stepNumber == 4) {
            jQuery('.btn-info').removeClass('disabled');
            jQuery('.btn-info').show();
        } else {
            jQuery('.btn-info').addClass('disabled');
            jQuery('.btn-info').hide();
        }
    });

    jQuery('.btn-success').addClass('disabled');
    jQuery('.btn-success').hide();

    jQuery('.btn-info').addClass('disabled');
    jQuery('.btn-info').hide();

    jQuery('.sw-btn-prev').text('Precedente');
    jQuery('.sw-btn-next').text('Successivo');

}

function inizializza() {

    reSize();

    window.addEventListener('resize', function() {
        reSize();
    }, false);

    var eventMethod = window.addEventListener
			? "addEventListener"
			: "attachEvent";
	var eventer = window[eventMethod];
	var messageEvent = eventMethod === "attachEvent"
		? "onmessage"
		: "message";
	eventer(messageEvent, function (e) {		
        if (e.data === "kp-indietro"){
            nascondiIframeGenerazioneOdfDaOrdine();
        }
        if(e.data.includes("kp-generato-")){
            var id_ordine = e.data.substring(12);
            if(jQuery.inArray(id_ordine, ordini_generati) == -1){
                ordini_generati.push(id_ordine);
            }
            nascondiIframeGenerazioneOdfDaOrdine();
        }
	});

}

function inizializzaStep() {

    getStep1();

}

function inizializzaExtra() {


}

function chiudiPopUp() {
    //closePopup();
    parent.location.reload();

}

function PopolaPicklistAnni(){

    var option_anni = "";

    for(var i = anno + 1; i >= anno - 4; i--){

        if(i == anno){
            option_anni += "<option value='" + i + "' selected>" + i + "</option>";
        }
        else{
            option_anni += "<option value='" + i + "'>" + i + "</option>";
        }

    }

    jform_anno_fatturazione.empty();
    jform_anno_fatturazione.append(option_anni);
}
/* kpro@bid040920181110 */
function ControlloSelezionati(classe_elementi){
    var selezionati = getSelezionati(classe_elementi, 'check');
    if(selezionati.length > 0){
        var res_confirm = confirm("ATTENZIONE! Sono stati selezionati alcuni elementi, proseguire senza generare?");
        if(res_confirm === true){
            return true;
        }
        else{
            return false;
        }
    }
    else{
        return true;
    }
}
/* kpro@bid040920181110 end */
function getStep1() {

    jQuery.get("modules/SproCore/KpWizard/KpGeneraOdF/templates/step_1.html", function(data) {

        jQuery("#step-1").empty();
        jQuery("#step-1").append(data);

        jform_mese_fatturazione = jQuery("#form_mese_fatturazione");
        jform_anno_fatturazione = jQuery("#form_anno_fatturazione");
        
        PopolaPicklistAnni();

        jform_mese_fatturazione.val(mese);
        jform_anno_fatturazione.val(anno);
    });

}

function getStep2() {

    jQuery.get("modules/SproCore/KpWizard/KpGeneraOdF/templates/step_2.html", function(data) {

        jQuery("#step-2").empty();
        jQuery("#step-2").append(data);

        jtable_canoni = jQuery("#table_canoni");
        jsearch_table_canoni_nome_canone = jQuery("#search_table_canoni_nome_canone");
        jsearch_table_canoni_azienda = jQuery("#search_table_canoni_azienda");
        jsearch_table_canoni_servizio = jQuery("#search_table_canoni_servizio");
        jcheck_canoni = jQuery("#check_canoni");

        filtro_canoni = {
            mese: mese,
            anno: anno,
            nome: '',
            azienda: '',
            servizio: ''
        };

        getCanoni(filtro_canoni);

        jcheck_canoni.click(function(){
            if(jcheck_canoni.prop("checked") == true || jcheck_canoni.prop("checked") == 'true'){
                jQuery("input[id^=check_canone_]").prop("checked",true);
            }
            else{
                jQuery("input[id^=check_canone_]").prop("checked",false);
            }
        });
        
        jsearch_table_canoni_nome_canone.keyup(function(e){
            if(e.which == 13 || jsearch_table_canoni_nome_canone.val() == ''){
                filtro_canoni.nome = jsearch_table_canoni_nome_canone.val();
                getCanoni(filtro_canoni);
            }
        });

        jsearch_table_canoni_azienda.keyup(function(e){
            if(e.which == 13 || jsearch_table_canoni_azienda.val() == ''){
                filtro_canoni.azienda = jsearch_table_canoni_azienda.val();
                getCanoni(filtro_canoni);
            }
        });

        jsearch_table_canoni_servizio.keyup(function(e){
            if(e.which == 13 || jsearch_table_canoni_servizio.val() == ''){
                filtro_canoni.servizio = jsearch_table_canoni_servizio.val();
                getCanoni(filtro_canoni);
            }
        });
    });

}

function getStep3() {

    jQuery.get("modules/SproCore/KpWizard/KpGeneraOdF/templates/step_3.html", function(data) {

        jQuery("#step-3").empty();
        jQuery("#step-3").append(data);

        jtable_tickets = jQuery("#table_tickets");
        jsearch_table_tickets_nome_ticket = jQuery("#search_table_tickets_nome_ticket");
        jsearch_table_tickets_azienda = jQuery("#search_table_tickets_azienda");
        jsearch_table_tickets_servizio = jQuery("#search_table_tickets_servizio");
        jcheck_tickets = jQuery("#check_tickets");

        filtro_tickets = {
            mese: mese,
            anno: anno,
            nome: '',
            azienda: '',
            servizio: ''
        };

        getTickets(filtro_tickets);

        jcheck_tickets.click(function(){
            if(jcheck_tickets.prop("checked") == true || jcheck_tickets.prop("checked") == 'true'){
                jQuery("input[id^=check_ticket_]").prop("checked",true);
            }
            else{
                jQuery("input[id^=check_ticket_]").prop("checked",false);
            }
        });
        
        jsearch_table_tickets_nome_ticket.keyup(function(e){
            if(e.which == 13 || jsearch_table_tickets_nome_ticket.val() == ''){
                filtro_tickets.nome = jsearch_table_tickets_nome_ticket.val();
                getTickets(filtro_tickets);
            }
        });

        jsearch_table_tickets_azienda.keyup(function(e){
            if(e.which == 13 || jsearch_table_tickets_azienda.val() == ''){
                filtro_tickets.azienda = jsearch_table_tickets_azienda.val();
                getTickets(filtro_tickets);
            }
        });

        jsearch_table_tickets_servizio.keyup(function(e){
            if(e.which == 13 || jsearch_table_tickets_servizio.val() == ''){
                filtro_tickets.servizio = jsearch_table_tickets_servizio.val();
                getTickets(filtro_tickets);
            }
        });
    });

}

function getStep4() {

    jQuery.get("modules/SproCore/KpWizard/KpGeneraOdF/templates/step_4.html", function(data) {

        jQuery("#step-4").empty();
        jQuery("#step-4").append(data);

        jtable_report_attivita = jQuery("#table_report_attivita");
        jsearch_table_ra_nome_attivita = jQuery("#search_table_ra_nome_attivita");
        jsearch_table_ra_azienda = jQuery("#search_table_ra_azienda");
        jsearch_table_ra_servizio = jQuery("#search_table_ra_servizio");
        jsearch_table_ra_tipo_rimborso = jQuery("#search_table_ra_tipo_rimborso");
        jcheck_report_attivita = jQuery("#check_report_attivita");

        filtro_report_attivita = {
            mese: mese,
            anno: anno,
            nome: '',
            azienda: '',
            servizio: '',
            tipo_rimborso: ''
        };

        getReportAttivita(filtro_report_attivita);

        jcheck_report_attivita.click(function(){
            if(jcheck_report_attivita.prop("checked") == true || jcheck_report_attivita.prop("checked") == 'true'){
                jQuery("input[id^=check_report_attivita_]").prop("checked",true);
            }
            else{
                jQuery("input[id^=check_report_attivita_]").prop("checked",false);
            }
        });
        
        jsearch_table_ra_nome_attivita.keyup(function(e){
            if(e.which == 13 || jsearch_table_ra_nome_attivita.val() == ''){
                filtro_report_attivita.nome = jsearch_table_ra_nome_attivita.val();
                getReportAttivita(filtro_report_attivita);
            }
        });

        jsearch_table_ra_azienda.keyup(function(e){
            if(e.which == 13 || jsearch_table_ra_azienda.val() == ''){
                filtro_report_attivita.azienda = jsearch_table_ra_azienda.val();
                getReportAttivita(filtro_report_attivita);
            }
        });

        jsearch_table_ra_servizio.keyup(function(e){
            if(e.which == 13 || jsearch_table_ra_servizio.val() == ''){
                filtro_report_attivita.servizio = jsearch_table_ra_servizio.val();
                getReportAttivita(filtro_report_attivita);
            }
        });

        jsearch_table_ra_tipo_rimborso.keyup(function(e){
            if(e.which == 13 || jsearch_table_ra_tipo_rimborso.val() == ''){
                filtro_report_attivita.tipo_rimborso = jsearch_table_ra_tipo_rimborso.val();
                getReportAttivita(filtro_report_attivita);
            }
        });
    });

}

function getStep5() {

    jQuery.get("modules/SproCore/KpWizard/KpGeneraOdF/templates/step_5.html", function(data) {

        jQuery("#step-5").empty();
        jQuery("#step-5").append(data);

        jtable_ordini = jQuery("#table_ordini");
        jsearch_table_ordini_soggetto = jQuery("#search_table_ordini_soggetto");
        jsearch_table_ordini_azienda = jQuery("#search_table_ordini_azienda");
        jsearch_table_ordini_data = jQuery("#search_table_ordini_data");
        jsearch_table_ordini_stato_ticket = jQuery("#search_table_ordini_stato_ticket");
        jsearch_table_ordini_stato = jQuery("#search_table_ordini_stato");

        filtro_ordini = {
            mese: mese,
            anno: anno,
            nome: '',
            azienda: '',
            data: '',
            stato_ticket: '',
            stato: ''
        };

        getOrdiniDiVendita(filtro_ordini);
        
        jsearch_table_ordini_soggetto.keyup(function(e){
            if(e.which == 13 || jsearch_table_ordini_soggetto.val() == ''){
                filtro_ordini.nome = jsearch_table_ordini_soggetto.val();
                getOrdiniDiVendita(filtro_ordini);
            }
        });

        jsearch_table_ordini_azienda.keyup(function(e){
            if(e.which == 13 || jsearch_table_ordini_azienda.val() == ''){
                filtro_ordini.azienda = jsearch_table_ordini_azienda.val();
                getOrdiniDiVendita(filtro_ordini);
            }
        });

        jsearch_table_ordini_data.keyup(function(e){
            if(e.which == 13 || jsearch_table_ordini_data.val() == ''){
                filtro_ordini.data = jsearch_table_ordini_data.val();
                getOrdiniDiVendita(filtro_ordini);
            }
        });

        jsearch_table_ordini_stato_ticket.keyup(function(e){
            if(e.which == 13 || jsearch_table_ordini_stato_ticket.val() == ''){
                filtro_ordini.stato_ticket = jsearch_table_ordini_stato_ticket.val();
                getOrdiniDiVendita(filtro_ordini);
            }
        });

        jsearch_table_ordini_stato.keyup(function(e){
            if(e.which == 13 || jsearch_table_ordini_stato.val() == ''){
                filtro_ordini.stato = jsearch_table_ordini_stato.val();
                getOrdiniDiVendita(filtro_ordini);
            }
        });
    });

}

function getCanoni(filtro) {

    jQuery.ajax({
        url: 'modules/SproCore/KpWizard/KpGeneraOdF/GetCanoni.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {


        },
        success: function(data) {
            //console.log(data);

            var lista = "";

            if(data.length > 0){
                for(var i = 0; i < data.length; i++){

                    if(data[i].errore == ''){
                        lista += "<tr><td><input type='checkbox' id='check_canone_"+data[i].id+"'></td>";
                    }
                    else{
                        lista += "<tr><td></td>";
                    }
                    lista += "<td><a href='index.php?module=Canoni&action=DetailView&record="+data[i].id+"' target='_blank'>"+data[i].nome+"</a></td>";
                    lista += "<td>"+data[i].azienda+"</td>";
                    lista += "<td>"+data[i].servizio+"</td>";
                    lista += "<td style='text-align:right;'>"+data[i].prezzo+" €</td>";
                    lista += "<td style='color:red;'>"+data[i].errore+"</td></tr>";

                }
            }
            else{
                lista += "<tr><td colspan='6' style='text-align:center;'>Nessun canone da fatturare.</td></tr>";
            }

            jtable_canoni.empty();
            jtable_canoni.append(lista);

        },
        fail: function() {

            console.error("Errore template!");

            //location.reload();

        }
    });

}

function getTickets(filtro) {

    jQuery.ajax({
        url: 'modules/SproCore/KpWizard/KpGeneraOdF/GetTickets.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {


        },
        success: function(data) {
            //console.log(data);

            var lista = "";

            if(data.length > 0){
                for(var i = 0; i < data.length; i++){

                    if(data[i].errore == ''){
                        lista += "<tr><td><input type='checkbox' id='check_ticket_"+data[i].id+"'></td>";
                    }
                    else{
                        lista += "<tr><td></td>";
                    }
                    lista += "<td><a href='index.php?module=HelpDesk&action=DetailView&record="+data[i].id+"' target='_blank'>"+data[i].nome+"</a></td>";
                    lista += "<td>"+data[i].azienda+"</td>";
                    lista += "<td>"+data[i].servizio+"</td>";
                    lista += "<td style='text-align:right;'>"+data[i].ore+"</td>"; /* kpro@bid040920181110 */
                    lista += "<td style='text-align:right;'>"+data[i].prezzo+" €</td>";
                    lista += "<td style='color:red;'>"+data[i].errore+"</td></tr>";

                }
            }
            else{
                lista += "<tr><td colspan='7' style='text-align:center;'>Nessun ticket da fatturare.</td></tr>"; /* kpro@bid040920181110 */
            }

            jtable_tickets.empty();
            jtable_tickets.append(lista);

        },
        fail: function() {

            console.error("Errore template!");

            //location.reload();

        }
    });

}

function getReportAttivita(filtro) {

    jQuery.ajax({
        url: 'modules/SproCore/KpWizard/KpGeneraOdF/GetReportAttivita.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {


        },
        success: function(data) {
            //console.log(data);

            var lista = "";

            if(data.length > 0){
                for(var i = 0; i < data.length; i++){

                    if(data[i].errore == ''){
                        lista += "<tr><td><input type='checkbox' id='check_report_attivita_"+data[i].id+"'></td>";
                    }
                    else{
                        lista += "<tr><td></td>";
                    }
                    lista += "<td><a href='index.php?module=Visitreport&action=DetailView&record="+data[i].id+"' target='_blank'>"+data[i].nome+"</a></td>";
                    lista += "<td>"+data[i].azienda+"</td>";
                    lista += "<td>"+data[i].servizio+"</td>";
                    lista += "<td>"+data[i].tipo_rimborso+"</td>";
                    lista += "<td style='text-align:right;'>"+data[i].ore_fatturate+"</td>";
                    lista += "<td style='color:red;'>"+data[i].errore+"</td></tr>";

                }
            }
            else{
                lista += "<tr><td colspan='7' style='text-align:center;'>Nessun report attività da fatturare.</td></tr>";
            }

            jtable_report_attivita.empty();
            jtable_report_attivita.append(lista);

        },
        fail: function() {

            console.error("Errore template!");

            //location.reload();

        }
    });

}

function getOrdiniDiVendita(filtro) {
    
    jQuery.ajax({
        url: 'modules/SproCore/KpWizard/KpGeneraOdF/GetOrdiniDiVendita.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {


        },
        success: function(data) {
            //console.log(data);

            var lista = "";

            if(data.length > 0){
                for(var i = 0; i < data.length; i++){

                    if(jQuery.inArray(data[i].id, ordini_generati) === -1){
                        if(data[i].errore == ''){
                            lista += "<tr><td>";
                            lista += "<button id='genera_ordine_"+data[i].id+"' type='button' class='btn btn-default btn-genera-ordine'>Genera</button>";
                            lista += "</td>";
                        }
                        else{
                            lista += "<tr><td></td>";
                        }
                        lista += "<td><a href='index.php?module=SalesOrder&action=DetailView&record="+data[i].id+"' target='_blank'>"+data[i].nome+"</a></td>";
                        lista += "<td>"+data[i].azienda+"</td>";
                        lista += "<td>"+data[i].data+"</td>";
                        lista += "<td>"+data[i].stato_ticket+"</td>";
                        lista += "<td>"+data[i].stato+"</td>";
                        lista += "<td style='text-align:right;'>"+data[i].totale_da_fatturare+"</td>";
                        lista += "<td style='text-align:right;'>"+data[i].totale+"</td>";
                        lista += "<td style='color:red;'>"+data[i].errore+"</td></tr>";
                    }
                }
            }
            
            if(lista == ""){
                lista += "<tr><td colspan='9' style='text-align:center;'>Nessun ordine di vendita da fatturare.</td></tr>";
            }

            jtable_ordini.empty();
            jtable_ordini.append(lista);

            jQuery('.btn-genera-ordine').click(function(){
                var id_bottone = jQuery(this).prop("id");
                var id_ordine = id_bottone.substring(14);
                
                caricaIframeGenerazioneOdfDaOrdine(id_ordine);
                
            });
            
        },
        fail: function() {

            console.error("Errore template!");

            //location.reload();

        }
    });

}

function caricaIframeGenerazioneOdfDaOrdine(id_ordine){

    jQuery('#tabella_lista_ordini').hide();
    jQuery('#titolo_lista_ordini').hide();
    jQuery('#iframe_generazione_odf_da_ordine').show();

    jQuery('#iframe_generazione_odf_da_ordine').prop("src","");
    var link_iframe = 'index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/PopupGenerazioneOdfDaOrdine/index&mode=iframe&record=' + id_ordine;
    jQuery('#iframe_generazione_odf_da_ordine').prop("src",link_iframe);

}

function nascondiIframeGenerazioneOdfDaOrdine(){

    jQuery('#tabella_lista_ordini').show();
    jQuery('#titolo_lista_ordini').show();
    jQuery('#iframe_generazione_odf_da_ordine').hide();

    jQuery('#iframe_generazione_odf_da_ordine').prop("src","");

    getOrdiniDiVendita(filtro_ordini);

}
/* kpro@bid040920181110 */
function getSelezionati(prefix, mode='genera'){

    var selezionati = [];
    var controllo_selezionati = false;

    if(prefix == 'check_canone_' && jQuery('#tabella_lista_canoni').is(":visible")){
        controllo_selezionati = true;
    }
    else if(prefix == 'check_ticket_' && jQuery('#tabella_lista_tickets').is(":visible")){
        controllo_selezionati = true;
    }
    else if(prefix == 'check_report_attivita_' && jQuery('#tabella_lista_ra').is(":visible")){
        controllo_selezionati = true;
    }
    
    if(controllo_selezionati){
        jQuery("input[id^="+prefix+"]").each(function(){

            if(jQuery(this).prop("checked")){
                var id_elemento = jQuery(this).prop("id");
                var array_id_elemento = id_elemento.split("_");
                var record = array_id_elemento[array_id_elemento.length - 1];
                selezionati.push(record);
            }

        });
    }

    if(mode == 'genera'){
        if(selezionati.length <= 0){
            alert("Nessun record selezionato!");
        }
        else{
            var res_confirm = confirm("Generare gli OdF per i "+selezionati.length+" record selezionati?");
            if(!res_confirm){
                selezionati = [];
            }
        }
    }

    return selezionati;

}
/* kpro@bid040920181110 end */

function generaOdfCanoni(){

    var selezionati = getSelezionati("check_canone_");
    if(selezionati.length > 0){

        /* kpro@bid040920181110 */
        var dati = {
            selezionati: selezionati,
            anno: anno,
            mese: mese
        };
        /* kpro@bid040920181110 end */

        jQuery.ajax({
            url: 'modules/SproCore/KpWizard/KpGeneraOdF/SetOdfCanoni.php',
            type: 'POST',
            dataType: "text",
            data: dati,
            beforeSend: function() {
    
    
            },
            success: function(data) {

                data = JSON.parse(data); /* kpro@bid040920181110 */
                
                //console.log(data);
    
                if(data.length > 0){
                    jQuery('#canoni_totale_odf_creati').text(data[0].totale_odf_creati);
                    jQuery('#canoni_totale_odf_con_prezzo').text(data[0].odf_prezzo);
                    jQuery('#canoni_totale_odf_senza_prezzo').text(data[0].odf_senza_prezzo);

                    jQuery('#tabella_lista_canoni').hide();
                    jQuery('#tabella_risultati_canoni').show();
                }
    
            },
            fail: function() {
    
                console.error("Errore template!");
    
                //location.reload();
    
            }
        });
    }

}

function generaOdfTickets(){
    
    var selezionati = getSelezionati("check_ticket_");
    if(selezionati.length > 0){
        
        var dati = {
            selezionati: selezionati
        };

        jQuery.ajax({
            url: 'modules/SproCore/KpWizard/KpGeneraOdF/SetOdfTickets.php',
            type: 'POST',
            dataType: "text",
            data: dati,
            beforeSend: function() {
    
    
            },
            success: function(data) {

                data = JSON.parse(data); /* kpro@bid040920181110 */

                //console.log(data);
    
                if(data.length > 0){
                    jQuery('#totale_ticket_da_fatturare').text(data[0].totale_ticket_da_fatturare);
                    jQuery('#ticket_totale_odf_creati').text(data[0].totale_odf_creati);
                    jQuery('#totale_ticket_senza_servizio').text(data[0].totale_ticket_senza_servizio);
                    jQuery('#totale_ticket_senza_prezzo').text(data[0].totale_ticket_senza_prezzo);
                    jQuery('#totale_ticket_senza_tempo').text(data[0].totale_ticket_senza_tempo);
                    jQuery('#totale_ticket_senza_cliente').text(data[0].totale_ticket_senza_cliente);

                    jQuery('#tabella_lista_tickets').hide();
                    jQuery('#tabella_risultati_tickets').show();
                }
    
            },
            fail: function() {
    
                console.error("Errore template!");
    
                //location.reload();
    
            }
        });
    }

}

function generaOdfReportAttivita(){

    var selezionati = getSelezionati("check_report_attivita_");
    if(selezionati.length > 0){
        
        var dati = {
            selezionati: selezionati
        };

        jQuery.ajax({
            url: 'modules/SproCore/KpWizard/KpGeneraOdF/SetOdfReportAttivita.php',
            type: 'POST',
            dataType: "text",
            data: dati,
            beforeSend: function() {
    
    
            },
            success: function(data) {

                data = JSON.parse(data); /* kpro@bid040920181110 */

                //console.log(data);
                
                if(data.length > 0){
                    jQuery('#ra_totale_odf_creati').text(data[0].totale_odf_creati);
                    jQuery('#ra_totale_odf_con_prezzo').text(data[0].totale_odf_con_prezzo);
                    jQuery('#ra_totale_odf_senza_prezzo').text(data[0].totale_odf_senza_prezzo);

                    jQuery('#tabella_lista_ra').hide();
                    jQuery('#tabella_risultati_ra').show();
                }

            },
            fail: function() {
    
                console.error("Errore template!");
    
                //location.reload();
    
            }
        });
    }

}