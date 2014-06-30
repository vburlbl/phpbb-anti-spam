<?php
/**
*
* @package acp
* @version $Id: acp_httpbl.php,v 1.001 2012/11/14 Martin Truckenbrodt Exp $
* @copyright (c) 2009, 2012 Martin Truckenbrodt 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @ignore
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package acp
*/
class acp_httpbl
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache;

		$user->add_lang('mods/abm');

		$this->tpl_name = 'acp_httpbl';
		$this->page_title = 'ACP_HTTPBL';

		$form_key = 'acp_httpbl';
		add_form_key($form_key);

		// Check the permission setting again
		if (!$auth->acl_get('a_board'))
		{
			trigger_error($user->lang['NO_AUTH_OPERATION'] . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$action		= request_var('action', '');
		$update		= (isset($_POST['update'])) ? true : false;
		$httpbl_id	= request_var('h', 0);

		$httpbl_data = $errors = array();
		if ($update && !check_form_key($form_key))
		{
			$update = false;
			$errors[] = $user->lang['FORM_INVALID'];
		}

		// Major routines
		if ($update)
		{
			switch ($action)
			{
				case 'edit':

					$httpbl_data = array(
						'httpbl_id'					=> $httpbl_id,
						'httpbl_weight'				=> request_var('httpbl_weight', 0),
						'httpbl_key'				=> request_var('httpbl_key', ''),
						'httpbl_check_ip'			=> request_var('httpbl_check_ip', 0),
						'httpbl_check_username'		=> request_var('httpbl_check_username', 0),
						'httpbl_check_email'		=> request_var('httpbl_check_email', 0),
						'httpbl_check_message'		=> request_var('httpbl_check_message', 0),
						'httpbl_active_to_report'	=> request_var('httpbl_active_to_report', 0),
						'httpbl_active_for_report'	=> request_var('httpbl_active_for_report', 0),
					);

					if (isset($_POST['httpbl_reset']))
					{
						$sql = 'UPDATE ' . HTTPBL_TABLE . '
							SET httpbl_count = 0 
							WHERE httpbl_id = ' . (int) $httpbl_id;
						$db->sql_query($sql);
					}

					$errors = update_httpbl_data($httpbl_data);

					trigger_error($user->lang['HTTPBL_EDITED'] . adm_back_link($this->u_action));

					break;
			}
		}

		switch ($action)
		{
			case 'edit':

				// Show form to create/modify a httpbl
				$this->page_title = 'HTTPBL_EDIT';
				$row = get_httpbl_info($httpbl_id);

				if (!$update)
				{
					$httpbl_data = $row;
				}

				$httpbl_weight_options = '';
				$httpbl_weight_ary = array(WEIGHT_ZERO, WEIGHT_ONE, WEIGHT_TWO, WEIGHT_THREE, WEIGHT_FOUR, WEIGHT_FIVE);

				foreach ($httpbl_weight_ary as $value)
				{
					$httpbl_weight_options .= '<option value="' . $value . '"' . (($value == $httpbl_data['httpbl_weight']) ? ' selected="selected"' : '') . '>' . $value . '</option>';
				}

				$template->assign_vars(array(
					'S_HTTPBL_EDIT'					=> true,
					'HTTPBL_FULLNAME'				=> $httpbl_data['httpbl_fullname'],
					'HTTPBL_WEBSITE'				=> $httpbl_data['httpbl_website'],
					'HTTPBL_LOOKUP'					=> $httpbl_data['httpbl_lookup'],
					'HTTPBL_DETAILS'				=> $user->lang['HTTPBL_DETAILS_' . strtoupper($httpbl_data['httpbl_name'])],
					'HTTPBL_KEY'					=> $httpbl_data['httpbl_key'],
					'S_HTTPBL_KEY_REQUIRED'			=> ($httpbl_data['httpbl_key_required']) ? true : false,
					'S_HTTPBL_USE_IP'				=> ($httpbl_data['httpbl_use_ip']) ? true : false,
					'S_HTTPBL_CHECK_IP'				=> ($httpbl_data['httpbl_check_ip']) ? true : false,
					'S_HTTPBL_USE_USERNAME'			=> ($httpbl_data['httpbl_use_username']) ? true : false,
					'S_HTTPBL_CHECK_USERNAME'		=> ($httpbl_data['httpbl_check_username']) ? true : false,
					'S_HTTPBL_USE_EMAIL'			=> ($httpbl_data['httpbl_use_email']) ? true : false,
					'S_HTTPBL_CHECK_EMAIL'			=> ($httpbl_data['httpbl_check_email']) ? true : false,
					'S_HTTPBL_USE_MESSAGE'			=> ($httpbl_data['httpbl_use_message']) ? true : false,
					'S_HTTPBL_CHECK_MESSAGE'		=> ($httpbl_data['httpbl_check_message']) ? true : false,
					'S_HTTPBL_USE_TO_REPORT'		=> ($httpbl_data['httpbl_use_to_report']) ? true : false,
					'S_HTTPBL_ACTIVE_TO_REPORT'		=> ($httpbl_data['httpbl_active_to_report']) ? true : false,
					'S_HTTPBL_USE_FOR_REPORT'		=> ($httpbl_data['httpbl_use_for_report']) ? true : false,
					'S_HTTPBL_ACTIVE_FOR_REPORT'	=> ($httpbl_data['httpbl_active_for_report']) ? true : false,
					'S_HTTPBL_WEIGHT_OPTIONS'		=> $httpbl_weight_options,
					'HTTPBL_WEIGHT_DEFAULT'			=> $httpbl_data['httpbl_weight_default'],
					'HTTPBL_COUNT'					=> $httpbl_data['httpbl_count'],
					'S_ERROR'						=> (sizeof($errors)) ? true : false,
					'U_BACK'						=> $this->u_action,
					'U_EDIT_ACTION'					=> $this->u_action . "&amp;action=$action&amp;h=$httpbl_id",
					'L_TITLE'						=> $user->lang[$this->page_title],
					'ERROR_MSG'						=> (sizeof($errors)) ? implode('<br />', $errors) : '',
				));

				return;

			break;

		}

		$sql = 'SELECT httpbl_id, httpbl_fullname, httpbl_website, httpbl_weight, httpbl_weight_default, httpbl_count FROM ' . HTTPBL_TABLE . '
			ORDER BY httpbl_weight DESC, httpbl_count DESC';
		$result = $db->sql_query($sql);

		if ($row = $db->sql_fetchrow($result))
		{
			do
			{
				$url = $this->u_action . "&amp;h={$row['httpbl_id']}";

				$template->assign_block_vars('httpbl', array(
					'HTTPBL_FULLNAME'		=> $row['httpbl_fullname'],
					'HTTPBL_WEBSITE'		=> $row['httpbl_website'],
					'HTTPBL_WEIGHT'			=> $row['httpbl_weight'],
					'HTTPBL_WEIGHT_DEFAULT'	=> $row['httpbl_weight_default'],
					'HTTPBL_COUNT'			=> $row['httpbl_count'],

					'U_EDIT'				=> $url . '&amp;action=edit',
				));
			}
			while ($row = $db->sql_fetchrow($result));
		}

		$db->sql_freeresult($result);

		$template->assign_vars(array(
			'ERROR_MSG'		=> (sizeof($errors)) ? implode('<br />', $errors) : '',
			'U_ACTION'		=> $this->u_action,

		));

	}

}

