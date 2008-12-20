<?
	$output .= "<h1>Tema&ouml;verblick</h1>";
	$handle = opendir($img_path);
	$subdir = readdir($handle);
	while (false !== ($file = readdir($handle))) 
		{
		$files[] = $file;
		}
	array_multisort($files);
	$many = count($files);
	$output .= rounded_corners_top(array('color' => 'blue_deluxe'));
	$output .= '<form name="form" action="/profilteman/ptadmin.php?showall=yes" method="post">
	<select name="view" onchange="this.form.submit()">
	<option value="V&auml;lj anv&auml;ndare">V&auml;lj anv&auml;ndare</option>';
	foreach ($files as $k => $v)
		{
		if ($v !== "." && $v !== ".." && $v !== "styles" && $v !== "submits")
			{
				$v = str_replace(".png", "", $v);
				$query = "SELECT username FROM login WHERE `id` = '".$v."' LIMIT 1";
				$username = mysql_query($query);
				while($row = mysql_fetch_array($username))
					{
						$username2 = $row["username"];
					}
				$output .= '<option value="'.$v.'">'.$v.' - '.$username2.'</option>
				';
			}
		}
	$output .= '</select>';
	if (isset($_POST["view"]))
	{
		$query = "SELECT username FROM login WHERE `id` = '".$_POST["view"]."' LIMIT 1";
		$username = mysql_query($query);
		while($row = mysql_fetch_array($username))
		{
			$username2 = $row["username"];
		}
	$output .= ' Just nu: '. $_POST["view"].' - '.$username2;
	}
	$output .= '</form>';
	
	


$output .= rounded_corners_bottom();
	
	
	if (isset($_POST["view"]))
		{
$view = $_POST["view"];
$dotcss = $view.".css";
$dotpng = $view.".png";

$styleadress = "http://images.hamsterpaj.net/profile_bg_candidates/styles/".$dotcss;
$imgadress = $imgroot."profile_bg_candidates/".$dotpng;

$filename = $style_path.$dotcss;
$handle = fopen($filename, "r");
$css = fread($handle, filesize($filename));
fclose($handle);

$output .= '<style type="text/css">
'.$css.'
.profile_head
{
background: url(\''.$imgadress.'\');
}
</style>';

$output .= '<div class="profile_head">
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
</div>
Bildadress: <a href="'.$imgadress.'">'.$imgadress.'</a><br />
Stilmallsadress: <a href="'.$styleadress.'">'.$styleadress.'</a>';
}
$output .= rounded_corners_top(array('color' => 'blue_deluxe'));
$output .= '<a href="/profilteman/ptadmin.php">G&aring; till inskickade teman</a>';
$output .= rounded_corners_bottom();
$output .= rounded_corners_top(array('color' => 'blue_deluxe'));
$output .= '<a href="/profilteman">G&aring; till huvudsidan</a>';
$output .= rounded_corners_bottom();
?>