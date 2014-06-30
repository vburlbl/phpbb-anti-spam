<?php 
/** 
*
* @package Advanced Block Mod
* @version $Id: abm.php, english, v 1.000 2012/05/05 Martin Truckenbrodt Exp$
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
	'ABM_COUNT'							=> 'Counter',
	'ABM_WEIGHT'						=> 'Weight',
	'ABM_DEFAULT'						=> 'Default',

	'DOMAINRBL'							=> 'Domain-RBL DNS Blacklist',
	'DOMAINRBL_ADDED_EDITED'			=> 'Domain-RBL DNS Blacklist added or edited successfully',
	'DOMAINRBL_ADMIN'					=> 'Domain-RBL DNS Blacklist administration',
	'DOMAINRBL_ADMIN_EXPLAIN'			=> 'There are no categories. You see only one list of all the Domain-RBL DNS Blacklists. The Domain-RBL DNS Blacklists will be used related to the order of the list.',
	'DOMAINRBL_COUNT'					=> 'Domain-RBL DNS Blacklist counter',
	'DOMAINRBL_COUNT_EXPLAIN'			=> 'The number of spammers recognized by this Domain-RBL DNS Blacklist.',
	'DOMAINRBL_CREATE'					=> 'Create Domain-RBL DNS Blacklist',
	'DOMAINRBL_DELETE'					=> 'Delete Domain-RBL DNS Blacklist',
	'DOMAINRBL_DELETE_EXPLAIN'			=> 'The form below will allow you to delete a Domain-RBL DNS Blacklist.',
	'DOMAINRBL_DELETED'					=> 'Domain-RBL DNS Blacklist deleted successfully',
	'DOMAINRBL_DNS_A_RECORD'			=> 'There is a DNS A record for the FQDN?',
	'DOMAINRBL_DNS_A_RECORD_EXPLAIN'	=> 'If there is no DNS A RECORD then the FQDN can be wrong. But a lot of Domain-RBL DNS Blacklists do not have a DNS A record.',
	'DOMAINRBL_EDIT'					=> 'Edit Domain-RBL DNS Blacklist',
	'DOMAINRBL_EDIT_EXPLAIN'			=> 'The form below will allow you to edit this Domain-RBL DNS Blacklist.',
	'DOMAINRBL_FQDN'					=> 'Domain-RBL DNS Blacklist FQDN',
	'DOMAINRBL_FQDN_EXPLAIN'			=> 'The Fully Qualified Domain Name for the Domain-RBL DNS Blacklist.',
	'DOMAINRBL_FQDN_NOT_VALID'			=> 'The entered FQDN is not valid.',
	'DOMAINRBL_LOOKUP'					=> 'Domain-RBL DNS Blacklist loopkup URL',
	'DOMAINRBL_LOOKUP_EXPLAIN'			=> 'You can use this link to see details about the Domain-RBL DNS Blacklist and can get information about the reasons for the blocking. The IP address will be added automatically by the Block log. You have to add http:// in front of the entry.',
	'DOMAINRBL_LOOK_UP'					=> 'Select a Domain-RBL DNS Blacklist',
	'DOMAINRBL_LOOK_UP_EXPLAIN'			=> 'You are <strong>not</strong> able to select more than one Domain-RBL DNS Blacklist.',
	'DOMAINRBL_RESET'					=> 'Reset Domain-RBL DNS Blacklist counter',
	'DOMAINRBL_RESET_EXPLAIN'			=> 'If you are reseting the counter for this Domain-RBL DNS Blacklist then at least other Domain-RBL DNS Blacklists with the same weight value will be preferred.',
	'DOMAINRBL_SETTINGS'				=> 'Domain-RBL DNS Blacklist settings',
	'DOMAINRBL_WEIGHT'					=> 'Domain-RBL DNS Blacklist weight',
	'DOMAINRBL_WEIGHT_EXPLAIN'			=> 'Domain will be blocked then a threshold of weight value of 5 is reached. For single Domain-RBL DNS Blacklists you can set lower values if you are not sure if it is a good idea to use this list or e.g. if free e-mail providers are listed on this blacklist. So the domain have to be listed at several DOMAIN-RBLs to be blocked. 0 disables the Domain-RBL DNS Blacklist.',

	'HTTPBL'							=> 'HTTP Blacklist',
	'HTTPBL_ACTIVE_FOR_REPORT'			=> 'Use HTTP Blacklist for reporting',
	'HTTPBL_ACTIVE_FOR_REPORT_EXPLAIN'	=> 'If enabled, spam will be reported to this HTTP Blacklist.',
	'HTTPBL_ACTIVE_TO_REPORT'			=> 'Use HTTP Blacklist to report spam',
	'HTTPBL_ACTIVE_TO_REPORT_EXPLAIN'	=> 'If enabled, spam blocked by this HTTP Blacklist will be reported to other HTTP Blacklists.',
	'HTTPBL_ADMIN'						=> 'HTTP Blacklist administration',
	'HTTPBL_ADMIN_EXPLAIN'				=> 'There are no categories. You see only one list of all the HTTP Blacklists.',
	'HTTPBL_COUNT'						=> 'HTTP Blacklist counter',
	'HTTPBL_CHECK_EMAIL'				=> 'Check for the e-mail address',
	'HTTPBL_CHECK_EMAIL_EXPLAIN'		=> 'If enbaled, e-mail addresses will been check against this blacklist.',
	'HTTPBL_COUNT_EXPLAIN'				=> 'The number of spammers recognized by this HTTP Blacklist.',
	'HTTPBL_CHECK_IP'					=> 'Check for the ip address',
	'HTTPBL_CHECK_IP_EXPLAIN'			=> 'If enbaled, ip addresses will been check against this blacklist.',
	'HTTPBL_CHECK_MESSAGE'				=> 'Check for the message',
	'HTTPBL_CHECK_MESSAGE_EXPLAIN'		=> 'If enbaled, messages will been check against this blacklist.',
	'HTTPBL_CHECK_USERNAME'				=> 'Check for the username',
	'HTTPBL_CHECK_USERNAME_EXPLAIN'		=> 'If enbaled, usernames will been check against this blacklist.',
	'HTTPBL_DETAILS'					=> 'HTTP Blacklist details',
	'HTTPBL_DETAILS_AKISMET'			=> 'Akismet is free for non-business personal pages. A key is required for checking and reporting. Akismet is analysing the message, too. There is just a true/false response for the whole requests. You can report or submit false positives - called ham - on the Akismet website.',
	'HTTPBL_DETAILS_BDE'				=> 'The Block Disposable Email Addresses blacklist has a free service with 200 request per month and a non-free service. The key is required for checking. There is no reporting. The blacklist is blocking disposable e-mail addresses (DEAs) of disposable email address providers based on the e-mail domain name. So you can use the service not only to prevent spam. Also you can force legimate users to use real and well working e-mail addresses. You can not use this blacklist to report spam to other blacklists cause false positives are possible for ip address and username.',
	'HTTPBL_DETAILS_BOTSCOUT'			=> 'You can use BotScout free without a key for 20 requests per day. With a key you can use a free account for 300 requests per day. There is no automatically reporting.',
	'HTTPBL_DETAILS_EXPLAIN'			=> 'Details for this HTTP Blacklist.',
	'HTTPBL_DETAILS_HONEYPOT'			=> 'Project Honeypot aka httb:BL is free. A key is required for checking. This blacklist is used like an IP-RBL DNS Blacklist, but in a special way. There is no automatically reporting.',
	'HTTPBL_DETAILS_SFS'				=> 'The Stop Forum Spam blacklist is completely free to use. The key is required for reporting only. Automatically reporting is possible but not allowed. So if you want to enable it please be sure that you do not have any false positives. If you have false positives reported than you can remove them on the SFS website under <em>My Spammers</em> after login. For reporting ip address, username and e-mail address are required.',
	'HTTPBL_EDIT'						=> 'Edit HTTP Blacklist',
	'HTTPBL_EDIT_EXPLAIN'				=> 'The form below will allow you to edit this HTTP Blacklist.',
	'HTTPBL_EDITED'						=> 'HTTP Blacklist edited successfully',
	'HTTPBL_FULLNAME'					=> 'HTTP Blacklist name',
	'HTTPBL_FULLNAME_EXPLAIN'			=> 'The common full name for the HTTP Blacklist.',
	'HTTPBL_KEY'						=> 'HTTP Blacklist key',
	'HTTPBL_KEY_EXPLAIN'				=> 'Some HTTP Blacklists are requiring a key and a user registration. For more information look at HTTP Blacklist details.',
	'HTTPBL_LOOKUP'						=> 'HTTP Blacklist loopkup URL',
	'HTTPBL_LOOKUP_EXPLAIN'				=> 'This URL is used for the HTTP Blacklist check and for the Blacklist lookup entries at the Block log.',
	'HTTPBL_LOOK_UP'					=> 'Select a HTTP Blacklist',
	'HTTPBL_LOOK_UP_EXPLAIN'			=> 'You are <strong>not</strong> able to select more than one HTTP Blacklist.',
	'HTTPBL_RESET'						=> 'Reset HTTP Blacklist counter',
	'HTTPBL_RESET_EXPLAIN'				=> 'If you are reseting the counter for this HTTP Blacklist then at least other HTTP Blacklists with the same weight value will be preferred.',
	'HTTPBL_SETTINGS'					=> 'HTTP Blacklist settings',
	'HTTPBL_WEBSITE'					=> 'HTTP Blacklist website',
	'HTTPBL_WEBSITE_EXPLAIN'			=> 'The website of the HTTP Blacklist.',
	'HTTPBL_WEIGHT'						=> 'HTTP Blacklist weight',
	'HTTPBL_WEIGHT_EXPLAIN'				=> 'Spammer will be blocked then a threshold of weight value of 5 is reached. For single HTTP Blacklists you can set lower values if you are not sure if it is a good idea to use this listSo the spammer have to be listed at several HTTPBLs to be blocked. 0 disables the HTTP Blacklist.',

	'IPRBL'								=> 'IP-RBL DNS Blacklist',
	'IPRBL_ADDED_EDITED'				=> 'IP-RBL DNS Blacklist added or edited successfully',
	'IPRBL_ADMIN'						=> 'IP-RBL DNS Blacklist administration',
	'IPRBL_ADMIN_EXPLAIN'				=> 'There are no categories. You see only one list of all the IP-RBL DNS Blacklists. The IP-RBL DNS Blacklists will be used related to the order of the list.',
	'IPRBL_COUNT'						=> 'IP-RBL DNS Blacklist counter',
	'IPRBL_COUNT_EXPLAIN'				=> 'The number of spammers recognized by this IP-RBL DNS Blacklist.',
	'IPRBL_CREATE'						=> 'Create IP-RBL DNS Blacklist',
	'IPRBL_DELETE'						=> 'Delete IP-RBL DNS Blacklist',
	'IPRBL_DELETE_EXPLAIN'				=> 'The form below will allow you to delete a IP-RBL DNS Blacklist.',
	'IPRBL_DELETED'						=> 'IP-RBL DNS Blacklist deleted successfully',
	'IPRBL_DNS_A_RECORD'				=> 'There is a DNS A record for the FQDN?',
	'IPRBL_DNS_A_RECORD_EXPLAIN'		=> 'If there is no DNS A RECORD then the FQDN can be wrong. But a lot of IP-RBL DNS Blacklists do not have a DNS A record.',
	'IPRBL_EDIT'						=> 'Edit IP-RBL DNS Blacklist',
	'IPRBL_EDIT_EXPLAIN'				=> 'The form below will allow you to edit this IP-RBL DNS Blacklist.',
	'IPRBL_FQDN'						=> 'IP-RBL DNS Blacklist FQDN',
	'IPRBL_FQDN_EXPLAIN'				=> 'The Fully Qualified Domain Name for the IP-RBL DNS Blacklist.',
	'IPRBL_FQDN_NOT_VALID'				=> 'The entered FQDN is not valid.',
	'IPRBL_LOOKUP'						=> 'IP-RBL DNS Blacklist loopkup URL',
	'IPRBL_LOOKUP_EXPLAIN'				=> 'You can use this link to see details about the IP-RBL DNS Blacklist and can get information about the reasons for the blocking. The IP address will be added automatically by the Block log. You have to add http:// in front of the entry.',
	'IPRBL_LOOK_UP'						=> 'Select a IP-RBL DNS Blacklist',
	'IPRBL_LOOK_UP_EXPLAIN'				=> 'You are <strong>not</strong> able to select more than one IP-RBL DNS Blacklist.',
	'IPRBL_RESET'						=> 'Reset IP-RBL DNS Blacklist counter',
	'IPRBL_RESET_EXPLAIN'				=> 'If you are reseting the counter for this IP-RBL DNS Blacklist then at least other IP-RBL DNS Blacklists with the same weight value will be preferred.',
	'IPRBL_SETTINGS'					=> 'IP-RBL DNS Blacklist settings',
	'IPRBL_WEIGHT'						=> 'IP-RBL DNS Blacklist weight',
	'IPRBL_WEIGHT_EXPLAIN'				=> 'IP adresses will be blocked then a threshold of weight value of 5 is reached. For single IP-RBL DNS Blacklists you can set lower values if you are not sure if it is a good idea to use this list or e.g. if the IPRBL lists not single IP addresses either whole address ranges. So the IP address have to be listed at several IPRBLs to be blocked. 0 disables the IP-RBL DNS Blacklist.',

	'NO_DOMAINRBL'						=> 'No Domain-RBL DNS Blacklist id found. Please contact an administrator.',
	'NO_DOMAINRBL_SELECTED'				=> 'No Domain-RBL DNS Blacklist selected!',
	'NO_DOMAINRBLS'						=> 'There are no Domain-RBL DNS Blacklists.',
	'NO_HTTPBL'							=> 'No HTTP Blacklist id found. Please contact an administrator.',
	'NO_HTTPBL_SELECTED'				=> 'No HTTP Blacklist selected!',
	'NO_HTTPBLS'						=> 'There are no HTTP Blacklists.',
	'NO_IPRBL'							=> 'No IP-RBL DNS Blacklist id found. Please contact an administrator.',
	'NO_IPRBL_SELECTED'					=> 'No IP-RBL DNS Blacklist selected!',
	'NO_IPRBLS'							=> 'There are no IP-RBL DNS Blacklists.',

	'VIEW_DOMAINRBL'					=> '1 Domain-RBL DNS Blacklist',
	'VIEW_DOMAINRBLS'					=> '%d Domain-RBL DNS Blacklists',
	'VIEW_HTTPBL'						=> '1 HTTP Blacklist',
	'VIEW_HTTPBLS'						=> '%d HTTP Blacklists',
	'VIEW_IPRBL'						=> '1 IP-RBL DNS Blacklist',
	'VIEW_IPRBLS'						=> '%d IP-RBL DNS Blacklists',
));

?>