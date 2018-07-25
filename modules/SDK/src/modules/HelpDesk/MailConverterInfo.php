<?php

/* crmv@2043m crmv@60095 crmv@87556 */
global $sdk_mode;
//mycrmv@166625 - standard code not needed
/* kpro@bid250720181755 */
/*
if ($sdk_mode == "") {
	if ($fieldname == 'parent_id' && $_REQUEST['isDuplicate'] != 'true') { // crmv@157204
		if(isset($_REQUEST['parent_id']) && $_REQUEST['parent_id'] != '') {
			$value = $_REQUEST['parent_id'];
		}
		if($value != '') {
			$parent_module = getSalesEntityType($value);
			if (!in_array($parent_module,array('Contacts','Accounts','Leads'))) {
				$value = '';
			}
		}
		$col_fields['parent_id'] = $value;
	}
}
*/
/* kpro@bid250720181755 end */
//mycrmv@166625e
$success = true;
?>
