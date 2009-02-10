<?php
	require('../include/core/common.php');
	require(PATHS_LIBRARIES . 'comments.lib.php');
	require(PATHS_LIBRARIES . 'schedule.lib.php');
	$ui_options['menu_path'] = array('chat', 'statistik');
	$ui_options['title'] = 'Undersökningar på Hamsterpaj';
	
	$ui_options['stylesheets'][] = 'comments.css';
	$ui_options['javascripts'][] = 'comments.js';
	
	$request = poll_get_action($_SERVER['REQUEST_URI']);

	switch($request['action'])
	{
		case 'view_poll':
			$output .= poll_render($request['poll']);

			$output .= '<h2>Visa undersökningen i forumet</h2>' . "\n";
			$output .= '<p>Använd detta ID-nummer om du vill infoga undersökningen i vårt forum: <strong>' . $request['poll']['id'] . '</strong></p>' . "\n";
			
			$output .= '<h2>Lägg till på din blogg eller websida</h2>' . "\n";
			$output .= '<p>Du kan lägga till denna omröstning på din blogg eller hemsida. Kopiera bara koden nedanför och klistra in den där du vill ha undersökningen. <strong>Denna kod fungerar inte i vårt forum!</strong></p>' . "\n";
			$output .= '<input type="text" class="poll_embed_code" value=\'&lt;script type="text/javascript" language="javascript" src="http://www.hamsterpaj.net/poll/embed.php?poll=' . $request['poll']['id'] . '"&gt;&lt;/script&gt;\' />' . "\n";
			$output .= '<h2>Kommentera omröstningen</h2>' . "\n";
			$output .= comments_input_draw($request['poll']['id'], 'poll');
			$output .= comments_list($request['poll']['id'], 'poll');
			break;
		case 'index':
			$output .= '<h1>Här kan man göra sina enga undersökningar</h1>' . "\n";
			if(login_checklogin())
			{
				$output .= '<h2>Gör en ny undersökning</h2>';
				$output .= poll_form();
				$polls = poll_fetch(array('author' => $_SESSION['login']['id'], 'limit' => 100));
				if(count($polls) > 0)
				{
					$output .= '<h1>Dina undersökningar</h1>' . "\n";
					$output .= '<ul class="poll_list">' . "\n";
					foreach($polls AS $poll)
					{
						$output .= '<li>' . date('Y-m-d', $poll['timestamp']) . ' <a href="/poll/' . $poll['handle'] . '.html">' . $poll['question'] . '</a></li>' . "\n";
					}
					$output .= '</ul>' . "\n";
				}
			}
			else
			{
				$output .= '<p>För att kunna göra dina egna undersökningar måste du vara inloggad som medlem. Att bli medlem är gratis, tar ungefär en minut och vi frågar inte efter varken personnummer eller e-postadress.<br /><a href="/register.php">Bli medlem</a> nu!</p>' . "\n";
			}
			break;

		case 'compose':
			$output .= '<h1>Fyll bara i formuläret så har du din undersökning på studs!</h1>' . "\n";
			$output .= poll_form();
			break;
		case 'create':
			if($request['poll']['type'] == 'daily' && is_privilegied('frontpage_poll_admin'))
			{
				$schedule['data'] = serialize($request['poll']);
				$schedule['release'] = strtotime($request['poll']['release']);
				$schedule['type'] = 'poll';
				schedule_event_add($schedule);

				$output .= '<h1>Undersökningen på plats! Seså, gör en till, tjockis!</h1>' . "\n";
				$output .= poll_form();
			}
			else
			{
				$handle = poll_create($request['poll']);
				header('Location: /poll/' . $handle . '.html');
				exit;
			}
			break;
		
		case 'poll_not_found':
		default:
			$output = '<h1>Error</h1>';
			break;
	}
	
	ui_top($ui_options);

	echo $output;
	
	ui_bottom();
?>


