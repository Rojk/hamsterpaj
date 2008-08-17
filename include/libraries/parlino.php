<?php
	function parlino_sms_credits($user)
	{
		$credits = 3;
		
		$query = 'SELECT sms_sent FROM parlino_sms_credits WHERE user = "' . $user . '" AND date = "' . date('Y-m-d') . '"';
		$result = mysql_query($query) or die(report_sql_error($query, __FILE__, __LINE__));
		if(mysql_num_rows($result) == 1)
		{
			$data = mysql_fetch_assoc($result);
			$credits -= $data['sms_sent'];
		}
		return $credits;
	}
	
	function parlino_sms_ok($sms)
	{
		$return['status'] = 'ok';
		
		if(!$sms['recipient'])
		{
			$return['recipient'] = 'error';
			$return['status'] = 'fail';
		}
		return $return;
	}
	
	function parlino_sms_send()
	{
	}
	
	function parlino_sms_form()
	{
		echo '<form action="/parlino/gratis_sms.php" method="post">' . "\n";
		echo '<fieldset class="sms_form">' . "\n";
		echo '<legend>Skicka SMS</legend>' . "\n";
		echo '<h3>Mottagare</h3>' . "\n";
		echo '<p>Svenskt mobilnummer, betal-SMS fungerar ej</p>' . "\n";
		echo '<input type="text" name="recipient" value="07" />' . "\n";
		echo '<h3>Meddelande</h3>' . "\n";
		echo '<p><span id="parlino_sms_counter">255</span> tecken kvar</p>' . "\n";
		echo '<textarea name="message" id="parlino_sms_compose"></textarea>' . "\n";
		echo '<h3>Ditt kontantkort idag</h3>' . "\n";
		echo '<p>Här väljer du vad <strong>du</strong> har för kontantkort idag</p>' . "\n";
		echo '<select name="operator">' . "\n";
		echo '<option value="untouched">--Välj en operatör i listan--</option>' . "\n";
		echo '<option value="comviq_knockout">Comviq Kontant KNOCK-OUT</option>' . "\n";
		echo '<option value="telia_refill">Telia Refill kontantkort</option>' . "\n";
		echo '<option value="parlino">Parlino</option>' . "\n";
		echo '</select>' . "\n";
		echo '<br style="clear: both;">' . "\n";
		echo '<br />' . "\n";
		echo '<input type="submit" value="Skicka SMS &raquo;" />' . "\n";
		echo '</fieldset>' . "\n";
		echo '</form>' . "\n";
	}	
?>