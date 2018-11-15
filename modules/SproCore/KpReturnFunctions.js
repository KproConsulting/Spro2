function return_scadenza_da_servizio(entity_id, entity_name, field, validita_documento) {

    var formName = getReturnFormName();
    var form = getReturnForm(formName);
    form.servizio_display.value = entity_name;
    form.servizio.value = entity_id;
    disableReferenceField(form.servizio_display, form.servizio);
    form.data_scadenza.value = validita_documento;

}

function return_dati_prodotto_riga(entity_id, entity_name, field, unita_di_misura, costo_unitario, prezzo_unitario) {

    var formName = getReturnFormName();
    var form = getReturnForm(formName);
    form.kp_grl_prodotto_display.value = entity_name;
    form.kp_grl_prodotto.value = entity_id;
    disableReferenceField(form.kp_grl_prodotto_display, form.kp_grl_prodotto);
    form.usageunit.value = unita_di_misura;
    form.kp_grl_costo_unit.value = costo_unitario;
    form.kp_grl_prezzo_unit.value = prezzo_unitario;

}

function return_codice_prodotto_da_prodotto(entity_id, entity_name, field, codice_prodotto) {

    var formName = getReturnFormName();
    var form = getReturnForm(formName);
    form.kp_prodotto_display.value = entity_name;
    form.kp_prodotto.value = entity_id;
    disableReferenceField(form.kp_prodotto_display, form.kp_prodotto);
    form.kp_codice_prodotto.value = codice_prodotto;

}

function return_codice_fiscale_da_risorsa(entity_id, entity_name, field, codice_fiscale) {

    var formName = getReturnFormName();
    var form = getReturnForm(formName);
    form.risorsa_display.value = entity_name;
    form.risorsa.value = entity_id;
    disableReferenceField(form.risorsa_display, form.risorsa);
    form.kp_codice_fiscale.value = codice_fiscale;

}

function set_return_account_to_quote(entity_id, entity_name, field, bill_str, ship_str, bill_city, ship_city, bill_state, ship_state, bill_code, ship_code, bill_country, ship_country, mod_pagamento, nome_mod_pag, business_unit, nome_business_unit, agente, nome_agente, tasse, listino, nome_listino) {

    var formName = getReturnFormName();
    var form = getReturnForm(formName);
    form.account_id_display.value = entity_name;
    form.account_id.value = entity_id;
    disableReferenceField(form.account_id_display, form.account_id);
    /* kpro@bid24112017 */
    if (business_unit != null && business_unit != "" && business_unit != 0) {
        form.kp_business_unit_display.value = nome_business_unit;
        form.kp_business_unit.value = business_unit;
    }

    if (agente != null && agente != "" && agente != 0) {
        form.kp_agente_display.value = nome_agente;
        form.kp_agente.value = agente;
    }
    /* kpro@bid24112017 end */
    if (mod_pagamento != null && mod_pagamento != "" && mod_pagamento != 0) {
        form.mod_pagamento_display.value = nome_mod_pag;
        form.mod_pagamento.value = mod_pagamento;
    }

    form.bill_street.value = bill_str;
    form.ship_street.value = ship_str;
    form.bill_city.value = bill_city;
    form.ship_city.value = ship_city;
    form.bill_state.value = bill_state;
    form.ship_state.value = ship_state;
    form.bill_code.value = bill_code;
    form.ship_code.value = ship_code;
    form.bill_country.value = bill_country;
    form.ship_country.value = ship_country;
    form.kp_tasse.value = tasse;

    /* kpro@tom130920181516 */
    if( form.kp_tasse ){
        if (listino != null && listino != "" && listino != 0) {
            form.kp_listino_display.value = nome_listino;
            form.kp_listino.value = listino;
        }
    }
    /* kpro@tom130920181516 end */

}

