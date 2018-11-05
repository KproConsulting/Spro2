/* kpro@tom29062017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

var jpanel;
var jtitolo_pagina;

var altezza_schermo = '';
var larghezza_schermo = '';

var timer_check_offline;
var readonly = false;
var myclock = "";
var isOnline = true;
var salvataggioInCorso = false;

var mProLayout;
var bpmnViewer;
var canvas;
var overlays;
var elementRegistry;
var elemento_by_id = [];
var array_bpmn_id_elementi = [];
var revisione = false;

var elemento_selezionato;
var elemento_selezionato_crmid;
var elemento_selezionato_nome;
var documento;
var nome_processo_principale;
var descrizione_processo_principale;
var richiedi_approvazione = '0';
var descrizione_revisione = "";
var disegnato_da;
var approvato_da;

var jlayoutObj;
var jgraphContainer;

//Elementi personalizzati

var jdettagliContainer;
var jreadonly_nome_attivita;
var jreadonly_nome_processo;
var jnome_documento;
var jform_descrizione;
var jbottone_aggiungi_documento;
var jbottone_aggiungi_ruolo;
var jbottone_rel_seleziona_processo_relazionato;
var jbottone_rel_pulisci_processo_relazionato;
var jpopup_selezione_documenti;
var jpopup_selezione_ruoli;
var jtabs_selezione_documenti;
var jpopup_selezione_processo;
var jbody_tabella_documenti;
var jbottone_download_documento;
var jbody_tabella_associa_documenti;
var jbottone_link_documento;
var jbottone_disassocia_documento
var jbody_tabella_relaziona_processo;
var jreadonly_processo_relazionato;
var jbody_tabella_ruoli;
var jbottone_link_ruolo
var jbody_tabella_relaziona_ruolo;
var jbottone_salva_chiudi;
var jbottone_aggiungi_documento;
var jbody_tabella_workflow;
var jbottone_aggiungi_workflow;
var jbottone_visualizza_workflow;
var jbottone_disassocia_workflow;
var jbody_tabella_relaziona_workflow;
var jbottone_link_workflow;
var jpopup_selezione_workflow;
var jform_valore_aggiunto;
var jpopup_selezione_rischi;
var jsearch_nuovo_nome_rischio_qualita;
var jsearch_nuovo_nome_rischio_privacy;
var jsearch_nuovo_nome_rischio_sicurezza;
var jtabs_selezione_rischi;
var jdiv_popup;
var jbody_tabella_associa_rischi_qualita;
var jbody_tabella_associa_rischi_privacy;
var jbody_tabella_associa_rischi_sicurezza;
var jbottone_rischi_qualita_elemento;
var jbottone_rischi_privacy_elemento;
var jbottone_rischi_sicurezza_elemento;
var jbody_tabella_rischi;
var jbottone_disassocia_rischio_qualita;
var jbottone_disassocia_rischio_privacy;
var jbottone_disassocia_rischio_sicurezza;
var jbottone_crea_nuovo_processo;
var jbottone_conferma_revisione;
var jpopup_revisione;
var jform_descrizione_revisione;
var jbottone_salva_revisione;
var jbottone_annulla_revisione;
var jsearch_nuovo_nome_processo;
var jsearch_nuovo_nome_ruolo;
var jsearch_nuovo_nome_documento;
var jbottone_rel_seleziona_tipo_attivita;
var jbottone_rel_pulisci_tipo_attivita;
var jreadonly_tipo_attivita;
var jbody_tabella_relaziona_tipo_attivita;
var jpopup_selezione_tipo_attivita;
var jsearch_nuovo_nome_tipo_attivita;
var jbottone_link_tipo_attivita;
var jbody_tabella_aree;
var jbottone_aggiungi_area;
var jbottone_disassocia_area;
var jbody_tabella_relaziona_area;
var jpopup_selezione_area;
var jsearch_nuovo_nome_azienda;
var jsearch_nuovo_nome_area;
var jsearch_nuovo_nome_stabilimento;
var jbottone_link_area;
var jbottone_approva_processo;
var jpopup_firma_esecutore;
var jarea_firma_esecutore;
var jbottone_pulisci_firma_esecutore;
var jbottone_salva_firma_esecutore;
var jarea_firma_esecutore_div;
var jcaricamento;
var jbottone_keyboard_esecutore;
var jform_firmatario_esecutore;
var jpopup_firma_approvatore;
var jarea_firma_approvatore;
var jbottone_pulisci_firma_approvatore;
var jbottone_salva_firma_approvatore;
var jarea_firma_approvatore_div;
var jcaricamento;
var jbottone_keyboard_approvatore;
var jform_firmatario_approvatore;
var jbottone_centra_disegno;

var uploaderForm;
var uploaderFormData;

var documento = [];
var nome_documento = "";

var filtro_documenti = {};
var filtro_documenti_relazionabili = {};
var filtro_processi = {};
var filtro_ruoli = {};
var filtro_ruoli_relazionabili = {};
var filtro_workflow = {};
var filtro_workflow_relazionabili = {};
var filtro_rischi = {};
var filtro_rischi_relazionabili = {};
var filtro_tipi_attivita_relazionabili = {};
var filtro_aree = {};
var filtro_aree_relazionabili = {};

//Elementi personalizzati end

jQuery(document).ready(function() {

    inizializzazione();

    inizializzazioneMaterialize();

    inizializzazioneLayout();

    inizializzazioneBPMN();

    inizializzaUploader();

});

function inizializzazione() {

    jtitolo_pagina = $("#titolo_pagina");
    jlayoutObj = jQuery("#layoutObj");
    jgraphContainer = jQuery("#graphContainer");
    jdettagliContainer = jQuery("#dettagliContainer");
    jpopup_selezione_documenti = jQuery("#popup_selezione_documenti");
    jtabs_selezione_documenti = jQuery("#tabs_selezione_documenti");
    jbody_tabella_associa_documenti = jQuery("#body_tabella_associa_documenti");
    jbottone_salva_chiudi = jQuery("#bottone_salva_chiudi");
    jdiv_popup = jQuery("#div_popup");
    jbottone_conferma_revisione = jQuery("#bottone_conferma_revisione");
    jpopup_revisione = jQuery("#popup_revisione");
    jform_descrizione_revisione = jQuery("#form_descrizione_revisione");
    jbottone_salva_revisione = jQuery("#bottone_salva_revisione");
    jbottone_annulla_revisione = jQuery("#bottone_annulla_revisione");
    jsearch_nuovo_nome_documento = jQuery("#search_nuovo_nome_documento");
    jbottone_approva_processo = jQuery("#bottone_approva_processo");
    jbottone_centra_disegno = jQuery("#bottone_centra_disegno");

    window.addEventListener('resize', function() {
        reSize();
    }, false);

    reSize();

    setTitoloPagina();

    getDatiUtente();

    jbottone_salva_chiudi.click(function() {

        salvaChiudiScheda();

    });

    jbottone_conferma_revisione.click(function() {

        gestioneRevisioni();

    });
    
    jbottone_salva_revisione.click(function() {

        if( !salvataggioInCorso ){

            if( checkCampiObbligatoriRevisione() ){

                if( richiedi_approvazione == "1" ){

                    descrizione_revisione = jform_descrizione_revisione.val();
                    descrizione_revisione = HtmlEntities.kpencode(descrizione_revisione);

                    gestioneApprovazioneProcesso();

                }
                else{

                    salvataggioInCorso = true;
                    getBPMNioSVG();
                    confermaRevisione();

                }

            }

        }

    });

    jbottone_approva_processo.click(function() {

        if( richiedi_approvazione == "1" ){

            gestioneApprovazioneProcesso();

        }
        else{

            confermaProcesso();

        }

    });

    jsearch_nuovo_nome_documento.keyup(function(ev) {

        var nome_documento_temp = jsearch_nuovo_nome_documento.val();

        var code = ev.which;
        if (code == 13 || nome_documento_temp == "") {

            filtro_documenti_relazionabili.record = elemento_selezionato_crmid;
            filtro_documenti_relazionabili.nome_documento = nome_documento_temp;
            getDocumentiRelazionabiliAElemento(filtro_documenti_relazionabili);

        }

    });

    jbottone_centra_disegno.click(function(){

        canvas.zoom('fit-viewport', {});
        canvas.zoom('fit-viewport', {});

    });

}

function reSize() {

    jpanel = jQuery(".panel");

    larghezza_schermo = window.innerWidth;
    altezza_schermo = window.innerHeight;

    jpanel.css("height", innerHeight - 110);

    jlayoutObj.css("height", innerHeight - 110);
    jgraphContainer.css("height", innerHeight - 160);

}

function inizializzazioneLayout() {

    window.dhx4.skin = 'material';

    mProLayout = new dhtmlXLayoutObject({
        parent: "layoutObj",
        pattern: "2U",
        skin: "material"
    });

    mProLayout.cells("a").setText("Flow Chart");
    mProLayout.cells("a").setCollapsedText("Flow Chart");
    mProLayout.cells("a").setWidth(300);

    mProLayout.cells("b").setText("Dettagli");
    mProLayout.cells("b").setCollapsedText("Dettagli");

    var larghezza_dettagli = larghezza_schermo / 3;
    mProLayout.cells("b").setWidth(larghezza_dettagli);

    mProLayout.cells("a").attachObject("graphContainer");

    mProLayout.cells("b").attachObject("dettagliContainer");

}

function inizializzazioneMaterialize() {

    Materialize.updateTextFields();

    $('select').material_select();

    $('.collapsible').collapsible({
        accordion: false // A setting that changes the collapsible behavior to expandable instead of the default accordion style
    });

    $('ul.tabs').tabs();

    $('.tooltipped').tooltip({ delay: 50 });

}

function popolaPicking(campo, nome_campo_crm, valore_default) {

    var filtro = { nome_campo: nome_campo_crm };

    $.ajax({
        url: 'PickingList.php',
        dataType: 'json',
        async: true,
        data: filtro,
        success: function(data) {

            //console.table(data);

            var lista = "";
            for (var i = 0; i < data.length; i++) {
                if (data[i].valore == valore_default) {
                    lista += "<option value='" + data[i].valore + "' selected='selected'>" + data[i].valore + "</option>";
                } else {
                    lista += "<option value='" + data[i].valore + "'>" + data[i].valore + "</option>";
                }
            }
            campo.empty();
            campo.append(lista);

            Materialize.updateTextFields();

            $('select').material_select();

        },
        fail: function() {
            console.error("Errore nel caricamento della picking list: " + nome_campo_crm);
        }
    });

}

function setTitoloPagina() {

    var dati = {
        record: record
    };

    jQuery.ajax({
        url: 'DatiProcedura.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {

        },
        success: function(data) {

            if (data.length > 0) {

                nome_processo_principale = HtmlEntities.decode(data[0].nome);
                descrizione_processo_principale = HtmlEntities.decode(data[0].descrizione);

                richiedi_approvazione = data[0].richiedi_approvazione;

                var titolo_pagina = data[0].nome;

                if(data[0].revisione_di != 0 && data[0].revisione_di != ""){
                    titolo_pagina = titolo_pagina + " (Rev. " + data[0].numero_revisione + ")";
                    revisione = true;
                }
                else{
                    revisione = false;
                }

                if( (data[0].stato_procedura == "In sviluppo" || data[0].stato_procedura == "Da approvare" ) && revisione && data[0].revisione_di != 0 && data[0].revisione_di != ""){
                    jbottone_conferma_revisione.show(); 
                }
                else{
                    jbottone_conferma_revisione.hide();

                    if( richiedi_approvazione == "1" && (data[0].stato_procedura == "In sviluppo" || data[0].stato_procedura == "Da approvare" ) ){
                        jbottone_approva_processo.show();
                    }
                    else{
                        jbottone_approva_processo.hide();
                    }

                }

                var titolo_pagina_temp = "";
                titolo_pagina_temp += "<a href='#' onclick='chiudiScheda()' class='breadcrumb' style='font-weight: bold; font-size: 24px !important; color: #2b577c;'>" + titolo_pagina + "</a>";
                titolo_pagina_temp += "<a href='#!' class='breadcrumb' style='color: #2b577c;'>BPMN Editing</a>";
                jtitolo_pagina.html(titolo_pagina_temp);

            }
        },
        fail: function() {

        }
    });

}

function getDatiUtente() {

    jQuery.ajax({
        url: 'GetDatiUtente.php',
        dataType: 'json',
        async: true,
        beforeSend: function() {

        },
        success: function(data) {

            disegnato_da = data.last_name +  " " + data.first_name;

        },
        fail: function() {

        }
    });


}

function chiudiScheda() {

    dhtmlx.confirm({
        type: "confirm",
        text: "Eseguendo questa operazione la schermata corrente verrà chiusa e le modifiche non salvate verranno perse; continuare?",
        callback: function(result) {

            if (result) {
                if (return_module == "drawer") {
                    window.open("index.php?record=" + record, "_self");
                } else {
                    self.close();
                }
            }

        }
    });

}

function salvaChiudiScheda() {

    dhtmlx.confirm({
        type: "confirm",
        text: "Eseguendo questa operazione la schermata corrente verrà chiusa; continuare?",
        callback: function(result) {

            if (result) {
                self.close();
            }

        }
    });

}

function inizializzazioneBPMN() {

    (function(BpmnJS) {

        //create viewer
        bpmnViewer = new BpmnJS({
            container: '#graphContainer'
        });

        getBPMNxml();

    })(window.BpmnJS);

    bpmnViewer.on('element.click', function(event) {
        var element = event.element;
        //console.log("element.click");
        //console.log(element);

        switch (element.type) {
            case "bpmn:Task":
                setTaskSelezionata(element);
                break;
            case "bpmn:SendTask":
                setTaskSelezionata(element);
                break;
            case "bpmn:ReceiveTask":
                setTaskSelezionata(element);
                break;
            case "bpmn:UserTask":
                setTaskSelezionata(element);
                break;
            case "bpmn:ManualTask":
                setTaskSelezionata(element);
                break;
            case "bpmn:ServiceTask":
                setTaskSelezionata(element);
                break;
            case "bpmn:ScriptTask":
                setTaskSelezionata(element);
                break;
            case "bpmn:CallActivity":
                setTaskSelezionata(element);
                break;
            case "bpmn:Transaction":
                setTaskSelezionata(element);
                break;
            case "bpmn:SubProcess":
                setSottoProcessoSelezionato(element);
                break;
            case "bpmn:Process":
                setProcessoSelezionato();
                break;
        }

    });

}

function importBPMNxml(xml, lista_elementi) {

    bpmnViewer.importXML(xml, function(err) {

        if (err) {
            return console.error('could not import BPMN 2.0 diagram', err);
        }

        canvas = bpmnViewer.get('canvas');
        overlays = bpmnViewer.get('overlays');
        elementRegistry = bpmnViewer.get('elementRegistry');

        //zoom to fit full viewport
        canvas.zoom('fit-viewport');

        //console.log(elementRegistry); //Contiene tutti gli elementi presenti nel disegno

        var structure = { 'shapes': {}, 'connections': {}, 'tree': {} };

        jQuery.each(elementRegistry.getAll(), function(index, object) {
            if (object.constructor.name == 'Shape') {

                var id = object.id;
                var type = object.type;
                var dom_obj = jQuery('[data-element-id=' + id + ']');
                var subType = '';

                if (typeof(object.businessObject.cancelActivity) == 'boolean') {
                    var cancelActivity = object.businessObject.cancelActivity;
                }

                if (typeof(elementRegistry.get(id + '_label')) == 'object') {
                    var text = jQuery('[data-element-id=' + id + '_label]').find('text').text();
                } else {
                    var text = dom_obj.find('text').text();
                }

                var connections = { 'incoming': {}, 'outgoing': {}, 'attachers': new Array() };
                if (object.incoming != undefined) {
                    jQuery(object.incoming).each(function(index, connection) {
                        connections['incoming'][connection.id] = connection.source.id;
                    });
                }
                if (object.outgoing != undefined) {
                    jQuery(object.outgoing).each(function(index, connection) {
                        connections['outgoing'][connection.id] = connection.target.id;
                    });
                }
                if (object.attachers != undefined && jQuery(object.attachers).length > 0) {
                    jQuery(object.attachers).each(function(index, attacher) {
                        connections['attachers'].push(attacher.id);
                    });
                }

                structure['shapes'][id] = { 'type': type, 'text': text };

                if (typeof(cancelActivity) == 'boolean') structure['shapes'][id]['cancelActivity'] = cancelActivity;
                structure['tree'][id] = connections;

                //console.log(id, type, text);

                dom_obj.css('cursor', 'pointer');

                dom_obj.hover(function() {
                    canvas.toggleMarker(id, 'highlights-shape');
                }, function() {
                    canvas.toggleMarker(id, 'highlights-shape');
                });

                applicaStileShape(object);

                dom_obj.click(function() {

                });

            } else if (object.constructor.name == 'Connection') {
                var id = object.id;
                var type = object.type;
                if (typeof(elementRegistry.get(id + '_label')) == 'object') {
                    var text = jQuery('[data-element-id=' + id + '_label]').find('text').text();
                }
                structure['connections'][id] = { 'type': type, 'text': text };
            }

        });

        //console.log(structure);

        setProcessoSelezionato();

        setArrayListaElementiById(lista_elementi);

        applicaStileAggiuntivoShape(lista_elementi);

    });

}

function getBPMNxml() {

    var dati = {
        record: record
    };

    jQuery.ajax({
        url: 'GetBPMN.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {

        },
        success: function(data) {

            if (data.length > 0) {

                var diagramXML = data[0]["processo"].bpmn_xml;

                //console.log(diagramXML);
                //console.log(data[0]["elementi"]);

                if (diagramXML != "") {

                    importBPMNxml(diagramXML, data[0]["elementi"]);

                }

            }

        },
        fail: function() {

        }
    });

}

function setTaskSelezionata(elemento) {

    elemento_selezionato = elemento.id;

    elemento_selezionato_nome = HtmlEntities.decode(elemento.businessObject.name);

    setSelezioneShape(elemento_selezionato);

    mProLayout.cells("b").setText("Dettagli: " + elemento_selezionato_nome);
    mProLayout.cells("b").setCollapsedText("Dettagli: " + elemento_selezionato_nome);

    jQuery.get("templates/dettagli_task.html", function(data) {

        //console.log(data);

        jdettagliContainer.empty();
        jdettagliContainer.append(data);

        inizializzazioneMaterialize();

        jreadonly_nome_attivita = jQuery("#readonly_nome_attivita");
        jform_descrizione = jQuery("#form_descrizione");
        jbottone_aggiungi_documento = jQuery("#bottone_aggiungi_documento");
        jbottone_aggiungi_ruolo = jQuery("#bottone_aggiungi_ruolo");
        jbody_tabella_documenti = jQuery("#body_tabella_documenti");
        jbody_tabella_ruoli = jQuery("#body_tabella_ruoli");
        jform_valore_aggiunto = jQuery("#form_valore_aggiunto");
        jbody_tabella_rischi = jQuery("#body_tabella_rischi");
        jbottone_aggiungi_rischio = jQuery("#bottone_aggiungi_rischio");
        jbottone_rel_seleziona_tipo_attivita = jQuery("#bottone_rel_seleziona_tipo_attivita");
        jbottone_rel_pulisci_tipo_attivita = jQuery("#bottone_rel_pulisci_tipo_attivita");
        jreadonly_tipo_attivita = jQuery("#readonly_tipo_attivita");
        jbody_tabella_aree = jQuery("#body_tabella_aree");
        jbottone_aggiungi_area = jQuery("#bottone_aggiungi_area");

        jbottone_aggiungi_documento.click(function() {

            apriPopupSelezioneDocumenti();

        });

        jbottone_aggiungi_ruolo.click(function() {

            apriPopupSelezioneRuoli();

        });

        jbottone_aggiungi_rischio.click(function() {

            apriPopupSelezioneRischi();

        });

        jform_descrizione.change(function() {

            setDescrizioneElemento(elemento_selezionato_crmid, jform_descrizione.val());

        });

        jform_valore_aggiunto.change(function() {

            setValoreAggiuntoElemento(elemento_selezionato_crmid, jform_valore_aggiunto.val());

        });

        jbottone_rel_seleziona_tipo_attivita.click(function() {

            apriPopupSelezioneTipoAttivita();

        });

        jbottone_rel_pulisci_tipo_attivita.click(function() {

            disassociaTipoAttivitaTask(elemento_selezionato_crmid);

        });

        jbottone_aggiungi_area.click(function() {

            apriPopupSelezioneArea();

        });

        var dati = {
            processo: record,
            elemento_bpmn_id: elemento_selezionato
        };

        jQuery.ajax({
            url: 'GetElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

            },
            success: function(data) {

                if (data.length > 0) {

                    elemento_selezionato_crmid = data[0].id;

                    jreadonly_nome_attivita.val(elemento_selezionato_nome);
                    jreadonly_nome_attivita.trigger('autoresize');

                    jform_descrizione.val(HtmlEntities.decode(data[0].descrizione));
                    jform_descrizione.trigger('autoresize');

                    jreadonly_tipo_attivita.val(HtmlEntities.decode(data[0].nome_tipo_attivita));
                    jreadonly_tipo_attivita.trigger('autoresize');

                    popolaPicking(jform_valore_aggiunto, "kp_valore_aggiunto", data[0].valore_aggiunto);

                    filtro_documenti.record = elemento_selezionato_crmid;
                    filtro_documenti.nome_documento = "";

                    getDocumentiElemento(filtro_documenti);

                    filtro_ruoli.record = elemento_selezionato_crmid;
                    filtro_ruoli.nome_ruolo = "";

                    getRuoliElemento(filtro_ruoli);

                    filtro_rischi.record = elemento_selezionato_crmid;
                    filtro_rischi.nome_rischio = "";

                    getRischiElemento(filtro_rischi);

                    filtro_aree.record = elemento_selezionato_crmid;

                    getAreaElemento(filtro_aree);

                    inizializzazioneMaterialize();

                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

            },
            fail: function() {

                console.error("Errore");

                location.reload();

            }
        });

    });

}

function setSottoProcessoSelezionato(elemento) {

    elemento_selezionato = elemento.id;

    elemento_selezionato_nome = elemento.businessObject.name;

    setSelezioneShape(elemento_selezionato);

    mProLayout.cells("b").setText("Dettagli: " + elemento_selezionato_nome);
    mProLayout.cells("b").setCollapsedText("Dettagli: " + elemento_selezionato_nome);

    jQuery.get("templates/dettagli_sottoprocesso.html", function(data) {

        //console.log(data);

        jdettagliContainer.empty();
        jdettagliContainer.append(data);

        inizializzazioneMaterialize();

        jreadonly_nome_processo = jQuery("#readonly_nome_processo");
        jbottone_rel_seleziona_processo_relazionato = jQuery("#bottone_rel_seleziona_processo_relazionato");
        jbottone_rel_pulisci_processo_relazionato = jQuery("#bottone_rel_pulisci_processo_relazionato");
        jreadonly_processo_relazionato = jQuery("#readonly_processo_relazionato");

        jbottone_rel_seleziona_processo_relazionato.click(function() {

            apriPopupSelezioneProcesso();

        });

        jbottone_rel_pulisci_processo_relazionato.click(function() {

            disassociaProcessoTask(elemento_selezionato_crmid);

        });

        var dati = {
            processo: record,
            elemento_bpmn_id: elemento_selezionato
        };

        jQuery.ajax({
            url: 'GetElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

            },
            success: function(data) {

                if (data.length > 0) {

                    elemento_selezionato_crmid = data[0].id;

                    jreadonly_nome_processo.val(elemento_selezionato_nome);
                    jreadonly_nome_processo.trigger('autoresize');

                    jreadonly_processo_relazionato.val(data[0].relazionato_a_nome);
                    jreadonly_processo_relazionato.trigger('autoresize');

                    inizializzazioneMaterialize();

                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

            },
            fail: function() {

                console.error("Errore");

                location.reload();

            }
        });

    });

}

function setProcessoSelezionato() {

    elemento_selezionato = record;

    elemento_selezionato_crmid = record;

    elemento_selezionato_nome = nome_processo_principale;

    jQuery.each(elementRegistry.getAll(), function(index, object) {

        canvas.removeMarker(object.id, 'selected-shape');

    });

    mProLayout.cells("b").setText("Dettagli: " + elemento_selezionato_nome);
    mProLayout.cells("b").setCollapsedText("Dettagli: " + elemento_selezionato_nome);

    jQuery.get("templates/dettagli_processo.html", function(data) {

        //console.log(data);

        jdettagliContainer.empty();
        jdettagliContainer.append(data);

        inizializzazioneMaterialize();

        jreadonly_nome_processo = jQuery("#readonly_nome_processo");
        jform_descrizione = jQuery("#form_descrizione");
        jbody_tabella_documenti = jQuery("#body_tabella_documenti");
        jbottone_aggiungi_documento = jQuery("#bottone_aggiungi_documento");
        jbody_tabella_workflow = jQuery("#body_tabella_workflow");
        jbottone_aggiungi_workflow = jQuery("#bottone_aggiungi_workflow");
        jbody_tabella_aree = jQuery("#body_tabella_aree");
        jbottone_aggiungi_area = jQuery("#bottone_aggiungi_area");

        jreadonly_nome_processo.val(nome_processo_principale);
        jreadonly_nome_processo.trigger('autoresize');

        jform_descrizione.val(descrizione_processo_principale);
        jform_descrizione.trigger('autoresize');

        filtro_documenti.record = elemento_selezionato_crmid;
        filtro_documenti.nome_documento = "";

        getDocumentiElemento(filtro_documenti);

        filtro_workflow.record = elemento_selezionato_crmid;
        filtro_workflow.nome_workflow = "";

        getWorkflowElemento(filtro_workflow);

        filtro_aree.record = elemento_selezionato_crmid;

        getAreaElemento(filtro_aree);

        jbottone_aggiungi_documento.click(function() {

            apriPopupSelezioneDocumenti();

        });

        jbottone_aggiungi_workflow.click(function() {

            apriPopupSelezioneWorkflow();

        });

        jbottone_aggiungi_area.click(function() {

            apriPopupSelezioneArea();

        });

        inizializzazioneMaterialize();

    });

}

function setSelezioneShape(id) {

    jQuery.each(elementRegistry.getAll(), function(index, object) {

        canvas.removeMarker(object.id, 'selected-shape');

    });

    canvas.toggleMarker(id, 'selected-shape');

}

function inizializzaUploader() {

    jnome_documento = jQuery("#nome_documento");

    var tipo_file_concessi = ["pdf", "txt", "doc", "docx", "xls", "xlsx"];

    uploaderFormData = [{
        type: "fieldset",
        label: "Uploader",
        className: "uploaderForm",
        list: [{
            type: "upload",
            name: "myFiles",
            inputWidth: 400,
            inputHeight: "auto",
            url: "dhtmlxform_item_upload.php?crmid=" + elemento_selezionato_crmid + "&nome_documento=" + nome_documento,
            autoStart: true,
            autoRemove: true,
            titleScreen: true,
            titleText: "Trascina i file nel riquadro o clicca sull'immagine",
        }]
    }];

    uploaderForm = new dhtmlXForm("uploaderForm", uploaderFormData);
    //console.log(uploaderForm);

    uploaderForm.lock();

    uploaderForm.attachEvent("onBeforeFileAdd", function(realName, size) {
        //console.log("Prima di iniziare "+realName);

        var ext_filename = realName.split('.').pop();

        if (jQuery.inArray(ext_filename, tipo_file_concessi) == -1) {
            Materialize.toast("Il file deve essere in uno dei seguenti formati: pdf, txt, doc, docx, xls, xlsx", 4000);
            return false;
        } else {
            return true;
        }

    });

    uploaderForm.attachEvent("onFileAdd", function(realName) {
        //console.log("Prima di iniziare");

        nome_documento = jnome_documento.val();

        var myUploader = uploaderForm.getUploader("myFiles");
        myUploader.setURL("dhtmlxform_item_upload.php?crmid=" + elemento_selezionato_crmid + "&nome_documento=" + nome_documento);

    });

    uploaderForm.attachEvent("onUploadComplete", function(count) {
        //console.log("Finito");

        jnome_documento.val("");
        jnome_documento.trigger('autoresize');
        uploaderForm.clear();
        uploaderForm.lock();

        getDocumentiElemento(filtro_documenti);
        jpopup_selezione_documenti.closeModal();

    });

    jnome_documento.keyup(function(ev) {

        var nome_documento_temp = jnome_documento.val();

        if (nome_documento_temp == "") {
            uploaderForm.lock();
        } else {
            uploaderForm.unlock();
        }

    });

}

function apriPopupSelezioneDocumenti() {

    jnome_documento.val("");
    jnome_documento.trigger('autoresize');

    jsearch_nuovo_nome_documento.val("");
    jsearch_nuovo_nome_documento.trigger('autoresize');

    filtro_documenti_relazionabili.record = elemento_selezionato_crmid;
    filtro_documenti_relazionabili.nome_documento = "";
    getDocumentiRelazionabiliAElemento(filtro_documenti_relazionabili);

    Materialize.updateTextFields();

    $('select').material_select();

    jpopup_selezione_documenti.openModal();

    jtabs_selezione_documenti.tabs('select_tab', 'tab_nuovo_documento');

}

function apriPopupSelezioneRuoli() {

    jQuery.get("templates/popup_selezione_ruoli.html", function(data) {

        jdiv_popup.empty();
        jdiv_popup.append(data);

        jpopup_selezione_ruoli = jQuery("#popup_selezione_ruoli");
        jbody_tabella_relaziona_ruolo = jQuery("#body_tabella_relaziona_ruolo");
        jsearch_nuovo_nome_ruolo = jQuery("#search_nuovo_nome_ruolo");

        jsearch_nuovo_nome_ruolo.val("");
        jsearch_nuovo_nome_ruolo.trigger('autoresize');

        filtro_ruoli_relazionabili.record = elemento_selezionato_crmid;
        filtro_ruoli_relazionabili.nome_ruolo = "";
        getRuoliRelazionabiliAElemento(filtro_ruoli_relazionabili);

        inizializzazioneMaterialize();

        jpopup_selezione_ruoli.openModal();

        jsearch_nuovo_nome_ruolo.keyup(function(ev) {

            var nome_ruolo_temp = jsearch_nuovo_nome_ruolo.val();

            var code = ev.which;
            if (code == 13 || nome_ruolo_temp == "") {

                filtro_ruoli_relazionabili.record = elemento_selezionato_crmid;
                filtro_ruoli_relazionabili.nome_ruolo = nome_ruolo_temp;

                getRuoliRelazionabiliAElemento(filtro_ruoli_relazionabili);

            }

        });

    });

}

function apriPopupSelezioneProcesso() {

    jQuery.get("templates/popup_selezione_processo.html", function(data) {

        jdiv_popup.empty();
        jdiv_popup.append(data);

        jpopup_selezione_processo = jQuery("#popup_selezione_processo");
        jbody_tabella_relaziona_processo = jQuery("#body_tabella_relaziona_processo");
        jbottone_crea_nuovo_processo = jQuery("#bottone_crea_nuovo_processo");
        jsearch_nuovo_nome_processo = jQuery("#search_nuovo_nome_processo");

        jsearch_nuovo_nome_processo.val("");
        jsearch_nuovo_nome_processo.trigger('autoresize');

        filtro_processi.record = elemento_selezionato_crmid;
        filtro_processi.processo_aperto = record;
        filtro_processi.nome_processo = "";

        inizializzazioneMaterialize();

        getProcessiRelazionabili(filtro_processi);

        jpopup_selezione_processo.openModal();

        jsearch_nuovo_nome_processo.keyup(function(ev) {

            var nome_processo_temp = jsearch_nuovo_nome_processo.val();

            var code = ev.which;
            if (code == 13 || nome_processo_temp == "") {

                filtro_processi.record = elemento_selezionato_crmid;
                filtro_processi.processo_aperto = record;
                filtro_processi.nome_processo = nome_processo_temp;

                getProcessiRelazionabili(filtro_processi);

            }

        });

    });

}

function apriPopupSelezioneRischi() {

    jQuery.get("templates/popup_selezione_rischi.html", function(data) {

        jdiv_popup.empty();
        jdiv_popup.append(data);

        jpopup_selezione_rischi = jQuery("#popup_selezione_rischi");
        jsearch_nuovo_nome_rischio_qualita = jQuery("#search_nuovo_nome_rischio_qualita");
        jsearch_nuovo_nome_rischio_privacy = jQuery("#search_nuovo_nome_rischio_privacy");
        jsearch_nuovo_nome_rischio_sicurezza = jQuery("#search_nuovo_nome_rischio_sicurezza");
        jtabs_selezione_rischi = jQuery("#tabs_selezione_rischi");

        jbody_tabella_associa_rischi_qualita = jQuery("#body_tabella_associa_rischi_qualita");
        jbody_tabella_associa_rischi_privacy = jQuery("#body_tabella_associa_rischi_privacy");
        jbody_tabella_associa_rischi_sicurezza = jQuery("#body_tabella_associa_rischi_sicurezza");

        jsearch_nuovo_nome_rischio_qualita.val("");
        jsearch_nuovo_nome_rischio_qualita.trigger('autoresize');

        jsearch_nuovo_nome_rischio_privacy.val("");
        jsearch_nuovo_nome_rischio_privacy.trigger('autoresize');

        jsearch_nuovo_nome_rischio_sicurezza.val("");
        jsearch_nuovo_nome_rischio_sicurezza.trigger('autoresize');

        filtro_rischi_relazionabili.record = elemento_selezionato_crmid;
        filtro_rischi_relazionabili.nome_rischio = "";
        getRischiRelazionabiliAElemento(filtro_rischi_relazionabili);

        inizializzazioneMaterialize();

        jpopup_selezione_rischi.openModal();

        jtabs_selezione_rischi.tabs('select_tab', 'tab_rischi_qualita');

    });

}

function getDocumentiElemento(filtro) {

    jQuery.ajax({
        url: 'GetDocumentiElemento.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {

        },
        success: function(data) {

            var lista_documenti_temp = "";
            documento = [];

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    lista_documenti_temp += "<tr>";
                    lista_documenti_temp += "<td style='width: 150px; text-align: center;'>";
                    lista_documenti_temp += "<a id='down_" + data[i].notesid + "' class='bottone_download_documento btn-floating btn-medium waves-effect waves-light amber' title='Download'><i class='material-icons'>file_download</i></a>";
                    lista_documenti_temp += "<a id='el_" + data[i].notesid + "' class='bottone_disassocia_documento btn-floating btn-medium waves-effect waves-light red' title='Delete' style='margin-left: 10px;'><i class='material-icons'>close</i></a>";
                    lista_documenti_temp += "</td>";
                    lista_documenti_temp += "<td>" + data[i].title + "</td>";
                    lista_documenti_temp += "</tr>";

                    documento[data[i].notesid] = {
                        notesid: data[i].notesid,
                        attachmentsid: data[i].attachmentsid,
                        title: data[i].title
                    };

                }

            } else {

                lista_documenti_temp += "<tr><td colspan='5' style='text-align: center;'><em>Nessun documento trovato!</em></td></tr>";

            }

            jbody_tabella_documenti.empty();
            jbody_tabella_documenti.append(lista_documenti_temp);

            jbottone_download_documento = $(".bottone_download_documento");
            jbottone_disassocia_documento = $(".bottone_disassocia_documento");

            jbottone_download_documento.click(function() {

                var documento_selezionato = $(this).attr("id");
                documento_selezionato = documento_selezionato.substring(5, documento_selezionato.length);

                location.href = "DownloadfileKP.php?fileid=" + documento[documento_selezionato].attachmentsid + "&entityid=" + documento_selezionato;

            });

            jbottone_disassocia_documento.click(function() {

                var documento_selezionato = $(this).attr("id");
                documento_selezionato = documento_selezionato.substring(3, documento_selezionato.length);

                dhtmlx.confirm({
                    type: "confirm",
                    text: "Eseguendo questa operazione il documento verrà diassociato; continuare?",
                    callback: function(result) {

                        if (result) {

                            disassociaDocumentoElemento(elemento_selezionato_crmid, documento_selezionato);

                        }

                    }
                });

            });

        },
        fail: function() {

            console.error("Errore");

            location.reload();

        }
    });

}

function getDocumentiRelazionabiliAElemento(filtro) {

    jQuery.ajax({
        url: 'GetDocumentiRelazionabiliAElemento.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {

        },
        success: function(data) {

            var lista_documenti_temp = "";

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    lista_documenti_temp += "<tr>";
                    lista_documenti_temp += "<td style='text-align: center;'><a id='link_" + data[i].notesid + "' class='bottone_link_documento btn-floating btn-medium waves-effect waves-light amber' title='Link'><i class='material-icons'>insert_link</i></a></td>";
                    lista_documenti_temp += "<td>" + data[i].title + "</td>";
                    lista_documenti_temp += "</tr>";

                }

            } else {

                lista_documenti_temp += "<tr><td colspan='5' style='text-align: center;'><em>Nessun documento trovato!</em></td></tr>";

            }

            jbody_tabella_associa_documenti.empty();
            jbody_tabella_associa_documenti.append(lista_documenti_temp);

            jbottone_link_documento = jQuery(".bottone_link_documento");
            jbottone_link_documento.click(function() {

                var documento_selezionato = $(this).attr("id");
                documento_selezionato = documento_selezionato.substring(5, documento_selezionato.length);

                setLinkDocumento(elemento_selezionato_crmid, documento_selezionato);

            });

        },
        fail: function() {

            console.error("Errore");

            location.reload();

        }
    });

}

function setLinkDocumento(elemento_id, documento_id) {

    if (!salvataggioInCorso) {

        salvataggioInCorso = true;

        var dati = {
            record: elemento_id,
            documento: documento_id
        };

        jQuery.ajax({
            url: 'SetLinkDocumentoElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

                dhtmlx.message({
                    id: "alert_salvataggio",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });

            },
            success: function(data) {

                if (data.length > 0) {

                    getDocumentiRelazionabiliAElemento(filtro_documenti_relazionabili)
                    getDocumentiElemento(filtro_documenti);

                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

                dhtmlx.message.hide("alert_salvataggio");
                salvataggioInCorso = false;

            },
            fail: function() {

                console.error("Errore");

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }
        });

    }

}

function disassociaDocumentoElemento(elemento_id, documento_id) {

    if (!salvataggioInCorso) {

        salvataggioInCorso = true;

        var dati = {
            record: elemento_id,
            documento: documento_id
        };

        jQuery.ajax({
            url: 'RemoveLinkDocumentoElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

                dhtmlx.message({
                    id: "alert_salvataggio",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });

            },
            success: function(data) {

                if (data.length > 0) {

                    getDocumentiElemento(filtro_documenti);

                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

                dhtmlx.message.hide("alert_salvataggio");
                salvataggioInCorso = false;

            },
            fail: function() {

                console.error("Errore");

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }
        });

    }

}

function getProcessiRelazionabili(filtro) {

    jQuery.ajax({
        url: 'GetProcessiRelazionabiliAElemento.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {

        },
        success: function(data) {

            var lista_processi_temp = "";

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    if (data[i].id != record) {

                        lista_processi_temp += "<tr>";
                        lista_processi_temp += "<td style='text-align: center;'><a id='link_" + data[i].id + "' class='bottone_link_processo btn-floating btn-medium waves-effect waves-light amber' title='Link'><i class='material-icons'>insert_link</i></a></td>";
                        lista_processi_temp += "<td>" + data[i].nome + "</td>";
                        lista_processi_temp += "</tr>";

                    }

                }

            }

            if (lista_processi_temp == "") {
                lista_processi_temp += "<tr><td colspan='5' style='text-align: center;'><em>Nessun processo trovato!</em></td></tr>";
            }

            jbody_tabella_relaziona_processo.empty();
            jbody_tabella_relaziona_processo.append(lista_processi_temp);

            jbottone_link_processo = jQuery(".bottone_link_processo");
            jbottone_link_processo.click(function() {

                var processo_selezionato_temp = $(this).attr("id");
                processo_selezionato_temp = processo_selezionato_temp.substring(5, processo_selezionato_temp.length);

                setLinkProcesso(elemento_selezionato_crmid, processo_selezionato_temp);

            });

            jbottone_crea_nuovo_processo.click(function() {

                if( !salvataggioInCorso ){
                    creaNuovoProcessoCollegato(elemento_selezionato_crmid);
                }

            });

        },
        fail: function() {

            console.error("Errore");

            location.reload();

        }
    });

}

function setLinkProcesso(elemento_id, processo_id) {

    if (!salvataggioInCorso) {

        salvataggioInCorso = true;

        var dati = {
            record: elemento_id,
            processo: processo_id
        };

        jQuery.ajax({
            url: 'SetLinkProcessoElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

                dhtmlx.message({
                    id: "alert_salvataggio",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });

            },
            success: function(data) {

                if (data.length > 0) {

                    jreadonly_processo_relazionato.val(data[0].nome);
                    jreadonly_processo_relazionato.trigger('autoresize');

                    Materialize.updateTextFields();

                    jpopup_selezione_processo.closeModal();

                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

                dhtmlx.message.hide("alert_salvataggio");
                salvataggioInCorso = false;

            },
            fail: function() {

                console.error("Errore");

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }
        });

    }

}

function disassociaProcessoTask(elemento_id) {

    if (!salvataggioInCorso) {

        salvataggioInCorso = true;

        var dati = {
            record: elemento_id
        };

        jQuery.ajax({
            url: 'UnsetLinkProcessoElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

                dhtmlx.message({
                    id: "alert_salvataggio",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });

            },
            success: function(data) {

                if (data.length > 0) {

                    jreadonly_processo_relazionato.val("");
                    jreadonly_processo_relazionato.trigger('autoresize');

                    Materialize.updateTextFields();

                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

                dhtmlx.message.hide("alert_salvataggio");
                salvataggioInCorso = false;

            },
            fail: function() {

                console.error("Errore");

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }
        });

    }

}

function getRuoliElemento(filtro) {

    jQuery.ajax({
        url: 'GetRuoliElemento.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {

        },
        success: function(data) {

            var lista_ruoli_temp = "";

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    lista_ruoli_temp += "<tr>";
                    lista_ruoli_temp += "<td style='width: 80px; text-align: center;'>";
                    lista_ruoli_temp += "<a id='el_" + data[i].id + "' class='bottone_disassocia_ruolo btn-floating btn-medium waves-effect waves-light red' title='Delete' style='margin-left: 10px;'><i class='material-icons'>close</i></a>";
                    lista_ruoli_temp += "</td>";
                    lista_ruoli_temp += "<td>" + data[i].nome + "</td>";
                    lista_ruoli_temp += "</tr>";

                }

            } else {

                lista_ruoli_temp += "<tr><td colspan='5' style='text-align: center;'><em>Nessun ruolo trovato!</em></td></tr>";

            }

            jbody_tabella_ruoli.empty();
            jbody_tabella_ruoli.append(lista_ruoli_temp);

            jbottone_disassocia_ruolo = $(".bottone_disassocia_ruolo");

            jbottone_disassocia_ruolo.click(function() {

                var ruolo_selezionato = $(this).attr("id");
                ruolo_selezionato = ruolo_selezionato.substring(3, ruolo_selezionato.length);

                dhtmlx.confirm({
                    type: "confirm",
                    text: "Eseguendo questa operazione il ruolo verrà diassociato; continuare?",
                    callback: function(result) {

                        if (result) {

                            disassociaRuoloElemento(elemento_selezionato_crmid, ruolo_selezionato);

                        }

                    }
                });

            });

        },
        fail: function() {

            console.error("Errore");

            location.reload();

        }
    });

}

function getRuoliRelazionabiliAElemento(filtro) {

    jQuery.ajax({
        url: 'GetRuoliRelazionabiliAElemento.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {

        },
        success: function(data) {

            var lista_ruoli_temp = "";

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    lista_ruoli_temp += "<tr>";
                    lista_ruoli_temp += "<td style='text-align: center;'><a id='link_" + data[i].id + "' class='bottone_link_ruolo btn-floating btn-medium waves-effect waves-light amber' title='Link'><i class='material-icons'>insert_link</i></a></td>";
                    lista_ruoli_temp += "<td>" + data[i].nome + "</td>";
                    lista_ruoli_temp += "</tr>";

                }

            } else {

                lista_ruoli_temp += "<tr><td colspan='5' style='text-align: center;'><em>Nessun ruolo trovato!</em></td></tr>";

            }

            jbody_tabella_relaziona_ruolo.empty();
            jbody_tabella_relaziona_ruolo.append(lista_ruoli_temp);

            jbottone_link_ruolo = jQuery(".bottone_link_ruolo");
            jbottone_link_ruolo.click(function() {

                var ruolo_selezionato = $(this).attr("id");
                ruolo_selezionato = ruolo_selezionato.substring(5, ruolo_selezionato.length);

                setLinkRuolo(elemento_selezionato_crmid, ruolo_selezionato);

            });

        },
        fail: function() {

            console.error("Errore");

            location.reload();

        }
    });

}

function setLinkRuolo(elemento_id, ruolo_id) {

    if (!salvataggioInCorso) {

        salvataggioInCorso = true;

        var dati = {
            record: elemento_id,
            ruolo: ruolo_id
        };

        jQuery.ajax({
            url: 'SetLinkRuoloElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

                dhtmlx.message({
                    id: "alert_salvataggio",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });

            },
            success: function(data) {

                if (data.length > 0) {

                    getRuoliRelazionabiliAElemento(filtro_ruoli_relazionabili);
                    getRuoliElemento(filtro_ruoli);

                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

                dhtmlx.message.hide("alert_salvataggio");
                salvataggioInCorso = false;

            },
            fail: function() {

                console.error("Errore");

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }
        });

    }

}

function disassociaRuoloElemento(elemento_id, ruolo_id) {

    if (!salvataggioInCorso) {

        salvataggioInCorso = true;

        var dati = {
            record: elemento_id,
            ruolo: ruolo_id
        };

        jQuery.ajax({
            url: 'UnsetRuoloElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

                dhtmlx.message({
                    id: "alert_salvataggio",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });

            },
            success: function(data) {

                if (data.length > 0) {

                    getRuoliElemento(filtro_ruoli);

                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

                dhtmlx.message.hide("alert_salvataggio");
                salvataggioInCorso = false;

            },
            fail: function() {

                console.error("Errore");

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }
        });

    }

}

function setDescrizioneElemento(elemento_id, descrizione) {

    if (!salvataggioInCorso) {

        salvataggioInCorso = true;

        var dati = {
            record: elemento_id,
            descrizione: HtmlEntities.kpencode(descrizione)
        };

        jQuery.ajax({
            url: 'SetDescrizioneElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

                dhtmlx.message({
                    id: "alert_salvataggio",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });

            },
            success: function(data) {

                if (data.length > 0) {


                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

                dhtmlx.message.hide("alert_salvataggio");
                salvataggioInCorso = false;

            },
            fail: function() {

                console.error("Errore");

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }
        });

    }

}

function setValoreAggiuntoElemento(elemento_id, valore) {

    if (!salvataggioInCorso) {

        salvataggioInCorso = true;

        var dati = {
            record: elemento_id,
            valore: valore
        };

        jQuery.ajax({
            url: 'SetValoreAggiuntoElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

                dhtmlx.message({
                    id: "alert_salvataggio",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });

            },
            success: function(data) {

                if (data.length > 0) {


                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

                dhtmlx.message.hide("alert_salvataggio");
                salvataggioInCorso = false;

            },
            fail: function() {

                console.error("Errore");

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }
        });

    }

}

function getWorkflowElemento(filtro) {

    jQuery.ajax({
        url: 'GetWorkflowElemento.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {

        },
        success: function(data) {

            var lista_workflow_temp = "";

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    lista_workflow_temp += "<tr>";
                    lista_workflow_temp += "<td style='width: 150px; text-align: center;'>";
                    lista_workflow_temp += "<a id='view_" + data[i].id + "' class='bottone_visualizza_workflow btn-floating btn-medium waves-effect waves-light amber' title='Download'><i class='material-icons'>visibility</i></a>";
                    lista_workflow_temp += "<a id='el_" + data[i].id + "' class='bottone_disassocia_workflow btn-floating btn-medium waves-effect waves-light red' title='Delete' style='margin-left: 10px;'><i class='material-icons'>close</i></a>";
                    lista_workflow_temp += "</td>";
                    lista_workflow_temp += "<td>" + data[i].nome + "</td>";
                    lista_workflow_temp += "</tr>";

                }

            } else {

                lista_workflow_temp += "<tr><td colspan='5' style='text-align: center;'><em>Nessun workflow trovato!</em></td></tr>";

            }

            jbody_tabella_workflow.empty();
            jbody_tabella_workflow.append(lista_workflow_temp);

            jbottone_visualizza_workflow = $(".bottone_visualizza_workflow");
            jbottone_disassocia_workflow = $(".bottone_disassocia_workflow");

            jbottone_visualizza_workflow.click(function() {

                var workflow_selezionato = $(this).attr("id");
                workflow_selezionato = workflow_selezionato.substring(5, workflow_selezionato.length);

                window.open("../../../../index.php?module=Settings&action=SettingsAjax&file=ProcessMaker&parenttab=Settings&mode=detail&id=" + workflow_selezionato, "_blank");

            });

            jbottone_disassocia_workflow.click(function() {

                var workflow_selezionato = $(this).attr("id");
                workflow_selezionato = workflow_selezionato.substring(3, workflow_selezionato.length);

                dhtmlx.confirm({
                    type: "confirm",
                    text: "Eseguendo questa operazione il workflow verrà diassociato; continuare?",
                    callback: function(result) {

                        if (result) {

                            disassociaWorflowElemento(elemento_selezionato_crmid, workflow_selezionato);

                        }

                    }
                });

            });

        },
        fail: function() {

            console.error("Errore");

            location.reload();

        }
    });

}

function getWorkflowRelazionabiliAElemento(filtro) {

    jQuery.ajax({
        url: 'GetWorkflowRelazionabiliAElemento.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {

        },
        success: function(data) {

            var lista_workflow_temp = "";

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    lista_workflow_temp += "<tr>";
                    lista_workflow_temp += "<td style='text-align: center;'><a id='link_" + data[i].id + "' class='bottone_link_workflow btn-floating btn-medium waves-effect waves-light amber' title='Link'><i class='material-icons'>insert_link</i></a></td>";
                    lista_workflow_temp += "<td>" + data[i].nome + "</td>";
                    lista_workflow_temp += "</tr>";

                }

            } else {

                lista_workflow_temp += "<tr><td colspan='5' style='text-align: center;'><em>Nessun workflow trovato!</em></td></tr>";

            }

            jbody_tabella_relaziona_workflow.empty();
            jbody_tabella_relaziona_workflow.append(lista_workflow_temp);

            jbottone_link_workflow = jQuery(".bottone_link_workflow");
            jbottone_link_workflow.click(function() {

                var workflow_selezionato = $(this).attr("id");
                workflow_selezionato = workflow_selezionato.substring(5, workflow_selezionato.length);

                setLinkWorkflow(elemento_selezionato_crmid, workflow_selezionato);

            });

        },
        fail: function() {

            console.error("Errore");

            location.reload();

        }
    });

}

function setLinkWorkflow(elemento_id, workflow_id) {

    if (!salvataggioInCorso) {

        salvataggioInCorso = true;

        var dati = {
            record: elemento_id,
            workflow: workflow_id
        };

        jQuery.ajax({
            url: 'SetLinkWorkflowElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

                dhtmlx.message({
                    id: "alert_salvataggio",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });

            },
            success: function(data) {

                if (data.length > 0) {

                    getWorkflowRelazionabiliAElemento(filtro_workflow_relazionabili);
                    getWorkflowElemento(filtro_workflow);

                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

                dhtmlx.message.hide("alert_salvataggio");
                salvataggioInCorso = false;

            },
            fail: function() {

                console.error("Errore");

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }
        });

    }

}

function apriPopupSelezioneWorkflow() {

    jQuery.get("templates/popup_selezione_workflow.html", function(data) {

        jdiv_popup.empty();
        jdiv_popup.append(data)

        jbody_tabella_relaziona_workflow = jQuery("#body_tabella_relaziona_workflow");
        jpopup_selezione_workflow = jQuery("#popup_selezione_workflow");

        filtro_workflow_relazionabili.record = elemento_selezionato_crmid;
        filtro_workflow_relazionabili.nome_workflow = "";

        getWorkflowRelazionabiliAElemento(filtro_workflow_relazionabili);

        inizializzazioneMaterialize();

        jpopup_selezione_workflow.openModal();

    });

}

function disassociaWorflowElemento(elemento_id, workflow_id) {

    if (!salvataggioInCorso) {

        salvataggioInCorso = true;

        var dati = {
            record: elemento_id,
            workflow: workflow_id
        };

        jQuery.ajax({
            url: 'UnsetWorkflowElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

                dhtmlx.message({
                    id: "alert_salvataggio",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });

            },
            success: function(data) {

                if (data.length > 0) {

                    getWorkflowElemento(filtro_workflow);

                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

                dhtmlx.message.hide("alert_salvataggio");
                salvataggioInCorso = false;

            },
            fail: function() {

                console.error("Errore");

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }
        });

    }

}

function setArrayListaElementiById(lista_elementi) {

    elemento_by_id = [];
    array_bpmn_id_elementi = [];

    for (var i = 0; i < lista_elementi.length; i++) {

        //console.log(lista_elementi[i]);

        elemento_by_id[lista_elementi[i].bpmn_id] = {
            bpmn_id: lista_elementi[i].bpmn_id,
            crm_id: lista_elementi[i].id,
            valore_aggiunto: lista_elementi[i].valore_aggiunto,
            tipo_entita_bpmn: lista_elementi[i].tipo_entita_bpmn,
        };

        array_bpmn_id_elementi.push(lista_elementi[i].bpmn_id);

    }

    //console.table(elemento_by_id);

}

function applicaStileAggiuntivoShape(lista_elementi) {

    //console.table(lista_elementi);

    for (var i = 0; i < lista_elementi.length; i++) {

        if (lista_elementi[i].tipo_entita_bpmn == "subProcess" && lista_elementi[i].procedureid == "0") {

            canvas.toggleMarker(lista_elementi[i].bpmn_id, 'SubProcessNonCollegato-shape');

        }

        applicaStileValoreAggiuntoNonValoreAggiunto(lista_elementi[i]);

    }

}

function applicaStileValoreAggiuntoNonValoreAggiunto(elemento) {

    switch (elemento.valore_aggiunto) {
        case "A valore aggiunto":
            canvas.toggleMarker(elemento.bpmn_id, 'ValoreAggiunto-shape');
            break;
        case "Non a valore ma necessaria":
            canvas.toggleMarker(elemento.bpmn_id, 'NonValoreAggiuntoNecessaria-shape');
            break;
        case "Non a valore e non necessaria":
            canvas.toggleMarker(elemento.bpmn_id, 'NonValoreAggiuntoNonNecessaria-shape');
            break;
    }

}

function applicaStileShape(object) {

    if (object.type == "bpmn:SubProcess") {

        canvas.toggleMarker(object.id, 'SubProcess-shape');

    } else if (object.type == "bpmn:StartEvent") {

        canvas.toggleMarker(object.id, 'StartEvent-shape');

    } else if (object.type == "bpmn:EndEvent") {

        canvas.toggleMarker(object.id, 'EndEvent-shape');

    } else if (object.type == "bpmn:Task") {

        canvas.toggleMarker(object.id, 'Task-shape');

    }

}

function getRischiRelazionabiliAElemento(filtro) {

    jQuery.ajax({
        url: 'GetRischiRelazionabiliAElemento.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {

        },
        success: function(data) {

            getRischiQualitaRelazionabiliAElemento(data.lista_rischi_qualita);
            getRischiPrivacyRelazionabiliAElemento(data.lista_rischi_privacy);
            getRischiSicurezzaRelazionabiliAElemento(data.lista_rischi_sicurezza);

        },
        fail: function() {

            console.error("Errore");

            location.reload();

        }
    });

}

function getRischiQualitaRelazionabiliAElemento(lista) {

    var lista_temp = "";

    if (lista.length > 0) {

        for (var i = 0; i < lista.length; i++) {

            if (lista[i].id != record) {

                lista_temp += "<tr>";
                lista_temp += "<td style='text-align: center;'><a id='rischi_qualita_" + lista[i].id + "' class='bottone_rischi_qualita_elemento btn-floating btn-medium waves-effect waves-light amber' title='Link'><i class='material-icons'>insert_link</i></a></td>";
                lista_temp += "<td>" + lista[i].nome + "</td>";
                lista_temp += "</tr>";

            }

        }

    }

    if (lista_temp == "") {
        lista_temp += "<tr><td colspan='5' style='text-align: center;'><em>Nessun rischio qualita trovato!</em></td></tr>";
    }

    jbody_tabella_associa_rischi_qualita.empty();
    jbody_tabella_associa_rischi_qualita.append(lista_temp);

    jbottone_rischi_qualita_elemento = jQuery(".bottone_rischi_qualita_elemento");
    jbottone_rischi_qualita_elemento.click(function() {

        var elemento_selezionato_temp = $(this).attr("id");
        elemento_selezionato_temp = elemento_selezionato_temp.substring(15, elemento_selezionato_temp.length);

        setRischioQualitaElemento(elemento_selezionato_crmid, elemento_selezionato_temp);

    });


}

function getRischiPrivacyRelazionabiliAElemento(lista) {

    var lista_temp = "";

    if (lista.length > 0) {

        for (var i = 0; i < lista.length; i++) {

            if (lista[i].id != record) {

                lista_temp += "<tr>";
                lista_temp += "<td style='text-align: center;'><a id='rischi_privacy_" + lista[i].id + "' class='bottone_rischi_privacy_elemento btn-floating btn-medium waves-effect waves-light amber' title='Link'><i class='material-icons'>insert_link</i></a></td>";
                lista_temp += "<td>" + lista[i].nome + "</td>";
                lista_temp += "</tr>";

            }

        }

    }

    if (lista_temp == "") {
        lista_temp += "<tr><td colspan='5' style='text-align: center;'><em>Nessun rischio privacy trovato!</em></td></tr>";
    }

    jbody_tabella_associa_rischi_privacy.empty();
    jbody_tabella_associa_rischi_privacy.append(lista_temp);

    jbottone_rischi_privacy_elemento = jQuery(".bottone_rischi_privacy_elemento");
    jbottone_rischi_privacy_elemento.click(function() {

        var elemento_selezionato_temp = $(this).attr("id");
        elemento_selezionato_temp = elemento_selezionato_temp.substring(15, elemento_selezionato_temp.length);

        setRischioPrivacyElemento(elemento_selezionato_crmid, elemento_selezionato_temp);

    });


}

function getRischiSicurezzaRelazionabiliAElemento(lista) {

    var lista_temp = "";

    if (lista.length > 0) {

        for (var i = 0; i < lista.length; i++) {

            if (lista[i].id != record) {

                lista_temp += "<tr>";
                lista_temp += "<td style='text-align: center;'><a id='rischi_sicurezza_" + lista[i].id + "' class='bottone_rischi_sicurezzza_elemento btn-floating btn-medium waves-effect waves-light amber' title='Link'><i class='material-icons'>insert_link</i></a></td>";
                lista_temp += "<td>" + lista[i].nome + "</td>";
                lista_temp += "</tr>";

            }

        }

    }

    if (lista_temp == "") {
        lista_temp += "<tr><td colspan='5' style='text-align: center;'><em>Nessun rischio sicurezza trovato!</em></td></tr>";
    }

    jbody_tabella_associa_rischi_sicurezza.empty();
    jbody_tabella_associa_rischi_sicurezza.append(lista_temp);

    jbottone_rischi_sicurezzza_elemento = jQuery(".bottone_rischi_sicurezzza_elemento");
    jbottone_rischi_sicurezzza_elemento.click(function() {

        var elemento_selezionato_temp = $(this).attr("id");
        elemento_selezionato_temp = elemento_selezionato_temp.substring(17, elemento_selezionato_temp.length);

        setRischioSicurezzaElemento(elemento_selezionato_crmid, elemento_selezionato_temp);

    });


}

function getRischiElemento(filtro) {

    jQuery.ajax({
        url: 'GetRischiElemento.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {

        },
        success: function(data) {

            var lista_temp = "";

            if (data.lista_rischi_qualita.length + data.lista_rischi_privacy.length + data.lista_rischi_sicurezza.length > 0) {

                for (var i = 0; i < data.lista_rischi_qualita.length; i++) {

                    lista_temp += "<tr>";
                    lista_temp += "<td style='width: 80px; text-align: center;'>";
                    lista_temp += "<a id='rs_" + data.lista_rischi_qualita[i].id + "' class='bottone_disassocia_rischio_qualita btn-floating btn-medium waves-effect waves-light red' title='Delete' style='margin-left: 10px;'><i class='material-icons'>close</i></a>";
                    lista_temp += "</td>";
                    lista_temp += "<td>" + data.lista_rischi_qualita[i].nome + "</td>";
                    lista_temp += "<td style='width: 100px;'>" + data.lista_rischi_qualita[i].tipo + "</td>";
                    lista_temp += "</tr>";

                }

                for (var y = 0; y < data.lista_rischi_privacy.length; y++) {

                    lista_temp += "<tr>";
                    lista_temp += "<td style='width: 80px; text-align: center;'>";
                    lista_temp += "<a id='rs_" + data.lista_rischi_privacy[y].id + "' class='bottone_disassocia_rischio_privacy btn-floating btn-medium waves-effect waves-light red' title='Delete' style='margin-left: 10px;'><i class='material-icons'>close</i></a>";
                    lista_temp += "</td>";
                    lista_temp += "<td>" + data.lista_rischi_privacy[y].nome + "</td>";
                    lista_temp += "<td style='width: 100px;'>" + data.lista_rischi_privacy[y].tipo + "</td>";
                    lista_temp += "</tr>";
                }

                for (var z = 0; z < data.lista_rischi_sicurezza.length; z++) {

                    lista_temp += "<tr>";
                    lista_temp += "<td style='width: 80px; text-align: center;'>";
                    lista_temp += "<a id='rs_" + data.lista_rischi_sicurezza[z].id + "' class='bottone_disassocia_rischio_sicurezza btn-floating btn-medium waves-effect waves-light red' title='Delete' style='margin-left: 10px;'><i class='material-icons'>close</i></a>";
                    lista_temp += "</td>";
                    lista_temp += "<td>" + data.lista_rischi_sicurezza[z].nome + "</td>";
                    lista_temp += "<td style='width: 100px;'>" + data.lista_rischi_sicurezza[z].tipo + "</td>";
                    lista_temp += "</tr>";
                }

            } else {

                lista_temp += "<tr><td colspan='5' style='text-align: center;'><em>Nessun rischio trovato!</em></td></tr>";

            }

            jbody_tabella_rischi.empty();
            jbody_tabella_rischi.append(lista_temp);

            jbottone_disassocia_rischio_qualita = $(".bottone_disassocia_rischio_qualita");
            jbottone_disassocia_rischio_privacy = $(".bottone_disassocia_rischio_privacy");
            jbottone_disassocia_rischio_sicurezza = $(".bottone_disassocia_rischio_sicurezza");

            jbottone_disassocia_rischio_qualita.click(function() {

                var rischio_selezionato = $(this).attr("id");
                rischio_selezionato = rischio_selezionato.substring(3, rischio_selezionato.length);

                dhtmlx.confirm({
                    type: "confirm",
                    text: "Eseguendo questa operazione il rischio verrà diassociato; continuare?",
                    callback: function(result) {

                        if (result) {

                            disassociaRischioElemento(elemento_selezionato_crmid, rischio_selezionato, "Qualita");

                        }

                    }
                });

            });

            jbottone_disassocia_rischio_privacy.click(function() {

                var rischio_selezionato = $(this).attr("id");
                rischio_selezionato = rischio_selezionato.substring(3, rischio_selezionato.length);

                dhtmlx.confirm({
                    type: "confirm",
                    text: "Eseguendo questa operazione il rischio verrà diassociato; continuare?",
                    callback: function(result) {

                        if (result) {

                            disassociaRischioElemento(elemento_selezionato_crmid, rischio_selezionato, "Privacy");

                        }

                    }
                });

            });

            jbottone_disassocia_rischio_sicurezza.click(function() {

                var rischio_selezionato = $(this).attr("id");
                rischio_selezionato = rischio_selezionato.substring(3, rischio_selezionato.length);

                dhtmlx.confirm({
                    type: "confirm",
                    text: "Eseguendo questa operazione il rischio verrà diassociato; continuare?",
                    callback: function(result) {

                        if (result) {

                            disassociaRischioElemento(elemento_selezionato_crmid, rischio_selezionato, "Sicurezza");

                        }

                    }
                });

            });

        },
        fail: function() {

            console.error("Errore");

            location.reload();

        }
    });

}

function setRischioQualitaElemento(elemento_id, rischio_id) {

    if (!salvataggioInCorso) {

        salvataggioInCorso = true;

        var dati = {
            record: elemento_id,
            rischio: rischio_id
        };

        jQuery.ajax({
            url: 'SetLinkRischioQualitaElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

                dhtmlx.message({
                    id: "alert_salvataggio",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });

            },
            success: function(data) {

                if (data.length > 0) {

                    getRischiRelazionabiliAElemento(filtro_rischi_relazionabili);
                    getRischiElemento(filtro_rischi);

                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

                dhtmlx.message.hide("alert_salvataggio");
                salvataggioInCorso = false;

            },
            fail: function() {

                console.error("Errore");

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }
        });

    }

}

function setRischioPrivacyElemento(elemento_id, rischio_id) {

    if (!salvataggioInCorso) {

        salvataggioInCorso = true;

        var dati = {
            record: elemento_id,
            rischio: rischio_id
        };

        jQuery.ajax({
            url: 'SetLinkRischioPrivacyElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

                dhtmlx.message({
                    id: "alert_salvataggio",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });

            },
            success: function(data) {

                if (data.length > 0) {

                    getRischiRelazionabiliAElemento(filtro_rischi_relazionabili);
                    getRischiElemento(filtro_rischi);

                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

                dhtmlx.message.hide("alert_salvataggio");
                salvataggioInCorso = false;

            },
            fail: function() {

                console.error("Errore");

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }
        });

    }

}

function setRischioSicurezzaElemento(elemento_id, rischio_id) {

    if (!salvataggioInCorso) {

        salvataggioInCorso = true;

        var dati = {
            record: elemento_id,
            rischio: rischio_id
        };

        jQuery.ajax({
            url: 'SetLinkRischioSicurezzaElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

                dhtmlx.message({
                    id: "alert_salvataggio",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });

            },
            success: function(data) {

                if (data.length > 0) {

                    getRischiRelazionabiliAElemento(filtro_rischi_relazionabili);
                    getRischiElemento(filtro_rischi);

                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

                dhtmlx.message.hide("alert_salvataggio");
                salvataggioInCorso = false;

            },
            fail: function() {

                console.error("Errore");

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }
        });

    }

}

function disassociaRischioElemento(elemento_id, rischio_id, tipo) {

    if (!salvataggioInCorso) {

        salvataggioInCorso = true;

        var dati = {
            record: elemento_id,
            rischio: rischio_id,
            tipo: tipo
        };

        jQuery.ajax({
            url: 'UnsetRischioElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

                dhtmlx.message({
                    id: "alert_salvataggio",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });

            },
            success: function(data) {

                if (data.length > 0) {

                    getRischiElemento(filtro_rischi);

                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

                dhtmlx.message.hide("alert_salvataggio");
                salvataggioInCorso = false;

            },
            fail: function() {

                console.error("Errore");

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }
        });

    }

}

function creaNuovoProcessoCollegato(processo) {

    salvataggioInCorso = true;

    var dati = {
        processo: processo
    };

    jQuery.ajax({
        url: 'CreaSottoprocesso.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {

            dhtmlx.message({
                id: "alert_salvataggio",
                type: "error",
                text: "Salvataggio in Corso!",
                expire: -1
            });

        },
        success: function(data) {

            if (data.length > 0) {

                jreadonly_processo_relazionato.val(data[0].nome);
                jreadonly_processo_relazionato.trigger('autoresize');

                Materialize.updateTextFields();

                jpopup_selezione_processo.closeModal();

            }
            /*else {

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }*/

            dhtmlx.message.hide("alert_salvataggio");
            salvataggioInCorso = false;

        },
        fail: function() {

            console.error("Errore");

            dhtmlx.message({
                id: "alert_errore",
                type: "error",
                text: "Errore!",
                expire: -1
            });

            location.reload();

            salvataggioInCorso = false;

        }
    });

}

