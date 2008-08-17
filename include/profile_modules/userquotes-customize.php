<?php
	if($_GET['action'] == 'new')
	{
		$author = htmlspecialchars($_POST['author']);
		$type = (in_array($_POST['type'], array('text', 'talk', 'song'))) ? $_POST['type'] : 'talk';
		$quote = htmlspecialchars($_POST['quote']);
		$description = htmlspecialchars($_POST['description']);
		$author_is_member = ($_POST['author_is_member'] == 1) ? 1 : 0;
		$query = 'INSERT INTO userquotes (profile, author, type, quote, description, author_is_member) VALUES(';
		$query .= $_SESSION['login']['id'] . ', "' . $author . '", "' . $type . '", "' . $quote . '", "' . $description . '", ' . $author_is_member . ')';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	}
	elseif($_GET['action'] == 'remove' && is_numeric($_GET['quote']))
	{
		$query = 'DELETE FROM userquotes WHERE id = "' . $_GET['quote'] . '" AND profile = "' . $_SESSION['login']['id'] . '" LIMIT 1';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	}
	elseif($_GET['action'] == 'update')
	{
		$author = htmlspecialchars($_POST['author']);
		$type = (in_array($_POST['type'], array('text', 'talk', 'song'))) ? $_POST['type'] : 'talk';
		$quote = htmlspecialchars($_POST['quote']);
		$description = htmlspecialchars($_POST['description']);
		$author_is_member = ($_POST['author_is_member'] == 1) ? 1 : 0;
		$query = 'UPDATE userquotes SET author =" ' . $author . '", type ="' . $type . '", quote = "' . $quote . '", description ="' . $description . '"';
		$query .= ', author_is_member ="' . $author_is_member . '" WHERE profile ="' . $_SESSION['login']['id'] . '" AND id ="' . $_GET['quote'] . '" LIMIT 1'; 
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	}
	echo '<div class="grey_faded_div">';
	echo '<h2>Ta bort eller ändra citat</h2>';
	$query = 'SELECT id, author, type, quote, description, author_is_member FROM userquotes WHERE profile = "' . $_SESSION['login']['id'] . '"';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	while($data = mysql_fetch_assoc($result))
	{
		echo '<div>';
		echo '<span style="font-style: italic;">"' . $data['quote'] . '"</span><br />' . "\n";
		echo '[<a href="' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '&action=remove&quote=' . $data['id'] . '">Ta bort</a>] [<a href="javascript: void(0);" onclick="document.getElementById(\'userquote_' . $data['id'] . '_edit\').style.display=\'block\';">Ändra</a>]' . "\n";
		echo '<div class="orange_faded_div" style="margin-bottom: 10px; width: 722px; display: none;" id="userquote_' . $data['id'] . '_edit">';
		echo '<form action="' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '&action=update&quote=' . $data['id'] . '" method="post">' . "\n";
		echo '<div style="height: 40px;">';
		echo '<div style="float: left; margin-right: 5px;"><span style="font-weight: bold;">Vem</span><br /><input type="text" name="author" value="' . $data['author'] . '"class="textbox" /></div>' . "\n";
		echo '<div style="float: left; margin-right: 5px;"><br />';
		echo '<select name="type">';
		echo ($data['type'] == 'text') ? '<option value="text" selected="selected">skrev</option>' : '<option value="text">skrev</option>';
		echo ($data['type'] == 'talk') ? '<option value="talk" selected="selected">sa</option>' : '<option value="talk">sa</option>';
		echo ($data['type'] == 'song') ? '<option value="song" selected="selected">sjöng</option>' : '<option value="song">sjöng</option>';
		echo '</select></div>' . "\n";
		echo '<div style="float: left;"><span style="font-weight: bold;">vad?</span><br /><input type="text" name="quote" value="' . $data['quote'] . '" class="textbox" style="width: 160px;" /></div>' . "\n";
		echo '<div style="float: left;"><span style="font-weight: bold;">Här kan du skriva en förklaring</span><br /><input name="description" value="' . $data['description'] . '" class="textbox" style="width: 265px;"></textarea></div>';
		echo '<br /><input type="submit" value="Spara &raquo;" class="button" />';
		echo '</div>';
		echo '<div>';
		echo 'Den som kläckte ur sig citatet är medlem på Hamsterpaj';
		echo '<input type="checkbox" name="author_is_member" value="1" ';
		echo ($data['author_is_member'] == 1) ? 'checked="true" />' : '/>';
		echo '</form>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
	}
	echo '</div>';

	echo '<div class="grey_faded_div" style="height: 140px;">';
	echo '<h2>Nytt citat</h2>' . "\n";
	echo '<form action="' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id'] . '&action=new" method="post">' . "\n";
	echo '<div style="height: 40px;">';
	echo '<div style="float: left; margin-right: 5px;"><span style="font-weight: bold;">Vem</span><br /><input type="text" name="author" class="textbox" /></div>' . "\n";
	echo '<div style="float: left; margin-right: 5px;"><br />';
	echo '<select name="type"><option value="text">skrev</option><option value="talk">sa</option><option value="song">sjöng</option></select></div>' . "\n";
	echo '<div style="float: left;"><span style="font-weight: bold;">vad?</span><br /><input type="text" name="quote" class="textbox" style="width: 167px;" /></div>' . "\n";
	echo '<div style="float: left;"><span style="font-weight: bold;">Här kan du skriva en förklaring</span><br /><input name="description" class="textbox" style="width: 265px;"></textarea></div>';
	echo '<br /><input type="submit" value="Lägg till &raquo;" class="button" />';
	echo '</div>';
	echo '<div>';
	echo '<p style="float: left;">';
	echo 'Om den som kläckte ur sig citatet är medlem på Hamsterpaj så skriver du personens användarnamn i rutan "Vem" och kryssar i denna rutan. ';
	echo '<input type="checkbox" name="author_is_member" value="1" />';
	echo '</form>';
	echo '</p></div>';
	echo '<br style="clear: both;" />';
	echo '</div>' . "\n";
?>
