<?php
	require('../include/core/common.php');

	$ui_options['current_menu'] = 'annat';
	$ui_options['title'] = 'Byz-intervjun på Hamsterpaj.net';

	ui_top($ui_options);
	
	if(login_checklogin())
	{
		echo 'Your RT90X: ' . $_SESSION['userinfo']['x_rt90'] . '<br />';
		echo 'Your RT90Y: ' . $_SESSION['userinfo']['y_rt90'] . '<br />';
	
		echo '<h2>Alpha, RT90 distances</h2>';
	
		
		$rt90_close_distance = 5000;
	
		$x_max = $_SESSION['userinfo']['x_rt90'] + $rt90_close_distance;
		$x_min = $_SESSION['userinfo']['x_rt90'] - $rt90_close_distance;
		$y_max = $_SESSION['userinfo']['y_rt90'] + $rt90_close_distance;
		$y_min = $_SESSION['userinfo']['y_rt90'] - $rt90_close_distance;
		
		$query = 'SELECT u.userid, l.username, z.x_rt90, z.y_rt90, u.gender, u.image, u.birthday, l.lastlogon FROM userinfo AS u, login AS l, zip_codes AS z WHERE l.is_removed = 0 ';
		$query .= 'AND l.id = u.userid AND z.zip_code = u.zip_code ';
		$query .= ' AND (z.x_rt90 > ' . $x_min . ' && z.x_rt90 < ' . $x_max . ') ';
		$query .= ' && (z.y_rt90 > ' . $y_min . ' && z.y_rt90 < ' . $y_max . ') LIMIT 100';
		
		echo $query;
		
		$result = mysql_query($query) or die(report_sql_error());
		while($data = mysql_fetch_assoc($result))
		{
			$map_points .= '<Point X=\'' . $data['y_rt90'] . '\' Y=\'' . $data['x_rt90'] . '\'>';
			$map_points .= '<Name>' . $data['username'] . '</Name>';
			$map_points .= '<IconImage>http://www.hitta.se/images/point.png</IconImage>';
			$map_points .= '<Content><![CDATA[' . $data['gender'] . ' ' . date_get_age($data['birthday']);
			if($data['image'] == 1 || $data['image'] == 2)
			{
				$map_points .= '<br /><img src=\'http://images.hamsterpaj.net/images/users/thumb/' . $data['userid'] . '.jpg\' />';
			}
			$map_points .= ']]></Content>';
			$map_points .= '</Point>';
		}
		
		echo '<form method="post" action="http://www.hitta.se/LargeMap.aspx">' . "\n";
		echo '<input type="hidden" name="MapPoints" value="<?xml version=\'1.0\' encoding=\'utf-8\'?><MapPoints xmlns=\'http://tempuri.org/XMLFile1.xsd\'>' . $map_points . '</MapPoints>">' . "\n";
	  echo '<input type="submit" value="Folk som bor nära dig" class="button">' . "\n";
		echo '</form>' . "\n";
	}
		
	$map_points = '';
	
	if(login_checklogin() && in_array($_SESSION['userinfo']['image'], array(1,2)) && $_SESSION['userinfo']['zip_code'] > 0 && in_array($_SESSION['userinfo']['gender'], array('P','F')))
	{
		$query = 'SELECT u.userid, l.username, z.x_rt90, z.y_rt90, u.gender, u.image, u.birthday, l.lastlogon ';
		$query .= 'FROM userinfo AS u, login AS l, zip_codes AS z ';
		$query .= 'WHERE l.username NOT LIKE "Borttagen" AND u.image IN (1,2) AND l.lastaction > ' . (time()-900) . ' AND u.zip_code > 0 ';
		$query .= ($_SESSION['userinfo']['gender'] == 'P') ? 'AND u.gender = "F" ' : 'AND u.gender = "P" ';
		$userage = date_get_age($_SESSION['userinfo']['birthday']);
		if($_SESSION['userinfo']['gender'] == 'P')
		{
			 $query .= 'AND DATE_SUB(CURDATE(),INTERVAL ' . ($userage+1) .' YEAR) <= u.birthday AND DATE_SUB(CURDATE(),INTERVAL ' . ($userage - 2) . ' YEAR) >= u.birthday ';
		}
		else
		{
			$query .= 'AND DATE_SUB(CURDATE(),INTERVAL ' . ($userage+2) .' YEAR) <= u.birthday AND DATE_SUB(CURDATE(),INTERVAL ' . ($userage - 1) . ' YEAR) >= u.birthday ';
		}
		$query .= 'AND l.id = u.userid AND z.zip_code = u.zip_code ';
		$query .= 'LIMIT 30';	
	
		$result = mysql_query($query) or die(report_sql_error());
		echo '<pre>';
		echo $query . '<br /><br />';
		while($data = mysql_fetch_assoc($result))
		{
			print_r($data);
			$map_points .= '<Point X=\'' . $data['y_rt90'] . '\' Y=\'' . $data['x_rt90'] . '\'>';
			$map_points .= '<Name>' . $data['username'] . '</Name>';
			$map_points .= '<IconImage>http://www.hitta.se/images/point.png</IconImage>';
			$map_points .= '<Content><![CDATA[' . $data['gender'] . ' ' . date_get_age($data['birthday']);
			if($data['image'] == 1 || $data['image'] == 2)
			{
				$map_points .= '<br /><a href=\'http://www.hamsterpaj.net/hittapunktse_map_link_redirect.php?id=' . $data['userid'] . '\'><img src=\'http://images.hamsterpaj.net/images/users/thumb/' . $data['userid'] . '.jpg\' /></a>';
			}
			if(login_checklogin())
			{
				$map_points .= '<br />' . rt90_readable(rt90_distance($_SESSION['userinfo']['x_rt90'], $_SESSION['userinfo']['y_rt90'], $data['x_rt90'], $data['y_rt90']));
			}
			$map_points .= ']]></Content>';
			$map_points .= '</Point>';
		}
		
		echo '<form method="post" action="http://www.hitta.se/LargeMap.aspx" target="hittapunktse" onsubmit="window.open(\'\', \'hittapunktse\', \'location=no, width=750, height=500\');">' . "\n";
		echo '<input type="hidden" name="MapPoints" value="<?xml version=\'1.0\' encoding=\'utf-8\'?><MapPoints xmlns=\'http://tempuri.org/XMLFile1.xsd\'>' . $map_points . '</MapPoints>">' . "\n";
	  echo '<input type="submit" value="Tjejer 15-19 år online" class="button">' . "\n";
		echo '</form>' . "\n";
	}
	
	ui_bottom();

?>