function gestioneRevisioni() {

    jform_descrizione_revisione.val("");
    jform_descrizione_revisione.trigger('autoresize');

    Materialize.updateTextFields();

    var dati = {
        record: record
    };

    jQuery.ajax({
        url: 'GetDatiRevisione.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {

        },
        success: function(data) {

            jform_descrizione_revisione.val(data.log);
            jform_descrizione_revisione.trigger('autoresize');

            Materialize.updateTextFields();

            jpopup_revisione.openModal();

        },
        fail: function() {

        }
    });

}

function confermaRevisione(){

    var descrizione_temp = jform_descrizione_revisione.val();
    descrizione_temp = HtmlEntities.kpencode(descrizione_temp);

    descrizione_revisione = descrizione_temp;

    var dati = {
        record: record,
        descrizione: descrizione_revisione
    };

    jQuery.ajax({
        url: 'ConfermaRevisione.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {

        },
        success: function(data) {

            self.close();

        },
        fail: function() {

        }
    });

}

function checkCampiObbligatoriRevisione() {

    var result = true;

    if (jform_descrizione_revisione.val().trim() == "") {
        result = false;
        jform_descrizione_revisione.css("background-color", "yellow");

        Materialize.toast("La descrizione della revisione non può essere lasciata vuota!", 4000);

    } else {
        jform_descrizione_revisione.css("background-color", "white");
    }

    return result;

}

