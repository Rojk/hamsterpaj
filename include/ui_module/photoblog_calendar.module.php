<?php
	function photoblog_calendar($month, $year)
	{
		$date = mktime(12, 0, 0, $month, 1, $year);
		$daysInMonth = date('t', $date);

		$offset = date('N', $date);
		$rows = 1;
		$out.= '<a href="">&laquo;</a>' . "\n";
			$out .= '<span>' . date('F', $date) . '</span>' . "\n";
		$out.= '<a href="">&raquo;</a>' . "\n";
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
			$out .= '<td>' . $day . '</td>' . "\n";
		}
		while( ($day + $offset) <= $rows * 7)
		{
			$out .= '<td></td>' . "\n";
			$day++;
		}
		$out .= '</tr>' . "\n";
		$out .= '</table>' . "\n";
		$out .= '<span>2007</span><strong>2008</strong>' . "\n";
		return $out;
	}
	
	$options['output'].= photoblog_calendar(12, 2008); 
?>