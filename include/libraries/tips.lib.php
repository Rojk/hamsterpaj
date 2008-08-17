<?php

include(PATHS_INCLUDE . '/texts/tips.texts.php');

function  check_email($email)
{
	if (!preg_match("/^( [a-zA-Z0-9] )+( [a-zA-Z0-9\._-] )*@( [a-zA-Z0-9_-] )+( [a-zA-Z0-9\._-] +)+$/" , $email))
	{
		return false;
	}
	return true;
}
    
function tips_send($options)
{
	/*
	options				kommentar
	reciever			e-mail
	sender_id			user_id
	sender_name			full name (a regular string)
	subject				email subject string
	message				html message string
	*/
	
	// check if reciever has recieved before, how many times and if reciever accepts tip e-mail
	if(check_email($options['reciever']))
	{
		return 'not a valid e-mail address';
	}
	$query = 'SELECT * FROM tips_recievers WHERE reciever="' . $options['reciever'] . '"';
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	if($data = mysql_fetch_assoc($result))
	{
		$has_recieved = true;
		$status = $data['status'];
		$hash = $data['hash'];
	}
	else
	{
		$has_recieved = false;
		$status = 'first_time';
	}
	if($has_recieved)
	{
		if($status == 'accepts')
		{
			// if reciever has recieved before and accepts, send e-mail with standard pre-message - "accepted"
		}
		else
		{
			// if reciever has recieved before but not (yet) accepted, do nothing and return error
			return 'denies';
		}
	}
	else
	{
		// if reciever has not recived before, send e-mail with standard pre-message - "first time" and save to database
		$hash = md5(rand());
		$query = 'INSERT INTO tips_recievers (reciever, hash) VALUES ("' . $options['reciever'] . '", "' . $hash . '")';
		mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	}

	if(isset($options['sender_id']))
	{
		$query = 'SELECT username FROM login WHERE id="' . $options['sender_id'] . '"';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if($data = mysql_fetch_assoc($result))
		{
			$sender = $data['username'];
			if($sender == 'Borttagen')
			{
				return 'false_sender';
			}
		}
		else
		{
			return 'false_sender';
		}
	}
	else
	{
		$sender = $options['sender_name'];
	}
	
	if($status == 'first_time')
	{
		$message = 
			'Hej, någon som säger sig heta ' . $sender . ' vill tipsa dig om en kul grej på Hamsterpaj!' . "\n" .
			'Eftersom vi hatar spam har vi spärrat din mailadress från fler tips, du kommer inte få fler tips eller utmaningar från hamsterpajare om du inte tackar ja till det.' . "\n" . "\n" .
			$options['message'] . "\n" . "\n" .
			'För att kolla in tipset och tacka ja till fler tips och utmaningar, använd den här länken: ' . "\n" .
			'http://www.hamsterpaj.net/tips.php?action=accept&link=' . $options['link'] . '&hash=' . $hash . "\n" . "\n" .
			'För att kolla in tipset, men inte tacka ja till fler tips och utmaningar, använd den här länken:' . "\n" .
			'http://www.hamsterpaj.net/tips.php?action=view&link=' . $options['link'] . '&hash=' . $hash . "\n";
	}
	elseif($status == 'accepts')
	{
		$message = 
			'Hej igen, nu vill någon som säger sig heta ' . $sender . ' tipsa dig om en grej på Hamsterpaj!' . "\n" . "\n" .
			$options['message'] . "\n" . "\n" .
			'Kolla in tipset här:' . "\n" .
			'http://www.hamsterpaj.net/tips.php?action=view&link=' . $options['link'] . '&hash=' . $hash . "\n" .
			'Du får det här tipset eftersom du tidigare tackat ja till att ta emot tips och utmaningar från användare på Hamsterpaj.' . "\n" . "\n" .
			'Om du vill spärra din e-postadress från fler tips och utmaningar, använd den här länken:' . "\n" .
			'http://www.hamsterpaj.net/tips.php?action=deny&link=' . $options['link'] . '&hash=' . $hash . "\n";
	}

	$headers = 'From: tips@hamsterpaj.net' . "\r\n";
	mail($options['reciever'], $options['subject'], $message, $headers);
	log_to_file('tips', LOGLEVEL_DEBUG, __FILE__, __LINE__, $reciever . ' recieved message: (subject: ' . $options['subject'] . ') ' . $message);
	return 'ok';
}

function tips_reciever_status_set($options)
{
	/*
	options				kommentar
	reciever			e-mail
	hash				hash password
	status				'accepts', 'denies'
	*/
	$query = 'UPDATE tips_recievers SET status="' . $options['status'] . '" WHERE';
	if(isset($options['hash']))
	{
		$query .= ' hash="' . $options['hash'] . '"';
	}
	else
	{
		$query .= ' reciever="' . $options['reciever'] . '"';
	}
	$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
	return $result > 0;
}

?>