function apriPopupSelezioneTipoAttivita(){

    jQuery.get("templates/popup_selezione_tipo_attivita.html", function(data) {

        jdiv_popup.empty();
        jdiv_popup.append(data)

        jbody_tabella_relaziona_tipo_attivita = jQuery("#body_tabella_relaziona_tipo_attivita");
        jpopup_selezione_tipo_attivita = jQuery("#popup_selezione_tipo_attivita");
        jsearch_nuovo_nome_tipo_attivita = jQuery("#search_nuovo_nome_tipo_attivita");

        jsearch_nuovo_nome_tipo_attivita.val("");
        jsearch_nuovo_nome_tipo_attivita.trigger('autoresize');

        filtro_tipi_attivita_relazionabili.record = elemento_selezionato_crmid;
        filtro_tipi_attivita_relazionabili.nome_tipo_attivita = "";
        getTipiAttivitaRelazionabiliAElemento(filtro_tipi_attivita_relazionabili);

        inizializzazioneMaterialize();

        jpopup_selezione_tipo_attivita.openModal();

        jsearch_nuovo_nome_tipo_attivita.keyup(function(ev) {

            var nome_tipo_attivita_temp = jsearch_nuovo_nome_tipo_attivita.val();

            var code = ev.which;
            if (code == 13 || nome_tipo_attivita_temp == "") {

                filtro_tipi_attivita_relazionabili.record = elemento_selezionato_crmid;
                filtro_tipi_attivita_relazionabili.nome_tipo_attivita = nome_tipo_attivita_temp;

                getTipiAttivitaRelazionabiliAElemento(filtro_tipi_attivita_relazionabili);

            }

        });

    });


}

