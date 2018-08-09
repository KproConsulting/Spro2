<?php 
include_once('../../../config.inc.php'); 
chdir($root_directory); 
require_once('include/utils/utils.php'); 
include_once('vtlib/Vtiger/Module.php'); 
require_once('modules/SproCore/SDK/KpSDK.php'); 
$Vtiger_Utils_Log = true; 
global $adb, $table_prefix;
session_start(); 

//Moduli per Organigrammi
/*
KpSDK::registraModulo($nome_modulo = 'KpOrganigrammi', $label_modulo = 'Organigrammi', $label_modulo_singolare = 'Organigramma', $tipo_campo = 'Testo', $nome_campo = 'kp_nome_organigramma', $label_campo = 'Nome Organigramma', $privilegi = 'Private', $merge = true, $import_export = true, $messaggi = true, $documenti = true, $calendario = true, $processi = true, $homeview = true);

KpSDK::registraCampo($nome_modulo = 'KpOrganigrammi', $blocco = 'LBL_KPORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_data_organigramma', $label_campo = 'Data Organigramma', $uitype = '5', $columntype = 'date', $typeofdata = 'D~M', $readonly = '1', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpOrganigrammi', $blocco = 'LBL_KPORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_id_sis_origine', $label_campo = 'Id Sistema Origine per Migrazione', $uitype = '1', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '100', $helpinfo = 'In tale campo viene memorizzato l\'ID del record nel sistema da cui è stato migrato');


KpSDK::registraModulo($nome_modulo = 'KpEntitaOrganigrammi', $label_modulo = 'Entita Organigrammi', $label_modulo_singolare = 'Entita Organigramma', $tipo_campo = 'Testo', $nome_campo = 'kp_nome_entita', $label_campo = 'Nome Entita', $privilegi = 'Private', $merge = true, $import_export = true, $messaggi = true, $documenti = true, $calendario = true, $processi = true, $homeview = true);

KpSDK::registraCampo($nome_modulo = 'KpEntitaOrganigrammi', $blocco = 'LBL_KPENTITAORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_in_staff', $label_campo = 'In Staff', $uitype = '56', $columntype = 'varchar(3)', $typeofdata = 'C~O', $readonly = '99', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpEntitaOrganigrammi', $blocco = 'LBL_KPENTITAORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_colore', $label_campo = 'Colore', $uitype = '1', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '99', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpEntitaOrganigrammi', $blocco = 'LBL_KPENTITAORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_chiuso', $label_campo = 'Chiuso', $uitype = '56', $columntype = 'varchar(3)', $typeofdata = 'C~O', $readonly = '99', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpEntitaOrganigrammi', $blocco = 'LBL_KPENTITAORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_verticale', $label_campo = 'Verticale', $uitype = '56', $columntype = 'varchar(3)', $typeofdata = 'C~O', $readonly = '99', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpEntitaOrganigrammi', $blocco = 'LBL_KPENTITAORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_id_creator', $label_campo = 'ID Automatico Creator', $uitype = '1', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '100', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpEntitaOrganigrammi', $blocco = 'LBL_KPENTITAORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_aggiornato', $label_campo = 'Aggiornato', $uitype = '56', $columntype = 'varchar(3)', $typeofdata = 'C~O', $readonly = '100', $helpinfo = '');


KpSDK::registraCampoRelazionato($nome_modulo = 'KpOrganigrammi', $blocco = 'LBL_KPORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_azienda', $label_campo = 'Azienda',  $typeofdata = 'I~M', $readonly = '1', $helpinfo = '', $relatedModules = array('Accounts'), $relatedModulesAction = array('Accounts'=>array('ADD','SELECT')));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpEntitaOrganigrammi', $blocco = 'LBL_KPENTITAORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_risorsa', $label_campo = 'Risorsa',  $typeofdata = 'I~O', $readonly = '99', $helpinfo = '', $relatedModules = array('Contacts'), $relatedModulesAction = array('Contacts'=>array('ADD','SELECT')));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpEntitaOrganigrammi', $blocco = 'LBL_KPENTITAORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_ruolo', $label_campo = 'Ruolo',  $typeofdata = 'I~O', $readonly = '99', $helpinfo = '', $relatedModules = array('KpRuoli'), $relatedModulesAction = array('KpRuoli'=>array('ADD','SELECT')));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpEntitaOrganigrammi', $blocco = 'LBL_KPENTITAORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_organigramma', $label_campo = 'Organigramma',  $typeofdata = 'I~M', $readonly = '99', $helpinfo = '', $relatedModules = array('KpOrganigrammi'), $relatedModulesAction = array('KpOrganigrammi'=>array('ADD','SELECT')));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpEntitaOrganigrammi', $blocco = 'LBL_KPENTITAORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_subordinato_a', $label_campo = 'Subordinato a',  $typeofdata = 'I~O', $readonly = '99', $helpinfo = '', $relatedModules = array('KpEntitaOrganigrammi'));


KpSDK::registraEstensioneClasse($nome_modulo = "KpOrganigrammi", $tipo_estenzione = "Standard", $percorso_core = "modules/SproCore/");

KpSDK::registraEstensioneClasse($nome_modulo = "KpEntitaOrganigrammi", $tipo_estenzione = "Standard", $percorso_core = "modules/SproCore/");

KpSDK::aggiornaFiltro($nome_modulo = "KpOrganigrammi", $nome_filtro = "All", $elenco_campi = array("kp_nome_organigramma", "kp_azienda", "kp_data_organigramma", "createdtime", "modifiedtime", "assigned_user_id") );

KpSDK::aggiornaFiltro($nome_modulo = "KpEntitaOrganigrammi", $nome_filtro = "All", $elenco_campi = array("kp_nome_entita", "kp_risorsa", "kp_ruolo", "kp_organigramma", "kp_subordinato_a", "kp_in_staff", "createdtime", "modifiedtime", "assigned_user_id") );

KpSDK::registraModuleHomeCustom($nome_modulo = "KpOrganigrammi", $nome_tab = "KP_LBL_ORGANIGRAMMI_VIEWER", $traduzione_nome_tab = "Organigrammi", $percorso_file = "Smarty/templates/SproCore/KpOrganigrammiHomeViewer.tpl");

KpSDK::registraPulsante($nome_modulo = "KpOrganigrammi", $nome_pulsante = "Disegna Organigramma", $tipo_pulsante = "Menu ALTRO", $funzione = 'javascript:kpOrganigrammaCreator(\'$RECORD$\');');

KpSDK::registraCampo($nome_modulo = 'Contacts', $blocco = 'LBL_CONTACT_INFORMATION', $nome_campo = 'kp_app_organigrammi', $label_campo = 'App. Organigrammi', $uitype = '56', $columntype = 'varchar(3)', $typeofdata = 'C~O', $readonly = '1', $helpinfo = '');
*/

//SDK::setPreSave('Contacts', 'modules/SproCore/Contacts/PresaveContactsKp.php');

//Moduli per Organigrammi end

/*
KpSDK::registraPulsante($nome_modulo = "KpProcedure", $nome_pulsante = "Crea Revisione", $tipo_pulsante = "Menu ALTRO", $funzione = 'javascript:kpCreaRevisioneProcedura(\'$RECORD$\');');

KpSDK::registraCampoRelazionato($nome_modulo = 'KpProcedure', $blocco = 'LBL_KPPROCEDURE_INFORMATION', $nome_campo = 'kp_revisione_di', $label_campo = 'Revisione Di',  $typeofdata = 'I~O', $readonly = '1', $helpinfo = '', $relatedModules = array('KpProcedure'));

KpSDK::registraCampo($nome_modulo = 'KpProcedure', $blocco = 'LBL_KPPROCEDURE_INFORMATION', $nome_campo = 'kp_numero_revisione', $label_campo = 'Numero Revisione', $uitype = '7', $columntype = 'decimal(10,0)', $typeofdata = 'N~O~10,0', $readonly = '1', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpProcedure', $blocco = 'LBL_KPPROCEDURE_INFORMATION', $nome_campo = 'kp_data_revisione', $label_campo = 'Data Revisione', $uitype = '5', $columntype = 'date', $typeofdata = 'D~O', $readonly = '1', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpProcedure', $blocco = 'LBL_KPPROCEDURE_INFORMATION', $nome_campo = 'kp_stato_procedura', $label_campo = 'Stato', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '1', $helpinfo = '', $picklist = array('In sviluppo','Da approvare','Attivo','Non attivo','Sospeso','Revisionato'));

KpSDK::registraCampo($nome_modulo = 'KpProcedure', $blocco = 'LBL_KPPROCEDURE_INFORMATION', $nome_campo = 'kp_rev_in_data', $label_campo = 'Revisionato in Data', $uitype = '5', $columntype = 'date', $typeofdata = 'D~O', $readonly = '1', $helpinfo = '');

KpSDK::aggiornaFiltro($nome_modulo = "KpProcedure", $nome_filtro = "All", $elenco_campi = array("kp_nome_procedura", "kp_tipo_procedura", "kp_data_procedura", "kp_primario", "kp_revisione_di", "kp_numero_revisione", "kp_stato_procedura", "createdtime", "assigned_user_id") );

KpSDK::registraCampo($nome_modulo = 'KpProcedure', $blocco = 'LBL_KPPROCEDURE_INFORMATION', $nome_campo = 'kp_log_revisione', $label_campo = 'Log Revisione', $uitype = '19', $columntype = 'longtext', $typeofdata = 'V~O', $readonly = '99', $helpinfo = '');
*/