function set_return_account_to_sales_order(entity_id, entity_name, field, bill_str, ship_str, bill_city, ship_city, bill_state, ship_state, bill_code, ship_code, bill_country, ship_country, mod_pagamento, nome_mod_pag, business_unit, nome_business_unit, agente, nome_agente, tasse, listino, nome_listino) {

    var formName = getReturnFormName();
    var form = getReturnForm(formName);
    form.account_id_display.value = entity_name;
    form.account_id.value = entity_id;
    disableReferenceField(form.account_id_display, form.account_id);
    /* kpro@bid24112017 */
    if (business_unit != null && business_unit != "" && business_unit != 0) {
        form.kp_business_unit_display.value = nome_business_unit;
        form.kp_business_unit.value = business_unit;
    }

    if (agente != null && agente != "" && agente != 0) {
        form.kp_agente_display.value = nome_agente;
        form.kp_agente.value = agente;
    }
    /* kpro@bid24112017 end */
    if (mod_pagamento != null && mod_pagamento != "" && mod_pagamento != 0) {
        form.mod_pagamento_display.value = nome_mod_pag;
        form.mod_pagamento.value = mod_pagamento;
    }

    form.bill_street.value = bill_str;
    form.ship_street.value = ship_str;
    form.bill_city.value = bill_city;
    form.ship_city.value = ship_city;
    form.bill_state.value = bill_state;
    form.ship_state.value = ship_state;
    form.bill_code.value = bill_code;
    form.ship_code.value = ship_code;
    form.bill_country.value = bill_country;
    form.ship_country.value = ship_country;
    form.kp_tasse.value = tasse;

    /* kpro@tom130920181516 */
    if( form.kp_tasse ){
        if (listino != null && listino != "" && listino != 0) {
            form.kp_listino_display.value = nome_listino;
            form.kp_listino.value = listino;
        }
    }
    /* kpro@tom130920181516 end */

}
/* kpro@bid24112017 */
function set_return_account_to_potential(entity_id, entity_name, field, business_unit, nome_business_unit, agente, nome_agente) {

    var formName = getReturnFormName();
    var form = getReturnForm(formName);
    form.related_to_display.value = entity_name;
    form.related_to.value = entity_id;
    disableReferenceField(form.related_to_display, form.related_to);

    if (business_unit != null && business_unit != "" && business_unit != 0) {
        form.kp_business_unit_display.value = nome_business_unit;
        form.kp_business_unit.value = business_unit;
    }

    if (agente != null && agente != "" && agente != 0) {
        form.kp_agente_display.value = nome_agente;
        form.kp_agente.value = agente;
    }

}

function set_return_banca_to_salesorder(entity_id, entity_name, field, nome_banca, nome_agenzia) {

    var formName = getReturnFormName();
    var form = getReturnForm(formName);
    form.kp_conto_corrente_display.value = entity_name;
    form.kp_conto_corrente.value = entity_id;
    disableReferenceField(form.kp_conto_corrente_display, form.kp_conto_corrente);

    form.kp_banca_cliente.value = nome_banca + " " + nome_agenzia;

}

function set_return_banca_to_invoice(entity_id, entity_name, field, nome_banca, nome_agenzia) {

    var formName = getReturnFormName();
    var form = getReturnForm(formName);
    form.kp_conto_corrente_display.value = entity_name;
    form.kp_conto_corrente.value = entity_id;
    disableReferenceField(form.kp_conto_corrente_display, form.kp_conto_corrente);

    form.kp_banca_cliente.value = nome_banca + " " + nome_agenzia;

}

/* kpro@bid24112017 end */

function return_fornitore_into_tabella_provvigionale(recordid, value, target_fieldname, nome_fornitore) {

    var formName = getReturnFormName();
    var form = getReturnForm(formName);

    form.kp_fornitore_display.value = value;
    form.kp_fornitore.value = recordid;
    disableReferenceField(form.kp_fornitore_display, form.kp_fornitore);

    var soggetto_tabella_provvigionale = "Tab. Prov. " + nome_fornitore;

    if (form.kp_servizio.value != "" && form.kp_servizio.value != 0) {

        soggetto_tabella_provvigionale += " - Servizio: " + form.kp_servizio_display.value;

    }

    form.kp_soggetto.value = soggetto_tabella_provvigionale;

}

