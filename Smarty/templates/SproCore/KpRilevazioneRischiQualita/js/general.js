/* kpro@tom03042018 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2018, Kpro Consulting Srl
 */

var altezza_schermo;
var larghezza_schermo;

var record;

var kproLayout;
var jlayoutObj;
var mProTree;
var bpmnViewer;
var altezza_layoutObj;

var canvas;
var overlays;
var elementRegistry;

var azienda = "";
var stabilimento = "";
var processi_navigati = [];
var array_processi_padri = [];
var elemento_selezionato_rilevazione = {};
var elemento_selezionato =  {};
var processo_aperto = {};
var elemento_by_id = [];
var array_bpmn_id_elementi = [];
var shape_selezionata = {};
var lista_element_id = [];
var lista_element_bpmnid = [];
var lista_pericoli_attivita = [];

var readonly = true;
var chiudi_nodi_non_correnti = true;
var bpmn_inizializzato = false;
var time_blink = 3000;
var changeStatusArrow;

var jrilevazioneContainer;
var jdettagliContainer;
var jgraphContainer;
var jbottone_modifica;
var jbottone_salva;
var jbottone_pdf;
var jtabella_rilevazione;
var jbottone_centra_disegno;
var jbottone_processo_precedente;
var jbottone_processo_padre;
var jtr_rischio;
var jreadonly_nome_pericolo;
var jform_probabilita_pericolo;
var jform_magnitudo_pericolo;
var jreadonly_rischio_pericolo;
var jform_misurazione_rischio;
var jform_check_rischio_rilevato;
var jbody_tabella_ruoli_pericolo;
var jbody_tabella_misure;
var jbottone_aggiungi_misura;
var jhelp_probabilita;
var jhelp_magnitudo;
var jlabel_misura_associata_pericolo;
var jtab_ruoli;
var jtab_misure;
var jreadonly_misura_relazionato_a;
var jreadonly_misura_nome_pericolo;
var jform_misura_tipo_misura;
var jform_misura_nome_intervento;
var jform_misura_adottare_entro;
var jform_misura_nota;
var jbottone_salva_misura_riduzione;
var jform_misura_riduzione_probabilita;
var jform_misura_riduzione_magnitudo;
var jtd_pericolo;
var jgif_freccia;

var filtro_processi = {};

jQuery(document).ready(function() {

    record = getObj('record').value;

    inizializzazioneBootstrap();

    inizializzazione();

});

function inizializzazione() {

    jlayoutObj = jQuery("#layoutObj");
    jpopup_generico = jQuery("#popup_generico");
    jbottone_modifica = jQuery("#bottone_modifica");
    jbottone_salva = jQuery("#bottone_salva");
    jbottone_pdf = jQuery("#bottone_pdf");

    window.addEventListener('resize', function() {
        reSize();
    }, false);

    reSize();

    jbottone_modifica.click(function() {

        sbloccaForm();

    });

    jbottone_salva.click(function() {

        if (!readonly) {

            bloccaForm();

        }

    });

    jbottone_pdf.click(function() {


    });

}

function reSize() {

    larghezza_schermo = window.innerWidth;
    altezza_schermo = window.innerHeight;

    altezza_layoutObj = altezza_schermo - 320;

    if (altezza_layoutObj < 500) {
        altezza_layoutObj = 500;
    }

    jlayoutObj.height(altezza_layoutObj);

    if (jdettagliContainer) {
        jdettagliContainer.height(altezza_layoutObj - 80);
    }

    if (jrilevazioneContainer) {

        jrilevazioneContainer.height( jrilevazioneContainer.parent().css("height") );
        jrilevazioneContainer.css("overflow-y", "scroll");

    }

    if (jgraphContainer) {

        jgraphContainer.height( jgraphContainer.parent().css("height") );

    }

}

function inizializzazioneBootstrap() {

    jQuery('.campo_ora_bootstrap').bootstrapMaterialDatePicker({
        date: false,
        shortTime: false,
        format: 'HH:mm',
        lang: 'it',
        cancelText: 'ANNULLA',
        clearText: 'PULISCI',
        clearButton: true
    });

    jQuery('.campo_data_bootstrap').each(function() {

        var temp_id = jQuery(this).prop("id");

        //Se a fronte del campo con classe "campo_data_bootstrap" esiste un bulsante di tipo "trigger_" allora la funzion imposta
        //il triger su tale pulsante, altrimenti il trigger sarà impostato sul campo data stesso
        if (jQuery("#trigger_" + temp_id).length) {

            setupDatePicker(temp_id, {
                trigger: "trigger_" + temp_id,
                date_format: "%D/%M/%Y".replace('%Y', 'YYYY').replace('%M', 'MM').replace('%D', 'DD'),
                language: "it",
                time: false,
                weekStart: 1,
                cancelText: 'ANNULLA',
                clearText: 'PULISCI',
                clearButton: true,
                nowButton: false,
                switchOnClick: false
            });

        } else {

            jQuery("#" + temp_id).bootstrapMaterialDatePicker({
                format: 'DD/MM/YYYY',
                lang: 'it',
                time: false,
                weekStart: 1,
                cancelText: 'ANNULLA',
                clearText: 'PULISCI',
                clearButton: true,
                nowButton: false,
                switchOnClick: false
            });

        }

    });

}

