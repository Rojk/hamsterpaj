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

	function ui_dropbox($title, $data, $styleinfo, $expanded = NULL)
	{
		$rand = rand(100000, 999999);
		$return = '<div';
		if (array_key_exists('class', $styleinfo))
		{
			$return.= ' class="' . $styleinfo['class'] . '"';
		}
		if (array_key_exists('style', $styleinfo))
		{
			$return.= ' style="' . $styleinfo['style'] . '"';
		}
		$return.= '>' . "\n";
		$return.= '<div class="droptitle" onclick="collapse_expand(\'' . $rand . '\');">' . "\n";
	  	$return.= '<h2 style="margin-top: 0;">' . $title . '</h2>' . "\n";
//		$return.= '<img class="dropimage" id="dropbox_image_' . $rand . '" src="http://images.hamsterpaj.net/famfamfam_icons/bullet_toggle_';
//		if (isset($expanded)) {
//			$return.= 'minus';
//		}
//		else
//			{
//			$return.= 'plus';
//		}
//		$return.= '.png" alt="" />' . "\n";
		$return.= '</div>' . "\n";
		$return.= '<div id="dropbox_' . $rand . '"';
		if (!isset($expanded))
		{
			$return.= ' style="display: none;"';
		}
		$return.= '>' . "\n";
		$return.= $data;
		$return.= '</div>' . "\n";
		$return.= '</div>' . "\n";
		return $return;
	}
?>
