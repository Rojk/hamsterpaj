<?php
// This is a part of the comments system for Hamsterpaj.net.
// dependencies: rank (library and db-table)


require_once(PATHS_INCLUDE . 'libraries/rank.lib.php');

function comments_input_draw($item_id, $item_type)
{
	if(login_checklogin())
	{
		$query = 'SELECT comment FROM user_comments' .
					' WHERE user_id = "' . $_SESSION['login']['id'] . '" AND item_id = "' . $item_id . '" AND item_type = "' . $item_type . '" AND removed = 0';
		$result = mysql_query($query);
		if(mysql_num_rows($result) == 1)
		{
			$data = mysql_fetch_assoc($result);
		}
		$output .= '<div class="comment_input_container">' . "\n";
		$output .= '<input type="text" class="comment_input_text" id="comment_input_text" value="' . $data['comment'] . '" name="comment" maxlength="150" />' . "\n";
		$output .= '<input type="hidden" id="comment_item_id" value="' . $item_id . '">' . "\n";
		$output .= '<input type="hidden" id="comment_item_type" value="' . $item_type . '">' . "\n";
		$output .= '<input id="comment_submit" type="button" class="button_50" onclick="comment_submit_click()" value="Skicka" />' . "\n";
		$output .= '</div>' . "\n";
	}
	else
	{
		$output .= '<div class="comment_input_container">' . "\n";
		$output .= '<input onclick="javascript: tiny_reg_form_show();" type="text" class="comment_input_text" id="comment_input_text" value="' . $data['comment'] . '" name="comment" maxlength="150" />' . "\n";
		$output .= '<input id="comment_submit_not_logged_in" onclick="javascript: tiny_reg_form_show();" class="button_50" value="Skicka" />' . "\n";
		$output .= '</div>' . "\n";
	}
	return $output;
}

/**
 *	The default behavior is to list the four latest comments. The selection and number of 
 *	comments can be changed thru settings in $options
	item_id
	item_type
 	options			array_support	description
 	user_id				yes				include these users comments first in the array
 	limit_offset		no
 	limit				no
 */
function comments_list($item_id, $item_type, $options)
{
	log_to_file('comments', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'comments_list', $item_type . ' ' . $item_id);

	$query = 'SELECT c.id, c.user_id, c.timestamp, c.comment, c.answer, l.username, lo.username AS answerer_username, u.image, u.gender, u.birthday, ur.rank, u.image FROM login l, userinfo u, user_comments c';
	$query .= ' LEFT OUTER JOIN user_ranks ur';
	$query .= ' ON ur.user_id = c.user_id';
	$query .= ' AND ur.item_id="' . $item_id . '" AND ur.item_type="' . $item_type . '"';
	$query .= ' LEFT OUTER JOIN login lo';
	$query .= ' ON lo.id = c.answerer_id';
	$query .= ' WHERE c.user_id = l.id AND u.userid = l.id AND c.item_type="' . $item_type . '" AND c.item_id="' . $item_id . '" AND c.item_type="' . $item_type . '" AND removed="0"';
	$query .= ' ORDER BY c.timestamp DESC';
	if(isset($options['limit']) && $options['limit'] != 'no_limit')
	{
		$query .= ' LIMIT ' . $options['limit'];
		if(isset($options['limit_offset']))
		{
			$query .= ', ' . $options['limit_offset'];
		}
	}
	elseif($options['limit'] != 'no_limit')
	{
		$query .= ' LIMIT 6';
	}
	
	$options['style'] = isset($options['style']) ? $options['style'] : 'normal';
	$result = mysql_query($query) or die(report_sql_error($query));
	if($options['list_style'] == 'compact')
	{
		$output = '<div style="width: 575px; height: 590px; scroll: auto; overflow: auto;" id="comments_list" class="comments_list_compact">' . "\n";
		$output .= '<dl>' . "\n";
		while($data = mysql_fetch_assoc($result))
		{
			$output .= '<dt>' . $data['username'] . '</dt>' . "\n";
			$output .= '<dd style="margin-left: 70px;">' . $data['comment'] . '</dd>' . "\n";
		}
		$output .= '</dl>' . "\n";
	}
	else
	{
		$output = '<div id="comments_list" class="comments_list">' . "\n";
		while($data = mysql_fetch_assoc($result))
		{
			$output .= '<div class="comment_' . $options['style'] . '" id="comment_'.$data['id'].'">' . "\n";
			if($options['style'] == 'normal')
			{
				if($data['image'] == 1 || $data['image'] == 2)
				{
					$output .= '<img class="user_avatar" src="http://images.hamsterpaj.net/images/users/thumb/' . $data['user_id'] . '.jpg" />' . "\n";
				}
				else
				{
					$output .= '<div class="user_avatar"></div>' . "\n";
				}
			}
			$output .= '<div class="comment_main_';
			if(($data['image'] == 1 || $data['image'] == 2) && $options['style'] == 'normal')
			{
				$output .= 'normal';
			}
			else
			{
				$output .= $options['style'];
			}
			$output .= '">' . "\n";
			if(isset($data['rank']))
			{
				$output .= rank_draw($data['rank']);
			}
			
			$output .= '<div class="comment_author_name"><a href="/traffa/profile.php?id=' . $data['user_id'] . '">' . $data['username'] . '</a> ';
			$genders = array('m' => 'P', 'f' => 'F');
			$output .= $genders[$data['gender']];
			$output .= (date_get_age($data['birthday']) > 0) ? date_get_age($data['birthday']) : '';
			$output .= '</div>' . "\n";
			$output .= '<span class="comment_time">(' . fix_time($data['timestamp']) . ')';

			if(login_checklogin() && ($_SESSION['login']['id'] == $data['user_id'] || is_privilegied('comments_admin') || $_SESSION['login']['id'] == $options['photo_owner']))
			{
				$output .= ' <strong><a href="#img_full" onclick="comment_remove('.$data['id'].')">[X]</a></strong>' . "\n";
			}

			$output .= '</span><p class="comment_text">' . $data['comment'] . '</p>' . "\n";
			// Shows a answer
			if(strlen($data['answer']) > 1)
			{
				$output .= '<span class="comment_answerer">' . $data['answerer_username'] . '\'s svar:</span> <p class="comment_answer">' . $data['answer'] . '</p>' . "\n";
			}
			elseif($_SESSION['login']['id'] == $options['photo_owner'] && login_checklogin()) // Write a answer (for the owner)
			{
				$output .= '<button type="submit" onclick="comment_answer(' . $data['id'] . ', ' . $item_id . ')" class="button_60">Svara</button>' . "\n";
			}
			$output .= '</div>' . "\n";
			$output .= '</div>' . "\n";
		}
		$output .= '<br style="clear: both" />' . "\n";
		if(mysql_num_rows($result) == 6)
		{
			$output .= '<button class="button_150" id="comments_view_all_button">Visa alla kommentarer</button>' . "\n";
		}
	}
	$output .= '</div>' . "\n"; // comments_list
	return $output;
}

