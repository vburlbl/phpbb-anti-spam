<?php
/**
*
* @package acp
* @version $Id: acp_iprbl.php,v 1.001 2012/11/14 Martin Truckenbrodt Exp $
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
class acp_iprbl
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache;

		$user->add_lang('mods/abm');

		$this->tpl_name = 'acp_iprbl';
		$this->page_title = 'ACP_IPRBL';

		$form_key = 'acp_iprbl';
		add_form_key($form_key);

		// Check the permission setting again
		if (!$auth->acl_get('a_board'))
		{
			trigger_error($user->lang['NO_AUTH_OPERATION'] . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$action		= request_var('action', '');
		$update		= (isset($_POST['update'])) ? true : false;
		$iprbl_id	= request_var('d', 0);

		$iprbl_data = $errors = array();
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
				case 'delete':

					$errors = delete_iprbl($iprbl_id);

					if (sizeof($errors))
					{
						break;
					}

					$auth->acl_clear_prefetch();
					$cache->destroy('sql', IPRBL_TABLE);

					trigger_error($user->lang['IPRBL_DELETED'] . adm_back_link($this->u_action));

				break;

				case 'edit':
					$iprbl_data = array(
						'iprbl_id'		=>	$iprbl_id
					);

					if (isset($_POST['iprbl_reset']))
					{
						$sql = 'UPDATE ' . IPRBL_TABLE . '
							SET iprbl_count = 0 
							WHERE iprbl_id = ' . (int) $iprbl_id;
						$db->sql_query($sql);
					}

				// No break here

				case 'add':

					$iprbl_data += array(
						'iprbl_fqdn'		=> utf8_normalize_nfc(request_var('iprbl_fqdn', '', true)),
						'iprbl_lookup'		=> utf8_normalize_nfc(request_var('iprbl_lookup', '', true)),
						'iprbl_weight'		=> request_var('iprbl_weight', 0),
					);

					if (check_fqdn($iprbl_data['iprbl_fqdn']) === false)
					{
						trigger_error($user->lang['IPRBL_FQDN_NOT_VALID'] . adm_back_link($this->u_action), E_USER_WARNING);
					}
					else
					{
						$errors = update_iprbl_data($iprbl_data);
					}

					trigger_error($user->lang['IPRBL_ADDED_EDITED'] . adm_back_link($this->u_action));

				break;
			}
		}

		switch ($action)
		{
			case 'add':
			case 'edit':

				// Show form to create/modify a iprbl
				if ($action == 'edit')
				{
					$this->page_title = 'IPRBL_EDIT';
					$row = get_iprbl_info($iprbl_id);

					if (!$update)
					{
						$iprbl_data = $row;
					}
				}
				else
				{
					$this->page_title = 'IPRBL_CREATE';

					// Fill iprbl data with default values
					if (!$update)
					{
						$iprbl_data = array(
							'iprbl_fqdn'		=> utf8_normalize_nfc(request_var('iprbl_fqdn', '', true)),
							'iprbl_lookup'		=> '',
							'iprbl_weight'		=> 0,
							'iprbl_count'		=> 0,
						);
					}
				}

				$iprbl_weight_options = '';
				$iprbl_weight_ary = array(WEIGHT_ZERO, WEIGHT_ONE, WEIGHT_TWO, WEIGHT_THREE, WEIGHT_FOUR, WEIGHT_FIVE);

				foreach ($iprbl_weight_ary as $value)
				{
					$iprbl_weight_options .= '<option value="' . $value . '"' . (($value == $iprbl_data['iprbl_weight']) ? ' selected="selected"' : '') . '>' . $value . '</option>';
				}

				$template->assign_vars(array(
					'S_IPRBL_EDIT'				=> true,
					'IPRBL_FQDN'				=> $iprbl_data['iprbl_fqdn'],
					'IPRBL_LOOKUP'				=> $iprbl_data['iprbl_lookup'],
					'IPRBL_WEIGHT_DEFAULT'		=> (isset($iprbl_data['iprbl_weight_default'])) ? $iprbl_data['iprbl_weight_default'] : 0,
					'IPRBL_COUNT'				=> $iprbl_data['iprbl_count'],
					'S_IPRBL_WEIGHT_OPTIONS'	=> $iprbl_weight_options,
					'S_ERROR'					=> (sizeof($errors)) ? true : false,
					'S_ADD_ACTION'				=> ($action == 'add') ? true : false,
					'U_BACK'					=> $this->u_action,
					'U_EDIT_ACTION'				=> $this->u_action . "&amp;action=$action&amp;d=$iprbl_id",
					'L_TITLE'					=> $user->lang[$this->page_title],
					'ERROR_MSG'					=> (sizeof($errors)) ? implode('<br />', $errors) : '',
					'S_DNS_A_RECORD'			=> (phpbb_checkdnsrr($iprbl_data['iprbl_fqdn'], 'A')) ? true : false,
				));

				return;

			break;

			case 'delete':

				if (!$iprbl_id)
				{
					trigger_error($user->lang['NO_IPRBL'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$sql = 'SELECT iprbl_fqdn FROM ' . IPRBL_TABLE . '
					WHERE iprbl_id= ' . (int) $iprbl_id;
				$result = $db->sql_query($sql);
				$iprbl_data = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				$template->assign_vars(array(
					'S_IPRBL_DELETE'	=> true,
					'U_ACTION'			=> $this->u_action . "&amp;action=delete&amp;d=$iprbl_id",
					'U_BACK'			=> $this->u_action,
					'IPRBL_FQDN'		=> $iprbl_data['iprbl_fqdn'],
					'S_ERROR'			=> (sizeof($errors)) ? true : false,
					'ERROR_MSG'			=> (sizeof($errors)) ? implode('<br />', $errors) : '')
				);

				return;

			break;
		}

		$sql = 'SELECT iprbl_id, iprbl_fqdn, iprbl_weight, iprbl_weight_default, iprbl_count FROM ' . IPRBL_TABLE . '
			ORDER BY iprbl_weight DESC, iprbl_count DESC, iprbl_fqdn ASC';
		$result = $db->sql_query($sql);

		if ($row = $db->sql_fetchrow($result))
		{
			do
			{
				$url = $this->u_action . "&amp;d={$row['iprbl_id']}";

				$template->assign_block_vars('iprbl', array(
					'IPRBL_FQDN'			=> $row['iprbl_fqdn'],
					'IPRBL_WEIGHT'			=> $row['iprbl_weight'],
					'IPRBL_WEIGHT_DEFAULT'	=> $row['iprbl_weight_default'],
					'IPRBL_COUNT'			=> $row['iprbl_count'],

					'U_EDIT'				=> $url . '&amp;action=edit',
					'U_DELETE'				=> $url . '&amp;action=delete',
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
* Get iprbl details
*/
function get_iprbl_info($iprbl_id)
{
	global $db;
		$sql = 'SELECT *
		FROM ' . IPRBL_TABLE . '
		WHERE iprbl_id = ' . (int) $iprbl_id;
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	return $row;
}

