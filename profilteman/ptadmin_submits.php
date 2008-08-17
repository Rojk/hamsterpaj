<?
	require('../include/core/common.php');
	$ui_options['stylesheets'][] = 'traffa_index.css';
	$ui_options['stylesheets'][] = '/profilteman_testmiljo/joar_lef_style.css';
	$ui_options['title'] = 'Profilteman p&aring; Hamsterpaj.net';
	ui_top($ui_options);
	if ($_SESSION['login']['userlevel'] > 0)
	{
		$ul = $_SESSION['login']['userlevel'];
		$un = $_SESSION['login']['username'];
		$uid = $_SESSION['login']['id'];
	}

$ul = $_SESSION['login']['userlevel'];
$un = $_SESSION['login']['username'];
$uid = $_SESSION['login']['id'];
$img_path = IMAGE_PATH."profile_bg_candidates/";
$imgroot = "http://images.hamsterpaj.net/";
$imgadress = $imgroot."profile_bg_candidates/".$uid.".png";
$style_path = IMAGE_PATH."profile_bg_candidates/styles/";
$styleadress = "http://images.hamsterpaj.net/profile_bg_candidates/styles/$uid.css";

$submit_path = IMAGE_PATH."profile_bg_candidates/submits/";
$submitadress = $imgroot."profile_bg_candidates/submits/";

echo '<style type="text/css">';

include "/mnt/images/profile_bg_candidates/styles/testenv.submits.css.joar.php";
echo $css;

echo '</style>';

// Userlevel restriction
if (is_privilegied('profile_theme_admin'))
{
	$go = true;
}


// USER ECHO
if ($go)
{
	//  ORDER BY `timestamp` DESC LIMIT 100
	$query = "SELECT * FROM profile_bg_candidates_submits WHERE `status` = '0' ORDER BY `timestamp` DESC LIMIT 100";
	$submits = mysql_query($query);
	$output .= '<div class="theme_list">';
	while($row = mysql_fetch_array($submits))
		{
			$query2 = "SELECT username FROM login WHERE `id` = '".$row["user_id"]."' LIMIT 1";
			$username = mysql_query($query2);
			while($innerrow = mysql_fetch_array($username))
			{
				$user = $innerrow["username"];
			}
			$output .= rounded_corners_top(array('color' => 'orange_deluxe'), true);
			$output .= '<div class="name">'.$user.'</div><div style="margin-top: -14px; height: 14px;text-align: right; width: auto;">'.$row["status"].'</div>';
			$output .= rounded_corners_bottom(array('color' => 'orange_deluxe'), true);
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

			$date = getdate($row["timestamp"]);
			$output .= rounded_corners_top(array('color' => 'orange_deluxe'), true);
			$submit_date = $date[hours].':'.$date[minutes].' - '.$date[mday].' '.$date[month].' '.$date[year];
			$output .= 'CSS: <a href="'.$submitadress.$row["user_id"].'.css">'.$submitadress.$row["user_id"].'.css</a><br />
			Bild: <a href="'.$submitadress.$row["user_id"].'.png">'.$submitadress.$row["user_id"].'.png</a>
			<div class="name">'
			.'<a href="/profilteman/ptadmin_submits.php?theme_user_id='.$row["user_id"].'&setlvl=2">Godk&auml;nn</a>
			<a href="/profilteman/ptadmin_submits.php?theme_user_id='.$row["user_id"].'&setlvl=1">Avb&ouml;j</a>'.
			'</div><div style="margin-top: -14px; height: 14px;text-align: right; width: auto;">'.$submit_date.'</div>';
			$output .= rounded_corners_bottom(array('color' => 'orange_deluxe'), true);
			
		}
		$output .= '</div>';
	//SELECT `user_id`, `timestamp`, `status`, `theme_name` FROM `profile_bg_candidates_submits`
}
else
{
	$output .= rounded_corners_top(array('color' => 'orange_deluxe'), true);
	$output .= 'Den h&auml;r sidan &auml;r tyvv&auml;r bara tillg&auml;nglig f&ouml;r anv&auml;ndare med speciella r&auml;ttigheter, men om du vill s&aring; kan du kolla vad som h&auml;nder hos <a href="/traffa/profile.php?user_id=3">Johan</a>.';
	$output .= rounded_corners_bottom(array('color' => 'orange_deluxe'), true);
}


echo $output;

ui_bottom();
?>