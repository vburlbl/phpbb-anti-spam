<?php

/**
 *
 * @package Advanced Block MOD
 * @version $Id: functions.php, v 1.004 2012/12/19 Martin Truckenbrodt Exp$
 * @copyright (c) 2009, 2012 Martin Truckenbrodt 
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License 
 *
 */
if (!defined('IN_PHPBB')) {
    exit;
}

/**
 * Check if ip is blacklisted
 * */
function check_iprbl($action = 'recheck', $ip = false)
{
    global $db, $config;

    // Need to be listed on all servers...
    $weight = 0;
    $info = array();

    //check only if ip is IPv4
    if ($ip && !preg_match(get_preg_expression('ipv6'), $ip)) {
        $quads = explode('.', $ip);
        $reverse_ip = $quads[3] . '.' . $quads[2] . '.' . $quads[1] . '.' . $quads[0];

        $sql = 'SELECT iprbl_id, iprbl_fqdn, iprbl_lookup, iprbl_weight FROM ' . IPRBL_TABLE . "
			WHERE iprbl_weight > '0'
			ORDER BY iprbl_weight DESC, iprbl_count DESC";

        $result = $db->sql_query($sql);

        while (($row = $db->sql_fetchrow($result)) && ($weight < 5 || $action == 'recheck'))
        {
            if (phpbb_checkdnsrr($reverse_ip . '.' . $row['iprbl_fqdn'] . '.', 'A') === true) {
                if ($weight < 5) {
                    $info['blacklists'][] = array($row['iprbl_fqdn'], $row['iprbl_lookup'] . $ip);
                } else if ($weight > 4 && $action == 'recheck') {
                    $info['next'][] = array($row['iprbl_fqdn'], $row['iprbl_lookup'] . $ip);
                }
                if ($config['log_check_iprbl'] && $action != 'recheck') {
                    add_log('block', $row['iprbl_id'], 0, 0, 'LOG_IPRBL_FOUND', $row['iprbl_fqdn'], $ip, $row['iprbl_lookup'] . $ip);
                }
                $weight += $row['iprbl_weight'];

                $sql = 'UPDATE ' . IPRBL_TABLE . '
					SET iprbl_count = iprbl_count + 1 
					WHERE iprbl_id = ' . $row['iprbl_id'];
                $db->sql_query($sql);
            }
        }

        $db->sql_freeresult($result);

        if ($weight > 4 || $action == 'recheck') {
            if ($weight > 4) {
                $info['blocked'] = true;
            }
            if ($config['log_check_iprbl'] && $action != 'recheck') {
                add_log('block', 0, 0, 0, 'LOG_IPRBL_' . strtoupper($action));
            }
            return $info;
        }
    }

    if ($info && $action == 'recheck') {
        return $info;
    } else {
        return false;
    }
}

/**
 * Check if ip, username, user_email or message is blacklisted
 * */
