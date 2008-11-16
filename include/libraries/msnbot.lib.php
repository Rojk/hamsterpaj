<?php
	function msnbot_is_valid_msn($msn)
	{
		return true;
	}
	
	function msnbot_is_valid_salt($options)
	{
		if(!defined('MSNBOT_SALT'))
		{
			die('MSNBOT_SALT constant is not defined. This script can only be run from dynamic01.');
		}
		return $options['salt'] == sha1(MSNBOT_SALT . md5(strrev(MSNBOT_SALT) . $options['msn']));
	}
?>