//Traduzione modulo Rischi DVR in Pericoli DVR
/*SDK::clearSessionValues();
$nome_modulo = "KpRischiDVR";
$label_modulo_singolare = "Pericolo DVR";
$label_modulo = "Pericoli DVR";
SDK::setLanguageEntries($nome_modulo, "LBL_".strtoupper($nome_modulo)."_INFORMATION", array('it_it' => 'Informazione '.$label_modulo_singolare,'en_us' => 'Informazione '.$label_modulo_singolare));
SDK::setLanguageEntries($nome_modulo, "SINGLE_".$nome_modulo." Informazioni", array('it_it' => 'Informazione '.$label_modulo_singolare,'en_us' => 'Informazione '.$label_modulo_singolare));
SDK::setLanguageEntries($nome_modulo, "SINGLE_".$nome_modulo, array('it_it' => $label_modulo_singolare,'en_us'=> $label_modulo_singolare));
SDK::setLanguageEntries($nome_modulo, $nome_modulo, array('it_it' => $label_modulo,'en_us' => $label_modulo));
SDK::setLanguageEntries($nome_modulo, 'Nome Rischio', array('it_it' => 'Nome Pericolo' ,'en_us' => 'Nome Pericolo'));*/

//Crea modulo Tipologie Impianti
//KpSDK::registraModulo($nome_modulo = 'KpTipologieImpianti', $label_modulo = 'Tipologie Impianti', $label_modulo_singolare = 'Tipologia Impianto', $tipo_campo = 'Testo', $nome_campo = 'kp_nome_tipologia', $label_campo = 'Nome Tipologia', $privilegi = 'Private', $merge = true, $import_export = true, $messaggi = true, $documenti = true, $calendario = true, $processi = true, $homeview = true);
//KpSDK::registraEstensioneClasse($nome_modulo = "KpTipologieImpianti", $tipo_estenzione = "Standard", $percorso_core = "modules/SproCore/");

//Crea relazione tra Tipologie Impianti ed Impianti
//KpSDK::registraCampoRelazionato($nome_modulo = 'Impianti', $blocco = 'LBL_IMPIANTI_INFORMATION', $nome_campo = 'kp_tipologia_imp', $label_campo = 'Tipologia Impianto',  $typeofdata = 'I~O', $readonly = '1', $helpinfo = '', $relatedModules = array('KpTipologieImpianti'), $relatedModulesAction = array('KpTipologieImpianti'=>array('ADD','SELECT')));

//Crea relazione tra Tipologie Impianti e Rischi DVR
//KpSDK::registraRelated("KpTipologieImpianti", "KpRischiDVR");
//KpSDK::registraRelated("KpRischiDVR", "KpTipologieImpianti");

//Crea relazione tra Tipologie Impianti e Ruoli
//KpSDK::registraRelated("KpTipologieImpianti", "KpRuoli");
//KpSDK::registraRelated("KpRuoli", "KpTipologieImpianti");

//Crea modulo Sostanze Chimiche
//KpSDK::registraModulo($nome_modulo = 'KpSostanzeChimiche', $label_modulo = 'Sostanze Chimiche', $label_modulo_singolare = 'Sostanza Chimica', $tipo_campo = 'Testo', $nome_campo = 'kp_nome_sostanza', $label_campo = 'Nome Sostanza', $privilegi = 'Private', $merge = true, $import_export = true, $messaggi = true, $documenti = true, $calendario = true, $processi = true, $homeview = true);
//KpSDK::registraEstensioneClasse($nome_modulo = "KpSostanzeChimiche", $tipo_estenzione = "Standard", $percorso_core = "modules/SproCore/");

//Crea relazione tra Sostanze Chimiche e Rischi DVR
//KpSDK::registraRelated("KpSostanzeChimiche", "KpRischiDVR");
//KpSDK::registraRelated("KpRischiDVR", "KpSostanzeChimiche");

//Crea relazione tra Sostanze Chimiche e Ruoli
//KpSDK::registraRelated("KpSostanzeChimiche", "KpRuoli");
//KpSDK::registraRelated("KpRuoli", "KpSostanzeChimiche");

//Crea relazione tra Sostanze Chimiche e Tipologie Impianti
//KpSDK::registraRelated("KpSostanzeChimiche", "KpTipologieImpianti");
//KpSDK::registraRelated("KpTipologieImpianti", "KpSostanzeChimiche");

//KpSDK::registraModulo($nome_modulo = 'KpMaterialiUtilizzo', $label_modulo = 'Materiali di Utilizzo', $label_modulo_singolare = 'Materiale di Utilizzo', $tipo_campo = 'Testo', $nome_campo = 'kp_nome_materiale', $label_campo = 'Nome Materiale', $privilegi = 'Private', $merge = true, $import_export = true, $messaggi = true, $documenti = true, $calendario = true, $processi = true, $homeview = true);
//KpSDK::registraEstensioneClasse($nome_modulo = "KpMaterialiUtilizzo", $tipo_estenzione = "Standard", $percorso_core = "modules/SproCore/");

//Crea relazione tra Materiali di Utilizzo e Rischi DVR
//KpSDK::registraRelated("KpMaterialiUtilizzo", "KpRischiDVR");
//KpSDK::registraRelated("KpRischiDVR", "KpMaterialiUtilizzo");

//Crea relazione tra Materiali di Utilizzo e Ruoli
//KpSDK::registraRelated("KpMaterialiUtilizzo", "KpRuoli");
//KpSDK::registraRelated("KpRuoli", "KpMaterialiUtilizzo");

//Crea relazione tra Aree Stabilimento e Ruoli
//KpSDK::registraRelated("KpAreeStabilimento", "KpRuoli");
//KpSDK::registraRelated("KpRuoli", "KpAreeStabilimento");

//Crea relazione tra Aree Stabilimento e Rischi DVR
//KpSDK::registraRelated("KpAreeStabilimento", "KpRischiDVR");
//KpSDK::registraRelated("KpRischiDVR", "KpAreeStabilimento");

//Crea relazione tra Aree Stabilimento e Attività DVR
//KpSDK::registraRelated("KpAreeStabilimento", "KpAttivitaDVR");
//KpSDK::registraRelated("KpAttivitaDVR", "KpAreeStabilimento");

//Crea relazione tra Aree Stabilimento e Tipologie Impianti
//KpSDK::registraRelated("KpAreeStabilimento", "KpTipologieImpianti");
//KpSDK::registraRelated("KpTipologieImpianti", "KpAreeStabilimento");

//Crea relazione tra Aree Stabilimento e Sostanze Chimiche
//KpSDK::registraRelated("KpAreeStabilimento", "KpSostanzeChimiche");
//KpSDK::registraRelated("KpSostanzeChimiche", "KpAreeStabilimento");

//Crea relazione tra Aree Stabilimento e Materiali di Utilizzo
//KpSDK::registraRelated("KpAreeStabilimento", "KpMaterialiUtilizzo");
//KpSDK::registraRelated("KpMaterialiUtilizzo", "KpAreeStabilimento");

//Crea relazione tra Rilevazione Rischi e Aree Stabilimento
//KpSDK::registraCampoRelazionato($nome_modulo = 'KpRilevazioniRischi', $blocco = 'LBL_KPRILEVAZIONIRISCHI_INFORMATION', $nome_campo = 'kp_area_stab', $label_campo = 'Area Stabilimento',  $typeofdata = 'I~M', $readonly = '1', $helpinfo = '', $relatedModules = array('KpAreeStabilimento'), $relatedModulesAction = array('KpAreeStabilimento'=>array('ADD','SELECT')));

//KpSDK::registraCampo($nome_modulo = 'KpRischiDVR', $blocco = 'LBL_KPRISCHIDVR_INFORMATION', $nome_campo = 'kp_natura_pericolo', $label_campo = 'Natura', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~M', $readonly = '1', $helpinfo = '', $picklist = array('Meccanica','Elettrica','Termica','Chimica','Biologica','Illuminazione','Microclima','Ergonomia','Rumore','Vibrazione','Radiazioni','Organizzativa'));

//KpSDK::aggiornaFiltro($nome_modulo = "KpRischiDVR", $nome_filtro = "All", $elenco_campi = array("kp_nome_rischio", "kp_natura_pericolo", "createdtime", "modifiedtime", "description", "assigned_user_id") );

//KpSDK::aggiornaFiltro($nome_modulo = "KpRilevazioniRischi", $nome_filtro = "All", $elenco_campi = array("kp_nome_rilevazione", "kp_azienda", "kp_stabilimento", "kp_area_stab", "kp_data_rilevazione", "createdtime", "modifiedtime", "description", "assigned_user_id") );

//Modifico il modulo Rilevazione Rischi eliminando i campi che non servono più
/*$query = "DELETE FROM {$table_prefix}_field WHERE tabid = 124 AND tablename = 'vte_kprilevazrischirig' AND fieldid = 2023 AND fieldname = 'kp_attivita'";
$adb->query($query);

$query = "DELETE FROM {$table_prefix}_fieldmodulerel WHERE module = 'KpRilevazRischiRig' AND fieldid = 2023 AND relmodule = 'KpAttivitaDVR'";
$adb->query($query);

$query = "DELETE FROM {$table_prefix}_relatedlists WHERE label = 'KpRilevazRischiRig' AND related_tabid = 124 AND tabid = 121 AND relation_id = 808";
$adb->query($query);


$query = "DELETE FROM {$table_prefix}_field WHERE tabid = 124 AND tablename = 'vte_kprilevazrischirig' AND fieldid = 2021 AND fieldname = 'kp_mansione'";
$adb->query($query);

$query = "DELETE FROM {$table_prefix}_fieldmodulerel WHERE module = 'KpRilevazRischiRig' AND fieldid = 2021 AND relmodule = 'Mansioni'";
$adb->query($query);

$query = "DELETE FROM {$table_prefix}_relatedlists WHERE label = 'KpRilevazRischiRig' AND related_tabid = 124 AND tabid = 69 AND relation_id = 806";
$adb->query($query);


$query = "ALTER TABLE {$table_prefix}_kprilevazrischirig
            DROP COLUMN kp_attivita,
            DROP COLUMN kp_mansione";
$adb->query($query);*/

