<?php
	include(PATHS_INCLUDE . 'xhpml.php');

	$query = 'SELECT stylesheet, freetext FROM traffa_freetext WHERE userid = "' . $userinfo['login']['id'] . '"';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	$data = mysql_fetch_assoc($result);

	$stylesheet = unserialize($data['stylesheet']);
	$output .=  '<style>' . "\n";
	foreach($stylesheet AS $class => $properties)
	{
		$output .=  ($class == 'h5' ||$clas == 'h6') ? $class : '.traffa_freetext_' . $class . "\n";
		$output .=  '{' . "\n";
		foreach($properties AS $property => $value)
		{
			if(strlen($value) < 1)
			{
				$value = $cfg_defaults[$property];
			}
			if($property == 'font-size' || $property == 'padding')
			{
				$value .= 'px';
			}
			$output .=  "\t" . $property . ': ' . $value . ';' . "\n";
		}
		$output .=  '}' . "\n\n";
	}
	htmlspecialchars($output);
	
	$output .=  '</style>' . "\n";
	echo $output;
	
	$output =  '<div style="border: 1px solid ' . $profile_colors['border'] . '; z-index: -1;" class="traffa_freetext_presentation">' . "\n";
	if(strlen($data['freetext']) > 0)
	{
		if($userinfo['login']['id'] == 68767)
		{
			$output .=  $data['freetext'];
		}
		if($userinfo['login']['id'] == 625747)
		{
			$output .=  $data['freetext'];
		}
		else
		{
			/* Lite haxx här då	*/
			$data['freetext'] = str_replace('bullar', 'kakor', $data['freetext']);
			$output .=  profile_parse_presentation(nl2br($data['freetext']));
		}
	}
	else {
		$output .=  '<i>' . $userinfo['login']['username'] . ' har inte skrivit någon presentation än.</i>';
	}
	echo $output;
?>
</div>
