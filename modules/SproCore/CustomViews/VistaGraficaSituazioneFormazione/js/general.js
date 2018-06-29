/* kpro@tom29112016 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2016, Kpro Consulting Srl
 */

var jalert_offline;
var jcaricamento;

var larghezza_schermo;
var altezza_schermo;
var timer_check_offline;


//Elementi personalizzati

var jbottone_dettaglio;
var jdatalist_aziende;
var jform_stabilimento;
var jform_azienda;
var jpopup_filtri_stato;
var jbottone_filtri_stato;
var jform_tipo_corso;
var jbottone_aggiorna;
var jbottone_help;
var jpopup_help;

var larghezza_gantt;
var altezza_gantt;
var tasks = {
    data: [],
    links: []
};
var lista_operazioni;
var lista_relazioni;
var lista_scadenze;
var task_selezionata = 0;
var show_details = false;
var readonly = false;
var filtri_avanzati = {};
var filtro_azienda = "";

//Elementi personalizzati end

$(document).ready(function() {

    inizializzazione();

    inizializzazioneExtra();

    inizializzazioneGantt();

    //timer_check_offline = window.setInterval(checkConnection, 3000);

});

function reSize() {

    larghezza_schermo = window.innerWidth;
    altezza_schermo = window.innerHeight;

    larghezza_gantt = larghezza_schermo - 70;
    altezza_gantt = altezza_schermo - 140;

    $("#gantt_div").css("height", altezza_gantt);
    gantt.init("gantt_div");

}

function inizializzazione() {

    jalert_offline = $("#alert_offline");
    jcaricamento = $(".caricamento");

    larghezza_schermo = window.innerWidth;
    altezza_schermo = window.innerHeight;

    reSize();

    window.addEventListener('resize', function() {
        reSize();
    }, false);

}

function checkConnection() {

    if (navigator.onLine) {

        if (jalert_offline.dialog() && jalert_offline.dialog("isOpen")) {

            jalert_offline.dialog("close");

        }

    } else {

        jalert_offline.dialog();

    }
}

function inizializzazioneExtra() {

    jbottone_dettaglio = $("#bottone_dettaglio");
    jdatalist_aziende = $("#datalist_aziende");
    jform_stabilimento = $("#form_stabilimento");
    jform_azienda = $("#form_azienda");
    jpopup_filtri_stato = $("#popup_filtri_stato");
    jbottone_filtri_stato = $("#bottone_filtri_stato");
    jform_tipo_corso = $("#form_tipo_corso");
    jbottone_aggiorna = $("#bottone_aggiorna");
    jbottone_help = $("#bottone_help");
    jpopup_help = $("#popup_help");

    popolaDatalistAziende();

    popolaPickingListTipiCorso();

    jform_azienda.keyup(function(ev) {

        var nome_azienda_temp = jform_azienda.val();

        var code = ev.which;
        if (code == 13 || nome_azienda_temp == "") {

            filtro_azienda = nome_azienda_temp;

            popolaPickingListStabilimenti(filtro_azienda);

        }

    });

    jbottone_filtri_stato.click(function() {

        apriPopupFiltri();

    });

    jbottone_aggiorna.click(function() {

        filtri_avanzati = {
            azienda: jform_azienda.val(),
            stabilimento: jform_stabilimento.val(),
            tipo_corso: jform_tipo_corso.val()
        }

        caricaListaOperazioni();

        if (filtro_azienda != jform_azienda.val()) {

            filtro_azienda = jform_azienda.val();
            popolaPickingListStabilimenti(filtro_azienda);

        }

    });

    jbottone_help.click(function() {

        apriPopupHelp();

    });

}

function inizializzazioneGantt() {

    larghezza_gantt = larghezza_schermo - 70;
    altezza_gantt = altezza_schermo - 140;

    $("#gantt_div").css("height", altezza_gantt);

    drawGantt();
    gantt.init("gantt_div");
    caricaListaOperazioni();

    jbottone_dettaglio.click(function() {

        toggleView();

    });

    var func = function(e) {
        e = e || window.event;
        var el = e.target || e.srcElement;
        var value = el.value;
        setScaleConfig(value);
        gantt.render();
    };

    var els = document.getElementsByName("scale");
    for (var i = 0; i < els.length; i++) {
        els[i].onclick = func;
    }

}

