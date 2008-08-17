<?php
	/* Add open source signature later... */
	require('../include/core/common.php');

	$all_functions = array(
		'ui_top' => 'include/ui-functions.php',
		'ui_bottom' => 'include/ui-functions.php'
	);
	
	if(isset($_GET['function']))
	{
		if(array_key_exists($_GET['function'], $all_functions))
		{
			header('Location: /open_source/readfile.php?file=' . $all_functions[$_GET['function']]) . '&goto_function=' . $_GET['function'];
		}
		else
		{
			echo 'Could not find function in index.';
		}
	}
	else
	{
		echo 'Call with ?function=function_name';
	}
?>