function getTipiAttivitaRelazionabiliAElemento(filtro) {

    jQuery.ajax({
        url: 'GetTipiAttivitaRelazionabiliAElemento.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {

        },
        success: function(data) {

            var lista_righe_temp = "";

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    lista_righe_temp += "<tr>";
                    lista_righe_temp += "<td style='text-align: center;'><a id='tp_attivita_" + data[i].id + "' class='bottone_link_tipo_attivita btn-floating btn-medium waves-effect waves-light amber' title='Link'><i class='material-icons'>insert_link</i></a></td>";
                    lista_righe_temp += "<td>" + data[i].nome + "</td>";
                    lista_righe_temp += "</tr>";

                }

            } else {

                lista_righe_temp += "<tr><td colspan='5' style='text-align: center;'><em>Nessun tipo attività trovato!</em></td></tr>";

            }

            jbody_tabella_relaziona_tipo_attivita.empty();
            jbody_tabella_relaziona_tipo_attivita.append(lista_righe_temp);

            jbottone_link_tipo_attivita = jQuery(".bottone_link_tipo_attivita");
            jbottone_link_tipo_attivita.click(function() {

                var elemento_selezionato_temp = $(this).attr("id");
                elemento_selezionato_temp = elemento_selezionato_temp.substring(12, elemento_selezionato_temp.length);

                setLinkTipoAttivita(elemento_selezionato_crmid, elemento_selezionato_temp);

            });

        },
        fail: function() {

            console.error("Errore");

            location.reload();

        }
    });

}

