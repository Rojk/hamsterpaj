<?php
function report_sql_error($query, $file = null, $line = null)
{
  echo '<div class="server_message_error"><h2>Såhär skall det ju inte bli, usch!</h2><p>Ett fel på hamsterpaj har inträffat! Utvecklingsansvariga har meddelats om detta, du behöver inte rapportera felet. Vi åtgärdar det snart (om vi kan :P)</p>';
	echo '<h3 class="server_message_collapse_header" id="server_message_collapse_header_sqlerror">Visa felsökningsinformation</h3>' . "\n";
  echo '<div class="server_message_collapsed_information" id="server_message_collapse_information_sqlerror">' . "\n";
  echo '<br />Felsökningsinformation:<br />' . htmlspecialchars(mysql_error());
  echo '<br />Frågan löd:<br /><p>' . htmlspecialchars($query) . '</p>';
  echo $file . ' #' . $line;
 	echo '<h1>Backtrace</h1>' . "\n";
 	preint_r(debug_backtrace());
	echo '</div></div>' . "\n";
  if(isset($file))
  {
  	echo '<strong>Logging</strong>';
		//log_to_file('sql_error', LOGLEVEL_ERROR, $file, $line, $query);
		trace('sql_errors', $query . ' in ' . $file . ' on line ' . $line);
  }
}

function database_error_create($options)
{
	$output = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">' . "\n";
	$output .= '<html xmlns="http://www.w3.org/1999/xhtml">' . "\n";
	$output .= '<head>' . "\n";
	$output .= '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />' . "\n";
	$output .= '<title>';
	switch($options['type'])
	{
		case 'running_backup':
			$output .= 'Hamsterpaj tar backup just nu';
		break;
		
		case 'connection_error':
			$output .= 'Databasuppkoplingsfel (databas offline?)';
		break;
	}
	$output .= '</title>' . "\n";
	$output .= '<link rel="icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />' . "\n";
	$output .= '<link rel="shortcut icon" href="http://images.hamsterpaj.net/favicon.png" type="image/x-icon" />' . "\n";
	
	$output .= '<style type="text/css">' . "\n";
	$output .= '@import url(\'/stylesheets/max_connections.css\');' . "\n";
	$output .= '</style>' . "\n";
	
	switch($options['type'])
	{
		case 'running_backup':
			$output .= '<meta http-equiv="refresh" content="10">' . "\n";
		break;
		
		case 'connection_error':
			$output .= (isset($options['try_again']) && $options['try_again']) ? '<meta http-equiv="refresh" content="2">' . "\n" : '';
		break;
	}
	
	$output .= '</head>' . "\n";
	
	$output .= '<body>' . "\n";
	
	$output .= '	<div class="main">' . "\n";
	$output .= '		<h1>';
	switch($options['type'])
	{
		case 'running_backup':
			$output .= 'Hamsterpaj tar backup nu igen...';
		break;
		
		case 'connection_error':
			$output .= 'Det är för trångt på Hamsterpaj just nu (eller så är databasen trasig)';
		break;
	}
	$output .= '</h1>' . "\n";
	$output .= '		<p>' . "\n";
		
	switch($options['type'])
	{
		case 'running_backup':
			$output .= 'När vår databas tar backup så blir den tyvärr lite upptagen och hinner inte svara på frågor.<br />' . "\n";
			$output .= 'Ursäkta dröjsmålet, ca 05:00 kommer den igång igen!' . "\n";
		break;
		
		case 'connection_error':
			if(isset($options['try_again']) && $options['try_again'])
			{
				$output .= 'När väldigt många personer försöker komma åt Hamsterpaj samtidigt så fungerar det inte alltid så bra,' . "\n";
				$output .= 'just nu är det lite för fullt, din förfrågan fick inte plats. Vi försöker automatiskt att hämta din' . "\n";
				$output .= 'sida igen om några sekunder.<br />' . "\n";
				$output .= 'Ursäkta dröjsmålet!' . "\n";
			}
			else
			{
				$output .= 'Du kan inte komma åt Hamsterpaj nu. Trots flera försök får du inte plats på sidan, kika tillbaks om en halvtimma eller så!' . "\n";
			}
		break;
	}
	
	$output .= '		</p>' . "\n";
	$output .= '	</div>' . "\n";
		
	$output .= '</body>' . "\n";
	$output .= '</html>' . "\n";
	
	return $output;
}
?>