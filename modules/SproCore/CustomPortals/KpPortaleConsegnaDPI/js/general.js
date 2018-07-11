/* kpro@bid26102016 */

/**
 * @author Bidese Jacopo
 * @copyright (c) 2016, Kpro Consulting Srl
 * @package portaleConsegnaDPI
 * @version 1.0
 */

var jbody = '';
var jmenu_button = '';
var jnav_div = '';
var jpanel = '';
var jtable_header = '';
var jnav_div_list_li = '';
var jtitolo_pagina = '';
var jtorna_a_precedente = '';
var jcaricamento;

var jtasti_pdf_div;
var jarea_firma_div;
var jarea_firma;
var jbottone_pulisci_firma;
var jbottone_anteprima_pdf;

var jtd_nuovo_movimento = "";
var id_movimento_creato = 0;
var jtd_modifica_movimento = "";
var risorse = [];
var id_risorsa_selezionata = 0;
var id_azienda_selezionata = 0;
var id_stabilimento_selezionato = 0;
var jform_numero = "";
var jform_data = "";
var jform_tipo_consegna = "";
var jform_risorsa = "";
var jform_azienda = "";
var jform_stabilimento = "";
var jform_stato = "";
var stato_consegna = "";
var jdescrizione_consegna = "";
var jcorpo_lista_righe_movimento = "";
var jbottone_aggiungi_riga = "";
var testata = {};
var righe = [];
var prodotti_righe = [];
var prodotti_da_aggiungere = [];
var quantita_prodotti_da_aggiungere = [];
var data_scadenza_prodotti_da_aggiungere = [];
var variazione_carico = false;
var aggiornando = false; /* kpro@bid090520170900 */
var salvando = false; /* kpro@bid090520170900 */
var id_riga_modifica = 0; /* kpro@bid090520170900 */
var tab_selezionato = 'tab_tutti_prodotti';

var jbottone_aggiungi_documento = '';
var jprogress_status = '';
var jfile = '';
var jform_titolo_documento = '';
var jpercorso_file = '';
var jnome_documento_temp = '';
var jbottone_download_doc = '';
var jcorpo_lista_documenti = '';
var documento = [];
var jbollino_numero_documenti = "";

var menu_aperto = true;
var pagina_selezionata = '';
var altezza_schermo = '';
var larghezza_schermo = '';
var jclock;
var myclock;
var timer_check_offline;

var imgDataBackup = '';
var salvando_firma = false;

$(document).ready(function() {

    inizializzazione();

    inizializzazioneMaterialize();

    inizializzazioneBootstrap(); /* kpro@bid090520171000 */

    inizializzazioneForm();

    myclock = window.setInterval(myTimer, 1000);

    timer_check_offline = window.setInterval(checkConnection, 3000);

});

