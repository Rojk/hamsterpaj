<?
	require('../include/core/common.php');
	$ui_options['stylesheets'][] = 'traffa_index.css';
	$ui_options['stylesheets'][] = '/profilteman_testmiljo/joar_lef_style.css';
	$ui_options['title'] = 'Profilteman p&aring; Hamsterpaj.net';
	ui_top($ui_options);
	
?>

<?
$ul = $_SESSION['login']['userlevel'];
$un = $_SESSION['login']['username'];
$uid = $_SESSION['login']['id'];

if (is_privilegied('profile_theme_admin'))
{
	if (isset($_GET["fake_id"]))
	{
		$uid = $_GET["fake_id"];
	}
}

$temp = $_FILES["pt"]["tmp_name"];

// Root path for profile theme candidates.
$savepath = IMAGE_PATH . "profile_bg_candidates/";

if ($_GET["really"] == "yes" || isset($_GET["fake_id"]))
	{
		unlink($savepath.$uid.".png");
		echo "<span class=\"rubrik\">Bilden borttagen :)</span><br />";
		if (is_privilegied('profile_theme_admin') && isset($_GET["fake_id"]))
		{
			echo '<a href="/profilteman/?fake_id='.$_GET["fake_id"].'">Tillbaka</a>';
		}
		else
		{
			echo '<a href="/profilteman/">Tillbaka</a>';
		}
					
	}
else
	{
echo "<span class=\"rubrik\">Vill du verkligen radera din bild?</span><br />
			<a href=\"/profilteman/ptunlink.php?really=yes\">Ja</a>
			<a href=\"/profilteman/\">Nej</a>";
	}
	
if ($_GET["done"] == "yes")
	{
	}
	


ui_bottom();
?>
