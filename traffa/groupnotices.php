<?php
require('../include/core/common.php');
$ui_options['menu_path'] = array('traeffa', 'grupper');
$ui_options['title'] = 'Gruppnotiser - Hamsterpaj.net';
ui_top($ui_options);

if(login_checklogin() != 1)
{
	jscript_alert('Du måste vara inloggad för att komma åt denna sidan!');
	jscript_location('index.php');
}

echo rounded_corners_top(array('color' => 'blue'));
echo '<h2 style="margin-top: 0px;">Nya inlägg i dina grupper</h2>';
 foreach($_SESSION['groups_members'] AS $key => $value)
 {
    $query = 'SELECT groups_list.message_count, groups_members.read_msg, groups_list.name, groups_members.notices FROM groups_members, groups_list ';
    $query .= 'WHERE groups_members.groupid = ' . $value . ' AND groups_list.groupid = ' . $value;
    $query .= ' AND groups_members.userid =' . $_SESSION['login']['id'];
    $result = mysql_query($query) or die(report_sql_error($query));
    $data = mysql_fetch_assoc($result);
		if ($data['notices'] == "Y")
		{
    	$new_posts =  $data['message_count'] - $data['read_msg'];
			if ($new_posts > 0)
			{
    		echo '<strong>';
			}
    }
		
		echo '<a href="groups.php?action=goto&groupid=' . $value . '">' . $data['name'] . '</a>';
		
		if ($data['notices'] == "Y")
		{
			echo ' - ' . $new_posts . ' nya inlägg.';
    	if ($new_posts > 0)
			{
				echo '</strong>';
			}
		}
		else
		{
			echo ' - Bevakas inte';
		}
   	echo '<br />' . "\n";
	}

echo '<br /><a href="/traffa/groups.php">Till alla dina grupper >></a><br />';
echo rounded_corners_bottom(array('color' => 'blue'));


cache_update_groups();
	ui_bottom();
?>
