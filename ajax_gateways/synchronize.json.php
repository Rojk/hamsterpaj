<?php
	/*
		It is very important that output is properly escaped.
		If not, we may end upp with a XSS-attack.
		
		
		If you don't know what you're doing, HANDS OFF!!!
	
	
	*/

	require('../include/core/common.php');
	if(isset($_GET['fetch']) && !empty($_GET['fetch']))
	{
		$objects_to_fetch = explode(',', $_GET['fetch']);
		foreach($objects_to_fetch as $object_to_fetch)
		{
			if(in_array($object_to_fetch, array('ui_noticebar_guestbook', 'ui_noticebar_discussion_forum', 'ui_noticebar_groups')))
			{
				$notices = ui_notices_fetch();
			}
			switch($object_to_fetch)
			{				
				case 'ui_noticebar_guestbook':
					$data = $notices['guestbook'];
				break;
				
				case 'ui_noticebar_discussion_forum':
					$data = $notices['discussion_forum'];
				break;
				
				case 'ui_noticebar_groups':
					$data = $notices['groups'];
				break;
				default: continue 2;
			}
			
			$return[] = '{"' . $object_to_fetch . '": ' . $data . '}';
		}
		
		echo '[' . implode(', ', $return) . ']';
	}
	else
	{
		echo '[]';
	}
?>