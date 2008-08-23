<?php
	require('../include/core/common.php');
	
	if(!is_privilegied('privilegies_admin'))
	{
		die('Inte snoka, annars blir det smisk.');
	}
	
	
	$query = 'SHOW COLUMNS FROM privilegies';
	$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
	while($data = mysql_fetch_assoc($result))
	{
		if($data['Field'] == 'privilegie' && substr($data['Type'], 0, 5) == 'enum(')
		{
			// enum(' [...] ')
			$types = substr($data['Type'], 6, -2);
			$available_privilegies = explode("','", $types);
		}
	}
	
	if(empty($available_privilegies))
	{
		die('Knaskalas (Visst ÄR det kul med informativa felmeddelanden). ' . __FILE__ . ' line ' . __LINE__);
	}
	
	switch(isset($_GET['action']) ? $_GET['action'] : 'DEFAULT')
	{
		// Load privilegies for a specified user...
		case 'load_user':
					
			if(isset($_GET['username']) && strtolower($_GET['username']) != 'borttagen')
			{
				$query = 'SELECT id, username FROM login WHERE username LIKE "' . $_GET['username'] . '"';
				$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				if(mysql_num_rows($result) == 1)
				{
					$data = mysql_fetch_assoc($result);
					$user_id = $data['id'];
					$username = $data['username'];// Right case...
				}
			}
			elseif(isset($_GET['user_id']) && is_numeric($_GET['user_id']))
			{
				$query = 'SELECT username FROM login WHERE id = "' . $_GET['user_id'] . '"';
				$result = mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
				if(mysql_num_rows($result) == 1)
				{
					$data = mysql_fetch_assoc($result);
					$user_id = $_GET['user_id'];
					$username = $data['username'];
				}
			}
			else
			{
				$user_id = 0;	
			}
				
			if($user_id > 0)
			{	
				$out_on_top .= '<a href="/admin/privilegies_admin.php">&laquo;Tillbaka</a>';
				$output .= '<h2>' . $username . ' (user id #' . $user_id . ')</h2>' . "\n";
				
				$output .= rounded_corners_top(array('color' => 'blue_delux', 'return' => true));
				
				$output .= '<form method="post" action="' . $_SERVER['PHP_SELF'] . '?action=change_privilegies">' . "\n";
				$output .= '<input type="hidden" name="user_id" value="' . $user_id . '" />' . "\n";
				
				$output .= '<table>' . "\n";
				
				$privilegies_query = 'SELECT privilegie, privilegie_id, value FROM privilegies WHERE user = ' . $user_id;
				$privilegies_result = mysql_query($privilegies_query) or report_sql_error($privilegies_query, __FILE__, __LINE__);

				while($data = mysql_fetch_assoc($privilegies_result))
				{
					$output .= '<tr>';
					
					$output .= '<td><a href="' . $_SERVER['PHP_SELF'] . '?action=remove_privilegie&privilegie_id=' . $data['privilegie_id'] . '&back_to_user_id=' . $user_id . '">[X]</a></td>';
					$output .= '<td>' . $data['privilegie'] . '</td>' . "\n";		
					$output .= '<td><input type="text"  name="privilegie_value_' . $data['privilegie_id'] . '" value="' . $data['value'] . '" /></td>' . "\n";
					
					$output .= '</tr>' . "\n";
				}
				
				$output .= '</table>' . "\n";
				
				$output .= '<input type="submit" value="Spara" class="button_80" style="float: right;" /><br style="clear: both;" />' . "\n";
				
				$output .= '</form>' . "\n";
				
				
				$output .= rounded_corners_bottom(array('color' => 'blue_delux', 'return' => true));



				$output .= rounded_corners_top(array('color' => 'blue_delux', 'return' => true));

				$output .= '<form action="' . $_SERVER['PHP_SELF'] . '?action=add_privilegie" method="post">' . "\n";
				$output .= '<input type="hidden" name="user_id" value="' . $user_id . '" />' . "\n";
				$output .= '<select name="privilegie_add_privilegie">' . "\n";
				foreach($available_privilegies as $privilegie)
				{
					$output .= "\t" . '<option value="' . $privilegie . '"' . (($privilegie == $data['privilegie']) ? ' selected="selected"' : '') . '>' . $privilegie . '</option>' . "\n";
				}
				$output .= '</select>';
				$output .= ' = ';
				$output .= '<input type="text" name="privilegie_add_value" /> (0 = alla) ' . "\n";
				
				
				$output .= '<input type="submit" value="Lägg till privilegie..." class="button_140" />' . "\n";
				
				$output .= '</form>' . "\n";
				
				$output .= rounded_corners_bottom(array('color' => 'blue_delux', 'return' => true));
			}
			else
			{
				$output .= rounded_corners_top(array('color' => 'blue_delux', 'return' => true));
				$output .= 'Kunde inte hitta användaren.';
				$output .= rounded_corners_bottom(array('color' => 'blue_delux', 'return' => true));
			}
						
		break;
		
		case 'change_privilegies':
			if(isset($_POST['user_id']) && is_numeric($_POST['user_id']))
			{
				$privilegies_to_set = array();
				
				foreach($_POST as $key => $value)
				{
					if(preg_match('/^privilegie_value_([0-9]+)$/', $key, $matches))
					{
						if(is_numeric($matches[1]))
						{
							$query = 'UPDATE privilegies SET value = "' . $value . '" WHERE privilegie_id = ' . $matches[1];
							mysql_query($query) or report_sql_error($query, __FILE__, __LINE__);
						}
					}
				}
				
				jscript_alert('OBS! Användaren måste logga ut och in innan privilegien ändras. Du kan logga ut användaren genom Profil > Fakta > Logga ut användare om det är nödvändigt!');
				jscript_location($_SERVER['PHP_SELF'] . '?action=load_user&user_id=' . $_POST['user_id']);
				exit;
			}
		break;
		
		case 'add_privilegie':
			if(isset($_POST['user_id'], $_POST['privilegie_add_privilegie'], $_POST['privilegie_add_value']) && is_numeric($_POST['user_id']) && in_array($_POST['privilegie_add_privilegie'], $available_privilegies) && $_POST['privilegie_add_value'] != '')
			{
				$query = 'INSERT INTO privilegies (privilegie, value, user) VALUES ("' . $_POST['privilegie_add_privilegie'] . '", "' . $_POST['privilegie_add_value'] . '", ' . $_POST['user_id'] . ')';
				mysql_query($query) or report_sql_error($query);
				
				jscript_alert('OBS! Användaren måste logga ut och in innan privilegien blir satt!');
				jscript_location($_SERVER['PHP_SELF'] . '?action=load_user&user_id=' . $_POST['user_id']);
				exit;
			}
		break;
		
		case 'remove_privilegie':
			if(isset($_GET['privilegie_id'], $_GET['back_to_user_id']) && is_numeric($_GET['privilegie_id']) && is_numeric($_GET['back_to_user_id']))
			{
				$query = 'DELETE FROM privilegies WHERE privilegie_id = ' . $_GET['privilegie_id'] . ' LIMIT 1';
				mysql_query($query) or report_sql_error($query);
				
				jscript_alert('OBS! Användaren måste logga ut och in innan privilegien tas bort. Du kan logga ut användaren genom Profil > Fakta > Logga ut användare om det är nödvändigt!');
				jscript_location($_SERVER['PHP_SELF'] . '?action=load_user&user_id=' . $_GET['back_to_user_id']);
				exit;
			}
		break;
		
		case 'view_users_by_privilegie':

			$output .= '<h2>Listar användare med privilegien ' . $_GET['privilegie'] . '</h2>' . "\n";
			$output .= rounded_corners_top(array('color' => 'blue_delux', 'return' => true));

			if(in_array($_GET['privilegie'], $available_privilegies))
			{
				$query = 'SELECT l.username AS username, l.id AS user_id';
				$query .= ' FROM login AS l, privilegies AS pl';
				$query .= ' WHERE pl.user = l.id AND pl.privilegie = "' . $_GET['privilegie'] . '"';
				$result = mysql_query($query) or return_sql_error($query, __FILE__, __LINE__);
				
				if(mysql_num_rows($result) > 0)
				{
					$output .= '<ul>' . "\n";
					while($data = mysql_fetch_assoc($result))
					{
						$output .= '<li><a href="' . $_SERVER['PHP_SELF'] .  '?action=load_user&user_id=' . $data['user_id'] . '">' . $data['username'] . '</a> (<a href="/traffa/profile.php?user_id=' . $data['user_id'] . '">till profil &raquo;</a>)</li>' . "\n";
					}
					$output .= '</ul>' . "\n";
				}
				else
				{
					$output .= 'Hittade inga användare med privilegien.' . "\n";
				}
			}
			else
			{
				$output .= 'Den privilegien hittade vi tyvärr inte!' . "\n";
			}
			
			$output .= rounded_corners_bottom(array('color' => 'blue_delux', 'return' => true));
			
		break;
		
		case 'document':
		?>
<pre>
Notis: Jag vet, jag vet, det heter privilegium och inte privilegie...
Men försök att undvika att dänga SAOL i huvet på mig :D
	
use_debug_tools	 			handy, handy encoders/decoders, visa sessionsdata
use_statistic_tools			sidvisningsdiargram, event_log, OV-statistik
use_ghosting_tools			ghostfunktion, gästboksghostning(guestbook hack), administrativ logg
ip_ban_admin				ip-ban, user_facts.php
avatar_admin				Avataradmin, user_facts.php (pres.)
remove_user				user_facts.php, /remove_user.php
privilegies_admin			privilegies_admin.php
entertain_add				entertain.lib.php
entertain_update
entertain_delete
schedule_admin
backgrounds_admin			Wally's bakgrundssystem.
register_suspend_admin			register_suspend.php (stäng av användarregistrering).
discussion_forum_remove_posts		Radera poster.
discussion_forum_edit_posts		Ändra poster.
discussion_forum_rename_threads		Byta namn på trådar.
discussion_forum_lock_threads		Låsa trådar.
discussion_forum_sticky_threads		Klistra trådar.
discussion_forum_move_thread		Flytta trådar till andra kategorier.
discussion_forum_post_addition		Tillägg på andra inlägg än ens egna.

entertain_* tar emot en "handle" som value.
discussion_forum_* tar ett forum_id som value

Såhär funkar det:
Om en privilegie har 0 som värde, betyder det "allt". Alltså fungerar privilegien på alla ställen (ex. alla forum).
En privilegie har oftast ett nummer, som representerar t.ex. ett forum_id. Sätt 0 så gäller det som sagt alla forum.
En användare kan ha flera privilegier av samma sort, ex. för en för radiounderground-forumet och en för a1sweden-forumet.

Specialprivilegie:
igotgodmode heter våran hackerprivilegie, som ger alla andra privilegier på högsta nivå!

Funktionsanrop:
is_privilegied används, OCH INTE $_SESSION['privilegies']!
Den ser ut som följande:

function is_privilegied($privilegie, $item_id = 'ANY')
{
	if(isset($_SESSION['privilegies']['igotgodmode'][0]))
	{
		return true;
	}
	return ($item_id == 'ANY') ? isset($_SESSION['privilegies'][$privilegie]) : (isset($_SESSION['privilegies'][$privilegie][$item_id]) || isset($_SESSION['privilegies'][$privilegie][0]));
}

Slutsatser:
is_privilegied('privilegie') returnerar true om användaren har den privilegien,
med VILKET VÄRDE SOM HELST.

Ska vi bli värdespecifika kan man göra såhär:

is_privilegied('discussion_forum_move_thread', $forum_id);
Detta returnerar true om användaren har discussion_forum_move_thread-privilegien
och denna antingen är värdet på $forum_id (ex. 79 för SPAM-forumet) ELLER värdet är 0.

Värdet 0 är nämligen speciellt, det gör att man kommer åt allt. Även om t.ex. ett forumid
skickas med till is_privilegied() så kommer man alltså få tillbaka true om användarens privilegie är = 0.
</pre>
		<?
		exit;
		break;
		
		// Index page...
		default:
		
			$output .= rounded_corners_top(array('color' => 'red', 'return' => true));
			$output .= '<strong>Pilla inte på det nya privilegiesystemet innan du pratat med Joel. Under tiden kan du läsa <a href="?action=document">det här dokümentet</a>!</strong>';
			$output .= rounded_corners_bottom(array('color' => 'red', 'return' => true));

		
			$output .= '<h2>Ladda användare...</h2>' . "\n";
			$output .= rounded_corners_top(array('color' => 'blue_deluxe', 'return' => true));
		
			$output .= '<form action="' . $_SERVER['PHP_SELF'] . '" method="get">' . "\n";
			$output .= '<input type="hidden" name="action" value="load_user" />' . "\n";
			$output .= 'Användarnamn: <input type="text" name="username" value="" />' . "\n";
			$output .= '<input type="submit" value="Ladda användare..." class="button_140" />' . "\n";
			$output .= '</form>' . "\n";
			
			$output .= rounded_corners_bottom(array('color' => 'blue_deluxe', 'return' => true));
			
			
			
			$output .= '<h2>Lista användare med en viss privilegie</h2>' . "\n";
			$output .= rounded_corners_top(array('color' => 'blue_delux', 'return' => true));

			$output .= '<ul>' . "\n";
			foreach($available_privilegies AS $privilegie)
			{
				$output .= '<li><a href="' . $_SERVER['PHP_SELF'] .  '?action=view_users_by_privilegie&privilegie=' . $privilegie . '">' . $privilegie . '</a></li>' . "\n";
			}
			$output .= '</ul>' . "\n";

			$output .= rounded_corners_bottom(array('color' => 'blue_delux', 'return' => true));

		break;
	}
	
	$ui_options['menu_path'] = array('admin', 'privilegies_admin');
	$ui_options['title'] = 'Privilegier på Hamsterpaj.net';
	ui_top($ui_options);
		echo $out_on_top . $output;
	ui_bottom();
?>