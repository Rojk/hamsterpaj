Bababloggen flyttas till "Dagboken", som är på g, precis som allt annat
<?php
/*
	echo '<div style="border: 1px solid ' . $profile_colors['border'] . '; background: ' . $profile_colors['background'] . '; padding: 3px;">' . "\n";
	$query = 'SELECT id, date, IF(LENGTH(title) > 20, CONCAT(LEFT(title, 17), \'...\'), title) AS title FROM blog ';
	$query .= 'WHERE user = "' . $userid . '" ORDER BY id ASC';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	$months = array('januari', 'februari', 'mars', 'april', 'maj', 'juni', 'juli', 'augusti', 'september', 'oktober', 'november', 'december');
	while($data = mysql_fetch_assoc($result))
	{
		$time_selector[date('Y-m', strtotime($data['date']))] = ucfirst($months[date('n', strtotime($data['date']))-1]) . ' ' . date('Y', strtotime($data['date']));
		$entries[date('Y-m', strtotime($data['date']))][$data['id']] = date('d', strtotime($data['date'])) . ' - ' . $data['title'];
	}
?>
	<script>
		var current_blog_id = 0;
		function blog_show_month(show)
		{
			<?php
			foreach(array_keys($time_selector) AS $month)
			{
				echo 'document.getElementById(\'' . $month . '\').style.display = \'none\';' . "\n";
			}
			?>
			document.getElementById(show).style.display = 'inline';
			blog_load_entry(document.getElementById(show).value);
		}
		function blog_load_entry(blog_id)
		{
			document.getElementById('blog_gateway').src = '/blog_gateway.php?action=fetch_entry&entry_id=' + blog_id;
			current_blog_id = blog_id;
			document.getElementById('blog_comments').style.display = 'none';
			blog_hide_comments();
		}
		function blog_load_comments(blog_id)
		{
			document.getElementById('blog_gateway').src = '/blog_gateway.php?action=fetch_comments&entry_id=' + blog_id + '&rand=' + Math.random();
			document.getElementById('blog_comments').style.display = 'block';
			document.getElementById('blog_comment_control').className = 'blog_comments_visible';
			document.getElementById('blog_comment_entries').innerHTML = 'Laddar...';
			document.getElementById('hide_comments_link').style.display = 'inline';
			document.getElementById('show_comments_link').style.display = 'none';
		}
		function blog_post_comment(blog_id)
		{
			var comment = escape(document.getElementById('blog_comment_input').value);
			document.getElementById('blog_gateway').src = '/blog_gateway.php?action=save_comment&entry_id=' + blog_id + '&comment=' + comment;
		}
		function blog_hide_comments()
		{
			document.getElementById('blog_comments').style.display = 'none';
			document.getElementById('blog_comment_control').className = 'blog_comments_hidden';
			document.getElementById('hide_comments_link').style.display = 'none';
			document.getElementById('show_comments_link').style.display = 'inline';
		}
	</script>

<?php
	echo '<select onchange="blog_show_month(this.value);">' . "\n";
	foreach(array_reverse($time_selector) AS $month => $label)
	{
		echo '<option value="' . $month . '">' . $label . '</option>' . "\n";
		$first_month = (!isset($first_month)) ? $month : $first_month;
	}
	echo '</select>';
	
	foreach(array_keys($time_selector) AS $month)
	{
		echo '<select id="' . $month . '" onchange="blog_load_entry(this.value);">';
		foreach(array_reverse($entries[$month], true) AS $id => $label)
		{
			echo '<option value="' . $id . '">' . $label . '</option>' . "\n";
		}
		echo '</select>';
	}
	
	echo '<p id="blog_info"></p>';
	echo '<h2 id="blog_title">Den här bloggen är tom</h2>' . "\n";
	echo '<p id="blog_text">För att skriva någonting i din blog, tryck på länken nedanför denna rutan.</p>' . "\n";
	
	echo '<div id="blog_photos">' . "\n";
	echo '<div class="blog_photo_div"><img id="blog_photo_1" class="blog_photo" src="http://www.hamsterpaj.net/images/ui/logo.png" /></div>' . "\n";
	echo '<div class="blog_photo_div"><img id="blog_photo_2" class="blog_photo" src="http://www.hamsterpaj.net/images/ui/logo.png" /></div>' . "\n";
	echo '<div class="blog_photo_div"><img id="blog_photo_3" class="blog_photo" src="http://www.hamsterpaj.net/images/ui/logo.png" /></div>' . "\n";
	echo '<div class="blog_photo_div"><img id="blog_photo_4" class="blog_photo" src="http://www.hamsterpaj.net/images/ui/logo.png" /></div>' . "\n";
	echo '</div>' . "\n";
	
	echo '<iframe id="blog_gateway" style="display: none;"></iframe>';
	echo '<script>' . "\n";
	echo 'blog_show_month("' . $first_month . '");' . "\n";
	echo '</script>' . "\n";
?>
<style>
	.blog_comments_visible
	{
		border: 1px solid white;
	}
	.blog_comments_hidden
	{
		border: none;
	}
	.blog_photo_div
	{
		float: left;
		margin-left: 13px;
		width: 140px;
		height: 110px;
		background: white;
		border: 1px solid #cdcdcd;
	}
	.blog_photo
	{
		background: black;
		margin: 9px;
		border: 1px solid #cdcdcd;
		width: 120px;
		height: 90px;
	}
	#blog_info
	{
		color: #565656;
		margin-top: 2px;
		margin-bottom: 2px;
		font-size: 10px;
	}
	#blog_photos
	{
		height: 125px;
	}
</style>
<br style="clear: both;" />
<div class="blog_comments_hidden" style="padding: 3px;" id="blog_comment_control">
<span onclick="blog_load_comments(current_blog_id);" id="show_comments_link" style="cursor: pointer; text-decoration: underline;">Visa kommentarer &raquo;</span>
<span onclick="blog_hide_comments();" id="hide_comments_link" style="cursor: pointer; text-decoration: underline; display: none;">&laquo; Dölj kommentarer</span>
<div id="blog_comments" style="display: none;">
<strong>Skriv en kommentar:<br /></strong>
<input id="blog_comment_input" style="width: 450px;">
<input type="button" class="button" value="Skicka kommentar &raquo;" style="margin-top: 3px;" onclick="blog_post_comment(current_blog_id);" />
<div id="blog_comment_entries"></div>
</div>
</div>
</div>
<?php
*/
?>