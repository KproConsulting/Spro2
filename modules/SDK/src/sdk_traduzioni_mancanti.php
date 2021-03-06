<?php
include_once('../../../config.inc.php');
chdir($root_directory);
require_once('include/utils/utils.php');
include_once('vtlib/Vtiger/Module.php');
require_once('modules/SproCore/SDK/KpSDK.php');
$Vtiger_Utils_Log = true;
global $adb, $table_prefix;
session_start();

/*
SDK::setLanguageEntries('ALERT_ARR', 'DELETE', array('it_it' => 'Sicuro di voler cancellare il selezionato ' ,'en_us' => 'Are you sure you want to delete the selected '));
SDK::setLanguageEntries('ALERT_ARR', 'RECORDS', array('it_it' => ' record?' ,'en_us' => ' records?'));
SDK::setLanguageEntries('ALERT_ARR', 'SELECT', array('it_it' => 'Prego selezionare almeno un\'entità' ,'en_us' => 'Please select at least one entity'));
SDK::setLanguageEntries('ALERT_ARR', 'DELETE_RECORD', array('it_it' => 'Sicuro di voler cancellare il record selezionato?' ,'en_us' => 'Are you sure you want to delete the selected record?'));
SDK::setLanguageEntries('ALERT_ARR', 'DELETE_RECORDS', array('it_it' => 'Sicuro di voler cancellare i %s record selezionati?' ,'en_us' => 'Are you sure you want to delete the %s selected records?'));
SDK::setLanguageEntries('ALERT_ARR', 'DELETE_ACCOUNT', array('it_it' => 'Cancellando questa azienda verranno cancellate anche le opportunità e i preventivi associati. Sicuro di voler eliminarla?' ,'en_us' => 'Deleting this account will remove its related potentials and quotes. Are you sure you want to delete it?'));
SDK::setLanguageEntries('ALERT_ARR', 'DELETE_ACCOUNTS', array('it_it' => 'Cancellando queste aziende verranno cancellate anche le opportunità e i preventivi associati. Sicuro di voler eliminarla?' ,'en_us' => 'Deleting these accounts will remove its related potentials and quotes. Are you sure you want to delete them?'));
SDK::setLanguageEntries('ALERT_ARR', 'DELETE_VENDOR', array('it_it' => 'Cancellando questa azienda verranno cancellate anche le opportunità e i preventivi associati. Sicuro di voler eliminarla?' ,'en_us' => 'Deleting this vendor will remove its related purchase orders. Are you sure you want to delete it?'));
SDK::setLanguageEntries('ALERT_ARR', 'DELETE_VENDORS', array('it_it' => 'Cancellado questi fornitori verranno rimossi anche gli ordini di acquisto correlati. Sicuro di voler eliminarli?' ,'en_us' => 'Deleting these vendors will remove its related purchase orders. Are you sure you want to delete them?'));
SDK::setLanguageEntries('ALERT_ARR', 'SELECT_MAILID', array('it_it' => 'Prego selezionare un identificativo di email valido' ,'en_us' => 'Please Select a mailid'));
SDK::setLanguageEntries('ALERT_ARR', 'OVERWRITE_EXISTING_ACCOUNT1', array('it_it' => 'Sovrascrivere l\'indirizzo esistente con l\'indirizzo dell\'Azienda (' ,'en_us' => 'Do you want to Overwrite the existing address with this selected account('));
SDK::setLanguageEntries('ALERT_ARR', 'OVERWRITE_EXISTING_ACCOUNT2', array('it_it' => ') selezionato/a? Cliccando Annulla l\'elemento verrà comunque legato mantenedo indipendenti gli indirizzi.' ,'en_us' => ') address details? If you click Cancel the item is still linked keeping independent addresses.'));
SDK::setLanguageEntries('ALERT_ARR', 'MISSING_FIELDS', array('it_it' => 'Numero di campi non specificato:' ,'en_us' => 'Missing required fields:'));
SDK::setLanguageEntries('ALERT_ARR', 'NOT_ALLOWED_TO_EDIT', array('it_it' => 'non hai i privilegi per modificare questo campo' ,'en_us' => 'you are not allowed to edit this field'));
SDK::setLanguageEntries('ALERT_ARR', 'NOT_ALLOWED_TO_EDIT_FIELDS', array('it_it' => 'non hai i privilegi per modificare questi campi' ,'en_us' => 'you are not allowed to edit the field(s)'));
SDK::setLanguageEntries('ALERT_ARR', 'COLUMNS_CANNOT_BE_EMPTY', array('it_it' => 'Le colonne selezionate non possono essere vuote' ,'en_us' => 'Selected Columns cannot be empty'));
SDK::setLanguageEntries('ALERT_ARR', 'CANNOT_BE_EMPTY', array('it_it' => '%s non può essere vuoto' ,'en_us' => '%s cannot be empty'));
SDK::setLanguageEntries('ALERT_ARR', 'CANNOT_BE_NONE', array('it_it' => '%s non può essere nullo' ,'en_us' => '%s cannot be none'));
SDK::setLanguageEntries('ALERT_ARR', 'ENTER_VALID', array('it_it' => 'Prego inserire un valido ' ,'en_us' => 'Please enter a valid '));
SDK::setLanguageEntries('ALERT_ARR', 'SHOULDBE_LESS', array('it_it' => ' deve essere minore di ' ,'en_us' => ' should be less than '));
SDK::setLanguageEntries('ALERT_ARR', 'SHOULDBE_LESS_EQUAL', array('it_it' => ' deve essere minore o uguale a ' ,'en_us' => ' should be less than or equal to '));
SDK::setLanguageEntries('ALERT_ARR', 'SHOULDBE_EQUAL', array('it_it' => ' deve essere uguale a ' ,'en_us' => ' should be equal to '));
SDK::setLanguageEntries('ALERT_ARR', 'SHOULDBE_GREATER', array('it_it' => ' deve essere maggiore di ' ,'en_us' => ' should be greater than '));
SDK::setLanguageEntries('ALERT_ARR', 'SHOULDBE_GREATER_EQUAL', array('it_it' => ' deve essere maggiore o uguale a ' ,'en_us' => ' should be greater than or equal to '));
SDK::setLanguageEntries('ALERT_ARR', 'SHOULDNOTBE_EQUAL', array('it_it' => ' non deve essere uguale a ' ,'en_us' => ' should not be equal to '));
SDK::setLanguageEntries('ALERT_ARR', 'SHOULDBE_LESS_1', array('it_it' => '%s deve essere minore di %d' ,'en_us' => '%s should be less than %d'));
SDK::setLanguageEntries('ALERT_ARR', 'SHOULDBE_LESS_EQUAL_1', array('it_it' => '%s deve essere minore o uguale a %d' ,'en_us' => '%s should be less than or equal to %d'));
SDK::setLanguageEntries('ALERT_ARR', 'SHOULDBE_EQUAL_1', array('it_it' => '%s deve essere uguale a %d' ,'en_us' => '%s should be equal to %d'));
SDK::setLanguageEntries('ALERT_ARR', 'SHOULDBE_GREATER_1', array('it_it' => '%s deve essere maggiore di %d' ,'en_us' => '%s should be greater than %d'));
SDK::setLanguageEntries('ALERT_ARR', 'SHOULDBE_GREATER_EQUAL_1', array('it_it' => '%s deve essere maggiore o uguale a %d' ,'en_us' => '%s should be greater than or equal to %d'));
SDK::setLanguageEntries('ALERT_ARR', 'SHOULDNOTBE_EQUAL_1', array('it_it' => '%s non deve essere uguale a %d' ,'en_us' => '%s should not be equal to %d'));
SDK::setLanguageEntries('ALERT_ARR', 'DATE_SHOULDBE_LESS', array('it_it' => '%s deve essere minore di %s' ,'en_us' => '%s should be less than %s'));
SDK::setLanguageEntries('ALERT_ARR', 'DATE_SHOULDBE_LESS_EQUAL', array('it_it' => '%s deve essere minore o uguale a %s' ,'en_us' => '%s should be less than or equal to %s'));
SDK::setLanguageEntries('ALERT_ARR', 'DATE_SHOULDBE_EQUAL', array('it_it' => '%s deve essere uguale a %s' ,'en_us' => '%s should be equal to %s'));
SDK::setLanguageEntries('ALERT_ARR', 'DATE_SHOULDBE_GREATER', array('it_it' => '%s deve essere maggiore di %s' ,'en_us' => '%s should be greater than %s'));
SDK::setLanguageEntries('ALERT_ARR', 'DATE_SHOULDBE_GREATER_EQUAL', array('it_it' => '%s deve essere maggiore o uguale a %s' ,'en_us' => '%s should be greater than or equal to %s'));
SDK::setLanguageEntries('ALERT_ARR', 'DATE_SHOULDNOTBE_EQUAL', array('it_it' => '%s non deve essere uguale a %s' ,'en_us' => '%s should not be equal to %s'));
SDK::setLanguageEntries('ALERT_ARR', 'LENGTH_SHOULDBE_LESS', array('it_it' => '%s %s deve essere minore di %d %s' ,'en_us' => '%s %s should be less than %d %s'));
SDK::setLanguageEntries('ALERT_ARR', 'LENGTH_SHOULDBE_LESS_EQUAL', array('it_it' => '%s %s deve essere minore o uguale a %d %s' ,'en_us' => '%s %s should be less than or equal to %d %s'));
SDK::setLanguageEntries('ALERT_ARR', 'LENGTH_SHOULDBE_EQUAL', array('it_it' => '%s %s deve essere uguale a %d %s' ,'en_us' => '%s %s should be equal to %d %s'));
SDK::setLanguageEntries('ALERT_ARR', 'LENGTH_SHOULDBE_GREATER', array('it_it' => '%s %s deve essere maggiore di %d %s' ,'en_us' => '%s %s should be greater than %d %s'));
SDK::setLanguageEntries('ALERT_ARR', 'LENGTH_SHOULDBE_GREATER_EQUAL', array('it_it' => '%s %s deve essere maggiore o uguale a %d %s' ,'en_us' => '%s %s should be greater than or equal to %d %s'));
SDK::setLanguageEntries('ALERT_ARR', 'LENGTH_SHOULDNOTBE_EQUAL', array('it_it' => '%s %s non deve essere uguale a %d %s' ,'en_us' => '%s %s should not be equal to %d %s'));
SDK::setLanguageEntries('ALERT_ARR', 'INVALID', array('it_it' => 'Non valido ' ,'en_us' => 'Invalid '));
SDK::setLanguageEntries('ALERT_ARR', 'EXCEEDS_MAX', array('it_it' => ' esce dai limiti massimi ' ,'en_us' => ' exceeds the maximum limit '));
SDK::setLanguageEntries('ALERT_ARR', 'OUT_OF_RANGE', array('it_it' => ' fuori dai limiti massimi ' ,'en_us' => ' is out of range'));
SDK::setLanguageEntries('ALERT_ARR', 'PORTAL_PROVIDE_EMAILID', array('it_it' => 'L\'utente del portale deve specificare un identificativo di email per entrare' ,'en_us' => 'Portal user should provide email Id for portal login'));
SDK::setLanguageEntries('ALERT_ARR', 'ADD_CONFIRMATION', array('it_it' => 'Sicuro di voler aggiungere ' ,'en_us' => 'Are you sure you want to add the selected '));
SDK::setLanguageEntries('ALERT_ARR', 'ACCOUNTNAME_CANNOT_EMPTY', array('it_it' => 'Nome Azienda non può essere vuoto' ,'en_us' => 'AccountName Cannot be Empty'));
SDK::setLanguageEntries('ALERT_ARR', 'CANT_SELECT_CONTACTS', array('it_it' => 'Non è possibile selezionare il contatto correlato dal Lead' ,'en_us' => 'You can\'t select related contacts from Lead'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_THIS', array('it_it' => 'Questo ' ,'en_us' => 'This '));
SDK::setLanguageEntries('ALERT_ARR', 'DOESNOT_HAVE_MAILIDS', array('it_it' => ' non ha alcun identificativo di email' ,'en_us' => ' doesn\'t have any mail ids'));
SDK::setLanguageEntries('ALERT_ARR', 'ARE_YOU_SURE', array('it_it' => 'Sicuro?' ,'en_us' => 'Are You Sure You want to Delete?'));
SDK::setLanguageEntries('ALERT_ARR', 'DOESNOT_HAVE_AN_MAILID', array('it_it' => '\" non ha alcun identificativo di email' ,'en_us' => '\" doesn\'t have an Email Id'));
SDK::setLanguageEntries('ALERT_ARR', 'MISSING_REQUIRED_FIELDS', array('it_it' => 'Mancano dei campi obbligatori: ' ,'en_us' => 'Missing required fields: '));
SDK::setLanguageEntries('ALERT_ARR', 'READONLY', array('it_it' => 'è in sola lettura' ,'en_us' => 'it\'s readonly'));
SDK::setLanguageEntries('ALERT_ARR', 'SELECT_ATLEAST_ONE_USER', array('it_it' => 'Prego, selezionare almeno un utente' ,'en_us' => 'Please select at least one user'));
SDK::setLanguageEntries('ALERT_ARR', 'DISABLE_SHARING_CONFIRMATION', array('it_it' => 'Sicuro di voler disabilitare le condivisioni selezionate ' ,'en_us' => 'Are you sure you want to disable sharing for selected '));
SDK::setLanguageEntries('ALERT_ARR', 'USERS', array('it_it' => ' utente(i) ?' ,'en_us' => ' user(s) ?'));
SDK::setLanguageEntries('ALERT_ARR', 'ENDTIME_GREATER_THAN_STARTTIME', array('it_it' => 'Data fine deve essere maggiore di data inizio ' ,'en_us' => 'End Time should be greater than Start Time '));
SDK::setLanguageEntries('ALERT_ARR', 'FOLLOWUPTIME_GREATER_THAN_STARTTIME', array('it_it' => 'Ora di Followup deve essere maggiore della data inizio ' ,'en_us' => 'Followup Time should be greater than End Time '));
SDK::setLanguageEntries('ALERT_ARR', 'MISSING_EVENT_NAME', array('it_it' => 'Omesso il nome dell\'evento' ,'en_us' => 'Missing Event Name'));
SDK::setLanguageEntries('ALERT_ARR', 'EVENT_TYPE_NOT_SELECTED', array('it_it' => 'Non e\' stato scelto il tipo di evento' ,'en_us' => 'Event Type is not selected'));
SDK::setLanguageEntries('ALERT_ARR', 'OPPORTUNITYNAME_CANNOT_BE_EMPTY', array('it_it' => 'Il nome dell\'opportunità non può essere vuoto' ,'en_us' => 'Potential Name field cannot be empty'));
SDK::setLanguageEntries('ALERT_ARR', 'CLOSEDATE_CANNOT_BE_EMPTY', array('it_it' => 'Data di chiusura non può essere vuota' ,'en_us' => 'Closing Date cannot be Empty'));
SDK::setLanguageEntries('ALERT_ARR', 'SITEURL_CANNOT_BE_EMPTY', array('it_it' => 'L\'URL del sito non può essere vuoto' ,'en_us' => 'Site Url cannot be empty'));
SDK::setLanguageEntries('ALERT_ARR', 'SITENAME_CANNOT_BE_EMPTY', array('it_it' => 'Il nome del sito non può essere vuoto' ,'en_us' => 'Site Name cannot be empty'));
SDK::setLanguageEntries('ALERT_ARR', 'LISTPRICE_CANNOT_BE_EMPTY', array('it_it' => 'Il prezzo di listino non può essere vuoto' ,'en_us' => 'List Price cannot be empty'));
SDK::setLanguageEntries('ALERT_ARR', 'INVALID_LIST_PRICE', array('it_it' => 'Prezzo di listino non valido' ,'en_us' => 'Invalid List Price'));
SDK::setLanguageEntries('ALERT_ARR', 'PROBLEM_ACCESSSING_URL', array('it_it' => 'Problemi di accesso all\'URL: ' ,'en_us' => 'Problem accessing url: '));
SDK::setLanguageEntries('ALERT_ARR', 'CODE', array('it_it' => ' Codice: ' ,'en_us' => ' Code: '));
SDK::setLanguageEntries('ALERT_ARR', 'WISH_TO_QUALIFY_MAIL_AS_CONTACT', array('it_it' => 'Sicuro di voler qualificare questa email come contatto?' ,'en_us' => 'Are you sure you wish to Qualify this Mail as Contact?'));
SDK::setLanguageEntries('ALERT_ARR', 'SELECT_ATLEAST_ONEMSG_TO_DEL', array('it_it' => 'Prego, selezionare almeno un messaggio da rimuover' ,'en_us' => 'Please select at least one message to delete'));
SDK::setLanguageEntries('ALERT_ARR', 'ERROR', array('it_it' => 'Errore' ,'en_us' => 'Error'));
SDK::setLanguageEntries('ALERT_ARR', 'FIELD_TYPE_NOT_SELECTED', array('it_it' => 'Tipo di campo non selezionato' ,'en_us' => 'Field Type is not selected'));
SDK::setLanguageEntries('ALERT_ARR', 'SPECIAL_CHARACTERS_NOT_ALLOWED', array('it_it' => 'I caratteri speciali non sono ammessi nella label del campo' ,'en_us' => 'Special characters are not allowed in Label field'));
SDK::setLanguageEntries('ALERT_ARR', 'PICKLIST_CANNOT_BE_EMPTY', array('it_it' => 'Il valore della picklist non può essere vuoto' ,'en_us' => 'Picklist value cannot be empty'));
SDK::setLanguageEntries('ALERT_ARR', 'DUPLICATE_VALUES_FOUND', array('it_it' => 'Valore duplicato non trovato' ,'en_us' => 'Duplicate Values found'));
SDK::setLanguageEntries('ALERT_ARR', 'DUPLICATE_MAPPING_ACCOUNTS', array('it_it' => 'Mappatura duplicati per le Aziende!' ,'en_us' => 'Duplicate mapping for accounts!!'));
SDK::setLanguageEntries('ALERT_ARR', 'DUPLICATE_MAPPING_CONTACTS', array('it_it' => 'Mappatura dei duplicati per i Contatti!' ,'en_us' => 'Duplicate mapping for Contacts!!'));
SDK::setLanguageEntries('ALERT_ARR', 'DUPLICATE_MAPPING_POTENTIAL', array('it_it' => 'Mappatura dei duplicati per le Opportunità!' ,'en_us' => 'Duplicate mapping for Potential!!'));
SDK::setLanguageEntries('ALERT_ARR', 'ERROR_WHILE_EDITING', array('it_it' => 'Errore in modifica' ,'en_us' => 'Error while Editing'));
SDK::setLanguageEntries('ALERT_ARR', 'CURRENCY_CHANGE_INFO', array('it_it' => 'Il cambio di valuta è avvenuto con successo' ,'en_us' => 'Currency Changes has been made Successfully'));
SDK::setLanguageEntries('ALERT_ARR', 'CURRENCY_CONVERSION_INFO', array('it_it' => 'Sicuro di voler usare il dollaro $ come valuta? Premi su OK per rimanere nel $, Annulla per cambiare il tasso di cambio.' ,'en_us' => 'Are you using Dollar $ as Currency? Click OK to remain as $, Cancel to change the currency conversion rate.'));
SDK::setLanguageEntries('ALERT_ARR', 'THE_EMAILID', array('it_it' => 'L\'identificativo di email \'' ,'en_us' => 'The email id \''));
SDK::setLanguageEntries('ALERT_ARR', 'EMAIL_FIELD_INVALID', array('it_it' => '\' nel campo email non è valido.' ,'en_us' => '\' in the email field is invalid'));
SDK::setLanguageEntries('ALERT_ARR', 'MISSING_REPORT_NAME', array('it_it' => 'Nome report non specificato' ,'en_us' => 'Missing Report Name'));
SDK::setLanguageEntries('ALERT_ARR', 'REPORT_NAME_EXISTS', array('it_it' => 'Nome report già esistente, prova ancora...' ,'en_us' => 'Report name already exists, try again...'));
SDK::setLanguageEntries('ALERT_ARR', 'WANT_TO_CHANGE_CONTACT_ADDR', array('it_it' => 'Vuoi modificare gli indirizzi del contatto collegato a questa Azienda?' ,'en_us' => 'Do you want to change the addresses of the Contacts related to this Account?'));
SDK::setLanguageEntries('ALERT_ARR', 'SURE_TO_DELETE', array('it_it' => 'Sicuro di voler eliminare ?' ,'en_us' => 'Are you sure you want to delete ?'));
SDK::setLanguageEntries('ALERT_ARR', 'NO_PRODUCT_SELECTED', array('it_it' => 'Nessun prodotto selezionato. Selezionare almeno un prodotto' ,'en_us' => 'No product is selected. Select at least one Product'));
SDK::setLanguageEntries('ALERT_ARR', 'VALID_FINAL_PERCENT', array('it_it' => 'Inserire un valore valido per la percentuale di sconto' ,'en_us' => 'Enter valid Final Discount Percentage'));
SDK::setLanguageEntries('ALERT_ARR', 'VALID_FINAL_AMOUNT', array('it_it' => 'Inserire un valore valido per l\'ammontare dello sconto' ,'en_us' => 'Enter valid Final Discount Amount'));
SDK::setLanguageEntries('ALERT_ARR', 'VALID_SHIPPING_CHARGE', array('it_it' => 'Inserire un valore valido per le spese di spedizione' ,'en_us' => 'Enter a valid Shipping &amp; Handling charge'));
SDK::setLanguageEntries('ALERT_ARR', 'VALID_ADJUSTMENT', array('it_it' => 'Inserire un valore valido per l\'arrotondamento' ,'en_us' => 'Enter a valid Adjustment'));
SDK::setLanguageEntries('ALERT_ARR', 'WANT_TO_CONTINUE', array('it_it' => 'Vuoi procedere?' ,'en_us' => 'Do you want to Continue?'));
SDK::setLanguageEntries('ALERT_ARR', 'ENTER_VALID_TAX', array('it_it' => 'Prego inserire un valore valido per le tasse' ,'en_us' => 'Please enter Valid TAX value'));
SDK::setLanguageEntries('ALERT_ARR', 'VALID_TAX_NAME', array('it_it' => 'Prego inserire un nome tassa valido' ,'en_us' => 'Enter valid Tax Name'));
SDK::setLanguageEntries('ALERT_ARR', 'CORRECT_TAX_VALUE', array('it_it' => 'Prego inserire un valore corretto per la tassa' ,'en_us' => 'Enter Correct Tax Value'));
SDK::setLanguageEntries('ALERT_ARR', 'ENTER_POSITIVE_VALUE', array('it_it' => 'Prego inserire un valore positivo' ,'en_us' => 'Please enter positive value'));
SDK::setLanguageEntries('ALERT_ARR', 'LABEL_SHOULDNOT_EMPTY', array('it_it' => 'La label della tassa non può essere vuota' ,'en_us' => 'The tax label name should not be empty'));
SDK::setLanguageEntries('ALERT_ARR', 'NOT_VALID_ENTRY', array('it_it' => 'non è un valore valido. Prego inserirne un valido' ,'en_us' => 'is not a valid entry. Please enter correct value'));
SDK::setLanguageEntries('ALERT_ARR', 'VALID_DISCOUNT_PERCENT', array('it_it' => 'Prego inserire un valore valido per la percentuale di sconto' ,'en_us' => 'Enter a valid Discount percentage'));
SDK::setLanguageEntries('ALERT_ARR', 'VALID_DISCOUNT_AMOUNT', array('it_it' => 'Prego inserire un valore valido per l\'ammontare dello sconto' ,'en_us' => 'Enter a valid Discount Amount'));
SDK::setLanguageEntries('ALERT_ARR', 'SELECT_TEMPLATE_TO_MERGE', array('it_it' => 'Prego selezionare un template per il merge' ,'en_us' => 'Please select a template to merge'));
SDK::setLanguageEntries('ALERT_ARR', 'SELECTED_MORE_THAN_ONCE', array('it_it' => 'Hai selezionato i seguenti prodotti più di una volta.' ,'en_us' => 'You have selected the following product(s) more than once.'));
SDK::setLanguageEntries('ALERT_ARR', 'YES', array('it_it' => 'si' ,'en_us' => 'yes'));
SDK::setLanguageEntries('ALERT_ARR', 'NO', array('it_it' => 'no' ,'en_us' => 'no'));
SDK::setLanguageEntries('ALERT_ARR', 'MAIL', array('it_it' => 'mail' ,'en_us' => 'mail'));
SDK::setLanguageEntries('ALERT_ARR', 'EQUALS', array('it_it' => 'uguale' ,'en_us' => 'equals'));
SDK::setLanguageEntries('ALERT_ARR', 'NOT_EQUALS_TO', array('it_it' => 'diverso' ,'en_us' => 'not equal to'));
SDK::setLanguageEntries('ALERT_ARR', 'STARTS_WITH', array('it_it' => 'inizia per' ,'en_us' => 'starts with'));
SDK::setLanguageEntries('ALERT_ARR', 'CONTAINS', array('it_it' => 'contiene' ,'en_us' => 'contains'));
SDK::setLanguageEntries('ALERT_ARR', 'DOES_NOT_CONTAINS', array('it_it' => 'non contiene' ,'en_us' => 'does not contains'));
SDK::setLanguageEntries('ALERT_ARR', 'LESS_THAN', array('it_it' => 'minore di' ,'en_us' => 'less than'));
SDK::setLanguageEntries('ALERT_ARR', 'GREATER_THAN', array('it_it' => 'maggiore di' ,'en_us' => 'greater than'));
SDK::setLanguageEntries('ALERT_ARR', 'LESS_OR_EQUALS', array('it_it' => 'minore o uguale' ,'en_us' => 'less or equal'));
SDK::setLanguageEntries('ALERT_ARR', 'GREATER_OR_EQUALS', array('it_it' => 'maggiore o uguale' ,'en_us' => 'greater or equal'));
SDK::setLanguageEntries('ALERT_ARR', 'NO_SPECIAL_CHARS', array('it_it' => 'I caratteri speciali non sono amessi nella stringa di fattura' ,'en_us' => 'Special Characters are not allowed in Invoice String'));
SDK::setLanguageEntries('ALERT_ARR', 'SHARED_EVENT_DEL_MSG', array('it_it' => 'L\'utente non ha il permesso di cancellare il record' ,'en_us' => 'The User does not have permission to Edit/Delete Shared Event.'));
SDK::setLanguageEntries('ALERT_ARR', 'PLS_SELECT_VALID_FILE', array('it_it' => 'Selezionare un file con le seguenti estensioni: ' ,'en_us' => 'Please select a file with the following extension:'));
SDK::setLanguageEntries('ALERT_ARR', 'NO_SPECIAL', array('it_it' => 'caratteri speciali non sono ammessi' ,'en_us' => 'Special Characters are not allowed'));
SDK::setLanguageEntries('ALERT_ARR', 'NO_QUOTES', array('it_it' => 'Apostrofi (\'), virgolette (\") ed il simbolo di somma (+) non sono consentiti ' ,'en_us' => ''));
SDK::setLanguageEntries('ALERT_ARR', 'IN_PROFILENAME', array('it_it' => ' nel nome del profilo' ,'en_us' => ' in Profile Name'));
SDK::setLanguageEntries('ALERT_ARR', 'IN_GROUPNAME', array('it_it' => ' nel nome del gruppo' ,'en_us' => ' in Group Name'));
SDK::setLanguageEntries('ALERT_ARR', 'IN_ROLENAME', array('it_it' => ' nel nome del ruolo' ,'en_us' => ' in Role Name'));
SDK::setLanguageEntries('ALERT_ARR', 'VALID_TAX_PERCENT', array('it_it' => 'Prego inserire un valore valido per la percentuale delle tasse' ,'en_us' => 'Enter a valid Tax percentage'));
SDK::setLanguageEntries('ALERT_ARR', 'VALID_SH_TAX', array('it_it' => 'Prego inserire un valore valido per le tasse di spedizione ' ,'en_us' => 'Enter valid Taxes for shipping and handling '));
SDK::setLanguageEntries('ALERT_ARR', 'ROLE_DRAG_ERR_MSG', array('it_it' => 'Non è consentito cancellare un ruolo superiore da uno inferiore' ,'en_us' => 'You cannot move a Parent Node under a Child Node'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_DEL', array('it_it' => 'Canc' ,'en_us' => 'del'));
SDK::setLanguageEntries('ALERT_ARR', 'VALID_DATA', array('it_it' => ' Prego inserire un dato valido... ' ,'en_us' => ' Enter Valid Data ,Please try again... '));
SDK::setLanguageEntries('ALERT_ARR', 'STDFILTER', array('it_it' => 'Filri standard' ,'en_us' => 'Standard Filters'));
SDK::setLanguageEntries('ALERT_ARR', 'STARTDATE', array('it_it' => 'Data inzio' ,'en_us' => 'Start Date'));
SDK::setLanguageEntries('ALERT_ARR', 'ENDDATE', array('it_it' => 'Data fine' ,'en_us' => 'End Date'));
SDK::setLanguageEntries('ALERT_ARR', 'START_DATE_TIME', array('it_it' => 'Data e orario inizio' ,'en_us' => 'Start Date &amp; Time'));
SDK::setLanguageEntries('ALERT_ARR', 'START_TIME', array('it_it' => 'Orario inizio' ,'en_us' => 'Start Time'));
SDK::setLanguageEntries('ALERT_ARR', 'DATE_SHOULDNOT_PAST', array('it_it' => 'Data e orario corrente per le attività pianificate' ,'en_us' => 'Current date &amp; time for Activities with status as Planned'));
SDK::setLanguageEntries('ALERT_ARR', 'TIME_SHOULDNOT_PAST', array('it_it' => 'Ora corrente per le attivitàpianificate' ,'en_us' => 'Current Time for Activities with status as Planned'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_AND', array('it_it' => 'E' ,'en_us' => 'And'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ENTER_VALID_PORT', array('it_it' => 'Prego inserire un numero di porta valido' ,'en_us' => 'Please enter valid port number'));
SDK::setLanguageEntries('ALERT_ARR', 'IN_USERNAME', array('it_it' => ' nel Nome Utente ' ,'en_us' => ' in Username '));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ENTER_VALID_NO', array('it_it' => 'Prego inseire un numero valido' ,'en_us' => 'Please enter valid number'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_PROVIDE_YES_NO', array('it_it' => ' Valore non valido. Prego inserire Yes o No' ,'en_us' => ' Invalid value. Please Provide Yes or No'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SELECT_CRITERIA', array('it_it' => ' Criterio non valido. Prego selezionare un criterio' ,'en_us' => ' Invalid criteria. Please select criteria'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_WRONG_IMAGE_TYPE', array('it_it' => 'Tipo di file immagine per i contatti - jpeg, png, jpg, pjpeg, x-png or gif' ,'en_us' => 'Allowed file types for Contacts - jpeg, png, jpg, pjpeg, x-png or gif'));
SDK::setLanguageEntries('ALERT_ARR', 'SELECT_MAIL_MOVE', array('it_it' => 'Prego scegli una mail e poi sposta..' ,'en_us' => 'Please select a mail and then move..'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_NOTSEARCH_WITHSEARCH_ALL', array('it_it' => 'Non hai utilizzato la funzione di ricerca. Tutti i record verranno esportati in' ,'en_us' => 'You haven\'t used the search. All the records will be Exported from '));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_NOTSEARCH_WITHSEARCH_CURRENTPAGE', array('it_it' => 'Non hai utilizzato la funzione di ricerca. Ma hai selezionato l\'opzione ricerca&amp;pagina corrente, per cui i record della pagina corrente verranno esportati in ' ,'en_us' => 'You haven\'t searched any thing. But you selected with search &amp; current page options. So the records in the current page will be Exported from '));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_NO_DATA_SELECTED', array('it_it' => 'Nessun record selezionato. Seleziona almeno un record da esportare.' ,'en_us' => 'There is no record selected. Select at least one record to Export'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SEARCH_WITHOUTSEARCH_ALL', array('it_it' => 'Hai utilizzato l\'opzione di ricerca ma non hai selezionato senza ricerca e tutte le opzioni. Clicca [ok] per esportare tutti i dati o clicca [cancel] e riprova con altri criteri di esportazione' ,'en_us' => 'You have used search option but you have not selected without search &amp; all options. You can click [ok] to export all data or You can click [cancel] and try again with other export criteria'));
SDK::setLanguageEntries('ALERT_ARR', 'STOCK_IS_NOT_ENOUGH', array('it_it' => 'Lo Stock in magazzino non è sufficiente' ,'en_us' => 'Stock is not enough'));
SDK::setLanguageEntries('ALERT_ARR', 'INVALID_QTY', array('it_it' => 'Quantità non valida' ,'en_us' => 'Invalid Qty'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SEARCH_WITHOUTSEARCH_CURRENTPAGE', array('it_it' => 'Hai utilizzato l\'opzione di ricerca ma non hai selezionato senza ricerca e le opzioni per la pagina corrente. Clicca [ok] per esportare i dati della pagina corrente o clicca [cancel] e riprova con altri criteri di esportazione.' ,'en_us' => 'You have used search option but you have not selected without search &amp; currentpage options. You can click [ok] to export current page data or You can click [cancel] and try again with some other export criteria.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SELECT_COLUMN', array('it_it' => 'Colonna non valida. Prego seleziona una colonna valida' ,'en_us' => ' Invalid column. Please select column'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_NOT_ACCESSIBLE', array('it_it' => 'Non Accessibile' ,'en_us' => 'Not Accessible'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_FILENAME_LENGTH_EXCEED_ERR', array('it_it' => 'Il nome del file non può superare i 255 caratteri di lunghezza' ,'en_us' => 'Filename cannot exceed 255 characters'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_DONT_HAVE_EMAIL_PERMISSION', array('it_it' => 'Non hai i permessi per il campo Email quindi non puoi selezionare l\'id dell\'email' ,'en_us' => 'You don\'t have permission for Email field so you can\'t choose the email id'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_NO_FEEDS_SELECTED', array('it_it' => 'Nessun Feed selezionato' ,'en_us' => 'No Feeds Selected'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SELECT_PICKLIST', array('it_it' => 'Prego seleziona almeno un valore da eliminare' ,'en_us' => 'Please select at least one value to delete'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_CANT_REMOVE', array('it_it' => 'Non puoi eliminare tutti i valori' ,'en_us' => 'You can\'t remove all the values'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_UTF8', array('it_it' => 'Prego cambiare il file di configurazione (situato nella root di vtiger, con il nome config-inc.php) per il supporto al set di caratteri UTF-8 e poi aggiorna la pagina' ,'en_us' => ''));
SDK::setLanguageEntries('ALERT_ARR', 'SPECIAL_CHARACTERS', array('it_it' => 'I caratteri speciali' ,'en_us' => 'Special characters'));
SDK::setLanguageEntries('ALERT_ARR', 'NOT_ALLOWED', array('it_it' => 'non sono ammessi nella label del campo. Prego provare con altri valori' ,'en_us' => 'are not allowed. Please try with some other values'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_NONE', array('it_it' => 'Nessuno' ,'en_us' => 'None'));
SDK::setLanguageEntries('ALERT_ARR', 'ENDS_WITH', array('it_it' => 'finisce con' ,'en_us' => 'ends with'));
SDK::setLanguageEntries('ALERT_ARR', 'POTENTIAL_AMOUNT_CANNOT_BE_EMPTY', array('it_it' => 'Potential amount cannot be empty' ,'en_us' => 'Potential amount cannot be empty'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ALERT_EXT_CODE', array('it_it' => 'E\' stato trovata una azienda con lo stesso codice esterno, vuoi fare il merge delle due aziende?' ,'en_us' => 'There is an account with the same external code, do you want to merge these clients?'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ALERT_EXT_CODE_NOTFOUND', array('it_it' => 'Non è stata trovata nessuna azienda importata con quel codice esterno, operazione annullata' ,'en_us' => 'No accounts with that external code found. Operation aborted'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ALERT_EXT_CODE_COMMIT', array('it_it' => 'Merge delle aziende avvenuto con successo, verra\' caricata la pagina dell\'azienda importata' ,'en_us' => 'Accounts merged succesfully, You will be redirected to the page of the merged account'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ALERT_EXT_CODE_FAIL', array('it_it' => 'Operazione fallita' ,'en_us' => 'Operation failed'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ALERT_EXT_CODE_DUPLICATE', array('it_it' => 'Si sta tentando di rifare il merge con quel codice oppure di usare il codice di un\'azienda cancellata, operazione annullata. Svuotare il cestino e riprovare.' ,'en_us' => 'Merge already done with that code or the code is used also by a deleted account, operation aborted. Empty the Recycle Bin and try again.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ALERT_EXT_CODE_SAVE', array('it_it' => 'Vuoi salvare lo stesso le modifiche?' ,'en_us' => 'Do you want to save changes anyway?'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ALERT_EXT_CODE_NOTFOUND_SAVE', array('it_it' => 'Non è stata trovata nessuna azienda importato con quel codice esterno, salvare anche il codice esterno?' ,'en_us' => 'No accounts with that code were found.Do you want to save external code anyway?'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ALERT_EXT_CODE_NOTFOUND_SAVE2', array('it_it' => 'Non è stata trovata nessuna azienda importata con quel codice esterno, salvare comunque le modifiche all\'azienda?' ,'en_us' => 'No accounts with that code were found.Do you want to save anyway?'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ALERT_EXT_CODE_NO_PERMISSION', array('it_it' => 'Esiste già un\'azienda con lo stesso codice assegnata ad un altro utente, non hai quindi i permessi per eseguire il merge.' ,'en_us' => 'There is already an account with the same code assigned to other users. So you can\'t merge it.'));
SDK::setLanguageEntries('ALERT_ARR', 'DOESNOT_HAVE_AN_FAXID', array('it_it' => '\" non ha un numero di fax' ,'en_us' => '\" doesn\'t have a Fax Id'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_DONT_HAVE_FAX_PERMISSION', array('it_it' => 'Non hai i permessi per il campo Fax quindi non puoi selezionare l\'id del Fax' ,'en_us' => 'You don\'t have permission for Fax field so you can\'t choose the fax id'));
SDK::setLanguageEntries('ALERT_ARR', 'DOESNOT_HAVE_AN_SMSID', array('it_it' => '\" non ha un numero di cellulare' ,'en_us' => '\" doesn\'t have a Sms Id'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_DONT_HAVE_SMS_PERMISSION', array('it_it' => 'Non hai i permessi per il campo Cellulare quindi non puoi selezionare l\'id dell\'sms' ,'en_us' => 'You don\'t have permission for Mobile field so you can\'t choose the sms id'));
SDK::setLanguageEntries('ALERT_ARR', 'NO_RULES_FOUND', array('it_it' => 'Nessuna regola trovata per questo modulo, Sarai rimandato al form di creazione di una nuova regola' ,'en_us' => 'No rules found for this module, You will redirected to the rule creation form'));
SDK::setLanguageEntries('ALERT_ARR', 'SAME_GROUPS', array('it_it' => 'Devi selezionare i record presenti nello stesso gruppo di unione' ,'en_us' => 'You have to select the records in the same groups for merging'));
SDK::setLanguageEntries('ALERT_ARR', 'ATLEAST_TWO', array('it_it' => 'Seleziona almeno due record per l\'unione' ,'en_us' => 'Select at least two records for merging'));
SDK::setLanguageEntries('ALERT_ARR', 'MAX_THREE', array('it_it' => 'è permesso selezionare un massimo di tre record' ,'en_us' => 'You are allowed to select a maximum of three records'));
SDK::setLanguageEntries('ALERT_ARR', 'MAX_RECORDS', array('it_it' => 'è permesso selezionare un massimo di quattro record' ,'en_us' => 'You are allowed to select a maximum of four records'));
SDK::setLanguageEntries('ALERT_ARR', 'CON_MANDATORY', array('it_it' => 'Seleziona il campo obbligatorio \"Cognome\"' ,'en_us' => 'Select the mandatory field Last Name'));
SDK::setLanguageEntries('ALERT_ARR', 'LE_MANDATORY', array('it_it' => 'Seleziona il campo obbligatorio \"Cognome\" e \"Società\"' ,'en_us' => 'Select the mandatory fields Last Name and Company'));
SDK::setLanguageEntries('ALERT_ARR', 'ACC_MANDATORY', array('it_it' => 'Seleziona il campo obbligatorio \"Nome azienda\"' ,'en_us' => 'Select the mandatory field Account Name'));
SDK::setLanguageEntries('ALERT_ARR', 'PRO_MANDATORY', array('it_it' => 'Seleziona il campo obbligatorio \"Nome Prodotto\"' ,'en_us' => 'Select the mandatory field Product Name'));
SDK::setLanguageEntries('ALERT_ARR', 'TIC_MANDATORY', array('it_it' => 'Seleziona il campo obbligatorio \"Titolo Ticket\"' ,'en_us' => 'Select the mandatory field Ticket Title'));
SDK::setLanguageEntries('ALERT_ARR', 'POTEN_MANDATORY', array('it_it' => 'Seleziona il campo obbligatorio \"Nome Opportunità\"' ,'en_us' => 'Select the mandatory field Potential Name'));
SDK::setLanguageEntries('ALERT_ARR', 'VEN_MANDATORY', array('it_it' => 'Seleziona il campo obbligatorio \"Nome fornitore\"' ,'en_us' => 'Select the mandatory field Vendor Name'));
SDK::setLanguageEntries('ALERT_ARR', 'DEL_MANDATORY', array('it_it' => 'Non hai i permessi per cancellare un campo obbligatorio' ,'en_us' => 'You are not allowed to delete the mandatory field'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_HIDEHIERARCH', array('it_it' => 'Nascondi gerarchia' ,'en_us' => 'Hide hierarchy'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SHOWHIERARCH', array('it_it' => 'Visualizza gerarchia' ,'en_us' => 'Show hierarchy'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_NO_ROLES_SELECTED', array('it_it' => 'Non è stato selezionato alcun ruolo, continuare ugualmente?' ,'en_us' => 'No roles have been selected, do you wish to continue?'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_DUPLICATE_FOUND', array('it_it' => 'Trovate voci Duplicate per questo valore ' ,'en_us' => 'Duplicate entries found for the value '));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_CANNOT_HAVE_EMPTY_VALUE', array('it_it' => 'Impossibile sostituire con valore vuoto, per rimuovere il valore usa l\'opzione Elimina.' ,'en_us' => 'Cannot replace with blank value, to remove the value use Delete option.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_DUPLICATE_VALUE_EXISTS', array('it_it' => 'Valore duplicato esistente' ,'en_us' => 'Duplicate value exists'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_WANT_TO_DELETE', array('it_it' => 'Questo eliminerà i valori selezionati dalla picklist per tutti i ruoli. Sicuro di voler continuare? ' ,'en_us' => 'This will delete the selected picklist value(s) for all roles. You sure you want to continue? '));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_DELETE_ALL_WARNING', array('it_it' => 'Deve avere almeno un valore per la picklist' ,'en_us' => 'Must have at least one value for the picklist'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_PLEASE_CHANGE_REPLACEMENT', array('it_it' => 'prego cambiare il valore di sostituzione; è stato selezionato per l\'eliminazione' ,'en_us' => 'please change the replacement value; it is also selected for delete'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_BLANK_REPLACEMENT', array('it_it' => 'Non è possibile selezionare un valore vuoto per la sostituzione' ,'en_us' => 'Cannot select blank value for replacement'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_PLEASE_SELECT', array('it_it' => '--Prego Selezionare--' ,'en_us' => '--Please select--'));
SDK::setLanguageEntries('ALERT_ARR', 'MUST_BE_CHECKED', array('it_it' => 'Deve essere selezionato' ,'en_us' => 'Must be checked'));
SDK::setLanguageEntries('ALERT_ARR', 'CHARACTER', array('it_it' => 'caratteri' ,'en_us' => 'characters'));
SDK::setLanguageEntries('ALERT_ARR', 'LENGTH', array('it_it' => 'la lunghezza di' ,'en_us' => 'length of'));
SDK::setLanguageEntries('ALERT_ARR', 'MSG_CHANGE_CURRENCY_REVISE_UNIT_PRICE', array('it_it' => 'Prezzo unitario di tutte le valute sarà rivisto sulla base della valuta scelta. Sicuro ?' ,'en_us' => 'Unit price of all the Currencies will be revised based on the selected Currency. Are you sure?'));
SDK::setLanguageEntries('ALERT_ARR', 'Select_one_record_as_parent_record', array('it_it' => 'Seleziona un record come rcord relazionato' ,'en_us' => 'Select one record as parent record'));
SDK::setLanguageEntries('ALERT_ARR', 'RECURRING_FREQUENCY_NOT_PROVIDED', array('it_it' => 'Frequenza periodica non fornita' ,'en_us' => 'Recurring frequency not provided'));
SDK::setLanguageEntries('ALERT_ARR', 'RECURRING_FREQNECY_NOT_ENABLED', array('it_it' => 'Frequenza periodica è fornita, ma non abilitata' ,'en_us' => 'Recurring frequency is provided, but recurring is not enabled'));
SDK::setLanguageEntries('ALERT_ARR', 'NO_SPECIAL_CHARS_DOCS', array('it_it' => 'Caratteri Speciali come apici, backslash, +, % e ? non sono consentiti' ,'en_us' => 'Special characters like quotes, backslash, + symbol, % and ? are not allowed'));
SDK::setLanguageEntries('ALERT_ARR', 'FOLDER_NAME_TOO_LONG', array('it_it' => 'Nome Cartella troppo lungo. Prova ancora!' ,'en_us' => 'Folder name is too long. Try again!'));
SDK::setLanguageEntries('ALERT_ARR', 'FOLDERNAME_EMPTY', array('it_it' => 'Nome Cartella non può essere vuoto' ,'en_us' => 'The Folder name cannot be empty'));
SDK::setLanguageEntries('ALERT_ARR', 'DUPLICATE_FOLDER_NAME', array('it_it' => 'Si è provato a duplicare un nome du una cartella già esistente. Prego prova ancora !' ,'en_us' => 'Trying to duplicate an existing folder name. Please try again !'));
SDK::setLanguageEntries('ALERT_ARR', 'FOLDER_DESCRIPTION_TOO_LONG', array('it_it' => 'Descrizione Cartella troppo lunga. Prova ancora!' ,'en_us' => 'Folder description is too long. Try again!'));
SDK::setLanguageEntries('ALERT_ARR', 'NOT_PERMITTED', array('it_it' => 'Non ti è permesso eseguire questa operazione.' ,'en_us' => 'You are not permitted to execute this operation.'));
SDK::setLanguageEntries('ALERT_ARR', 'ALL_FILTER_CREATION_DENIED', array('it_it' => 'Impossibile creare una CustomView usando il nome \"Tutto\", prova ad usare un nome diverso' ,'en_us' => 'Cannot create CustomView using name \"All\", try using different ViewName'));
SDK::setLanguageEntries('ALERT_ARR', 'OPERATION_DENIED', array('it_it' => 'Operazione non eseguibile' ,'en_us' => 'You are denied to perform this operation'));
SDK::setLanguageEntries('ALERT_ARR', 'EMAIL_CHECK_MSG', array('it_it' => 'Disabilita accesso al Portale per salvare l\'email come campo vuoto' ,'en_us' => 'Disable portal access to save the email field as blank'));
SDK::setLanguageEntries('ALERT_ARR', 'IS_PARENT', array('it_it' => 'Questo Prodotto ha sotto prodotti, Non ti è consentito scegliere una relazione per questo prodotto' ,'en_us' => 'This Product has Sub Products, You are not allowed to choose a Parent for this Product'));
SDK::setLanguageEntries('ALERT_ARR', 'BLOCK_NAME_CANNOT_BE_BLANK', array('it_it' => 'Nome Blocco non può essere vuoto' ,'en_us' => 'Block name can not be blank'));
SDK::setLanguageEntries('ALERT_ARR', 'ARE_YOU_SURE_YOU_WANT_TO_DELETE', array('it_it' => 'Sicuro di volerlo eliminare ?' ,'en_us' => 'Are you sure you want to delete ?'));
SDK::setLanguageEntries('ALERT_ARR', 'PLEASE_MOVE_THE_FIELDS_TO_ANOTHER_BLOCK', array('it_it' => 'Prego, spostare il campo in un altro blocco' ,'en_us' => 'Please move the fields to another block'));
SDK::setLanguageEntries('ALERT_ARR', 'ARE_YOU_SURE_YOU_WANT_TO_DELETE_BLOCK', array('it_it' => 'Sicuro di volerl eliminare il blocco ?' ,'en_us' => 'Are you sure you want to delete block?'));
SDK::setLanguageEntries('ALERT_ARR', 'LABEL_CANNOT_NOT_EMPTY', array('it_it' => 'Etichetta non può essere Vuota' ,'en_us' => 'Label cannot be Emtpy'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_TYPEALERT_1', array('it_it' => 'Spiacente, non si può mappare' ,'en_us' => 'Sorry, you cannot map the'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_WITH', array('it_it' => 'con' ,'en_us' => 'with'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_TYPEALERT_2', array('it_it' => 'tipo dato. Per favore mappa gli stessi tipi di dato.' ,'en_us' => 'data type. Kindly map the same data types.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_LENGTHALERT', array('it_it' => 'Spiacenti, non è possibile mappare i campi con diversi caratteri. Per favore mappa i dati con gli stessi o più caratteri.' ,'en_us' => 'Sorry, you can cannot map fields with different character size. Kindly map the data with same or more character size.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_DECIMALALERT', array('it_it' => 'Spiacenti, non è possibile mappare i campi con diversi decimali. Per favore mappa i dati con gli stessi o più decimali.' ,'en_us' => 'Sorry, you can cannot map fields with different decimal places. Kindly map the data with same or more decimal places.'));
SDK::setLanguageEntries('ALERT_ARR', 'FIELD_IS_MANDATORY', array('it_it' => 'Campo Obbligatorio' ,'en_us' => 'Mandatory Field'));
SDK::setLanguageEntries('ALERT_ARR', 'FIELD_IS_ACTIVE', array('it_it' => 'Campo disponibile per l\'uso' ,'en_us' => 'Field is available for use'));
SDK::setLanguageEntries('ALERT_ARR', 'FIELD_IN_QCREATE', array('it_it' => 'Presente in Creazione Veloce' ,'en_us' => 'Present in Quick Create'));
SDK::setLanguageEntries('ALERT_ARR', 'FIELD_IS_MASSEDITABLE', array('it_it' => 'Disponibile per il Mass Edit' ,'en_us' => 'Available for Mass Edit'));
SDK::setLanguageEntries('ALERT_ARR', 'IS_MANDATORY_FIELD', array('it_it' => 'è un Campo Obbligatorio' ,'en_us' => 'is Mandatory Field'));
SDK::setLanguageEntries('ALERT_ARR', 'AMOUNT_CANNOT_BE_EMPTY', array('it_it' => 'Quantità non può essere Vuota' ,'en_us' => 'Amount cannot be Empty'));
SDK::setLanguageEntries('ALERT_ARR', 'LABEL_ALREADY_EXISTS', array('it_it' => 'Etichetta già presente. Prego inserire una nome diverso' ,'en_us' => 'Label already exists. Please specify a different Label'));
SDK::setLanguageEntries('ALERT_ARR', 'LENGTH_OUT_OF_RANGE', array('it_it' => 'Lunghezza del blocco deve essere inferiore a 50 caratteri' ,'en_us' => 'Length of the Block should be less than 50 characters'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SELECT_ONE_FILE', array('it_it' => 'Prego selezionare almeno un File' ,'en_us' => 'Please select at least one File'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_UNABLE_TO_ADD_FOLDER', array('it_it' => 'Imposibile aggiunger cartella. Prego prova ancora.' ,'en_us' => 'Unable to add Folder. Please try again.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ARE_YOU_SURE_YOU_WANT_TO_DELETE_FOLDER', array('it_it' => 'Sicuro di voler eliminare la cartella?' ,'en_us' => 'Are you sure you want to delete the folder?'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ERROR_WHILE_DELETING_FOLDER', array('it_it' => 'Errore mentre si eliminava la cartella. Prego prova ancora.' ,'en_us' => 'Error while deleting the folder.Please try again later.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_FILE_CAN_BE_DOWNLOAD', array('it_it' => 'File è disponibile per il download' ,'en_us' => 'File is available for download'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_DOCUMENT_LOST_INTEGRITY', array('it_it' => 'Questi Documneti non sono disponibili. Saranno marcati come Inattivi' ,'en_us' => 'This Documents is not available. It will be marked as Inactive'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_DOCUMENT_NOT_AVAILABLE', array('it_it' => 'Questo Documento non è disponibile per il Download' ,'en_us' => 'This Document is not available for Download'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_FOLDER_SHOULD_BE_EMPTY', array('it_it' => 'La Cartella dovrebbe essere vuota per rimuoverla!' ,'en_us' => 'Folder should be empty to remove it!'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_PLEASE_SELECT_FILE_TO_UPLOAD', array('it_it' => 'Prego selezionare il File da caricare.' ,'en_us' => 'Please select the file to upload.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ARE_YOU_SURE_TO_MOVE_TO', array('it_it' => 'Sicuro di voler spostare i file in ' ,'en_us' => 'Are you sure you want to move the file(s) to '));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_FOLDER', array('it_it' => ' cartella' ,'en_us' => ' folder'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_UNABLE_TO_UPDATE', array('it_it' => 'Impossibile Aggiornare! Prego prova ancora.' ,'en_us' => 'Unable to update! Please try it again.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_IMAGE_DELETED', array('it_it' => 'Immagine Eliminata' ,'en_us' => 'Image Deleted'));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_FIELD_SELECTION', array('it_it' => 'Qualche errore nel campo selezionato' ,'en_us' => 'Some error in field selection'));
SDK::setLanguageEntries('ALERT_ARR', 'NO_LINE_ITEM_SELECTED', array('it_it' => 'Nessuna riga è stata selezioanta. Prego selezionare almeno una riga.' ,'en_us' => 'No line item is selected. Please select at least one line item.'));
SDK::setLanguageEntries('ALERT_ARR', 'LINE_ITEM', array('it_it' => 'Riga prodotto' ,'en_us' => 'Product'));
SDK::setLanguageEntries('ALERT_ARR', 'LIST_PRICE', array('it_it' => 'Listino' ,'en_us' => 'List Price'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_WIDGET_HIDDEN', array('it_it' => 'Widget Nascosto' ,'en_us' => 'Widget Hidden'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_RESTORE_FROM_PREFERENCES', array('it_it' => 'Puoi Ripristinarlo dalle tue preferenze' ,'en_us' => 'You can restore it from your preferences'));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_HIDING', array('it_it' => 'Errore mentre si nascondeva il widget' ,'en_us' => 'Error while hiding'));
SDK::setLanguageEntries('ALERT_ARR', 'MSG_TRY_AGAIN', array('it_it' => 'Prego prova ancora' ,'en_us' => 'Please try again'));
SDK::setLanguageEntries('ALERT_ARR', 'MSG_ENABLE_SINGLEPANE_VIEW', array('it_it' => 'Vita a Pannello Singolo Abilitata' ,'en_us' => 'Singlepane View Enabled'));
SDK::setLanguageEntries('ALERT_ARR', 'MSG_DISABLE_SINGLEPANE_VIEW', array('it_it' => 'Vita a Pannello Singolo Disabilitata' ,'en_us' => 'Singlepane View Disabled'));
SDK::setLanguageEntries('ALERT_ARR', 'MSG_FTP_BACKUP_DISABLED', array('it_it' => 'FTP Backup Disabilitato' ,'en_us' => 'FTP Backup Disabled'));
SDK::setLanguageEntries('ALERT_ARR', 'MSG_LOCAL_BACKUP_DISABLED', array('it_it' => 'Backup Locale Disabilitato' ,'en_us' => 'Local Backup Disabled'));
SDK::setLanguageEntries('ALERT_ARR', 'MSG_FTP_BACKUP_ENABLED', array('it_it' => 'FTP Backup Abilitato' ,'en_us' => 'FTP Backup Enabled'));
SDK::setLanguageEntries('ALERT_ARR', 'MSG_LOCAL_BACKUP_ENABLED', array('it_it' => 'Backup Locale Abilitato' ,'en_us' => 'Local Backup Enabled'));
SDK::setLanguageEntries('ALERT_ARR', 'MSG_CONFIRM_PATH', array('it_it' => 'percorso aggiornato' ,'en_us' => 'confirm with the Path details'));
SDK::setLanguageEntries('ALERT_ARR', 'MSG_CONFIRM_FTP_DETAILS', array('it_it' => 'conferma con i dettagli FTP' ,'en_us' => 'confirm with the FTP details'));
SDK::setLanguageEntries('ALERT_ARR', 'START_PERIOD_END_PERIOD_CANNOT_BE_EMPTY', array('it_it' => 'Periodo Iniziale o Finale non può essere vuoto' ,'en_us' => 'Start period or End period cannot be empty'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADD', array('it_it' => 'Aggiungi ' ,'en_us' => 'Add '));
SDK::setLanguageEntries('ALERT_ARR', 'Module', array('it_it' => 'Modulo' ,'en_us' => 'Module'));
SDK::setLanguageEntries('ALERT_ARR', 'DashBoard', array('it_it' => 'Cruscotto' ,'en_us' => 'DashBoard'));
SDK::setLanguageEntries('ALERT_ARR', 'RSS', array('it_it' => 'RSS' ,'en_us' => 'RSS'));
SDK::setLanguageEntries('ALERT_ARR', 'Default', array('it_it' => 'Default' ,'en_us' => 'Default'));
SDK::setLanguageEntries('ALERT_ARR', 'SPECIAL_CHARS', array('it_it' => ' / &lt; &gt; + \' \" ' ,'en_us' => '\\ / &lt; &gt; + \' \" '));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_PIVA', array('it_it' => 'Partita IVA non valida !' ,'en_us' => ''));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_CF', array('it_it' => 'Codice Fiscale non valido !' ,'en_us' => ''));
SDK::setLanguageEntries('ALERT_ARR', 'EXISTING_RECORD', array('it_it' => 'Record già esistente nel sistema con dati: ' ,'en_us' => 'Record already exists width these dates: '));
SDK::setLanguageEntries('ALERT_ARR', 'EXISTING_SAVE', array('it_it' => 'Vuoi salvare lo stesso?' ,'en_us' => 'Do you want to save anymore?'));
SDK::setLanguageEntries('ALERT_ARR', 'EXISTING_SAVE_CONVERTLEAD', array('it_it' => 'Procedendo il contatto e l\'eventuale opportunità verranno collegate all\'azienda esistente.' ,'en_us' => 'If you click to OK the contact and the potential will be linked to the existing account.'));
SDK::setLanguageEntries('ALERT_ARR', 'no_valid_extension', array('it_it' => 'Estensione del file non valida. Le enstensioni permesse sono pdf,ps e tiff' ,'en_us' => 'Not valid file extension.Allowed extensions are pdf,ps and tiff'));
SDK::setLanguageEntries('ALERT_ARR', 'BETWEEN', array('it_it' => 'tra' ,'en_us' => 'between'));
SDK::setLanguageEntries('ALERT_ARR', 'BEFORE', array('it_it' => 'prima' ,'en_us' => 'before'));
SDK::setLanguageEntries('ALERT_ARR', 'AFTER', array('it_it' => 'dopo' ,'en_us' => 'after'));
SDK::setLanguageEntries('ALERT_ARR', 'ERROR_DELETING_TRY_AGAIN', array('it_it' => 'Errore durante la cancellazione.Prego riprova.' ,'en_us' => 'Error while deleting.Please try again.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ENTER_WINDOW_TITLE', array('it_it' => 'Prego inserire il titolo della finestra.' ,'en_us' => 'Please enter Window Title.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SELECT_ONLY_FIELDS', array('it_it' => 'Prego seleziona solo 2 campi.' ,'en_us' => 'Please select only two fields.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ENTER_RSS_URL', array('it_it' => 'Prego inserire l\'indirizzo del RSS' ,'en_us' => 'Please enter RSS URL'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ENTER_URL', array('it_it' => 'Prego inserire l\'indirizzo del website' ,'en_us' => 'Please enter URL'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_DELETED_SUCCESSFULLY', array('it_it' => 'Widget cancellato con successo.' ,'en_us' => 'Widget deleted sucessfully.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADD_HOME_WIDGET', array('it_it' => 'Non è stato possibile aggiungere il widget!! Prego riprova.' ,'en_us' => 'Unable to add homestuff! Please try again'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_STATUS_CHANGING', array('it_it' => 'Cambia lo stato in ' ,'en_us' => 'Change state in '));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_STATUS_CHANGING_MOTIVATION', array('it_it' => ' note :' ,'en_us' => ' note :'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_STATUS_PLEASE_SELECT_A_MODULE', array('it_it' => 'Scegliere un modulo' ,'en_us' => 'Choose a Module'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_STATUS_PLEASE_SELECT_A_ROLE', array('it_it' => 'Scegliere un ruolo' ,'en_us' => 'Choose a Role'));
SDK::setLanguageEntries('ALERT_ARR', 'OVERWRITE_EXISTING_CONTACT1', array('it_it' => 'Sovrascrivere l\'indirizzo esistente con l\'indirizzo del Contatto (' ,'en_us' => 'Do you want to Overwrite the existing address with this selected contact('));
SDK::setLanguageEntries('ALERT_ARR', 'OVERWRITE_EXISTING_CONTACT2', array('it_it' => ') selezionato/a? Cliccando Annulla l\'elemento verrà comunque legato mantenedo indipendenti gli indirizzi.' ,'en_us' => ') address details? If you click Cancel the item is still linked keeping independent addresses.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_MASS_EDIT_WITHOUT_WF_1', array('it_it' => 'Hai selezionato più di ' ,'en_us' => 'You have selected more than '));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_MASS_EDIT_WITHOUT_WF_2', array('it_it' => ' elementi, questo potrebbe sovraccaricare il server. Procedere all\'aggiornamento escludendo i Workflow?' ,'en_us' => ' items, this may overload the server. Proceed to update excluding the Workflow?'));
SDK::setLanguageEntries('ALERT_ARR', 'SELECT_SMSID', array('it_it' => 'Prego selezionare un identificativo di sms valido' ,'en_us' => 'Please Select a mailid'));
SDK::setLanguageEntries('ALERT_ARR', 'NOTVALID_SMSID', array('it_it' => 'Numero di Cellulare non valido' ,'en_us' => 'Sms number not valid'));
SDK::setLanguageEntries('ALERT_ARR', 'NULL_SMSID', array('it_it' => 'Nessun numero di Cellulare specificato' ,'en_us' => 'No Sms number defined'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_MANDATORY_FIELDS_WF', array('it_it' => 'Inserire il valore per i campi obbligatori' ,'en_us' => 'Please enter value for mandatory fields'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_DELETE_MSG', array('it_it' => 'Sei sicuro di volere eliminare il webform?' ,'en_us' => 'Are you sure, you want to delete the webform?'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_DUPLICATE_NAME', array('it_it' => 'Il Webform esiste già' ,'en_us' => 'Webform already exists'));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_SELECT_EITHER', array('it_it' => 'Selezionare Azienda o Contatto per convertire il Lead' ,'en_us' => 'Select either Organization or Contact to convert the lead'));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_SELECT_ACCOUNT', array('it_it' => 'Selezionare l\'Azienda per procedere' ,'en_us' => 'Select Organization to proceed'));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_SELECT_CONTACT', array('it_it' => 'Selezionare il Contatto per procedere' ,'en_us' => 'Select Contact to proceed'));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_MANDATORY_FIELD_VALUE', array('it_it' => 'Alcuni valori obbligatori sono mancanti, si prega di inserirli' ,'en_us' => 'Values for Mandatory Fields are missing'));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_POTENTIAL_AMOUNT', array('it_it' => 'L\'Ammontare deve essere un importo valido' ,'en_us' => 'Potential Amount must be a number'));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_EMAILID', array('it_it' => 'Inserire un valido Email Id' ,'en_us' => 'Enter valid Email Id'));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_TRANSFER_TO_ACC', array('it_it' => 'Deve essere selezionata un\'Azienda per trasferire i record relazionati' ,'en_us' => 'Organization should be selected to transfer related records'));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_TRANSFER_TO_CON', array('it_it' => 'Deve essere selzionato un contatto per trasferire i record relazionati' ,'en_us' => 'Contact should be selected to transfer related records '));
SDK::setLanguageEntries('ALERT_ARR', 'SURE_TO_DELETE_CUSTOM_MAP', array('it_it' => 'Sei sicuro di voler cancellare la Mappatura Campi?' ,'en_us' => 'Are you sure you want to delete the Field Mapping?'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_CLOSE_DATE', array('it_it' => 'Data Chiusura' ,'en_us' => 'Close Date'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_EMAIL', array('it_it' => 'Email' ,'en_us' => 'Email'));
SDK::setLanguageEntries('ALERT_ARR', 'MORE_THAN_500', array('it_it' => 'Hai selezionato più di 500 records. Questa operazione potrebbe impiegare molto tempo. Vuoi continuare?' ,'en_us' => 'You selected more than 500 records. For this action it may take longer time. Are you sure want to proceed?'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_MAPPEDALERT', array('it_it' => 'Il campo è già stato mappato' ,'en_us' => 'The field has been already mapped'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SELECT_DEL_FOLDER', array('it_it' => 'Seleziona almeno una cartella' ,'en_us' => 'Select at least one folder'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_NO_EMPTY_FOLDERS', array('it_it' => 'Non ci sono cartelle vuote' ,'en_us' => 'There are no empty folders'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_OR', array('it_it' => 'o' ,'en_us' => 'or'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SAVING_DRAFT', array('it_it' => 'Salvataggio bozza in corso' ,'en_us' => 'Saving draft'));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_SELECT_ATLEAST_ONE_MERGE_CRITERIA_FIELD', array('it_it' => 'Selezionare almeno un campo per il controllo duplicati' ,'en_us' => 'Select at least one field for merge criteria'));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_FIELDS_MAPPED_MORE_THAN_ONCE', array('it_it' => 'Il seguente campo è mappato più di una volta. Controllare la mappatura.' ,'en_us' => 'Following field is mapped more than once. Please check the mapping.'));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_PLEASE_MAP_MANDATORY_FIELDS', array('it_it' => 'è necessario mappare i seguenti campi obbligatori' ,'en_us' => 'Please map the following mandatory fields'));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_MAP_NAME_CANNOT_BE_EMPTY', array('it_it' => 'Inserire un nome per la mappatura' ,'en_us' => 'Map name cannot be empty'));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_MAP_NAME_ALREADY_EXISTS', array('it_it' => 'Esiste già una mappatura con questo nome.' ,'en_us' => 'Map name already exists. Please give a different name'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_UT208_PASSWORDEMPTY', array('it_it' => 'Scrivi una password' ,'en_us' => 'Type a password'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_UT208_INVALIDSRV', array('it_it' => 'Risposta non valida dal server' ,'en_us' => 'Invalid server answer'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_UT208_WRONGPWD', array('it_it' => 'Password errata' ,'en_us' => 'Wrong password'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_UT208_DIFFPWD', array('it_it' => 'Le password non coincidono' ,'en_us' => 'Passwords are not equal'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_UT208_PWDCRITERIA', array('it_it' => 'La password deve essere di almeno 6 caratteri' ,'en_us' => 'Password must be at least 6 characters long'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_CHECK_BOUNCED_MESSAGES', array('it_it' => 'Verifica email respinte' ,'en_us' => 'Check bounced messages'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_MAX_REPORT_SECMODS', array('it_it' => 'Hai raggiunto il numero massimo di moduli secondari' ,'en_us' => 'You reached the maximum number of related modules'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_FILTER', array('it_it' => 'Filtro' ,'en_us' => 'Filter'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_TEMPLATE_MUST_HAVE_NAME', array('it_it' => 'Devi dare un nome al template' ,'en_us' => 'You have to give a name to the template'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_MUST_TYPE_SUBJECT', array('it_it' => 'Devi specificare un oggetto' ,'en_us' => 'You have to type a subject'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SELECT_RECIPIENTS', array('it_it' => 'Seleziona almeno un destinatario' ,'en_us' => 'Select at least one recipient'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SELECT_TEMPLATE', array('it_it' => 'Seleziona un template' ,'en_us' => 'Select a template'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_FILL_FIELDS', array('it_it' => 'Compilare i seguenti campi' ,'en_us' => 'Fill the following fields'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SEND_TEST_EMAIL', array('it_it' => 'Devi inviare l\'email di test prima' ,'en_us' => 'You have to send the test email first'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_INVALID_EMAIL', array('it_it' => 'Indirizzo email non valido' ,'en_us' => 'Invalid email address'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_TEST_EMAIL_SENT', array('it_it' => 'Email di test spedita correttamente' ,'en_us' => 'Test Email sent correctly'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ERROR_SENDING_TEST_EMAIL', array('it_it' => 'Errore durante la spedizione dell\'email di test' ,'en_us' => 'Error while sending test email'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ERROR_SAVING', array('it_it' => 'Errore durante il salvataggio' ,'en_us' => 'Error while saving'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_NEWSLETTER_SCHEDULED', array('it_it' => 'Newsletter creata e pianificata per l\'ora indicata' ,'en_us' => 'Newsletter created and scheduled for the specified time'));
SDK::setLanguageEntries('ALERT_ARR', 'SEND_MAIL_ERROR', array('it_it' => 'Errore: invio mail non riuscito' ,'en_us' => 'Error: sending email failed'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SAVE_LAST_CHANGES', array('it_it' => 'Vuoi salvare? Cliccando Annulla verranno perse le modifiche appena apportate.' ,'en_us' => 'Do you want to save the last changes? Click OK to save or Cancel to dismiss.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_FPOFV_RULE_NAME', array('it_it' => 'Nome Regola' ,'en_us' => 'Rule name'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_LEAST_ONE_CONDITION', array('it_it' => 'Manca almeno una condizione su un campo' ,'en_us' => 'Insert at least one condition'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_STARTDATE', array('it_it' => 'da' ,'en_us' => 'from'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_ENDDATE', array('it_it' => 'a' ,'en_us' => 'to'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_DATE_CUSTOM', array('it_it' => 'personalizzato' ,'en_us' => 'custom'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_DATE_YESTARDAY', array('it_it' => 'ieri' ,'en_us' => 'yesterday'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_DATE_TODAY', array('it_it' => 'oggi' ,'en_us' => 'today'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_DATE_LASTWEEK', array('it_it' => 'settimana scorsa' ,'en_us' => 'lastweek'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_DATE_THISWEEK', array('it_it' => 'questa settimana' ,'en_us' => 'thisweek'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_DATE_LASTMONTH', array('it_it' => 'mese scorso' ,'en_us' => 'lastmonth'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_DATE_THISMONTH', array('it_it' => 'questo mese' ,'en_us' => 'thismonth'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_DATE_LAST60DAYS', array('it_it' => 'scorsi 60 giorni' ,'en_us' => 'last60days'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADVSEARCH_DATE_LAST90DAYS', array('it_it' => 'scorsi 90 giorni' ,'en_us' => 'last90days'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_TEMPLATE_MUST_HAVE_UNSUBSCRIPTION_LINK', array('it_it' => 'Manca il link per la disiscrizione. Procedere comunque?' ,'en_us' => 'Missing link for unsubscribing. Proceed anyway?'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_TEMPLATE_MUST_HAVE_PREVIEW_LINK', array('it_it' => 'Manca il link per l\'anteprima. Procedere comunque?' ,'en_us' => 'Missing link for the preview. Proceed anyway?'));
SDK::setLanguageEntries('ALERT_ARR', 'HAS_CHANGED', array('it_it' => 'è cambiato in' ,'en_us' => 'has changed to'));
SDK::setLanguageEntries('ALERT_ARR', 'ANSWER_SENT', array('it_it' => 'La risposta e` stata inviata' ,'en_us' => 'The answer has been sent'));
SDK::setLanguageEntries('ALERT_ARR', 'CONFIRM_LINKED_EVENT_DELETION', array('it_it' => 'Vuoi anche eliminare l\'evento collegato?' ,'en_us' => 'Do you also want to delete the linked event?'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_TOO_LONG', array('it_it' => '%s e` troppo lungo' ,'en_us' => '%s is too long'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_NAME', array('it_it' => 'Nome' ,'en_us' => 'Name'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_NAME_S', array('it_it' => 'Nome %s' ,'en_us' => '%s name'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_FILL_ALL_FIELDS', array('it_it' => 'Inserire i valori per tutti i campi richiesti' ,'en_us' => 'Please fill all the required fields'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_FILTER_FIELD_MORE_THAN_ONCE', array('it_it' => 'Hai selezionato lo stesso campo più di una volta. I campi devono essere diversi' ,'en_us' => 'You selected the same field more than once. The fields must be all different'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SELECT_AT_LEAST_ONE_FIELD', array('it_it' => 'Seleziona almeno un campo' ,'en_us' => 'Please select at least one field'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_MMAKER_CONFIRM_RESET', array('it_it' => 'Sicuro di voler ripristinare i files allo stato iniziale? Verranno perse eventuali modifiche.' ,'en_us' => 'Are you sure to restore the files to their original state? All modifications will be lost.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_WANT_TO_SAVE_PENDING_CHANGES', array('it_it' => 'Vuoi salvare le modifiche effettuate?' ,'en_us' => 'Do you want to save the pending modifications?'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SURE_TO_UNINSTALL_MODULE', array('it_it' => 'Disinstallando il modulo, ne verranno rimossi tutti i record. Procedere?' ,'en_us' => 'Uninstalling the module will remove all of its records. Do you want to proceed?'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_TOO_MANY_UITYPE4', array('it_it' => 'E\' presente più di un campo di tipo Numerazione Automatica. E\' possibile crearne solo uno per modulo' ,'en_us' => 'There is more than one Auto Numbering field. It\'s possible to create only one of them per module'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SAMEMODULERELATED', array('it_it' => 'Il modulo %s è presente in più di un campo relazione. E\' possible avere solo una relazione per ogni modulo collegato' ,'en_us' => 'The module %s is present in more than one relation field. It\'s possible to have only one relation for each module'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_PLEASE_SELECT_MODULE', array('it_it' => 'Seleziona un modulo' ,'en_us' => 'Please select one module'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_PLEASE_SELECT_VALUE', array('it_it' => 'Seleziona un valore' ,'en_us' => 'Please select a value'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_FIELD_IS_NUMERIC', array('it_it' => 'Il campo %s deve essere un numero' ,'en_us' => 'The field %s must be a number'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_FIELD_IS_INVALID', array('it_it' => 'Il campo %s non è corretto' ,'en_us' => 'The field %s is not correct'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_CSVPATH_MUST_NOT_BE_ABSOLUTE', array('it_it' => 'Il percorso del file CSV non può essere assoluto' ,'en_us' => 'The CSV path must not be absolute'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_VALUE_TOO_SMALL', array('it_it' => 'Valore troppo piccolo' ,'en_us' => 'Value too small'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_VALUE_TOO_BIG', array('it_it' => 'Valore troppo grande' ,'en_us' => 'Value too big'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_INVALID_VALUE', array('it_it' => 'Valore non valido' ,'en_us' => 'Invalid value'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_CONTINUE_WITHOUT_KEY_FIELD', array('it_it' => 'Non è stato selezionato nessun campo come chiave. Ad ogni importazione i record verranno aggiunti al CRM. Confermi?' ,'en_us' => 'No key field has been selected. On every import run, the records will be added to the CRM. Proceed?'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_DATA_IMPORT_SCHEDULED_NOW', array('it_it' => 'L\'importazione è stata messa in coda. Entro pochi minuti inizierà automaticamente' ,'en_us' => 'The import has been queued. It will start automatically in a few minutes'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_DATA_IMPORT_ABORTED', array('it_it' => 'L\'importazione è stata annullata. In caso fosse già iniziata, verrà interrotta entro pochi minuti' ,'en_us' => 'The import has been canceled. If the process has already started, it will be interrupted in a few minutes'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SELECT_TABLE_OR_QUERY', array('it_it' => 'Se vuoi usare una query personalizzata, scegli \"Nessuno\" come tabella' ,'en_us' => 'If you want to use a custom query, please select \"None\" as a table'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_CANT_USE_DEFAULT_MAPPED_FIELD', array('it_it' => 'Non puoi usare un default in creazione o modifica per un campo che è mappato nell\'importazione' ,'en_us' => 'You can\'t use a default field if it is already mapped for the import'));
SDK::setLanguageEntries('ALERT_ARR', 'CONFIRM_EMPTY_FOLDER', array('it_it' => 'Sicuro di voler svuotare la cartella? Tutti i messaggi saranno eliminati.' ,'en_us' => 'Are you sure you want to empty this folder? All messages will be deleted.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_FOLLOW', array('it_it' => 'Notificami modifiche' ,'en_us' => 'Notify me of changes'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_UNFOLLOW', array('it_it' => 'Non notificarmi le modifiche' ,'en_us' => 'Don\'t notify me of changes'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_MASS_EDIT_ENQUEUE', array('it_it' => 'Hai selezionato più di {max_records} elementi. L\'elaborazione verrà eseguita in background e verrai notificato al termine.' ,'en_us' => 'You selected more than {max_records} items. The process will continue in background and you\'ll be notified at the end.'));
SDK::setLanguageEntries('ALERT_ARR', 'GROUPAGE_DUPLICATED', array('it_it' => 'Raggruppamento duplicato per il campo: %s' ,'en_us' => 'Groupage duplated for the field: %s'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_NEW_CONDITION_BUTTON_LABEL', array('it_it' => 'Nuova Condizione' ,'en_us' => 'New Condition'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_REMOVE_GROUP_CONDITION', array('it_it' => 'Cancella Gruppo' ,'en_us' => 'Delete group'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_PMH_SELECT_RELATED_TO', array('it_it' => 'Seleziona un\'entità collegata al Process Helper oppure disattivalo' ,'en_us' => 'Select a related record to the Process Helper or disable it'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_PM_CHECK_ACTIVE', array('it_it' => 'Il processo non è ancora attivo. Vuoi attivarlo adesso?' ,'en_us' => 'The process is not yet active. Do you want to activate it now?'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_PM_NO_ENTITY_SELECTED', array('it_it' => 'Nessuna entità selezionata' ,'en_us' => 'No entity selected'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_PM_NO_CHECK_SELECTED', array('it_it' => 'Nessun controllo impostato' ,'en_us' => 'No check set'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_FILESIZE_EXCEEDS_MAX_UPLOAD_SIZE', array('it_it' => 'Il file è più grande della dimensione massima consentita.' ,'en_us' => 'Sorry, the uploaded file exceeds the maximum file allowed size.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_GROUPBY', array('it_it' => 'Raggruppa' ,'en_us' => 'Group by'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SUMMARY', array('it_it' => 'Riassuntivo' ,'en_us' => 'Summary'));
SDK::setLanguageEntries('ALERT_ARR', 'MODULE_RELATED_TO', array('it_it' => 'Modulo relazionato a' ,'en_us' => 'Module related to'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SEARCH', array('it_it' => 'Cerca' ,'en_us' => 'Search'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_CHOOSE_A_REPORT', array('it_it' => 'Scegli un report' ,'en_us' => 'Choose a report'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_BACK', array('it_it' => 'Indietro' ,'en_us' => 'Back'));
SDK::setLanguageEntries('ALERT_ARR', 'MISSING_COMPARATOR', array('it_it' => 'Scegli una condizione di confronto' ,'en_us' => 'Please choose a comparison condition'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_LABEL', array('it_it' => 'Etichetta' ,'en_us' => 'Label'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_KANBAN_DRAG_HERE', array('it_it' => 'Abilita drag here' ,'en_us' => 'Enable drag here'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_DISABLE_MODULE', array('it_it' => 'Disabilitare il modulo %s?' ,'en_us' => 'Disable the module %s?'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_REPORT_NAME', array('it_it' => 'Nome report' ,'en_us' => 'Report name'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_DESCRIPTION', array('it_it' => 'Descrizione' ,'en_us' => 'Description'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_NO_RUN_PROCESSES', array('it_it' => 'Nessun processo eseguito' ,'en_us' => 'No process runs'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_RUN_PROCESSES_OK', array('it_it' => 'Processo eseguito con successo' ,'en_us' => 'Process executed successfully'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_RUN_PROCESSES_ERROR', array('it_it' => 'E\' stato riscontrato un errore nell\'esecuzione del processo' ,'en_us' => 'Error occurred in the execution of the process'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_PM_ELEMENTS_ACTORS', array('it_it' => 'Partecipanti' ,'en_us' => 'Participants'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_PM_SELECT_RESOURCE', array('it_it' => 'Prego selezionare un utente' ,'en_us' => 'Please select the user'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_PM_SELECT_ENTITY', array('it_it' => 'Prego selezionare un\'entità' ,'en_us' => 'Please select an entity'));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_TARGET', array('it_it' => 'Non è possibile creare il target. La lista risulta vuota.' ,'en_us' => 'No target created. The list is empty.'));
SDK::setLanguageEntries('ALERT_ARR', 'ERR_TARGET_XLS', array('it_it' => 'Non è possibile esportare i dati. La lista risulta vuota.' ,'en_us' => 'No data exported. The list is empty.'));
SDK::setLanguageEntries('ALERT_ARR', 'NO_ADDRESS_SELECTED', array('it_it' => 'Nessun indirizzo selezionato' ,'en_us' => 'No address selected'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_PLEASE_ADD_COLUMNS', array('it_it' => 'Aggiungere almeno una colonna' ,'en_us' => 'Please add at least one column'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_PLEASE_NAME_ALL_COLUMNS', array('it_it' => 'Dai un nome a tutte le colonne' ,'en_us' => 'Please give a name to all the columns'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_PLEASE_CHOOSE_FIELDNAME', array('it_it' => 'Inserisci un nome per il campo' ,'en_us' => 'Please give a name to the field'));
SDK::setLanguageEntries('ALERT_ARR', 'HAS_EXACTLY_ROWS', array('it_it' => 'ha esattamente' ,'en_us' => 'has exactly'));
SDK::setLanguageEntries('ALERT_ARR', 'HAS_LESS_ROWS', array('it_it' => 'ha meno di' ,'en_us' => 'has less than'));
SDK::setLanguageEntries('ALERT_ARR', 'HAS_MORE_ROWS', array('it_it' => 'ha più di' ,'en_us' => 'has more than'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ROWS', array('it_it' => 'righe' ,'en_us' => 'rows'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_AUTO_TMP_NAME', array('it_it' => '[AUTO TEMPLATE]' ,'en_us' => '[AUTO TEMPLATE]'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_TODAY', array('it_it' => 'Oggi' ,'en_us' => 'Today'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_CANCEL', array('it_it' => 'Annulla' ,'en_us' => 'Cancel'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_NO_NETWORK', array('it_it' => 'Nessuna connessione di rete disponibile.' ,'en_us' => 'No network connection available.'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_TABLEFIELD_SUM', array('it_it' => 'Somma' ,'en_us' => 'Sum'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_TABLEFIELD_MIN', array('it_it' => 'Minimo' ,'en_us' => 'Min'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_TABLEFIELD_MAX', array('it_it' => 'Massimo' ,'en_us' => 'Max'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_TABLEFIELD_AVERAGE', array('it_it' => 'Media' ,'en_us' => 'Average'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_TABLEFIELD_LAST_VALUE', array('it_it' => 'Ultimo' ,'en_us' => 'Last'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_TABLEFIELD_SEQUENCE', array('it_it' => 'Sequenza' ,'en_us' => 'Sequence'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_TABLEFIELD_CURR_VALUE', array('it_it' => 'Corrente' ,'en_us' => 'Current'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_TABLEFIELD_ALL', array('it_it' => 'Tutti' ,'en_us' => 'All'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_TABLEFIELD_AT_LEAST_ONE', array('it_it' => 'Almeno uno' ,'en_us' => 'At least one'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SELECT_OPTION_DOTDOTDOT', array('it_it' => 'Seleziona Opzione...' ,'en_us' => 'Select Option...'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADD_PICKLIST_VALUE', array('it_it' => 'Prego inserire almeno un valore' ,'en_us' => 'Please make at least one new entry'));
SDK::setLanguageEntries('ALERT_ARR', 'HelpDeskFromMail', array('it_it' => 'Mail Mittente VTE' ,'en_us' => 'VTE From Mail'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_CONFIRM_CLOSE_POPUP', array('it_it' => 'Chiudere il popup?' ,'en_us' => 'Are you sure you want to close the popup?'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_NO_VALUES_TO_DELETE', array('it_it' => 'Nessun valore selezionato' ,'en_us' => 'No values to delete'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_ADDTODO', array('it_it' => 'Compito' ,'en_us' => 'To Do'));
SDK::setLanguageEntries('ALERT_ARR', 'SUCCESS', array('it_it' => 'Success' ,'en_us' => 'Success'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_EXTWS_NO_RETURN_FIELDS', array('it_it' => 'Devi configurare almeno un campo restituito. Puoi aggiungerli manualmente o automaticamte tramite il pulsante Prova Web service' ,'en_us' => 'You have to configure at least one returned field. You can add them manually or use the Test Web service button to do it automatically'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_EXTWS_DUP_RETURN_FIELDS', array('it_it' => 'I campi restituiti devono avere nomi diversi' ,'en_us' => 'Returned fields must have distinct names'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_EXTWS_EMPTY_RETURN_FIELD', array('it_it' => 'Imposta un valore per tutti i campi restituiti' ,'en_us' => 'Specify an expression for all the return fields'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_DONT_USE', array('it_it' => 'Non usare' ,'en_us' => 'Don\'t use'));
SDK::setLanguageEntries('ALERT_ARR', 'DELETE_CONTACT', array('it_it' => 'Cancellando questo contatto verranno cancellate anche le opportunità associate. Sicuro di volerlo eliminare?' ,'en_us' => 'Deleting this contact will remove its related potentials. Are you sure you want to delete it?'));
SDK::setLanguageEntries('ALERT_ARR', 'DELETE_CONTACTS', array('it_it' => 'Cancellando questi contatti verranno cancellate anche le opportunità associate. Sicuro di volerli eliminare?' ,'en_us' => 'Deleting these contacts will remove its related potentials. Are you sure you want to delete them?'));
SDK::setLanguageEntries('ALERT_ARR', 'DB_ROW_LIMIT_REACHED', array('it_it' => 'Il database non permette di aggiungere ulteriori campi. Contatta il servizio clienti VTECRM per aumentare il limite.' ,'en_us' => 'The database doesn\'t allow to add more fields. Contact VTECRM customer service to raise the limit.'));
SDK::setLanguageEntries('ALERT_ARR', 'call', array('it_it' => 'Chiamata' ,'en_us' => 'Call'));
SDK::setLanguageEntries('ALERT_ARR', 'meeting', array('it_it' => 'Riunione' ,'en_us' => 'Meeting'));
SDK::setLanguageEntries('ALERT_ARR', 'tracked', array('it_it' => 'Tracciato' ,'en_us' => 'Tracked'));
SDK::setLanguageEntries('ALERT_ARR', 'select_template', array('it_it' => 'Selezionare il template pdf' ,'en_us' => 'Please select the pdf template'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_IMAP_SERVER_NAME', array('it_it' => 'Nome Server Imap' ,'en_us' => 'Imap Server Name'));
SDK::setLanguageEntries('ALERT_ARR', 'LBL_SMTP_SERVER_NAME', array('it_it' => 'Nome Server Smtp' ,'en_us' => 'Smtp Server Name'));

SDK::setLanguageEntries('', 'LBL_HELPDESK_INFORMATION', array('it_it' => 'Informazione Richiesta Intervento' ,'en_us' => 'Informazione Richiesta Intervento'));
SDK::setLanguageEntries('', 'SINGLE_HelpDesk Informazioni', array('it_it' => 'Informazione Richiesta Intervento' ,'en_us' => 'Informazione Richiesta Intervento'));
SDK::setLanguageEntries('', 'SINGLE_HelpDesk', array('it_it' => 'Richiesta Intervento' ,'en_us' => 'Richiesta Intervento'));
SDK::setLanguageEntries('', 'HelpDesk', array('it_it' => 'Richieste Intervento' ,'en_us' => 'Richieste Intervento'));
SDK::setLanguageEntries('', 'Nome Rischio', array('it_it' => 'Nome Pericolo' ,'en_us' => 'Nome Pericolo'));
*/

