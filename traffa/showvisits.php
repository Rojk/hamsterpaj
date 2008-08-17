<?php

require('../include/core/common.php');
require(PATHS_INCLUDE . 'traffa-functions.php');
$ui_options['current_menu'] = 'traffa';
ui_top($ui_options);
if(login_checklogin() != 1)
{
	header('location: /');
	exit;
}

	traffa_draw_user_div($_SESSION['login']['id'], $_SESSION);

?>
<h1>Dina senaste besökare</h1>
<?php
$query = 'SELECT DISTINCT login.username, traffa_visits.userid, traffa_visits.tstamp, userinfo.image, ';
$query .= 'userinfo.gender, userinfo.birthday, userinfo.geo_location, userinfo.contact1, userinfo.contact2 ';
$query .= 'FROM login, traffa_visits, userinfo ';
$query .= 'WHERE login.id = traffa_visits.userid AND userinfo.userid = traffa_visits.userid AND ';
$query .= 'traffa_visits.profileid = ' . $_SESSION['login']['id'] . ' ORDER BY traffa_visits.tstamp DESC LIMIT 30';
$result = mysql_query($query) or die(mysql_error());
if(mysql_num_rows($result) == 0){
	echo '<div class="grey_faded_div">';
	echo '<b>Du har inte haft några besökare än :(</b>';
	echo '</div>';
}
$alreadyshowed = array();

while($data = mysql_fetch_assoc($result))
{
	if(in_array($data['userid'], $alreadyshowed)){
		continue;
	}
	array_push($alreadyshowed, $data['userid']);

	$userage = date_get_age($data['birthday']);
	if($data['gender'] == 'P')
	{
		$divbg = 'blue_faded_div';
	}
	elseif($data['gender'] == 'F')
	{
		$divbg = 'pink_faded_div';
	}
	else
	{
		$divbg = 'grey_faded_div';
	}
	
	echo '<div class="'.$divbg.'" style="repeat-x; margin-top: 10px; border: 1px solid #CCCCCC;">';
	echo '<table class="body" style="width: 100%;"><tr><td style="vertical-align: top; width: 75px;">';
	if($data['image'] == 1 || $data['image'] == 2)
	{
		echo insert_avatar($data['userid']);
	}
	else
	{
		echo '<img src="' . IMAGE_URL . 'images/noimage.png" style="width: 75px; height: 75px; border: 1px solid #cccccc;" alt="Ingen visningsbild"/>';
	}
	echo '</td><td style="vertical-align: top;">';
	echo fix_time($data['tstamp']) . '<br/>';
	echo '<a href="/traffa/profile.php?id=' . $data['userid'] . '">';
	echo '<strong>' . $data['username']  . '</strong></a> ';
	echo birthdaycake($data['birthday']) . ' ';
	if($data['gender'] == 'P'){
		echo ' Kille, ';
	}
	elseif($data['gender'] == 'F'){
		echo ' Tjej, ';
	}
	if($data['birthday'] > '0000-00-00'){
		$age = date_get_age($data['birthday']);
		echo $age . ' år ';
	}
	if(strlen($data['geo_location']) > 0){
		echo 'från ' . $data['geo_location'];
	}
	echo '<br/><br/>';
	if(strlen($data['contact1']) > 0){
		$contact1 = parseContact($data['contact1']);
		echo ' <strong>' . $contact1['label'] . ':</strong> ' . $contact1['link'] . '<br />';
	}
	if(strlen($data['contact2']) > 0){
		$contact2 = parseContact($data['contact2']);
		echo ' <strong>' . $contact2['label'] . ':</strong> ' . $contact2['link'] . '<br />';
	}
	echo '</td></tr></table>';
	echo '</div>';
}

ui_bottom();
?>