/**
* Get httpbl details
*/
function get_httpbl_info($httpbl_id)
{
	global $db;

	$sql = 'SELECT *
		FROM ' . HTTPBL_TABLE . '
		WHERE httpbl_id = ' . (int) $httpbl_id;
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	return $row;
}

/**
* Update httpbl data
*/
function update_httpbl_data(&$httpbl_data)
{
	global $db, $user, $cache;

	$errors = array();

	// Unset data that are not database fields
	$httpbl_data_sql = $httpbl_data;

	// What are we going to do tonight Brain? The same thing we do everynight,
	// try to take over the world ... or decide whether to continue update
	// and if so, whether which groups and users we have to remove from which tables
	if (sizeof($errors))
	{
		return $errors;
	}

	// Setting the httpbl id to the httpbl id is not really received well by some dbs. ;)
	$httpbl_id = $httpbl_data_sql['httpbl_id'];
	unset($httpbl_data_sql['httpbl_id']);

	$sql = 'UPDATE ' . HTTPBL_TABLE . '
		SET ' . $db->sql_build_array('UPDATE', $httpbl_data_sql) . '
		WHERE httpbl_id = ' . (int) $httpbl_id;
	$db->sql_query($sql);

	$sql = 'SELECT httpbl_fullname
		FROM ' . HTTPBL_TABLE . '
		WHERE httpbl_id = ' . (int) $httpbl_id;
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	add_log('admin', 'LOG_HTTPBL_EDIT', $row['httpbl_fullname']);

	return $errors;
}

?>