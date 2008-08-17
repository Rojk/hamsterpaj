<?php 
	require('../include/core/common.php');
	include_once(PATHS_INCLUDE . 'libraries/sex_sense.lib.php');
	include_once(PATHS_INCLUDE . 'libraries/sex_sense_ui.lib.php');
	$ui_options['stylesheets'][] = 'sex_sense.css';
	$ui_options['title'] = 'Ändra ett svar - Hamsterpaj.net';
	$ui_options['menu_path'] = array('sex_sense');
	
	if (!is_privilegied('sex_sense_admin'))
	{
		die('Slutaaah!');
	}
	if (isset($_POST['answer'], $_POST['id']))
	{
		$sql = 'UPDATE sex_answers SET answer = "' . $_POST['answer'] . '" WHERE id = "' . $_POST['id'] . '" LIMIT 1';
		mysql_query($sql) or report_sql_error($sql, __FILE__, __LINE__);
		$sql = 'SELECT answer_to FROM sex_answers WHERE id = "' . $_POST['id'] . '" LIMIT 1';
		$result = mysql_query($sql);
		$data = mysql_fetch_assoc($result);
		header('Location: /sex_och_sinne/' . $data['answer_to'] . '.html');
	}
	else
	{
		$sql = 'SELECT * FROM sex_answers WHERE id = "' . $_GET['id'] . '" LIMIT 1';
		$result = mysql_query($sql);
		$data = mysql_fetch_assoc($result);
		$out .= sex_sense_bright_container_top();
			$out .= sex_sense_dark_container_top();
				$out .= '<h3>Ändra svar</h3>' . "\n";
			$out .= sex_sense_dark_container_bottom();
			$out .= '<form action="/sex_och_sinne/aendra_svar.php" method="post">';
			$out .= '<textarea name="answer" style="width: 550px; height: 200px;">';
			$out .= $data['answer'];
			$out .= '</textarea>';
			$out .= '<input type="hidden" name="id" value="' . $_GET['id'] . '" />';
			$out .= '<br /><input class="button_120" type="submit" value="Spara ändringar" />';
			$out .= '</form>';
		  $out .= '<div style="clear: both; height: 5px;"></div>' . "\n";
		$out .= sex_sense_bright_container_bottom();
	}
	
	
	
	
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();