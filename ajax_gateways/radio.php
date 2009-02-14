<?php
	try
	{
		require('../include/core/common.php');
		require(PATHS_LIBRARIES . 'radio.lib.php');
		
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
			
			case 'dj_remove':
				if(!is_privilegied('radio_admin'))
				{
					throw new Exception('Du har inte privilegier att lägga till radio DJs');
				}
				if(!isset($_GET['id']))
				{
					throw new Exception('Inget ID kom med');
				}
				$options['user_id'] = $_GET['id'];
				radio_dj_remove($options);
				
				if(isset($_GET['no_ajax']) && $_GET['no_ajax'] == true)
				{
					header('Location: /radio/crew/');
				}
				
				echo '<div class="form_notice_success">';
				echo 'Användaren borttagen som DJ';
				echo '</div>';
			break;
			
			case 'program_add':
				if(!is_privilegied('radio_admin'))
				{
					throw new Exception('Du har inte privilegier att lägga till radio DJs');
				}
				if(!isset($_POST['name']))
				{
					throw new Exception('Du måste fylla i ett namn');
				}
				if(!isset($_POST['dj']))
				{
					throw new Exception('Du måste välja en DJ');
				}
				if(!isset($_POST['information']))
				{
					throw new Exception('Du måste skriva in lite information');
				}
				if(!is_numeric($_POST['dj']))
				{
					throw new Exception('ID\'t är inte godkänt');
				}
				$query = 'SELECT id FROM login WHERE id = ' . $_POST['dj'] . ' AND is_removed = 0 LIMIT 1';
				$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				if(mysql_num_rows($result) != 1)
				{
					throw new Exception('Ingen användare med användarnamnet kunde hittas');
				}

				$options['user_id'] = $_POST['dj'];
				$options['name'] = $_POST['name'];
				$options['information'] = $_POST['information'];
				$options['sendtime'] = $_POST['sendtime'];
				radio_program_add($options);
				
				echo '<div class="form_notice_success">';
		   		echo 'Programmet är tillagt!';
				echo '</div>';
			break;
			
			case program_remove:
				if(!is_privilegied('radio_admin'))
				{
					throw new Exception('Du har inte privilegier att lägga till radio DJs');
				}
				if(!isset($_GET['id']))
				{
					throw new Exception('Inget ID kom med');
				}
				$options['id'] = $_GET['id'];
				radio_program_remove($options);
				
				echo '<div class="form_notice_success">';
				echo 'Programmet borttaget';
				echo '</div>';
			break;
			
			case 'schedule_add':
				if(!is_privilegied('radio_sender'))
				{
					throw new Exception('Du har inte privilegier att ändra radioschemat');
				}					
				if(!isset($_POST['program']) || !is_numeric($_POST['program']))
				{
					throw new Exception('Du måste välja ett program');
				}
				if(!isset($_POST['starttime']) || strlen($_POST['endtime']) < 0)
				{
					throw new Exception('Du måste sätta en starttid');
				}
				if(!isset($_POST['endtime']) || strlen($_POST['starttime']) < 0)
				{
					throw new Exception('Du måste sätta en sluttid');
				}
				if($_POST['starttime'] >= $_POST['endtime'])
				{
					throw new Exception('Det är ju bra om programmet har börjat innan det slutar om man säger så...');
				}
				$options['program_id'] = $_POST['program'];
				$options['starttime'] = $_POST['starttime'];
				$options['endtime'] = $_POST['endtime'];
				radio_schedule_add($options);
				
				echo '<div class="form_notice_success">';
				echo 'Programmet inplanerat';
				echo '</div>';
			break;
			
			case schedule_remove:
				if(!is_privilegied('radio_sender'))
				{
					throw new Exception('Du har inte privilegier att ta bort sändningar');
				}
				if(!isset($_GET['id']))
				{
					throw new Exception('Inget ID kom med');
				}
				if(!is_numeric($_GET['id']))
				{
					throw new Exception('ID\'t är inte numeriskt');
				}
				$options['id'] = $_GET['id'];
				radio_schedule_remove($options);
				
				echo '<div class="form_notice_success">';
				echo 'Schemaläggningen borttagen';
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