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

$un = $_SESSION['login']['username'];
$uid = $_SESSION['login']['id'];
$img_path = IMAGE_PATH."profile_bg_candidates/";
$imgroot = "http://images.hamsterpaj.net/";
$imgadress = $imgroot."profile_bg_candidates/".$uid.".png";
$style_path = IMAGE_PATH."profile_bg_candidates/styles/";
$styleadress = "http://images.hamsterpaj.net/profile_bg_candidates/styles/$uid.css";


$submit_path = IMAGE_PATH."profile_bg_candidates/submits/";
$submitadress = $imgroot."profile_bg_candidates/submits/";

echo "<style>";
include "/mnt/images/profile_bg_candidates/styles/testenv.css.joar.php";
echo "</style>";

// Userlevel restriction
if (is_privilegied('profile_theme_admin'))
{
	$go = true;
}


//$query = "SELECT `is_admin` FROM profile_bg_candidates_admins WHERE `user_id` = '".$uid."'";
//$result = mysql_query($query) or die(mysql_error());
//
//while($row = mysql_fetch_array($result))
//	{
//	if ($row["is_admin"] == 1)
//		{
//			$go = true;
//		}
//	}	


// USER ECHO
if ($go)
	{
	if ($_GET["showall"] == "yes")
	{
		include "/storage/www/www.hamsterpaj.net/data/profilteman/ptadmin_content_all.php";
	}
	else
	{
		include "/storage/www/www.hamsterpaj.net/data/profilteman/ptadmin_content_01.php";
	}
}
//$output = htmlspecialchars($output);
//echo "<br />".$imgadress.$styleadress;
else
{
	$output .= rounded_corners_top(array('color' => 'orange_deluxe'), true);
	$out = 'Den h&auml;r sidan &auml;r tyvv&auml;r bara tillg&auml;nglig f&ouml;r anv&auml;ndare med speciella r&auml;ttigheter, men om du vill s&aring; kan du kolla vad som h&auml;nder hos <a href="/traffa/profile.php?user_id=3">Johan</a>.';
	$out = 
	$output .= $out;
	$output .= rounded_corners_bottom(array('color' => 'orange_deluxe'), true);
}


echo $output;

ui_bottom();
?>