<?php
	require('../include/core/common.php');
	
	if($_GET['action'] == 'common_users')
	{
			$query = 'SELECT COUNT(*) AS users FROM user_flags WHERE flag = "' . $_GET['flag'] . '"';
			$result = mysql_query($query);
			$data = mysql_fetch_assoc($result);
			$flag_count = $data['users'];			
			
			$query = 'SELECT l.id, l.username FROM login AS l, user_flags AS uf WHERE uf.flag = "' . $_GET['flag'] . '" AND l.id = uf.user AND l.lastaction > ' . (time()-600);
			$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		
		echo utf8_encode('<h2>' . $flag_count . ' har flaggan!</h2>');
		
		if(mysql_num_rows($result) == 0)
		{
			echo '<h3>Inga användare med flaggan online</h3>';
		}
		else
		{
			echo '<h3>Användare med flaggan online</h3>';
			while($data = mysql_fetch_assoc($result))
			{
				echo '<a href="/traffa/profile.php?id=' . $data['id'] . '">' . $data['username'] . '</a><br />';
			}
		}
	}
	else
	{
		$query = 'SELECT info, title, `group`, find_others_label FROM user_flags_list WHERE id ="' . $_GET['flag'] . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_num_rows($result) == 1)
		{
			$data = mysql_fetch_assoc($result);
			echo '<h2>' . $data['title'] . '</h2>';
			echo '<p>' . $data['info'] . '</p>';
			echo '<input type="button" class="find_others" value="' . $data['find_others_label'] . '" onclick="loadFragmentInToElement(\'/traffa/user_flag_info.php?action=common_users&flag=' . $_GET['flag'] . '\', \'common_flag_users\');" />' . "\n";
			echo '<em>Givetvis är alla satiriska texter skrivna på skämt, ta inte illa upp...</em>';
			echo '<div id="common_flag_users"></div>' . "\n";
		}
		else
		{
			echo utf8_encode('Det finns ingen information om denna flaggan');
		}
	}
?>