<?php
function report_sql_error($query, $file = null, $line = null)
{
  echo '<div class="server_message_error"><h2>S�h�r skall det ju inte bli, usch!</h2><p>Ett fel p� hamsterpaj har intr�ffat! Utvecklingsansvariga har meddelats om detta, du beh�ver inte rapportera felet. Vi �tg�rdar det snart (om vi kan :P)</p>';
	echo '<h3 class="server_message_collapse_header" id="server_message_collapse_header_sqlerror">Visa fels�kningsinformation</h3>' . "\n";
  echo '<div class="server_message_collapsed_information" id="server_message_collapse_information_sqlerror">' . "\n";
  echo '<br />Fels�kningsinformation:<br />' . htmlspecialchars(mysql_error());
  echo '<br />Fr�gan l�d:<br /><p>' . htmlspecialchars($query) . '</p>';
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
			$output .= 'Det �r f�r tr�ngt p� Hamsterpaj just nu (eller s� �r databasen trasig)';
		break;
	}
	$output .= '</h1>' . "\n";
	$output .= '		<p>' . "\n";
		
	switch($options['type'])
	{
		case 'running_backup':
			$output .= 'N�r v�r databas tar backup s� blir den tyv�rr lite upptagen och hinner inte svara p� fr�gor.<br />' . "\n";
			$output .= 'Urs�kta dr�jsm�let, ca 05:00 kommer den ig�ng igen!' . "\n";
		break;
		
		case 'connection_error':
			if(isset($options['try_again']) && $options['try_again'])
			{
				$output .= 'N�r v�ldigt m�nga personer f�rs�ker komma �t Hamsterpaj samtidigt s� fungerar det inte alltid s� bra,' . "\n";
				$output .= 'just nu �r det lite f�r fullt, din f�rfr�gan fick inte plats. Vi f�rs�ker automatiskt att h�mta din' . "\n";
				$output .= 'sida igen om n�gra sekunder.<br />' . "\n";
				$output .= 'Urs�kta dr�jsm�let!' . "\n";
			}
			else
			{
				$output .= 'Du kan inte komma �t Hamsterpaj nu. Trots flera f�rs�k f�r du inte plats p� sidan, kika tillbaks om en halvtimma eller s�!' . "\n";
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