function caricaListaOperazioni() {

    jcaricamento.show();

    gantt.clearAll();
    var date_to_str = gantt.date.date_to_str(gantt.config.task_date);
    var today = new Date(anno_corrente, mese_corrente - 1, giorno_corrente);
    gantt.addMarker({
        start_date: today,
        css: "today",
        text: "Today",
        title: "Today: " + date_to_str(today)
    });

    tasks = {
        data: [],
        links: []
    };
    gantt.parse(tasks);

    gantt.config.readonly = true;

    lista_operazioni = [];

    $.ajax({
        url: 'ListaOperazioni.php',
        dataType: 'json',
        async: false,
        data: filtri_avanzati,
        success: function(data) {
            lista_operazioni = data;
            //console.log(lista_operazioni);

            for (var i = 0; i < lista_operazioni.length; i++) {

                if (lista_operazioni[i].tipo_operazione == 'Raggruppamento') {
                    if (lista_operazioni[i].padre == 0 || lista_operazioni[i].padre == '' || lista_operazioni[i].padre == null) {
                        tasks.data.push({
                            id: lista_operazioni[i].projecttaskid,
                            text: lista_operazioni[i].projecttaskname,
                            type: gantt.config.types.project,
                            start_date: lista_operazioni[i].startdate_inv,
                            end_date: lista_operazioni[i].enddate_inv,
                            deadline: lista_operazioni[i].deadline_inv,
                            duration: 1,
                            tipo_operazione: lista_operazioni[i].tipo_operazione,
                            open: false,
                            nome_impianto: lista_operazioni[i].impianto_name,
                            nome_componente: lista_operazioni[i].nome_componente
                        });
                    } else {
                        tasks.data.push({
                            id: lista_operazioni[i].projecttaskid,
                            text: lista_operazioni[i].projecttaskname,
                            type: gantt.config.types.project,
                            start_date: lista_operazioni[i].startdate_inv,
                            end_date: lista_operazioni[i].enddate_inv,
                            deadline: lista_operazioni[i].deadline_inv,
                            duration: 1,
                            parent: lista_operazioni[i].padre,
                            tipo_operazione: lista_operazioni[i].tipo_operazione,
                            open: false,
                            nome_impianto: lista_operazioni[i].impianto_name,
                            nome_componente: lista_operazioni[i].nome_componente
                        });
                    }
                } else {
                    tasks.data.push({
                        id: lista_operazioni[i].projecttaskid,
                        text: lista_operazioni[i].projecttaskname,
                        type: gantt.config.types.task,
                        start_date: lista_operazioni[i].startdate_inv,
                        end_date: lista_operazioni[i].enddate_inv,
                        deadline: lista_operazioni[i].deadline_inv,
                        duration: 1,
                        progress: lista_operazioni[i].projecttaskprogress,
                        stato: lista_operazioni[i].stato,
                        tipo_operazione: lista_operazioni[i].tipo_operazione,
                        accountname: lista_operazioni[i].accountname,
                        nome_stabilimento: lista_operazioni[i].nome_stabilimento,
                        nome_risorsa: lista_operazioni[i].nome_risorsa,
                        tipicorso_name: lista_operazioni[i].tipicorso_name,
                        mansione_name: lista_operazioni[i].mansione_name
                    });
                }
            }

            //console.log(tasks);
            caricaListaMilestones();

        },
        fail: function() {
            console.error("Errore nel caricamento delle operazioni");
        }
    });

}

function caricaListaMilestones() {

    lista_scadenze = [];

    $.ajax({
        url: 'ListaScadenze.php',
        dataType: 'json',
        async: false,
        data: filtri_avanzati,
        success: function(data) {
            lista_scadenze = data;

            for (var i = 0; i < lista_scadenze.length; i++) {

                tasks.data.push({
                    id: lista_scadenze[i].projecttaskid,
                    text: lista_scadenze[i].projecttaskname,
                    type: gantt.config.types.milestone,
                    start_date: lista_scadenze[i].startdate_inv,
                    deadline: lista_scadenze[i].deadline_inv,
                    tipo_operazione: lista_scadenze[i].tipo_operazione,
                    stato: lista_scadenze[i].stato,
                    accountname: lista_scadenze[i].accountname,
                    nome_stabilimento: lista_scadenze[i].nome_stabilimento,
                    nome_risorsa: lista_scadenze[i].nome_risorsa,
                    tipicorso_name: lista_scadenze[i].tipicorso_name,
                    mansione_name: lista_scadenze[i].mansione_name,
                    selected_row: ''
                });

            }
            //console.log("Scadenze caricate");
            //console.table(tasks);
            caricaListaRelazioni();
        },
        fail: function() {
            console.error("Errore caricamento scadenze");
        }
    });
}

function caricaListaRelazioni() {

    gantt.parse(tasks); //Passa i dati al gantt
    //console.log(JSON.stringify(tasks));
    gantt.sort("start_date", false);

    jcaricamento.hide();

}

