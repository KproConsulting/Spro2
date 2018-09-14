/************************************************************************************
 * The contents of this file are subject to the vtiger CRM Public License Version 1.0
 * ("License"); You may not use this file except in compliance with the License
 * The Original Code is:  vtiger CRM Open Source
 * The Initial Developer of the Original Code is vtiger.
 * Portions created by vtiger are Copyright (C) vtiger.
 * Portions created by CRMVILLAGE.BIZ are Copyright (C) CRMVILLAGE.BIZ.
 * All Rights Reserved.
 ************************************************************************************/

loadFileJs('modules/Products/multifile.js');
loadFileJs('include/js/Merge.js');

function updateListPrice(unitprice,fieldname, oSelect)
{
	if(oSelect.checked == true)
	{
		document.getElementById(fieldname).style.visibility = 'visible';
		document.getElementById(fieldname).value = unitprice;
	}else
	{
		document.getElementById(fieldname).style.visibility = 'hidden';
	}
}

function check4null(form)
{
	var isError = false;
	var errorMessage = "";
	if (trim(form.productname.value) =='') {
		isError = true;
		errorMessage += "\n Product Name";
		form.productname.focus();
	}
	if (isError == true) {
		alert(alert_arr.MISSING_REQUIRED_FIELDS + errorMessage);
		return false;
	}
	return true;
}

function set_return(product_id, product_name) {
	//crmv@29190
	var formName = getReturnFormName();
	var form = getReturnForm(formName);
	//crmv@29190e
	form.parent_name.value = product_name;
	form.parent_id.value = product_id;
	disableReferenceField(form.parent_name,form.parent_id,form.parent_id_mass_edit_check);	//crmv@29190
}

function set_return_specific(product_id, product_name) {
	//crmv@29190
	var formName = getReturnFormName();
	var form = getReturnForm(formName);
	//crmv@29190e
	form.product_name.value = product_name;
	form.product_id.value = product_id;
	disableReferenceField(form.product_name,form.product_id,form.product_id_mass_edit_check);	//crmv@29190
}

function add_data_to_relatedlist(entity_id,recordid) {
	opener.document.location.href="index.php?module={RETURN_MODULE}&action=updateRelations&smodule={SMODULE}&destination_module=Products&entityid="+entity_id+"&parentid="+recordid;
}

//crmv@16267 crmv@48407 crmv@51631 crmv@55228
function set_return_inventory(product_id,product_name,unitprice,qtyinstock,taxstr,curr_row,desc,subprod_id,product_code,unit_cost) {
	var subprod = subprod_id.split("::");
	var jQuery_obj = jQuery;
	var popup = false;
	if (jQuery("form[name=EditView] #subproduct_ids"+curr_row).length <= 0){
		jQuery_obj = parent.jQuery;
		popup = true;
	}
	jQuery_obj("form[name=EditView] #subproduct_ids"+curr_row).val(subprod[0]);
	jQuery_obj("form[name=EditView] #subprod_names"+curr_row).val(subprod[1]);
	jQuery_obj("form[name=EditView] #productName"+curr_row).val(product_name);
	jQuery_obj("form[name=EditView] #hdnProductId"+curr_row).val(product_id);
	disableReferenceField(jQuery_obj("form[name=EditView] #hdnProductId"+curr_row));
	jQuery_obj("form[name=EditView] #listPrice"+curr_row).val(unitprice);
	jQuery_obj("form[name=EditView] #productDescription"+curr_row).val(desc);
	jQuery_obj("form[name=EditView] #hdnProductcode"+curr_row).val(product_code);
	jQuery_obj("form[name=EditView] #qtyInStock"+curr_row).html(qtyinstock);
	jQuery_obj("form[name=EditView] #qty"+curr_row).focus();
	if (unit_cost != undefined) jQuery_obj("form[name=EditView] #unit_cost"+curr_row).val(unit_cost);
	if (popup) parent.loadTaxes_Ajax(curr_row); else loadTaxes_Ajax(curr_row);
}

