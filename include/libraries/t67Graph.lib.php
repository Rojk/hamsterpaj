<?php
	function t67Graph_line($parameters)
	{
		$parameters['graph_id'] = isset($parameters['graph_id']) ? $parameters['graph_id'] : uniqid(rand(0, 99999999999999));
		$parameters['width'] = isset($parameters['width']) ? $parameters['width'] : 630;
		$parameters['height'] = isset($parameters['height']) ? $parameters['height'] : 250;
		$parameters['y_axis_labels'] = isset($parameters['y_axis_labels']) ? $parameters['y_axis_labels'] : true;		

		$bar_area_height = (isset($parameters['title'])) ? ($parameters['height'] - 24) : $parameters['height'] - 2;
		
		/* Make sure all values are arrays */
		foreach($parameters['bars'] AS $key => $bar)
		{
			if(!is_array($bar))
			{
				$parameters['bars'][$key] = array('value' => $bar);
			}
		}
		
		/* Get value of the highest bar and set the factor used to calculate bar heights */
		$max_value = 0;
		foreach($parameters['bars'] AS $bar)
		{
			$max_value = ($bar['value'] > $max_value) ? $bar['value'] : $max_value;
		}
		$bar_height_factor = ($bar_area_height-2) / $max_value;
		
		/* Calculate bar width */
		if($parameters['y_axis_labels'])
		{
			$y_axis_labels_width = (strlen($max_value) * 5) + 10;
			$bar_area_width = $parameters['width'] - 2 - $y_axis_labels_width;
		}
		else
		{
			$bar_area_width = $parameters['width'] - 2;
		}
		$bar_width = floor($bar_area_width / count($parameters['bars']));
		
		$ouput .= "\n\n";
		$ouput .= '<!-- T65 line graph with ' . count($parameters['bars']) . ' bars -->' . "\n\n";
		
		$ouput .= '<div class="t67Graph_line" style="width: ' . ($parameters['width'] - 2) . 'px; height: ' . ($parameters['height'] - 2) . 'px;">' . "\n";
		if(isset($parameters['title']))
		{
			$ouput .= '	<h2>' . $parameters['title'] . '</h2>' . "\n";
		}
		
		if($parameters['y_axis_labels'])
		{
			$ouput .= '<div class="t65Graph_line_y_labels" style="width: ' . $y_axis_labels_width . 'px; height: ' . $bar_area_height . 'px;">' . "\n";
			$y_axis_label_count = ceil(($bar_area_height - 10) / 30);

			for($i = 0; $i < $y_axis_label_count; $i++)
			{
				$label = ($i == 0) ? 0 : round( ($max_value - ($max_value % 30))* ($i/$y_axis_label_count));
				$ouput .= '<span class="t65Graph_line_y_label" style="bottom: ' . ($i*30) . 'px;">' . $label . '</span>' . "\n";
			}
			$ouput .= '</div>' . "\n";
		}
		
		$ouput .= '<ul class="t65Graph_line_bars" style="height: ' . $bar_area_height . 'px;">' . "\n";
		$i = 0;
		foreach($parameters['bars'] AS $bar)
		{
			$bar_height = floor($bar_height_factor * $bar['value']);
			$ouput .= '<li style="left: ' . ($bar_width*$i) . 'px; height: ' . $bar_height . 'px; width: ' . ($bar_width - floor($bar_width / 3)) . 'px" title="' . t67Core_readable_number($bar['value']) . '"></li>' . "\n";
			$i++;
		}
		$ouput .= '</ul>' . "\n";

		$ouput .= '</div>' . "\n";
		
		return $ouput;
	}

?>