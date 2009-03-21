<?php
require('../include/core/common.php');
require_once(PATHS_LIBRARIES . 'schedule.lib.php');
$ui_options['menu_path'] = array('admin', 'registrering');

if(!is_privilegied('register_suspend_admin'))
{
	header('location: /');
	die();
}

ui_top($ui_options);

if(isset($_POST))
{
	cache_save('register_suspend', $_POST['reg_status']);
}

$reg_status = cache_load('register_suspend');

if($reg_status == 'disabled')
{
	echo '<h1>Användarregistreringen är avstängd</h1>' . "\n";
}
else
{
	echo '<h1>Användarregistreringen är aktiv</h1>' . "\n";
}

echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post">' . "\n";
echo '<input type="submit" value="disabled" name="reg_status" />' . "\n";
echo '<input type="submit" value="enabled" name="reg_status" />' . "\n";
echo '</form>' . "\n";

ui_bottom();
?>