function check_httpbl($action = 'recheck', $ip = false, $username = false, $email = false, $message = false)
{
    global $phpbb_root_path, $phpEx, $db, $config;

    if (!function_exists('get_remote_file')) {
        include($phpbb_root_path . 'includes/functions_admin.' . $phpEx);
    }

    // Need to be listed on all servers...
    $next = false;
    $weight = 0;
    $info = array();

    if (($ip || $username || $email || $message)) {
        // no reverse_ip for IPv6 clients!!!
        if ($ip && !preg_match(get_preg_expression('ipv6'), $ip)) {
            $quads = explode('.', $ip);
            $reverse_ip = $quads[3] . '.' . $quads[2] . '.' . $quads[1] . '.' . $quads[0];
        } else {
            $reverse_ip = '';
        }

        $sql = 'SELECT * FROM ' . HTTPBL_TABLE . "
			WHERE httpbl_weight > '0'
			ORDER BY httpbl_weight DESC, httpbl_count DESC";

        $result = $db->sql_query($sql);

        while ($row = $db->sql_fetchrow($result))
        {
            $block_spam = $types_found = false;
            // Stop Forum Spam
            if ($row['httpbl_name'] == 'sfs') {
                $type = $value = $appears = $frequency = $frequency_min = false;
                $apiurl = '/api?';

                // SFS does not support IPv6
                if ($ip && !preg_match(get_preg_expression('ipv6'), $ip) && $row['httpbl_use_ip'] && $row['httpbl_check_ip']) {
                    $apiurl .= 'ip=' . $ip;
                    if (($username && $row['httpbl_use_username'] && $row['httpbl_check_username']) || ($email && $row['httpbl_use_email'] && $row['httpbl_check_email'])) {
                        $apiurl .= '&';
                    }
                }
                if ($username && $row['httpbl_use_username'] && $row['httpbl_check_username']) {
                    $apiurl .= 'username=' . $username;
                    if ($email && $row['httpbl_use_email'] && $row['httpbl_check_email']) {
                        $apiurl .= '&';
                    }
                }
                if ($email && $row['httpbl_use_email'] && $row['httpbl_check_email']) {
                    $apiurl .= 'email=' . $email;
                }
                $file = get_remote_file('stopforumspam.com', '', $apiurl, $errstr, $errno);
                if ($file !== false) {
                    $file = str_replace("\r\n", "\n", $file);
                    $file = explode("\n", $file);

                    foreach ($file as $line)
                    {
                        if (strpos($line, '<type>') !== false && strpos($line, '</type>') !== false) {
                            $start = strpos($line, '<type>') + 6;
                            $end = strpos($line, '</type>') - $start;
                            $type = substr($line, $start, $end);
                            switch ($type)
                            {
                                case 'ip':
                                    $value = $ip;
                                    //increase the following value if you have false positives with SFS caused by listed ip addresses
                                    $frequency_min = 1;
                                    break;

                                case 'username':
                                    $value = $username;
                                    //increase the following value if you have false positives with SFS caused by listed usernames
                                    $frequency_min = 1;
                                    break;

                                case 'email':
                                    $value = $email;
                                    //increase the following value if you have false positives with SFS caused by listed e-mail addresses
                                    $frequency_min = 1;
                                    break;
                            }
                        } else if (strpos($line, '<appears>') !== false && strpos($line, '</appears>') !== false) {
                            $start = strpos($line, '<appears>') + 9;
                            $end = strpos($line, '</appears>') - $start;
                            $appears = (substr($line, $start, $end) == 'yes') ? true : false;
                        } else if (strpos($line, '<frequency>') !== false && strpos($line, '</frequency>') !== false) {
                            $start = strpos($line, '<frequency>') + 11;
                            $end = strpos($line, '</frequency>') - $start;
                            $frequency = (int) substr($line, $start, $end);
                            if ($appears && $frequency >= $frequency_min) {
                                $types_found .= (($types_found !== false) ? ', ' : '') . $type;
                                // block only if at least ip or email have been found - username is easy to change and can have a lot of false positives for regular and legitimate usernames
                                if ($type == 'ip' || $type == 'email') {
                                    $block_spam = true;
                                }
                                $appears = $frequency = false;
                            }
                        }
                    }
                }
            }

            // BotScout
            else if ($row['httpbl_name'] == 'botscout') {
                $type = $value = $fullapiurl = false;
                $apiurl = '/test/?multi';

                //increase the following values if you have false positives with BotScout
                $frequency_ip_min = 1;
                $frequency_username_min = 1;
                $frequency_email_min = 1;

                // BotScout does not support IPv6
                if ($ip && !preg_match(get_preg_expression('ipv6'), $ip) && $row['httpbl_use_ip'] && $row['httpbl_check_ip']) {
                    $apiurl .= '&ip=' . $ip;
                }
                if ($username && $row['httpbl_use_username'] && $row['httpbl_check_username']) {
                    $apiurl .= '&name=' . $username;
                }
                if ($email && $row['httpbl_use_email'] && $row['httpbl_check_email']) {
                    $apiurl .= '&mail=' . $email;
                }
                // without key only 20 requests per day are allowed, with key 300 requests per day are free
                // do not add the key to the public lookup URL
                if ($row['httpbl_key']) {
                    $fullapiurl = $apiurl . '&key=' . $row['httpbl_key'];
                }
                $file = get_remote_file('botscout.com', '', $fullapiurl, $errstr, $errno);
                if ($file !== false) {
                    $response = explode('|', $file);
                    if (substr($response[0], -1) == 'Y') {
                        //block only then ip or email are listed, username has a lot of false positives
                        if ($response[3] >= $frequency_ip_min) {
                            $types_found .= (($types_found !== false) ? ', ' : '') . 'ip';
                            $block_spam = true;
                        }
                        if ($response[5] >= $frequency_email_min) {
                            $types_found .= (($types_found !== false) ? ', ' : '') . 'email';
                            $block_spam = true;
                        }
                        if ($response[7] >= $frequency_username_min) {
                            $types_found .= (($types_found !== false) ? ', ' : '') . 'username';
                        }
                    } else if (substr($file, 4, 1) == '!') {
                        add_log('critical', 'LOG_ERROR_HTTPBL', $row['httpbl_fullname'], $errno, $errstr . $file);
                    }
                }
            }

            // Akismet
            // based on http://akismet.com/development/api
            // a key always is needed
            else if ($row['httpbl_name'] == 'akismet' && $row['httpbl_key']) {
                global $phpbb_root_path, $phpEx, $user;
                $server_url = generate_board_url();

                $apiurl = 'key=' . $row['httpbl_key'] . '&blog=' . urlencode($server_url . '/index.' . $phpEx);
                $file = post_remote_file('rest.akismet.com', '/1.1/verify-key', $apiurl, $errstr, $errno);

                if ($file[1] != 'valid') {
                    add_log('critical', 'LOG_ERROR_HTTPBL', $row['httpbl_fullname'], $errno, $errstr . $file[0]);
                }

                $apiurl = '';

                if (!$ip) {
                    $ip = $user->ip;
                }
                $apiurl = 'user_ip=' . urlencode($ip) . '&user_agent=' . urlencode($user->browser) . '&referrer=' . urlencode($user->referer) . '&blog=' . urlencode($server_url . '/index.' . $phpEx) . '&comment_type=forum';

                if ($username && $row['httpbl_use_username'] && $row['httpbl_check_username']) {
                    $apiurl .= '&comment_author=' . urlencode($username);
                }
                if ($email && $row['httpbl_use_email'] && $row['httpbl_check_email']) {
                    $apiurl .= '&comment_author_email=' . urlencode($email);
                }
                if ($email && $row['httpbl_use_message'] && $row['httpbl_check_message']) {
                    $apiurl .= '&comment_content=' . urlencode($message);
                }

                $file = post_remote_file($row['httpbl_key'] . '.rest.akismet.com', '/1.1/comment-check', $apiurl, $errstr, $errno);
                if ($file[1] == 'true') {
                    $types_found = 'N/A';
                    $block_spam = true;
                } else if ($file[1] == 'invalid') {
                    add_log('critical', 'LOG_ERROR_HTTPBL', $row['httpbl_fullname'], $errno, $errstr . $file[0]);
                }
            }

            // Project Honey Pot
            // based on http://www.projecthoneypot.org/httpbl_api.php
            // a key always is needed
            // does not support IPv6
            else if ($row['httpbl_name'] == 'honeypot' && $row['httpbl_key'] && $ip && !preg_match(get_preg_expression('ipv6'), $ip)) {
                $apiurl = '';

                $query = $row['httpbl_key'] . '.' . $reverse_ip . '.dnsbl.httpbl.org';
                $response = gethostbyname($query);
                if ($response != $query) {
                    $response = explode('.', $response);
                    // don't report and block search engines and suspicious ips
                    // more information on http://www.projecthoneypot.org/httpbl_api.php
                    if ($response[1] > 0 && $response[2] > 0 && $response[3] > 1 && sizeof($response) == 4) {
                        $types_found = 'ip';
                        $block_spam = true;
                    } else if ($response[0] != '127') {
                        add_log('critical', 'LOG_ERROR_HTTPBL', $row['httpbl_fullname'], 0, implode('.', $response));
                    }
                }
            }

            // Block Disposable Email Addresses
            else if ($row['httpbl_name'] == 'bde' && $row['httpbl_key'] && ($action == 'register' || $action == 'profile') && $email) {
                $apiurl = '';

                $response = get_remote_file('check.block-disposable-email.com', '', '/check.php?mail=' . $email . '&apikey=' . $row['httpbl_key'], $errstr, $errno);
                if ($response !== false) {
                    if ($response == 'OK') {
                        break;
                    } else if ($response == 'BLOCK') {
                        $types_found = 'email';
                        $block_spam = true;
                    } else {
                        add_log('critical', 'LOG_ERROR_HTTPBL', $row['httpbl_fullname'], 0, $response);
                    }
                }
            }
            // Filter Disposable and Proxy IPS
            else if ($row['httpbl_name'] == 'spm' && $row['httpbl_key'] && ($action == 'register' || $action == 'profile') && $email) {
                $apiurl = '';

                $response = get_remote_file('b.spam-trap.net', '', '/verify.php?mail=' . $email . '&ip='.$ip.'&apikey=' . md5($_SERVER['HTTP_HOST'].'|'.$email), $errstr, $errno);
                if(isset($response) && !empty($response)){
                    $response_r = json_decode($response,TRUE);
                }
                if (isset($response['status'])) {
                    if ($response['status'] == 'OK') {
                        break;
                    } else if ($response['status'] == 'BLOCK') {
                        $types_found = 'email';
                        $block_spam = true;
                    } else {
                        add_log('critical', 'LOG_ERROR_HTTPBL', $row['httpbl_fullname'], 0, $response);
                    }
                }
            }

            if ($types_found) {
                if ($config['log_check_httpbl'] && $action != 'recheck') {
                    add_log('block', 0, 0, $row['httpbl_id'], 'LOG_HTTPBL_FOUND', $row['httpbl_fullname'], $types_found, ($row['httpbl_lookup']) ? $row['httpbl_lookup'] . $apiurl : $row['httpbl_website']);
                }
                if ($block_spam) {
                    if ($weight > 4 && $action == 'recheck') {
                        $info['next'][] = array('data' => array($row['httpbl_name'], $row['httpbl_fullname'], ($row['httpbl_lookup']) ? $row['httpbl_lookup'] . $apiurl : $row['httpbl_website'], $types_found), 'report' => ($row['httpbl_use_to_report'] && $row['httpbl_active_to_report']) ? true : false);
                        $next = true;
                    }
                    $weight += $row['httpbl_weight'];
                    if ($next == false) {
                        $info['blacklists'][] = array('data' => array($row['httpbl_name'], $row['httpbl_fullname'], ($row['httpbl_lookup']) ? $row['httpbl_lookup'] . $apiurl : $row['httpbl_website'], $types_found), 'report' => ($row['httpbl_use_to_report'] && $row['httpbl_active_to_report']) ? true : false);
                    }

                    $sql = 'UPDATE ' . HTTPBL_TABLE . '
						SET httpbl_count = httpbl_count + 1 
						WHERE httpbl_id = ' . $row['httpbl_id'];
                    $db->sql_query($sql);
                }
            }

            if (($weight > 4 && $block_spam) || $action == 'recheck') {
                if ($weight > 4 && $block_spam) {
                    $info['blocked'] = 'spam';
                }
                if ($action != 'recheck') {
                    if ($config['log_check_httpbl']) {
                        add_log('block', 0, 0, 0, 'LOG_HTTPBL_' . strtoupper($action));
                    }
                    return $info;
                    break;
                }
            }
        }

        $db->sql_freeresult($result);

        if ($info && $action == 'recheck') {
            return $info;
        } else {
            return false;
        }
    }

    return false;
}

