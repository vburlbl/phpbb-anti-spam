<?php
/**
*
* @package acp
* @version $Id: abm_check_version.php 1.001 2012-12-24 Martin Truckenbrodt $
* @copyright (c) 2005 phpBB Group
* @license http://opensource.org/licenses/gpl-license.php GNU Public License
*
*/

if (!defined('IN_PHPBB'))
{
	exit;
}

/**
* @package mod_version_check
*/
class abm_check_version
{
	function version()
	{
		return array(
			'author'	=> 'Martin Truckenbrodt',
			'title'		=> 'Advanced Block MOD',
			'tag'		=> 'abm',
			'version'	=> '1.1.4',
			'file'		=> array('martin-truckenbrodt.com', 'phpbb3mods', 'abm.xml'),
		);
	}
}

?>