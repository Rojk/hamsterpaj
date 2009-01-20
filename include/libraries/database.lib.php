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
?>