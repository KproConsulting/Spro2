<?php

/* kpro@tom09062017 */

/**
 * @author Tomiello Marco
 * @copyright (c) 2017, Kpro Consulting Srl
 */
die;
include_once('../../../config.inc.php'); 
chdir($root_directory); 
require_once('include/utils/utils.php'); 
include_once('vtlib/Vtiger/Module.php'); 
require_once('modules/SproCore/SDK/KpSDK.php'); 
$Vtiger_Utils_Log = true; 
global $adb, $table_prefix;
session_start(); 

//Esempio creazione moduli: 
//KpSDK::registraModulo("Nome Modulo", "File vtlib", "File SDK");
//File vtlib: deve contenere solo il nome del file con la relativa estenzione (esempio: vtlib_test.php) e deve essere messo nel percorso plugins/script/; inoltre tale file deve iniziare con require('config.inc.php');
//File SDK: deve contenere solo il nome del file con la relativa estenzione (esempio: sdk_test.php) e deve essere messo nel percorso modules/SDK/src/; inoltre tale file deve iniziare con require('config.inc.php');
KpSDK::registraModuloDaFile("Nome Modulo", "vtlib_test.php", "sdk_test.php");

//Esempio creazione moduli: 
//Diversamente dalla precedente questa funzione crea un modulo senza bisogno del file vtlib ed sdk
KpSDK::registraModulo($nome_modulo = "KpNuovoModuloTest", $label_modulo = "Nuovi Moduli Test", $label_modulo_singolare = "Nuovo Modulo Test", $tipo_campo = "Testo", $nome_campo = "kp_campo_ident", $label_campo = "Campo Identicativo", $privilegi = "Private", $merge = true, $import_export = true, $messaggi = true, $documenti = true, $calendario = true, $processi = true, $homeview = true);

//Esempio creazione campi: 
//KpSDK::registraCampo($nome_modulo = "Nome Modulo", $blocco = "Nome Blocco", $nome_campo = "Nome Campo", $label_campo = "Label Campo", $uitype = "UI Type", $columntype = "Column Type", $typeofdata = "Type of Data", $readonly = "Readonly", $helpinfo = "Help Info", $picklist = "Valori PickingList", $generatedtype = 1);
KpSDK::registraCampo($nome_modulo = 'Accounts', $blocco = 'LBL_ACCOUNT_INFORMATION', $nome_campo = 'kp_test_campo', $label_campo = 'Test campo', $uitype = '1', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '1', $helpinfo = '', '', $generatedtype = 1);
KpSDK::registraCampo($nome_modulo = 'Accounts', $blocco = 'LBL_ACCOUNT_INFORMATION', $nome_campo = 'kp_test_campo', $label_campo = 'Test campo', $uitype = '15', $columntype = 'varchar(255)', $typeofdata = 'V~O', $readonly = '1', $helpinfo = '', $picklist = array('a','b','c','d'), $generatedtype = 1);

//Esempio creazione campi relazionati: 
//KpSDK::registraCampoRelazionato($nome_modulo = "Nome Modulo", $blocco = "Nome Blocco", $nome_campo = "Nome Campo", $label_campo = "Label Campo", $typeofdata = "Type of Data", $readonly = "Readonly", $helpinfo = "Help Info", $relatedModules = "Modulo Relazionato", $relatedModulesAction = "Azione Modulo Relazionato");
KpSDK::registraCampoRelazionato($nome_modulo = 'Accounts', $blocco = 'LBL_ACCOUNT_INFORMATION', $nome_campo = 'kp_test_campo', $label_campo = 'Test campo', $typeofdata = 'I~O', $readonly = '1', $helpinfo = '', $relatedModules = array('KpIndiciPostazioni'), $relatedModulesAction = array('KpIndiciPostazioni'=>array('ADD','SELECT')) );

//Esempio creazione related: 
//KpSDK::registraRelated("Nome modulo 1", "Nome modulo 2", "Nome related", "Azioni", "Tipo Related");
//Nome modulo 1: rappresenta il nome del modulo in cui va create la related
//Nome modulo 2: rappresenta il nome del modulo con cui va instaurata la related
//Nome related: rappresenta il nome da dare alla related (tipicamente coincide con 'Nome modulo 2'); se impostato a vuoto verrà posto automaticamente pari a "Nome modulo 2" 
//Azioni: rappresenta le azioni che può eseguire la related: array("ADD", "SELECT") o array("SELECT"); se impostato a vuoto verrà posto automaticamente pari a array("ADD", "SELECT")
//Tipo Related: rappresenta la tipologia di related; se impostato a vuoto verrà posto automaticamente pari a "get_related_list"
KpSDK::registraRelated("SituazFormaz", "KpPartecipFormaz", "KpPartecipFormaz", array("ADD", "SELECT"), "get_related_list");
KpSDK::registraRelated("SituazFormaz", "KpPartecipFormaz");