/**
 * Check if URI is blacklisted
 * */
function check_domainrbl($action = 'recheck', $domains_array)
{
    global $db, $config;

    if (!is_array($domains_array)) {
        $domains_array = array($domains_array);
    }

    $weight = 0;
    $info = array();

    if ($domains_array) {
        $sql = 'SELECT domainrbl_id, domainrbl_fqdn, domainrbl_lookup, domainrbl_weight FROM ' . DOMAINRBL_TABLE . "
			WHERE domainrbl_weight > '0'
			ORDER BY domainrbl_weight DESC, domainrbl_count DESC";

        $result = $db->sql_query($sql);

        while (($row = $db->sql_fetchrow($result)) && ($weight < 5 || $action == 'recheck'))
        {
            foreach ($domains_array as $domain)
            {
                if (phpbb_checkdnsrr($domain . '.' . $row['domainrbl_fqdn'] . '.', 'A') === true) {
                    if ($weight < 5) {
                        $info['blacklists'][] = array($row['domainrbl_fqdn'], $row['domainrbl_lookup'] . $domain, $domain);
                    } else if ($weight > 4 && $action == 'recheck') {
                        $info['next'][] = array($row['domainrbl_fqdn'], $row['domainrbl_lookup'] . $domain, $domain);
                    }
                    if ($config['log_check_domainrbl'] && $action != 'recheck') {
                        add_log('block', 0, $row['domainrbl_id'], 0, 'LOG_DOMAINRBL_FOUND', $row['domainrbl_fqdn'], $domain, $row['domainrbl_lookup'] . $domain);
                    }
                    $weight += $row['domainrbl_weight'];

                    $sql = 'UPDATE ' . DOMAINRBL_TABLE . '
						SET domainrbl_count = domainrbl_count + 1 
						WHERE domainrbl_id = ' . $row['domainrbl_id'];
                    $db->sql_query($sql);
                }
                if ($weight > 4 && $action != 'recheck') {
                    break 2;
                }
            }
        }

        $db->sql_freeresult($result);

        if ($weight > 4 || $action == 'recheck') {
            if ($weight > 4) {
                $info['blocked'] = true;
            }
            if ($action != 'recheck') {
                if ($config['log_check_domainrbl']) {
                    add_log('block', 0, 0, 0, 'LOG_DOMAINRBL_' . strtoupper($action));
                }
                return $info;
            }
        }
    }

    if ($info && $action == 'recheck') {
        return $info;
    } else {
        return false;
    }
}

/**
 * Check spam ... used to check users and posts for spam
 * does not use block log and reporting on recheck!
 */
