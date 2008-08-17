<?php

function suggestion_create($suggestion)
{
	$query = 'INSERT INTO suggestions (author, timestamp, last_update, text, category, display_level)';
	$query .= ' VALUES("' . $_SESSION['login']['id'] . '", "' . time() . '", "' . time() . '", "' . $suggestion['text'] . '"';
	$query .= ', "' . $suggestion['category'] . '", "normal")';
	
	mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	
	return mysql_insert_id();
}

function suggestion_update($suggestion)
{

	if(isset($suggestion['responsible_username']))
	{
		$query = 'SELECT id FROM login WHERE username LIKE "' . $suggestion['responsible_username'] . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_num_rows($result) == 1)
		{
			$data = mysql_fetch_assoc($result);
			$suggestion['responsible'] = $data['id'];
		}
	}
	
	$updatable_fields = array('text', 'category', 'reply', 'reply_by', 'classification', 'responsible', 'display_level');
	$query = 'UPDATE suggestions SET id = "' . $suggestion['id'] . '"';
	foreach($suggestion AS $field => $value)
	{
		if(in_array($field, $updatable_fields))
		{
			$query .= ', ' . $field . ' = "' . $value . '"';
		}
	}
	$query .= ', last_update = "' . time() . '" WHERE id = "' . $suggestion['id'] . '" LIMIT 1';
	mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
}

function suggestion_fetch($fetch)
{
	$fetch['display_level'] = (isset($fetch['display_level'])) ? $fetch['display_level'] : array('normal');
	
	$query = 'SELECT s.*, al.username AS author_username, login.username AS responsible_username FROM login AS al, suggestions AS s';
	$query .= ' LEFT JOIN login ON s.responsible = login.id';
	$query .= ' WHERE al.id = s.author';
	$query .= (isset($fetch['display_level'])) ? ' AND s.display_level IN("' . implode('", "', $fetch['display_level']) . '")' : '';
	$query .= (isset($fetch['id'])) ? ' AND s.id IN("' . implode('", "', $fetch['id']) . '")' : '';
	$query .= (isset($fetch['category'])) ? ' AND s.category IN("' . implode('", "', $fetch['category']) . '")' : '';
	$query .= (isset($fetch['classification'])) ? ' AND s.classification IN("' . implode('", "', $fetch['classification']) . '")' : '';
	$query .= ' ORDER BY s.last_update DESC, s.id DESC';
	$query .= (isset($fetch['limit'])) ? ' LIMIT ' . $fetch['limit'] : '';
	
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	while($data = mysql_fetch_assoc($result))
	{
		$return[$data['id']] = $data;
	}

	return $return;
}

function suggestion_form($suggestion)
{
	global $SUGGESTIONS;

	echo '<h2>Ett förslag åt gången!</h2>' . "\n";
	echo '<p>Skriv bara ett förslag åt gången, med flera förslag i samma inlägg kan vi inte ta hand om förslagen.</p>' . "\n";

	echo '<form action="/hamsterpaj/suggestions.php?action=create" method="post" id="suggestions_form">' . "\n";
	$action = (isset($suggestion['id'])) ? 'update' : 'create';
	echo '<input type="hidden" name="action" value="' . $action . '" />' . "\n";
	if(isset($suggestion['id']))
	{
		echo '<input type="hidden" name="id" value="' . $suggestion['id'] . '" />' . "\n";
	}
	
	echo '<h5>Typ av förslag</h5>' . "\n";
	echo '<select name="category" id="category_select">' . "\n";
	foreach($SUGGESTIONS['categories'] AS $handle => $category)
	{
		$selected = ($suggestion['category'] == $handle) ? ' selected="selected"' : '';
		echo '<option value="' . $handle . '"' . $selected . '>' . $category['label'] . '</option>' . "\n";
	}
	echo '</select>' . "\n";
	
	if(!isset($suggestion['category']))
	{
		$suggestion['category'] = 'bug';
	}
	foreach($SUGGESTIONS['categories'] AS $handle => $category)
	{
		$style = ($handle == $suggestion['category']) ? ' style="display: block;"' : '';
		echo '<div id="suggestion_' . $handle . '_help_text" class="suggestion_help_text"' . $style . '>' . $category['help_text'] . '</div>' . "\n";
	}
	
	echo '<h5>Förslag</h5>' . "\n";
	echo '<textarea name="text">' . $suggestion['text'] . '</textarea>' . "\n";
	echo '<input type="submit" value="Spara" class="button_60" />' . "\n";
	echo '</form>' . "\n";
}

function suggestion_list($suggestions)
{
	foreach($suggestions AS $id => $suggestion)
	{
		if(!isset($updates_today) && $suggestion['last_update'] > strtotime(date('Y-m-d')))
		{
			echo '<h2>Uppdaterat eller skapat idag</h2>' . "\n";
			$updates_today = true;
		}
		
		if($suggestion['last_update'] < strtotime(date('Y-m-d')) && isset($updates_today))
		{
			echo '<h2>Äldre förslag</h2>' . "\n";
			unset($updates_today);
		}
		
		echo '<div class="suggestion" id="suggestion_' . $id . '">' . "\n";
		echo '<h5 class="author_header">Inskickat av</h5>' . "\n";
		echo ' <a href="/traffa/profile.php?id=' . $suggestion['author'] . '">' . $suggestion['author_username'] . '</a>' . "\n";
		echo ' ' . fix_time($suggestion['timestamp']) . (($suggestion['timestamp'] == $suggestion['last_update']) ? '' : ' - senast ändrat ' . fix_time($suggestion['last_update'])) . "\n";
		if(is_privilegied('suggestion_admin'))
		{
			echo '<button class="button_80" onclick="xmlhttp_ping(\'http://www.hamsterpaj.net/hamsterpaj/suggestions.php?action=delete&id=' . $id . '\' + this.href);$(this).parent().hide(\'slow\');return false;" style="cursor: pointer;">Ta bort</button>' . "\n";
		}
		echo '<h5>Förslag:</h5>' . "\n";
		echo '<p>' . nl2br($suggestion['text']) . '</p>' . "\n";
		if(strlen($suggestion['reply']) > 0)
		{
			echo '<h5>Svar:</h5>' . "\n";
			echo '<p>' . nl2br($suggestion['reply']) . '</p>' . "\n";
		}
		if(strlen($suggestion['responsible_username']) > 0)
		{
			echo '<h5 class="responsible_username_header">Ansvarig:</h5> <a href="/traffa/profile.php?id=' . $suggestion['responsible'] . '">' . $suggestion['responsible_username'] . '</a>';
		}
		if(is_privilegied('suggestion_admin'))
		{
			echo '<form action="?action=update" method="post" id="suggestions_admin_form">' . "\n";
				echo '<h5>Svar</h5>' . "\n";
				echo '<textarea cols="75" name="reply">' . htmlspecialchars($suggestion['reply']) . '</textarea>' . "\n";
				echo '<input type="hidden" name="id" value="' . $suggestion['id'] . '" />' . "\n";
				echo '<input type="text" style="display: none;" name="responsible_username" value="' . $_SESSION['login']['username'] . '" />' . "\n";
				echo '<input type="submit" value="Spara" class="button_60" />' . "\n";
			echo '</form>';
		}
		echo '</div>' . "\n";
	}
}
?>