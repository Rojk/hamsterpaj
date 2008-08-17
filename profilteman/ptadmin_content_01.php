<?
//  ORDER BY `timestamp` DESC LIMIT 100
	$view = $_POST["view"];
	$output .= '<h1>Inskickade teman</h1>';
	$output .= rounded_corners_top(array('color' => 'blue_deluxe'), true);
	
	$output .= '<form name="form" action="/profilteman/ptadmin.php" method="post">
	<select name="view" onchange="this.form.submit()">
	<option value="V&auml;lj anv&auml;ndare">V&auml;lj anv&auml;ndare</option>';
		$query = "SELECT user_id, theme_name, timestamp FROM profile_bg_candidates_submits WHERE `status` = '0' LIMIT 100";
		$options = mysql_query($query);
		while($row = mysql_fetch_array($options))
		{
			$tmpuser = $row["user_id"];
			$tmptheme = $row["theme_name"];
			$tmptime = $row["timestamp"];
			$output .= '<option value="'.$tmpuser.'|'.$tmptheme.'|'.$tmptime.'">'.$tmptheme.' - '.$tmpuser.'</option>';
		}
		
		$view_exploded = explode("|", $view);
		$view_id = $view_exploded[0];
		$view_theme = $view_exploded[1];
		$view_time = $view_exploded[2];
		
		$query = "SELECT username FROM login WHERE `id` = '".$view_id."' LIMIT 1";
		$result = mysql_query($query);
		while($row = mysql_fetch_array($result))
		{
			$view_un = $row["username"];
		}
		
	
	echo '<style type="text/css">';

	include "/mnt/images/profile_bg_candidates/styles/testenv.submits.css.joar.php";
	include $submit_path.$view_id.'.css';
	echo '.profile_head{background: url(\''.$submitadress.$view_id.'.png'.'\');}';

	echo '</style>';
		
	$output .= '</select>';
	
	if (isset($view))
		$output .= ' Just nu: '.$view_theme.' av '.$view_un.' - '.$view_id.'';
		
	$output .= '</form>';
	
	$output .= rounded_corners_bottom(array('color' => 'blue_deluxe'), true);
	
	
	if (isset($view_id))
	{
		
			
//			$output .= rounded_corners_top(array('color' => 'blue_deluxe'), true);
//			$output .= '<div class="name">'.$row["theme_name"].' Av '.$user.' - '.$row["user_id"].'</div><div style="margin-top: -14px; height: 14px;text-align: right; width: auto;">'.$row["status"].'</div>';
//			$output .= rounded_corners_bottom(array('color' => 'blue_deluxe'), true);
			if (isset($_POST["view"]))
			{
			$output .= '
			<div class="profile_head">
			<div class="avatar_passepartout">
			<img src="http://images.hamsterpaj.net/images/users/thumb/3" class="user_avatar" />
			</div>
			<div class="name_asl">
			<span class="username">Johan</span> <span class="gender">kille</span> <span class="age">19</span> <span class="spot">G&ouml;teborg
			</span> <span class="online">online</span>
			
			</div>
			<p class="user_status">Anv&auml;ndarstatus</p>
			<ul class="user_action_log">
			<li><span class="time">Ig&aring;r 18:44</span> skrev i dagboken <a href="">Test</a></li>
			<li><span class="time">Ig&aring;r 18:41</span> blev kompis med <a href="">Sig sj&auml;lv</a></li>
			<li><span class="time">I Fredags 21:02</span> skrev i dagboken <a href="">testitestitest</a></li>
			
			</ul>
			<div class="navigation">
			<ul>
			<li><a href="">Presentation</a></li>
			<li><a href="">G&auml;stbok</a></li>
			<li><a href="">Gammal presentation</a></li>
			<li><a href="">Fotoalbum</a></li>
			<li><a href="">Dagbok</a></li>
			<li><a href="">V&auml;nner</a></li>
			<li><a href="">Fakta</a></li>
			<li><a href="">Bes&ouml;kare</a></li>
			</ul>
			</div>
			</div>';

			$date = getdate($view_time);
			$output .= rounded_corners_top(array('color' => 'blue_deluxe'), true);
			
			$submit_date = $date[hours].':'.$date[minutes].' - '.$date[mday].' '.$date[month].' '.$date[year];
			
			
		//	$output .= rounded_corners_bottom(array('color' => 'blue_deluxe'), true);
		//	$output .= rounded_corners_top(array('color' => 'blue_deluxe'), true);
			
			$output .= '<div style="padding: 1px;"><div class="name">
			<a href="/profilteman/ptadmin.php?theme_user_id='.$view_id.'&setlvl=2">Godk&auml;nn</a>
			<a href="/profilteman/ptadmin.php?theme_user_id='.$view_id.'&setlvl=1">Avb&ouml;j</a>'.
			'</div><div style="margin-top: -14px; height: 14px;text-align: right; width: auto;">'.$submit_date.'</div></div>';
			
		
			
			$output .= rounded_corners_bottom(array('color' => 'blue_deluxe'), true);
			
			$output .= 'CSS: <a href="'.$submitadress.$view_id.'.css">'.$submitadress.$view_id.'.css</a><br />
			Bild: <a href="'.$submitadress.$view_id.'.png">'.$submitadress.$view_id.'.png</a>';
		
			}
			$output .= rounded_corners_top(array('color' => 'blue_deluxe'), true);
			$output .= '<a href="/profilteman/ptadmin.php?showall=yes">G&aring; till alla teman</a><br />';
			$output .= rounded_corners_bottom(array('color' => 'blue_deluxe'), true);
			$output .= rounded_corners_top(array('color' => 'blue_deluxe'), true);
			$output .= '<a href="/profilteman">G&aring; till huvudsidan</a>';
			$output .= rounded_corners_bottom(array('color' => 'blue_deluxe'), true);
			
			
			if (isset($_GET["setlvl"]) && isset($_GET["theme_user_id"]))
			{
				if ($_GET["setlvl"] == "1")
				{
					$query = 'DELETE FROM `profile_bg_candidates_submits` 
					WHERE `user_id` = \''.$_GET["theme_user_id"].'\'';
				}
				elseif ($_GET["setlvl"] == "2")
				{
					$query = 'UPDATE `profile_bg_candidates_submits` SET `status` = \'2\' WHERE `user_id` ='.$_GET["theme_user_id"];
				}
				mysql_query($query);
			}
		
	}
		?>