//KpSDK::registraCampoRelazionato($nome_modulo = 'KpRilevazRischiRig', $blocco = 'LBL_KPRILEVAZRISCHIRIG_INFORMATION', $nome_campo = 'kp_rilevazione', $label_campo = 'Rilevazione Rischi',  $typeofdata = 'I~M', $readonly = '1', $helpinfo = '', $relatedModules = array('KpRilevazioniRischi'), $relatedModulesAction = array('KpRilevazioniRischi'=>array('ADD','SELECT')));

//KpSDK::registraCampoRelazionato($nome_modulo = 'KpRilevazRischiRig', $blocco = 'LBL_KPRILEVAZRISCHIRIG_INFORMATION', $nome_campo = 'kp_related_to', $label_campo = 'Collegato a',  $typeofdata = 'I~O', $readonly = '1', $helpinfo = '', $relatedModules = array('KpAttivitaDVR', 'KpTipologieImpianti', 'KpSostanzeChimiche', 'KpMaterialiUtilizzo'));

//KpSDK::registraCampoRelazionato($nome_modulo = 'KpRuoli', $blocco = 'LBL_KPRUOLI_INFORMATION', $nome_campo = 'kp_mansione', $label_campo = 'Mansione',  $typeofdata = 'I~O', $readonly = '1', $helpinfo = '', $relatedModules = array('Mansioni'), $relatedModulesAction = array('Mansioni'=>array('ADD','SELECT')));
/*
SDK::setLanguageEntries('KpRilevazRischiRig', 'Rischio', array('it_it' => 'Pericolo' ,'en_us' => 'Pericolo'));
SDK::setLanguageEntries('KpRilevazRischiRig', 'Gravita', array('it_it' => 'Magnitudo' ,'en_us' => 'Magnitudo'));

KpSDK::registraCampo($nome_modulo = 'KpRilevazRischiRig', $blocco = 'LBL_KPRILEVAZRISCHIRIG_INFORMATION', $nome_campo = 'kp_attivo', $label_campo = 'Attivo', $uitype = '56', $columntype = 'varchar(3)', $typeofdata = 'C~O', $readonly = '1', $helpinfo = '');
KpSDK::registraCampo($nome_modulo = 'KpRilevazRischiRig', $blocco = 'LBL_KPRILEVAZRISCHIRIG_INFORMATION', $nome_campo = 'kp_valutazione_risc', $label_campo = 'Valutazione Rischio', $uitype = '7', $columntype = 'decimal(10,0)', $typeofdata = 'N~O~10,0', $readonly = '99', $helpinfo = '');
KpSDK::registraCampo($nome_modulo = 'KpRilevazRischiRig', $blocco = 'LBL_KPRILEVAZRISCHIRIG_INFORMATION', $nome_campo = 'kp_frase_risc_dvr', $label_campo = 'Frase di Rischio', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '99', $helpinfo = '', $picklist = array('Irrilevante','Minore','Moderato','Significativo','Estremo'));
*/

//KpSDK::registraRelated("KpRilevazRischiRig", "KpRuoli");
//KpSDK::registraRelated("KpRuoli", "KpRilevazRischiRig");

//KpSDK::registraModulo($nome_modulo = 'KpTipiMisureRiduttive', $label_modulo = 'Tipi Misure Riduttive', $label_modulo_singolare = 'Tipo Misura Riduttiva', $tipo_campo = 'Testo', $nome_campo = 'kp_nome_misura', $label_campo = 'Nome Misura', $privilegi = 'Private', $merge = true, $import_export = true, $messaggi = true, $documenti = true, $calendario = true, $processi = true, $homeview = true);

//KpSDK::registraModulo($nome_modulo = 'KpMisureRiduttive', $label_modulo = 'Misure Riduttive', $label_modulo_singolare = 'Misura Riduttiva', $tipo_campo = 'Testo', $nome_campo = 'kp_nome_misura', $label_campo = 'Nome Misura', $privilegi = 'Private', $merge = true, $import_export = true, $messaggi = true, $documenti = true, $calendario = true, $processi = true, $homeview = true);
/*
KpSDK::registraCampoRelazionato($nome_modulo = 'KpMisureRiduttive', $blocco = 'LBL_KPMISURERIDUTTIVE_INFORMATION', $nome_campo = 'kp_tipo_misura', $label_campo = 'Tipo Misura',  $typeofdata = 'I~M', $readonly = '1', $helpinfo = '', $relatedModules = array('KpTipiMisureRiduttive'), $relatedModulesAction = array('KpTipiMisureRiduttive'=>array('ADD','SELECT')));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpMisureRiduttive', $blocco = 'LBL_KPMISURERIDUTTIVE_INFORMATION', $nome_campo = 'kp_area_stab', $label_campo = 'Area Stabilimento',  $typeofdata = 'I~M', $readonly = '1', $helpinfo = '', $relatedModules = array('KpAreeStabilimento'), $relatedModulesAction = array('KpAreeStabilimento'=>array('ADD','SELECT')));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpMisureRiduttive', $blocco = 'LBL_KPMISURERIDUTTIVE_INFORMATION', $nome_campo = 'kp_azienda', $label_campo = 'Azienda',  $typeofdata = 'I~M', $readonly = '1', $helpinfo = '', $relatedModules = array('Accounts'), $relatedModulesAction = array('Accounts'=>array('ADD','SELECT')));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpMisureRiduttive', $blocco = 'LBL_KPMISURERIDUTTIVE_INFORMATION', $nome_campo = 'kp_stabilimento', $label_campo = 'Stabilimento',  $typeofdata = 'I~M', $readonly = '1', $helpinfo = '', $relatedModules = array('Stabilimenti'), $relatedModulesAction = array('Stabilimenti'=>array('ADD','SELECT')));

KpSDK::registraCampo($nome_modulo = 'KpMisureRiduttive', $blocco = 'LBL_KPMISURERIDUTTIVE_INFORMATION', $nome_campo = 'kp_eseguire_entro', $label_campo = 'Da Eseguire Entro Il', $uitype = '5', $columntype = 'date', $typeofdata = 'D~O', $readonly = '1', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpMisureRiduttive', $blocco = 'LBL_KPMISURERIDUTTIVE_INFORMATION', $nome_campo = 'kp_stato_misura_rid', $label_campo = 'Stato Misura', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '1', $helpinfo = '', $picklist = array('Da adottare','Adottata parzialmente','Adottata'));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpMisureRiduttive', $blocco = 'LBL_KPMISURERIDUTTIVE_INFORMATION', $nome_campo = 'kp_related_to', $label_campo = 'Collegato a',  $typeofdata = 'I~O', $readonly = '1', $helpinfo = '', $relatedModules = array('KpAttivitaDVR', 'KpTipologieImpianti', 'KpSostanzeChimiche', 'KpMaterialiUtilizzo'));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpMisureRiduttive', $blocco = 'LBL_KPMISURERIDUTTIVE_INFORMATION', $nome_campo = 'kp_pericolo', $label_campo = 'Pericolo',  $typeofdata = 'I~M', $readonly = '1', $helpinfo = '', $relatedModules = array('KpRischiDVR'), $relatedModulesAction = array('KpRischiDVR'=>array('ADD','SELECT')));

KpSDK::aggiornaFiltro($nome_modulo = "KpMisureRiduttive", $nome_filtro = "All", $elenco_campi = array("kp_nome_misura", "kp_tipo_misura", "kp_azienda", "kp_stabilimento", "kp_area_stab", "kp_pericolo", "kp_eseguire_entro", "kp_stato_misura_rid", "assigned_user_id") );

KpSDK::registraCampo($nome_modulo = 'KpTipiMisureRiduttive', $blocco = 'LBL_KPTIPIMISURERIDUTTIVE_INFORMATION', $nome_campo = 'kp_categoria_misura', $label_campo = 'Categoria Misura', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '1', $helpinfo = '', $picklist = array('Organizzativa','Formativa','DPI','Strutturale','Verifica e manutenzione'));

KpSDK::aggiornaFiltro($nome_modulo = "KpTipiMisureRiduttive", $nome_filtro = "All", $elenco_campi = array("kp_nome_misura", "kp_categoria_misura", "createdtime", "modifiedtime", "assigned_user_id") );

KpSDK::registraCampo($nome_modulo = 'KpMisureRiduttive', $blocco = 'LBL_KPMISURERIDUTTIVE_INFORMATION', $nome_campo = 'kp_riduzione_prob', $label_campo = 'Riduce Probabilita Di', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '1', $helpinfo = '', $picklist = array('','1','2','3','4','5'));

KpSDK::registraCampo($nome_modulo = 'KpMisureRiduttive', $blocco = 'LBL_KPMISURERIDUTTIVE_INFORMATION', $nome_campo = 'kp_riduzione_magn', $label_campo = 'Riduce Magnitudo Di', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '1', $helpinfo = '', $picklist = array('','1','2','3','4','5'));

KpSDK::registraCampo($nome_modulo = 'KpRischiDVR', $blocco = 'LBL_KPRISCHIDVR_INFORMATION', $nome_campo = 'kp_soggeto_a_misura', $label_campo = 'Pericolo Soggetto a Misurazione', $uitype = '56', $columntype = 'varchar(3)', $typeofdata = 'C~O', $readonly = '1', $helpinfo = 'Tale flag identifica le tipologie di pericoli per i queli è richiesta una misurazione (ad esempio: rumore in dB)');

KpSDK::registraCampo($nome_modulo = 'KpRischiDVR', $blocco = 'LBL_KPRISCHIDVR_INFORMATION', $nome_campo = 'kp_nome_misurazione', $label_campo = 'Nome Da Associare Alla Misurazione', $uitype = '1', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '1', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpRischiDVR', $blocco = 'LBL_KPRISCHIDVR_INFORMATION', $nome_campo = 'kp_help_probabilita', $label_campo = 'Help Probabilita', $uitype = '19', $columntype = 'longtext', $typeofdata = 'V~O', $readonly = '1', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpRischiDVR', $blocco = 'LBL_KPRISCHIDVR_INFORMATION', $nome_campo = 'kp_help_magnitudo', $label_campo = 'Help Magnitudo', $uitype = '19', $columntype = 'longtext', $typeofdata = 'V~O', $readonly = '1', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpRilevazRischiRig', $blocco = 'LBL_KPRILEVAZRISCHIRIG_INFORMATION', $nome_campo = 'kp_misurazione', $label_campo = 'Misurazione', $uitype = '7', $columntype = 'decimal(15,4)', $typeofdata = 'NN~O~15,4', $readonly = '99', $helpinfo = '');
*/

