<?php
	
require('../include/core/common.php');
require_once(PATHS_INCLUDE . 'libraries/posts.php');
require_once(PATHS_INCLUDE . 'libraries/markup.php');
//require_once($hp_includepath . '/libraries/markup.php');
//require_once($hp_includepath . '/libraries/games.lib.php');
//require_once($hp_includepath . '/libraries/schedule.lib.php');
//require_once(PATHS_INCLUDE . 'libraries/tips.lib.php');

$ui_options['menu_path'] = array('admin', 'ov_reg');

ui_top($ui_options);

if(!is_privilegied('crew_register'))
{
	echo 'inte för dig...';
}
else
{
	if(count($_POST) > 0)
	{
//		preint_r($_POST);
		if(login_checklogin() && $_SESSION['login']['userlevel'] >= 3)
		{
			//Spara uppgifter
			$query = 'UPDATE userinfo SET';
			$query .= ' firstname="' . $_POST['firstname'] . '"';
			$query .= ', surname="' . $_POST['surname'] . '"';
			$query .= ', email="' . $_POST['email'] . '"';
			$query .= ', msn="' . $_POST['msn'] . '"';
			$query .= ', streetaddress="' . $_POST['streetaddress'] . '"';
			$query .= ', zip_code="' . $_POST['zip_code'] . '"';
			$query .= ', birthday="' . $_POST['birthday'] . '"';
			$query .= ', phone_ov="' . $_POST['phone_ov'] . '"';
			$query .= ', visible_level="' . $_POST['visible_level'] . '"';
			$query .= ' WHERE userid ="' . $_SESSION['login']['id'] . '"';
			mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
			echo '<h2>Dina uppgifter är sparade</h2>';
			
			$fetch['userinfo'] = array('contact1', 'contact2', 'gender', 'birthday', 'image', 'image_ban_expire', 'forum_signature', 'zip_code', 'forum_quality_rank', 'parlino_activated', 'cell_phone', 'firstname', 'surname', 'email', 'streetaddress', 'msn', 'visible_level', 'phone_ov');
			$userinfo = login_load_user_data($_SESSION['login']['id'], $fetch, __FILE__, __LINE__);
					
			$_SESSION = array_merge($_SESSION, $userinfo);
	
		}
	}

	echo '
	<h1>Kontaktuppgifter för ordningsvakter</h1>
	<p>För att vi ska kunna komma i kontakt med ordningsvakter som kan ha hanterat
pedofiler, eventuellt brutit mot våra regler eller om vi snabbt behöver reda
ut något behöver vi kontaktuppgifter.
I krissituationer, som när Flashback kör spammattacker, skulle
kontaktuppgifter göra det lättare att mobilisera en OV-styrka</p>
	<form method="post" action="/admin/ov_reg.php">';

	$items['firstname'] = 'Förnamn';
	$items['surname'] = 'Efternamn';
	$items['email'] = 'E-post';
	$items['msn'] = 'MSN';
	$items['streetaddress'] = 'Gatuadress';
	$items['birthday'] = 'Födelsedatum';
	$items['zip_code'] = 'Postnummer';
	$items['phone_ov'] = 'Mobilnummer';

	foreach($items as $key => $label)
	{
		if(!isset($_SESSION['userinfo'][$key]) || $_SESSION['userinfo'][$key] == '')
		{
			echo '<h5 style="color: red;">';
		}
		else
		{
			echo '<h5>';
		}
		echo $label;
		echo '</h5>' . "\n";
		echo '<input type="text" name="' . $key . '" value="' . $_SESSION['userinfo'][$key] . '"/>';
	}
	if(!isset($_SESSION['userinfo']['visible_level']) || $_SESSION['userinfo']['visible_level'] == 0)
	{
		echo '<h5 style="color: red;">';
	}
	else
	{
		echo '<h5>';
	}
	echo 'Visningsnivå</h5>' . "\n";
	echo '<p>(Ort och ålder kommer att synas på sajten)</p> ' . "\n";
	echo '<input id="visible_0" type="radio" name="visible_level" value="3" ' . ($_SESSION['userinfo']['visible_level'] == 3 ? 'checked' : '') . '/>
	<label for="visible_0">Visa uppgifterna för alla ordningsvakter (nivå 3) och högre</label>
	<br />
	<input id="visible_1" type="radio" name="visible_level" value="4" ' . ($_SESSION['userinfo']['visible_level'] == 4 ? 'checked' : '') . '/>
	<label for="visible_1">Visa bara uppgifterna för administratörer och sysops (nivå 4 och 5)</label>';

/*	<input type="checkbox" id="zip_code_visible" name="zip_code_visible" ' . ($_SESSION['userinfo']['zip_code_visible'] == 1 ? 'checked' : '') . '>
	<label for="zip_code_visible">Visa min bostadsord i Träffa</label> 
	echo'
	<input type="checkbox" id="birthdate_visible" name="birthdate_visible" ' . ($_SESSION['userinfo']['birthday_visible'] == 1 ? 'checked' : '') . '>
	<label for="birthdate_visible">Visa min ålder på sajten</label>*/
	echo '<br />' . "\n";
	echo '<input class="button_60" type="submit" value="Spara" />
	</form>
	';
}	

//preint_r($_SESSION);
ui_bottom();

?>