function setLinkTipoAttivita(elemento_id, tipo_attivita_id) {

    if (!salvataggioInCorso) {

        salvataggioInCorso = true;

        var dati = {
            record: elemento_id,
            tipo_attivita: tipo_attivita_id
        };

        jQuery.ajax({
            url: 'SetLinkTipoAttivitaElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

                dhtmlx.message({
                    id: "alert_salvataggio",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });

            },
            success: function(data) {

                if (data.length > 0) {

                    jreadonly_tipo_attivita.val(data[0].nome);
                    jreadonly_tipo_attivita.trigger('autoresize');

                    filtro_rischi.record = elemento_id;
                    filtro_rischi.nome_rischio = "";

                    getRischiElemento(filtro_rischi);

                    Materialize.updateTextFields();

                    jpopup_selezione_tipo_attivita.closeModal();

                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

                dhtmlx.message.hide("alert_salvataggio");
                salvataggioInCorso = false;

            },
            fail: function() {

                console.error("Errore");

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }
        });

    }

}

function disassociaTipoAttivitaTask(elemento_id) {

    if (!salvataggioInCorso) {

        salvataggioInCorso = true;

        var dati = {
            record: elemento_id
        };

        jQuery.ajax({
            url: 'UnsetLinkTipoAttivitaElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

                dhtmlx.message({
                    id: "alert_salvataggio",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });

            },
            success: function(data) {

                if (data.length > 0) {

                    jreadonly_tipo_attivita.val("");
                    jreadonly_tipo_attivita.trigger('autoresize');

                    Materialize.updateTextFields();

                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

                dhtmlx.message.hide("alert_salvataggio");
                salvataggioInCorso = false;

            },
            fail: function() {

                console.error("Errore");

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }
        });

    }

}