//Modifiche per qualita

//Crea relazione tra Categorie Attivita e Ruoli
//KpSDK::registraRelated("KpAttivitaDVR", "KpRuoli");
//KpSDK::registraRelated("KpRuoli", "KpAttivitaDVR");

//KpSDK::registraCampoRelazionato($nome_modulo = 'KpEntitaProcedure', $blocco = 'LBL_KPENTITAPROCEDURE_INFORMATION', $nome_campo = 'kp_attivita_dvr', $label_campo = 'Tipo Attivita',  $typeofdata = 'I~O', $readonly = '99', $helpinfo = '', $relatedModules = array('KpAttivitaDVR'), $relatedModulesAction = array('KpAttivitaDVR'=>array('ADD','SELECT')));

//KpSDK::registraRelated("KpAttivitaDVR", "KpMinaccePrivacy");
//KpSDK::registraRelated("KpMinaccePrivacy", "KpAttivitaDVR");

//KpSDK::registraRelated("KpAttivitaDVR", "KpRischiQualita");
//KpSDK::registraRelated("KpRischiQualita", "KpAttivitaDVR");

//KpSDK::registraModulo($nome_modulo = 'KpRuoliAttivita', $label_modulo = 'Ruoli-Attivita', $label_modulo_singolare = 'Ruolo-Attivita', $tipo_campo = 'Testo', $nome_campo = 'kp_soggetto', $label_campo = 'Soggetto', $privilegi = 'Private', $merge = true, $import_export = true, $messaggi = true, $documenti = true, $calendario = true, $processi = true, $homeview = true);

/*
KpSDK::registraCampoRelazionato($nome_modulo = 'KpRuoliAttivita', $blocco = 'LBL_KPRUOLIATTIVITA_INFORMATION', $nome_campo = 'kp_attivita', $label_campo = 'Attivita',  $typeofdata = 'I~M', $readonly = '99', $helpinfo = '', $relatedModules = array('KpEntitaProcedure'), $relatedModulesAction = array('KpEntitaProcedure'=>array()));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpRuoliAttivita', $blocco = 'LBL_KPRUOLIATTIVITA_INFORMATION', $nome_campo = 'kp_ruolo', $label_campo = 'Ruolo',  $typeofdata = 'I~M', $readonly = '99', $helpinfo = '', $relatedModules = array('KpRuoli'), $relatedModulesAction = array('KpRuoli'=>array()));

KpSDK::registraCampo($nome_modulo = 'KpRuoliAttivita', $blocco = 'LBL_KPRUOLIATTIVITA_INFORMATION', $nome_campo = 'kp_resp_ruolo', $label_campo = 'Responsabilita', $uitype = '33', $columntype = 'varchar(255)', $typeofdata = 'V~M', $readonly = '99', $helpinfo = '', $picklist = array('Esecutore','Responsabile','Informato'));

KpSDK::aggiornaFiltro($nome_modulo = "KpRuoliAttivita", $nome_filtro = "All", $elenco_campi = array("kp_soggetto", "kp_attivita", "kp_ruolo", "kp_resp_ruolo", "createdtime", "modifiedtime", "assigned_user_id") );

KpSDK::registraEstensioneClasse($nome_modulo = "KpRuoliAttivita", $tipo_estenzione = "Standard", $percorso_core = "modules/SproCore/");
*/

/*$query = "DELETE FROM {$table_prefix}_relatedlists WHERE label = 'KpRuoli' AND related_tabid = 137 AND tabid = 136";
$adb->query($query);

$query = "DELETE FROM {$table_prefix}_relatedlists WHERE label = 'KpEntitaProcedure' AND related_tabid = 136 AND tabid = 137";
$adb->query($query);*/

/*$query = "DELETE FROM {$table_prefix}_relatedlists WHERE label = 'Contacts' AND related_tabid = 4 AND tabid = 137";
$adb->query($query);

$query = "DELETE FROM {$table_prefix}_relatedlists WHERE label = 'KpRuoli' AND related_tabid = 137 AND tabid = 4";
$adb->query($query);*/

//KpSDK::registraRelated("KpEntitaProcedure", "KpAreeStabilimento");
//KpSDK::registraRelated("KpAreeStabilimento", "KpEntitaProcedure");

//KpSDK::registraRelated("KpProcedure", "KpAreeStabilimento");
//KpSDK::registraRelated("KpAreeStabilimento", "KpProcedure");

/*$query = "ALTER TABLE kp_settings_procedure CHANGE COLUMN revisione_processi richiedi_approvazione VARCHAR(1)";
$adb->query($query);*/

//KpSDK::registraCampo($nome_modulo = 'KpProcedure', $blocco = 'LBL_KPPROCEDURE_INFORMATION', $nome_campo = 'kp_disegnato_da', $label_campo = 'Disegnato da', $uitype = '1', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '99', $helpinfo = '');

//KpSDK::registraCampo($nome_modulo = 'KpProcedure', $blocco = 'LBL_KPPROCEDURE_INFORMATION', $nome_campo = 'kp_approvato_da', $label_campo = 'Approvato da', $uitype = '1', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '99', $helpinfo = '');

//KpSDK::registraCampo($nome_modulo = 'KpProcedure', $blocco = 'LBL_KPPROCEDURE_INFORMATION', $nome_campo = 'kp_approvato', $label_campo = 'Processo Approvato', $uitype = '56', $columntype = 'varchar(3)', $typeofdata = 'C~O', $readonly = '99', $helpinfo = '');

//KpSDK::registraCampo($nome_modulo = 'KpProcedure', $blocco = 'LBL_KPPROCEDURE_INFORMATION', $nome_campo = 'kp_data_approvazion', $label_campo = 'Data Approvazione', $uitype = '5', $columntype = 'date', $typeofdata = 'D~O', $readonly = '99', $helpinfo = '');

/*
KpSDK::registraCampo($nome_modulo = 'KpOrganigrammi', $blocco = 'LBL_KPORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_disegnato_da', $label_campo = 'Disegnato da', $uitype = '1', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '99', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpOrganigrammi', $blocco = 'LBL_KPORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_approvato_da', $label_campo = 'Approvato da', $uitype = '1', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '99', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpOrganigrammi', $blocco = 'LBL_KPORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_approvato', $label_campo = 'Organigramma Approvato', $uitype = '56', $columntype = 'varchar(3)', $typeofdata = 'C~O', $readonly = '99', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpOrganigrammi', $blocco = 'LBL_KPORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_data_approvazion', $label_campo = 'Data Approvazione', $uitype = '5', $columntype = 'date', $typeofdata = 'D~O', $readonly = '99', $helpinfo = '');


KpSDK::registraPulsante($nome_modulo = "KpOrganigrammi", $nome_pulsante = "Crea Revisione", $tipo_pulsante = "Menu ALTRO", $funzione = 'javascript:kpCreaRevisioneOrganigramma(\'$RECORD$\');');

KpSDK::registraCampoRelazionato($nome_modulo = 'KpOrganigrammi', $blocco = 'LBL_KPORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_revisione_di', $label_campo = 'Revisione Di',  $typeofdata = 'I~O', $readonly = '1', $helpinfo = '', $relatedModules = array('KpOrganigrammi'));

KpSDK::registraCampo($nome_modulo = 'KpOrganigrammi', $blocco = 'LBL_KPORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_numero_revisione', $label_campo = 'Numero Revisione', $uitype = '7', $columntype = 'decimal(10,0)', $typeofdata = 'N~O~10,0', $readonly = '1', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpOrganigrammi', $blocco = 'LBL_KPORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_data_revisione', $label_campo = 'Data Revisione', $uitype = '5', $columntype = 'date', $typeofdata = 'D~O', $readonly = '1', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpOrganigrammi', $blocco = 'LBL_KPORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_stato_organigramma', $label_campo = 'Stato', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '1', $helpinfo = '', $picklist = array('In sviluppo','Da approvare','Attivo','Non attivo','Sospeso','Revisionato'));

KpSDK::registraCampo($nome_modulo = 'KpOrganigrammi', $blocco = 'LBL_KPORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_rev_in_data', $label_campo = 'Revisionato in Data', $uitype = '5', $columntype = 'date', $typeofdata = 'D~O', $readonly = '1', $helpinfo = '');

KpSDK::aggiornaFiltro($nome_modulo = "KpOrganigrammi", $nome_filtro = "All", $elenco_campi = array("kp_nome_organigramma", "kp_azienda", "kp_data_organigramma", "kp_revisione_di", "kp_numero_revisione", "kp_stato_organigramma", "createdtime", "assigned_user_id") );

KpSDK::registraCampo($nome_modulo = 'KpOrganigrammi', $blocco = 'LBL_KPORGANIGRAMMI_INFORMATION', $nome_campo = 'kp_log_revisione', $label_campo = 'Log Revisione', $uitype = '19', $columntype = 'longtext', $typeofdata = 'V~O', $readonly = '99', $helpinfo = '');
*/