//Esempio creazione/ ricreazione filtro:
//KpSDK::creaFiltro($nome_modulo = "Nome Modulo", $nome_filtro = "Nome filtro", $elenco_campi = "Elenco campi");
//Nome filtro: se lasciato vuoto di default il programma creerà il filtro All
//Elenco campi: se lasciato vuoto di default il programma inserirà i campi principali del modulo
KpSDK::creaFiltro($nome_modulo = "KpNuovoModuloTest", $nome_filtro = "All", $elenco_campi = array("kp_nuovo_campo", "description", "assigned_user_id") );
KpSDK::creaFiltro($nome_modulo = "KpNuovoModuloTest");

//Esempio aggiunta campi a filtro:
//KpSDK::aggiungiAlFiltro($nome_modulo = "Nome Modulo", $nome_filtro = "Nome filtro", $elenco_campi = "Elenco campi");
//Nome filtro: se lasciato vuoto di default il programma modificherà il filtro All
KpSDK::aggiungiAlFiltro($nome_modulo = "KpNuovoModuloTest", $nome_filtro = "All", $elenco_campi = array("createdtime", "modifiedtime") );

//Esempio aggiorna il filtro:
//KpSDK::aggiornaFiltro($nome_modulo = "Nome Modulo", $nome_filtro = "Nome filtro", $elenco_campi = "Elenco campi");
//Nome filtro: se lasciato vuoto di default il programma modificherà il filtro All
KpSDK::aggiornaFiltro($nome_modulo = "KpNuovoModuloTest", $nome_filtro = "All", $elenco_campi = array("kp_nuovo_campo", "description", "assigned_user_id") );

//Esempio creazione pulsante:
//KpSDK::registraPulsante($nome_modulo = "Nome modulo", $nome_pulsante = "Nome Pulsante", $tipo_pulsante = "Tipo pulsante", $funzione = 'Funzione', $icona = "Icona");
//Tipo pulsante: può assumere i seguenti valori: index, ListView, DetailView, Menu ALTRO
//Funzione: indicare la funzione da eseguire; se nella funzione vi è ad esempio a variabile $RECORD$ sarà necessario usare l'apice anziché il doppio apice, ovvero 'javascript:nome_funzione(\'$RECORD$\');' anziche "javascript:nome_funzione(\'$RECORD$\');"
KpSDK::registraPulsante($nome_modulo = "KpNuovoModuloTest", $nome_pulsante = "Nome Pulsante", $tipo_pulsante = "DetailView", $funzione = 'gantt_pianificazioni();', $icona = "gantt.png");
KpSDK::registraPulsante($nome_modulo = "KpNuovoModuloTest", $nome_pulsante = "Nome Pulsante", $tipo_pulsante = "Menu ALTRO", $funzione = 'javascript:kpSchedulazioneOrdineDiVendita(\'$RECORD$\');');

//Esempio registrazione file:
//KpSDK::registraFile("Percorso file");
KpSDK::registraFile("modules/SproCore/KpGestioneSchedulazioneOrdiniDiVendita.js");

//Esempio crea estensione di classe:
//KpSDK::registraEstensioneClasse($nome_modulo = "Nome Modulo", $tipo_estenzione = "Tipo estensione", $percorso_core = "Percorso core");
//Tipo estensione: può assumere i seguenti valori: Standard, Custom; se Standard crea (qualora già non esista) il file di estensione nel
//percorso in cui si è indicato il "core"; se Custom estende l'estensione Standard (se non presente la crea) e crea (qualora già non esista) 
//il file di estensione nel percorso modules/SDK/src/
KpSDK::registraEstensioneClasse($nome_modulo = "KpNuovoModuloTest", $tipo_estenzione = "Standard", $percorso_core = "modules/SproCore/");
KpSDK::registraEstensioneClasse($nome_modulo = "KpNuovoModuloTest", $tipo_estenzione = "Custom", $percorso_core = "modules/SproCore/");