function getAreaElemento(filtro) {

    jQuery.ajax({
        url: 'GetAreeElemento.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {

        },
        success: function(data) {

            var lista_record_temp = "";

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    lista_record_temp += "<tr>";
                    lista_record_temp += "<td style='width: 70px; text-align: center;'>";
                    lista_record_temp += "<a id='el_" + data[i].id + "' class='bottone_disassocia_area btn-floating btn-medium waves-effect waves-light red' title='Delete' style='margin-left: 10px;'><i class='material-icons'>close</i></a>";
                    lista_record_temp += "</td>";
                    lista_record_temp += "<td>" + data[i].nome + "</td>";
                    lista_record_temp += "<td>" + data[i].azienda + "</td>";
                    lista_record_temp += "<td>" + data[i].stabilimento + "</td>";
                    lista_record_temp += "</tr>";

                }

            } else {

                lista_record_temp += "<tr><td colspan='5' style='text-align: center;'><em>Nessuna area trovata!</em></td></tr>";

            }

            jbody_tabella_aree.empty();
            jbody_tabella_aree.append(lista_record_temp);

            jbottone_disassocia_area = $(".bottone_disassocia_area");

            jbottone_disassocia_area.click(function() {

                var elemento_selezionato_temp = $(this).attr("id");
                elemento_selezionato_temp = elemento_selezionato_temp.substring(3, elemento_selezionato_temp.length);

                dhtmlx.confirm({
                    type: "confirm",
                    text: "Eseguendo questa operazione l'area verrà diassociata; continuare?",
                    callback: function(result) {

                        if (result) {

                            disassociaAreaElemento(elemento_selezionato_crmid, elemento_selezionato_temp);

                        }

                    }
                });

            });

        },
        fail: function() {

            console.error("Errore");

            location.reload();

        }
    });

}