//Modifiche per qualita end

/*SDK::clearSessionValues();
$nome_modulo = "Processes";
$label_modulo_singolare = "Workflow";
$label_modulo = "Workflow";
SDK::setLanguageEntries($nome_modulo, "LBL_".strtoupper($nome_modulo)."_INFORMATION", array('it_it' => 'Informazione '.$label_modulo_singolare,'en_us' => 'Informazione '.$label_modulo_singolare));
SDK::setLanguageEntries($nome_modulo, "SINGLE_".$nome_modulo." Informazioni", array('it_it' => 'Informazione '.$label_modulo_singolare,'en_us' => 'Informazione '.$label_modulo_singolare));
SDK::setLanguageEntries($nome_modulo, "SINGLE_".$nome_modulo, array('it_it' => $label_modulo_singolare,'en_us'=> $label_modulo_singolare));
SDK::setLanguageEntries($nome_modulo, $nome_modulo, array('it_it' => $label_modulo,'en_us' => $label_modulo));
SDK::setLanguageEntries($nome_modulo, 'Process Name', array('it_it' => 'Nome Attivita' ,'en_us' => 'Nome Attivita'));

$nome_modulo = "KpProcedure";
$label_modulo_singolare = "Processo";
$label_modulo = "Processi";
SDK::setLanguageEntries($nome_modulo, "LBL_".strtoupper($nome_modulo)."_INFORMATION", array('it_it' => 'Informazione '.$label_modulo_singolare,'en_us' => 'Informazione '.$label_modulo_singolare));
SDK::setLanguageEntries($nome_modulo, "SINGLE_".$nome_modulo." Informazioni", array('it_it' => 'Informazione '.$label_modulo_singolare,'en_us' => 'Informazione '.$label_modulo_singolare));
SDK::setLanguageEntries($nome_modulo, "SINGLE_".$nome_modulo, array('it_it' => $label_modulo_singolare,'en_us'=> $label_modulo_singolare));
SDK::setLanguageEntries($nome_modulo, $nome_modulo, array('it_it' => $label_modulo,'en_us' => $label_modulo));
SDK::setLanguageEntries($nome_modulo, 'Nome Procedura', array('it_it' => 'Nome Processo' ,'en_us' => 'Nome Processo'));
SDK::setLanguageEntries($nome_modulo, 'Numero Procedura', array('it_it' => 'Numero Processo' ,'en_us' => 'Numero Processo'));
SDK::setLanguageEntries($nome_modulo, 'Tipo Procedura', array('it_it' => 'Tipo Processo' ,'en_us' => 'Tipo Processo'));
SDK::setLanguageEntries($nome_modulo, 'Data Procedura', array('it_it' => 'Data Processo' ,'en_us' => 'Data Processo'));
SDK::setLanguageEntries($nome_modulo, 'Scadenza Procedura', array('it_it' => 'Scadenza Processo' ,'en_us' => 'Scadenza Processo'));
SDK::setLanguageEntries($nome_modulo, 'Procedura Primaria', array('it_it' => 'Processo Primario' ,'en_us' => 'Processo Primario'));
SDK::setLanguageEntries($nome_modulo, 'KP_LBL_PROCESSI_BPMN_VIEWER', array('it_it' => 'Processi (BPMN)' ,'en_us' => 'Processi (BPMN)'));
*/
/*
SDK::clearSessionValues();

$nome_modulo = "KpEntitaProcedure";
$label_modulo_singolare = "Entita Processo";
$label_modulo = "Entita Processi";
SDK::setLanguageEntries($nome_modulo, "LBL_".strtoupper($nome_modulo)."_INFORMATION", array('it_it' => 'Informazione '.$label_modulo_singolare,'en_us' => 'Informazione '.$label_modulo_singolare));
SDK::setLanguageEntries($nome_modulo, "SINGLE_".$nome_modulo." Informazioni", array('it_it' => 'Informazione '.$label_modulo_singolare,'en_us' => 'Informazione '.$label_modulo_singolare));
SDK::setLanguageEntries($nome_modulo, "SINGLE_".$nome_modulo, array('it_it' => $label_modulo_singolare,'en_us'=> $label_modulo_singolare));
SDK::setLanguageEntries($nome_modulo, $nome_modulo, array('it_it' => $label_modulo,'en_us' => $label_modulo));
SDK::setLanguageEntries($nome_modulo, 'Procedura', array('it_it' => 'Processo' ,'en_us' => 'Processo'));

$nome_modulo = "KpRevisioniProcedure";
$label_modulo_singolare = "Resoconto Revisione Processo";
$label_modulo = "Resoconti Revisioni Processi";
SDK::setLanguageEntries($nome_modulo, "LBL_".strtoupper($nome_modulo)."_INFORMATION", array('it_it' => 'Informazione '.$label_modulo_singolare,'en_us' => 'Informazione '.$label_modulo_singolare));
SDK::setLanguageEntries($nome_modulo, "SINGLE_".$nome_modulo." Informazioni", array('it_it' => 'Informazione '.$label_modulo_singolare,'en_us' => 'Informazione '.$label_modulo_singolare));
SDK::setLanguageEntries($nome_modulo, "SINGLE_".$nome_modulo, array('it_it' => $label_modulo_singolare,'en_us'=> $label_modulo_singolare));
SDK::setLanguageEntries($nome_modulo, $nome_modulo, array('it_it' => $label_modulo,'en_us' => $label_modulo));
SDK::setLanguageEntries($nome_modulo, 'Procedura', array('it_it' => 'Processo' ,'en_us' => 'Processo'));

$nome_modulo = "KpNotificheRevProc";
$label_modulo_singolare = "Notifica Revisione Processo";
$label_modulo = "Notifiche Revisioni Processi";
SDK::setLanguageEntries($nome_modulo, "LBL_".strtoupper($nome_modulo)."_INFORMATION", array('it_it' => 'Informazione '.$label_modulo_singolare,'en_us' => 'Informazione '.$label_modulo_singolare));
SDK::setLanguageEntries($nome_modulo, "SINGLE_".$nome_modulo." Informazioni", array('it_it' => 'Informazione '.$label_modulo_singolare,'en_us' => 'Informazione '.$label_modulo_singolare));
SDK::setLanguageEntries($nome_modulo, "SINGLE_".$nome_modulo, array('it_it' => $label_modulo_singolare,'en_us'=> $label_modulo_singolare));
SDK::setLanguageEntries($nome_modulo, $nome_modulo, array('it_it' => $label_modulo,'en_us' => $label_modulo));
SDK::setLanguageEntries($nome_modulo, 'Revisione Procedura', array('it_it' => 'Revisione Processo' ,'en_us' => 'Revisione Processo'));
SDK::setLanguageEntries($nome_modulo, 'Procedura', array('it_it' => 'Processo' ,'en_us' => 'Processo'));

$nome_modulo = "Contacts";
SDK::setLanguageEntries($nome_modulo, 'App. Procedure', array('it_it' => 'App. Processi' ,'en_us' => 'App. Processi'));
*/

