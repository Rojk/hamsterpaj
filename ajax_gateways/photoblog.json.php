<?php
	try
	{
		require('../include/core/common.php');
		require(PATHS_LIBRARIES . 'photoblog.lib.php');
		
		if(isset($_GET['action']))
		{
			$action = $_GET['action'];
		}
		else
		{
			throw new Exception('No action in get data recieved');
		}
		
		switch($action)
		{
			case 'photo_fetch':
				if(!isset($_GET['id']) || !is_numeric($_GET['id']))
			    {
			    	throw new Exception('No ID or faulty ID recieved');
			    }
				
				// fetch a single image
			    if(!isset($_GET['month']) )
			    {
			        $options['id'] = $_GET['id'];
					friends_notices_set_read(array('action' => 'photos', 'item_id' => $_GET['id']));
			    }
			    // fetch an entire month
			    else
			    {
			        if(!is_numeric($_GET['month']))
			        {
			            throw new Exception('Month not numerical.');
			        }
			        
			        $options['user'] = $_GET['id'];
					$options['month'] = $_GET['month'];
			    }
			    $options['order-by'] = 'up.date';
			    $photo = photoblog_photos_fetch($options);
			    echo json_encode($photo);
			break;
			
			case 'comments_fetch':
				if(!isset($_GET['id']) || !is_numeric($_GET['id']))
			    {
			    	throw new Exception('No Photo-ID or faulty ID recieved');
			    }
				
				$options['photo_id'] = $_GET['id'];
	            $comments = photoblog_comments_fetch($options);
	            $options['use_container'] = false;
	            echo photoblog_comments_list($comments, $options);
			break;
			
			case 'comments_post':
				if(!isset($_GET['id']) || !is_numeric($_GET['id']))
			    {
			    	throw new Exception('No Photo-ID or faulty ID recieved');
			    }
				if(!login_checklogin())
	            {
	            	throw new Exception('Only users can post comments.');
	            }
				
	            $options['photo_id'] = $_GET['id'];
				$options['comment'] = $_POST['comment'];
				$options['author'] = $_SESSION['login']['id'];
	            photoblog_comments_add($options);
			break;
			
			case 'calendar_render':
				if (!isset($_GET['user_id'], $_GET['month'], $_GET['year']))
			    {
			        throw new Exception('No input.');
			    }
			    if (!is_numeric($_GET['user_id']) || !is_numeric($_GET['month']) || !is_numeric($_GET['year']))
			    {
			        throw new Exception('Not numerical input.');
			    }
			    
			    echo photoblog_calendar($_GET['user_id'], $_GET['month'], $_GET['year']);
			break;
			
			default:
				throw new Exception('Action not found');
			break;
		}
	}
	catch (Exception $error)
	{
		$options['type'] = 'error';
    	$options['title'] = 'Nu blev det fel här';
   		$options['message'] = $error -> getMessage();
    	$options['collapse_link'] = 'Visa felsökningsinformation';
   		$options['collapse_information'] = preint_r($error, true);
    	$out .= ui_server_message($options);
		preint_r($error);
	}
?>