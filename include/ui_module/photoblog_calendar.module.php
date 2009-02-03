<?php
	function photoblog_calendar($user_id, $month, $year)
	{
		$options = array('user' => $user_id);
		$used_dates = photoblog_dates_fetch($options);
		$used_dates = $used_dates[$year][$month];
		
		preint_r(func_get_args());
		
		$date = mktime(12, 0, 0, $month, 1, $year);
		$daysInMonth = date('t', $date);

		$offset = date('N', $date);
		$rows = 1;
		$out .= '<div id="photoblog_calendar_month">' . "\n";
			$out.= '<a href="">&laquo;</a>' . "\n";
				$out .= '<span>' . date('F', $date) . ', ' . $year . '</span>' . "\n";
			$out.= '<a href="">&raquo;</a>' . "\n";
		$out .= '</div>' . "\n";
		$out .= '<table>' . "\n";
		$out .= '<tr><th>M</th><th>T</th><th>O</th><th>T</th><th>F</th><th>L</th><th>S</th></tr>' . "\n";
		$out .= '<tr>';
		for($i = 1; $i <= $offset; $i++)
		{
			$out .= '<td></td>' . "\n";
		}
		for($day = 1; $day <= $daysInMonth; $day++)
		{
			if( ($day + $offset) % 7 == 0 && $day != 1)
			{
				$out .= '</tr><tr>' . "\n";
				$rows++;
			}
			$out .= '<td>' . (isset($used_dates[$day]) ? '<a href="#day-' . $year . $month . $day . '">' . $day . '</a>' : $day) . '</td>' . "\n";
		}
		while( ($day + $offset) <= $rows * 7)
		{
			$out .= '<td></td>' . "\n";
			$day++;
		}
		$out .= '</tr>' . "\n";
		$out .= '</table>' . "\n";
		$out .= '<div id="photoblog_calendar_year">' . "\n";
		$out .= '<span class="photoblog_calendar_year_pre">' . ((int)$year - 1) . '</span><span class="photoblog_calendar_year_after">' . ((int)$year + 1) . '</span>' . "\n";
		$out .= '</div>' . "\n";
		return $out;
	}
	
	$options['output'].= photoblog_calendar(PHOTOBLOG_CURRENT_USER, PHOTOBLOG_CURRENT_MONTH, PHOTOBLOG_CURRENT_YEAR); 
?>