function convertDate(str) {
    var date = new Date(str),
        mnth = ("0" + (date.getMonth() + 1)).slice(-2),
        day = ("0" + date.getDate()).slice(-2);
    return [date.getFullYear(), mnth, day].join("-");
}

function convertDateHour(str) {
    var date = new Date(str);
    hour = ("0" + (date.getHours())).slice(-2);
    minutes = ("0" + (date.getMinutes())).slice(-2);
    return [hour, minutes].join(":");
}

function convertDateInv(str) {
    var date = new Date(str),
        mnth = ("0" + (date.getMonth() + 1)).slice(-2),
        day = ("0" + date.getDate()).slice(-2);
    return [day, mnth, date.getFullYear()].join("-");
}

function drawGantt() {

    gantt.config.xml_date = "%d-%m-%Y";

    gantt.config.autosize = false;

    gantt.config.static_background = true; //Questa impostazione, assieme allo smart rendering, permette di incrementare le prestazioni del gantt

    var date_to_str = gantt.date.date_to_str(gantt.config.task_date);
    var today = new Date(anno_corrente, mese_corrente - 1, giorno_corrente);

    gantt.addMarker({ //Aggiunge il marker sul giorno odierno
        start_date: today,
        css: "today",
        text: "Today",
        title: "Today: " + date_to_str(today)
    });

    setScaleConfig('2');

    gantt.config.scale_height = 50;

    gantt.config.subscales = [
        { unit: "day", step: 1, date: "%j, %D" }
    ];

    gantt.config.sort = true;

    gantt.config.readonly = true;

    gantt.locale.labels.deadline_enable_button = 'Set';
    gantt.locale.labels.deadline_disable_button = 'Remove';

    gantt.locale.labels["section_priority"] = "Priorit&agrave";
    gantt.locale.labels["users"] = "Risorsa";

    gantt.config.columns = [{
            name: "overdue",
            label: "",
            width: 30,
            template: function(obj) {
                if (obj.deadline) {
                    var deadline = gantt.date.parseDate(obj.deadline, "xml_date");
                    if (deadline && (obj.stato == 'Non eseguita' || obj.stato == 'Scaduta' || obj.stato == 'Non eseguito corso base' || obj.stato == 'Non eseguita formazione precedente')) {
                        return '<div class="overdue_indicator">!</div>';
                    } else if (deadline && (obj.stato == 'In scadenza')) {
                        return '<div class="overdue_indicator_in_scadenza">!</div>';
                    } else if (deadline && (obj.stato == 'Eseguita' || obj.stato == 'Eseguito Corso Base' || obj.stato == 'Valida senza scadenza' || obj.stato == 'In corso di validita')) {
                        return '<div class="indicator_eseguita"></div>';
                    }
                }
                return '<div></div>';
            }
        },
        { name: "nome_risorsa", label: "Risorsa", tree: true, width: '*', resize: true },
        { name: "mansione_name", label: "Mansione", tree: true, width: '*', resize: true, hide: true },
        { name: "tipicorso_name", label: "Tipo Corso", tree: true, width: '*', resize: true, hide: true },
        { name: "accountname", label: "Azienda", align: "left", resize: true, hide: true },
        { name: "nome_stabilimento", label: "Stabilimento", align: "left", resize: true, hide: true },
        { name: "stato", label: "Stato", align: "left", resize: true, hide: true },
        { name: "deadline", label: "Data Scadenza", width: 100, align: "center" }
    ];

    function myGridFunc(task) {
        /*if(task.type == gantt.config.types.milestone){
            return "<div class='milestone_columns'>" + task.text + "</div>";
        }
        else if(gantt.hasChild(task.id)){
            return "<div class='raggruppamento_columns'>" + task.text + "</div>";
        }
        else{
            return task.text;
        }*/

        return task.text;
    };

    gantt.config.lightbox.sections = [
        { name: "description", height: 38, map_to: "text", type: "textarea", focus: true },
        { name: "priority", height: 22, map_to: "priority", type: "select", options: [{ key: "low", label: "Low" }, { key: "normal", label: "Normal" }, { key: "hight", label: "Hight" }] },
        { name: "template", height: 30, map_to: "my_template", type: "template" },
        { name: "time", type: "time", map_to: "auto", time_format: ["%d", "%m", "%Y"] },
        { name: "deadline", map_to: { start_date: "deadline" }, type: "duration_optional", button: true, single_date: true }
    ];

    gantt.locale.labels.section_deadline = "Deadline";

    gantt.config.grid_width = 400;
    gantt.config.grid_resize = true;
    gantt.config.keep_grid_width = true;

    show_details = false;

    gantt.locale.labels.section_template = "Dettaglio";

    gantt.templates.scale_cell_class = function(date) {
        if (date.getDay() == 0 || date.getDay() == 6) {
            return "weekend";
        }
    };
    gantt.templates.task_cell_class = function(item, date) {
        if (date.getDay() == 0 || date.getDay() == 6) {
            return "weekend"
        }
    };

    gantt.config.wide_form = 1;

    gantt.templates.tooltip_text = function(start, end, task) {

        var tooltip_text = "";

        tooltip_text = "<div class='tooltip_style'>";
        tooltip_text += "<p style='text-align: center;'>";
        tooltip_text += "<b>Dati Scadenza</b><hr /></p><p>";
        tooltip_text += "<b>Risorsa:</b> " + task.nome_risorsa + "</p>";
        tooltip_text += "<b>Mansione:</b> " + task.mansione_name + "</p>";
        tooltip_text += "<b>Tipo Corso:</b> " + task.tipicorso_name + "</p>";
        tooltip_text += "<p><b>Data Scadenza:</b> " + convertDateInv(task.deadline) + "</p>";
        tooltip_text += "<p><b>Stato Scadenza:</b> " + task.stato + "</p>";
        tooltip_text += "<p><b>Data Ult. Formazione:</b> " + convertDateInv(task.start_date) + "</p></div>";

        return tooltip_text;
    };

    gantt.templates.task_class = function(start, end, task) {

        if (task.tipo_operazione == 'Raggruppamento') {
            return "master";
        } else if (task.stato == 'Non eseguita' || task.stato == 'Scaduta' || task.stato == 'Eseguito Corso Base') {
            return "non_eseguita";
        } else if (task.stato == 'In scadenza') {
            return "in_scadenza";
        } else if (task.stato == 'Eseguita' || task.stato == 'Valida senza scadenza' || task.stato == 'In corso di validita') {
            return "eseguita";
        }

    };

    gantt.locale.labels["dettaglio_button"] = "Dettaglio";
    gantt.locale.labels["risorsa_button"] = "Risorsa";
    gantt.config.buttons_left = ["dhx_save_btn", "dhx_cancel_btn", "dettaglio_button", "risorsa_button"];

    var filter_input = '';

    var filtri_gantt = document.getElementById("filtri_stato").getElementsByTagName("input");
    for (var i = 0; i < filtri_gantt.length; i++) {
        filter_input = filtri_gantt[i];

        filter_input.onchange = function() {
            gantt.refreshData();
        }
    }

    function hasStatus(parent, status) {

        if (gantt.getTask(parent).tipo_operazione == 'Raggruppamento') {
            var child = gantt.getChildren(parent);
            for (var i = 0; i < child.length; i++) {
                if (hasStatus(child[i], status)) {

                    return true;

                }
            }
        } else if (gantt.getTask(parent).stato == status) {
            return true;
        }

        return false;
    }

    gantt.attachEvent("onBeforeTaskDisplay", function(id, task) {
        for (var i = 0; i < filtri_gantt.length; i++) {

            var filtro_stato = filtri_gantt[i];

            if (filtro_stato.checked) {
                if (hasStatus(id, filtro_stato.name)) {
                    return true;
                }
            }

        }

        return false;
    });

    gantt.attachEvent("onTaskLoading", function(task) {

        if (task.start_date) {

            task.start_date = gantt.date.parseDate(task.start_date, "xml_date");

        }

        if (task.end_date) {

            task.end_date = gantt.date.parseDate(task.end_date, "xml_date");

        }

        if (task.deadline) {

            task.deadline = gantt.date.parseDate(task.deadline, "xml_date");

            gantt.addTaskLayer(function draw_deadline(task) {

                if (task.deadline && task.type == gantt.config.types.task) {

                    var el = document.createElement('div');
                    el.className = 'deadline';
                    var sizes = gantt.getTaskPosition(task, task.deadline);

                    el.style.left = sizes.left + 'px';
                    el.style.top = sizes.top + 'px';

                    el.setAttribute('title', gantt.templates.task_date(task.deadline));
                    return el;

                }

                return false;

            });

        }

        return true;

    });

    gantt.init("gantt_div");

}