function setupDatePicker(fieldid, options) {

    // default options
    options = jQuery.extend({}, {
        //trigger: null
        date_format: '',
        language: 'en_us',
    }, options || {});

    if (!jQuery.fn.bootstrapMaterialDatePicker) {
        console.log('Material DatePicker not loaded. Unable to initialize datepicker');
        return;
    }

    if (typeof fieldid == 'string') {
        // fieldid may contain spaces, so I can't use jquery "#id" selector
        var field = document.getElementById(fieldid);
        if (!field) return;
        var $field = jQuery(field);
    } else if (fieldid instanceof jQuery) {
        var $field = fieldid;
    } else {
        // unknwown type for fieldid
        console.log('The specified field is not supported');
        return;
    }

    var dpopts = {
        format: options.date_format,
        lang: options.language,
        time: options.time,
        weekStart: options.weekStart,
        cancelText: options.cancelText,
        clearText: options.clearText,
        clearButton: options.clearButton,
        nowButton: options.nowButton,
        switchOnClick: options.switchOnClick
    };

    if (options.trigger) {
        // the picker is shown on the trigger click
        dpopts.triggerEvent = 'showpicker';

        if (typeof options.trigger == 'string') {
            var $trigger = jQuery('#' + options.trigger);
        } else if (options.trigger instanceof jQuery) {
            var $trigger = options.trigger;
        } else {
            console.log('The specified trigger is not supported');
            return;
        }

        $trigger.click(function() {
            $field.trigger('showpicker');
        });
    } else {
        // the picker is shown when focusing the input, the default
    }

    $field.bootstrapMaterialDatePicker(dpopts);
}

function normalizzaData(data) {

    var data_normalizzata = "";
    var anno = "";
    var mese = "";
    var giorno = "";

    data = data.trim();

    var new_date = data.replace(/([.*+?^=!:${}()|\[\]\/\\])/g, "-");

    new_date_split = new_date.split("-");

    if (new_date_split.length == 3) {

        if (new_date_split[2].length == 4) {

            anno = new_date_split[2];
            mese = new_date_split[1];
            giorno = new_date_split[0];

        } else if (new_date_split[0].length == 4) {

            anno = new_date_split[0];
            mese = new_date_split[1];
            giorno = new_date_split[2];

        }

        if (anno != "") {

            anno = String("0" + anno).slice(-4);

            mese = String("0" + mese).slice(-2);

            giorno = String("0" + giorno).slice(-2);

            data_normalizzata = anno + "-" + mese + "-" + giorno;

        }

    }

    return data_normalizzata;

}

function inizializzazioneBPMN() {

    (function(BpmnJS) {

        //create viewer
        bpmnViewer = new BpmnJS({
            container: '#graphContainer'
        });

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
                setProcessoSelezionato(element, false);
                break;
            case "bpmn:Process":
                setProcessoSelezionato(element, true);
                break;
        }

    });

    bpmnViewer.on('element.dblclick', function(event) {
        var element = event.element;
        //console.log("element.click");
        //console.log(element);

        switch (element.type) {
            case "bpmn:Task":
                break;
            case "bpmn:SubProcess":
                apriProcessoDaBPMN(element);
                break;
        }

    });

}

function inizializzazioneLayoutKp() {

    window.dhx4.skin = 'material';

    kproLayout = new dhtmlXLayoutObject({
        parent: "layoutObj",
        pattern: "4H",
        skin: "material"
    });

    kproLayout.setOffsets({
        top: 0,
        right: 0,
        bottom: 0,
        left: 0
    });

    kproLayout.cells("a").setText("Lista Processi");
    kproLayout.cells("a").setCollapsedText("Lista Processi");
    kproLayout.cells("a").setWidth(300);
    kproLayout.cells("a").fixSize(true, false);

    kproLayout.cells("b").setText("Flow Chart");
    kproLayout.cells("b").setCollapsedText("Flow Chart");
    //kproLayout.cells("b").collapse();
    kproLayout.cells("b").fixSize(true, false);

    kproLayout.cells("c").setText("Rilevazione Rischi");
    kproLayout.cells("c").setCollapsedText("Rilevazione Rischi");
    kproLayout.cells("c").fixSize(true, false);

    kproLayout.cells("d").setText("Dettagli");
    kproLayout.cells("d").setCollapsedText("Dettagli");
    kproLayout.cells("d").setWidth(400);
    kproLayout.cells("d").fixSize(true, false);

    kproLayout.cells("b").attachObject("graphContainer");

    kproLayout.cells("c").attachObject("rilevazioneContainer");

    kproLayout.cells("d").attachObject("dettagliContainer");

    inizializzazioneBPMN();

    inizializzazioneAlberoProcessi();

    getDatiRilevazione();

    inizializzazioneExtra();

    reSize();

    kproLayout.attachEvent("onResizeFinish", function() {
        reSize();
        if( bpmn_inizializzato ){
            canvas.zoom('fit-viewport', {});
            canvas.zoom('fit-viewport', {});
        }
    });

    kproLayout.attachEvent("onPanelResizeFinish", function(names) {
        reSize();
        if( bpmn_inizializzato ){
            canvas.zoom('fit-viewport', {});
            canvas.zoom('fit-viewport', {});
        }
    });

    kproLayout.attachEvent("onCollapse", function(name) {
        reSize();
        if( bpmn_inizializzato ){
            canvas.zoom('fit-viewport', {});
            canvas.zoom('fit-viewport', {});
        }
    });

    kproLayout.attachEvent("onExpand", function(name) {
        reSize();
        if( bpmn_inizializzato ){
            canvas.zoom('fit-viewport', {});
            canvas.zoom('fit-viewport', {});
        }
    });

}

