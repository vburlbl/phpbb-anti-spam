<?php 
/** 
*
* @package Advanced Block Mod
* @version $Id: abm.php, german, v 1.000 2012/05/05 Martin Truckenbrodt Exp$
* @copyright (c) 2009, 2012 Martin Truckenbrodt 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License 
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

$lang = array_merge($lang, array(
	'ABM_COUNT'							=> 'Zähler',
	'ABM_WEIGHT'						=> 'Gewichtung',
	'ABM_DEFAULT'						=> 'Standard',

	'DOMAINRBL'							=> 'Domain-RBL-DNS-Blacklist',
	'DOMAINRBL_ADDED_EDITED'			=> 'Domain-RBL-DNS-Blacklist erfolgreich hinzugefügt oder geändert',
	'DOMAINRBL_ADMIN'					=> 'Domain-RBL-DNS-Blacklist-Verwaltung',
	'DOMAINRBL_ADMIN_EXPLAIN'			=> 'Es gibt keine Kategorien. Du siehst nur eine Liste aller Domain-RBL-DNS-Blacklist-Einträge. Die Domain-RBL-DNS-Blacklist-Einträge werden in der Reihenfolge der Einträge hier verarbeitet.',
	'DOMAINRBL_COUNT'					=> 'Domain-RBL-DNS-Blacklist-Zähler',
	'DOMAINRBL_COUNT_EXPLAIN'			=> 'Die Anzahl der Spammer, die durch diese Domain-RBL-DNS-Blacklist festgestellt wurden.',
	'DOMAINRBL_CREATE'					=> 'Erstelle Domain-RBL-DNS-Blacklist',
	'DOMAINRBL_DELETE'					=> 'Lösche Domain-RBL-DNS-Blacklist',
	'DOMAINRBL_DELETE_EXPLAIN'			=> 'Das nachfolgende Formular erlaubt es Dir einen Domain-RBL-DNS-Blacklist-Eintrag zu löschen.',
	'DOMAINRBL_DELETED'					=> 'Domain-RBL-DNS-Blacklist-Eintrag erfolgreich gelöscht',
	'DOMAINRBL_DNS_A_RECORD'			=> 'Gibt es einen DNS-A-Eintrag für den FQDN?',
	'DOMAINRBL_DNS_A_RECORD_EXPLAIN'	=> 'Falls keinen DNS-A-Eintrag gibt, kann der FQDN falsch sein. Jedoch besitzen viele Domain-RBL-DNS-Blacklisten keinen DNS-A-Eintrag.',
	'DOMAINRBL_EDIT'					=> 'Ändere Domain-RBL-DNS-Blacklist',
	'DOMAINRBL_EDIT_EXPLAIN'			=> 'Das nachfolgende Formular erlaubt es Dir den Domain-RBL-DNS-Blacklist-Eintrag zu bearbeiten.',
	'DOMAINRBL_FQDN'					=> 'Domain-RBL-DNS-Blacklist-FQDN',
	'DOMAINRBL_FQDN_EXPLAIN'			=> 'Der Fully Qualified Domain Name für die Domain-RBL-DNS-Blacklist.',
	'DOMAINRBL_FQDN_NOT_VALID'			=> 'Der eingegebene FQDN ist nicht gültig.',
	'DOMAINRBL_LOOKUP'					=> 'URL für die Domain-RBL-DNS-Blacklist-Überprüfung ',
	'DOMAINRBL_LOOKUP_EXPLAIN'			=> 'Du kannst den Link benutzen um Details zum Domain-RBL-DNS-Blacklist-Eintrag und Informationen zum Grund des Eintrags zu erfahren. Die IP-Addresse wird automatisch hinzugefügt. Du mußt dem Eintrag http:// voran stellen.',
	'DOMAINRBL_LOOK_UP'					=> 'Wähle einen Domain-RBL-DNS-Blacklist-Eintrag aus',
	'DOMAINRBL_LOOK_UP_EXPLAIN'			=> 'Es ist <strong>nicht</strong> möglich mehr als einen Domain-RBL-DNS-Blacklist-Eintrag auszuwählen.',
	'DOMAINRBL_RESET'					=> 'Domain-RBL-DNS-Blacklist-Zähler zurücksetzen',
	'DOMAINRBL_RESET_EXPLAIN'			=> 'Durch das Zurücksetzen des Zählers wir u.a. erreicht, dass andere Domain-RBL-DNS-Blacklisten mit gleicher Gewichtung gegenüber dieser Blacklist bevorzugt benutzt werden.',
	'DOMAINRBL_SETTINGS'				=> 'Domain-RBL-DNS-Blacklist-Einstellungen',
	'DOMAINRBL_WEIGHT'					=> 'Domain-RBL-DNS-Blacklist-Gewichtung',
	'DOMAINRBL_WEIGHT_EXPLAIN'			=> 'Domänen werden automatisch geblockt, wenn in der Summe der Gewichtungen der Schwellwert von 5 überschritten wird. Für einzlene Einträge kann man einen niedriegen Wert auswählen, wenn man sich mit diesem Eintrag nicht sicher ist oder z.B. wenn die Domain-RBL-DNS-Blacklist kostenfrei E-Mail-Provider listet. So kann erreicht werden, dass die Domäne erst dann geblockt wird, wenn mehrere Domain-RBL-DNS-Blacklist-Einträge zusammen anschlagen. 0 deaktiviert den Domain-RBL-DNS-Blacklist-Eintrag.',

	'HTTPBL'							=> 'HTTP-Blacklist',
	'HTTPBL_ACTIVE_FOR_REPORT'			=> 'Nutze HTTP-Blacklist für Meldungen',
	'HTTPBL_ACTIVE_FOR_REPORT_EXPLAIN'	=> 'Wenn aktiviert, wird Spam an diese HTTP-Blacklist gemeldet.',
	'HTTPBL_ACTIVE_TO_REPORT'			=> 'Nutze HTTP-Blacklist um Spam zu melden',
	'HTTPBL_ACTIVE_TO_REPORT_EXPLAIN'	=> 'Wenn aktiviert, wird durch diese HTTP-Blacklist gefundener Spam an andere HTTP-Blacklisten gemeldet.',
	'HTTPBL_ADMIN'						=> 'HTTP-Blacklist-Verwaltung',
	'HTTPBL_ADMIN_EXPLAIN'				=> 'Es gibt keine Kategorien. Du siehst nur eine Liste aller HTTP-Blacklist-Einträge. Die HTTP-Blacklist-Einträge werden in der Reihenfolge der Einträge hier verarbeitet.',
	'HTTPBL_COUNT'						=> 'HTTP-Blacklist-Zähler',
	'HTTPBL_CHECK_EMAIL'				=> 'Prüfe E-Mail-Adresse',
	'HTTPBL_CHECK_EMAIL_EXPLAIN'		=> 'Wenn aktiviert, werden E-Mail-Adressen gegen diese Blacklist geprüft.',
	'HTTPBL_COUNT_EXPLAIN'				=> 'Die Anzahl an mit dieser HTTP-Blacklist gefundenem Spam.',
	'HTTPBL_CHECK_IP'					=> 'Prüfe IP-Adresse',
	'HTTPBL_CHECK_IP_EXPLAIN'			=> 'Wenn aktiviert, werden IP-Adressen gegen diese Blacklist geprüft.',
	'HTTPBL_CHECK_MESSAGE'				=> 'Prüfe Nachricht',
	'HTTPBL_CHECK_MESSAGE_EXPLAIN'		=> 'Wenn aktiviert, werden Nachrichten gegen diese Blacklist geprüft.',
	'HTTPBL_CHECK_USERNAME'				=> 'Prüfe Benutzername',
	'HTTPBL_CHECK_USERNAME_EXPLAIN'		=> 'Wenn aktiviert, werden Benutzernamen gegen diese Blacklist geprüft.',
	'HTTPBL_DETAILS'					=> 'HTTP-Blacklist-Details',
	'HTTPBL_DETAILS_AKISMET'			=> 'Akismet ist für nicht-kommerzielle Webseiten kostenfrei. Ein Schlüssel wird für das Prüfen und das Melden benötigt. Akismet prüft auch die Nachricht. Es gibt nur eine Wahr/Falsch-Anwort für die gesamte Anfrage. Auf der Akismet-Webseite kannst Du False-Postives, genannt ham, manuell eintragen.',
	'HTTPBL_DETAILS_BDE'				=> 'Die Block Disposable Email Addresses-Blacklist bietet, neben einem kostenpflichtigen Dienst, auch einen kostenfreien Dienst mit monatlich 200 Anfragen. Der Schlüssel wird das Prüfen benötigt. Es gibt keine Möglichkeit für automatische Meldungen. Die Blacklist blockt anhand der E-Mail-Domäne sogenannte Ein-Weg-E-Mail-Adressen (DEAs) entsprechender Provider. So kann mit diesem Dienst nicht nur Spam blockiert werden. Ebenso kannst Du damit Benutzer zwingen relle und funktionierende E-Mail-Adressen zu verwenden. Du kannst diese Blacklist nicht verwenden um Spam an andere Blacklisten verwenden, da False-Postives für IP-Adresse und Benutzername möglich wären.',
	'HTTPBL_DETAILS_BOTSCOUT'			=> 'Du kannst BotScout ohne Schlüssel kostenfrei für 20 Anfragen am Tag verwenden. Mit einem Schlüssel sind 300 Anfragen am Tag kostenfrei. Es gibt keine Möglichkeit für automatische Meldungen.',
	'HTTPBL_DETAILS_EXPLAIN'			=> 'Details für diese HTTP-Blacklist.',
	'HTTPBL_DETAILS_HONEYPOT'			=> 'Project Honeypot aka httb:BL ist kostenfrei. Ein Schlüssel wird für das Prüfen benötigt. Diese Blacklist wird, allerdings in untypischer Weise, wie eine IP-RBL-DNS-Blacklist benutzt. Es gibt keine Möglichkeit für automatische Meldungen.',
	'HTTPBL_DETAILS_SFS'				=> 'Die Stop Forum Spam-Blacklist ist kostenfrei. Ein Schlüssel wird nur für das Melden benötigt. Automatische Meldungen sind möglich aber nicht erlaubt. Falls Du es aktivieren willst, sei also bitte sicher, dass es keine False-Positives gibt. Hast Du False-Positives gemeldet, kann Du diese auf der SFS-Webseite unter <em>Meine Spammer</em> nach vorheriger Anmeldung selbst entfernen. Für Meldungen werden immer IP-Adresse, Benutzername und E-Mail-Adresse benötigt.',
	'HTTPBL_EDIT'						=> 'Ändere HTTP-Blacklist',
	'HTTPBL_EDIT_EXPLAIN'				=> 'Das nachfolgende Formular erlaubt es Dir den HTTP-Blacklist-Eintrag zu bearbeiten.',
	'HTTPBL_EDITED'						=> 'HTTP-Blacklist erfolgreich geändert',
	'HTTPBL_FULLNAME'					=> 'HTTP-Blacklist-Name',
	'HTTPBL_FULLNAME_EXPLAIN'			=> 'Der allgemein gebräuchliche Name der HTTP-Blacklist.',
	'HTTPBL_KEY'						=> 'HTTP-Blacklist-Schlüssel',
	'HTTPBL_KEY_EXPLAIN'				=> 'Einige HTTP-Blacklisten verlangen einen Schlüssel und eine Benutzerregistrierung. Mehr Informationen dazu findest Du unter HTTP-Blacklist-Details.',
	'HTTPBL_LOOKUP'						=> 'URL für die HTTP-Blacklist-Überprüfung',
	'HTTPBL_LOOKUP_EXPLAIN'				=> 'Die URL wird benutzt um Details zum HTTP-Blacklist-Eintrag und Informationen zum Grund des Eintrags zu erfahren.',
	'HTTPBL_LOOK_UP'					=> 'Wähle einen HTTP-Blacklist-Eintrag aus',
	'HTTPBL_LOOK_UP_EXPLAIN'			=> 'Es ist <strong>nicht</strong> möglich mehr als einen HTTP-Blacklist-Eintrag auszuwählen.',
	'HTTPBL_RESET'						=> 'HTTP-Blacklist-Zähler zurücksetzen',
	'HTTPBL_RESET_EXPLAIN'				=> 'Durch das Zurücksetzen des Zählers wir u.a. erreicht, dass andere HTTP-Blacklisten mit gleicher Gewichtung gegenüber dieser Blacklist bevorzugt benutzt werden.',
	'HTTPBL_SETTINGS'					=> 'HTTP-Blacklist-Einstellungen',
	'HTTPBL_WEBSITE'					=> 'HTTP-Blacklist-Webseite',
	'HTTPBL_WEBSITE_EXPLAIN'			=> 'Die Webseite zur HTTP-Blacklist.',
	'HTTPBL_WEIGHT'						=> 'HTTP-Blacklist-Gewichtung',
	'HTTPBL_WEIGHT_EXPLAIN'				=> 'Spam wird automatisch geblockt, wenn in der Summe der Gewichtungen der Schwellwert von 5 überschritten wird. Für einzlene Einträge kann man einen niedriegen Wert auswählen, wenn man sich mit diesem Eintrag nicht sicher ist oder z.B. wenn die HTTP-Blacklist nicht einzelne IP-Adressen sondern ganze IP-Adressbereiche listet. So kann erreicht werden, dass die IP-Adresse erst dann geblockt wird, wenn mehrere HTTP-Blacklist-Einträge zusammen anschlagen. 0 deaktiviert den HTTP-Blacklist-Eintrag.',

	'IPRBL'								=> 'IP-RBL-DNS-Blacklist',
	'IPRBL_ADDED_EDITED'				=> 'IP-RBL-DNS-Blacklist erfolgreich hinzugefügt oder geändert',
	'IPRBL_ADMIN'						=> 'IP-RBL-DNS-Blacklist-Verwaltung',
	'IPRBL_ADMIN_EXPLAIN'				=> 'Es gibt keine Kategorien. Du siehst nur eine Liste aller IP-RBL-DNS-Blacklist-Einträge. Die IP-RBL-DNS-Blacklist-Einträge werden in der Reihenfolge der Einträge hier verarbeitet.',
	'IPRBL_COUNT'						=> 'IP-RBL-DNS-Blacklist-Zähler',
	'IPRBL_COUNT_EXPLAIN'				=> 'Die Anzahl der Spammer, die durch diese IP-RBL-DNS-Blacklist festgestellt wurden.',
	'IPRBL_CREATE'						=> 'Erstelle IP-RBL-DNS-Blacklist',
	'IPRBL_DELETE'						=> 'Lösche IP-RBL-DNS-Blacklist',
	'IPRBL_DELETE_EXPLAIN'				=> 'Das nachfolgende Formular erlaubt es Dir einen IP-RBL-DNS-Blacklist-Eintrag zu löschen.',
	'IPRBL_DELETED'						=> 'IP-RBL-DNS-Blacklist-Eintrag erfolgreich gelöscht',
	'IPRBL_DNS_A_RECORD'				=> 'Gibt es einen DNS-A-Eintrag für den FQDN?',
	'IPRBL_DNS_A_RECORD_EXPLAIN'		=> 'Falls keinen DNS-A-Eintrag gibt, kann der FQDN falsch sein. Jedoch besitzen viele IP-RBL-DNS-Blacklisten keinen DNS-A-Eintrag.',
	'IPRBL_EDIT'						=> 'Ändere IP-RBL-DNS-Blacklist',
	'IPRBL_EDIT_EXPLAIN'				=> 'Das nachfolgende Formular erlaubt es Dir den IP-RBL-DNS-Blacklist-Eintrag zu bearbeiten.',
	'IPRBL_FQDN'						=> 'IP-RBL-DNS-Blacklist-FQDN',
	'IPRBL_FQDN_EXPLAIN'				=> 'Der Fully Qualified Domain Name für die IP-RBL-DNS-Blacklist.',
	'IPRBL_FQDN_NOT_VALID'				=> 'Der eingegebene FQDN ist nicht gültig.',
	'IPRBL_LOOKUP'						=> 'URL für die IP-RBL-DNS-Blacklist-Überprüfung ',
	'IPRBL_LOOKUP_EXPLAIN'				=> 'Du kannst den Link benutzen um Details zum IP-RBL-DNS-Blacklist-Eintrag und Informationen zum Grund des Eintrags zu erfahren. Die IP-Addresse wird automatisch hinzugefügt. Du mußt dem Eintrag http:// voran stellen.',
	'IPRBL_LOOK_UP'						=> 'Wähle einen IP-RBL-DNS-Blacklist-Eintrag aus',
	'IPRBL_LOOK_UP_EXPLAIN'				=> 'Es ist <strong>nicht</strong> möglich mehr als einen IP-RBL-DNS-Blacklist-Eintrag auszuwählen.',
	'IPRBL_RESET'						=> 'IP-RBL-DNS-Blacklist-Zähler zurücksetzen',
	'IPRBL_RESET_EXPLAIN'				=> 'Durch das Zurücksetzen des Zählers wir u.a. erreicht, dass andere IP-RBL-DNS-Blacklisten mit gleicher Gewichtung gegenüber dieser Blacklist bevorzugt benutzt werden.',
	'IPRBL_SETTINGS'					=> 'IP-RBL-DNS-Blacklist-Einstellungen',
	'IPRBL_WEIGHT'						=> 'IP-RBL-DNS-Blacklist-Gewichtung',
	'IPRBL_WEIGHT_EXPLAIN'				=> 'IP-Adressen werden automatisch geblockt, wenn in der Summe der Gewichtungen der Schwellwert von 5 überschritten wird. Für einzlene Einträge kann man einen niedriegen Wert auswählen, wenn man sich mit diesem Eintrag nicht sicher ist oder z.B. wenn die IP-RBL-DNS-Blacklist nicht einzelne IP-Adressen sondern ganze IP-Adressbereiche listet. So kann erreicht werden, dass die IP-Adresse erst dann geblockt wird, wenn mehrere IP-RBL-DNS-Blacklist-Einträge zusammen anschlagen. 0 deaktiviert den IP-RBL-DNS-Blacklist-Eintrag.',

	'NO_DOMAINRBL'						=> 'Kein Domain-RBL-DNS-Blacklist-Eintrag gefunden. Bitte kontaktiere einen Administrator.',
	'NO_DOMAINRBL_SELECTED'				=> 'Kein Domain-RBL-DNS-Blacklist-Eintrag ausgewählt!',
	'NO_DOMAINRBLS'						=> 'Keine Domain-RBL-DNS-Blacklist-Einträge gefunden.',
	'NO_HTTPBL'							=> 'Kein HTTP-Blacklist-Eintrag gefunden. Bitte kontaktiere einen Administrator.',
	'NO_HTTPBL_SELECTED'				=> 'Kein HTTP-Blacklist-Eintrag ausgewählt!',
	'NO_HTTPBLS'						=> 'Keine HTTP-Blacklist-Einträge gefunden.',
	'NO_IPRBL'							=> 'Kein IP-RBL-DNS-Blacklist-Eintrag gefunden. Bitte kontaktiere einen Administrator.',
	'NO_IPRBL_SELECTED'					=> 'Kein IP-RBL-DNS-Blacklist-Eintrag ausgewählt!',
	'NO_IPRBLS'							=> 'Keine IP-RBL-DNS-Blacklist-Einträge gefunden.',

	'VIEW_DOMAINRBL'					=> '1 Domain-RBL-DNS-Blacklist-Eintrag',
	'VIEW_DOMAINRBLS'					=> '%d Domain-RBL-DNS-Blacklist-Einträge',
	'VIEW_HTTPBL'						=> '1 HTTP-Blacklist-Eintrag',
	'VIEW_HTTPBLS'						=> '%d HTTP-Blacklist-Einträge',
	'VIEW_IPRBL'						=> '1 IP-RBL-DNS-Blacklist-Eintrag',
	'VIEW_IPRBLS'						=> '%d IP-RBL-DNS-Blacklist-Einträge',
));

?>