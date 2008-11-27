<?php
	$hp_url = 'http://www.hamsterpaj.net/';

	$sql = 'SELECT login.id, login.username, userinfo.birthday FROM login, userinfo WHERE userinfo.userid = login.id AND userinfo.gender = "F" && (userinfo.image = "1" || userinfo.image = "2") ORDER BY login.lastlogon DESC LIMIT 21';
	$result = mysql_query($sql) or die('Query failed: ' . mysql_error());


	$file_content .= '<div style="background: #F7F7F7; border: 1px solid #CCCCCC;">';
	$file_content .= '<h2 style="margin: 3px;">Flickor</h2>';
	$file_content .= '<table style="width: 100%"><tr>';
	$rowcount = 0;	
	
	while($data = mysql_fetch_assoc($result)) {
		if($rowcount == 7){
			$file_content .= '</tr><tr>';
			$rowcount = 1;
		}
		else{
			$rowcount++;
		}

		$age = date_get_age($data['birthday']);

		if(isset($age)) {
			$strAge = ' (' . $age . ' år)';
		}
		else {
			$strAge = '';
		}
		
		$file_content .= '<td>';
		$file_content .= '<a href="/traffa/profile.php?id=' . $data['id'] . '" title="' . $data['username'] . $strAge . '">';
		$file_content .= '<img src="' . IMAGE_URL . '/images/users/thumb/' . $data['id'] . '.jpg" style="border: 1px solid #CCCCCC;" /></a>';
		$file_content .= '</td>';
		
		unset($userAge);
	}
	
	$file_content .= '</table>';
	$file_content .= '</div><br />';

	$file_content .= '<div style="background: #F7F7F7; border: 1px solid #CCCCCC;">';
	$file_content .= '<h2 style="margin: 3px;">Pojkar</h2>';

	$sql = 'SELECT login.id, login.username, userinfo.birthday FROM login, userinfo WHERE userinfo.userid = login.id AND userinfo.gender = "m" && (userinfo.image = "1" || userinfo.image = "2") ORDER BY login.lastlogon DESC LIMIT 21';
	$result = mysql_query($sql) or die('Query failed: ' . mysql_error()); // && userinfo.traffa = "1"

	$file_content .= '<table style="width: 100%;"><tr>';
	$rowcount = 0;
	
	while($data = mysql_fetch_assoc($result)) {
		$age = date_get_age($data['birthday']);
		if($rowcount == 7){
			$file_content .= '</tr><tr>';
			$rowcount = 1;
		}
		else {
			$rowcount++;
		}		
		if(isset($age)) {
			$strAge = ' (' . $age . ')';
		}
		else {
			$strAge = '';
		}
		
		$file_content .= '<td>';
		$file_content .= '<a href="/traffa/profile.php?id=' . $data['id'] . '" title="' . $data['username'] . $strAge . '"><img src="' . IMAGE_URL . '/images/users/thumb/' . $data['id'] . '.jpg" style="border: 1px solid #CCCCCC;" /></a>';
		$file_content .= '</td>';
		
		unset($userAge);
	}
	$file_content .= '</table>';
	$file_content .= '</div>';
	$file_content .= '<!-- Last updated at: ' . date('Y-m-d H:i:s') . ' -->';

	$handle = fopen($hp_path . 'traffa/gallery_content.html', 'w');
	fwrite($handle, $file_content);
	fclose($handle);
?>
