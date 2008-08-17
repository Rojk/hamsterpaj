<?php
	require('../include/core/common.php'); 

	if(isset($_GET['username']))
	{
		$query = 'SELECT id FROM login WHERE username LIKE "' . addcslashes($_GET['username'], '_') . '" LIMIT 1';
		$result = mysql_query($query) or die(mysql_error());
		if(mysql_num_rows($result) != 1)
		{
			header('location: /traffa/search.php?notfound');
		}
		else
		{
			$data = mysql_fetch_assoc($result);
			if($_GET['page'] == 'guestbook')
			{
				header('location: /traffa/guestbook.php?view=' . $data['id']);
			}
			elseif($_GET['page'] == 'message')
			{
				header('location: /traffa/messages.php?action=compose&recipient_username=' . $_GET['username'] . '&recipient_id=' . $data['id']);
			}
			else
			{
				header('location: /traffa/profile.php?id=' . $data['id']);
			}
		}
	}
	else
	{
		die('Någonting tycks ha blivit fel med din sökning :(');
	}
?>