function disassociaAreaElemento(elemento_id, area_id) {

    if (!salvataggioInCorso) {

        salvataggioInCorso = true;

        var dati = {
            record: elemento_id,
            area: area_id
        };

        jQuery.ajax({
            url: 'UnsetAreaElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

                dhtmlx.message({
                    id: "alert_salvataggio",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });

            },
            success: function(data) {

                if (data.length > 0) {

                    getAreaElemento(filtro_aree);

                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

                dhtmlx.message.hide("alert_salvataggio");
                salvataggioInCorso = false;

            },
            fail: function() {

                console.error("Errore");

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }
        });

    }

}

function apriPopupSelezioneArea() {

    jQuery.get("templates/popup_selezione_area.html", function(data) {

        jdiv_popup.empty();
        jdiv_popup.append(data)

        jbody_tabella_relaziona_area = jQuery("#body_tabella_relaziona_area");
        jpopup_selezione_area = jQuery("#popup_selezione_area");
        jsearch_nuovo_nome_azienda = jQuery("#search_nuovo_nome_azienda");
        jsearch_nuovo_nome_area = jQuery("#search_nuovo_nome_area");
        jsearch_nuovo_nome_stabilimento = jQuery("#search_nuovo_nome_stabilimento");

        jsearch_nuovo_nome_azienda.val("");
        jsearch_nuovo_nome_azienda.trigger('autoresize');

        jsearch_nuovo_nome_area.val("");
        jsearch_nuovo_nome_area.trigger('autoresize');

        jsearch_nuovo_nome_stabilimento.val("");
        jsearch_nuovo_nome_stabilimento.trigger('autoresize');

        Materialize.updateTextFields();

        filtro_aree_relazionabili.record = elemento_selezionato_crmid;
        filtro_aree_relazionabili.nome_area = "";
        filtro_aree_relazionabili.nome_azienda = "";
        filtro_aree_relazionabili.nome_stabilimento = "";

        getAreeRelazionabiliAElemento(filtro_aree_relazionabili);

        inizializzazioneMaterialize();

        jpopup_selezione_area.openModal();

        jsearch_nuovo_nome_area.keyup(function(ev) {

            var nome_area_temp = jsearch_nuovo_nome_area.val();
    
            var code = ev.which;
            if (code == 13 || nome_area_temp == "") {
    
                filtro_aree_relazionabili.record = elemento_selezionato_crmid;
                filtro_aree_relazionabili.nome_area = nome_area_temp;
                getAreeRelazionabiliAElemento(filtro_aree_relazionabili);
    
            }
    
        });

        jsearch_nuovo_nome_azienda.keyup(function(ev) {

            var nome_azienda_temp = jsearch_nuovo_nome_azienda.val();
    
            var code = ev.which;
            if (code == 13 || nome_azienda_temp == "") {
    
                filtro_aree_relazionabili.record = elemento_selezionato_crmid;
                filtro_aree_relazionabili.nome_azienda = nome_azienda_temp;
                getAreeRelazionabiliAElemento(filtro_aree_relazionabili);
    
            }
    
        });

        jsearch_nuovo_nome_stabilimento.keyup(function(ev) {

            var nome_stabilimento_temp = jsearch_nuovo_nome_stabilimento.val();
    
            var code = ev.which;
            if (code == 13 || nome_stabilimento_temp == "") {
    
                filtro_aree_relazionabili.record = elemento_selezionato_crmid;
                filtro_aree_relazionabili.nome_stabilimento = nome_stabilimento_temp;
                getAreeRelazionabiliAElemento(filtro_aree_relazionabili);
    
            }
    
        });

    });

}

