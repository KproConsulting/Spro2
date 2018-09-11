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
var altezza_layoutObj;
var readonly = true;

var elemento_selezionato = {};
var tipo_misura_riduttiva_selezionata = {};

var lista_tipi_misure = [];

var jrilevazioneContainer;
var jdettagliContainer;
var jbottone_modifica;
var jbottone_salva;
var jtr_pericolo;
var jreadonly_nome_pericolo;
var jform_probabilita_pericolo;
var jform_magnitudo_pericolo;
var jreadonly_rischio_pericolo;
var jform_misurazione_rischio;
var jform_check_pericolo_area;
var jbody_tabella_ruoli_pericolo;
var jbody_tabella_misure;
var jbottone_aggiungi_misura;
var jpopup_generico;
var jbody_tabella_lista_tipi_misura;
var jbottone_selezionatipo_misura;
var jsearch_tipo_misura;
var jsearch_categoria_misura;
var jreadonly_misura_relazionato_a;
var jreadonly_misura_nome_pericolo;
var jreadonly_misura_tipo_misura;
var jreadonly_misura_categoria_misura;
var jform_misura_nome_intervento;
var jform_misura_adottare_entro;
var jform_misura_nota;
var jbottone_rel_seleziona_tipo_misura;
var jbottone_salva_misura_riduzione;
var jform_misura_riduzione_probabilita;
var jform_misura_riduzione_magnitudo;
var jdiv_pericolo_con_misura;
var jform_misura_associata_pericolo;
var jhelp_probabilita;
var jhelp_magnitudo;
var jlabel_misura_associata_pericolo;
var jbody_help;
var jform_check_pericolo_ruolo;
var jtab_ruoli;
var jtab_misure;
var jbottone_rischi;
var jtabella_rilevazione;
var jbottone_pdf;
var jbottone_excel;
var jform_descrizione_pericolo;
var jgif_freccia;

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
    jbottone_excel = jQuery("#bottone_excel");

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

        getPDF();

    });

    jbottone_excel.click(function() {

        getExcel();

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

    if (jrilevazioneContainer) {
        jrilevazioneContainer.height(altezza_layoutObj - 50);
        jdettagliContainer.height(altezza_layoutObj - 120);

        jrilevazioneContainer.css("overflow-y", "scroll");
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

function inizializzazioneLayoutKp() {

    window.dhx4.skin = 'material';

    kproLayout = new dhtmlXLayoutObject({
        parent: "layoutObj",
        pattern: "2U",
        skin: "material"
    });

    kproLayout.setOffsets({
        top: 0,
        right: 0,
        bottom: 0,
        left: 0
    });

    kproLayout.cells("a").setText("Rilevazione Rischi");
    kproLayout.cells("a").setCollapsedText("Rilevazione Rischi");

    kproLayout.cells("b").setText("Dettagli");
    kproLayout.cells("b").setCollapsedText("Dettagli");
    kproLayout.cells("b").setWidth(400);

    kproLayout.cells("a").attachObject("rilevazioneContainer");

    kproLayout.cells("b").attachObject("dettagliContainer");

    inizializzazioneExtra();

    reSize();

    kproLayout.attachEvent("onResizeFinish", function() {
       
    });

    kproLayout.attachEvent("onPanelResizeFinish", function(names) {
        
    });

    kproLayout.attachEvent("onCollapse", function(name) {
        reSize();
    });

    kproLayout.attachEvent("onExpand", function(name) {
        reSize();
    });

}

function inizializzazioneExtra() {

    jrilevazioneContainer = jQuery("#rilevazioneContainer");
    jdettagliContainer = jQuery("#dettagliContainer");
    jbottone_rischi = jQuery(".bottone_rischi");

    selectTipoRilevazione("Area");

    jbottone_rischi.click(function() {

        var selezionato_temp = jQuery(this).attr("id");
        selezionato_temp = selezionato_temp.substring(15, selezionato_temp.length);

        selectTipoRilevazione(selezionato_temp);

    });

}

function getTemplate(modulo) {

    var dati = {
        record: record,
        modulo: modulo
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischi/php/getTemplate.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {


        },
        success: function(data) {
            //console.log(data);

            jrilevazioneContainer.empty();
            jdettagliContainer.empty();
            jrilevazioneContainer.append(data["table"]);

            var immagine_freccia = "<img id='gif_freccia' src='Smarty/templates/SproCore/KpRilevazioneRischi/img/arrow_right.gif' style='position: absolute; right: 0px; top: 100px; display: none;' height='100' width='200'></img>";

            jrilevazioneContainer.append(immagine_freccia);

            jtabella_rilevazione = jQuery(".tabella_rilevazione");
            jgif_freccia = jQuery("#gif_freccia");
            jtr_pericolo = jQuery(".tr_pericolo");

            reSize();

            if (data["records"].length > 0) {

                var primo_pericolo = data["records"][0];
                //console.log(primo_pericolo);

                getTemplatePericolo(primo_pericolo["related_to"], primo_pericolo["pericolo_id"]);

            } else {

                getTemplatePericolo("", "");

            }

            jtr_pericolo.click(function() {

                var temp = jQuery(this).attr("id");

                var temp_res = temp.split("_");

                var related_to_sel = temp_res[0];
                var pericolo_sel = temp_res[1];

                //console.log("Related to: " + related_to_sel + ", Pericolo: " + pericolo_sel);

                getTemplatePericolo(related_to_sel, pericolo_sel);

            });

        },
        fail: function() {

            console.error("Errore template!");

            //location.reload();

        }
    });

}

function getTemplatePericolo(related_to, pericolo) {

    if (jgif_freccia.is(':visible')) {
        jgif_freccia.hide();
    }

    jQuery(".tr_pericolo > .td_pericolo").removeClass("pericolo_selezionato");

    jQuery("#" + related_to + "_" + pericolo + " > .td_pericolo").addClass("pericolo_selezionato");

    jQuery.get("Smarty/templates/SproCore/KpRilevazioneRischi/templates/dettagli_pericolo.html", function(data) {

        //console.table(data);

        elemento_selezionato = {};

        elemento_selezionato = {
            related_to: related_to,
            pericolo: pericolo
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
        jform_check_pericolo_area = jQuery("#form_check_pericolo_area");
        jbody_tabella_ruoli_pericolo = jQuery("#body_tabella_ruoli_pericolo");
        jbody_tabella_misure = jQuery("#body_tabella_misure");
        jbottone_aggiungi_misura = jQuery("#bottone_aggiungi_misura");
        jdiv_pericolo_con_misura = jQuery("#div_pericolo_con_misura");
        jform_misura_associata_pericolo = jQuery("#form_misura_associata_pericolo");
        jhelp_probabilita = jQuery("#help_probabilita");
        jhelp_magnitudo = jQuery("#help_magnitudo");
        jlabel_misura_associata_pericolo = jQuery("#label_misura_associata_pericolo");
        jtab_ruoli = jQuery("#tab_ruoli");
        jtab_misure = jQuery("#tab_misure");
        jform_descrizione_pericolo = jQuery("#form_descrizione_pericolo");

        jreadonly_nome_pericolo.val("");
        jform_probabilita_pericolo.val("");
        jform_magnitudo_pericolo.val("");
        jreadonly_rischio_pericolo.val("");
        jform_misura_associata_pericolo.val(0);
        jdiv_pericolo_con_misura.hide();
        jform_check_pericolo_area.prop("checked", false);
        jform_misurazione_rischio.hide();
        jbody_tabella_ruoli_pericolo.empty();
        jbody_tabella_misure.empty();
        jlabel_misura_associata_pericolo.html("Misurazione");
        jform_descrizione_pericolo.val("");

        getDatiPericolo(related_to, pericolo);

        startBlinKArrow();

        jform_check_pericolo_area.change(function() {

            if (jQuery(this).prop("checked")) {

                if( elemento_selezionato.probabilita_pericolo == '' ){
                    elemento_selezionato.probabilita_pericolo = "1";
                    jform_probabilita_pericolo.val(elemento_selezionato.probabilita_pericolo);
                    var probabilita_pericolo_temp = jQuery("#form_probabilita_pericolo option:selected").text();
                    jQuery("#" + elemento_selezionato.related_to + "_" + elemento_selezionato.pericolo + " .td_probabilita").html(probabilita_pericolo_temp);
                    setValuePickPericolo();
                }

                if( elemento_selezionato.magnitudo_pericolo == '' ){
                    elemento_selezionato.magnitudo_pericolo = "1";
                    jform_magnitudo_pericolo.val(elemento_selezionato.magnitudo_pericolo);
                    var magnitudo_pericolo_temp = jQuery("#form_magnitudo_pericolo option:selected").text();
                    jQuery("#" + elemento_selezionato.related_to + "_" + elemento_selezionato.pericolo + " .td_magnitudo").html(magnitudo_pericolo_temp);
                    setValuePickPericolo();
                }

                jform_misurazione_rischio.show();
                jtab_ruoli.show();
                jtab_misure.show();

                if (elemento_selezionato.soggeto_a_misura) {
                    jdiv_pericolo_con_misura.show();
                } else {
                    jdiv_pericolo_con_misura.hide();
                }

            } else {
                jform_misurazione_rischio.hide();
                jdiv_pericolo_con_misura.hide();
                jtab_ruoli.hide();
                jtab_misure.hide();
            }

            jQuery("#" + elemento_selezionato.related_to + "_" + elemento_selezionato.pericolo + " .td_attivo").prop("checked", jQuery(this).prop("checked"));

            setRigaRilevazioneRischio();

        });

        jform_probabilita_pericolo.change(function() {

            var probabilita_pericolo_temp = jQuery("#form_probabilita_pericolo option:selected").text();

            jQuery("#" + elemento_selezionato.related_to + "_" + elemento_selezionato.pericolo + " .td_probabilita").html(probabilita_pericolo_temp);

            setValuePickPericolo();

        });

        jform_magnitudo_pericolo.change(function() {

            var magnitudo_pericolo_temp = jQuery("#form_magnitudo_pericolo option:selected").text();

            jQuery("#" + elemento_selezionato.related_to + "_" + elemento_selezionato.pericolo + " .td_magnitudo").html(magnitudo_pericolo_temp);

            setValuePickPericolo();

        });

        jform_misura_associata_pericolo.change(function() {

            setRigaRilevazioneRischio();

        });

        jbottone_aggiungi_misura.click(function() {

            apriPopupSelezioneTipoMisuraRiduzione();

        });

        jhelp_probabilita.click(function() {

            apriPopupProbabilita();

        });

        jhelp_magnitudo.click(function() {

            apriPopupMagnitudo();

        });

        jform_descrizione_pericolo.focusout(function() {

            setRigaRilevazioneRischio();

        });

    });

}

function getDatiPericolo(related_to, pericolo) {

    var dati = {
        record: record,
        related_to: related_to,
        pericolo: pericolo
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischi/php/getPericolo.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {

        },
        success: function(data) {
            //console.log(data);

            if (data["nome"] != "") {
                elemento_selezionato.nome_pericolo = HtmlEntities.decode(data["nome"]);
            } else {
                elemento_selezionato.nome_pericolo = "";
            }

            if (data["nome_related_to"] != "") {
                elemento_selezionato.nome_related_to = HtmlEntities.decode(data["nome_related_to"]);
            } else {
                elemento_selezionato.nome_related_to = "";
            }

            if (data["help_probabilita"] != "") {
                elemento_selezionato.help_probabilita = HtmlEntities.decode(data["help_probabilita"]);
                jhelp_probabilita.show();
            } else {
                elemento_selezionato.help_probabilita = "";
                jhelp_probabilita.hide();
            }

            if (data["help_magnitudo"] != "") {
                elemento_selezionato.help_magnitudo = HtmlEntities.decode(data["help_magnitudo"]);
                jhelp_magnitudo.show();
            } else {
                elemento_selezionato.help_magnitudo = "";
                jhelp_magnitudo.hide();
            }

            if (data["nome_misurazione"] != "") {
                elemento_selezionato.nome_misurazione = HtmlEntities.decode(data["nome_misurazione"]);
            } else {
                elemento_selezionato.nome_misurazione = "";
            }

            if (data["descrizione"] != "") {
                elemento_selezionato.descrizione = HtmlEntities.decode(data["descrizione"]);
            } else {
                elemento_selezionato.descrizione = "";
            }

            elemento_selezionato.soggeto_a_misura = data["soggeto_a_misura"];

            kproLayout.cells("b").setText("Dettagli: " + elemento_selezionato.nome_pericolo);
            kproLayout.cells("b").setCollapsedText("Dettagli: " + elemento_selezionato.nome_pericolo);

            jreadonly_nome_pericolo.val(elemento_selezionato.nome_pericolo);

            elemento_selezionato.probabilita_pericolo = "";
            var probabilita_pericolo_temp = data["probabilita"];
            if( probabilita_pericolo_temp != "" ){
                probabilita_pericolo_temp = probabilita_pericolo_temp.split("-");
                if( probabilita_pericolo_temp.length > 0 ){
                    elemento_selezionato.probabilita_pericolo = probabilita_pericolo_temp[0].trim();
                }
            }

            elemento_selezionato.magnitudo_pericolo = "";
            var magnitudo_pericolo_temp = data["magnitudo"];
            if( magnitudo_pericolo_temp != "" ){
                magnitudo_pericolo_temp = magnitudo_pericolo_temp.split("-");
                if( magnitudo_pericolo_temp.length > 0 ){
                    elemento_selezionato.magnitudo_pericolo = magnitudo_pericolo_temp[0].trim();
                }
            }

            if( elemento_selezionato.probabilita_pericolo != "" ){
                jform_probabilita_pericolo.val(elemento_selezionato.probabilita_pericolo);
            }
            else{
                jform_probabilita_pericolo.val("1");
            }

            if( elemento_selezionato.magnitudo_pericolo != "" ){
                jform_magnitudo_pericolo.val(elemento_selezionato.magnitudo_pericolo);
            }
            else{
                jform_magnitudo_pericolo.val("1");
            }
            
            jreadonly_rischio_pericolo.val(data["rischio"]);

            jform_descrizione_pericolo.val(elemento_selezionato.descrizione);

            if (data["check"] === true) {
                jform_check_pericolo_area.prop("checked", true);
                jform_misurazione_rischio.show();
                jdiv_pericolo_con_misura.show();
                jtab_ruoli.show();
                jtab_misure.show();
            } else {
                jform_check_pericolo_area.prop("checked", false);
                jform_misurazione_rischio.hide();
                jdiv_pericolo_con_misura.hide();
                jtab_ruoli.hide();
                jtab_misure.hide();
            }

            if (data["soggeto_a_misura"] === true) {

                if (data["check"] === true) {
                    jdiv_pericolo_con_misura.show();
                } else {
                    jdiv_pericolo_con_misura.hide();
                }

                jform_misura_associata_pericolo.val(data["misurazione"]);
                jlabel_misura_associata_pericolo.html(elemento_selezionato.nome_misurazione);
            } else {
                jdiv_pericolo_con_misura.hide();
            }

            if (data["lista_ruoli"].length > 0) {
                setTabellaRuoliPericolo(data["lista_ruoli"]);
            }

        },
        fail: function() {

            console.error("Errore pericolo!");

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

    jQuery("#" + elemento_selezionato.related_to + "_" + elemento_selezionato.pericolo + " .td_rischio").html(rischio_pericolo_temp);

    setRigaRilevazioneRischio();

}

function setTabellaRuoliPericolo(lista_ruoli) {

    var temp = "";

    for (var i = 0; i < lista_ruoli.length; i++) {

        temp += "<tr>";
        temp += "<td>";

        temp += "<div class='checkbox' >";
        temp += "<label >";

        if (lista_ruoli[i].check) {
            temp += "<input id='form_pericolo_ruolo_" + lista_ruoli[i]["id"] + "' class='form_check_pericolo_ruolo' type='checkbox' checked >";
        } else {
            temp += "<input id='form_pericolo_ruolo_" + lista_ruoli[i]["id"] + "' class='form_check_pericolo_ruolo' type='checkbox' >";
        }

        temp += "<span style='margin-left: 10px;'><b>";
        temp += lista_ruoli[i].nome;
        temp += "</b></span ></label></div>";

        temp += "</td>";
        temp += "</tr>";

    }

    jbody_tabella_ruoli_pericolo.empty();
    jbody_tabella_ruoli_pericolo.append(temp);

    if (readonly) {
        setFormReaonly();
    } else {
        unsetFormReaonly();
    }

    jform_check_pericolo_ruolo = jQuery(".form_check_pericolo_ruolo");

    jform_check_pericolo_ruolo.change(function() {

        var selezionato_temp = jQuery(this).attr("id");
        selezionato_temp = selezionato_temp.substring(20, selezionato_temp.length);

        if (jQuery(this).prop("checked")) {

            //console.log("Link ruolo " + selezionato_temp);
            setLinkRuolo(selezionato_temp);

        } else {

            //console.log("Unlink ruolo " + selezionato_temp);
            unsetLinkRuolo(selezionato_temp);

        }

    });

}

function setRigaRilevazioneRischio() {

    var attivo_temp = '0';

    if (jform_check_pericolo_area.prop("checked")) {
        attivo_temp = '1';
    }

    var dati = {
        record: record,
        related_to: elemento_selezionato.related_to,
        pericolo: elemento_selezionato.pericolo,
        attivo: attivo_temp,
        probabilita: jform_probabilita_pericolo.val(),
        magnitudo: jform_magnitudo_pericolo.val(),
        rischio: jreadonly_rischio_pericolo.val(),
        misurazione: jform_misura_associata_pericolo.val(),
        descrizione: HtmlEntities.kpencode(jform_descrizione_pericolo.val())
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischi/php/setRigaRilevazioneRischio.php',
        dataType: 'json',
        async: true,
        method: 'POST',
        data: dati,
        beforeSend: function() {


        },
        success: function(data) {
            //console.log(data);

            if (data.check) {

                for (var i = 0; i < data["lista_ruoli"].length; i++) {

                    if (data["lista_ruoli"][i].check) {
                        jQuery("#pericolo_ruolo_" + elemento_selezionato.related_to + "_" + elemento_selezionato.pericolo + "_" + data["lista_ruoli"][i].id).prop("checked", true);
                    } else {
                        jQuery("#pericolo_ruolo_" + elemento_selezionato.related_to + "_" + elemento_selezionato.pericolo + "_" + data["lista_ruoli"][i].id).prop("checked", false);
                    }

                }

                setTabellaRuoliPericolo(data["lista_ruoli"]);

            } else {

                jQuery('[id^="pericolo_ruolo_' + elemento_selezionato.related_to + '_' + elemento_selezionato.pericolo + '_"]').prop("checked", false);

            }

        },
        fail: function() {

            console.error("Errore scrittura!");

            //location.reload();

        }
    });


}

function tabSelezionato(tab) {

    switch (tab) {
        case "misure_riduttive":
            getMisureRiduttivePericolo();
            break;
    }

}

function getMisureRiduttivePericolo() {

    var dati = {
        record: record,
        related_to: elemento_selezionato.related_to,
        pericolo: elemento_selezionato.pericolo
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischi/php/getMisureRiduttivePericolo.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {


        },
        success: function(data) {
            //console.log(data);

            setTabellaMisureRiduttivePericolo(data);

        },
        fail: function() {

            console.error("Errore!");

            //location.reload();

        }
    });

}

function setTabellaMisureRiduttivePericolo(lista_misure) {

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

function apriPopupAggiuntaMisuraRiduzione() {

    jpopup_generico.empty();

    jQuery.get("Smarty/templates/SproCore/KpRilevazioneRischi/templates/popup_misura_riduzione.html", function(data) {

        jpopup_generico.html(data);

        jreadonly_misura_relazionato_a = jQuery("#readonly_misura_relazionato_a");
        jreadonly_misura_nome_pericolo = jQuery("#readonly_misura_nome_pericolo");
        jreadonly_misura_tipo_misura = jQuery("#readonly_misura_tipo_misura");
        jreadonly_misura_categoria_misura = jQuery("#readonly_misura_categoria_misura");
        jform_misura_nome_intervento = jQuery("#form_misura_nome_intervento");
        jform_misura_adottare_entro = jQuery("#form_misura_adottare_entro");
        jform_misura_nota = jQuery("#form_misura_nota");
        jbottone_rel_seleziona_tipo_misura = jQuery("#bottone_rel_seleziona_tipo_misura");
        jbottone_salva_misura_riduzione = jQuery("#bottone_salva_misura_riduzione");
        jform_misura_riduzione_probabilita = jQuery("#form_misura_riduzione_probabilita");
        jform_misura_riduzione_magnitudo = jQuery("#form_misura_riduzione_magnitudo");

        jreadonly_misura_relazionato_a.val(elemento_selezionato.nome_related_to);
        jreadonly_misura_nome_pericolo.val(elemento_selezionato.nome_pericolo);
        jreadonly_misura_tipo_misura.val(tipo_misura_riduttiva_selezionata.nome);
        jreadonly_misura_categoria_misura.val(tipo_misura_riduttiva_selezionata.categoria_misura);

        jform_misura_nome_intervento.val(tipo_misura_riduttiva_selezionata.nome);
        jform_misura_adottare_entro.val("");
        jform_misura_nota.val("");
        jform_misura_riduzione_probabilita.val("");
        jform_misura_riduzione_magnitudo.val("");

        inizializzazioneBootstrap();

        jpopup_generico.modal({ backdrop: "static" });

        jbottone_rel_seleziona_tipo_misura.click(function() {

            apriPopupSelezioneTipoMisuraRiduzione();

        });

        jbottone_salva_misura_riduzione.click(function() {

            setMisuraRiduzioneRischio();

        });

    });

}

function apriPopupSelezioneTipoMisuraRiduzione() {

    tipo_misura_riduttiva_selezionata = {};
    jpopup_generico.empty();

    jQuery.get("Smarty/templates/SproCore/KpRilevazioneRischi/templates/popup_selezione_tipo_misura_riduzione.html", function(data) {

        jpopup_generico.html(data);

        jbody_tabella_lista_tipi_misura = jQuery("#body_tabella_lista_tipi_misura");
        jsearch_tipo_misura = jQuery("#search_tipo_misura");
        jsearch_categoria_misura = jQuery("#search_categoria_misura");

        var filtro_tipi_misure = {
            record: record,
            related_to: elemento_selezionato.related_to,
            pericolo: elemento_selezionato.pericolo,
            tipo_misura: "",
            categoria_misura: ""
        };

        getListaTipiMisureRiduttive(filtro_tipi_misure);

        jpopup_generico.modal({ backdrop: "static" });

        jsearch_categoria_misura.keyup(function(ev) {

            var categoria_misura_temp = jsearch_categoria_misura.val();

            var code = ev.which;
            if (code == 13 || categoria_misura_temp == "") {

                var filtro_tipi_misure = {};

                filtro_tipi_misure.record = record;
                filtro_tipi_misure.related_to = elemento_selezionato.related_to;
                filtro_tipi_misure.pericolo = elemento_selezionato.pericolo;
                filtro_tipi_misure.tipo_misura = jsearch_tipo_misura.val();
                filtro_tipi_misure.categoria_misura = categoria_misura_temp;
                getListaTipiMisureRiduttive(filtro_tipi_misure);

            }

        });

        jsearch_tipo_misura.keyup(function(ev) {

            var tipo_misura_temp = jsearch_tipo_misura.val();

            var code = ev.which;
            if (code == 13 || tipo_misura_temp == "") {

                var filtro_tipi_misure = {};

                filtro_tipi_misure.record = record;
                filtro_tipi_misure.related_to = elemento_selezionato.related_to;
                filtro_tipi_misure.pericolo = elemento_selezionato.pericolo;
                filtro_tipi_misure.tipo_misura = tipo_misura_temp;
                filtro_tipi_misure.categoria_misura = jsearch_categoria_misura.val();
                getListaTipiMisureRiduttive(filtro_tipi_misure);

            }

        });

    });

}

function getListaTipiMisureRiduttive(filtro) {

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischi/php/getListaTipiMisureRiduttive.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {


        },
        success: function(data) {
            //console.log(data);

            var temp = "";
            lista_tipi_misure = [];

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    temp += "<tr style='vertica-align: middle;'>";
                    temp += "<td style='text-align: center;'>";
                    temp += "<button id='sel_" + data[i].id + "' class='bottone_selezionatipo_misura mdl-button mdl-js-button mdl-button--icon'>";
                    temp += "<i class='material-icons'>add</i>";
                    temp += "</button>";
                    temp += "</td>";
                    temp += "<td style='padding-top: 15px;'>" + data[i].nome + "</td>";
                    temp += "<td style='padding-top: 15px;'>" + data[i].categoria_misura + "</td>";
                    temp += "</tr>";

                    lista_tipi_misure[data[i].id] = {
                        id: data[i].id,
                        nome: data[i].nome,
                        categoria_misura: data[i].categoria_misura
                    };

                }

            } else {
                temp += "<tr>";
                temp += "<td colspan='5' style='text-align: center;'><em>Nessun tipo misura riduttiva trovata!</em></td>";
                temp += "</tr>";
            }

            jbody_tabella_lista_tipi_misura.empty();
            jbody_tabella_lista_tipi_misura.append(temp);

            jbottone_selezionatipo_misura = jQuery(".bottone_selezionatipo_misura");

            jbottone_selezionatipo_misura.click(function() {

                var selezionato_temp = jQuery(this).attr("id");
                selezionato_temp = selezionato_temp.substring(4, selezionato_temp.length);

                tipo_misura_riduttiva_selezionata = {
                    id: selezionato_temp,
                    nome: lista_tipi_misure[selezionato_temp].nome,
                    categoria_misura: lista_tipi_misure[selezionato_temp].categoria_misura,
                };

                //console.table(tipo_misura_riduttiva_selezionata);

                apriPopupAggiuntaMisuraRiduzione();

            });

        },
        fail: function() {

            console.error("Errore!");

            //location.reload();

        }
    });

}

