<?php

	require('../include/core/common.php');
	$ui_options['menu_path'] = array('traeffa', 'soek');
	$ui_options['dom_tt_lib'] = true;
	$ui_options['stylesheets'][] = 'domTT.css';
	$faq_category = 'traffa_search';

	$ui_options['current_submenu'] = 'Leta';
	ui_top($ui_options);
	echo '<div style="overflow: hidden; width: 637px;">';

	echo rounded_corners_top(array('color' => 'blue_deluxe'), true);
function traffa_make_search()
{

	$query = 'SELECT login.username, login.id AS userid, login.lastaction, userinfo.gender, userinfo.birthday, userinfo.image, z.spot AS geo_location FROM userinfo, login, zip_codes AS z WHERE userinfo.userid = login.id AND login.is_removed = 0 AND z.zip_code = userinfo.zip_code ';


	if (isset($_POST['username']))
	{
		$query .= ' AND login.username LIKE "%' . $_POST['username'] . '%" ';
	}
	
	if(strlen($_POST['geo_location']) > 0)
	{
		$query .= ' AND z.spot LIKE "' . $_POST['geo_location'] . '%" ';
	}

	if (($_POST['start_year'] <= $_POST['end_year']) || ($_POST['end_year'] == 0 && $_POST['start_year'] != 0) && is_numeric($_POST['start_year']) && is_numeric($_POST['end_year']))
	{
	
		if ($_POST['start_year'] > 0 ) 
		{
			$startage = (date('Y') - $_POST['start_year']) . date('-m-d');
			$agequery_start = ' userinfo.birthday <= "' . $startage . '"';
		}

		if ($_POST['end_year'] > 0)
		{
			$endage = (date('Y') - $_POST['end_year']-1) . date('-m-d');
			$agequery_end = ' userinfo.birthday >= "' . $endage . '"';
		}
	
		if (isset($agequery_start) && isset($agequery_end)) 
		{
			$query .= 'AND' . $agequery_start . ' AND ' . $agequery_end . 'AND userinfo.birthday <> 0000-00-00';
		}
		else if (isset($agequery_start))
		{
			$query .= 'AND' . $agequery_start  . 'AND userinfo.birthday <> 0000-00-00';
		}
		else if (isset($agequery_end))
		{	
			$query .= 'AND' . $agequery_end  . 'AND userinfo.birthday <> 0000-00-00';
		}
	}
	
	if (isset($_POST['gender']) && ($_POST['gender'] == 'F' || $_POST['gender'] == 'M' || $_POST['gender'] == 'null')) 
	{
		if ($_POST['gender'] != 'null')
		{
			$query .= ' AND userinfo.gender = "' . $_POST['gender'] . '"';			
		}
	}

	if (isset($_POST['image'])) 
	{
		if ($_POST['image'] == 'yes')
		{
			$query .= ' AND (userinfo.image = 1 OR userinfo.image = 2)';			
		}
		if ($_POST['image'] == 'no')
		{
			$query .= ' AND (userinfo.image = 0 OR userinfo.image = 3 OR userinfo.image = 4)';			
		}
	}

	$query .= ' ORDER BY login.lastaction DESC LIMIT 0, 65';

	$result = mysql_query($query) or die(report_sql_error($query));

	/* Let's show the result to the user */
	echo '<h2>Resultat</h2>';
	if (mysql_num_rows($result) > 0) 
	{
		echo 'Hittade en hel bunt med folk som passade in på din sökning! <br /><br />';
		echo '<table border="0" style="width: 100%; margin-bottom: 3px;" class="body" cellspacing="0">';
  	echo '<tr><td><b>Användare</b></td><td>&nbsp;</td><td>Ort</td></tr>';	
	
		while ($data = mysql_fetch_assoc($result))
		{
			if($background == '#e7e7e7')
    	{
  		  $background = '#ffffff';
    	}
    	else
    	{
    		$background = '#e7e7e7';
    	}

			$tooltip = '<b>' . $data['username'] . '</b>';
			if($data['image'] == 1 || $data['image'] == 2)
			{
				$tooltip .= '<br /><img src=' . IMAGE_URL . 'images/users/thumb/' . $data['userid'] . '.jpg />';
			}
			if($data['gender'] == 'P')
			{
				$tooltip .= '<br />Kön: kille';
			}
			elseif($data['gender'] == 'F')
			{
				$tooltip .= '<br />Kön: tjej';
			}
			if(isset($data['birthday']) && $data['birthday'] != '0000-00-00')
			{
				$tooltip .= '<br />Ålder: ' . date_get_age($data['birthday']) . 'år';
			}
			if(strlen($data['geo_location']) > 1)
			{
				$tooltip .= '<br />Bor: ' . $data['geo_location'];
			}
			
			echo '<tr style="background: ' . $background . ';">';
			echo '<td>';
			/*if ($data['image'] == 1 || $data['image'] == 2)
			{
				echo '<img src="/images/icons/photo.png" />';
			}*/
			echo '<a href="/traffa/profile.php?id=' . $data['userid'] . '" onmouseover="return makeTrue(domTT_activate(this, event, \'content\', \'' . $tooltip . '\', \'trail\', true));">' . $data['username'] . '</a> ' . $data['gender'] . date_get_age($data['birthday']) . '</td>';
			if ($data['lastaction'] > time() - 900)
			{
				echo '<td style="color: green; font-style: italic;">Online</td>';
			}
			else
			{
				echo '<td>&nbsp;</td>';
			}
			echo '<td>' . $data['geo_location'] . '</td>';
			echo '</tr>';
		}
		echo '</table>';
	}	
	else
	{
		echo 'Tyvärr hittades ingen som matchade din sökning :(';
	}
}