//KpSDK::registraModulo($nome_modulo = 'KpResRevisioniOrg', $label_modulo = 'Resoconti Revisioni Organigrammi', $label_modulo_singolare = 'Resoconto Revisione Organigramma', $tipo_campo = 'Testo', $nome_campo = 'kp_nome_revisione', $label_campo = 'Nome Revisione', $privilegi = 'Private', $merge = true, $import_export = true, $messaggi = true, $documenti = true, $calendario = true, $processi = true, $homeview = true);
/*
KpSDK::registraCampo($nome_modulo = 'KpResRevisioniOrg', $blocco = 'LBL_KPRESREVISIONIORG_INFORMATION', $nome_campo = 'kp_numero_revisione', $label_campo = 'Numero Revisione', $uitype = '7', $columntype = 'decimal(10,0)', $typeofdata = 'N~O~10,0', $readonly = '99', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpResRevisioniOrg', $blocco = 'LBL_KPRESREVISIONIORG_INFORMATION', $nome_campo = 'kp_data_revisione', $label_campo = 'Data Revisione', $uitype = '5', $columntype = 'date', $typeofdata = 'D~O', $readonly = '99', $helpinfo = '');

KpSDK::registraCampoRelazionato($nome_modulo = 'KpResRevisioniOrg', $blocco = 'LBL_KPRESREVISIONIORG_INFORMATION', $nome_campo = 'kp_organigramma', $label_campo = 'Organigramma',  $typeofdata = 'I~O', $readonly = '99', $helpinfo = '', $relatedModules = array('KpOrganigrammi'), $relatedModulesAction = array('KpOrganigrammi'=>array('ADD','SELECT')));

KpSDK::aggiornaFiltro($nome_modulo = "KpResRevisioniOrg", $nome_filtro = "All", $elenco_campi = array("kp_nome_revisione", "kp_numero_revisione", "kp_data_revisione", "kp_organigramma", "createdtime", "modifiedtime", "assigned_user_id") );

KpSDK::registraEstensioneClasse($nome_modulo = "KpResRevisioniOrg", $tipo_estenzione = "Standard", $percorso_core = "modules/SproCore/");
*/
//KpSDK::registraModulo($nome_modulo = 'KpNotificheRevOrg', $label_modulo = 'Notifiche Revisioni Organigrammi', $label_modulo_singolare = 'Notifica Revisione Organigramma', $tipo_campo = 'Testo', $nome_campo = 'kp_soggetto', $label_campo = 'Soggetto', $privilegi = 'Private', $merge = true, $import_export = true, $messaggi = true, $documenti = true, $calendario = true, $processi = true, $homeview = true);
/*
KpSDK::registraCampoRelazionato($nome_modulo = 'KpNotificheRevOrg', $blocco = 'LBL_KPNOTIFICHEREVORG_INFORMATION', $nome_campo = 'kp_rev_organigramma', $label_campo = 'Revisione Organigramma',  $typeofdata = 'I~O', $readonly = '99', $helpinfo = '', $relatedModules = array('KpResRevisioniOrg'), $relatedModulesAction = array('KpResRevisioniOrg'=>array('ADD','SELECT')));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpNotificheRevOrg', $blocco = 'LBL_KPNOTIFICHEREVORG_INFORMATION', $nome_campo = 'kp_organigramma', $label_campo = 'Organigramma',  $typeofdata = 'I~O', $readonly = '99', $helpinfo = '', $relatedModules = array('KpOrganigrammi'));

KpSDK::registraCampo($nome_modulo = 'KpNotificheRevOrg', $blocco = 'LBL_KPNOTIFICHEREVORG_INFORMATION', $nome_campo = 'kp_data_notifica', $label_campo = 'Data Notifica', $uitype = '5', $columntype = 'date', $typeofdata = 'D~M', $readonly = '99', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpNotificheRevOrg', $blocco = 'LBL_KPNOTIFICHEREVORG_INFORMATION', $nome_campo = 'kp_data_visione', $label_campo = 'Data Conferma Visione', $uitype = '5', $columntype = 'date', $typeofdata = 'D~O', $readonly = '99', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpNotificheRevOrg', $blocco = 'LBL_KPNOTIFICHEREVORG_INFORMATION', $nome_campo = 'kp_stato_notifica_r', $label_campo = 'Stato Notifica', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '99', $helpinfo = '');

KpSDK::registraCampoRelazionato($nome_modulo = 'KpNotificheRevOrg', $blocco = 'LBL_KPNOTIFICHEREVORG_INFORMATION', $nome_campo = 'kp_risorsa', $label_campo = 'Risorsa',  $typeofdata = 'I~O', $readonly = '99', $helpinfo = '', $relatedModules = array('Contacts'));

KpSDK::aggiornaFiltro($nome_modulo = "KpNotificheRevOrg", $nome_filtro = "All", $elenco_campi = array("kp_soggetto", "kp_rev_organigramma", "kp_organigramma", "kp_risorsa", "kp_data_notifica", "kp_stato_notifica_r", "kp_data_visione", "assigned_user_id") );

KpSDK::registraEstensioneClasse($nome_modulo = "KpNotificheRevOrg", $tipo_estenzione = "Standard", $percorso_core = "modules/SproCore/");
*/
//KpSDK::registraModuleHomeCustom($nome_modulo = "KpNotificheRevOrg", $nome_tab = "KP_LBL_NOTIFICHE_REV_ORG_VIEWER", $traduzione_nome_tab = "Notifiche (Organigrammi)", $percorso_file = "Smarty/templates/SproCore/KpNotificheRevOrgHomeViewer.tpl");

//KpSDK::registraCampo($nome_modulo = 'KpPartecipFormaz', $blocco = 'LBL_CUSTOM_INFORMATION', $nome_campo = 'kp_chiusura_scagl', $label_campo = 'Chiusura Scaglione', $uitype = '56', $columntype = 'varchar(3)', $typeofdata = 'C~O', $readonly = '99', $helpinfo = 'Questo campo indica se tale partecipazione (qualora facente parte di una formazione scaglionata) va a completamento delle ore previste dal tipo corso');

//KpSDK::registraCampo($nome_modulo = 'SituazFormaz', $blocco = 'LBL_CUSTOM_INFORMATION', $nome_campo = 'kp_ore_prossimo_rin', $label_campo = 'Ore Da Eff. Per Prossimo Rinnovo', $uitype = '7', $columntype = 'decimal(15,2)', $typeofdata = 'N~O~15,2', $readonly = '99', $helpinfo = '');

//KpSDK::registraCampo($nome_modulo = 'KpPartecipFormaz', $blocco = 'LBL_CUSTOM_INFORMATION', $nome_campo = 'kp_formazione_scagl', $label_campo = 'Formazione Scaglionata', $uitype = '56', $columntype = 'varchar(3)', $typeofdata = 'C~O', $readonly = '99', $helpinfo = '');

//SDK::addView('KpPartecipFormaz', 'modules/SproCore/KpPartecipFormaz/KpViewKpPartecipFormaz.php', 'constrain', 'continue');

//13/06/2018

//KpSDK::registraModulo($nome_modulo = 'KpRilRischiQualita', $label_modulo = 'Rilevazioni Rischi Qualita', $label_modulo_singolare = 'Rilevazione Rischio Qualita', $tipo_campo = 'Testo', $nome_campo = 'kp_soggetto', $label_campo = 'Soggetto', $privilegi = 'Private', $merge = true, $import_export = true, $messaggi = true, $documenti = true, $calendario = true, $processi = true, $homeview = true);
/*
KpSDK::registraCampoRelazionato($nome_modulo = 'KpRilRischiQualita', $blocco = 'LBL_KPRILRISCHIQUALITA_INFORMATION', $nome_campo = 'kp_azienda', $label_campo = 'Azienda',  $typeofdata = 'I~M', $readonly = '1', $helpinfo = '', $relatedModules = array('Accounts'));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpRilRischiQualita', $blocco = 'LBL_KPRILRISCHIQUALITA_INFORMATION', $nome_campo = 'kp_stabilimento', $label_campo = 'Stabilimento',  $typeofdata = 'I~M', $readonly = '1', $helpinfo = '', $relatedModules = array('Stabilimenti'));

KpSDK::registraCampo($nome_modulo = 'KpRilRischiQualita', $blocco = 'LBL_KPRILRISCHIQUALITA_INFORMATION', $nome_campo = 'kp_data_rilevazione', $label_campo = 'Data Rilevazione', $uitype = '5', $columntype = 'date', $typeofdata = 'D~M', $readonly = '1', $helpinfo = '');
*/
//KpSDK::aggiornaFiltro($nome_modulo = "KpRilRischiQualita", $nome_filtro = "All", $elenco_campi = array("kp_soggetto", "kp_azienda", "kp_stabilimento", "kp_data_rilevazione", "createdtime", "modifiedtime", "assigned_user_id") );

//KpSDK::registraModulo($nome_modulo = 'KpRigheRilRischiQual', $label_modulo = 'Righe Ril. Rischi Qualita', $label_modulo_singolare = 'Riga Ril. Rischi Qualita', $tipo_campo = 'Testo', $nome_campo = 'kp_soggetto', $label_campo = 'Soggetto', $privilegi = 'Private', $merge = true, $import_export = true, $messaggi = true, $documenti = true, $calendario = true, $processi = true, $homeview = true);
/*
KpSDK::registraCampoRelazionato($nome_modulo = 'KpRigheRilRischiQual', $blocco = 'LBL_KPRIGHERILRISCHIQUAL_INFORMATION', $nome_campo = 'kp_rilevazione', $label_campo = 'Rilevazione Rischio',  $typeofdata = 'I~M', $readonly = '99', $helpinfo = '', $relatedModules = array('KpRilRischiQualita'), $relatedModulesAction = array('KpRilRischiQualita'=>array('ADD','SELECT')));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpRigheRilRischiQual', $blocco = 'LBL_KPRIGHERILRISCHIQUAL_INFORMATION', $nome_campo = 'kp_rischio', $label_campo = 'Rischio',  $typeofdata = 'I~M', $readonly = '99', $helpinfo = '', $relatedModules = array('KpRischiQualita'));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpRigheRilRischiQual', $blocco = 'LBL_KPRIGHERILRISCHIQUAL_INFORMATION', $nome_campo = 'kp_processo', $label_campo = 'Processo',  $typeofdata = 'I~O', $readonly = '99', $helpinfo = '', $relatedModules = array('KpProcedure'));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpRigheRilRischiQual', $blocco = 'LBL_KPRIGHERILRISCHIQUAL_INFORMATION', $nome_campo = 'kp_attivita', $label_campo = 'Attivita',  $typeofdata = 'I~O', $readonly = '99', $helpinfo = '', $relatedModules = array('KpEntitaProcedure'));

KpSDK::registraCampo($nome_modulo = 'KpRigheRilRischiQual', $blocco = 'LBL_KPRIGHERILRISCHIQUAL_INFORMATION', $nome_campo = 'kp_gravita_risc_q', $label_campo = 'Magnitudo', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '99', $helpinfo = '', $picklist = array('1 - Trascurabile','2 - Contenuto','3 - Significativo','4 - Rilevante','5 - Catastrofico'));

KpSDK::registraCampo($nome_modulo = 'KpRigheRilRischiQual', $blocco = 'LBL_KPRIGHERILRISCHIQUAL_INFORMATION', $nome_campo = 'kp_prob_risc_q', $label_campo = 'Probabilita', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '99', $helpinfo = '', $picklist = array('1 - Improbabile','2 - Raro','3 - Possibile','4 - Probabile','5 - Molto Probabile'));

KpSDK::registraCampo($nome_modulo = 'KpRigheRilRischiQual', $blocco = 'LBL_KPRIGHERILRISCHIQUAL_INFORMATION', $nome_campo = 'kp_attivo', $label_campo = 'Attivo', $uitype = '56', $columntype = 'varchar(3)', $typeofdata = 'C~O', $readonly = '99', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpRigheRilRischiQual', $blocco = 'LBL_KPRIGHERILRISCHIQUAL_INFORMATION', $nome_campo = 'kp_valutazione_risc', $label_campo = 'Valutazione Rischio', $uitype = '7', $columntype = 'decimal(10,0)', $typeofdata = 'N~O~10,0', $readonly = '99', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpRigheRilRischiQual', $blocco = 'LBL_KPRIGHERILRISCHIQUAL_INFORMATION', $nome_campo = 'kp_frase_risc_qual', $label_campo = 'Frase di Rischio', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '99', $helpinfo = '', $picklist = array('Irrilevante','Minore','Moderato','Significativo','Estremo'));
*/
//KpSDK::aggiornaFiltro($nome_modulo = "KpRigheRilRischiQual", $nome_filtro = "All", $elenco_campi = array("kp_soggetto", "kp_rilevazione", "kp_processo", "kp_attivita", "kp_rischio", "kp_attivo", "kp_frase_risc_qual", "assigned_user_id") );