function setMisuraRiduzioneRischio() {

    var nome_intervento_temp = HtmlEntities.kpencode(jform_misura_nome_intervento.val());

    if (nome_intervento_temp == "") {
        nome_intervento_temp = tipo_misura_riduttiva_selezionata.nome;
    }

    var dati = {
        record: record,
        related_to: elemento_selezionato.related_to,
        pericolo: elemento_selezionato.pericolo,
        tipo_misura: tipo_misura_riduttiva_selezionata.id,
        nome_intervento: HtmlEntities.kpencode(jform_misura_nome_intervento.val()),
        adottare_entro: normalizzaData(jform_misura_adottare_entro.val()),
        nota: HtmlEntities.kpencode(jform_misura_nota.val()),
        riduzione_probabilita: jform_misura_riduzione_probabilita.val(),
        riduzione_magnitudo: jform_misura_riduzione_magnitudo.val()
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischi/php/setMisuraRiduzioneRischio.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {


        },
        success: function(data) {
            //console.log(data);

            getMisureRiduttivePericolo();

            jpopup_generico.modal("hide");

        },
        fail: function() {

            console.error("Errore!");

            //location.reload();

        }
    });

}

function apriPopupProbabilita() {

    jQuery.get("Smarty/templates/SproCore/KpRilevazioneRischi/templates/popup_help.html", function(data) {

        jpopup_generico.html(data);

        jbody_help = jQuery("#body_help");

        jbody_help.html(elemento_selezionato.help_probabilita);

        jpopup_generico.modal("show");

    });

}

