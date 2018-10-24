/* kpro@tom29062017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

var jpanel;
var jdiv_canvas;
var jtitolo_pagina;

var altezza_schermo = '';
var larghezza_schermo = '';

var timer_check_offline;
var readonly = false;
var myclock = "";
var isOnline = true;
var salvataggioInCorso = false;
var autosaveActive = true;
var intervallo_autosave = 30000;
var timer_autosave;
var salvataggio_automatico = false;
var prosegui = false;
var bpmnModeler;
var newDiagramXML;
var canvas;
var elementRegistry;
var bpmn_xml;
var bpmn_xml_nuovo;
var richiedi_approvazione;
var elemento_by_id = [];
var array_bpmn_id_elementi = [];

//Elementi personalizzati

var jbottone_annulla_bpmn;
var jpopup_revisione;
var jform_descrizione_revisione;
var jbottone_salva_revisione;
var jbottone_annulla_revisione;
var jbottone_prosegui;
var jcaricamento;
var saveButton;
var jbottone_centra_disegno;


//Elementi personalizzati end

jQuery(document).ready(function() {

    inizializzazione();

    inizializzazioneMaterialize();

    inizializzazioneBPMN();

    inizializzazioneExtra();

    if( autosaveActive ){
        timer_autosave = window.setInterval(autosave, intervallo_autosave);
    }

});

function inizializzazione() {

    jtitolo_pagina = $("#titolo_pagina");

    window.addEventListener('resize', function() {
        reSize();
    }, false);

    reSize();

    setTitoloPagina();

}

function reSize() {

    jpanel = $(".panel");
    jdiv_canvas = $("#div_canvas");

    larghezza_schermo = window.innerWidth;
    altezza_schermo = window.innerHeight;

    jpanel.css("height", innerHeight - 100);
    jdiv_canvas.css("height", innerHeight - 100);

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

                var titolo_pagina = data[0].nome;

                if(data[0].revisione_di != 0 && data[0].revisione_di != ""){
                    titolo_pagina = titolo_pagina + " (Rev. " + data[0].numero_revisione + ")";
                }

                var titolo_pagina_temp = "";
                titolo_pagina_temp += "<a href='#' onclick='chiudiScheda()' class='breadcrumb' style='font-weight: bold; font-size: 24px !important; color: #2b577c;'>" + titolo_pagina + "</a>";
                titolo_pagina_temp += "<a href='#!' class='breadcrumb' style='color: #2b577c;'>BPMN Modeler</a>";
                jtitolo_pagina.html(titolo_pagina_temp);

                richiedi_approvazione = data[0].richiedi_approvazione;

            }
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
                self.close();
            }

        }
    });

}

function inizializzazioneBPMN() {

    (function(BpmnModeler) {

        // create modeler
        bpmnModeler = new BpmnModeler({
            container: '#canvas'
        });

        getBPMNxml();

    })(window.BpmnJS);

    // save diagram on button click
    saveButton = document.querySelector('#bottone_salva_bpmn');

    saveButton.addEventListener('click', function() {

        // get the diagram contents
        bpmnModeler.saveXML({ format: true }, function(err, xml) {

            if (err) {
                console.error('diagram save failed', err);
            } else {
                if (!salvataggioInCorso) {
                    salvataggioInCorso = true;
                    salvaBPMN(xml);
                }
            }
        });
    });

    bpmnModeler.on('element.changed', function(event) {
        var element = event.element;
        //console.log("element.changed");
        //console.log(element);

        /*switch (element.type) {
            case "bpmn:Task":
                //element.businessObject.name = "ciao";
                break;
        }*/

    });

    bpmnModeler.on('element.paste', function(event) {
        var element = event.element;
        //console.log("element.paste");
        //console.log(element);

    });

    bpmnModeler.on('element.click', function(event) {
        var element = event.element;
        //console.log("element.click");
        //console.log(element);

    });

    bpmnModeler.on('element.hover', function(event) {
        var element = event.element;
        //console.log("element.hover");
        //console.log(element);

    });

    bpmnModeler.on('element.out', function(event) {
        var element = event.element;
        //console.log("element.outs");
        //console.log(element);

    });

    bpmnModeler.on('element.move', function(event) {
        var element = event.element;
        //console.log("element.move");
        //console.log(element);

    });

    bpmnModeler.on('element.delete', function(event) {
        var element = event.element;
        //console.log("element.delete");
        //console.log(element);

    });

}