function return_servizio_into_tabella_provvigionale(recordid, value, target_fieldname, nome_servizio) {

    var formName = getReturnFormName();
    var form = getReturnForm(formName);

    form.kp_servizio_display.value = value;
    form.kp_servizio.value = recordid;
    disableReferenceField(form.kp_servizio_display, form.kp_servizio);

    var soggetto_tabella_provvigionale = "";

    if (form.kp_fornitore.value != "" && form.kp_fornitore.value != 0) {

        soggetto_tabella_provvigionale += "Tab. Prov. " + form.kp_fornitore_display.value;

    }

    soggetto_tabella_provvigionale += " - Servizio: " + nome_servizio;

    form.kp_soggetto.value = soggetto_tabella_provvigionale;

}

function return_domanda_into_domanda_questionario(recordid, value, target_fieldname, nome_domanda) {

    var formName = getReturnFormName();
    var form = getReturnForm(formName);

    form.kp_domanda_display.value = value;
    form.kp_domanda.value = recordid;
    disableReferenceField(form.kp_domanda_display, form.kp_domanda);

    var soggetto_domanda_questionario = "";

    if (form.kp_ordinamento.value != "" && form.kp_ordinamento.value != 0) {

        var ordinamento = form.kp_ordinamento.value;

        ordinamento = "0" + ordinamento;

        ordinamento = ordinamento.slice(-2);

        soggetto_domanda_questionario += ordinamento;

    }

    soggetto_domanda_questionario += " - " + nome_domanda;

    form.kp_soggetto.value = soggetto_domanda_questionario;

}

function set_return_service_to_helpdesk(entity_id, entity_name, field, nome_servizio, area_aziendale) {

    var formName = getReturnFormName();
    var form = getReturnForm(formName);
    form.servizio_display.value = entity_name;
    form.servizio.value = entity_id;
    disableReferenceField(form.servizio_display, form.servizio);

    if (form.ticket_title.value == '' || form.ticket_title.value == undefined) {
        form.ticket_title.value = nome_servizio;
    }
    form.area_aziendale.value = area_aziendale;

}

function set_return_dati_azienda_to_report_attivita(entity_id, entity_name, field, distanza, ore_viaggio, pedaggio, business_unit_id, business_unit_name) {

    var formName = getReturnFormName();
    var form = getReturnForm(formName);
    form.accountid_display.value = entity_name;
    form.accountid.value = entity_id;
    disableReferenceField(form.accountid_display, form.accountid);
    /* kpro@bid151120181550 */
    form.kp_business_unit_display.value = business_unit_name;
    form.kp_business_unit.value = business_unit_id;
    disableReferenceField(form.kp_business_unit_display, form.kp_business_unit);
    /* kpro@bid151120181550 end */
    form.spautostr.value = pedaggio;
    form.kmpercorsi.value = distanza;
    form.kp_ore_viaggio.value = ore_viaggio;

}

function set_return_dati_stabilimento_to_report_attivita(entity_id, entity_name, field, distanza, ore_viaggio, pedaggio) {

    var formName = getReturnFormName();
    var form = getReturnForm(formName);
    form.kp_stabilimento_display.value = entity_name;
    form.kp_stabilimento.value = entity_id;
    disableReferenceField(form.kp_stabilimento_display, form.kp_stabilimento);

    form.spautostr.value = pedaggio;
    form.kmpercorsi.value = distanza;
    form.kp_ore_viaggio.value = ore_viaggio;

}

