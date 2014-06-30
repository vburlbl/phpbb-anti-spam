<?php
/**
*
* @package acp
* @version $Id: acp_domainrbl.php,v 1.001 2012/11/14 Martin Truckenbrodt Exp $
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
class acp_domainrbl
{
	var $u_action;

	function main($id, $mode)
	{
		global $db, $user, $auth, $template, $cache;

		$user->add_lang('mods/abm');

		$this->tpl_name = 'acp_domainrbl';
		$this->page_title = 'ACP_DOMAINRBL';

		$form_key = 'acp_domainrbl';
		add_form_key($form_key);

		// Check the permission setting again
		if (!$auth->acl_get('a_board'))
		{
			trigger_error($user->lang['NO_AUTH_OPERATION'] . adm_back_link($this->u_action), E_USER_WARNING);
		}

		$action		= request_var('action', '');
		$update		= (isset($_POST['update'])) ? true : false;
		$domainrbl_id	= request_var('d', 0);

		$domainrbl_data = $errors = array();
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

					$errors = delete_domainrbl($domainrbl_id);

					if (sizeof($errors))
					{
						break;
					}

					$auth->acl_clear_prefetch();
					$cache->destroy('sql', DOMAINRBL_TABLE);

					trigger_error($user->lang['DOMAINRBL_DELETED'] . adm_back_link($this->u_action));

				break;

				case 'edit':
					$domainrbl_data = array(
						'domainrbl_id'		=>	$domainrbl_id
					);

					if (isset($_POST['domainrbl_reset']))
					{
						$sql = 'UPDATE ' . DOMAINRBL_TABLE . '
							SET domainrbl_count = 0 
							WHERE domainrbl_id = ' . (int) $domainrbl_id;
						$db->sql_query($sql);
					}

				// No break here

				case 'add':

					$domainrbl_data += array(
						'domainrbl_fqdn'		=> utf8_normalize_nfc(request_var('domainrbl_fqdn', '', true)),
						'domainrbl_lookup'		=> utf8_normalize_nfc(request_var('domainrbl_lookup', '', true)),
						'domainrbl_weight'		=> request_var('domainrbl_weight', 0),
					);

					if (check_fqdn($domainrbl_data['domainrbl_fqdn']) === false)
					{
						trigger_error($user->lang['DOMAINRBL_FQDN_NOT_VALID'] . adm_back_link($this->u_action), E_USER_WARNING);
					}
					else
					{
						$errors = update_domainrbl_data($domainrbl_data);
					}

					trigger_error($user->lang['DOMAINRBL_ADDED_EDITED'] . adm_back_link($this->u_action));

				break;
			}
		}

		switch ($action)
		{
			case 'add':
			case 'edit':

				// Show form to create/modify a domainrbl
				if ($action == 'edit')
				{
					$this->page_title = 'DOMAINRBL_EDIT';
					$row = get_domainrbl_info($domainrbl_id);

					if (!$update)
					{
						$domainrbl_data = $row;
					}
				}
				else
				{
					$this->page_title = 'DOMAINRBL_CREATE';

					// Fill domainrbl data with default values
					if (!$update)
					{
						$domainrbl_data = array(
							'domainrbl_fqdn'		=> utf8_normalize_nfc(request_var('domainrbl_fqdn', '', true)),
							'domainrbl_lookup'		=> '',
							'domainrbl_weight'		=> 0,
							'domainrbl_count'		=> 0,
						);
					}
				}

				$domainrbl_weight_options = '';
				$domainrbl_weight_ary = array(WEIGHT_ZERO, WEIGHT_ONE, WEIGHT_TWO, WEIGHT_THREE, WEIGHT_FOUR, WEIGHT_FIVE);

				foreach ($domainrbl_weight_ary as $value)
				{
					$domainrbl_weight_options .= '<option value="' . $value . '"' . (($value == $domainrbl_data['domainrbl_weight']) ? ' selected="selected"' : '') . '>' . $value . '</option>';
				}

				$template->assign_vars(array(
					'S_DOMAINRBL_EDIT'				=> true,
					'DOMAINRBL_FQDN'				=> $domainrbl_data['domainrbl_fqdn'],
					'DOMAINRBL_LOOKUP'				=> $domainrbl_data['domainrbl_lookup'],
					'DOMAINRBL_WEIGHT_DEFAULT'		=> (isset($domainrbl_data['domainrbl_weight_default'])) ? $domainrbl_data['domainrbl_weight_default'] : 0,
					'DOMAINRBL_COUNT'				=> $domainrbl_data['domainrbl_count'],
					'S_DOMAINRBL_WEIGHT_OPTIONS'	=> $domainrbl_weight_options,
					'S_ERROR'					=> (sizeof($errors)) ? true : false,
					'S_ADD_ACTION'				=> ($action == 'add') ? true : false,
					'U_BACK'					=> $this->u_action,
					'U_EDIT_ACTION'				=> $this->u_action . "&amp;action=$action&amp;d=$domainrbl_id",
					'L_TITLE'					=> $user->lang[$this->page_title],
					'ERROR_MSG'					=> (sizeof($errors)) ? implode('<br />', $errors) : '',
					'S_DNS_A_RECORD'			=> (phpbb_checkdnsrr($domainrbl_data['domainrbl_fqdn'], 'A')) ? true : false,
				));

				return;

			break;

			case 'delete':

				if (!$domainrbl_id)
				{
					trigger_error($user->lang['NO_DOMAINRBL'] . adm_back_link($this->u_action), E_USER_WARNING);
				}

				$sql = 'SELECT domainrbl_fqdn FROM ' . DOMAINRBL_TABLE . '
					WHERE domainrbl_id= ' . (int) $domainrbl_id;
				$result = $db->sql_query($sql);
				$domainrbl_data = $db->sql_fetchrow($result);
				$db->sql_freeresult($result);

				$template->assign_vars(array(
					'S_DOMAINRBL_DELETE'	=> true,
					'U_ACTION'			=> $this->u_action . "&amp;action=delete&amp;d=$domainrbl_id",
					'U_BACK'			=> $this->u_action,
					'DOMAINRBL_FQDN'		=> $domainrbl_data['domainrbl_fqdn'],
					'S_ERROR'			=> (sizeof($errors)) ? true : false,
					'ERROR_MSG'			=> (sizeof($errors)) ? implode('<br />', $errors) : '')
				);

				return;

			break;
		}

		$sql = 'SELECT domainrbl_id, domainrbl_fqdn, domainrbl_weight, domainrbl_weight_default, domainrbl_count FROM ' . DOMAINRBL_TABLE . '
			ORDER BY domainrbl_weight DESC, domainrbl_count DESC, domainrbl_fqdn ASC';
		$result = $db->sql_query($sql);

		if ($row = $db->sql_fetchrow($result))
		{
			do
			{
				$url = $this->u_action . "&amp;d={$row['domainrbl_id']}";

				$template->assign_block_vars('domainrbl', array(
					'DOMAINRBL_FQDN'			=> $row['domainrbl_fqdn'],
					'DOMAINRBL_WEIGHT'			=> $row['domainrbl_weight'],
					'DOMAINRBL_WEIGHT_DEFAULT'	=> $row['domainrbl_weight_default'],
					'DOMAINRBL_COUNT'			=> $row['domainrbl_count'],

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
* Get domainrbl details
*/
function get_domainrbl_info($domainrbl_id)
{
	global $db;
		$sql = 'SELECT *
		FROM ' . DOMAINRBL_TABLE . '
		WHERE domainrbl_id = ' . (int) $domainrbl_id;
	$result = $db->sql_query($sql);
	$row = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	return $row;
}

