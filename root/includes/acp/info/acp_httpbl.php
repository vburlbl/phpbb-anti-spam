<?php
/**
*
* @package acp
* @version $Id$
* @copyright (c) 2012 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package module_install
*/
class acp_httpbl_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_httpbl',
			'title'		=> 'ACP_HTTPBL',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'manage'		=> array('title' => 'ACP_HTTPBL', 'auth' => 'acl_a_board', 'cat' => array('ACP_BLOCK_CONFIGURATION')),
			),
		);
	}

	function install()
	{
	}

	function uninstall()
	{
	}
}

?>