function getAreeRelazionabiliAElemento(filtro) {

    jQuery.ajax({
        url: 'GetAreeRelazionabiliAElemento.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {

        },
        success: function(data) {

            var lista_record_temp = "";

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    lista_record_temp += "<tr>";
                    lista_record_temp += "<td style='text-align: center;'><a id='link_" + data[i].id + "' class='bottone_link_area btn-floating btn-medium waves-effect waves-light amber' title='Link'><i class='material-icons'>insert_link</i></a></td>";
                    lista_record_temp += "<td>" + data[i].nome + "</td>";
                    lista_record_temp += "<td>" + data[i].azienda + "</td>";
                    lista_record_temp += "<td>" + data[i].stabilimento + "</td>";
                    lista_record_temp += "</tr>";

                }

            } else {

                lista_record_temp += "<tr><td colspan='5' style='text-align: center;'><em>Nessuna area trovato!</em></td></tr>";

            }

            jbody_tabella_relaziona_area.empty();
            jbody_tabella_relaziona_area.append(lista_record_temp);

            jbottone_link_area = jQuery(".bottone_link_area");
            jbottone_link_area.click(function() {

                var elemento_selezionato_temp = $(this).attr("id");
                elemento_selezionato_temp = elemento_selezionato_temp.substring(5, elemento_selezionato_temp.length);

                setLinkArea(elemento_selezionato_crmid, elemento_selezionato_temp);

            });

        },
        fail: function() {

            console.error("Errore");

            location.reload();

        }
    });

}

function setLinkArea(elemento_id, area_id) {

    if (!salvataggioInCorso) {

        salvataggioInCorso = true;

        var dati = {
            record: elemento_id,
            area: area_id
        };

        jQuery.ajax({
            url: 'SetLinkAreaElemento.php',
            dataType: 'json',
            async: true,
            data: dati,
            beforeSend: function() {

                dhtmlx.message({
                    id: "alert_salvataggio",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });

            },
            success: function(data) {

                if (data.length > 0) {

                    getAreeRelazionabiliAElemento(filtro_aree_relazionabili)
                    getAreaElemento(filtro_aree)

                } else {

                    dhtmlx.message({
                        id: "alert_errore",
                        type: "error",
                        text: "Errore!",
                        expire: -1
                    });

                    location.reload();

                }

                dhtmlx.message.hide("alert_salvataggio");
                salvataggioInCorso = false;

            },
            fail: function() {

                console.error("Errore");

                dhtmlx.message({
                    id: "alert_errore",
                    type: "error",
                    text: "Errore!",
                    expire: -1
                });

                location.reload();

            }
        });

    }

}

function gestioneApprovazioneProcesso(){

    getBPMNioSVG();

    apriPopupFirmaEsecutore();

}

function confermaProcesso(){

    getBPMNioSVG();

    setConfermaProcesso();

}

function apriPopupFirmaEsecutore(){

    jpopup_revisione.closeModal();

    jQuery.get("templates/popup_firma_esecutore.html", function(data) {

        jdiv_popup.empty();
        jdiv_popup.append(data);

        jpopup_firma_esecutore = jQuery("#popup_firma_esecutore");
        jbottone_pulisci_firma_esecutore = jQuery("#bottone_pulisci_firma_esecutore");
        jbottone_salva_firma_esecutore = jQuery("#bottone_salva_firma_esecutore");
        jarea_firma_esecutore_div = jQuery("#area_firma_esecutore");
        jcaricamento = jQuery(".caricamento");
        jbottone_keyboard_esecutore = jQuery("#bottone_keyboard_esecutore"); 
        jform_firmatario_esecutore = jQuery("#form_firmatario_esecutore");

        jform_firmatario_esecutore.val(disegnato_da);
        jform_firmatario_esecutore.trigger('autoresize');

        jcaricamento.hide();

        jarea_firma_esecutore = jQuery("#area_firma_esecutore").jqScribble();

        jarea_firma_esecutore.data('jqScribble').clear();

        inizializzazioneMaterialize();

        var options_modal = {
            opacity: 0.5,
            in_duration: 350,
            out_duration: 250,
            ready: undefined,
            complete: undefined,
            dismissible: false,
            starting_top: '4%'
        };

        jpopup_firma_esecutore.openModal(options_modal);

        dimensionaAreaFirmaEsecutore();


        jbottone_pulisci_firma_esecutore.click(function(){

            jarea_firma_esecutore.data('jqScribble').clear();

        });

        jbottone_keyboard_esecutore.click(function() {

            jform_firmatario_esecutore.blur();
    
        });

        jbottone_salva_firma_esecutore.click(function() {

            salvaFirmaEsecutore();
    
        });

    });

}

function apriPopupFirmaApprovatore(){

    jQuery.get("templates/popup_firma_approvatore.html", function(data) {

        jdiv_popup.empty();
        jdiv_popup.append(data);

        jpopup_firma_approvatore = jQuery("#popup_firma_approvatore");
        jbottone_pulisci_firma_approvatore = jQuery("#bottone_pulisci_firma_approvatore");
        jbottone_salva_firma_approvatore = jQuery("#bottone_salva_firma_approvatore");
        jarea_firma_approvatore_div = jQuery("#area_firma_approvatore");
        jcaricamento = jQuery(".caricamento");
        jbottone_keyboard_approvatore = jQuery("#bottone_keyboard_approvatore"); 
        jform_firmatario_approvatore = jQuery("#form_firmatario_approvatore");

        jform_firmatario_approvatore.val("");
        jform_firmatario_approvatore.trigger('autoresize');

        jcaricamento.hide();

        jarea_firma_approvatore = jQuery("#area_firma_approvatore").jqScribble();

        jarea_firma_approvatore.data('jqScribble').clear();

        inizializzazioneMaterialize();

        var options_modal = {
            opacity: 0.5,
            in_duration: 350,
            out_duration: 250,
            ready: undefined,
            complete: undefined,
            dismissible: false,
            starting_top: '4%'
        };

        jpopup_firma_approvatore.openModal(options_modal);

        dimensionaAreaFirmaApprovatore();


        jbottone_pulisci_firma_approvatore.click(function(){

            jarea_firma_approvatore.data('jqScribble').clear();

        });

        jbottone_keyboard_approvatore.click(function() {

            jform_firmatario_approvatore.blur();
    
        });

        jbottone_salva_firma_approvatore.click(function() {
            
            salvaFirmaApprovatore();
    
        });

    });

}

function dimensionaAreaFirmaEsecutore() {

    var altezza_area_firma = jarea_firma_esecutore_div.height();
    var larghezza_area_firma = jarea_firma_esecutore_div.width();

    $("#area_firma_esecutore").height(altezza_area_firma)

    var settings_firma = {
        width: larghezza_area_firma,
        height: altezza_area_firma
    };

    //console.log(settings_firma);

    jarea_firma_esecutore.data("jqScribble").update(settings_firma);

}

function dimensionaAreaFirmaApprovatore() {

    var altezza_area_firma = jarea_firma_approvatore_div.height();
    var larghezza_area_firma = jarea_firma_approvatore_div.width();

    $("#area_firma_approvatore").height(altezza_area_firma)

    var settings_firma = {
        width: larghezza_area_firma,
        height: altezza_area_firma
    };

    //console.log(settings_firma);

    jarea_firma_approvatore.data("jqScribble").update(settings_firma);

}


function salvaFirmaEsecutore() {
    if (checkFirmaEsecutore()) {

        jarea_firma_esecutore.data("jqScribble").save(function(imageData) {

            var firmatario_temp = jform_firmatario_esecutore.val();
            firmatario_temp = HtmlEntities.kpencode(firmatario_temp);

            var dati_firma = {
                imagedata: imageData,
                crmid: record,
                firmatario: firmatario_temp
            };

            jQuery.ajax({
                url: 'SalvaFirmaEsecutore.php',
                dataType: 'json',
                type: "POST",
                async: true,
                data: dati_firma,
                beforeSend: function() {

                    dhtmlx.message({
                        id: "dhtmlx_salvataggio_in_corso",
                        type: "error",
                        text: "Salvataggio in corso!"
                    });

                    jcaricamento.show();

                },
                success: function(data) {

                    //console.table(data);

                    jpopup_firma_esecutore.closeModal();;
                    jcaricamento.hide();
                    dhtmlx.message.hide("dhtmlx_salvataggio_in_corso");

                    apriPopupFirmaApprovatore();

                },
                fail: function() {

                    jcaricamento.hide();
                    dhtmlx.message.hide("dhtmlx_salvataggio_in_corso");
                    console.error("Errore nel salvataggio della firma");

                }
            });

        });
    }

}

function salvaFirmaApprovatore() {
    if (checkFirmaApprovatore()) {

        jarea_firma_approvatore.data("jqScribble").save(function(imageData) {

            var firmatario_temp = jform_firmatario_approvatore.val();
            firmatario_temp = HtmlEntities.kpencode(firmatario_temp);

            var revisione_temp = '0';
            if( revisione ){
                revisione_temp = '1';
            }

            var dati_firma = {
                imagedata: imageData,
                crmid: record,
                firmatario: firmatario_temp,
                revisione: revisione_temp,
                descrizione: descrizione_revisione
            };

            jQuery.ajax({
                url: 'SalvaFirmaApprovatore.php',
                dataType: 'json',
                type: "POST",
                async: true,
                data: dati_firma,
                beforeSend: function() {

                    dhtmlx.message({
                        id: "dhtmlx_salvataggio_in_corso",
                        type: "error",
                        text: "Salvataggio in corso!"
                    });

                    jcaricamento.show();

                },
                success: function(data) {

                    //console.table(data);

                    self.close();

                    jpopup_firma_approvatore.closeModal();
                    jcaricamento.hide();
                    dhtmlx.message.hide("dhtmlx_salvataggio_in_corso");

                },
                fail: function() {

                    jcaricamento.hide();
                    dhtmlx.message.hide("dhtmlx_salvataggio_in_corso");
                    console.error("Errore nel salvataggio della firma");

                }
            });

        });
    }

}

function checkFirmaEsecutore() {

    if (jarea_firma_esecutore.data("jqScribble").blank) {

        Materialize.toast('Apporre la firma!', 4000);

        return false;
    }

    if (jform_firmatario_esecutore.val() == "") {

        jform_firmatario_esecutore.css("background", "#ECFEA5");
        Materialize.toast('Indicare il firmatario!', 4000);

        return false;

    }

    return true;

}

function checkFirmaApprovatore() {

    if (jarea_firma_approvatore.data("jqScribble").blank) {

        Materialize.toast('Apporre la firma!', 4000);

        return false;
    }

    if (jform_firmatario_approvatore.val() == "") {

        jform_firmatario_approvatore.css("background", "#ECFEA5");
        Materialize.toast('Indicare il firmatario!', 4000);

        return false;

    }

    return true;

}

function salvaSVG(svg){

    var dati = {
        svg: svg,
        crmid: record,
    };

    jQuery.ajax({
        url: 'SalvaSVG.php',
        dataType: 'json',
        type: "POST",
        async: true,
        data: dati,
        beforeSend: function() {

            dhtmlx.message({
                id: "dhtmlx_salvataggio_in_corso",
                type: "error",
                text: "Salvataggio in corso!"
            });

        },
        success: function(data) {

            //console.table(data);

            dhtmlx.message.hide("dhtmlx_salvataggio_in_corso");

        },
        fail: function() {

            dhtmlx.message.hide("dhtmlx_salvataggio_in_corso");
            console.error("Errore nel salvataggio SVG");

        }
    });

}

function getBPMNioSVG(){

    bpmnViewer.saveSVG({}, function(err, svg) {

        //console.log(err);
        if (svg) {

            //console.log(svg);
            salvaSVG(svg);

        }

    });

}

function setConfermaProcesso() {
    
    var dati= {
        crmid: record
    };

    jQuery.ajax({
        url: 'SetConfermaProcesso.php',
        dataType: 'json',
        type: "POST",
        async: true,
        data: dati,
        beforeSend: function() {

            dhtmlx.message({
                id: "dhtmlx_salvataggio_in_corso",
                type: "error",
                text: "Salvataggio in corso!"
            });

        },
        success: function(data) {

            //console.table(data);

            self.close();
            dhtmlx.message.hide("dhtmlx_salvataggio_in_corso");

        },
        fail: function() {


            dhtmlx.message.hide("dhtmlx_salvataggio_in_corso");
            console.error("Errore nel salvataggio della firma");

        }
    });

}