function check_spam($mode = false, $action = 'recheck', $ip = false, $user_id = 0, $post_id = 0, $data = false, $message = false)
{
    global $user, $config, $db, $phpbb_root_path, $phpEx;

    $info = $user_data = $post_data = array();

    if (!isset($data) || !$data) {
        $data = array();
    }

    $fill_keys = array('user_id', 'user_ip', 'username', 'user_email');
    $user_data = array_fill_keys($fill_keys, '');

    if ($mode == 'post') {
        if ($post_id) {
            $sql = 'SELECT poster_id, poster_ip, post_username, post_user_email, post_text, bbcode_bitfield, bbcode_uid FROM ' . POSTS_TABLE . '
				WHERE post_id = ' . (int) $post_id;
            $result = $db->sql_query($sql);
            $post_data = $db->sql_fetchrow($result);
            $db->sql_freeresult($result);
        } else {
            $post_data = $data;
        }
        if ($post_data['poster_id'] != ANONYMOUS) {
            $sql = 'SELECT user_id, user_ip, username, user_email, user_sig, user_sig_bbcode_uid, user_sig_bbcode_bitfield, user_website FROM ' . USERS_TABLE . '
				WHERE user_id = ' . (int) $post_data['poster_id'];
            $result = $db->sql_query_limit($sql, 1);
            $user_data = $db->sql_fetchrow($result);
            $db->sql_freeresult($result);
        }
        $user_data['user_id'] = ($user_data['user_id']) ? $user_data['user_id'] : ((array_key_exists('poster_id', $post_data)) ? $post_data['poster_id'] : '');
        $user_data['user_ip'] = ($ip) ? $ip : (($user_data['user_ip']) ? $user_data['user_ip'] : ((array_key_exists('poster_ip', $post_data)) ? $post_data['poster_ip'] : ''));
        $user_data['username'] = ($user_data['username']) ? $user_data['username'] : ((array_key_exists('username', $post_data)) ? $post_data['username'] : '');
        $user_data['user_email'] = ($user_data['user_email']) ? $user_data['user_email'] : ((array_key_exists('post_user_email', $post_data)) ? $post_data['post_user_email'] : '');

        if (empty($message) && $post_data['post_text']) {
            if (!function_exists('parse_message')) {
                include($phpbb_root_path . 'includes/message_parser.' . $phpEx);
            }
            $message = new parse_message();
            $message->message = &$post_data['post_text'];
            $message->bbcode_uid = $post_data['bbcode_uid'];
            $message->bbcode_bitfield = $post_data['bbcode_bitfield'];
        }

        unset($post_data);
    } else if ($mode == 'user') {
        // used only for recheck
        if ($user_id) {
            $sql = 'SELECT user_id, user_ip, username, user_email, user_sig, user_sig_bbcode_uid, user_sig_bbcode_bitfield, user_website, user_timezone FROM ' . USERS_TABLE . '
				WHERE user_id = ' . (int) $user_id;
            $result = $db->sql_query_limit($sql, 1);
            $user_data = $db->sql_fetchrow($result);
            $db->sql_freeresult($result);
        } else {
            $user_data['user_ip'] = ($ip) ? $ip : (($user_data['user_ip']) ? $user_data['user_ip'] : ((array_key_exists('user_ip', $data)) ? $data['user_ip'] : ''));
            $user_data['username'] = (array_key_exists('username', $data)) ? $data['username'] : ((array_key_exists('username', $data)) ? $data['username'] : '');
            $user_data['user_email'] = (array_key_exists('user_email', $data)) ? $data['user_email'] : ((array_key_exists('email', $data)) ? $data['email'] : '');
            $user_data['user_timezone'] = (array_key_exists('user_timezone', $data)) ? $data['user_timezone'] : ((array_key_exists('tz', $data)) ? $data['tz'] : '');
            $user_data['user_website'] = (array_key_exists('user_website', $data)) ? $data['user_website'] : ((array_key_exists('website', $data)) ? $data['website'] : '');
            $user_data['user_sig'] = (array_key_exists('user_sig', $data)) ? $data['user_sig'] : ((array_key_exists('signature', $data)) ? $data['signature'] : '');
        }
        if (empty($message) && $user_data['user_sig']) {
            if (!function_exists('parse_message')) {
                include($phpbb_root_path . 'includes/message_parser.' . $phpEx);
            }
            $message = new parse_message();
            $message->message = &$user_data['user_sig'];
            $message->bbcode_uid = $user_data['user_sig_bbcode_uid'];
            $message->bbcode_bitfield = $user_data['user_sig_bbcode_bitfield'];
        }
    }

    // get the URIs from message or signature
    if (isset($message) && $message) {
        $data['enable_bbcode'] = (array_key_exists('enable_bbcode', $data)) ? $data['enable_bbcode'] : '';
        $data['enable_smilies'] = (array_key_exists('enable_smilies', $data)) ? $data['enable_smilies'] : '';
        $data['enable_magic_url'] = (array_key_exists('enable_magic_url', $data)) ? $data['enable_magic_url'] : '';
        $bbcode_options = (array_key_exists('user_options', $data)) ? $data['user_options'] : ((($data['enable_bbcode']) ? OPTION_FLAG_BBCODE : 0) +
                (($data['enable_smilies']) ? OPTION_FLAG_SMILIES : 0) +
                (($data['enable_magic_url']) ? OPTION_FLAG_LINKS : 0));

        $message = generate_text_for_display($message->message, $message->bbcode_uid, $message->bbcode_bitfield, $bbcode_options);

        $uris_array = array_unique(get_base_domains_from_text($message));
    }

    // Now let us uncover the bad guys to kill em all! :)
    // Like Kaya Yanar says it in Turk-German: Du kommst hier net rein!
    $iprbl_array = $httpbl_array = $domainrbl_array = array();
    $break = $report_spam = false;

    // IPRBL check
    // supports only IPv4
    if ($user_data['user_ip'] && !preg_match(get_preg_expression('ipv6'), $user_data['user_ip']) && (((($config['check_iprbl_post'] == SPAM_CHECK_ALL || ($config['check_iprbl_post'] == SPAM_CHECK_GUESTS && $user_data['user_id'] == ANONYMOUS)) && $mode == 'post') || ($config['check_iprbl_register'] && $mode == 'user' && $action == 'register')) || $action == 'recheck')) {
        if (($iprbl_array = check_iprbl($action, $user_data['user_ip'])) && array_key_exists('blacklists', $iprbl_array)) {
            if ($action == 'recheck') {
                if (($config['check_iprbl_post'] == SPAM_CHECK_ALL || (($config['check_iprbl_post'] == SPAM_CHECK_GUESTS) && $user_data['user_id'] == ANONYMOUS) && $mode == 'post') || ($config['check_iprbl_register'] && $mode == 'user')) {
                    if (array_key_exists('blocked', $iprbl_array)) {
                        $info[] = $user->lang['RECHECK_SPAM_IPRBL'];
                    } else {
                        $info[] = $user->lang['RECHECK_SPAM_IPRBL_NOT'];
                    }
                } else {
                    $info[] = $user->lang['RECHECK_SPAM_IPRBL_NO'];
                }
                foreach ($iprbl_array['blacklists'] as $iprbl)
                {
                    $info[] = sprintf($user->lang['RECHECK_SPAM_IPRBL_IP'], $iprbl[0], $iprbl[1]);
                }
                if ($config['break_after_iprbl'] && array_key_exists('blocked', $iprbl_array)) {
                    $info[] = $user->lang['RECHECK_SPAM_BREAK'];
                    $break = true;
                } else {
                    $info[] = '<br />';
                }
                if (array_key_exists('next', $iprbl_array)) {
                    $info[] = $user->lang['RECHECK_SPAM_IPRBL_NEXT'];
                    foreach ($iprbl_array['next'] as $iprbl)
                    {
                        $info[] = sprintf($user->lang['RECHECK_SPAM_IPRBL_IP'], $iprbl[0], $iprbl[1]);
                    }
                    $info[] = '<br />';
                }
            } else {
                $report_spam = true;
                foreach ($iprbl_array['blacklists'] as $iprbl)
                {
                    $info[] = sprintf($user->lang['IP_BLACKLISTED'], $user_data['user_ip'], $iprbl[1]);
                }
            }
        }
    }

    // HTTPBL check
    if (($user_data['user_ip'] || $user_data['username'] || $user_data['user_email'] || $message) && (((((($config['check_httpbl_register'] && $action == 'register') || ($config['check_httpbl_profile'] && $action == 'profile')) && $mode == 'user') || ((($config['check_httpbl_post'] == SPAM_CHECK_ALL) || ($config['check_httpbl_post'] == SPAM_CHECK_GUESTS && $user_data['user_id'] == ANONYMOUS)) && $mode == 'post')) && ($config['break_after_iprbl'] && $info) == false) || $action == 'recheck')) {
        if (($httpbl_array = check_httpbl($action, $user_data['user_ip'], $user_data['username'], $user_data['user_email'], $message)) && array_key_exists('blacklists', $httpbl_array)) {
            if ($action == 'recheck') {
                if (($config['check_httpbl_register'] && $mode == 'user') || (($config['check_httpbl_post'] == SPAM_CHECK_ALL || (($config['check_httpbl_post'] == SPAM_CHECK_GUESTS) && $user_data['user_id'] == ANONYMOUS) && $mode == 'post'))) {
                    if (array_key_exists('blocked', $httpbl_array)) {
                        $info[] = $user->lang['RECHECK_SPAM_HTTPBL'];
                    } else {
                        $info[] = $user->lang['RECHECK_SPAM_HTTPBL_NOT'];
                    }
                } else {
                    $info[] = $user->lang['RECHECK_SPAM_HTTPBL_NO'];
                }
                foreach ($httpbl_array['blacklists'] as $httpbl)
                {
                    $info[] = sprintf($user->lang['RECHECK_SPAM_HTTPBL_DATA'], $httpbl['data'][1], $httpbl['data'][2], $httpbl['data'][3]);
                }
                if ($config['break_after_httpbl'] && array_key_exists('blocked', $httpbl_array) && $break == false) {
                    $info[] = $user->lang['RECHECK_SPAM_BREAK'];
                } else {
                    $info[] = '<br />';
                }
                if (array_key_exists('next', $httpbl_array)) {
                    $info[] = $user->lang['RECHECK_SPAM_HTTPBL_NEXT'];
                    foreach ($httpbl_array['next'] as $httpbl)
                    {
                        $info[] = sprintf($user->lang['RECHECK_SPAM_HTTPBL_DATA'], $httpbl['data'][1], $httpbl['data'][2], $httpbl['data'][3]);
                    }
                    $info[] = '<br />';
                }
            } else {
                foreach ($httpbl_array['blacklists'] as $httpbl)
                {
                    if ($httpbl['report'] == 'spam') {
                        $report_spam = true;
                    }
                    $info[] = sprintf($user->lang['HTTPBL_BLACKLISTED'], ($user_data['user_ip']) ? $user_data['user_ip'] : $user->lang['NA'], ($user_data['username']) ? $user_data['username'] : $user->lang['NA'], ($user_data['user_email']) ? $user_data['user_email'] : $user->lang['NA'], $httpbl['data'][2]);
                }
            }
        }
    }

    // Domain-RBL check for user_email
    if ($user_data['user_email'] && ((((($config['check_domainrbl_email'] == SPAM_CHECK_ALL || $config['check_domainrbl_email'] == SPAM_CHECK_GUESTS) && $mode == 'user') || ($user_data['user_id'] == ANONYMOUS && $mode == 'post')) && ($config['break_after_httpbl'] && $info) == false) || $action == 'recheck')) {
        $email_domainrbl_array = $email_uris_array = array();
        list(, $uri) = explode('@', $user_data['user_email']);
        $email_uris_array[] = get_base_domain($uri, true);
        if (($email_domainrbl_array = check_domainrbl($action . '_email', $email_uris_array)) && array_key_exists('blacklists', $email_domainrbl_array)) {
            if ($action == 'recheck') {
                if ($config['check_domainrbl_email'] == SPAM_CHECK_ALL || $config['check_domainrbl_email'] == SPAM_CHECK_GUESTS) {
                    if (array_key_exists('blocked', $email_domainrbl_array)) {
                        $info[] = $user->lang['RECHECK_SPAM_DOMAINRBL_EMAIL'];
                    } else {
                        $info[] = $user->lang['RECHECK_SPAM_DOMAINRBL_EMAIL_NOT'];
                    }
                } else {
                    $info[] = $user->lang['RECHECK_SPAM_DOMAINRBL_EMAIL_NO'];
                }
                foreach ($email_domainrbl_array['blacklists'] as $domainrbl)
                {
                    $info[] = sprintf($user->lang['RECHECK_SPAM_DOMAINRBL_EMAIL_URI'], $domainrbl[2], $domainrbl[0], $domainrbl[1]);
                }
                if (array_key_exists('next', $email_domainrbl_array)) {
                    $info[] = $user->lang['RECHECK_SPAM_DOMAINRBL_EMAIL_NEXT'];
                    foreach ($email_domainrbl_array['next'] as $domainrbl)
                    {
                        $info[] = sprintf($user->lang['RECHECK_SPAM_DOMAINRBL_EMAIL_URI'], $domainrbl[2], $domainrbl[0], $domainrbl[1]);
                    }
                }
            } else {
                $report_spam = true;
                foreach ($email_domainrbl_array['blacklists'] as $domainrbl)
                {
                    $info[] = sprintf($user->lang['EMAIL_DOMAIN_BLACKLISTED'], $domainrbl[2], $domainrbl[1]);
                }
            }
        }
    }

    if ($user_data['user_email'] && ($config['email_check_mx'] || $action == 'recheck')) {
        list(, $domain) = explode('@', $user_data['user_email']);

        if (phpbb_checkdnsrr($domain, 'A') === false && phpbb_checkdnsrr($domain, 'MX') === false) {
            if ($config['log_email_check_mx']) {
                add_log('block', 0, 0, 0, 'LOG_DNSMX', $domain);
            }
            if ($action == 'recheck') {
                $info[] = sprintf($user->lang['RECHECK_SPAM_DNSMX'], $domain);
            } else {
                // do not report human failures!
                // $report_spam = true;
                $info[] = $user->lang['DOMAIN_NO_MX_RECORD_EMAIL'];
            }
        }
    }

    if ($mode == 'user') {
        // anti spam check for the former UTC -12 trick
        if ($config['check_tz']) {
            if ((float) $user_data['user_timezone'] == -19 || (float) $user_data['user_timezone'] == 19) {
                if ($config['log_check_tz']) {
                    add_log('block', 0, 0, 0, 'LOG_WRONG_TZ', (float) $user_data['user_timezone']);
                }
                // do not report human failures or funny human!
                // $report_spam = true;
                $info[] = $user->lang['WRONG_TIMEZONE'];
            }
        }

        // Domain-RBL check for user_website
        if ($user_data['user_website'] && (($config['check_domainrbl_website'] && ($config['break_after_httpbl'] && $info) == false) || $action == 'recheck')) {
            $domainrbl_array = $uris_array = array();
            // do not use the large function, it's only one complete hyperlink URL
            $uris_array = get_base_domain(parse_url($user_data['user_website'], PHP_URL_HOST), true);
            if (($domainrbl_array = check_domainrbl($action . '_website', $uris_array)) && array_key_exists('blacklists', $domainrbl_array)) {
                if ($action == 'recheck') {
                    if ($config['check_domainrbl_website']) {
                        if (array_key_exists('blocked', $domainrbl_array)) {
                            $info[] = $user->lang['RECHECK_SPAM_DOMAINRBL_WEBSITE'];
                        } else {
                            $info[] = $user->lang['RECHECK_SPAM_DOMAINRBL_WEBSITE_NOT'];
                        }
                    } else {
                        $info[] = $user->lang['RECHECK_SPAM_DOMAINRBL_WEBSITE_NO'];
                    }
                    foreach ($domainrbl_array['blacklists'] as $domainrbl)
                    {
                        $info[] = sprintf($user->lang['RECHECK_SPAM_DOMAINRBL_WEBSITE_URI'], $domainrbl[2], $domainrbl[0], $domainrbl[1]);
                    }
                    if (array_key_exists('next', $domainrbl_array)) {
                        $info[] = $user->lang['RECHECK_SPAM_DOMAINRBL_WEBSITE_NEXT'];
                        foreach ($domainrbl_array['next'] as $domainrbl)
                        {
                            $info[] = sprintf($user->lang['RECHECK_SPAM_DOMAINRBL_WEBSITE_URI'], $domainrbl[2], $domainrbl[0], $domainrbl[1]);
                        }
                    }
                } else {
                    $report_spam = true;
                    foreach ($domainrbl_array['blacklists'] as $domainrbl)
                    {
                        $info[] = sprintf($user->lang['WEBSITE_BLACKLISTED'], $domainrbl[2], $domainrbl[1]);
                    }
                }
            }
        }

        // Domain-RBL check for signature
        if (isset($uris_array) && $uris_array && (($config['check_domainrbl_signature'] && ($config['break_after_httpbl'] && $info) == false) || $action == 'recheck')) {
            if (($domainrbl_array = check_domainrbl($action . '_signature', $uris_array)) && array_key_exists('blacklists', $domainrbl_array)) {
                if ($action == 'recheck') {
                    if ($config['check_domainrbl_signature']) {
                        if (array_key_exists('blocked', $domainrbl_array)) {
                            $info[] = $user->lang['RECHECK_SPAM_DOMAINRBL_SIGNATURE'];
                        } else {
                            $info[] = $user->lang['RECHECK_SPAM_DOMAINRBL_SIGNATURE_NOT'];
                        }
                    } else {
                        $info[] = $user->lang['RECHECK_SPAM_DOMAINRBL_SIGNATURE_NO'];
                    }
                    foreach ($domainrbl_array['blacklists'] as $domainrbl)
                    {
                        $info[] = sprintf($user->lang['RECHECK_SPAM_DOMAINRBL_SIGNATURE_URI'], $domainrbl[2], $domainrbl[0], $domainrbl[1]);
                    }
                    if (array_key_exists('next', $domainrbl_array)) {
                        $info[] = $user->lang['RECHECK_SPAM_DOMAINRBL_SIGNATURE_NEXT'];
                        foreach ($domainrbl_array['next'] as $domainrbl)
                        {
                            $info[] = sprintf($user->lang['RECHECK_SPAM_DOMAINRBL_SIGNATURE_URI'], $domainrbl[2], $domainrbl[0], $domainrbl[1]);
                        }
                    }
                } else {
                    $report_spam = true;
                    foreach ($domainrbl_array['blacklists'] as $domainrbl)
                    {
                        $info[] = sprintf($user->lang['SIGNATURE_BLACKLISTED'], $domainrbl[2], $domainrbl[1]);
                    }
                }
            }
        }
    }

    if ($mode == 'post') {
        // Domain-RBL check for URIs
        if (isset($uris_array) && $uris_array && (((($config['check_domainrbl_post'] == SPAM_CHECK_ALL) || ($config['check_domainrbl_post'] == SPAM_CHECK_GUESTS && $user_data['user_id'] == ANONYMOUS)) && ($config['break_after_httpbl'] && $info) == false) || $action == 'recheck')) {
            if (($domainrbl_array = check_domainrbl($action, $uris_array)) && array_key_exists('blacklists', $domainrbl_array)) {
                if ($action == 'recheck') {
                    if ($config['check_domainrbl_post'] == SPAM_CHECK_ALL || (($config['check_domainrbl_post'] == SPAM_CHECK_GUESTS))) {
                        if (array_key_exists('blocked', $domainrbl_array)) {
                            $info[] = $user->lang['RECHECK_SPAM_DOMAINRBL_POST'];
                        } else {
                            $info[] = $user->lang['RECHECK_SPAM_DOMAINRBL_POST_NOT'];
                        }
                    } else {
                        $info[] = $user->lang['RECHECK_SPAM_DOMAINRBL_POST_NO'];
                    }
                    foreach ($domainrbl_array['blacklists'] as $domainrbl)
                    {
                        $info[] = sprintf($user->lang['RECHECK_SPAM_DOMAINRBL_POST_URI'], $domainrbl[2], $domainrbl[0], $domainrbl[1]);
                    }
                    if (array_key_exists('next', $domainrbl_array)) {
                        $info[] = $user->lang['RECHECK_SPAM_DOMAINRBL_POST_NEXT'];
                        foreach ($domainrbl_array['next'] as $domainrbl)
                        {
                            $info[] = sprintf($user->lang['RECHECK_SPAM_DOMAINRBL_POST_URI'], $domainrbl[2], $domainrbl[0], $domainrbl[1]);
                        }
                    }
                } else {
                    $report_spam = true;
                    foreach ($domainrbl_array['blacklists'] as $domainrbl)
                    {
                        $info[] = sprintf($user->lang['DOMAIN_BLACKLISTED'], $domainrbl[2], $domainrbl[1]);
                    }
                }
            }
        }
    }

    if (!sizeof($info) && $action == 'recheck') {
        $info[] = $user->lang['RECHECK_SPAM_NO'];
    }

    if (sizeof($info) && $action !== 'recheck') {
        $info[] = $user->lang['BLACKLISTED_INFO'];
    }

    // support for Contact Board Administration MOD http://www.phpbb.com/customise/db/mod/contact_board_administration/
    if (array_key_exists('contact_enable', $config) && $config['contact_enable'] && sizeof($info) && $info && $action != 'recheck') {
        $info[] = sprintf($user->lang['CONTACT_BLACKLISTED'], $phpbb_root_path . 'contact.' . $phpEx);
    }

    if ($action == 'recheck') {
        return implode(' ', $info);
    } else {
        if ($config['report_httpbl'] && $report_spam) {
            report_httpbl($action, $user_data['user_ip'], $user_data['username'], $user_data['user_email'], $message);
        }
        return $info;
    }
}

