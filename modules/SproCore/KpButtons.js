function assegnaRisorseManutenzioni() {

    /* kpro@tom300120171013 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package manutenzioni
     * @version 1.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                var idstring = get_real_selected_ids('Manutenzioni');
                if (idstring.substr('0', '1') == ';') {
                    idstring = idstring.substr('1');
                }
                var idarr = idstring.split(';');
                var count = idarr.length;
                var xx = count - 1;

                if (idstring == '' || idstring == ';' || idstring == 'null') {
                    alert('Nessuna manutenzione selezionata');
                } else {

                    var altezza = window.innerHeight - 150;

                    if (!document.getElementById('kpAssegnaRisorseManutenzione')) {
                        if (document.getElementById('Buttons_List_HomeMod')) {
                            document.getElementById('Buttons_List_HomeMod').innerHTML = "";
                        }
                        if (document.getElementById('Buttons_List_3')) {
                            document.getElementById('Buttons_List_3').innerHTML = "";
                        }
                        if (document.getElementById('ListViewContents')) {
                            document.getElementById('ListViewContents').remove();
                        }
                        if (document.getElementById('ModuleHomeMatrix')) {
                            document.getElementById('ModuleHomeMatrix').remove();
                        }

                        var frame = "<iframe id='kpAssegnaRisorseManutenzione' src='modules/SproCore/CustomViews/PopupAssegnazioneRisorseManutenzioni/index.php?ids=" + escape(JSON.stringify(idarr)) + "' width='100%' height='" + altezza + "px' frameborder='0'>";
                        document.getElementById('Buttons_List_HomeMod').innerHTML = frame;

                        document.scrollTop;

                        document.getElementById("Buttons_List_HomeMod").scrollIntoView(true);

                    } else {
                        window.open('index.php?module=Manutenzioni&action=index', '_self');
                    }

                    /*var larghezza = window.innerWidth - 100;
                    var altezza = window.innerHeight - 100;

                    openPopup('index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/PopupAssegnazioneRisorseManutenzioni/index&ids=' + escape(JSON.stringify(idarr)),'','','auto',larghezza,altezza,'');*/

                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function AssociaCheckLists() {

    /* kpro@bid14112016 */

    /**
     * @author Bidese Jacopo
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package AssociaCheckLists
     * @version 1.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                var idstring = get_real_selected_ids('CompImpianto');
                if (idstring.substr('0', '1') == ';')
                    idstring = idstring.substr('1');
                var idarr = idstring.split(';');
                var count = idarr.length;
                var xx = count - 1;

                if (idstring == '' || idstring == ';' || idstring == 'null') {
                    //Inserisci la funzione che deve essere eseguita qualora non fosse stato selezionato alcun record
                    alert('Nessun componente impianto selezionato!');
                } else {
                    var larghezza = window.innerWidth - 100;
                    var altezza = window.innerHeight - 100;

                    openPopup('index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/AssociaCheckLists/index&ids=' + escape(JSON.stringify(idarr)), '', '', 'auto', larghezza, altezza, '');

                    //openPopup('modules/SproCore/CustomViews/AssociaCheckLists/index.php?ids=' + escape(JSON.stringify(idarr)), '', '', 'auto', larghezza, altezza, '');
                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function gantt_pianificazioni() {

    /* kpro@tom0512015 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2015, Kpro Consulting Srl
     * @package ganttPianificazioni
     * @version 1.0
     * 
     * Carica il Gantt
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                crmid = getObj('record').value;

                /*var altezza_div_sopra = document.getElementById("vte_menu_white").style.height;
                altezza_div_sopra = altezza_div_sopra.replace("px", "");
                var altezza = window.innerHeight - altezza_div_sopra - 40;*/

                var altezza = window.innerHeight - 190;

                if (!document.getElementById('ganttKpro')) {
                    if (document.getElementById('DetailViewBlocks')) {
                        document.getElementById('DetailViewBlocks').innerHTML = "";
                    }
                    if (document.getElementById('turboLiftContainer')) {
                        document.getElementById('turboLiftContainer').remove();
                    }
                    if (document.getElementById('detailviewwidget1')) {
                        document.getElementById('detailviewwidget1').remove();
                    }
                    if (document.getElementById('detailviewwidget2')) {
                        document.getElementById('detailviewwidget2').remove();
                    }
                    if (document.getElementById('detailviewwidget3')) {
                        document.getElementById('detailviewwidget3').remove();
                    }
                    if (document.getElementById('RelatedLists')) {
                        document.getElementById('RelatedLists').remove();
                    }
                    if (document.getElementById('DynamicRelatedList')) {
                        document.getElementById('DynamicRelatedList').remove();
                    }
                    if (document.getElementById('vte_footer')) {
                        document.getElementById('vte_footer').remove();
                    }
                    if (document.getElementById('DetailViewTabs')) {
                        document.getElementById('DetailViewTabs').remove();
                    }

                    var frame = "<iframe id='ganttKpro' src='modules/SproCore/CustomViews/GanttProjectPlan/index.php?record_id=" + crmid + "' width='100%' height='" + altezza + "px' frameborder='0'>";
                    document.getElementById('DetailViewBlocks').innerHTML = frame;

                    document.scrollTop;
                    //Alligna lo scroll della pagina
                    document.getElementById("DetailViewBlocks").scrollIntoView(true);

                } else {
                    window.open('index.php?module=ProjectPlan&parenttab=Projects&action=DetailView&record=' + crmid, '_self');
                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function aggiorna_indici_pianificazione() {

    /* kpro@tom0512015 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2015, Kpro Consulting Srl
     * @package ganttPianificazioni
     * @version 1.0
     * 
     * Aggiorna gli indici della pianificazione
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                crmid = getObj('record').value;
                //openPopup('modules/SproCore/CustomViews/GanttProjectPlan/AggiornaIndiciPianificazione.php?record_id=' + crmid,'','','auto','100','50','');
                var res = getFile('modules/SproCore/CustomViews/GanttProjectPlan/AggiornaIndiciPianificazione.php?record_id=' + crmid);
                window.open('index.php?module=ProjectPlan&action=DetailView&record=' + crmid, '_self');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function gantt_pianificazioni_generale() {

    /* kpro@tom260116 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package ganttPianificazioni
     * @version 1.0
     * 
     * Carica il Gantt Generale
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                if (!document.getElementById('ganttKpro')) {
                    var altezza = window.innerHeight - 100;

                    var idstring = get_real_selected_ids('ProjectPlan');
                    if (idstring.substr('0', '1') == ';') {
                        idstring = idstring.substr('1');
                    }
                    var idarr = idstring.split(';');
                    var count = idarr.length;
                    var xx = count - 1;

                    if (idstring == '' || idstring == ';' || idstring == 'null') {
                        alert('Nessuna pianificazione selezionata');
                    } else {

                        if (document.getElementById('Buttons_List_3')) {
                            document.getElementById('Buttons_List_3').innerHTML = "";
                        }
                        if (document.getElementById('ListViewContents')) {
                            document.getElementById('ListViewContents').remove();
                        }

                        var frame = "<iframe id='ganttKpro' src='modules/SproCore/CustomViews/GanttProjectPlanGenerale/index.php?ids=" + escape(JSON.stringify(idarr)) + "' width='100%' height='" + altezza + "px' frameborder='0'>";
                        document.getElementById('Buttons_List_3').innerHTML = frame;

                        document.scrollTop;
                        //Alligna lo scroll della pagina
                        document.getElementById("Buttons_List_3").scrollIntoView(true);

                    }
                } else {
                    window.open('index.php?module=ProjectPlan&action=index', '_self');
                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function users_dashboard() {

    /* kpro@tom08112016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package ganttPianificazioni
     * @version 2.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                if (!document.getElementById('dashboardKpro')) {

                    var altezza = window.innerHeight - 140;

                    if (document.getElementsByClassName('showPanelBg')[0]) {
                        document.getElementsByClassName('showPanelBg')[0].innerHTML = "";
                        document.getElementsByClassName('showPanelBg')[0].style.width = "100%";
                    }

                    if (document.getElementById('vte_footer')) {
                        document.getElementById('vte_footer').remove();
                    }

                    var frame = "<iframe id='dashboardKpro' src='modules/SproCore/CustomViews/DashBoardUsers/index.php' width='100%' height='" + altezza + "px' frameborder='0'>";
                    document.getElementsByClassName('showPanelBg')[0].innerHTML = frame;

                    document.scrollTop;
                    //Alligna lo scroll della pagina
                    document.getElementsByClassName('showPanelBg')[0].scrollIntoView(true);
                } else {
                    window.open('index.php?module=Home&action=index', '_self');
                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function ticket_scheduler() {

    /* kpro@tom180316 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package ganttPianificazioni
     * @version 2.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                if (!document.getElementById('ticketSchedulerKpro')) {

                    jQuery.ajax({
                        url: 'modules/SproCore/CustomViews/TicketScheduler/DatiUtente.php',
                        dataType: 'json',
                        async: false,
                        success: function(data) {

                            //console.table(data);
                            if (data.length > 0) {

                                var altezza = window.innerHeight - 100;

                                var idstring = get_real_selected_ids('HelpDesk');
                                if (idstring.substr('0', '1') == ';') {
                                    idstring = idstring.substr('1');
                                }
                                var idarr = idstring.split(';');
                                var count = idarr.length;
                                var xx = count - 1;

                                var frame = "";

                                if (idstring == '' || idstring == ';' || idstring == 'null') {

                                    if (data[0].ticket_scheduler == "si") {
                                        frame = "<iframe id='ticketSchedulerKpro' src='modules/SproCore/CustomViews/TicketScheduler/index.php' width='100%' height='" + altezza + "px' frameborder='0'>";
                                    } else {
                                        frame = "<iframe id='ticketSchedulerKpro' src='modules/SproCore/CustomViews/DashBoardUsers/index.php' width='100%' height='" + altezza + "px' frameborder='0'>";
                                    }

                                } else {

                                    if (data[0].ticket_scheduler == "si") {
                                        frame = "<iframe id='ticketSchedulerKpro' src='modules/SproCore/CustomViews/TicketScheduler/index.php?ids=" + escape(JSON.stringify(idarr)) + "' width='100%' height='" + altezza + "px' frameborder='0'>";
                                    } else {
                                        frame = "<iframe id='ticketSchedulerKpro' src='modules/SproCore/CustomViews/DashBoardUsers/index.php' width='100%' height='" + altezza + "px' frameborder='0'>";
                                    }

                                }

                                if (document.getElementById('Buttons_List_3')) {
                                    document.getElementById('Buttons_List_3').innerHTML = "";
                                }
                                if (document.getElementById('ListViewContents')) {
                                    document.getElementById('ListViewContents').remove();
                                }

                                document.getElementById('Buttons_List_3').innerHTML = frame;

                                document.scrollTop;
                                //Alligna lo scroll della pagina
                                document.getElementById("Buttons_List_3").scrollIntoView(true);

                            }

                        },
                        fail: function() {

                            console.error("Errore nel caricamento del ticket scheduler");

                        }
                    });

                } else {
                    window.open('index.php?module=HelpDesk&action=index', '_self');
                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function kp_rapportino_intervento_helpdesk() {

    /* kpro@tom15112016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                crmid = getObj('record').value;

                window.open('modules/SproCore/CustomViews/FirmaInterventoHelpDesk/pagina_firma.php?return_module=Timecards&record=' + crmid, '_blank');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function gantt_situazione_manutenzioni() {

    /* kpro@tom190216 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package manutenzioni
     * @version 1.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                if (!document.getElementById('ganttKpro')) {
                    var altezza = window.innerHeight - 120;

                    if (document.getElementById('Buttons_List_3')) {
                        document.getElementById('Buttons_List_3').innerHTML = "";
                    }
                    if (document.getElementById('ListViewContents')) {
                        document.getElementById('ListViewContents').remove();
                    }

                    var frame = "<iframe id='ganttKpro' src='modules/SproCore/CustomViews/GanttSituazioneManutenzioni/index.php' width='100%' height='" + altezza + "px' frameborder='0'>";
                    document.getElementById('Buttons_List_3').innerHTML = frame;

                    document.scrollTop;
                    //Alligna lo scroll della pagina
                    document.getElementById("Buttons_List_3").scrollIntoView(true);

                } else {
                    window.open('index.php?module=SituazCheckList&action=index', '_self');
                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function gantt_manutenzioni() {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                if (!document.getElementById('ganttKpro')) {
                    var altezza = window.innerHeight - 100;

                    if (document.getElementById('Buttons_List_3')) {
                        document.getElementById('Buttons_List_3').innerHTML = "";
                    }
                    if (document.getElementById('ListViewContents')) {
                        document.getElementById('ListViewContents').remove();
                    }

                    var frame = "<iframe id='ganttKpro' src='modules/SproCore/CustomViews/GanttManutenzioni/index.php' width='100%' height='" + altezza + "px' frameborder='0'>";
                    document.getElementById('Buttons_List_3').innerHTML = frame;

                    document.scrollTop;
                    //Alligna lo scroll della pagina
                    document.getElementById("Buttons_List_3").scrollIntoView(true);

                } else {
                    window.open('index.php?module=Manutenzioni&action=index', '_self');
                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function ModificaConsegna() {

    /* kpro@bid150116 */
    /**
     * @author Bidese Jacopo
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package ConsegnaDPI
     * @version 1.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                record = getObj('record').value;
                window.open('modules/SproCore/CustomPortals/KpPortaleConsegnaDPI/index.php?record=' + record, '_self');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function CreaConsegna() {

    /* kpro@bid150116 */
    /**
     * @author Bidese Jacopo
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package ConsegnaDPI
     * @version 1.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                window.open('modules/SproCore/CustomPortals/KpPortaleConsegnaDPI/index.php?record=new', '_self');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function caricamento_mansioni_risorsa(record) {

    /* kpro@tom150116 */
    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package inserimentoVeloceMansioniRisorsa
     * @version 1.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                var altezza = window.innerHeight - 180;

                if (!document.getElementById('inserimentoVeloceMansRis')) {
                    if (document.getElementById('Buttons_List_3')) {
                        document.getElementById('Buttons_List_3').innerHTML = "";
                    }
                    if (document.getElementById('DetailViewTabs')) {
                        document.getElementById('DetailViewTabs').innerHTML = "";
                    }
                    if (document.getElementById('DetailViewBlocks')) {
                        document.getElementById('DetailViewBlocks').innerHTML = "";
                    }
                    if (document.getElementById('turboLiftContainer')) {
                        document.getElementById('turboLiftContainer').remove();
                    }
                    if (document.getElementById('detailviewwidget1')) {
                        document.getElementById('detailviewwidget1').remove();
                    }
                    if (document.getElementById('detailviewwidget2')) {
                        document.getElementById('detailviewwidget2').remove();
                    }
                    if (document.getElementById('detailviewwidget3')) {
                        document.getElementById('detailviewwidget3').remove();
                    }
                    if (document.getElementById('RelatedLists')) {
                        document.getElementById('RelatedLists').remove();
                    }
                    if (document.getElementById('DynamicRelatedList')) {
                        document.getElementById('DynamicRelatedList').remove();
                    }

                    if (altezza < 505) {
                        altezza = 505;
                    }

                    var frame = "<iframe id='inserimentoVeloceMansRis' src='modules/SproCore/CustomViews/InserimentoVeloceMansioniRisorsa/index.php?cur_id=" + record + "' width='100%' height='" + altezza + "px' frameborder='0'>";
                    document.getElementById('Buttons_List_3').innerHTML = frame;

                    document.scrollTop;

                    document.getElementById("Buttons_List_3").scrollIntoView(true);
                } else {
                    window.open('index.php?module=Accounts&action=DetailView&record=' + record, '_self');
                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });


}

function AssociaMansioni() {

    /* kpro@bid14112016 */
    /**
     * @author Bidese Jacopo
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package AssociaMansioni
     * @version 1.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                var idstring = get_real_selected_ids('Contacts');
                if (idstring.substr('0', '1') == ';')
                    idstring = idstring.substr('1');
                var idarr = idstring.split(';');
                var count = idarr.length;
                var xx = count - 1;

                if (idstring == '' || idstring == ';' || idstring == 'null') {
                    //Inserisci la funzione che deve essere eseguita qualora non fosse stato selezionato alcun record
                    alert('Nessuna risorsa selezionata!');
                } else {
                    var larghezza = window.innerWidth - 100;
                    var altezza = window.innerHeight - 100;

                    openPopup('index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/AssociaMansioni/index&ids=' + escape(JSON.stringify(idarr)), '', '', 'auto', larghezza, altezza, '');

                    //openPopup('modules/SproCore/CustomViews/AssociaMansioni/index.php?ids=' + escape(JSON.stringify(idarr)), '', '', 'auto', larghezza, altezza, '');
                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function AggImpianti(module) {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                $("status").style.display = "inline"; //Inserisce la barra di caricamento
                //openPopup("index.php?module=Impianti&action=ImpiantiAjax&file=getImpianti&cur_module="+module,'','','auto','60','50','');

                var res = getFile("index.php?module=Impianti&action=ImpiantiAjax&file=getImpianti&cur_module=" + module);

                $("status").style.display = "none"; //Termina la barra di caricamento	
                window.open('index.php?module=Impianti&action=index&areaid=8', '_self');
                alert('Operazione eseguita');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function calendario_visite_mediche() {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                if (!document.getElementById('calendarioVisiteMedicheKpro')) {
                    var altezza = window.innerHeight - 80;

                    if (document.getElementById('Buttons_List_3')) {
                        document.getElementById('Buttons_List_3').innerHTML = "";
                    }
                    if (document.getElementById('ListViewContents')) {
                        document.getElementById('ListViewContents').remove();
                    }

                    var frame = "<iframe id='calendarioVisiteMedicheKpro' src='modules/SproCore/CustomViews/CalendarioVisiteMediche/index.php' width='100%' height='" + altezza + "px' frameborder='0' >";
                    document.getElementById('Buttons_List_3').innerHTML = frame;

                    document.scrollTop;
                    //Alligna lo scroll della pagina
                    document.getElementById("Buttons_List_3").scrollIntoView(true);

                } else {
                    window.open('index.php?module=VisiteMediche&action=index', '_self');
                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function calendario_formazione() {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                if (!document.getElementById('calendarioFormazioneKpro')) {
                    var altezza = window.innerHeight - 80;

                    if (document.getElementById('Buttons_List_3')) {
                        document.getElementById('Buttons_List_3').innerHTML = "";
                    }
                    if (document.getElementById('ListViewContents')) {
                        document.getElementById('ListViewContents').remove();
                    }

                    var frame = "<iframe id='calendarioFormazioneKpro' src='modules/SproCore/CustomViews/CalendarioFormazione/index.php' width='100%' height='" + altezza + "px' frameborder='0' >";
                    document.getElementById('Buttons_List_3').innerHTML = frame;

                    document.scrollTop;
                    //Alligna lo scroll della pagina
                    document.getElementById("Buttons_List_3").scrollIntoView(true);

                } else {
                    window.open('index.php?module=KpFormazione&action=index', '_self');
                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function genera_manutenzioni() {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                var idstring = get_real_selected_ids('SituazCheckList'); //Catturo l'id dei record selezionati
                if (idstring.substr('0', '1') == ';')
                    idstring = idstring.substr('1');
                var idarr = idstring.split(';');
                var count = idarr.length;
                var xx = count - 1;

                if (idstring == '' || idstring == ';' || idstring == 'null') {
                    //Inserisci la funzione che deve essere eseguita qualora non fosse stato selezionato alcun record
                    alert('Nessun record selezionato');
                } else {
                    VtigerJS_DialogBox.block();
                    $('status').style.display = 'inline'; //Inserisce la barra di caricamento
                    //var res = getFile('index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/PopupGenerazioneManutenzioni/getManutenzione&ids='+escape(JSON.stringify(idarr)));
                    $('status').style.display = 'none'; //Termina la barra di caricamento
                    openPopup('index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/PopupGenerazioneManutenzioni/index&ids=' + escape(JSON.stringify(idarr)), '', '', 'auto', '80', '40', '');
                    //window.open('index.php?module=SituazCheckList&action=index', '_self');
                    VtigerJS_DialogBox.unblock();
                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function genera_canone(crmid) {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                window.open('index.php?module=Canoni&action=EditView&record_action=' + crmid + '&function=GenerateCanone', '_self');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function calcolaSituazFormaz() {

    /* kpro@tom190116 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package situazioneFormazione
     * @version 1.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                /*//OLD Script
                //openPopup("index.php?module=SituazFormaz&action=SituazFormazAjax&file=getSituazFormaz&cur_module="+module,'','','auto','60','50','');
                $("status").style.display="inline";
                var res = getFile("index.php?module=SituazFormaz&action=SituazFormazAjax&file=getSituazFormaz&cur_module="+module);
                $("status").style.display="none";	//Termina la barra di caricamento	
                window.open('index.php?module=SituazFormaz&action=index&areaid=6', '_self');
                alert('Operazione eseguita');*/

                //openPopup("modules/SproCore/SituazFormaz/getSituazFormaz.php", '', '', 'auto', '60', '50', '');

                $("status").style.display = "inline";
                var res = getFile("modules/SproCore/SituazFormaz/getSituazFormaz.php");
                $("status").style.display = "none";
                window.open('index.php?module=SituazFormaz&action=index', '_self');
                alert('Operazione eseguita');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function aggiorna_scadenze_documenti() {

    /* kpro@tom190116 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package situazioneDocumenti
     * @version 1.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                //openPopup("index.php?module=SproCore&action=SproCoreAjax&file=Documents/getAnalisiDocumenti",'','','auto','60','50','');

                $('status').style.display = 'inline'; //Inserisce la barra di caricamento
                var res = getFile('index.php?module=SproCore&action=SproCoreAjax&file=Documents/getAnalisiDocumenti');
                $('status').style.display = 'none'; //Termina la barra di caricamento
                window.open('index.php?module=Documents&action=ListView', '_self');
                alert('Operazione eseguita');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function NewSituazCheckList(module) {

    /* kpro@tom190216 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package manutenzioni
     * @version 1.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                $("status").style.display = "inline"; //Inserisce la barra di caricamento
                //openPopup("index.php?module=SituazCheckList&action=SituazCheckListAjax&file=getSituazCheckList&cur_module="+module,'','','auto','60','50','');

                var res = getFile("index.php?module=SituazCheckList&action=SituazCheckListAjax&file=getSituazCheckList&cur_module=" + module);

                $("status").style.display = "none"; //Termina la barra di caricamento	
                window.open('index.php?module=SituazCheckList&action=index&areaid=6', '_self');
                alert('Operazione eseguita');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function mpro(module) {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                openPopup("index.php?module=Processi&action=ProcessiAjax&file=getProcessi4Module&cur_module=" + module, '', '', 'auto', '60', '50', '');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function mprologin(module) {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                var utente_cript = '';
                var processo_selezionato = '';
                window.open("index.php?module=Processi&action=index&utente=" + utente_cript + "&processo=" + processo_selezionato, "_blank");

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function mpro_site() {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                var larghezza = window.innerWidth - 5;
                var altezza = window.innerHeight - 80;

                var frame = "<iframe src='http://156.54.173.67//MProViewer/ext/ProcessiGraficoExt.aspx?user=admin&env=00042&comp=0001&proc=M00062/P00044' width='" + larghezza + "px' height='" + altezza + "px' frameborder='0'>";

                document.getElementById('Buttons_List_3').innerHTML = frame;
                document.getElementById('ListViewContents').innerHTML = "";

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function aggiorna_lista_partecipanti(crmid) {

    /* kpro@tom190116 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @version 1.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                //openPopup('index.php?module=SproCore&action=SproCoreAjax&file=ListaPartecip/AggiornaListaPartecip&cur_id='+crmid,'','','auto','50','32',''); 

                $('status').style.display = 'inline'; //Inserisce la barra di caricamento
                var res = getFile('modules/SproCore/ListaPartecip/AggiornaListaPartecip&cur_id=' + crmid);
                $('status').style.display = 'none'; //Termina la barra di caricamento
                window.open('index.php?module=Formazione&parenttab=Inventory&action=DetailView&record=' + crmid, '_self');
                alert('Operazione eseguita');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function vista_grafica_sit_vis_med() {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                if (!document.getElementById('vistaGraficaSitVisMedKpro')) {
                    var altezza = window.innerHeight - 80;

                    if (document.getElementById('Buttons_List_3')) {
                        document.getElementById('Buttons_List_3').innerHTML = "";
                    }
                    if (document.getElementById('ListViewContents')) {
                        document.getElementById('ListViewContents').remove();
                    }

                    var frame = "<iframe id='vistaGraficaSitVisMedKpro' src='modules/SproCore/CustomViews/VistaGraficaSituazioneVisMed/index.php' width='100%' height='" + altezza + "px' frameborder='0' >";
                    document.getElementById('Buttons_List_3').innerHTML = frame;

                    document.scrollTop;
                    //Alligna lo scroll della pagina
                    document.getElementById("Buttons_List_3").scrollIntoView(true);

                } else {
                    window.open('index.php?module=SituazVisiteMed&action=index', '_self');
                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function vista_grafica_sit_form() {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                if (!document.getElementById('vistaGraficaSitFormKpro')) {
                    var altezza = window.innerHeight - 80;

                    if (document.getElementById('Buttons_List_3')) {
                        document.getElementById('Buttons_List_3').innerHTML = "";
                    }
                    if (document.getElementById('ListViewContents')) {
                        document.getElementById('ListViewContents').remove();
                    }

                    var frame = "<iframe id='vistaGraficaSitFormKpro' src='modules/SproCore/CustomViews/VistaGraficaSituazioneFormazione/index.php' width='100%' height='" + altezza + "px' frameborder='0' >";
                    document.getElementById('Buttons_List_3').innerHTML = frame;

                    document.scrollTop;
                    //Alligna lo scroll della pagina
                    document.getElementById("Buttons_List_3").scrollIntoView(true);

                } else {
                    window.open('index.php?module=SituazFormaz&action=index', '_self');
                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function NewAnalisiVenduto(record) {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                openPopup("index.php?module=Accounts&action=AccountsAjax&file=getAnalisiVenduto&cur_id=" + record, '', '', 'auto', '110', '55', '');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function NewSituazVisiteMed(module) {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                $("status").style.display = "inline"; //Inserisce la barra di caricamento
                //openPopup("index.php?module=SituazVisiteMed&action=SituazVisiteMedAjax&file=getSituazVisiteMed&cur_module="+module,'','','auto','60','50','');

                var res = getFile("index.php?module=SituazVisiteMed&action=SituazVisiteMedAjax&file=getSituazVisiteMed&cur_module=" + module);

                $("status").style.display = "none"; //Termina la barra di caricamento	
                window.open('index.php?module=SituazVisiteMed&action=index', '_self');
                alert('Operazione eseguita');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function inserimentoVeloceStoricoFormazione(record) {

    /* kpro@tom310120170912 */
    /**
     * @author Tomiello Marco
     * @copyright (c) 2017, Kpro Consulting Srl
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                //record = getObj('record').value;

                var altezza = window.innerHeight - 120;

                if (!document.getElementById('inserimentoVeloceStoricoForm')) {

                    if (document.getElementById('Buttons_List_3')) {
                        document.getElementById('Buttons_List_3').innerHTML = "";
                    }
                    if (document.getElementById('DetailViewTabs')) {
                        document.getElementById('DetailViewTabs').innerHTML = "";
                    }
                    if (document.getElementById('DetailViewBlocks')) {
                        document.getElementById('DetailViewBlocks').innerHTML = "";
                    }
                    if (document.getElementById('turboLiftContainer')) {
                        document.getElementById('turboLiftContainer').remove();
                    }
                    if (document.getElementById('detailviewwidget1')) {
                        document.getElementById('detailviewwidget1').remove();
                    }
                    if (document.getElementById('detailviewwidget2')) {
                        document.getElementById('detailviewwidget2').remove();
                    }
                    if (document.getElementById('detailviewwidget3')) {
                        document.getElementById('detailviewwidget3').remove();
                    }
                    if (document.getElementById('RelatedLists')) {
                        document.getElementById('RelatedLists').remove();
                    }
                    if (document.getElementById('DynamicRelatedList')) {
                        document.getElementById('DynamicRelatedList').remove();
                    }

                    if (altezza < 505) {
                        altezza = 505;
                    }

                    var frame = "<iframe id='inserimentoVeloceStoricoForm' src='modules/SproCore/CustomViews/InserimentoVeloceStoricoFormazione/index.php?record=" + record + "' width='100%' height='" + altezza + "px' frameborder='0' >";
                    document.getElementById('Buttons_List_3').innerHTML = frame;

                    document.scrollTop;

                    document.getElementById("Buttons_List_3").scrollIntoView(true);

                } else {
                    window.open('index.php?module=Accounts&action=DetailView&record=' + record, '_self');
                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function inserimentoVeloceEsitiVisiteMediche(record) {

    /* kpro@bid150116 */
    /**
     * @author Bidese Jacopo
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package inserimentoVeloceEsitiVisiteMediche
     * @version 1.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                //record = getObj('record').value;

                var altezza = window.innerHeight - 120;

                if (!document.getElementById('inserimentoVeloceEsitiVisiteForm')) {

                    if (document.getElementById('Buttons_List_3')) {
                        document.getElementById('Buttons_List_3').innerHTML = "";
                    }
                    if (document.getElementById('DetailViewTabs')) {
                        document.getElementById('DetailViewTabs').innerHTML = "";
                    }
                    if (document.getElementById('DetailViewBlocks')) {
                        document.getElementById('DetailViewBlocks').innerHTML = "";
                    }
                    if (document.getElementById('turboLiftContainer')) {
                        document.getElementById('turboLiftContainer').remove();
                    }
                    if (document.getElementById('detailviewwidget1')) {
                        document.getElementById('detailviewwidget1').remove();
                    }
                    if (document.getElementById('detailviewwidget2')) {
                        document.getElementById('detailviewwidget2').remove();
                    }
                    if (document.getElementById('detailviewwidget3')) {
                        document.getElementById('detailviewwidget3').remove();
                    }
                    if (document.getElementById('RelatedLists')) {
                        document.getElementById('RelatedLists').remove();
                    }
                    if (document.getElementById('DynamicRelatedList')) {
                        document.getElementById('DynamicRelatedList').remove();
                    }

                    if (altezza < 505) {
                        altezza = 505;
                    }

                    var frame = "<iframe id='inserimentoVeloceEsitiVisiteForm' src='modules/SproCore/CustomViews/InserimentoVeloceEsitiVisite/index.php?record=" + record + "' width='100%' height='" + altezza + "px' frameborder='0'>";
                    document.getElementById('Buttons_List_3').innerHTML = frame;

                    document.scrollTop;

                    document.getElementById("Buttons_List_3").scrollIntoView(true);

                } else {
                    window.open('index.php?module=VisiteMediche&action=DetailView&record=' + record, '_self');
                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function kproWorkflow() {

    /* kpro@tom06062016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package gestioneWorkflow
     * @version 1.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                crmid = getObj('record').value;

                var larghezza = window.innerWidth - 500;
                var altezza = window.innerHeight - 200;

                openPopup("index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/GestioneWorkflow/index&crmid=" + crmid, '', '', 'auto', larghezza, altezza, '');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function esportazioneVeloceDocumento() {

    /* kpro@tom06062016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package gestioneSemplificataDocumenti
     * @version 1.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                crmid = getObj('record').value;

                var dati_documento = {
                    crmid: crmid
                };

                jQuery.ajax({
                    url: 'modules/SproCore/CustomViews/GestioneSemplificataDocumenti/DatiDocumento.php',
                    dataType: 'json',
                    async: false,
                    data: dati_documento,
                    success: function(data) {

                        //console.table(data);

                        if (data.length > 0) {

                            location.href = "index.php?module=uploads&action=downloadfile&return_module=Documents&fileid=" + data[0].attachmentsid + "&entityid=" + data[0].notesid;

                        }

                    },
                    fail: function() {

                        console.error("Errore nell'esportazione del documentio");

                    }
                });

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function importazioneVeloceDocumento() {

    /* kpro@tom06062016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package gestioneSemplificataDocumenti
     * @version 1.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                crmid = getObj('record').value;

                var larghezza = window.innerWidth - 500;
                var altezza = window.innerHeight - 300;

                openPopup("index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/GestioneSemplificataDocumenti/index&crmid=" + crmid, '', '', 'auto', larghezza, altezza, '');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function ReportGestioneRifiuti() {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                var frame_attivo = document.getElementById('iframe_report_gestione_rifiuti');
                if (frame_attivo == null || frame_attivo == "") {
                    document.getElementById('Buttons_List_3').innerHTML = "";
                    document.getElementById('ListViewContents').innerHTML = "";
                    document.getElementById('vte_footer').innerHTML = "";

                    var larghezza = window.innerWidth - 5;
                    var altezza = window.innerHeight - 80;

                    var frame = "<iframe id='iframe_report_gestione_rifiuti' src='modules/SproCore/CustomViews/ReportGestioneRifiuti/index.php' width='" + larghezza + "px' height='" + altezza + "px' frameborder='0'>";
                    document.getElementById('Buttons_List_3').innerHTML = frame;

                    //Alligna lo scroll della pagina
                    //document.getElementById("DetailExtraBlock").scrollIntoView(true);
                } else {
                    parent.location.reload();
                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function KpImportRisorseMansioni() {

    /* kpro@bid210420170930 */
    /**
     * @author Bidese Jacopo
     * @copyright (c) 2017, Kpro Consulting Srl
     * @package KpImportCustom
     * @version 1.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {
                var larghezza = window.innerWidth - 100;
                var altezza = window.innerHeight - 100;

                window.open('modules/SproCore/CustomViews/KpImportRisorseMansioni/index.php', '_blank');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function KpImportFormazione() {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {
                var larghezza = window.innerWidth - 100;
                var altezza = window.innerHeight - 100;

                window.open('modules/SproCore/CustomViews/KpImportFormazione/index.php', '_blank');
            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function KpImportVisiteMediche() {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {
                var larghezza = window.innerWidth - 100;
                var altezza = window.innerHeight - 100;

                window.open('modules/SproCore/CustomViews/KpImportVisiteMediche/index.php', '_blank');
            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function KpImportImpianti() {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {
                var larghezza = window.innerWidth - 100;
                var altezza = window.innerHeight - 100;

                window.open('modules/SproCore/CustomViews/KpImportImpianti/index.php', '_blank');
            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function ProjectPlanGantt(record) {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                //$("status").style.display="inline";		//Inserisce la barra di caricamento
                openPopup("index.php?module=ProjectPlan&action=ProjectPlanAjax&file=getProjectGantt&cur_id=" + record, '', '', 'auto', '100', '80', '');

                //var res = getFile("index.php?module=ProjectPlan&action=ProjectPlanAjax&file=getProjectGantt&cur_id="+record);

                //$("status").style.display="none";	//Termina la barra di caricamento	
                //alert('Operazione eseguita');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function NewProjectPlan(record) {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                $("status").style.display = "inline"; //Inserisce la barra di caricamento
                //openPopup("index.php?module=ProjectPlan&action=ProjectPlanAjax&file=NewProjectPlan&cur_id="+record,'','','auto','60','50','');

                var res = getFile("index.php?module=ProjectPlan&action=ProjectPlanAjax&file=NewProjectPlan&cur_id=" + record);

                $("status").style.display = "none"; //Termina la barra di caricamento	
                alert('Operazione eseguita');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function RipTaskSucessive(record) {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                $("status").style.display = "inline"; //Inserisce la barra di caricamento
                //openPopup("index.php?module=ProjectTask&action=ProjectTaskAjax&file=getRipianificaAvanti&cur_id="+record,'','','auto','60','50','');

                var res = getFile("index.php?module=ProjectTask&action=ProjectTaskAjax&file=getRipianificaAvanti&cur_id=" + record);

                $("status").style.display = "none"; //Termina la barra di caricamento	
                alert('Operazione eseguita');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function inserimento_manutenzione() {

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {
            //console.log(data);

            if (data.utente_demo == "false") {

                crmid = getObj('record').value;

                tipo_manutenzione = getObj('tipo_manutenzione').value;
                //console.log(tipo_manutenzione + " "+crmid);

                /* kpro@bid031220181220 */
                /*if (tipo_manutenzione == "Verifica") {

                    window.open('modules/SproCore/CustomPortals/PortaleVerifiche/index.php?cur_id=' + crmid, '_blank');

                } else {*/

                    window.open('modules/SproCore/CustomPortals/PortaleManutenzioni/index.php?cur_id=' + crmid, '_blank');

                //}
                /* kpro@bid031220181220 end */

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function inserimentoVeloceStoricoVisiteMediche(record) {

    /* kpro@tom130116 */
    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     * @package inserimentoVeloceStoricoVisiteMediche
     * @version 1.0
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                //record = getObj('record').value;

                var altezza = window.innerHeight - 180;

                if (!document.getElementById('inserimentoVeloceStoricoVisMed')) {
                    if (document.getElementById('DetailViewBlocks')) {
                        document.getElementById('DetailViewBlocks').innerHTML = "";
                    }
                    if (document.getElementById('turboLiftContainer')) {
                        document.getElementById('turboLiftContainer').remove();
                    }
                    if (document.getElementById('detailviewwidget1')) {
                        document.getElementById('detailviewwidget1').remove();
                    }
                    if (document.getElementById('detailviewwidget2')) {
                        document.getElementById('detailviewwidget2').remove();
                    }
                    if (document.getElementById('detailviewwidget3')) {
                        document.getElementById('detailviewwidget3').remove();
                    }
                    if (document.getElementById('RelatedLists')) {
                        document.getElementById('RelatedLists').remove();
                    }
                    if (document.getElementById('DynamicRelatedList')) {
                        document.getElementById('DynamicRelatedList').remove();
                    }

                    var frame = "<iframe id='inserimentoVeloceStoricoVisMed' src='modules/SproCore/CustomViews/InserimentoVeloceStoricoVisiteMediche/index.php?record=" + record + "' width='100%' height='" + altezza + "px' frameborder='0'>";
                    document.getElementById('DetailViewBlocks').innerHTML = frame;

                    document.scrollTop;

                    document.getElementById("DetailViewBlocks").scrollIntoView(true);

                } else {
                    window.open('index.php?module=Accounts&action=DetailView&record=' + record, '_self');
                }

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function genera_odf_canoni() {

    /* kpro@tom310316 */

    var larghezza = window.innerWidth - 500;
    var altezza = window.innerHeight - 300;

    var idstring = get_real_selected_ids('Canoni'); //Catturo l'id dei record selezionati
    if (idstring.substr('0', '1') == ';') {
        idstring = idstring.substr('1');
    }
    var idarr = idstring.split(';');
    var count = idarr.length;
    var xx = count - 1;

    if (idstring == '' || idstring == ';' || idstring == 'null') {
        openPopup("index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/PopupGenerazioneOdfDaCanoni/index", '', '', 'auto', larghezza, altezza, '');
    } else {
        openPopup("index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/PopupGenerazioneOdfDaCanoni/index", '', '', 'auto', larghezza, altezza, '');
    }

}

function genera_odf_ticket() {

    /* kpro@tom310316 */
    /**
     * @author Tomiello Marco
     * @copyright Kpro Consulting Srl
     * @package fatturazioneConOdf
     * @version 1.0
     * 
     * Questo script gestisce l'apertura del popup della generazione odf da ticket
     */

    var larghezza = window.innerWidth - 500;
    var altezza = window.innerHeight - 300;

    var idstring = get_real_selected_ids('HelpDesk');
    if (idstring.substr('0', '1') == ';')
        idstring = idstring.substr('1');
    var idarr = idstring.split(';');
    var count = idarr.length;
    var xx = count - 1;

    if (idstring == '' || idstring == ';' || idstring == 'null') {
        openPopup("index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/PopupGenerazioneOdfDaTicket/index", '', '', 'auto', larghezza, altezza, '');
    } else {
        openPopup("index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/PopupGenerazioneOdfDaTicket/index", '', '', 'auto', larghezza, altezza, '');
    }

}

function genera_fattura() {

    /* kpro@tom310316 */
    /**
     * @author Tomiello Marco
     * @copyright Kpro Consulting Srl
     * @package fatturazioneConOdf
     * @version 1.0
     * 
     * Questo script gestisce l'apertura del popup della generazione fatture da OdF
     */

    var larghezza = window.innerWidth - 50;
    var altezza = window.innerHeight - 50;

    var idstring = get_real_selected_ids('OdF'); //Catturo l'id dei record selezionati
    if (idstring.substr('0', '1') == ';')
        idstring = idstring.substr('1');
    var idarr = idstring.split(';');
    var count = idarr.length;
    var xx = count - 1;

    if (idstring == '' || idstring == ';' || idstring == 'null') {
        openPopup("index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/PopupGenerazioneFatturaDaOdf/index", '', '', 'auto', larghezza, altezza, '');
    } else {
        openPopup("index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/PopupGenerazioneFatturaDaOdf/index", '', '', 'auto', larghezza, altezza, '');
    }

}

function genera_odf_report_visita() {

    var larghezza = window.innerWidth - 500;
    var altezza = window.innerHeight - 300;

    var idstring = get_real_selected_ids('Visitreport'); //Catturo l'id dei record selezionati
    if (idstring.substr('0', '1') == ';') {
        idstring = idstring.substr('1');
    }
    var idarr = idstring.split(';');
    var count = idarr.length;
    var xx = count - 1;

    if (idstring == '' || idstring == ';' || idstring == 'null') {
        openPopup("index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/PopupGenerazioneOdfDaReportVisita/index", '', '', 'auto', larghezza, altezza, '');
    } else {
        openPopup("index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/PopupGenerazioneOdfDaReportVisita/index", '', '', 'auto', larghezza, altezza, '');
    }

}

function firmaPartecipazioniFormazione() {

    /* kpro@tom05092016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                crmid = getObj('record').value;

                /*$('status').style.display = 'inline'; //Inserisce la barra di caricamento
                var res = getFile('index.php?module=SproCore&action=SproCoreAjax&file=ListaPartecip/AggiornaListaPartecip&cur_id=' + crmid);
                $('status').style.display = 'none'; //Termina la barra di caricamento*/

                jQuery.ajax({
                    url: 'modules/SproCore/CustomViews/GestioneFirmaPartecipazioniCorso/DatiUtente.php',
                    dataType: 'json',
                    async: true,
                    success: function(data) {

                        //console.table(data);

                        if (data[0].firma_grafometrica == '1') {
                            window.open('modules/SproCore/CustomViews/GestioneFirmaGrafometricaPartecipazioniCorso/index.php?crmid=' + crmid, '_blank');
                        } else {
                            window.open('modules/SproCore/CustomViews/GestioneFirmaPartecipazioniCorso/index.php?crmid=' + crmid, '_blank');
                        }

                    },
                    fail: function() {
                        window.open('modules/SproCore/CustomViews/GestioneFirmaPartecipazioniCorso/index.php?crmid=' + crmid, '_blank');
                    }
                });

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function elencoPartecipazioniFormazione() {

    /* kpro@tom05092016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                crmid = getObj('record').value;

                $('status').style.display = 'inline'; //Inserisce la barra di caricamento
                var res = getFile('index.php?module=SproCore&action=SproCoreAjax&file=ListaPartecip/AggiornaListaPartecip&cur_id=' + crmid);
                $('status').style.display = 'none'; //Termina la barra di caricamento

                window.open('modules/SproCore/CustomViews/GestionePartecipazioniCorsoFormazione/index.php?crmid=' + crmid, '_blank');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function rilevazione_rischi() {

    /* kpro@tom25112016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                crmid = getObj('record').value;

                /*var altezza = window.innerHeight - 140;

                if (!document.getElementById('rilRischiKpro')) {
                    if (document.getElementById('DetailViewBlocks')) {
                        document.getElementById('DetailViewBlocks').innerHTML = "";
                    }
                    if (document.getElementById('turboLiftContainer')) {
                        document.getElementById('turboLiftContainer').remove();
                    }
                    if (document.getElementById('detailviewwidget1')) {
                        document.getElementById('detailviewwidget1').remove();
                    }
                    if (document.getElementById('detailviewwidget2')) {
                        document.getElementById('detailviewwidget2').remove();
                    }
                    if (document.getElementById('detailviewwidget3')) {
                        document.getElementById('detailviewwidget3').remove();
                    }
                    if (document.getElementById('RelatedLists')) {
                        document.getElementById('RelatedLists').remove();
                    }
                    if (document.getElementById('DynamicRelatedList')) {
                        document.getElementById('DynamicRelatedList').remove();
                    }

                    var frame = "<iframe id='rilRischiKpro' src='modules/SproCore/CustomPortals/PortaleRilevazioneRischi/index.php?record=" + crmid + "' width='100%' height='" + altezza + "px' frameborder='0'>";
                    document.getElementById('DetailViewBlocks').innerHTML = frame;

                    document.scrollTop;
                    //Alligna lo scroll della pagina
                    document.getElementById("DetailViewBlocks").scrollIntoView(true);

                } else {
                    window.open('index.php?module=KpRilevazioniRischi&action=DetailView&record=' + crmid, '_self');
                }*/

                //window.open('modules/SproCore/CustomPortals/PortaleRilevazioneRischi/index.php?record=' + crmid, '_blank');

                var larghezza = window.innerWidth - 100;
                var altezza = window.innerHeight - 100;

                /*if (window.innerWidth > 1200) {

                    larghezza = 1200;

                }*/

                openPopup('index.php?module=SproCore&action=SproCoreAjax&file=KpWizard/KpRilevazioneRischi/index&id=' + crmid, '', '', 'auto', larghezza, altezza, '');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function intervento_riduzione_rischi() {

    /* kpro@tom25112016 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2016, Kpro Consulting Srl
     */

    jQuery.ajax({
        url: 'modules/SproCore/SproUtils/getUser.php',
        dataType: 'json',
        async: true,
        success: function(data) {

            if (data.utente_demo == "false") {

                crmid = getObj('record').value;

                /*var altezza = window.innerHeight - 140;

                if (!document.getElementById('rilRischiKpro')) {
                    if (document.getElementById('DetailViewBlocks')) {
                        document.getElementById('DetailViewBlocks').innerHTML = "";
                    }
                    if (document.getElementById('turboLiftContainer')) {
                        document.getElementById('turboLiftContainer').remove();
                    }
                    if (document.getElementById('detailviewwidget1')) {
                        document.getElementById('detailviewwidget1').remove();
                    }
                    if (document.getElementById('detailviewwidget2')) {
                        document.getElementById('detailviewwidget2').remove();
                    }
                    if (document.getElementById('detailviewwidget3')) {
                        document.getElementById('detailviewwidget3').remove();
                    }
                    if (document.getElementById('RelatedLists')) {
                        document.getElementById('RelatedLists').remove();
                    }
                    if (document.getElementById('DynamicRelatedList')) {
                        document.getElementById('DynamicRelatedList').remove();
                    }

                    var frame = "<iframe id='rilRischiKpro' src='modules/SproCore/CustomPortals/PortaleRilevazioneRischi/index.php?record=" + crmid + "' width='100%' height='" + altezza + "px' frameborder='0'>";
                    document.getElementById('DetailViewBlocks').innerHTML = frame;

                    document.scrollTop;
                    //Alligna lo scroll della pagina
                    document.getElementById("DetailViewBlocks").scrollIntoView(true);

                } else {
                    window.open('index.php?module=KpRilevazioniRischi&action=DetailView&record=' + crmid, '_self');
                }*/

                window.open('modules/SproCore/CustomPortals/PortaleInterventoRiduzioneRischi/index.php?record=' + crmid, '_blank');

            } else {
                alert("Funzione non attiva nella demo; richiedi il video dimostrativo!");
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati utente");

        }
    });

}

function kpBPMNcreator(record) {

    /* kpro@tom29062017 */
    /**
     * @author Tomiello Marco
     * @copyright (c) 2017, Kpro Consulting Srl
     */

    window.open('modules/SproCore/CustomViews/KpBPMNcreator/index.php?record=' + record, '_blank');

}

function kpAggiornaLetteraDiIncarico(record) {

    /* kpro@tom04092017 */
    /**
     * @author Tomiello Marco
     * @copyright (c) 2017, Kpro Consulting Srl
     */

    $('status').style.display = 'inline'; //Inserisce la barra di caricamento
    var res = getFile('index.php?module=SproCore&action=SproCoreAjax&file=KpLettereNomina/AggiornaLetteraDiNominaRisorsa&crmid=' + record);
    //window.open('index.php?module=SproCore&action=SproCoreAjax&file=KpLettereNomina/AggiornaLetteraDiNominaRisorsa&crmid=' + record, '_blank');
    $('status').style.display = 'none'; //Termina la barra di caricamento

    location.reload();

}

function kpAggiornaSitMinaccePrivacy() {

    /* kpro@tom25092017 */
    /**
     * @author Tomiello Marco
     * @copyright (c) 2017, Kpro Consulting Srl
     */

    $('status').style.display = 'inline'; //Inserisce la barra di caricamento
    var res = getFile('index.php?module=SproCore&action=SproCoreAjax&file=KpSitMinaccePrivacy/AggiornaSituazioneMinaccePrivacy');
    //window.open('index.php?module=SproCore&action=SproCoreAjax&file=KpSitMinaccePrivacy/AggiornaSituazioneMinaccePrivacy', '_blank');
    $('status').style.display = 'none'; //Termina la barra di caricamento

    location.reload();

}

function kpDuplicaDocumentoConCaricamentoPDF(record) {

    /* kpro@tom21092017 */
    /**
     * @author Tomiello Marco
     * @copyright (c) 2017, Kpro Consulting Srl
     */

    /*var larghezza = window.innerWidth - 100;
    var altezza = window.innerHeight - 100;*/

    var larghezza = 500;
    var altezza = 380;

    openPopup('index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/KpPopupDuplicaDocumentiConCaricamentoPDF/index&id=' + record, '', '', 'auto', larghezza, altezza, '');

}

function kpGenerazioneAttestati(record) {

    /* kpro@tom21092017 */
    /**
     * @author Tomiello Marco
     * @copyright (c) 2017, Kpro Consulting Srl
     */

    var larghezza = window.innerWidth - 750;
    var altezza = window.innerHeight - 300;

    openPopup('index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/kpPopupGenerazioneAttestati/index&id=' + record, '', '', 'auto', larghezza, altezza, '');

}

function kpGeneraOdfDaPartecipazioni() {

    /* kpro@tom27102017 */
    /**
     * @author Tomiello Marco
     * @copyright Kpro Consulting Srl
     */

    var larghezza = window.innerWidth - 500;
    var altezza = window.innerHeight - 300;

    var idstring = get_real_selected_ids('KpPartecipFormaz');
    if (idstring.substr('0', '1') == ';')
        idstring = idstring.substr('1');
    var idarr = idstring.split(';');
    var count = idarr.length;
    var xx = count - 1;

    if (idstring == '' || idstring == ';' || idstring == 'null') {

        alert("Nessuna partecipazione selezionata!");

    } else {
        VtigerJS_DialogBox.block();
        openPopup("index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/kpPopupGenerazioneOdfDaPartecipazioni/index&ids=" + escape(JSON.stringify(idarr)), '', '', 'auto', larghezza, altezza, '');
        VtigerJS_DialogBox.unblock();
    }

}

function InvioMassivoFatture(record_detailview="") {

    if(record_detailview == ""){
        var idstring = get_real_selected_ids('Invoice');
        if (idstring.substr('0', '1') == ';')
            idstring = idstring.substr('1');
        var idarr = idstring.split(';');
        var count = idarr.length;
        var xx = count - 1;
    }
    else{
        var idstring = record_detailview;
        var idarr = [record_detailview, ""];
    }

    if (idstring == '' || idstring == ';' || idstring == 'null') {
        //Inserisci la funzione che deve essere eseguita qualora non fosse stato selezionato alcun record
        alert('Nessuna fattura selezionata!');
    } else {
        var larghezza = window.innerWidth - 100;
        var altezza = window.innerHeight - 100;

        openPopup('index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/PopupInvioMassivoFatture/index&ids=' + escape(JSON.stringify(idarr)), '', '', 'auto', larghezza, altezza, '');
    }

}

function GeneraSollecitiPagamenti() {

    var idstring = get_real_selected_ids('Scadenziario');
    if (idstring.substr('0', '1') == ';')
        idstring = idstring.substr('1');
    var idarr = idstring.split(';');
    var count = idarr.length;
    var xx = count - 1;

    if (idstring == '' || idstring == ';' || idstring == 'null') {
        //Inserisci la funzione che deve essere eseguita qualora non fosse stato selezionato alcun record
        alert('Nessun record di scadenziario selezionato!');
    } else {
        var larghezza = window.innerWidth - 100;
        var altezza = window.innerHeight - 100;

        openPopup('index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/PopupGenerazioneSollecitiPagamenti/index&ids=' + escape(JSON.stringify(idarr)), '', '', 'auto', larghezza, altezza, '');
    }

}

function InvioSollecitiPagamenti() {

    var idstring = get_real_selected_ids('KpSollecitiPagamenti');
    if (idstring.substr('0', '1') == ';')
        idstring = idstring.substr('1');
    var idarr = idstring.split(';');
    var count = idarr.length;
    var xx = count - 1;

    if (idstring == '' || idstring == ';' || idstring == 'null') {
        //Inserisci la funzione che deve essere eseguita qualora non fosse stato selezionato alcun record
        alert('Nessun sollecito selezionato!');
    } else {
        var larghezza = window.innerWidth - 100;
        var altezza = window.innerHeight - 100;

        openPopup('index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/PopupInvioSollecitiPagamenti/index&ids=' + escape(JSON.stringify(idarr)), '', '', 'auto', larghezza, altezza, '');
    }

}

function kpGeneraConsegnaDocumento(record) {

    /* kpro@tom20112017 */
    /**
     * @author Tomiello Marco
     * @copyright (c) 2017, Kpro Consulting Srl
     */

    var larghezza = window.innerWidth - 100;
    var altezza = window.innerHeight - 100;

    if (window.innerWidth > 1200) {

        larghezza = 1200;

    }

    openPopup('index.php?module=SproCore&action=SproCoreAjax&file=KpWizard/KpConsegnaDocumenti/index&id=' + record, '', '', 'auto', larghezza, altezza, '');

}

function kpGeneraOdFDaOrdine(record) {

    var dati_ordine = {
        record: record
    };

    jQuery.ajax({
        url: 'modules/SproCore/CustomViews/PopupGenerazioneOdfDaOrdine/ControlloTipoOrdine.php',
        dataType: 'json',
        data: dati_ordine,
        async: true,
        success: function(data) {

            if (data.length > 0) {

                var stati_odv_abilitati = ['Approved','Emessa fattura acconto'];
                var tipologie_odv_abilitate = ['A progetto'];

                if (jQuery.inArray(data[0].stato_ordine, stati_odv_abilitati) !== -1) {

                    if (jQuery.inArray(data[0].tipologia_ordine, tipologie_odv_abilitate) !== -1) {

                        var larghezza = window.innerWidth - 100;
                        var altezza = window.innerHeight - 100;

                        openPopup('index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/PopupGenerazioneOdfDaOrdine/index&record=' + record, '', '', 'auto', larghezza, altezza, '');
                    } else {
                        alert("Tipo ordine: " + data[0].tipologia_ordine + "! Non è possibile generare OdF per ordini di questo tipo. Le tipologie abilitate sono "+tipologie_odv_abilitate.toString()+".");
                    }
                } else {
                    alert("L'ordine deve essere in stato "+stati_odv_abilitati.toString()+".");
                }
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati ordine");

        }
    });

}

function kpRiportaMinaccePrivacyImpianto(record) {

    /* kpro@tom28112017 */
    /**
     * @author Tomiello Marco
     * @copyright (c) 2017, Kpro Consulting Srl
     */

    var larghezza = window.innerWidth - 100;
    var altezza = window.innerHeight - 100;

    if (window.innerWidth > 1200) {

        larghezza = 1200;

    }

    openPopup('index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/kpPopupRiportaMinaccePrivacyImpianto/index&id=' + record, '', '', 'auto', larghezza, altezza, '');

}

function kpGeneraPropostaDiFatturaFornitore() {

    /* kpro@tom07092017 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2017, Kpro Consulting Srl
     */

    var idstring = get_real_selected_ids('KpProvvigioni');
    if (idstring.substr('0', '1') == ';') {
        idstring = idstring.substr('1');
    }
    var idarr = idstring.split(';');
    var count = idarr.length;
    var xx = count - 1;

    if (idstring == '' || idstring == ';' || idstring == 'null') {

        alert('Nessuna provvigione selezionata!');

    } else {

        var larghezza = window.innerWidth - 500;
        var altezza = window.innerHeight - 300;

        /*var larghezza = 600;
        var altezza = 300;*/

        openPopup('index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/KpPopupPropostaFatturaDaProvvigioni/index&ids=' + escape(JSON.stringify(idarr)), '', '', 'auto', larghezza, altezza, '');

    }

}

function kpWizardCreazioneQuestionario(record) {

    /* kpro@tom20112017 */
    /**
     * @author Tomiello Marco
     * @copyright (c) 2017, Kpro Consulting Srl
     */

    var larghezza = window.innerWidth - 100;
    var altezza = window.innerHeight - 100;

    if (window.innerWidth > 1200) {

        larghezza = 1200;

    }

    openPopup('index.php?module=SproCore&action=SproCoreAjax&file=KpWizard/KpQuestionario/index&id=' + record, '', '', 'auto', larghezza, altezza, '');

}

function kpAggiornaCommesse() {

    /* kpro@bid27122017 */
    /**
     * @author Bidese Jacopo
     * @copyright (c) 2017, Kpro Consulting Srl
     */

    $('status').style.display = 'inline'; //Inserisce la barra di caricamento
    var res = getFile('index.php?module=SproCore&action=SproCoreAjax&file=Commesse/AggiornaCommesse');
    $('status').style.display = 'none'; //Termina la barra di caricamento

    location.reload();

}

function kpDuplicaSpecialeOrdineDiVendita(record) {

    /* kpro@bid25012018 */
    /**
     * @author Bidese Jacopo
     * @copyright (c) 2018, Kpro Consulting Srl
     */

    var dati_ordine = {
        record: record
    };

    jQuery.ajax({
        url: 'modules/SproCore/CustomViews/PopupDuplicaSpecialeOrdine/ControlloTipoOrdine.php',
        dataType: 'json',
        data: dati_ordine,
        async: true,
        success: function(data) {

            if (data.length > 0) {

                var stati_odv_abilitati = ['Approved','Emessa fattura acconto','Emessa fattura a saldo'];
                var tipologie_odv_abilitate = ['Abbonamento'];

                if (jQuery.inArray(data[0].stato_ordine, stati_odv_abilitati) !== -1) {

                    if (jQuery.inArray(data[0].tipologia_ordine, tipologie_odv_abilitate) !== -1) {

                        var larghezza = window.innerWidth - 100;
                        var altezza = window.innerHeight - 100;

                        if (window.innerWidth > 1200) {

                            larghezza = 1200;

                        }

                        openPopup('index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/PopupDuplicaSpecialeOrdine/index&id=' + record, '', '', 'auto', larghezza, altezza, '');
                    } else {
                        alert("Tipo ordine: " + data[0].tipologia_ordine + "! Non è possibile usare questa funzione per ordini di questo tipo. Le tipologie abilitate sono "+tipologie_odv_abilitate.toString()+".");
                    }
                } else {
                    alert("L'ordine deve essere in stato "+stati_odv_abilitati.toString()+".");
                }
            }

        },
        fail: function() {

            console.error("Errore nel recupero dei dati ordine");

        }
    });
}

function kpCalcolaSituazFormazioneAzienda(record) {

    /* kpro@tom14022018 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2018, Kpro Consulting Srl
     */

    $("status").style.display = "inline";
    var res = getFile("modules/SproCore/SituazFormaz/getSituazFormazManualeMirataAzienda.php?crmid=" + record);
    $("status").style.display = "none";

    alert('Operazione eseguita');

    location.reload();

}

function kpOrganigrammaCreator(record) {

    /* kpro@tom07022018 */
    /**
     * @author Tomiello Marco
     * @copyright (c) 2018, Kpro Consulting Srl
     */

    window.open('modules/SproCore/CustomViews/KpOrganigrammaCreator/index.php?record=' + record, '_blank');

}

function kpAggiornaStatisticheInfortuni() {

    /* kpro@bid20032018 */
    /**
     * @author Bidese Jacopo
     * @copyright (c) 2018, Kpro Consulting Srl
     */

    $("status").style.display = "inline";
    var res = getFile("plugins/script_schedulati/calcolo_statistiche_infortuni.php");
    $("status").style.display = "none";

    alert('Operazione eseguita');

    location.reload();

}

function kpGeneraOdF() {

    /* kpro@bid24042018 */
    /**
     * @author Bidese Jacopo
     * @copyright (c) 2018, Kpro Consulting Srl
     */

    var larghezza = window.innerWidth - 100;
    var altezza = window.innerHeight - 100;

    if (window.innerWidth > 1200) {

        larghezza = 1200;

    }

    openPopup('index.php?module=SproCore&action=SproCoreAjax&file=KpWizard/KpGeneraOdF/index', '', '', 'auto', larghezza, altezza, '');

}

function kpCreaRevisioneProcedura(record) {

    /* kpro@tom03052018 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2018, Kpro Consulting Srl
     */

    $("status").style.display = "inline";
    var res = getFile("modules/SproCore/KpProcedure/SetRevisioneProcedura.php?record=" + record);
    $("status").style.display = "none";

    if( res != "" && res != 0){
        window.open('index.php?module=KpProcedure&parenttab=Qualita&action=DetailView&record=' + res, '_self');
    }
    else{
        alert("Operazione non riuscita! Verificare d'avere i privilegi necessari.");
    }

}

function kpCalcolaSituazioneDocumenti(){

    $("status").style.display = "inline";
    var res = getFile("modules/SproCore/KpSituazioneDocumenti/getSituazioneDocumenti.php");
    $("status").style.display = "none";
    window.open('index.php?module=KpSituazioneDocumenti&action=index', '_self');
    alert('Operazione eseguita');

}

function kpCreaRevisioneOrganigramma(record) {

    /* kpro@tom25052018 */

    /**
     * @author Tomiello Marco
     * @copyright (c) 2018, Kpro Consulting Srl
     */

    $("status").style.display = "inline";
    var res = getFile("modules/SproCore/KpOrganigrammi/SetRevisioneOrganigramma.php?record=" + record);
    $("status").style.display = "none";

    if( res != "" && res != 0){
        window.open('index.php?module=KpOrganigrammi&parenttab=Qualita&action=DetailView&record=' + res, '_self');
    }
    else{
        alert("Operazione non riuscita! Verificare d'avere i privilegi necessari.");
    }

}

function kpCalcolaSituazioneDocumentiFornitori(){

    $("status").style.display = "inline";
    var res = getFile("modules/SproCore/KpSituazioneDocFornit/getSituazioneDocumentiFornitori.php");
    $("status").style.display = "none";
    window.open('index.php?module=KpSituazioneDocFornit&action=index', '_self');
    alert('Operazione eseguita');

}

function kpGeneraRIBA() {

    /* kpro@bid25062018 */

    /**
     * @author Bidese Jacopo
     * @copyright (c) 2018, Kpro Consulting Srl
     */

    var idstring = get_real_selected_ids('Scadenziario');
    if (idstring.substr('0', '1') == ';')
        idstring = idstring.substr('1');
    var idarr = idstring.split(';');
    var count = idarr.length;
    var xx = count - 1;

    var larghezza = window.innerWidth - 500;
    var altezza = window.innerHeight - 300;

    if (idstring == '' || idstring == ';' || idstring == 'null') {
        openPopup('index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/KpPopupGenerazioneRIBA/index&ids=' + escape(JSON.stringify(idarr)), '', '', 'auto', larghezza, altezza, '');
    } else {
        openPopup('index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/KpPopupGenerazioneRIBA/index&ids=' + escape(JSON.stringify(idarr)), '', '', 'auto', larghezza, altezza, '');
    }

}

function kpExportCBI() {

    /* kpro@bid25062018 */

    /**
     * @author Bidese Jacopo
     * @copyright (c) 2018, Kpro Consulting Srl
     */

    var idstring = get_real_selected_ids('KpRIBA');
    if (idstring.substr('0', '1') == ';')
        idstring = idstring.substr('1');
    var idarr = idstring.split(';');
    var count = idarr.length;
    var xx = count - 1;

    if (idstring == '' || idstring == ';' || idstring == 'null') {
        //Inserisci la funzione che deve essere eseguita qualora non fosse stato selezionato alcun record
        alert('Nessuna RIBA selezionata!');
    } else {
        var larghezza = window.innerWidth - 100;
        var altezza = window.innerHeight - 100;

        openPopup('index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/KpPopupEsportazioneRIBA/index&ids=' + escape(JSON.stringify(idarr)), '', '', 'auto', larghezza, altezza, '');
    }

}

function kpApprovazioneFatture(record_detailview="") {

    /* kpro@bid28072018 */

    /**
     * @author Bidese Jacopo
     * @copyright (c) 2018, Kpro Consulting Srl
     */

    if(record_detailview == ""){
        var idstring = get_real_selected_ids('Invoice');
        if (idstring.substr('0', '1') == ';')
            idstring = idstring.substr('1');
        var idarr = idstring.split(';');
        var count = idarr.length;
        var xx = count - 1;
    }
    else{
        var idstring = record_detailview;
        var idarr = [record_detailview, ""];
    }

    if (idstring == '' || idstring == ';' || idstring == 'null') {
        //Inserisci la funzione che deve essere eseguita qualora non fosse stato selezionato alcun record
        alert('Nessuna fattura selezionata!');
    } else {
        var larghezza = window.innerWidth - 100;
        var altezza = window.innerHeight - 100;
        
        openPopup('index.php?module=SproCore&action=SproCoreAjax&file=CustomViews/KpPopupApprovazioneFatture/index&ids=' + escape(JSON.stringify(idarr)), '', '', 'auto', larghezza, altezza, '');
    }

}

function kpWizardConfigurazioneAreaStabilimento(record) {

    /* kpro@bid09072018 */
    /**
     * @author Bidese Jacopo
     * @copyright (c) 2018, Kpro Consulting Srl
     */

    var larghezza = window.innerWidth - 100;
    var altezza = window.innerHeight - 100;

    if (window.innerWidth > 1200) {

        larghezza = 1200;

    }

    openPopup('index.php?module=SproCore&action=SproCoreAjax&file=KpWizard/KpAreeStabilimento/index&id=' + record, '', '', 'auto', larghezza, altezza, '');

}

function kpAggiornaSitRischiDVR() {

    /* kpro@tom25092017 */
    /**
     * @author Tomiello Marco
     * @copyright (c) 2017, Kpro Consulting Srl
     */

    $('status').style.display = 'inline'; //Inserisce la barra di caricamento
    var res = getFile('index.php?module=SproCore&action=SproCoreAjax&file=KpSitRischiDVR/AggiornaSituazioneRischiDVR');
    //window.open('index.php?module=SproCore&action=SproCoreAjax&file=KpSitRischiDVR/AggiornaSituazioneRischiDVR', '_blank');
    $('status').style.display = 'none'; //Termina la barra di caricamento

    location.reload();

}

function kpDuplicaAreaStabilimento(record){

    var dati = {
        id: record
    };

    jQuery.ajax({
        url: 'modules/SproCore/KpAreeStabilimento/DuplicaAreaStabilimentoConRelazioni.php',
        dataType: 'json',
        data: dati,
        async: true,
        success: function(data) {

            console.log(data);

            if(data.length > 0){

                window.open('index.php?module=KpAreeStabilimento&parenttab=DVR&action=EditView&record='+data[0].res, '_self');

            }
            else{
                
                alert('Errore');

            }

        },
        fail: function() {

            console.error("Errore nella duplicazione");

        }
    });

}
