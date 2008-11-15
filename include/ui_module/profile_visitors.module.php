<?php
	$options['output'] .= '<ul>' . "\n";
	foreach($_SESSION['visitors_with_image'] AS $visitor)
	{
		$options['output'] .= '<li>' . "\n";
		$options['output'] .= '<a href="/traffa/profile.php?id=' . $visitor['id'] . '" title="' . $visitor['username'] . ' besökte dig ' . strtolower(fix_time($visitor['timestamp'])) . '">';
		$options['output'] .= '<img src="http://images.hamsterpaj.net/images/users/thumb/' . $visitor['id'] . '.jpg" />';
		$options['output'] .= '</a>' . "\n";
		$options['output'] .= '</li>' . "\n";
	}
	$options['output'] .= '</ul>' . "\n";
	$options['output'] .= '<a href="/traffa/my_visitors.php" class="show_more_link">Visa fler &raquo;</a>' . "\n";
?>
