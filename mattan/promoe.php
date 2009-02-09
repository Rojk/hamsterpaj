<?php
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'promoe.lib.php');
	require(PATHS_CONFIGS . 'promoe.conf.php');
	$ui_options['menu_path'] = array('mattan', 'promoe');
	$ui_options['title'] = 'Promoe på Hamsterpaj.net, rita dina egna pixel-bilder!';
	$ui_options['stylesheets'][] = 'promoe_new.css';
	$ui_options['javascripts'][] = 'promoe_new.js';
	ui_top($ui_options);

	if(login_checklogin() && isset($_GET['save']))
	{
		$query = 'INSERT INTO promoes (owner, description, imagestring, date, parent) VALUES("' . $_SESSION['login']['id'] . '", "' . $_GET['name'] . '", "' . $_GET['imagestring'] . '", "';
		$query .= time() . '", "' . $_GET['parent'] . '")';
		
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		jscript_location('?view=' . mysql_insert_id());
	}
	if(isset($_GET['search']))
	{
		$query = 'SELECT p.id, p.owner AS author_id, p.imagestring, p.date, l.username AS author_username ';
		$query .= 'FROM promoes AS p, login AS l WHERE p.description LIKE "' . $_GET['search'] . '" AND l.id = p.owner ORDER BY p.id ASC LIMIT 100';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));

		if(mysql_num_rows($result) > 1)
		{
			while($promoe = mysql_fetch_assoc($result))
			{
				$promoes[] = $promoe;
			}
			echo promoe_thumbs_list('Sökresultat: ' . $_GET['search'], $promoes);
		}
	}
	if(isset($_GET['view']))
	{
		$query = 'SELECT p.id, p.owner AS author_id, p.imagestring, p.date, p.description, p.parent, l.username AS author_username ';
		$query .= 'FROM promoes AS p, login AS l WHERE p.id = "' . $_GET['view'] . '" AND l.id = p.owner ORDER BY p.id ASC';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		
		$promoe = mysql_fetch_assoc($result);
		promoe_paintboard($promoe);	
		
		$author = $promoe['author_id'];
		$author_username = $promoe['author_username'];
		
		$parent_reference = ($promoe['parent'] > 0) ? $promoe['parent'] : $promoe['id'];
		
		$query = 'SELECT p.id, p.owner AS author_id, p.imagestring, p.date, l.username AS author_username ';
		$query .= 'FROM promoes AS p, login AS l WHERE (p.parent = "' . $parent_reference . '" OR p.id = "' . $parent_reference . '") AND l.id = p.owner ORDER BY p.id ASC';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));

		if(mysql_num_rows($result) > 1)
		{
			while($promoe = mysql_fetch_assoc($result))
			{
				$promoes[] = $promoe;
				$animate_url .= '&' . $promoe['id'];
			}
			echo promoe_thumbs_list('Andra versioner av den här bilden', $promoes);
//			echo '<a href="/annat/promoe_animate.php?animate' . $animate_url . '" target="_blank">Animera</a>';
		}
		
		unset($promoes);
		$query = 'SELECT p.id, p.owner AS author_id, p.imagestring, p.date, l.username AS author_username ';
		$query .= 'FROM promoes AS p, login AS l WHERE p.owner = "' . $author . '" AND l.id = p.owner ORDER BY p.id ASC';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));

		if(mysql_num_rows($result) > 1)
		{
			while($promoe = mysql_fetch_assoc($result))
			{
				$promoes[] = $promoe;
			}
			echo promoe_thumbs_list('Fler bilder av ' . $author_username, $promoes);
		}

	}
	else
	{
		promoe_paintboard();
	}

	unset($promoes);
	$query = 'SELECT p.id, p.owner AS author_id, p.imagestring, p.date, p.description, p.parent, l.username AS author_username ';
	$query .= 'FROM promoes AS p, login AS l WHERE l.id = p.owner ORDER BY p.id DESC LIMIT 10';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));

	while($promoe = mysql_fetch_assoc($result))
	{
		$promoes[] = $promoe;
	}
	echo promoe_thumbs_list('Senaste Promoe-bilderna', $promoes);

	unset($promoes);	
	$query = 'SELECT p.id, p.owner AS author_id, p.imagestring, p.date, p.description, p.parent, l.username AS author_username ';
	$query .= 'FROM promoes AS p, login AS l WHERE l.id = p.owner ORDER BY p.hypes DESC LIMIT 10';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));

	while($promoe = mysql_fetch_assoc($result))
	{
		$promoes[] = $promoe;
	}
	echo promoe_thumbs_list('Mest hypade Promoe-bilderna', $promoes);
	
	unset($promoes);
	$query = 'SELECT p.id, p.owner AS author_id, p.imagestring, p.date, p.description, p.parent, l.username AS author_username ';
	$query .= 'FROM promoes AS p, login AS l WHERE l.id = p.owner AND p.date > "' . (time()- (86400*3)) . '" ORDER BY p.hypes DESC LIMIT 10';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));

	while($promoe = mysql_fetch_assoc($result))
	{
		$promoes[] = $promoe;
	}
	echo promoe_thumbs_list('Färska Promoe-bilder på väg upp', $promoes);
?>
<form>
<input type="text" name="search" />
<input type="submit" value="Sök" />
</form>
<?php
	ui_bottom();
?>