/**
 * Report spam to HTTP Blacklists
 * */
function report_httpbl($action = false, $ip = false, $username = false, $email = false, $message = false)
{
    global $db, $config;

// for future use if other databases will need it
    $quads = explode('.', $ip);
    $reverse_ip = $quads[3] . '.' . $quads[2] . '.' . $quads[1] . '.' . $quads[0];


    $sql = 'SELECT * FROM ' . HTTPBL_TABLE . "
		WHERE httpbl_weight > '0'
		ORDER BY httpbl_weight DESC, httpbl_count DESC";

    $result = $db->sql_query($sql);

    while ($row = $db->sql_fetchrow($result))
    {
        // Stop Forum Spam
        // requires ip, username and email - does not support IPv6
        if (($row['httpbl_name'] == 'sfs') && $row['httpbl_key'] && $ip && !preg_match(get_preg_expression('ipv6'), $ip) && $username && $email && $row['httpbl_use_for_report'] && $row['httpbl_active_for_report']) {
            $apiurl = 'username=';
            if ($username && $row['httpbl_use_username'] && $row['httpbl_check_username']) {
                $apiurl .= $username;
            }
            $apiurl .= '&ip_addr=';
            if ($ip && $row['httpbl_use_ip'] && $row['httpbl_check_ip']) {
                $apiurl .= $ip;
            }
            $apiurl .= '&email=';
            if ($email && $row['httpbl_use_email'] && $row['httpbl_check_email']) {
                $apiurl .= $email;
            }
            $apiurl .= '&api_key=' . $row['httpbl_key'];
            $file = post_remote_file('www.stopforumspam.com', '/add.php', $apiurl, $errstr, $errno);
            if ($errstr) {
                add_log('critical', 'LOG_ERROR_HTTPBL', $row['httpbl_fullname'], $errno, $errstr);
            } else {
                add_log('block', 0, 0, $row['httpbl_id'], 'LOG_HTTPBL_REPORTED_' . strtoupper($action), $row['httpbl_fullname'], $username, $ip, $email, $row['httpbl_lookup'] . '/api?ip=' . $ip . '&username=' . $username . '&email=' . $email);
            }
        }

        // Akismet
        // based on http://akismet.com/development/api
        // a key always is needed
        else if ($row['httpbl_name'] == 'akismet' && $row['httpbl_key']) {
            global $phpbb_root_path, $phpEx, $user;
            $server_url = generate_board_url();

            $apiurl = 'key=' . $row['httpbl_key'] . '&blog=' . urlencode($server_url . '/index.' . $phpEx);
            $file = post_remote_file('rest.akismet.com', '/1.1/verify-key', $apiurl, $errstr, $errno);

            if ($file[1] != 'valid') {
                add_log('critical', 'LOG_ERROR_HTTPBL', $row['httpbl_fullname'], $errno, $errstr . $file[0]);
            }

            $apiurl = '';

            if (!$ip) {
                $ip = $user->ip;
            }
            $apiurl = 'user_ip=' . urlencode($ip) . '&user_agent=' . urlencode($user->browser) . '&referrer=' . urlencode($user->referer) . '&blog=' . urlencode($server_url . '/index.' . $phpEx) . '&comment_type=forum';

            if ($username && $row['httpbl_use_username'] && $row['httpbl_check_username']) {
                $apiurl .= '&comment_author=' . urlencode($username);
            }
            if ($email && $row['httpbl_use_email'] && $row['httpbl_check_email']) {
                $apiurl .= '&comment_author_email=' . urlencode($email);
            }
            if ($email && $row['httpbl_use_message'] && $row['httpbl_check_message']) {
                $apiurl .= '&comment_content=' . urlencode($message);
            }

            $file = post_remote_file($row['httpbl_key'] . '.rest.akismet.com', '/1.1/submit-spam', $apiurl, $errstr, $errno);
            if ($errstr) {
                add_log('critical', 'LOG_ERROR_HTTPBL', $row['httpbl_fullname'], $errno, $errstr . $file[0]);
            } else {
                add_log('block', 0, 0, $row['httpbl_id'], 'LOG_HTTPBL_REPORTED_' . strtoupper($action), $row['httpbl_fullname'], $username, $ip, $email, $row['httpbl_website']);
            }
        }
    }

    $db->sql_freeresult($result);
}

