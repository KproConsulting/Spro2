/*+*************************************************************************************
 * The contents of this file are subject to the VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is: VTECRM
 * The Initial Developer of the Original Code is VTECRM LTD.
 * Portions created by VTECRM LTD are Copyright (C) VTECRM LTD.
 * All Rights Reserved.
 ***************************************************************************************/

//crmv@29190 crmv@69568
function set_return(product_id, product_name) {
    var formName = getReturnFormName();
    var form = (formName ? getReturnForm(formName) : null);
    if (form) {
        form.parent_name.value = product_name;
        form.parent_id.value = product_id;
        disableReferenceField(form.parent_name, form.parent_id, form.parent_id_mass_edit_check);
    }
}
//crmv@29190e crmv@69568e

/* kpro@tom030420181510 */
var kpLayoutInizializzato = false;
var nome_rilevazione;

var jkp_azienda_display;
var jkp_area_stab_display;
var jjscal_field_kp_data_rilevazione;
var jkp_nome_rilevazione;

jQuery(document).ready(function(){

    jkp_azienda_display = jQuery("#kp_azienda_display");
    jkp_area_stab_display = jQuery("#kp_area_stab_display");
    jjscal_field_kp_data_rilevazione = jQuery("#jscal_field_kp_data_rilevazione");
    jkp_nome_rilevazione = jQuery("#kp_nome_rilevazione");

    jkp_azienda_display.focusout(function(){
        setNomeRilevazione();
    });

    jkp_area_stab_display.focusout(function(){
        setNomeRilevazione();
    });

    jjscal_field_kp_data_rilevazione.change(function(){
        setNomeRilevazione();
    });

});

function setNomeRilevazione(){

    nome_rilevazione = "Rilevazione ";

    var nome_azienda = jkp_azienda_display.val();
    var nome_area = jkp_area_stab_display.val();
    var data = jjscal_field_kp_data_rilevazione.val();

    nome_rilevazione += nome_area + " (" + nome_azienda + ") del " + data;

    jkp_nome_rilevazione.val(nome_rilevazione);

}

function kpChangeDetailTab(module, crmid, tabname, self, goto) {
    return kpChangeTab(module, crmid, tabname, self, 'detail', goto);
}

function kpChangeTab(module, crmid, tabname, self, mode, goto) {
    var panelid = parseInt(tabname);

    if (typeof mode == 'undefined' || mode === null) {
        // try to autodetect
        if (jQuery('#EditViewTabs').length > 0) {
            mode = 'edit';
        } else {
            mode = 'detail';
        }
    }

    // hide sharkpanel (it's custom)
    jQuery('#potPanelMainDiv').hide();

    if (panelid > 0) {
        // standard tab
        kpGoToPanelTab(module, crmid, panelid, self, mode, goto);
    } else {
        // tab with name (extra tab)
        kpGoToNamedTab(module, crmid, tabname, self, mode, goto);
    }
}

function kpGoToPanelTab(module, crmid, panelid, self, mode, goto) {
    var contId = (mode == 'detail' ? 'DetailViewTabs' : 'EditViewTabs');

    if (!window.panelBlocks) {
        console.error('Missing panelBlocks variable');
        return;
    }
    var showPanel = panelBlocks[panelid],
        showBlocks = showPanel ? showPanel.blockids : [],
        classname = (mode == 'detail' ? 'detailBlock' : 'editBlock');

    jQuery('div.' + classname).each(function(idx, el) {
        var bid_str = el.id.replace('block_', '');
        var bid = parseInt(bid_str);
        if (showBlocks.indexOf(bid) >= 0 || showBlocks.indexOf(bid_str) >= 0) {
            // show it!
            jQuery(el).show();
        } else {
            // hide it!
            jQuery(el).hide();
        }
    });
    jQuery('#' + contId + ' .dvtSelectedCell').removeClass('dvtSelectedCell').addClass('dvtUnSelectedCell');
    if (!self) {
        self = jQuery('#' + contId + ' td[data-panelid=' + panelid + ']');
    }
    jQuery(self).removeClass('dvtUnSelectedCell').addClass('dvtSelectedCell');

    // show bocks and related divs
    jQuery('.detailTabsMainDiv').hide();
    jQuery('#DetailViewBlocks').show();
    jQuery('#turboLiftContainer').show();
    jQuery('#DetailViewWidgets').show();
    jQuery('#calendarExtraTable').show(); // crmv@107341
    // products block is handled like a block, but it's in a different div
    jQuery('#proTab').show();
    jQuery('#finalProTab').show();

    //crmv@104562
    if (window.HistoryTabScript) HistoryTabScript.hideTab();
    if (window.GanttScript) GanttScript.hideTab();
    if (window.ProcessScript) ProcessScript.hideTab();
    jQuery('#KpRilevazioneRischiTab').hide();
    //crmv@104562e

    window.currentPanelId = panelid;

    // align the related
    if (mode == 'detail' && typeof window.alignTabRelated == 'function') {
        alignTabRelated(panelid, goto);
    }

}

function kpGoToNamedTab(module, crmid, tabname, self, mode, goto) {
    var contId = (mode == 'detail' ? 'DetailViewTabs' : 'EditViewTabs');

    if (tabname == 'DetailViewBlocks') {
        jQuery('.detailTabsMainDiv').hide();
    } else {
        jQuery('#DetailViewBlocks').hide();
        jQuery('#proTab').hide();
        jQuery('#finalProTab').hide();
        jQuery('#calendarExtraTable').hide(); // crmv@107341
        jQuery('.detailTabsMainDiv').show();
        jQuery('#' + tabname).show();
    }
    if (tabname == 'DetailViewBlocks') {
        jQuery('#proTab').show();
        jQuery('#finalProTab').show();
        jQuery('#calendarExtraTable').show(); // crmv@107341
        jQuery('#turboLiftContainer').show();
        jQuery('#DetailViewWidgets').show();
    } else if (tabname == 'detailCharts' && window.VTECharts) VTECharts.refreshAll();

    if (window.ProcessScript) {
        if (tabname == 'ProcessGraph') ProcessScript.showTab(module, crmid);
        else ProcessScript.hideTab();
    }
    if (window.HistoryTabScript) {
        if (tabname == 'HistoryTab') HistoryTabScript.showTab(module, crmid);
        else HistoryTabScript.hideTab();
    }
    //crmv@104562
    if (window.GanttScript) {
        if (tabname == 'Gantt') GanttScript.showTab(module, crmid);
        else GanttScript.hideTab();
    }
    //crmv@104562e

    if (tabname == 'KpRilevazioneRischiTab') {
        jQuery('#KpRilevazioneRischiTab').show();
        jQuery('#turboLiftContainer').hide();
        jQuery('#DetailViewWidgets').hide();
        if (!kpLayoutInizializzato) {
            kpLayoutInizializzato = true;
            inizializzazioneLayoutKp();
        }
    } else {
        jQuery('#KpRilevazioneRischiTab').hide();
        jQuery('#turboLiftContainer').show();
        jQuery('#DetailViewWidgets').show();
    }

    jQuery('#' + contId + ' .dvtSelectedCell').removeClass('dvtSelectedCell').addClass('dvtUnSelectedCell');
    jQuery(self).removeClass('dvtUnSelectedCell').addClass('dvtSelectedCell');
    jQuery('#' + tabname).show();
}
/* kpro@tom030420181510 end */