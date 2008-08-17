<?php
	require('../include/core/common.php');
	$ui_options['current_menu'] = 'hamsterpaj';
	ui_top($ui_options);
?>
<form method="post" action="<?php $_SERVER['PHP_SELF'] ?>">
	<input type="text" name="title" /><br />
	<textarea name="text_entry"></textarea><br />
	<input type="submit" name="submit" class="button" value="Spara" /><br />
</form>
	


<?php
	ui_bottom();
?>
