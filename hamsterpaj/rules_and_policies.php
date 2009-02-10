<?php
	require('../include/core/common.php');
	$ui_options['menu_path'] = array('hamsterpaj', 'rules_and_policies');
	$ui_options['javascripts'][] = 'settings.js';
	
	require(PATHS_LIBRARIES . 'articles.lib.php');
	$ui_options['stylesheets'][] = 'articles.css';
	
	$article = articles_fetch(array('id' => '65'));
	$out .= render_full_article($article);
/*	$user_id = $_SESSION['login']['id'];
	
	if ($_GET['action'] == 'edit' && ($user_id == 57100 || $user_id == 774586))
	{
		
		$out .= rounded_corners_top($void, true);
		$out .= '<h1>Ändra "Regler och policies"</h1>';
		
		if ($user_id == 57100)
		{
			$out .= '<p>Tjenare Eric,';
		}
		elseif ($user_id == 774586)
		{
			$out .= '<p>Tjenare Joar,';
		}
		//$out .= $user_id;
		$out .= ' här kan du ändra innehållet i "Regler och policies"-sidan, lägg märke till att du måste skriva i HTML dock :)</p>';
		$out .= '<form action="' . $_SERVER['PHP_SELF'] . '?action=post" method="post">';
		$out .= '<textarea name="content" style="width: 622px; height: 700px">';
		$sql = 'SELECT * FROM rules_and_policies WHERE description = "index" LIMIT 1';
		$result = mysql_query($sql);
		$data = mysql_fetch_assoc($result);
		$out .= $data['content'];
		$out .= '</textarea>';
		$out .= '<input class="button_70" type="submit" value="Spara" />';
			$return .= '<button id="profile_presentation_change_preview_button" tabindex="3" class="button_120">Förhandsgranska</button>';
			$return .= ' <input type="submit" value="Spara" id="profile_presentation_change_save" tabindex="3" class="button_60" />' . "\n";
			
			$return .= '<br style="clear: both;" /></form>' . "\n";
			
			
			$return .= '<div id="profile_presentation_change_preview_area">&nbsp;</div>';
			
		//	$out .= $return;
		$out .= '</form>';
		$out .= rounded_corners_bottom($void, true);
	}
	elseif ($_GET['action'] == 'post' && ($user_id == 57100 || $user_id == 774586))
	{
		$sql = 'UPDATE rules_and_policies';
    $sql .= ' SET content="'. $_POST['content'] .'" , last_changed_by=' . $user_id . ', last_changed=' . time();
    $sql .= ' WHERE description = "index"';
    $sql .= ' LIMIT 1';
		if (mysql_query($sql))
		{
			$out .= 'Sparat<br /><a href="/hamsterpaj/rules_and_policies.php">Gå och kolla!</a>';
		}
		else
		{
			$params = array('color' => 'orange_deluxe');
			$out .= rounded_corners_top($params, true);
			$out .= '<h2 style="margin: 0px">Nu har Joar gjort fel, kontakta honom och säg "fiskattack!" så vet han nog vad du pratar om.</h2>';
			$out .= rounded_corners_bottom($params, true);
		}
	}
	else
	{
		$sql = 'SELECT * FROM rules_and_policies WHERE description = "index" LIMIT 1';
		$result = mysql_query($sql);
		while ($data = mysql_fetch_assoc($result))
		{
			$out .= html_entity_decode($data['content']);
		}
		if ($user_id == 57100 || $user_id == 774586)
		{
			$out .= '<form action="/hamsterpaj/rules_and_policies.php" method="get">
			<input type="hidden" name="action" value="edit" /><input type="submit" class="button_150" value="Klicka här för att ändra" /></form>';
		}
	}
*/	
	ui_top($ui_options);
	echo $out;
	ui_bottom();


	
?>