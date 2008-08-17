<?
	require('../include/core/common.php');
	//require(PATHS_INCLUDE  . 'libraries/photos.lib.php');
	$ui_options['stylesheets'][] = 'start.css';
	$ui_options['javascripts'][] = 'start.js';
	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['stylesheets'][] = 'online_ovs.css';
	$ui_options['title'] = 'Online OV\'s - Hamsterpaj.net';
	$ui_options['menu_path'] = array('hamsterpaj');
	$ui_options['adtoma_category'] = 'start';
	$ul = $_SESSION['login']['userlevel'];
	
	function online_ovs2()
	{
		$ul = $_SESSION['login']['userlevel'];
		//$ul = 1;
		//$userlevel_fetch = ($level == 3) ? "userlevel = 3 OR userlevel = 4" : "userlevel = 5";
		
		$userlevel_fetch = "userlevel >= 3";
		
		$query = query_cache(array('query' => 'SELECT userlevel, id, username, lastaction FROM login WHERE ' . $userlevel_fetch . ' ORDER BY lastaction DESC'));
		$out .= '<div class="ovlist">
		<ul>';
		
		foreach ($query AS $row)
		{
			if($row['lastaction'] > time() - 600)
			{
				$ov_ul = $row['userlevel'];
				switch ($ov_ul)
				{
					case 3:
					$ov_ul_in_swedish = "Ordningsvakt";
					break;
					case 4:
					$ov_ul_in_swedish = "Administratör";
					break;
					case 5:
					$ov_ul_in_swedish = "Sysop";
					break;
				}
				
				$out .= '<li>' . "\n";
				$out .= ui_avatar($row['id']);
				$out .= '<a href="/traffa/guestbook.php?view='.$row['id'].'">'.$row['username'].'</a><br />';
				$out .= $ov_ul_in_swedish . '<br />';
				//$out .= '<span style="color: #008f00;">online</span>' . "\n";
				$out .= '</li>';
				
				$many[] = $row['id'];
			}
		}
		$out .= '</ul>
		<br style="clear: both;" />
		</div>';
		return $out;
	}
	
	//$ul = 1;
	
	$out .= online_ovs2();
	$title = 'Ordningsvakter, Administratörer och Sysops';
	
	ui_top($ui_options);
	echo '<h1>Online just nu: '.$title.'</h1>';
	//echo 'Sysops visas inte om det är någon annan inloggad';
		
	
	echo rounded_corners($out, $void, true);
		
	//echo '<img src="http://images.hamsterpaj.net/rojk/heart.gif" id="rojk_love" />' . "\n";
	ui_bottom();
?>