/**
* Update iprbl data
*/
function update_iprbl_data(&$iprbl_data)
{
	global $db, $user, $cache;

	$errors = array();

	if (!$iprbl_data['iprbl_fqdn'])
	{
		$errors[] = $user->lang['IPRBL_FQDN_EMPTY'];
	}

	// Unset data that are not database fields
	$iprbl_data_sql = $iprbl_data;

	// What are we going to do tonight Brain? The same thing we do everynight,
	// try to take over the world ... or decide whether to continue update
	// and if so, whether which groups and users we have to remove from which tables
	if (sizeof($errors))
	{
		return $errors;
	}

	if (!isset($iprbl_data_sql['iprbl_id']))
	{
		// no iprbl_id means we're creating a new iprbl
		unset($iprbl_data_sql['type_action']);

		$sql = 'INSERT INTO ' . IPRBL_TABLE . ' ' . $db->sql_build_array('INSERT', $iprbl_data_sql);
		$db->sql_query($sql);

		$iprbl_data['iprbl_id'] = $db->sql_nextid();

		add_log('admin', 'LOG_IPRBL_ADD', $iprbl_data['iprbl_fqdn']);
	}
	else
	{
		// Setting the iprbl id to the iprbl id is not really received well by some dbs. ;)
		$iprbl_id = $iprbl_data_sql['iprbl_id'];
		unset($iprbl_data_sql['iprbl_id']);

		$sql = 'UPDATE ' . IPRBL_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $iprbl_data_sql) . '
			WHERE iprbl_id = ' . (int) $iprbl_id;
		$db->sql_query($sql);

		add_log('admin', 'LOG_IPRBL_EDIT', $iprbl_data['iprbl_fqdn']);
	}

	return $errors;
}

/**
* Remove complete iprbl
*/
function delete_iprbl($iprbl_id)
{
	global $db, $user, $cache;

	$sql = 'SELECT iprbl_fqdn FROM ' . IPRBL_TABLE . '
		WHERE iprbl_id= ' . (int) $iprbl_id;
	$result = $db->sql_query($sql);
	$iprbl_data = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$errors = array();

	$sql = 'DELETE FROM ' . IPRBL_TABLE . '
		WHERE iprbl_id = ' . (int) $iprbl_id;
	$db->sql_query($sql);

	add_log('admin', 'LOG_IPRBL_DELETE', $iprbl_data['iprbl_fqdn']);

	return $errors;
}

/**
* check FQDN for a valid format
* based on http://blogchuck.googlecode.com/svn/trunk/domains.php
*/
function check_fqdn($fqdn)
{
	// domain name length
	if(strlen($fqdn) > 256 or strlen($fqdn) < 4)
	{
		// FQDN too long or too short
		return false;
	}
	
	// domain name must contain at least two dots
	if(substr_count($fqdn, '.') < 2)
	{
		// FQDN too long or too short
		return false;
	}
	
	// check to see if this might be an IP address
	if(ip2long($fqdn))
	{
		// is IP
		return true;
	}
	else
	{
		// split on each . to get the nodes
		$nodes = split('\.', $fqdn);
		
		// process each node
		foreach($nodes as $node)
		{
			// each node is limited to 63 characters
			if(strlen($node) > 63)
			{
				//node too long
				return false;
			}
			
			// each node is limited to specific characters and structure
			if(!preg_match('/^[a-z\d]*(?:([a-z\d-]*[a-z\d]))$/i', $node))
			{
				//node contains invalid characters
				return false;
			}
		}
		
		// made it this far, it must be valid
		return true;
	}
}

?>