//KpSDK::registraModulo($nome_modulo = 'KpMisureMigliorative', $label_modulo = 'Misure Migliorative', $label_modulo_singolare = 'Misura Migliorativa', $tipo_campo = 'Testo', $nome_campo = 'kp_nome_misura', $label_campo = 'Nome Misura', $privilegi = 'Private', $merge = true, $import_export = true, $messaggi = true, $documenti = true, $calendario = true, $processi = true, $homeview = true);
/*
KpSDK::registraCampoRelazionato($nome_modulo = 'KpMisureMigliorative', $blocco = 'LBL_KPMISUREMIGLIORATIVE_INFORMATION', $nome_campo = 'kp_azienda', $label_campo = 'Azienda',  $typeofdata = 'I~M', $readonly = '99', $helpinfo = '', $relatedModules = array('Accounts'));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpMisureMigliorative', $blocco = 'LBL_KPMISUREMIGLIORATIVE_INFORMATION', $nome_campo = 'kp_stabilimento', $label_campo = 'Stabilimento',  $typeofdata = 'I~M', $readonly = '99', $helpinfo = '', $relatedModules = array('Stabilimenti'));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpMisureMigliorative', $blocco = 'LBL_KPMISUREMIGLIORATIVE_INFORMATION', $nome_campo = 'kp_rischio', $label_campo = 'Rischio',  $typeofdata = 'I~M', $readonly = '99', $helpinfo = '', $relatedModules = array('KpRischiQualita'));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpMisureMigliorative', $blocco = 'LBL_KPMISUREMIGLIORATIVE_INFORMATION', $nome_campo = 'kp_processo', $label_campo = 'Processo',  $typeofdata = 'I~O', $readonly = '99', $helpinfo = '', $relatedModules = array('KpProcedure'));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpMisureMigliorative', $blocco = 'LBL_KPMISUREMIGLIORATIVE_INFORMATION', $nome_campo = 'kp_attivita', $label_campo = 'Attivita',  $typeofdata = 'I~O', $readonly = '99', $helpinfo = '', $relatedModules = array('KpEntitaProcedure'));

KpSDK::registraCampo($nome_modulo = 'KpMisureMigliorative', $blocco = 'LBL_KPMISUREMIGLIORATIVE_INFORMATION', $nome_campo = 'kp_eseguire_entro', $label_campo = 'Da Eseguire Entro Il', $uitype = '5', $columntype = 'date', $typeofdata = 'D~O', $readonly = '1', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpMisureMigliorative', $blocco = 'LBL_KPMISUREMIGLIORATIVE_INFORMATION', $nome_campo = 'kp_stato_misura_mig', $label_campo = 'Stato Misura', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '1', $helpinfo = '', $picklist = array('Da adottare','Adottata parzialmente','Adottata'));

KpSDK::registraCampo($nome_modulo = 'KpMisureMigliorative', $blocco = 'LBL_KPMISUREMIGLIORATIVE_INFORMATION', $nome_campo = 'kp_data_adozione', $label_campo = 'Data Adozione', $uitype = '5', $columntype = 'date', $typeofdata = 'D~O', $readonly = '1', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpMisureMigliorative', $blocco = 'LBL_KPMISUREMIGLIORATIVE_INFORMATION', $nome_campo = 'kp_rid_prob_qual', $label_campo = 'Riduce Probabilita Di', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '1', $helpinfo = '', $picklist = array('','1','2','3','4','5'));

KpSDK::registraCampo($nome_modulo = 'KpMisureMigliorative', $blocco = 'LBL_KPMISUREMIGLIORATIVE_INFORMATION', $nome_campo = 'kp_rid_magn_qual', $label_campo = 'Riduce Magnitudo Di', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '1', $helpinfo = '', $picklist = array('','1','2','3','4','5'));
*/

//KpSDK::registraEstensioneClasse($nome_modulo = "KpRilRischiQualita", $tipo_estenzione = "Standard", $percorso_core = "modules/SproCore/");

//KpSDK::registraCampo($nome_modulo = 'KpMisureMigliorative', $blocco = 'LBL_KPMISUREMIGLIORATIVE_INFORMATION', $nome_campo = 'kp_tipo_misura_migl', $label_campo = 'Tipo Misura', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '1', $helpinfo = '', $picklist = array('','Formazione','Verifica','Personalizzazione software','Modifica processo','Altro'));

//KpSDK::aggiornaFiltro($nome_modulo = "KpMisureMigliorative", $nome_filtro = "All", $elenco_campi = array("kp_nome_misura", "kp_azienda", "kp_rischio", "kp_processo", "kp_attivita", "kp_tipo_misura_migl", "kp_eseguire_entro", "kp_stato_misura_mig", "assigned_user_id") );

//KpSDK::registraModulo($nome_modulo = 'KpSitRischiDVR', $label_modulo = 'Situazione Rischi', $label_modulo_singolare = 'Situazione Rischio', $tipo_campo = 'Testo', $nome_campo = 'kp_soggetto', $label_campo = 'Soggetto', $privilegi = 'Private', $merge = true, $import_export = true, $messaggi = false, $documenti = false, $calendario = false, $processi = false, $homeview = true);
/*
KpSDK::registraCampoRelazionato($nome_modulo = 'KpSitRischiDVR', $blocco = 'LBL_KPSITRISCHIDVR_INFORMATION', $nome_campo = 'kp_azienda', $label_campo = 'Azienda',  $typeofdata = 'I~O', $readonly = '99', $helpinfo = '', $relatedModules = array('Accounts'), $relatedModulesAction = array('Accounts'=>array()));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpSitRischiDVR', $blocco = 'LBL_KPSITRISCHIDVR_INFORMATION', $nome_campo = 'kp_stabilimento', $label_campo = 'Stabilimento',  $typeofdata = 'I~O', $readonly = '99', $helpinfo = '', $relatedModules = array('Stabilimenti'), $relatedModulesAction = array('Stabilimenti'=>array()));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpSitRischiDVR', $blocco = 'LBL_KPSITRISCHIDVR_INFORMATION', $nome_campo = 'kp_area_stab', $label_campo = 'Area Stabilimento',  $typeofdata = 'I~M', $readonly = '99', $helpinfo = '', $relatedModules = array('KpAreeStabilimento'), $relatedModulesAction = array('KpAreeStabilimento'=>array()));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpSitRischiDVR', $blocco = 'LBL_KPSITRISCHIDVR_INFORMATION', $nome_campo = 'kp_rilevazione_risc', $label_campo = 'Ultima Rilevazione Rischi',  $typeofdata = 'I~O', $readonly = '99', $helpinfo = '', $relatedModules = array('KpRilevazioniRischi'), $relatedModulesAction = array('KpRilevazioniRischi'=>array()));

KpSDK::registraCampo($nome_modulo = 'KpSitRischiDVR', $blocco = 'LBL_KPSITRISCHIDVR_INFORMATION', $nome_campo = 'kp_data_rilevazione', $label_campo = 'Data Ultima Rilevazione Rischi', $uitype = '5', $columntype = 'date', $typeofdata = 'D~O', $readonly = '99', $helpinfo = '');

KpSDK::registraCampoRelazionato($nome_modulo = 'KpSitRischiDVR', $blocco = 'LBL_KPSITRISCHIDVR_INFORMATION', $nome_campo = 'kp_related_to', $label_campo = 'Collegato a',  $typeofdata = 'I~O', $readonly = '99', $helpinfo = '', $relatedModules = array('KpAttivitaDVR', 'KpTipologieImpianti', 'KpSostanzeChimiche', 'KpMaterialiUtilizzo'));

KpSDK::registraCampoRelazionato($nome_modulo = 'KpSitRischiDVR', $blocco = 'LBL_KPSITRISCHIDVR_INFORMATION', $nome_campo = 'kp_rischio', $label_campo = 'Pericolo',  $typeofdata = 'I~M', $readonly = '99', $helpinfo = '', $relatedModules = array('KpRischiDVR'), $relatedModulesAction = array('KpRischiDVR'=>array()));

KpSDK::registraCampo($nome_modulo = 'KpSitRischiDVR', $blocco = 'LBL_KPSITRISCHIDVR_INFORMATION', $nome_campo = 'kp_gravita_rischio', $label_campo = 'Gravita', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '99', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpSitRischiDVR', $blocco = 'LBL_KPSITRISCHIDVR_INFORMATION', $nome_campo = 'kp_probabilita_risc', $label_campo = 'Probabilita', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '99', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpSitRischiDVR', $blocco = 'LBL_KPSITRISCHIDVR_INFORMATION', $nome_campo = 'kp_misurazione', $label_campo = 'Misurazione', $uitype = '7', $columntype = 'decimal(15,4)', $typeofdata = 'NN~O~15,4', $readonly = '99', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpSitRischiDVR', $blocco = 'LBL_KPSITRISCHIDVR_INFORMATION', $nome_campo = 'kp_valutazione_risc', $label_campo = 'Valutazione Rischio', $uitype = '7', $columntype = 'decimal(10,0)', $typeofdata = 'N~O~10,0', $readonly = '99', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpSitRischiDVR', $blocco = 'LBL_KPSITRISCHIDVR_INFORMATION', $nome_campo = 'kp_frase_risc_dvr', $label_campo = 'Frase di Rischio', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '99', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpSitRischiDVR', $blocco = 'LBL_KPSITRISCHIDVR_INFORMATION', $nome_campo = 'kp_aggiornato', $label_campo = 'Aggiornato', $uitype = '56', $columntype = 'varchar(3)', $typeofdata = 'C~O', $readonly = '100', $helpinfo = '');

KpSDK::registraRelated("KpSitRischiDVR", "KpRuoli");
KpSDK::registraRelated("KpRuoli", "KpSitRischiDVR");

KpSDK::registraRelated("KpSitRischiDVR", "KpRilevazRischiRig");
KpSDK::registraRelated("KpRilevazRischiRig", "KpSitRischiDVR");

KpSDK::registraRelated("KpSitRischiDVR", "KpMisureRiduttive");
KpSDK::registraRelated("KpMisureRiduttive", "KpSitRischiDVR");
*/
//KpSDK::aggiornaFiltro($nome_modulo = "KpSitRischiDVR", $nome_filtro = "All", $elenco_campi = array("kp_soggetto", "kp_azienda", "kp_stabilimento", "kp_area_stab", "kp_related_to", "kp_rischio", "kp_valutazione_risc", "kp_frase_risc_dvr", "assigned_user_id") );

