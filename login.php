<?php
	require('include/core/common.php');

	
	switch($_GET['action'])
	{
		case 'register':
			if($_POST) {
				if($_SESSION['authcode'] != $_POST['authcode']){
					header('location: /msg.php?message=login_authcode');
				}
				elseif(strlen($_POST['Anvandarnamn']) > 16 || !preg_match("/^[0-9a-zA-Z_-]+$/i", $_POST['Anvandarnamn'])) {
					header('Location: /msg.php?message=login_invalidusername');
				}
				elseif(strlen($_POST['Losenord']) < 4) {
					header('Location: /msg.php?message=login_passwordlength');
				}
				elseif($_POST['Losenord'] != $_POST['Losenord2']) {
					header('Location: /msg.php?message=login_passwordmatch');
				}
				else {
					$msg = login_register($_POST['Anvandarnamn'], $_POST['Losenord'], 1);
					switch($msg)
					{
						case 0:
							header('Location: /msg.php?message=login_useradded');
							break;
						case 1:
							header('Location: /msg.php?message=login_emailinuse');
							break;
						case 2:
							header('Location: /msg.php?message=login_usernameinuse');
							break;
						default:
							header('Location: /msg.php?message=error');
							break;
					}
				}
			}
			else
			{
				header('Location: ' . $_SERVER['HTTP_REFERRER']);
			}
			break;
			
		case 'login':
			if(!$_POST)
			{
				header('location: ' . $_SERVER['HTTP_REFERRER']);
			}
			else
			{
				$msg = login_dologin($_POST['username'], $_POST['password']);
/*				if ($_SESSION['login']['id'] = 897773)
				{
					session_destroy();
					header('Location: http://internetisseriousbusiness.com/');
				}*/
				switch($msg)
				{
					case 0:
						header('Location: /msg.php?message=login_loginfailed');
						break;
					case 1:
						event_log_log('user_log_on');
						if(substr_count($_SERVER['HTTP_REFERER'], "msg.php") > 0 )
						{
							header('location: /index.php');
						}
						else {
						  if(strlen($_SERVER['HTTP_REFERER']) > 5)
						  {
						      header('Location: ' . $_SERVER['HTTP_REFERER']);
						  }
						  else
						  {
						      header('Location: /index.php');
						  }
						}
						exit();
						break;
					case 2:
						header('Location: /msg.php?message=login_invalidlogin');
						break;
					case 3:
						header('Location: /installningar/renew_password.php');
						break;
					default:
						header('Location: /msg.php?message=error');
						break;
				}
			}
			
			break;
			
		case 'logout':
			$msg = login_logout();
			
			switch($msg)
			{
				case 0:
					header('Location: /msg.php?message=login_logoutfailed');
					break;
				case 1:
					header('location: ' . $_SERVER['HTTP_REFERER'] . '');
					break;
				default:
					header('Location: /msg.php?message=error');
					break;
			}
				
			break;
		
		case 'changepass':
			if(!isset($_SESSION['login'])) {
				header('Location: /index.php');
			}
			elseif($_POST['newpassword'] != $_POST['newpassword2']) {
				header('Location: /msg.php?message=login_changepassmismatch');
			}
			elseif(strlen($_POST['oldpassword']) >= 4 && strlen($_POST['newpassword']) >= 4 && $_POST['newpassword'] == $_POST['newpassword2']) {
				$msg = login_changepassword($_SESSION['login']['id'], $_POST['oldpassword'], $_POST['newpassword']);
				
				switch($msg) {
					case 0:
						login_logout();
						header('Location: /msg.php?message=login_changepassok');
						break;
						
					case 1:
						header('Location: /msg.php?message=login_changepasswrongpass');
						break;
						
					default:
						header('Location: /msg.php?message=error&default='.$msg);
						break;
						
				}
			}
			else {
				header('Location: /msg.php?message=error&else');
			}
			
			break;
		
		case 'changeinfo':
			if(!isset($_SESSION['login'])) {
				header('Location: /index.php');
			}
			else {
				if($_POST['showhetluft'] == '1') {
					$showhetluft = 1;
				}
				else {
					$showhetluft = 0;
				}
				if($_POST['gender'] == 'P' || $_POST['gender'] == 'F') {
					$gender = $_POST['gender'];
				}
				else {
					$gender = '';
				}
				
				if($_POST['contact1_medium'] == 'null') {
					$contact1 = 'NULL';
				}
				else {
					$contact1 = '"' . $_POST['contact1_medium'] . ':' . $_POST['contact1_handle'] . '"';
				}
				
				if($_POST['contact2_medium'] == 'null') {
					$contact2 = 'NULL';
				}
				else {
					$contact2 = '"' . $_POST['contact2_medium'] . ':' . $_POST['contact2_handle'] . '"';
				}
				
				if($_POST['birth_year'] != 'null' && $_POST['birth_month'] != 'null' && $_POST['birth_day'] != 'null') {
					$birthday = '"' . $_POST['birth_year'] . $_POST['birth_month'] . $_POST['birth_day'] . '"';
				}
				else {
					$birthday = 'NULL';
				}
				
				$msg = login_changeinfo($_SESSION['login']['id'], $showhetluft, $contact1, $contact2, $_POST['forum_signature'], $gender, $birthday, $_POST['geo_location'], $_POST['RegionNr'], $_POST['CityNr'], $_POST['KommunNr'], $_POST['geo_municipal']);
				switch($msg) {
					case 0:
						$_SESSION['userdata'] = login_getuserdata($_SESSION['login']['id']);
						header('Location: /msg.php?message=login_changeinfook');
						break;
					default:
						header('Location: /msg.php?message=error');
						break;
				}
			}
			
			break;
						
		default:
			header('Location: /index.php');
			break;
	}
?>
