<?php

function t67Core_readable_number($number)
{
	$number = trim($number);
	
	$number = explode('.', $number);
	
	$return = trim(strrev(chunk_split(strrev($number[0]), 3, ' ')));

	if($number[1] > 0)
	{
		$return .= ',' . trim(strrev(chunk_split(strrev($number[1]), 3, ' ')));
	}
	
	return $return;
}

?>