<h1>Nedstängt tillfälligt!</h1>
<?php
	exit;
	require('include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/live_chat.lib.php');
	$ui_options['menu_path'] = array('chat', 'statistik');
	$ui_options['title'] = 'Klotterplank på Hamsterpaj!';
	$ui_options['stylesheets'][] = 'live_chat.css';
	$ui_options['javascripts'][] = 'live_chat.js';
	ui_top($ui_options);
	
	switch($_GET['channel'])
	{
		case 'musik':
			echo '<h1>Musik</h1>' . "\n";
			echo '<p>Här samlas alla musikälskare!</p>';
			echo live_chat_render(array('type' => 'chatroom', 'reference_id' => '1'));
			break;
		case 'mysgos':
			echo '<h1>Mysgos</h1>' . "\n";		
			echo '<p>Mysigt och trevligt, flickor och pojkar :)</p>';
			echo live_chat_render(array('type' => 'chatroom', 'reference_id' => '2'));
			break;
		case 'nattöppet':
			echo '<h1>Nattöppet</h1>' . "\n";		
			echo '<p>Bara öppet mellan 23 - 05 på natten</p>';
			if(date('H') >= 23 || date('H') <= 5)
			{
				echo live_chat_render(array('type' => 'chatroom', 'reference_id' => '3'));				
			}
			break;
		case 'klotter':
		default:
			echo '<h1>Klotter</h1>' . "\n";		
			echo '<p>Mysigt och trevligt, flickor och pojkar :)</p>';
			echo live_chat_render(array('type' => 'chatroom', 'reference_id' => '2'));
			break;
	}
	


	ui_bottom();
?>


