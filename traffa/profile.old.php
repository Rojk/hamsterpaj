<?php
	if(!isset($_GET['id']) || !is_numeric($_GET['id']))
	{
		if($_SESSION['login']['id'] > 0)
		{
			$_GET['id'] = $_SESSION['login']['id'];
		}
		else
		{
			header('location: /traffa/');	
		}
	}

	require('../include/core/common.php');
	include($hp_path . '/amusefiles/amuse-functions.php');
	include($hp_includepath . 'photo-functions.php');
	include($hp_includepath . 'traffa-functions.php');
	include($hp_includepath . 'traffa-definitions.php');
	include($hp_includepath . 'hpml.php');

	$ui_options['current_menu'] = 'traffa';
	$ui_options['javascripts'] = array('scripts.js');
	$ui_options['stylesheets'] = array('profile.css', 'amuse.css');

	ui_top($ui_options);	

	if(isset($_GET['create_photo_album']) && $_GET['id'] == $_SESSION['login']['id'])
	{
		create_photo_album($_GET['id']);
		header('location: ' . $_SERVER['PHP_SELF'] . '?id=' . $_GET['id']);
	}
	
	$userid = $_GET['id'];

	$fetch['login'] = array('username', 'regtimestamp', 'lastlogon', 'userlevel', 'lastusernamechange', 'lastusername', 'session_id', 'lastaction', 'id');
	if($_SESSION['login']['userlevel'] >= 4)
	{
		$fetch['login'] = array_merge($fetch['login'], array('regip', 'lastip'));
	}
	$fetch['userinfo'] = array('homepage', 'contact1', 'contact2', 'gender', 'birthday', 'geo_municipal', 'geo_location', 'image');
	$fetch['traffa'] = array('firstname', 'presentation', 'personality', 'bgimage', 'photos', 'irl', 'height');
	$fetch['preferences'] = array('bubblemessage_visitors');

	$userdata = login_load_user_data($userid, $fetch);

	if($userdata['login']['username'] == 'Borttagen')
	{
		echo '<p class="error">Denna användare existerar inte!</p>';
		ui_bottom();
		exit;
	}
	/* Fix municipal/city info */
	if (strlen($userdata['userinfo']['geo_location']) > 0 && $userdata['userinfo']['geo_location'] != $userdata['userinfo']['geo_municipal'])
	{
		$userdata['userinfo']['geo_municipal'] = $userdata['userinfo']['geo_municipal'] . ' (' . $userdata['userinfo']['geo_location'] . ')';
	}

	if($userdata['login']['lastusernamechange'] > time() - 604800)
	{
		echo '<div class="pink_faded_div" style="font-weight: bold;">Denna användare hette tidigare ' . $userdata['login']['lastusername'] . '</div>';
	}

	traffaDrawUserDiv($userid, $userdata['login']['username'], $userdata['userinfo']['gender'], $userdata['userinfo']['birthday'], $userdata['login']['lastaction'], $userdata['userinfo']['geo_municipal'], $userdata['login']['lastip']);


	echo '<div class="contentPostbox" style="background-image: url(\'/images/traffatablebg.png\'); height: 110px;">';
	if($userdata['userinfo']['image'] == 1 || $userdata['userinfo']['image'] == 2)
	{
		echo '<div style="float: left; height: 100px; margin: 2px; width: 93px;">';
		echo insert_avatar($userid);
		echo '</div>';
	}
	else 
	{
		echo '<div style="float: left; height: 100px; width: 75px; margin: 2px; background: #000; color: #FFF;">Ingen bild</div>';
	}

	$contactlabel1 = 'Kontaktsätt';
	$contactalias1 = '-';
	$contactlabel2 = 'Kontaktsätt';
	$contactalias2 = '-';

	if(strlen($userdata['userinfo']['contact1']) > 0) {
		$usercontact1 = parseContact($userdata['userinfo']['contact1']);
		if($usercontact1['label'] != NULL) {
			$contactlabel1 = $usercontact1['label'];
			$contactalias1 = $usercontact1['link'];
		}
	}
	if(strlen($userdata['userinfo']['contact2']) > 0) {
		$usercontact2 = parseContact($userdata['userinfo']['contact2']);
		if($usercontact2['label'] != NULL) {
			$contactlabel2 = $usercontact2['label'];
			$contactalias2 = $usercontact2['link'];
		}
	}

	echo '<table class="body" style="width: 532px; border: none;">';
		echo '<tr>';
			echo '<td><b>Förnamn:</b></td>';
			echo '<td><b>' . $contactlabel1 . '</b></td>';
			echo '<td><b>' . $contactlabel2 . '</b></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td>' . strip_tags($userdata['traffa']['firstname']) . '</td>';
			echo '<td>' .	$contactalias1 . '</td>';
			echo '<td>' . $contactalias2 . '</td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td><b>Blev medlem:</b></td>';
			echo '<td><b>Loggade senast in:</b></td>';
			echo '<td><b>Längd:</b></td>';
		echo '</tr>';
		echo '<tr>';
			echo '<td>' . date('Y-m-d', $userdata['login']['regtimestamp']) . '</td>';
			echo '<td>' . fix_time($userdata['login']['lastlogon']) . '</td>';
			echo '<td>' . $userdata['traffa']['height'] . 'cm</td>';
		echo '</tr>';
	echo '</table>';
	echo '</div>';
	if($userdata['userinfo']['irl']{0} == 1)
	{
		echo '<img src="/images/irlsthlm05.png" style="width: 100px; height: 20px; border: 1px solid #cccccc; margin: 2px;" />';
	}
	if($userdata['userinfo']['irl']{1} == 1)
	{
		echo '<img src="/images/miniirlgbg05.png" style="width: 100px; height: 20px; border: 1px solid #cccccc; margin: 2px;" />';
	}
	if($userdata['userinfo']['irl']{2} == 1)
	{
		echo '<img src="/images/regnirl.png" style="width: 100px; height: 20px; border: 1px solid #cccccc; margin: 2px;" />';
	}

	$query = 'SELECT m.groupid, l.name FROM groups_list AS l, groups_members AS m WHERE ';
	$query.= 'l.groupid = m.groupid AND m.userid = "' . $_GET['id'] . '" AND m.approved = "1" LIMIT 10;';
	$result = mysql_query($query) or report_sql_error($query);
	$groups = null;
	while ($data = mysql_fetch_assoc($result))
	{
		$groups.= '<a style="font-weight:bold;" href="/traffa/groups.php?action=goto&amp;groupid=' . $data['groupid'] . '">' . $data['name'] . '</a> ';
	}
	if (isset($groups))
	{
		echo '<div class="contentPostbox" style="border-bottom:0px;">';
		echo 'Grupper jag är med i: ' . $groups;
		echo '</div>';
	}

	if($userdata['traffa']['bgimage'] > 100)
	{	
		$div_extra = ' background-image: url(\'' . $hp_url . 'images/traffabgs/' . $userdata['traffa']['bgimage'] . '.jpg\');';
	}
	elseif($userdata['traffa']['bgimage'] > 0)
	{
		$div_extra = ' background-image: url(\'' . $hp_url . 'images/traffabgs/' . $userdata['traffa']['bgimage'] . '.png\');';
	}
	echo '<div class="contentPostbox" style="' . $div_extra . '">';

	if(strlen($userdata['traffa']['presentation']) > 0) {
		echo setSmilies(nl2br(parseAll(stripslashes($userdata['traffa']['presentation']))), 25);
	}
	else {
		echo '<i>' . $userdata['login']['username'] . ' har inte skrivit någon presentation än.</i>';
	}
	echo '</div><br />';

	if($_SESSION['login']['userlevel'] >= USERLEVELS_EDIT_PRESENTATION)
	{
		echo '<a href="/traffa/admin_edit_presentation.php?id=' . $userid . '">Ändra denna persons presentation</a>';
	}

	if($userdata['traffa']['personality'] <> 0) {
		echo '<div class="contentPostbox" style="margin-top: 10px">';
		echo '<b>Personlighet: ' . $traffaDefPersonalities[$userdata['traffa']['personality']] . '</b><br>';
		echo $traffaDefPersonalitiesDesc[$userdata['traffa']['personality']] . '<br><br>';
		echo '<font style="font-size: 10px; font-style: italic;">(Denna personlighetsbeskrivning har författats av crew i syfte att underhålla och bör inte tas på för stort allvar.)</font>';
		echo '</div>' . "\n";
	}

  $query = 'SELECT i.id, i.name, i.image, i.type, ';
  $query .= 'IF(LENGTH(i.info) > 80, CONCAT(LEFT(i.info, 77), \'...\'), i.info) AS info ';
  $query .= 'FROM amuse_items AS i, amuse_notices AS n WHERE i.id = n.item_id AND n.user = "' . $userid . '" AND i.type = 1 ';
  $query .= 'ORDER BY n.timestamp DESC LIMIT 6';
  $result = mysql_query($query) or die(report_sql_error($query));
	if(mysql_num_rows($result) > 0)
	{
		echo '<h1>Spel som ' . $userdata['login']['username'] . ' spelat nyss</h1>';
  	echo '<table><tr>';
  	for($i = 0; $data = mysql_fetch_assoc($result); $i++)
  	{
	    if($i == 3)
	    {
	      echo '</tr><tr>';
	      $i = 0;
	    }
	    amuse_draw_small_item($data, 'table');
	  }
	  echo '</table>';
	}

	listPhotos($userid, $userdata['traffa']['photos']);
	if(login_checklogin() == 1 && $_SESSION['login']['id'] != $userid)
	{
		$query = 'INSERT INTO traffa_visits(profileid, userid, tstamp) VALUES(' . $userid . ', ';
		$query .= $_SESSION['login']['id'] . ', UNIX_TIMESTAMP())';
		mysql_query($query) or die(report_sql_error($query));
		$view = $userid;
		/* This is for the bubblemessage-stuff */
	  if(strlen($userdata['login']['session_id']) > 5)
	  {
			if($_SESSION['visited_profiles'][$view] != 1 && $userdata['preferences']['bubblemessage_visitors'] == 'Y')
			{
				$_SESSION['visited_profiles'][$view] = 1;
				$bubblemessage = '<a href="/traffa/profile.php?id=' . $_SESSION['login']['id'] . '">' . $_SESSION['login']['username'] . '</a> surfade nyss in på din presentation ;)';
		    $remote_session = session_load($userdata['login']['session_id']);
		    $remote_session['bubblemessage'] = $bubblemessage;
		    session_save($userdata['login']['session_id'], $remote_session);
			}
	  }
		/* Add the visit to thevisitors left panel */
		if(count($_SESSION['profile_visits']) == 10)
		{
			array_pop($_SESSION['profile_visits']);
		}
		if(!isset($_SESSION['profile_visits']))
		{
			$_SESSION['profile_visits'][] = array('id' => $view, 'username' => $userdata['login']['username']);
		}
		else
		{
			array_unshift($_SESSION['profile_visits'], array('id' => $view, 'username' => $userdata['login']['username']));
		}
	}
	ui_bottom();
?>