function inizializzazioneExtra() {

    jrilevazioneContainer = jQuery("#rilevazioneContainer");
    jdettagliContainer = jQuery("#dettagliContainer");
    jgraphContainer = jQuery("#graphContainer");
    jbottone_centra_disegno = jQuery("#bottone_centra_disegno");
    jbottone_processo_precedente = jQuery("#bottone_processo_precedente");
    jbottone_processo_padre = jQuery("#bottone_processo_padre");

    jbottone_centra_disegno.click(function() {
        canvas.zoom('fit-viewport', {});
        canvas.zoom('fit-viewport', {});
    });

    jbottone_processo_precedente.click(function() {

        if (processi_navigati.length > 1) {

            var processo_corrente = processi_navigati.pop();

            /*console.log("Processo corrente");
            console.log(processo_corrente);*/

            var processo_precedente = processi_navigati.pop();

            /*console.log("Processo precedente");
            console.log(processo_precedente);*/

            mProTree.selectItem(processo_precedente);

            processi_navigati.push(processo_precedente);
            
            //console.log("jbottone_processo_precedente: " + processo_precedente);
            var processo_precedente_array = processo_precedente.split('_');
            var temp_id = processo_precedente_array.pop();
            //console.log("jbottone_processo_precedente: " + temp_id);

            array_processi_padri = processo_precedente_array;

            elemento_selezionato.id = temp_id;
            elemento_selezionato.bpmn_id = "";
            elemento_selezionato.nome = mProTree.getItemText(processo_precedente);
            elemento_selezionato.descrizione = "";
            
            getBPMNxml(elemento_selezionato);

        }

    });

    jbottone_processo_padre.click(function(){

        if (array_processi_padri.length > 1) {

            var elemento_selezionato_temp = getPadreByArray(array_processi_padri);

            processi_navigati.push(elemento_selezionato_temp);

            var temp_id = array_processi_padri.pop();

            elemento_selezionato.id = temp_id;
            elemento_selezionato.bpmn_id = "";
            elemento_selezionato.nome = mProTree.getItemText(elemento_selezionato_temp);
            elemento_selezionato.descrizione = "";

            getBPMNxml(elemento_selezionato);

            mProTree.selectItem(elemento_selezionato_temp);
        
        }

    });

}

function inizializzazioneAlberoProcessi() {

    mProTree = kproLayout.cells("a").attachTree();
    mProTree.setImagesPath("Smarty/templates/SproCore/KpRilevazioneRischiQualita/dhtmlx_codebase/imgs/dhxtree_material/");

    mProTree.setOnClickHandler(function(id) {

        var elemento_selezionato_temp = id.substring(0, 3);

        if (elemento_selezionato_temp != "tp_") {

            elemento_selezionato_temp = id;
            //console.log(" mProTree.setOnClickHandler: " + elemento_selezionato_temp);

            processi_navigati.push(elemento_selezionato_temp);

            if( chiudi_nodi_non_correnti ){
                closeAllRoots(elemento_selezionato_temp);
            }

            elemento_selezionato_temp = elemento_selezionato_temp.split('_');

            var temp_id = elemento_selezionato_temp.pop();
            //console.log(" mProTree.setOnClickHandler: " + temp_id);

            elemento_selezionato.id = temp_id;
            elemento_selezionato.bpmn_id = "";
            elemento_selezionato.nome = mProTree.getItemText(id);
            elemento_selezionato.descrizione = "";

            array_processi_padri = elemento_selezionato_temp;
            //console.log(array_processi_padri);

            getDatiRilevazioneProcesso(elemento_selezionato);
            getBPMNxml(elemento_selezionato);

        } else {

            mProTree.selectItem(elemento_selezionato.id);

        }

    });

    /*filtro_notifiche = {
        stato: stato_notifiche
    };

    getListaNotifiche(filtro_notifiche);*/

}

function getDatiRilevazione(){

    var dati = {
        record: record
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischiQualita/php/getDatiRilevazione.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {

        },
        success: function(data) {

            //console.table(data);
            azienda = data.azienda;
            stabilimento = data.stabilimento;

            filtro_processi = {
                azienda: azienda,
                stabilimento: azienda
            };
        
            getListaProcessi(filtro_processi);
            
        },
        fail: function() {

        }
    });

}

function getListaProcessi(filtro) {

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischiQualita/php/getListaProcessi.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {

        },
        success: function(data) {

            //console.table(data);

            var lista_procedure_temp = "";
            var first_element_temp = "";
            var first_element_text_temp = "";

            var xml = '<?xml version="1.0" encoding="iso-8859-1"?>';
            xml += '<tree id="0">';

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    xml += '<item text="' + data[i].tipo_procedura + '" id="tp_' + data[i].tipo_procedura + '" open="1">';

                    var albero_procedure = getXMLAlberoProcedure(data[i].lista_procedure, "m" + i);

                    if (i == 0) {

                        first_element_temp = albero_procedure.primo_elemento_id;
                        first_element_text_temp = albero_procedure.primo_elemento_nome;

                    }

                    xml += albero_procedure.xml;

                    xml += '</item>';

                }

            }

            xml += '</tree>';

            //console.log("getListaProcessi");
            //console.log(xml);

            mProTree.parse(xml);

            if (first_element_temp != "" && first_element_temp != 0) {

                mProTree.selectItem(first_element_temp);

                processi_navigati.push(first_element_temp);

                first_element_temp = first_element_temp.split('_');

                var temp_id = first_element_temp.pop();

                array_processi_padri = first_element_temp;

                elemento_selezionato = {};

                elemento_selezionato.id = temp_id;
                elemento_selezionato.bpmn_id = "";
                elemento_selezionato.nome = first_element_text_temp;
                elemento_selezionato.descrizione = data[0].descrizione;

                getBPMNxml(elemento_selezionato);

            }
            else{

                jbottone_modifica_disegno_processo.hide();

            }

        },
        fail: function() {

        }
    });

}

