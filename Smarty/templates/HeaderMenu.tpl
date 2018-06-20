{***************************************************************************************
 * The contents of this file are subject to the CRMVILLAGE.BIZ VTECRM License Agreement
 * ("licenza.txt"); You may not use this file except in compliance with the License
 * The Original Code is:  CRMVILLAGE.BIZ VTECRM
 * The Initial Developer of the Original Code is CRMVILLAGE.BIZ.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ***************************************************************************************}
{* crmv@16265 crmv@34885 crmv@2281m crmv@22952 crmv@43942 crmv@44723 crmv@54707 crmv@82419 *}
{if $MENU_LAYOUT.type eq 'modules'}
    <div id="vte_main_menu" class="navbar-collapse collapse navbar-responsive-collapse">
		<ul class="nav navbar-nav">
			<li class="shrink"><a>
			{php}
			// define this function (SDK::setUtil) to override the logo with anything
			if (function_exists('get_logo_override')) echo get_logo_override('project'); else { global $enterprise_project; if (!empty($enterprise_project)) echo '<img src="'.get_logo('project').'" border="0">'; }
			{/php}
			</a></li>
		{foreach item=info from=$VisibleModuleList}
			{assign var="label" value=$info.name|@getTranslatedString:$info.name}
			{assign var="url" value="index.php?module="|cat:$info.name|cat:"&amp;action=index"}
			{if $info.name eq $MODULE_NAME}
				<li class="active"><a href="{$url}">{$label}</a></li>
			{else}
				<li class=""><a href="{$url}">{$label}</a></li>
			{/if}
		{/foreach}
		{if $LAST_MODULE_VISITED neq ''}
			{if $LAST_MODULE_VISITED eq $MODULE_NAME}
				<li class="active"><a href="index.php?module={$LAST_MODULE_VISITED}&amp;action=index">{$LAST_MODULE_VISITED|@getTranslatedString:$LAST_MODULE_VISITED}</a></li>
			{else}
				<li class=""><a href="index.php?module={$LAST_MODULE_VISITED}&amp;action=index">{$LAST_MODULE_VISITED|@getTranslatedString:$LAST_MODULE_VISITED}</a></li>
			{/if}
		{/if}

		{* kpro@tom150620181140 migrazione vte18.05 *}
		{* kpro@tom09012017 *}
		{* Questa modifica permette di avere il raggruppamento "Moduli" anche per i menù con layout a tabs *}
		
		<li class="dropdown" onClick="AllMenuObj.showAllMenu(this);"><a href="javascript:void(0);">{$APP.LBL_MODULES} <b class="caret"></b></a></li>
		
		{* kpro@tom09012017 end *}
		{* kpro@tom150620181140 migrazione vte18.05 end *}

		{if $ENABLE_AREAS eq '1'}
			<li class="dropdown" onClick="UnifiedSearchAreasObj.show(this,'list');"><a href="javascript:void(0);">{$APP.LBL_AREAS} <b class="caret"></b></a></li>
		{/if}
		{if isset($OtherModuleList)}
			<li class="dropdown" onClick="AllMenuObj.showAllMenu(this);"><a href="javascript:void(0);">{$APP.LBL_MODULES} <b class="caret"></b></a></li>
		{/if}
		</ul>
		<ul class="nav navbar-nav navbar-right">
			{if $smarty.session.MorphsuitZombie eq false && $IS_ADMIN eq '1'}
				{* crmv@75301 *}
				{if $HEADER_OVERRIDE.settings_icon}
					{$HEADER_OVERRIDE.settings_icon}
				{else}
					<li class="shrink">
						<a href="index.php?module=Settings&amp;action=index&amp;parenttab=Settings&amp;reset_session_menu_tab=true">
							<img src="{'settingsBtn.png'|resourcever}" title="{'Settings'|getTranslatedString:'Settings'}" border=0>
						</a>
					</li>
				{/if}
				{* crmv@75301e *}
			{/if}
			
			{if !$ISVTEDESKTOP}
			 	{* crmv@75301 *}
				{if $HEADER_OVERRIDE.user_icon}
					{$HEADER_OVERRIDE.user_icon}
				{else}
					<li class="shrink" onclick="showOverAll(this,'Preferences_sub');">
						<a>{$CURRENT_USER_ID|getUserAvatarImg:"style='cursor:pointer;'":'menu'}</a>
					</li>
				{/if}
				{* crmv@75301e *}
			{/if}
			<li class="shrink">
				<a><img id="logo" src="{php}echo get_logo('header');{/php}" alt="{$APP.LBL_BROWSER_TITLE}" title="{$APP.LBL_BROWSER_TITLE} - {php}if (function_exists('getVTENumberUserLabel')) echo getVTENumberUserLabel();{/php}" border="0" height="90%"></a>
			</li>
			<li>
			</li>
		</ul>
    </div>
    
	<div class="drop_mnu_all" id="OtherModuleList_sub">
		<table cellspacing="0" cellpadding="5" border="0" class="small" width="100%">
		<tr>
			<td align="right">
				<div class="form-group moduleSearch">
					<input type="text" class="form-control searchBox" id="menu_search_text" placeholder="{$APP.LBL_SEARCH_MODULE}" onclick="AllMenuObj.clearMenuSearchText(this)" onblur="AllMenuObj.restoreMenuSearchDefaultText(this)" />
					<span class="cancelIcon">
						<i class="vteicon md-link md-sm" id="menu_search_icn_canc" style="display:none" title="Reset" onclick="AllMenuObj.cancelMenuSearchSearchText()">cancel</i>&nbsp;	
					</span>
					<span class="searchIcon">
						<i class="vteicon md-link" id="menu_search_icn_go" title="{$APP.LBL_FIND}" onclick="AllMenuObj.searchInMenu();">search</i>
					</span>
				</div>
			</td>
		</tr>
		</table>
		{assign var="count" value=0}
		{foreach item=info from=$OtherModuleList}
			{assign var="url" value="index.php?module="|cat:$info.name|cat:"&action=index"}
			{if $count eq 0}
				{assign var="div_open" value=true}
				<div style="float:left"><table cellspacing="0" cellpadding="0" border="0" class="small" width="100%">
			{/if}
			{assign var="count" value=$count+1}
			<tr>
				<td><a class="drop_down menu_entry" href="{$url}" style="padding:5px;">{$info.translabel}</a></td>
			</tr>
			{if $count eq 13}
				</table></div>
				{assign var="count" value=0}
				{assign var="div_open" value=false}
			{/if}
		{/foreach}
		{if $div_open eq true}
			</table></div>
		{/if}
	</div>
{* crmv@82419e *}
{elseif $MENU_LAYOUT.type eq 'tabs'}

	<div id="vte_main_menu" class="navbar-collapse collapse navbar-responsive-collapse">
		<ul class="nav navbar-nav">
			<li class="shrink"><a>
			{php}
			// define this function (SDK::setUtil) to override the logo with anything
			if (function_exists('get_logo_override')) echo get_logo_override('project'); else { global $enterprise_project; if (!empty($enterprise_project)) echo '<img src="'.get_logo('project').'" border="0">'; }
			{/php}
			</a></li>
			{foreach key=maintabs item=detail from=$HEADERS}

				{* kpro@tom150620181140 migrazione vte18.05 *}
				{* kpro@tom09012017 *}
				{if $maintabs ne 'Analytics' && $maintabs ne 'Tools' && $maintabs ne 'Inventory' }
				{* kpro@tom09012017 end *}
				{* kpro@tom150620181140 migrazione vte18.05 end *}
				
					{if $maintabs ne $CATEGORY}
						<li class="dropdown" onmouseover="fnDropDown(this,'{$maintabs}_sub');" onmouseout="fnHideDrop('{$maintabs}_sub');"><a href="index.php?module={$detail[0]}&amp;action=index&amp;parenttab={$maintabs}">{$APP[$maintabs]} <b class="caret"></b></a></li>
					{else}
						<li class="dropdown active" onmouseover="fnDropDown(this,'{$maintabs}_sub');" onmouseout="fnHideDrop('{$maintabs}_sub');"><a href="index.php?module={$detail[0]}&amp;action=index&amp;parenttab={$maintabs}">{$APP[$maintabs]} <b class="caret"></b></a></li>
					{/if}

				{* kpro@tom150620181140 migrazione vte18.05 *}
				{* kpro@tom09012017 *}
				{/if}
				{* kpro@tom09012017 end *}
				{* kpro@tom150620181140 migrazione vte18.05 end *}

			{/foreach}

			{* kpro@tom150620181140 migrazione vte18.05 *}
			{* kpro@tom09012017 *}
			{* Questa modifica permette di avere il raggruppamento "Moduli" anche per i menù con layout a tabs *}
			
			<li class="dropdown" onClick="AllMenuObj.showAllMenu(this);"><a href="javascript:void(0);">{$APP.LBL_MODULES} <b class="caret"></b></a></li>
			
			{* kpro@tom09012017 end *}
			{* kpro@tom150620181140 migrazione vte18.05 end *}

			{if $ENABLE_AREAS eq '1'}
				<li class="dropdown" onClick="UnifiedSearchAreasObj.show(this,'list');"><a href="javascript:void(0);">{$APP.LBL_AREAS} <b class="caret"></b></a></li>
			{/if}
		</ul>
		<ul class="nav navbar-nav navbar-right">
			{if $smarty.session.MorphsuitZombie eq false && $IS_ADMIN eq '1'}
				{* crmv@75301 *}
				{if $HEADER_OVERRIDE.settings_icon}
					{$HEADER_OVERRIDE.settings_icon}
				{else}
					<li class="shrink">
						<a href="index.php?module=Settings&amp;action=index&amp;parenttab=Settings&amp;reset_session_menu_tab=true">
							<img src="{'settingsBtn.png'|resourcever}" title="{'Settings'|getTranslatedString:'Settings'}" border=0>
						</a>
					</li>
				{/if}
				{* crmv@75301e *}
			{/if}
			
			{if !$ISVTEDESKTOP}
			 	{* crmv@75301 *}
				{if $HEADER_OVERRIDE.user_icon}
					{$HEADER_OVERRIDE.user_icon}
				{else}
					<li class="shrink" onclick="showOverAll(this,'Preferences_sub');">
						<a>{$CURRENT_USER_ID|getUserAvatarImg:"style='cursor:pointer;'":'menu'}</a>
					</li>
				{/if}
				{* crmv@75301e *}
			{/if}
			<li class="shrink">
				<a><img id="logo" src="{php}echo get_logo('header');{/php}" alt="{$APP.LBL_BROWSER_TITLE}" title="{$APP.LBL_BROWSER_TITLE} - {php}if (function_exists('getVTENumberUserLabel')) echo getVTENumberUserLabel();{/php}" border=0></a>
			</li>
			<li>
			</li>
		</ul>
	</div>
	
	<TABLE border=0 cellspacing=0 cellpadding=2 width=100% class="level2Bg">
	<tr>
		<td>
			<table border=0 cellspacing=0 cellpadding=0>
			<tr>
				{foreach key=number item=modules from=$QUICKACCESS.$CATEGORY}
					{assign var="modulelabel" value=$modules[1]|@getTranslatedString:$modules[0]}
   					{* Use Custom module action if specified *}
					{assign var="moduleaction" value="index"}
   					{if isset($modules[2])}
   						{assign var="moduleaction" value=$modules[2]}
   					{/if}
					{if $modules.0 eq $MODULE_NAME}
						<td class="level2SelTab" nowrap><a href="index.php?module={$modules.0}&amp;action={$moduleaction}&amp;parenttab={$CATEGORY}">{$modulelabel}</a></td>
					{else}
						<td class="level2UnSelTab" nowrap> <a href="index.php?module={$modules.0}&amp;action={$moduleaction}&amp;parenttab={$CATEGORY}">{$modulelabel}</a> </td>
					{/if}
				{/foreach}
			</tr>
			</table>
		</td>
	</tr>
	</TABLE>
	
	{foreach name=parenttablist key=parenttab item=details from=$QUICKACCESS}
		<div class="drop_mnu" id="{$parenttab}_sub" onmouseout="fnHideDrop('{$parenttab}_sub')" onmouseover="fnShowDrop('{$parenttab}_sub')" style="width:150px;">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				{foreach name=modulelist item=modules from=$details}
					{assign var="modulelabel" value=$modules[1]|@getTranslatedString:$modules[0]}
					{* Use Custom module action if specified *}
					{assign var="moduleaction" value="index"}
				   	{if isset($modules[2])}
				   		{assign var="moduleaction" value=$modules[2]}
				   	{/if}
					<tr><td><a href="index.php?module={$modules.0}&amp;action={$moduleaction}&amp;parenttab={$parenttab}" class="drop_down">{$modulelabel}</a></td></tr>
				{/foreach}
			</table>
		</div>
	{/foreach}

	{* kpro@tom150620181140 migrazione vte18.05 *}
	{* kpro@tom09012017 *}
	{* Questa modifica permette di avere il raggruppamento "Moduli" anche per i menù con layout a tabs *}
	
	<div class="drop_mnu_all" id="OtherModuleList_sub">
		<table cellspacing="0" cellpadding="5" border="0" class="small" width="100%">
		<tr>
			<td align="right">
				<div class="form-group moduleSearch">
					<input type="text" class="form-control searchBox" id="menu_search_text" placeholder="{$APP.LBL_SEARCH_MODULE}" onclick="AllMenuObj.clearMenuSearchText(this)" onblur="AllMenuObj.restoreMenuSearchDefaultText(this)" />
					<span class="cancelIcon">
						<i class="vteicon md-link md-sm" id="menu_search_icn_canc" style="display:none" title="Reset" onclick="AllMenuObj.cancelMenuSearchSearchText()">cancel</i>&nbsp;	
					</span>
					<span class="searchIcon">
						<i class="vteicon md-link" id="menu_search_icn_go" title="{$APP.LBL_FIND}" onclick="AllMenuObj.searchInMenu();">search</i>
					</span>
				</div>
			</td>
		</tr>
		</table>
		{assign var="count" value=0}
		{foreach item=info from=$OtherModuleList}
			{assign var="url" value="index.php?module="|cat:$info.name|cat:"&action=index"}
			{if $count eq 0}
				{assign var="div_open" value=true}
				<div style="float:left"><table cellspacing="0" cellpadding="0" border="0" class="small" width="100%">
			{/if}
			{assign var="count" value=$count+1}
			<tr>
				<td><a class="drop_down menu_entry" href="{$url}" style="padding:5px;">{$info.translabel}</a></td>
			</tr>
			{if $count eq 13}
				</table></div>
				{assign var="count" value=0}
				{assign var="div_open" value=false}
			{/if}
		{/foreach}
		{if $div_open eq true}
			</table></div>
		{/if}
	</div>
	
	{* kpro@tom09012017 end *}
	{* kpro@tom150620181140 migrazione vte18.05 end *}