function traffa_show_search($post_startage = 0, $post_endage = 0, $post_imgstatus = 'all', $post_gender = 'null') 
{
	global   $personalities;

	$genders = array(
		'null'	=> 'Båda',
		'M'		=> 'Pojkar',
		'F'		=> 'Flickor'
	);

	$imgstatus = array(
		'all'	=> 'Alla',
		'yes'		=> 'Med bild',
		'no'		=> 'Utan bild'
	);
	
	echo '<h1 style="margin-top: 0px; padding-top: 5px">Leta</h1>';

  if(isset($_GET['notfound']))
  {
    echo '<span style="font-weight: bold; color: red">Användaren du sökte efter kunde inte hittas, använd denna, lite mer avancerade sökningen</span>';
  }

	//echo '<div class="grey_faded_div" style="float: left;">';
	echo '<h2>Sökalternativ</h2>';
	echo '<form name="optional_info" action="' . $_SERVER['PHP_SELF'] . '?search" method="post">';
		echo 'Användarnamn: <input type="text" name="username" />';

		/* Some other stuff like gender, age */
  //	echo '<div style="width: 50%; float: left;">';
  	echo '<p>';
  	echo 'Kön:<br/>';
  	echo '<select name="gender" class="textbox">';
		foreach($genders as $value => $gender) 
		{
			if ($value == $post_gender)
			{
					echo '<option value="' . $value . '" selected>' . $gender . '</option>';
			}
			else
			{	
				echo '<option value="' . $value . '">' . $gender . '</option>';
			}
		}	
 	  echo '</select><br/>';
  	echo '</p>';
  	echo '<p>';
  	echo 'Ålder:<br/>';
		echo '<select name="start_year" class="textbox">';
		$i = 1;
		echo '<option value="0">Från</option>';
		while($i < 100) 
		{
			if ($i == $post_startage)
			{
				echo '<option value="' . $i . '" selected>' . $i . '</option>';
				$i++;
			}
			else
			{
				echo '<option value="' . $i . '">' . $i . '</option>';
				$i++;
			}
		}
		echo '</select>';
		echo '&nbsp;- <select name="end_year" class="textbox">';
		$i = 1;
		echo '<option value="0">Till</option>';
		while($i < 100) 
		{
			if ($i == $post_endage)
			{
				echo '<option value="' . $i . '" selected>' . $i . '</option>';
			}
			else
			{
				echo '<option value="' . $i . '">' . $i . '</option>';
			}
			$i++;
		}
	  echo '</select><br />';
		echo '</p>';
	
  	echo '<p>';
  	echo 'Bild: <br />';
  	echo '<select name="image" class="textbox">';

 		foreach($imgstatus as $imgstatus_value => $imgstatus_text) 
		{
   		if ($imgstatus_value == $post_imgstatus)
			{
				echo '<option value="' . $imgstatus_value . '" selected>' . $imgstatus_text . '</option>';
			}
			else
			{
				echo '<option value="' . $imgstatus_value . '">' . $imgstatus_text . '</option>';
			}
 		}
  	echo '</select>';
  	echo '</p>';

		echo '<h5>Ort</h5>' . "\n";
		echo '<input type="text" name="geo_location" />' . "\n";

  	echo '<input name="submit" type="submit" value="Sök!" class="button_50" />';
	//  echo '</div>';

  	echo '</form>';
	//echo '</div>';
}


/* Main page */
	if (isset($_GET['search']))
	{
		traffa_make_search();
		traffa_show_search($_POST['start_year'], $_POST['end_year'], $_POST['image'], $_POST['gender']);

	}	
	else
	{
		traffa_show_search();
	}

?>

<?php

	echo rounded_corners_bottom(array('color' => 'blue_deluxe'), true);
	echo '</div>';
	ui_bottom();
?>
