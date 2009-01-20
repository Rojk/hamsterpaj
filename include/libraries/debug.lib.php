<?php
	function preint_r($array, $bool_return = false)
	{
		if ($bool_return)
		{
			$return = '<pre>' . "\n";
			$return .= print_r($array, true);
			$return .= '</pre>' . "\n";
			return $return;
		}
		else
		{
			echo '<pre>' . "\n";
			print_r($array);
			echo '</pre>' . "\n";
		}
	}
?>