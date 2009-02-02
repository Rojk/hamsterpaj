<?php
	try
	{
		require('../include/core/common.php');
		require(PATHS_INCLUDE . 'libraries/radio.lib.php');
		
		if(isset($_GET['action']))
		{
			$action = $_GET['action'];
		}
		else
		{
			throw new Exception('No action in get data recieved');
		}
		
		switch($action)
		{
			case 'dj_add':
				if(!is_privilegied('radio_admin'))
				{
					throw new Exception('Du har inte privilegier att lägga till radio DJs');
				}
				if(!isset($_POST['radio_dj_add_name']))
				{
					throw new Exception('Du måste fylla i ett namn');
				}
				if(!isset($_POST['radio_dj_add_information']))
				{
					throw new Exception('Du måste fylla i information');
				}
				if(strtolower($dj_username) == 'borttagen')
				{
					throw new Exception('Borttagna användare går inte att lägga till');
				}
				$query = 'SELECT id FROM login WHERE username = "' . $_POST['radio_dj_add_name'] . '" AND is_removed = 0 LIMIT 1';
				$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				if(mysql_num_rows($result) != 1)
				{
					preint_r($_POST);
					throw new Exception('Ingen användare med användarnamnet kunde hittas');
				}
				$data = mysql_fetch_assoc($result);
				$dj_user_id = $data['id'];
				$dj_information = $_POST['radio_dj_add_information'];
				radio_dj_add($dj_user_id, $dj_information);
				
				echo '<div class="form_notice_success">';
		   		echo 'Användaren är tillagd!';
				echo '</div>';
			break;
			default:
				throw new Exception('Action not found');
			break;
		}
	}
	catch (Exception $error)
	{
		echo '<div class="form_notice_error">';
   		echo $error -> getMessage();
		echo '</div>';
	}
?>