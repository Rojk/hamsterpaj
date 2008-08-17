<?
	require('../include/core/common.php');
	$ui_options['stylesheets'][] = 'traffa_index.css';
	$ui_options['stylesheets'][] = '/profilteman_testmiljo/joar_lef_style.css';
	$ui_options['title'] = 'Profilteman på Hamsterpaj.net';
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
$imgroot = "http://images.hamsterpaj.net/";
$imgadress = $imgroot."profile_bg_candidates/".$uid.".png";
$style_path = IMAGE_PATH."profile_bg_candidates/styles/";
$styleadress = "http://images.hamsterpaj.net/profile_bg_candidates/styles/$uid.css";

$uid = $_SESSION['login']['id'];

if (is_privilegied('profile_theme_admin') && isset($_GET["fake_id"]))
{
		$uid = $_GET["fake_id"];
}


if (isset($_POST["css"]))
	{
		$css = $_POST["css"];
		$handle = fopen($style_path.$uid.".css", "w+");
		fwrite($handle, $css);
		fclose($handle);
	}
else
	{
		$filename = $style_path.$uid.".css";
		$handle = fopen($filename, "r");
		$css = fread($handle, filesize($filename));
		fclose($handle);
	}
	
echo '<style type="text/css">';

include "/mnt/images/profile_bg_candidates/styles/testenv.css.joar.php";
echo $css;

echo '</style>';
	

echo '<span class="rubrik">Testmiljö för profilteman BETA,</span><br />';

if (!file_exists(IMAGE_PATH."profile_bg_candidates/".$uid.".png"))
{
$output .= rounded_corners_top(array('color' => 'orange_deluxe'), true);

$output .= '
<div class="imgdiv">
<img src="http://images.hamsterpaj.net/profile_themes_testenv/profthem2.png" style="align: left;" />
</div>
<div class="textdiv">Här kan ni ladda upp era profilteman för test.<br />
<b>OBS!!! Man kan bara ha ett profiltema uppe i taget</b>
</div>';

$output .= rounded_corners_bottom(array('color' => 'orange_deluxe'), true);
}
if ($ul > 0)
{
	if (file_exists(IMAGE_PATH."profile_bg_candidates/".$uid.".png"))
	{
		
		
		$output .= rounded_corners_top(array('color' => 'blue_deluxe'), true);
		$output .= '
		<b>När du tycker att du är färdig med ditt tema så kan du skicka in det för validering och bedömning.</b><br />
		<a href="/profilteman/ptsubmit.php">Skicka in ditt tema.</a>';
		$output .= rounded_corners_bottom(array('color' => 'blue_deluxe'), true);
		
$out .= '
<div class="profile_head">
<div class="avatar_passepartout">
<img src="http://images.hamsterpaj.net/images/users/thumb/3" class="user_avatar" />
</div>
<div class="name_asl">
<span class="username">Johan</span> <span class="gender">kille</span> <span class="age">19</span> <span class="spot">Göteborg
</span> <span class="online">online</span>

</div>
<p class="user_status">Användarstatus</p>
<ul class="user_action_log">
<li><span class="time">Ig&aring;r 18:44</span> skrev i dagboken <a href="">Test</a></li>
<li><span class="time">Ig&aring;r 18:41</span> blev kompis med <a href="">Sig sj&auml;lv</a></li>
<li><span class="time">I Fredags 21:02</span> skrev i dagboken <a href="">testitestitest</a></li>

</ul>
<div class="navigation">
<ul>
<li><a href="">Presentation</a></li>
<li><a href="">Gästbok</a></li>
<li><a href="">Gammal presentation</a></li>
<li><a href="">Fotoalbum</a></li>
<li><a href="">Dagbok</a></li>
<li><a href="">Vänner</a></li>
<li><a href="">Fakta</a></li>
<li><a href="">Besökare</a></li>
</ul>
</div>
</div>';
		
		$output .= $out;
		
		$output .= '<b>Bildadress:</b><br />
		<a href=\"'.$imgadress.'\">'.$imgadress.'</a><br/>';

if (is_privilegied('profile_theme_admin') && isset($_GET["fake_id"]))
{
		$output .= '<b><a href="/profilteman/ptunlink.php?fake_id='.$_GET["fake_id"].'">Ta bort bilden</a></b><br />';
}
else
{
	$output .= '<b><a href="/profilteman/ptunlink.php">Ta bort bilden</a></b><br />';
}
		
		$output .= '<br />
		<h1>CSS-editor</h1>';
			
if (is_privilegied('profile_theme_admin') && isset($_GET["fake_id"]))
{
		$output .= '<form action="/profilteman/?fake_id='.$_GET["fake_id"].'" method="post">';
}
else
{
		$output .= '<form action="/profilteman/" method="post">';
}		
		$output .= '<textarea name="css" class="css">'.$css.'</textarea>
		<input type="submit" value="Uppdatera CSS" /></form>
		CSS:<a href="'.$styleadress.'">'.$styleadress.'</a><br />
		
		<b>så här ser strukturen ut.</b>
		<textarea class="css" readonly="readonly">
		'.$out.'
		</textarea>';
		
	}
	else
	{		
		// Echo upload form
		if ($_SESSION['login']['username'] != 'Borttagen')
		{
			
if (is_privilegied('profile_theme_admin') && isset($_GET["fake_id"]))
{
		$output .= '<form action="/profilteman/ptaction.php?fake_id='.$uid.'" method="post" enctype="multipart/form-data">';
}
else
{
	$output .= '<form action="/profilteman/ptaction.php" method="post" enctype="multipart/form-data">';
}
			$output .= '
			<input type="file" name="file" id="file" />
			<input type="submit" value="Ladda upp" />
			</form>
			<b>Du har inte laddat upp något tema ännu</b>';
		}
	}
echo $output;
}
if (is_privilegied('profile_theme_admin'))
{
	$output = rounded_corners_top(array('color' => 'blue_deluxe'), true);
	$output .= '<a href="/profilteman/ptadmin.php">Till administratörssidan.</a>';
	$output .= rounded_corners_bottom(array('color' => 'blue_deluxe'), true);
	echo $output;
}
ui_bottom();
?>