function apriPopupMagnitudo() {

    jQuery.get("Smarty/templates/SproCore/KpRilevazioneRischi/templates/popup_help.html", function(data) {

        jpopup_generico.html(data);

        jbody_help = jQuery("#body_help");

        jbody_help.html(elemento_selezionato.help_magnitudo);

        jpopup_generico.modal("show");

    });

}

function setLinkRuolo(ruolo) {

    var dati = {
        record: record,
        related_to: elemento_selezionato.related_to,
        pericolo: elemento_selezionato.pericolo,
        ruolo: ruolo
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischi/php/setLinkRuolo.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {


        },
        success: function(data) {
            //console.log(data);

            jQuery("#pericolo_ruolo_" + elemento_selezionato.related_to + "_" + elemento_selezionato.pericolo + "_" + ruolo).prop("checked", true);

        },
        fail: function() {

            console.error("Errore scrittura!");

            //location.reload();

        }
    });

}

function unsetLinkRuolo(ruolo) {

    var dati = {
        record: record,
        related_to: elemento_selezionato.related_to,
        pericolo: elemento_selezionato.pericolo,
        ruolo: ruolo
    };

    jQuery.ajax({
        url: 'Smarty/templates/SproCore/KpRilevazioneRischi/php/unsetLinkRuolo.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {


        },
        success: function(data) {
            //console.log(data);

            jQuery("#pericolo_ruolo_" + elemento_selezionato.related_to + "_" + elemento_selezionato.pericolo + "_" + ruolo).prop("checked", false);

        },
        fail: function() {

            console.error("Errore scrittura!");

            //location.reload();

        }
    });

}

function selectTipoRilevazione(tipo) {

    //console.log(tipo);

    jbottone_rischi = jQuery(".bottone_rischi");

    jbottone_rischi.css("background-color", "#ebf2f8 !important");

    jQuery("#bottone_rischi_" + tipo).css("background-color", "#ffcc00 !important;");

    getTemplate(tipo);

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

function getPDF() {

    var dati = {
        record: record
    };

    window.open("Smarty/templates/SproCore/KpRilevazioneRischi/php/getPDF.php?record=" + record, "_blank");

}

function getExcel() {

    var dati = {
        record: record
    };

    window.open("Smarty/templates/SproCore/KpRilevazioneRischi/php/getExcel.php?record=" + record, "_blank");

}

function startBlinKArrow(){

    jgif_freccia.show();

    var changeStatusArrow = window.setTimeout("stopBlinKArrow()", 3000);

}

function stopBlinKArrow(){

    jgif_freccia.hide();

}