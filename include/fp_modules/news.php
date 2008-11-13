<?php
	$query = 'SELECT * FROM nyheter ORDER BY id DESC LIMIT 1';
	$data = query_cache(array('query' => $query, 'max_delay' => 60));
	if($data[0]['tstamp'] > time() - 86400)
	{
		$data = $data[0];
		$output .= '<div id="news">' . "\n";
		$output .= '<h2>' . $data['title'] . '</h2>' . "\n";
		$output .= '<p>';
		$output .= (strlen($data['body']) > 1000) ? substr($data['body'], 0, 1000) . '...' : $data['body'];
		$output .= '</p>' . "\n";
		$output .= '<p>Skrevs ' . fix_time($data['tstamp']) . ' <a href="' . $data['thread_url'] . '">Diskutera nyheten »</a></p>' . "\n";
		$output .= '</div>' . "\n";
	}	
?>