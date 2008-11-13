<?php
	$output .= '<div id="fp_spotlight_area" style="width: 280px; float:right;">' . "\n";
		
	$users = cache_load('hetluften');

	$output .= '<div id="fp_spotlight">' . "\n";
	$output .= '<div id="fp_spotlight_scroller">' . "\n";
	foreach($users AS $user)
	{
		$output .= '<div class="fp_spotlight_profile">' . "\n";
		$output .= ui_avatar($user['id'], array('style' => 'float: left; margin-right: 15px; border: 1px solid white;'));
		$output .= '<h2><a href="/traffa/profile.php?id=' . $user['id'] . '">' . $user['username'] . '</a></h2>' . "\n";
		$output .= ($user['gender'] == 'f') ? '<p>Tjej' : '<p>Kille';
		$output .= ($user['birthday'] != '0000-00-00') ? ' ' . date_get_age($user['birthday']) . ' år' : '';
		$output .= (strlen($user['spot']) > 0) ? ' från ' . $user['spot'] . '</p>' : '</p>';
		if(count($user['flags']) > 0)
		{
			$output .= '<ul class="user_flags">' . "\n";
			$flag_count = 0;
			foreach($user['flags'] AS $flag)
			{
				if(strlen($flags_by_id[$flag]) > 0)
				{
					$output .= '<li><img src="' . IMAGE_URL . '/user_flags/' . $flags_by_id[$flag] . '" /></li>' . "\n";
					$flag_count++;
					if($flag_count == 5)
					{
						break;
					}
				}
			}
			$output .= '</ul>' . "\n";
		}
		$output .= '</div>' . "\n";
	}
	$output .= '</div>' . "\n";
	$output .= '</div>' . "\n";


	$output .= '<ul class="fp_users_list">' . "\n";
	$count = 0;
	foreach($users AS $user)
	{
		$output .= '<li><img src="' . IMAGE_URL . 'images/users/thumb/' . $user['id'] . '" class="fp_user_list_thumb" id="fp_user_thumb_' . $count . '" /></li>' . "\n";
		$count++;
	}
	$output .= '</ul>' . "\n";
	
	
	$output .= '<p style="clear: both;">Gå till: <a href="/traffa/age_guess.php">Gissa åldern</a> ' . "\n";
	$output .= '<a href="/traffa/gallery.php">Galleriet</a> ' . "\n";
	$output .= '<a href="/traffa/klotterplanket.php">klotterplanket</a></p>' . "\n";
	$output .= '</div>' . "\n";
?>