function toggleView() {
    show_details = !show_details;
    gantt.getGridColumn("mansione_name").hide = !show_details;
    gantt.getGridColumn("tipicorso_name").hide = !show_details;
    gantt.getGridColumn("accountname").hide = !show_details;
    gantt.getGridColumn("nome_stabilimento").hide = !show_details;
    gantt.getGridColumn("stato").hide = !show_details;

    if (show_details) {
        gantt.config.grid_width = 1200;
    } else {
        gantt.config.grid_width = 400;
    }
    gantt.render();
};

function setScaleConfig(value) {
    switch (value) {
        case "1":
            gantt.config.scale_unit = "day";
            gantt.config.step = 1;
            gantt.config.date_scale = "%d %M";
            gantt.config.subscales = [];
            gantt.config.scale_height = 27;
            gantt.templates.date_scale = null;
            break;
        case "2":
            var weekScaleTemplate = function(date) {
                var dateToStr = gantt.date.date_to_str("%d %M");
                var endDate = gantt.date.add(gantt.date.add(date, 1, "week"), -1, "day");
                return dateToStr(date) + " - " + dateToStr(endDate);
            };

            gantt.config.scale_unit = "week";
            gantt.config.step = 1;
            gantt.templates.date_scale = weekScaleTemplate;
            gantt.config.subscales = [
                { unit: "day", step: 1, date: "%j, %D" }
            ];
            gantt.config.scale_height = 50;
            gantt.config.min_column_width = 50;
            break;
        case "3":
            gantt.config.scale_unit = "month";
            gantt.config.date_scale = "%F, %Y";
            gantt.config.subscales = [
                { unit: "week", step: 1, date: "%W" }
            ];
            gantt.config.scale_height = 50;
            gantt.templates.date_scale = null;
            gantt.config.min_column_width = 50;
            break;
        case "4":
            gantt.config.scale_unit = "year";
            gantt.config.step = 1;
            gantt.config.date_scale = "%Y";
            gantt.config.min_column_width = 50;
            gantt.config.scale_height = 50;
            gantt.templates.date_scale = null;
            gantt.config.subscales = [
                { unit: "month", step: 1, date: "%M" }
            ];
            break;
    }
}