{/if}

{* scripts *}
<script type="text/javascript">
	{literal}
	var AllMenuObj = {
	
		menu_search_submitted : false,
		
		initialize: function() {
			jQuery('#menu_search_text').keyup(function() {
				AllMenuObj.searchInMenu();
			});
		},
		
		showAllMenu : function(currObj) {
			var olayernode = VtigerJS_DialogBox._olayer(true);
			olayernode.style.opacity = '0';

			fnDropDown(currObj,'OtherModuleList_sub');
			document.getElementById('OtherModuleList_sub').style.zIndex = findZMax()+1;
			//crmv@56399 - adjust div position, 2 px more left
			var actual_div_pos_tmp = jQuery('#OtherModuleList_sub').css('left').match(/(\d*\.?\d*)(.*)/);
			var actual_div_pos = parseFloat(actual_div_pos_tmp[1], 10) || 0;
			var new_div_pos = (actual_div_pos - 2) + actual_div_pos_tmp[2];
			jQuery('#OtherModuleList_sub').css('left',new_div_pos);
			//crmv@56399e
			jQuery('#OtherModuleList_sub').appendTo(document.body);
			
			AllMenuObj.clearMenuSearchText(document.getElementById('menu_search_text'));
			jQuery('#__vtigerjs_dialogbox_olayer__').click(function(){
				AllMenuObj.hideAllMenu();
			});
		},
		
		hideAllMenu : function () {
			fnHideDrop('OtherModuleList_sub');
			VtigerJS_DialogBox.unblock();
			jQuery('#__vtigerjs_dialogbox_olayer__').remove();
		},
		
		searchInMenu : function() {
			AllMenuObj.menu_search_submitted = true;
			jQuery('.highlighted').removeClass('highlighted');
			jQuery('.drop_down_hover').removeClass('drop_down_hover');
			var searchText = jQuery('#menu_search_text').val();
			if (searchText == '') {
				jQuery('#menu_search_icn_canc').hide();
			} else {
				jQuery('#menu_search_icn_canc').show();
			}
			if (searchText != '') {
				jQuery("#OtherModuleList_sub .menu_entry").each(function(i, ele){
					var content = jQuery(ele).text();
					var contentNew = content.replace( new RegExp(searchText, "gi"), "<span class='highlighted'>$&</span>" );
					if (contentNew != content) {
						jQuery(ele).html(contentNew);
					}
				});
				if (jQuery("#OtherModuleList_sub .highlighted").length == 1) {
					var el = jQuery("#OtherModuleList_sub .highlighted");
					el.parent().addClass('drop_down_hover');
					el.removeClass('highlighted');
					jQuery(document).keyup(function(e) {
					    if(e.which == 13) {
							if (jQuery("#OtherModuleList_sub").css('display') == 'block' && el.parent().attr('href') != undefined && el.parent().attr('href') != '') {
								location.href = el.parent().attr('href');
							}
					    }
					});
				}
			}
		},
		
		clearMenuSearchText : function(elem) {
			var jelem = jQuery(elem);
			jelem.focus();
			jelem.val('');
			AllMenuObj.restoreMenuSearchDefaultText(elem);
		},
		
		restoreMenuSearchDefaultText : function(elem) {
			var jelem = jQuery(elem);
			if (jelem.val() == '') {
				jQuery('#menu_search_icn_canc').hide();
				if (AllMenuObj.menu_search_submitted == true) {
					AllMenuObj.searchInMenu();
				} else {
					jelem.val('');
				}
				jelem.focus();
			}
		},
		
		cancelMenuSearchSearchText : function() {
			jQuery('#menu_search_text').val('');
			AllMenuObj.restoreMenuSearchDefaultText(document.getElementById('menu_search_text'));
		}
		
	}
	{/literal}
	
	{if $MENU_LAYOUT.type eq 'modules'}
		AllMenuObj.initialize();
	{/if}

	{* kpro@tom150620181140 migrazione vte18.05 *}
	{* kpro@tom09012017 *}
	{* Questa modifica permette di avere il raggruppamento "Moduli" anche per i menù con layout a tabs *}

	{if $MENU_LAYOUT.type eq 'tabs'}
		AllMenuObj.initialize();
	{/if}

	{* kpro@tom09012017 end *}
	{* kpro@tom150620181140 migrazione vte18.05 end *}

</script>