function importBPMNxml(xml, lista_elementi) {

    // import diagram
    bpmnModeler.importXML(xml, function(err) {

        if (err) {
            return console.error('could not import BPMN 2.0 diagram', err);
        }

        canvas = bpmnModeler.get('canvas');
        elementRegistry = bpmnModeler.get('elementRegistry');

        // zoom to fit full viewport
        canvas.zoom('fit-viewport');

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

        setArrayListaElementiById(lista_elementi);

        applicaStileAggiuntivoShape(lista_elementi);

    });

}

function newBPMNDiagram() {

    jQuery.get('resources/newDiagram.bpmn', function(data) {
        //console.log(data);
        importBPMNxml(data, []);
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

                if (diagramXML != "") {

                    bpmn_xml = diagramXML;

                    importBPMNxml(diagramXML, data[0]["elementi"]);

                } else {

                    newBPMNDiagram();

                }

            } else {

                newBPMNDiagram();

            }

        },
        fail: function() {

        }
    });

}

function salvaBPMN(xml) {

    //var dati = jQuery.param({ 'bpmn': JSON.stringify(xml) });
    var dati = { 'bpmn': xml };

    jQuery.ajax({
        url: 'SalvaBPMN.php?record=' + record,
        type: 'POST',
        data: dati,
        async: true,
        beforeSend: function() {

            jcaricamento.show();
            
            if( !salvataggio_automatico ){
                dhtmlx.message({
                    id: "alert_salvataggio_in_corso",
                    type: "error",
                    text: "Salvataggio in Corso!",
                    expire: -1
                });
            }

        },
        success: function(data) {

            bpmn_xml_nuovo = xml;

            salvataggioInCorso = false;

            jcaricamento.hide();

            if( !salvataggio_automatico ){
                dhtmlx.message.hide("alert_salvataggio_in_corso");

                dhtmlx.message({
                    text: "BPMN Salvato!"
                });
            }

            salvataggio_automatico = false;

            if( prosegui ){
                window.open("pagina_editing.php?return_module=drawer&record=" + record, "_self");
            }

        },
        fail: function() {
            jcaricamento.hide();
            salvataggioInCorso = false;
        }
    });

}

function inizializzazioneExtra() {

    jbottone_annulla_bpmn = jQuery("#bottone_annulla_bpmn");
    jpopup_revisione = jQuery("#popup_revisione");
    jform_descrizione_revisione = jQuery("#form_descrizione_revisione");
    jbottone_salva_revisione = jQuery("#bottone_salva_revisione");
    jbottone_annulla_revisione = jQuery("#bottone_annulla_revisione");
    jcaricamento = jQuery(".caricamento");
    jbottone_prosegui = jQuery("#bottone_prosegui");
    jbottone_centra_disegno = jQuery("#bottone_centra_disegno");

    jbottone_annulla_bpmn.click(function() {

        chiudiScheda();

    });

    jbottone_salva_revisione.click(function() {

        if (checkCampiObbligatoriRevisione() && !salvataggioInCorso) {
            salvataggioInCorso = true;
            salvaRevisione();
        }

    });

    jbottone_prosegui.click(function() {

        prosegui = true;
        salvataggio_automatico = false;
        saveButton.click();

    });

    jbottone_centra_disegno.click(function() {

        canvas.zoom('fit-viewport', {});
        canvas.zoom('fit-viewport', {});

    });

}

function gestioneRevisioni() {

    jform_descrizione_revisione.val("");
    jform_descrizione_revisione.trigger('autoresize');

    Materialize.updateTextFields();

    jpopup_revisione.openModal();

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

function salvaRevisione() {

    jQuery.ajax({
        url: 'SalvaRevisione.php?record=' + record + "&descrizione=" + jform_descrizione_revisione.val(),
        type: 'POST',
        data: jQuery.param({ 'bpmn_xml': JSON.stringify(bpmn_xml), 'bpmn_xml_nuovo': JSON.stringify(bpmn_xml_nuovo) }),
        async: true,
        beforeSend: function() {

            jcaricamento.show();

        },
        success: function(data) {

            Materialize.toast("Revisione Salvata!", 4000);

            if( prosegui ){
                window.open("pagina_editing.php?return_module=drawer&record=" + record, "_self");
            }

            jcaricamento.hide();

            salvataggioInCorso = false;

        },
        fail: function() {

            jcaricamento.hide();
            salvataggioInCorso = false;

        }
    });

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

function autosave(){

    if( autosaveActive ){

        prosegui = false;
        salvataggio_automatico = true;
        saveButton.click();

    }

}