function getXMLAlberoProcedure(dati, padre) {

    var result = {
        xml: "",
        primo_elemento_id: 0,
        primo_elemento_nome: ""
    };

    for (var i = 0; i < dati.length; i++) {

        var lista_sottoprocessi_temp = dati[i].lista_sottoprocessi;

        if (lista_sottoprocessi_temp.length > 0) {

            result.xml += '<item text="' + dati[i].nome + '" id="' + padre + '_' + dati[i].id +'">';

            var new_padre = padre + "_" + dati[i].id;

            result.xml += getXMLAlberoSottoprocesso(dati[i].lista_sottoprocessi, new_padre);

            result.xml += '</item>';

        } else {

            result.xml += '<item text="' + dati[i].nome + '" id="' + padre + '_' + dati[i].id +'" />';

        }

        if (i == 0) {
            result.primo_elemento_id = padre + "_" + dati[i].id;
            result.primo_elemento_nome = dati[i].nome;
        }

    }

    return result;

}

function getXMLAlberoSottoprocesso(dati, padre) {

    //console.log(dati);

    var result = "";

    for (var i = 0; i < dati.length; i++) {

        var lista_sottoprocessi_temp = dati[i].lista_sottoprocessi;

        if (lista_sottoprocessi_temp.length > 0) {

            result += '<item text="' + dati[i].nome + '" id="' + padre + '_' + dati[i].procedureid +'">';

            var new_padre = padre + "_" + dati[i].procedureid;

            result += getXMLAlberoSottoprocesso(dati[i].lista_sottoprocessi, new_padre);

            result += '</item>';

        } else {

            result += '<item text="' + dati[i].nome + '" id="' + padre + '_' + dati[i].procedureid +'" />';

        }

    }

    return result;

}

function getBPMNxml(elemento) {

    var dati = {
        record: elemento.id
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischiQualita/php/getProcesso.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {

        },
        success: function(data) {

            //console.table(data);

            if (data.length > 0 && data[0]["processo"] != null) {

                processo_aperto = {};
                elemento_selezionato = {};
                elemento_by_id = [];
                array_bpmn_id_elementi = [];

                processo_aperto.id = data[0]["processo"].id;
                processo_aperto.nome = HtmlEntities.decode(data[0]["processo"].nome);
                processo_aperto.descrizione = HtmlEntities.decode(data[0]["processo"].descrizione);
                processo_aperto.bpmn_xml = data[0]["processo"].bpmn_xml;

                elemento_selezionato.id = data[0]["processo"].id;
                elemento_selezionato.bpmn_id = "";
                elemento_selezionato.nome = HtmlEntities.decode(data[0]["processo"].nome);
                elemento_selezionato.descrizione = HtmlEntities.decode(data[0]["processo"].descrizione);

                jQuery('body').scrollTop(0);

                lista_element_id = [];
                lista_element_bpmnid = [];

                if( data[0]["elementi"] != null ){

                    var temp_elementi = data[0]["elementi"];

                    //console.table(temp_elementi);

                    for( var i = 0; i < temp_elementi.length; i++ ){

                        lista_element_id[ temp_elementi[i].id ] = {
                            nome: temp_elementi[i].nome,
                            bpmn_id: temp_elementi[i].bpmn_id
                        };

                        lista_element_bpmnid[ temp_elementi[i].bpmn_id ] = {
                            nome: temp_elementi[i].nome,
                            id: temp_elementi[i].id
                        };

                    }

                }

                //(console.table(lista_element_id);
                //console.table(lista_element_bpmnid);
                
                setProcessoSelezionatoDaAlbero(processo_aperto);

                var diagramXML = processo_aperto.bpmn_xml;

                if (diagramXML != "") {

                    importBPMNxml(diagramXML, data[0]["elementi"], elemento);

                } else {

                    newBPMNDiagram();

                }

            }

        },
        fail: function() {

        }
    });

}

function newBPMNDiagram() {

    jQuery.get('Smarty/templates/SproCore/KpRilevazioneRischiQualita/resources/newDiagram.bpmn', function(data) {
        importBPMNxml(data, []);
    });

}

function importBPMNxml(xml, lista_elementi, elemento) {

    bpmnViewer.importXML(xml, function(err) {

        if (err) {
            //return console.error('could not import BPMN 2.0 diagram', err);
        }

        canvas = bpmnViewer.get('canvas');
        overlays = bpmnViewer.get('overlays');
        elementRegistry = bpmnViewer.get('elementRegistry');

        //console.log(elementRegistry);

        //zoom to fit full viewport
        //canvas.zoom('fit-viewport');
        canvas.zoom('fit-viewport', {});

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

                //Questo blocco permette di gestire la variazione di stile della shape al passaggio del mouse
                dom_obj.css('cursor', 'pointer');

                dom_obj.hover(function() {
                    canvas.toggleMarker(id, 'highlights-shape');
                }, function() {
                    canvas.toggleMarker(id, 'highlights-shape');
                });

                //Rendo selezionabili solo i sottoprocessi
                if (object.type == "bpmn:SubProcess") {

                    dom_obj.css('cursor', 'pointer');

                    dom_obj.hover(function() {
                        canvas.toggleMarker(id, 'highlights-shape');
                    }, function() {
                        canvas.toggleMarker(id, 'highlights-shape');
                    });

                }

                //console.log(object);

                applicaStileShape(object);

                dom_obj.click(function() {

                });

            }

        });

        setArrayListaElementiById(lista_elementi);

        applicaStileAggiuntivoShape(lista_elementi);

        bpmn_inizializzato = true;

        getDatiRilevazioneProcesso(elemento);

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

