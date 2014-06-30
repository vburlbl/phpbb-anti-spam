<?php

/**
 *
 * @package Advanced Block Mod
 * @version $Id: umil_auto_abm.php, v 1.005 2012/12/24 Martin Truckenbrodt Exp$
 * @copyright (c) 2009, 2012 Martin Truckenbrodt 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License 
 *
 */
/**
 * @ignore
 */
define('UMIL_AUTO', true);
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = substr(strrchr(__FILE__, '.'), 1);
include($phpbb_root_path . 'common.' . $phpEx);
$user->session_begin();
$auth->acl($user->data);
$user->setup();

if (!file_exists($phpbb_root_path . 'umil/umil_auto.' . $phpEx)) {
    trigger_error('Please download the latest UMIL (Unified MOD Install Library) from: <a href="http://www.phpbb.com/mods/umil/">phpBB.com/mods/umil</a>', E_USER_ERROR);
}

$mod_name = 'ADVANCED_BLOCK_MOD';

$version_config_name = 'abm_version';

$language_file = 'mods/umil_auto_abm';

$options = array(
);

$versions = array(
    // Version 1.1.0
    '1.1.0' => array(
        'table_add' => array(
            array(DOMAINRBL_TABLE, array(
                    'COLUMNS' => array(
                        'domainrbl_id' => array('UINT', NULL, 'auto_increment'),
                        'domainrbl_fqdn' => array('VCHAR', ''),
                        'domainrbl_lookup' => array('VCHAR', ''),
                        'domainrbl_weight' => array('TINT:1', '0'),
                        'domainrbl_weight_default' => array('TINT:1', '0'),
                        'domainrbl_count' => array('UINT', '0'),
                    ),
                    'PRIMARY_KEY' => array('domainrbl_id'),
                ),
            ),
            array(HTTPBL_TABLE, array(
                    'COLUMNS' => array(
                        'httpbl_id' => array('UINT', NULL, 'auto_increment'),
                        'httpbl_name' => array('VCHAR', ''),
                        'httpbl_fullname' => array('VCHAR', ''),
                        'httpbl_website' => array('VCHAR', ''),
                        'httpbl_lookup' => array('VCHAR', ''),
                        'httpbl_weight' => array('TINT:1', '0'),
                        'httpbl_weight_default' => array('TINT:1', '0'),
                        'httpbl_count' => array('UINT', '0'),
                        'httpbl_key' => array('VCHAR', ''),
                        'httpbl_key_required' => array('TINT:1', '0'),
                        'httpbl_use_ip' => array('TINT:1', '0'),
                        'httpbl_check_ip' => array('TINT:1', '0'),
                        'httpbl_use_username' => array('TINT:1', '0'),
                        'httpbl_check_username' => array('TINT:1', '0'),
                        'httpbl_use_email' => array('TINT:1', '0'),
                        'httpbl_check_email' => array('TINT:1', '0'),
                        'httpbl_use_message' => array('TINT:1', '0'),
                        'httpbl_check_message' => array('TINT:1', '0'),
                        'httpbl_use_for_report' => array('TINT:1', '0'),
                        'httpbl_active_for_report' => array('TINT:1', '0'),
                        'httpbl_use_to_report' => array('TINT:1', '0'),
                        'httpbl_active_to_report' => array('TINT:1', '0'),
                    ),
                    'PRIMARY_KEY' => array('httpbl_id'),
                ),
            ),
            array(IPRBL_TABLE, array(
                    'COLUMNS' => array(
                        'iprbl_id' => array('UINT', NULL, 'auto_increment'),
                        'iprbl_fqdn' => array('VCHAR', ''),
                        'iprbl_lookup' => array('VCHAR', ''),
                        'iprbl_weight' => array('TINT:1', '0'),
                        'iprbl_weight_default' => array('TINT:1', '0'),
                        'iprbl_count' => array('UINT', '0'),
                    ),
                    'PRIMARY_KEY' => array('iprbl_id'),
                ),
            ),
        ),
        'table_row_insert' => array(
            array(DOMAINRBL_TABLE, array(
                    array(
                        'domainrbl_fqdn' => 'bsb.spamlookup.net',
                        'domainrbl_lookup' => 'http://bsb.spamlookup.net/lookup?q=',
                        'domainrbl_weight' => 5,
                        'domainrbl_weight_default' => 5,
                    ),
                    array(
                        'domainrbl_fqdn' => 'dnsbl.othello.ch',
                        'domainrbl_lookup' => 'http://dnsbl.otello.ch?',
                        'domainrbl_weight' => 4,
                        'domainrbl_weight_default' => 4,
                    ),
                    array(
                        'domainrbl_fqdn' => 'multi.surbl.org',
                        'domainrbl_lookup' => 'http://www.surbl.org/surbl-analysis?host=',
                        'domainrbl_weight' => 5,
                        'domainrbl_weight_default' => 5,
                    ),
                    array(
                        'domainrbl_fqdn' => 'multi.uribl.com',
                        'domainrbl_lookup' => 'https://admin.uribl.com/?domains=',
                        'domainrbl_weight' => 0,
                        'domainrbl_weight_default' => 0,
                    ),
                    array(
                        'domainrbl_fqdn' => 'rhsbl.ahbl.org',
                        'domainrbl_lookup' => 'http://www.ahbl.org/lktool?lookup=',
                        'domainrbl_weight' => 5,
                        'domainrbl_weight_default' => 5,
                    ),
                    array(
                        'domainrbl_fqdn' => 'uribl.spameatingmonkey.net',
                        'domainrbl_lookup' => 'http://spameatingmonkey.com/lookup.html?check=',
                        'domainrbl_weight' => 5,
                        'domainrbl_weight_default' => 5,
                    ),
                )),
            array(HTTPBL_TABLE, array(
                    array(
                        'httpbl_name' => 'akismet',
                        'httpbl_fullname' => 'Akismet',
                        'httpbl_website' => 'http://www.akismet.com',
                        'httpbl_lookup' => 'http://rest.akismet.com',
                        'httpbl_weight' => 5,
                        'httpbl_weight_default' => 5,
                        'httpbl_key' => '',
                        'httpbl_key_required' => 1,
                        'httpbl_use_ip' => 1,
                        'httpbl_check_ip' => 1,
                        'httpbl_use_username' => 1,
                        'httpbl_check_username' => 1,
                        'httpbl_use_email' => 1,
                        'httpbl_check_email' => 1,
                        'httpbl_use_message' => 1,
                        'httpbl_check_message' => 1,
                        'httpbl_use_for_report' => 1,
                        'httpbl_active_for_report' => 1,
                        'httpbl_use_to_report' => 1,
                        'httpbl_active_to_report' => 1,
                    ),
                    array(
                        'httpbl_name' => 'bde',
                        'httpbl_fullname' => 'Block Disposable Email Addresses',
                        'httpbl_website' => 'http://www.block-disposable-email.com',
                        'httpbl_lookup' => 'http://www.block-disposable-email.com',
                        'httpbl_weight' => 5,
                        'httpbl_weight_default' => 5,
                        'httpbl_key' => '',
                        'httpbl_key_required' => 1,
                        'httpbl_use_ip' => 0,
                        'httpbl_check_ip' => 0,
                        'httpbl_use_username' => 0,
                        'httpbl_check_username' => 0,
                        'httpbl_use_email' => 1,
                        'httpbl_check_email' => 1,
                        'httpbl_use_message' => 0,
                        'httpbl_check_message' => 0,
                        'httpbl_use_for_report' => 0,
                        'httpbl_active_for_report' => 0,
                        'httpbl_use_to_report' => 0,
                        'httpbl_active_to_report' => 0,
                    ), array(
                        'httpbl_name' => 'spm',
                        'httpbl_fullname' => 'Filter Disposible Emails and Proxies',
                        'httpbl_website' => 'http://www.spam-trap.net',
                        'httpbl_lookup' => 'http://b.spam-trap.net',
                        'httpbl_weight' => 5,
                        'httpbl_weight_default' => 5,
                        'httpbl_key' => '',
                        'httpbl_key_required' => 1,
                        'httpbl_use_ip' => 1,
                        'httpbl_check_ip' => 1,
                        'httpbl_use_username' => 0,
                        'httpbl_check_username' => 0,
                        'httpbl_use_email' => 1,
                        'httpbl_check_email' => 1,
                        'httpbl_use_message' => 0,
                        'httpbl_check_message' => 0,
                        'httpbl_use_for_report' => 0,
                        'httpbl_active_for_report' => 0,
                        'httpbl_use_to_report' => 0,
                        'httpbl_active_to_report' => 0,
                    ),
                    array(
                        'httpbl_name' => 'botscout',
                        'httpbl_fullname' => 'BotScout',
                        'httpbl_website' => 'http://www.botscout.com',
                        'httpbl_lookup' => 'http://www.botscout.com',
                        'httpbl_weight' => 5,
                        'httpbl_weight_default' => 5,
                        'httpbl_key' => '',
                        'httpbl_key_required' => 1,
                        'httpbl_use_ip' => 1,
                        'httpbl_check_ip' => 1,
                        'httpbl_use_username' => 1,
                        'httpbl_check_username' => 1,
                        'httpbl_use_email' => 1,
                        'httpbl_check_email' => 1,
                        'httpbl_use_message' => 0,
                        'httpbl_check_message' => 0,
                        'httpbl_use_for_report' => 0,
                        'httpbl_active_for_report' => 0,
                        'httpbl_use_to_report' => 1,
                        'httpbl_active_to_report' => 1,
                    ),
                    array(
                        'httpbl_name' => 'honeypot',
                        'httpbl_fullname' => 'Project Honey Pot',
                        'httpbl_website' => 'http://www.projecthoneypot.org',
                        'httpbl_lookup' => 'http://www.projecthoneypot.org',
                        'httpbl_weight' => 5,
                        'httpbl_weight_default' => 5,
                        'httpbl_key' => '',
                        'httpbl_key_required' => 1,
                        'httpbl_use_ip' => 1,
                        'httpbl_check_ip' => 1,
                        'httpbl_use_username' => 0,
                        'httpbl_check_username' => 0,
                        'httpbl_use_email' => 0,
                        'httpbl_check_email' => 0,
                        'httpbl_use_message' => 0,
                        'httpbl_check_message' => 0,
                        'httpbl_use_for_report' => 0,
                        'httpbl_active_for_report' => 0,
                        'httpbl_use_to_report' => 1,
                        'httpbl_active_to_report' => 1,
                    ),
                    array(
                        'httpbl_name' => 'sfs',
                        'httpbl_fullname' => 'Stop Forum Spam',
                        'httpbl_website' => 'http://www.stopforumspam.com',
                        'httpbl_lookup' => 'http://www.stopforumspam.com',
                        'httpbl_weight' => 5,
                        'httpbl_weight_default' => 5,
                        'httpbl_key' => '',
                        'httpbl_key_required' => 1,
                        'httpbl_use_ip' => 1,
                        'httpbl_check_ip' => 1,
                        'httpbl_use_username' => 1,
                        'httpbl_check_username' => 1,
                        'httpbl_use_email' => 1,
                        'httpbl_check_email' => 1,
                        'httpbl_use_message' => 0,
                        'httpbl_check_message' => 0,
                        'httpbl_use_for_report' => 1,
                        'httpbl_active_for_report' => 1,
                        'httpbl_use_to_report' => 1,
                        'httpbl_active_to_report' => 1,
                    ),
                )),
            array(IPRBL_TABLE, array(
                    array(
                        'iprbl_fqdn' => 'b.barracudacentral.org',
                        'iprbl_lookup' => 'http://www.barracudacentral.org/lookups/ip-reputation?ip_address=',
                        'iprbl_weight' => 2,
                        'iprbl_weight_default' => 2,
                    ),
                    array(
                        'iprbl_fqdn' => 'bl.blocklist.de',
                        'iprbl_lookup' => 'http://www.blocklist.de/en/view.html?ip=',
                        'iprbl_weight' => 4,
                        'iprbl_weight_default' => 4,
                    ),
                    array(
                        'iprbl_fqdn' => 'bl.spamcannibal.org',
                        'iprbl_lookup' => 'http://www.spamcannibal.org/cannibal.cgi?page=lookup&amplookup=',
                        'iprbl_weight' => 4,
                        'iprbl_weight_default' => 4,
                    ),
                    array(
                        'iprbl_fqdn' => 'bl.spamcop.net',
                        'iprbl_lookup' => 'http://spamcop.net/bl.shtml?',
                        'iprbl_weight' => 0,
                        'iprbl_weight_default' => 0,
                    ),
                    array(
                        'iprbl_fqdn' => 'blackholes.five-ten-sg.com',
                        'iprbl_lookup' => 'http://www.five-ten-sg.com/blackhole.php?Search=Search&ampip=',
                        'iprbl_weight' => 2,
                        'iprbl_weight_default' => 2,
                    ),
                    array(
                        'iprbl_fqdn' => 'cbl.abuseat.org',
                        'iprbl_lookup' => 'http://cbl.abuseat.org/lookup.cgi?.submit=Lookup&ampip=',
                        'iprbl_weight' => 4,
                        'iprbl_weight_default' => 4,
                    ),
                    array(
                        'iprbl_fqdn' => 'combined.abuse.ch',
                        'iprbl_lookup' => 'http://dnsbl.abuse.ch/?ipaddress=',
                        'iprbl_weight' => 0,
                        'iprbl_weight_default' => 0,
                    ),
                    array(
                        'iprbl_fqdn' => 'combined.njabl.org',
                        'iprbl_lookup' => 'http://dnsbl.njabl.org/lookup.html',
                        'iprbl_weight' => 0,
                        'iprbl_weight_default' => 0,
                    ),
                    array(
                        'iprbl_fqdn' => 'dnsbl.ahbl.org',
                        'iprbl_lookup' => 'http://www.ahbl.org/lktool?lookup=',
                        'iprbl_weight' => 5,
                        'iprbl_weight_default' => 5,
                    ),
                    array(
                        'iprbl_fqdn' => 'dnsbl.tornevall.org',
                        'iprbl_lookup' => 'http://www.stopforumspam.com/api?ip=',
                        'iprbl_weight' => 4,
                        'iprbl_weight_default' => 4,
                    ),
                    array(
                        'iprbl_fqdn' => 'dnsbl-1.uceprotect.net',
                        'iprbl_lookup' => 'http://www.uceprotect.net/en/rblcheck.php?ipr=',
                        'iprbl_weight' => 5,
                        'iprbl_weight_default' => 5,
                    ),
                    array(
                        'iprbl_fqdn' => 'dnsbl-2.uceprotect.net',
                        'iprbl_lookup' => 'http://www.uceprotect.net/en/rblcheck.php?ipr=',
                        'iprbl_weight' => 2,
                        'iprbl_weight_default' => 2,
                    ),
                    array(
                        'iprbl_fqdn' => 'dnsbl-3.uceprotect.net',
                        'iprbl_lookup' => 'http://www.uceprotect.net/en/rblcheck.php?ipr=',
                        'iprbl_weight' => 2,
                        'iprbl_weight_default' => 2,
                    ),
                    array(
                        'iprbl_fqdn' => 'escalations.dnsbl.sorbs.net',
                        'iprbl_lookup' => 'http://www.sorbs.net/lookup.shtml?',
                        'iprbl_weight' => 0,
                        'iprbl_weight_default' => 0,
                    ),
                    array(
                        'iprbl_fqdn' => 'no-more-funn.moensted.dk',
                        'iprbl_lookup' => 'http://moensted.dk/spam/no-more-funn/?Submit=Submit&addr=',
                        'iprbl_weight' => 0,
                        'iprbl_weight_default' => 0,
                    ),
                    array(
                        'iprbl_fqdn' => 'psbl.surriel.com',
                        'iprbl_lookup' => 'http://psbl.org/listing?ip=',
                        'iprbl_weight' => 0,
                        'iprbl_weight_default' => 0,
                    ),
                    array(
                        'iprbl_fqdn' => 'sbl-xbl.spamhaus.org',
                        'iprbl_lookup' => 'http://www.spamhaus.org/query/bl?ip=',
                        'iprbl_weight' => 4,
                        'iprbl_weight_default' => 4,
                    ),
                    array(
                        'iprbl_fqdn' => 'smtp.dnsbl.sorbs.net',
                        'iprbl_lookup' => 'http://www.sorbs.net/lookup.shtml?',
                        'iprbl_weight' => 0,
                        'iprbl_weight_default' => 0,
                    ),
                    array(
                        'iprbl_fqdn' => 'socks.dnsbl.sorbs.net',
                        'iprbl_lookup' => 'http://www.sorbs.net/lookup.shtml?',
                        'iprbl_weight' => 0,
                        'iprbl_weight_default' => 0,
                    ),
                    array(
                        'iprbl_fqdn' => 'spam.dnsbl.sorbs.net',
                        'iprbl_lookup' => 'http://www.sorbs.net/lookup.shtml?',
                        'iprbl_weight' => 4,
                        'iprbl_weight_default' => 4,
                    ),
                    array(
                        'iprbl_fqdn' => 'ubl.unsubscore.com',
                        'iprbl_lookup' => 'http://www.lashback.com/blacklist/?',
                        'iprbl_weight' => 0,
                        'iprbl_weight_default' => 0,
                    ),
                )),
        ),
        'table_column_add' => array(
            array(LOG_TABLE, 'domainrbl_id', array('UINT', '0')),
            array(LOG_TABLE, 'httpbl_id', array('UINT', '0')),
            array(LOG_TABLE, 'iprbl_id', array('UINT', '0')),
            array(POSTS_TABLE, 'post_user_email', array('VCHAR', '')),
        ),
        'module_add' => array(
            array('acp', 'ACP_CAT_GENERAL', array(
                    'module_basename' => '',
                    'module_langname' => 'ACP_BLOCK_CONFIGURATION',
                    'modes' => array(''),
                ),
            ),
            array('acp', 'ACP_BLOCK_CONFIGURATION', array(
                    'module_basename' => 'board',
                    'modes' => array('block'),
                ),
            ),
            array('acp', 'ACP_BLOCK_CONFIGURATION', array(
                    'module_basename' => 'domainrbl',
                    'modes' => array('manage'),
                ),
            ),
            array('acp', 'ACP_BLOCK_CONFIGURATION', array(
                    'module_basename' => 'httpbl',
                    'modes' => array('manage'),
                ),
            ),
            array('acp', 'ACP_BLOCK_CONFIGURATION', array(
                    'module_basename' => 'iprbl',
                    'modes' => array('manage'),
                ),
            ),
            array('acp', 'ACP_FORUM_LOGS', array(
                    'module_basename' => 'logs',
                    'modes' => array('block'),
                ),
            ),
        ),
        'config_remove' => array(
            array('check_dnsbl'),
        ),
        'config_add' => array(
            array('break_after_httpbl', 1, false),
            array('break_after_iprbl', 1, false),
            array('check_domainrbl_email', 1, false),
            array('check_domainrbl_post', 1, false),
            array('check_domainrbl_profile', 1, false),
            array('check_domainrbl_signature', 1, false),
            array('check_domainrbl_website', 1, false),
            array('check_httpbl_post', 1, false),
            array('check_httpbl_profile', 1, false),
            array('check_httpbl_register', 1, false),
            array('check_iprbl_post', 1, false),
            array('check_iprbl_register', 1, false),
            array('check_tz', 1, false),
            array('log_check_domainrbl', 1, false),
            array('log_check_httpbl', 1, false),
            array('log_check_iprbl', 1, false),
            array('log_check_tz', 1, false),
            array('log_email_check_mx', 1, false),
            array('report_httpbl', 0, false),
            array('require_email_guest', 1, false),
        ),
        'config_update' => array(
            array('email_check_mx', 1, false),
            array('enable_confirm', 0, false),
            array('enable_post_confirm', 0, false),
        ),
        'cache_purge' => array('', 'imageset', 'template', 'theme'),
    ),
    // Version 1.1.1
    '1.1.1' => array(
        'cache_purge' => array('', 'template'),
    ),
    // Version 1.1.2
    '1.1.2' => array(
    ),
    // Version 1.1.3
    '1.1.3' => array(
    ),
    // Version 1.1.4
    '1.1.4' => array(
        'cache_purge' => array(),
    ),
);

// Include the UMIF Auto file and everything else will be handled automatically.
include($phpbb_root_path . 'umil/umil_auto.' . $phpEx);
?>