/**
 * Retrieve contents from remotely written file
 * based on http://akismet.com/development/api and phpBB3 core function get_remote_file
 * */
function post_remote_file($host, $directory, $filename, &$errstr, &$errno, $port = 80, $timeout = 10)
{
    global $user, $phpEx;
    $server_url = generate_board_url();

    if ($fsock = @fsockopen($host, $port, $errno, $errstr, $timeout)) {
        @fwrite($fsock, "POST $directory HTTP/1.0\r\n");
        @fwrite($fsock, "HOST: $host\r\n");
        @fwrite($fsock, "Content-Type: application/x-www-form-urlencoded\r\n");
        @fwrite($fsock, "User-Agent: phpBB/3.0 | Advanced Block MOD/1.1\r\n");
        @fwrite($fsock, "Content-Length: " . strlen($filename) . "\r\n\r\n");
        @fwrite($fsock, $filename . "\r\n");

        $file_info = '';

        while (!@feof($fsock))
        {
            $file_info .= @fgets($fsock, 1160);
        }
        @fclose($fsock);
        $file_info = explode("\r\n\r\n", $file_info, 2);
        return $file_info;
    } else {
        if ($errstr) {
            $errstr = utf8_convert_message($errstr);
            return false;
        } else {
            $errstr = $user->lang['FSOCK_DISABLED'];
            return false;
        }
    }
}

