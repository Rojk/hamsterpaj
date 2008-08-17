<?php

	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/forum.php');
	
	// NOTE: if(false)! (This file is deprecated)
	
	if(false)
	{
		if($_GET['action'] == 'update_tags')
		{
			/* Remove all old tags */
			$tags = tag_get_by_item('discussion', $_GET['discussion_id']);
			foreach($tags AS $tag)
			{
				$tag_ids[] = $tag['tag_id'];
			}
			tag_remove($_GET['discussion_id'], 'discussion', $tag_ids);
			
			/* Set the new tags */
			$tags = explode(' ', str_replace(',', ' ', $_GET['tags']));
			foreach($tags AS $key => $tag)
			{
				$tags[$key] = trim($tag);
			}
			$tags = array_unique($tags);
			
			foreach($tags AS $tag_label)
			{
				$return = tag_exists($tag_label);
				if($return['status'] == 'exists')
				{
					$set_tag_ids[] = array('tag_id' => $return['tag_id']);
				}
				else
				{
					$set_tag_ids[] = array('tag_id' => tag_create($taglabel));
				}
			}
			
			tag_set($_GET['discussion_id'], 'discussion', $set_tag_ids);
			
			// Äntligen kan vi börja sätta de nya taggarna
		}

			
	}

?>