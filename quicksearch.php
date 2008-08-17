<?php
	require('include/core/common.php');
	switch($_GET['type'])
	{
		case 'user':
			if (stristr(strtolower($_GET['search']), "id:"))
			{
				header('location: /traffa/user_facts.php?user_id=' . substr($_GET['search'], 3));
				echo substr($_GET['search'], 0, 3);
			}
			else
			{
				header('location: /traffa/quicksearch.php?username=' . $_GET['search']);
			}
		break;
		case 'forum':
			header('Location: /forum/search.php?quicksearch=' . $_GET['search']);
		break;
		case 'groups':
			header('Location: /traffa/groups.php?action=search_group_quick&search_text=' . $_GET['search']);
		break;
		case 'uid':
			header('Location: /traffa/profile.php?id=' . $_GET['search']);
		break;
		case 'ip':
			header('Location: /admin/users_by_ip.php?ip=' . $_GET['search']);
		break;
		default:
			header('Location: /');
		break;
	}
?>
