<?php
	$return .= '<ul>' . "\n";
	foreach($_SESSION['visitors_with_image'] AS $visitor)
	{
		$return .= '<li>' . "\n";
		$return .= '<a href="/traffa/profile.php?id=' . $visitor['id'] . '" title="' . $visitor['username'] . ' besökte dig ' . strtolower(fix_time($visitor['timestamp'])) . '">';
		$return .= '<img src="http://images.hamsterpaj.net/images/users/thumb/' . $visitor['id'] . '.jpg" />';
		$return .= '</a>' . "\n";
		$return .= '</li>' . "\n";
	}
	$return .= '</ul>' . "\n";

	$return .= '<a href="/traffa/my_visitors.php" class="show_more_link">Visa fler &raquo;</a>' . "\n";
?>