/*
require_once('include/utils/VtlibUtils.php');

$translations[$module]['it_it'] = array(
    'LBL_GDPR_INFORMATION' => 'Informazioni GDPR',
    'LBL_GDPR_PRIVACYPOLICY' => 'Visione Informativa',
    'LBL_GDPR_PRIVACYPOLICY_CHECKEDTIME' => 'Data Visione Informativa',
    'LBL_GDPR_PRIVACYPOLICY_REMOTE_ADDRESS' => 'Indirizzo IP Visione Informativa',
    'LBL_GDPR_PERSONAL_DATA' => 'Consenso Dati Personali',
    'LBL_GDPR_PERSONAL_DATA_CHECKEDTIME' => 'Data Consenso Dati Personali',
    'LBL_GDPR_PERSONAL_DATA_REMOTE_ADDRESS' => 'Indirizzo IP Dati Personali',
    'LBL_GDPR_MARKETING' => 'Consenso Marketing',
    'LBL_GDPR_MARKETING_CHECKEDTIME' => 'Data Consenso Marketing',
    'LBL_GDPR_MARKETING_REMOTE_ADDRESS' => 'Indirizzo IP Consenso Marketing',
    'LBL_GDPR_THIRDPARTIES' => 'Consenso Terze Parti',
    'LBL_GDPR_THIRDPARTIES_CHECKEDTIME' => 'Data Consenso Terze Parti',
    'LBL_GDPR_THIRDPARTIES_REMOTE_ADDRESS' => 'Indirizzo IP Consenso Terze Parti',
    'LBL_GDPR_PROFILING' => 'Consenso Profilazione',
    'LBL_GDPR_PROFILING_CHECKEDTIME' => 'Data Consenso Profilazione',
    'LBL_GDPR_PROFILING_REMOTE_ADDRESS' => 'Indirizzo IP Consenso Profilazione',
    'LBL_GDPR_RESTRICTED' => 'Consenso Comunicazione Dati Ambiti Informativa',
    'LBL_GDPR_RESTRICTED_CHECKEDTIME' => 'Data Consenso Comunicazione Dati Ambiti Informativa',
    'LBL_GDPR_RESTRICTED_REMOTE_ADDRESS' => 'Indirizzo IP Consenso Comunicazione Dati Ambiti Informativa',
    'LBL_GDPR_NOTIFYCHANGE' => 'Avvisa Cambio Dati',
    'LBL_GDPR_NOTIFYCHANGE_CHECKEDTIME' => 'Data Consenso Avvisa Cambio Dati',
    'LBL_GDPR_NOTIFYCHANGE_REMOTE_ADDRESS' => 'Indirizzo IP Avvisa Cambio Dati',
    'LBL_GDPR_DELETED' => 'Eliminato',
    'LBL_GDPR_DELETED_CHECKEDTIME' => 'Data Eliminazione',
    'LBL_GDPR_DELETED_REMOTE_ADDRESS' => 'Indirizzo IP Eliminazione',
    'LBL_GDPR_SENTTIME' => 'Data Invio GDPR',
);

$translations[$module]['en_us'] = array(
    'LBL_GDPR_INFORMATION' => 'GDPR Information',
    'LBL_GDPR_PRIVACYPOLICY' => 'Privacy Policy',
    'LBL_GDPR_PRIVACYPOLICY_CHECKEDTIME' => 'Privacy Policy - Date',
    'LBL_GDPR_PRIVACYPOLICY_REMOTE_ADDRESS' => 'Privacy Policy - IP Address',
    'LBL_GDPR_PERSONAL_DATA' => 'Consent to Personal Data',
    'LBL_GDPR_PERSONAL_DATA_CHECKEDTIME' => 'Consent to Personal Data - Date',
    'LBL_GDPR_PERSONAL_DATA_REMOTE_ADDRESS' => 'Consent to Personal Data - IP Address',
    'LBL_GDPR_MARKETING' => 'Consent to Marketing',
    'LBL_GDPR_MARKETING_CHECKEDTIME' => 'Consent to Marketing - Date',
    'LBL_GDPR_MARKETING_REMOTE_ADDRESS' => 'Consent to Marketing - IP Address',
    'LBL_GDPR_THIRDPARTIES' => 'Consent to Third Parties',
    'LBL_GDPR_THIRDPARTIES_CHECKEDTIME' => 'Consent to Third Parties - Date',
    'LBL_GDPR_THIRDPARTIES_REMOTE_ADDRESS' => 'Consent to Third Parties - IP Address',
    'LBL_GDPR_PROFILING' => 'Consent to Profiling',
    'LBL_GDPR_PROFILING_CHECKEDTIME' => 'Consent to Profiling - Date',
    'LBL_GDPR_PROFILING_REMOTE_ADDRESS' => 'Consent to Profiling - IP Address',
    'LBL_GDPR_RESTRICTED' => 'Consent only to specified institutions',
    'LBL_GDPR_RESTRICTED_CHECKEDTIME' => 'Consent only to specified institutions - Date',
    'LBL_GDPR_RESTRICTED_REMOTE_ADDRESS' => 'Consent only to specified institutions - IP Address',
    'LBL_GDPR_NOTIFYCHANGE' => 'Notify contact changes',
    'LBL_GDPR_NOTIFYCHANGE_CHECKEDTIME' => 'Notify contact changes - Date',
    'LBL_GDPR_NOTIFYCHANGE_REMOTE_ADDRESS' => 'Notify contact changes - IP Address',
    'LBL_GDPR_DELETED' => 'Deleted',
    'LBL_GDPR_DELETED_CHECKEDTIME' => 'Deleted - Date',
    'LBL_GDPR_DELETED_REMOTE_ADDRESS' => 'Deleted - IP Address',
    'LBL_GDPR_SENTTIME' => 'GDPR Sent Date',
);

$translations['Newsletter'] = array(
    'it_it' => array(
        'LBL_GDPR_AND_PRICAY_POLICY' => 'GDPR e informativa privacy',
        'LBL_GDPR_VERIFY_LINK' => 'GDPR Accesso - Link di verifica',
        'LBL_GDPR_ACCESS_LINK' => 'GDPR Accesso - Link di accesso',
        'LBL_GDPR_CONFIRM_LINK' => 'GDPR Aggiornamento - Link di conferma',
        'LBL_GDPR_SUPPORT_REQUEST_SENDER' => 'GDPR Richiesta supporto - Mittente',
        'LBL_GDPR_SUPPORT_REQUEST_SUBJECT' => 'GDPR Richiesta supporto - Oggetto',
        'LBL_GDPR_SUPPORT_REQUEST_DESC' => 'GDPR Richiesta supporto - Descrizione',
    ),
    'en_us' => array(
        'LBL_GDPR_AND_PRICAY_POLICY' => 'GDPR and privacy policy',
        'LBL_GDPR_VERIFY_LINK' => 'GDPR Access - Verify link',
        'LBL_GDPR_ACCESS_LINK' => 'GDPR Access - Access link',
        'LBL_GDPR_CONFIRM_LINK' => 'GDPR Update - Confirm link',
        'LBL_GDPR_SUPPORT_REQUEST_SENDER' => 'GDPR Support Request - Sender',
        'LBL_GDPR_SUPPORT_REQUEST_SUBJECT' => 'GDPR Support Request - Subject',
        'LBL_GDPR_SUPPORT_REQUEST_DESC' => 'GDPR Support Request - Description',
    ),
);

$translations['APP_STRINGS'] = array(
    'it_it' => array(
        'LBL_GDPR_ANONYMIZE' => 'Anonimizza',
    ),
    'en_us' => array(
        'LBL_GDPR_ANONYMIZE' => 'Anonymize',				
    ),
);

$translations['Settings'] = array(
    'it_it' => array(
        'GDPR' => 'GDPR',
        'LBL_GDPR' => 'GDPR',
        'LBL_GDPR_DESCRIPTION' => 'Configura le impostazioni del GDPR',
        'LBL_WEBSERVICE' => 'Webservice',
        'LBL_WEBSERVICE_ENDPOINT' => 'Webservice endpoint',
        'LBL_WEBSERVICE_USERNAME' => 'Webservice username',
        'LBL_WEBSERVICE_ACCESSKEY' => 'Webservice accesskey',
        'LBL_DEFAULT_LANGUAGE' => 'Lingua di default',
        'LBL_WEBSITE_LOGO' => 'Logo di default',
        'LBL_SENDER_NAME' => 'Nome mittente',
        'LBL_SENDER_EMAIL' => 'Email mittente',
        'LBL_TEMPLATES' => 'Template',
        'LBL_PRIVACY_POLICY' => 'Informativa Privacy',
        'LBL_WEBSERVICE_ENDPOINT_DESC' => 'L\'URL dove &egrave; installato il CRM',
        'LBL_WEBSERVICE_USERNAME_DESC' => 'Utente utilizzato per le chiamate Webservice',
        'LBL_WEBSERVICE_ACCESSKEY_DESC' => 'Accesskey dell\'utente utilizzato per le chiamate Webservice',
        'LBL_WEBSITE_LOGO_DESC' => 'Il logo di default utilizzato nell\'app',
        'LBL_SENDER_NAME_DESC' => 'Il nome del mittente utilizzato per le comunicazioni GDPR',
        'LBL_SENDER_EMAIL_DESC' => 'L\'email del mittente utilizzata per le comunicazioni GDPR',
        'LBL_ENGLISH_LANG' => 'EN English',
        'LBL_ITALIAN_LANG' => 'IT Italiano',
        'LBL_DEFAULT_LANGUAGE_DESC' => 'La lingua di default utilizzata nell\'app',
        'LBL_SUPPORT_REQUEST_TEMPLATE' => 'Template richiesta supporto',
        'LBL_SUPPORT_REQUEST_TEMPLATE_DESC' => 'Template utilizzato per le richieste di supporto',
        'LBL_ACCESS_TEMPLATE' => 'Template accesso',
        'LBL_ACCESS_TEMPLATE_DESC' => 'Template utilizzato per l\'invio dell\'accesso al contatto',
        'LBL_CONFIRM_UPDATE_TEMPLATE' => 'Template di richiesta conferma',
        'LBL_CONFIRM_UPDATE_TEMPLATE_DESC' => 'Template utilizzato per la conferma dell\'aggiornamento del contatto',
        'LBL_CONTACT_UPDATED_TEMPLATE' => 'Template modifiche contatto',
        'LBL_CONTACT_UPDATED_TEMPLATE_DESC' => 'Template utilizzato per inviare le notifiche di cambio dati al contatto',
        'LBL_GDPR_VERIFY_LINK' => 'GDPR Accesso - Link di verifica',
        'LBL_GDPR_ACCESS_LINK' => 'GDPR Accesso - Link di accesso',
        'LBL_GDPR_CONFIRM_LINK' => 'GDPR Aggiornamento - Link di conferma',
        'LBL_GDPR_SUPPORT_REQUEST_SENDER' => 'GDPR Richiesta supporto - Mittente',
        'LBL_GDPR_SUPPORT_REQUEST_SUBJECT' => 'GDPR Richiesta supporto - Oggetto',
        'LBL_GDPR_SUPPORT_REQUEST_DESC' => 'GDPR Richiesta supporto - Descrizione',
        'CompanyDetails' => 'Dettagli societa`',
        'LBL_ANONYMOUS' => 'Anonymous',
        'LBL_GDPR_NOTIFY_ANONYMIZE_SUBJECT'=>'Anonimizzazione contatto',
        'LBL_GDPR_NOTIFY_ANONYMIZE_BODY'=>'E\' stata effettuata l\'anonimizzazione di %s, %s, %s.<br>Entro il %s devi assicurarti che vengano eliminati i suoi dati anche da eventuali supporti cartacei o esterni.<br>Ricordati di cancellare anche questa email!',
        'LBL_NOCONFIRM_DELETION_MOTHS' => 'Mesi attesa conferma',
        'LBL_NOCONFIRM_DELETION_MOTHS_DESC' => 'Il numero di mesi dopo il quale il contatto verra` anonimizzato',
    ),
    'en_us' => array(
        'GDPR' => 'GDPR',
        'LBL_GDPR' => 'GDPR',
        'LBL_GDPR_DESCRIPTION' => 'Configure the GDPR settings',
        'LBL_WEBSERVICE' => 'Webservice',
        'LBL_WEBSERVICE_ENDPOINT' => 'Webservice endpoint',
        'LBL_WEBSERVICE_USERNAME' => 'Webservice username',
        'LBL_WEBSERVICE_ACCESSKEY' => 'Webservice access key',
        'LBL_DEFAULT_LANGUAGE' => 'Default language',
        'LBL_WEBSITE_LOGO' => 'Default logo',
        'LBL_SENDER_NAME' => 'Sender name',
        'LBL_SENDER_EMAIL' => 'Sender email',
        'LBL_TEMPLATES' => 'Template',
        'LBL_PRIVACY_POLICY' => 'Privacy Policy',
        'LBL_WEBSERVICE_ENDPOINT_DESC' => 'The URL where CRM is installed',
        'LBL_WEBSERVICE_USERNAME_DESC' => 'User employed for the Webservice calls',
        'LBL_WEBSERVICE_ACCESSKEY_DESC' => 'User Access key used for the Webservice calls',
        'LBL_WEBSITE_LOGO_DESC' => 'The default logo used in the app',
        'LBL_SENDER_NAME_DESC' => 'The sender name used for GDPR communication',
        'LBL_SENDER_EMAIL_DESC' => 'The sender email used for GDPR communication',
        'LBL_ENGLISH_LANG' => 'EN English',
        'LBL_ITALIAN_LANG' => 'IT Italiano',
        'LBL_DEFAULT_LANGUAGE_DESC' => 'The default language used in the app',
        'LBL_SUPPORT_REQUEST_TEMPLATE' => 'Support request Template',
        'LBL_SUPPORT_REQUEST_TEMPLATE_DESC' => 'Template used for support request',
        'LBL_ACCESS_TEMPLATE' => 'Access Template',
        'LBL_ACCESS_TEMPLATE_DESC' => 'Template used for sending the access details to the contact',
        'LBL_CONFIRM_UPDATE_TEMPLATE' => 'Confirm update template',
        'LBL_CONFIRM_UPDATE_TEMPLATE_DESC' => 'Template used for confirming the contact update',
        'LBL_CONTACT_UPDATED_TEMPLATE' => 'Contact updated template',
        'LBL_CONTACT_UPDATED_TEMPLATE_DESC' => 'Template used for sending update notifications to the contact',
        'LBL_GDPR_VERIFY_LINK' => 'GDPR Access - Verify link',
        'LBL_GDPR_ACCESS_LINK' => 'GDPR Access - Access link',
        'LBL_GDPR_CONFIRM_LINK' => 'GDPR Update - Confirm link',
        'LBL_GDPR_SUPPORT_REQUEST_SENDER' => 'GDPR Support Request - Sender',
        'LBL_GDPR_SUPPORT_REQUEST_SUBJECT' => 'GDPR Support Request - Subject',
        'LBL_GDPR_SUPPORT_REQUEST_DESC' => 'GDPR Support Request - Description',
        'CompanyDetails' => 'Company details',
        'LBL_ANONYMOUS' => 'Anonymous',
        'LBL_GDPR_NOTIFY_ANONYMIZE_SUBJECT'=>'Contact anonymization',
        'LBL_GDPR_NOTIFY_ANONYMIZE_BODY'=>'Has been made the anonymization of %s, %s and %s was made.<br>Within the %s you must ensure that your data is also deleted from any paper or external media.<br>Remember to also delete this email!',
        'LBL_NOCONFIRM_DELETION_MOTHS' => 'Number of waiting months for confirm',
        'LBL_NOCONFIRM_DELETION_MOTHS_DESC' => 'The number of months after which the contact will be anonymised',
    ),
);

$languages = vtlib_getToggleLanguageInfo();
foreach ($translations as $module => $modlang) {
    foreach ($modlang as $lang => $translist) {
        if (array_key_exists($lang, $languages)) {
            foreach ($translist as $label => $translabel) {
                SDK::setLanguageEntry($module, $lang, $label, $translabel);
            }
        }
    }
}*/