/**
* Update domainrbl data
*/
function update_domainrbl_data(&$domainrbl_data)
{
	global $db, $user, $cache;

	$errors = array();

	if (!$domainrbl_data['domainrbl_fqdn'])
	{
		$errors[] = $user->lang['DOMAINRBL_FQDN_EMPTY'];
	}

	// Unset data that are not database fields
	$domainrbl_data_sql = $domainrbl_data;

	// What are we going to do tonight Brain? The same thing we do everynight,
	// try to take over the world ... or decide whether to continue update
	// and if so, whether which groups and users we have to remove from which tables
	if (sizeof($errors))
	{
		return $errors;
	}

	if (!isset($domainrbl_data_sql['domainrbl_id']))
	{
		// no domainrbl_id means we're creating a new domainrbl
		unset($domainrbl_data_sql['type_action']);

		$sql = 'INSERT INTO ' . DOMAINRBL_TABLE . ' ' . $db->sql_build_array('INSERT', $domainrbl_data_sql);
		$db->sql_query($sql);

		$domainrbl_data['domainrbl_id'] = $db->sql_nextid();

		add_log('admin', 'LOG_DOMAINRBL_ADD', $domainrbl_data['domainrbl_fqdn']);
	}
	else
	{
		// Setting the domainrbl id to the domainrbl id is not really received well by some dbs. ;)
		$domainrbl_id = $domainrbl_data_sql['domainrbl_id'];
		unset($domainrbl_data_sql['domainrbl_id']);

		$sql = 'UPDATE ' . DOMAINRBL_TABLE . '
			SET ' . $db->sql_build_array('UPDATE', $domainrbl_data_sql) . '
			WHERE domainrbl_id = ' . (int) $domainrbl_id;
		$db->sql_query($sql);

		add_log('admin', 'LOG_DOMAINRBL_EDIT', $domainrbl_data['domainrbl_fqdn']);
	}

	return $errors;
}

/**
* Remove complete domainrbl
*/
function delete_domainrbl($domainrbl_id)
{
	global $db, $user, $cache;

	$sql = 'SELECT domainrbl_fqdn FROM ' . DOMAINRBL_TABLE . '
		WHERE domainrbl_id= ' . (int) $domainrbl_id;
	$result = $db->sql_query($sql);
	$domainrbl_data = $db->sql_fetchrow($result);
	$db->sql_freeresult($result);

	$errors = array();

	$sql = 'DELETE FROM ' . DOMAINRBL_TABLE . '
		WHERE domainrbl_id = ' . (int) $domainrbl_id;
	$db->sql_query($sql);

	add_log('admin', 'LOG_DOMAINRBL_DELETE', $domainrbl_data['domainrbl_fqdn']);

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