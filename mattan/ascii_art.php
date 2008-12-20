<?php
	require('../include/core/common.php');
	
	$ui_options['title'] = 'ASCII-art på Hamsterpaj';
	$ui_options['menu_path'] = array('mattan', 'ascii_art');
	$ui_options['stylesheets'][] = 'ascii_art.css';
	$ui_options['javascripts'][] = 'ascii_art.js';
	
	ui_top($ui_options);

	echo '<h1>ASCII-art</h1>' . "\n";
	echo '<p>Här har vi samlat lite ASCII-art vi hittat. Välj och vraka! Saknar du något? Skriv ett förslag på <a href="/hamsterpaj/suggestions.php">förslagssidan</a>. </p>' . "\n";
	
	$page = (isset($_GET['page']) && is_numeric($_GET['page'])) ? intval($_GET['page']) : 0;
	$page = ($page < 0 || $page > 999) ? 0 : $page;
	$limit = $page * 10;
	
	if($page > 0)
	{
		echo '<a href="?page=' . ($page - 1) . '" class="ascii_art_previous">&laquo; Föregående sida</a>';
	}
	echo '<a href="?page=' . ($page + 1) . '" class="ascii_art_next">Nästa sida &raquo;</a>' . "\n";
	
	echo '<br style="clear: both;" />' . "\n";
	
	$query = 'SELECT *' .
					 ' FROM ascii_art' .
					 ' ORDER BY title' .
					 ' LIMIT ' . $limit . ', 10';
	//$data_array = query_cache(array('query' => $query, 'max_delay' => 60));
	
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	while($data = mysql_fetch_assoc($result))
	{
		$data_array[] = $data;
	}
	
	$user_votes = array();
	if(login_checklogin())
	{
		$query = 'SELECT ascii_art_id FROM ascii_art_votes WHERE userid = ' . $_SESSION['login']['id'];
		$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
		while($vote = mysql_fetch_assoc($query))
		{
			$user_votes[] = $vote['ascii_art_id'];
		}
	}
	
	foreach($data_array  as $data)
	{
		$allow_voting = false;
		if(login_checklogin() && !in_array($data['id'], $user_votes))
		{
			$allow_voting = true;
		}
		
		echo '<a name="ascii_art_link_' . $data['id'] . '"></a>';
		echo '<div class="ascii_art_div">';
		
		
		echo '<div class="resources">';
		
		echo '<h2>' . $data['title'] . '</h2>' . "\n";
		echo ' <a href="#ascii_art_link_' . $data['id'] . '" id="ascii_art_direct_link_show_' . $data['id'] . '">(Direktlänk)</a>';
		echo '<input type="text" id="ascii_art_direct_link_input_' . $data['id'] . '" class="ascii_art_direct_link_input" value="http://www.hamsterpaj.net/mattan/ascii_art.php#ascii_art_link_' . $data['id'] . '" />' . "\n";
		if(is_privilegied('ascii_art_admin'))
		{ 
			echo ' <a href="?delete=' . $data['id'] . '" onclick="return confirm(\\"Sure?\\")">(X)</a>'; 
		}
		// Avoid division-by-zero-errors...
		$voters = (((int)$data['voters'] == 0) ? 1 : (int)$data['voters']);
		echo '<div class="ascii_art_vote" style="background-position: 0px ' . (75 - ((round((int)$data['votes'] / $voters) - 1) * 15)) . 'px"' . ($allow_voting ? ' id="ascii_art_vote_' . $data['id'] . '_' . round((int)$data['votes'] / $voters) . '"' : '') . ' />&nbsp;</div>';
		echo '</div>';

		echo '<br style="clear: both;" />' . "\n";
		
		echo '<pre>';
		echo $data['the_art'];
		echo '</pre><br />' . "\n";
				
		echo '<br style="clear: both" />';
		echo '</div>';
		
	}
	
	if($page > 0)
	{
		echo '<a href="?page=' . ($page - 1) . '" class="ascii_art_previous">&laquo; Föregående sida</a>';
	}
	echo '<a href="?page=' . ($page + 1) . '" class="ascii_art_next">Nästa sida &raquo;</a>' . "\n";
	
	echo '<br style="clear: both;" />' . "\n";
	
	if(is_privilegied('ascii_art_admin'))
	{
		if(isset($_GET['delete']) && is_numeric($_GET['delete']) && (int) $_GET['delete'] > 0)
		{
			$query  = 'DELETE FROM ascii_art WHERE id = ' . $_GET['delete'] . ' LIMIT 1';
			mysql_query($query) or report_sql_error($query);
			jscript_alert('Go, went och... GONE!');
			jscript_location('ascii_art.php');
		}
	}
	
	if(is_privilegied('ascii_art_admin'))
	{
		if(isset($_POST['title'], $_POST['the_art']))
		{
			$query  = 'INSERT INTO ascii_art (title, the_art) VALUES ("' . $_POST['title'] . '", "' . $_POST['the_art'] . '")';
			mysql_query($query) or report_sql_error($query);
			jscript_alert('Vi har nu lagt till ASCII-arten i databasen. Det kan dröja upp till 60 sekunder innan den dyker upp bland de andra ASCII-artsen!');
			jscript_location('ascii_art.php');
		}
		
		echo rounded_corners_top(array('color' => 'white'));
		?>
		<form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
			<input type="text" name="title" /> (titel)<br />
			<textarea name="the_art" style="width: 100%; height: 300px"></textarea><br />
			<input type="submit" value="Skapa" class="button_80" />
		</form>
		<?php
		echo rounded_corners_bottom();
	}
	
	ui_bottom();
?>
	