function evidenziaTask(valore) {

    //console.table(elemento_by_id);

    jQuery.each(elementRegistry.getAll(), function(index, object) {

        //console.log(object.id);
        //console.log(jQuery.inArray(object.id, array_bpmn_id_elementi));

        if (jQuery.inArray(String(object.id), array_bpmn_id_elementi) != -1 && object.type != "bpmn:SubProcess" && object.type != "bpmn:StartEvent" && object.type != "bpmn:EndEvent") {

            if (elemento_by_id[object.id].valore_aggiunto == valore && valore != "") {

                canvas.toggleMarker(object.id, 'Evidenziate-shape');

            } else {

                canvas.removeMarker(object.id, 'Evidenziate-shape');

            }

        }

    });

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

function setProcessoSelezionatoDaAlbero(elemento) {

    //console.table(elemento);

    kproLayout.cells("b").setText("Flow Chart: " + elemento.nome);
    kproLayout.cells("b").setCollapsedText("Flow Chart: " + elemento.nome);

    kproLayout.cells("c").setText("Rilevazione Rischi: " + elemento.nome);
    kproLayout.cells("c").setCollapsedText("Rilevazione Rischi: " + elemento.nome);

    kproLayout.cells("d").setText("Dettagli: " + elemento.nome);
    kproLayout.cells("d").setCollapsedText("Dettagli: " + elemento.nome);

    getBPMNProcesso(elemento, true, false);

}

function setSelezioneShape(id) {

    jQuery.each(elementRegistry.getAll(), function(index, object) {

        canvas.removeMarker(object.id, 'selected-shape');

    });

    canvas.toggleMarker(id, 'selected-shape');

}

function apriProcessoDaBPMN(elemento) {

    elemento_selezionato.id = 0;
    elemento_selezionato.bpmn_id = elemento.id;
    elemento_selezionato.nome = HtmlEntities.decode(elemento.businessObject.name);
    elemento_selezionato.descrizione = "";

    setSelezioneShape(elemento_selezionato.bpmn_id);

    kproLayout.cells("b").setText("Flow Chart: " + elemento_selezionato.nome);
    kproLayout.cells("b").setCollapsedText("Flow Chart: " + elemento_selezionato.nome);

    kproLayout.cells("c").setText("Rilevazione Rischi: " + elemento_selezionato.nome);
    kproLayout.cells("c").setCollapsedText("Rilevazione Rischi: " + elemento_selezionato.nome);

    kproLayout.cells("d").setText("Dettagli: " + elemento_selezionato.nome);
    kproLayout.cells("d").setCollapsedText("Dettagli: " + elemento_selezionato.nome);

    getBPMNProcesso(elemento_selezionato, false, true);

}

function getBPMNProcesso(elemento, da_albero, apri) {

    var dati = {
        processo: processo_aperto.id,
        elemento_bpmn_id: elemento.bpmn_id
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischiQualita/php/getElemento.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {

        },
        success: function(data) {

            //console.table(data);

            if (apri) {

                array_processi_padri.push(processo_aperto.id);

                var elemento_id_albero_temp = getPadreByArray(array_processi_padri) + "_" + data[0].relazionato_a_id;
                //console.log("getTemplateProcesso: " + elemento_id_albero_temp);

                mProTree.selectItem(elemento_id_albero_temp);

                processi_navigati.push(elemento_id_albero_temp);

                var elemento_temp = {
                    id: data[0].relazionato_a_id
                };

                elemento_selezionato.id = data[0].id;

                getDatiRilevazioneProcesso(elemento_selezionato);
                getBPMNxml(elemento_temp);

            } else {

        

            }

        },
        fail: function() {

        }
    });

}

function getDatiRilevazioneProcesso(elemento) {

    var dati = {
        processo: elemento.id,
        record: record
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischiQualita/php/getDatiRilevazioneProcesso.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {

        },
        success: function(data) {

            //console.table(data);

            jrilevazioneContainer.empty();
            jdettagliContainer.empty();
            jrilevazioneContainer.append(data["table"]);

            var immagine_freccia = "<img id='gif_freccia' src='Smarty/templates/SproCore/KpRilevazioneRischiQualita/img/arrow_right.gif' style='position: absolute; right: 0px; top: 50px; display: none;' height='100' width='200'></img>";

            jrilevazioneContainer.append(immagine_freccia);

            jtabella_rilevazione = jQuery(".tabella_rilevazione");
            jtd_pericolo = jQuery(".td_pericolo");
            jgif_freccia = jQuery("#gif_freccia");
            jtr_rischio = jQuery(".tr_rischio");

            jQuery(".tr_rischio > .td_pericolo").removeClass("pericolo_selezionato");

            reSize();

            jrilevazioneContainer.animate({
                scrollTop: 0
            },'slow');

            if (data["records"].length > 0) {

                var temp_pericoli_attivita = data["records"];
                lista_pericoli_attivita = [];

                for( var i = 0; i < temp_pericoli_attivita.length; i++ ){

                    //console.log("Attivita: " + temp_pericoli_attivita[i].attivita);

                    if( lista_pericoli_attivita[ temp_pericoli_attivita[i].attivita ] ){

                        //console.log("Pericolo Precedente: " + lista_pericoli_attivita[ temp_pericoli_attivita[i].attivita ].rischio);
                        
                    }

                    if( lista_pericoli_attivita[ temp_pericoli_attivita[i].attivita ] && lista_pericoli_attivita[ temp_pericoli_attivita[i].attivita ].rischio != 0 ){

                    }
                    else{
                        lista_pericoli_attivita[ temp_pericoli_attivita[i].attivita ] = {
                            rischio: temp_pericoli_attivita[i].rischio
                        };
                    }

                    //console.log("Pericolo Attuale: " + lista_pericoli_attivita[ temp_pericoli_attivita[i].attivita ].rischio);

                }

                //console.table(lista_pericoli_attivita);

                var primo_rischio = data["records"][0];
                //console.log(primo_pericolo);

                if(primo_rischio["attivita"] != 0 && primo_rischio["attivita"] != "" && primo_rischio["rischio"] != 0 && primo_rischio["rischio"] != ""){
                    getTemplateRischio(primo_rischio["attivita"], primo_rischio["rischio"]);
                }
                else{
                    jdettagliContainer.empty();
                }

            } else {

                jdettagliContainer.empty();

            }

            jtr_rischio.click(function() {

                var temp = jQuery(this).attr("id");

                var temp_res = temp.split("_");

                var attivita_sel = temp_res[0];
                var rischio_sel = temp_res[1];

                //console.log("Attivita: " + attivita_sel + ", Rischio: " + rischio_sel);
                
                if(attivita_sel != 0 && attivita_sel != "" && rischio_sel != 0 && rischio_sel != ""){
                    getTemplateRischio(attivita_sel, rischio_sel);
                }
                else{
                    jdettagliContainer.empty();
                }

            });

        },
        fail: function() {

        }
    });

}

