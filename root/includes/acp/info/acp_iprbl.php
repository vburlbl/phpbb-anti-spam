<?php
/**
*
* @package acp
* @version $Id$
* @copyright (c) 2012 Martin Truckenbrodt 
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

/**
* @package module_install
*/
class acp_iprbl_info
{
	function module()
	{
		return array(
			'filename'	=> 'acp_iprbl',
			'title'		=> 'ACP_IPRBL',
			'version'	=> '1.0.0',
			'modes'		=> array(
				'manage'		=> array('title' => 'ACP_IPRBL', 'auth' => 'acl_a_board', 'cat' => array('ACP_BLOCK_CONFIGURATION')),
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