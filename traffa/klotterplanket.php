<?php
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'live_chat.lib.php');
	$_GET['channel'] = (isset($_GET['channel'])) ? $_GET['channel'] : 'klotter';

	$ui_options['menu_path'] = array('traeffa', 'klotterplank', $_GET['channel']);

	$ui_options['title'] = 'Klotterplank på Hamsterpaj!';
	$ui_options['stylesheets'][] = 'live_chat.css';
	$ui_options['javascripts'][] = 'live_chat.js';
	
	$ui_options['menu_addition']['traeffa']['children']['klotterplank']['children']['klotter'] = array('label' => 'Klotter', 'url' => '/traffa/klotterplanket.php?channel=klotter');
	$ui_options['menu_addition']['traeffa']['children']['klotterplank']['children']['musik'] = array('label' => 'Musik', 'url' => '/traffa/klotterplanket.php?channel=musik');
	$ui_options['menu_addition']['traeffa']['children']['klotterplank']['children']['mysgos'] = array('label' => 'Mysgos', 'url' => '/traffa/klotterplanket.php?channel=mysgos');
	$ui_options['menu_addition']['traeffa']['children']['klotterplank']['children']['nattoeppet'] = array('label' => 'Nattöppet', 'url' => '/traffa/klotterplanket.php?channel=nattoeppet');	
	$ui_options['menu_addition']['traeffa']['children']['klotterplank']['children']['english'] = array('label' => 'English', 'url' => '/traffa/klotterplanket.php?channel=english');
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
		case 'nattoeppet':
			echo '<h1>Nattöppet</h1>' . "\n";		
			echo '<p>Bara öppet mellan 23 - 05 på natten</p>';
			if(date('H') >= 23 || date('H') <= 5)
			{
				echo live_chat_render(array('type' => 'chatroom', 'reference_id' => '3'));				
			}
			break;
		case 'english':
			echo '<h1>English support</h1>' . "\n";		
			echo '<p>Support board for the Hamsterpaj English Day. Post your questions here and someone will shortly help you to form a correct sentence in English! (At least we hope that someone will)</p>';
			echo live_chat_render(array('type' => 'chatroom', 'reference_id' => '5'));
			break;
		case 'klotter':
		default:
			echo '<h1>Klotter</h1>' . "\n";		
			echo '<p>Mysigt och trevligt, flickor och pojkar :)</p>';
			echo live_chat_render(array('type' => 'chatroom', 'reference_id' => '4'));
			break;
	}
	


	ui_bottom();
?>


