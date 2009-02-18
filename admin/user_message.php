<?php
	require('../include/core/common.php');
	try
	{
		$ui_options['stylesheets'][] = 'forms.css';
		
		if(!is_privilegied('user_message'))
		{
			throw new Exception('Du har inte privilegier för den här sidan');
		}
		
		if(isset($_POST['username']) && isset($_POST['message']))
		{
			$query = 'SELECT session_id FROM login WHERE username = "' . $_POST['username'] . '" LIMIT 1';
			$result = mysql_query($query) or report_sql_error($query);
			if(mysql_num_rows($result) == 1)
			{
				$data = mysql_fetch_assoc($result);
				if(strlen($data['session_id']) > 0)
				{
					$remote_session = session_load($data['session_id']);
					$remote_session['user_message'] = $_POST['message'];
					session_save($data['session_id'], $remote_session);
					$out .= '<div class="form_notice_success">Meddelande skickat!</div>' . "\n";
				}
				else
				{
					$out .= '<div class="form_notice_error">Den användaren verkar inte ha en aktiv session.</div>' . "\n";
				}
			}
			else
			{
				$out .= '<div class="form_notice_error">Användaren hittades inte.</div>' . "\n";
			}
		}
		
		// form
		$out .= '<fieldset>' . "\n";
		$out .= '<legend>Användarmeddelande!</legend>' . "\n";
		$out .= '<p>Den här funktionen ger användaren en javascriptruta med ditt meddelande. Detta kräver dock att användaren är inloggad.</p>' . "\n";
		$out .= '<form action="?action=submit" method="post">';
		$out .= '<table class="form">' . "\n";
		$out .= '<tr>' . "\n";
		$out .= '<th><label for="username">Användarnamn <strong>*</strong></label></th>' . "\n";
		$out .= '<td><input type="text" name="username" /></td>' . "\n";
		$out .= '</tr>' . "\n";
		$out .= '<tr>' . "\n";
		$out .= '<th><label for="message">Meddelande <strong>*</strong></label></th>' . "\n";
		$out .= '<td><input type="text" name="message" /></td>' . "\n";
		$out .= '</tr>' . "\n";
		$out .= '</table>' . "\n";
		$out .= '<input type="submit" id="submit" value="Skicka" />' . "\n";
		$out .= '</form>';
		$out .= '</fieldset>' . "\n";
	}
	catch (Exception $error)
	{
		$options['type'] = 'error';
    	$options['title'] = 'Nu blev det fel här';
   		$options['message'] = $error -> getMessage();
    	$options['collapse_link'] = 'Visa felsökningsinformation';
   		$options['collapse_information'] = preint_r($error, true);
    	$out .= ui_server_message($options);
	}
	
	ui_top($ui_options);
	echo $out;
	ui_bottom();
?>