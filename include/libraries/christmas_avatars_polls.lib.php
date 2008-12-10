<?php

	function christmas_avatar_recent_updates_check_new()
	{
		$query = 'SELECT poll_id, poll_title FROM christmas_avatars_polls WHERE is_in_recent_updates = 0';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

		if(mysql_num_rows($result) > 0)
		{
			while($data = mysql_fetch_assoc($result))
			{
				$label = $data['poll_title'];
				$url = '/traffa/omrostning_julavatarer.php#poll_'.$data['poll_id'];
				
				$query = 'INSERT INTO recent_updates(type, label, timestamp, url) VALUES(';
				$query .= '"christmas_avatar_poll"';
				$query .= ', "'.$label.'"';
				$query .= ', '.time();
				$query .= ', "'.$url.'"';
				$query .= ')';
				mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				
				$query = 'UPDATE christmas_avatars_polls SET is_in_recent_updates = 1 WHERE poll_id = '.$data['poll_id'];
				mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			}
		}
	}
	 
	function christmas_avatar_list_poll($options=null)
	{
		$options['poll_id'] = isset($options['poll_id']) && is_numeric($options['poll_id']) ? intval($options['poll_id']) : 0;
		$options['num_avatars'] = isset($options['num_avatars']) && is_numeric($options['num_avatars']) ? intval($options['num_avatars']) : 7;
		
		$output = '';
		
		$query = 'SELECT *';
		$query .= ' FROM christmas_avatars_polls';
		$query .= ' WHERE poll_id = '.$options['poll_id'];
		$query .= ' AND is_removed = 0';
		$query .= ' AND poll_publish <= '.time();
		$query .= ' LIMIT 1';
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		$data = mysql_fetch_assoc($result);

		if(mysql_num_rows($result) > 0)
		{
			if($data['poll_expire'] < time())
			{
				$output .= christmas_avatar_draw_result(array('poll_id'=>$options['poll_id']));
			}
			else
			{
				$user_has_voted = false;
				if(login_checklogin())
				{
					$query = 'SELECT NULL FROM christmas_avatars_votes WHERE poll_id = '.$options['poll_id'];
					$query .= ' AND voter = ';
					$query .= intval($_SESSION['login']['id']);			
					$query .= ' AND is_removed = 0';
					$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
					if(mysql_num_rows($result) == 0)
					{
						$user_has_voted = false;
					}
					else
					{
						$user_has_voted = true;
					}
				}
				
				if(!$user_has_voted)
				{
					/*
						If the user hasn't voted, show the form. Show from to non-logged in people
					*/
					$query = 'SELECT ca.contender, ca.id AS contender_id, l.username, u.gender, u.birthday FROM christmas_avatars_contenders AS ca, userinfo AS u, login AS l WHERE u.userid = ca.contender AND l.id = ca.contender AND l.is_removed = 0 AND ca.is_removed = 0 AND ca.parent_poll = '.$options['poll_id'];
					$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);

					$output .= '<h2 id="poll_'.$data['poll_id'].'">'.$data['poll_title'].'</h2>'."\n";				
					
					if(mysql_num_rows($result) > 0)
					{
						$output .= christmas_avatar_draw_poll($data, $result);
						if(!login_checklogin())
						{
							$output .= '<p><em>Resultaten visas om du är inloggad och har röstat.</em></p>'."\n";
						}
					}
					else
					{
						$output .= 'Hittade inga tomtar här!';
					}
				}
				else
				{
					$output .= christmas_avatar_draw_result(array('poll_id'=>$options['poll_id']));
				}
			}
		}
		return $output;
	}
	
	function christmas_avatar_draw_poll($poll, $result)
	{
		$output = '<form method="post" action="/ajax_gateways/christmas_avatars_poll.php?action=vote&poll_id='.$poll['poll_id'].'" class="christmas_avatar_poll" id="christmas_avatar_poll_'.$poll['poll_id'].'">'."\n";
			$output .= '<ul class="avatar_christmas_poll">'."\n";
				while($data = mysql_fetch_assoc($result))
				{
					$output .= "\t".'<li>'."\n";
							$output .= "\t".'<a href="/traffa/profile.php?user_id='.$data['contender'].'" title="Gå till '.rtrim($data['username'], 's').'s'.' profil!">'.$data['username'].'</a>'."\n";
							$genders = array('f' => 'F', 'm' => 'P', 'u' => '');
							$output .= "\t".$genders[$data['gender']];
							$output .= ' ';
							$output .= (date_get_age($data['birthday']) > 0) ? date_get_age($data['birthday']) : '';

							$output .= "\t".'<br />'."\n";
							$output .= "\t".ui_avatar($data['contender'], null)."\n";

							$output .= "\t".'<br />'."\n";
							$output .= 'Välj <input type="radio" value="'.$data['contender_id'].'" name="avatar_christmas_id" />'."\n";

					$output .= "\n"."\t".'</li>'."\n";
				}
			$output .= '</ul>'."\n";
			$output .= '<input type="submit" class="button_150" value="Rösta på bästa julavatar!" />'."\n";
		$output .= '</form>'."\n";
		return $output;
	}
	
	function christmas_avatar_draw_result($options)
	{
		$output = '';
		$poll_list = array();
		$total_votes = 0;
		
		$options['poll_id'] = isset($options['poll_id']) ? intval($options['poll_id']) : 0;
		
		$query_title = 'SELECT poll_title FROM christmas_avatars_polls WHERE poll_id = '.$options['poll_id'].' AND is_removed = 0 LIMIT 1';
		$result_title = mysql_query($query_title) or report_sql_error($query_title, __FILE__, __LINE__);
		$data_title = mysql_fetch_assoc($result_title);
		$output .= '<h2 id="poll_'.$options['poll_id'].'">Resultat för '.$data_title['poll_title'].'</h2>'."\n";
		
		//fetch contenders
		$query = 'SELECT c.id AS contender_id, c.contender AS contender_uid, l.username FROM christmas_avatars_contenders AS c, login AS l WHERE c.parent_poll = '.$options['poll_id'].' AND l.id = c.contender AND c.is_removed = 0';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		$number_of_contenders = mysql_num_rows($result);

		if($number_of_contenders > 0)
		{
			while($data = mysql_fetch_assoc($result))
			{
				$query_votes = 'SELECT NULL FROM christmas_avatars_votes AS v, christmas_avatars_contenders AS c WHERE v.poll_id = '.$options['poll_id'].' AND v.contender_id = '.$data['contender_id'].'';

				$result_votes = mysql_query($query_votes) or report_sql_error($query_votes, __FILE__, __LINE__);
				$contenders_votes = mysql_num_rows($result_votes);
				$poll_list[] = array('votes'=>$contenders_votes, 'contender'=>$data['contender_uid'], 'username'=>$data['username']);
				$total_votes += $contenders_votes;
			}

			if($total_votes > 0)
			{
				foreach($poll_list as $contender)
				{
					$per_cent = round(($contender['votes']/$total_votes)*100, 2);
					
					$output .= '<a href="/traffa/profile.php?user_id='.$contender['contender'].'">'.$contender['username'].'</a>'."\n";				
					$output .= '<br />'."\n";
					$output .= ui_avatar($contender['contender'], array('style'=>'height: 67;width: 50.25px'))."\n";
					$output .= '<div class="box">'."\n";
						$output .= '<div class="bar" title="'.$per_cent.'"></div>'."\n";
					$output .= '</div>'."\n";
					$output .= '<br />'."\n";
				}
			}
			else
			{
				$output .= 'Inga har röstat!';
			}
			
		}
		else
		{
			$output .= 'Inga deltagare!';
		}
		return $output;
	}
	
	function christmas_avatar_poll_vote($options)
	{
		if(!is_numeric($options['contender_id']))
		{
			return 'Fel datatyp på användarid:et. Avbryter.';
		}
		else
		{
			$contender_id = intval($options['contender_id']);
		}
		
		if(!is_numeric($options['poll_id']))
		{
			return 'Fel datatyp på poll_id. Avbryter.';
		}
		else
		{
			$poll_id = intval($options['poll_id']);
		}
		if(login_checklogin())
		{
		
			$query = 'SELECT NULL FROM christmas_avatars_votes WHERE poll_id = '.$poll_id;
			$query .= ' AND voter = '.intval($_SESSION['login']['id']);
			$query .= ' AND voter != 0 LIMIT 1';

			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
			if(mysql_num_rows($result) > 0)
			{
				return 'Du har redan röstat på den här omröstningen! Fy på dig! Inget fuskande!';
			}
		}

		$query = 'INSERT INTO christmas_avatars_votes(voter, contender_id, timestamp, poll_id) VALUES(';
		$query .= (login_checklogin() ? intval($_SESSION['login']['id']) : 0); //voter
		$query .= ', '.$contender_id;// user voted on this guy
		$query .= ', '.time();//timestamp
		$query .= ', '.$poll_id;//poll_id
		$query .= ')';
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		return '<span style="color:#FF0000;">Tack för din röst!</span>';
	}
	
	function christmas_avatar_current_polls_list($options=null)
	{
		$options['admin-links'] = isset($options['admin-links']) ? $options['admin-links'] : false;
		$output = '';
		
		$query = 'SELECT poll_id, poll_title, poll_expire FROM christmas_avatars_polls WHERE poll_expire >= '.time().' AND poll_publish <= '.time().' AND is_removed = 0  ORDER BY (poll_expire-'.time().') ASC';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		if(mysql_num_rows($result) > 0)
		{
			$output .= '<h2>Just nu pågår...</h2>'."\n";
			$output .= '<ul>'."\n";
			while($data = mysql_fetch_assoc($result))
			{
				$poll_expire_readable_date = date('d/m H:i', $data['poll_expire']);
				$output .= '<li><a href="';

				if($options['admin-links'])
				{
					$output .= '?action=edit&poll_id='.$data['poll_id'];
				}
				else
				{
					$output .= '#poll_'.$data['poll_id'];
				}
				
				$output .= '" title="Gå till omröstningen nedan">'.$data['poll_title'].' - går ut '.$poll_expire_readable_date.'</a></li>'."\n";
			}
			$output .= '</ul>'."\n";
		}
		else
		{
			$output .= '<h2>Inga omröstningar för tillfället!</h2>'."\n";
		}
		
		return $output;
	}
	
	function christmas_avatar_polls_list()
	{
		$output = '';
		
		$query = 'SELECT poll_id FROM christmas_avatars_polls WHERE is_removed = 0 AND poll_expire >= '.time().' ORDER BY (poll_expire-'.time().') ASC';
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		if(mysql_num_rows($result) > 0)
		{
			while($data = mysql_fetch_assoc($result))
			{
				$output .= christmas_avatar_list_poll(array('poll_id'=>$data['poll_id']));
			}
		}
		else
		{
			$output .= '<h2>Inga omröstningar för tillfället!</h2>'."\n";
		}
		
		return $output;
	}
	
	function christmas_avatar_results_list()
	{
		$output = '';
		
		$query = 'SELECT poll_id FROM christmas_avatars_polls WHERE is_removed = 0 AND poll_expire < '.time();
		
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		if(mysql_num_rows($result) > 0)
		{
			while($data = mysql_fetch_assoc($result))
			{
				$output .= christmas_avatar_draw_result(array('poll_id'=>$data['poll_id']));
			}
		}
		else
		{
			$output .= '<h2>Inga resultat för tillfället!</h2>'."\n";
		}
		return $output;
	}
	
	function christmas_avatar_admin_menu_list($action='home')
	{
		$menu = array();
		$menu[0]['href'] = '?action=home';
		$menu[0]['label'] = 'Start';
		$menu[1]['href'] = '?action=add';
		$menu[1]['label'] = 'Lägg till omröstning';
		$menu[2]['href'] = '?action=edit';
		$menu[2]['label'] = 'Redigera / ta bort';
  
		foreach($menu as $key=>$menu_item)
		{
			if($menu_item['href'] == '?action='.$action)
				$menu[$key]['current'] = TRUE;
		}
		
		$rounded_corners_tabs_options['tabs'] = $menu;
		$rounded_corners_tabs_options['color'] = 'blue';
		$output = rounded_corners_tabs_top($rounded_corners_tabs_options, true);
		return $output;
	}
	
	function christmas_avatar_admin_add()
	{
		$output = '<h1>Lägg till omröstning</h1>'."\n";
		
		$output .= '<form action="/ajax_gateways/christmas_avatars_poll.php?action=admin_add_vote" method="post" id="christmas_avatar_form">'."\n";
			$output .= '<h2>Allmän info om omröstningen</h2>'."\n";
			
			$output .= '<label for="poll_title">Titel (t.ex. "Kval 1"):</label> <input type="text" name="poll_title" id="poll_title" /><br />'."\n";
			$output .= '<label for="poll_publish">Publiseringsdatum:</label> <input type="text" value="'.date('Y/m/d H:i:s').'" name="poll_publish" id="poll_publish" /> (från när man ska kunna rösta. Får <strong>inte</strong> vara <strong>efter utgångsdatumet</strong>!)<br />'."\n";
			$output .= 'Släpp direkt: <input type="checkbox" name="release_immediately" checked="checked" /> - Just nu släpps allt direkt eftersom den schemalagda funktionen inte är klar än.<br /><br />'."\n";
			$output .= '<label for="poll_expire">Utgångsdatum:</label> <input type="text" value="'.date('Y/m/d H:i:s', time()+60*60*24*4).'" name="poll_expire" id="poll_expire" /> (Efter detta datum kan man inte rösta. Får <strong>inte</strong> vara <strong>före publiseringsdatumet</strong>!)<br />'."\n";
			
			$output .= '<h2>Deltagare (UID)</h2>'."\n";
			$output .= '<input type="hidden" id="number_of_contenders" />'."\n";
			$output .= '<p>Användarna måste anges som användarid (userid, UID). Det finns i länken till användarens presentation <a href="/traffa/profile.php?user_id=225454">http://www.hamsterpaj.net/traffa/profile.php?user_id=<strong>225454</strong></a> (länken går till wallys presenation).<br /><br />De <strong>kan inte ändras i efterhand</strong> eftersom rösterna skulle bli felberäknade då.</p>'."\n";
			$output .= '<ol id="contenders">'."\n";
			/*
			$output .= '<li id="contender_0">'."\n";
			$output .= '<input type="text" id="contender_input_0" name="contenders[]" /><img class="link_images" onclick="add_link();" src="http://links.guida.nu/img/add.png" alt="Add link" width="16" height="16" />'."\n";
			$output .= '</li>'."\n";
			*/
			for($i=0;$i<30;$i++)
			{
				$output .= '<li><input type="text" name="contender_'.$i.'" /></li>'."\n";
			}
			
			$output .= '</ol>'."\n";

			$output .= '<input type="submit" value="Lägg till omröstning!" class="button_130" />'."\n";
			$output .= '<div id="form_error" class="error"></div>';
			$output .= '</form>'."\n";
		
		$output .= '<div id="form_result"></div>'."\n";
		$output .= '<a id="show_form_again">Visa formulär igen</a>'."\n";

		return $output;
	}
	
	function christmas_avatar_admin_add_doadd($data)
	{
		if(empty($data['poll_title']))
		{
			return 'Titeln får inte vara tom!';
		}
		
		$data['contenders'] = array();
		
		for($i=0;$i<30;$i++)
		{
			if(isset($data['contender_'.$i]) && is_numeric($data['contender_'.$i]))
			{
				$data['contenders'][] = intval($data['contender_'.$i]);
			}

			unset($data['contender_'.$i]);
		}
		
		if(count($data['contenders']) < 2)
		{
			return 'Du måste ange några (mer än 1) deltagare! Hittade endast '.count($data['contenders']).'.';
		}

		if(empty($data['poll_publish']) && !isset($data['release_immediately']))
		{
			return 'Det finns inget utgivningsdatum!';
		}
		elseif(isset($data['release_immediately']))
		{
			//release now
			$publish_date = time();
			$publish_date_text = 'nu';
		}
		else
		{
			//release at publish date
			$publish_date = strtotime($data['poll_publish']);
			$publish_date_text = date('H:i \d\e\n d/m', $publish_date);
		}
		
		//release now because schedulesystem is not yet ready
		$publish_date = time();
		$publish_date_text = 'nu';
		
		if(!isset($data['poll_expire']) || empty($data['poll_expire']))
		{
			$expire_date = time()+60*60*24*4; //add four days if user hasn't specified other
		}
		else
		{
			$expire_date = strtotime($data['poll_expire']);
		}
		
		foreach($data['contenders'] as $contender)
		{
			if(is_numeric($contender))
			{
				$contenders[] = intval($contender);
			}
		}
		unset($contender);
		
		$query_poll = 'INSERT INTO christmas_avatars_polls(poll_title, poll_publish, poll_expire) VALUES(';
		$query_poll .= '"'.$data['poll_title'].'"';
		$query_poll .= ', '.$publish_date;
		$query_poll .= ', '.$expire_date;
		$query_poll .= ')';
		$result_poll = mysql_query($query_poll) or report_sql_error($query_poll, __FILE__, __LINE__);
		
		$poll_id = mysql_insert_id();
		
		foreach($contenders as $contender)
		{
			$query = 'INSERT INTO christmas_avatars_contenders(contender, parent_poll) VALUES(';
			$query .= $contender;
			$query .= ', '.$poll_id;
			$query .= ')';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		}

		return 'Tillagt! Den kommer att släppas '.$publish_date_text.'.';
	}
	
	function christmas_avatar_admin_edit_list()
	{
		$output = '';
		
		$query = 'SELECT * FROM christmas_avatars_polls WHERE is_removed = 0 ORDER BY poll_publish ASC';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		if(mysql_num_rows($result) > 0)
		{
			while($data = mysql_fetch_assoc($result))
			{
				$output .= '<h2>'.$data['poll_title'].'</h2>'."\n";
				$output .= '<a href="?action=edit&poll_id='.$data['poll_id'].'" title="Redigera omröstningen">Redigera</a> - <a href="?action=remove_poll&poll_id='.$data['poll_id'].'" onclick="return confirm(\'Är du säker på att du vill radera omröstningen?\')">Radera</a>'."\n";
			}
		}
		
		return $output;
	}
	
	function christmas_avatar_edit_poll($options)
	{
		$output = '';
		$options['poll_id'] = isset($options['poll_id']) ? intval($options['poll_id']) : 0;
		
		$query = 'SELECT * FROM christmas_avatars_polls WHERE poll_id = '.$options['poll_id'].' LIMIT 1';
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		
		if(mysql_num_rows($result) == 1)
		{
			$data = mysql_fetch_assoc($result);
			$output .= '<h1>Redigera omröstning</h1>'."\n";
			$output .= '<form action="/ajax_gateways/christmas_avatars_poll.php?action=admin_edit_poll&poll_id='.$options['poll_id'].'" method="post" id="christmas_avatar_form">'."\n";
				$output .= "<br />\n";
				$output .= 'Titel: <input type="text" name="poll_title" value="'.$data['poll_title'].'" />'."\n";
				$output .= "<br />\n";
				$output .= 'Publiseringsdatum: <input type="text" name="poll_publish" value="'.date('Y/m/d H:i', $data['poll_publish']).'" />'."\n";
				$output .= "<br />\n";
				$output .= 'Utgångsdatum: <input type="text" name="poll_expire" value="'.date('Y/m/d H:i', $data['poll_expire']).'" />'."\n";
				
				$query = 'SELECT c.*, l.username FROM christmas_avatars_contenders AS c, login AS l WHERE l.id = c.contender AND c.parent_poll = '.$options['poll_id'];
				$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				if(mysql_num_rows($result) > 0)
				{
					$output .= '<h2>Deltagare</h2>'."\n";
					$output .= '<ol id="contenders">'."\n";
					while($data = mysql_fetch_assoc($result))
					{
						$output .= '<li>';
						$output .= '<a href="/traffa/profile.php?user_id='.$data['contender'].'">'.$data['username'].'</a>'."\n";
					}
					$output .= '</ol>'."\n";
				}
				else
				{
					$output .= 'Det finns inga tomtar till denna omröstning!<br />'."\n";
				}
				$output .= '<input type="submit" value="Spara" class="button_50" />'."\n";
			$output .= '</form>'."\n";
			$output .= '<div id="form_result"></div>'."\n";
			$output .= '<a id="show_form_again">Visa formulär igen</a>'."\n";

		}
		else
		{
			$output .= $query;
			$output .= 'Kunde inte hitta omröstningen!'."\n";
		}
		
		return $output;
	}
	
	function christmas_avatar_admin_edit_doedit($options)
	{
		$output = '';

		if(!isset($options['poll_id']) || !is_numeric($options['poll_id']))
		{
			$output .= 'Poll_id är av fel datatyp.';
		}
		else
		{
			if(!isset($options['data']))
			{
				$output .= 'Det finns ingen data att uppdatera omröstningen med.';
			}
			else
			{
				$data = $options['data'];
				$poll_id = intval($options['poll_id']);
				//release at publish date
				$update_poll_title = empty($data['poll_title']) ? false : true;

				$publish_date = strtotime($data['poll_publish']);
				$expire_date = strtotime($data['poll_expire']);
								
				$query_poll = 'UPDATE christmas_avatars_polls SET';
				if($update_poll_title)
				{
					$query_poll .= ' poll_title = "'.$data['poll_title'].'"';
					$query_poll .= ', ';
				}
				$query_poll .= ' poll_publish = '.$publish_date;
				$query_poll .= ', poll_expire = '.$expire_date;
				$query_poll .= ' WHERE poll_id = '.$poll_id.' LIMIT 1';
				$result = mysql_query($query_poll) or report_sql_error($query_poll, __FILE__, __LINE__);
				
				$output .= '<h2 style="color: #408000;">Uppdaterat!</h2>'."\n";
			}
		}
		return $output;
	}

	function christmas_avatar_admin_remove_poll($poll_id)
	{
		if(!isset($poll_id) || !is_numeric($poll_id))
		{
			return 'Poll_id är inte av rätt datatyp!';
		}
		else
		{
			$query = 'UPDATE christmas_avatars_polls SET is_removed = 1 WHERE poll_id = '.$poll_id.' LIMIT 1';
			$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
			return '<h1>Omröstning borttagen!</h1>'."\n";
		}
	}
?>