function set_return_inventory_po(product_id,product_name,unitprice,taxstr,curr_row,desc,subprod_id,product_code,unit_cost) {
	var subprod = subprod_id.split("::");
	var jQuery_obj = jQuery;
	var popup = false;
	if (jQuery("form[name=EditView] #subproduct_ids"+curr_row).length <= 0){
		jQuery_obj = parent.jQuery;
		popup = true;
	}
	jQuery_obj("form[name=EditView] #subproduct_ids"+curr_row).val(subprod[0]);
	jQuery_obj("form[name=EditView] #subprod_names"+curr_row).val(subprod[1]);
	jQuery_obj("form[name=EditView] #productName"+curr_row).val(product_name);
	jQuery_obj("form[name=EditView] #hdnProductId"+curr_row).val(product_id);
	disableReferenceField(jQuery_obj("form[name=EditView] #productName"+curr_row));
	jQuery_obj("form[name=EditView] #listPrice"+curr_row).val(unitprice);
	jQuery_obj("form[name=EditView] #productDescription"+curr_row).val(desc);
	jQuery_obj("form[name=EditView] #hdnProductcode"+curr_row).val(product_code);
	jQuery_obj("form[name=EditView] #qty"+curr_row).focus();
	if (unit_cost != undefined) jQuery_obj("form[name=EditView] #unit_cost"+curr_row).val(unit_cost);
	if (popup) parent.loadTaxes_Ajax(curr_row); else loadTaxes_Ajax(curr_row);
}
//crmv@16267e crmv@48407e crmv@51631e crmv@55228e

function getImageListBody() {
	if (browser_ie) {
		var ImageListBody=getObj("ImageList")
	} else if (browser_nn4 || browser_nn6) {
		if (getObj("ImageList").childNodes.item(0).tagName=="TABLE") {
			var ImageListBody=getObj("ImageList")
		} else {
			var ImageListBody=getObj("ImageList")
		}
	}
	return ImageListBody;
}

/* kpro@tom130920181516 */
function kp_set_return_inventory(product_id, product_name, unitprice, taxstr, curr_row, desc, product_code, kp_prezzo) {	//crmv@16267
	var subprod = subprod_id.split("::");
	//crmv@21048m
	
	parent.document.EditView.elements["subproduct_ids"+curr_row].value = subprod[0];
	parent.document.getElementById("subprod_names"+curr_row).innerHTML = subprod[1];

	parent.document.EditView.elements["productName"+curr_row].value = product_name;
	parent.document.EditView.elements["hdnProductId"+curr_row].value = product_id;
	disableReferenceField(parent.document.EditView.elements["productName"+curr_row]);	//crmv@29190
	
	parent.document.EditView.elements["listPrice"+curr_row].value = formatUserNumber(unitprice);
	//crmv@16267
	//parent.document.EditView.elements["comment"+curr_row].value = desc;
	parent.document.EditView.elements["productDescription"+curr_row].value = desc;
	parent.document.EditView.elements["hdnProductcode"+curr_row].value = product_code;
	//crmv@16267e
	//getOpenerObj("unitPrice"+curr_row).innerHTML = unitprice;
	if(getOpenerObj("qtyInStock"+curr_row) != null)
	getOpenerObj("qtyInStock"+curr_row).innerHTML = qtyinstock;
	
	if(!isNaN(parseFloat(kp_prezzo))) {
		parent.document.EditView.elements["listPrice"+curr_row].value = formatUserNumber(kp_prezzo);
	}
	
	var tax_array = new Array();
	var tax_details = new Array();
	tax_array = taxstr.split(',');
	for(var i=0;i<tax_array.length;i++)
	{
		tax_details = tax_array[i].split('=');
	}
	
	parent.document.EditView.elements["qty"+curr_row].focus();
}
/* kpro@tom130920181516 end */

/* kpro@bid200620181800 migrazione vte18.05 */
var module = gVTModule;
jQuery.getScript('modules/SproCore/'+module+'/'+module+'Kp.js', function(){
	
});
/* kpro@bid200620181800 migrazione vte18.05 */