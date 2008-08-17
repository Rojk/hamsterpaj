<?php
	require('../include/core/common.php');
	
	/*$action = isset($_GET['action']) ? $_GET['action'] : '';
	$value = isset($_GET['value']) ? $_GET['value'] : '';*/
	$value_isset = (isset($_GET['value'])) ? true : false;
	$value = $_GET['value'];
	$action = $_GET['action'];
	
	if (!is_privilegied('use_handy_tools'))
	{
		//die('Det där är att gå över gränsen.');
	}
	
	
	elseif($type == 'ip2host' && $value_isset)
	{
		if (ereg('^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}(/[0-9]{1,2}){0,1}$', $value))
		{
			$out .= gethostbyaddr($value);
		}
		else
		{
			$out .= 'Det där var tydligen ingen IP-adress.';
		}
	}
	
	elseif($action == 'ip2long' && $value_isset)
	{
		if (ereg('^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}(/[0-9]{1,2}){0,1}$', $value))
		{
			$out .= ip2long($value);
		}
		else
		{
			$out .= 'Det där var tydligen ingen IP-adress.';
		}
	}
	
	elseif($action == 'long2ip' && $value_isset)
	{
		if (is_numeric($value))
		{
			$out .= long2ip($value);
		}
		else
		{
			$out .= 'En \'long\' måste bestå av nummer.';
		}
	}
	
	elseif($action == 'serialize2preint_r' && $value_isset)
	{
		$out .= preint_r(unserialize(stripslashes($value)));
	}
	
	elseif($action == 'md5' && $value_isset)
	{
		$out .= md5($value);
	}
	
	elseif ($action == 'sha1' && $value_isset)
	{
		$out .= sha1($value);
	}
	
	elseif ($action == 'hamsterpaj_password_hash' && $value_isset)
	{
		$out .= sha1(utf8_decode($value) . PASSWORD_SALT);
	}
	
	elseif ($action == 'timestamp2readable' && $value_isset && is_numeric($value))
	{
		$out .= date('Y-m-d H:i:s', $value);
	}
	
	elseif ($action == 'base64encode' && $value_isset)
	{
		$out .= base64_encode($value);
	}
	
	elseif ($action == 'base64decode' && $value_isset)
	{
		$out .= base64_decode($value);
	}
	
	echo utf8_encode($out);
	//echo $out;

?>