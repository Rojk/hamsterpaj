<?php
	require('../include/core/common.php');

	$ui_options['menu_path'] = array('traeffa', 'soek');	
	$ui_options['stylesheets'][] = 'search.lef.css';
	
	if(login_checklogin())
	{
		$query = 'SELECT login.username, login.id AS userid, login.lastaction, userinfo.gender, userinfo.birthday, userinfo.image, z.spot AS geo_location FROM userinfo, login, zip_codes AS z WHERE userinfo.userid = login.id AND login.is_removed = 0 AND z.zip_code = userinfo.zip_code ';
		
		$startage = (date('Y') - (date_get_age($_SESSION['userinfo']['birthday']) - 2)) . date('-m-d');
		$agequery_start = ' userinfo.birthday <= "' . $startage . '"';
		
		$endage = (date('Y') - (date_get_age($_SESSION['userinfo']['birthday']))) . date('-m-d');
		$agequery_end = ' userinfo.birthday >= "' . $endage . '"';
		
		$query .= 'AND' . $agequery_start . ' AND ' . $agequery_end . 'AND userinfo.birthday <> 0000-00-00';
		
		$query .= ' AND userinfo.gender != "' . $_SESSION['userinfo']['gender'] . '"';		
		$query .= ' AND userinfo.gender != "u"';	
		$query .= ' AND userinfo.gender != ""';	
		
		$query .= ' AND (userinfo.image = 1 OR userinfo.image = 2)';				
		
		$query .= ' ORDER BY login.lastaction DESC LIMIT 0, 16';

		$result = mysql_query($query) or die(report_sql_error($query));
	

	// Let's show the result to the user 
		$out .=  '<h2>Resultat</h2>';
		if (mysql_num_rows($result) > 0) 
		{
			$out .= 'Hittade en hel bunt med folk som passade in på din sökning! <br /><br />';
		
			while ($data = mysql_fetch_assoc($result))
			{
				if($data['gender'] == 'f')
				{
					$out .= '<div class="search_userbox girl">';
				}
				elseif($data['gender'] == 'm')
				{
					$out .= '<div class="search_userbox boy">';
				}
				else
				{
					$out .= '<div class="search_userbox">';
				}
				$out .= ui_avatar($data['userid']);
				$out .= '<br />' . "\n";
				$out .= '<a href="/traffa/profile.php?user_id=' . $data['userid'] . '">' . $data['username'] . '  ' . $data['gender'] . date_get_age($data['birthday']) . '</a>' . "\n";
				$out .= '</div>' . "\n";
			}	
		}
		else
		{
				$out .= 'Tyvärr hittades ingen som matchade din sökning :(';
		}
	}
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>