function set_return_dati_commessa_to_report_attivita(entity_id, entity_name, field, business_unit, nome_business_unit, tipo_commessa) {

    var formName = getReturnFormName();
    var form = getReturnForm(formName);
    form.commessa_display.value = entity_name;
    form.commessa.value = entity_id;
    disableReferenceField(form.commessa_display, form.commessa);

    form.kp_business_unit_display.value = nome_business_unit;
    form.kp_business_unit.value = business_unit;
    disableReferenceField(form.kp_business_unit_display, form.kp_business_unit);

    if (tipo_commessa != '') {
        form.kp_tipo_attivita.value = tipo_commessa;
    }

    if (tipo_commessa == 2 || tipo_commessa == '2') {
        parent.document.EditView['kp_da_fatturare'].checked = true;
    } else {
        parent.document.EditView['kp_da_fatturare'].checked = false;
    }

}

function set_return_frequenza_tipo_corso(entity_id, entity_name, field, validita, nome_corso){

    var formName = getReturnFormName();
    var form = getReturnForm(formName);
    form.kp_tipo_corso_display.value = entity_name;
    form.kp_tipo_corso.value = entity_id;
    disableReferenceField(form.kp_tipo_corso_display, form.kp_tipo_corso);

    if(form.kp_nome_corso.value == '' || form.kp_nome_corso.value == 0){
        form.kp_nome_corso.value = nome_corso;
    }
    
    var data_formazione = form.kp_data_formazione.value;

    var dati = {
        data_formazione: data_formazione,
        validita: validita
    };
    
    jQuery.ajax({
        url: 'modules/SproCore/KpFormazione/CalcolaDataScadenzaCorso.php',
        dataType: 'json',
        async: true,
        data: dati,
        success: function(data) {
            //console.log(data);
            if(data.length > 0){
                form.kp_data_scad_for.value = data[0].data_scadenza_formazione_inv;
            }
        },
        fail: function() {
            console.error("Errore calcolo data scadenza");
        }
    });

}

function set_return_frequenza_tipo_documento(entity_id, entity_name, field, validita){

    var formName = getReturnFormName();
    var form = getReturnForm(formName);
    form.kp_tipo_documento_display.value = entity_name;
    form.kp_tipo_documento.value = entity_id;
    disableReferenceField(form.kp_tipo_documento_display, form.kp_tipo_documento);

    var data_documento = form.kp_data_documento.value;

    var dati = {
        data_documento: data_documento,
        validita: validita
    };
    
    jQuery.ajax({
        url: 'modules/SproCore/Documents/CalcolaDataScadenzaDocumento.php',
        dataType: 'json',
        async: true,
        data: dati,
        success: function(data) {
            //console.log(data);
            if(data.length > 0){
                form.data_scadenza.value = data[0].data_scadenza_documento_inv;
            }
        },
        fail: function() {
            console.error("Errore calcolo data scadenza");
        }
    });

}

function set_return_nome_tipo_documento(recordid, value, target_fieldname, nome_tipo_documento) {
    
    var formName = getReturnFormName();
    var form = getReturnForm(formName);

    form.kp_tipo_documento_display.value = value;
    form.kp_tipo_documento.value = recordid;
    disableReferenceField(form.kp_tipo_documento_display, form.kp_tipo_documento);

    var nome_tipo_documento_azienda = nome_tipo_documento;

    if (form.kp_stabilimento.value != "" && form.kp_stabilimento.value != 0) {

        nome_tipo_documento_azienda += " - " + form.kp_stabilimento_display.value;

    }

    form.kp_nome_tipo_doc_az.value = nome_tipo_documento_azienda;

}

function set_return_nome_stabilimento_tipo_documento(recordid, value, target_fieldname, nome_stabilimento, azienda, nome_azienda) {

    var formName = getReturnFormName();
    var form = getReturnForm(formName);

    form.kp_stabilimento_display.value = value;
    form.kp_stabilimento.value = recordid;
    disableReferenceField(form.kp_stabilimento_display, form.kp_stabilimento);

    form.kp_azienda_display.value = nome_azienda;
    form.kp_azienda.value = azienda;
    disableReferenceField(form.kp_azienda_display, form.kp_azienda);

    var nome_tipo_documento_azienda = "";

    if (form.kp_tipo_documento.value != "" && form.kp_tipo_documento.value != 0) {

        nome_tipo_documento_azienda += form.kp_tipo_documento_display.value;

    }

    nome_tipo_documento_azienda += " - " + nome_stabilimento;

    form.kp_nome_tipo_doc_az.value = nome_tipo_documento_azienda;

}

