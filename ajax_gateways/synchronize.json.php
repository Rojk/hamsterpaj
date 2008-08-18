<?php
	/*
		It is very important that output is properly escaped.
		If not, we may end upp with a XSS-attack.
		
		
		If you don't know what you're doing, HANDS OFF!!!
	
	
	*/

	require('../include/core/common.php');
	if(isset($_GET['fetch']) && !empty($_GET['fetch']))
	{
		$objects_to_fetch = explode(',', $_GET['fetch']);
		foreach($objects_to_fetch as $object_to_fetch)
		{
			switch($object_to_fetch)
			{				
				default: continue 2;
			}
			
			$return[] = '"' . $object_to_fetch . '": ' . $data;
		}
		
		echo '[' . implode(', ', $return) . ']';
	}
?>