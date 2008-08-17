<?php
	echo '<div style="border: 1px solid ' . $profile_colors['border'] . '; background: ' . $profile_colors['background'] . '; padding: 3px;">' . "\n";
?>
<?php
require(PATHS_INCLUDE . 'future-functions.php');

$query = 'SELECT * FROM traffa_future WHERE userid = ' . $userid . ' ORDER BY future_date';
$result = mysql_query($query) or die(report_sql_error($query));
while ($data = mysql_fetch_assoc($result))
{
	$img = ($data['status'] == 'X') ? IMAGE_URL . 'images/icons/checkbox1_checked.png' : IMAGE_URL . 'images/icons/checkbox1_unchecked.png';
	echo '<img src="' . $img . '" width="12" height="12" />' . "\n";
	echo $data['future_name'];
	echo '<span style="line-height: 20px;">' . profile_future_draw_text($data['date_text'], $data['future_date'], $data['future_enddate']) . '</span>';
	echo '<br />';
}

?>
</div>
