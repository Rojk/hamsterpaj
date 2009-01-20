<?php
	/* OPEN_SOURCE */
	
	define('EMOLAND', 'off');
	
	// Debug, $victims[] = <secret>; ;)
	
	if (is_array($_SESSION) && array_key_exists('login', $_SESSION))
	{
		// Do everything that has to do with $_SESSION
	}
	
	//session_start();

	/* To ip ban user: Use /admin/ip_ban_admin.php */
	
	// Se /storage/www/ip_handling.php

	
	function insert_avatar($userid, $imgextra = NULL)
	{
		global $hp_url;
		$output = '<a href="javascript:;" onclick="window.open(\'' . $hp_url . 'avatar.php?id=' . $userid . '\',\'' . rand() . '\',\'toolbar=no, location=no, directories=no, status=no, menubar=no, scrollbars=no, resizable=no, copyhistory=no, width=410, height=600\')">';
		
		$output .= '<img src="' . IMAGE_URL . 'images/users/thumb/' . $userid . '.jpg?' . filemtime(PATHS_IMAGES . 'users/thumb/' . $userid . '.jpg');
		
		$output .= '" border="0" width="75" height="100" ';
		if (isset($imgextra) && preg_match("/alt/i",$imgextra)) {
			$output .= $imgextra;
		}
		else {
			$output .= 'alt="" '. $imgextra;
		}
		$output .= '/>';
		$output .= '</a>';
		return $output;
	}
?>
