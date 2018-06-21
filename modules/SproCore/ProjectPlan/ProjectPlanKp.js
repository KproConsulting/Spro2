

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
    jQuery('#KpGanttPianificazioniTab').hide();
    jQuery('#KpCaricoPianificazioniTab').hide();
    jQuery('#KpSchedulerPianificazioniTab').hide();
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

    if(tabname == 'KpGanttPianificazioniTab'){
        jQuery('#'+tabname).show();
        jQuery('#KpCaricoPianificazioniTab').hide();
        jQuery('#KpSchedulerPianificazioniTab').hide();
    }
    else if(tabname == 'KpCaricoPianificazioniTab'){
        jQuery('#'+tabname).show();
        jQuery('#KpGanttPianificazioniTab').hide();
        jQuery('#KpSchedulerPianificazioniTab').hide();
    }
    else if(tabname == 'KpSchedulerPianificazioniTab'){
        jQuery('#'+tabname).show();
        jQuery('#KpGanttPianificazioniTab').hide();
        jQuery('#KpCaricoPianificazioniTab').hide();
    }
    else{
        jQuery('#KpGanttPianificazioniTab').hide();
        jQuery('#KpCaricoPianificazioniTab').hide();
        jQuery('#KpSchedulerPianificazioniTab').hide();
    }

    if (tabname == 'KpGanttPianificazioniTab' || tabname == 'KpCaricoPianificazioniTab' || tabname == 'KpSchedulerPianificazioniTab') {
        jQuery('#turboLiftContainer').hide();
        jQuery('#DetailViewWidgets').hide();
        if (!kpLayoutInizializzato) {
            kpLayoutInizializzato = true;
        }
    } else {
        jQuery('#turboLiftContainer').show();
        jQuery('#DetailViewWidgets').show();
    }

    jQuery('#' + contId + ' .dvtSelectedCell').removeClass('dvtSelectedCell').addClass('dvtUnSelectedCell');
    jQuery(self).removeClass('dvtUnSelectedCell').addClass('dvtSelectedCell');
	jQuery('#' + tabname).show();
	
	var iframe_src = jQuery('#iframe'+tabname).prop('src');
	jQuery('#iframe'+tabname).prop('src', iframe_src);
	
}