function getPadreByArray(array){

    padre = array.join('_');

    return padre;

}

function closeAllRoots(escludi){
    
    var rootsAr = mProTree.getSubItems(0).split(",");
    
    chiusuraRicorsivaNodiAlbero(rootsAr, escludi);
}

function chiusuraRicorsivaNodiAlbero(items, escludi){

    //console.log("chiusuraRicorsivaNodiAlbero: " + items);
    //console.log("chiusuraRicorsivaNodiAlbero: escludi " + escludi);

    var figli = [];
    var item_percorso = [];

    for (var i = 0; i < items.length; i++){

        if(items[i] != escludi && !escludi.startsWith(items[i]) ){

            item_percorso = items[i].split("_");

            if(item_percorso[0] != "tp"){   //In questo modo non chiudo il primissimo livello
                //console.log("chiusuraRicorsivaNodiAlbero: Chiusura Item " + items[i] + " " + mProTree.getItemText(items[i]));
                mProTree.closeAllItems(items[i])
            }

            figli = mProTree.getSubItems(items[i]);

            if( figli != "" ){

                figli = figli.split(",");
                chiusuraRicorsivaNodiAlbero(figli, escludi);

            }

        }

    }

}

function getTemplateRischio(attivita, rischio){

    jQuery.get("Smarty/templates/SproCore/KpRilevazioneRischiQualita/templates/dettagli_rischio.html", function(data) {

        //console.table(data);

        shape_selezionata.id = attivita;
        shape_selezionata.bpmn_id = lista_element_id[attivita].bpmn_id;

        setSelezioneShape(shape_selezionata.bpmn_id);

        jQuery(".tr_rischio > .td_pericolo").removeClass("pericolo_selezionato");

        jQuery("#" + attivita + "_" + rischio + " > .td_pericolo").addClass("pericolo_selezionato");

        startBlinKArrow();

        elemento_selezionato_rilevazione = {};

        elemento_selezionato_rilevazione = {
            attivita: attivita,
            rischio: rischio
        };

        jdettagliContainer.empty();
        jdettagliContainer.append(data);

        if (readonly) {
            setFormReaonly();
        } else {
            unsetFormReaonly();
        }

        jreadonly_nome_pericolo = jQuery("#readonly_nome_pericolo");
        jform_probabilita_pericolo = jQuery("#form_probabilita_pericolo");
        jform_magnitudo_pericolo = jQuery("#form_magnitudo_pericolo");
        jreadonly_rischio_pericolo = jQuery("#readonly_rischio_pericolo");
        jform_misurazione_rischio = jQuery(".form_misurazione_rischio");
        jform_check_rischio_rilevato = jQuery("#form_check_rischio_rilevato");
        jbody_tabella_ruoli_pericolo = jQuery("#body_tabella_ruoli_pericolo");
        jbody_tabella_misure = jQuery("#body_tabella_misure");
        jbottone_aggiungi_misura = jQuery("#bottone_aggiungi_misura");
        jhelp_probabilita = jQuery("#help_probabilita");
        jhelp_magnitudo = jQuery("#help_magnitudo");
        jlabel_misura_associata_pericolo = jQuery("#label_misura_associata_pericolo");
        jtab_ruoli = jQuery("#tab_ruoli");
        jtab_misure = jQuery("#tab_misure");

        jreadonly_nome_pericolo.val("");
        jform_probabilita_pericolo.val("");
        jform_magnitudo_pericolo.val("");
        jreadonly_rischio_pericolo.val("");
        jform_check_rischio_rilevato.prop("checked", false);
        jform_misurazione_rischio.hide();
        jbody_tabella_ruoli_pericolo.empty();
        jbody_tabella_misure.empty();

        getDatiRischio(attivita, rischio);

        jform_check_rischio_rilevato.change(function() {

            if (jQuery(this).prop("checked")) {
                jform_misurazione_rischio.show();
                jtab_ruoli.show();
                jtab_misure.show();

            } else {
                jform_misurazione_rischio.hide();
                jtab_ruoli.hide();
                jtab_misure.hide();
            }

            jQuery("#" + elemento_selezionato_rilevazione.attivita + "_" + elemento_selezionato_rilevazione.rischio + " .td_attivo").prop("checked", jQuery(this).prop("checked"));

            setRigaRilevazioneRischio();

        });

        jform_probabilita_pericolo.change(function() {

            var probabilita_pericolo_temp = jQuery("#form_probabilita_pericolo option:selected").text();

            jQuery("#" + elemento_selezionato_rilevazione.attivita + "_" + elemento_selezionato_rilevazione.rischio + " .td_probabilita").html(probabilita_pericolo_temp);

            setValuePickPericolo();

        });

        jform_magnitudo_pericolo.change(function() {

            var magnitudo_pericolo_temp = jQuery("#form_magnitudo_pericolo option:selected").text();

            jQuery("#" + elemento_selezionato_rilevazione.attivita + "_" + elemento_selezionato_rilevazione.rischio + " .td_magnitudo").html(magnitudo_pericolo_temp);

            setValuePickPericolo();

        });

        jbottone_aggiungi_misura.click(function() {

            apriPopupAggiuntaMisuraMigliorativa();

        });

        jhelp_probabilita.click(function() {

            apriPopupProbabilita();

        });

        jhelp_magnitudo.click(function() {

            apriPopupMagnitudo();

        });

    });

}