function in_array(needle, haystack, argStrict) {
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

function popolaDatalistAziende() {

    $.ajax({
        url: 'ListaAziende.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            var datalist_temp = "";

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    datalist_temp += "<option value='" + data[i].accountname + "'>";

                }

            }

            jdatalist_aziende.empty();
            jdatalist_aziende.append(datalist_temp);

        },
        fail: function() {
            console.error("Errore caricamento del datalist");
        }
    });

}

function popolaPickingListStabilimenti(nome_azienda) {

    var filtro_lista_stabilimenti = {
        nome_azienda: nome_azienda
    };

    $.ajax({
        url: 'ListaStabilimenti.php',
        dataType: 'json',
        async: true,
        data: filtro_lista_stabilimenti,
        beforeSend: function() {

            jcaricamento.show();

        },
        success: function(data) {

            var lista_stabilimenti_temp = "<option value='all'>Tutti</option>";

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    lista_stabilimenti_temp += "<option value='" + data[i].stabilimentiid + "'>" + data[i].nome_stabilimento + "</option>";

                }

            }

            jform_stabilimento.empty();
            jform_stabilimento.append(lista_stabilimenti_temp);

            jcaricamento.hide();

        },
        fail: function() {
            jcaricamento.hide();
            console.error("Errore caricamento della picking list dei stabilimenti");
        }
    });

}

function apriPopupFiltri() {

    jpopup_filtri_stato.dialog({
        resizable: false,
        height: 400,
        width: 360,
        modal: true,
        buttons: {
            "Chiudi": {
                text: "Chiudi",
                class: 'AlignLeft',
                click: function() {

                    $(this).dialog("close");
                }
            }
        }
    });

}

function popolaPickingListTipiCorso() {

    $.ajax({
        url: 'ListaTipiCorso.php',
        dataType: 'json',
        async: true,
        beforeSend: function() {

        },
        success: function(data) {

            var lista_tipi_corso_temp = "<option value='all'>Tutti</option>";

            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    lista_tipi_corso_temp += "<option value='" + data[i].tipo_corso + "'>" + data[i].tipicorso_name + "</option>";

                }

            }

            jform_tipo_corso.empty();
            jform_tipo_corso.append(lista_tipi_corso_temp);

        },
        fail: function() {

            console.error("Errore caricamento della picking list tipi corso");

        }
    });

}

function apriPopupHelp() {

    jpopup_help.dialog({
        resizable: false,
        height: 350,
        width: 450,
        modal: true,
        buttons: {
            "Chiudi": {
                text: "Chiudi",
                class: 'AlignLeft',
                click: function() {

                    $(this).dialog("close");
                }
            }
        }
    });

}