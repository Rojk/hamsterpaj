<?php
	require('../include/core/common.php');

	echo '<style type="text/css">' . "\n";
	echo '@import url(\'/stylesheets/digga_popup.css?version=' . filemtime($hp_path . 'stylesheets/digga_popup.css') . '\');' . "\n";
	echo '</style>' . "\n";

	function digga_view_info($artist)
	{
		$query = 'SELECT name, popularity FROM artists WHERE id = "' . $artist . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_num_rows($result) != 1)
		{
			die('Artisten finns inte');
		}
		$data = mysql_fetch_assoc($result);
		$artist_name = $data['name'];
		echo '<h1>' . $artist_name . '</h1>';
		echo 'Uppskattas av ' . $data['popularity'] . ' hamsterpajare<br />' . "\n";
		if(login_checklogin())
		{
			if($artist_name == 'Kent')
			{
				treasure_item(27);
			}
			
			$query = 'SELECT user FROM user_artists WHERE user = "' . $_SESSION['login']['id'] . '" AND artist = "' . $artist . '" LIMIT 1';
			$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			if(mysql_num_rows($result) == 1)
			{
				echo '<input type="button" class="button" value="Sluta digga!" onclick="window.location=\'?action=dislike&artist=' . $_GET['artist'] . '\';" />' . "\n";			
			}
			else
			{
				echo '<input type="button" class="button" value="Börja digga!" onclick="window.location=\'?action=like&artist=' . $_GET['artist'] . '\';" />' . "\n";
			}
		}
		echo '<input type="button" value="Mer om ' . htmlentities($artist_name) . '" onclick="opener.window.location=\'digga.php?action=view_info&artist_id=' . $_GET['artist'] . '\'; window.close();" />' . "\n";
	}
	
	function digga_like($artist)
	{
		$query = 'INSERT INTO user_artists (user, artist) VALUES("' . $_SESSION['login']['id'] . '", "' . $artist . '")';
		if(mysql_query($query))
		{
			$query = 'UPDATE artists SET popularity = popularity + 1 WHERE id = "' . $artist . '"';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		}
		digga_view_info($artist);
	}

	if($_GET['action'] == 'like')
	{
		digga_like($_GET['artist']);
	}
	elseif($_GET['action'] == 'dislike')
	{
		$query = 'DELETE FROM user_artists WHERE user = "' . $_SESSION['login']['id'] . '" AND artist = "' . $_GET['artist'] . '" LIMIT 1';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_affected_rows() == 1)
		{
			$query = 'UPDATE artists SET popularity = popularity - 1 WHERE id = "' . $_GET['artist'] . '"';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		}
		digga_view_info($_GET['artist']);		
	}
	elseif($_GET['action'] == 'create')
	{
		echo '<form action="?action=add" method="post">' . "\n";
		echo '<input type="text" name="artist_name" />' . "\n";
		echo '<input type="submit" value="Börja digga!" />' . "\n";
		echo '</form>' . "\n";
	}
	elseif($_GET['action'] == 'add')
	{
		$query = 'SELECT id FROM artists WHERE name LIKE "' . $_POST['artist_name'] . '" LIMIT 1';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_num_rows($result) == 1)
		{
			$data = mysql_fetch_assoc($result);
			digga_like($data['id']);
		}
		else
		{
			$query = 'INSERT INTO artists (name) VALUES("' . htmlspecialchars($_POST['artist_name']) . '")';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			
			digga_like(mysql_insert_id());
		}
	}
	elseif($_GET['action'] == 'view')
	{
		digga_view_info($_GET['artist']);
	}
	elseif($_GET['action'] == 'help')
	{
?>
	<h1>Digga låter dig visa vad du gillar för musik!</h1>
	<p>
		Digga är ett system där du talar om vilka artister/band/grupper du lyssnar på. Systemet låter dig jämföra din
		musiksmak med andras och i framtiden kanske du kommer kunna söka efter folk med liknande musiksmak.<br />
		När du klickar på en artist på någon annans presentation kan du välja "Börja digga" och artisten kopieras över
		till din lista. Vill du ta bort en artist klickar du bara på denna och väljer "Sluta digga".<br />
		Detta är en ALPHA, dvs en tidig test-release, som antagligen är full av buggar.		
	</p>
<?php
	}
?>