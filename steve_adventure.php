<?php
	require('/storage/www/standard.php');

	$ui_options['title'] = 'Steves äventyr';
	ui_top($ui_options);
	
	$_SESSION['steve_adventure_stage'] = isset($_SESSION['steve_adventure_stage']) ? $_SESSION['steve_adventure_stage'] : 1;
	$stage = $_SESSION['steve_adventure_stage'];
	
	echo '<h1>Steves äventyr</h1>' . "\n";
	
	switch($stage)
	{
		case 1:
			if(isset($_POST['unlock_stage_2']))
			{
				$_SESSION['steve_adventure_stage']++;
				jscript_location($_SERVER['PHP_SELF']);
			}
			else
			{
				$text  = 'Saa du tror att du kan hacka?' . "\n";
				$text .= 'Om du nu aer saa haxx sae skickar du POST-variabeln' . "\n";
				$text .= 'unlock_stage_2=true till den haer sidan' . "\n";
				$text .= 'och ser vad som haender.' . "\n";
				$text .= 'Lycka till!' . "\n";
				
				$cipher_text = '';
				for($index = 0; $index < strlen($text); $index++)
				{
					$char = substr($text, $index, 1);
					$cipher_text .= (($char == "\n" || $char == ' ') ?  $char  :  chr(ord($char) + ((ord($char) % 2) ? -1 : 3))  );
				}
				
	
				
				echo '<span style="color: #ffffff">Ja, vart annat tecken har en annan finjustering på algoritmen...</span>';
				echo '<p>';
				echo 'Nu blir det till att utforska sidan lite för att få några tips...';
				echo '</p><p>';
				echo nl2br(htmlspecialchars($cipher_text));
				echo '</p>';
			}
			
		break;
		
		case 2:
		
		break;
		
		case 3:
		
		break;
	}
	
	ui_bottom();
?>