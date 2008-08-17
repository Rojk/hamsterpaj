<?php
function render_warnings_table($result, $highlight)
{
	if (!isset($time) || !is_numeric($time))
	{
		$highlight = time() - 604800;
	}
	$out .= '<table style="border: 0px;"><tbody>';
	$out .= '<tr><td><strong>Varnad</strong></td><td><strong>Av</strong></td><td><strong>Tidpunkt</strong></td><td><strong>Anledning</strong></td></tr>' . "\n";
	
	while($data = mysql_fetch_assoc($result))
	{
		$found_something = true;
		$out_later .= ($data['timestamp'] > $highlight) ? '<tr class="_warning_row_active" background: ' . $within_a_week_color . ';">' : '<tr class="_warning_row">' . "\n";
		
		$out_later .= '<td class="_warning" valign="top" style="text-align: left"><strong>
		<a href="/traffa/user_facts.php?user_id=' . $data['user_user_id'] . '">' . $data['user_username'] . '</a></strong>'./*(' . $data['user_user_id'] . ')</td>' .*/ "\n"; 
		
		$out_later .= '<td class="_warning" valign="top"><strong>
		<a href="/traffa/user_facts.php?user_id=' . $data['admin_user_id'] . '">' . $data['admin_username'] . '</a></strong>'./*(' . $data['admin_user_id'] . ')</td>' .*/ "\n";
		
		$out_later .= '<td class="_warning" valign="top">
		' . fix_time($data['timestamp']) . '</td>' . "\n";
		
		$out_later .= '<td class="_warning" valign="top">
		' . nl2br($data['reason']) . '</td>' . "\n";
		
		$out_later .= '</tr>' . "\n";
	}
	$out .= $out_later;
	
	$out .= '</tbody></table>';
	
	if ($found_something)
	{
		return $out;
	}
	elseif (!$found_something)
	{
		return "<h2>Hittade inget i databasen :(</h2>";
	}
}


?>