//KpSDK::registraEstensioneClasse($nome_modulo = "KpSitRischiDVR", $tipo_estenzione = "Standard", $percorso_core = "modules/SproCore/");

//KpSDK::registraPulsante($nome_modulo = "KpSitRischiDVR", $nome_pulsante = "Aggiorna Sit. Rischi", $tipo_pulsante = "index", $funzione = 'kpAggiornaSitRischiDVR();', $icona = "aggiorna.png");

//KpSDK::registraPulsante($nome_modulo = "KpSitRischiDVR", $nome_pulsante = "Aggiorna Sit. Rischi", $tipo_pulsante = "ListView", $funzione = 'kpAggiornaSitRischiDVR();', $icona = "aggiorna.png");

//KpSDK::registraCampo($nome_modulo = 'KpMisureRiduttive', $blocco = 'LBL_KPMISURERIDUTTIVE_INFORMATION', $nome_campo = 'kp_data_adozione', $label_campo = 'Data Adozione', $uitype = '5', $columntype = 'date', $typeofdata = 'D~O', $readonly = '1', $helpinfo = '');

//KpSDK::registraEstensioneClasse($nome_modulo = "KpMisureRiduttive", $tipo_estenzione = "Standard", $percorso_core = "modules/SproCore/");

//KpSDK::registraModuleHomeCustom($nome_modulo = "KpSitRischiDVR", $nome_tab = "KP_LBL_SIT_RISCHI_DVR", $traduzione_nome_tab = "Sit. Rischi", $percorso_file = "Smarty/templates/SproCore/KpSitRischiDVR.tpl");

//KpSDK::registraCampo($nome_modulo = 'KpRilevazioniRischi', $blocco = 'LBL_KPRILEVAZIONIRISCHI_INFORMATION', $nome_campo = 'kp_rip_dati_situaiz', $label_campo = 'Riporta Situazione Attuale', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '1', $helpinfo = 'Se impostao a Si in fase di creazione la rilevazione verrà automaticamente proposta con i dati della sittuazione attuale (se presente)', $picklist = array('Si','No'));

//KpSDK::aggiornaFiltro($nome_modulo = "KpRilevazRischiRig", $nome_filtro = "All", $elenco_campi = array("kp_nome_riga", "kp_area_stab", "kp_related_to", "kp_rischio", "kp_rilevazione", "kp_gravita_rischio", "kp_probabilita_risc", "kp_valutazione_risc", "kp_frase_risc_dvr") );
/*
KpSDK::registraCampo($nome_modulo = 'KpRischiDVR', $blocco = 'LBL_CUSTOM_INFORMATION', $nome_campo = 'kp_id_sis_origine', $label_campo = 'Id Sistema Origine (per Migrazione)', $uitype = '1', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '100', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpTipiMisureRiduttive', $blocco = 'LBL_CUSTOM_INFORMATION', $nome_campo = 'kp_id_sis_origine', $label_campo = 'Id Sistema Origine (per Migrazione)', $uitype = '1', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '100', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpAttivitaDVR', $blocco = 'LBL_CUSTOM_INFORMATION', $nome_campo = 'kp_id_sis_origine', $label_campo = 'Id Sistema Origine (per Migrazione)', $uitype = '1', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '100', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpTipologieImpianti', $blocco = 'LBL_CUSTOM_INFORMATION', $nome_campo = 'kp_id_sis_origine', $label_campo = 'Id Sistema Origine (per Migrazione)', $uitype = '1', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '100', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpSostanzeChimiche', $blocco = 'LBL_CUSTOM_INFORMATION', $nome_campo = 'kp_id_sis_origine', $label_campo = 'Id Sistema Origine (per Migrazione)', $uitype = '1', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '100', $helpinfo = '');

KpSDK::registraCampo($nome_modulo = 'KpMaterialiUtilizzo', $blocco = 'LBL_CUSTOM_INFORMATION', $nome_campo = 'kp_id_sis_origine', $label_campo = 'Id Sistema Origine (per Migrazione)', $uitype = '1', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '100', $helpinfo = '');
*/
/*
$query = "UPDATE vte_kp_gravita_rischio SET kp_gravita_rischio = '1 - Lieve', presence = 0 WHERE kp_gravita_rischio = '1 - Trascurabile'";
KpSDK::eseguiQuerySDK($query);

$query = "UPDATE vte_kp_gravita_rischio SET kp_gravita_rischio = '2 - Medio', presence = 0 WHERE kp_gravita_rischio = '2 - Contenuto'";
KpSDK::eseguiQuerySDK($query);

$query = "UPDATE vte_kp_gravita_rischio SET kp_gravita_rischio = '3 - Alto', presence = 0 WHERE kp_gravita_rischio = '3 - Significativo'";
KpSDK::eseguiQuerySDK($query);

$query = "UPDATE vte_kp_gravita_rischio SET kp_gravita_rischio = '4 - Molto alto', presence = 0 WHERE kp_gravita_rischio = '4 - Rilevante'";
KpSDK::eseguiQuerySDK($query);

$query = "UPDATE vte_kp_gravita_rischio SET presence = 1 WHERE kp_gravita_rischio = '5 - Catastrofico'";
KpSDK::eseguiQuerySDK($query);


$query = "UPDATE vte_kp_probabilita_risc SET kp_probabilita_risc = '1 - Improbabile', presence = 0 WHERE kp_probabilita_risc = '1 - Improbabile'";
KpSDK::eseguiQuerySDK($query);

$query = "UPDATE vte_kp_probabilita_risc SET kp_probabilita_risc = '2 - Poco Probabile', presence = 0 WHERE kp_probabilita_risc = '2 - Raro'";
KpSDK::eseguiQuerySDK($query);

$query = "UPDATE vte_kp_probabilita_risc SET kp_probabilita_risc = '3 - Probabile', presence = 0 WHERE kp_probabilita_risc = '3 - Possibile'";
KpSDK::eseguiQuerySDK($query);

$query = "UPDATE vte_kp_probabilita_risc SET kp_probabilita_risc = '4 - Altamente Probabile', presence = 0 WHERE kp_probabilita_risc = '4 - Probabile'";
KpSDK::eseguiQuerySDK($query);

$query = "UPDATE vte_kp_probabilita_risc SET presence = 1 WHERE kp_probabilita_risc = '5 - Molto Probabile'";
KpSDK::eseguiQuerySDK($query);


$query = "UPDATE vte_kp_frase_risc_dvr SET kp_frase_risc_dvr = 'Minimo', presence = 0 WHERE kp_frase_risc_dvr = 'Irrilevante'";
KpSDK::eseguiQuerySDK($query);

$query = "UPDATE vte_kp_frase_risc_dvr SET kp_frase_risc_dvr = 'Modesto', presence = 0 WHERE kp_frase_risc_dvr = 'Minore'";
KpSDK::eseguiQuerySDK($query);

$query = "UPDATE vte_kp_frase_risc_dvr SET kp_frase_risc_dvr = 'Rilevante', presence = 0 WHERE kp_frase_risc_dvr = 'Moderato'";
KpSDK::eseguiQuerySDK($query);

$query = "UPDATE vte_kp_frase_risc_dvr SET kp_frase_risc_dvr = 'Grave', presence = 0 WHERE kp_frase_risc_dvr = 'Significativo'";
KpSDK::eseguiQuerySDK($query);

$query = "UPDATE vte_kp_frase_risc_dvr SET kp_frase_risc_dvr = 'Molto grave', presence = 0 WHERE kp_frase_risc_dvr = 'Estremo'";
KpSDK::eseguiQuerySDK($query);
*/
?>