<?php
	// People online, with maps 
	$output .= '<h2>Användare på karta</h2>' . "\n";
	foreach(array('f', 'm') AS $gender)
	{
		$query = 'SELECT l.id, l.username, u.gender, u.birthday, u.image, z.x_rt90, z.y_rt90';
		$query .= ' FROM login AS l, userinfo AS u, zip_codes AS z';
		$query .= ' WHERE u.userid = l.id AND z.zip_code = u.zip_code AND z.zip_code > 0 AND (u.image = 2 OR u.image = 1) AND u.gender = "%GENDER%" AND u.birthday < 1970';
		$query .= ' ORDER BY l.lastaction DESC LIMIT 40';

		$query = str_replace('%GENDER%', $gender, $query);
		
		$people = query_cache(array('query' => $query, 'max_delay' => 600));

		foreach($people AS $data)
		{
			$map_points .= '<Point X=\'' . $data['y_rt90'] . '\' Y=\'' . $data['x_rt90'] . '\'>';
			$map_points .= '<Name>' . $data['username'] . '</Name>';
			$map_points .= '<IconImage>http://www.hitta.se/images/point.png</IconImage>';
			$map_points .= '<Content><![CDATA[' . $data['gender'] . ' ' . date_get_age($data['birthday']);
			if($data['image'] == 1 || $data['image'] == 2)
			{
				$map_points .= '<br /><a href=\'http://www.hamsterpaj.net/hittapunktse_map_link_redirect.php?id=' . $data['id'] . '\'><img src=\'http://images.hamsterpaj.net/images/users/thumb/' . $data['id'] . '.jpg\' /></a>';
			}
			if(login_checklogin())
			{
				$map_points .= '<br />' . rt90_readable(rt90_distance($_SESSION['userinfo']['x_rt90'], $_SESSION['userinfo']['y_rt90'], $data['x_rt90'], $data['y_rt90']));
			}
			$map_points .= ']]></Content>';
			$map_points .= '</Point>';
		}
		
		$output .= '<form method="post" action="http://www.hitta.se/LargeMap.aspx" target="hittapunktse" onsubmit="window.open(\'\', \'hittapunktse\', \'location=no, width=750, height=500\');" style="display: block; margin: 0px; float: left;">' . "\n";
		$output .= '<input type="hidden" name="MapPoints" value="<?xml version=\'1.0\' encoding=\'utf-8\'?><MapPoints xmlns=\'http://tempuri.org/XMLFile1.xsd\'>' . $map_points . '</MapPoints>">' . "\n";

	  $display_gender = ($gender == 'm') ? 'Killar' : 'Tjejer';

	  if($_SESSION['userinfo']['gender'] == 'm')
		{
			$age_min = $age - 2;
			$age_max = $age + 1;
		}
		else
		{
			$age_min = $age - 1;
			$age_max = $age + 2;
		}
		$label = ($gender == 'f') ? 'Tjejer' : 'Killar';
	  $output .= '<input type="submit" value="' . $label . ' på karta" class="button_120" />&nbsp;&nbsp;' . "\n";
		$output .= '</form>' . "\n";
		unset($map_points);
	}

?>