//Esempio crea tab custom in list view (per tutti gli utenti):
//KpSDK::registraModuleHomeCustom($nome_modulo = "Nome Modulo", $nome_tab = "Label Tab", $traduzione_nome_tab = "Traduzione Label Tab", $percorso_file = "Percorso file");
//La funzione crea in automatico i record del tab standard per tutti gli utenti (vte_modulehome) e successivamente il record del tab custom (kp_modulehome).
//Non si possono creare 2 tab custom con la stessa label per lo stesso modulo
//Non si possono creare tab custom che hanno come label il nome di un tab standard già creato nello stesso modulo (anche da un solo utente)
KpSDK::registraModuleHomeCustom($nome_modulo = "KpNuovoModuloTest", $nome_tab = "KP_LBL_NUOVA_TAB_CUSTOM", $traduzione_nome_tab = "Nuova Tab Custom", $percorso_file = "modules/SproCore/SDK/Esempi/KpTabCustom1.tpl");

//Esempio registrazione storico stati in un campo a scelta:
//KpSDK::registraStoricoStati($nome_modulo = "Nome Modulo", $nome_campo_stato = "Nome campo della gestione stati", $nome_campo_storico = "Nome campo in cui scrivere lo storico")
//Nome campo stato: deve essere il fieldname (vte_field). 
//Nome campo storico: deve essere il columnname (vte_field) e deve avere uitype 19 o 21 o 210.
//Il nome campo stato e il nome campo storico devono essere nello stesso modulo.
//E' possibile registrare un solo storico stati per modulo.
KpSDK::registraStoricoStati($nome_modulo = "KpNuovoModuloTest", $nome_campo_stato = "kp_nuovo_campo_stato", $nome_campo_storico = "kp_nuovo_campo_storico");

//Esempio aggiunta campi a una picking-list:
//KpSDK::aggiungiAPickingList($nome_campo = "Nome Campo", $array_valori = array(elenco valori) );
KpSDK::aggiungiAPickingList($nome_campo = "nome_campo", $array_valori = array('1', '2', '3') );

//Esempio sostituzione file php standard:
//KpSDK::sostituisciFileStandard($nome_modulo = "Nome Modulo", $nome_file_standard = "Nome file standard presente nella cartella del modulo");
//Nome file standard: deve essere il nome del file senza estensione e senza percorso
//Questa funzione sostituisce il file "Nome file standard" con un file con lo stesso nome ma con "Kp" alla fine (es. EditView -> EditViewKp)
//Se il file custom non è già presente nella cartella del modulo, il programma lo crea in automatico duplicando il file standard di partenza
KpSDK::sostituisciFileStandard($nome_modulo = "KpNuovoModuloTest", $nome_file_standard = "EditView");

//Esempio creazione area custom nelle impostazioni:
//KpSDK::aggiungiAreaImpostazioni($blocco = 6, $nome = "Area Test", $icona = "module_maker.png", $nome_file = "KpAreaTest");
//blocco: ID del blocco in cui creare la nuova area (6 è l'ID del blocco Impostazioni Spro)
//nome: Nome dell'area
//icona: nome o percorso del file immagine che farà da icona alla nuova area
//nome_file: nome del file PHP (senza estensione) della nuova area che andrà posizionato in modules/Settings/
//Questa funzione crea il record nella tabella vte_settings_field per la nuova area (il blocco deve essere già presente)
KpSDK::aggiungiAreaImpostazioni($blocco = 6, $nome = "Area Test", $icona = "module_maker.png", $nome_file = "KpAreaTest");

//Esempio aggiunta campi a una picking-list multilinguaggio:
//KpSDK::aggiungiAPickingListMultilinguaggio($nome_campo = "Nome Campo", $codice = "22", $valore = "IVA 22%");
KpSDK::aggiungiAPickingListMultilinguaggio($nome_campo = "Nome Campo", $codice = "22", $valore = "IVA 22%");

//Esempio aggiunta relazione ad un modulo in un campo relazionato esistente
//KpSDK::registraCampoRelazionatoMultiplo($nome_modulo = 'Nome Modulo', $nome_modulo_relazionato = 'Nome Modulo Relazionato', $nome_campo = 'Nome Campo');
//Questa funzione aggiunge la relazione ad un modulo in un campo relazionato esistente
//N.B. Questa funzione NON crea la related nel modulo relazionato
KpSDK::registraCampoRelazionatoMultiplo($nome_modulo = 'Nome Modulo', $nome_modulo_relazionato = 'Nome Modulo Relazionato', $nome_campo = 'Nome Campo');

//Esempio esecuzione query
//KpSDK::eseguiQuerySDK($query_da_eseguire = 'query');
//Questa funzione esegue una query registrandone l'esecuzione nell'apposita tabella 'kp_query_sdk'; se una query è già stata eseguita in precedenza non verrà ulteriormente eseguita.
KpSDK::eseguiQuerySDK($query_da_eseguire = 'query');


?>