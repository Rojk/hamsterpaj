<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/photoblog.lib.php');
	
	if(isset($_GET['PHPSESSID']))
	{
		session_id($_GET['PHPSESSID']);
	}
	
	try
	{
		if(is_uploaded_file($_FILES['Filedata']['tmp_name']))
		{
			if(login_checklogin())
			{
				if(!isset($_GET['upload_id']))
				{
					throw new Exception('Invalid upload ticket.');
				}
				if(!isset($_GET['current_file_id']))
				{
					throw new Exception('Invalid upload ticket.');
				}
				
				if(!isset($_SESSION['photoblog']['upload']['upload_tickets'][$_GET['upload_id']]))
				{
					throw new Exception('Invalid upload ticket.');
				}
				
				unset($options);
				$options['file_temp_path'] = $_FILES['Filedata']['tmp_name'];
				$options['user'] = $_SESSION['login']['id'];
				
				$photo_id = photoblog_upload_upload($options);
				
				$_SESSION['photoblog']['upload']['upload_tickets'][$_GET['upload_id']][$_GET['current_file_id']] = $photo_id;
			}
			else
			{
				throw new Exception('You must be logged in to use this service.');
			}
		}
		else
		{
			throw new Exception('Hakker där...');
		}
	}
	catch(Exception $error)
	{
		header('"HTTP/1.0 500 Internal Server Error');
		echo $error;
		trace('photoblog_upload_error', $error . "\n" . print_r($_GET, true) . "\n'" . session_id() . "'\n" . print_r($_SESSION, true));
	}
?>