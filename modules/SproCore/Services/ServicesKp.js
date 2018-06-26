/* kpro@bid020320181600 */
jQuery(document).ready(function() {

	jQuery('input[type="checkbox"].small').each(function(){
		var str_onclick = String(jQuery(this).prop('onclick'));
		if(str_onclick.includes("tax2") || str_onclick.includes("tax3") || str_onclick.includes("tax4") || str_onclick.includes("tax5")){
			jQuery(this).closest('tr').hide();
		}
	});

});
/* kpro@bid020320181600 end */