function comments_new($item_id, $item_type, $user_id, $comment)
{
	$content_check = content_check($comment);
	if($content_check === 1)
	{
		$insertquery = 'INSERT INTO user_comments (item_type, item_id, timestamp, user_id, comment) VALUES ("';
		$insertquery .= $item_type . '", "';
		$insertquery .= $item_id . '", "';
		$insertquery .= time() . '", "';
		$insertquery .= $user_id . '", "';
		$insertquery .= $comment . '")';
	
		$updatequery = 'UPDATE user_comments SET' .
						' timestamp = "' . time() . '", ' .
						' comment = "' . $comment . '",' .
						' removed = 0 '.
						' WHERE item_type = "' . $item_type . '" ' .
							' AND item_id = "' . $item_id . '"' .
							' AND user_id = "' . $user_id . '"' .
							' LIMIT 1';
		log_to_file('comments', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'new_comment', $insertquery);
		log_to_file('comments', LOGLEVEL_DEBUG, __FILE__, __LINE__, 'new_comment', $updatequery);

		if(mysql_query($insertquery))
		{
			$query = 'UPDATE item_ranks SET comment_count = comment_count + 1 WHERE item_type = "' . $item_type . '" ' .
							' AND item_id = "' . $item_id . '"';
		}
		else
		{
			mysql_query($updatequery);
		}
		
		if($item_type == 'photos')
		{
			$query = 'UPDATE user_photos SET unread_comments = unread_comments + 1 WHERE id = "' . $item_id . '" LIMIT 1';
			mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		}
		
	}
	else
	{
		die('<p style="margin: 0px;">' . $content_check . '</p>');
	}
}

function comments_remove($id)
{
	$query = 'SELECT user_id, item_type, item_id FROM user_comments WHERE id = '.$id.' LIMIT 1';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

	if(mysql_num_rows($result) > 0)
	{
		$data = mysql_fetch_assoc($result);
	
		if($data['item_type'] == 'photos')
		{
			$photos = photos_fetch(array('id'=> $data['item_id'], 'limit' => 1));
			$photo_owner = $photos[0]['user'];

			if(login_checklogin() 
			&& ($_SESSION['login']['id'] == $data['user_id'] 
				|| $_SESSION['login']['id'] == $photo_owner
				|| is_privilegied('comments_admin') 
				)
			)
			{
				$query = 'UPDATE user_comments SET removed=1 WHERE id = ' . $id . ' LIMIT 1';
				mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			}
			else
			{
				die('FEL @ '.__FILE__.' #'.__LINE__);
			}
		}
		else
		{
			if(login_checklogin() 
				&& ($_SESSION['login']['id'] == $data['user_id'] 
				|| is_privilegied('comments_admin')
				)
			)
			{
				$query = 'UPDATE user_comments SET removed=1 WHERE id = ' . $id . ' LIMIT 1';
				mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			}
		}
	}
}

function comment_answer($id, $reply)
{
	$query = 'SELECT up.user, up.description, up.id, uc.user_id, uc.comment, l.username FROM user_photos AS up, user_comments AS uc, login AS l WHERE l.id = ' . $_SESSION['login']['id'] . ' AND uc.item_id = up.id AND up.user = ' . $_SESSION['login']['id'] . ' AND uc.id = ' . $id . '';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	$data = mysql_fetch_assoc($result);
	
	if($data['user'] == $_SESSION['login']['id'])
	{
		$query = 'UPDATE user_comments SET answer = "' . $reply . '", answerer_id = ' . $_SESSION['login']['id'] . ' WHERE id = ' . $id . ' LIMIT 1';
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		$entry['sender'] = $_SESSION['login']['id'];
		$message = $data['username'] . ' svarade precis p&aring; din kommentar till fotot: <br /><a href="/traffa/photos.php?id=' . $data['id'] . '#photo">' . ((strlen($data['description']) > 1) ? $data['description'] : 'namnl&ouml;s') . '</a>' . "\n\n";
		$message .= '<strong>Din kommentar:</strong>' . "\n";
		$message .= $data['comment'] . "\n\n";
		$message .= '<strong>' . $data['username'] . '\'s svar:</strong>' . "\n";
		$message .= $reply . "\n";
		$entry['message'] = mysql_real_escape_string($message);
		
		$entry['recipient'] = $data['user_id'];
		guestbook_insert($entry);	
	}
	else
	{
		jscript_alert('Nehejdu, den gick inte!');
	}
}
?>