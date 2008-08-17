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

$savepath = IMAGE_PATH . "profile_bg_candidates/";

if ($ul > 0)
{
	if (isset($_POST["theme_name"]))
	{
		if (file_exists($style_path.$uid.".css"))
		{
			// GET CSS
			$filename = $style_path.$uid.".css";
			$handle = fopen($filename, "r");
			$css = fread($handle, filesize($filename));
			
			// SUBMIT CSS
			$handle = fopen($img_path."submits/".$uid.".css", "w+");
			fwrite($handle, $css);
			fclose($handle);
		}
		
		// GET IMAGE
		$filename = $img_path.$uid.".png";
		$handle = fopen($filename, "r");
		$img = fread($handle, filesize($filename));
		fclose($handle);
		
		// SUBMIT IMAGE
		$handle = fopen($img_path."submits/".$uid.".png", "w+");
		fwrite($handle, $img);
		fclose($handle);
		
		if (file_exists($img_path."submits/".$uid.".png") && file_exists($img_path."submits/".$uid.".css"))
		{
			$output .= rounded_corners_top(array('color' => 'orange_deluxe'), true);
			$output .= 'Allt gick fint, vi meddelar dig n&auml;r vi har tittat p&aring; ditt tema.<br />
			<a href="/profilteman">G&aring; tillbaka.</a>';
			$output .= rounded_corners_bottom(array('color' => 'orange_deluxe'), true);
			
			$query = "INSERT INTO `profile_bg_candidates_submits` (`user_id`, `timestamp`, `status`, `theme_name`) 
			VALUES ('$uid', '".date("U")."', '0', '".$_POST['theme_name']."')";
			mysql_query($query);
			
		}
	}
	else
	{
		$output .= rounded_corners_top(array('color' => 'orange_deluxe'), true);
		$output .= '<form action="/profilteman/ptsubmit.php" method="post">
		V&auml;lj ett namn f&ouml;r ditt tema:<input name="theme_name" type="text" /><input type="submit" value="Ok" /><br />
		<a href="/profilteman">G&aring; tillbaka.</a>
		</form>';
		$output .= rounded_corners_bottom(array('color' => 'orange_deluxe'), true);
	}
}

echo $output;

ui_bottom();
?>