function setFormReaonly() {

    readonly = true;
    var jform = jQuery('[id*="form_"]');
    jform.prop("disabled", true);
    jform.addClass("disabled");

}

function unsetFormReaonly() {

    readonly = false;
    var jform = jQuery('[id*="form_"]');
    jform.prop("disabled", false);
    jform.removeClass("disabled");

}

function sbloccaForm() {

    unsetFormReaonly();

    jbottone_modifica.hide();
    jbottone_salva.show();

}

function bloccaForm() {

    setFormReaonly();

    jbottone_modifica.show();
    jbottone_salva.hide();

}

function getDatiRischio(attivita, rischio) {

    var dati = {
        record: record,
        attivita: attivita,
        rischio: rischio
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischiQualita/php/getRischio.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {

        },
        success: function(data) {
            //console.log(data);

            if (data["nome"] != "") {
                elemento_selezionato_rilevazione.nome_rischio = HtmlEntities.decode(data["nome"]);
            } else {
                elemento_selezionato_rilevazione.nome_rischio = "";
            }

            if (data["nome_attivita"] != "") {
                elemento_selezionato_rilevazione.nome_attivita= HtmlEntities.decode(data["nome_attivita"]);
            } else {
                elemento_selezionato_rilevazione.nome_attivita = "";
            }

            kproLayout.cells("d").setText("Dettagli: " + elemento_selezionato_rilevazione.nome_attivita);
            kproLayout.cells("d").setCollapsedText("Dettagli: " + elemento_selezionato_rilevazione.nome_attivita);

            jreadonly_nome_pericolo.val(elemento_selezionato_rilevazione.nome_rischio);

            var probabilita_pericolo_temp = data["probabilita"];
            probabilita_pericolo_temp = probabilita_pericolo_temp.split("-");

            var magnitudo_pericolo_temp = data["magnitudo"];
            magnitudo_pericolo_temp = magnitudo_pericolo_temp.split("-");

            jform_probabilita_pericolo.val(probabilita_pericolo_temp[0].trim());
            jform_magnitudo_pericolo.val(magnitudo_pericolo_temp[0].trim());
            jreadonly_rischio_pericolo.val(data["rischio"]);

            if (data["check"] === true) {
                jform_check_rischio_rilevato.prop("checked", true);
                jform_misurazione_rischio.show();
                jtab_ruoli.show();
                jtab_misure.show();
            } else {
                jform_check_rischio_rilevato.prop("checked", false);
                jform_misurazione_rischio.hide();
                jtab_ruoli.hide();
                jtab_misure.hide();
            }

        },
        fail: function() {

            console.error("Errore rischio!");

            //location.reload();

        }
    });

}

function setValuePickPericolo() {

    var probabilita_temp = jform_probabilita_pericolo.val();
    var magnitudo_temp = jform_magnitudo_pericolo.val();
    var rischio_temp = "";

    if (probabilita_temp != "" && magnitudo_temp != "") {

        rischio_temp = parseInt(probabilita_temp) * parseInt(magnitudo_temp);
        rischio_temp = rischio_temp.toString();

    }

    jreadonly_rischio_pericolo.val(rischio_temp);

    var rischio_pericolo_temp = jQuery("#readonly_rischio_pericolo option:selected").text();

    jQuery("#" + elemento_selezionato_rilevazione.attivita + "_" + elemento_selezionato_rilevazione.rischio + " .td_rischio").html(rischio_pericolo_temp);

    setRigaRilevazioneRischio();

}

function setRigaRilevazioneRischio() {

    var attivo_temp = '0';

    if (jform_check_rischio_rilevato.prop("checked")) {
        attivo_temp = '1';
    }

    var dati = {
        record: record,
        attivita: elemento_selezionato_rilevazione.attivita,
        rischio_id: elemento_selezionato_rilevazione.rischio,
        attivo: attivo_temp,
        probabilita: jform_probabilita_pericolo.val(),
        magnitudo: jform_magnitudo_pericolo.val(),
        rischio: jreadonly_rischio_pericolo.val()
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischiQualita/php/setRigaRilevazioneRischio.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {


        },
        success: function(data) {
            //console.log(data);


        },
        fail: function() {

            console.error("Errore scrittura!");

            //location.reload();

        }
    });

}

function tabSelezionato(tab) {

    switch (tab) {
        case "misure_migliorative":
            getMisureMigliorativeRischio();
            break;
    }

}

function getMisureMigliorativeRischio() {

    var dati = {
        record: record,
        attivita: elemento_selezionato_rilevazione.attivita,
        rischio: elemento_selezionato_rilevazione.rischio
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischiQualita/php/getMisureMigliorativeRischio.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {


        },
        success: function(data) {
            //console.log(data);

            setTabellaMisureMigliorative(data);

        },
        fail: function() {

            console.error("Errore!");

            //location.reload();

        }
    });

}

function setTabellaMisureMigliorative(lista_misure) {

    var temp = "";

    if (lista_misure.length > 0) {

        for (var i = 0; i < lista_misure.length; i++) {

            temp += "<tr>";
            temp += "<td><span>" + lista_misure[i]["nome"] + "</span></td>";
            temp += "<td><span>" + lista_misure[i]["eseguire_entro"] + "</span></td>";
            temp += "</tr>";

        }

    } else {
        temp += "<tr>";
        temp += "<td colspan='5' style='text-align: center;'><em>Nessuna misura trovata!</em></td";
        temp += "</tr>";
    }

    jbody_tabella_misure.empty();
    jbody_tabella_misure.append(temp);

}