function set_return_nome_fornitore_tipo_documento_fornitore(recordid, value, target_fieldname, nome_fornitore) {
    
    var formName = getReturnFormName();
    var form = getReturnForm(formName);

    form.kp_fornitore_display.value = value;
    form.kp_fornitore.value = recordid;
    disableReferenceField(form.kp_fornitore_display, form.kp_fornitore);

    var nome_tipo_documento_fornitore = "";

    if (form.kp_tipo_documento.value != "" && form.kp_tipo_documento.value != 0) {

        nome_tipo_documento_fornitore += form.kp_tipo_documento_display.value;

    }

    nome_tipo_documento_fornitore += " - " + nome_fornitore;

    if (form.kp_risorsa_fornit.value != "" && form.kp_risorsa_fornit.value != 0) {

        nome_tipo_documento_fornitore += " (" + form.kp_risorsa_fornit_display.value + ")";

    }

    form.kp_nome_tipo_doc_fo.value = nome_tipo_documento_fornitore;

}

function set_return_nome_tipo_documento_tipo_documento_fornitore(recordid, value, target_fieldname, nome_tipo_documento) {
    
    var formName = getReturnFormName();
    var form = getReturnForm(formName);

    form.kp_tipo_documento_display.value = value;
    form.kp_tipo_documento.value = recordid;
    disableReferenceField(form.kp_tipo_documento_display, form.kp_tipo_documento);

    var nome_tipo_documento_fornitore = nome_tipo_documento;

    if (form.kp_fornitore.value != "" && form.kp_fornitore.value != 0) {

        nome_tipo_documento_fornitore += " - " + form.kp_fornitore_display.value;

    }

    if (form.kp_risorsa_fornit.value != "" && form.kp_risorsa_fornit.value != 0) {

        nome_tipo_documento_fornitore += " (" + form.kp_risorsa_fornit_display.value + ")";

    }

    form.kp_nome_tipo_doc_fo.value = nome_tipo_documento_fornitore;

}

function set_return_nome_risorsa_tipo_documento_fornitore(recordid, value, target_fieldname, nome_risorsa, fornitore, nome_fornitore) {
    
    var formName = getReturnFormName();
    var form = getReturnForm(formName);

    form.kp_risorsa_fornit_display.value = value;
    form.kp_risorsa_fornit.value = recordid;
    disableReferenceField(form.kp_risorsa_fornit_display, form.kp_risorsa_fornit);

    form.kp_fornitore_display.value = nome_fornitore;
    form.kp_fornitore.value = fornitore;
    disableReferenceField(form.kp_fornitore_display, form.kp_fornitore);

    var nome_tipo_documento_fornitore = "";

    if (form.kp_tipo_documento.value != "" && form.kp_tipo_documento.value != 0) {

        nome_tipo_documento_fornitore += form.kp_tipo_documento_display.value;

    }

    nome_tipo_documento_fornitore += " - " + nome_fornitore + " (" + nome_risorsa + ")";

    form.kp_nome_tipo_doc_fo.value = nome_tipo_documento_fornitore;

}

function set_return_tipo_soggetto_gestione_avvisi(recordid, value, target_fieldname, tipo_soggetto_avviso){

    var formName = getReturnFormName();
    var form = getReturnForm(formName);

    form.stabilimento_display.value = value;
    form.stabilimento.value = recordid;
    disableReferenceField(form.stabilimento_display, form.stabilimento);

    form.kp_tipo_sogg_avvisi.value = tipo_soggetto_avviso;

}
