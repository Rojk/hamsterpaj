<?php
	echo '<div style="border: 1px solid ' . $profile_colors['border'] . '; background: ' . $profile_colors['background'] . '; padding: 1px;">' . "\n";
?>
<script>
function userquote_div_control(id)
{
	var innerdiv = document.getElementById('userquote_' + id);
	var outerdiv = document.getElementById('userquote_' + id + '_container');
	if(innerdiv.style.display == 'block')
	{
		innerdiv.style.display = 'none';
		outerdiv.style.background = 'none';
	}
	else
	{
		innerdiv.style.display = 'block';
		outerdiv.style.background = '#edf5fd';
	}
}
</script>
<?php
	$query = 'SELECT id, quote, type, author, author_is_member, description, profile FROM userquotes WHERE profile = "' . $userid . '" LIMIT ' . USERQUOTES_MAX_QUOTES;
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	$types = array('song' => 'Sjöngs', 'text' => 'Skrevs', 'talk' => 'Sas');
	for($row = 0; $data = mysql_fetch_assoc($result); $row++)
	{
		echo '<div id="userquote_' . $row . '_container" style="margin-bottom: 5px;">';
		echo '<a href="javascript: void(0);" style="text-decoration: none; font-style: italic;" onclick="userquote_div_control(' . $row . ');">"' . $data['quote'] . '"</a>';
		echo '<div style="display: none; height: 100%;" id="userquote_' . $row . '">' . "\n";
		echo '<span style="text-style: italic;">' . $types[$data['type']] . ' av ';
		echo ($data['author_is_member'] == 1) ? '<a href="/traffa/quicksearch.php?username=' . $data['author'] . '">' . $data['author'] . '</a>' : $data['author'];
		echo '</span>';
		echo '<p>' . $data['description'] . '</p>';
		echo '</div>';
		echo '</div>';
	}
?>

</div>
