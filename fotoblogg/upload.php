<?php
	if(isset($_POST['PHPSESSID']) && preg_match('/^([a-z0-9]+)$/', $_POST['PHPSESSID']) && $_POST['PHPSESSID'] != session_id())
	{
		session_destroy();
		session_id($_POST['PHPSESSID']);
		session_start();
	}
	
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'photoblog.lib.php');
	
	try
	{
		if(is_uploaded_file($_FILES['Filedata']['tmp_name']))
		{
			if(login_checklogin())
			{
				unset($options);
				$options['file_temp_path'] = $_FILES['Filedata']['tmp_name'];
				$options['user'] = $_SESSION['login']['id'];
				
				if(ENVIRONMENT == 'production')
				{
					$photo_id = photoblog_upload_upload($options);
					echo $photo_id;
				}
				else
				{
					throw new Exception('Upload successfull, but cannot save in development environment.');
				}
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
	}
?>