/**
 * get base domain (domain.tld)
 * based on http://phosphorusandlime.blogspot.com/2007/08/php-get-base-domain.html
 * */
function get_base_domain($full_domain = '', $reverse_ip = true)
{
    $base_domain = '';

    // generic tlds (source: http://en.wikipedia.org/wiki/Generic_top-level_domain)
    $generic_tlds = array(
        'biz', 'com', 'edu', 'gov', 'info', 'int', 'mil', 'name', 'net', 'org',
        'aero', 'asia', 'cat', 'coop', 'jobs', 'mobi', 'museum', 'pro', 'tel', 'travel',
        'arpa', 'root',
        'berlin', 'bzh', 'cym', 'gal', 'geo', 'kid', 'kids', 'lat', 'mail', 'nyc', 'post', 'sco', 'web', 'xxx',
        'nato',
        'example', 'invalid', 'localhost', 'test',
        'bitnet', 'csnet', 'ip', 'local', 'onion', 'uucp',
        'co' // note: not technically, but used in things like co.uk
    );

    // country tlds (source: http://en.wikipedia.org/wiki/Country_code_top-level_domain)
    $country_tlds = array(
        // active
        'ac', 'ad', 'ae', 'af', 'ag', 'ai', 'al', 'am', 'an', 'ao', 'aq', 'ar', 'as', 'at', 'au', 'aw', 'ax', 'az',
        'ba', 'bb', 'bd', 'be', 'bf', 'bg', 'bh', 'bi', 'bj', 'bm', 'bn', 'bo', 'br', 'bs', 'bt', 'bw', 'by', 'bz',
        'ca', 'cc', 'cd', 'cf', 'cg', 'ch', 'ci', 'ck', 'cl', 'cm', 'cn', 'co', 'cr', 'cu', 'cv', 'cx', 'cy', 'cz',
        'de', 'dj', 'dk', 'dm', 'do', 'dz', 'ec', 'ee', 'eg', 'er', 'es', 'et', 'eu', 'fi', 'fj', 'fk', 'fm', 'fo',
        'fr', 'ga', 'gd', 'ge', 'gf', 'gg', 'gh', 'gi', 'gl', 'gm', 'gn', 'gp', 'gq', 'gr', 'gs', 'gt', 'gu', 'gw',
        'gy', 'hk', 'hm', 'hn', 'hr', 'ht', 'hu', 'id', 'ie', 'il', 'im', 'in', 'io', 'iq', 'ir', 'is', 'it', 'je',
        'jm', 'jo', 'jp', 'ke', 'kg', 'kh', 'ki', 'km', 'kn', 'kr', 'kw', 'ky', 'kz', 'la', 'lb', 'lc', 'li', 'lk',
        'lr', 'ls', 'lt', 'lu', 'lv', 'ly', 'ma', 'mc', 'md', 'mg', 'mh', 'mk', 'ml', 'mm', 'mn', 'mo', 'mp', 'mq',
        'mr', 'ms', 'mt', 'mu', 'mv', 'mw', 'mx', 'my', 'mz', 'na', 'nc', 'ne', 'nf', 'ng', 'ni', 'nl', 'no', 'np',
        'nr', 'nu', 'nz', 'om', 'pa', 'pe', 'pf', 'pg', 'ph', 'pk', 'pl', 'pn', 'pr', 'ps', 'pt', 'pw', 'py', 'qa',
        're', 'ro', 'ru', 'rw', 'sa', 'sb', 'sc', 'sd', 'se', 'sg', 'sh', 'si', 'sk', 'sl', 'sm', 'sn', 'sr', 'st',
        'sv', 'sy', 'sz', 'tc', 'td', 'tf', 'tg', 'th', 'tj', 'tk', 'tl', 'tm', 'tn', 'to', 'tr', 'tt', 'tv', 'tw',
        'tz', 'ua', 'ug', 'uk', 'us', 'uy', 'uz', 'va', 'vc', 've', 'vg', 'vi', 'vn', 'vu', 'wf', 'ws', 'ye', 'yu',
        'za', 'zm', 'zw',
        // inactive
        'eh', 'kp', 'me', 'rs', 'um', 'bv', 'gb', 'pm', 'sj', 'so', 'yt', 'su', 'tp', 'bu', 'cs', 'dd', 'zr'
    );

    // break up domain, reverse
    $domain = explode('.', $full_domain);
    $domain = array_reverse($domain);

    // first check for ip address
    // domain-rbl check needs reverse octects
    if (sizeof($domain) == 4 && is_numeric($domain[0]) && is_numeric($domain[3])) {
        if ($reverse_ip) {
            return $domain[0] . '.' . $domain[1] . '.' . $domain[2] . '.' . $domain[3];
        } else {
            return $full_domain;
        }
    }

    // if only 2 domain parts, that must be our domain
    if (sizeof($domain) <= 2) {
        return $full_domain;
    }

    /*
      finally, with 3+ domain parts: obviously D0 is tld
      now, if D0 = ctld and D1 = gtld, we might have something like com.uk
      so, if D0 = ctld && D1 = gtld && D2 != 'www', domain = D2.D1.D0
      else if D0 = ctld && D1 = gtld && D2 == 'www', domain = D1.D0
      else domain = D1.D0
      these rules are simplified below
     */
    if (in_array($domain[0], $country_tlds) && in_array($domain[1], $generic_tlds) && $domain[2] != 'www') {
        $full_domain = $domain[2] . '.' . $domain[1] . '.' . $domain[0];
    } else {
        $full_domain = $domain[1] . '.' . $domain[0];
        ;
    }

    // did we succeed? 
    return $full_domain;
}

