<?php
	/* OPEN_SOURCE */

function tail($options)
{
	$options['line_count'] = (isset($options['line_count'])) ? $options['line_count'] : 10;
	$options['buffer_length'] = (isset($options['buffer_length'])) ? $options['buffer_length'] : 200;
	
	$file = fopen($options['filename'], 'r');
	$filesize = filesize($options['filename']);

	$offset = $filesize - ($options['buffer_length'] * $options['line_count']);		
	while($offset > 0)
	{
		$lines = array();
		$offset = $filesize - ($options['buffer_length'] * $options['line_count']);
		$offset = ($offset < 0) ? 0 : $offset;

		fseek($file, $offset);

  	while(!feof($file))
  	{
			$lines[] = fgets($file);
		}
		
		if(count($lines) > $options['line_count'])
		{
			$lines = array_reverse($lines);
			for($i = 0; $i < $options['line_count']; $i++)
			{
				$return[] = $lines[$i];
			}
			$return = array_reverse($return);
			return $return;
		}
	
		// We didn't read enoguh lines, increase the buffer length and try again
		$options['buffer_length'] = ($options['buffer_length'] / count($lines)) * $options['line_count'] + 1;
	}
	$lines = file($options['filename']);
	return $lines;
}
?>
