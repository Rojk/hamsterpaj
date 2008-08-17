<?
	require('include/core/common.php');
	require(PATHS_INCLUDE  . 'libraries/photos.lib.php');
	$ui_options['stylesheets'][] = 'start.css';
	$ui_options['javascripts'][] = 'start.js';
	$ui_options['stylesheets'][] = 'photos.css';
	$ui_options['stylesheets'][] = 'profile.css';
	$ui_options['title'] = 'Online OV\'s - Hamsterpaj.net';
	$ui_options['menu_path'] = array('hamsterpaj');
	$ui_options['adtoma_category'] = 'start';
	ui_top($ui_options);
	
	function online_ovs($level)
	{
		$query = 'SELECT username, lastaction, id FROM login WHERE userlevel = '.$level.'';
		$result = mysql_query($query);
		while ($row = mysql_fetch_array($result))
		{
			if($row['lastaction'] > time() - 600)
			{
				$out .= '<div style="width: 150px; height: 150px; float: left; margin: 0px;">' . "\n";
				$out .= $row['username'].'<br />';
				$out .= '<span class="online">online</span>' . "\n";
				$out .= ui_avatar($row['id']);
				$out .= '</div>';
				
			}
		}
	}
	
	echo online_ovs(3); 
	echo $out;

	ui_bottom();
?>