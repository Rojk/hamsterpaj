<?
	require('../include/core/common.php');
	$ui_options['stylesheets'][] = 'traffa_index.css';
	$ui_options['stylesheets'][] = '/profilteman_testmiljo/joar_lef_style.css';
	$ui_options['stylesheets'][] = '/mattan/joar_mattan.css';
	$ui_options['title'] = 'Profilteman på Hamsterpaj.net';
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

// Go command, should be set to "false" if something os wrong.
$go = true;

// Extract file ending.
$file = $_FILES["file"]["name"];
$len = strlen($file);
$ending_start = $len - 4;

// THE file ending.
$file_end = substr($file, $ending_start);

// Stop process if the file ending is'nt ".png"
if ($file_end != ".png" && $file_end != ".PNG")
{
//	die("<b>Fel filformat, det måste vara \".png\".<br />sluta hacka :'(</b>");
	$go = false;
}

// EN BAN-FUNKTION SKA SNART SÄTTAS IN HÄR

// Rename file and save it as "/mnt/images/profile_bg_candidates/<userid>.png".
$_FILES["file"]["name"] = "$uid.png";

$max_file_size = 300000;

if ($ul > 0 && $go == true)
{
if ($_FILES["file"]["size"] < $max_file_size)
  {
  if ($_FILES["file"]["error"] > 0)
    {
    echo "Return Code: " . $_FILES["file"]["error"] . "<br />";
    }
  else
    {
    if (file_exists("upload/" . $savepath . $_FILES["file"]["name"]))
      {
      echo $_FILES["file"]["name"] . " already exists. ";
      }
    else
      {
      move_uploaded_file($_FILES["file"]["tmp_name"], $savepath . $_FILES["file"]["name"]);
      echo "<span class=\"rubrik\">Bilden är sparad:)</span><br /><a href=\"/profilteman/\">Tillbaka</a>";
//      echo "Stored in: " . "upload/" . $_FILES["file"]["name"];
      }
    }
  }
else
  {
  echo "<b>För stor fil. kontakta <a href=\"/traffa/profile.php?user_id=774586\">Joar</a> om du tycker att det borde fungera.</b><br /><a href=\"/profilteman/\">Tillbaka</a><br />";
  echo "Din fil: " . ($_FILES["file"]["size"] / 1024) . " Kb<br />
  Max: $max_file_size";
  
  }
}
else
{
	if ($ul == 0)
	{
		echo "<b>Du måste vara inloggad.</b>";
	}
	else
	{
		echo "<b>Fel filformat, det måste vara \".png\", sluta hacka :'(</b><br />
		OBS. felet loggades för säkerhets skull.<br /><a href=\"/profilteman/\">Tillbaka</a>";
		
		// Log "crashdata". RATAD AV THE MASTER :)
//		$query = "INSERT INTO profile_bg_candidates_log (`user_id`, `file_ending`, `ip`, `timestamp`) 
//		VALUES ('$uid', '$file_end', '".$_SERVER["REMOTE_ADDR"]."', '".date("U")."')";
//		mysql_query($query);
		
	}
}

?>


<?
ui_bottom();
?>