/**
 * get base domains from text
 * text needs to be parsed by generate_text_for_display before
 * */
function get_base_domains_from_text($message = '')
{
    global $user;

    $post_uris_array = array();

    //remove phpBB tags to simplyfy the string
    $search = array(" class=\"postlink\"", " class=\"postlink-local\"", " alt=\"" . $user->lang['IMAGE'] . "\"");
    $replacement = array("", "", "");
    $message = str_replace($search, $replacement, $message);

    // get the URLs
    // decode HTML special chars
    $message = htmlspecialchars_decode($message);
    //extract the urls from HTML a tags
    $message = preg_replace('#<a href="((https?://|mailto:)[^"]+)">(.*?)</a>#', ' \\1 ', $message);
    // extract the urls from HTML img tags
    $message = preg_replace('#<img src="(https?://[^"]+)" />#', ' \\1 ', $message);
    // build array of URLs from text
    $urls_array = preg_split('#(https?://\S+(?<![,.]))#', $message, -1, PREG_SPLIT_DELIM_CAPTURE);
    // filter URLs from array
    $urls_array = preg_grep('#(https?://\S+(?<![,.]))#', $urls_array);
    // filter empty entries from array
    $urls_array = array_filter($urls_array);

    // get the URIs (domain names)
    foreach ($urls_array as $url)
    {
        // extract host (FQDN) and and get base domain
        $post_uris_array[] = get_base_domain(parse_url($url, PHP_URL_HOST), true);
    }
    // remove double entries from array
    $post_uris_array = array_unique($post_uris_array);
    return $post_uris_array;
}

?>