function apriPopupAggiuntaMisuraMigliorativa(){

    jpopup_generico.empty();

    jQuery.get("Smarty/templates/SproCore/KpRilevazioneRischiQualita/templates/popup_misura_migliorativa.html", function(data) {

        jpopup_generico.html(data);

        jreadonly_misura_relazionato_a = jQuery("#readonly_misura_relazionato_a");
        jreadonly_misura_nome_pericolo = jQuery("#readonly_misura_nome_pericolo");
        jform_misura_tipo_misura = jQuery("#form_misura_tipo_misura");
        jform_misura_nome_intervento = jQuery("#form_misura_nome_intervento");
        jform_misura_adottare_entro = jQuery("#form_misura_adottare_entro");
        jform_misura_nota = jQuery("#form_misura_nota");
        jbottone_salva_misura_riduzione = jQuery("#bottone_salva_misura_riduzione");
        jform_misura_riduzione_probabilita = jQuery("#form_misura_riduzione_probabilita");
        jform_misura_riduzione_magnitudo = jQuery("#form_misura_riduzione_magnitudo");

        popolaPicking(jform_misura_tipo_misura, "kp_tipo_misura_migl", "");

        jreadonly_misura_relazionato_a.val(elemento_selezionato_rilevazione.nome_attivita);
        jreadonly_misura_nome_pericolo.val(elemento_selezionato_rilevazione.nome_rischio);

        jform_misura_nome_intervento.val("");
        jform_misura_adottare_entro.val("");
        jform_misura_nota.val("");
        jform_misura_riduzione_probabilita.val("");
        jform_misura_riduzione_magnitudo.val("");

        inizializzazioneBootstrap();

        jpopup_generico.modal({ backdrop: "static" });

        jform_misura_nome_intervento.focus();

        jbottone_salva_misura_riduzione.click(function() {

            setMisuraRiduzioneRischio();

        });

        jform_misura_tipo_misura.change(function(){

            if( jform_misura_nome_intervento.val() == "" ){
                jform_misura_nome_intervento.val( jform_misura_tipo_misura.val() );
            }
            else{

                dhtmlx.confirm({
                    type: "confirm",
                    text: "E' stato variato il tipo misura; modificare il nome intervento?",
                    callback: function(result) {
                        
                        if(result){
            
                            jform_misura_nome_intervento.val( jform_misura_tipo_misura.val() );
                        
                        }
            
                    }
                });

            }

        });

    });

}

function setMisuraRiduzioneRischio() {

    var nome_intervento_temp = HtmlEntities.kpencode(jform_misura_nome_intervento.val());

    if (nome_intervento_temp == "") {
        nome_intervento_temp = tipo_misura_riduttiva_selezionata.nome;
    }

    var dati = {
        record: record,
        attivita: elemento_selezionato_rilevazione.attivita,
        rischio: elemento_selezionato_rilevazione.rischio,
        nome_intervento: HtmlEntities.kpencode(jform_misura_nome_intervento.val()),
        tipo_misura: HtmlEntities.kpencode(jform_misura_tipo_misura.val()),
        adottare_entro: normalizzaData(jform_misura_adottare_entro.val()),
        nota: HtmlEntities.kpencode(jform_misura_nota.val()),
        riduzione_probabilita: jform_misura_riduzione_probabilita.val(),
        riduzione_magnitudo: jform_misura_riduzione_magnitudo.val()
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischiQualita/php/setMisuraMigliorativaRischio.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {


        },
        success: function(data) {
            //console.log(data);

            getMisureMigliorativeRischio();

            jpopup_generico.modal("hide");

        },
        fail: function() {

            console.error("Errore!");

            //location.reload();

        }
    });

}

function popolaPicking(campo, nome_campo_crm, valore_default) {

    var filtro = { nome_campo: nome_campo_crm };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischiQualita/php/pickingList.php',
        dataType: 'json',
        async: true,
        data: filtro,
        success: function(data) {

            //console.table(data);
            var lista = "";
            for (var i = 0; i < data.length; i++) {
                if (data[i].valore === valore_default) {
                    lista += "<option value='" + data[i].valore + "' selected='selected'>" + data[i].valore + "</option>";
                } else {
                    lista += "<option value='" + data[i].valore + "'>" + data[i].valore + "</option>";
                }
            }
            campo.empty();
            campo.append(lista);

        },
        fail: function() {
            console.error("Errore nel caricamento della picking list: " + nome_campo_crm);
        }
    });

}

function startBlinKArrow(){

    jgif_freccia.show();

    changeStatusArrow = window.setTimeout("stopBlinKArrow()", time_blink);

}

function stopBlinKArrow(){

    jgif_freccia.hide();

}

function setProcessoSelezionato(elemento, click_su_vuoto) {

    /*shape_selezionata.id = 0;
    shape_selezionata.bpmn_id = elemento.id;

    setSelezioneShape(shape_selezionata.bpmn_id);*/

}

function setTaskSelezionata(elemento) {

    shape_selezionata.id = lista_element_bpmnid[elemento.id].id;
    shape_selezionata.bpmn_id = elemento.id;

    getTemplateRischio( shape_selezionata.id, lista_pericoli_attivita[shape_selezionata.id].rischio );

}

function setSelezioneShape(id) {

    if( bpmn_inizializzato ){

        jQuery.each(elementRegistry.getAll(), function(index, object) {

            canvas.removeMarker(object.id, 'selected-shape');

        });

        canvas.toggleMarker(id, 'selected-shape');

    }

}