function inizializzazione() {

    jclock = $("#clock");
    jalert_offline = $("#alert_offline");
    jcaricamento = $(".caricamento");
    jmenu_button = $("#menu_button");
    jnav_div_list_li = $("#nav_div .list li");
    jpanel = $(".panel");
    jnome_assistenza = $("#nome_assistenza");
    jdiv_img_worker = $("#div_img_worker");
    jlist_number_page2 = $("#list_number_page2");
    jtorna_a_precedente = $("#torna_a_precedente");

    jtd_nuovo_movimento = $('#td_nuovo_movimento');
    jtd_modifica_movimento = $('#td_modifica_movimento');
    jform_numero = $("#form_numero");
    jform_data = $("#form_data");
    jform_tipo_consegna = $("#form_tipo_consegna");
    jform_risorsa = $("#form_risorsa");
    jform_azienda = $("#form_azienda");
    jform_stabilimento = $("#form_stabilimento");
    jform_stato = $("#form_stato");
    jdescrizione_consegna = $("#descrizione_consegna");
    jcorpo_lista_righe_movimento = $("#corpo_lista_righe_movimento");
    jbottone_aggiungi_riga = $("#bottone_aggiungi_riga");

    jbottone_aggiungi_documento = $("#bottone_aggiungi_documento");
    jprogress_status = $("#progress_status");
    jfile = $("#file");
    jform_titolo_documento = $("#form_titolo_documento");
    jpercorso_file = $("#percorso_file");
    jnome_documento_temp = $("#nome_documento_temp");
    jbottone_download_doc = $(".bottone_download_doc");
    jcorpo_lista_documenti = $("#corpo_lista_documenti");
    jbollino_numero_documenti = $("#bollino_numero_documenti");

    var jpagina_default = $("#button_page1");

    jpanel.hide();
    selezioneMenu(jpagina_default);
    apriMenu();

    larghezza_schermo = innerWidth;
    altezza_schermo = innerHeight;

    jmenu_button.click(function() {
        if (menu_aperto === true) {
            chiudiMenu();
        } else {
            apriMenu();
        }
    });

    jnav_div_list_li.click(function() {
        if (controllo_form()) {
            var elemento_selezionato = $(this);
            selezioneMenu(elemento_selezionato);
        }
    });

    window.addEventListener('resize', function() {
        reSize();
    }, false);

    jtorna_a_precedente.click(function() {
        if (record == "new") {
            window.open(indirizzo_crm + "/index.php?action=ListView&module=ConsegnaDPI", "_self");
        } else {
            window.open(indirizzo_crm + "/index.php?action=DetailView&module=ConsegnaDPI&record=" + record, "_self");
        }
    });

    $("#button_fine_consegna").click(function() {
        if (record != "new") {
            if (stato_consegna != "Confermata") {
                if (controllo_form() && controllo_righe()) {

                    if (firma_grafometrica == "si") {
                        generaPdfTemporaneo(record);
                    } else {
                        anteprimaPdf();
                    }
                }
            } else {
                alert("Movimento gia' confermato");
            }
        } else {
            alert("E' necessario completare i campi obbligatori e aggiungere almeno una riga per poter confermare il movimento");
        }
    });

    jbottone_aggiungi_documento.click(function() {
        if (record != "new") {
            apriPopupDocumento();
        }
    });

    jform_risorsa.click(function() {
        if (stato_consegna != "Confermata") {
            apriPopupRisorse();
        }
    });

    jbottone_aggiungi_riga.click(function() {
        if (stato_consegna != "Confermata") {
            apriPopupAggiungiRiga();
        }
    });

    jdescrizione_consegna.change(function() {
        var dati_movimento = {
            id: record,
            descrizione: jdescrizione_consegna.val(),
            variazione: 'descrizione'
        };
        salvaMovimentoMagazzino(dati_movimento);
    });

    jform_tipo_consegna.change(function() {
        var dati_movimento = {
            id: record,
            tipo_consegna: jform_tipo_consegna.val(),
            variazione: 'tipo_consegna'
        };
        salvaMovimentoMagazzino(dati_movimento);
    });

    $('ul.tabs').click(function(){
        var tab_selezionato_temp = $("ul.tabs a.active").prop('id');
        if(tab_selezionato != tab_selezionato_temp){
            tab_selezionato = tab_selezionato_temp;

            variazione_carico = false;
            prodotti_da_aggiungere = [];
            quantita_prodotti_da_aggiungere = [];
            data_scadenza_prodotti_da_aggiungere = [];

            $('search_codice_prodotto').val("");
            $('search_nome_prodotto').val("");
            $('search_codice_prodotto_mans').val("");
            $('search_nome_prodotto_mans').val("");

            caricaPopupTuttiProdotti("", "");
            caricaPopupProdottiMansione("", "");
        }
    });
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

/* kpro@bid090520171000 */
function inizializzazioneBootstrap() {

    jQuery('.campo_data_datepicker').bootstrapMaterialDatePicker({
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
/* kpro@bid090520171000 end */

function myTimer() {

    now = new Date();

    var giorno = now.getDate();
    giorno = String("0" + giorno).slice(-2);

    var mese = now.getMonth() + 1;
    mese = String("0" + mese).slice(-2);

    var anno = now.getFullYear();
    anno = String("0" + anno).slice(-4);

    var ore = now.getHours();
    ore = String("0" + ore).slice(-2);

    var minuti = now.getMinutes();
    minuti = String("0" + minuti).slice(-2);

    var secondi = now.getSeconds();
    secondi = String("0" + secondi).slice(-2);

    var data = giorno + "/" + mese + "/" + anno;
    var ora = ore + ":" + minuti + ":" + secondi;

    jclock.html(data + " " + ora);

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

function checkConnection() {

    if (navigator.onLine) {

        jalert_offline.closeModal();

    } else {

        jalert_offline.openModal();

    }
}

function controllo_righe() {
    var res = false;
    if (righe.length > 0) {
        res = true;
    } else {
        alert("Movimento privo di righe!");
    }
    return res;
}

function controllo_form() {
    var res = false;
    if (id_risorsa_selezionata != "" && id_risorsa_selezionata != 0 &&
        id_azienda_selezionata != "" && id_azienda_selezionata != 0 &&
        id_stabilimento_selezionato != "" && id_stabilimento_selezionato != 0) {

        res = true;
    } else {
        alert("Il campo Risorsa e' obbligatorio!");
    }
    return res;
}

function salvaMovimentoMagazzino(filtro) {
    //console.log(filtro);
    $.ajax({
        url: "salvaMovimentoMagazzino.php",
        dataType: 'json',
        data: filtro,
        async: true,
        success: function(data) {
            //console.log(data);
            if (data.length > 0) {
                if (filtro.variazione == "creazione") {
                    if (data[0].id != "" && data[0].id != 0 && data[0].id != "0" && data[0].id != "new") {
                        window.open(indirizzo_crm + "/modules/SproCore/CustomPortals/KpPortaleConsegnaDPI/index.php?record=" + data[0].id, "_self");
                    }
                }
            }
        },
        fail: function() {
            console.log("Errore nel salvare movimento");
        }
    });
}

function salvaRigheMovimentoMagazzino() {
    var errore_quantita_data = false;
    //console.log(prodotti_da_aggiungere);
    //console.log(quantita_prodotti_da_aggiungere);
    //console.log(data_scadenza_prodotti_da_aggiungere);
    for (var i = 0; i < prodotti_da_aggiungere.length; i++) {
        var id_prodotto_corrente = prodotti_da_aggiungere[i];
        if (jQuery.inArray(id_prodotto_corrente, prodotti_righe) == -1) {
            var quantita_prodotto_corrente = quantita_prodotti_da_aggiungere[id_prodotto_corrente];
            var data_scadenza_prodotto_corrente = data_scadenza_prodotti_da_aggiungere[id_prodotto_corrente];
            if (record != "" && record != 0 && record != "new" &&
                id_prodotto_corrente != "" && id_prodotto_corrente != 0) {
                if (quantita_prodotto_corrente != "" && quantita_prodotto_corrente != undefined && quantita_prodotto_corrente > 0) { /* kpro@bid090520170900 */

                    /* kpro@bid090520170900 */
                    if(data_scadenza_prodotto_corrente != "" && data_scadenza_prodotto_corrente != undefined){
                        data_scadenza_prodotto_corrente = normalizzaData(data_scadenza_prodotto_corrente);
                    }
                    else{
                        data_scadenza_prodotto_corrente = "";
                    }
                    /* kpro@bid090520170900 end */

                    var dati_righe_movimento = {
                        id: record,
                        id_prodotto: id_prodotto_corrente,
                        quantita: quantita_prodotto_corrente,
                        data: data_scadenza_prodotto_corrente
                    };
                    //console.log(dati_righe_movimento_carico);
                    $.ajax({
                        url: "salvaRigheMovimentoMagazzino.php",
                        dataType: 'json',
                        data: dati_righe_movimento,
                        async: false,
                        success: function(data) {
                            //console.log(data);
                        },
                        fail: function() {
                            console.log("Errore nel salvare righe movimento");
                        }
                    });
                } else {
                    errore_quantita_data = true;
                }
            }
        }
    }
    if (errore_quantita_data) {
        alert("Alcuni prodotti non sono completi di una quantita' e/o una data scadenza corretti.");
    }
}

function caricaPopupRisorse(nome, cognome, azienda, stabilimento, mansione) {
    $('#body_tabella_popup_risorse').empty();
    var dati = {
        nome: nome,
        cognome: cognome,
        azienda: azienda,
        stabilimento: stabilimento,
        mansione: mansione
    };
    
    $.ajax({
        url: "caricaPopupRisorse.php",
        dataType: 'json',
        data: dati,
        async: true,
        success: function(data) {
            var html_lista_risorse = "";

            if (data.length > 0) {
                for (var i = 0; i < data.length; i++) {
                    html_lista_risorse += "<tr class='tr_risorse' id='tr_risorse_" + data[i].id + "'>";
                    html_lista_risorse += "<td id='td_nome_risorsa_" + data[i].id + "'>" + data[i].nome + "</td>";
                    html_lista_risorse += "<td id='td_cognome_risorsa_" + data[i].id + "'>" + data[i].cognome + "</td>";
                    html_lista_risorse += "<td id='td_azienda_risorsa_" + data[i].id + "'>" + data[i].azienda + "</td>";
                    html_lista_risorse += "<td id='td_stabilimento_risorsa_" + data[i].id + "'>" + data[i].stabilimento + "</td>";
                    html_lista_risorse += "<td id='td_mansione_risorsa_" + data[i].id + "'>" + data[i].mansione + "</td></tr>";

                    risorse[data[i].id] = {
                        id: data[i].id,
                        nome: data[i].nome,
                        cognome: data[i].cognome,
                        azienda: data[i].azienda,
                        id_azienda: data[i].id_azienda,
                        stabilimento: data[i].stabilimento,
                        id_stabilimento: data[i].id_stabilimento,
                        mansione: data[i].mansione
                    };
                }
            } else {
                html_lista_risorse += "<tr><td colspan='5' style='text-align:center;'>Nessuna risorsa</td></tr>";
            }

            $('#body_tabella_popup_risorse').empty();
            $('#body_tabella_popup_risorse').append(html_lista_risorse);

            $('#search_nome_risorsa').keypress(function(e) {
                if (e.which == 13) {
                    caricaPopupRisorse($('#search_nome_risorsa').val(), "", "", "", "");
                }
            });

            $('#search_cognome_risorsa').keypress(function(e) {
                if (e.which == 13) {
                    caricaPopupRisorse("", $('#search_cognome_risorsa').val(), "", "", "");
                }
            });

            $('#search_azienda_risorsa').keypress(function(e) {
                if (e.which == 13) {
                    caricaPopupRisorse("", "", $('#search_azienda_risorsa').val(), "", "");
                }
            });

            $('#search_stabilimento_risorsa').keypress(function(e) {
                if (e.which == 13) {
                    caricaPopupRisorse("", "", "", $('#search_stabilimento_risorsa').val(), "");
                }
            });

            $('#search_mansione_risorsa').keypress(function(e) {
                if (e.which == 13) {
                    caricaPopupRisorse("", "", "", "", $('#search_mansione_risorsa').val());
                }
            });

            $('.tr_risorse').click(function() {
                var id_tr_risorsa = $(this).prop("id");

                id_risorsa_selezionata = id_tr_risorsa.substring(11);
                id_azienda_selezionata = risorse[id_risorsa_selezionata].id_azienda;
                id_stabilimento_selezionato = risorse[id_risorsa_selezionata].id_stabilimento;

                var dati_movimento = {
                    id: record,
                    data: normalizzaData(jform_data.val()),
                    risorsa: id_risorsa_selezionata,
                    azienda: id_azienda_selezionata,
                    stabilimento: id_stabilimento_selezionato,
                    tipo_consegna: jform_tipo_consegna.val(),
                    descrizione: jdescrizione_consegna.val(),
                    variazione: 'creazione'
                };

                salvaMovimentoMagazzino(dati_movimento);
            });
        },
        fail: function() {
            console.log("Errore nel caricare popup risorse");
        }
    });
}

function eliminaRiga(id) {
    var filtro_riga = {
        id: id
    };

    $.ajax({
        url: "eliminaRiga.php",
        dataType: 'json',
        data: filtro_riga,
        async: true,
        success: function(data) {
            caricaDatiRigheMagazzino(record);
        },
        fail: function() {
            console.log("Errore nel eliminare riga");
        }
    });
}

function aggiornaRiga(id, quantita, data_scadenza) {
    /* kpro@bid090520170900 */
    if(data_scadenza != "" && data_scadenza != undefined){
        data_scadenza = normalizzaData(data_scadenza);
    }
    else{
        data_scadenza = "";
    }
    /* kpro@bid090520170900 end */

    var filtro_riga = {
        id: id,
        quantita: quantita,
        data: data_scadenza
    };

    $.ajax({
        url: "aggiornaRiga.php",
        dataType: 'json',
        data: filtro_riga,
        async: true,
        success: function(data) {
            aggiornando = false; /* kpro@bid090520170900 */
            caricaDatiRigheMagazzino(record);
            $('#popup_modifica_riga').closeModal();            
        },
        fail: function() {
            aggiornando = false; /* kpro@bid090520170900 */
            console.log("Errore nel aggiornare riga");
        }
    });
}

function caricaRigaModifica(id_riga) {
    $('#body_tabella_popup_modifica_riga').empty();
    var filtro_riga = {
        record: id_riga
    };

    $.ajax({
        url: "caricaRigaVisualizzaModifica.php",
        dataType: 'json',
        data: filtro_riga,
        async: true,
        success: function(data) {
            var html_riga_visualizza_modifica = "";

            if (data.length > 0) {
                html_riga_visualizza_modifica += "<tr id='tr_mod_riga_" + data[0].id + "'>";
                html_riga_visualizza_modifica += "<td class='td_immagine_prodotto' id='td_mod_immagine_riga_" + data[0].id + "'>";
                if (data[0].id_immagine != 0 && data[0].id_immagine != '0') {
                    html_riga_visualizza_modifica += "<img style='max-height:40px; max-width:60px;' src='" + indirizzo_crm + "/" + data[0].path_immagine + data[0].id_immagine + "_" + data[0].nome_immagine + "'></td>";
                } else {
                    html_riga_visualizza_modifica += "<i class='material-icons center md-24'>image</i></td>";
                }
                html_riga_visualizza_modifica += "<td class='td_nome_prodotto' id='td_mod_nome_riga_" + data[0].id + "'>" + data[0].nome + "</td>";
                html_riga_visualizza_modifica += "<td class='td_quantita_riga' id='td_mod_quantita_riga_" + data[0].id + "'>";
                html_riga_visualizza_modifica += "<input style='text-align:right;' type='number' class='input_text' id='input_quantita_mod_" + data[0].id + "' value='" + data[0].quantita + "' min='1' step='1'/></td>";
                html_riga_visualizza_modifica += "<td class='td_data_scadenza' id='td_mod_data_scadenza_riga_" + data[0].id + "'>";
                html_riga_visualizza_modifica += "<input type='text' class='input_text campo_data_datepicker' id='input_data_scadenza_mod_" + data[0].id + "' value='" + data[0].data_inv + "' readonly='true'></td>";
                html_riga_visualizza_modifica += "<td class='td_richiedente' id='td_mod_richiedente_riga_" + data[0].id + "'>" + data[0].nome_richiedente + "</td></tr>";
            } else {
                html_riga_visualizza_modifica += "<tr><td colspan='5' style='text-align:center;'><em>Impossibile visualizzare la riga</em></td></tr>";
            }

            $('#body_tabella_popup_modifica_riga').empty();
            $('#body_tabella_popup_modifica_riga').append(html_riga_visualizza_modifica);

            inizializzazioneBootstrap(); /* kpro@bid090520171000 */
        },
        fail: function() {
            console.log("Errore nel caricare riga");
        }
    });
}

function apriPopupModificaRiga(id_riga) {
    id_riga_modifica = id_riga; /* kpro@bid090520170900 */

    caricaRigaModifica(id_riga);

    $('#popup_modifica_riga').openModal();

    $('#salva_popup_modifica_riga').click(function() {
        /* kpro@bid090520170900 */
        if(!aggiornando){
            aggiornando = true;
            var quantita_mod = $("#input_quantita_mod_" + id_riga_modifica).val();
            var data_scadenza_mod = $("#input_data_scadenza_mod_" + id_riga_modifica).val();
            if (quantita_mod != "" && quantita_mod != undefined && quantita_mod >= 1) {
                aggiornaRiga(id_riga_modifica, quantita_mod, data_scadenza_mod);
            } else {
                alert("Inserire una quantita' corretta!");
                aggiornando = false;
            }
        }
        /* kpro@bid090520170900 end */
    });
}

function caricaRigaVisualizza(id_riga) {
    $('#body_tabella_popup_visualizza_riga').empty();
    var filtro_riga = {
        record: id_riga
    };

    $.ajax({
        url: "caricaRigaVisualizzaModifica.php",
        dataType: 'json',
        data: filtro_riga,
        async: true,
        success: function(data) {
            var html_riga_visualizza_modifica = "";

            if (data.length > 0) {
                html_riga_visualizza_modifica += "<tr id='tr_vis_riga_" + data[0].id + "'>";
                html_riga_visualizza_modifica += "<td class='td_immagine_prodotto' id='td_vis_immagine_riga_" + data[0].id + "'>";
                if (data[0].id_immagine != 0 && data[0].id_immagine != '0') {
                    html_riga_visualizza_modifica += "<img style='max-height:40px; max-width:60px;' src='" + indirizzo_crm + "/" + data[0].path_immagine + data[0].id_immagine + "_" + data[0].nome_immagine + "'></td>";
                } else {
                    html_riga_visualizza_modifica += "<i class='material-icons center md-24'>image</i></td>";
                }
                html_riga_visualizza_modifica += "<td class='td_nome_prodotto' id='td_vis_nome_riga_" + data[0].id + "'>" + data[0].nome + "</td>";
                html_riga_visualizza_modifica += "<td class='td_quantita_riga' id='td_vis_quantita_riga_" + data[0].id + "'>";
                html_riga_visualizza_modifica += data[0].quantita + "</td>";
                html_riga_visualizza_modifica += "<td class='td_data_scadenza' id='td_vis_data_scadenza_riga_" + data[0].id + "'>";
                html_riga_visualizza_modifica += data[0].data_inv + "</td>";
                html_riga_visualizza_modifica += "<td class='td_richiedente' id='td_vis_richiedente_riga_" + data[0].id + "'>" + data[0].nome_richiedente + "</td></tr>";
            } else {
                html_riga_visualizza_modifica += "<tr><td colspan='5' style='text-align:center;'><em>Impossibile visualizzare la riga</em></td></tr>";
            }

            $('#body_tabella_popup_visualizza_riga').empty();
            $('#body_tabella_popup_visualizza_riga').append(html_riga_visualizza_modifica);
        },
        fail: function() {
            console.log("Errore nel caricare riga");
        }
    });
}

function apriPopupVisualizzaRiga(id_riga) {
    caricaRigaVisualizza(id_riga);
    $('#popup_visualizza_riga').openModal();
}

function apriPopupRisorse() {
    caricaPopupRisorse("", "", "", "", "");
    $('#popup_risorse').openModal();
}

function caricaPopupTuttiProdotti(codice, nome) {
    $('#body_tabella_prodotti').empty();

    var dati = {
        codice: codice,
        nome: nome,
        risorsa: id_risorsa_selezionata,
        data_consegna: testata.data
    };
    //console.log(dati);
    $.ajax({
        url: "caricaPopupTuttiProdotti.php",
        dataType: 'json',
        data: dati,
        async: true,
        success: function(data) {
            //console.log(data);
            var lista_dpi = "";
            if (data.length > 0) {
                for (var i = 0; i < data.length; i++) {
                    lista_dpi += "<tr class='tr_prodotti' id='tr_prodotto_" + data[i].id + "'>";

                    lista_dpi += "<td class='td_popup_immagine_prodotti' id='td_immagine_" + data[i].id + "'>";
                    if (data[i].id_immagine != 0 && data[i].id_immagine != '0') {
                        lista_dpi += "<img style='max-height:40px; max-width: 60px;' src='" + indirizzo_crm + "/" + data[i].path_immagine + data[i].id_immagine + "_" + data[i].nome_immagine + "'>";
                    } else {
                        lista_dpi += "<i class='material-icons center md-24'>image</i>";
                    }
                    lista_dpi += "</td>";
                    lista_dpi += "<td class='td_popup_codice_prodotti' id='td_codice_" + data[i].id + "'>" + data[i].codice + "</td>";
                    lista_dpi += "<td class='td_popup_nome_prodotti' id='td_nome_" + data[i].id + "'>" + data[i].nome + "</td>";
                    lista_dpi += "<td class='td_popup_data_ultima_consegna' id='td_data_ultima_consegna_" + data[i].id + "'>" + data[i].data_ultima_consegna + "</td>";
                    lista_dpi += "<td class='td_popup_data_ultima_scadenza' id='td_data_ultima_scadenza_" + data[i].id + "'>" + data[i].data_ultima_scadenza + "</td>";

                    if (jQuery.inArray(data[i].id, prodotti_righe) == -1) {
                        lista_dpi += "<td class='td_popup_quantita_prodotti' id='td_quantita_" + data[i].id + "'>";
                        if (jQuery.inArray(data[i].id, prodotti_da_aggiungere) == -1) {
                            lista_dpi += "<input type='number' style='text-align:right;' class='input_quantita_carico input_text' id='input_quantita_" + data[i].id + "' min='1' step='1'/></td>";
                        } 
                        else {
                            lista_dpi += "<input type='number' style='text-align:right;' class='input_quantita_carico input_text' id='input_quantita_" + data[i].id + "' value='" + quantita_prodotti_da_aggiungere[data[i].id] + "' min='1' step='1'/></td>";
                        }
                        lista_dpi += "<td class='td_popup_data_scadenza_prodotti' id='td_data_scadenza_" + data[i].id + "'>";
                        if (jQuery.inArray(data[i].id, prodotti_da_aggiungere) == -1) {
                            if(data[i].data_scadenza != "" && data[i].data_scadenza != undefined){
                                lista_dpi += "<input type='text' class='input_data_scadenza_carico input_text campo_data_datepicker' id='input_data_scadenza_" + data[i].id + "' value='" + data[i].data_scadenza_inv + "' readonly='true'></td>";
                            }
                            else{                            
                                lista_dpi += "<input type='text' class='input_data_scadenza_carico input_text campo_data_datepicker' id='input_data_scadenza_" + data[i].id + "' readonly='true'></td>";
                            }
                        } 
                        else {
                            if(data_scadenza_prodotti_da_aggiungere[data[i].id] != "" && data_scadenza_prodotti_da_aggiungere[data[i].id] != undefined){
                                lista_dpi += "<input type='text' class='input_data_scadenza_carico input_text campo_data_datepicker' id='input_data_scadenza_" + data[i].id + "' value='" + data_scadenza_prodotti_da_aggiungere[data[i].id] + "' readonly='true'></td>";
                            }
                            else{
                                lista_dpi += "<input type='text' class='input_data_scadenza_carico input_text campo_data_datepicker' id='input_data_scadenza_" + data[i].id + "' readonly='true'></td>";
                            }
                        }
                        lista_dpi += "</tr>";
                    } 
                    else {
                        lista_dpi += "<td colspan='2' style='text-align:center; color:red;'>-- Gia' inserito --</td></tr>";
                    }
                }
            } 
            else {
                lista_dpi += "<tr><td colspan='7' style='text-align:center;'><em>Nessun DPI disponibile</em></td></tr>";
            }
            $('#body_tabella_prodotti').empty();
            $('#body_tabella_prodotti').append(lista_dpi);

            inizializzazioneBootstrap(); /* kpro@bid090520171000 */

            $("#search_codice_prodotto").keypress(function(e) {
                if (e.which == 13) {
                    caricaPopupTuttiProdotti($("#search_codice_prodotto").val(), "");
                }
            });

            $("#search_nome_prodotto").keypress(function(e) {
                if (e.which == 13) {
                    caricaPopupTuttiProdotti("", $("#search_nome_prodotto").val());
                }
            });

            $(".input_data_scadenza_carico").change(function() {
                variazione_carico = true;
                var id_tr_aggiungi_prodotto = $(this).prop("id");
                var id_prodotto_da_aggiungere = id_tr_aggiungi_prodotto.substring(20);
                
                aggiungiProdottiDaDataScadenza(id_prodotto_da_aggiungere, 'input_data_scadenza_');
            });

            $(".input_data_scadenza_carico").keyup(function() {
                variazione_carico = true;
                var id_tr_aggiungi_prodotto = $(this).prop("id");
                var id_prodotto_da_aggiungere = id_tr_aggiungi_prodotto.substring(20);
                
                aggiungiProdottiDaDataScadenza(id_prodotto_da_aggiungere, 'input_data_scadenza_');
            });

            $(".input_quantita_carico").change(function() {
                variazione_carico = true;
                var id_tr_aggiungi_prodotto = $(this).prop("id");
                var id_prodotto_da_aggiungere = id_tr_aggiungi_prodotto.substring(15);
                
                aggiungiProdottiDaQuantita(id_prodotto_da_aggiungere, 'input_quantita_');
                aggiungiProdottiDaDataScadenza(id_prodotto_da_aggiungere, 'input_data_scadenza_');
            });

            $(".input_quantita_carico").keyup(function() {
                variazione_carico = true;
                var id_tr_aggiungi_prodotto = $(this).prop("id");
                var id_prodotto_da_aggiungere = id_tr_aggiungi_prodotto.substring(15);
                
                aggiungiProdottiDaQuantita(id_prodotto_da_aggiungere, 'input_quantita_');
                aggiungiProdottiDaDataScadenza(id_prodotto_da_aggiungere, 'input_data_scadenza_');
            });

            $("#bottone_carrello_prodotti").click(function() {
                caricaDatiRigheMagazzino(record);
                $('#popup_aggiungi_riga').closeModal();
            });
        },
        fail: function() {
            console.log("Errore nel caricare popup aggiungi riga");
        }
    });
}

function caricaPopupProdottiMansione(codice, nome) {
    $('#body_tabella_prodotti_mansione').empty();

    var dati = {
        codice: codice,
        nome: nome,
        risorsa: id_risorsa_selezionata,
        data_consegna: testata.data
    };
    //console.log(dati);
    $.ajax({
        url: "caricaPopupProdottiMansione.php",
        dataType: 'json',
        data: dati,
        async: true,
        success: function(data) {
            //console.log(data);
            var lista_dpi = "";
            if (data.length > 0) {
                for (var i = 0; i < data.length; i++) {
                    lista_dpi += "<tr class='tr_prodotti' id='tr_prodotto_mans_" + data[i].id + "'>";

                    lista_dpi += "<td class='td_popup_immagine_prodotti' id='td_immagine_mans_" + data[i].id + "'>";
                    if (data[i].id_immagine != 0 && data[i].id_immagine != '0') {
                        lista_dpi += "<img style='max-height:40px; max-width: 60px;' src='" + indirizzo_crm + "/" + data[i].path_immagine + data[i].id_immagine + "_" + data[i].nome_immagine + "'>";
                    } else {
                        lista_dpi += "<i class='material-icons center md-24'>image</i>";
                    }
                    lista_dpi += "</td>";
                    lista_dpi += "<td class='td_popup_codice_prodotti' id='td_codice_mans_" + data[i].id + "'>" + data[i].codice + "</td>";
                    lista_dpi += "<td class='td_popup_nome_prodotti' id='td_nome_mans_" + data[i].id + "'>" + data[i].nome + "</td>";
                    lista_dpi += "<td class='td_popup_data_ultima_consegna' id='td_data_ultima_consegna_mans_" + data[i].id + "'>" + data[i].data_ultima_consegna + "</td>";
                    lista_dpi += "<td class='td_popup_data_ultima_scadenza' id='td_data_ultima_scadenza_mans_" + data[i].id + "'>" + data[i].data_ultima_scadenza + "</td>";

                    if (jQuery.inArray(data[i].id, prodotti_righe) == -1) {
                        lista_dpi += "<td class='td_popup_quantita_prodotti' id='td_quantita_mans_" + data[i].id + "'>";
                        if (jQuery.inArray(data[i].id, prodotti_da_aggiungere) == -1) {
                            lista_dpi += "<input type='number' style='text-align:right;' class='input_quantita_carico input_text' id='input_quantita_mans_" + data[i].id + "' min='1' step='1'/></td>";
                        } 
                        else {
                            lista_dpi += "<input type='number' style='text-align:right;' class='input_quantita_carico input_text' id='input_quantita_mans_" + data[i].id + "' value='" + quantita_prodotti_da_aggiungere[data[i].id] + "' min='1' step='1'/></td>";
                        }
                        lista_dpi += "<td class='td_popup_data_scadenza_prodotti' id='td_data_scadenza_mans_" + data[i].id + "'>";
                        if (jQuery.inArray(data[i].id, prodotti_da_aggiungere) == -1) {
                            if(data[i].data_scadenza != "" && data[i].data_scadenza != undefined){
                                lista_dpi += "<input type='text' class='input_data_scadenza_carico input_text campo_data_datepicker' id='input_data_scadenza_mans_" + data[i].id + "' value='" + data[i].data_scadenza_inv + "' readonly='true'></td>";
                            }
                            else{                            
                                lista_dpi += "<input type='text' class='input_data_scadenza_carico input_text campo_data_datepicker' id='input_data_scadenza_mans_" + data[i].id + "' readonly='true'></td>";
                            }
                        } 
                        else {
                            if(data_scadenza_prodotti_da_aggiungere[data[i].id] != "" && data_scadenza_prodotti_da_aggiungere[data[i].id] != undefined){
                                lista_dpi += "<input type='text' class='input_data_scadenza_carico input_text campo_data_datepicker' id='input_data_scadenza_mans_" + data[i].id + "' value='" + data_scadenza_prodotti_da_aggiungere[data[i].id] + "' readonly='true'></td>";
                            }
                            else{
                                lista_dpi += "<input type='text' class='input_data_scadenza_carico input_text campo_data_datepicker' id='input_data_scadenza_mans_" + data[i].id + "' readonly='true'></td>";
                            }
                        }
                        lista_dpi += "</tr>";
                    } 
                    else {
                        lista_dpi += "<td colspan='2' style='text-align:center; color:red;'>-- Gia' inserito --</td></tr>";
                    }
                }
            } 
            else {
                lista_dpi += "<tr><td colspan='7' style='text-align:center;'><em>Nessun DPI disponibile</em></td></tr>";
            }
            $('#body_tabella_prodotti_mansione').empty();
            $('#body_tabella_prodotti_mansione').append(lista_dpi);

            inizializzazioneBootstrap(); /* kpro@bid090520171000 */

            $("#search_codice_prodotto_mans").keypress(function(e) {
                if (e.which == 13) {
                    caricaPopupProdottiMansione($("#search_codice_prodotto_mans").val(), "");
                }
            });

            $("#search_nome_prodotto_mans").keypress(function(e) {
                if (e.which == 13) {
                    caricaPopupProdottiMansione("", $("#search_nome_prodotto_mans").val());
                }
            });

            $(".input_data_scadenza_carico").change(function() {
                variazione_carico = true;
                var id_tr_aggiungi_prodotto = $(this).prop("id");
                var id_prodotto_da_aggiungere = id_tr_aggiungi_prodotto.substring(25);
                
                aggiungiProdottiDaDataScadenza(id_prodotto_da_aggiungere, 'input_data_scadenza_mans_');
            });

            $(".input_data_scadenza_carico").keyup(function() {
                variazione_carico = true;
                var id_tr_aggiungi_prodotto = $(this).prop("id");
                var id_prodotto_da_aggiungere = id_tr_aggiungi_prodotto.substring(25);
                
                aggiungiProdottiDaDataScadenza(id_prodotto_da_aggiungere, 'input_data_scadenza_mans_');
            });

            $(".input_quantita_carico").change(function() {
                variazione_carico = true;
                var id_tr_aggiungi_prodotto = $(this).prop("id");
                var id_prodotto_da_aggiungere = id_tr_aggiungi_prodotto.substring(20);
                
                aggiungiProdottiDaQuantita(id_prodotto_da_aggiungere, 'input_quantita_mans_');
                aggiungiProdottiDaDataScadenza(id_prodotto_da_aggiungere, 'input_data_scadenza_mans_');
            });

            $(".input_quantita_carico").keyup(function() {
                variazione_carico = true;
                var id_tr_aggiungi_prodotto = $(this).prop("id");
                var id_prodotto_da_aggiungere = id_tr_aggiungi_prodotto.substring(20);
                
                aggiungiProdottiDaQuantita(id_prodotto_da_aggiungere, 'input_quantita_mans_');
                aggiungiProdottiDaDataScadenza(id_prodotto_da_aggiungere, 'input_data_scadenza_mans_');
            });

            $("#bottone_carrello_prodotti").click(function() {
                caricaDatiRigheMagazzino(record);
                $('#popup_aggiungi_riga').closeModal();
            });
        },
        fail: function() {
            console.log("Errore nel caricare popup aggiungi riga");
        }
    });
}

function aggiungiProdottiDaQuantita(id_prodotto_da_aggiungere, prefisso){    
    var quantita_selezionata_prodotto_da_aggiungere = $("#"+ prefisso + id_prodotto_da_aggiungere).val();
    if (prodotti_da_aggiungere.length <= 0) {
        if (quantita_selezionata_prodotto_da_aggiungere != "" && quantita_selezionata_prodotto_da_aggiungere > 0) {
            prodotti_da_aggiungere.push(id_prodotto_da_aggiungere);
            quantita_prodotti_da_aggiungere[id_prodotto_da_aggiungere] = quantita_selezionata_prodotto_da_aggiungere;
        }
    } else {
        if (jQuery.inArray(id_prodotto_da_aggiungere, prodotti_da_aggiungere) == -1) {
            if (quantita_selezionata_prodotto_da_aggiungere != "" && quantita_selezionata_prodotto_da_aggiungere > 0) {
                prodotti_da_aggiungere.push(id_prodotto_da_aggiungere);
                quantita_prodotti_da_aggiungere[id_prodotto_da_aggiungere] = quantita_selezionata_prodotto_da_aggiungere;
            }
        } else {
            if (quantita_selezionata_prodotto_da_aggiungere != "" && quantita_selezionata_prodotto_da_aggiungere > 0) {
                quantita_prodotti_da_aggiungere[id_prodotto_da_aggiungere] = quantita_selezionata_prodotto_da_aggiungere;
            } else {
                var pos = prodotti_da_aggiungere.indexOf(id_prodotto_da_aggiungere);

                if (pos > -1) {
                    prodotti_da_aggiungere.splice(pos, 1);
                    delete quantita_prodotti_da_aggiungere[id_prodotto_da_aggiungere];
                }
            }
        }
    }
}

function aggiungiProdottiDaDataScadenza(id_prodotto_da_aggiungere, prefisso){
    var data_scadenza_selezionata_prodotto_da_aggiungere = $("#" + prefisso + id_prodotto_da_aggiungere).val();
    if (prodotti_da_aggiungere.length <= 0) {
        if (data_scadenza_selezionata_prodotto_da_aggiungere != "") {
            prodotti_da_aggiungere.push(id_prodotto_da_aggiungere);
            data_scadenza_prodotti_da_aggiungere[id_prodotto_da_aggiungere] = data_scadenza_selezionata_prodotto_da_aggiungere;
        }
    } else {
        if (jQuery.inArray(id_prodotto_da_aggiungere, prodotti_da_aggiungere) == -1) {
            if (data_scadenza_selezionata_prodotto_da_aggiungere != "") {
                prodotti_da_aggiungere.push(id_prodotto_da_aggiungere);
                data_scadenza_prodotti_da_aggiungere[id_prodotto_da_aggiungere] = data_scadenza_selezionata_prodotto_da_aggiungere;
            }
        } else {
            if (data_scadenza_selezionata_prodotto_da_aggiungere != "") {
                data_scadenza_prodotti_da_aggiungere[id_prodotto_da_aggiungere] = data_scadenza_selezionata_prodotto_da_aggiungere;
            } else {
                var pos = prodotti_da_aggiungere.indexOf(id_prodotto_da_aggiungere);

                if (pos > -1) {
                    prodotti_da_aggiungere.splice(pos, 1);
                    delete data_scadenza_prodotti_da_aggiungere[id_prodotto_da_aggiungere];
                }
            }
        }
    }
}

function apriPopupAggiungiRiga() {
    variazione_carico = false;
    prodotti_da_aggiungere = [];

    caricaPopupTuttiProdotti("", "");

    caricaPopupProdottiMansione("", "");

    $("#bollino_prodotti_carrello").text("");
    if (prodotti_righe.length > 0) {
        $("#bollino_prodotti_carrello").text(prodotti_righe.length);
    }

    $('#popup_aggiungi_riga').openModal({
        dismissible: false
    });

    inizializzazioneMaterialize();

    $("#chiudi_popup_aggiungi_riga").click(function() {
        variazione_carico = false;
        prodotti_da_aggiungere = [];
        quantita_prodotti_da_aggiungere = [];
        data_scadenza_prodotti_da_aggiungere = [];

        caricaDatiRigheMagazzino(record);
        $('#popup_aggiungi_riga').closeModal();
    });

    $("#salva_popup_aggiungi_riga").click(function() {
        if(!salvando){
            salvando = true;
            if (record != "new") {
                if (variazione_carico) {
                    salvaRigheMovimentoMagazzino();
                    caricaDatiRigheMagazzino(record);
                }
            }
            variazione_carico = false;
            prodotti_da_aggiungere = [];
            quantita_prodotti_da_aggiungere = [];
            data_scadenza_prodotti_da_aggiungere = [];
            salvando = false;

            $('#popup_aggiungi_riga').closeModal();
        }
    });
}

function apriMenu() {

    jnav_div = $("#nav_div");
    jpanel = $(".panel");
    jtable_header = $("#table_header");

    jpanel.css("margin-left", "260px");
    jpanel.css("width", innerWidth - 280);
    jpanel.css("height", innerHeight - 80);
    jtable_header.css("margin-left", "260px");
    jtable_header.css("width", innerWidth - 280);
    jnav_div.css("height", innerHeight);

    jnav_div.show();

    menu_aperto = true;

}

function chiudiMenu() {

    jnav_div = $("#nav_div");
    jpanel = $(".panel");
    jtable_header = $("#table_header");

    jpanel.css("margin-left", "10px");
    jpanel.css("width", innerWidth - 30);
    jpanel.css("height", innerHeight - 80);
    jtable_header.css("margin-left", "10px");
    jtable_header.css("width", innerWidth - 30);

    jnav_div.hide();

    menu_aperto = false;
}

function reSize() {

    jnav_div = $("#nav_div");
    jpanel = $(".panel");
    jtable_header = $("#table_header");

    larghezza_schermo = innerWidth;
    altezza_schermo = innerHeight;

    dimensionaAreaFirma();

    jpanel.css("height", innerHeight - 80);

    if (menu_aperto === true) {
        jpanel.css("margin-left", "260px");
        jpanel.css("width", innerWidth - 280);
        jtable_header.css("margin-left", "260px");
        jtable_header.css("width", innerWidth - 280);
        jnav_div.css("height", innerHeight);
    } else {
        jpanel.css("margin-left", "10px");
        jpanel.css("width", innerWidth - 30);
        jtable_header.css("margin-left", "10px");
        jtable_header.css("width", innerWidth - 30);
    }
}

function selezioneMenu(elemento) {
    jpanel = $(".panel");
    jnav_div_list_li = $("#nav_div .list li");
    jtitolo_pagina = $("#titolo_pagina");

    pagina_selezionata = elemento.attr("id");
    pagina_selezionata = pagina_selezionata.substr(7, pagina_selezionata.length - 7);
    pagina_selezionata = $("#" + pagina_selezionata);

    jnav_div_list_li.css("background-color", "white");
    elemento.css("background-color", "#ccc");

    jpanel.hide();
    jtitolo_pagina.html(elemento.attr("name"));
    pagina_selezionata.show();
}

function inizializzazioneForm() {
    caricaDatiTestataMagazzino(record);
    caricaDatiRigheMagazzino(record);
    caricaListaDocumenti(record);
    jpercorso_file.val(record);

    if (record == "new") {
        apriPopupRisorse();
    }
}

function popolaPicking(campo, nome_campo_crm, valore_default) {

    var filtro = { nome_campo: nome_campo_crm };

    $.getJSON('PickingList.php', filtro, function(data) {
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
        $('select').material_select();
    });

}

function caricaDatiTestataMagazzino(record) {
    jform_numero.val("");
    jform_data.val("");
    jform_tipo_consegna.empty();
    jform_risorsa.val("");
    jform_azienda.val("");
    jform_stabilimento.val("");
    jdescrizione_consegna.val("");
    jform_stato.val("");

    if (record == "new") {
        jform_data.val(data_corrente_inv);
        jform_stato.val("--Nessuno--");
        popolaPicking(jform_tipo_consegna, "tipo_consegna", "Manuale");
    } else {
        var filtro = { record: record };
        /* kpro@bid110720181745 */
        $.ajax({
            url: 'caricaDatiTestataMagazzino.php',
            dataType: 'json',
            async: false,
            data: filtro,
            beforeSend: function() {

                jcaricamento.show();

            },
            success: function(data) {
                //console.log(data);
                if (data.length > 0) {
                    stato_consegna = data[0].stato;
                    jform_stato.val(stato_consegna);
                    jform_stato.trigger('autoresize');

                    jform_numero.val(data[0].numero);
                    jform_numero.trigger('autoresize');

                    jform_data.val(data[0].data_inv);
                    jform_data.trigger('autoresize');

                    popolaPicking(jform_tipo_consegna, "tipo_consegna", data[0].tipo_consegna);

                    jform_risorsa.val(data[0].nome_risorsa);
                    jform_risorsa.trigger('autoresize');
                    id_risorsa_selezionata = data[0].risorsa;

                    jform_azienda.val(data[0].nome_azienda);
                    jform_azienda.trigger('autoresize');
                    id_azienda_selezionata = data[0].azienda;

                    jform_stabilimento.val(data[0].nome_stabilimento);
                    jform_stabilimento.trigger('autoresize');
                    id_stabilimento_selezionato = data[0].stabilimento;

                    jdescrizione_consegna.val(data[0].descrizione);
                    jdescrizione_consegna.trigger('autoresize');

                    if (stato_consegna == "Confermata") {
                        jform_tipo_consegna.prop("disabled", true);
                        jform_risorsa.prop("readonly", true);
                        jdescrizione_consegna.prop("readonly", true);
                        jbottone_aggiungi_riga.hide();
                    }

                    testata = {
                        id: data[0].id,
                        numero: data[0].numero,
                        data: data[0].data,
                        data_inv: data[0].data_inv,
                        tipo_consegna: data[0].tipo_consegna,
                        risorsa: data[0].risorsa,
                        nome_risorsa: data[0].nome_risorsa,
                        azienda: data[0].azienda,
                        nome_azienda: data[0].nome_azienda,
                        stabilimento: data[0].stabilimento,
                        nome_stabilimento: data[0].nome_stabilimento,
                        descrizione: data[0].descrizione
                    };

                    Materialize.updateTextFields();
                    $('select').material_select();
                }
                jcaricamento.hide();
            },
            fail: function() {
                jcaricamento.hide();
                console.log("Errore nel caricare dati testata");
            }
        });
    }
}

function caricaDatiRigheMagazzino(record) {
    prodotti_righe = [];
    righe = [];
    var lista_righe_movimento = "";
    $("#bollino_numero_righe").text("");
    jcorpo_lista_righe_movimento.empty();

    if (record == "new") {
        lista_righe_movimento += "<tr><td colspan='6' style='text-align:center;><em>Nessuna riga movimento trovata!</em></td></tr>";
        jcorpo_lista_righe_movimento.append(lista_righe_movimento);
    } else {
        var filtro = { record: record };

        $.ajax({
            url: 'caricaDatiRigheMagazzino.php',
            dataType: 'json',
            async: true,
            data: filtro,
            beforeSend: function() {

                jcaricamento.show();

            },
            success: function(data) {
                //console.log(data);
                lista_righe_movimento = "";
                if (data.length > 0) {
                    $("#bollino_numero_righe").show();
                    $("#bollino_numero_righe").text("");
                    $("#bollino_numero_righe").text(data.length);
                    for (var i = 0; i < data.length; i++) {
                        lista_righe_movimento += "<tr class='tr_righe_movimento' id='tr_riga_" + data[i].id + "'>";
                        lista_righe_movimento += "<td class='td_azioni_riga' id='td_azioni_" + data[i].id + "'>";
                        
                        if (stato_consegna != "Confermata") {
                            lista_righe_movimento += "<i id='modifica_riga_" + data[i].id + "' class='bottone_modifica_riga material-icons center md-36'>mode_edit</i>";
                            lista_righe_movimento += "<i id='elimina_riga_" + data[i].id + "' class='bottone_elimina_riga material-icons center md-36'>delete_forever</i>";
                            lista_righe_movimento += "<i id='visualizza_riga_" + data[i].id + "' class='bottone_visualizza_riga material-icons center md-36'>visibility</i></td>";
                        } else {
                            lista_righe_movimento += "<i id='visualizza_riga_" + data[i].id + "' class='bottone_visualizza_riga material-icons center md-36'>visibility</i></td>";
                        }
                        lista_righe_movimento += "</td>";
                        lista_righe_movimento += "<td class='td_immagine_prodotto' id='td_immagine_" + data[i].id + "'>";
                        if (data[i].id_immagine != 0 && data[i].id_immagine != '0') {
                            lista_righe_movimento += "<img style='max-height:40px; max-width: 60px;' src='" + indirizzo_crm + "/" + data[i].path_immagine + data[i].id_immagine + "_" + data[i].nome_immagine + "'>";
                        } else {
                            lista_righe_movimento += "<i class='material-icons center md-24'>image</i>";
                        }
                        lista_righe_movimento += "</td>";
                        lista_righe_movimento += "<td class='td_nome_prodotto' id='td_nome_" + data[i].id + "'>" + data[i].nome + "</td>";
                        lista_righe_movimento += "<td class='td_quantita_riga' id='td_quantita_" + data[i].id + "'>" + data[i].quantita + "</td>";
                        lista_righe_movimento += "<td class='td_data_scadenza' id='td_data_scadenza_" + data[i].id + "'>" + data[i].data_inv + "</td>";
                        lista_righe_movimento += "<td class='td_richiedente' id='td_richiedente_" + data[i].id + "'>" + data[i].nome_richiedente + "</td></tr>";

                        prodotti_righe.push(data[i].id_prodotto);
                        righe[data[i].id] = {
                            id: data[i].id,
                            numero: data[i].numero,
                            id_prodotto: data[i].id_prodotto,
                            nome: data[i].nome,
                            data_scadenza: data[i].data,
                            data_scadenza_inv: data[i].data_inv,
                            richiedente: data[i].richiedente,
                            nome_richiedente: data[i].nome_richiedente,
                            quantita: data[i].quantita
                        };
                    }
                } else {
                    $("#bollino_numero_righe").text("");
                    $("#bollino_numero_righe").hide();
                    lista_righe_movimento += "<tr><td colspan='6' style='text-align:center;'><em>Nessuna riga movimento trovata!</em></td></tr>";
                }
                jcorpo_lista_righe_movimento.empty();
                jcorpo_lista_righe_movimento.append(lista_righe_movimento);

                $(".bottone_modifica_riga").click(function() {
                    var id_bottone_selezionato = $(this).prop("id");
                    var id_selezionato = id_bottone_selezionato.substring(14);
                    apriPopupModificaRiga(id_selezionato);
                });

                $(".bottone_visualizza_riga").click(function() {
                    var id_bottone_selezionato = $(this).prop("id");
                    var id_selezionato = id_bottone_selezionato.substring(16);
                    apriPopupVisualizzaRiga(id_selezionato);
                });

                $(".bottone_elimina_riga").click(function() {
                    var id_bottone_selezionato = $(this).prop("id");
                    var id_selezionato = id_bottone_selezionato.substring(13);
                    var r = confirm("Sicuro?");
                    if (r == true) {
                        eliminaRiga(id_selezionato);
                    }
                });
                jcaricamento.hide();
            },
            fail: function() {
                jcaricamento.hide();
                console.log("Errore nel caricare dati righe");
            }
        });
    }
}

/* FIRMA */

/*function apriPopupFineConsegna() {
    inizializza_firma();

    $('#popup_fine_consegna').openModal({
        dismissible: false
    });

    $('#chiudi_popup_fine_consegna').click(function() {
        $('#popup_fine_consegna').closeModal();
    });

    $('#salva_popup_fine_consegna').click(function() {
        salvaFirma();
    });

    jbottone_pulisci_firma.click(function() {
        jarea_firma.data('jqScribble').clear();
    });

    jbottone_anteprima_pdf.click(function() {
        salvaAnteprimaFirma();
    });
}*/

function apriPopupFineConsegna() {
    inizializza_firma();

    $('#popup_fine_consegna').openModal({
        dismissible: false
    });

    $('#chiudi_popup_fine_consegna').click(function() {
        $('#popup_fine_consegna').closeModal();
    });

    $('#salva_popup_fine_consegna').click(function() {
        salvaFirma();
    });

    jbottone_pulisci_firma.click(function() {
        jarea_firma.data('jqScribble').clear();
    });
}

function inizializza_firma() {

    larghezza_schermo = innerWidth;
    altezza_schermo = innerHeight;

    jarea_firma_div = $("#area_firma");
    jbottone_pulisci_firma = $("#bottone_pulisci_firma");
    jbottone_anteprima_pdf = $("#bottone_anteprima_pdf");
    jbottone_tastiera = $("#bottone_tastiera");

    jarea_firma = $("#area_firma").jqScribble();

    dimensionaAreaFirma();

}

function dimensionaAreaFirma() {
    var altezza_area_firma = $("#popup_fine_consegna").height() - ($("#popup_fine_consegna").height() / 3);
    var larghezza_area_firma = larghezza_schermo - (larghezza_schermo / 2);

    var settings_firma = {
        width: larghezza_area_firma,
        height: altezza_area_firma
    };

    if(jarea_firma){
        jarea_firma.data("jqScribble").update(settings_firma);
    }
}

function checkFirma() {

    if (jarea_firma.data("jqScribble").blank) {
        alert('Apporre la firma');
        return false;
    }

    return true;

}

function salvaFirma() {
    if (!salvando_firma) {
        salvando_firma = true;
        if (checkFirma()) {

            jarea_firma.data("jqScribble").save(function(imageData) {

                var dati_firma = {
                    imagedata: imageData,
                    record: record,
                    risposta: "ok"
                };

                $.post('SalvaFirma.php', dati_firma, function(response) {
                    if (response == 'ok') {
                        SalvaPDFConsegnaDPI();
                    } else {
                        alert("E' necessario firmare!");
                    }
                    salvando_firma = false;
                });

            });
        } else {
            salvando_firma = false;
        }
    }
}

function SalvaPDFConsegnaDPI() {
    var dati_firma = {
        record: record
    };

    $.post('SalvaPDFConsegnaDPI.php', dati_firma, function(response) {
        if (response == 'ok') {
            window.open(indirizzo_crm + "/index.php?action=DetailView&module=ConsegnaDPI&record=" + record, "_self");
        } else {
            //console.log(response);
            alert("Errore");
        }
    });
}

function salvaAnteprimaFirma() {

    if (!jarea_firma.data("jqScribble").blank) {

        jarea_firma.data("jqScribble").save(function(imageData) {

            var dati_firma = {
                imagedata: imageData,
                record: record,
                risposta: "path"
            };

            $.post('SalvaFirma.php', dati_firma, function(response) {

                anteprimaPdf();

            });

        });

    } else {

        anteprimaPdf();

    }

}

function anteprimaPdf() {

    var dati_firma = {
        record: record
    };

    $.ajax({
        url: 'AnteprimaPDF.php',
        dataType: 'json',
        async: false,
        data: dati_firma,
        success: function(data) {
            if (data.length > 0) {
                $("#popup_anteprima_pdf .modal-content").empty();
                $("#popup_anteprima_pdf .modal-content").html("<h2>Template Documento</h2>" + data[0].result);

                apriPopupAnteprimaPDF();
            }

        },
        fail: function() {
            console.error("Errore nella creazione dell'anteprima del pdf");
        }
    });

}

function apriPopupAnteprimaPDF() {
    $('#popup_anteprima_pdf').openModal({
        dismissible: false
    });

    $('#chiudi_popup_anteprima_pdf').click(function() {
        $('#popup_anteprima_pdf').closeModal();
    });

    $('#firma_popup_anteprima_pdf').click(function() {
        $('#popup_anteprima_pdf').closeModal();

        apriPopupFineConsegna();
    });
}

/*function apriPopupAnteprimaPDF() {
    $('#popup_fine_consegna').closeModal();

    $('#popup_anteprima_pdf').openModal({
        dismissible: false
    });

    $('#chiudi_popup_anteprima_pdf').click(function() {
        $('#popup_anteprima_pdf').closeModal();

        apriPopupFineConsegna();
    });
}*/

/* DOCUMENTI */

function apriPopupDocumento() {

    jfile.val("");
    jform_titolo_documento.val("");
    jnome_documento_temp.val("");

    $('#popup_documento').openModal();

    $('#salva_popup_documento').click(function() {
        jnome_documento_temp.val(jform_titolo_documento.val());
        uploadFile();
    });
}

function uploadFile() {

    var form = $("#form_documenti");
    var formData = new FormData(form[0]);
    formData.append('text', form[1]);

    $.ajax({
        url: "UploadFileMagazzino.php",
        type: 'POST',
        data: formData,
        async: true,
        cache: false,
        contentType: false,
        processData: false,
        xhr: function() {
            var myXhr = $.ajaxSettings.xhr();
            if (myXhr.upload) {
                myXhr.upload.addEventListener('progress', onUploadProgress, false);
            }
            return myXhr;
        },
        success: function(data) {
            //console.log(data[0].return);
            jprogress_status.hide();
            caricaListaDocumenti(record);
            $('#popup_documento').closeModal();
        },
        fail: function() {
            //console.log(data[0].return);
            jprogress_status.hide();
            alert("Errore nel caricamento del file!");
            caricaListaDocumenti(record);
            $('#popup_documento').closeModal();
        }
    });

}

function onUploadProgress(event) {

    if (event.lengthComputable) {
        jprogress_status.show();
        var max = event.total;
        var current = event.loaded;

        var percentComplete = (current * 100) / max;
        percentComplete = parseInt(percentComplete);
        //console.log(percentComplete);

        jprogress_status.html(percentComplete + '%');
    }

}

function caricaListaDocumenti(ticket) {

    var filtro_dati_documenti = {
        record: ticket
    };

    $.getJSON('caricaListaDocumenti.php', filtro_dati_documenti, function(data) {
        var lista_documenti = '';

        if (data.length > 0) {
            jbollino_numero_documenti.show();
            jbollino_numero_documenti.text("");
            jbollino_numero_documenti.text(data.length);
            for (var i = 0; i < data.length; i++) {
                lista_documenti += "<tr class='td_nome_documento' id='tr_doc_" + data[i].attachmentsid + "'>";
                lista_documenti += "<td>" + data[i].title + "</td>";
                lista_documenti += "<td class='td_azioni_documento'><i id='down_" + data[i].attachmentsid + "' class='bottone_download_doc material-icons md-36 center'>file_download</i></td>";
                lista_documenti += "</tr>";

                documento[data[i].attachmentsid] = {
                    attachmentsid: data[i].attachmentsid,
                    name: data[i].name,
                    path: data[i].path,
                    title: data[i].title,
                    notesid: data[i].notesid
                };

            }
        } else {
            jbollino_numero_documenti.text("");
            jbollino_numero_documenti.hide();
            lista_documenti += "<tr><td colspan='2' style='text-align:center;'><em>Nessun documento trovato!</em></td></tr>";
        }

        jcorpo_lista_documenti.empty();
        jcorpo_lista_documenti.append(lista_documenti);

        jbottone_download_doc = $(".bottone_download_doc");

        jbottone_download_doc.click(function() {
            documento_selezionato = $(this).attr("id");
            documento_selezionato = documento_selezionato.substr(5, documento_selezionato.length - 5);

            document.location.href = indirizzo_crm + "/modules/SproCore/CustomPortals/KpPortaleConsegnaDPI/DownloadFileMagazzino.php?record=" + ticket + "&entityid=" + documento[documento_selezionato].notesid + "&fileid=" + documento[documento_selezionato].attachmentsid;
        });

    }).
    fail(function() {
        console.log("Errore caricamento lista Documenti!");
    });

}

function generaPdfTemporaneo(record) {

    var dati = {
        record: record
    };

    jQuery.ajax({
        url: 'GeneraPdfTemporaneo.php',
        async: true,
        data: dati,
        beforeSend: function() {

            jcaricamento.show();

        },
        success: function(data) {

            //console.table(data);

            jcaricamento.hide();

            verificaCorrettaGenerazionePdfTemporaneo(record);

        },
        fail: function() {
            console.error("Errore nella generazione del pdf");
            jcaricamento.hide();
        }
    });

}

function verificaCorrettaGenerazionePdfTemporaneo(record) {

    var dati = {
        record: record
    };

    jQuery.ajax({
        url: 'VerificaPresenzaPdfTemporaneo.php',
        dataType: 'json',
        async: true,
        data: dati,
        beforeSend: function() {

            jcaricamento.show();

        },
        success: function(data) {

            //console.table(data);

            if (data[0].result == "ok") {

                window.open("pagina_firma_grafometrica.php?crmid=" + record, "_self");

            } else {

                Materialize.toast("Errore nella generazione del PDF; Ripetere l'operazione!", 4000);

            }

            jcaricamento.hide();

        },
        fail: function() {
            console.error("Errore nella generazione del pdf");
            jcaricamento.hide();
        }
    });

}
