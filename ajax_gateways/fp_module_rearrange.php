<?php
	require('../include/core/common.php');

	for($i = 0; $i < 100; $i++)
	{
		if(isset($_GET['pos_' . $i]))
		{
			$modules[] = $_GET['pos_' . $i];
		}
	}
	
	cache_save('fp_module_order', $modules);

?>