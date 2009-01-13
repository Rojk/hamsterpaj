<?php
	require('../include/core/common.php');
	require(PATHS_INCLUDE . 'libraries/profile.lib.php');
	$ui_options['title'] = 'Guestbook-hack - Hamsterpaj.net';
	$ui_options['menu_path'] = array('fra', 'guestbook_hack');
	
	try 
	{
		if (!is_privilegied('use_ghosting_tools'))
		{
			die('Üt ür mein Haus! FülhachkaRRe!! Ni ähr Allah LIhkhadhaaana!');
		}
		
		$out = '<head>
			<title>GB-HACKZ! - Hamsterpaj.net</title>
			<script>
			function if_not_empty_enable(id) {
				var id = document.getElementById(id);
				if (this.value != "") 
				{
					id.disabled = false;
				} 
			}
			</script>
			<style>
				
				@import url(/stylesheets/forms.css);
				@import url(/stylesheets/shared.css);
				
				body {
					font-family: Verdana monospace; 
					color: #000; 
					font-size: 14px;
				}
			</style>
		</head>' . "\n";
		
		$highlights[] = '<strong>%VALUE%</strong>';
		$highlights[] = '<em>%VALUE%</em>';
		$highlights[] = '<span style="color: green; font-weight: bold;">%VALUE%</span>';
		$highlights[] = '<span style="color: red; font-weight: bold;">%VALUE%</span>';
		$highlights[] = '<span style="color: blue; font-weight: bold;">%VALUE%</span>';
		$highlights[] = '<span style="color: #ababab; font-weight: bold;">%VALUE%</span>';
		$highlights[] = '<span style="color: green;">%VALUE%</span>';
		$highlights[] = '<span style="color: red;">%VALUE%</span>';
		$highlights[] = '<span style="color: blue;">%VALUE%</span>';
		$highlights[] = '<span style="color: #ababab;">%VALUE%</span>';
		$highlights[] = '<span style="color: #9b0ca0; font-weight: bold;">%VALUE%</span>';
		$highlights[] = '<span style="color: #9b0ca0;">%VALUE%</span>';
		
		$highlights[] = '<span style="color: #A52A2A;">%VALUE%</span>';
		$highlights[] = '<span style="color: #CD5C5C;">%VALUE%</span>';
		$highlights[] = '<span style="color: #FF00FF;">%VALUE%</span>';
		$highlights[] = '<span style="color: #191970;">%VALUE%</span>';
		$highlights[] = '<span style="color: #2E8B57;">%VALUE%</span>';
		$highlights[] = '<span style="color: #9ACD32; font-weight: bold;">%VALUE%</span>';
		$highlights[] = '<span style="color: #4682B4; font-weight: bold;">%VALUE%</span>';
		$highlights[] = '<span style="color: #DDA0DD; font-weight: bold;">%VALUE%</span>';
		$highlights[] = '<span style="color: #688E23; font-weight: bold;">%VALUE%</span>';
		$highlights[] = '<span style="color: #87CEEB; font-weight: bold;">%VALUE%</span>';
		$highlights[] = '<span style="color: #800000; font-weight: bold;">%VALUE%</span>';
		$highlights[] = '<span style="color: #A52A2A;">%VALUE%</span>';
		
		
		function number_isset($numbers)
		{
			foreach ($numbers AS $number)
			{
				if (!isset($number))
				{
					return false;
				}
			 	elseif (!is_numeric($number))
			 	{
					return false;
				}
			}
			return true;
		}
		
		if (!empty($_GET['id_1']) || !empty($_GET['username_1']))
		{
			if ( !empty($_GET['username_1']) )
			{
				if ( strtolower($_GET['username_1']) == 'borttagen' )
				{
					throw new Exception('Rötägg och fiskrom.');
				}
				$sql = 'SELECT id FROM login WHERE username = "' . $_GET['username_1'] . '" LIMIT 1';
				$result = mysql_query($sql);
				$data = mysql_fetch_assoc($result);
				$_GET['id_1'] = $data['id'];
				if ( !empty($_GET['username_2']) )
				{
					if ( strtolower($_GET['username_2']) == 'borttagen' )
					{
						throw new Exception('Fiskrom och rötägg.');
					}
					$sql = 'SELECT id FROM login WHERE username = "' . $_GET['username_2'] . '" LIMIT 1';
					$result = mysql_query($sql);
					$data = mysql_fetch_assoc($result);
					$_GET['id_2'] = $data['id'];
				}
			}
			
			$out .= (!empty($_GET['fullscreen'])) ? '' : '<a href="/admin/guestbook_hack.php">Tillbaka</a>';
			if (number_isset(array($_GET['id_1'], $_GET['id_2'])))
			{
				$sql = 'SELECT gb.* FROM traffa_guestbooks AS gb
				 WHERE gb.sender = ' . $_GET['id_1'] . '
				 AND gb.recipient = ' . $_GET['id_2'] . '
				 OR gb.sender = ' . $_GET['id_2'] . '
				 AND gb.recipient = ' . $_GET['id_1'];
				$sql .= ' ORDER BY gb.timestamp DESC LIMIT 2000';
			}
			elseif (!empty($_GET['id_1']) && strlen($_GET['id_2']) == 0)
			{
				$sql = 'SELECT gb.* FROM traffa_guestbooks AS gb
				 WHERE gb.sender = ' . $_GET['id_1'] . '
				 OR gb.recipient = ' . $_GET['id_1'] . '
				 ORDER BY gb.timestamp DESC LIMIT 2000';
			}
			$result = mysql_query($sql) or $out .= rounded_corners(mysql_error(), array('color' => 'orange_deluxe'), true);
			$out .= '<table style="width: 100%;">';
			$out .= '<tr>' . "\n";
			$out .= '<th colspan="11" style="background: #fc8; color: #000;">' . "\n";
			$sql_2 = 'SELECT username FROM login WHERE id = ' . $_SESSION['login']['id'] . ' LIMIT 1';
			$data_2 = mysql_fetch_assoc(mysql_query($sql_2));
			$out .= 'Frågan utförd ' . date("Y.m.d - H:i.s") . ' (' . time() . ') av ' . $data_2['username'] . '(' . $_SESSION['login']['id'] . ')';
			$out .= '</tr>' . "\n\n";
			$out .= '<tr>' . "\n";
			$out .= '<th valign="top">post id</th>' . "\n";
			$out .= '<th valign="top">time</th>' . "\n";
			$out .= '<th valign="top">sender</th>' . "\n";
			$out .= '<th valign="top">recipient</th>' . "\n";
			$out .= '<th valign="top">is_private</th>' . "\n";
			$out .= '<th valign="top">deleted</th>' . "\n";
			$out .= '<th valign="top">message</th>' . "\n";
			$out .= '</tr>' . "\n\n";
			while ($data = mysql_fetch_assoc($result))
			{
				if ($data['deleted'] == 1)
				{
					$out .= '<tr style="background: #FEE;">' . "\n";
				}
				elseif ($data['is_private'] == 1)
				{
					$out .= '<tr style="background: #EEF;">' . "\n";
				}
				elseif ($zebra == 1)
				{
					$out .= '<tr style="background: #F9F9F9;">' . "\n";
				}
				$zebra = ($zebra == 1) ? $zebra = 0 : $zebra = 1;
				
				$highlight_items = array(
					'sender',
					'recipient'
				);
				
				foreach ($highlight_items as $val)
				{
					if ($val == 2348)
					{
						$data[$val] = '<span style="color: #FA0; font-weight: bold;">' . $val . '</span>';
					}
					if(!isset($assigned[$data[$val]]))
					{
						if(count($highlights) > 0)
						{
							$assigned[$data[$val]] = array_pop($highlights);
						}
						else
						{
							$assigned[$data[$val]] = '%VALUE%';
						}
					}
					$data[$val] = str_replace('%VALUE%', $data[$val], $assigned[$data[$val]]);
				}
				
				$out .= '<td valign="top">' . $data['id'] . "\n";
				$out .= '<td valign="top">' . date("Y.m.d - H:i.s", $data['timestamp']) . '</td>';
				$out .= '<td valign="top">' . $data['sender'] . '</td>' . "\n";
				$out .= '<td valign="top">' . $data['recipient'] . '</td>' . "\n";
				$out .= '<td valign="top">' . $data['is_private'] . '</td>' . "\n";
				$out .= '<td valign="top">' . $data['deleted'] . '</td>' . "\n";
				$out .= '<td>' . nl2br($data['message']) . '</td>' . "\n";
				$out .= '</tr>' . "\n\n";
			}
			$out .= '</table>' . "\n";
			$sql = 'INSERT INTO admin_event (event, value, timestamp, admin_id, user_id, item_id)
			 VALUES ("guestbook_hack",';
			$sql .= (isset($_GET['id_1']) && $_GET['id_2'] == '') ? ' "target: ' . $_GET['id_1'] . '",' : ' "target: ' . $_GET['id_1'] . ' and ' . $_GET['id_2'] . '",'; 
			$sql .= ' ' . time() . ','; 
			$sql .= ' ' . $_SESSION['login']['id'] . ',';
			$sql .= ' ' . $_GET['id_1'] . ','; 
			$sql .= ' ' . 0 . ')';
			if ( !mysql_query($sql) )
			{
				throw new Exception($sql . '<br />' . mysql_error() . '<br /><br /> Kontakta <a href="/joar">Joar</a>. det är han som har kodat skiten :S');
			}
			
		}
		else
		{
			$out .= '
			<div id="ui_content">
				<fieldset style="width: 500px;">
					<legend>GB-Hack</legend>
					<table class="form">
						<tr>
							<td colspan="2">
							<h2>Hacka GB! (OBS! integritetskränkande)</h2>
							<p>Det GÅR att skriva in EN användare i "ID 1"-fältet, 
							men det kan bli en ganska tung process om det är något spamtroll.</p><p>
							Annars är det meningen att man ska följa en privat
								eller borttagen konversation mellan två parter med den här funktionen.
							Kopiera bara user_id och klistra in i fälten, simple as that.</p>
							</td>
						</tr>
						<form method="get">
						<tr>
							<td>
								<label for="id_1">ID 1</label>
							</td>
							<td>
								<input type="text" onkeyup="if_not_empty_enable(\'id_2\')" id="id_1" name="id_1" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="id_2">ID 2</label>
							</td>
							<td>
								<input type="text" disabled="disabled" id="id_2" name="id_2" />
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="submit" value="FRA via Användarid!" />
							</td>
						</tr>
						</form>
						<form action="' . $_SERVER['SCRIPT_URI'] . '?get_by=username" method="get">
						<tr>
							<td>
								<label for="username_1">Användarnamn 1</label>
							</td>
							<td>
								<input type="text" onkeyup="if_not_empty_enable(\'username_2\')" name="username_1" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="username_2">Användarnamn 2</label>
							</td>
							<td>
								<input type="text" disabled="disabled" id="username_2" name="username_2" />
							</td>
						</tr>
						<tr>
							<td colspan="2">
								<input type="submit" value="FRA via Användarnamn!" />
							</td>
						</tr>
						</form>
					</table>
				</fieldset>
			</div>
			' . "\n";
		}
	}
	catch (Exception $e)
	{
		$out .= '<p class="error">' . "\n";
		$out .= $e->getMessage();
		$out .= '</p>' . "\n";
	}
	echo $out;
?>