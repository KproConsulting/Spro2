/* kpro@tom28112017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */

var larghezza_schermo;
var altezza_schermo;
var in_salvataggio = false;


var jtabella_lista_impianti;
var jbottone_selezione;
var jsearch_matricola;
var jsearch_nome_impianto;
var jsearch_azienda;
var jsearch_stabilimento;

var impianto = [];

var filtro = {};

window.addEventListener("load", function() {

    inizializza();

    inizializzaExtra();

});

function reSize() {

    larghezza_schermo = window.innerWidth;
    altezza_schermo = window.innerHeight;

}

function inizializza() {

    reSize();

    window.addEventListener('resize', function() {
        reSize();
    }, false);

}

function chiudiPopUp() {

    closePopup();
    //parent.location.reload();

}

function inizializzaExtra() {

    jtabella_lista_impianti = jQuery("#tabella_lista_impianti");
    jsearch_matricola = jQuery("#search_matricola");
    jsearch_nome_impianto = jQuery("#search_nome_impianto");
    jsearch_azienda = jQuery("#search_azienda");
    jsearch_stabilimento = jQuery("#search_stabilimento");

    filtro = {
        matricola: "",
        nome_impianto: "",
        azienda: "",
        stabilimento: ""
    };

    getListaImpianti(filtro);

    jsearch_matricola.keyup(function(ev) {

        var matricola_temp = HtmlEntities.kpencode(jsearch_matricola.val());

        var code = ev.which;
        if (code == 13 || matricola_temp == "") {

            filtro.matricola = matricola_temp;
            getListaImpianti(filtro);

        }

    });

    jsearch_nome_impianto.keyup(function(ev) {

        var nome_impianto_temp = HtmlEntities.kpencode(jsearch_nome_impianto.val());

        var code = ev.which;
        if (code == 13 || nome_impianto_temp == "") {

            filtro.nome_impianto = nome_impianto_temp;
            getListaImpianti(filtro);

        }

    });

    jsearch_azienda.keyup(function(ev) {

        var nome_azienda_temp = HtmlEntities.kpencode(jsearch_azienda.val());

        var code = ev.which;
        if (code == 13 || nome_azienda_temp == "") {

            filtro.azienda = nome_azienda_temp;
            getListaImpianti(filtro);

        }

    });

    jsearch_stabilimento.keyup(function(ev) {

        var nome_stabilimento_temp = HtmlEntities.kpencode(jsearch_stabilimento.val());

        var code = ev.which;
        if (code == 13 || nome_stabilimento_temp == "") {

            filtro.stabilimento = nome_stabilimento_temp;
            getListaImpianti(filtro);

        }

    });

}

function getListaImpianti(filtro) {

    jQuery.ajax({
        url: 'modules/SproCore/CustomViews/kpPopupRiportaMinaccePrivacyImpianto/GetListaImpianti.php',
        dataType: 'json',
        async: true,
        data: filtro,
        beforeSend: function() {

            //jcaricamento.show();

        },
        success: function(data) {

            var lista_temp = "";

            //console.table(data);
            if (data.length > 0) {

                for (var i = 0; i < data.length; i++) {

                    if (data[i].id != record_id) {

                        lista_temp += "<tr>";

                        lista_temp += "<td style='text-align: center;'><a href='#' id='" + data[i].id + "' class='bottone_selezione' ><span class='glyphicon glyphicon-plus'></span></a></td>";

                        lista_temp += "<td>" + data[i].matricola + "</td>";

                        lista_temp += "<td>" + data[i].nome + "</td>";

                        lista_temp += "<td>" + data[i].nome_azienda + "</td>";

                        lista_temp += "<td>" + data[i].nome_stabilimento + "</td>";

                        lista_temp += "<td style='text-align: right;'>" + data[i].numero_minacce + "</td>";

                        lista_temp += "</tr>";

                        impianto[data[i].id] = {
                            matricola: data[i].matricola,
                            nome: data[i].nome,
                            nome_azienda: data[i].nome_azienda,
                            nome_stabilimento: data[i].nome_stabilimento
                        };

                    }

                }

            } else {

                lista_temp += "<tr><td colspan='10' style='text-align: center;'><em>Nessun impianto trovato!</em></td></tr>";

            }

            jtabella_lista_impianti.empty();
            jtabella_lista_impianti.append(lista_temp);

            jbottone_selezione = jQuery(".bottone_selezione");
            jbottone_selezione.click(function() {

                if( !in_salvataggio ){

                    var elemento_selezionato_temp = jQuery(this).prop("id");

                    var result_confirm = confirm("Sei sicuro di voler copiare le minacce dell'impianto " + impianto[elemento_selezionato_temp].nome + "?");

                    if (result_confirm == true) {

                        in_salvataggio = true;
                        setMinacceImpianto(elemento_selezionato_temp);

                    }

                }

            });


        },
        fail: function() {

            console.error("Errore");

        }
    });

}

function setMinacceImpianto(id) {

    var dati = {
        record: record_id,
        copia_da: id
    };

    jQuery.ajax({
        url: 'modules/SproCore/CustomViews/kpPopupRiportaMinaccePrivacyImpianto/SetMinaccePrivacyImpianto.php',
        dataType: 'json',
        async: false,
        data: dati,
        beforeSend: function() {



        },
        success: function(data) {

            //chiudiPopup();
            parent.location.reload();

        },
        fail: function() {

            console.error("Errore");

        }
    });

}