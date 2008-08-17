This file is like... deprecated?...
<?php
__halt_compiler();


	require('../include/core/common.php');
	require_once(PATHS_INCLUDE . 'libraries/user_modules.lib.php');
	
	// IMPORTANT!
	if(!login_checklogin())
	{
		die('Du måste vara inloggad, bla bla bla...');
	}
	if(!is_privilegied('igotgodmode'))
	{
		exit;
	}
	
	
	die('Ej klart, KÖR INTE KODEN!');
	
	$modules = array();
	
	foreach($_POST as $key => $value)
	{
		if(substr($key, 0, 12) != 'user_module_'){ break; }
		
		list($module_type, $property) = explode('-', substr($key, 12));
		
		if(!user_modules_valid_module_handler_check(array('handle' => $module_type))){ break; }
		if(!user_modules_valid_properties_check(array('module_type' => $module_type, 'property' => $property, 'value' => $value)){ break; }
	
		$modules[$module_type][$property] = $value;
	}
	
	if(count($modules) > 0 || (isset($_POST['hide_all_modules']) && $_POST['hide_all_modules'] == 'true'))
	{
		
		$modules_to_save = array();
	
		foreach($modules as $module_handle => $module_properties)
		{
			if(user_modules_has_enough_parameters_check(array('module_handle' => $module_handle, 'module_properties' => $module_properties)))
			{
				$modules_to_save[$module_handle] = $module_properties;
			}
			else
			{
				echo 'Not enough parameters in ' . __FILE__ . ' on line ' . __LINE__ . '!';
			}
		}
		
		$_SESSION['user_modules'] = $modules_to_save;
		$query = 'UPDATE userinfo SET user_modules = "' . mysql_real_escape_string(serialize($modules_to_save)) . '" WHERE userid = ' . $_SESSION['login']['id'];
		mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	}
	
	$user_modules = user_modules_fetch();
	echo $user_modules;
?>