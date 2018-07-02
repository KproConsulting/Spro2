/* kpro@bid04122017 */
jQuery(document).ready(function() {
	var avviso_fatturazione = jQuery("input[name='kp_avviso_fattura']").prop("checked");
	if(!avviso_fatturazione){
		jQuery("select[name='invoicestatus'] option[value*='Proforma']").hide();
	}
});
/* kpro@bid04122017 end */
