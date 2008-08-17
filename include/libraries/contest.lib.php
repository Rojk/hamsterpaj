<?php
function echo_sysop_images($params)
{
	if ($params['return_options'] == true)
	{
		$query = 'SELECT `id`, `username` FROM login WHERE `userlevel` = \'5\' LIMIT 20';
		$result = mysql_query($query);
		while ($row = mysql_fetch_array($result))
		{
			$sysop_id = $row["id"];
			$sysop_name = $row["username"];
			$out .= '<option value="'.$sysop_id.'">'.$sysop_name.'</option>';
		}
	}
	else
	{
		$out .= '<h1>Våra soeta Sysops</h1>';
		$out .= '<div id="sysops" class="sysops">';
		$out .= rounded_corners_top(array("color" => "blue_deluxe"), true);
		$out .= '<ul class="rita_img">';
	
		$query = 'SELECT `id`, `username` FROM login WHERE `userlevel` = \'5\' LIMIT 20';
		$result = mysql_query($query);
		while ($row = mysql_fetch_array($result))
		{
			$sysop_id = $row["id"];
			$sysop_name = $row["username"];
			if (file_exists(IMAGE_PATH.'images/users/thumb/'.$sysop_id.'.jpg'))
			{
				$out .= '<li class="imgli">';
					$out .= '';
						$out .= ui_avatar($sysop_id);
					$out .= '';
					$out .= '<a href="/traffa/profile.php?user_id='.$sysop_id.'">'.$sysop_name.'</a>';
				$out .= '</li>';
			}
			else
			{
	
				$out .= '<li class="imgli">';
					$out .= '<a href="/traffa/profile.php?user_id='.$sysop_id.'">';
						$out .= '<img src="http://images.hamsterpaj.net/tavling/gissa.png" class="user_avatar" />';
					$out .= '</a>';
					$out .= '<a href="/traffa/profile.php?user_id='.$sysop_id.'">'.$sysop_name.'</a>';
				$out .= '</li>';
			}
	
		}
	}
	return $out;
}
?>