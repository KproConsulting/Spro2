<?php

/* kpro@bid12032018 */
/* kpro@bid290820181150 */
if($type == 'MassEditSave'){
    if($values['kp_ore_effettive'] != '' && $values['kp_ore_effettive'] != null && !ControlloFormatoOre($values['kp_ore_effettive'])) {
        $message = "Le ore effettive non sono nel formato corretto (hh:mm).";
        $status = false;
    }  
    else if($values['worktime'] != '' && $values['worktime'] != null && !ControlloFormatoOre($values['worktime'])) {
        $message = "Le ore consuntive (contrattuali) non sono nel formato corretto (hh:mm).";
        $status = false;
    }  
    else{
        $status = true;
        $message = '';
    }
}
else{
    if(!ControlloFormatoOre($values['kp_ore_effettive'])){
        $message = "Le ore effettive non sono nel formato corretto (hh:mm).";
        $status = false;
        $focus = "kp_ore_effettive";
    }
    else if(!ControlloFormatoOre($values['worktime'])){
        $message = "Le ore consuntive (contrattuali) non sono nel formato corretto (hh:mm).";
        $status = false;
        $focus = "kp_ore_effettive";
    }
    else{
        $status = true;
        $message = '';
    }
}

function ControlloFormatoOre($stringa){

    $result = true;

    if($stringa == null || $stringa == ""){			
        $result = false;
    }
    else{
        $pattern = '/^[0-2]{1}[0-9]{1}.[0-5]{1}[0-9]{1}$/';
        if(preg_match($pattern, $stringa)){				
            $array_delimiters = array(':','.','-','/');
            $array_ora_esploso = explode($array_delimiters[0], str_replace($array_delimiters, $array_delimiters[0], $stringa));
            $ore_controllo = $array_ora_esploso[0];
            $minuti_controllo = $array_ora_esploso[1];				
            if($ore_controllo >= 24 || $minuti_controllo >= 60){					
                $result = false;					
            }				
        }
        else{				
            $result = false;				
        }
    }

    return $result;
}
/* kpro@bid290820181150 end */
?>
