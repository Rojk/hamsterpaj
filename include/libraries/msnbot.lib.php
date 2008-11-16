<?php
	msnbot_is_valid_msn($msn)
	{
		return preg_match('/$([a-zA-Z0-9_\.-]+)@([a-zA-Z0-9_\.-]+)\.([a-z]+)^/', $msn);
	}
	
	msnbot_is_valid_salt($options)
	{
		if(!defined('MSNBOT_SALT'))
		{
			throw new Exception('MSNBOT_SALT constant is not defined. This script can only be run from dynamic01.');
		}
		return $options['salt'] == sha1(MSNBOT_SALT . md5(strrev(MSNBOT_SALT) . $passport_username));
	}
?>